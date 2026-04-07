<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.sellers') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Seller Report</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Seller Ledger</h1>
            <p class="text-muted mb-0 small">{{ $seller->seller_name }}</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- 1. Seller Company Details --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-building me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Seller Company Details</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="small text-muted">Company Name</label><div class="fw-semibold">{{ $seller->seller_name ?? '—' }}</div></div>
                        <div class="col-md-6"><label class="small text-muted">PI Short Name</label><div>{{ $seller->pi_short_name ?? '—' }}</div></div>
                        <div class="col-md-6"><label class="small text-muted">Email</label><div>{{ $seller->email ?? '—' }}</div></div>
                        <div class="col-md-6"><label class="small text-muted">Mobile</label><div>{{ $seller->mobile ?? '—' }}</div></div>
                        <div class="col-12"><label class="small text-muted">Address</label><div>{{ $seller->address ?? '—' }}</div></div>
                        <div class="col-md-6"><label class="small text-muted">Country</label><div>{{ $seller->country->name ?? '—' }}</div></div>
                        <div class="col-md-6"><label class="small text-muted">GST No</label><div>{{ $seller->gst_no ?? '—' }}</div></div>
                        @if($seller->bankDetails->isNotEmpty())
                            <div class="col-12">
                                <label class="small text-muted">Bank Details</label>
                                <div class="mt-1">
                                    @foreach($seller->bankDetails as $bank)
                                        <div class="small mb-1">{{ $bank->bank_name ?? '' }} — {{ $bank->account_number ?? '' }} ({{ $bank->ifsc_code ?? '' }})</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Proforma Invoices with Machines --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-file-invoice me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Proforma Invoices & Machines</h2>
                </div>
                <div class="card-body p-0">
                    @if($proformaInvoices->isEmpty())
                        <p class="px-4 py-4 mb-0 text-muted small">No proforma invoices found for this seller in the selected period.</p>
                    @else
                        @php
                            $grandTotalMachines = 0;
                            $grandTotalAmount = 0;
                        @endphp
                        @foreach($proformaInvoices as $pi)
                            <div class="border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 15%, transparent) !important;">
                                <div class="px-4 py-3 bg-light" style="background: color-mix(in srgb, var(--primary-color) 8%, #fff) !important;">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div>
                                            <span class="fw-semibold">{{ $pi->proforma_invoice_number }}</span>
                                            <span class="text-muted ms-2">| Contract: {{ $pi->contract->contract_number ?? '—' }}</span>
                                            <span class="text-muted ms-2">| Buyer: {{ $pi->buyer_company_name ?: ($pi->contract->company_name ?? $pi->contract->buyer_name ?? '—') }}</span>
                                        </div>
                                        <div class="fw-medium" style="color: var(--primary-color);">{{ format_amount($pi->total_amount, $pi->currency ?? 'USD') }}</div>
                                    </div>
                                    <div class="small text-muted mt-1">{{ $pi->created_at->format('d M Y') }}</div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0 align-middle">
                                        <thead style="background: color-mix(in srgb, var(--primary-color) 10%, #fff);">
                                            <tr>
                                                <th class="px-4 py-2 small fw-semibold">Category</th>
                                                <th class="px-4 py-2 small fw-semibold">Brand</th>
                                                <th class="px-4 py-2 small fw-semibold">Model</th>
                                                <th class="px-4 py-2 small fw-semibold text-end">Qty</th>
                                                <th class="px-4 py-2 small fw-semibold text-end">Unit Price</th>
                                                <th class="px-4 py-2 small fw-semibold text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pi->proformaInvoiceMachines as $m)
                                                @php
                                                    $lineTotal = ($m->amount ?? 0) * ($m->quantity ?? 0);
                                                    $grandTotalMachines += $m->quantity ?? 0;
                                                    $grandTotalAmount += $lineTotal;
                                                @endphp
                                                <tr>
                                                    <td class="px-4 py-2">{{ $m->machineCategory->name ?? '—' }}</td>
                                                    <td class="px-4 py-2">{{ $m->brand->name ?? '—' }}</td>
                                                    <td class="px-4 py-2">{{ $m->machineModel->model_no ?? '—' }}</td>
                                                    <td class="px-4 py-2 text-end">{{ $m->quantity ?? 0 }}</td>
                                                    <td class="px-4 py-2 text-end">{{ format_amount($m->amount ?? 0, $pi->currency ?? 'USD') }}</td>
                                                    <td class="px-4 py-2 text-end fw-medium">{{ format_amount($lineTotal, $pi->currency ?? 'USD') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="px-4 py-2 small text-muted">
                                    PI Total: {{ format_amount($pi->total_amount, $pi->currency ?? 'USD') }} | Machines: {{ $pi->proformaInvoiceMachines->sum('quantity') }}
                                </div>
                            </div>
                        @endforeach
                        <div class="px-4 py-3 fw-semibold" style="background: color-mix(in srgb, var(--primary-color) 12%, #fff); border-top: 2px solid color-mix(in srgb, var(--primary-color) 30%, transparent);">
                            Grand Total: {{ $proformaInvoices->count() }} PIs | {{ $grandTotalMachines }} Machines | {{ format_amount($proformaInvoices->sum('total_amount'), $proformaInvoices->first()->currency ?? 'USD') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Filters for ledger --}}
    <div class="card shadow-sm border-0 mt-4" style="border-radius: 12px;">
        <div class="card-header py-2">
            <span class="small fw-medium">Filter PIs by date</span>
        </div>
        <div class="card-body py-2">
            <form method="GET" action="{{ route('reports.seller-ledger', $seller) }}" id="sellerLedgerFilterForm" class="d-flex flex-wrap gap-3 align-items-end">
                <input type="hidden" name="period" id="sellerLedgerPeriod" value="{{ request('period', 'last_month') }}">
                <div>
                    <label class="form-label small mb-0">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="width: 150px;">
                </div>
                <div>
                    <label class="form-label small mb-0">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="width: 150px;">
                </div>
                <div>
                    <label class="form-label small mb-0">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="PI / Contract / Buyer" style="width: 200px;">
                </div>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search me-1"></i>Apply</button>
                <a href="{{ route('reports.seller-ledger', $seller) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('sellerLedgerFilterForm')?.addEventListener('submit', function() {
            const from = this.querySelector('[name=date_from]')?.value;
            const to = this.querySelector('[name=date_to]')?.value;
            const periodField = this.querySelector('#sellerLedgerPeriod');
            if (periodField && from && to) periodField.value = 'custom';
        });
    </script>
</x-app-layout>
