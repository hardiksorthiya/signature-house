<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leads Report</title>
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
    <h1>Leads Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }} | Total: {{ $leads->count() }} leads</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Area</th>
                <th>Status</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $lead)
            <tr>
                <td>{{ $lead->created_at->format('d M Y') }}</td>
                <td>{{ $lead->name }}</td>
                <td>{{ $lead->phone_number }}</td>
                <td>{{ $lead->area->name ?? '—' }}</td>
                <td>{{ $lead->status->name ?? '—' }}</td>
                <td>{{ $lead->creator->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
