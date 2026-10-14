@extends('layouts.app')

@section('title', 'নতুন পণ্য প্রাপ্তি নোট তৈরি (Create GRN)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">নতুন পণ্য প্রাপ্তি গ্রহণ (Goods Receipt Note - GRN)</h4>
                <p class="text-muted small mb-0">চালান নম্বর, প্রাপ্ত পরিমাণ ও কোয়ালিটি পরিদর্শনের পর গৃহীত পরিমাণ লিপিবদ্ধ করুন</p>
            </div>
            <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        @if(!$selectedPo)
        <!-- Step 1: Select Approved Purchase Order -->
        <div class="card card-custom mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">ধাপ ১: ক্রয়াদেশ (PO) নির্বাচন করুন</h5>
                <form method="GET" action="{{ route('goods-receipts.create') }}" class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">অনুমোদিত ক্রয়াদেশ (Approved Purchase Order) <span class="text-danger">*</span></label>
                        <select name="po_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">ক্রয়াদেশ বেছে নিন</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}">
                                    {{ $po->po_number }} — {{ $po->supplier?->name }} (তারিখ: {{ $po->po_date?->format('d-m-Y') }}, অবস্থা: {{ $po->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
        @else
        <!-- Step 2: Fill GRN Details for Selected PO -->
        <div class="card card-custom">
            <div class="card-body p-4">
                <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <i class="bi bi-info-circle me-1"></i>
                        নির্বাচিত ক্রয়াদেশ: <strong>{{ $selectedPo->po_number }}</strong> (সরবরাহকারী: {{ $selectedPo->supplier?->name }})
                    </div>
                    <a href="{{ route('goods-receipts.create') }}" class="btn btn-sm btn-outline-secondary bg-white">
                        অন্য PO নির্বাচন করুন
                    </a>
                </div>

                <form action="{{ route('goods-receipts.store') }}" method="POST" id="grnForm">
                    @csrf
                    <input type="hidden" name="purchase_order_id" value="{{ $selectedPo->id }}">

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">গোডাউন (Destination Warehouse) <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                <option value="">গোডাউন বেছে নিন</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }} {{ $wh->is_central ? '(Central)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">GRN নম্বর <span class="text-danger">*</span></label>
                            <input type="text" name="grn_number" class="form-control" value="{{ old('grn_number', 'GRN-' . date('Ymd') . '-' . rand(100, 999)) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">গ্রহণের তারিখ <span class="text-danger">*</span></label>
                            <input type="date" name="received_date" class="form-control" value="{{ old('received_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">সরবরাহকারীর চালান নম্বর (Delivery Challan No) <span class="text-danger">*</span></label>
                            <input type="text" name="delivery_challan_no" class="form-control" placeholder="যেমন: CH-8921" value="{{ old('delivery_challan_no') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">সরবরাহকারী বিল/ইনভয়েস নম্বর (Invoice No)</label>
                            <input type="text" name="supplier_invoice_no" class="form-control" placeholder="যেমন: INV-4491" value="{{ old('supplier_invoice_no') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">পরিদর্শন মন্তব্য / নোট (Inspection Notes)</label>
                            <input type="text" name="notes" class="form-control" placeholder="ওজন ব্রিজ স্লিপ ও টেস্ট রিপোর্ট সংক্রান্ত তথ্য" value="{{ old('notes') }}">
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">মালামালের রিসিভ ও পরিদর্শন তথ্য</h5>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">মালামাল (Item)</th>
                                    <th style="width: 12%;" class="text-end">অর্ডারকৃত</th>
                                    <th style="width: 12%;" class="text-end">পূর্বে গৃহীত</th>
                                    <th style="width: 13%;">চালান পরিমাণ <span class="text-danger">*</span></th>
                                    <th style="width: 13%;">গৃহীত পরিমাণ <span class="text-danger">*</span></th>
                                    <th style="width: 12%;">গৃহীত (Accepted) <span class="text-danger">*</span></th>
                                    <th style="width: 13%;">প্রত্যাখ্যাত (Rejected)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedPo->items as $idx => $pItem)
                                @php
                                    $remaining = max(0, (float)$pItem->quantity_ordered - (float)$pItem->quantity_received);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $pItem->item?->name }}</div>
                                        <small class="text-muted">{{ $pItem->item?->code }} ({{ $pItem->item?->unit?->symbol }})</small>
                                        <input type="hidden" name="items[{{ $idx }}][po_item_id]" value="{{ $pItem->id }}">
                                        <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $pItem->item_id }}">
                                        <input type="hidden" name="items[{{ $idx }}][unit_price]" value="{{ $pItem->unit_price }}">
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($pItem->quantity_ordered, 4) }}</td>
                                    <td class="text-end text-muted">{{ number_format($pItem->quantity_received, 4) }}</td>
                                    <td>
                                        <input type="number" step="0.0001" min="0" name="items[{{ $idx }}][quantity_challan]" class="form-control form-control-sm" value="{{ $remaining }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0" name="items[{{ $idx }}][quantity_received]" class="form-control form-control-sm qty-recv" value="{{ $remaining }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0" name="items[{{ $idx }}][quantity_accepted]" class="form-control form-control-sm qty-acc text-success fw-bold" value="{{ $remaining }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0" name="items[{{ $idx }}][quantity_rejected]" class="form-control form-control-sm qty-rej text-danger" value="0.0000">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('goods-receipts.index') }}" class="btn btn-light">বাতিল</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> GRN সংরক্ষণ করুন (Save GRN)
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
