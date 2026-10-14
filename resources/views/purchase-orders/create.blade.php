@extends('layouts.app')

@section('title', 'নতুন ক্রয়াদেশ তৈরি (Create PO)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">নতুন ক্রয়াদেশ তৈরি (Purchase Order)</h4>
                <p class="text-muted small mb-0">অনুমোদিত উপকরণ চাহিদা অথবা সরাসরি আইটেম যুক্ত করে ক্রয়াদেশ প্রস্তুত করুন</p>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">প্রকল্প (Project) <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">প্রকল্প নির্বাচন করুন</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ (old('project_id', $mr?->project_id) == $project->id) ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">সরবরাহকারী (Supplier) <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">সরবরাহকারী নির্বাচন করুন</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} ({{ $supplier->company_name ?? $supplier->phone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">পিও নম্বর (PO Number) <span class="text-danger">*</span></label>
                            <input type="text" name="po_number" class="form-control @error('po_number') is-invalid @enderror"
                                value="{{ old('po_number', 'PO-' . date('Y') . '-' . rand(1000, 9999)) }}" required>
                            @error('po_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">পিও তারিখ <span class="text-danger">*</span></label>
                            <input type="date" name="po_date" class="form-control" value="{{ old('po_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">প্রত্যাশিত ডেলিভারি তারিখ</label>
                            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date', date('Y-m-d', strtotime('+5 days'))) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">ভ্যাট হার (VAT %) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="vat_percentage" id="vatPercentage" class="form-control" value="{{ old('vat_percentage', '7.50') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">পেমেন্ট শর্তাবলী (Payment Terms)</label>
                            <input type="text" name="payment_terms" class="form-control" placeholder="যেমন: 30 Days Credit" value="{{ old('payment_terms', '30 Days Credit') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ডেলিভারি ঠিকানা (Delivery Address)</label>
                            <input type="text" name="delivery_address" class="form-control" placeholder="সাইটের সুনির্দিষ্ট ঠিকানা" value="{{ old('delivery_address') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">বিশেষ নির্দেশনা / মন্তব্য (Notes)</label>
                            <input type="text" name="notes" class="form-control" placeholder="কোনো বিশেষ শর্ত থাকলে উল্লেখ করুন" value="{{ old('notes') }}">
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">মালামালের মূল্য ও পরিমাণের তালিকা</h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="poItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%;">উপকরণ (Item) <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">পরিমাণ (Quantity) <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">একক দর (Unit Price ৳) <span class="text-danger">*</span></th>
                                    <th style="width: 15%;" class="text-end">মোট মূল্য (৳)</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="poItemsBody">
                                @if($mr && $mr->items->count() > 0)
                                    @foreach($mr->items as $idx => $mItem)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="items[{{ $idx }}][mr_item_id]" value="{{ $mItem->id }}">
                                            <select name="items[{{ $idx }}][item_id]" class="form-select form-select-sm" required>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" {{ $item->id == $mItem->item_id ? 'selected' : '' }}>
                                                        {{ $item->name }} ({{ $item->unit?->symbol ?? '' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.0001" min="0.0001" name="items[{{ $idx }}][quantity_ordered]" class="form-control form-select-sm item-qty" value="{{ $mItem->quantity_approved }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[{{ $idx }}][unit_price]" class="form-control form-select-sm item-price" value="{{ $mItem->item?->standard_rate ?? 0 }}" required>
                                        </td>
                                        <td class="text-end fw-bold item-total">0.00</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-po-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td>
                                        <select name="items[0][item_id]" class="form-select form-select-sm" required>
                                            <option value="">উপকরণ নির্বাচন করুন</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" data-rate="{{ $item->standard_rate }}">
                                                    {{ $item->name }} ({{ $item->unit?->symbol ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0.0001" name="items[0][quantity_ordered]" class="form-control form-select-sm item-qty" placeholder="পরিমাণ" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control form-select-sm item-price" placeholder="দর (৳)" required>
                                    </td>
                                    <td class="text-end fw-bold item-total">0.00</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-po-row" disabled><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">উপ-মোট (Subtotal):</td>
                                    <td class="text-end fw-bold" id="lblSubtotal">৳ 0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">ভ্যাট (VAT):</td>
                                    <td class="text-end fw-bold text-danger" id="lblVat">৳ 0.00</td>
                                    <td></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end fw-bold fs-6">সর্বমোট (Grand Total):</td>
                                    <td class="text-end fw-bold fs-6" id="lblTotal">৳ 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" id="addPoRowBtn">
                        <i class="bi bi-plus-lg me-1"></i> আরো আইটেম যোগ করুন
                    </button>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-light">বাতিল</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> ক্রয়াদেশ সংরক্ষণ করুন (Save PO)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = {{ $mr ? $mr->items->count() : 1 }};
    const addPoRowBtn = document.getElementById('addPoRowBtn');
    const poItemsBody = document.getElementById('poItemsBody');
    const vatInput = document.getElementById('vatPercentage');

    const itemOptions = `@foreach($items as $item)<option value="{{ $item->id }}" data-rate="{{ $item->standard_rate }}">{{ $item->name }} ({{ $item->unit?->symbol ?? '' }})</option>@endforeach`;

    addPoRowBtn.addEventListener('click', function () {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm" required>
                    <option value="">উপকরণ নির্বাচন করুন</option>
                    ${itemOptions}
                </select>
            </td>
            <td>
                <input type="number" step="0.0001" min="0.0001" name="items[${rowIndex}][quantity_ordered]" class="form-control form-select-sm item-qty" placeholder="পরিমাণ" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="form-control form-select-sm item-price" placeholder="দর (৳)" required>
            </td>
            <td class="text-end fw-bold item-total">0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-po-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        poItemsBody.appendChild(row);
        rowIndex++;
        updateTotals();
    });

    poItemsBody.addEventListener('change', function(e) {
        if (e.target.tagName === 'SELECT') {
            const selectedOpt = e.target.options[e.target.selectedIndex];
            const defaultRate = selectedOpt.getAttribute('data-rate');
            const priceInput = e.target.closest('tr').querySelector('.item-price');
            if (defaultRate && priceInput && !priceInput.value) {
                priceInput.value = parseFloat(defaultRate).toFixed(2);
            }
        }
        updateTotals();
    });

    poItemsBody.addEventListener('input', updateTotals);
    vatInput.addEventListener('input', updateTotals);

    poItemsBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-po-row')) {
            const rows = poItemsBody.querySelectorAll('tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                updateTotals();
            }
        }
    });

    function updateTotals() {
        let subtotal = 0;
        const rows = poItemsBody.querySelectorAll('tr');
        rows.forEach(r => {
            const qty = parseFloat(r.querySelector('.item-qty')?.value) || 0;
            const price = parseFloat(r.querySelector('.item-price')?.value) || 0;
            const lineTotal = qty * price;
            const totalEl = r.querySelector('.item-total');
            if (totalEl) totalEl.textContent = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            subtotal += lineTotal;
        });

        const vatPct = parseFloat(vatInput.value) || 0;
        const vatAmt = subtotal * (vatPct / 100);
        const grandTotal = subtotal + vatAmt;

        document.getElementById('lblSubtotal').textContent = '৳ ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('lblVat').textContent = '৳ ' + vatAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('lblTotal').textContent = '৳ ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    updateTotals();
});
</script>
@endsection
