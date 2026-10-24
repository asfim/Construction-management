@extends('layouts.app')

@section('title', 'Item Categories')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Item Categories (ক্যাটাগরি ম্যানেজমেন্ট)</h4>
        <p class="text-muted small mb-0">Manage main parent categories (Steel, Cement, Finishing) and subcategories hierarchy</p>
    </div>
    @can('item.manage')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('item-categories.create', ['type' => 'parent']) }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-folder-plus"></i>
            <span>Add Parent Category</span>
        </a>
        <a href="{{ route('item-categories.create', ['type' => 'sub']) }}" class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-arrow-return-right"></i>
            <span>Add Subcategory</span>
        </a>
    </div>
    @endcan
</div>

<!-- Tabs & Filters -->
<div class="card card-custom mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <!-- Hierarchy Filter Tabs -->
            <ul class="nav nav-pills gap-2 flex-wrap">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $type === 'all' ? 'active' : '' }}" 
                       href="{{ route('item-categories.index', array_filter(['type' => 'all', 'search' => request('search')])) }}">
                        <i class="bi bi-collection me-1"></i>
                        <span>All Categories</span>
                        <span class="badge {{ $type === 'all' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} ms-1 rounded-pill">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $type === 'parent' ? 'active' : '' }}" 
                       href="{{ route('item-categories.index', array_filter(['type' => 'parent', 'search' => request('search')])) }}">
                        <i class="bi bi-folder2 me-1"></i>
                        <span>Main / Parent Categories</span>
                        <span class="badge {{ $type === 'parent' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} ms-1 rounded-pill">
                            {{ $counts['parent'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $type === 'sub' ? 'active' : '' }}" 
                       href="{{ route('item-categories.index', array_filter(['type' => 'sub', 'search' => request('search')])) }}">
                        <i class="bi bi-arrow-return-right me-1"></i>
                        <span>Subcategories</span>
                        <span class="badge {{ $type === 'sub' ? 'bg-white text-primary' : 'bg-success-subtle text-success' }} ms-1 rounded-pill">
                            {{ $counts['sub'] }}
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Search & Parent Filter Form -->
            <form method="GET" action="{{ route('item-categories.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <input type="hidden" name="type" value="{{ $type }}">
                
                @if($parentCategories->count() > 0)
                <select name="parent_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 170px;">
                    <option value="">Filter by Parent...</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ $parentId == $parent->id ? 'selected' : '' }}>
                            📁 {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @endif

                <div class="input-group input-group-sm" style="min-width: 220px;">
                    <input type="text" name="search" class="form-control" placeholder="Search name or code..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>

                @if($search || $parentId || $type !== 'all')
                <a href="{{ route('item-categories.index') }}" class="btn btn-sm btn-outline-danger" title="Reset Filters">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th class="ps-4">Code</th>
                        <th>Category Name</th>
                        <th>Level / Structure</th>
                        <th>Parent Category</th>
                        <th class="text-center">Total Items</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="ps-4 fw-bold font-monospace text-primary">{{ $cat->code }}</td>
                        <td>
                            <div class="fw-bold text-dark">
                                @if($cat->parent_id)
                                    <i class="bi bi-arrow-return-right text-muted me-1"></i>
                                @else
                                    <i class="bi bi-folder-fill text-primary me-1"></i>
                                @endif
                                {{ $cat->name }}
                            </div>
                            @if($cat->name_bn)
                                <small class="text-muted">{{ $cat->name_bn }}</small>
                            @endif
                        </td>
                        <td>
                            @if(! $cat->parent_id)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="bi bi-folder2 me-1"></i> Main / Parent
                                </span>
                                @if($cat->children_count > 0)
                                    <small class="badge bg-light text-dark border ms-1">{{ $cat->children_count }} subcategories</small>
                                @endif
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-arrow-return-right me-1"></i> Subcategory
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($cat->parent)
                                <a href="{{ route('item-categories.index', ['parent_id' => $cat->parent_id]) }}" class="text-decoration-none fw-semibold text-dark">
                                    📁 {{ $cat->parent->name }} <span class="text-muted font-monospace small">({{ $cat->parent->code }})</span>
                                </a>
                            @else
                                <span class="text-muted fst-italic small">None (Top-Level)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $cat->items_count }} items
                            </span>
                        </td>
                        <td>
                            @if($cat->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                @can('item.manage')
                                @if(! $cat->parent_id)
                                <a href="{{ route('item-categories.create', ['parent_id' => $cat->id, 'type' => 'sub']) }}" 
                                   class="btn btn-sm btn-outline-success" 
                                   title="Add Subcategory under {{ $cat->name }}">
                                    <i class="bi bi-plus"></i> Subcategory
                                </a>
                                @endif
                                <a href="{{ route('item-categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary" title="Edit Category">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('item-categories.destroy', $cat) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($cat->name) }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" {{ ($cat->items_count > 0 || $cat->children_count > 0) ? 'disabled' : '' }} title="{{ ($cat->items_count > 0 || $cat->children_count > 0) ? 'Cannot delete: items or subcategories exist' : 'Delete Category' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No item categories found for the selected filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
