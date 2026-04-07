<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplainType;
use App\Models\Contract;
use App\Models\Spare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    /**
     * List complaints. Same visibility as customers: Admin/Super Admin or view customers = all; else own + team.
     */
    protected function contractQuery()
    {
        $query = Contract::with(['creator'])->orderBy('contract_number');
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->where(function ($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())
                    ->orWhereIn('created_by', $teamMemberIds);
            });
        }
        return $query;
    }

    /**
     * Display list of complaints.
     */
    public function index(Request $request)
    {
        $this->authorize('view complain');

        $query = Complaint::with(['contract', 'complainType', 'machineCategory', 'creator', 'assignees']);

        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->where(function ($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())
                    ->orWhereIn('created_by', $teamMemberIds);
            });
        }

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

        $complaints = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('complaints.index', compact('complaints'));
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

        return redirect()->route('complaints.index')->with('success', 'Complaint created successfully.');
    }

    /**
     * Show the complaint (read-only view).
     */
    public function show(Complaint $complaint)
    {
        $this->authorize('view complain');
        $this->authorizeComplaintAccess($complaint);
        $complaint->load(['contract.state', 'contract.city', 'contract.area', 'complainType', 'machineCategory', 'creator', 'assignees', 'spares']);
        return view('complaints.show', compact('complaint'));
    }

    /**
     * Show edit form.
     */
    public function edit(Complaint $complaint)
    {
        $this->authorize('edit complain');
        $this->authorizeComplaintAccess($complaint);
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
        $this->authorizeComplaintAccess($complaint);

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

        return redirect()->route('complaints.index')->with('success', 'Complaint updated successfully.');
    }

    /**
     * Delete the complaint.
     */
    public function destroy(Complaint $complaint)
    {
        $this->authorize('delete complain');
        $this->authorizeComplaintAccess($complaint);
        $complaint->delete();
        return redirect()->route('complaints.index')->with('success', 'Complaint deleted successfully.');
    }

    /**
     * Show assign form.
     */
    public function assign(Complaint $complaint)
    {
        $this->authorize('edit complain');
        $this->authorizeComplaintAccess($complaint);
        $users = \App\Models\User::where('is_active', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Junior Engineer', 'Senior Engineer']))
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
        $this->authorizeComplaintAccess($complaint);

        $request->validate([
            'assigned_to_ids' => 'nullable|array',
            'assigned_to_ids.*' => 'exists:users,id',
        ]);

        $ids = $request->input('assigned_to_ids', []);
        $complaint->assignees()->sync(array_filter($ids));

        $redirect = $request->get('redirect');
        if ($redirect === 'show') {
            return redirect()->route('complaints.show', $complaint)->with('success', 'Complaint assignment updated.');
        }
        return redirect()->route('complaints.index')->with('success', 'Complaint assignment updated.');
    }

    /**
     * Show complaint status update page.
     */
    public function status(Complaint $complaint)
    {
        $this->authorize('edit complain');
        $this->authorizeComplaintAccess($complaint);
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
        $this->authorizeComplaintAccess($complaint);

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

        $complaint->update([
            'status' => $status,
            'remarks' => $request->input('remarks'),
        ]);

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

        return redirect()->route('complaints.index')->with('success', 'Complaint status updated successfully. Spare stock updated.');
    }

    protected function authorizeComplaintAccess(Complaint $complaint): void
    {
        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            return;
        }
        $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
        if ($complaint->created_by !== auth()->id() && !in_array($complaint->created_by, $teamMemberIds)) {
            abort(403);
        }
    }
}
