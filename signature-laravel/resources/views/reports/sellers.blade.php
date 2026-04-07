<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Seller Report</h1>
            <p class="text-muted mb-0 small">Seller company details, machines sold, prices and totals</p>
        </div>
    </div>

    @include('reports.partials.filters', [
        'formAction' => route('reports.sellers'),
        'resetUrl' => route('reports.sellers'),
        'exportExcelUrl' => route('reports.sellers.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'sort', 'dir', 'search', 'seller_id']), ['format' => 'excel'])),
        'exportPdfUrl' => route('reports.sellers.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'sort', 'dir', 'search', 'seller_id']), ['format' => 'pdf'])),
        'showCreatedBy' => false,
        'showSeller' => true,
        'allSellers' => $allSellers,
        'sortOptions' => [
            ['value' => 'seller_name', 'label' => 'Seller Name'],
            ['value' => 'pi_count', 'label' => 'PI Count'],
            ['value' => 'total_machines', 'label' => 'Total Machines'],
            ['value' => 'total_amount', 'label' => 'Total Amount'],
        ],
        'defaultSort' => 'seller_name',
        'reportTitle' => 'Seller List',
        'reportTotal' => $sellers->total(),
    ])

    <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
        <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Sellers <span class="badge ms-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color);">{{ $sellers->total() }}</span></h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Seller Company</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Email</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Mobile</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">PI Count</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Total Machines</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Total Amount</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellers as $item)
                            @php $s = $item->seller; @endphp
                            <tr>
                                <td class="px-4 py-3 fw-medium" style="color: #1f2937;">{{ $s->seller_name ?? '—' }}</td>
                                <td class="px-2"><small>{{ $s->email ?? '—' }}</small></td>
                                <td class="px-2"><small>{{ $s->mobile ?? '—' }}</small></td>
                                <td class="px-2">{{ $item->pi_count }}</td>
                                <td class="px-2">{{ $item->total_machines }}</td>
                                <td class="px-2">{{ format_amount($item->total_amount, $item->currency) }}</td>
                                <td class="px-2">
                                    <a href="{{ route('reports.seller-ledger', ['seller' => $s->id] + request()->only(['period', 'date_from', 'date_to', 'search'])) }}" class="btn btn-sm btn-outline-info" title="View full details">
                                        <i class="fas fa-eye me-1"></i>Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">No sellers found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sellers->hasPages())
            <div class="card-footer border-0 bg-transparent py-2">{{ $sellers->links() }}</div>
        @endif
    </div>
</x-app-layout>
