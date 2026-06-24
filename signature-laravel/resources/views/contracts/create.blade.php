<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Create Contract</h1>
            <p class="text-muted mb-0">Create a new contract without a lead</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Contracts
        </a>
    </div>

    <div class="row g-4" x-data="contractForm()">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Contract Information</h2>
                    </div>

                    <form action="{{ route('contracts.store') }}" method="POST" id="contractForm" @submit="prepareContractSubmit($event)">
                        @csrf
                        @if($errors->has('error'))
                            <div class="alert alert-danger mb-3" role="alert">{{ $errors->first('error') }}</div>
                        @endif

                        <!-- Basic Information Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Business Firm <span class="text-danger">*</span></label>
                                <select name="business_firm_id" required class="form-select @error('business_firm_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @foreach($businessFirms as $firm)
                                        <option value="{{ $firm->id }}" {{ old('business_firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                    @endforeach
                                </select>
                                @error('business_firm_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Contract Number <span class="text-danger">*</span></label>
                                <input type="text" name="contract_number" required value="{{ old('contract_number', $contractNumber) }}" 
                                       class="form-control @error('contract_number') is-invalid @enderror" 
                                       placeholder="Contract Number" style="border-radius: 8px; border: 1px solid #e5e7eb;" readonly>
                                @error('contract_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Auto-generated serial number</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Buyer Name <span class="text-danger">*</span></label>
                                <input type="text" name="buyer_name" required value="{{ old('buyer_name') }}" 
                                       class="form-control @error('buyer_name') is-invalid @enderror" 
                                       placeholder="Enter buyer name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('buyer_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Company Name</label>
                                <input type="text" name="company_name" value="{{ old('company_name') }}" 
                                       class="form-control @error('company_name') is-invalid @enderror" 
                                       placeholder="Enter company name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('company_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-medium" style="color: #374151;">Contact Address</label>
                                <textarea name="contact_address" rows="3"
                                          class="form-control @error('contact_address') is-invalid @enderror"
                                          placeholder="Enter contact address" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('contact_address') }}</textarea>
                                @error('contact_address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="color: #374151;">State <span class="text-danger">*</span></label>
                                <select name="state_id" required id="state_id" class="form-select @error('state_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadCities($event.target.value)">
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="color: #374151;">City <span class="text-danger">*</span></label>
                                <select name="city_id" required id="city_id" class="form-select @error('city_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadAreas($event.target.value)">
                                    <option value="">Select city</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="color: #374151;">Area <span class="text-danger">*</span></label>
                                <select name="area_id" required id="area_id" class="form-select @error('area_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                    @endforeach
                                </select>
                                @error('area_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="Enter email" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" required value="{{ old('phone_number') }}" 
                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                       placeholder="Enter phone number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('phone_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #374151;">Phone Number 2</label>
                                <input type="text" name="phone_number_2" value="{{ old('phone_number_2') }}" 
                                       class="form-control @error('phone_number_2') is-invalid @enderror" 
                                       placeholder="Enter alternate phone number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('phone_number_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium" style="color: #374151;">GST</label>
                                <input type="text" name="gst" value="{{ old('gst') }}" 
                                       class="form-control @error('gst') is-invalid @enderror" 
                                       placeholder="Enter GST" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('gst')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium" style="color: #374151;">PAN</label>
                                <input type="text" name="pan" value="{{ old('pan') }}" 
                                       class="form-control @error('pan') is-invalid @enderror" 
                                       placeholder="Enter PAN" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('pan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Machine Details Section -->
                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-semibold mb-0" style="color: #1f2937;">Machine Details</h5>
                            </div>

                            <div id="machines-container">
                                <div x-for="(machine, index) in machines" :key="index">
                                    <div class="card mb-3" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-medium" style="color: #374151;">Machine <span x-text="index + 1"></span></h6>
                                                <button type="button" @click="removeMachine(index)" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium" style="color: #374151;">Machine Category <span class="text-danger">*</span></label>
                                                    <select :name="`machines[${index}][machine_category_id]`" required x-model="machine.machine_category_id" @change="loadCategoryItems(index, $event.target.value)" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <option value="">Select category</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Brand - only show when category is selected -->
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Brand</label>
                                                        <select :name="`machines[${index}][brand_id]`" x-model="machine.brand_id" @change="loadMachineModels(index, $event.target.value)" :id="`brand_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <template x-for="brand in (machine.categoryItems && machine.categoryItems.brands ? machine.categoryItems.brands : [])" :key="brand.id">
                                                                <option :value="String(brand.id)" x-text="brand.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                                <!-- Machine Seller - only show when category is selected -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.sellers && machine.categoryItems.sellers.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Seller</label>
                                                        <select :name="`machines[${index}][seller_id]`" x-model="machine.seller_id" :id="`seller_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <template x-for="seller in (machine.categoryItems && machine.categoryItems.sellers ? machine.categoryItems.sellers : [])" :key="seller.id">
                                                                <option :value="String(seller.id)" x-text="seller.seller_name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                                <!-- Model - only show when brand is selected -->
                                                <template x-if="machine.brand_id">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Model</label>
                                                        <select :name="`machines[${index}][machine_model_id]`" x-model="machine.machine_model_id" :id="`machine_model_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <template x-for="model in (machine.machineModels || [])" :key="model.id">
                                                                <option :value="String(model.id)" x-text="model.model_no"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                                
                                                <!-- Machine Size -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_sizes && machine.categoryItems.machine_sizes.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Size</label>
                                                        <select :name="`machines[${index}][machine_size_id]`" x-model="machine.machine_size_id" :id="`machine_size_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <template x-for="size in machine.categoryItems.machine_sizes" :key="size.id">
                                                                <option :value="String(size.id)" x-text="size.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                                
                                                <!-- Category-related items (shown dynamically based on category) -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.feeders && machine.categoryItems.feeders.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Feeder</label>
                                                                        <select :name="`machines[${index}][feeder_id]`" x-model="machine.feeder_id" :id="`feeder_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="(feeder, feederIndex) in machine.categoryItems.feeders" :key="feeder.id">
                                                                                <option :value="feeder.id" :selected="feederIndex === 0" x-text="feeder.feeder"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Hook -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_hooks && machine.categoryItems.machine_hooks.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Hook</label>
                                                                        <select :name="`machines[${index}][machine_hook_id]`" x-model="machine.machine_hook_id" :id="`machine_hook_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="hook in machine.categoryItems.machine_hooks" :key="hook.id">
                                                                                <option :value="hook.id" x-text="hook.hook"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine E-Read -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_e_reads && machine.categoryItems.machine_e_reads.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine E-Read</label>
                                                                        <select :name="`machines[${index}][machine_e_read_id]`" x-model="machine.machine_e_read_id" :id="`machine_e_read_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="eread in machine.categoryItems.machine_e_reads" :key="eread.id">
                                                                                <option :value="eread.id" x-text="eread.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Color -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.colors && machine.categoryItems.colors.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Color</label>
                                                                        <select :name="`machines[${index}][color_id]`" x-model="machine.color_id" :id="`color_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="color in machine.categoryItems.colors" :key="color.id">
                                                                                <option :value="color.id" x-text="color.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Nozzle -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_nozzles && machine.categoryItems.machine_nozzles.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Nozzle</label>
                                                                        <select :name="`machines[${index}][machine_nozzle_id]`" x-model="machine.machine_nozzle_id" :id="`machine_nozzle_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="nozzle in machine.categoryItems.machine_nozzles" :key="nozzle.id">
                                                                                <option :value="nozzle.id" x-text="nozzle.nozzle"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Dropin -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_dropins && machine.categoryItems.machine_dropins.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Dropin</label>
                                                                        <select :name="`machines[${index}][machine_dropin_id]`" x-model="machine.machine_dropin_id" :id="`machine_dropin_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="dropin in machine.categoryItems.machine_dropins" :key="dropin.id">
                                                                                <option :value="dropin.id" x-text="dropin.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Beam -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_beams && machine.categoryItems.machine_beams.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Beam</label>
                                                                        <select :name="`machines[${index}][machine_beam_id]`" x-model="machine.machine_beam_id" :id="`machine_beam_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="beam in machine.categoryItems.machine_beams" :key="beam.id">
                                                                                <option :value="beam.id" x-text="beam.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Cloth Roller -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_cloth_rollers && machine.categoryItems.machine_cloth_rollers.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Cloth Roller</label>
                                                                        <select :name="`machines[${index}][machine_cloth_roller_id]`" x-model="machine.machine_cloth_roller_id" :id="`machine_cloth_roller_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="roller in machine.categoryItems.machine_cloth_rollers" :key="roller.id">
                                                                                <option :value="roller.id" x-text="roller.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Software -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_softwares && machine.categoryItems.machine_softwares.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Software</label>
                                                                        <select :name="`machines[${index}][machine_software_id]`" x-model="machine.machine_software_id" :id="`machine_software_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="software in machine.categoryItems.machine_softwares" :key="software.id">
                                                                                <option :value="software.id" x-text="software.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- HSN Code -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.hsn_codes && machine.categoryItems.hsn_codes.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">HSN Code</label>
                                                                        <select :name="`machines[${index}][hsn_code_id]`" x-model="machine.hsn_code_id" :id="`hsn_code_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="hsn in machine.categoryItems.hsn_codes" :key="hsn.id">
                                                                                <option :value="hsn.id" x-text="hsn.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- WIR -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.wirs && machine.categoryItems.wirs.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">WIR</label>
                                                                        <select :name="`machines[${index}][wir_id]`" x-model="machine.wir_id" :id="`wir_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="wir in machine.categoryItems.wirs" :key="wir.id">
                                                                                <option :value="wir.id" x-text="wir.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Shaft -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_shafts && machine.categoryItems.machine_shafts.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Shaft</label>
                                                                        <select :name="`machines[${index}][machine_shaft_id]`" x-model="machine.machine_shaft_id" :id="`machine_shaft_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="shaft in machine.categoryItems.machine_shafts" :key="shaft.id">
                                                                                <option :value="shaft.id" x-text="shaft.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Lever -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_levers && machine.categoryItems.machine_levers.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Lever</label>
                                                                        <select :name="`machines[${index}][machine_lever_id]`" x-model="machine.machine_lever_id" :id="`machine_lever_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="lever in machine.categoryItems.machine_levers" :key="lever.id">
                                                                                <option :value="lever.id" x-text="lever.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Chain -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_chains && machine.categoryItems.machine_chains.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Chain</label>
                                                                        <select :name="`machines[${index}][machine_chain_id]`" x-model="machine.machine_chain_id" :id="`machine_chain_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="chain in machine.categoryItems.machine_chains" :key="chain.id">
                                                                                <option :value="chain.id" x-text="chain.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Heald Wire -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_heald_wires && machine.categoryItems.machine_heald_wires.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Heald Wire</label>
                                                                        <select :name="`machines[${index}][machine_heald_wire_id]`" x-model="machine.machine_heald_wire_id" :id="`machine_heald_wire_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="wire in machine.categoryItems.machine_heald_wires" :key="wire.id">
                                                                                <option :value="wire.id" x-text="wire.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                </template>
                                                
                                                <!-- Quantity, Amount and Description at the end - only show when category is selected -->
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-medium" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                                        <input type="number" :name="`machines[${index}][quantity]`" required x-model="machine.quantity" min="1" class="form-control" placeholder="Quantity" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </template>
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-medium" style="color: #374151;">Amount ($) <span class="text-danger">*</span></label>
                                                        <input type="number" :name="`machines[${index}][amount]`" required x-model="machine.amount" step="0.01" min="0" class="form-control" placeholder="0.00" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </template>
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-medium" style="color: #374151;">Machine Amount</label>
                                                        <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb; padding: 0.375rem 0.75rem; display: flex; align-items: center; min-height: 38px;">
                                                            <span class="fw-semibold" style="color: var(--primary-color);" x-text="'$' + ((parseFloat(machine.quantity) || 0) * (parseFloat(machine.amount) || 0)).toFixed(2)"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-medium" style="color: #374151;">Description</label>
                                                        <input type="text" :name="`machines[${index}][description]`" x-model="machine.description" class="form-control" placeholder="Machine description (optional)" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </template>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
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
                                        <div class="card bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-semibold" style="color: #374151; font-size: 1.1rem;">Total Machine Amount:</span>
                                                    <span class="fw-semibold" style="color: var(--primary-color); font-size: 1.2rem;" x-text="'$' + getTotalMachineAmount().toFixed(2)"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Buyer Expenses Details Section (toggle via SHOW_OTHER_BUYER_EXPENSES_SECTION in .env) -->
                        @if(\App\Models\Contract::showOtherBuyerExpensesSection())
                        @php $createShowBuyerExp = filter_var(old('other_buyer_expenses_in_print', $settings->global_other_buyer_expenses_in_print ?? true), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mt-4 mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;" x-data="{ showFields: {{ $createShowBuyerExp ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Other Buyer Expenses Details</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="other_buyer_expenses_in_print" id="buyer_expenses_show" value="1" {{ $createShowBuyerExp ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="buyer_expenses_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="other_buyer_expenses_in_print" id="buyer_expenses_hide" value="0" {{ !$createShowBuyerExp ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="buyer_expenses_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Overseas Freight</label>
                                                <input type="text" name="overseas_freight" value="{{ old('overseas_freight', $settings->global_overseas_freight ?? 'CHA will provide') }}" 
                                                       class="form-control" placeholder="CHA will provide" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Demurrage / Detention / CFS Charges</label>
                                                <input type="text" name="demurrage_detention_cfs_charges" value="{{ old('demurrage_detention_cfs_charges', $settings->global_demurrage_detention_cfs_charges ?? 'At Actual') }}" 
                                                       class="form-control" placeholder="At Actual" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Air Pipe Connection</label>
                                                <input type="text" name="air_pipe_connection" value="{{ old('air_pipe_connection', $settings->global_air_pipe_connection) }}" 
                                                       class="form-control" placeholder="Enter air pipe connection" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Custom Duty</label>
                                                <input type="text" name="custom_duty" value="{{ old('custom_duty', $settings->global_custom_duty) }}" 
                                                       class="form-control" placeholder="Enter custom duty" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Port Expenses & Transport</label>
                                                <input type="text" name="port_expenses_transport" value="{{ old('port_expenses_transport', $settings->global_port_expenses_transport ?? 'CHA will provide') }}" 
                                                       class="form-control" placeholder="CHA will provide" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Crane & Foundation</label>
                                                <input type="text" name="crane_foundation" value="{{ old('crane_foundation', $settings->global_crane_foundation ?? 'By Buyer') }}" 
                                                       class="form-control" placeholder="By Buyer" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Humidification</label>
                                                <input type="text" name="humidification" value="{{ old('humidification', $settings->global_humidification) }}" 
                                                       class="form-control" placeholder="Enter humidification" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Damage</label>
                                                <input type="text" name="damage" value="{{ old('damage', $settings->global_damage) }}" 
                                                       class="form-control" placeholder="Enter damage" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">GST & Custom Charges</label>
                                                <input type="text" name="gst_custom_charges" value="{{ old('gst_custom_charges', $settings->global_gst_custom_charges ?? 'At Actual By Buyer') }}" 
                                                       class="form-control" placeholder="At Actual By Buyer" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Compressor</label>
                                                <input type="text" name="compressor" value="{{ old('compressor', $settings->global_compressor) }}" 
                                                       class="form-control" placeholder="Enter compressor" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Optional Spares</label>
                                                <input type="text" name="optional_spares" value="{{ old('optional_spares', $settings->global_optional_spares) }}" 
                                                       class="form-control" placeholder="Enter optional spares" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Other Details Section -->
                        @php $createShowOtherDet = filter_var(old('other_details_in_print', $settings->global_other_details_in_print ?? true), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;" x-data="{ showFields: {{ $createShowOtherDet ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Other Details</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="other_details_in_print" id="other_details_show" value="1" {{ $createShowOtherDet ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="other_details_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="other_details_in_print" id="other_details_hide" value="0" {{ !$createShowOtherDet ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="other_details_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" @click="showFields = !showFields" :title="showFields ? 'Collapse section' : 'Expand section'">
                                                    <i class="fas" :class="showFields ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small text-muted mb-2" x-show="!showFields" x-cloak>Section collapsed. Choose <strong>Show</strong> for In Print, or use the arrow to expand fields.</p>
                                        <div class="row g-3" x-show="showFields" x-cloak>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Payment Terms</label>
                                                <input type="text" name="payment_terms" value="{{ old('payment_terms', $settings->global_payment_terms ?? '10% Token + 15% Advance + 75% Before Shipment') }}" 
                                                       class="form-control" placeholder="Enter payment terms" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Quote Validity</label>
                                                <input type="text" name="quote_validity" value="{{ old('quote_validity', $settings->global_quote_validity ?? '10 Days') }}" 
                                                       class="form-control" placeholder="Enter quote validity" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Loading Terms</label>
                                                <input type="text" name="loading_terms" value="{{ old('loading_terms', $settings->global_loading_terms ?? '30 Days from 100% Payment') }}" 
                                                       class="form-control" placeholder="Enter loading terms" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Warranty</label>
                                                <input type="text" name="warranty" value="{{ old('warranty', $settings->global_warranty ?? '1 Year from Date of Loading') }}" 
                                                       class="form-control" placeholder="Enter warranty" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Complimentary Spares</label>
                                                <input type="text" name="complimentary_spares" value="{{ old('complimentary_spares', $settings->global_complimentary_spares ?? 'As per list attached') }}" 
                                                       class="form-control" placeholder="Enter complimentary spares" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('contracts.partials.not-included-in-offer-section', [
                            'nioFieldPrefix' => 'not_included_in_offer',
                            'nioPrintField' => 'not_included_in_offer_in_print',
                            'nioShowPrint' => filter_var(old('not_included_in_offer_in_print', $settings->global_not_included_in_offer_in_print ?? true), FILTER_VALIDATE_BOOLEAN),
                            'nioFlags' => \App\Models\Contract::mergeNotIncludedInOfferFlags(old('not_included_in_offer'), $settings->global_not_included_in_offer),
                        ])

                        <!-- Difference of Specification Section -->
                        @php $createShowSpecMain = filter_var(old('difference_specification_in_print', $settings->global_difference_specification_in_print ?? true), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;" x-data="{ showFields: {{ $createShowSpecMain ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification (Rapier - Jacquard)n</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="difference_specification_in_print" id="specification_show" value="1" {{ $createShowSpecMain ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="specification_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="difference_specification_in_print" id="specification_hide" value="0" {{ !$createShowSpecMain ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="specification_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
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
                                                <input type="text" name="spec_color_8_to_12_selectors" value="{{ old('spec_color_8_to_12_selectors', $settings->global_spec_color_8_to_12_selectors) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Extra Feeder (Per PC)</label>
                                                <input type="text" name="spec_extra_feeder_per_pc" value="{{ old('spec_extra_feeder_per_pc', $settings->global_spec_extra_feeder_per_pc) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Extra Warp Beam (Per PC)</label>
                                                <input type="text" name="spec_extra_warp_beam_per_pc" value="{{ old('spec_extra_warp_beam_per_pc', $settings->global_spec_extra_warp_beam_per_pc) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Reduction of Every 20cm in Reed Space</label>
                                                <input type="text" name="spec_reed_reduction_per_20cm" value="{{ old('spec_reed_reduction_per_20cm', $settings->global_spec_reed_reduction_per_20cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Increase of Every 20cm in Reed Space</label>
                                                <input type="text" name="spec_reed_increase_per_20cm" value="{{ old('spec_reed_increase_per_20cm', $settings->global_spec_reed_increase_per_20cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Increase from 380cm to 480cm</label>
                                                <input type="text" name="spec_increase_380_to_480cm" value="{{ old('spec_increase_380_to_480cm', $settings->global_spec_increase_380_to_480cm) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Electronic Weft Cutter</label>
                                                <input type="text" name="spec_electronic_weft_cutter" value="{{ old('spec_electronic_weft_cutter', $settings->global_spec_electronic_weft_cutter) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 6144 Hooks</label>
                                                <input type="text" name="spec_hooks_5376_to_6144" value="{{ old('spec_hooks_5376_to_6144', $settings->global_spec_hooks_5376_to_6144) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 10240 Hooks</label>
                                                <input type="text" name="spec_hooks_5376_to_10240" value="{{ old('spec_hooks_5376_to_10240', $settings->global_spec_hooks_5376_to_10240) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">5376 Hooks to 2688 Hooks</label>
                                                <input type="text" name="spec_hooks_5376_to_2688" value="{{ old('spec_hooks_5376_to_2688', $settings->global_spec_hooks_5376_to_2688) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS CAM</label>
                                                <input type="text" name="spec_changshu_to_sns_cam" value="{{ old('spec_changshu_to_sns_cam', $settings->global_spec_changshu_to_sns_cam) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS Chain (24 Line)</label>
                                                <input type="text" name="spec_changshu_to_sns_chain_24" value="{{ old('spec_changshu_to_sns_chain_24', $settings->global_spec_changshu_to_sns_chain_24) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to SNS Chain (16 Line)</label>
                                                <input type="text" name="spec_changshu_to_sns_chain_16" value="{{ old('spec_changshu_to_sns_chain_16', $settings->global_spec_changshu_to_sns_chain_16) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to JKD or ChangFang</label>
                                                <input type="text" name="spec_changshu_to_jkd_or_changfang" value="{{ old('spec_changshu_to_jkd_or_changfang', $settings->global_spec_changshu_to_jkd_or_changfang) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-medium" style="color: #374151;">Changshu to Wumu</label>
                                                <input type="text" name="spec_changshu_to_wumu" value="{{ old('spec_changshu_to_wumu', $settings->global_spec_changshu_to_wumu) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Difference of Specification (Airjet) -->
                        @php $createShowSpecExt = filter_var(old('difference_specification_extended_in_print', $settings->global_difference_specification_extended_in_print ?? false), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;" x-data="{ showFields: {{ $createShowSpecExt ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification (Airjet)</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="difference_specification_extended_in_print" id="create_spec_ext_show" value="1" {{ $createShowSpecExt ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="create_spec_ext_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="difference_specification_extended_in_print" id="create_spec_ext_hide" value="0" {{ !$createShowSpecExt ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="create_spec_ext_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
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
                                                <input type="text" name="{{ $field }}" value="{{ old($field, $settings->{'global_' . $field}) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Difference of Specification (Waterjet) -->
                        @php $createShowSpec3 = filter_var(old('difference_specification_3_in_print', $settings->global_difference_specification_3_in_print ?? false), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;" x-data="{ showFields: {{ $createShowSpec3 ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Difference of Specification (Waterjet)</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="difference_specification_3_in_print" id="create_spec_3_show" value="1" {{ $createShowSpec3 ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="create_spec_3_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="difference_specification_3_in_print" id="create_spec_3_hide" value="0" {{ !$createShowSpec3 ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="create_spec_3_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
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
                                                <input type="text" name="{{ $field }}" value="{{ old($field, $settings->{'global_' . $field}) }}" class="form-control" placeholder="Enter value" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & conditions -->
                        @php $createShowTerms = filter_var(old('terms_conditions_in_print', $settings->global_terms_conditions_in_print ?? true), FILTER_VALIDATE_BOOLEAN); @endphp
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;" x-data="{ showFields: {{ $createShowTerms ? 'true' : 'false' }} }">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                            <h5 class="fw-semibold mb-0" style="color: #1f2937;">Terms &amp; conditions</h5>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <label class="form-label fw-medium mb-0" style="color: #374151;">In Print :</label>
                                                <div class="btn-group" role="group">
                                                    <input type="radio" class="btn-check" name="terms_conditions_in_print" id="create_terms_conditions_show" value="1" {{ $createShowTerms ? 'checked' : '' }} @change="showFields = true">
                                                    <label class="btn btn-outline-success btn-sm" for="create_terms_conditions_show" style="border-radius: 6px 0 0 6px;">Show</label>
                                                    <input type="radio" class="btn-check" name="terms_conditions_in_print" id="create_terms_conditions_hide" value="0" {{ !$createShowTerms ? 'checked' : '' }} @change="showFields = false">
                                                    <label class="btn btn-outline-danger btn-sm" for="create_terms_conditions_hide" style="border-radius: 0 6px 6px 0;">Hide</label>
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
                                                <textarea name="{{ $field }}" rows="4" class="form-control" placeholder="Enter {{ strtolower($label) }}" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old($field, $settings->{'global_' . $field}) }}</textarea>
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
                                <input type="number" name="token_amount" value="{{ old('token_amount') }}" 
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
                                    <i class="fas fa-save me-2"></i>Create Contract
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('contracts.partials.machine-form-sync')

    <script>
        function contractForm() {
            return {
                machines: [{
                    machine_category_id: '',
                    brand_id: '',
                    machine_model_id: '',
                    machineModels: [],
                    quantity: '',
                    description: '',
                    categoryItems: null,
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
                    machine_heald_wire_id: ''
                }],

                getTotalMachineAmount() {
                    return this.machines.reduce((total, machine) => {
                        const quantity = parseFloat(machine.quantity) || 0;
                        const amount = parseFloat(machine.amount) || 0;
                        const machineTotal = quantity * amount;
                        return total + machineTotal;
                    }, 0);
                },

                addMachine() {
                    this.machines.push({
                        machine_category_id: '',
                        brand_id: '',
                        machine_model_id: '',
                        machineModels: [],
                        seller_id: '',
                        quantity: '',
                        description: '',
                        categoryItems: null,
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
                        machine_heald_wire_id: ''
                    });
                },


                removeMachine(index) {
                    if (this.machines.length > 1) {
                        this.machines.splice(index, 1);
                    } else {
                        alert('At least one machine is required.');
                    }
                },

                async loadCities(stateId) {
                    if (!stateId) {
                        document.getElementById('city_id').innerHTML = '';
                        document.getElementById('area_id').innerHTML = '';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/cities') }}/${stateId}`);
                        const cities = await response.json();
                        const citySelect = document.getElementById('city_id');
                        const areaSelect = document.getElementById('area_id');
                        const selectedCityId = citySelect.value; // Preserve current selection
                        const selectedAreaId = areaSelect.value; // Preserve current area selection
                        
                        citySelect.innerHTML = '';
                        cities.forEach(city => {
                            const selected = city.id == selectedCityId ? 'selected' : '';
                            citySelect.innerHTML += `<option value="${city.id}" ${selected}>${city.name}</option>`;
                        });
                        
                        // If city was pre-selected, load its areas
                        if (selectedCityId) {
                            await this.loadAreas(selectedCityId);
                            // Restore area selection if it exists
                            if (selectedAreaId && areaSelect) {
                                setTimeout(() => {
                                    areaSelect.value = selectedAreaId;
                                }, 100);
                            }
                        } else {
                            areaSelect.innerHTML = '';
                        }
                    } catch (error) {
                        console.error('Error loading cities:', error);
                    }
                },

                async loadAreas(cityId) {
                    if (!cityId) {
                        document.getElementById('area_id').innerHTML = '';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/areas') }}/${cityId}`);
                        const areas = await response.json();
                        const areaSelect = document.getElementById('area_id');
                        areaSelect.innerHTML = '';
                        areas.forEach(area => {
                            areaSelect.innerHTML += `<option value="${area.id}">${area.name}</option>`;
                        });
                    } catch (error) {
                        console.error('Error loading areas:', error);
                    }
                },

                async loadMachineModels(index, brandId) {
                    if (!brandId) {
                        this.machines[index].machine_model_id = '';
                        this.machines[index].machineModels = [];
                        return;
                    }
                    try {
                        const categoryId = this.machines[index] && this.machines[index].machine_category_id;
                        const query = categoryId ? `?category_id=${encodeURIComponent(String(categoryId))}` : '';
                        const response = await fetch(`{{ url('leads/machine-models') }}/${brandId}${query}`);
                        const models = await response.json();
                        this.machines[index].machineModels = models;
                        
                        // Auto-select first model if available - ensure it's a string to match option values
                        if (models && models.length > 0) {
                            this.machines[index].machine_model_id = String(models[0].id);
                        } else {
                            this.machines[index].machine_model_id = '';
                        }
                    } catch (error) {
                        console.error('Error loading models:', error);
                        this.machines[index].machineModels = [];
                        this.machines[index].machine_model_id = '';
                    }
                },

                async loadCategoryItems(index, categoryId) {
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
                        console.log('Category items loaded:', items); // Debug log
                        this.machines[index].categoryItems = items;
                        
                        // Reset brand, model, and seller when category changes
                        this.machines[index].brand_id = '';
                        this.machines[index].machine_model_id = '';
                        this.machines[index].machine_size_id = '';
                        this.machines[index].seller_id = '';
                        this.machines[index].machineModels = [];
                        
                        // Auto-select first brand if available - use $nextTick to ensure dropdown is rendered
                        if (items.brands && items.brands.length > 0) {
                            await this.$nextTick();
                            // Ensure brand_id is set as string to match option values
                            this.machines[index].brand_id = String(items.brands[0].id);
                            // Load models for the first brand (which will auto-select first model)
                            await this.loadMachineModels(index, items.brands[0].id);
                        }
                        
                        // Auto-select first value in category-related dropdowns
                        this.$nextTick(() => {
                            // Auto-select first feeder
                            if (items.feeders && items.feeders.length > 0) {
                                this.machines[index].feeder_id = items.feeders[0].id;
                            }
                            // Auto-select first hook
                            if (items.machine_hooks && items.machine_hooks.length > 0) {
                                this.machines[index].machine_hook_id = items.machine_hooks[0].id;
                            }
                            // Auto-select first e-read
                            if (items.machine_e_reads && items.machine_e_reads.length > 0) {
                                this.machines[index].machine_e_read_id = items.machine_e_reads[0].id;
                            }
                            // Auto-select first color
                            if (items.colors && items.colors.length > 0) {
                                this.machines[index].color_id = items.colors[0].id;
                            }
                            // Auto-select first nozzle
                            if (items.machine_nozzles && items.machine_nozzles.length > 0) {
                                this.machines[index].machine_nozzle_id = items.machine_nozzles[0].id;
                            }
                            // Auto-select first machine size
                            if (items.machine_sizes && items.machine_sizes.length > 0) {
                                this.machines[index].machine_size_id = items.machine_sizes[0].id;
                            }
                            // Auto-select first dropin
                            if (items.machine_dropins && items.machine_dropins.length > 0) {
                                this.machines[index].machine_dropin_id = items.machine_dropins[0].id;
                            }
                            // Auto-select first beam
                            if (items.machine_beams && items.machine_beams.length > 0) {
                                this.machines[index].machine_beam_id = items.machine_beams[0].id;
                            }
                            // Auto-select first cloth roller
                            if (items.machine_cloth_rollers && items.machine_cloth_rollers.length > 0) {
                                this.machines[index].machine_cloth_roller_id = items.machine_cloth_rollers[0].id;
                            }
                            // Auto-select first software
                            if (items.machine_softwares && items.machine_softwares.length > 0) {
                                this.machines[index].machine_software_id = items.machine_softwares[0].id;
                            }
                            // Auto-select first HSN code
                            if (items.hsn_codes && items.hsn_codes.length > 0) {
                                this.machines[index].hsn_code_id = items.hsn_codes[0].id;
                            }
                            // Auto-select first WIR
                            if (items.wirs && items.wirs.length > 0) {
                                this.machines[index].wir_id = items.wirs[0].id;
                            }
                            // Auto-select first shaft
                            if (items.machine_shafts && items.machine_shafts.length > 0) {
                                this.machines[index].machine_shaft_id = items.machine_shafts[0].id;
                            }
                            // Auto-select first lever
                            if (items.machine_levers && items.machine_levers.length > 0) {
                                this.machines[index].machine_lever_id = items.machine_levers[0].id;
                            }
                            // Auto-select first chain
                            if (items.machine_chains && items.machine_chains.length > 0) {
                                this.machines[index].machine_chain_id = items.machine_chains[0].id;
                            }
                            // Auto-select first heald wire
                            if (items.machine_heald_wires && items.machine_heald_wires.length > 0) {
                                this.machines[index].machine_heald_wire_id = items.machine_heald_wires[0].id;
                            }
                        });
                    } catch (error) {
                        console.error('Error loading category items:', error);
                    }
                },

                prepareContractSubmit(event) {
                    syncContractMachineFields(event.target, this.machines);
                }
            }
        }

        // Initialize cities and areas if state/city is pre-selected
        document.addEventListener('DOMContentLoaded', function() {
            const stateSelect = document.getElementById('state_id');
            const citySelect = document.getElementById('city_id');
            
            // If state is pre-selected, ensure cities are loaded
            if (stateSelect && stateSelect.value) {
                // Get the Alpine component instance
                const alpineElement = document.querySelector('[x-data="contractForm()"]');
                if (alpineElement && alpineElement._x_dataStack && alpineElement._x_dataStack[0]) {
                    const contractForm = alpineElement._x_dataStack[0];
                    
                    // Load cities first
                    contractForm.loadCities(stateSelect.value).then(() => {
                        // After cities load, if city is pre-selected, load areas
                        if (citySelect && citySelect.value) {
                            setTimeout(() => {
                                contractForm.loadAreas(citySelect.value);
                            }, 100);
                        }
                    });
                } else {
                    // Fallback: trigger events manually
                    if (stateSelect.value) {
                        const stateEvent = new Event('change', { bubbles: true });
                        stateSelect.dispatchEvent(stateEvent);
                        
                        // Wait for cities to load, then trigger city change
                        setTimeout(() => {
                            if (citySelect && citySelect.value) {
                                const cityEvent = new Event('change', { bubbles: true });
                                citySelect.dispatchEvent(cityEvent);
                            }
                        }, 300);
                    }
                }
            }
        });
    </script>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
</x-app-layout>



