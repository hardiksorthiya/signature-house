<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Report</title>
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
    <h1>Seller Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }} | Total: {{ $sellerData->count() }} sellers</p>
    <table>
        <thead>
            <tr>
                <th>Seller Company</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Country</th>
                <th>PI Count</th>
                <th>Total Machines</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sellerData as $item)
            @php $s = $item->seller; @endphp
            <tr>
                <td>{{ $s->seller_name ?? '—' }}</td>
                <td>{{ $s->email ?? '—' }}</td>
                <td>{{ $s->mobile ?? '—' }}</td>
                <td>{{ Str::limit($s->address ?? '—', 40) }}</td>
                <td>{{ $s->country->name ?? '—' }}</td>
                <td>{{ $item->pi_count }}</td>
                <td>{{ $item->total_machines }}</td>
                <td>{{ format_amount($item->total_amount, $item->currency) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
