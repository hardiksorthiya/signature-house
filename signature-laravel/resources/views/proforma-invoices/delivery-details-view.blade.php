@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <div class="card shadow-sm border-0 mb-4 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="list-header d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-3" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-truck text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="h2 fw-semibold mb-1 text-truncate" style="color: #1f2937;">Delivery Details</h1>
                        <p class="text-muted mb-0 small text-truncate">PI {{ $proformaInvoice->proforma_invoice_number }}
                            @if($proformaInvoice->contract)
                                · Contract {{ $proformaInvoice->contract->contract_number }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('proforma-invoices.delivery-details-index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-list me-2"></i>Delivery list
                    </a>
                    <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-file-invoice me-2"></i>PI details
                    </a>
                    @can('edit proforma invoices')
                        <a href="{{ route('proforma-invoices.delivery-details', $proformaInvoice) }}" class="btn btn-primary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-edit me-2"></i>Edit delivery details
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-file-alt text-white"></i>
                </div>
                <div>
                    <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Delivery documents</h2>
                    <p class="text-muted small mb-0">Read-only summary (same fields as on the add/edit screen)</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                        <tr>
                            <th class="text-center" style="width: 6%;">Status</th>
                            <th class="text-center" style="width: 5%;">S.No</th>
                            <th style="width: 28%;">Delivery document</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 22%;">Number</th>
                            <th style="width: 12%;">No. of copies</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryDocuments as $index => $documentName)
                            @php
                                $row = $existingDetails->get($documentName);
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($row && $row->is_received)
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Received</span>
                                    @elseif($row)
                                        <span class="badge bg-secondary"><i class="fas fa-clock"></i> Pending</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center fw-medium">{{ $index + 1 }}</td>
                                <td class="fw-medium" style="color: #374151;">{{ $documentName }}</td>
                                <td>{{ $row && $row->date ? $row->date->format('d-m-Y') : '—' }}</td>
                                <td>{{ $row && $row->document_number ? $row->document_number : '—' }}</td>
                                <td>{{ $row && $row->no_of_copies !== null && $row->no_of_copies !== '' ? $row->no_of_copies : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-images text-white"></i>
                </div>
                <div>
                    <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Uploaded images</h2>
                    <p class="text-muted small mb-0">Files attached on the delivery details screen</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if(isset($existingImages) && $existingImages->count() > 0)
                <div class="row g-3">
                    @foreach($existingImages as $image)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card border position-relative h-100">
                                <img src="{{ Storage::url($image->file_path) }}"
                                     class="card-img-top"
                                     style="height: 160px; object-fit: cover;"
                                     alt="{{ $image->file_name }}"
                                     onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                <div class="card-body p-2">
                                    <small class="text-muted d-block text-truncate" title="{{ $image->file_name }}">{{ $image->file_name }}</small>
                                    <small class="text-muted">{{ number_format($image->file_size / 1024, 2) }} KB</small>
                                </div>
                                <a href="{{ Storage::url($image->file_path) }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="btn btn-sm btn-outline-info position-absolute top-0 end-0 m-2"
                                   title="Open full size">
                                    <i class="fas fa-expand"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0 text-center py-4">No images uploaded yet.</p>
            @endif
        </div>
    </div>
</x-app-layout>
