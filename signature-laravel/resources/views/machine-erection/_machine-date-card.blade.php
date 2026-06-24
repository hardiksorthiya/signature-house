@props([
    'machineNum',
    'primaryLabel' => 'Completion date',
    'primaryName',
    'primaryValue' => '',
    'secondaryLabel' => null,
    'secondaryName' => null,
    'secondaryValue' => '',
])

<div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
    <div class="erection-machine-card h-100">
        <div class="erection-machine-card__head">
            <span class="erection-machine-card__badge">M{{ $machineNum }}</span>
            <span class="erection-machine-card__title">Machine {{ $machineNum }}</span>
        </div>
        <div class="erection-machine-card__body">
            <label class="erection-machine-card__label">{{ $primaryLabel }}</label>
            <div class="input-group input-group-sm erection-machine-card__date">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="text"
                       name="{{ $primaryName }}"
                       value="{{ $primaryValue }}"
                       placeholder="dd-mm"
                       class="form-control date-input"
                       autocomplete="off">
            </div>
            @if($secondaryLabel && $secondaryName)
                <label class="erection-machine-card__label mt-2">{{ $secondaryLabel }}</label>
                <div class="input-group input-group-sm erection-machine-card__date">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="text"
                           name="{{ $secondaryName }}"
                           value="{{ $secondaryValue }}"
                           placeholder="dd-mm"
                           class="form-control date-input"
                           autocomplete="off">
                </div>
            @endif
        </div>
    </div>
</div>
