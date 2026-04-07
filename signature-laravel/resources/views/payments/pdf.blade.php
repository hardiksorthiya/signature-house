<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Payment - {{ $payment->type === 'collect' ? 'Collect' : 'Return' }} - {{ $payment->id }}</title>
    <style>
        @page {
            margin-top: 5mm;
            margin-bottom: 5mm;
            margin-left: 10mm;
            margin-right: 10mm;
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
            border-bottom: 2px solid #dc2626;
            padding-bottom: 15px;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #dc2626;
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            color: #333;
            padding-right: 10px;
        }
        .info-value {
            display: table-cell;
            width: 65%;
            color: #000;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #dc2626;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .amount-box {
            background-color: #f3f4f6;
            border: 2px solid #dc2626;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: {{ $payment->type === 'collect' ? '#10b981' : '#dc2626' }};
        }
        .notes-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #10b981;
            color: white;
        }
        .badge-danger {
            background-color: #dc2626;
            color: white;
        }
        .badge-info {
            background-color: #3b82f6;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table td:first-child {
            font-weight: bold;
            background-color: #f9fafb;
            width: 30%;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">PAYMENT {{ strtoupper($payment->type === 'collect' ? 'COLLECT' : 'RETURN') }} RECEIPT</div>
        <div class="subtitle">Payment ID: #{{ $payment->id }} | Date: {{ $payment->payment_date->format('M d, Y') }}</div>
    </div>

    <div class="info-section">
        <div class="section-title">Payment Information</div>
        <div class="info-row">
            <div class="info-label">Payment Type:</div>
            <div class="info-value">
                <span class="badge {{ $payment->type === 'collect' ? 'badge-success' : 'badge-danger' }}">
                    {{ strtoupper($payment->type === 'collect' ? 'Collect Payment' : 'Return Payment') }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Payment Date:</div>
            <div class="info-value">{{ $payment->payment_date->format('F d, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Payment Mode:</div>
            <div class="info-value">
                <span class="badge badge-info">{{ $payment->payment_method ?? 'N/A' }}</span>
            </div>
        </div>
        @if($payment->payment_by)
        <div class="info-row">
            <div class="info-label">Payment By:</div>
            <div class="info-value">{{ $payment->payment_by }}</div>
        </div>
        @endif
    </div>

    <div class="amount-box">
        <div class="amount-label">Payment Amount</div>
        <div class="amount-value">
            {{ $payment->payeeCountry && $payment->payeeCountry->currency ? $payment->payeeCountry->currency : '$' }}{{ number_format($payment->amount, 2) }}
        </div>
    </div>

    <div class="info-section">
        <div class="section-title">Contract/Proforma Invoice Details</div>
        @if($payment->proformaInvoice)
        <div class="info-row">
            <div class="info-label">PI Number:</div>
            <div class="info-value">{{ $payment->proformaInvoice->proforma_invoice_number }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Contract Number:</div>
            <div class="info-value">{{ $payment->contract->contract_number ?? ($payment->proformaInvoice->contract->contract_number ?? 'N/A') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Buyer Name:</div>
            <div class="info-value">
                {{ $payment->contract->buyer_name ?? ($payment->proformaInvoice->buyer_company_name ?? 'N/A') }}
                @if($payment->contract && $payment->contract->company_name)
                    ({{ $payment->contract->company_name }})
                @endif
            </div>
        </div>
    </div>

    @if($payment->payeeCountry || $payment->paymentToSeller || $payment->bankDetail)
    <div class="info-section">
        <div class="section-title">Payment Details</div>
        @if($payment->payeeCountry)
        <div class="info-row">
            <div class="info-label">Payee (Country):</div>
            <div class="info-value">{{ $payment->payeeCountry->name }}</div>
        </div>
        @endif
        @if($payment->paymentToSeller)
        <div class="info-row">
            <div class="info-label">Payment To (Seller):</div>
            <div class="info-value">{{ $payment->paymentToSeller->seller_name }}</div>
        </div>
        @endif
        @if($payment->bankDetail)
        <div class="info-row">
            <div class="info-label">Bank Name:</div>
            <div class="info-value">{{ $payment->bankDetail->bank_name }}</div>
        </div>
        @if($payment->bankDetail->account_number)
        <div class="info-row">
            <div class="info-label">Account Number:</div>
            <div class="info-value">{{ $payment->bankDetail->account_number }}</div>
        </div>
        @endif
        @endif
        @if($payment->transaction_id)
        <div class="info-row">
            <div class="info-label">Transaction ID:</div>
            <div class="info-value">{{ $payment->transaction_id }}</div>
        </div>
        @endif
    </div>
    @endif

    @if($payment->notes)
    <div class="notes-box">
        <div class="section-title" style="margin-top: 0;">Notes</div>
        <div style="color: #333; line-height: 1.6;">{{ $payment->notes }}</div>
    </div>
    @endif

    <div class="info-section">
        <div class="section-title">Created Information</div>
        <div class="info-row">
            <div class="info-label">Created By:</div>
            <div class="info-value">{{ $payment->creator->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Created At:</div>
            <div class="info-value">{{ $payment->created_at->format('F d, Y h:i A') }}</div>
        </div>
    </div>

    <div class="footer">
        <div>This is a computer-generated document. No signature is required.</div>
        <div style="margin-top: 5px;">Generated on: {{ now()->format('F d, Y h:i A') }}</div>
    </div>
</body>
</html>
