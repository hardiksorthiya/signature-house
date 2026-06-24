@php
    $feedbackFilterQuery = request()->only(['search', 'area_id', 'machine_category_id', 'complain_type_id']);
    $backUrl = route('complaints.feedback', $feedbackFilterQuery);
    $clientLabel = $complaint->contract?->company_name ?: $complaint->contract?->buyer_name ?: 'Complaint';
@endphp
<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">
                {{ $complaint->feedback_status ? 'Update Feedback' : 'Add Feedback' }}
            </h1>
            <p class="text-muted mb-0">
                Complaint #{{ $complaint->id }} · {{ $clientLabel }}
                @if($complaint->contract?->contract_number)
                    · {{ $complaint->contract->contract_number }}
                @endif
            </p>
        </div>
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 py-3 px-4" style="border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 20%, transparent); background: transparent;">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: color-mix(in srgb, var(--primary-color) 12%, #fff); color: var(--primary-color);">
                    <i class="fas fa-comment-dots small"></i>
                </span>
                <div>
                    <h2 class="h6 fw-semibold mb-0" style="color: #1f2937;">Feedback Details</h2>
                    <p class="small text-muted mb-0">
                        Completed on {{ $complaint->completed_at?->format('d M Y h:i A') ?? '—' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if($canEditFeedback ?? true)
            <form action="{{ route('complaints.feedback-update', $complaint) }}" method="POST">
                @csrf
                @method('PUT')
                @foreach($feedbackFilterQuery as $key => $value)
                    @if($value !== null && $value !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <div class="mb-3">
                    <label for="feedback_status" class="form-label fw-medium small" style="color: #374151;">
                        Feedback Status <span class="text-danger">*</span>
                    </label>
                    <select id="feedback_status"
                            name="feedback_status"
                            class="form-select @error('feedback_status') is-invalid @enderror"
                            required
                            style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        <option value="" disabled {{ old('feedback_status', $complaint->feedback_status) ? '' : 'selected' }}>Select feedback status</option>
                        @foreach($feedbackStatuses as $value => $label)
                            <option value="{{ $value }}" {{ old('feedback_status', $complaint->feedback_status) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('feedback_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="feedback_remarks" class="form-label fw-medium small" style="color: #374151;">Feedback Remarks</label>
                    <textarea id="feedback_remarks"
                              name="feedback_remarks"
                              rows="5"
                              class="form-control @error('feedback_remarks') is-invalid @enderror"
                              placeholder="Enter feedback remarks..."
                              style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('feedback_remarks', $complaint->feedback_remarks) }}</textarea>
                    @error('feedback_remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Feedback
                    </button>
                </div>
            </form>
            @else
            <div class="mb-3">
                <label class="form-label fw-medium small" style="color: #374151;">Feedback Status</label>
                <div class="form-control-plaintext">
                    {{ $feedbackStatuses[$complaint->feedback_status] ?? ($complaint->feedback_status ? ucfirst(str_replace('_', ' ', $complaint->feedback_status)) : 'Pending') }}
                </div>
            </div>
            <div class="mb-0">
                <label class="form-label fw-medium small" style="color: #374151;">Feedback Remarks</label>
                <div class="form-control-plaintext">{{ $complaint->feedback_remarks ?: '—' }}</div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
