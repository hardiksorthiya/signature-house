<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contracts Report</title>
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
    <h1>Contracts Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }} | Total: {{ $contracts->count() }} contracts</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Contract No</th>
                <th>Buyer / Company</th>
                <th>State</th>
                <th>City</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $c)
            <tr>
                <td>{{ $c->created_at->format('d M Y') }}</td>
                <td>{{ $c->contract_number }}</td>
                <td>{{ $c->company_name ?: $c->buyer_name }}</td>
                <td>{{ $c->state->name ?? '—' }}</td>
                <td>{{ $c->city->name ?? '—' }}</td>
                <td>{{ format_amount($c->total_amount, 'USD') }}</td>
                <td>{{ $c->approval_status ?? '—' }}</td>
                <td>{{ $c->creator->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
