<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit Complain</h1>
            <p class="text-muted mb-0">Update complaint #{{ $complaint->id }}</p>
        </div>
        <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Complain Details</h2>
            </div>
        </div>
        <div class="card-body p-4" x-data="{
            clientDropdownOpen: false,
            clientSearch: '',
            selectedContractId: '{{ old('contract_id', $complaint->contract_id) }}',
            contracts: @js($contracts->map(fn($c) => ['id' => $c->id, 'contract_number' => $c->contract_number ?? '', 'company_name' => $c->company_name ?? '', 'buyer_name' => $c->buyer_name ?? ''])->values()->toArray()),
            get filteredClients() {
                const search = (this.clientSearch || '').trim().toLowerCase();
                if (!search) return this.contracts;
                return this.contracts.filter(c =>
                    (c.contract_number && c.contract_number.toLowerCase().includes(search)) ||
                    (c.company_name && c.company_name.toLowerCase().includes(search)) ||
                    (c.buyer_name && c.buyer_name.toLowerCase().includes(search))
                );
            },
            get selectedClientLabel() {
                if (!this.selectedContractId) return null;
                const c = this.contracts.find(x => x.id == this.selectedContractId);
                return c ? (c.company_name || c.buyer_name) + (c.contract_number ? ' (' + c.contract_number + ')' : '') : null;
            },
            selectClient(c) {
                this.selectedContractId = c.id;
                this.clientDropdownOpen = false;
                this.clientSearch = '';
                this.selectedMachineCategoryId = '';
                this.loadMachineCategories();
            },
            machineCategoriesForContract: [],
            selectedMachineCategoryId: '{{ old('machine_category_id', $complaint->machine_category_id) }}',
            loadingMachineCategories: false,
            async loadMachineCategories() {
                if (!this.selectedContractId) { this.machineCategoriesForContract = []; return; }
                this.loadingMachineCategories = true;
                this.machineCategoriesForContract = [];
                try {
                    const r = await fetch('{{ route('complaints.machine-categories-by-contract') }}?contract_id=' + encodeURIComponent(this.selectedContractId));
                    const data = await r.json();
                    this.machineCategoriesForContract = Array.isArray(data) ? data : [];
                } catch (e) { this.machineCategoriesForContract = []; }
                this.loadingMachineCategories = false;
            }
        }" x-init="if (selectedContractId) loadMachineCategories()">
            <form action="{{ route('complaints.update', $complaint) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="contract_id" :value="selectedContractId">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">Select Client <span class="text-danger">*</span></label>
                        <div class="position-relative" @click.away="clientDropdownOpen = false">
                            <button type="button" @click="clientDropdownOpen = !clientDropdownOpen" :class="{ 'border-primary': clientDropdownOpen }" class="form-control text-start d-flex justify-content-between align-items-center @error('contract_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                <span x-text="selectedClientLabel || 'Select Client'"></span>
                                <i class="fas fa-chevron-down" :class="{ 'rotate-180': clientDropdownOpen }" style="transition: transform 0.2s ease; color: #6b7280;"></i>
                            </button>
                            <div x-show="clientDropdownOpen" x-cloak class="position-absolute w-100 bg-white border rounded shadow-lg mt-1" style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;" @click.stop>
                                <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                    <input type="text" x-model="clientSearch" @click.stop placeholder="Search by company, buyer name or contract..." class="form-control form-control-sm" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                </div>
                                <template x-if="filteredClients.length === 0"><div class="p-3 text-center text-muted small">No clients found.</div></template>
                                <template x-for="c in filteredClients" :key="c.id">
                                    <div class="d-flex align-items-center py-2 px-3" @click="selectClient(c)" style="cursor: pointer;" :class="{ 'text-white': selectedContractId == c.id }" :style="selectedContractId == c.id ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''" onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'" onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                        <div class="flex-grow-1"><div class="fw-medium" x-text="(c.company_name || c.buyer_name) || '—'"></div><small class="d-block" x-text="c.contract_number || ''"></small></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('contract_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: #374151;">Machine Category</label>
                        <select name="machine_category_id" x-model="selectedMachineCategoryId" :disabled="!selectedContractId || loadingMachineCategories" class="form-select @error('machine_category_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">— Select category —</option>
                            <template x-for="cat in machineCategoriesForContract" :key="cat.id"><option :value="cat.id" x-text="cat.name"></option></template>
                        </select>
                        <template x-if="selectedContractId && !loadingMachineCategories && machineCategoriesForContract.length === 0"><div class="small text-muted mt-1">No machine categories for this client.</div></template>
                        <template x-if="loadingMachineCategories"><div class="small text-muted mt-1"><i class="fas fa-spinner fa-spin me-1"></i>Loading...</div></template>
                        @error('machine_category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="complain_type_id" class="form-label fw-medium" style="color: #374151;">Complain Type <span class="text-danger">*</span></label>
                        <select name="complain_type_id" id="complain_type_id" required class="form-select @error('complain_type_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="">— Select Type —</option>
                            @foreach($complainTypes as $type)
                                <option value="{{ $type->id }}" {{ old('complain_type_id', $complaint->complain_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('complain_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="machine_khata_number" class="form-label fw-medium" style="color: #374151;">Machine Khata Number / Serial Number</label>
                        <input type="text" name="machine_khata_number" id="machine_khata_number" value="{{ old('machine_khata_number', $complaint->machine_khata_number) }}" class="form-control @error('machine_khata_number') is-invalid @enderror" placeholder="Enter machine khata number or serial number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('machine_khata_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="other_detail" class="form-label fw-medium" style="color: #374151;">Other Detail</label>
                        <textarea name="other_detail" id="other_detail" rows="4" class="form-control @error('other_detail') is-invalid @enderror" placeholder="Enter other details (optional)" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('other_detail', $complaint->other_detail) }}</textarea>
                        @error('other_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Complain</button>
                            <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
