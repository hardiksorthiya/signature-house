{{-- Expects: $nioFieldPrefix, $nioPrintField, $nioShowPrint (bool), $nioFlags (array<string,bool>) --}}
@php
    $nioItems = config('not_included_in_offer.items', []);
@endphp
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;" x-data="{ showFields: {{ $nioShowPrint ? 'true' : 'false' }} }">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-semibold mb-0" style="color: #1f2937;">Not Included in Offer</h5>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="{{ $nioPrintField }}" id="{{ $nioPrintField }}_show" value="1" {{ $nioShowPrint ? 'checked' : '' }} @change="showFields = true">
                            <label class="btn btn-outline-success btn-sm" for="{{ $nioPrintField }}_show" style="border-radius: 6px 0 0 6px;">Show</label>
                            <input type="radio" class="btn-check" name="{{ $nioPrintField }}" id="{{ $nioPrintField }}_hide" value="0" {{ !$nioShowPrint ? 'checked' : '' }} @change="showFields = false">
                            <label class="btn btn-outline-danger btn-sm" for="{{ $nioPrintField }}_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                            <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                </div>
                <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand checkboxes.</p>
                <div class="row g-2" x-show="showFields" x-cloak>
                    @foreach($nioItems as $key => $label)
                        <div class="col-md-6 col-lg-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="{{ $nioFieldPrefix }}[{{ $key }}]" id="{{ $nioFieldPrefix }}_{{ $key }}" value="1" {{ !empty($nioFlags[$key]) ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="{{ $nioFieldPrefix }}_{{ $key }}" style="color: #374151;">{{ $label }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
