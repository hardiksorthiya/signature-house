@props([
    'machineNum',
    'name',
    'value' => '',
    'idPrefix' => 'cert',
])

@php
    $selected = $value;
    $fieldId = $idPrefix . '_m' . $machineNum;
@endphp

<div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
    <div class="erection-machine-card h-100">
        <div class="erection-machine-card__head">
            <span class="erection-machine-card__badge">M{{ $machineNum }}</span>
            <span class="erection-machine-card__title">Machine {{ $machineNum }}</span>
        </div>
        <div class="erection-machine-card__body">
            <label class="erection-machine-card__label">Certificate received</label>
            <div class="d-flex gap-3 erection-radio-group">
                <div class="form-check erection-radio-option">
                    <input class="form-check-input"
                           type="radio"
                           name="{{ $name }}"
                           id="{{ $fieldId }}_yes"
                           value="yes"
                           {{ $selected === 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="{{ $fieldId }}_yes">Yes</label>
                </div>
                <div class="form-check erection-radio-option">
                    <input class="form-check-input"
                           type="radio"
                           name="{{ $name }}"
                           id="{{ $fieldId }}_no"
                           value="no"
                           {{ $selected === 'no' ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="{{ $fieldId }}_no">No</label>
                </div>
            </div>
        </div>
    </div>
</div>
