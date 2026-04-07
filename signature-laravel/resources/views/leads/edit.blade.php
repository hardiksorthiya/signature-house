<x-app-layout>
    @php
        $planningMonths = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        $currentYear = (int) date('Y');
        $planningYears = range($currentYear - 2, $currentYear + 5);
    @endphp

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Lead</h1>
            <p class="text-muted mb-0">Update lead information</p>
        </div>
        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Leads
        </a>
    </div>

    <div class="row g-4" x-data="leadForm()">
        <!-- FULL WIDTH FORM -->
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Edit Lead</h2>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium" style="color: #374151;">Lead Type <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="lead_type" id="type_new" value="new" x-model="leadType" @change="switchFormType('new')" {{ $lead->type === 'new' ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label" for="type_new" style="cursor: pointer; color: #374151;">
                                    New
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="lead_type" id="type_old" value="old" x-model="leadType" @change="switchFormType('old')" {{ $lead->type === 'old' ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label" for="type_old" style="cursor: pointer; color: #374151;">
                                    Old
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- New Form -->
                    <div x-show="leadType === 'new'">
                        <form action="{{ route('leads.update', $lead) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="type" value="new">
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-medium" style="color: #374151;">Business <span class="text-danger">*</span></label>
                                    <select name="business_id" required class="form-select @error('business_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Business</option>
                                        @foreach($businesses as $business)
                                            <option value="{{ $business->id }}" {{ $lead->business_id == $business->id ? 'selected' : '' }}>{{ $business->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('business_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required value="{{ old('name', $lead->name) }}" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" required value="{{ old('phone_number', $lead->phone_number) }}" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           placeholder="Enter phone number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('phone_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">State <span class="text-danger">*</span></label>
                                    <select name="state_id" required id="new_state_id" class="form-select @error('state_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadCities($event.target.value, 'new')">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}" {{ $lead->state_id == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">City <span class="text-danger">*</span></label>
                                    <select name="city_id" required id="new_city_id" class="form-select @error('city_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadAreas($event.target.value, 'new')">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ $lead->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Area <span class="text-danger">*</span></label>
                                    <select name="area_id" required id="new_area_id" class="form-select @error('area_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Area</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}" {{ $lead->area_id == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('area_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Machine Category <small class="text-muted">(Multiple Select)</small> <span class="text-danger">*</span></label>
                                    <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                        <button type="button" 
                                                @click="categoryDropdownOpen = !categoryDropdownOpen"
                                                class="form-control text-start d-flex justify-content-between align-items-center"
                                                style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                            <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                            <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                        </button>
                                        <div x-show="categoryDropdownOpen" 
                                             x-cloak
                                             class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                             style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                             @click.stop>
                                            @forelse($categories ?? [] as $category)
                                                <div class="d-flex align-items-center py-2 px-3" 
                                                     x-data="{ hovered: false }"
                                                     :class="isCategorySelected({{ $category->id }}) ? 'bg-red-50' : ''"
                                                     :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                     style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                     @mouseenter="hovered = true"
                                                     @mouseleave="hovered = false"
                                                     @click="toggleCategory({{ $category->id }})">
                                                    <input class="form-check-input me-3" 
                                                           type="checkbox" 
                                                           :checked="isCategorySelected({{ $category->id }})"
                                                           style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                           @click.stop="toggleCategory({{ $category->id }})">
                                                    <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                        {{ $category->name }}
                                                    </label>
                                                    <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                                </div>
                                            @empty
                                                <div class="p-3 text-center text-muted">
                                                    <small>No categories available. Add categories first.</small>
                                                </div>
                                            @endforelse
                                        </div>
                                        <template x-for="categoryId in selectedCategories" :key="categoryId">
                                            <input type="hidden" :name="`categories[]`" :value="categoryId">
                                        </template>
                                    </div>
                                    @error('categories')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" required value="{{ old('quantity', $lead->quantity) }}" min="1"
                                           class="form-control @error('quantity') is-invalid @enderror" 
                                           placeholder="Enter quantity" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('quantity')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Planning Month <span class="text-danger">*</span></label>
                                    <select name="planning_month" required class="form-select @error('planning_month') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Month</option>
                                        @foreach($planningMonths as $monthValue => $monthLabel)
                                            <option value="{{ $monthValue }}" {{ (int) old('planning_month', $lead->planning_month ?? now()->month) === $monthValue ? 'selected' : '' }}>{{ $monthLabel }}</option>
                                        @endforeach
                                    </select>
                                    @error('planning_month')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Planning Year <span class="text-danger">*</span></label>
                                    <select name="planning_year" required class="form-select @error('planning_year') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Year</option>
                                        @foreach($planningYears as $planningYear)
                                            <option value="{{ $planningYear }}" {{ (int) old('planning_year', $lead->planning_year ?? now()->year) === (int) $planningYear ? 'selected' : '' }}>{{ $planningYear }}</option>
                                        @endforeach
                                    </select>
                                    @error('planning_year')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-medium" style="color: #374151;">Status <span class="text-danger">*</span></label>
                                    <select name="status_id" required 
                                            x-model="selectedStatusId"
                                            @change="checkSchedulingStatus()"
                                            class="form-select @error('status_id') is-invalid @enderror" 
                                            style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" 
                                                    data-requires-scheduling="{{ $status->requires_scheduling ? 1 : 0 }}"
                                                    {{ $lead->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Scheduling Toggle and Fields -->
                                <div class="col-md-12" x-show="showSchedulingToggle" x-cloak>
                                    <div class="card p-3 mb-3" style="background-color: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px;">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <label class="form-label fw-medium mb-0" style="color: #374151;">
                                                <i class="fas fa-calendar-alt me-2"></i>Schedule Date & Time
                                            </label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="needs_scheduling_new_edit"
                                                       x-model="needsScheduling"
                                                       style="cursor: pointer; width: 3rem; height: 1.5rem;"
                                                       name="needs_scheduling"
                                                       value="1">
                                                <label class="form-check-label ms-2" for="needs_scheduling_new_edit" style="cursor: pointer;">
                                                    <span x-text="needsScheduling ? 'Enabled' : 'Disabled'"></span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div x-show="needsScheduling" x-cloak class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Scheduled Date <span class="text-danger">*</span></label>
                                                <input type="date" 
                                                       name="scheduled_date" 
                                                       x-model="scheduledDate"
                                                       :required="needsScheduling"
                                                       min="{{ date('Y-m-d') }}"
                                                       value="{{ old('scheduled_date', $lead->scheduled_date ? \Carbon\Carbon::parse($lead->scheduled_date)->format('Y-m-d') : '') }}"
                                                       class="form-control @error('scheduled_date') is-invalid @enderror" 
                                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('scheduled_date')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Scheduled Time <span class="text-danger">*</span></label>
                                                <input type="time" 
                                                       name="scheduled_time" 
                                                       x-model="scheduledTime"
                                                       :required="needsScheduling"
                                                       value="{{ old('scheduled_time', $lead->scheduled_time ? \Carbon\Carbon::parse($lead->scheduled_time)->format('H:i') : '') }}"
                                                       class="form-control @error('scheduled_time') is-invalid @enderror" 
                                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('scheduled_time')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                        <i class="fas fa-save me-2"></i>Update Lead
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Old Form -->
                    <div x-show="leadType === 'old'">
                        <form action="{{ route('leads.update', $lead) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="type" value="old">
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Brand of Machine <span class="text-danger">*</span></label>
                                    <input type="text" name="brand_name" required value="{{ old('brand_name', $lead->brand_name) }}" 
                                           class="form-control @error('brand_name') is-invalid @enderror" 
                                           placeholder="Enter brand name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('brand_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Machine Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="machine_quantity" required value="{{ old('machine_quantity', $lead->machine_quantity) }}" min="1"
                                           class="form-control @error('machine_quantity') is-invalid @enderror" 
                                           placeholder="Enter machine quantity" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('machine_quantity')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Running Since <span class="text-danger">*</span></label>
                                    <input type="text" name="running_since" required value="{{ old('running_since', $lead->running_since) }}" 
                                           class="form-control @error('running_since') is-invalid @enderror" 
                                           placeholder="Enter running since" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('running_since')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required value="{{ old('name', $lead->name) }}" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" required value="{{ old('phone_number', $lead->phone_number) }}" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           placeholder="Enter phone number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('phone_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">State <span class="text-danger">*</span></label>
                                    <select name="state_id" required id="old_state_id" class="form-select @error('state_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadCities($event.target.value, 'old')">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}" {{ $lead->state_id == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">City <span class="text-danger">*</span></label>
                                    <select name="city_id" required id="old_city_id" class="form-select @error('city_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadAreas($event.target.value, 'old')">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ $lead->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium" style="color: #374151;">Area <span class="text-danger">*</span></label>
                                    <select name="area_id" required id="old_area_id" class="form-select @error('area_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Area</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}" {{ $lead->area_id == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('area_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" required value="{{ old('quantity', $lead->quantity) }}" min="1"
                                           class="form-control @error('quantity') is-invalid @enderror" 
                                           placeholder="Enter quantity" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('quantity')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Planning Month <span class="text-danger">*</span></label>
                                    <select name="planning_month" required class="form-select @error('planning_month') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Month</option>
                                        @foreach($planningMonths as $monthValue => $monthLabel)
                                            <option value="{{ $monthValue }}" {{ (int) old('planning_month', $lead->planning_month ?? now()->month) === $monthValue ? 'selected' : '' }}>{{ $monthLabel }}</option>
                                        @endforeach
                                    </select>
                                    @error('planning_month')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-medium" style="color: #374151;">Planning Year <span class="text-danger">*</span></label>
                                    <select name="planning_year" required class="form-select @error('planning_year') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Year</option>
                                        @foreach($planningYears as $planningYear)
                                            <option value="{{ $planningYear }}" {{ (int) old('planning_year', $lead->planning_year ?? now()->year) === (int) $planningYear ? 'selected' : '' }}>{{ $planningYear }}</option>
                                        @endforeach
                                    </select>
                                    @error('planning_year')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Machine Category <small class="text-muted">(Multiple Select)</small> <span class="text-danger">*</span></label>
                                    <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                        <button type="button" 
                                                @click="categoryDropdownOpen = !categoryDropdownOpen"
                                                class="form-control text-start d-flex justify-content-between align-items-center"
                                                style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                            <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                            <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }" style="transition: transform 0.3s ease;"></i>
                                        </button>
                                        <div x-show="categoryDropdownOpen" 
                                             x-cloak
                                             class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                             style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                             @click.stop>
                                            @forelse($categories ?? [] as $category)
                                                <div class="d-flex align-items-center py-2 px-3" 
                                                     x-data="{ hovered: false }"
                                                     :class="isCategorySelected({{ $category->id }}) ? 'bg-red-50' : ''"
                                                     :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                                     style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                     @mouseenter="hovered = true"
                                                     @mouseleave="hovered = false"
                                                     @click="toggleCategory({{ $category->id }})">
                                                    <input class="form-check-input me-3" 
                                                           type="checkbox" 
                                                           :checked="isCategorySelected({{ $category->id }})"
                                                           style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                           @click.stop="toggleCategory({{ $category->id }})">
                                                    <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                        {{ $category->name }}
                                                    </label>
                                                    <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                                </div>
                                            @empty
                                                <div class="p-3 text-center text-muted">
                                                    <small>No categories available. Add categories first.</small>
                                                </div>
                                            @endforelse
                                        </div>
                                        <template x-for="categoryId in selectedCategories" :key="categoryId">
                                            <input type="hidden" :name="`categories[]`" :value="categoryId">
                                        </template>
                                    </div>
                                    @error('categories')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #374151;">Status <span class="text-danger">*</span></label>
                                    <select name="status_id" required 
                                            x-model="selectedStatusId"
                                            @change="checkSchedulingStatus()"
                                            class="form-select @error('status_id') is-invalid @enderror" 
                                            style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="">Select Status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" 
                                                    data-requires-scheduling="{{ $status->requires_scheduling ? 1 : 0 }}"
                                                    {{ $lead->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Scheduling Toggle and Fields -->
                                <div class="col-md-12" x-show="showSchedulingToggle" x-cloak>
                                    <div class="card p-3 mb-3" style="background-color: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px;">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <label class="form-label fw-medium mb-0" style="color: #374151;">
                                                <i class="fas fa-calendar-alt me-2"></i>Schedule Date & Time
                                            </label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="needs_scheduling_old_edit"
                                                       x-model="needsScheduling"
                                                       style="cursor: pointer; width: 3rem; height: 1.5rem;"
                                                       name="needs_scheduling"
                                                       value="1">
                                                <label class="form-check-label ms-2" for="needs_scheduling_old_edit" style="cursor: pointer;">
                                                    <span x-text="needsScheduling ? 'Enabled' : 'Disabled'"></span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div x-show="needsScheduling" x-cloak class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Scheduled Date <span class="text-danger">*</span></label>
                                                <input type="date" 
                                                       name="scheduled_date" 
                                                       x-model="scheduledDate"
                                                       :required="needsScheduling"
                                                       min="{{ date('Y-m-d') }}"
                                                       value="{{ old('scheduled_date', $lead->scheduled_date ? \Carbon\Carbon::parse($lead->scheduled_date)->format('Y-m-d') : '') }}"
                                                       class="form-control @error('scheduled_date') is-invalid @enderror" 
                                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('scheduled_date')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Scheduled Time <span class="text-danger">*</span></label>
                                                <input type="time" 
                                                       name="scheduled_time" 
                                                       x-model="scheduledTime"
                                                       :required="needsScheduling"
                                                       value="{{ old('scheduled_time', $lead->scheduled_time ? \Carbon\Carbon::parse($lead->scheduled_time)->format('H:i') : '') }}"
                                                       class="form-control @error('scheduled_time') is-invalid @enderror" 
                                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('scheduled_time')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                        <i class="fas fa-save me-2"></i>Update Lead
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function leadForm() {
            return {
                leadType: '{{ $lead->type }}',
                selectedCategories: @json($lead->machineCategories->pluck('id')->map(fn($id) => (string)$id)),
                categoryDropdownOpen: false,
                selectedStatusId: '{{ $lead->status_id }}',
                showSchedulingToggle: false,
                needsScheduling: {{ $lead->needs_scheduling ? 'true' : 'false' }},
                scheduledDate: '{{ $lead->scheduled_date ? \Carbon\Carbon::parse($lead->scheduled_date)->format('Y-m-d') : '' }}',
                scheduledTime: '{{ $lead->scheduled_time ? \Carbon\Carbon::parse($lead->scheduled_time)->format('H:i') : '' }}',

                init() {
                    // Initialize with current lead type
                    this.leadType = '{{ $lead->type }}';
                    
                    // Check if current status requires scheduling
                    if (this.selectedStatusId !== '') {
                        const statusSelect = document.querySelector('select[name="status_id"]');
                        if (statusSelect) {
                            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                            const requiresScheduling = selectedOption ? parseInt(selectedOption.getAttribute('data-requires-scheduling')) === 1 : false;
                            
                            // Show scheduling toggle if:
                            // 1. Status requires scheduling, OR
                            // 2. Lead already has scheduling data (even if status doesn't require it)
                            this.showSchedulingToggle = requiresScheduling || this.needsScheduling;
                            
                            // If status requires scheduling, automatically enable it
                            if (requiresScheduling) {
                                this.needsScheduling = true;
                            }
                        }
                    }
                },

                switchFormType(type) {
                    this.leadType = type;
                    this.categoryDropdownOpen = false;
                },

                checkSchedulingStatus() {
                    // Check if a status is selected and if it requires scheduling
                    if (this.selectedStatusId !== '') {
                        const statusSelect = document.querySelector('select[name="status_id"]');
                        const selectedOption = statusSelect ? statusSelect.options[statusSelect.selectedIndex] : null;
                        const requiresScheduling = selectedOption ? parseInt(selectedOption.getAttribute('data-requires-scheduling')) === 1 : false;
                        
                        // Only show scheduling toggle if the status requires scheduling
                        this.showSchedulingToggle = requiresScheduling;
                        
                        // If status requires scheduling, automatically enable it
                        if (requiresScheduling) {
                            this.needsScheduling = true;
                        } else {
                            // Hide and reset scheduling fields if status doesn't require it
                            this.showSchedulingToggle = false;
                            this.needsScheduling = false;
                            this.scheduledDate = '';
                            this.scheduledTime = '';
                        }
                    } else {
                        // Hide scheduling when no status is selected
                        this.showSchedulingToggle = false;
                        this.needsScheduling = false;
                        this.scheduledDate = '';
                        this.scheduledTime = '';
                    }
                },

                toggleCategory(id) {
                    id = String(id);
                    const index = this.selectedCategories.indexOf(id);
                    index > -1
                        ? this.selectedCategories.splice(index, 1)
                        : this.selectedCategories.push(id);
                },

                isCategorySelected(id) {
                    return this.selectedCategories.includes(String(id));
                },

                loadCities(stateId, formType) {
                    const citySelect = document.getElementById(formType + '_city_id');
                    const areaSelect = document.getElementById(formType + '_area_id');
                    
                    citySelect.innerHTML = '<option value="">Select City</option>';
                    areaSelect.innerHTML = '<option value="">Select Area</option>';
                    
                    if (stateId) {
                        fetch(`/leads/cities/${stateId}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(city => {
                                    const option = document.createElement('option');
                                    option.value = city.id;
                                    option.textContent = city.name;
                                    if (city.id == {{ $lead->city_id }}) {
                                        option.selected = true;
                                        this.loadAreas(city.id, formType);
                                    }
                                    citySelect.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error loading cities:', error));
                    }
                },

                loadAreas(cityId, formType) {
                    const areaSelect = document.getElementById(formType + '_area_id');
                    areaSelect.innerHTML = '<option value="">Select Area</option>';
                    
                    if (cityId) {
                        fetch(`/leads/areas/${cityId}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(area => {
                                    const option = document.createElement('option');
                                    option.value = area.id;
                                    option.textContent = area.name;
                                    if (area.id == {{ $lead->area_id }}) {
                                        option.selected = true;
                                    }
                                    areaSelect.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error loading areas:', error));
                    }
                }
            }
        }
    </script>

    <style>
        .rotate-180 {
            transform: rotate(180deg);
        }
        [x-cloak] {
            display: none !important;
        }
    </style>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <span class="fw-medium">{{ session('success') }}</span>
            </div>
        </div>
        <style>
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    @endif
</x-app-layout>




