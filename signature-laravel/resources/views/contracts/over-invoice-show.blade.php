<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">{{ $pageTitle }}</h1>
            <p class="text-muted mb-0 small">Contract {{ $contract->contract_number }} — PI total exceeds contract amount</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('contracts.over-invoice') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                <i class="fas fa-arrow-left me-2"></i>Over Invoice List
            </a>
            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-info btn-sm d-flex align-items-center" style="border-radius: 8px;">
                <i class="fas fa-file-contract me-2"></i>Contract
            </a>
            <a href="{{ route('proforma-invoices.index') }}?contract_number={{ urlencode($contract->contract_number) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center" style="border-radius: 8px;">
                <i class="fas fa-file-invoice me-2"></i>Proforma Invoices
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $initialRate = old('usd_inr_rate', $contract->over_invoice_usd_inr_rate !== null ? (string) $contract->over_invoice_usd_inr_rate : '');
    @endphp

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;"
         x-data="overInvoiceDetail({{ (float) $difference_amount }}, @js($initialRate))">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-calculator text-white"></i>
                </div>
                <div>
                    <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Amount difference &amp; conversion</h2>
                    <p class="text-muted small mb-0">Enter current rate (₹ per $), then save — the rupee amount appears on the Over Invoice list.</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted small">Buyer</label>
                    <div class="fw-semibold" style="color: #1f2937;">{{ $contract->buyer_name }}</div>
                    @if($contract->company_name)
                        <div class="text-muted small">{{ $contract->company_name }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium text-muted small">Sales manager</label>
                    <div style="color: #1f2937;">{{ $contract->creator->name ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted small">Contract amount ($)</label>
                    <div class="fw-semibold" style="color: #059669; font-size: 1.15rem;">${{ number_format($contract->total_amount ?? 0, 2) }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted small">Total PI amount ($)</label>
                    <div class="fw-semibold" style="color: #2563eb; font-size: 1.15rem;">${{ number_format($total_pi_amount, 2) }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-muted small">Difference ($)</label>
                    <div class="fw-semibold" style="color: #dc2626; font-size: 1.15rem;">${{ number_format($difference_amount, 2) }}</div>
                </div>
            </div>

            @if($contract->over_invoice_difference_inr !== null)
                <div class="alert alert-light border mb-4 small" style="border-color: #e5e7eb !important;">
                    <strong>Saved on list:</strong> ₹{{ number_format((float) $contract->over_invoice_difference_inr, 2) }}
                    @if($contract->over_invoice_usd_inr_rate !== null)
                        <span class="text-muted">(rate {{ number_format((float) $contract->over_invoice_usd_inr_rate, 4) }} ₹/$)</span>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('contracts.over-invoice.save-inr', $contract) }}" class="border rounded-3 p-4 mb-0" style="background: #f9fafb; border-color: #e5e7eb !important;">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="usdInrRate" class="form-label fw-medium">Current rate (₹ per $)</label>
                        <input type="number"
                               id="usdInrRate"
                               name="usd_inr_rate"
                               x-model="currentRate"
                               min="0"
                               step="0.01"
                               placeholder="e.g. 83.50"
                               class="form-control"
                               style="border-radius: 8px; border: 1px solid #e5e7eb;"
                               required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-medium">Difference in rupees (preview)</label>
                        <div class="fw-bold" style="color: #059669; font-size: 1.35rem;">
                            <span x-text="inrDifferenceLabel"></span>
                        </div>
                        <small class="text-muted">Saved value = difference ($) × rate (rounded to 2 decimals).</small>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto" style="border-radius: 8px;">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function overInvoiceDetail(usdDifference, initialRate) {
            return {
                differenceUsd: parseFloat(usdDifference) || 0,
                currentRate: initialRate !== null && initialRate !== undefined && String(initialRate) !== '' ? String(initialRate) : '',
                get inrDifferenceLabel() {
                    const raw = this.currentRate;
                    if (raw === '' || raw === null || raw === undefined) return '—';
                    const rate = parseFloat(String(raw).trim());
                    if (Number.isNaN(rate) || rate < 0) return '—';
                    return '₹' + (this.differenceUsd * rate).toFixed(2);
                },
            };
        }
    </script>
</x-app-layout>
