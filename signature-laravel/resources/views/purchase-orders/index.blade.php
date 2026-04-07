<x-app-layout>
    <div x-data="{ filterSidebarOpen: false }">
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg-auto order-lg-0">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Purchase Order Management</h1>
                <p class="text-muted mb-0 small">View and manage all purchase orders with their details</p>
            </div>
            @can('create purchase order')
            <div class="col-12 col-lg order-lg-1">
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-lg-end">
                    <a href="{{ route('purchase-orders.create') }}" class="btn btn-success d-flex align-items-center shadow-sm">
                        <i class="fas fa-plus me-1 me-sm-2"></i><span class="d-none d-sm-inline">Create Purchase Order</span><span class="d-inline d-sm-none">Create PO</span>
                    </a>
                </div>
            </div>
            @endcan
        </div>
    </div>

    <div x-show="filterSidebarOpen" x-cloak @click="filterSidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5; z-index: 1040;"></div>
    <div x-show="filterSidebarOpen" x-cloak class="position-fixed top-0 end-0 h-100 bg-white shadow-lg filter-sidebar" style="z-index: 1050; overflow-y: auto; border-left: 1px solid #e5e7eb;" @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;"><i class="fas fa-filter me-2 text-primary"></i>Filters</h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('purchase-orders.index') }}" id="poFilterForm">
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="PO number, PI number, buyer..." style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Sales Manager</label>
                    <select name="sales_manager" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="">All Sales Managers</option>
                        @foreach($salesManagers as $manager)
                            <option value="{{ $manager->id }}" {{ request('sales_manager') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">PI Number</label>
                    <input type="text" name="pi_number" value="{{ request('pi_number') }}" class="form-control" placeholder="PI number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium" style="color: #374151;">Buyer Name</label>
                    <input type="text" name="buyer_name" value="{{ request('buyer_name') }}" class="form-control" placeholder="Buyer name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-check me-2"></i>Apply</button>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="list-header-title-row d-flex align-items-center justify-content-between flex-shrink-0" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-shopping-bag text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;" title="PO List">PO List</h2>
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0" style="background-color: color-mix(in srgb, #ef4444 15%, #ffffff); color: #dc2626; font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $purchaseOrders->total() }} Total</span>
                    </div>
                    <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                        <button type="button" @click="filterSidebarOpen = !filterSidebarOpen" class="btn border-0 d-flex align-items-center justify-content-center p-0" style="width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color);" title="Filter"><i class="fas fa-filter"></i></button>
                        @if(request()->hasAny(['search', 'sales_manager', 'pi_number', 'buyer_name']) && (request('search') || request('sales_manager') || request('pi_number') || request('buyer_name')))
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 36px; height: 36px; min-width: 36px;" title="Clear Filters"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
                <form method="GET" action="{{ route('purchase-orders.index') }}" class="d-flex align-items-center gap-2 list-header-search flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search PO, PI, buyer..." style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                    </div>
                    @if(request('sales_manager'))<input type="hidden" name="sales_manager" value="{{ request('sales_manager') }}">@endif
                    @if(request('pi_number'))<input type="hidden" name="pi_number" value="{{ request('pi_number') }}">@endif
                    @if(request('buyer_name'))<input type="hidden" name="buyer_name" value="{{ request('buyer_name') }}">@endif
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0" style="border-radius: 8px; width: 38px; height: 38px; min-width: 38px;" title="Search"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                     <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr. No</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">PO Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">PI Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Buyer Name</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Port of Destination</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Created By</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Created At</th>
                            <th class="px-4 py-3   small fw-semibold text-center" style="color: var(--primary-color) !important;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr class="border-bottom">
                                <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $purchaseOrders->firstItem() + $loop->index }}</span></td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $po->purchase_order_number }}</div>
                                </td>
                                <td class="px-2">
                                    <div style="color: #6b7280;">{{ $po->proformaInvoice->proforma_invoice_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2">
                                    <div class="fw-medium" style="color: #1f2937;">{{ $po->buyer_name }}</div>
                                </td>
                                <td class="px-2">
                                    <div style="color: #6b7280;">{{ $po->portOfDestination->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2">
                                    <small class="text-muted">{{ $po->creator->name ?? 'N/A' }}</small>
                                </td>
                                <td class="px-2">
                                    <small class="text-muted">{{ $po->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="px-2">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="action-btn action-btn-view" title="View">
                                            <i class="fas fa-eye" style="font-size: 14px;"></i>
                                        </a>
                                        <a href="{{ route('purchase-orders.edit', $po->id) }}" class="action-btn action-btn-edit" title="Edit">
                                            <i class="fas fa-edit" style="font-size: 14px;"></i>
                                        </a>
                                        <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                                <i class="fas fa-trash" style="font-size: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-shopping-bag fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="mb-0">No purchase orders found.</p>
                                        <small class="text-muted mt-1">Create your first purchase order or adjust filters</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($purchaseOrders->hasPages())
            <div class="card-footer border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $purchaseOrders->firstItem() ?? 0 }} to {{ $purchaseOrders->lastItem() ?? 0 }} of {{ $purchaseOrders->total() }} purchase orders
                    </div>
                    <div>
                        {{ $purchaseOrders->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer border-0 bg-transparent">
                <div class="text-muted small text-center">
                    Showing {{ $purchaseOrders->count() }} of {{ $purchaseOrders->total() }} purchase orders
                </div>
            </div>
        @endif
    </div>
    </div>
</x-app-layout>

<style>
    [x-cloak] { display: none !important; }
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border-radius: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid;
    }
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .action-btn-view {
        border-color: #06b6d4;
        color: #06b6d4;
    }
    .action-btn-view:hover {
        background-color: #06b6d4;
        color: white;
    }
    .action-btn-edit {
        border-color: #800020;
        color: #800020;
    }
    .action-btn-edit:hover {
        background-color: #800020;
        color: white;
    }
    .action-btn-delete {
        border-color: #dc2626;
        color: #dc2626;
    }
    .action-btn-delete:hover {
        background-color: #dc2626;
        color: white;
    }
</style>
