<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Contract Details</h1>
            <p class="text-muted mb-0">Contract: {{ $contract->contract_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('contracts.download-pdf', $contract) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </a>
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Contracts
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0"
                style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom"
                        style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Other Contract Details</h2>
                            <small class="text-muted">Buyer: {{ $contract->buyer_name }}</small>
                        </div>
                    </div>

                    <form action="{{ route('contracts.update', $contract) }}" method="POST" id="contractForm"
                        x-data="contractForm()" x-init="init()">

                        @csrf
                        @method('PUT')
                        @if($errors->has('error'))
                            <div class="alert alert-danger mb-3" role="alert">{{ $errors->first('error') }}</div>
                        @endif

                        <!-- Personal Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <h5 class="fw-semibold mb-3" style="color: #1f2937;">Personal Information</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Business
                                                    Firm <span class="text-danger">*</span></label>
                                                <select name="business_firm_id" required
                                                    class="form-select @error('business_firm_id') is-invalid @enderror"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    <option value="">Select Business Firm</option>
                                                    @foreach ($businessFirms as $firm)
                                                        <option value="{{ $firm->id }}"
                                                            {{ old('business_firm_id', $contract->business_firm_id) == $firm->id ? 'selected' : '' }}>
                                                            {{ $firm->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('business_firm_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Contract
                                                    Number</label>
                                                <input type="text" value="{{ $contract->contract_number }}"
                                                    class="form-control"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;" readonly>
                                                <small class="text-muted">Contract number cannot be changed</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Buyer Name
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" name="buyer_name" required
                                                    value="{{ old('buyer_name', $contract->buyer_name) }}"
                                                    class="form-control @error('buyer_name') is-invalid @enderror"
                                                    placeholder="Enter buyer name"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('buyer_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Company
                                                    Name</label>
                                                <input type="text" name="company_name"
                                                    value="{{ old('company_name', $contract->company_name) }}"
                                                    class="form-control @error('company_name') is-invalid @enderror"
                                                    placeholder="Enter company name"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('company_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label fw-medium" style="color: #374151;">Contact
                                                    Address</label>
                                                <textarea name="contact_address" rows="3" class="form-control @error('contact_address') is-invalid @enderror"
                                                    placeholder="Enter contact address" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('contact_address', $contract->contact_address) }}</textarea>
                                                @error('contact_address')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">State
                                                    <span class="text-danger">*</span></label>
                                                <select name="state_id" required id="state_id"
                                                    class="form-select @error('state_id') is-invalid @enderror"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                                    @change="loadCities($event.target.value)">
                                                    <option value="">Select State</option>
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}"
                                                            {{ old('state_id', $contract->state_id) == $state->id ? 'selected' : '' }}>
                                                            {{ $state->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('state_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">City
                                                    <span class="text-danger">*</span></label>
                                                <select name="city_id" required id="city_id"
                                                    class="form-select @error('city_id') is-invalid @enderror"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                                    @change="loadAreas($event.target.value)">
                                                    <option value="">Select City</option>
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}"
                                                            {{ old('city_id', $contract->city_id) == $city->id ? 'selected' : '' }}>
                                                            {{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('city_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Area
                                                    <span class="text-danger">*</span></label>
                                                <select name="area_id" required id="area_id"
                                                    class="form-select @error('area_id') is-invalid @enderror"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    <option value="">Select Area</option>
                                                    @foreach ($areas as $area)
                                                        <option value="{{ $area->id }}"
                                                            {{ old('area_id', $contract->area_id) == $area->id ? 'selected' : '' }}>
                                                            {{ $area->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('area_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">Email</label>
                                                <input type="email" name="email"
                                                    value="{{ old('email', $contract->email) }}"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Enter email"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Phone
                                                    Number <span class="text-danger">*</span></label>
                                                <input type="text" name="phone_number" required
                                                    value="{{ old('phone_number', $contract->phone_number) }}"
                                                    class="form-control @error('phone_number') is-invalid @enderror"
                                                    placeholder="Enter phone number"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('phone_number')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-medium" style="color: #374151;">Phone
                                                    Number 2</label>
                                                <input type="text" name="phone_number_2"
                                                    value="{{ old('phone_number_2', $contract->phone_number_2) }}"
                                                    class="form-control @error('phone_number_2') is-invalid @enderror"
                                                    placeholder="Enter alternate phone number"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('phone_number_2')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">GST</label>
                                                <input type="text" name="gst"
                                                    value="{{ old('gst', $contract->gst) }}"
                                                    class="form-control @error('gst') is-invalid @enderror"
                                                    placeholder="Enter GST"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('gst')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">PAN</label>
                                                <input type="text" name="pan"
                                                    value="{{ old('pan', $contract->pan) }}"
                                                    class="form-control @error('pan') is-invalid @enderror"
                                                    placeholder="Enter PAN"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @error('pan')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Machine Details Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Machine Details</h5>
                                        </div>

                                        <div id="machines-container">
                                            <template x-for="(machine, index) in machines" :key="machine.id ?? index">

                                                <div class="card mb-3"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    <div class="card-body">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <h6 class="mb-0 fw-medium" style="color: #374151;">
                                                                Machine <span x-text="index + 1"></span></h6>
                                                            <button type="button" @click="removeMachine(index)"
                                                                class="btn btn-sm btn-outline-danger"
                                                                style="border-radius: 6px;">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-medium"
                                                                    style="color: #374151;">Machine Category <span
                                                                        class="text-danger">*</span></label>
                                                                <select
                                                                    :name="`machines[${index}][machine_category_id]`"
                                                                    required x-model="machine.machine_category_id"
                                                                    @change="loadCategoryItems(index, $event.target.value)"
                                                                    class="form-select">

                                                                    <option value="">Select category</option>

                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category->id }}">
                                                                            {{ $category->name }}</option>
                                                                    @endforeach
                                                                </select>

                                                            </div>
                                                            <!-- Brand - only show when category is selected -->
                                                            <template x-if="machine.categoryItems">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Brand</label>
                                                                    <select :name="`machines[${index}][brand_id]`"
                                                                        x-model="machine.brand_id"
                                                                        @change="loadMachineModels(index, $event.target.value)"
                                                                        :id="`brand_${index}`" class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="brand in (machine.categoryItems?.brands || [])"
                                                                            :key="brand.id">
                                                                            <option :value="String(brand.id)"
                                                                                x-text="brand.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>
                                                            <!-- Machine Seller - only show when category is selected -->
                                                            <template x-if="machine.categoryItems && machine.categoryItems.sellers && machine.categoryItems.sellers.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Seller</label>
                                                                    <select :name="`machines[${index}][seller_id]`"
                                                                        x-model="machine.seller_id"
                                                                        :id="`seller_${index}`" class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="seller in (machine.categoryItems?.sellers || [])"
                                                                            :key="seller.id">
                                                                            <option :value="String(seller.id)"
                                                                                x-text="seller.seller_name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>
                                                            <!-- Model - only show when brand is selected -->
                                                            <template x-if="machine.brand_id">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Model</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_model_id]`"
                                                                        x-model="machine.machine_model_id"
                                                                        :id="`machine_model_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="model in (machine.machineModels || [])"
                                                                            :key="model.id">
                                                                            <option :value="String(model.id)"
                                                                                x-text="model.model_no"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Size -->
                                                            <template x-if="machine.categoryItems && machine.categoryItems.machine_sizes && machine.categoryItems.machine_sizes.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Size</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_size_id]`"
                                                                        x-model="machine.machine_size_id"
                                                                        :id="`machine_size_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="size in machine.categoryItems.machine_sizes"
                                                                            :key="size.id">
                                                                            <option :value="String(size.id)"
                                                                                x-text="size.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Category-related items (shown dynamically based on category) -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.feeders && machine.categoryItems.feeders.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Feeder</label>
                                                                    <select :name="`machines[${index}][feeder_id]`"
                                                                        x-model="machine.feeder_id"
                                                                        :id="`feeder_${index}`" class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="(feeder, feederIndex) in machine.categoryItems.feeders"
                                                                            :key="feeder.id">
                                                                            <option :value="String(feeder.id)"
                                                                                x-text="feeder.feeder"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Hook -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_hooks && machine.categoryItems.machine_hooks.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Hook</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_hook_id]`"
                                                                        x-model="machine.machine_hook_id"
                                                                        :id="`machine_hook_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="hook in machine.categoryItems.machine_hooks"
                                                                            :key="hook.id">
                                                                            <option :value="String(hook.id)"
                                                                                x-text="hook.hook"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine E-Read -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_e_reads && machine.categoryItems.machine_e_reads.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine E-Read</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_e_read_id]`"
                                                                        x-model="machine.machine_e_read_id"
                                                                        :id="`machine_e_read_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="eread in machine.categoryItems.machine_e_reads"
                                                                            :key="eread.id">
                                                                            <option :value="String(eread.id)"
                                                                                x-text="eread.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Color -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.colors && machine.categoryItems.colors.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Color</label>
                                                                    <select :name="`machines[${index}][color_id]`"
                                                                        x-model="machine.color_id"
                                                                        :id="`color_${index}`" class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="color in machine.categoryItems.colors"
                                                                            :key="color.id">
                                                                            <option :value="String(color.id)"
                                                                                x-text="color.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Nozzle -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_nozzles && machine.categoryItems.machine_nozzles.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Nozzle</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_nozzle_id]`"
                                                                        x-model="machine.machine_nozzle_id"
                                                                        :id="`machine_nozzle_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="nozzle in machine.categoryItems.machine_nozzles"
                                                                            :key="nozzle.id">
                                                                            <option :value="String(nozzle.id)"
                                                                                x-text="nozzle.nozzle"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Dropin -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_dropins && machine.categoryItems.machine_dropins.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Dropin</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_dropin_id]`"
                                                                        x-model="machine.machine_dropin_id"
                                                                        :id="`machine_dropin_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="dropin in machine.categoryItems.machine_dropins"
                                                                            :key="dropin.id">
                                                                            <option :value="String(dropin.id)"
                                                                                x-text="dropin.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Beam -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_beams && machine.categoryItems.machine_beams.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Beam</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_beam_id]`"
                                                                        x-model="machine.machine_beam_id"
                                                                        :id="`machine_beam_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="beam in machine.categoryItems.machine_beams"
                                                                            :key="beam.id">
                                                                            <option :value="String(beam.id)"
                                                                                x-text="beam.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Cloth Roller -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_cloth_rollers && machine.categoryItems.machine_cloth_rollers.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Cloth
                                                                        Roller</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_cloth_roller_id]`"
                                                                        x-model="machine.machine_cloth_roller_id"
                                                                        :id="`machine_cloth_roller_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="roller in machine.categoryItems.machine_cloth_rollers"
                                                                            :key="roller.id">
                                                                            <option :value="String(roller.id)"
                                                                                x-text="roller.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Software -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_softwares && machine.categoryItems.machine_softwares.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine
                                                                        Software</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_software_id]`"
                                                                        x-model="machine.machine_software_id"
                                                                        :id="`machine_software_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="software in machine.categoryItems.machine_softwares"
                                                                            :key="software.id">
                                                                            <option :value="String(software.id)"
                                                                                x-text="software.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- HSN Code -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.hsn_codes && machine.categoryItems.hsn_codes.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">HSN Code</label>
                                                                    <select :name="`machines[${index}][hsn_code_id]`"
                                                                        x-model="machine.hsn_code_id"
                                                                        :id="`hsn_code_${index}`" class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="hsn in machine.categoryItems.hsn_codes"
                                                                            :key="hsn.id">
                                                                            <option :value="String(hsn.id)"
                                                                                x-text="hsn.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- WIR -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.wirs && machine.categoryItems.wirs.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">WIR</label>
                                                                    <select :name="`machines[${index}][wir_id]`"
                                                                        x-model="machine.wir_id"
                                                                        :id="`wir_${index}`" class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="wir in machine.categoryItems.wirs"
                                                                            :key="wir.id">
                                                                            <option :value="String(wir.id)"
                                                                                x-text="wir.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Shaft -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_shafts && machine.categoryItems.machine_shafts.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Shaft</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_shaft_id]`"
                                                                        x-model="machine.machine_shaft_id"
                                                                        :id="`machine_shaft_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="shaft in machine.categoryItems.machine_shafts"
                                                                            :key="shaft.id">
                                                                            <option :value="String(shaft.id)"
                                                                                x-text="shaft.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Lever -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_levers && machine.categoryItems.machine_levers.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Lever</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_lever_id]`"
                                                                        x-model="machine.machine_lever_id"
                                                                        :id="`machine_lever_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="lever in machine.categoryItems.machine_levers"
                                                                            :key="lever.id">
                                                                            <option :value="String(lever.id)"
                                                                                x-text="lever.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Chain -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_chains && machine.categoryItems.machine_chains.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Chain</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_chain_id]`"
                                                                        x-model="machine.machine_chain_id"
                                                                        :id="`machine_chain_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="chain in machine.categoryItems.machine_chains"
                                                                            :key="chain.id">
                                                                            <option :value="String(chain.id)"
                                                                                x-text="chain.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Machine Heald Wire -->
                                                            <template
                                                                x-if="machine.categoryItems && machine.categoryItems.machine_heald_wires && machine.categoryItems.machine_heald_wires.length > 0">
                                                                <div class="col-md-4">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Heald
                                                                        Wire</label>
                                                                    <select
                                                                        :name="`machines[${index}][machine_heald_wire_id]`"
                                                                        x-model="machine.machine_heald_wire_id"
                                                                        :id="`machine_heald_wire_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        <template
                                                                            x-for="wire in machine.categoryItems.machine_heald_wires"
                                                                            :key="wire.id">
                                                                            <option :value="String(wire.id)"
                                                                                x-text="wire.name"></option>
                                                                        </template>
                                                                    </select>
                                                                </div>
                                                            </template>

                                                            <!-- Quantity, Amount and Description at the end - only show when category is selected -->
                                                            <template x-if="machine.categoryItems">
                                                                <div class="col-md-3">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Quantity <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number"
                                                                        :name="`machines[${index}][quantity]`" required
                                                                        x-model="machine.quantity" min="1"
                                                                        class="form-control" placeholder="Quantity"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                </div>
                                                            </template>
                                                            <template x-if="machine.categoryItems">
                                                                <div class="col-md-3">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Amount ($) <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number"
                                                                        :name="`machines[${index}][amount]`" required
                                                                        x-model="machine.amount" step="0.01"
                                                                        min="0" class="form-control"
                                                                        placeholder="0.00"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                </div>
                                                            </template>
                                                            <template x-if="machine.categoryItems">
                                                                <div class="col-md-3">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Machine Amount</label>
                                                                    <div class="form-control bg-light"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb; padding: 0.375rem 0.75rem; display: flex; align-items: center; min-height: 38px;">
                                                                        <span class="fw-semibold"
                                                                            style="color: var(--primary-color);"
                                                                            x-text="'$' + ((parseFloat(machine.quantity) || 0) * (parseFloat(machine.amount) || 0)).toFixed(2)"></span>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <template x-if="machine.categoryItems">
                                                                <div class="col-md-3">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Description</label>
                                                                    <input type="text"
                                                                        :name="`machines[${index}][description]`"
                                                                        x-model="machine.description"
                                                                        class="form-control"
                                                                        placeholder="Machine description (optional)"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                </div>
                                                            </template>
                                                            <template x-if="machine.categoryItems">
                                                                <div class="col-md-3">
                                                                    <label class="form-label fw-medium"
                                                                        style="color: #374151;">Delivery Term</label>
                                                                    <select
                                                                        :name="`machines[${index}][delivery_term_id]`"
                                                                        x-model="machine.delivery_term_id"
                                                                        :id="`delivery_term_${index}`"
                                                                        class="form-select"
                                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                        @foreach($deliveryTerms as $deliveryTerm)
                                                                            <option value="{{ $deliveryTerm->id }}">{{ $deliveryTerm->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                                            <button type="button" @click="addMachine()" class="btn btn-danger" style="border-radius: 8px;">
                                                                + Add Machine
                                                            </button>
                                                        </div>
                                        <!-- Total Machine Amount Summary -->
                                        <div class="mt-4 pt-3 border-top">
                                            <div class="row">
                                                <div class="col-md-6 offset-md-6">
                                                    <div class="card bg-light"
                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <div class="card-body">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <span class="fw-semibold"
                                                                    style="color: #374151; font-size: 1.1rem;">Total
                                                                    Machine Amount:</span>
                                                                <span class="fw-semibold"
                                                                    style="color: var(--primary-color); font-size: 1.2rem;"
                                                                    x-text="'$' + getTotalMachineAmount().toFixed(2)"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Buyer Expenses Details Section (toggle via SHOW_OTHER_BUYER_EXPENSES_SECTION in .env) -->
                        @if(\App\Models\Contract::showOtherBuyerExpensesSection())
                        @php $editShowBuyerExp = filter_var(old('other_buyer_expenses_in_print', $contract->other_buyer_expenses_in_print), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;"
                                    x-data="{ showFields: {{ $editShowBuyerExp ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Other Buyer Expenses
                                                Details</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0"
                                                    style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check"
                                                        name="other_buyer_expenses_in_print" id="buyer_expenses_show"
                                                        value="1"
                                                        {{ $editShowBuyerExp ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm"
                                                        for="buyer_expenses_show"
                                                        style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check"
                                                        name="other_buyer_expenses_in_print" id="buyer_expenses_hide"
                                                        value="0"
                                                        {{ !$editShowBuyerExp ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm"
                                                        for="buyer_expenses_hide"
                                                        style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Overseas
                                                    Freight</label>
                                                <input type="text" name="overseas_freight"
                                                    value="{{ old('overseas_freight', $contract->overseas_freight) }}"
                                                    class="form-control" placeholder="CHA will provide"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">Demurrage / Detention / CFS Charges</label>
                                                <input type="text" name="demurrage_detention_cfs_charges"
                                                    value="{{ old('demurrage_detention_cfs_charges', $contract->demurrage_detention_cfs_charges) }}"
                                                    class="form-control" placeholder="At Actual"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Air Pipe
                                                    Connection</label>
                                                <input type="text" name="air_pipe_connection"
                                                    value="{{ old('air_pipe_connection', $contract->air_pipe_connection) }}"
                                                    class="form-control" placeholder="Enter air pipe connection"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Custom
                                                    Duty</label>
                                                <input type="text" name="custom_duty"
                                                    value="{{ old('custom_duty', $contract->custom_duty) }}"
                                                    class="form-control" placeholder="Enter custom duty"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Port
                                                    Expenses & Transport</label>
                                                <input type="text" name="port_expenses_transport"
                                                    value="{{ old('port_expenses_transport', $contract->port_expenses_transport) }}"
                                                    class="form-control" placeholder="CHA will provide"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Crane &
                                                    Foundation</label>
                                                <input type="text" name="crane_foundation"
                                                    value="{{ old('crane_foundation', $contract->crane_foundation) }}"
                                                    class="form-control" placeholder="By Buyer"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">Humidification</label>
                                                <input type="text" name="humidification"
                                                    value="{{ old('humidification', $contract->humidification) }}"
                                                    class="form-control" placeholder="Enter humidification"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">Damage</label>
                                                <input type="text" name="damage"
                                                    value="{{ old('damage', $contract->damage) }}"
                                                    class="form-control" placeholder="Enter damage"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">GST &
                                                    Custom Charges</label>
                                                <input type="text" name="gst_custom_charges"
                                                    value="{{ old('gst_custom_charges', $contract->gst_custom_charges) }}"
                                                    class="form-control" placeholder="At Actual By Buyer"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">Compressor</label>
                                                <input type="text" name="compressor"
                                                    value="{{ old('compressor', $contract->compressor) }}"
                                                    class="form-control" placeholder="Enter compressor"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Optional
                                                    Spares</label>
                                                <input type="text" name="optional_spares"
                                                    value="{{ old('optional_spares', $contract->optional_spares) }}"
                                                    class="form-control" placeholder="Enter optional spares"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Other Details Section -->
                        @php $editShowOtherDet = filter_var(old('other_details_in_print', $contract->other_details_in_print), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;"
                                    x-data="{ showFields: {{ $editShowOtherDet ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Other Details</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0"
                                                    style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check"
                                                        name="other_details_in_print" id="other_details_show"
                                                        value="1"
                                                        {{ $editShowOtherDet ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm"
                                                        for="other_details_show"
                                                        style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check"
                                                        name="other_details_in_print" id="other_details_hide"
                                                        value="0"
                                                        {{ !$editShowOtherDet ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm"
                                                        for="other_details_hide"
                                                        style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Payment
                                                    Terms</label>
                                                <input type="text" name="payment_terms"
                                                    value="{{ old('payment_terms', $contract->payment_terms) }}"
                                                    class="form-control" placeholder="Enter payment terms"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Quote
                                                    Validity</label>
                                                <input type="text" name="quote_validity"
                                                    value="{{ old('quote_validity', $contract->quote_validity) }}"
                                                    class="form-control" placeholder="Enter quote validity"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Loading
                                                    Terms</label>
                                                <input type="text" name="loading_terms"
                                                    value="{{ old('loading_terms', $contract->loading_terms) }}"
                                                    class="form-control" placeholder="Enter loading terms"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">Warranty</label>
                                                <input type="text" name="warranty"
                                                    value="{{ old('warranty', $contract->warranty) }}"
                                                    class="form-control" placeholder="Enter warranty"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium"
                                                    style="color: #374151;">Complimentary Spares</label>
                                                <input type="text" name="complimentary_spares"
                                                    value="{{ old('complimentary_spares', $contract->complimentary_spares) }}"
                                                    class="form-control" placeholder="Enter complimentary spares"
                                                    style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('contracts.partials.not-included-in-offer-section', [
                            'nioFieldPrefix' => 'not_included_in_offer',
                            'nioPrintField' => 'not_included_in_offer_in_print',
                            'nioShowPrint' => filter_var(old('not_included_in_offer_in_print', $contract->not_included_in_offer_in_print ?? true), FILTER_VALIDATE_BOOLEAN),
                            'nioFlags' => \App\Models\Contract::mergeNotIncludedInOfferFlags(old('not_included_in_offer'), $contract->not_included_in_offer),
                        ])

                        <!-- Difference of Specification Section -->
                        @php $editShowSpecMain = filter_var(old('difference_specification_in_print', $contract->difference_specification_in_print), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;"
                                    x-data="{ showFields: {{ $editShowSpecMain ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification (Rapier - Jacquard)</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0"
                                                    style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check"
                                                        name="difference_specification_in_print"
                                                        id="specification_show" value="1"
                                                        {{ $editShowSpecMain ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm"
                                                        for="specification_show"
                                                        style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check"
                                                        name="difference_specification_in_print"
                                                        id="specification_hide" value="0"
                                                        {{ !$editShowSpecMain ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm"
                                                        for="specification_hide"
                                                        style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">8 Colour Selectors to 12 Colour Selectors</label>
                                                <input type="text" name="spec_color_8_to_12_selectors" value="{{ old('spec_color_8_to_12_selectors', $contract->spec_color_8_to_12_selectors) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Extra Feeder (Per PC)</label>
                                                <input type="text" name="spec_extra_feeder_per_pc" value="{{ old('spec_extra_feeder_per_pc', $contract->spec_extra_feeder_per_pc) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Extra Warp Beam (Per PC)</label>
                                                <input type="text" name="spec_extra_warp_beam_per_pc" value="{{ old('spec_extra_warp_beam_per_pc', $contract->spec_extra_warp_beam_per_pc) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Reduction of Every 20cm in Reed Space</label>
                                                <input type="text" name="spec_reed_reduction_per_20cm" value="{{ old('spec_reed_reduction_per_20cm', $contract->spec_reed_reduction_per_20cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Increase of Every 20cm in Reed Space</label>
                                                <input type="text" name="spec_reed_increase_per_20cm" value="{{ old('spec_reed_increase_per_20cm', $contract->spec_reed_increase_per_20cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Increase from 380cm to 480cm</label>
                                                <input type="text" name="spec_increase_380_to_480cm" value="{{ old('spec_increase_380_to_480cm', $contract->spec_increase_380_to_480cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Electronic Weft Cutter</label>
                                                <input type="text" name="spec_electronic_weft_cutter" value="{{ old('spec_electronic_weft_cutter', $contract->spec_electronic_weft_cutter) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 6144 Hooks</label>
                                                <input type="text" name="spec_hooks_5376_to_6144" value="{{ old('spec_hooks_5376_to_6144', $contract->spec_hooks_5376_to_6144) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 10240 Hooks</label>
                                                <input type="text" name="spec_hooks_5376_to_10240" value="{{ old('spec_hooks_5376_to_10240', $contract->spec_hooks_5376_to_10240) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 2688 Hooks</label>
                                                <input type="text" name="spec_hooks_5376_to_2688" value="{{ old('spec_hooks_5376_to_2688', $contract->spec_hooks_5376_to_2688) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS CAM</label>
                                                <input type="text" name="spec_changshu_to_sns_cam" value="{{ old('spec_changshu_to_sns_cam', $contract->spec_changshu_to_sns_cam) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS Chain (24 Line)</label>
                                                <input type="text" name="spec_changshu_to_sns_chain_24" value="{{ old('spec_changshu_to_sns_chain_24', $contract->spec_changshu_to_sns_chain_24) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS Chain (16 Line)</label>
                                                <input type="text" name="spec_changshu_to_sns_chain_16" value="{{ old('spec_changshu_to_sns_chain_16', $contract->spec_changshu_to_sns_chain_16) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to JKD or ChangFang</label>
                                                <input type="text" name="spec_changshu_to_jkd_or_changfang" value="{{ old('spec_changshu_to_jkd_or_changfang', $contract->spec_changshu_to_jkd_or_changfang) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to Wumu</label>
                                                <input type="text" name="spec_changshu_to_wumu" value="{{ old('spec_changshu_to_wumu', $contract->spec_changshu_to_wumu) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Difference of Specification (Airjet) -->
                        @php $editShowSpecExt = filter_var(old('difference_specification_extended_in_print', $contract->difference_specification_extended_in_print ?? false), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;"
                                    x-data="{ showFields: {{ $editShowSpecExt ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification (Airjet)</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="difference_specification_extended_in_print" id="edit_spec_ext_show" value="1" {{ $editShowSpecExt ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="edit_spec_ext_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="difference_specification_extended_in_print" id="edit_spec_ext_hide" value="0" {{ !$editShowSpecExt ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="edit_spec_ext_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            @foreach (\App\Models\Contract::differenceSpecificationExtendedLabels() as $field => $label)
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">{{ $label }}</label>
                                                <input type="text" name="{{ $field }}" value="{{ old($field, $contract->{$field}) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Difference of Specification (Waterjet) -->
                        @php $editShowSpec3 = filter_var(old('difference_specification_3_in_print', $contract->difference_specification_3_in_print ?? false), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;"
                                    x-data="{ showFields: {{ $editShowSpec3 ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification (Waterjet)</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="difference_specification_3_in_print" id="edit_spec_3_show" value="1" {{ $editShowSpec3 ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="edit_spec_3_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="difference_specification_3_in_print" id="edit_spec_3_hide" value="0" {{ !$editShowSpec3 ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="edit_spec_3_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            @foreach (\App\Models\Contract::differenceSpecification3Labels() as $field => $label)
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">{{ $label }}</label>
                                                <input type="text" name="{{ $field }}" value="{{ old($field, $contract->{$field}) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & conditions -->
                        @php $editShowTerms = filter_var(old('terms_conditions_in_print', $contract->terms_conditions_in_print ?? true), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0"
                                    style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;"
                                    x-data="{ showFields: {{ $editShowTerms ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Terms &amp; conditions</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="terms_conditions_in_print" id="edit_terms_conditions_show" value="1" {{ $editShowTerms ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="edit_terms_conditions_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="terms_conditions_in_print" id="edit_terms_conditions_hide" value="0" {{ !$editShowTerms ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="edit_terms_conditions_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            @foreach (\App\Models\Contract::termsConditionsLabels() as $field => $label)
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #374151;">{{ $label }}</label>
                                                <textarea name="{{ $field }}" rows="4" class="form-control" placeholder="Enter {{ strtolower($label) }}" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old($field, $contract->{$field} ?? $settings->{'global_' . $field}) }}</textarea>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Token Amount Section -->
                        <div class="row mt-4 pt-4 border-top" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Token Amount (₹)</label>
                                <input type="number" name="token_amount" value="{{ old('token_amount', $contract->token_amount) }}" 
                                       step="0.01" min="0"
                                       class="form-control @error('token_amount') is-invalid @enderror" 
                                       placeholder="Enter token amount in ₹" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('token_amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional: Enter the token amount in Indian Rupees (₹)</small>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                                    <i class="fas fa-save me-2"></i>Update Contract Details
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg"
            style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <script>
        function contractForm() {
            const existingMachines = @json($machinesData);

            return {
                machines: existingMachines.length > 0 ? existingMachines : [{
                    machine_category_id: '',
                    brand_id: '',
                    machine_model_id: '',
                    seller_id: '',
                    quantity: '',
                    description: '',
                    categoryItems: null,
                    machineModels: [],
                    amount: 0,
                    machine_size_id: '',
                    feeder_id: '',
                    machine_hook_id: '',
                    machine_e_read_id: '',
                    color_id: '',
                    machine_nozzle_id: '',
                    machine_dropin_id: '',
                    machine_beam_id: '',
                    machine_cloth_roller_id: '',
                    machine_software_id: '',
                    hsn_code_id: '',
                    wir_id: '',
                    machine_shaft_id: '',
                    machine_lever_id: '',
                    machine_chain_id: '',
                    machine_heald_wire_id: '',
                    delivery_term_id: ''
                }],

                async init() {
                    // Load category items for existing machines
                    for (let index = 0; index < this.machines.length; index++) {
                        const machine = this.machines[index];
                        // Ensure all IDs are strings (preserve actual values, convert null/undefined to empty string)
                        machine.machine_category_id = machine.machine_category_id != null ? String(machine.machine_category_id) : '';
                        machine.brand_id = machine.brand_id != null ? String(machine.brand_id) : '';
                        machine.machine_model_id = machine.machine_model_id != null ? String(machine.machine_model_id) : '';
                        machine.machine_size_id = machine.machine_size_id != null ? String(machine.machine_size_id) : '';
                        machine.seller_id = machine.seller_id != null ? String(machine.seller_id) : '';
                        // Convert all other IDs to strings (preserve actual values including 0)
                        machine.feeder_id = machine.feeder_id != null ? String(machine.feeder_id) : '';
                        machine.machine_hook_id = machine.machine_hook_id != null ? String(machine.machine_hook_id) : '';
                        machine.machine_e_read_id = machine.machine_e_read_id != null ? String(machine.machine_e_read_id) : '';
                        machine.color_id = machine.color_id != null ? String(machine.color_id) : '';
                        machine.machine_nozzle_id = machine.machine_nozzle_id != null ? String(machine.machine_nozzle_id) : '';
                        machine.machine_dropin_id = machine.machine_dropin_id != null ? String(machine.machine_dropin_id) : '';
                        machine.machine_beam_id = machine.machine_beam_id != null ? String(machine.machine_beam_id) : '';
                        machine.machine_cloth_roller_id = machine.machine_cloth_roller_id != null ? String(machine.machine_cloth_roller_id) : '';
                        machine.machine_software_id = machine.machine_software_id != null ? String(machine.machine_software_id) : '';
                        machine.hsn_code_id = machine.hsn_code_id != null ? String(machine.hsn_code_id) : '';
                        machine.wir_id = machine.wir_id != null ? String(machine.wir_id) : '';
                        machine.machine_shaft_id = machine.machine_shaft_id != null ? String(machine.machine_shaft_id) : '';
                        machine.machine_lever_id = machine.machine_lever_id != null ? String(machine.machine_lever_id) : '';
                        machine.machine_chain_id = machine.machine_chain_id != null ? String(machine.machine_chain_id) : '';
                        machine.machine_heald_wire_id = machine.machine_heald_wire_id != null ? String(machine.machine_heald_wire_id) : '';
                        machine.delivery_term_id = machine.delivery_term_id != null ? String(machine.delivery_term_id) : '';

                        // Load category items after ensuring IDs are strings
                        if (machine.machine_category_id) {
                            await this.loadCategoryItems(index, machine.machine_category_id, true);
                        }
                    }
                },

                getTotalMachineAmount() {
                    return this.machines.reduce((total, machine) => {
                        return total + ((parseFloat(machine.quantity) || 0) * (parseFloat(machine.amount) || 0));
                    }, 0);
                },

                addMachine() {
                    this.machines.push({
                        machine_category_id: '',
                        brand_id: '',
                        machine_model_id: '',
                        seller_id: '',
                        quantity: '',
                        description: '',
                        categoryItems: null,
                        machineModels: [],
                        amount: 0,
                        machine_size_id: '',
                        feeder_id: '',
                        machine_hook_id: '',
                        machine_e_read_id: '',
                        color_id: '',
                        machine_nozzle_id: '',
                        machine_dropin_id: '',
                        machine_beam_id: '',
                        machine_cloth_roller_id: '',
                        machine_software_id: '',
                        hsn_code_id: '',
                        wir_id: '',
                        machine_shaft_id: '',
                        machine_lever_id: '',
                        machine_chain_id: '',
                        machine_heald_wire_id: '',
                        delivery_term_id: ''
                    });
                },

                removeMachine(index) {
                    if (this.machines.length > 1) {
                        this.machines.splice(index, 1);
                    } else {
                        alert('At least one machine is required.');
                    }
                },

                async loadMachineModels(index, brandId) {
                    if (!brandId) {
                        this.machines[index].machine_model_id = '';
                        this.machines[index].machineModels = [];
                        return;
                    }
                    try {
                        // Store existing model_id before loading
                        const existingModelId = this.machines[index].machine_model_id;

                        const categoryId = this.machines[index]?.machine_category_id;
                        const query = categoryId ? `?category_id=${encodeURIComponent(String(categoryId))}` : '';
                        const response = await fetch(`{{ url('leads/machine-models') }}/${brandId}${query}`);
                        const models = await response.json();
                        this.machines[index].machineModels = models;

                        // Wait for Alpine to update DOM
                        await this.$nextTick();

                        if (models && models.length > 0) {
                            // If there's an existing model_id, try to keep it if it exists in the new list
                            if (existingModelId) {
                                const modelExists = models.some(m => String(m.id) === String(existingModelId));
                                if (modelExists) {
                                    this.machines[index].machine_model_id = String(existingModelId);
                                } else {
                                    // If existing model not found, select first one
                                    this.machines[index].machine_model_id = String(models[0].id);
                                }
                            } else {
                                // No existing model, select first one
                                this.machines[index].machine_model_id = String(models[0].id);
                            }
                        } else {
                            this.machines[index].machine_model_id = '';
                        }
                    } catch (error) {
                        console.error('Error loading models:', error);
                        this.machines[index].machineModels = [];
                        this.machines[index].machine_model_id = '';
                    }
                },

                async loadCategoryItems(index, categoryId, preserveValues = false) {
                    if (!categoryId) {
                        this.machines[index].categoryItems = null;
                        this.machines[index].brand_id = '';
                        this.machines[index].machine_model_id = '';
                        this.machines[index].machine_size_id = '';
                        this.machines[index].seller_id = '';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/category-items') }}/${categoryId}`);
                        const items = await response.json();

                        // Store existing values BEFORE loading category items
                        // Read values directly from the machine object (already converted to strings in init)
                        // Preserve actual values including empty strings (use nullish coalescing only for null/undefined)
                        const existingBrandId = preserveValues ? (this.machines[index].brand_id != null ? String(this.machines[index].brand_id) : '') : '';
                        const existingModelId = preserveValues ? (this.machines[index].machine_model_id != null ? String(this.machines[index].machine_model_id) : '') : '';
                        const existingMachineSizeId = preserveValues ? (this.machines[index].machine_size_id != null ? String(this.machines[index].machine_size_id) : '') : '';
                        const existingSellerId = preserveValues ? (this.machines[index].seller_id != null ? String(this.machines[index].seller_id) : '') : '';
                        const existingFeederId = preserveValues ? (this.machines[index].feeder_id != null ? String(this.machines[index].feeder_id) : '') : '';
                        const existingHookId = preserveValues ? (this.machines[index].machine_hook_id != null ? String(this.machines[index].machine_hook_id) : '') : '';
                        const existingEReadId = preserveValues ? (this.machines[index].machine_e_read_id != null ? String(this.machines[index].machine_e_read_id) : '') : '';
                        const existingColorId = preserveValues ? (this.machines[index].color_id != null ? String(this.machines[index].color_id) : '') : '';
                        const existingNozzleId = preserveValues ? (this.machines[index].machine_nozzle_id != null ? String(this.machines[index].machine_nozzle_id) : '') : '';
                        // Store Machine Dropin ID explicitly - preserve the original value
                        const existingDropinId = preserveValues ? (this.machines[index].machine_dropin_id != null && this.machines[index].machine_dropin_id !== '' ? String(this.machines[index].machine_dropin_id) : '') : '';
                        const existingBeamId = preserveValues ? (this.machines[index].machine_beam_id != null ? String(this.machines[index].machine_beam_id) : '') : '';
                        const existingClothRollerId = preserveValues ? (this.machines[index].machine_cloth_roller_id != null ? String(this.machines[index].machine_cloth_roller_id) : '') : '';
                        const existingSoftwareId = preserveValues ? (this.machines[index].machine_software_id != null ? String(this.machines[index].machine_software_id) : '') : '';
                        const existingHsnCodeId = preserveValues ? (this.machines[index].hsn_code_id != null ? String(this.machines[index].hsn_code_id) : '') : '';
                        const existingWirId = preserveValues ? (this.machines[index].wir_id != null ? String(this.machines[index].wir_id) : '') : '';
                        const existingShaftId = preserveValues ? (this.machines[index].machine_shaft_id != null ? String(this.machines[index].machine_shaft_id) : '') : '';
                        const existingLeverId = preserveValues ? (this.machines[index].machine_lever_id != null ? String(this.machines[index].machine_lever_id) : '') : '';
                        const existingChainId = preserveValues ? (this.machines[index].machine_chain_id != null ? String(this.machines[index].machine_chain_id) : '') : '';
                        const existingHealdWireId = preserveValues ? (this.machines[index].machine_heald_wire_id != null ? String(this.machines[index].machine_heald_wire_id) : '') : '';

                        // Set categoryItems AFTER storing values
                        this.machines[index].categoryItems = items;

                        // Wait for Alpine to update the DOM
                        await this.$nextTick();

                        if (!preserveValues) {
                            // Reset brand, model, and seller when category changes
                            this.machines[index].brand_id = '';
                            this.machines[index].machine_model_id = '';
                            this.machines[index].seller_id = '';
                            this.machines[index].machineModels = [];

                            // Auto-select first brand if available
                            if (items.brands && items.brands.length > 0) {
                                await this.$nextTick();
                                this.machines[index].brand_id = String(items.brands[0].id);
                                await this.loadMachineModels(index, items.brands[0].id);
                            }

                            // Auto-select first value in category-related dropdowns
                            await this.$nextTick();
                            if (items.feeders && items.feeders.length > 0) {
                                this.machines[index].feeder_id = String(items.feeders[0].id);
                            }
                            // Restore seller if preserved
                            if (preserveValues && existingSellerId && existingSellerId !== '' && items.sellers && items.sellers.length > 0) {
                                const sellerIdStr = String(existingSellerId);
                                const sellerExists = items.sellers.some(s => String(s.id) === sellerIdStr);
                                if (sellerExists) {
                                    this.machines[index].seller_id = sellerIdStr;
                                }
                            }
                            if (items.machine_hooks && items.machine_hooks.length > 0) {
                                this.machines[index].machine_hook_id = String(items.machine_hooks[0].id);
                            }
                            if (items.machine_e_reads && items.machine_e_reads.length > 0) {
                                this.machines[index].machine_e_read_id = String(items.machine_e_reads[0].id);
                            }
                            if (items.colors && items.colors.length > 0) {
                                this.machines[index].color_id = String(items.colors[0].id);
                            }
                            if (items.machine_nozzles && items.machine_nozzles.length > 0) {
                                this.machines[index].machine_nozzle_id = String(items.machine_nozzles[0].id);
                            }
                            if (items.machine_sizes && items.machine_sizes.length > 0) {
                                this.machines[index].machine_size_id = String(items.machine_sizes[0].id);
                            }
                            if (items.machine_dropins && items.machine_dropins.length > 0) {
                                this.machines[index].machine_dropin_id = String(items.machine_dropins[0].id);
                            }
                            if (items.machine_beams && items.machine_beams.length > 0) {
                                this.machines[index].machine_beam_id = String(items.machine_beams[0].id);
                            }
                            if (items.machine_cloth_rollers && items.machine_cloth_rollers.length > 0) {
                                this.machines[index].machine_cloth_roller_id = String(items.machine_cloth_rollers[0]
                                .id);
                            }
                            if (items.machine_softwares && items.machine_softwares.length > 0) {
                                this.machines[index].machine_software_id = String(items.machine_softwares[0].id);
                            }
                            if (items.hsn_codes && items.hsn_codes.length > 0) {
                                this.machines[index].hsn_code_id = String(items.hsn_codes[0].id);
                            }
                            if (items.wirs && items.wirs.length > 0) {
                                this.machines[index].wir_id = String(items.wirs[0].id);
                            }
                            if (items.machine_shafts && items.machine_shafts.length > 0) {
                                this.machines[index].machine_shaft_id = String(items.machine_shafts[0].id);
                            }
                            if (items.machine_levers && items.machine_levers.length > 0) {
                                this.machines[index].machine_lever_id = String(items.machine_levers[0].id);
                            }
                            if (items.machine_chains && items.machine_chains.length > 0) {
                                this.machines[index].machine_chain_id = String(items.machine_chains[0].id);
                            }
                            if (items.machine_heald_wires && items.machine_heald_wires.length > 0) {
                                this.machines[index].machine_heald_wire_id = String(items.machine_heald_wires[0].id);
                            }
                        } else {
                            // Preserve existing values - restore after category items are loaded
                            await this.$nextTick();

                            // Restore brand_id first, then load models
                            await this.$nextTick();
                            
                            if (existingBrandId && existingBrandId !== '' && items.brands && items.brands.length > 0) {
                                const brandIdStr = String(existingBrandId);
                                const brandExists = items.brands.some(b => String(b.id) === brandIdStr);
                                if (brandExists) {
                                    // Set brand_id multiple times
                                    this.machines[index].brand_id = brandIdStr;
                                    await this.$nextTick();
                                    this.machines[index].brand_id = brandIdStr;
                                    await this.$nextTick();
                                    this.machines[index].brand_id = brandIdStr;
                                    // Direct DOM manipulation
                                    await this.$nextTick();
                                    const brandSelectElement = document.getElementById(`brand_${index}`);
                                    if (brandSelectElement) {
                                        brandSelectElement.value = brandIdStr;
                                        brandSelectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                    
                                    // Load models for the brand
                                    await this.loadMachineModels(index, brandIdStr);
                                    
                                    // Restore model_id after models are loaded
                                    await this.$nextTick();
                                    if (existingModelId && existingModelId !== '' && this.machines[index]
                                        .machineModels && this.machines[index].machineModels.length > 0) {
                                        const modelIdStr = String(existingModelId);
                                        const modelExists = this.machines[index].machineModels.some(m => String(m
                                            .id) === modelIdStr);
                                        if (modelExists) {
                                            this.machines[index].machine_model_id = modelIdStr;
                                            await this.$nextTick();
                                            this.machines[index].machine_model_id = modelIdStr;
                                            await this.$nextTick();
                                            this.machines[index].machine_model_id = modelIdStr;
                                            // Direct DOM manipulation
                                            await this.$nextTick();
                                            const modelSelectElement = document.getElementById(`machine_model_${index}`);
                                            if (modelSelectElement) {
                                                modelSelectElement.value = modelIdStr;
                                                modelSelectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                            }
                                        }
                                    }
                                }
                            }

                            // Restore all other category-related IDs - ensure they exist in loaded items
                            await this.$nextTick();

                            // Helper function to restore a value
                            const restoreValue = (itemArray, existingId, propertyName) => {
                                // If there's an existing ID (not empty string and not null/undefined), try to restore it
                                if (existingId != null && existingId !== '' && itemArray && itemArray.length > 0) {
                                    const idStr = String(existingId);
                                    // Find the matching item
                                    const exists = itemArray.some(item => String(item.id) === idStr);
                                    if (exists) {
                                        // Force set the value explicitly
                                        this.machines[index][propertyName] = idStr;
                                        return true;
                                    }
                                }
                                return false;
                            };

                            // Restore all values explicitly - ensure correct values are restored
                            // Sellers
                            await this.$nextTick();
                            if (existingSellerId != null && existingSellerId !== '' && items.sellers && items.sellers.length > 0) {
                                const sellerIdStr = String(existingSellerId);
                                const sellerExists = items.sellers.some(s => String(s.id) === sellerIdStr);
                                if (sellerExists) {
                                    this.machines[index].seller_id = sellerIdStr;
                                    await this.$nextTick();
                                    this.machines[index].seller_id = sellerIdStr;
                                    await this.$nextTick();
                                    this.machines[index].seller_id = sellerIdStr;
                                    const selectElement = document.getElementById(`seller_${index}`);
                                    if (selectElement) {
                                        selectElement.value = sellerIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                }
                            }

                            // Feeders
                            await this.$nextTick();
                            
                            if (existingFeederId != null && existingFeederId !== '' && items.feeders && items.feeders.length > 0) {
                                const feederIdStr = String(existingFeederId);
                                const feederExists = items.feeders.some(f => String(f.id) === feederIdStr);
                                if (feederExists) {
                                    this.machines[index].feeder_id = feederIdStr;
                                    await this.$nextTick();
                                    this.machines[index].feeder_id = feederIdStr;
                                    await this.$nextTick();
                                    this.machines[index].feeder_id = feederIdStr;
                                    const selectElement = document.getElementById(`feeder_${index}`);
                                    if (selectElement) {
                                        selectElement.value = feederIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.feeders.length > 0) {
                                    this.machines[index].feeder_id = String(items.feeders[0].id);
                                }
                            } else if (items.feeders && items.feeders.length > 0) {
                                this.machines[index].feeder_id = String(items.feeders[0].id);
                            }
                            await this.$nextTick();

                            // Machine Hooks
                            await this.$nextTick();
                            
                            if (existingHookId != null && existingHookId !== '' && items.machine_hooks && items.machine_hooks.length > 0) {
                                const hookIdStr = String(existingHookId);
                                const hookExists = items.machine_hooks.some(h => String(h.id) === hookIdStr);
                                if (hookExists) {
                                    this.machines[index].machine_hook_id = hookIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_hook_id = hookIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_hook_id = hookIdStr;
                                    const selectElement = document.getElementById(`machine_hook_${index}`);
                                    if (selectElement) {
                                        selectElement.value = hookIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_hooks.length > 0) {
                                    this.machines[index].machine_hook_id = String(items.machine_hooks[0].id);
                                }
                            } else if (items.machine_hooks && items.machine_hooks.length > 0) {
                                this.machines[index].machine_hook_id = String(items.machine_hooks[0].id);
                            }
                            await this.$nextTick();

                            // Machine E-Reads
                            await this.$nextTick();
                            
                            if (existingEReadId != null && existingEReadId !== '' && items.machine_e_reads && items.machine_e_reads.length > 0) {
                                const eReadIdStr = String(existingEReadId);
                                const eReadExists = items.machine_e_reads.some(e => String(e.id) === eReadIdStr);
                                if (eReadExists) {
                                    this.machines[index].machine_e_read_id = eReadIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_e_read_id = eReadIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_e_read_id = eReadIdStr;
                                    const selectElement = document.getElementById(`machine_e_read_${index}`);
                                    if (selectElement) {
                                        selectElement.value = eReadIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_e_reads.length > 0) {
                                    this.machines[index].machine_e_read_id = String(items.machine_e_reads[0].id);
                                }
                            } else if (items.machine_e_reads && items.machine_e_reads.length > 0) {
                                this.machines[index].machine_e_read_id = String(items.machine_e_reads[0].id);
                            }
                            await this.$nextTick();

                            // Colors
                            await this.$nextTick();
                            
                            if (existingColorId != null && existingColorId !== '' && items.colors && items.colors.length > 0) {
                                const colorIdStr = String(existingColorId);
                                const colorExists = items.colors.some(c => String(c.id) === colorIdStr);
                                if (colorExists) {
                                    this.machines[index].color_id = colorIdStr;
                                    await this.$nextTick();
                                    this.machines[index].color_id = colorIdStr;
                                    await this.$nextTick();
                                    this.machines[index].color_id = colorIdStr;
                                    const selectElement = document.getElementById(`color_${index}`);
                                    if (selectElement) {
                                        selectElement.value = colorIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.colors.length > 0) {
                                    this.machines[index].color_id = String(items.colors[0].id);
                                }
                            } else if (items.colors && items.colors.length > 0) {
                                this.machines[index].color_id = String(items.colors[0].id);
                            }
                            await this.$nextTick();

                            // Machine Nozzles
                            await this.$nextTick();
                            
                            if (existingNozzleId != null && existingNozzleId !== '' && items.machine_nozzles && items.machine_nozzles.length > 0) {
                                const nozzleIdStr = String(existingNozzleId);
                                const nozzleExists = items.machine_nozzles.some(n => String(n.id) === nozzleIdStr);
                                if (nozzleExists) {
                                    this.machines[index].machine_nozzle_id = nozzleIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_nozzle_id = nozzleIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_nozzle_id = nozzleIdStr;
                                    const selectElement = document.getElementById(`machine_nozzle_${index}`);
                                    if (selectElement) {
                                        selectElement.value = nozzleIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_nozzles.length > 0) {
                                    this.machines[index].machine_nozzle_id = String(items.machine_nozzles[0].id);
                                }
                            } else if (items.machine_nozzles && items.machine_nozzles.length > 0) {
                                this.machines[index].machine_nozzle_id = String(items.machine_nozzles[0].id);
                            }
                            // Machine Sizes
                            await this.$nextTick();
                            if (existingMachineSizeId != null && existingMachineSizeId !== '' && items.machine_sizes && items.machine_sizes.length > 0) {
                                const sizeIdStr = String(existingMachineSizeId);
                                const sizeExists = items.machine_sizes.some(s => String(s.id) === sizeIdStr);
                                if (sizeExists) {
                                    this.machines[index].machine_size_id = sizeIdStr;
                                    await this.$nextTick();
                                    const selectElement = document.getElementById(`machine_size_${index}`);
                                    if (selectElement) {
                                        selectElement.value = sizeIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_sizes.length > 0) {
                                    this.machines[index].machine_size_id = String(items.machine_sizes[0].id);
                                }
                            } else if (items.machine_sizes && items.machine_sizes.length > 0) {
                                this.machines[index].machine_size_id = String(items.machine_sizes[0].id);
                            }
                            await this.$nextTick();

                            // Machine Dropins - ensure correct value is restored
                            await this.$nextTick();
                            
                            if (existingDropinId != null && existingDropinId !== '' && items.machine_dropins && items.machine_dropins.length > 0) {
                                const dropinIdStr = String(existingDropinId);
                                const dropinExists = items.machine_dropins.some(d => String(d.id) === dropinIdStr);
                                if (dropinExists) {
                                    // Set the value multiple times to ensure Alpine.js picks it up
                                    this.machines[index].machine_dropin_id = dropinIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_dropin_id = dropinIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_dropin_id = dropinIdStr;
                                    // Also try to set it directly on the select element if it exists
                                    await this.$nextTick();
                                    const selectElement = document.getElementById(`machine_dropin_${index}`);
                                    if (selectElement) {
                                        selectElement.value = dropinIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else {
                                    // If existing value not found, select first
                                    if (items.machine_dropins.length > 0) {
                                        this.machines[index].machine_dropin_id = String(items.machine_dropins[0].id);
                                    }
                                }
                            } else {
                                // No existing value, select first
                                if (items.machine_dropins && items.machine_dropins.length > 0) {
                                    this.machines[index].machine_dropin_id = String(items.machine_dropins[0].id);
                                }
                            }
                            await this.$nextTick();

                            // Machine Beams
                            await this.$nextTick();
                            
                            if (existingBeamId != null && existingBeamId !== '' && items.machine_beams && items.machine_beams.length > 0) {
                                const beamIdStr = String(existingBeamId);
                                const beamExists = items.machine_beams.some(b => String(b.id) === beamIdStr);
                                if (beamExists) {
                                    this.machines[index].machine_beam_id = beamIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_beam_id = beamIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_beam_id = beamIdStr;
                                    const selectElement = document.getElementById(`machine_beam_${index}`);
                                    if (selectElement) {
                                        selectElement.value = beamIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_beams.length > 0) {
                                    this.machines[index].machine_beam_id = String(items.machine_beams[0].id);
                                }
                            } else if (items.machine_beams && items.machine_beams.length > 0) {
                                this.machines[index].machine_beam_id = String(items.machine_beams[0].id);
                            }
                            await this.$nextTick();

                            // Machine Cloth Rollers
                            await this.$nextTick();
                            
                            if (existingClothRollerId != null && existingClothRollerId !== '' && items.machine_cloth_rollers && items.machine_cloth_rollers.length > 0) {
                                const rollerIdStr = String(existingClothRollerId);
                                const rollerExists = items.machine_cloth_rollers.some(r => String(r.id) === rollerIdStr);
                                if (rollerExists) {
                                    this.machines[index].machine_cloth_roller_id = rollerIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_cloth_roller_id = rollerIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_cloth_roller_id = rollerIdStr;
                                    const selectElement = document.getElementById(`machine_cloth_roller_${index}`);
                                    if (selectElement) {
                                        selectElement.value = rollerIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_cloth_rollers.length > 0) {
                                    this.machines[index].machine_cloth_roller_id = String(items.machine_cloth_rollers[0].id);
                                }
                            } else if (items.machine_cloth_rollers && items.machine_cloth_rollers.length > 0) {
                                this.machines[index].machine_cloth_roller_id = String(items.machine_cloth_rollers[0].id);
                            }
                            await this.$nextTick();

                            // Machine Softwares
                            await this.$nextTick();
                            
                            if (existingSoftwareId != null && existingSoftwareId !== '' && items.machine_softwares && items.machine_softwares.length > 0) {
                                const softwareIdStr = String(existingSoftwareId);
                                const softwareExists = items.machine_softwares.some(s => String(s.id) === softwareIdStr);
                                if (softwareExists) {
                                    this.machines[index].machine_software_id = softwareIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_software_id = softwareIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_software_id = softwareIdStr;
                                    const selectElement = document.getElementById(`machine_software_${index}`);
                                    if (selectElement) {
                                        selectElement.value = softwareIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_softwares.length > 0) {
                                    this.machines[index].machine_software_id = String(items.machine_softwares[0].id);
                                }
                            } else if (items.machine_softwares && items.machine_softwares.length > 0) {
                                this.machines[index].machine_software_id = String(items.machine_softwares[0].id);
                            }
                            await this.$nextTick();

                            // HSN Codes
                            await this.$nextTick();
                            
                            if (existingHsnCodeId != null && existingHsnCodeId !== '' && items.hsn_codes && items.hsn_codes.length > 0) {
                                const hsnIdStr = String(existingHsnCodeId);
                                const hsnExists = items.hsn_codes.some(h => String(h.id) === hsnIdStr);
                                if (hsnExists) {
                                    this.machines[index].hsn_code_id = hsnIdStr;
                                    await this.$nextTick();
                                    this.machines[index].hsn_code_id = hsnIdStr;
                                    await this.$nextTick();
                                    this.machines[index].hsn_code_id = hsnIdStr;
                                    const selectElement = document.getElementById(`hsn_code_${index}`);
                                    if (selectElement) {
                                        selectElement.value = hsnIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.hsn_codes.length > 0) {
                                    this.machines[index].hsn_code_id = String(items.hsn_codes[0].id);
                                }
                            } else if (items.hsn_codes && items.hsn_codes.length > 0) {
                                this.machines[index].hsn_code_id = String(items.hsn_codes[0].id);
                            }
                            await this.$nextTick();

                            // WIRs
                            await this.$nextTick();
                            
                            if (existingWirId != null && existingWirId !== '' && items.wirs && items.wirs.length > 0) {
                                const wirIdStr = String(existingWirId);
                                const wirExists = items.wirs.some(w => String(w.id) === wirIdStr);
                                if (wirExists) {
                                    this.machines[index].wir_id = wirIdStr;
                                    await this.$nextTick();
                                    this.machines[index].wir_id = wirIdStr;
                                    await this.$nextTick();
                                    this.machines[index].wir_id = wirIdStr;
                                    const selectElement = document.getElementById(`wir_${index}`);
                                    if (selectElement) {
                                        selectElement.value = wirIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.wirs.length > 0) {
                                    this.machines[index].wir_id = String(items.wirs[0].id);
                                }
                            } else if (items.wirs && items.wirs.length > 0) {
                                this.machines[index].wir_id = String(items.wirs[0].id);
                            }
                            await this.$nextTick();

                            // Machine Shafts
                            await this.$nextTick();
                            
                            if (existingShaftId != null && existingShaftId !== '' && items.machine_shafts && items.machine_shafts.length > 0) {
                                const shaftIdStr = String(existingShaftId);
                                const shaftExists = items.machine_shafts.some(s => String(s.id) === shaftIdStr);
                                if (shaftExists) {
                                    this.machines[index].machine_shaft_id = shaftIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_shaft_id = shaftIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_shaft_id = shaftIdStr;
                                    const selectElement = document.getElementById(`machine_shaft_${index}`);
                                    if (selectElement) {
                                        selectElement.value = shaftIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_shafts.length > 0) {
                                    this.machines[index].machine_shaft_id = String(items.machine_shafts[0].id);
                                }
                            } else if (items.machine_shafts && items.machine_shafts.length > 0) {
                                this.machines[index].machine_shaft_id = String(items.machine_shafts[0].id);
                            }
                            await this.$nextTick();

                            // Machine Levers
                            await this.$nextTick();
                            
                            if (existingLeverId != null && existingLeverId !== '' && items.machine_levers && items.machine_levers.length > 0) {
                                const leverIdStr = String(existingLeverId);
                                const leverExists = items.machine_levers.some(l => String(l.id) === leverIdStr);
                                if (leverExists) {
                                    this.machines[index].machine_lever_id = leverIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_lever_id = leverIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_lever_id = leverIdStr;
                                    const selectElement = document.getElementById(`machine_lever_${index}`);
                                    if (selectElement) {
                                        selectElement.value = leverIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_levers.length > 0) {
                                    this.machines[index].machine_lever_id = String(items.machine_levers[0].id);
                                }
                            } else if (items.machine_levers && items.machine_levers.length > 0) {
                                this.machines[index].machine_lever_id = String(items.machine_levers[0].id);
                            }
                            await this.$nextTick();

                            // Machine Chains
                            await this.$nextTick();
                            
                            if (existingChainId != null && existingChainId !== '' && items.machine_chains && items.machine_chains.length > 0) {
                                const chainIdStr = String(existingChainId);
                                const chainExists = items.machine_chains.some(c => String(c.id) === chainIdStr);
                                if (chainExists) {
                                    this.machines[index].machine_chain_id = chainIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_chain_id = chainIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_chain_id = chainIdStr;
                                    const selectElement = document.getElementById(`machine_chain_${index}`);
                                    if (selectElement) {
                                        selectElement.value = chainIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_chains.length > 0) {
                                    this.machines[index].machine_chain_id = String(items.machine_chains[0].id);
                                }
                            } else if (items.machine_chains && items.machine_chains.length > 0) {
                                this.machines[index].machine_chain_id = String(items.machine_chains[0].id);
                            }
                            await this.$nextTick();

                            // Machine Heald Wires
                            await this.$nextTick();
                            
                            if (existingHealdWireId != null && existingHealdWireId !== '' && items.machine_heald_wires && items.machine_heald_wires.length > 0) {
                                const wireIdStr = String(existingHealdWireId);
                                const wireExists = items.machine_heald_wires.some(w => String(w.id) === wireIdStr);
                                if (wireExists) {
                                    this.machines[index].machine_heald_wire_id = wireIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_heald_wire_id = wireIdStr;
                                    await this.$nextTick();
                                    this.machines[index].machine_heald_wire_id = wireIdStr;
                                    const selectElement = document.getElementById(`machine_heald_wire_${index}`);
                                    if (selectElement) {
                                        selectElement.value = wireIdStr;
                                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                } else if (items.machine_heald_wires.length > 0) {
                                    this.machines[index].machine_heald_wire_id = String(items.machine_heald_wires[0].id);
                                }
                            } else if (items.machine_heald_wires && items.machine_heald_wires.length > 0) {
                                this.machines[index].machine_heald_wire_id = String(items.machine_heald_wires[0].id);
                            }
                            
                            // Force Alpine to update after all restorations
                            await this.$nextTick();
                        }
                    } catch (error) {
                        console.error('Error loading category items:', error);
                    }
                },

                async loadCities(stateId) {
                    if (!stateId) {
                        document.getElementById('city_id').innerHTML = '<option value="">Select City</option>';
                        document.getElementById('area_id').innerHTML = '<option value="">Select Area</option>';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/cities') }}/${stateId}`);
                        const cities = await response.json();
                        const citySelect = document.getElementById('city_id');
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        cities.forEach(city => {
                            citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                        });
                        document.getElementById('area_id').innerHTML = '<option value="">Select Area</option>';
                    } catch (error) {
                        console.error('Error loading cities:', error);
                    }
                },

                async loadAreas(cityId) {
                    if (!cityId) {
                        document.getElementById('area_id').innerHTML = '<option value="">Select Area</option>';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/areas') }}/${cityId}`);
                        const areas = await response.json();
                        const areaSelect = document.getElementById('area_id');
                        areaSelect.innerHTML = '<option value="">Select Area</option>';
                        areas.forEach(area => {
                            areaSelect.innerHTML += `<option value="${area.id}">${area.name}</option>`;
                        });
                    } catch (error) {
                        console.error('Error loading areas:', error);
                    }
                }
            }
        }
    </script>
</x-app-layout>
