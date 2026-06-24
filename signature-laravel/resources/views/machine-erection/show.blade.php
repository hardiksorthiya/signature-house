<x-app-layout>
    <div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2">
        <div>
            <h1 class="h2 fw-semibold mb-1" style="color: #1f2937;">Machine Erection Details</h1>
            <p class="text-muted mb-0">PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
            <p class="text-muted mb-0">Customer: {{ $proformaInvoice->buyer_company_name }}</p>
            <p class="text-muted mb-0">Seller: {{ $proformaInvoice->seller->seller_name ?? 'N/A' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('machine-erection.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('proforma-invoices.show', $proformaInvoice) }}" class="btn btn-outline-secondary">
                <i class="fas fa-eye me-2"></i>View PI
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if($machineCategories->count() > 0)
        <form method="POST" action="{{ route('machine-erection.store', $proformaInvoice) }}" id="mainErectionForm">
            @csrf

            @foreach($machineCategories as $machineCategory)
                @php
                    $categoryQuantity = $machineCategoriesWithQuantity->firstWhere('category.id', $machineCategory->id)['quantity'] ?? 0;
                    $categorySummaries = $machineSummariesByCategory->get($machineCategory->id) ?? collect();
                    $categoryDetails = $existingDetails->filter(function ($group) use ($machineCategory) {
                        $first = $group->first();
                        return $first && $first->machine_category_id == $machineCategory->id;
                    });
                    $accordionId = 'erectionAccordion' . $machineCategory->id;
                @endphp

                <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                    <div class="card-header border-0 p-0" style="background: transparent;">
                        <div class="d-flex align-items-center py-3 px-4 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                                <i class="fas fa-cogs text-white"></i>
                            </div>
                            <h2 class="h5 fw-semibold mb-0" style="color: #1f2937;">
                                {{ $machineCategory->name }}
                                <span class="badge bg-secondary ms-2">{{ $categoryQuantity }} Machine{{ $categoryQuantity != 1 ? 's' : '' }}</span>
                            </h2>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4" x-data="{ openPanel: '{{ $accordionId }}Point0' }">
                        <div class="erection-accordion" id="{{ $accordionId }}">
                            {{-- Points to follow --}}
                            @foreach($defaultPointsToFollow as $pointIndex => $pointToFollow)
                                @php $collapseId = $accordionId . 'Point' . $pointIndex; @endphp
                                <div class="erection-accordion-item mb-2">
                                    <h2 class="erection-accordion-header m-0">
                                        <button class="erection-accordion-button w-100 d-flex align-items-center gap-2 text-start border-0"
                                                type="button"
                                                @click="openPanel = openPanel === '{{ $collapseId }}' ? '' : '{{ $collapseId }}'"
                                                :class="{ 'is-open': openPanel === '{{ $collapseId }}' }"
                                                :aria-expanded="openPanel === '{{ $collapseId }}'">
                                            <span class="erection-step-badge">{{ $pointIndex + 1 }}</span>
                                            <span class="flex-grow-1 erection-accordion-title">{{ $pointToFollow }}</span>
                                            <i class="fas fa-chevron-down erection-chevron flex-shrink-0" :class="{ 'rotate-180': openPanel === '{{ $collapseId }}' }"></i>
                                        </button>
                                    </h2>
                                    <div x-show="openPanel === '{{ $collapseId }}'" x-cloak class="erection-accordion-panel">
                                        <div class="erection-panel-inner">
                                            <input type="hidden" name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][point_to_follow]" value="{{ $pointToFollow }}">
                                            <input type="hidden" name="machine_erection_details[{{ $machineCategory->id }}][{{ $pointIndex }}][machine_category_id]" value="{{ $machineCategory->id }}">

                                            <div class="row g-3 erection-cards-row">
                                                @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                                    @php
                                                        $existingDate = null;
                                                        foreach ($categoryDetails as $key => $group) {
                                                            if (str_contains($key, $pointToFollow)) {
                                                                $detail = $group->where('machine_number', $machineNum)->first();
                                                                if ($detail && $detail->date) {
                                                                    $existingDate = $detail->date->format('d-m');
                                                                }
                                                                break;
                                                            }
                                                        }
                                                        $dateVal = old(
                                                            "machine_erection_details.{$machineCategory->id}.{$pointIndex}.machine_dates.{$machineNum}",
                                                            $existingDate ?? ''
                                                        );
                                                    @endphp
                                                    @include('machine-erection._machine-date-card', [
                                                        'machineNum' => $machineNum,
                                                        'primaryLabel' => 'Machine erection date',
                                                        'primaryName' => "machine_erection_details[{$machineCategory->id}][{$pointIndex}][machine_dates][{$machineNum}]",
                                                        'primaryValue' => $dateVal,
                                                    ])
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @php
                                $point34Id = $accordionId . 'Point34';
                                $point35Id = $accordionId . 'Point35';
                            @endphp

                            {{-- Point 34: Machine Erection & Installation Completed --}}
                            <div class="erection-accordion-item mb-2">
                                <h2 class="erection-accordion-header m-0">
                                    <button class="erection-accordion-button w-100 d-flex align-items-center gap-2 text-start border-0"
                                            type="button"
                                            @click="openPanel = openPanel === '{{ $point34Id }}' ? '' : '{{ $point34Id }}'"
                                            :class="{ 'is-open': openPanel === '{{ $point34Id }}' }"
                                            :aria-expanded="openPanel === '{{ $point34Id }}'">
                                        <span class="erection-step-badge">34</span>
                                        <span class="flex-grow-1 erection-accordion-title">{{ $pointMachineErectionLabel }}</span>
                                        <i class="fas fa-chevron-down erection-chevron flex-shrink-0" :class="{ 'rotate-180': openPanel === '{{ $point34Id }}' }"></i>
                                    </button>
                                </h2>
                                <div x-show="openPanel === '{{ $point34Id }}'" x-cloak class="erection-accordion-panel">
                                    <div class="erection-panel-inner">
                                        <div class="row g-3 erection-cards-row">
                                            @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                                @php
                                                    $summary = $categorySummaries->get($machineNum);
                                                    $erectionDate = old(
                                                        "machine_erection_machines.{$machineCategory->id}.{$machineNum}.machine_erection_date",
                                                        $summary && $summary->machine_erection_date ? $summary->machine_erection_date->format('d-m') : ''
                                                    );
                                                    $installationDate = old(
                                                        "machine_erection_machines.{$machineCategory->id}.{$machineNum}.installation_completed_date",
                                                        $summary && $summary->installation_completed_date ? $summary->installation_completed_date->format('d-m') : ''
                                                    );
                                                @endphp
                                                @include('machine-erection._machine-date-card', [
                                                    'machineNum' => $machineNum,
                                                    'primaryLabel' => 'Machine erection date',
                                                    'primaryName' => "machine_erection_machines[{$machineCategory->id}][{$machineNum}][machine_erection_date]",
                                                    'primaryValue' => $erectionDate,
                                                    'secondaryLabel' => 'Installation completed date',
                                                    'secondaryName' => "machine_erection_machines[{$machineCategory->id}][{$machineNum}][installation_completed_date]",
                                                    'secondaryValue' => $installationDate,
                                                ])
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Point 35: Certificate Received --}}
                            <div class="erection-accordion-item mb-2">
                                <h2 class="erection-accordion-header m-0">
                                    <button class="erection-accordion-button w-100 d-flex align-items-center gap-2 text-start border-0"
                                            type="button"
                                            @click="openPanel = openPanel === '{{ $point35Id }}' ? '' : '{{ $point35Id }}'"
                                            :class="{ 'is-open': openPanel === '{{ $point35Id }}' }"
                                            :aria-expanded="openPanel === '{{ $point35Id }}'">
                                        <span class="erection-step-badge">35</span>
                                        <span class="flex-grow-1 erection-accordion-title">{{ $pointCertificateLabel }}</span>
                                        <i class="fas fa-chevron-down erection-chevron flex-shrink-0" :class="{ 'rotate-180': openPanel === '{{ $point35Id }}' }"></i>
                                    </button>
                                </h2>
                                <div x-show="openPanel === '{{ $point35Id }}'" x-cloak class="erection-accordion-panel">
                                    <div class="erection-panel-inner">
                                        <div class="row g-3 erection-cards-row">
                                            @for($machineNum = 1; $machineNum <= $categoryQuantity; $machineNum++)
                                                @php
                                                    $summary = $categorySummaries->get($machineNum);
                                                    $certValue = old(
                                                        "machine_erection_machines.{$machineCategory->id}.{$machineNum}.certificate_received",
                                                        $summary && $summary->certificate_received !== null
                                                            ? ($summary->certificate_received ? 'yes' : 'no')
                                                            : ''
                                                    );
                                                @endphp
                                                @include('machine-erection._machine-certificate-card', [
                                                    'machineNum' => $machineNum,
                                                    'name' => "machine_erection_machines[{$machineCategory->id}][{$machineNum}][certificate_received]",
                                                    'value' => $certValue,
                                                    'idPrefix' => 'cert_' . $machineCategory->id,
                                                ])
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('machine-erection.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save All Machine Erection Details
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                <p class="text-muted">No machine categories found for this Proforma Invoice.</p>
            </div>
        </div>
    @endif

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.date-input').forEach(function(input) {
                flatpickr(input, { dateFormat: 'd-m', allowInput: true });
            });
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }

        .erection-accordion-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .erection-accordion-button {
            padding: 0.85rem 1rem;
            background: #fff;
            color: #1f2937;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .erection-accordion-button:hover {
            background: #f9fafb;
        }

        .erection-accordion-button.is-open {
            background: linear-gradient(to right, color-mix(in srgb, var(--primary-color) 8%, #fff), #fff);
            border-bottom: 1px solid color-mix(in srgb, var(--primary-color) 18%, #e5e7eb);
        }

        .erection-accordion-button:focus {
            outline: none;
            box-shadow: inset 0 0 0 2px color-mix(in srgb, var(--primary-color) 35%, transparent);
        }

        .erection-accordion-title {
            font-size: 0.9rem;
            font-weight: 600;
            line-height: 1.35;
            color: #1f2937;
        }

        .erection-step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
            background: #f3f4f6;
            flex-shrink: 0;
        }

        .erection-chevron {
            font-size: 0.7rem;
            color: #9ca3af;
            transition: transform 0.25s ease, color 0.2s ease;
        }

        .erection-accordion-button.is-open .erection-chevron {
            color: var(--primary-color);
        }

        .rotate-180 { transform: rotate(180deg); }

        .erection-accordion-panel { overflow: visible; }

        .erection-panel-inner {
            padding: 1rem 1rem 1.15rem;
            background: #f8fafc;
            border-top: 1px solid #eef2f7;
        }

        .erection-panel-hint {
            font-size: 0.8rem;
            color: #6b7280;
            margin: 0;
        }

        .erection-machine-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            border-left: 3px solid var(--primary-color);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .erection-machine-card__head {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.7rem 0.9rem;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
        }

        .erection-machine-card__badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.4rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            flex-shrink: 0;
        }

        .erection-machine-card__title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1f2937;
        }

        .erection-machine-card__body {
            padding: 0.85rem 0.9rem 0.95rem;
        }

        .erection-machine-card__label {
            display: block;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
            margin-bottom: 0.35rem;
        }

        .erection-machine-card__date .input-group-text {
            background: #fff;
            border-color: #e5e7eb;
            color: var(--primary-color);
            padding-left: 0.7rem;
            padding-right: 0.7rem;
        }

        .erection-machine-card__date .form-control {
            border-color: #e5e7eb;
            font-size: 0.875rem;
            min-height: 38px;
        }

        .erection-machine-card__date .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.15rem color-mix(in srgb, var(--primary-color) 20%, transparent);
        }

        .erection-radio-group .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .erection-radio-option .form-check-label {
            font-size: 0.875rem;
            color: #374151;
        }

        .erection-cards-row {
            margin-left: 0;
            margin-right: 0;
        }

        @media (max-width: 767.98px) {
            .erection-panel-inner { padding: 0.85rem; }
        }
    </style>
</x-app-layout>
