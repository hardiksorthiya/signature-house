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
            <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-hashtag text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Serial / Khata / Name Plate / Production Date</h2>
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
                <div class="alert alert-danger mb-4">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <div class="alert alert-info mb-4 py-2">
                <i class="fas fa-info-circle me-2"></i><strong>Note:</strong> If you enter a Khata number, Serial number is required for that row.
            </div>

            @if($machinesByCategory->count() > 0)
            <form method="POST" action="{{ route('serial-numbers.store', $proformaInvoice) }}" enctype="multipart/form-data">
                @csrf

                @php $globalRow = 0; @endphp
                @foreach($machinesByCategory as $categoryData)
                <div class="mb-4">
                    <h5 class="fw-semibold mb-2" style="color: var(--primary-color);">
                        <i class="fas fa-tag me-2"></i>{{ $categoryData['category']->name }}
                        <span class="badge bg-secondary ms-2">Total Qty: {{ $categoryData['total_quantity'] }}</span>
                    </h5>

                    @foreach($categoryData['machines'] as $machineData)
                        @php
                            $machine = $machineData['machine'];
                            $quantity = $machine->quantity;
                            $existingSerials = $machineData['serial_numbers'] ?? [];
                        @endphp
                        <div class="mb-3 p-3 rounded border" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important; background: white;">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 small text-muted">
                                <span class="fw-medium text-dark">{{ $machine->brand->name ?? 'N/A' }}</span>
                                <span>·</span>
                                <span>{{ $machine->machineModel->model_no ?? 'N/A' }}</span>
                                <span class="badge bg-primary ms-auto">{{ $quantity }} unit(s)</span>
                            </div>

                            {{-- Desktop table --}}
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-bordered table-hover mb-0 align-middle">
                                    <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                        <tr>
                                            <th class="text-center" style="width: 48px;">#</th>
                                            <th style="min-width: 140px;">Serial No</th>
                                            <th style="min-width: 120px;">Khata Number</th>
                                            <th style="min-width: 180px;">Image (Name Plate)</th>
                                            <th style="min-width: 150px;">Production Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 0; $i < $quantity; $i++)
                                            @php
                                                $globalRow++;
                                                $existing = $existingSerials[$i] ?? null;
                                                $prodDate = old(
                                                    "serial_numbers.{$machine->id}.{$i}.production_date",
                                                    $existing && $existing->production_date
                                                        ? $existing->production_date->format('Y-m-d')
                                                        : ''
                                                );
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-medium text-muted">{{ $globalRow }}</td>
                                                <td>
                                                    <input type="text"
                                                           name="serial_numbers[{{ $machine->id }}][{{ $i }}][serial_number]"
                                                           value="{{ old("serial_numbers.{$machine->id}.{$i}.serial_number", $existing?->serial_number ?? '') }}"
                                                           placeholder="Serial number"
                                                           class="form-control form-control-sm {{ $errors->has("serial_numbers.{$machine->id}.{$i}.serial_number") ? 'is-invalid' : '' }}"
                                                           style="border-radius: 6px;">
                                                    @error("serial_numbers.{$machine->id}.{$i}.serial_number")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                    <input type="hidden" name="serial_numbers[{{ $machine->id }}][{{ $i }}][machine_id]" value="{{ $machine->id }}">
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           name="serial_numbers[{{ $machine->id }}][{{ $i }}][khata_number]"
                                                           value="{{ old("serial_numbers.{$machine->id}.{$i}.khata_number", $existing?->khata_number ?? '') }}"
                                                           placeholder="Khata number"
                                                           class="form-control form-control-sm"
                                                           style="border-radius: 6px;">
                                                </td>
                                                <td>
                                                    @if($existing && $existing->name_plate_path)
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <a href="{{ asset('storage/' . $existing->name_plate_path) }}" target="_blank" rel="noopener">
                                                                <img src="{{ asset('storage/' . $existing->name_plate_path) }}" alt="Name plate" class="rounded border" style="width: 56px; height: 56px; object-fit: cover;">
                                                            </a>
                                                            <input type="hidden" name="serial_numbers[{{ $machine->id }}][{{ $i }}][keep_name_plate_path]" value="{{ $existing->name_plate_path }}">
                                                        </div>
                                                    @endif
                                                    <input type="file"
                                                           name="serial_numbers[{{ $machine->id }}][{{ $i }}][name_plate]"
                                                           accept="image/*"
                                                           capture="environment"
                                                           class="form-control form-control-sm"
                                                           style="border-radius: 6px;">
                                                    <small class="text-muted">Upload name plate photo</small>
                                                </td>
                                                <td>
                                                    <input type="date"
                                                           name="serial_numbers[{{ $machine->id }}][{{ $i }}][production_date]"
                                                           value="{{ $prodDate }}"
                                                           class="form-control form-control-sm"
                                                           style="border-radius: 6px;">
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>

                            {{-- Mobile cards --}}
                            <div class="d-md-none serial-mobile-wrap">
                                @for($i = 0; $i < $quantity; $i++)
                                    @php
                                        $displayRow = $globalRow - $quantity + $i + 1;
                                        $existing = $existingSerials[$i] ?? null;
                                        $prodDate = old(
                                            "serial_numbers.{$machine->id}.{$i}.production_date",
                                            $existing && $existing->production_date
                                                ? $existing->production_date->format('Y-m-d')
                                                : ''
                                        );
                                    @endphp
                                    <div class="border rounded p-3 mb-2" style="background: #f9fafb; border-color: #e5e7eb !important;">
                                        <div class="small fw-semibold mb-3" style="color: var(--primary-color);"># {{ $displayRow }}</div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Serial No</label>
                                            <input type="text"
                                                   name="serial_numbers[{{ $machine->id }}][{{ $i }}][serial_number]"
                                                   value="{{ old("serial_numbers.{$machine->id}.{$i}.serial_number", $existing?->serial_number ?? '') }}"
                                                   placeholder="Serial number"
                                                   class="form-control {{ $errors->has("serial_numbers.{$machine->id}.{$i}.serial_number") ? 'is-invalid' : '' }}"
                                                   style="border-radius: 8px; min-height: 44px;">
                                            @error("serial_numbers.{$machine->id}.{$i}.serial_number")
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Khata Number</label>
                                            <input type="text"
                                                   name="serial_numbers[{{ $machine->id }}][{{ $i }}][khata_number]"
                                                   value="{{ old("serial_numbers.{$machine->id}.{$i}.khata_number", $existing?->khata_number ?? '') }}"
                                                   placeholder="Khata number"
                                                   class="form-control"
                                                   style="border-radius: 8px; min-height: 44px;">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Image (Name Plate)</label>
                                            @if($existing && $existing->name_plate_path)
                                                <div class="mb-2">
                                                    <a href="{{ asset('storage/' . $existing->name_plate_path) }}" target="_blank" rel="noopener">
                                                        <img src="{{ asset('storage/' . $existing->name_plate_path) }}" alt="Name plate" class="rounded border w-100" style="max-height: 160px; object-fit: contain;">
                                                    </a>
                                                    <input type="hidden" name="serial_numbers[{{ $machine->id }}][{{ $i }}][keep_name_plate_path]" value="{{ $existing->name_plate_path }}">
                                                </div>
                                            @endif
                                            <input type="file"
                                                   name="serial_numbers[{{ $machine->id }}][{{ $i }}][name_plate]"
                                                   accept="image/*"
                                                   capture="environment"
                                                   class="form-control"
                                                   style="border-radius: 8px;">
                                        </div>
                                        <div>
                                            <label class="form-label small mb-1">Production Date</label>
                                            <input type="date"
                                                   name="serial_numbers[{{ $machine->id }}][{{ $i }}][production_date]"
                                                   value="{{ $prodDate }}"
                                                   class="form-control"
                                                   style="border-radius: 8px; min-height: 44px;">
                                        </div>
                                        <input type="hidden" name="serial_numbers[{{ $machine->id }}][{{ $i }}][machine_id]" value="{{ $machine->id }}">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
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
                document.querySelectorAll('.table-responsive.d-none.d-md-block').forEach(function(tableWrap) {
                    tableWrap.querySelectorAll('input, select, textarea').forEach(function(inp) {
                        inp.disabled = isMobile;
                    });
                });
                document.querySelectorAll('.serial-mobile-wrap.d-md-none').forEach(function(mobileWrap) {
                    mobileWrap.querySelectorAll('input, select, textarea').forEach(function(inp) {
                        inp.disabled = !isMobile;
                    });
                });
            }
            setSerialFormDisabled();
            window.addEventListener('resize', setSerialFormDisabled);
        })();
    </script>
</x-app-layout>
