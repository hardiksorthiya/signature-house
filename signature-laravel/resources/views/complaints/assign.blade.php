@php
    $fromFeedback = request('from') === 'feedback';
    $backUrl = $fromFeedback
        ? route('complaints.show', [$complaint, 'from' => 'feedback'])
        : route('complaints.show', $complaint);
@endphp
<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Assign Complaint</h1>
            <p class="text-muted mb-0">Assign complaint #{{ $complaint->id }} to engineers</p>
        </div>
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('complaints.assign-update', $complaint) }}" method="POST" x-data="{
                assignDropdownOpen: false,
                assignSearch: '',
                selectedIds: @js($complaint->assignees->pluck('id')->toArray()),
                users: @js($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()->toArray()),
                get filteredUsers() {
                    const s = (this.assignSearch || '').trim().toLowerCase();
                    if (!s) return this.users;
                    return this.users.filter(u => (u.name || '').toLowerCase().includes(s));
                },
                isSelected(id) { return this.selectedIds.includes(id); },
                toggle(id) {
                    if (this.selectedIds.includes(id)) {
                        this.selectedIds = this.selectedIds.filter(x => x !== id);
                    } else {
                        this.selectedIds = [...this.selectedIds, id];
                    }
                },
                get selectedLabels() {
                    return this.selectedIds.map(id => this.users.find(u => u.id == id)?.name).filter(Boolean);
                }
            }" @click.away="assignDropdownOpen = false">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-medium" style="color: #374151;">Assign To <span class="text-muted small">(Junior / Senior Engineer / Unloading Technician)</span></label>
                    <div class="position-relative">
                        <button type="button"
                                @click="assignDropdownOpen = !assignDropdownOpen"
                                class="form-control text-start d-flex justify-content-between align-items-center flex-wrap gap-2"
                                style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 44px;">
                            <span class="d-flex flex-wrap gap-1 align-items-center">
                                <template x-if="selectedIds.length === 0">
                                    <span class="text-muted">Select engineers...</span>
                                </template>
                                <template x-if="selectedIds.length > 0">
                                    <span class="d-flex flex-wrap gap-1">
                                        <template x-for="name in selectedLabels" :key="name">
                                            <span class="badge rounded-pill px-2 py-1" style="background: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);" x-text="name"></span>
                                        </template>
                                    </span>
                                </template>
                            </span>
                            <i class="fas fa-chevron-down ms-2" :class="{ 'rotate-180': assignDropdownOpen }" style="transition: transform 0.2s ease; flex-shrink: 0;"></i>
                        </button>
                        <div x-show="assignDropdownOpen"
                             x-cloak
                             class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                             style="z-index: 1000; max-height: 280px; overflow-y: auto; border-color: #e5e7eb !important; border-radius: 8px;"
                             @click.stop>
                            <div class="p-2 border-bottom" style="border-color: #e5e7eb !important;">
                                <input type="text"
                                       x-model="assignSearch"
                                       @click.stop
                                       placeholder="Search engineer..."
                                       class="form-control form-control-sm"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            </div>
                            <template x-if="filteredUsers.length === 0">
                                <div class="p-3 text-center text-muted small">No engineers found</div>
                            </template>
                            <template x-for="u in filteredUsers" :key="u.id">
                                <div class="d-flex align-items-center py-2 px-3"
                                     @click="toggle(u.id)"
                                     style="cursor: pointer;"
                                     :class="{ 'text-white': isSelected(u.id) }"
                                     :style="isSelected(u.id) ? 'background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));' : ''"
                                     onmouseover="if(!this.classList.contains('text-white')) this.style.backgroundColor='#f3f4f6'"
                                     onmouseout="if(!this.classList.contains('text-white')) this.style.backgroundColor=''">
                                    <div class="flex-grow-1 fw-medium" x-text="u.name"></div>
                                    <i class="fas fa-check ms-2" x-show="isSelected(u.id)"></i>
                                </div>
                            </template>
                        </div>
                    </div>
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="assigned_to_ids[]" :value="id">
                    </template>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-check me-2"></i>Save Assignment</button>
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
