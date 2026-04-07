<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Payment Report</h1>
            <p class="text-muted mb-0 small">Filter, sort and export payments by payment date and creator</p>
        </div>
    </div>

    @include('reports.partials.filters', [
        'formAction' => route('reports.payments'),
        'resetUrl' => route('reports.payments'),
        'exportExcelUrl' => route('reports.payments.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'excel'])),
        'exportPdfUrl' => route('reports.payments.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'pdf'])),
        'creators' => $creators,
        'sortOptions' => [['value' => 'payment_date', 'label' => 'Payment Date'], ['value' => 'amount', 'label' => 'Amount'], ['value' => 'type', 'label' => 'Type']],
        'defaultSort' => 'payment_date',
        'reportTitle' => 'Payments List',
        'reportTotal' => $payments->total(),
    ])

    <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
        <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Payments <span class="badge ms-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color);">{{ $payments->total() }}</span></h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Payment Date</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Type</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Contract</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">PI Number</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Customer</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Amount</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Method</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Created by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-2"><small>{{ $payment->payment_date->format('d M Y') }}</small></td>
                                <td class="px-2">
                                    @if($payment->type === 'collect')
                                        <span class="badge bg-success">Collect</span>
                                    @else
                                        <span class="badge bg-danger">Return</span>
                                    @endif
                                </td>
                                <td class="px-2">{{ $payment->contract->contract_number ?? ($payment->proformaInvoice->contract->contract_number ?? '—') }}</td>
                                <td class="px-2">{{ $payment->proformaInvoice->proforma_invoice_number ?? '—' }}</td>
                                <td class="px-2">{{ $payment->contract->buyer_name ?? ($payment->proformaInvoice->buyer_company_name ?? '—') }}</td>
                                <td class="px-4 py-3 fw-medium" style="{{ $payment->type === 'collect' ? 'color: #10b981;' : 'color: #dc2626;' }}">
                                    {{ ($payment->payeeCountry && $payment->payeeCountry->currency ? $payment->payeeCountry->currency : '₹') . number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-2">{{ $payment->payment_method ?? '—' }}</td>
                                <td class="px-2">{{ $payment->creator->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-5 text-center text-muted">No payments found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
            <div class="card-footer border-0 bg-transparent py-2">{{ $payments->links() }}</div>
        @endif
    </div>
</x-app-layout>
