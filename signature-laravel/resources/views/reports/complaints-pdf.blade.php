<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaints Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { color: #666; font-size: 9px; margin-bottom: 12px; }
        h2 { font-size: 12px; margin: 16px 0 6px; }
    </style>
</head>
<body>
    <h1>Complaints Report</h1>
    <p class="meta">Generated on {{ now()->format('d M Y H:i') }}</p>

    @if($view === 'list')
        <p class="meta">Total: {{ $complaints->count() }} records</p>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Contract</th>
                    <th>Area</th>
                    <th>Type</th>
                    <th>Machine</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $c)
                <tr>
                    <td>{{ $c->created_at->format('d M Y') }}</td>
                    <td>{{ $c->contract ? ($c->contract->company_name ?: $c->contract->buyer_name) : '—' }}</td>
                    <td>{{ $c->contract?->contract_number ?? '—' }}</td>
                    <td>{{ $c->contract?->area?->name ?? '—' }}</td>
                    <td>{{ $c->complainType?->name ?? '—' }}</td>
                    <td>{{ $c->machineCategory?->name ?? '—' }}</td>
                    <td>{{ ($c->status ?? 'on_going') === 'completed' ? 'Completed' : 'On Going' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        @foreach($groups ?? [] as $group)
            <h2>{{ $group['label'] }} ({{ $group['count'] }})</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Machine</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['complaints'] as $c)
                    <tr>
                        <td>{{ $c->created_at->format('d M Y') }}</td>
                        <td>{{ $c->contract ? ($c->contract->company_name ?: $c->contract->buyer_name) : '—' }}</td>
                        <td>{{ $c->complainType?->name ?? '—' }}</td>
                        <td>{{ $c->machineCategory?->name ?? '—' }}</td>
                        <td>{{ ($c->status ?? 'on_going') === 'completed' ? 'Completed' : 'On Going' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>
