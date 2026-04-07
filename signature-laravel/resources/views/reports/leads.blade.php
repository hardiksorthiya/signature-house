<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Leads Report</h1>
            <p class="text-muted mb-0 small">Filter, sort and export leads</p>
        </div>
    </div>

    @include('reports.partials.filters', [
        'formAction' => route('reports.leads'),
        'resetUrl' => route('reports.leads'),
        'exportExcelUrl' => route('reports.leads.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'excel'])),
        'exportPdfUrl' => route('reports.leads.export', array_merge(request()->only(['period', 'date_from', 'date_to', 'created_by', 'sort', 'dir', 'search']), ['format' => 'pdf'])),
        'creators' => $creators,
        'sortOptions' => [['value' => 'created_at', 'label' => 'Date'], ['value' => 'name', 'label' => 'Name']],
        'defaultSort' => 'created_at',
        'reportTitle' => 'Leads List',
        'reportTotal' => $leads->total(),
    ])

    <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);">
        <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Leads <span class="badge ms-2" style="background-color: color-mix(in srgb, var(--primary-color) 15%, #fff); color: var(--primary-color);">{{ $leads->total() }}</span></h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background: color-mix(in srgb, var(--primary-color) 12%, #fff);">
                        <tr>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Date</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Name</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Phone</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Business</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">State / City</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Status</th>
                            <th class="p-2 small fw-semibold" style="color: var(--primary-color);">Created by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td class="px-2"><small>{{ $lead->created_at->format('d M Y') }}</small></td>
                                <td class="px-4 py-3 fw-medium" style="color: #1f2937;">{{ $lead->name }}</td>
                                <td class="px-2">{{ $lead->phone_number }}</td>
                                <td class="px-2">{{ $lead->business->name ?? '—' }}</td>
                                <td class="px-2">{{ ($lead->state->name ?? '') . ' / ' . ($lead->city->name ?? '—') }}</td>
                                <td class="px-2">{{ $lead->status->name ?? '—' }}</td>
                                <td class="px-2">{{ $lead->creator->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-5 text-center text-muted">No leads found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leads->hasPages())
            <div class="card-footer border-0 bg-transparent py-2">{{ $leads->links() }}</div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reportFiltersForm');
            const periodField = document.getElementById('periodField');
            const dateFromField = document.getElementById('dateFromField');
            const dateToField = document.getElementById('dateToField');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const customRangeWrap = document.getElementById('customRangeWrap');

            document.querySelectorAll('.period-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.period-btn').forEach(function(b) {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-primary');
                    const p = this.getAttribute('data-period');
                    periodField.value = p;
                    if (p === 'custom') {
                        customRangeWrap.classList.remove('d-none');
                        customRangeWrap.classList.add('d-inline-block');
                    } else {
                        customRangeWrap.classList.add('d-none');
                        dateFromField.value = '';
                        dateToField.value = '';
                    }
                });
            });

            if (periodField.value === 'custom') {
                customRangeWrap.classList.remove('d-none');
                customRangeWrap.classList.add('d-inline-block');
            }

            form.addEventListener('submit', function() {
                if (periodField.value === 'custom' && dateFrom && dateTo) {
                    dateFromField.value = dateFrom.value || '';
                    dateToField.value = dateTo.value || '';
                }
            });
        });
    </script>
</x-app-layout>
