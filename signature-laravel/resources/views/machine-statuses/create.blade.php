<x-app-layout>
    @php
        $canEdit = $canEditMachineStatus ?? true;
    @endphp
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Machine Status</h1>
            <p class="text-muted mb-0">
                @if($contract)
                    Contract: {{ $contract->contract_number }}
                @elseif($proformaInvoice)
                    PI: {{ $proformaInvoice->proforma_invoice_number }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
        <a href="{{ route('machine-statuses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to list
                        </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
        <div class="card-header border-0 p-0" style="background: transparent;">
            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                    <i class="fas fa-calendar-check text-white"></i>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">Status Timeline</h2>
                    @if(!$canEdit)
                        <span class="badge bg-secondary">View only</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if($canEdit)
            <form method="POST" action="{{ route('machine-statuses.store') }}">
                @csrf
                @if($contract)
                    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                @endif
                @if($proformaInvoice)
                    <input type="hidden" name="proforma_invoice_id" value="{{ $proformaInvoice->id }}">
                @endif
            @endif

                <!-- Status table (editable or read-only) -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead style="background: linear-gradient(45deg, var(--primary-color), var(--primary-light)); color: white;">
                            <tr>
                                <th style="width: 5%;" class="text-center">{{ $canEdit ? 'Check' : 'Done' }}</th>
                                <th style="width: 30%;">Title</th>
                                <th style="width: 65%;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contract Date -->
                            <tr>
                                <td class="text-center align-middle">
                                    @if($canEdit)
                                    <input type="checkbox"
                                           name="contract_date_completed"
                                           value="1"
                                           {{ $machineStatus && $machineStatus->contract_date_completed ? 'checked' : '' }}
                                           class="form-check-input status-checkbox"
                                           style="width: 20px; height: 20px; cursor: pointer;"
                                           data-status="contract">
                                    @else
                                        @if($machineStatus && $machineStatus->contract_date_completed)
                                            <i class="fas fa-check-circle text-success" title="Complete"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="fw-medium" style="color: #374151;">Contract Date</td>
                                <td>
                                    @if($canEdit)
                                    <input type="date"
                                           name="contract_date"
                                           value="{{ $machineStatus && $machineStatus->contract_date ? $machineStatus->contract_date->format('Y-m-d') : '' }}"
                                           class="form-control status-date"
                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                           data-status="contract">
                                    @else
                                        <span class="text-body">{{ $machineStatus && $machineStatus->contract_date ? $machineStatus->contract_date->format('d-m-Y') : '—' }}</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Proforma Invoice Date -->
                            <tr>
                                <td class="text-center align-middle">
                                    @if($canEdit)
                                    <input type="checkbox"
                                           name="proforma_invoice_completed"
                                           value="1"
                                           {{ $machineStatus && $machineStatus->proforma_invoice_completed ? 'checked' : '' }}
                                           class="form-check-input status-checkbox"
                                           style="width: 20px; height: 20px; cursor: pointer;"
                                           data-status="proforma">
                                    @else
                                        @if($machineStatus && $machineStatus->proforma_invoice_completed)
                                            <i class="fas fa-check-circle text-success" title="Complete"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="fw-medium" style="color: #374151;">Proforma Invoice</td>
                                <td>
                                    @if($canEdit)
                                    <input type="date"
                                           name="proforma_invoice_date"
                                           value="{{ $machineStatus && $machineStatus->proforma_invoice_date ? $machineStatus->proforma_invoice_date->format('Y-m-d') : '' }}"
                                           class="form-control status-date"
                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                           data-status="proforma">
                                    @else
                                        <span class="text-body">{{ $machineStatus && $machineStatus->proforma_invoice_date ? $machineStatus->proforma_invoice_date->format('d-m-Y') : '—' }}</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- China Payment Date -->
                            <tr>
                                <td class="text-center align-middle">
                                    @if($canEdit)
                                    <input type="checkbox"
                                           name="china_payment_completed"
                                           value="1"
                                           {{ $machineStatus && $machineStatus->china_payment_completed ? 'checked' : '' }}
                                           class="form-check-input status-checkbox"
                                           style="width: 20px; height: 20px; cursor: pointer;"
                                           data-status="china">
                                    @else
                                        @if($machineStatus && $machineStatus->china_payment_completed)
                                            <i class="fas fa-check-circle text-success" title="Complete"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="fw-medium" style="color: #374151;">China Payment Date</td>
                                <td>
                                    @if($canEdit)
                                    <input type="date"
                                           name="china_payment_date"
                                           value="{{ $machineStatus && $machineStatus->china_payment_date ? $machineStatus->china_payment_date->format('Y-m-d') : '' }}"
                                           class="form-control status-date"
                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                           data-status="china">
                                    @else
                                        <span class="text-body">{{ $machineStatus && $machineStatus->china_payment_date ? $machineStatus->china_payment_date->format('d-m-Y') : '—' }}</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Actual Date of Dispatch -->
                            <tr>
                                <td class="text-center align-middle">
                                    @if($canEdit)
                                    <input type="checkbox"
                                           name="actual_dispatch_completed"
                                           value="1"
                                           {{ $machineStatus && $machineStatus->actual_dispatch_completed ? 'checked' : '' }}
                                           class="form-check-input status-checkbox"
                                           style="width: 20px; height: 20px; cursor: pointer;"
                                           data-status="dispatch">
                                    @else
                                        @if($machineStatus && $machineStatus->actual_dispatch_completed)
                                            <i class="fas fa-check-circle text-success" title="Complete"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="fw-medium" style="color: #374151;">Actual Date of Dispatch</td>
                                <td>
                                    @if($canEdit)
                                    <input type="date"
                                           name="actual_dispatch_date"
                                           value="{{ $machineStatus && $machineStatus->actual_dispatch_date ? $machineStatus->actual_dispatch_date->format('Y-m-d') : '' }}"
                                           class="form-control status-date"
                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                           data-status="dispatch">
                                    @else
                                        <span class="text-body">{{ $machineStatus && $machineStatus->actual_dispatch_date ? $machineStatus->actual_dispatch_date->format('d-m-Y') : '—' }}</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Expected Arrival Date -->
                            <tr>
                                <td class="text-center align-middle">
                                    @if($canEdit)
                                    <input type="checkbox"
                                           name="expected_arrival_completed"
                                           value="1"
                                           {{ $machineStatus && $machineStatus->expected_arrival_completed ? 'checked' : '' }}
                                           class="form-check-input status-checkbox"
                                           style="width: 20px; height: 20px; cursor: pointer;"
                                           data-status="expected">
                                    @else
                                        @if($machineStatus && $machineStatus->expected_arrival_completed)
                                            <i class="fas fa-check-circle text-success" title="Complete"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="fw-medium" style="color: #374151;">Expected Arrival Date</td>
                                <td>
                                    @if($canEdit)
                                    <input type="date"
                                           name="expected_arrival_date"
                                           value="{{ $machineStatus && $machineStatus->expected_arrival_date ? $machineStatus->expected_arrival_date->format('Y-m-d') : '' }}"
                                           class="form-control status-date"
                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                           data-status="expected">
                                    @else
                                        <span class="text-body">{{ $machineStatus && $machineStatus->expected_arrival_date ? $machineStatus->expected_arrival_date->format('d-m-Y') : '—' }}</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Actual Date of Arrival -->
                            <tr>
                                <td class="text-center align-middle">
                                    @if($canEdit)
                                    <input type="checkbox"
                                           name="actual_arrival_completed"
                                           value="1"
                                           {{ $machineStatus && $machineStatus->actual_arrival_completed ? 'checked' : '' }}
                                           class="form-check-input status-checkbox"
                                           style="width: 20px; height: 20px; cursor: pointer;"
                                           data-status="arrival">
                                    @else
                                        @if($machineStatus && $machineStatus->actual_arrival_completed)
                                            <i class="fas fa-check-circle text-success" title="Complete"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="fw-medium" style="color: #374151;">Actual Date of Arrival</td>
                                <td>
                                    @if($canEdit)
                                    <input type="date"
                                           name="actual_arrival_date"
                                           value="{{ $machineStatus && $machineStatus->actual_arrival_date ? $machineStatus->actual_arrival_date->format('Y-m-d') : '' }}"
                                           class="form-control status-date"
                                           style="border-radius: 6px; border: 1px solid #e5e7eb;"
                                           data-status="arrival">
                                    @else
                                        <span class="text-body">{{ $machineStatus && $machineStatus->actual_arrival_date ? $machineStatus->actual_arrival_date->format('d-m-Y') : '—' }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Timeline Visualization -->
                <div class="border-top pt-4 mt-4">
                    <h5 class="fw-semibold mb-4" style="color: #1f2937;">Status Timeline</h5>
                    <div class="timeline-container">
                        <div class="row g-3">
                            @php
                                $statuses = [
                                    ['key' => 'contract', 'label' => 'Contract Date', 'icon' => 'fa-file-contract', 'date' => $machineStatus && $machineStatus->contract_date ? $machineStatus->contract_date->format('d-m-Y') : null, 'completed' => $machineStatus && $machineStatus->contract_date_completed],
                                    ['key' => 'proforma', 'label' => 'Proforma Invoice', 'icon' => 'fa-file-invoice', 'date' => $machineStatus && $machineStatus->proforma_invoice_date ? $machineStatus->proforma_invoice_date->format('d-m-Y') : null, 'completed' => $machineStatus && $machineStatus->proforma_invoice_completed],
                                    ['key' => 'china', 'label' => 'China Payment Date', 'icon' => 'fa-money-bill-wave', 'date' => $machineStatus && $machineStatus->china_payment_date ? $machineStatus->china_payment_date->format('d-m-Y') : null, 'completed' => $machineStatus && $machineStatus->china_payment_completed],
                                    ['key' => 'dispatch', 'label' => 'Actual Date of Dispatch', 'icon' => 'fa-truck', 'date' => $machineStatus && $machineStatus->actual_dispatch_date ? $machineStatus->actual_dispatch_date->format('d-m-Y') : null, 'completed' => $machineStatus && $machineStatus->actual_dispatch_completed],
                                    ['key' => 'expected', 'label' => 'Expected Arrival Date', 'icon' => 'fa-calendar', 'date' => $machineStatus && $machineStatus->expected_arrival_date ? $machineStatus->expected_arrival_date->format('d-m-Y') : null, 'completed' => $machineStatus && $machineStatus->expected_arrival_completed],
                                    ['key' => 'arrival', 'label' => 'Actual Date of Arrival', 'icon' => 'fa-plane', 'date' => $machineStatus && $machineStatus->actual_arrival_date ? $machineStatus->actual_arrival_date->format('d-m-Y') : null, 'completed' => $machineStatus && $machineStatus->actual_arrival_completed],
                                ];
                            @endphp
                            @foreach($statuses as $index => $status)
                            <div class="col-md-2 text-center">
                                <div class="timeline-step position-relative">
                                    @if($index < count($statuses) - 1)
                                        <div class="timeline-line position-absolute" style="top: 25px; left: 50%; width: 100%; height: 2px; background: {{ $status['completed'] ? '#10b981' : '#e5e7eb' }}; z-index: 0;"></div>
                                    @endif
                                    <div class="timeline-circle position-relative {{ $status['completed'] ? 'completed' : 'pending' }}" 
                                         style="width: 50px; height: 50px; border-radius: 50%; background: {{ $status['completed'] ? '#10b981' : '#ffffff' }}; border: 3px solid {{ $status['completed'] ? '#10b981' : '#e5e7eb' }}; margin: 0 auto; display: flex; align-items: center; justify-content: center; z-index: 1;">
                                        <i class="fas {{ $status['icon'] }} {{ $status['completed'] ? 'text-white' : 'text-gray-400' }}"></i>
                                    </div>
                                    <div class="mt-2">
                                        <div class="fw-medium small" style="color: #374151;">{{ $status['label'] }}</div>
                                        @if($status['date'])
                                            <div class="text-muted small">({{ $status['date'] }})</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2 flex-wrap">
                    @if($canEdit)
                        @if($contract)
                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        @elseif($proformaInvoice)
                            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Submit
                        </button>
                    @else
                        <a href="{{ route('machine-statuses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to list
                        </a>
                    @endif
                </div>
            @if($canEdit)
            </form>
            @endif
        </div>
    </div>

    <style>
        .timeline-step {
            padding: 10px;
        }
        .timeline-circle.completed {
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        }
        .status-checkbox:checked + .status-date,
        .status-checkbox:checked ~ .status-date {
            border-color: #10b981 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update timeline when checkboxes are toggled
            const checkboxes = document.querySelectorAll('.status-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateTimeline();
                });
            });

            function updateTimeline() {
                // Reload page to update timeline visualization
                // Or use JavaScript to update the timeline dynamically
            }
        });
    </script>
</x-app-layout>

