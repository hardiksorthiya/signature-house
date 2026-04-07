<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Pre Errection Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pre-erection.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>View PI
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-tools text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Technical Specifications</h2>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('pre-erection.store', $proformaInvoice) }}">
                @csrf
                
                <!-- Pre Errection Details Table -->
                @if($existingDetails->count() > 0)
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                            <tr>
                                <th style="width: 8%;" class="text-center">Sno</th>
                                <th style="width: 32%;">Technical Specification</th>
                                <th style="width: 50%;">Details</th>
                                <th style="width: 10%;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($existingDetails as $index => $detail)
                                @php
                                    $specIndex = array_search($detail->technical_specification, $technicalSpecifications);
                                    if ($specIndex === false) $specIndex = $index;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center align-items-center" style="min-height: 38px;">
                                            <input type="checkbox" 
                                                   name="pre_erection_details[{{ $specIndex }}][is_completed]" 
                                                   value="1"
                                                   {{ $detail->is_completed ? 'checked' : '' }}
                                                   class="form-check-input" 
                                                   style="width: 20px; height: 20px; cursor: pointer; margin: 0;">
                                        </div>
                                        <input type="hidden" name="pre_erection_details[{{ $specIndex }}][technical_specification]" value="{{ $detail->technical_specification }}">
                                    </td>
                                    <td class="fw-medium" style="color: #374151; vertical-align: middle;">
                                        {{ $detail->technical_specification }}
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <input type="text" 
                                               name="pre_erection_details[{{ $specIndex }}][details]" 
                                               value="{{ $detail->details }}"
                                               placeholder="Enter Details" 
                                               class="form-control form-control-sm" 
                                               style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        @if($detail->is_completed)
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>No pre-errection details have been added yet. Use the form below to add new details.
                </div>
                @endif

                <!-- Add New Pre Errection Details -->
                <div class="mt-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                        <i class="fas fa-plus-circle me-2"></i>Add New Pre Errection Details
                    </h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 8%;" class="text-center">Sno</th>
                                    <th style="width: 32%;">Technical Specification</th>
                                    <th style="width: 60%;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($technicalSpecifications as $index => $specification)
                                    @php
                                        $existingDetail = $existingDetails->get($specification);
                                    @endphp
                                    @if(!$existingDetail)
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center align-items-center" style="min-height: 38px;">
                                                <input type="checkbox" 
                                                       name="pre_erection_details[{{ $index }}][is_completed]" 
                                                       value="1"
                                                       class="form-check-input" 
                                                       style="width: 20px; height: 20px; cursor: pointer; margin: 0;">
                                            </div>
                                            <input type="hidden" name="pre_erection_details[{{ $index }}][technical_specification]" value="{{ $specification }}">
                                        </td>
                                        <td class="fw-medium" style="color: #374151; vertical-align: middle;">
                                            {{ $specification }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <input type="text" 
                                                   name="pre_erection_details[{{ $index }}][details]" 
                                                   value=""
                                                   placeholder="Enter Details" 
                                                   class="form-control form-control-sm" 
                                                   style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('pre-erection.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Pre Errection Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

