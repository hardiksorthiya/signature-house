<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Complaint;
use App\Models\ComplainType;
use App\Models\Contract;
use App\Models\MachineCategory;
use App\Models\Spare;
use App\Support\ComplaintAreaAssignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    protected function complaintsListRoute(?string $status = null): string
    {
        return $status === 'completed' ? 'complaints.completed' : 'complaints.active';
    }

    protected function buildComplaintListQuery(Request $request): Builder
    {
        $query = Complaint::with(['contract.area', 'contract.city', 'complainType', 'machineCategory', 'creator', 'assignees', 'feedbackBy']);

        ComplaintAreaAssignment::applyVisibleScope($query);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('machine_khata_number', 'like', "%{$term}%")
                    ->orWhere('other_detail', 'like', "%{$term}%")
                    ->orWhereHas('complainType', function ($q) use ($term) {
                        $q->where('name', 'like', "%{$term}%");
                    })
                    ->orWhereHas('contract', function ($q) use ($term) {
                        $q->where('contract_number', 'like', "%{$term}%")
                            ->orWhere('company_name', 'like', "%{$term}%")
                            ->orWhere('buyer_name', 'like', "%{$term}%");
                    });
            });
        }

        if ($request->filled('complain_type_id')) {
            $query->where('complain_type_id', $request->complain_type_id);
        }

        if ($request->filled('area_id')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('area_id', $request->area_id);
            });
        }

        if ($request->filled('machine_category_id')) {
            $query->where('machine_category_id', $request->machine_category_id);
        }

        return $query;
    }

    protected function complaintFilterOptions(): array
    {
        return [
            'areas' => Area::orderBy('name')->get(['id', 'name']),
            'machineCategories' => MachineCategory::orderBy('name')->get(['id', 'name']),
            'complainTypes' => ComplainType::orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
        ];
    }

    protected function applyActiveStatusFilter(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'completed');
        });
    }

    protected function applyCompletedStatusFilter(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Sort complaints ascending by date/time, then by id for stable ordering.
     */
    protected function sortComplaintsAscending(Collection $complaints, string $dateField = 'created_at'): Collection
    {
        return $complaints
            ->sortBy([
                fn (Complaint $complaint) => ($complaint->{$dateField} ?? $complaint->created_at)?->getTimestamp() ?? 0,
                fn (Complaint $complaint) => $complaint->id,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{date: Carbon, key: string, label: string, complaints: Collection}>
     */
    protected function groupComplaintsForLastSevenDays(Collection $complaints, string $dateField = 'created_at'): Collection
    {
        $grouped = collect();

        for ($i = 0; $i <= 6; $i++) {
            $date = now()->startOfDay()->subDays($i);
            $key = $date->toDateString();

            $label = match ($i) {
                0 => 'Today — '.$date->format('d M Y'),
                1 => 'Yesterday — '.$date->format('d M Y'),
                default => $date->format('l, d M Y'),
            };

            $grouped->push([
                'date' => $date,
                'key' => $key,
                'label' => $label,
                'complaints' => $this->sortComplaintsAscending(
                    $complaints->filter(function (Complaint $complaint) use ($key, $dateField) {
                        $value = $complaint->{$dateField};

                        return $value && $value->toDateString() === $key;
                    }),
                    $dateField
                ),
            ]);
        }

        return $grouped
            ->sortByDesc(fn (array $group) => $group['date']->getTimestamp())
            ->values();
    }

    /**
     * @return Collection<int, array{date: Carbon, key: string, label: string, complaints: Collection}>
     */
    protected function groupComplaintsByMonth(Collection $complaints, string $dateField = 'created_at'): Collection
    {
        return $complaints
            ->groupBy(function (Complaint $complaint) use ($dateField) {
                $date = $complaint->{$dateField} ?? $complaint->created_at;

                return $date->format('Y-m');
            })
            ->map(function (Collection $monthComplaints, string $key) use ($dateField) {
                $date = Carbon::createFromFormat('Y-m', $key)->startOfMonth();

                return [
                    'date' => $date,
                    'key' => $key,
                    'label' => $date->format('F Y'),
                    'complaints' => $this->sortComplaintsAscending($monthComplaints, $dateField),
                ];
            })
            ->sortByDesc(fn (array $group) => $group['date']->getTimestamp())
            ->values();
    }

    /**
     * All clients/contracts available when creating or editing a complaint.
     */
    protected function contractQuery()
    {
        return Contract::with(['creator'])->orderBy('contract_number');
    }

    /**
     * All complaints with active / completed tabs, grouped by month.
     */
    public function index(Request $request)
    {
        $this->authorize('view complain');

        $query = $this->buildComplaintListQuery($request);

        $tab = $request->get('tab', 'active');
        if (! in_array($tab, ['active', 'completed'], true)) {
            $tab = 'active';
        }

        if ($tab === 'completed') {
            $this->applyCompletedStatusFilter($query);
            $orderColumn = 'completed_at';
            $groupDateField = 'completed_at';
        } else {
            $this->applyActiveStatusFilter($query);
            $orderColumn = 'created_at';
            $groupDateField = 'created_at';
        }

        $complaints = $query->orderBy($orderColumn, 'asc')->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();
        $complaintsByMonth = $this->groupComplaintsByMonth($complaints, $groupDateField);
        $totalCount = $complaints->count();

        return view('complaints.index', array_merge(
            compact('complaintsByMonth', 'tab', 'totalCount'),
            $this->complaintFilterOptions()
        ));
    }

    /**
     * Active complaints from the last 7 days, grouped by date.
     */
    public function active(Request $request)
    {
        $this->authorize('view complain');

        $query = $this->buildComplaintListQuery($request);
        $this->applyActiveStatusFilter($query);

        $from = now()->startOfDay()->subDays(6);
        $complaints = $query
            ->where('created_at', '>=', $from)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $complaintsByDay = $this->groupComplaintsForLastSevenDays($complaints, 'created_at');
        $totalCount = $complaints->count();

        return view('complaints.by-date', array_merge([
            'pageTitle' => 'Active Complain',
            'pageDescription' => 'Active complaints from the last 7 days',
            'listRoute' => 'complaints.active',
            'emptyMessage' => 'No active complaints in the last 7 days.',
            'complaintsByDay' => $complaintsByDay,
            'totalCount' => $totalCount,
        ], $this->complaintFilterOptions()));
    }

    /**
     * Completed complaints from the last 7 days, grouped by date.
     */
    public function completed(Request $request)
    {
        $this->authorize('view complain');

        $query = $this->buildComplaintListQuery($request);
        $this->applyCompletedStatusFilter($query);

        $from = now()->startOfDay()->subDays(6);
        $complaints = $query
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $from)
            ->orderBy('completed_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $complaintsByDay = $this->groupComplaintsForLastSevenDays($complaints, 'completed_at');
        $totalCount = $complaints->count();

        return view('complaints.by-date', array_merge([
            'pageTitle' => 'Completed Complain',
            'pageDescription' => 'Completed complaints from the last 7 days',
            'listRoute' => 'complaints.completed',
            'emptyMessage' => 'No completed complaints in the last 7 days.',
            'complaintsByDay' => $complaintsByDay,
            'totalCount' => $totalCount,
        ], $this->complaintFilterOptions()));
    }

    /**
     * Get machine categories for a contract (for the create form dropdown).
     */
    public function getMachineCategoriesByContract(Request $request)
    {
        $this->authorize('create complain');

        $contractId = $request->get('contract_id');
        if (!$contractId) {
            return response()->json([]);
        }

        $contract = $this->contractQuery()->where('id', $contractId)->first();
        if (!$contract) {
            return response()->json([]);
        }

        $categories = $contract->contractMachines()
            ->with('machineCategory:id,name')
            ->get()
            ->pluck('machineCategory')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
            ->toArray();

        return response()->json($categories);
    }

    /**
     * Show create form with client dropdown.
     */
    public function create(Request $request)
    {
        $this->authorize('create complain');

        $contracts = $this->contractQuery()->get(['id', 'contract_number', 'company_name', 'buyer_name']);
        $complainTypes = ComplainType::orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        $preselectedContractId = $request->get('contract_id');
        return view('complaints.create', compact('contracts', 'complainTypes', 'preselectedContractId'));
    }

    /**
     * Store a new complaint.
     */
    public function store(Request $request)
    {
        $this->authorize('create complain');

        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'complain_type_id' => 'required|exists:complain_types,id',
            'machine_category_id' => 'nullable|exists:machine_categories,id',
            'machine_khata_number' => 'nullable|string|max:255',
            'other_detail' => 'nullable|string',
        ]);

        Complaint::create([
            'contract_id' => $request->contract_id,
            'complain_type_id' => $request->complain_type_id,
            'machine_category_id' => $request->machine_category_id ?: null,
            'machine_khata_number' => $request->machine_khata_number,
            'other_detail' => $request->other_detail,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route($this->complaintsListRoute())->with('success', 'Complaint created successfully.');
    }

    /**
     * Show the complaint (read-only view).
     */
    public function show(Complaint $complaint)
    {
        $this->authorize('view complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        $complaint->load(['contract.state', 'contract.city', 'contract.area', 'complainType', 'machineCategory', 'creator', 'assignees', 'spares']);
        return view('complaints.show', compact('complaint'));
    }

    /**
     * Show edit form.
     */
    public function edit(Complaint $complaint)
    {
        $this->authorize('edit complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        if (! ComplaintAreaAssignment::userCanActOnComplaint($complaint)) {
            abort(403, 'You are not assigned to this complaint.');
        }
        $contracts = $this->contractQuery()->get(['id', 'contract_number', 'company_name', 'buyer_name']);
        $complainTypes = ComplainType::orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        return view('complaints.edit', compact('complaint', 'contracts', 'complainTypes'));
    }

    /**
     * Update the complaint.
     */
    public function update(Request $request, Complaint $complaint)
    {
        $this->authorize('edit complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        if (! ComplaintAreaAssignment::userCanActOnComplaint($complaint)) {
            abort(403, 'You are not assigned to this complaint.');
        }

        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'complain_type_id' => 'required|exists:complain_types,id',
            'machine_category_id' => 'nullable|exists:machine_categories,id',
            'machine_khata_number' => 'nullable|string|max:255',
            'other_detail' => 'nullable|string',
        ]);

        $complaint->update([
            'contract_id' => $request->contract_id,
            'complain_type_id' => $request->complain_type_id,
            'machine_category_id' => $request->machine_category_id ?: null,
            'machine_khata_number' => $request->machine_khata_number,
            'other_detail' => $request->other_detail,
        ]);

        return redirect()->route($this->complaintsListRoute($complaint->status))->with('success', 'Complaint updated successfully.');
    }

    /**
     * Delete the complaint.
     */
    public function destroy(Request $request, Complaint $complaint)
    {
        $this->authorize('delete complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        if (! ComplaintAreaAssignment::userCanActOnComplaint($complaint)) {
            abort(403, 'You are not assigned to this complaint.');
        }
        $route = $request->get('from') === 'feedback'
            ? 'complaints.feedback'
            : $this->complaintsListRoute($complaint->status);
        $complaint->delete();

        return redirect()->route($route)->with('success', 'Complaint deleted successfully.');
    }

    /**
     * Show assign form.
     */
    public function assign(Complaint $complaint)
    {
        $this->authorize('edit complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        if (! ComplaintAreaAssignment::userCanAssignComplaint($complaint)) {
            abort(403, 'You cannot assign this complaint.');
        }
        $users = \App\Models\User::where('is_active', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ComplaintAreaAssignment::assignableRoleNames()))
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('complaints.assign', compact('complaint', 'users'));
    }

    /**
     * Update assignment.
     */
    public function assignUpdate(Request $request, Complaint $complaint)
    {
        $this->authorize('edit complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        if (! ComplaintAreaAssignment::userCanAssignComplaint($complaint)) {
            abort(403, 'You cannot assign this complaint.');
        }

        $request->validate([
            'assigned_to_ids' => 'nullable|array',
            'assigned_to_ids.*' => 'exists:users,id',
        ]);

        $ids = array_filter($request->input('assigned_to_ids', []));
        $wasAssigned = $complaint->assignees()->exists();

        $complaint->assignees()->sync($ids);

        if (! empty($ids) && ! $wasAssigned && ! $complaint->assigned_at) {
            $complaint->update(['assigned_at' => now()]);
        }

        $redirect = $request->get('redirect');
        if ($redirect === 'show') {
            return redirect()->route('complaints.show', $complaint)->with('success', 'Complaint assignment updated.');
        }
        return redirect()->route($this->complaintsListRoute($complaint->status))->with('success', 'Complaint assignment updated.');
    }

    /**
     * Show complaint status update page.
     */
    public function status(Complaint $complaint)
    {
        $this->authorize('edit complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        if (! ComplaintAreaAssignment::userCanActOnComplaint($complaint)) {
            abort(403, 'You are not assigned to this complaint.');
        }
        $complaint->load('spares');
        $spares = Spare::orderBy('name')->get(['id', 'name', 'quantity']);
        return view('complaints.status', compact('complaint', 'spares'));
    }

    /**
     * Update complaint status and optional spare parts. Deducts used quantities from spare stock.
     */
    public function statusUpdate(Request $request, Complaint $complaint)
    {
        $this->authorize('edit complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);
        if (! ComplaintAreaAssignment::userCanActOnComplaint($complaint)) {
            abort(403, 'You are not assigned to this complaint.');
        }

        $request->validate([
            // Existing UI sends `status` with values: on_going, completed.
            'status' => 'nullable|in:on_going,completed',
            // Your new JSON sends `complaintstatus` with values: IN_PROGRESS, RESOLVED.
            'complaintstatus' => 'nullable|in:IN_PROGRESS,RESOLVED',
            'remarks' => 'nullable|string|regex:/^[A-Za-z0-9 ,\\-]{2,25}$/|max:255',
            'spares' => 'nullable|array',
            'spares.*.spare_id' => 'required_with:spares|exists:spares,id',
            'spares.*.quantity' => 'required_with:spares|integer|min:1',
        ]);

        $status = $request->input('status');
        if (!$status) {
            $incoming = $request->input('complaintstatus');
            if ($incoming === 'IN_PROGRESS') {
                $status = 'on_going';
            } elseif ($incoming === 'RESOLVED') {
                $status = 'completed';
            }
        }

        if (!$status) {
            return redirect()->back()->withInput()->withErrors([
                'status' => 'Please select complaint status.',
            ]);
        }

        // If your JSON workflow sends `complaintstatus`, ensure `remarks` is present too.
        if ($request->filled('complaintstatus') && trim((string) $request->input('remarks')) === '') {
            return redirect()->back()->withInput()->withErrors([
                'remarks' => 'Please enter remarks.',
            ]);
        }

        $updateData = [
            'status' => $status,
            'remarks' => $request->input('remarks'),
        ];

        if ($status === 'completed' && ($complaint->status ?? 'on_going') !== 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($status === 'on_going') {
            $updateData['completed_at'] = null;
        }

        $complaint->update($updateData);

        $complaint->load('spares');
        $oldSpares = $complaint->spares->keyBy('id');

        $sync = [];
        if ($request->filled('spares')) {
            foreach ($request->spares as $row) {
                if (empty($row['spare_id']) || (int) ($row['quantity'] ?? 0) < 1) {
                    continue;
                }
                $spareId = (int) $row['spare_id'];
                $qty = (int) $row['quantity'];
                $spare = Spare::find($spareId);
                if (!$spare) {
                    continue;
                }
                $oldQty = $oldSpares->has($spareId) ? (int) $oldSpares->get($spareId)->pivot->quantity : 0;
                $netChange = $qty - $oldQty;
                if ($spare->quantity + $oldQty - $qty < 0) {
                    return redirect()->back()->withInput()->withErrors(['spares' => 'Insufficient stock for spare "' . $spare->name . '". Available: ' . ($spare->quantity + $oldQty) . ', requested: ' . $qty . '.']);
                }
                $sync[$spareId] = ['quantity' => $qty, 'used_at' => now()];
            }
        }

        DB::transaction(function () use ($complaint, $oldSpares, $sync) {
            foreach ($oldSpares as $spareId => $spare) {
                $oldQty = (int) $spare->pivot->quantity;
                if ($oldQty > 0) {
                    Spare::where('id', $spareId)->increment('quantity', $oldQty);
                }
            }
            $complaint->spares()->sync($sync);
            foreach ($sync as $spareId => $data) {
                $qty = (int) $data['quantity'];
                if ($qty > 0) {
                    Spare::where('id', $spareId)->decrement('quantity', $qty);
                }
            }
        });

        return redirect()->route($this->complaintsListRoute($status))->with('success', 'Complaint status updated successfully. Spare stock updated.');
    }

    /**
     * Completed complaints grouped by month — for client feedback.
     */
    public function feedback(Request $request)
    {
        $this->authorize('view complain');

        $query = $this->buildComplaintListQuery($request);
        $this->applyCompletedStatusFilter($query);

        $complaints = $query
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $complaintsByMonth = $this->groupComplaintsByMonth($complaints, 'completed_at');
        $totalCount = $complaints->count();
        $feedbackStatuses = config('complaint-feedback.statuses');

        return view('complaints.feedback.index', array_merge(
            compact('complaintsByMonth', 'totalCount', 'feedbackStatuses'),
            $this->complaintFilterOptions()
        ));
    }

    public function feedbackForm(Complaint $complaint)
    {
        $this->authorize('view complain');
        ComplaintAreaAssignment::ensureCanViewComplaint($complaint);

        if (($complaint->status ?? '') !== 'completed') {
            abort(404);
        }

        $complaint->load(['contract', 'complainType', 'feedbackBy']);
        $feedbackStatuses = config('complaint-feedback.statuses');
        $canEditFeedback = auth()->user()->can('edit complain');

        return view('complaints.feedback.form', compact('complaint', 'feedbackStatuses', 'canEditFeedback'));
    }

    public function feedbackUpdate(Request $request, Complaint $complaint)
    {
        $this->authorize('edit complain');

        if (($complaint->status ?? '') !== 'completed') {
            abort(404);
        }

        $statusKeys = array_keys(config('complaint-feedback.statuses'));

        $validated = $request->validate([
            'feedback_status' => ['required', 'string', 'in:'.implode(',', $statusKeys)],
            'feedback_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $complaint->update([
            'feedback_status' => $validated['feedback_status'],
            'feedback_remarks' => $validated['feedback_remarks'] ?? null,
            'feedback_at' => now(),
            'feedback_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('complaints.feedback', $request->only(['search', 'area_id', 'machine_category_id', 'complain_type_id']))
            ->with('success', 'Feedback saved successfully.');
    }

}
