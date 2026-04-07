<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PO Report</title>
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
    <h1>Purchase Orders Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }} | Total: {{ $purchaseOrders->count() }} POs</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>PO Number</th>
                <th>PI Number</th>
                <th>Buyer</th>
                <th>Port</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrders as $po)
            <tr>
                <td>{{ $po->created_at->format('d M Y') }}</td>
                <td>{{ $po->purchase_order_number }}</td>
                <td>{{ $po->proformaInvoice->proforma_invoice_number ?? '—' }}</td>
                <td>{{ $po->buyer_name }}</td>
                <td>{{ $po->portOfDestination->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
