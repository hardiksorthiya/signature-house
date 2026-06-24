@php
    $areasForJs = $areas->map(fn ($a) => [
        'id' => $a->id,
        'name' => $a->name,
        'city' => $a->city?->name,
        'state' => $a->city?->state?->name,
        'label' => trim($a->name . ($a->city ? ' — ' . $a->city->name : '') . ($a->city?->state ? ', ' . $a->city->state->name : '')),
    ])->values();
@endphp
<x-app-layout>
    <div class="mb-4">
        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-lg">
                <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Assign Complain Area</h1>
                <p class="text-muted mb-0 small">Assign areas to Junior / Senior Engineers and Unloading Technicians — they will see complaints for clients in those areas</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <form action="{{ route('complaints.area-assignment.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow-sm border-0 complaint-area-assign-card" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 p-0" style="background: transparent;">
                <div class="list-header d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between py-3 px-3 px-md-4 border-bottom gap-2" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="d-flex align-items-center overflow-hidden" style="min-width: 0;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0" style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-map-marked-alt text-white small"></i>
                        </div>
                        <h2 class="h5 h6 mb-0 fw-semibold text-truncate" style="color: #1f2937;">User Areas</h2>
                        <span class="badge ms-2 flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #ffffff); color: var(--primary-color); font-size: 0.75rem;">{{ $users->count() }} Users</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="complaint-area-assign-table-wrap">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
                            <tr>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important; width: 220px;">User</th>
                                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Assigned Areas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $assignedIds = $user->complaintAreas->pluck('id')->toArray();
                                @endphp
                                <tr class="border-bottom complaint-area-assign-row">
                                    <td class="px-3 py-2">
                                        <div class="fw-semibold" style="color: #1f2937;">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->roles->pluck('name')->join(', ') }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="complaint-area-dropdown-wrap"
                                             x-data="complaintAreaRow(@js($assignedIds), @js($areasForJs))"
                                             @click.away="dropdownOpen = false">
                                            <label class="form-label fw-medium small mb-1" style="color: #374151;">Areas <span class="text-muted fw-normal">(search &amp; select)</span></label>
                                            <div class="position-relative">
                                                <button type="button"
                                                        @click="dropdownOpen = !dropdownOpen"
                                                        class="form-control form-control-sm text-start d-flex justify-content-between align-items-center gap-2 complaint-area-dropdown-trigger"
                                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white;">
                                                    <span class="d-flex flex-wrap gap-1 align-items-center flex-grow-1">
                                                        <template x-if="selectedIds.length === 0">
                                                            <span class="text-muted">Select areas...</span>
                                                        </template>
                                                        <template x-if="selectedIds.length > 0">
                                                            <span class="d-flex flex-wrap gap-1">
                                                                <template x-for="label in selectedLabels.slice(0, 4)" :key="label">
                                                                    <span class="badge rounded-pill px-2 py-1" style="background: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);" x-text="label"></span>
                                                                </template>
                                                                <template x-if="selectedLabels.length > 4">
                                                                    <span class="badge rounded-pill px-2 py-1 bg-secondary" x-text="'+' + (selectedLabels.length - 4) + ' more'"></span>
                                                                </template>
                                                            </span>
                                                        </template>
                                                    </span>
                                                    <i class="fas fa-chevron-down flex-shrink-0" :class="{ 'rotate-180': dropdownOpen }" style="transition: transform 0.2s ease;"></i>
                                                </button>
                                                <div x-show="dropdownOpen"
                                                     x-cloak
                                                     x-transition
                                                     class="position-absolute start-0 end-0 bg-white border rounded shadow-lg mt-1 complaint-area-dropdown-panel"
                                                     @click.stop>
                                                    <div class="p-2 border-bottom sticky-top bg-white" style="border-color: #e5e7eb !important; top: 0;">
                                                        <input type="text"
                                                               x-model="search"
                                                               @click.stop
                                                               placeholder="Search area, city, state..."
                                                               class="form-control form-control-sm"
                                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                    <template x-if="filteredAreas.length === 0">
                                                        <div class="p-3 text-center text-muted small">No areas match your search.</div>
                                                    </template>
                                                    <template x-for="a in filteredAreas" :key="a.id">
                                                        <div class="d-flex align-items-center py-2 px-3"
                                                             style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;"
                                                             :style="isSelected(a.id) ? 'background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff);' : ''"
                                                             @click="toggle(a.id)">
                                                            <input class="form-check-input me-3"
                                                                   type="checkbox"
                                                                   :checked="isSelected(a.id)"
                                                                   style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                                   @click.stop="toggle(a.id)">
                                                            <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0; font-size: 0.875rem; color: #374151;" x-text="a.label"></label>
                                                            <i class="fas fa-check text-primary ms-2" x-show="isSelected(a.id)" style="font-size: 0.875rem;"></i>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <template x-for="id in selectedIds" :key="id">
                                                <input type="hidden" name="assignments[{{ $user->id }}][]" :value="id">
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-5 text-center text-muted">
                                        <i class="fas fa-user-cog fa-2x mb-2" style="opacity: 0.3;"></i>
                                        <p class="mb-0">No Junior Engineer, Senior Engineer, or Unloading Technician users found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($users->isNotEmpty())
                <div class="card-footer border-0 bg-transparent px-4 py-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Assignments
                    </button>
                </div>
            @endif
        </div>
    </form>

    <style>
        .complaint-area-assign-card .complaint-area-assign-table-wrap {
            overflow: visible;
        }

        .complaint-area-assign-card .complaint-area-assign-row td {
            vertical-align: middle;
        }

        .complaint-area-assign-card .complaint-area-dropdown-wrap {
            max-width: 520px;
        }

        .complaint-area-assign-card .complaint-area-dropdown-trigger {
            min-height: 38px;
            height: auto;
        }

        .complaint-area-assign-card .complaint-area-dropdown-panel {
            z-index: 1050;
            max-height: 260px;
            overflow-y: auto;
            border-color: #e5e7eb !important;
            border-radius: 8px;
        }
    </style>

    <script>
        function complaintAreaRow(initialIds, areas) {
            return {
                selectedIds: [...initialIds],
                areas: areas,
                dropdownOpen: false,
                search: '',
                get filteredAreas() {
                    const s = (this.search || '').trim().toLowerCase();
                    if (!s) return this.areas;
                    return this.areas.filter(a => {
                        const hay = [a.name, a.city, a.state, a.label].filter(Boolean).join(' ').toLowerCase();
                        return hay.includes(s);
                    });
                },
                isSelected(id) {
                    return this.selectedIds.includes(id);
                },
                toggle(id) {
                    if (this.selectedIds.includes(id)) {
                        this.selectedIds = this.selectedIds.filter(x => x !== id);
                    } else {
                        this.selectedIds = [...this.selectedIds, id];
                    }
                },
                get selectedLabels() {
                    return this.selectedIds
                        .map(id => this.areas.find(a => a.id === id)?.label)
                        .filter(Boolean);
                },
            };
        }
    </script>
</x-app-layout>
