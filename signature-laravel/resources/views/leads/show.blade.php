<x-app-layout>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">View Lead</h1>
            <p class="text-muted mb-0 small">Lead details and information</p>
        </div>

        <div class="d-flex gap-2 flex-wrap justify-content-end">
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Leads
            </a>

            @can('edit leads')
                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Lead
                </a>
            @endcan

            @can('convert contract')
                @if(!$lead->contract)
                    <a href="{{ route('leads.convert-to-contract', $lead) }}" class="btn btn-success">
                        <i class="fas fa-user-check me-2"></i>Convert to Contract
                    </a>
                @endif
            @endcan
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 lead-panel">
                <div class="card-header border-0 lead-panel-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="lead-panel-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <h2 class="h6 fw-semibold mb-0">Basic Information</h2>
                        <span class="badge ms-auto {{ $lead->type === 'new' ? 'bg-success' : 'bg-info' }}">{{ ucfirst($lead->type) }} Lead</span>
                    </div>
                </div>
                <div class="card-body lead-panel-body">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <div class="lead-info">
                                <div class="lead-label">Name</div>
                                <div class="lead-value">{{ $lead->name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="lead-info">
                                <div class="lead-label">Phone Number</div>
                                <div class="lead-value">{{ $lead->phone_number ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="lead-info">
                                <div class="lead-label">Status</div>
                                <div class="lead-value">
                                    <span class="badge bg-info text-white">{{ $lead->status->name ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 lead-panel">
                <div class="card-header border-0 lead-panel-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="lead-panel-icon lead-panel-icon-secondary">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <h2 class="h6 fw-semibold mb-0">Address Information</h2>
                    </div>
                </div>
                <div class="card-body lead-panel-body">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <div class="lead-info">
                                <div class="lead-label">State</div>
                                <div class="lead-value">{{ $lead->state->name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="lead-info">
                                <div class="lead-label">City</div>
                                <div class="lead-value">{{ $lead->city->name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="lead-info">
                                <div class="lead-label">Area</div>
                                <div class="lead-value">{{ $lead->area->name ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 lead-panel">
                <div class="card-header border-0 lead-panel-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="lead-panel-icon lead-panel-icon-secondary">
                            <i class="fas fa-cogs"></i>
                        </span>
                        <h2 class="h6 fw-semibold mb-0">Business Information</h2>
                    </div>
                </div>
                @if($lead->type === 'new')
                    <div class="card-body lead-panel-body">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <div class="lead-info">
                                    <div class="lead-label">Business</div>
                                    <div class="lead-value">{{ $lead->business->name ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if($lead->type === 'old')
                    <div class="card-body lead-panel-body">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <div class="lead-info">
                                    <div class="lead-label">Brand of Machine</div>
                                    <div class="lead-value">{{ $lead->brand_name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="lead-info">
                                    <div class="lead-label">Machine Quantity</div>
                                    <div class="lead-value">{{ $lead->machine_quantity ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="lead-info">
                                    <div class="lead-label">Running Since</div>
                                    <div class="lead-value">{{ $lead->running_since ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 lead-panel">
                <div class="card-header border-0 lead-panel-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="lead-panel-icon lead-panel-icon-secondary">
                            <i class="fas fa-cogs"></i>
                        </span>
                        <h2 class="h6 fw-semibold mb-0">Machine Information</h2>
                    </div>
                </div>
                <div class="card-body lead-panel-body">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <div class="lead-info">
                                <div class="lead-label">Quantity</div>
                                <div class="lead-value">{{ $lead->quantity ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="lead-info">
                                <div class="lead-label">Machine Categories</div>
                                <div class="lead-value">
                                    @if($lead->machineCategories->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($lead->machineCategories as $category)
                                                <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 12%, #ffffff); color: var(--primary-dark); font-size: 0.875rem; padding: 0.35rem 0.6rem;">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">No categories assigned</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0 lead-panel">
                <div class="card-header border-0 lead-panel-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="lead-panel-icon lead-panel-icon-secondary">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <h2 class="h6 fw-semibold mb-0">Additional Information</h2>
                    </div>
                </div>
                <div class="card-body lead-panel-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="lead-info">
                                <div class="lead-label">Created At</div>
                                <div class="lead-value">{{ $lead->created_at ? $lead->created_at->format('M d, Y h:i A') : '—' }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="lead-info">
                                <div class="lead-label">Updated At</div>
                                <div class="lead-value">{{ $lead->updated_at ? $lead->updated_at->format('M d, Y h:i A') : '—' }}</div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .lead-panel {
            border: 1px solid color-mix(in srgb, var(--primary-color) 12%, #e5e7eb) !important;
            border-radius: 12px;
            overflow: hidden;
        }

        .lead-panel-header {
            padding: 0.75rem 1rem !important;
            background: #fff;
        }

        .lead-panel-body {
            padding: 0.9rem 1rem !important;
        }

        .lead-panel-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .lead-panel-icon-secondary {
            background: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
            color: var(--primary-color);
        }

        .lead-info {
            padding: 0.1rem 0;
        }

        .lead-label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.15rem;
        }

        .lead-value {
            color: #111827;
            font-weight: 500;
            line-height: 1.2;
        }
    </style>
</x-app-layout>

