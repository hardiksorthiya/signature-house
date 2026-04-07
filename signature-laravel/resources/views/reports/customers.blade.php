<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Customer Report</h1>
            <p class="text-muted mb-0 small">Select a customer (contract) to view full ledger: leads, contract, payments, PI, PO, MS unloading, complaints</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
        <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Select Customer (Contract)</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.customers') }}" class="d-flex flex-wrap gap-2 align-items-end mb-4">
                <div class="flex-grow-1" style="min-width: 200px;">
                    <label class="form-label small fw-medium text-muted">Search by contract number or buyer / company</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contract number, buyer name, company…" class="form-control" style="border-radius: 8px;">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Search</button>
                @if(request('search'))
                    <a href="{{ route('reports.customers') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Contract No</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Buyer / Company</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Location</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Amount</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Status</th>
                            <th class="px-4 py-3   small fw-semibold text-center" style="color: var(--primary-color);">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $c)
                            <tr>
                                <td class="px-4 py-3 fw-medium" style="color: #1f2937;">{{ $c->contract_number }}</td>
                                <td class="px-2">{{ $c->company_name ?: $c->buyer_name }}</td>
                                <td class="px-2">{{ ($c->state->name ?? '') . ' / ' . ($c->city->name ?? '—') }}</td>
                                <td class="px-2">{{ format_amount($c->total_amount, 'USD') }}</td>
                                <td class="px-2"><span class="badge bg-{{ $c->approval_status === 'approved' ? 'success' : ($c->approval_status === 'rejected' ? 'danger' : 'secondary') }}">{{ $c->approval_status ?? '—' }}</span></td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('reports.customer-ledger', $c) }}" class="btn btn-sm btn-primary"><i class="fas fa-book me-1"></i>View Ledger</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-5 text-center text-muted">No contracts found. Try a different search or clear the filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($contracts->hasPages())
                <div class="mt-3">{{ $contracts->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
