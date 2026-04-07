<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PI Report</title>
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
    <h1>Proforma Invoices Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }} | Total: {{ $proformaInvoices->count() }} PIs</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>PI Number</th>
                <th>Contract</th>
                <th>Buyer / Company</th>
                <th>Seller</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proformaInvoices as $pi)
            <tr>
                <td>{{ $pi->created_at->format('d M Y') }}</td>
                <td>{{ $pi->proforma_invoice_number }}</td>
                <td>{{ $pi->contract->contract_number ?? '—' }}</td>
                <td>{{ $pi->buyer_company_name ?: ($pi->contract->company_name ?? $pi->contract->buyer_name ?? '—') }}</td>
                <td>{{ $pi->seller->seller_name ?? '—' }}</td>
                <td>{{ format_amount($pi->total_amount, $pi->currency) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
