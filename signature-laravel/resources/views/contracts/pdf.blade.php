<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Contract - {{ $contract->contract_number }}</title>
    <style>
        @page {
            margin-top: 3mm;
            margin-bottom: 3mm;
            margin-left: 3mm;
            margin-right: 3mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        .firm-logo {
            /* max-width: 200px;
            max-height: 80px; */
            margin: 0 auto 10px;
            display: block;
        }
        .company-name {
            font-size: 14px;
            color: #000;
        }
        .company-address {
            font-size: 12px;
            color: #212121;
            margin-bottom: 0;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #dc2626;
            margin-top: 0;
        }
        .customer-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        .customer-info td {
            padding: 8px;
            border: 1px solid #5e5e5e;
        }
        .customer-info td:nth-child(odd) {
            font-weight: bold;
            background-color: #dbd9d6;
            width: 20%;
        }
        .customer-info td:nth-child(even) {
            background-color: #ffffff;
            width: 30%;
        }
        .machine-section {
            margin-top: 0;
            page-break-inside: avoid;
        }
        .machine-table {
            width: 100%;
            border-collapse: collapse;
        }
        .machine-table td {
            padding: 6px 8px;
            border: 1px solid #5e5e5e;
        }
        .machine-table td:nth-child(odd) {
            font-weight: bold;
            background-color: #dbd9d6;
            width: 20%;
        }
        .machine-table td:nth-child(even) {
            background-color: #ffffff;
            width: 30%;
        }
        .total-section {
            margin-top: 5px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
        .rupee-symbol {
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        .other-details-section {
            margin-top: 5px;
            margin-bottom: 10px;
            page-break-inside: auto;
        }
         .pdf-section-title-wrap {
            text-align: center;
            margin: 5px 0;
        }
        .pdf-section-title {
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #dc2626;
            background-color: #dc262615;
            padding: 4px 8px;
            margin: 0;
        }
        .other-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .other-details-table td {
            padding: 6px 8px;
            border: 1px solid #5e5e5e;
            font-size: 12px;
        }
        .other-details-table td:nth-child(odd) {
            font-weight: bold;
            background-color: #dbd9d6;
            width: 30%;
        }
        .other-details-table td:nth-child(even) {
            background-color: #ffffff;
            width: 20%;
        }
        /* Increase width for OTHER BUYER EXPENSES DETAILS table odd columns to ~60% total */
        .expenses-details-table td:nth-child(odd) {
            width: 30%;
        }
        .expenses-details-table td:nth-child(even) {
            width: 20%;
        }
        /* Long text blocks (e.g. terms) — same font/size as other detail cells */
        .other-details-body-cell {
            font-size: 12px;
            font-family: Arial, sans-serif;
            vertical-align: top;
            background-color: #ffffff;
        }
        /* Not Included in Offer — 4-column bullet grid (title uses .pdf-section-title-wrap / .pdf-section-title) */
        .not-included-offer-block {
            margin-top: 5px;
            margin-bottom: 5px;
            page-break-inside: avoid;
        }
        .not-included-offer-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            background: #ffffff;
        }
        .not-included-offer-grid td {
            width: 25%;
            border: 1px solid #5e5e5e;
            font-size: 12px;
            vertical-align: middle;
            text-align: center;
            padding: 6px 8px;
            color: #212121;
        }
    </style>
</head>
<body>
    <!-- Header with Firm Logo and Address -->
    <div class="header">
        @if($contract->businessFirm && $contract->businessFirm->logo)
            @php
                $logoPath = storage_path('app/public/' . $contract->businessFirm->logo);
                $logoExists = file_exists($logoPath);
            @endphp
            @if($logoExists)
                <img src="{{ $logoPath }}" alt="{{ $contract->businessFirm->name }}" class="firm-logo">
            @else
                <div class="company-name">{{ $contract->businessFirm->name }}</div>
            @endif
            <div class="company-address">{{ $contract->businessFirm->address ?? 'Signature House, Behind HP Petrol Pump, Bhatar Char Rasta, U.M. Road, Surat-395 007 (Guj.), India' }}</div>
        @elseif($contract->businessFirm)
            <div class="company-name">{{ $contract->businessFirm->name }}</div>
            <div class="company-address">{{ $contract->businessFirm->address ?? 'Signature House, Behind HP Petrol Pump, Bhatar Char Rasta, U.M. Road, Surat-395 007 (Guj.), India' }}</div>
        @else
            <div class="company-address">Signature House, Behind HP Petrol Pump, Bhatar Char Rasta, U.M. Road, Surat-395 007 (Guj.), India</div>
        @endif
    </div>

    <!-- Purchase Contract Title -->
    <div class="title">PURCHASE CONTRACT</div>

    <!-- Customer Information -->
    <table class="customer-info">
        <tr>
            <td>Name</td>
            <td>{{ $contract->buyer_name }}</td>
            <td>Email</td>
            <td>{{ $contract->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Company Name</td>
            <td>{{ $contract->company_name ?? 'N/A' }}</td>
            <td>Mob</td>
            <td>{{ $contract->phone_number }}</td>
        </tr>
    </table>

    <!-- Machine Details -->
    @php
        $grandTotal = 0;
    @endphp
    @foreach($contract->contractMachines as $index => $machine)
        @php
            $machineTotal = $machine->quantity * $machine->amount;
            $grandTotal += $machineTotal;
        @endphp
        <div class="machine-section">
            <div class="pdf-section-title-wrap">
                <span class="pdf-section-title">MACHINE SPECIFICATION
                    @if($machine->machineCategory)
                        &nbsp;— {{ $machine->machineCategory->name }}
                    @endif
                </span>
            </div>
            <table class="machine-table">
                @php
                    $fields = [];
                    if($machine->brand) $fields[] = ['label' => 'Brand', 'value' => $machine->brand->name];
                    if($machine->machineModel) $fields[] = ['label' => 'Model', 'value' => $machine->machineModel->model_no];
                    if($machine->feeder) $fields[] = ['label' => 'Feeder', 'value' => $machine->feeder->feeder];
                    if($machine->color) $fields[] = ['label' => 'Color Selector', 'value' => $machine->color->name];
                    if($machine->machineDropin) $fields[] = ['label' => 'Dropins', 'value' => $machine->machineDropin->name];
                    if($machine->machineBeam) $fields[] = ['label' => 'Beam', 'value' => $machine->machineBeam->name];
                    if(!empty($machine->machine_size_name)) $fields[] = ['label' => 'Machine Size', 'value' => $machine->machine_size_name];
                    if($machine->machineClothRoller) $fields[] = ['label' => 'Cloth Roller', 'value' => $machine->machineClothRoller->name];
                    if($machine->machineHook) $fields[] = ['label' => 'Hooks', 'value' => $machine->machineHook->hook];
                    if($machine->machineERead) $fields[] = ['label' => 'E-Read', 'value' => $machine->machineERead->name];
                    if($machine->machineNozzle) $fields[] = ['label' => 'Nozzle', 'value' => $machine->machineNozzle->nozzle];
                    if($machine->machineSoftware) $fields[] = ['label' => 'Software', 'value' => $machine->machineSoftware->name];
                    if($machine->machineShaft) $fields[] = ['label' => 'Shaft', 'value' => $machine->machineShaft->name];
                    if($machine->machineLever) $fields[] = ['label' => 'Lever', 'value' => $machine->machineLever->name];
                    if($machine->machineChain) $fields[] = ['label' => 'Chain', 'value' => $machine->machineChain->name];
                    if($machine->machineHealdWire) $fields[] = ['label' => 'Heald Wires', 'value' => $machine->machineHealdWire->name];
                @endphp
                
                @for($i = 0; $i < count($fields); $i += 2)
                    @if(isset($fields[$i]))
                        <tr>
                            <td>{{ $fields[$i]['label'] }}</td>
                            <td>{{ $fields[$i]['value'] }}</td>
                            @if(isset($fields[$i + 1]))
                                <td>{{ $fields[$i + 1]['label'] }}</td>
                                <td>{{ $fields[$i + 1]['value'] }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endif
                @endfor
                
                <tr>
                    <td>Quantity</td>
                    <td>{{ $machine->quantity }}</td>
                    <td>Price</td>
                    <td>${{ number_format($machine->amount, 2) }}</td>
                </tr>
                @if($machine->deliveryTerm)
                <tr>
                    <td>Total Price</td>
                    <td>${{ number_format($machineTotal, 2) }}</td>
                    <td>Delivery Terms</td>
                    <td>{{ $machine->deliveryTerm->name }}</td>
                </tr>
                @else
                <tr>
                    <td>Total Price</td>
                    <td colspan="3">${{ number_format($machineTotal, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>
    @endforeach
    
    <!-- Other Buyer Expenses Details (only when contract "In Print" = Show) -->
    @php $expenseFields = $contract->otherBuyerExpensesPdfRows(); @endphp
    @if(count($expenseFields) > 0)
    <div class="other-details-section">
        <div class="pdf-section-title-wrap"><span class="pdf-section-title">OTHER BUYER EXPENSES DETAILS</span></div>
        <table class="other-details-table expenses-details-table">
            @for($i = 0; $i < count($expenseFields); $i += 2)
                @if(isset($expenseFields[$i]))
                    <tr>
                        <td>{{ $expenseFields[$i]['label'] }}</td>
                        @if(isset($expenseFields[$i + 1]))
                            <td>{{ $expenseFields[$i]['value'] }}</td>
                            <td>{{ $expenseFields[$i + 1]['label'] }}</td>
                            <td>{{ $expenseFields[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $expenseFields[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    <!-- Other Details (only when contract "In Print" = Show) -->
    @php $otherDetailFields = $contract->otherDetailsPdfRows(); @endphp
    @if(count($otherDetailFields) > 0)
    <div class="other-details-section">
        <div class="pdf-section-title-wrap"><span class="pdf-section-title">OTHER DETAILS</span></div>
        <table class="other-details-table">
            @for($i = 0; $i < count($otherDetailFields); $i += 2)
                @if(isset($otherDetailFields[$i]))
                    <tr>
                        <td>{{ $otherDetailFields[$i]['label'] }}</td>
                        @if(isset($otherDetailFields[$i + 1]))
                            <td>{{ $otherDetailFields[$i]['value'] }}</td>
                            <td>{{ $otherDetailFields[$i + 1]['label'] }}</td>
                            <td>{{ $otherDetailFields[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $otherDetailFields[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    @php
        $nioPdfLabels = $contract->notIncludedInOfferPdfLabels();
        $nioPdfRows = count($nioPdfLabels) > 0 ? array_chunk($nioPdfLabels, 4) : [];
    @endphp
    @if(count($nioPdfRows) > 0)
    <div class="not-included-offer-block">
        <div class="pdf-section-title-wrap"><span class="pdf-section-title">NOT INCLUDED IN OFFER</span></div>
        <table class="not-included-offer-grid">
            @foreach($nioPdfRows as $row)
                <tr>
                    @for($c = 0; $c < 4; $c++)
                        <td>
                            @isset($row[$c])
                                &bull; {{ $row[$c] }}
                            @endisset
                        </td>
                    @endfor
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- Difference of Specification -->
    @if(count($contract->differenceSpecificationMainPrintRows()) > 0)
    <div class="other-details-section">
        <div class="pdf-section-title-wrap"><span class="pdf-section-title">Difference of Specification (Rapier - Jacquard)</span></div>
        <table class="other-details-table">
            @php
                $differenceFields = $contract->differenceSpecificationMainPrintRows();
            @endphp
            
            @for($i = 0; $i < count($differenceFields); $i += 2)
                @if(isset($differenceFields[$i]))
                    <tr>
                        <td>{{ $differenceFields[$i]['label'] }}</td>
                        @if(isset($differenceFields[$i + 1]))
                            <td>{{ $differenceFields[$i]['value'] }}</td>
                            <td>{{ $differenceFields[$i + 1]['label'] }}</td>
                            <td>{{ $differenceFields[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $differenceFields[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    <!-- Difference of Specification (Airjet) -->
    @if(count($contract->differenceSpecificationExtendedPrintRows()) > 0)
    <div class="other-details-section">
        <div class="pdf-section-title-wrap"><span class="pdf-section-title">Difference of Specification (Airjet)</span></div>
        <table class="other-details-table">
            @php
                $differenceFieldsExt = $contract->differenceSpecificationExtendedPrintRows();
            @endphp
            
            @for($i = 0; $i < count($differenceFieldsExt); $i += 2)
                @if(isset($differenceFieldsExt[$i]))
                    <tr>
                        <td>{{ $differenceFieldsExt[$i]['label'] }}</td>
                        @if(isset($differenceFieldsExt[$i + 1]))
                            <td>{{ $differenceFieldsExt[$i]['value'] }}</td>
                            <td>{{ $differenceFieldsExt[$i + 1]['label'] }}</td>
                            <td>{{ $differenceFieldsExt[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $differenceFieldsExt[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    <!-- Difference of Specification (Waterjet) -->
    @if(count($contract->differenceSpecification3PrintRows()) > 0)
    <div class="other-details-section">
        <div class="pdf-section-title-wrap"><span class="pdf-section-title">Difference of Specification (Waterjet)</span></div>
        <table class="other-details-table">
            @php
                $differenceFields3 = $contract->differenceSpecification3PrintRows();
            @endphp

            @for($i = 0; $i < count($differenceFields3); $i += 2)
                @if(isset($differenceFields3[$i]))
                    <tr>
                        <td>{{ $differenceFields3[$i]['label'] }}</td>
                        @if(isset($differenceFields3[$i + 1]))
                            <td>{{ $differenceFields3[$i]['value'] }}</td>
                            <td>{{ $differenceFields3[$i + 1]['label'] }}</td>
                            <td>{{ $differenceFields3[$i + 1]['value'] }}</td>
                        @else
                            <td colspan="3">{{ $differenceFields3[$i]['value'] }}</td>
                        @endif
                    </tr>
                @endif
            @endfor
        </table>
    </div>
    @endif

    <!-- Terms & conditions (only when contract "In Print" = Show) — same table layout as other sections -->
    @php $termsPdfBlocks = $contract->termsConditionsPdfBlocks(); @endphp
    @if(count($termsPdfBlocks) > 0)
    <div class="other-details-section">
        <div class="pdf-section-title-wrap"><span class="pdf-section-title">TERMS &amp; CONDITIONS</span></div>
        <table class="other-details-table expenses-details-table">
            @foreach($termsPdfBlocks as $block)
                <tr>
                    <td>{{ $block['label'] }}</td>
                    <td colspan="3" class="other-details-body-cell">{!! nl2br(e($block['body'])) !!}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- Total Amount and Token Amount -->
    <div class="total-section" style="margin-top: 10px;">
        <div style="margin-bottom: 10px;">
            <strong>Total Contract Amount: ${{ number_format($contract->total_amount ?? 0, 2) }}</strong>
        </div>
        @if($contract->token_amount)
        <div>
            <strong>Token Amount: <span class="rupee-symbol">&#8377;</span>{{ number_format($contract->token_amount, 2) }}</strong>
        </div>
        @endif
    </div>

    <!-- Signatures Section -->
    <div style="margin-top: 10px; page-break-inside: avoid;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <!-- Creator Signature (User who created the contract) -->
                <td style="width: {{ $contract->approval_status === 'approved' && $contract->approver ? '33.33' : '50' }}%; padding: 10px; vertical-align: top; border-top: 2px solid #5e5e5e;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 10px; font-size: 14px;">Created By</div>
                        @php
                            $creator = $contract->creator;
                            $creatorSignatureImg = '';
                            if ($creator) {
                                // Check if signature exists and is not empty
                                if (!empty($creator->signature)) {
                                    $signaturePath = storage_path('app/public/' . $creator->signature);
                                    // Check if file exists
                                    if (file_exists($signaturePath) && is_readable($signaturePath)) {
                                        try {
                                            $signatureData = file_get_contents($signaturePath);
                                            if ($signatureData !== false && !empty($signatureData)) {
                                                $signatureBase64 = base64_encode($signatureData);
                                                $signatureMime = mime_content_type($signaturePath);
                                                if (!$signatureMime) {
                                                    // Try to detect from extension
                                                    $ext = strtolower(pathinfo($signaturePath, PATHINFO_EXTENSION));
                                                    $signatureMime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : 'image/png'));
                                                }
                                                $creatorSignatureImg = 'data:' . $signatureMime . ';base64,' . $signatureBase64;
                                            }
                                        } catch (\Exception $e) {
                                            // Signature file exists but couldn't be read
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if(!empty($creatorSignatureImg))
                            <img src="{{ $creatorSignatureImg }}" alt="Creator Signature" style="max-height: 100px; max-width: 100%; object-fit: contain; margin-bottom: 10px;">
                        @endif
                        <div style="border-top: 1px solid #5e5e5e; padding-top: 5px; margin-top: 10px; font-size: 12px;">
                            @if($creator)
                                {{ $creator->name }}
                            @else
                                Contract Creator
                            @endif
                        </div>
                    </div>
                </td>
                
                <!-- Approver Signature (User who approved the contract) - Only show if approved -->
                @if($contract->approval_status === 'approved' && $contract->approver)
                <td style="width: 33.33%; padding: 10px; vertical-align: top; border-top: 2px solid #5e5e5e;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 10px; font-size: 14px;">Approved By</div>
                        @php
                            $approverSignatureImg = '';
                            if ($contract->approver && $contract->approver->signature && !empty($contract->approver->signature)) {
                                $approverSignaturePath = storage_path('app/public/' . $contract->approver->signature);
                                if (file_exists($approverSignaturePath) && is_readable($approverSignaturePath)) {
                                    try {
                                        $approverSignatureData = file_get_contents($approverSignaturePath);
                                        if ($approverSignatureData !== false && !empty($approverSignatureData)) {
                                            $approverSignatureBase64 = base64_encode($approverSignatureData);
                                            $approverSignatureMime = mime_content_type($approverSignaturePath);
                                            if (!$approverSignatureMime) {
                                                $ext = strtolower(pathinfo($approverSignaturePath, PATHINFO_EXTENSION));
                                                $approverSignatureMime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : 'image/png'));
                                            }
                                            $approverSignatureImg = 'data:' . $approverSignatureMime . ';base64,' . $approverSignatureBase64;
                                        }
                                    } catch (\Exception $e) {
                                        // Signature file exists but couldn't be read
                                    }
                                }
                            }
                        @endphp
                        @if(!empty($approverSignatureImg))
                            <img src="{{ $approverSignatureImg }}" alt="Approver Signature" style="max-height: 100px; max-width: 100%; object-fit: contain; margin-bottom: 10px;">
                        @endif
                        <div style="border-top: 1px solid #5e5e5e; padding-top: 5px; margin-top: 10px; font-size: 12px;">
                            {{ $contract->approver->name }}
                            @if($contract->approved_at)
                                <br><small style="font-size: 10px;">{{ $contract->approved_at->format('M d, Y') }}</small>
                            @endif
                        </div>
                    </div>
                </td>
                @endif
                
                <!-- Customer Signature -->
                <td style="width: {{ $contract->approval_status === 'approved' && $contract->approver ? '33.33' : '50' }}%; padding: 10px; vertical-align: top; border-top: 2px solid #5e5e5e;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 10px; font-size: 14px;">Customer Signature</div>
                        @if($contract->customer_signature)
                            <img src="{{ $contract->customer_signature }}" alt="Customer Signature" style="max-height: 100px; max-width: 100%; object-fit: contain; margin-bottom: 10px;">
                        @endif
                        <div style="border-top: 1px solid #5e5e5e; padding-top: 5px; margin-top: 10px; font-size: 12px;">
                            {{ $contract->buyer_name }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
