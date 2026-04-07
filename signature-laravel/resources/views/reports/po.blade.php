<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">PO Report</h1>
            <p class="text-muted mb-0 small">Filter, sort and export purchase orders</p>
        </div>
    </div>

    @include('reports.partials.filters', [
        'formAction' => route('reports.po'),
        'resetUrl' => route('reports.po'),
        'exportExcelUrl' => route('reports.po.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'sort', 'dir', 'search']), ['format' => 'excel'])),
        'exportPdfUrl' => route('reports.po.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'sort', 'dir', 'search']), ['format' => 'pdf'])),
        'showCreatedBy' => false,
        'sortOptions' => [['value' => 'created_at', 'label' => 'Date'], ['value' => 'purchase_order_number', 'label' => 'PO Number']],
        'reportTitle' => 'PO List',
        'reportTotal' => $purchaseOrders->total(),
    ])

    <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
        <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Purchase Orders <span class="badge ms-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color);">{{ $purchaseOrders->total() }}</span></h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Date</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">PO Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">PI Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Buyer</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Port</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr>
                                <td class="px-2"><small>{{ $po->created_at->format('d M Y') }}</small></td>
                                <td class="px-4 py-3 fw-medium" style="color: #1f2937;">{{ $po->purchase_order_number }}</td>
                                <td class="px-2">{{ $po->proformaInvoice->proforma_invoice_number ?? '—' }}</td>
                                <td class="px-2">{{ $po->buyer_name }}</td>
                                <td class="px-2">{{ $po->portOfDestination->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-5 text-center text-muted">No purchase orders found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($purchaseOrders->hasPages())
            <div class="card-footer border-0 bg-transparent py-2">{{ $purchaseOrders->links() }}</div>
        @endif
    </div>
</x-app-layout>
