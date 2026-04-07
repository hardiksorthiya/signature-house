@php
    $contract = $complaint->contract;
    $clientAddress = $contract ? trim(implode(', ', array_filter([
        $contract->contact_address ?? '',
        $contract->area->name ?? '',
        $contract->city->name ?? '',
        $contract->state->name ?? '',
    ]))) : '';
@endphp
<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Complaint Details</h1>
            <p class="text-muted mb-0">Complaint #{{ $complaint->id }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
            @can('edit complain')
            <a href="{{ route('complaints.status', $complaint) }}" class="btn btn-outline-success"><i class="fas fa-tasks me-2"></i>Update Status</a>
            <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edit</a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent);">
            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Complain Details</h2>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6"><label class="text-muted small">Client</label><div class="fw-medium">{{ $contract ? ($contract->company_name ?: $contract->buyer_name) . ' (' . $contract->contract_number . ')' : '—' }}</div></div>
                <div class="col-md-6"><label class="text-muted small">Complain Type</label><div class="fw-medium">{{ $complaint->complainType->name ?? '—' }}</div></div>
                @if($contract)
                <div class="col-12"><label class="text-muted small">Client Address</label><div>{{ $clientAddress ?: '—' }}</div></div>
                <div class="col-md-6"><label class="text-muted small">Phone Number</label><div>{{ $contract->phone_number ?? '—' }}{{ $contract->phone_number_2 ? ' / ' . $contract->phone_number_2 : '' }}</div></div>
                <div class="col-md-6"><label class="text-muted small">Email</label><div>{{ $contract->email ?? '—' }}</div></div>
                @endif
                <div class="col-md-6"><label class="text-muted small">Machine Category</label><div>{{ $complaint->machineCategory->name ?? '—' }}</div></div>
                <div class="col-md-6"><label class="text-muted small">Khata No. / Serial No.</label><div>{{ $complaint->machine_khata_number ?: '—' }}</div></div>
                <div class="col-12"><label class="text-muted small">Other Detail</label><div>{{ $complaint->other_detail ?: '—' }}</div></div>
                <div class="col-md-6"><label class="text-muted small">Assigned To</label><div>{{ $complaint->assignees->count() ? $complaint->assignees->pluck('name')->join(', ') : '—' }}</div></div>
                <div class="col-md-6"><label class="text-muted small">Date</label><div>{{ $complaint->created_at->format('d M Y') }}</div></div>
                <div class="col-md-6"><label class="text-muted small">Status</label><div>
                    @if(($complaint->status ?? 'on_going') === 'completed')
                        <span class="badge bg-success">Completed</span>
                    @else
                        <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">On Going</span>
                    @endif
                    <div class="text-muted small mt-2">
                        <span class="fw-medium" style="color: #374151;">Remarks:</span>
                        {{ $complaint->remarks ?: '—' }}
                    </div>
                </div></div>
                @if($complaint->spares->isNotEmpty())
                <div class="col-12">
                    <label class="text-muted small d-block mb-2">Spare Parts Used</label>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0" style="border-color: #e5e7eb;">
                            <thead style="background: color-mix(in srgb, var(--primary-color) 10%, #fff);">
                                <tr>
                                    <th class="px-3 py-2 small fw-medium" style="color: var(--primary-color);">Spare</th>
                                    <th class="px-3 py-2 small fw-medium text-end" style="color: var(--primary-color);">Quantity</th>
                                    <th class="px-3 py-2 small fw-medium" style="color: var(--primary-color);">Date Used</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaint->spares as $spare)
                                <tr>
                                    <td class="px-3 py-2">{{ $spare->name }}</td>
                                    <td class="px-3 py-2 text-end">{{ $spare->pivot->quantity }}</td>
                                    <td class="px-3 py-2">{{ $spare->pivot->used_at ? \Carbon\Carbon::parse($spare->pivot->used_at)->format('d M Y') : '—' }}</td>
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
</x-app-layout>
