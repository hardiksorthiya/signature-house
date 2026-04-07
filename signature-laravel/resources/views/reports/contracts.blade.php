<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Contract Report</h1>
            <p class="text-muted mb-0 small">Filter, sort and export contracts</p>
        </div>
    </div>

    @include('reports.partials.filters', [
        'formAction' => route('reports.contracts'),
        'resetUrl' => route('reports.contracts'),
        'exportExcelUrl' => route('reports.contracts.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'excel'])),
        'exportPdfUrl' => route('reports.contracts.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'pdf'])),
        'creators' => $creators,
        'sortOptions' => [['value' => 'created_at', 'label' => 'Date'], ['value' => 'contract_number', 'label' => 'Contract No']],
        'reportTitle' => 'Contracts List',
        'reportTotal' => $contracts->total(),
    ])

    <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
        <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Contracts <span class="badge ms-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color);">{{ $contracts->total() }}</span></h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Date</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Contract No</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Buyer / Company</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">State / City</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Amount</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Status</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Created by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $c)
                            <tr>
                                <td class="px-2"><small>{{ $c->created_at->format('d M Y') }}</small></td>
                                <td class="px-4 py-3 fw-medium" style="color: #1f2937;">{{ $c->contract_number }}</td>
                                <td class="px-2">{{ $c->company_name ?: $c->buyer_name }}</td>
                                <td class="px-2">{{ ($c->state->name ?? '') . ' / ' . ($c->city->name ?? '—') }}</td>
                                <td class="px-2">{{ format_amount($c->total_amount, 'USD') }}</td>
                                <td class="px-2"><span class="badge bg-{{ $c->approval_status === 'approved' ? 'success' : ($c->approval_status === 'rejected' ? 'danger' : 'secondary') }}">{{ $c->approval_status ?? '—' }}</span></td>
                                <td class="px-2">{{ $c->creator->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">No contracts found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($contracts->hasPages())
            <div class="card-footer border-0 bg-transparent py-2">{{ $contracts->links() }}</div>
        @endif
    </div>
</x-app-layout>
