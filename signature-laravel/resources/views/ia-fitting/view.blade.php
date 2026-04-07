<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">IA Fitting Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
            <p class="text-muted mb-0">Seller: {{ $proformaInvoice->seller->seller_name ?? 'N/A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ia-fitting.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            @canany(['create ia fitting', 'edit ia fitting'])
            <a href="{{ route('ia-fitting.show', $proformaInvoice) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            @endcanany
        </div>
    </div>

    @if($allSerialNumbers->count() > 0)
        @php
            // Group serial numbers by category
            $serialNumbersGrouped = $allSerialNumbers->groupBy('machine_category_id');
        @endphp
        
        @foreach($serialNumbersGrouped as $categoryId => $serialNumbers)
            @php
                $category = $serialNumbers->first()->machineCategory ?? null;
                $machineNum = 1;
            @endphp
            
            <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-wrench text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">{{ $category->name ?? 'Unknown Category' }}</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    @foreach($serialNumbers as $serialNumber)
                        @php
                            $machineKey = $categoryId . '_' . $machineNum;
                            $machineDetails = $existingDetailsByMachine->get($machineKey);
                        @endphp
                        
                        @if($machineDetails && $machineDetails->count() > 0)
                            <div class="mb-4">
                                <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">
                                    <i class="fas fa-cog me-2"></i>Machine {{ $machineNum }} - Serial: {{ $serialNumber->serial_number }} | Khata: {{ $serialNumber->khata_number }}
                                </h5>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-hover">
                                        <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                            <tr>
                                                <th style="width: 8%;" class="text-center">No</th>
                                                <th style="width: 25%;">Detail</th>
                                                <th style="width: 67%;">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($defaultDetails as $detailIndex => $detail)
                                                @php
                                                    $detailKey = $machineKey . '_' . $detail['name'];
                                                    $existingDetail = $existingDetails->get($detailKey);
                                                    $existingValue = $existingDetail ? $existingDetail->first()->value : '';
                                                @endphp
                                                @if($existingValue)
                                                    <tr>
                                                        <td class="text-center fw-medium" style="vertical-align: middle;">
                                                            {{ $detailIndex + 1 }}
                                                        </td>
                                                        <td class="fw-medium" style="vertical-align: middle; color: #374151;">
                                                            {{ $detail['name'] }}
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            @if($detail['type'] === 'radio')
                                                                @if($existingValue === 'OK')
                                                                    <span class="badge bg-success">OK</span>
                                                                @elseif($existingValue === 'Not OK')
                                                                    <span class="badge bg-danger">Not OK</span>
                                                                @else
                                                                    {{ $existingValue }}
                                                                @endif
                                                            @else
                                                                {{ $existingValue }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        
                        @php $machineNum++; @endphp
                    @endforeach
                    
                    @php
                        $hasAnyDetails = false;
                        $tempMachineNum = 1;
                        foreach ($serialNumbers as $serialNumber) {
                            $machineKey = $categoryId . '_' . $tempMachineNum;
                            $machineDetails = $existingDetailsByMachine->get($machineKey);
                            if ($machineDetails && $machineDetails->count() > 0) {
                                $hasAnyDetails = true;
                                break;
                            }
                            $tempMachineNum++;
                        }
                    @endphp
                    
                    @if(!$hasAnyDetails)
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-muted mb-3" style="opacity: 0.3;"></i>
                            <p class="text-muted mb-0">No IA fitting details added yet for this category.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted">No machines found with serial number and khata number for this Proforma Invoice.</p>
                <p class="text-muted small">Please add serial numbers and khata numbers first.</p>
            </div>
        </div>
    @endif
</x-app-layout>
