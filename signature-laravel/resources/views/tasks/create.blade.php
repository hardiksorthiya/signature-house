<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Create New Task</h1>
            <p class="text-muted mb-0">Add a new task or reminder</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label for="title" class="form-label fw-medium" style="color: #374151;">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" required
                               value="{{ old('title') }}"
                               class="form-control @error('title') is-invalid @enderror"
                               placeholder="Enter task title"
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-medium" style="color: #374151;">Description</label>
                        <textarea name="description" id="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter task description (optional)"
                                  style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-medium" style="color: #374151;">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" required
                                class="form-select @error('status') is-invalid @enderror"
                                style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="priority" class="form-label fw-medium" style="color: #374151;">Priority <span class="text-danger">*</span></label>
                        <select name="priority" id="priority" required
                                class="form-select @error('priority') is-invalid @enderror"
                                style="border-radius: 8px; border: 1px solid #e5e7eb;">
                            <option value="0" {{ old('priority', '0') == '0' ? 'selected' : '' }}>Low</option>
                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Medium</option>
                            <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="due_date" class="form-label fw-medium" style="color: #374151;">Due Date</label>
                        <input type="date" name="due_date" id="due_date"
                               value="{{ old('due_date') }}"
                               class="form-control @error('due_date') is-invalid @enderror"
                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Task
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
