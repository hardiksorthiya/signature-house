<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Lead;
use App\Models\ProformaInvoice;
use App\Models\PurchaseOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardChartController extends Controller
{
    public function chartData(Request $request): JsonResponse
    {
        $request->validate([
            'chart' => 'required|in:leads,contracts,pipo,customers,contract_status,snapshot',
            'preset' => 'required|in:7d,this_month,last_month,year,custom',
            'start' => 'nullable|date|required_if:preset,custom',
            'end' => 'nullable|date|required_if:preset,custom|after_or_equal:start',
        ]);

        if ($request->preset === 'custom' && $request->filled('start') && $request->filled('end')) {
            $s = Carbon::parse($request->start)->startOfDay();
            $e = Carbon::parse($request->end)->endOfDay();
            if ($e->lt($s)) {
                return response()->json(['message' => 'End date must be on or after start date.'], 422);
            }
        }

        $user = $request->user();
        $chart = $request->string('chart')->toString();

        $canPi = $user->can('view proforma invoices') || $user->can('create proforma invoices') || $user->can('edit proforma invoices');
        $canPo = $user->can('view proforma invoices') || $user->can('view contract approvals');

        $permissionMap = [
            'leads' => ['view leads'],
            'contracts' => ['convert contract', 'view contract approvals'],
            'pipo' => null,
            'customers' => ['view customers'],
            'contract_status' => ['convert contract', 'view contract approvals'],
            'snapshot' => null,
        ];

        if ($chart === 'pipo') {
            if (! $canPi && ! $canPo) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } elseif ($chart === 'snapshot') {
            if (! $user->can('view leads')
                && ! ($user->can('convert contract') || $user->can('view contract approvals'))
                && ! $user->can('view customers')
                && ! ($user->can('view proforma invoices') || $user->can('create proforma invoices') || $user->can('edit proforma invoices'))
                && ! ($user->can('view proforma invoices') || $user->can('view contract approvals'))) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } else {
            $perms = $permissionMap[$chart] ?? [];
            $allowed = false;
            foreach ($perms as $p) {
                if ($user->can($p)) {
                    $allowed = true;
                    break;
                }
            }
            if (! $allowed) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin']);
        $teamMemberIds = $isAdmin ? [] : User::where('created_by', $user->id)->pluck('id')->toArray();

        [$start, $end, $granularity] = $this->resolveRange($request);

        $buckets = $this->buildBuckets($start, $end, $granularity);

        return match ($chart) {
            'leads' => response()->json($this->timeseriesLeads($user, $isAdmin, $teamMemberIds, $buckets)),
            'contracts' => response()->json($this->timeseriesContracts($user, $isAdmin, $teamMemberIds, $buckets)),
            'pipo' => response()->json($this->timeseriesPipo($user, $isAdmin, $teamMemberIds, $buckets, $canPi, $canPo)),
            'customers' => response()->json($this->timeseriesCustomers($user, $isAdmin, $teamMemberIds, $buckets)),
            'contract_status' => response()->json($this->pieContractStatus($user, $isAdmin, $teamMemberIds, $start, $end)),
            'snapshot' => response()->json($this->doughnutSnapshot($user, $isAdmin, $teamMemberIds, $start, $end)),
            default => response()->json(['message' => 'Invalid chart'], 400),
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolveRange(Request $request): array
    {
        $now = now();

        return match ($request->preset) {
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), 'day'],
            'this_month' => [
                $now->copy()->startOfMonth()->startOfDay(),
                $now->copy()->endOfDay(),
                'day',
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth()->startOfDay(),
                $now->copy()->subMonth()->endOfMonth()->endOfDay(),
                'day',
            ],
            'year' => [$now->copy()->startOfYear()->startOfDay(), $now->copy()->endOfDay(), 'month'],
            'custom' => $this->customRange($request),
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function customRange(Request $request): array
    {
        $start = Carbon::parse($request->input('start'))->startOfDay();
        $end = Carbon::parse($request->input('end'))->endOfDay();
        $days = $start->diffInDays($end) + 1;
        $granularity = $days <= 31 ? 'day' : 'month';

        return [$start, $end, $granularity];
    }

    /**
     * @return list<array{label: string, start: Carbon, end: Carbon}>
     */
    private function buildBuckets(Carbon $rangeStart, Carbon $rangeEnd, string $granularity): array
    {
        $buckets = [];
        if ($granularity === 'day') {
            for ($d = $rangeStart->copy(); $d->lte($rangeEnd); $d->addDay()) {
                $buckets[] = [
                    'label' => $d->format('M j'),
                    'start' => $d->copy()->startOfDay(),
                    'end' => $d->copy()->endOfDay(),
                ];
            }
        } else {
            $cursor = $rangeStart->copy()->startOfMonth();
            while ($cursor->lte($rangeEnd)) {
                $bucketStart = $cursor->copy()->max($rangeStart)->startOfDay();
                $monthEnd = $cursor->copy()->endOfMonth();
                $bucketEnd = $monthEnd->min($rangeEnd)->copy()->endOfDay();
                if ($bucketStart->lte($bucketEnd)) {
                    $buckets[] = [
                        'label' => $cursor->format('M Y'),
                        'start' => $bucketStart,
                        'end' => $bucketEnd,
                    ];
                }
                $cursor->addMonth();
            }
        }

        return $buckets;
    }

    private function scopeTeamContracts($query, $user, bool $isAdmin, array $teamMemberIds): void
    {
        if (! $isAdmin) {
            $query->where(function ($q) use ($user, $teamMemberIds) {
                $q->where('created_by', $user->id)->orWhereIn('created_by', $teamMemberIds);
            });
        }
    }

    private function scopeTeamLeads($query, $user, bool $isAdmin, array $teamMemberIds): void
    {
        $this->scopeTeamContracts($query, $user, $isAdmin, $teamMemberIds);
    }

    private function scopeTeamPi($query, $user, bool $isAdmin, array $teamMemberIds): void
    {
        if (! $isAdmin) {
            $query->whereHas('contract', function ($cq) use ($user, $teamMemberIds) {
                $cq->where(function ($q2) use ($user, $teamMemberIds) {
                    $q2->where('created_by', $user->id)->orWhereIn('created_by', $teamMemberIds);
                });
            });
        }
    }

    private function scopeTeamPo($query, $user, bool $isAdmin, array $teamMemberIds): void
    {
        if (! $isAdmin) {
            $query->whereHas('proformaInvoice.contract', function ($cq) use ($user, $teamMemberIds) {
                $cq->where(function ($q2) use ($user, $teamMemberIds) {
                    $q2->where('created_by', $user->id)->orWhereIn('created_by', $teamMemberIds);
                });
            });
        }
    }

    /**
     * @param  list<array{label: string, start: Carbon, end: Carbon}>  $buckets
     */
    private function timeseriesLeads($user, bool $isAdmin, array $teamMemberIds, array $buckets): array
    {
        $labels = [];
        $values = [];
        foreach ($buckets as $b) {
            $labels[] = $b['label'];
            $q = Lead::query();
            $this->scopeTeamLeads($q, $user, $isAdmin, $teamMemberIds);
            $values[] = (int) $q->whereBetween('created_at', [$b['start'], $b['end']])->count();
        }

        return ['type' => 'timeseries', 'labels' => $labels, 'values' => $values];
    }

    /**
     * @param  list<array{label: string, start: Carbon, end: Carbon}>  $buckets
     */
    private function timeseriesContracts($user, bool $isAdmin, array $teamMemberIds, array $buckets): array
    {
        $labels = [];
        $values = [];
        foreach ($buckets as $b) {
            $labels[] = $b['label'];
            $q = Contract::query();
            $this->scopeTeamContracts($q, $user, $isAdmin, $teamMemberIds);
            $values[] = (int) $q->whereBetween('created_at', [$b['start'], $b['end']])->count();
        }

        return ['type' => 'timeseries', 'labels' => $labels, 'values' => $values];
    }

    /**
     * @param  list<array{label: string, start: Carbon, end: Carbon}>  $buckets
     */
    private function timeseriesPipo($user, bool $isAdmin, array $teamMemberIds, array $buckets, bool $canPi, bool $canPo): array
    {
        $labels = [];
        $pi = [];
        $po = [];
        foreach ($buckets as $b) {
            $labels[] = $b['label'];
            if ($canPi) {
                $q = ProformaInvoice::query();
                $this->scopeTeamPi($q, $user, $isAdmin, $teamMemberIds);
                $pi[] = (int) $q->whereBetween('created_at', [$b['start'], $b['end']])->count();
            }
            if ($canPo) {
                $q = PurchaseOrder::query();
                $this->scopeTeamPo($q, $user, $isAdmin, $teamMemberIds);
                $po[] = (int) $q->whereBetween('created_at', [$b['start'], $b['end']])->count();
            }
        }

        return ['type' => 'timeseries', 'labels' => $labels, 'pi' => $canPi ? $pi : null, 'po' => $canPo ? $po : null];
    }

    /**
     * @param  list<array{label: string, start: Carbon, end: Carbon}>  $buckets
     */
    private function timeseriesCustomers($user, bool $isAdmin, array $teamMemberIds, array $buckets): array
    {
        $labels = [];
        $values = [];
        foreach ($buckets as $b) {
            $labels[] = $b['label'];
            $q = Contract::query()->where('approval_status', 'approved');
            $this->scopeTeamContracts($q, $user, $isAdmin, $teamMemberIds);
            $values[] = (int) $q->whereBetween('created_at', [$b['start'], $b['end']])->count();
        }

        return ['type' => 'timeseries', 'labels' => $labels, 'values' => $values];
    }

    private function pieContractStatus($user, bool $isAdmin, array $teamMemberIds, Carbon $start, Carbon $end): array
    {
        $q = Contract::query();
        $this->scopeTeamContracts($q, $user, $isAdmin, $teamMemberIds);
        $q->whereBetween('created_at', [$start, $end]);
        $rows = $q->select('approval_status', DB::raw('COUNT(*) as c'))
            ->groupBy('approval_status')
            ->orderBy('approval_status')
            ->get();

        return [
            'type' => 'pie',
            'labels' => $rows->map(function ($r) {
                $s = $r->approval_status;

                return $s ? ucfirst(str_replace('_', ' ', (string) $s)) : 'Unknown';
            })->all(),
            'counts' => $rows->pluck('c')->map(fn ($c) => (int) $c)->all(),
        ];
    }

    private function doughnutSnapshot($user, bool $isAdmin, array $teamMemberIds, Carbon $start, Carbon $end): array
    {
        $labels = [];
        $counts = [];

        if ($user->can('view leads')) {
            $q = Lead::query();
            $this->scopeTeamLeads($q, $user, $isAdmin, $teamMemberIds);
            $labels[] = 'Leads';
            $counts[] = (int) $q->whereBetween('created_at', [$start, $end])->count();
        }
        if ($user->can('convert contract') || $user->can('view contract approvals')) {
            $q = Contract::query();
            $this->scopeTeamContracts($q, $user, $isAdmin, $teamMemberIds);
            $labels[] = 'Contracts';
            $counts[] = (int) $q->whereBetween('created_at', [$start, $end])->count();
        }
        if ($user->can('view customers')) {
            $q = Contract::query()->where('approval_status', 'approved');
            $this->scopeTeamContracts($q, $user, $isAdmin, $teamMemberIds);
            $labels[] = 'Customers';
            $counts[] = (int) $q->whereBetween('created_at', [$start, $end])->count();
        }
        if ($user->can('view proforma invoices') || $user->can('create proforma invoices') || $user->can('edit proforma invoices')) {
            $q = ProformaInvoice::query();
            $this->scopeTeamPi($q, $user, $isAdmin, $teamMemberIds);
            $labels[] = 'PI';
            $counts[] = (int) $q->whereBetween('created_at', [$start, $end])->count();
        }
        if ($user->can('view proforma invoices') || $user->can('view contract approvals')) {
            $q = PurchaseOrder::query();
            $this->scopeTeamPo($q, $user, $isAdmin, $teamMemberIds);
            $labels[] = 'PO';
            $counts[] = (int) $q->whereBetween('created_at', [$start, $end])->count();
        }

        return ['type' => 'doughnut', 'labels' => $labels, 'counts' => $counts];
    }
}
