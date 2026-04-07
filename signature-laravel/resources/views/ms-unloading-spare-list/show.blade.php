<x-app-layout>
    <div x-data="spareListForm()">
        {{-- Page header --}}
        <div class="card shadow-sm border-0 mb-4 list-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px; min-width: 0;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="list-header-title-row d-flex align-items-center flex-wrap gap-2" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; min-width: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-list-alt text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="h2 fw-semibold mb-1 text-truncate" style="color: #1f2937;">Spare List Details</h1>
                            <p class="text-muted mb-0 small">Delivery Documents & Quantity for PI {{ $proformaInvoice->proforma_invoice_number }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0 mt-2 mt-lg-0">
                        <a href="{{ route('ms-unloading-spare-list.index') }}" class="btn btn-outline-secondary d-flex align-items-center" style="border-radius: 8px;">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Read-only PI / Contract / Buyer row --}}
        <div class="card shadow-sm border-0 mb-4" style="background: #ffffff; border-radius: 12px;">
            <div class="card-body py-3 px-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">PI Number</label>
                        <input type="text" class="form-control" value="{{ $proformaInvoice->proforma_invoice_number }}" readonly style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Contract Number</label>
                        <input type="text" class="form-control" value="{{ $proformaInvoice->contract->contract_number ?? 'N/A' }}" readonly style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Buyer Company Name</label>
                        <input type="text" class="form-control" value="{{ $proformaInvoice->buyer_company_name ?? 'N/A' }}" readonly style="background-color: #f3f4f6; border: 1px solid #d1d5db;">
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('ms-unloading-spare-list.store', $proformaInvoice) }}" method="POST">
            @csrf

            {{-- Delivery Documents table --}}
            <div class="card shadow-sm border-0 mb-4" style="background: #ffffff; border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Delivery Documents & Quantity</h2>
                    <span class="badge bg-secondary" title="Check the Sno box when an item is fulfilled">Check row = fulfilled</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 50px;" title="Check when fulfilled">Sno</th>
                                    <th>Delivery Documents</th>
                                    <th style="width: 180px;">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fixedRows as $idx => $row)
                                <tr>
                                    <td class="align-middle text-center">
                                        <input type="checkbox" class="form-check-input" name="spares[{{ $idx }}][check]" value="1" formnovalidate style="cursor: pointer;" title="Fulfilled" {{ !empty($row['is_fulfilled']) ? 'checked' : '' }}>
                                    </td>
                                    <td class="align-middle">
                                        <input type="hidden" name="spares[{{ $idx }}][document_name]" value="{{ $row['document_name'] }}">
                                        <input type="hidden" name="spares[{{ $idx }}][is_custom]" value="0">
                                        <span>{{ $row['document_name'] }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="spares[{{ $idx }}][quantity]" class="form-control" placeholder="Number" value="{{ old('spares.'.$idx.'.quantity', $row['quantity']) }}" style="border-radius: 6px;">
                                    </td>
                                </tr>
                                @endforeach

                                {{-- Custom rows (from DB) --}}
                                @php $customStart = count($fixedRows); @endphp
                                @foreach($customRows as $cIdx => $custom)
                                <tr>
                                    <td class="align-middle text-center">
                                        <input type="checkbox" class="form-check-input" name="spares[{{ $customStart + $cIdx }}][check]" value="1" formnovalidate style="cursor: pointer;" title="Fulfilled" {{ $custom->is_fulfilled ? 'checked' : '' }}>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="spares[{{ $customStart + $cIdx }}][document_name]" class="form-control" value="{{ old('spares.'.($customStart + $cIdx).'.document_name', $custom->document_name) }}" placeholder="Delivery document name" style="border-radius: 6px;">
                                        <input type="hidden" name="spares[{{ $customStart + $cIdx }}][is_custom]" value="1">
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" name="spares[{{ $customStart + $cIdx }}][quantity]" class="form-control" placeholder="Number" value="{{ old('spares.'.($customStart + $cIdx).'.quantity', $custom->quantity) }}" style="border-radius: 6px;">
                                    </td>
                                </tr>
                                @endforeach

                                {{-- Alpine: new rows added via "Add more" --}}
                                <template x-for="(extra, idx) in newCustomRows" :key="'new-' + idx">
                                    <tr>
                                        <td class="align-middle text-center">
                                            <input type="checkbox" :name="'spares[' + (fixedCount + existingCustomCount + idx) + '][check]'" value="1" class="form-check-input me-1" title="Fulfilled" style="cursor: pointer;">
                                            <button type="button" @click="removeNewRow(idx)" class="btn btn-sm btn-outline-danger p-1" title="Remove row"><i class="fas fa-times"></i></button>
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" :name="'spares[' + (fixedCount + existingCustomCount + idx) + '][document_name]'" class="form-control" x-model="extra.document_name" placeholder="Delivery document name" style="border-radius: 6px;">
                                            <input type="hidden" :name="'spares[' + (fixedCount + existingCustomCount + idx) + '][is_custom]'" value="1">
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" :name="'spares[' + (fixedCount + existingCustomCount + idx) + '][quantity]'" class="form-control" x-model="extra.quantity" placeholder="Number" style="border-radius: 6px;">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer border-0 bg-light py-3 px-4">
                    <button type="button" @click="addNewRow()" class="btn btn-outline-primary d-inline-flex align-items-center" style="border-radius: 8px;">
                        <i class="fas fa-plus me-2"></i>Add more
                    </button>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center" style="border-radius: 8px;">
                    <i class="fas fa-save me-2"></i>Save Spare List
                </button>
                <a href="{{ route('ms-unloading-spare-list.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center" style="border-radius: 8px;">Cancel</a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <script>
        function spareListForm() {
            return {
                fixedCount: {{ count($fixedRows) }},
                existingCustomCount: {{ count($customRows) }},
                newCustomRows: [],
                addNewRow() {
                    this.newCustomRows.push({ document_name: '', quantity: '' });
                },
                removeNewRow(idx) {
                    this.newCustomRows.splice(idx, 1);
                }
            };
        }
    </script>
</x-app-layout>
