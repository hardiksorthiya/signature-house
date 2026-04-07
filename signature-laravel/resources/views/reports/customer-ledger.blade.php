<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.customers') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Customer Report</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Customer Ledger</h1>
            <p class="text-muted mb-0 small">{{ $contract->contract_number }} — {{ $contract->company_name ?: $contract->buyer_name }}</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- 1. Lead (if from lead) --}}
        @if($contract->lead)
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                    <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                        <i class="fas fa-user-plus me-2" style="color: var(--primary-color);"></i>
                        <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Lead Details</h2>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="small text-muted">Name</label><div class="fw-medium">{{ $contract->lead->name }}</div></div>
                            <div class="col-md-4"><label class="small text-muted">Phone</label><div>{{ $contract->lead->phone_number ?? '—' }}</div></div>
                            <div class="col-md-4"><label class="small text-muted">Business</label><div>{{ $contract->lead->business->name ?? '—' }}</div></div>
                            <div class="col-md-4"><label class="small text-muted">State / City / Area</label><div>{{ ($contract->lead->state->name ?? '') . ' / ' . ($contract->lead->city->name ?? '') . ' / ' . ($contract->lead->area->name ?? '—') }}</div></div>
                            <div class="col-md-4"><label class="small text-muted">Status</label><div>{{ $contract->lead->status->name ?? '—' }}</div></div>
                            <div class="col-md-4"><label class="small text-muted">Brand</label><div>{{ $contract->lead->brand->name ?? $contract->lead->brand_name ?? '—' }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 2. Contract detail --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-file-contract me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Contract Details</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="small text-muted">Contract Number</label><div class="fw-semibold">{{ $contract->contract_number }}</div></div>
                        <div class="col-md-4"><label class="small text-muted">Buyer / Company</label><div>{{ $contract->company_name ?: $contract->buyer_name }}</div></div>
                        <div class="col-md-4"><label class="small text-muted">Phone / Email</label><div>{{ $contract->phone_number ?? '—' }} / {{ $contract->email ?? '—' }}</div></div>
                        <div class="col-md-4"><label class="small text-muted">Location</label><div>{{ ($contract->area->name ?? '') . ', ' . ($contract->city->name ?? '') . ', ' . ($contract->state->name ?? '—') }}</div></div>
                        <div class="col-md-4"><label class="small text-muted">Total Amount</label><div class="fw-semibold text-primary">{{ format_amount($contract->total_amount, 'USD') }}</div></div>
                        <div class="col-md-4"><label class="small text-muted">Status</label><div><span class="badge bg-{{ $contract->approval_status === 'approved' ? 'success' : ($contract->approval_status === 'rejected' ? 'danger' : 'secondary') }}">{{ $contract->approval_status ?? '—' }}</span></div></div>
                        <div class="col-md-4"><label class="small text-muted">Created By</label><div>{{ $contract->creator->name ?? '—' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Payment transactions --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-money-bill-wave me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Payment Transactions</h2>
                </div>
                <div class="card-body p-0">
                    @if($payments->isEmpty())
                        <p class="px-4 py-4 mb-0 text-muted small">No payments recorded for this customer.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: color-mix(in srgb, var(--primary-color) 10%, #fff);">
                                    <tr>
                                        <th class="px-4 py-2 small fw-semibold">Date</th>
                                        <th class="px-4 py-2 small fw-semibold">Type</th>
                                        <th class="px-4 py-2 small fw-semibold">PI Number</th>
                                        <th class="px-4 py-2 small fw-semibold">Amount</th>
                                        <th class="px-4 py-2 small fw-semibold">Method</th>
                                        <th class="px-4 py-2 small fw-semibold">Transaction ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $p)
                                        <tr>
                                            <td class="px-4 py-2">{{ $p->payment_date->format('d M Y') }}</td>
                                            <td class="px-4 py-2"><span class="badge bg-{{ $p->type === 'collect' ? 'success' : 'danger' }}">{{ ucfirst($p->type ?? '—') }}</span></td>
                                            <td class="px-4 py-2">{{ $p->proformaInvoice->proforma_invoice_number ?? '—' }}</td>
                                            <td class="px-4 py-2 fw-medium">{{ ($p->payeeCountry && $p->payeeCountry->currency ? $p->payeeCountry->currency : '₹') . number_format($p->amount, 2) }}</td>
                                            <td class="px-4 py-2">{{ $p->payment_method ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $p->transaction_id ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 4. PI details --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-file-invoice me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Proforma Invoices (PI)</h2>
                </div>
                <div class="card-body p-0">
                    @if($contract->proformaInvoices->isEmpty())
                        <p class="px-4 py-4 mb-0 text-muted small">No proforma invoices for this contract.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: color-mix(in srgb, var(--primary-color) 10%, #fff);">
                                    <tr>
                                        <th class="px-4 py-2 small fw-semibold">PI Number</th>
                                        <th class="px-4 py-2 small fw-semibold">Buyer / Company</th>
                                        <th class="px-4 py-2 small fw-semibold">Seller</th>
                                        <th class="px-4 py-2 small fw-semibold">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->proformaInvoices as $pi)
                                        <tr>
                                            <td class="px-4 py-2 fw-medium">{{ $pi->proforma_invoice_number }}</td>
                                            <td class="px-4 py-2">{{ $pi->buyer_company_name ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $pi->seller->seller_name ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ format_amount($pi->total_amount, $pi->currency) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 5. PO details --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-shopping-cart me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Purchase Orders (PO)</h2>
                </div>
                <div class="card-body p-0">
                    @php $allPos = $contract->proformaInvoices->flatMap->purchaseOrders; @endphp
                    @if($allPos->isEmpty())
                        <p class="px-4 py-4 mb-0 text-muted small">No purchase orders for this contract.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: color-mix(in srgb, var(--primary-color) 10%, #fff);">
                                    <tr>
                                        <th class="px-4 py-2 small fw-semibold">PO Number</th>
                                        <th class="px-4 py-2 small fw-semibold">PI Number</th>
                                        <th class="px-4 py-2 small fw-semibold">Buyer</th>
                                        <th class="px-4 py-2 small fw-semibold">Port</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPos as $po)
                                        <tr>
                                            <td class="px-4 py-2 fw-medium">{{ $po->purchase_order_number }}</td>
                                            <td class="px-4 py-2">{{ $po->proformaInvoice->proforma_invoice_number ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $po->buyer_name }}</td>
                                            <td class="px-4 py-2">{{ $po->portOfDestination->name ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 6. MS Unloading details (Pre Erection, Image Upload, Damage, Serial Number, Machine Erection, IA Fitting, Spare List) --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-truck-loading me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">MS Unloading Details</h2>
                </div>
                <div class="card-body">
                    @if($contract->proformaInvoices->isEmpty())
                        <p class="mb-0 text-muted small">No PI linked; no MS unloading data.</p>
                    @else
                        @foreach($contract->proformaInvoices as $pi)
                            <div class="border rounded mb-4 p-3" style="border-color: color-mix(in srgb, var(--primary-color) 25%, #eee) !important;">
                                <h3 class="h6 fw-semibold mb-3" style="color: var(--primary-color);">PI: {{ $pi->proforma_invoice_number }}</h3>

                                <div class="mb-3">
                                    <h4 class="small fw-semibold text-muted mb-2"><i class="fas fa-tools me-1"></i>Pre Erection</h4>
                                    @if($pi->preErectionDetails->isEmpty())
                                        <p class="small text-muted mb-0">No records.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead><tr><th class="small">Specification</th><th class="small">Details</th><th class="small">Completed</th></tr></thead>
                                                <tbody>
                                                    @foreach($pi->preErectionDetails as $row)
                                                        <tr><td>{{ $row->technical_specification ?? '—' }}</td><td>{{ $row->details ?? '—' }}</td><td>{{ $row->is_completed ? 'Yes' : 'No' }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <h4 class="small fw-semibold text-muted mb-2"><i class="fas fa-images me-1"></i>Image Upload</h4>
                                    @if($pi->msUnloadingImages->isEmpty())
                                        <p class="small text-muted mb-0">No images.</p>
                                    @else
                                        <ul class="small mb-0 ps-3">
                                            @foreach($pi->msUnloadingImages as $img)
                                                <li>{{ $img->file_name }}</li>
                                            @endforeach
                                        </ul>
                                        @can('view image uploading')
                                            <a href="{{ route('ms-unloading-images.show', $pi) }}" class="btn btn-sm btn-outline-info mt-1">View / Upload</a>
                                        @endcan
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <h4 class="small fw-semibold text-muted mb-2"><i class="fas fa-exclamation-triangle me-1"></i>Damage</h4>
                                    @if($pi->damageDetails->isEmpty())
                                        <p class="small text-muted mb-0">No records.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead><tr><th class="small">Title</th><th class="small">Detail</th></tr></thead>
                                                <tbody>
                                                    @foreach($pi->damageDetails as $row)
                                                        <tr><td>{{ $row->title ?? '—' }}</td><td>{{ $row->detail ?? '—' }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <h4 class="small fw-semibold text-muted mb-2"><i class="fas fa-hashtag me-1"></i>Serial Number</h4>
                                    @if($pi->serialNumbers->isEmpty())
                                        <p class="small text-muted mb-0">No records.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead><tr><th class="small">Machine category</th><th class="small">Serial number</th><th class="small">Khata number</th></tr></thead>
                                                <tbody>
                                                    @foreach($pi->serialNumbers as $row)
                                                        <tr><td>{{ $row->machineCategory->name ?? '—' }}</td><td>{{ $row->serial_number ?? '—' }}</td><td>{{ $row->khata_number ?? '—' }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <h4 class="small fw-semibold text-muted mb-2"><i class="fas fa-cogs me-1"></i>Machine Erection</h4>
                                    @if($pi->machineErectionDetails->isEmpty())
                                        <p class="small text-muted mb-0">No records.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead><tr><th class="small">Machine category</th><th class="small">Machine no</th><th class="small">Point to follow</th><th class="small">Date</th></tr></thead>
                                                <tbody>
                                                    @foreach($pi->machineErectionDetails as $row)
                                                        <tr><td>{{ $row->machineCategory->name ?? '—' }}</td><td>{{ $row->machine_number ?? '—' }}</td><td>{{ $row->point_to_follow ?? '—' }}</td><td>{{ $row->date ? $row->date->format('d M Y') : '—' }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <h4 class="small fw-semibold text-muted mb-2"><i class="fas fa-wrench me-1"></i>IA Fitting</h4>
                                    @if($pi->iaFittingDetails->isEmpty())
                                        <p class="small text-muted mb-0">No records.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead><tr><th class="small">Machine category</th><th class="small">Machine no</th><th class="small">Detail name</th><th class="small">Value</th></tr></thead>
                                                <tbody>
                                                    @foreach($pi->iaFittingDetails as $row)
                                                        <tr><td>{{ $row->machineCategory->name ?? '—' }}</td><td>{{ $row->machine_number ?? '—' }}</td><td>{{ $row->detail_name ?? '—' }}</td><td>{{ $row->value ?? '—' }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-0">
                                    <h4 class="small fw-semibold text-muted mb-2"><i class="fas fa-list-alt me-1"></i>Spare List</h4>
                                    @if($pi->piSpareLists->isEmpty())
                                        <p class="small text-muted mb-0">No records.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead><tr><th class="small">Document / Item</th><th class="small">Quantity</th><th class="small">Fulfilled</th></tr></thead>
                                                <tbody>
                                                    @foreach($pi->piSpareLists as $row)
                                                        <tr><td>{{ $row->document_name ?? '—' }}</td><td>{{ $row->quantity ?? '—' }}</td><td>{{ $row->is_fulfilled ? 'Yes' : 'No' }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @can('view spare list')
                                            <a href="{{ route('ms-unloading-spare-list.show', $pi) }}" class="btn btn-sm btn-outline-secondary mt-1">View / Edit</a>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- 7. Complaints --}}
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%);">
                <div class="card-header border-0 py-3 d-flex align-items-center" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
                    <i class="fas fa-tools me-2" style="color: var(--primary-color);"></i>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Complaints</h2>
                </div>
                <div class="card-body p-0">
                    @if($contract->complaints->isEmpty())
                        <p class="px-4 py-4 mb-0 text-muted small">No complaints for this contract.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: color-mix(in srgb, var(--primary-color) 10%, #fff);">
                                    <tr>
                                        <th class="px-4 py-2 small fw-semibold">Type</th>
                                        <th class="px-4 py-2 small fw-semibold">Machine category</th>
                                        <th class="px-4 py-2 small fw-semibold">Status</th>
                                        <th class="px-4 py-2 small fw-semibold">Spares used</th>
                                        <th class="px-4 py-2 small fw-semibold">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->complaints as $complaint)
                                        <tr>
                                            <td class="px-4 py-2">{{ $complaint->complainType->name ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $complaint->machineCategory->name ?? '—' }}</td>
                                            <td class="px-4 py-2"><span class="badge bg-{{ $complaint->status === 'Completed' ? 'success' : 'warning' }}">{{ $complaint->status ?? '—' }}</span></td>
                                            <td class="px-4 py-2">{{ $complaint->spares->count() ? $complaint->spares->map(fn($s) => $s->name . ' (×' . $s->pivot->quantity . ')')->join(', ') : '—' }}</td>
                                            <td class="px-4 py-2">{{ $complaint->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
