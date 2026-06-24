@php
    use App\Support\ComplaintAreaAssignment;
    $contract = $complaint->contract;
    $canAct = ComplaintAreaAssignment::userCanActOnComplaint($complaint);
    $canAssign = ComplaintAreaAssignment::userCanAssignComplaint($complaint);
    $isCompleted = ($complaint->status ?? 'on_going') === 'completed';
    $fromFeedback = request('from') === 'feedback';
    $backRoute = $fromFeedback
        ? 'complaints.feedback'
        : ($isCompleted ? 'complaints.completed' : 'complaints.active');
    $fromQuery = $fromFeedback ? ['from' => 'feedback'] : [];
@endphp
<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                <h1 class="h2 fw-semibold mb-0" style="color: #1f2937;">Complaint Details</h1>
                @if($isCompleted)
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>
                @else
                    <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">
                        <i class="fas fa-clock me-1"></i>On Going
                    </span>
                @endif
            </div>
            <p class="text-muted mb-0">Complaint #{{ $complaint->id }} · {{ $complaint->created_at->format('d M Y') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            @can('edit complain')
                @if($canAct)
                <a href="{{ route('complaints.status', array_merge([$complaint], $fromQuery)) }}" class="btn btn-outline-success">
                    <i class="fas fa-tasks me-2"></i>Update Status
                </a>
                <a href="{{ route('complaints.edit', array_merge([$complaint], $fromQuery)) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                @endif
                @if($canAssign)
                <a href="{{ route('complaints.assign', array_merge([$complaint], $fromQuery)) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-user-plus me-2"></i>Assign
                </a>
                @endif
            @endcan
            @can('delete complain')
                @if($canAct)
                <form action="{{ route('complaints.destroy', array_merge([$complaint], $fromQuery)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this complaint?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-2"></i>Remove
                    </button>
                </form>
                @endif
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 8px;">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8 col-md-12">
            @if($contract)
            <div class="card shadow-sm border-0 complain-panel mb-4">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 px-md-4 border-bottom complain-panel-divider">
                        <div class="complain-panel-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Client Details</h2>
                            <p class="text-muted small mb-0">Customer and contract information</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="complain-label">Buyer Name</label>
                            <div class="complain-value fw-semibold">{{ $contract->buyer_name ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="complain-label">Company Name</label>
                            <div class="complain-value">{{ $contract->company_name ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="complain-label">Contract Number</label>
                            <div class="complain-value fw-semibold" style="color: var(--primary-color);">{{ $contract->contract_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="complain-label">Area</label>
                            <div class="complain-value">
                                @if($contract->area)
                                    <span class="badge rounded-pill" style="background: color-mix(in srgb, var(--primary-color) 12%, #fff); color: var(--primary-color); font-weight: 500;">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $contract->area->name }}
                                    </span>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top complain-panel-divider">
                        <h3 class="h6 fw-semibold mb-3" style="color: var(--primary-color);">
                            <i class="fas fa-location-dot me-2"></i>Address & Contact
                        </h3>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="complain-label">Address</label>
                                <div class="complain-highlight-box">{{ $contract->contact_address ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="complain-label">City</label>
                                <div class="complain-value">{{ $contract->city->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="complain-label">State</label>
                                <div class="complain-value">{{ $contract->state->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="complain-label">Email</label>
                                <div class="complain-value">
                                    @if($contract->email)
                                        <a href="mailto:{{ $contract->email }}" class="text-decoration-none" style="color: var(--primary-color);">{{ $contract->email }}</a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="complain-label">Phone Number</label>
                                <div class="complain-value">
                                    @if($contract->phone_number)
                                        <a href="tel:{{ $contract->phone_number }}" class="text-decoration-none" style="color: #1f2937;">{{ $contract->phone_number }}</a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="complain-label">Phone Number 2</label>
                                <div class="complain-value">
                                    @if($contract->phone_number_2)
                                        <a href="tel:{{ $contract->phone_number_2 }}" class="text-decoration-none" style="color: #1f2937;">{{ $contract->phone_number_2 }}</a>
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card shadow-sm border-0 complain-panel">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 px-md-4 border-bottom complain-panel-divider">
                        <div class="complain-panel-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Complain Details</h2>
                            <p class="text-muted small mb-0">Machine, type, and issue information</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="complain-label">Complain Type</label>
                            <div class="complain-value">
                                @if($complaint->complainType)
                                    <span class="badge rounded-pill" style="background: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color); font-weight: 500;">
                                        {{ $complaint->complainType->name }}
                                    </span>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="complain-label">Machine Category</label>
                            <div class="complain-value">{{ $complaint->machineCategory->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="complain-label">Khata No. / Serial No.</label>
                            <div class="complain-value fw-medium">{{ $complaint->machine_khata_number ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="complain-label">Complaint Date</label>
                            <div class="complain-value">{{ $complaint->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="complain-label">Other Detail</label>
                            <div class="complain-highlight-box">{{ $complaint->other_detail ?: '—' }}</div>
                        </div>
                    </div>

                    @if($complaint->spares->isNotEmpty())
                    <div class="mt-4 pt-3 border-top complain-panel-divider">
                        <h3 class="h6 fw-semibold mb-3" style="color: var(--primary-color);">
                            <i class="fas fa-cogs me-2"></i>Spare Parts Used
                        </h3>
                        <div class="table-responsive rounded" style="border: 1px solid #e5e7eb;">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 12%, #ffffff), color-mix(in srgb, var(--primary-color) 18%, #ffffff));">
                                    <tr>
                                        <th class="px-3 py-2 small fw-semibold" style="color: var(--primary-color);">Spare</th>
                                        <th class="px-3 py-2 small fw-semibold text-end" style="color: var(--primary-color);">Quantity</th>
                                        <th class="px-3 py-2 small fw-semibold" style="color: var(--primary-color);">Date Used</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($complaint->spares as $spare)
                                    <tr>
                                        <td class="px-3 py-2 fw-medium" style="color: #1f2937;">{{ $spare->name }}</td>
                                        <td class="px-3 py-2 text-end">{{ $spare->pivot->quantity }}</td>
                                        <td class="px-3 py-2 text-muted small">{{ $spare->pivot->used_at ? \Carbon\Carbon::parse($spare->pivot->used_at)->format('d M Y') : '—' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm border-0 complain-panel mb-4">
                <div class="card-header border-0 p-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 px-3 px-md-4 border-bottom complain-panel-divider">
                        <div class="complain-panel-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Status Summary</h2>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="text-center mb-4 pb-3 border-bottom complain-panel-divider">
                        @if($isCompleted)
                            <div class="complain-status-icon bg-success-subtle text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="fw-semibold mt-2" style="color: #059669;">Completed</div>
                            <div class="text-muted small">This complaint has been resolved</div>
                        @else
                            <div class="complain-status-icon" style="background: color-mix(in srgb, var(--primary-color) 12%, #fff); color: var(--primary-color);">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="fw-semibold mt-2" style="color: var(--primary-color);">On Going</div>
                            <div class="text-muted small">Complaint is in progress</div>
                        @endif
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div>
                            <label class="complain-label">Complaint Date</label>
                            <div class="complain-value">{{ $complaint->created_at->format('d M Y') }}</div>
                        </div>
                        <div>
                            <label class="complain-label">Assigned To</label>
                            <div class="complain-value">
                                @if($complaint->assignees->count())
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($complaint->assignees as $assignee)
                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background: #f3f4f6; color: #374151; font-weight: 500; padding: 0.4rem 0.65rem;">
                                                <i class="fas fa-user-circle" style="color: var(--primary-color);"></i>{{ $assignee->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </div>
                        </div>
                        @if($complaint->assigned_at)
                        <div>
                            <label class="complain-label">Assigned Time</label>
                            <div class="complain-value">{{ $complaint->assigned_at->format('d M Y, h:i A') }}</div>
                        </div>
                        @endif
                        @if($complaint->completed_at)
                        <div>
                            <label class="complain-label">Completed Time</label>
                            <div class="complain-value">{{ $complaint->completed_at->format('d M Y, h:i A') }}</div>
                        </div>
                        @endif
                        @if($complaint->creator)
                        <div>
                            <label class="complain-label">Created By</label>
                            <div class="complain-value">{{ $complaint->creator->name }}</div>
                        </div>
                        @endif
                        <div>
                            <label class="complain-label">Remarks</label>
                            <div class="complain-highlight-box small">{{ $complaint->remarks ?: 'No remarks added yet.' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .complain-panel {
            background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%);
            border-radius: 12px;
            border: 1px solid color-mix(in srgb, var(--primary-color) 12%, #e5e7eb) !important;
            overflow: hidden;
        }

        .complain-panel-divider {
            border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;
        }

        .complain-panel-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 50%;
            margin-right: 0.75rem;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .complain-label {
            display: block;
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .complain-value {
            color: #1f2937;
            line-height: 1.4;
        }

        .complain-highlight-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem 0.9rem;
            color: #374151;
            line-height: 1.5;
            white-space: pre-line;
        }

        .complain-status-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</x-app-layout>
