<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Edit PI Layout</h1>
            <p class="text-muted mb-0">Edit layout: {{ $piLayout->name }}</p>
        </div>
        <a href="{{ route('settings.pi-layouts') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Layouts
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-4"
             x-data="{
                 selectedSellers: {{ json_encode(old('sellers', $selectedSellerIds ?? [])) }},
                 sellerDropdownOpen: false,
                 toggleSeller(id) {
                     id = String(id);
                     const index = this.selectedSellers.indexOf(id);
                     index > -1 ? this.selectedSellers.splice(index, 1) : this.selectedSellers.push(id);
                 },
                 isSellerSelected(id) {
                     return this.selectedSellers.includes(String(id));
                 }
             }">
            <form action="{{ route('pi-layouts.update', $piLayout) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium" style="color: #374151;">Layout Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $piLayout->name) }}" 
                               class="form-control" placeholder="Enter layout name" 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium" style="color: #374151;">Description</label>
                        <input type="text" name="description" value="{{ old('description', $piLayout->description) }}" 
                               class="form-control" placeholder="Enter description" 
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $piLayout->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active" style="color: #374151;">
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $piLayout->is_default) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default" style="color: #374151;">
                                Set as Default Layout
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium" style="color: #374151;">Sellers <small class="text-muted">(Multiple Select)</small></label>
                    <div class="position-relative" @click.away="sellerDropdownOpen = false">
                        <button type="button" 
                                @click="sellerDropdownOpen = !sellerDropdownOpen"
                                class="form-control text-start d-flex justify-content-between align-items-center"
                                style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                            <span x-text="selectedSellers.length > 0 ? selectedSellers.length + ' seller(s) selected' : 'Select Sellers'"></span>
                            <i class="fas fa-chevron-down" :class="{ 'rotate-180': sellerDropdownOpen }"></i>
                        </button>
                        <div x-show="sellerDropdownOpen" 
                             x-cloak
                             class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                             style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                             @click.stop>
                            @forelse($sellers ?? [] as $seller)
                                <div class="d-flex align-items-center py-2 px-3" 
                                     x-data="{ hovered: false }"
                                     :class="isSellerSelected({{ $seller->id }}) ? 'bg-red-50' : ''"
                                     :style="isSellerSelected({{ $seller->id }}) || hovered ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : 'background-color: white;'"
                                     style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                     @mouseenter="hovered = true"
                                     @mouseleave="hovered = false"
                                     @click="toggleSeller({{ $seller->id }})">
                                    <input class="form-check-input me-3" 
                                           type="checkbox" 
                                           :checked="isSellerSelected({{ $seller->id }})"
                                           style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                           @click.stop="toggleSeller({{ $seller->id }})">
                                    <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                        {{ $seller->seller_name }} ({{ $seller->pi_short_name }})
                                    </label>
                                    <i class="fas fa-check text-primary ms-2" x-show="isSellerSelected({{ $seller->id }})" style="font-size: 0.875rem;"></i>
                                </div>
                            @empty
                                <div class="p-3 text-center text-muted">
                                    <small>No sellers available. Create sellers first.</small>
                                </div>
                            @endforelse
                        </div>
                        <template x-for="sellerId in selectedSellers" :key="sellerId">
                            <input type="hidden" name="sellers[]" :value="sellerId">
                        </template>
                    </div>
                    @error('sellers')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium" style="color: #374151;">Template HTML <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-2">
                        Use Blade syntax. Available variables: <code>$proformaInvoice</code>, <code>$seller</code>, <code>$machines</code>
                    </p>
                    <textarea name="template_html" required rows="20" 
                              class="form-control font-monospace" 
                              style="border-radius: 8px; border: 1px solid #e5e7eb; font-family: 'Courier New', monospace;"
                              placeholder="Enter HTML template with Blade syntax">{{ old('template_html', $piLayout->template_html) }}</textarea>
                    @error('template_html')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('settings.pi-layouts') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Layout
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
