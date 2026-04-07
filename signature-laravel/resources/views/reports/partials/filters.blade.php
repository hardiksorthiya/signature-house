@php
    $period = request('period', 'last_month');
    $reportTitle = $reportTitle ?? 'Report';
    $reportTotal = $reportTotal ?? null;
    $showCreatedBy = $showCreatedBy ?? true;
    $creators = $creators ?? collect([]);
    $showSeller = $showSeller ?? false;
    $allSellers = $allSellers ?? collect([]);
@endphp
<form method="GET" action="{{ $formAction }}" id="reportFiltersForm" class="report-filters-form"
      x-data="{
          filterSidebarOpen: false,
          creators: @js($creators->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()->toArray()),
          createdBySearch: '',
          createdById: @js(request('created_by') ?: ''),
          createdByOpen: false,
          get filteredCreators() {
              const s = (this.createdBySearch || '').trim().toLowerCase();
              if (!s) return this.creators;
              return this.creators.filter(c => (c.name || '').toLowerCase().includes(s));
          },
          createdByName() {
              const c = this.creators.find(x => x.id == this.createdById);
              return c ? c.name : '';
          }
      }"
      @click.away="createdByOpen = false">
    <input type="hidden" name="period" id="periodField" value="{{ $period }}">
    <input type="hidden" name="date_from" id="dateFromField" value="{{ request('date_from') }}">
    <input type="hidden" name="date_to" id="dateToField" value="{{ request('date_to') }}">
    @if($showCreatedBy)
    <input type="hidden" name="created_by" :value="createdById">
    @endif

    <!-- Filter sidebar overlay -->
    <div x-show="filterSidebarOpen" x-cloak
         @click="filterSidebarOpen = false"
         class="position-fixed top-0 start-0 w-100 h-100 bg-dark report-filter-overlay"></div>

    <!-- Filter sidebar -->
    <div x-show="filterSidebarOpen" x-cloak
         class="position-fixed top-0 end-0 h-100 bg-white shadow-lg report-filter-sidebar"
         @click.stop>
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0" style="color: #1f2937;">
                    <i class="fas fa-filter me-2" style="color: var(--primary-color);"></i>Filters
                </h5>
                <button type="button" @click="filterSidebarOpen = false" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label report-filter-label">Date range</label>
                <div class="d-flex flex-wrap gap-2 report-date-row">
                    <button type="button" class="btn btn-sm period-btn {{ $period === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="today">Today</button>
                    <button type="button" class="btn btn-sm period-btn {{ $period === 'yesterday' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="yesterday">Yesterday</button>
                    <button type="button" class="btn btn-sm period-btn {{ $period === 'last_week' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="last_week">Last Week</button>
                    <button type="button" class="btn btn-sm period-btn {{ $period === 'last_month' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="last_month">Last Month</button>
                    <button type="button" class="btn btn-sm period-btn {{ $period === 'last_year' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="last_year">Last Year</button>
                    <button type="button" class="btn btn-sm period-btn {{ $period === 'custom' ? 'btn-primary' : 'btn-outline-secondary' }}" data-period="custom">Custom</button>
                </div>
                <span class="d-none report-date-sep d-flex align-items-center gap-2 mt-2" id="customRangeWrap">
                    <input type="date" id="dateFrom" value="{{ request('date_from') }}" class="form-control form-control-sm report-date-input" aria-label="From date">
                    <span class="report-date-to">to</span>
                    <input type="date" id="dateTo" value="{{ request('date_to') }}" class="form-control form-control-sm report-date-input" aria-label="To date">
                </span>
            </div>

            @if($showCreatedBy)
            <div class="mb-3">
                <label class="form-label report-filter-label">Created by</label>
                <div class="position-relative">
                    <button type="button"
                            @click="createdByOpen = !createdByOpen"
                            class="form-control text-start d-flex justify-content-between align-items-center report-select-btn"
                            :class="{ 'report-select-btn-open': createdByOpen }">
                        <span class="text-truncate" x-text="createdById ? createdByName() : 'Select created by'" :class="{ 'text-muted': !createdById }"></span>
                        <i class="fas fa-chevron-down ms-2 flex-shrink-0 report-chevron" :class="{ 'rotate-180': createdByOpen }"></i>
                    </button>
                    <div x-show="createdByOpen"
                         x-cloak
                         x-transition
                         class="position-absolute w-100 bg-white border rounded shadow-lg mt-1 report-dropdown"
                         style="z-index: 1061;"
                         @click.stop>
                        <div class="p-2 border-bottom">
                            <input type="text"
                                   x-model="createdBySearch"
                                   @click.stop
                                   placeholder="Search created by..."
                                   class="form-control form-control-sm">
                        </div>
                        <div class="overflow-y-auto report-dropdown-list">
                            <div class="py-2 px-3 report-dropdown-item" @click="createdById = ''; createdByOpen = false">
                                <span class="fw-medium">All</span>
                            </div>
                            <template x-for="c in filteredCreators" :key="c.id">
                                <div class="py-2 px-3 report-dropdown-item" @click="createdById = c.id; createdByOpen = false">
                                    <span x-text="c.name"></span>
                                </div>
                            </template>
                            <template x-if="filteredCreators.length === 0">
                                <div class="p-3 text-center text-muted small">No users found</div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($showSeller)
            <div class="mb-3">
                <label class="form-label report-filter-label">Seller</label>
                <select name="seller_id" class="form-select report-input">
                    <option value="">All Sellers</option>
                    @foreach($allSellers as $s)
                        <option value="{{ $s->id }}" {{ request('seller_id') == $s->id ? 'selected' : '' }}>{{ $s->seller_name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label report-filter-label">Sort by</label>
                <select name="sort" class="form-select report-input">
                    @foreach($sortOptions as $opt)
                        <option value="{{ $opt['value'] }}" {{ request('sort', $defaultSort ?? 'created_at') === $opt['value'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label report-filter-label">Order</label>
                <select name="dir" class="form-select report-input">
                    <option value="desc" {{ request('dir', 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('dir') === 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1" @click="filterSidebarOpen = false">
                    <i class="fas fa-check me-2"></i>Apply
                </button>
                <a href="{{ $resetUrl }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 report-filters-card">
        <div class="card-header border-0 p-0 report-filters-list-header">
            <div class="list-header d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between py-3 px-3 px-md-4 gap-2">
                <div class="d-flex align-items-center flex-shrink-0" style="min-width: 0;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2 me-sm-3 flex-shrink-0 report-list-icon">
                        <i class="fas fa-chart-line text-white small"></i>
                    </div>
                    <h2 class="h5 h6 mb-0 fw-semibold text-truncate report-list-title">{{ $reportTitle }}</h2>
                    @if($reportTotal !== null)
                        <span class="badge ms-2 ms-sm-3 flex-shrink-0 report-list-badge">{{ $reportTotal }} Total</span>
                    @endif
                    <button type="button" @click="filterSidebarOpen = true" class="btn border-0 d-flex align-items-center justify-content-center ms-2 report-filter-btn" title="Filter">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center gap-2 flex-grow-1 flex-lg-grow-0" style="min-width: 0;">
                    <div class="flex-grow-1" style="min-width: 0;">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search anything…" class="form-control form-control-sm report-search-input" style="border-radius: 8px; border: 1px solid #e5e7eb; height: 38px;">
                    </div>
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center flex-shrink-0 report-search-submit" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    @can('export reports')
                        <a href="{{ $exportExcelUrl }}" class="report-export-icon report-export-excel" title="Export Excel"><i class="fas fa-file-excel"></i></a>
                        <a href="{{ $exportPdfUrl }}" class="report-export-icon report-export-pdf" title="Export PDF"><i class="fas fa-file-pdf"></i></a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.report-filter-overlay { opacity: 0.5; z-index: 1040; }
.report-filter-sidebar { z-index: 1050; width: 320px; max-width: 100vw; overflow-y: auto; border-left: 1px solid #e5e7eb; }
.report-filters-form .report-filter-label { font-weight: 500; color: #374151; margin-bottom: 0.35rem; font-size: 0.9rem; }
.report-filters-form .report-input { border-radius: 8px; border: 1px solid #e5e7eb; min-height: 38px; width: 100%; }
.report-date-row { min-height: 0; }
.report-date-sep .report-date-input { flex: 1; min-width: 0; border-radius: 8px; border: 1px solid #e5e7eb; }
.report-date-to { color: #6b7280; font-size: 0.875rem; white-space: nowrap; }
.report-select-btn { min-height: 38px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; }
.report-select-btn-open { border-color: #93c5fd !important; box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.25); }
.report-chevron { transition: transform 0.2s ease; color: #6b7280; }
.report-dropdown { max-height: 220px; border-color: #e5e7eb !important; border-radius: 8px; }
.report-dropdown-list { max-height: 180px; }
.report-dropdown-item { cursor: pointer; color: #374151; }
.report-dropdown-item:hover { background-color: #f9fafb; }

.report-filters-card { border-radius: 12px; background: linear-gradient(to bottom, #fff 0%, color-mix(in srgb, var(--primary-color) 6%, #fff) 100%); }
.report-filters-list-header .list-header { border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent); }
.report-list-icon { width: 40px; height: 40px; min-width: 40px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important; }
.report-list-title { color: #1f2937; }
.report-list-badge { background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color); font-size: 0.75rem; padding: 0.25rem 0.5rem; }
.report-filter-btn { width: 36px; height: 36px; min-width: 36px; background: transparent; color: var(--primary-color); }
.report-filter-btn:hover { color: var(--primary-dark); background: color-mix(in srgb, var(--primary-color) 12%, transparent); border-radius: 8px; }
.report-search-input:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.15); }
.report-search-submit { border-radius: 8px; width: 38px; height: 38px; min-width: 38px; }
.report-export-icon { width: 42px; height: 42px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.25rem; border: 2px solid; transition: opacity 0.2s, transform 0.15s; }
.report-export-icon:hover { opacity: 0.9; transform: scale(1.05); }
.report-export-excel { color: #198754; border-color: #198754; background: rgba(25, 135, 84, 0.08); }
.report-export-excel:hover { color: #157347; border-color: #157347; background: rgba(25, 135, 84, 0.15); }
.report-export-pdf { color: #dc3545; border-color: #dc3545; background: rgba(220, 53, 69, 0.08); }
.report-export-pdf:hover { color: #bb2d3b; border-color: #bb2d3b; background: rgba(220, 53, 69, 0.15); }
@media (max-width: 991.98px) { .report-filter-sidebar { width: 100%; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportFiltersForm');
    if (!form) return;
    const periodField = form.querySelector('#periodField');
    const dateFromField = form.querySelector('#dateFromField');
    const dateToField = form.querySelector('#dateToField');
    const dateFrom = form.querySelector('#dateFrom');
    const dateTo = form.querySelector('#dateTo');
    const customRangeWrap = form.querySelector('#customRangeWrap');
    if (!periodField || !customRangeWrap) return;

    function showCustomRange() {
        customRangeWrap.classList.remove('d-none');
        customRangeWrap.classList.add('d-flex');
    }
    function hideCustomRange() {
        customRangeWrap.classList.add('d-none');
        customRangeWrap.classList.remove('d-flex');
        if (dateFromField) dateFromField.value = '';
        if (dateToField) dateToField.value = '';
    }

    form.querySelectorAll('.period-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            form.querySelectorAll('.period-btn').forEach(function(b) {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary');
            const p = this.getAttribute('data-period');
            periodField.value = p;
            if (p === 'custom') showCustomRange();
            else hideCustomRange();
        });
    });

    if (periodField.value === 'custom') showCustomRange();

    form.addEventListener('submit', function() {
        if (periodField.value === 'custom' && dateFrom && dateTo) {
            if (dateFromField) dateFromField.value = dateFrom.value || '';
            if (dateToField) dateToField.value = dateTo.value || '';
        }
    });
});
</script>
