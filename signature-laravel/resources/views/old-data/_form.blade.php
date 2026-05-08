@php
    $oldDatum = $oldDatum ?? null;
    $selectedCity = old('city', $oldDatum->city ?? '');
    $selectedArea = old('area', $oldDatum->area ?? '');
    $machineRows = old('machines');
    if (!is_array($machineRows)) {
        $machineRows = isset($oldDatum) ? $oldDatum->machines->map(function ($machine) {
            return [
                'machine_category_id' => $machine->machine_category_id,
                'machine_model' => $machine->machine_model,
                'serial_number' => $machine->serial_number,
                'khata_number' => $machine->khata_number,
                'date_of_manufacturing' => $machine->date_of_manufacturing,
            ];
        })->toArray() : [];
    }
    if (count($machineRows) === 0) {
        $machineRows[] = [];
    }
@endphp

<div class="card shadow-sm border-0" style="border-radius: 12px;">
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

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-medium">Firm Name <span class="text-danger">*</span></label>
                <input type="text" name="firm_name" class="form-control" value="{{ old('firm_name', $oldDatum->firm_name ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Client Name <span class="text-danger">*</span></label>
                <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $oldDatum->client_name ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Phone Number 1 <span class="text-danger">*</span></label>
                <input type="text" name="phone_number_1" class="form-control" value="{{ old('phone_number_1', $oldDatum->phone_number_1 ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Phone Number 2</label>
                <input type="text" name="phone_number_2" class="form-control" value="{{ old('phone_number_2', $oldDatum->phone_number_2 ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">City</label>
                <select name="city" class="form-select">
                    <option value="">Select City</option>
                    @foreach(($cities ?? collect()) as $cityName)
                        <option value="{{ $cityName }}" {{ $selectedCity === $cityName ? 'selected' : '' }}>
                            {{ $cityName }}
                        </option>
                    @endforeach
                    @if($selectedCity !== '' && !collect($cities ?? [])->contains($selectedCity))
                        <option value="{{ $selectedCity }}" selected>{{ $selectedCity }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Area</label>
                <select name="area" class="form-select">
                    <option value="">Select Area</option>
                    @foreach(($areas ?? collect()) as $areaName)
                        <option value="{{ $areaName }}" {{ $selectedArea === $areaName ? 'selected' : '' }}>
                            {{ $areaName }}
                        </option>
                    @endforeach
                    @if($selectedArea !== '' && !collect($areas ?? [])->contains($selectedArea))
                        <option value="{{ $selectedArea }}" selected>{{ $selectedArea }}</option>
                    @endif
                </select>
            </div>
        </div>

        <div class="mb-3">
            <h5 class="mb-0">Machine Details</h5>
        </div>

        <div id="machineCards" class="d-flex flex-column gap-3">
            @foreach($machineRows as $idx => $machineRow)
                <div class="machine-card card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="fw-semibold" style="color: #1f2937;">
                                <i class="fas fa-microchip me-2 text-primary"></i>Machine {{ $idx + 1 }}
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-machine-row" title="Remove machine">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label fw-medium">Machine Category</label>
                                <select name="machines[{{ $idx }}][machine_category_id]" class="form-select">
                                    <option value="">Select Category</option>
                                    @foreach($machineCategories as $category)
                                        <option value="{{ $category->id }}" {{ (string)($machineRow['machine_category_id'] ?? '') === (string)$category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Machine Model</label>
                                <select name="machines[{{ $idx }}][machine_model]" class="form-select">
                                    <option value="">Select Model</option>
                                    @foreach($machineModels as $model)
                                        <option value="{{ $model }}" {{ ($machineRow['machine_model'] ?? '') === $model ? 'selected' : '' }}>{{ $model }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Serial Number</label>
                                <input type="text" name="machines[{{ $idx }}][serial_number]" class="form-control" value="{{ $machineRow['serial_number'] ?? '' }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Khata Number</label>
                                <input type="text" name="machines[{{ $idx }}][khata_number]" class="form-control" value="{{ $machineRow['khata_number'] ?? '' }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Date of Manufacturing</label>
                                <input type="date" name="machines[{{ $idx }}][date_of_manufacturing]" class="form-control" value="{{ $machineRow['date_of_manufacturing'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            <button type="button" id="addMachineRow" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus me-1"></i>Add Machine
            </button>
        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('old-data.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        </div>
    </div>
</div>

<template id="machineRowTemplate">
    <div class="machine-card card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-semibold" style="color: #1f2937;">
                    <i class="fas fa-microchip me-2 text-primary"></i>Machine __DISPLAY_INDEX__
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-machine-row" title="Remove machine">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label fw-medium">Machine Category</label>
                    <select name="machines[__INDEX__][machine_category_id]" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($machineCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Machine Model</label>
                    <select name="machines[__INDEX__][machine_model]" class="form-select">
                        <option value="">Select Model</option>
                        @foreach($machineModels as $model)
                            <option value="{{ $model }}">{{ $model }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Serial Number</label>
                    <input type="text" name="machines[__INDEX__][serial_number]" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Khata Number</label>
                    <input type="text" name="machines[__INDEX__][khata_number]" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label fw-medium">Date of Manufacturing</label>
                    <input type="date" name="machines[__INDEX__][date_of_manufacturing]" class="form-control">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cardsContainer = document.getElementById('machineCards');
        const addButton = document.getElementById('addMachineRow');
        const template = document.getElementById('machineRowTemplate').innerHTML;

        const currentIndex = () => cardsContainer.querySelectorAll('.machine-card').length;

        const addRow = () => {
            const nextIndex = currentIndex();
            const displayIndex = nextIndex + 1;
            const html = template
                .replaceAll('__INDEX__', String(nextIndex))
                .replaceAll('__DISPLAY_INDEX__', String(displayIndex));
            cardsContainer.insertAdjacentHTML('beforeend', html);
        };

        addButton.addEventListener('click', addRow);

        cardsContainer.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-machine-row');
            if (!button) return;

            const card = button.closest('.machine-card');
            const allCards = cardsContainer.querySelectorAll('.machine-card');
            if (allCards.length === 1) {
                card.querySelectorAll('input').forEach((field) => field.value = '');
                card.querySelectorAll('select').forEach((field) => field.value = '');
                return;
            }
            card.remove();
        });
    });
</script>
