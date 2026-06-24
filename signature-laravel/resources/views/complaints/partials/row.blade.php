@php
    use App\Support\ComplaintAreaAssignment;
    $canAct = ComplaintAreaAssignment::userCanActOnComplaint($complaint);
    $canAssign = ComplaintAreaAssignment::userCanAssignComplaint($complaint);
    $timeFormat = ($dateColumn ?? 'date') === 'time' ? 'h:i A' : 'd M Y h:i A';
    $feedbackMode = $feedbackMode ?? false;
    $dateField = $dateField ?? 'created_at';
    $rowDate = $complaint->{$dateField} ?? $complaint->created_at;
    $feedbackStatuses = $feedbackStatuses ?? config('complaint-feedback.statuses');
    $feedbackLabel = $complaint->feedback_status ? ($feedbackStatuses[$complaint->feedback_status] ?? ucfirst(str_replace('_', ' ', $complaint->feedback_status))) : null;
    $feedbackBadgeClass = match ($complaint->feedback_status) {
        'complaint_solved' => 'bg-success',
        'complaint_not_solved' => 'bg-danger',
        'under_observation' => 'bg-info text-dark',
        'work_in_progress' => 'bg-primary',
        'call_not_answered' => 'bg-warning text-dark',
        default => 'bg-secondary',
    };
    $canAddFeedback = $feedbackMode && auth()->user()->can('edit complain');
    $feedbackListQuery = request()->only(['search', 'area_id', 'machine_category_id', 'complain_type_id']);
    $feedbackFormUrl = $feedbackMode
        ? route('complaints.feedback-form', ['complaint' => $complaint] + array_filter($feedbackListQuery))
        : null;
@endphp
<tr class="border-bottom{{ $feedbackFormUrl ? ' complaint-feedback-row' : '' }}"
    @if($feedbackFormUrl)
        data-feedback-href="{{ $feedbackFormUrl }}"
        onclick="if (!event.target.closest('a, button, form')) { window.location.href = this.dataset.feedbackHref; }"
    @endif
>
    <td class="px-2"><span class="fw-medium" style="color: #1f2937;">{{ $rowNumber }}</span></td>
    <td class="px-2">
        @if($complaint->contract)
            <div class="fw-medium" style="color: #1f2937;">{{ $complaint->contract->company_name ?: $complaint->contract->buyer_name }}</div>
            <small class="text-muted">{{ $complaint->contract->contract_number }}</small>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td class="px-2">
        <span style="color: #374151;">{{ $complaint->contract?->area?->name ?? '—' }}</span>
    </td>
    <td class="px-2">
        @if($complaint->complainType)
            <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">{{ $complaint->complainType->name }}</span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td class="px-2"><span style="color: #374151;">{{ $complaint->machineCategory->name ?? '—' }}</span></td>
    <td class="px-2"><span style="color: #374151;">{{ $complaint->machine_khata_number ?: '—' }}</span></td>
    <td class="px-2"><span style="color: #6b7280;">{{ $complaint->other_detail ? Str::limit($complaint->other_detail, 40) : '—' }}</span></td>
    <td class="px-2">
        @if(($complaint->status ?? 'on_going') === 'completed')
            <span class="badge bg-success">Completed</span>
        @else
            <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">On Going</span>
        @endif
    </td>
    <td class="px-2"><small>{{ $rowDate ? (($dateColumn ?? 'date') === 'time' ? $rowDate->format('h:i A') : $rowDate->format('d M Y')) : '—' }}</small></td>
    <td class="px-2">
        @if($complaint->assignees->count())
            <div class="d-flex flex-column gap-1">
                @foreach($complaint->assignees as $assignee)
                    <small class="text-nowrap" style="color: #374151;">
                        <i class="fas fa-user-circle me-1" style="color: var(--primary-color); font-size: 0.7rem;"></i>{{ $assignee->name }}
                    </small>
                @endforeach
            </div>
        @else
            <small class="text-muted">—</small>
        @endif
    </td>
    <td class="px-2"><small>{{ $complaint->assigned_at ? $complaint->assigned_at->format($timeFormat) : '—' }}</small></td>
    <td class="px-2"><small>{{ $complaint->completed_at ? $complaint->completed_at->format($timeFormat) : '—' }}</small></td>
    @if($feedbackMode)
    <td class="px-2">
        @if($feedbackLabel)
            <span class="badge {{ $feedbackBadgeClass }}">{{ $feedbackLabel }}</span>
        @else
            <span class="badge bg-secondary">Pending</span>
        @endif
    </td>
    @endif
    <td class="px-2">
        <div class="d-flex gap-1 flex-wrap justify-content-center">
            @if($feedbackMode)
                <a href="{{ $feedbackFormUrl }}"
                   class="btn btn-sm btn-outline-primary"
                   title="{{ $canAddFeedback ? 'Add Feedback' : 'View Feedback' }}"
                   onclick="event.stopPropagation();">
                    <i class="fas fa-comment-dots"></i>
                </a>
            @else
            @can('view complain')
            <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
            @endcan
            @can('edit complain')
                @if($canAct)
                <a href="{{ route('complaints.status', $complaint) }}" class="btn btn-sm btn-outline-success" title="Status"><i class="fas fa-tasks"></i></a>
                <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-sm btn-outline-info" title="Edit"><i class="fas fa-edit"></i></a>
                @endif
                @if($canAssign)
                <a href="{{ route('complaints.assign', $complaint) }}" class="btn btn-sm btn-outline-secondary" title="Assign"><i class="fas fa-user-plus"></i></a>
                @endif
            @endcan
            @can('delete complain')
                @if($canAct)
                <form action="{{ route('complaints.destroy', $complaint) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this complaint?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
                @endif
            @endcan
            @if(! $canAct && ! $canAssign && auth()->user()->can('view complain') && ! auth()->user()->can('edit complain') && ! auth()->user()->can('delete complain'))
                <span class="text-muted small align-self-center" title="View only">—</span>
            @endif
            @endif
        </div>
    </td>
</tr>
