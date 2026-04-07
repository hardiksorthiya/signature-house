<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spare Used Report</title>
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
    <h1>Spare Used Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }} | Total: {{ $usages->count() }} records</p>
    <table>
        <thead>
            <tr>
                <th>Date Used</th>
                <th>Spare Name</th>
                <th>Quantity</th>
                <th>Contract</th>
                <th>Customer</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usages as $u)
            <tr>
                <td>{{ $u->used_at ? \Carbon\Carbon::parse($u->used_at)->format('d M Y') : '—' }}</td>
                <td>{{ $u->spare_name ?? '—' }}</td>
                <td>{{ $u->quantity ?? 0 }}</td>
                <td>{{ $u->contract_number ?? '—' }}</td>
                <td>{{ $u->company_name ?: ($u->buyer_name ?? '—') }}</td>
                <td>{{ $u->created_by_name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
