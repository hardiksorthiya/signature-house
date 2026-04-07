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
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>View PI
            </a>
        </div>
    </div>

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

    @if($allSerialNumbers->count() > 0)
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-wrench text-white"></i>
                    </div>
                    <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Select Machine</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-medium" style="color: #374151;">Select Machine Serial Number</label>
                        <select id="serial_number_select" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">-- Select Serial Number --</option>
                            @foreach($serialNumbersByCategory as $categoryId => $serialNumbers)
                                <optgroup label="{{ $serialNumbers->first()->machineCategory->name ?? 'Unknown Category' }}">
                                    @foreach($serialNumbers as $serialNumber)
                                        <option value="{{ $serialNumber->id }}" 
                                                data-category-id="{{ $serialNumber->machine_category_id }}"
                                                data-serial-number="{{ $serialNumber->serial_number }}"
                                                data-khata-number="{{ $serialNumber->khata_number }}"
                                                {{ session('selected_serial_number_id') == $serialNumber->id ? 'selected' : '' }}>
                                            Serial: {{ $serialNumber->serial_number }} | Khata: {{ $serialNumber->khata_number }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('ia-fitting.store', $proformaInvoice) }}" id="mainIAFittingForm" style="display: none;">
            @csrf
            <input type="hidden" name="serial_number_id" id="hidden_serial_number_id" value="">
            
            <div id="machine_form_container"></div>
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('ia-fitting.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save IA Fitting Details
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted">No machines found with serial number and khata number for this Proforma Invoice.</p>
                <p class="text-muted small">Please add serial numbers and khata numbers first.</p>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serialNumberSelect = document.getElementById('serial_number_select');
            const formContainer = document.getElementById('machine_form_container');
            const mainForm = document.getElementById('mainIAFittingForm');
            const hiddenSerialNumberId = document.getElementById('hidden_serial_number_id');
            
            // Get existing details from server (passed via PHP) - convert to object keyed by category_machine_detail
            const existingDetails = {};
            @php
                foreach ($existingDetails as $key => $details) {
                    $parts = explode('_', $key, 3);
                    if (count($parts) >= 3) {
                        $categoryId = $parts[0];
                        $machineNum = $parts[1];
                        $detailName = $parts[2];
                        $value = $details->first()->value ?? '';
                        $jsKey = $categoryId . '_' . $machineNum . '_' . $detailName;
                        echo "existingDetails['" . $jsKey . "'] = " . json_encode($value) . ";\n";
                    }
                }
            @endphp
            @php
                $serialNumbersData = $allSerialNumbers->map(function($sn) {
                    return [
                        'id' => $sn->id,
                        'category_id' => $sn->machine_category_id,
                        'serial_number' => $sn->serial_number,
                        'khata_number' => $sn->khata_number,
                        'category_name' => $sn->machineCategory->name ?? 'Unknown'
                    ];
                })->toArray();
            @endphp
            const defaultDetails = @json($defaultDetails);
            const serialNumbers = @json($serialNumbersData);
            
            // Helper function to escape HTML for safe insertion into attributes
            const escapeHtml = (str) => {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            };
            
            // Store machine numbers per category - must match backend logic
            // Backend orders by ID within category, then assigns machine_number based on position
            const machineNumbers = {};
            
            // Group serial numbers by category
            const serialNumbersByCategory = {};
            serialNumbers.forEach((sn) => {
                if (!serialNumbersByCategory[sn.category_id]) {
                    serialNumbersByCategory[sn.category_id] = [];
                }
                serialNumbersByCategory[sn.category_id].push(sn);
            });
            
            // For each category, sort by ID and assign machine numbers
            Object.keys(serialNumbersByCategory).forEach((categoryId) => {
                const categorySerialNumbers = serialNumbersByCategory[categoryId];
                // Sort by ID to match backend logic (orderBy('id'))
                categorySerialNumbers.sort((a, b) => a.id - b.id);
                
                // Assign machine numbers (1, 2, 3, etc.) based on position in sorted array
                categorySerialNumbers.forEach((sn, index) => {
                    if (!machineNumbers[categoryId]) {
                        machineNumbers[categoryId] = [];
                    }
                    machineNumbers[categoryId].push({
                        id: sn.id,
                        number: index + 1, // Machine number starts from 1
                        serial_number: sn.serial_number,
                        khata_number: sn.khata_number
                    });
                });
            });
            
            serialNumberSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const serialNumberId = this.value;
                
                if (!serialNumberId) {
                    formContainer.innerHTML = '';
                    mainForm.style.display = 'none';
                    hiddenSerialNumberId.value = '';
                    return;
                }
                
                const categoryId = selectedOption.getAttribute('data-category-id');
                const serialNumber = selectedOption.getAttribute('data-serial-number');
                const khataNumber = selectedOption.getAttribute('data-khata-number');
                
                // Find machine number for this serial number
                const machineData = machineNumbers[categoryId]?.find(m => m.id == serialNumberId);
                const machineNumber = machineData ? machineData.number : 1;
                
                hiddenSerialNumberId.value = serialNumberId;
                
                // Build form HTML
                let formHTML = `
                    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                        <div class="card-header border-0 p-0" style="background: transparent;">
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                    <i class="fas fa-wrench text-white"></i>
                                </div>
                                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Machine ${machineNumber} - Serial: ${serialNumber} | Khata: ${khataNumber}</h2>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                        <tr>
                                            <th style="width: 8%;" class="text-center">No</th>
                                            <th style="width: 25%;">Detail</th>
                                            <th style="width: 67%;">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                
                defaultDetails.forEach((detail, detailIndex) => {
                    const detailKey = `${categoryId}_${machineNumber}_${detail.name}`;
                    const existingValue = existingDetails[detailKey] || '';
                    const escapedValue = escapeHtml(existingValue);
                    
                    formHTML += `
                        <tr>
                            <td class="text-center fw-medium" style="vertical-align: middle;">
                                ${detailIndex + 1}
                            </td>
                            <td class="fw-medium" style="vertical-align: middle; color: #374151;">
                                ${detail.name}
                            </td>
                            <td style="vertical-align: middle;">`;
                    
                    if (detail.type === 'text') {
                        formHTML += `
                            <input type="text" 
                                   name="ia_fitting_details[${detailIndex}][value]" 
                                   value="${escapedValue}"
                                   class="form-control form-control-sm" 
                                   style="border-radius: 6px; border: 1px solid #e5e7eb;">`;
                    } else if (detail.type === 'radio') {
                        formHTML += `
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="ia_fitting_details[${detailIndex}][value]" 
                                           id="radio_ok_${detailIndex}" 
                                           value="OK"
                                           ${existingValue === 'OK' ? 'checked' : ''}>
                                    <label class="form-check-label" for="radio_ok_${detailIndex}">
                                        OK
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="ia_fitting_details[${detailIndex}][value]" 
                                           id="radio_not_ok_${detailIndex}" 
                                           value="Not OK"
                                           ${existingValue === 'Not OK' ? 'checked' : ''}>
                                    <label class="form-check-label" for="radio_not_ok_${detailIndex}">
                                        Not OK
                                    </label>
                                </div>
                            </div>`;
                    } else if (detail.type === 'textarea') {
                        formHTML += `
                            <textarea 
                                name="ia_fitting_details[${detailIndex}][value]" 
                                class="form-control form-control-sm" 
                                rows="3"
                                style="border-radius: 6px; border: 1px solid #e5e7eb; resize: vertical;">${escapedValue}</textarea>`;
                    }
                    
                    formHTML += `
                                <input type="hidden" 
                                       name="ia_fitting_details[${detailIndex}][machine_category_id]" 
                                       value="${categoryId}">
                                <input type="hidden" 
                                       name="ia_fitting_details[${detailIndex}][detail_name]" 
                                       value="${detail.name}">
                                <input type="hidden" 
                                       name="ia_fitting_details[${detailIndex}][value_type]" 
                                       value="${detail.type}">
                                <input type="hidden" 
                                       name="ia_fitting_details[${detailIndex}][sort_order]" 
                                       value="${detail.sort_order}">
                            </td>
                        </tr>`;
                });
                
                formHTML += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>`;
                
                formContainer.innerHTML = formHTML;
                mainForm.style.display = 'block';
                
                // Scroll to form
                mainForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            
            // Trigger change if there's a selected serial number from session
            @if(session('selected_serial_number_id'))
                serialNumberSelect.dispatchEvent(new Event('change'));
            @endif
        });
    </script>
</x-app-layout>
