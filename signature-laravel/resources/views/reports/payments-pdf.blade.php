<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #666; font-size: 9px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>Payment Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }} | Total: {{ $payments->count() }} payments</p>
    <table>
        <thead>
            <tr>
                <th>Payment Date</th>
                <th>Type</th>
                <th>Contract</th>
                <th>PI Number</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
            <tr>
                <td>{{ $p->payment_date->format('d M Y') }}</td>
                <td>{{ ucfirst($p->type ?? '—') }}</td>
                <td>{{ $p->contract->contract_number ?? ($p->proformaInvoice->contract->contract_number ?? '—') }}</td>
                <td>{{ $p->proformaInvoice->proforma_invoice_number ?? '—' }}</td>
                <td>{{ $p->contract->buyer_name ?? ($p->proformaInvoice->buyer_company_name ?? '—') }}</td>
                <td>{{ ($p->payeeCountry && $p->payeeCountry->currency ? $p->payeeCountry->currency : '₹') . number_format($p->amount, 2) }}</td>
                <td>{{ $p->payment_method ?? '—' }}</td>
                <td>{{ $p->creator->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
