<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Machine Erection Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
            <p class="text-muted mb-0">Seller: {{ $proformaInvoice->seller->seller_name ?? 'N/A' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('machine-erection.index') }}" class="btn btn-outline-secondary">
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

    @if($machineCategories->count() > 0)
        <form method="POST" action="{{ route('machine-erection.store', $proformaInvoice) }}" id="mainErectionForm">
            @csrf
            @foreach($machineCategories as $machineCategory)
            @php
                $categoryQuantity = $machineCategoriesWithQuantity->firstWhere('category.id', $machineCategory->id)['quantity'] ?? 0;
            @endphp
            <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-cogs text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">{{ $machineCategory->name }} ({{ $categoryQuantity }} Machine{{ $categoryQuantity != 1 ? 's' : '' }})</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Desktop: Points to Follow Table -->
                    <div class="table-responsive mb-4 d-none d-md-block erection-desktop-wrap">
                        <table class="table table-bordered table-hover" id="erectionTable{{ $machineCategory->id }}" data-machine-count="{{ $categoryQuantity }}">
                            <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                <tr>
                                    <th style="width: 5%;" class="text-center">No</th>
                                    <th style="width: 25%;">Point To Follow</th>
                                    <th colspan="{{ $categoryQuantity }}" class="text-center">Machine</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    @for($i = 1; $i <= $categoryQuantity; $i++)
                                        <th class="text-center" style="font-weight: normal;">Machine {{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody id="pointsBody{{ $machineCategory->id }}">
                                @php
                                    // Get existing points for this category
                                    $categoryDetails = $existingDetails->filter(function($group) use ($machineCategory) {
                                        $first = $group->first();
                                        return $first && $first->machine_category_id == $machineCategory->id;
                                    });
                                    
                                    // Get unique points that have been added
                                    $addedPoints = $categoryDetails->keys()->map(function($key) {
                                        $parts = explode('_', $key, 2);
                                        return isset($parts[1]) ? $parts[1] : '';
                                    })->filter()->unique();
                                @endphp
                                
                                @if($addedPoints->count() > 0)
                                    @foreach($addedPoints->values() as $pointIndex => $pointToFollow)
                                        <tr data-point-row>
                                            <td class="text-center fw-medium" style="vertical-align: middle;">
                                                {{ $pointIndex + 1 }}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <input type="text" 
                                                       name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][point_to_follow]" 
                                                       value="{{ $pointToFollow }}"
                                                       class="form-control form-control-sm point-input" 
                                                       style="border-radius: 6px; border: 1px solid #e5e7eb; background-color: #f9fafb;"
                                                       readonly>
                                                <input type="hidden" 
                                                       name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][machine_category_id]" 
                                                       value="{{ $machineCategory->id }}">
                                            </td>
                                            @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                                @php
                                                    $existingDate = null;
                                                    foreach ($categoryDetails as $key => $group) {
                                                        if (str_contains($key, $pointToFollow)) {
                                                            $detail = $group->where('machine_number', $machineNum)->first();
                                                            if ($detail && $detail->date) {
                                                                $existingDate = $detail->date->format('d-m');
                                                            }
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <td style="vertical-align: middle;">
                                                    <input type="text" 
                                                           name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][machine_dates][{{ $machineNum }}]" 
                                                           value="{{ $existingDate }}"
                                                           placeholder="dd-mm"
                                                           class="form-control form-control-sm date-input" 
                                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                                           data-machine-number="{{ $machineNum }}">
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ $categoryQuantity + 2 }}" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle me-2"></i>No machine erection details added yet. Use the form below to add new details.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Desktop: Add New Machine Erection Details -->
                    <div class="mt-4 d-none d-md-block erection-desktop-wrap">
                        <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                            <i class="fas fa-plus-circle me-2"></i>Add New Machine Erection Details
                        </h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                                    <tr>
                                        <th style="width: 5%;" class="text-center">No</th>
                                        <th style="width: 25%;">Point To Follow</th>
                                        <th colspan="{{ $categoryQuantity }}" class="text-center">Machine</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        @for($i = 1; $i <= $categoryQuantity; $i++)
                                            <th class="text-center" style="font-weight: normal;">Machine {{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Get points that haven't been added yet
                                        $availablePoints = collect($defaultPointsToFollow)->filter(function($point) use ($addedPoints) {
                                            return !$addedPoints->contains($point);
                                        });
                                    @endphp
                                    
                                    @if($availablePoints->count() > 0)
                                        @foreach($availablePoints->values() as $newPointIndex => $pointToFollow)
                                            @php
                                                $actualIndex = $addedPoints->count() + $newPointIndex;
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-medium" style="vertical-align: middle;">
                                                    {{ $actualIndex + 1 }}
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <input type="text" 
                                                           name="machine_erection_details[{{ $machineCategory->id }}][{{ $actualIndex }}][point_to_follow]" 
                                                           value="{{ $pointToFollow }}"
                                                           class="form-control form-control-sm point-input" 
                                                           style="border-radius: 6px; border: 1px solid #e5e7eb; background-color: #f9fafb;"
                                                           readonly>
                                                    <input type="hidden" 
                                                           name="machine_erection_details[{{ $machineCategory->id }}][{{ $actualIndex }}][machine_category_id]" 
                                                           value="{{ $machineCategory->id }}">
                                                </td>
                                                @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                                    <td style="vertical-align: middle;">
                                                        <input type="text" 
                                                               name="machine_erection_details[{{ $machineCategory->id }}][{{ $actualIndex }}][machine_dates][{{ $machineNum }}]" 
                                                               value=""
                                                               placeholder="dd-mm"
                                                               class="form-control form-control-sm date-input" 
                                                               style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                                               data-machine-number="{{ $machineNum }}">
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ $categoryQuantity + 2 }}" class="text-center text-muted py-3">
                                                <i class="fas fa-check-circle me-2"></i>All available points have been added.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Mobile: cards with full-width inputs (same names as desktop tables); JS disables hidden section --}}
                    <div class="d-md-none erection-mobile-wrap mb-4">
                        <h5 class="fw-semibold mb-3" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                            <i class="fas fa-list me-2"></i>Points to Follow
                        </h5>
                        @if($addedPoints->count() > 0)
                            @foreach($addedPoints->values() as $pointIndex => $pointToFollow)
                                <div class="card mb-3 shadow-sm" style="border-radius: 10px; border: 1px solid #e5e7eb;">
                                    <div class="card-header py-2 px-3 fw-medium small" style="background: color-mix(in srgb, var(--primary-color) 15%, #fff); color: #1f2937; border-radius: 9px 9px 0 0;">
                                        {{ $pointIndex + 1 }}. {{ $pointToFollow }}
                                    </div>
                                    <div class="card-body p-3">
                                        <input type="hidden" name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][point_to_follow]" value="{{ $pointToFollow }}">
                                        <input type="hidden" name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][machine_category_id]" value="{{ $machineCategory->id }}">
                                        @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                            @php
                                                $existingDate = null;
                                                foreach ($categoryDetails as $key => $group) {
                                                    if (str_contains($key, $pointToFollow)) {
                                                        $detail = $group->where('machine_number', $machineNum)->first();
                                                        if ($detail && $detail->date) {
                                                            $existingDate = $detail->date->format('d-m');
                                                        }
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <div class="mb-2">
                                                <label class="form-label small mb-1" style="color: #374151;">Machine {{ $machineNum }}</label>
                                                <input type="text"
                                                       name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][machine_dates][{{ $machineNum }}]"
                                                       value="{{ $existingDate }}"
                                                       placeholder="dd-mm"
                                                       class="form-control date-input"
                                                       style="border-radius: 8px; border: 1px solid #e5e7eb; min-height: 44px;"
                                                       data-machine-number="{{ $machineNum }}">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small mb-3"><i class="fas fa-info-circle me-2"></i>No points added yet. Add below.</p>
                        @endif
                        <h5 class="fw-semibold mb-3 mt-4" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
                            <i class="fas fa-plus-circle me-2"></i>Add New
                        </h5>
                        @if($availablePoints->count() > 0)
                            @foreach($availablePoints->values() as $newPointIndex => $pointToFollow)
                                @php $actualIndex = $addedPoints->count() + $newPointIndex; @endphp
                                <div class="card mb-3 shadow-sm" style="border-radius: 10px; border: 1px solid #e5e7eb;">
                                    <div class="card-header py-2 px-3 fw-medium small" style="background: color-mix(in srgb, var(--primary-color) 15%, #fff); color: #1f2937; border-radius: 9px 9px 0 0;">
                                        {{ $actualIndex + 1 }}. {{ $pointToFollow }}
                                    </div>
                                    <div class="card-body p-3">
                                        <input type="hidden" name="machine_erection_details[{{ $machineCategory->id }}][{{ $actualIndex }}][point_to_follow]" value="{{ $pointToFollow }}">
                                        <input type="hidden" name="machine_erection_details[{{ $machineCategory->id }}][{{ $actualIndex }}][machine_category_id]" value="{{ $machineCategory->id }}">
                                        @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                            <div class="mb-2">
                                                <label class="form-label small mb-1" style="color: #374151;">Machine {{ $machineNum }}</label>
                                                <input type="text"
                                                       name="machine_erection_details[{{ $machineCategory->id }}][{{ $actualIndex }}][machine_dates][{{ $machineNum }}]"
                                                       value=""
                                                       placeholder="dd-mm"
                                                       class="form-control date-input"
                                                       style="border-radius: 8px; border: 1px solid #e5e7eb; min-height: 44px;"
                                                       data-machine-number="{{ $machineNum }}">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small"><i class="fas fa-check-circle me-2"></i>All available points have been added.</p>
                        @endif
                    </div>
                    
                </div>
            </div>
        @endforeach
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <a href="{{ route('machine-erection.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save All Machine Erection Details
                    </button>
                </div>
            </div>
        </div>
        </form>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted">No machine categories found for this Proforma Invoice.</p>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setErectionFormDisabled() {
                var isMobile = window.innerWidth < 768;
                document.querySelectorAll('.erection-desktop-wrap').forEach(function(wrap) {
                    wrap.querySelectorAll('input, select, textarea').forEach(function(inp) {
                        inp.disabled = isMobile;
                    });
                });
                document.querySelectorAll('.erection-mobile-wrap').forEach(function(wrap) {
                    wrap.querySelectorAll('input, select, textarea').forEach(function(inp) {
                        inp.disabled = !isMobile;
                    });
                });
            }
            document.querySelectorAll('.date-input').forEach(function(input) {
                flatpickr(input, { dateFormat: "d-m", allowInput: true });
            });
            setErectionFormDisabled();
            window.addEventListener('resize', setErectionFormDisabled);
        });
    </script>
</x-app-layout>

