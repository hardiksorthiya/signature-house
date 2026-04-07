<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Update Complaint Status</h1>
            <p class="text-muted mb-0">Complaint #{{ $complaint->id }}</p>
        </div>
        <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('complaints.status-update', $complaint) }}" method="POST" x-data="{
                sparesList: @js($spares->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'quantity' => (int) $s->quantity])->values()->toArray()),
                rows: @js($complaint->spares->isEmpty() ? [['spare_id' => '', 'quantity' => 1, 'search' => '']] : $complaint->spares->map(fn($s) => ['spare_id' => (string)$s->id, 'quantity' => (int)$s->pivot->quantity, 'search' => ''])->values()->toArray()),
                openRowIndex: null,
                dropdownStyle: { top: '0', left: '0', width: '260px', bottom: 'auto' },
                addRow() {
                    this.rows.push({ spare_id: '', quantity: 1, search: '' });
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                    if (this.openRowIndex === index) this.openRowIndex = null;
                    else if (this.openRowIndex > index) this.openRowIndex--;
                },
                openDropdown(index, $event) {
                    if (this.openRowIndex === index) {
                        this.openRowIndex = null;
                        return;
                    }
                    const btn = $event.target.closest('.spare-dropdown-trigger');
                    if (btn) {
                        const r = btn.getBoundingClientRect();
                        const maxHeight = 260;
                        const spaceBelow = window.innerHeight - r.bottom;
                        const openDown = spaceBelow >= Math.min(maxHeight, 220) || spaceBelow >= r.top;
                        this.dropdownStyle = {
                            top: openDown ? (r.bottom + 4) + 'px' : 'auto',
                            bottom: openDown ? 'auto' : (window.innerHeight - r.top + 4) + 'px',
                            left: r.left + 'px',
                            width: Math.max(r.width, 260) + 'px'
                        };
                    }
                    this.openRowIndex = index;
                },
                selectSpare(index, spareId) {
                    this.rows[index].spare_id = spareId;
                    this.rows[index].quantity = 1;
                    this.openRowIndex = null;
                },
                filteredSpares(index) {
                    const row = this.rows[index];
                    if (!row) return [];
                    const s = (row.search || '').trim().toLowerCase();
                    if (!s) return this.sparesList;
                    return this.sparesList.filter(sp => (sp.name || '').toLowerCase().includes(s));
                },
                maxQty(spareId) {
                    const s = this.sparesList.find(x => x.id == spareId);
                    return s ? s.quantity : 1;
                },
                spareName(spareId) {
                    const s = this.sparesList.find(x => x.id == spareId);
                    return s ? s.name : '';
                }
            }" @click.away="openRowIndex = null">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    @php
                        $complaintStatusValue = null;
                        if (($complaint->status ?? null) === 'on_going') {
                            $complaintStatusValue = 'IN_PROGRESS';
                        } elseif (($complaint->status ?? null) === 'completed') {
                            $complaintStatusValue = 'RESOLVED';
                        }
                    @endphp

                    <label for="complaintstatus" class="form-label fw-medium" style="color: #374151;">Select Complaint Status <span class="text-danger">*</span></label>
                    <select name="complaintstatus" id="complaintstatus" required class="form-select @error('complaintstatus') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="IN_PROGRESS" {{ old('complaintstatus', $complaintStatusValue) === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="RESOLVED" {{ old('complaintstatus', $complaintStatusValue) === 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                    </select>
                    @error('complaintstatus')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="remarks" class="form-label fw-medium" style="color: #374151;">Remarks <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="remarks"
                        id="remarks"
                        required
                        value="{{ old('remarks') }}"
                        placeholder="Enter remarks"
                        class="form-control @error('remarks') is-invalid @enderror"
                        style="border-radius: 8px; border: 1px solid #e5e7eb;"
                    >
                    @error('remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="text-muted small mt-1">Allowed: letters, numbers, spaces, comma and hyphen (2–25 characters).</div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-medium mb-0" style="color: #374151;">Spare Parts</label>
                        <button type="button" @click="addRow()" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-plus me-1"></i>Add Spare
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Select spare and quantity (max = available quantity).</p>

                    <template x-for="(row, index) in rows" :key="index">
                        <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                            <div class="position-relative flex-grow-1" style="min-width: 260px;">
                                <label class="form-label fw-medium mb-1" style="color: #374151;">Spare</label>
                                <input type="hidden" :name="'spares[' + index + '][spare_id]'" :value="row.spare_id">
                                <button type="button"
                                        @click="openDropdown(index, $event)"
                                        class="spare-dropdown-trigger form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                        :style="openRowIndex === index ? 'border-color: #93c5fd !important; box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.25);' : ''">
                                    <span class="text-truncate" x-text="row.spare_id ? spareName(row.spare_id) : 'Select spare'" :class="{ 'text-muted': !row.spare_id }"></span>
                                    <i class="fas fa-chevron-down ms-2 flex-shrink-0" :class="{ 'rotate-180': openRowIndex === index }" style="transition: transform 0.2s ease; color: #6b7280;"></i>
                                </button>
                                <div x-show="openRowIndex === index"
                                     x-cloak
                                     x-transition
                                     class="position-fixed bg-white border rounded shadow-lg"
                                     style="z-index: 1060; max-height: 260px; overflow: hidden; border-color: #e5e7eb !important; border-radius: 8px;"
                                     :style="{ top: dropdownStyle.top, bottom: dropdownStyle.bottom, left: dropdownStyle.left, width: dropdownStyle.width }"
                                     @click.stop>
                                    <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                        <input type="text"
                                               x-model="rows[index].search"
                                               @click.stop
                                               placeholder="Search spare..."
                                               class="form-control form-control-sm"
                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    </div>
                                    <div class="overflow-y-auto" style="max-height: 220px;">
                                        <template x-if="filteredSpares(index).length === 0">
                                            <div class="p-3 text-center text-muted small">No spares found</div>
                                        </template>
                                        <template x-for="s in filteredSpares(index)" :key="s.id">
                                            <div class="py-2 px-3"
                                                 style="cursor: pointer; color: #374151;"
                                                 @click="selectSpare(index, s.id)"
                                                 onmouseover="this.style.backgroundColor='#f9fafb'"
                                                 onmouseout="this.style.backgroundColor=''">
                                                <span x-text="s.name"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 120px;">
                                <label class="form-label fw-medium mb-1" style="color: #374151;">Quantity</label>
                                <input type="number" :name="'spares[' + index + '][quantity]'" class="form-control" style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                       min="1" x-model.number="row.quantity"
                                       :max="maxQty(row.spare_id)"
                                       :disabled="!row.spare_id">
                            </div>
                            <button type="button" @click="removeRow(index)" class="btn btn-sm btn-outline-danger align-self-end" style="margin-bottom: 2px;" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Status</button>
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
