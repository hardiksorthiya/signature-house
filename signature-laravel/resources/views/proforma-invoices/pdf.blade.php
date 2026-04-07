<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ $proformaInvoice->proforma_invoice_number }}</title>
    <style>
        @page {
            margin-top: 10mm;
            margin-bottom: 5px;
            margin-left: 15mm;
            margin-right: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .company-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 20px 0;
            color: #000;
        }
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .left-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }
        .right-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 15px;
        }
        .section-label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .section-content {
            margin-bottom: 10px;
            font-size: 11px;
        }
        .goods-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10px;
        }
        .goods-table th,
        .goods-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
        }
        .goods-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .goods-table .col-no {
            width: 5%;
            text-align: center;
        }
        .goods-table .col-description {
            width: 50%;
        }
        .goods-table .col-qty {
            width: 10%;
            text-align: center;
        }
        .goods-table .col-price {
            width: 15%;
            text-align: right;
        }
        .goods-table .col-amount {
            width: 20%;
            text-align: right;
        }
        .sub-header {
            font-size: 9px;
            font-weight: normal;
            display: block;
        }
        .total-row {
            font-weight: bold;
            text-align: right;
        }
        .total-words {
            margin-top: 10px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        .banking-section {
            margin-top: 25px;
            font-size: 11px;
        }
        .banking-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        .banking-info {
            margin-bottom: 12px;
        }
        .banking-label {
            font-weight: bold;
            display: inline-block;
            min-width: 180px;
        }
        .banking-value {
            display: inline;
        }
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            margin-top: 50px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto;
            padding-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Company Header -->
    <div class="company-header">
        @if(!empty($sellerLogoDataUri))
            <img src="{{ $sellerLogoDataUri }}" alt="Seller Logo" style="max-width: 280px; max-height: 90px; margin-bottom: 8px;" />
        @endif
        <div class="company-name">{{ $proformaInvoice->seller->seller_name ?? 'SELLER NAME' }}</div>
    </div>

    <!-- Proforma Invoice Title -->
    <div class="invoice-title">PROFORMA INVOICE</div>

    <!-- Two Column Layout -->
    <div class="two-column">
        <!-- Left Column: TO Section -->
        <div class="left-column">
            <div class="section-label">TO: {{ $proformaInvoice->buyer_company_name }}</div>
            <div class="section-content">
                @if($proformaInvoice->billing_address)
                    {{ $proformaInvoice->billing_address }}
                @elseif($proformaInvoice->shipping_address)
                    {{ $proformaInvoice->shipping_address }}
                @else
                    {{ $proformaInvoice->contract->contact_address ?? 'Address not provided' }}
                @endif
            </div>
            
            <div class="section-content" style="margin-top: 10px;">
                <div><strong>Shipped from:</strong> {{ $proformaInvoice->contract->area->name ?? 'Origin' }} to {{ $proformaInvoice->contract->city->name ?? 'Destination' }}</div>
                <div><strong>Packing:</strong> {{ $proformaInvoice->contract->loading_terms ?? 'Standard Packing' }}</div>
            </div>
            
            @if($proformaInvoice->contract->payment_terms)
            <div class="section-content" style="margin-top: 10px;">
                <div><strong>Payment Terms:</strong></div>
                <div>{{ $proformaInvoice->contract->payment_terms }}</div>
            </div>
            @endif
        </div>

        <!-- Right Column: Invoice Details -->
        <div class="right-column">
            <div class="section-content">
                <div><strong>Invoice No.:</strong> {{ $proformaInvoice->proforma_invoice_number }}</div>
                <div><strong>Date:</strong> {{ $proformaInvoice->created_at->format('F d, Y') }}</div>
                @if($proformaInvoice->type_of_sale)
                <div><strong>Type of sale:</strong> {{ ucwords(str_replace('_', ' ', $proformaInvoice->type_of_sale)) }}</div>
                @endif
                @if($proformaInvoice->type_of_sale === 'local' && $proformaInvoice->usd_rate)
                <div><strong>USD / INR rate:</strong> {{ number_format((float) $proformaInvoice->usd_rate, 2) }}</div>
                @endif
                @if($proformaInvoice->contract->quote_validity)
                <div><strong>Validity:</strong> {{ $proformaInvoice->contract->quote_validity }}</div>
                @endif
                <div><strong>Delivery time:</strong> {{ $proformaInvoice->contract->loading_terms ?? 'As per contract' }}</div>
                @if($proformaInvoice->contract->other_details_in_print)
                <div>Partial deliveries allowed</div>
                @endif
                <div><strong>Country of origin:</strong> {{ $proformaInvoice->seller->country->name ?? 'CHINA' }}</div>
                @if($proformaInvoice->proformaInvoiceMachines->first() && $proformaInvoice->proformaInvoiceMachines->first()->contractMachine->hsnCode)
                <div><strong>HSN:</strong> {{ $proformaInvoice->proformaInvoiceMachines->first()->contractMachine->hsnCode->name }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Goods Description Table -->
    <table class="goods-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-description">Quantity and Description of Goods</th>
                <th class="col-qty">Qty.</th>
                <th class="col-price">
                    Unit Price
                    <span class="sub-header">FOB</span>
                </th>
                <th class="col-amount">
                    Amount
                    <span class="sub-header">Indian Port</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $isLocalPdf = ($proformaInvoice->type_of_sale === 'local');
                $pdfLocalMult = $isLocalPdf
                    ? (floatval($proformaInvoice->usd_rate ?? 0) > 0 ? floatval($proformaInvoice->usd_rate) : 1.0)
                    : 1.0;
                $pdfCurrencyPrefix = $isLocalPdf ? '₹' : ($proformaInvoice->currency === 'INR' ? '₹' : 'USD');
                $grandTotalDisplay = 0;
            @endphp
            @foreach($proformaInvoice->proformaInvoiceMachines as $index => $piMachine)
                @php
                    $contractMachine = $piMachine->contractMachine;
                    // Stored amounts are USD; local sale displays INR = USD × rate (same as PI view/edit)
                    $unitUsd = (float) ($piMachine->amount ?? 0);
                    $lineUsd = ($unitUsd * (float) ($piMachine->quantity ?? 0)) + (float) ($piMachine->amc_price ?? 0);
                    $displayUnit = $isLocalPdf ? $unitUsd * $pdfLocalMult : $unitUsd;
                    $displayLine = $isLocalPdf ? $lineUsd * $pdfLocalMult : $lineUsd;
                    $grandTotalDisplay += $displayLine;
                    
                    // Build description
                    $description = [];
                    if($contractMachine->machineCategory) {
                        $description[] = $contractMachine->machineCategory->name;
                    }
                    if($contractMachine->brand) {
                        $description[] = $contractMachine->brand->name;
                    }
                    if($contractMachine->machineModel) {
                        $description[] = 'model ' . $contractMachine->machineModel->model_no;
                    }
                    if($contractMachine->color) {
                        $description[] = 'with ' . $contractMachine->color->name . ' colors';
                    }
                    if($contractMachine->feeder) {
                        $description[] = $contractMachine->feeder->feeder . ' electronic feeders';
                    }
                    if($contractMachine->machineClothRoller) {
                        $description[] = $piMachine->quantity . ' cloth rollers';
                    }
                    if($contractMachine->machineBeam) {
                        $description[] = $piMachine->quantity . ' beams';
                    }
                    if($contractMachine->machineHealdWire) {
                        $description[] = $contractMachine->machineHealdWire->name . ' droppers';
                    }
                    if($contractMachine->machineHook) {
                        $description[] = 'ready for jacquard';
                    }
                    if($contractMachine->machineCategory && strpos(strtolower($contractMachine->machineCategory->name), 'rapier') !== false) {
                        $description[] = 'with standard accessories';
                    }
                    if($contractMachine->wir) {
                        $description[] = '[WIR:' . $contractMachine->wir->name . ']';
                    }
                    if($piMachine->description) {
                        $description[] = $piMachine->description;
                    }
                    $fullDescription = implode(', ', $description);
                @endphp
                <tr>
                    <td class="col-no">{{ $index + 1 }}.</td>
                    <td class="col-description">{{ $fullDescription }}</td>
                    <td class="col-qty">{{ $piMachine->quantity }} SETS</td>
                    <td class="col-price">{{ $pdfCurrencyPrefix }} {{ number_format($displayUnit, 2) }}</td>
                    <td class="col-amount">{{ $pdfCurrencyPrefix }} {{ number_format($displayLine, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right; padding-right: 10px;">
                    Total Value FOB {{ $proformaInvoice->seller->country->name ?? 'Origin' }} to -{{ $proformaInvoice->contract->city->name ?? 'Indian' }} Port:
                </td>
                <td class="col-amount">{{ $pdfCurrencyPrefix }} {{ number_format($grandTotalDisplay, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Total Amount in Words (matches PI list/view: full USD total, × rate for local display) -->
    @php
        if (!$proformaInvoice->relationLoaded('proformaInvoiceMachines')) {
            $proformaInvoice->load('proformaInvoiceMachines');
        }
        $pdfWordsMachineTotal = 0;
        $pdfWordsCommissionTotal = 0;
        foreach ($proformaInvoice->proformaInvoiceMachines as $m) {
            $ua = (float) ($m->amount ?? 0);
            $amc = (float) ($m->amc_price ?? 0);
            $linePi = ($ua * (float) ($m->quantity ?? 0)) + $amc;
            $pdfWordsMachineTotal += $linePi;
            if ($proformaInvoice->type_of_sale === 'high_seas' && $proformaInvoice->commission) {
                $pdfWordsCommissionTotal += ($linePi * (float) $proformaInvoice->commission) / 100;
            }
        }
        $pdfWordsTotalUsd = $pdfWordsMachineTotal + $pdfWordsCommissionTotal
            + (float) ($proformaInvoice->overseas_freight ?? 0)
            + (float) ($proformaInvoice->port_expenses_clearing ?? 0)
            + (float) ($proformaInvoice->gst_amount ?? 0);
        $pdfWordsIsLocal = ($proformaInvoice->type_of_sale === 'local');
        $pdfWordsMult = $pdfWordsIsLocal
            ? (floatval($proformaInvoice->usd_rate ?? 0) > 0 ? floatval($proformaInvoice->usd_rate) : 1.0)
            : 1.0;
        $amountForWords = $pdfWordsIsLocal ? $pdfWordsTotalUsd * $pdfWordsMult : $pdfWordsTotalUsd;

        $number = (int) floor($amountForWords);
        $ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN', 
                 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
        $tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
        
        function convertToWords($num, $ones, $tens) {
            if ($num == 0) return 'ZERO';
            if ($num < 20) return $ones[$num];
            if ($num < 100) {
                $tens_digit = floor($num / 10);
                $ones_digit = $num % 10;
                return $tens[$tens_digit] . ($ones_digit > 0 ? ' ' . $ones[$ones_digit] : '');
            }
            if ($num < 1000) {
                $hundreds = floor($num / 100);
                $remainder = $num % 100;
                return $ones[$hundreds] . ' HUNDRED' . ($remainder > 0 ? ' ' . convertToWords($remainder, $ones, $tens) : '');
            }
            if ($num < 1000000) {
                $thousands = floor($num / 1000);
                $remainder = $num % 1000;
                return convertToWords($thousands, $ones, $tens) . ' THOUSAND' . ($remainder > 0 ? ' ' . convertToWords($remainder, $ones, $tens) : '');
            }
            return 'LARGE NUMBER';
        }
        $amountInWords = convertToWords($number, $ones, $tens);
        $decimalPart = (int) round(($amountForWords - $number) * 100);
        if ($decimalPart > 0) {
            $amountInWords .= ' AND ' . convertToWords($decimalPart, $ones, $tens) . ($pdfWordsIsLocal ? ' PAISE' : ' CENTS');
        }
        $wordsCurrencyLabel = $pdfWordsIsLocal || $proformaInvoice->currency === 'INR' ? 'RUPEES' : 'US DOLLARS';
    @endphp
    <div class="total-words">
        TOTAL AMOUNT: {{ $amountInWords }} {{ $wordsCurrencyLabel }} ONLY
    </div>

    <!-- Banking Information -->
    @if($proformaInvoice->seller->bankDetails->count() > 0)
        @php
            $firstBank = $proformaInvoice->seller->bankDetails->first();
        @endphp
        <div class="banking-section">
            <div class="banking-title">REMITTANCE ROUTE FOR T/T:</div>
            
            @if($firstBank->ifsc_code)
            <div class="banking-info">
                <span class="banking-label">Intermediary Bank:</span>
                <span class="banking-value">{{ $firstBank->bank_name }}</span>
            </div>
            <div class="banking-info">
                <span class="banking-label">SWIFT:</span>
                <span class="banking-value">{{ $firstBank->ifsc_code }}</span>
            </div>
            @endif
            
            <div class="banking-title" style="margin-top: 15px;">BENEFICIARY'S BANK DETAILS:</div>
            <div class="banking-info">
                <span class="banking-label">Beneficiary's Bank's Name:</span>
                <span class="banking-value">{{ $firstBank->bank_name }}</span>
            </div>
            @if($firstBank->bank_address)
            <div class="banking-info">
                <span class="banking-label">Address:</span>
                <span class="banking-value">{{ $firstBank->bank_address }}</span>
            </div>
            @endif
            @if($firstBank->branch_name)
            <div class="banking-info">
                <span class="banking-label">Telephone & Fax:</span>
                <span class="banking-value">{{ $firstBank->branch_name }}</span>
            </div>
            @endif
            @if($firstBank->ifsc_code)
            <div class="banking-info">
                <span class="banking-label">SWIFT:</span>
                <span class="banking-value">{{ $firstBank->ifsc_code }}</span>
            </div>
            @endif
            
            <div class="banking-title" style="margin-top: 15px;">BENEFICIARY'S NAME AND ACCOUNT NO.:</div>
            <div class="banking-info">
                <span class="banking-label">A/C NO:</span>
                <span class="banking-value">{{ $firstBank->account_number }}</span>
            </div>
            <div class="banking-info">
                <span class="banking-label">Name:</span>
                <span class="banking-value">{{ $firstBank->account_holder_name ?? $proformaInvoice->seller->seller_name }}</span>
            </div>
            <div class="banking-info">
                <span class="banking-label">Address:</span>
                <span class="banking-value">{{ $proformaInvoice->seller->address }}</span>
            </div>
            
            @if($proformaInvoice->seller->bankDetails->count() > 1)
            <div class="banking-title" style="margin-top: 15px;">CREDIT OPENING ROUTE (FOR LC):</div>
            @php
                $secondBank = $proformaInvoice->seller->bankDetails->skip(1)->first();
            @endphp
            @if($secondBank)
            <div class="banking-info">
                <span class="banking-label">CORRESPONDENT:</span>
                <span class="banking-value">{{ $secondBank->bank_name }}</span>
            </div>
            <div class="banking-info">
                <span class="banking-label">SWIFT NO.:</span>
                <span class="banking-value">{{ $secondBank->ifsc_code ?? 'N/A' }}</span>
            </div>
            <div class="banking-info">
                <span class="banking-label">IN MT700 :</span>
                <span class="banking-value"></span>
            </div>
            @endif
            @endif
        </div>
    @endif

    <!-- Notes Section -->
    @if($proformaInvoice->notes)
    <div class="banking-section">
        <div class="banking-title">NOTES:</div>
        <div class="section-content" style="white-space: pre-line;">{{ $proformaInvoice->notes }}</div>
    </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">{{ $proformaInvoice->seller->seller_name ?? 'Seller' }}</div>
        </div>
    </div>
</body>
</html>
