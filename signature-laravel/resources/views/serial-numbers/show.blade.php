<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Serial Numbers & Khata Numbers</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('serial-numbers.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-hashtag text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Machine Categories & Serial Numbers</h2>
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

            <div class="alert alert-info mb-4 py-2">
                <i class="fas fa-info-circle me-2"></i><strong>Note:</strong> If you enter a Khata number, Serial number is compulsory for that row.
            </div>

            @if($machinesByCategory->count() > 0)
            <form method="POST" action="{{ route('serial-numbers.store', $proformaInvoice) }}">
                @csrf
                
                @foreach($machinesByCategory as $categoryData)
                <div class="mb-4">
                    <h5 class="fw-semibold mb-3" style="color: var(--primary-color);">
                        <i class="fas fa-tag me-2"></i>{{ $categoryData['category']->name }}
                        <span class="badge bg-secondary ms-2">Quantity: {{ $categoryData['total_quantity'] }}</span>
                    </h5>

                    {{-- Desktop: table (md and up) --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered table-hover mb-0">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 25%;">Category</th>
                                    <th style="width: 15%;">Brand</th>
                                    <th style="width: 15%;">Model</th>
                                    <th style="width: 10%;">Quantity</th>
                                    <th style="width: 17.5%;">Serial Number</th>
                                    <th style="width: 17.5%;">Khata Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryData['machines'] as $machineData)
                                    @php
                                        $machine = $machineData['machine'];
                                        $quantity = $machine->quantity;
                                        $existingSerials = $machineData['serial_numbers'] ?? [];
                                    @endphp
                                    @for($i = 0; $i < $quantity; $i++)
                                    <tr>
                                        @if($i === 0)
                                        <td rowspan="{{ $quantity }}" class="align-middle fw-medium" style="color: #374151;">{{ $categoryData['category']->name }}</td>
                                        <td rowspan="{{ $quantity }}" class="align-middle">{{ $machine->brand->name ?? 'N/A' }}</td>
                                        <td rowspan="{{ $quantity }}" class="align-middle">{{ $machine->machineModel->model_no ?? 'N/A' }}</td>
                                        <td rowspan="{{ $quantity }}" class="align-middle text-center"><span class="badge bg-primary">{{ $quantity }}</span></td>
                                        @endif
                                        <td>
                                            <input type="text" name="serial_numbers[{{ $machine->id }}][{{ $i }}][serial_number]" value="{{ old("serial_numbers.{$machine->id}.{$i}.serial_number", isset($existingSerials[$i]) ? $existingSerials[$i]->serial_number : '') }}" placeholder="Enter Serial Number (required if Khata is filled)" class="form-control form-control-sm {{ $errors->has("serial_numbers.{$machine->id}.{$i}.serial_number") ? 'is-invalid' : '' }}" style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                            @error("serial_numbers.{$machine->id}.{$i}.serial_number")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            <input type="hidden" name="serial_numbers[{{ $machine->id }}][{{ $i }}][machine_id]" value="{{ $machine->id }}">
                                        </td>
                                        <td>
                                            <input type="text" name="serial_numbers[{{ $machine->id }}][{{ $i }}][khata_number]" value="{{ old("serial_numbers.{$machine->id}.{$i}.khata_number", isset($existingSerials[$i]) ? $existingSerials[$i]->khata_number : '') }}" placeholder="Enter Khata Number" class="form-control form-control-sm" style="border-radius: 6px; border: 1px solid #e5e7eb;">
                                        </td>
                                    </tr>
                                    @endfor
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile: cards with full-width inputs; JS copies values to hidden fields that submit (table hidden on mobile so we need to submit from cards) --}}
                    <div class="d-md-none serial-mobile-wrap">
                        @foreach($categoryData['machines'] as $machineData)
                            @php $machine = $machineData['machine']; $quantity = $machine->quantity; $existingSerials = $machineData['serial_numbers'] ?? []; @endphp
                            <div class="card mb-3 shadow-sm" style="border-radius: 10px; border: 1px solid #e5e7eb;">
                                <div class="card-header py-2 px-3 d-flex flex-wrap align-items-center gap-2" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; border-radius: 9px 9px 0 0;">
                                    <span class="fw-medium small">{{ $categoryData['category']->name }}</span>
                                    <span class="badge bg-white text-dark">{{ $machine->brand->name ?? 'N/A' }}</span>
                                    <span class="badge bg-light text-dark">{{ $machine->machineModel->model_no ?? 'N/A' }}</span>
                                    <span class="badge bg-primary ms-auto">{{ $quantity }} unit(s)</span>
                                </div>
                                <div class="card-body p-3">
                                    @for($i = 0; $i < $quantity; $i++)
                                    <div class="border rounded p-3 mb-2" style="background: #f9fafb; border-color: #e5e7eb;">
                                        <div class="small fw-medium text-muted mb-2">Entry {{ $i + 1 }}</div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1" style="color: #374151;">Serial Number</label>
                                            <input type="text" name="serial_numbers[{{ $machine->id }}][{{ $i }}][serial_number]" value="{{ old("serial_numbers.{$machine->id}.{$i}.serial_number", isset($existingSerials[$i]) ? $existingSerials[$i]->serial_number : '') }}" placeholder="Enter serial number" class="form-control {{ $errors->has("serial_numbers.{$machine->id}.{$i}.serial_number") ? 'is-invalid' : '' }}" style="border-radius: 8px; border: 1px solid #e5e7eb; min-height: 44px;">
                                            @error("serial_numbers.{$machine->id}.{$i}.serial_number")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                        <div>
                                            <label class="form-label small mb-1" style="color: #374151;">Khata Number</label>
                                            <input type="text" name="serial_numbers[{{ $machine->id }}][{{ $i }}][khata_number]" value="{{ old("serial_numbers.{$machine->id}.{$i}.khata_number", isset($existingSerials[$i]) ? $existingSerials[$i]->khata_number : '') }}" placeholder="Enter khata number" class="form-control" style="border-radius: 8px; border: 1px solid #e5e7eb; min-height: 44px;">
                                        </div>
                                        <input type="hidden" name="serial_numbers[{{ $machine->id }}][{{ $i }}][machine_id]" value="{{ $machine->id }}">
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('serial-numbers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Serial Numbers
                    </button>
                </div>
            </form>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted mb-0">No machines found in this Proforma Invoice.</p>
            </div>
            @endif
        </div>
    </div>

    <script>
        (function() {
            function setSerialFormDisabled() {
                var isMobile = window.innerWidth < 768;
                // Every category has its own table + mobile block; querySelector only hit the first —
                // leaving later mobile inputs enabled on desktop caused duplicate names and empty values overwrote table data.
                document.querySelectorAll('.table-responsive.d-none.d-md-block').forEach(function (tableWrap) {
                    tableWrap.querySelectorAll('input, select, textarea').forEach(function (inp) {
                        inp.disabled = isMobile;
                    });
                });
                document.querySelectorAll('.serial-mobile-wrap.d-md-none').forEach(function (mobileWrap) {
                    mobileWrap.querySelectorAll('input, select, textarea').forEach(function (inp) {
                        inp.disabled = !isMobile;
                    });
                });
            }
            setSerialFormDisabled();
            window.addEventListener('resize', setSerialFormDisabled);
        })();
    </script>
</x-app-layout>

