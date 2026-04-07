<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Spare Used Report</h1>
            <p class="text-muted mb-0 small">Filter and export spares used in complaints by date used and creator</p>
        </div>
    </div>

    @include('reports.partials.filters', [
        'formAction' => route('reports.spare-used'),
        'resetUrl' => route('reports.spare-used'),
        'exportExcelUrl' => route('reports.spare-used.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'excel'])),
        'exportPdfUrl' => route('reports.spare-used.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'pdf'])),
        'creators' => $creators,
        'sortOptions' => [
            ['value' => 'used_at', 'label' => 'Date used'],
            ['value' => 'spare_name', 'label' => 'Spare name'],
            ['value' => 'quantity', 'label' => 'Quantity'],
            ['value' => 'contract_number', 'label' => 'Contract'],
        ],
        'defaultSort' => 'used_at',
        'reportTitle' => 'Spare Used List',
        'reportTotal' => $usages->total(),
    ])

    <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
        <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Spare used <span class="badge ms-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color);">{{ $usages->total() }}</span></h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Date used</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Spare name</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Quantity</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Contract</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Customer</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Created by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usages as $u)
                            <tr>
                                <td class="px-2"><small>{{ $u->used_at ? \Carbon\Carbon::parse($u->used_at)->format('d M Y') : '—' }}</small></td>
                                <td class="px-2">{{ $u->spare_name ?? '—' }}</td>
                                <td class="px-2">{{ $u->quantity ?? 0 }}</td>
                                <td class="px-2">{{ $u->contract_number ?? '—' }}</td>
                                <td class="px-2">{{ $u->company_name ?: ($u->buyer_name ?? '—') }}</td>
                                <td class="px-2">{{ $u->created_by_name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-5 text-center text-muted">No spare usages found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($usages->hasPages())
            <div class="card-footer border-0 bg-transparent py-2">{{ $usages->links() }}</div>
        @endif
    </div>
</x-app-layout>
