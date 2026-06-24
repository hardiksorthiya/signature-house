@php
    use App\Support\ComplaintAreaAssignment;
    $feedbackMode = $feedbackMode ?? false;
    $colspan = $feedbackMode ? 14 : 13;
@endphp
<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-head-hp" style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff)) !important;">
            <tr>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Sr.no</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Client</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Area</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Complain Type</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Machine Category</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Khata No. / Serial No.</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Other Detail</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Status</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">{{ $dateColumnHeader ?? 'Time' }}</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Assign To</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Assigned Time</th>
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Completed Time</th>
                @if($feedbackMode)
                <th class="p-2 small fw-semibold" style="color: var(--primary-color) !important;">Feedback Status</th>
                @endif
                <th class="px-4 py-3 small fw-semibold text-center" style="color: var(--primary-color) !important;">{{ $feedbackMode ? 'Feedback' : 'Actions' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $complaint)
                @include('complaints.partials.row', [
                    'complaint' => $complaint,
                    'rowNumber' => $loop->iteration,
                    'dateColumn' => $dateColumn ?? 'time',
                    'dateField' => $dateField ?? 'created_at',
                    'feedbackMode' => $feedbackMode,
                    'feedbackStatuses' => $feedbackStatuses ?? config('complaint-feedback.statuses'),
                ])
            @empty
                <tr>
                    <td colspan="{{ $colspan }}" class="px-4 py-4 text-center text-muted small">{{ $emptyRowMessage ?? 'No complaints for this day.' }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
