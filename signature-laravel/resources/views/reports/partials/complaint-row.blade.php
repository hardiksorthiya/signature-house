<tr>
    <td class="px-2"><small>{{ $complaint->created_at->format('d M Y') }}</small></td>
    <td class="px-2 fw-medium" style="color: #1f2937;">{{ $contractLabel ?? ($complaint->contract ? ($complaint->contract->company_name ?: $complaint->contract->buyer_name) : '—') }}</td>
    <td class="px-2">{{ $complaint->contract?->contract_number ?? '—' }}</td>
    <td class="px-2">{{ $complaint->contract?->area?->name ?? '—' }}</td>
    <td class="px-2">{{ $complaint->complainType?->name ?? '—' }}</td>
    <td class="px-2">{{ $complaint->machineCategory?->name ?? '—' }}</td>
    <td class="px-2">{{ $complaint->machine_khata_number ?: '—' }}</td>
    <td class="px-2">
        @if(($complaint->status ?? 'on_going') === 'completed')
            <span class="badge bg-success">Completed</span>
        @else
            <span class="badge" style="background-color: color-mix(in srgb, var(--primary-color) 18%, #fff); color: var(--primary-color);">On Going</span>
        @endif
    </td>
    <td class="px-2">{{ $complaint->creator?->name ?? '—' }}</td>
</tr>
