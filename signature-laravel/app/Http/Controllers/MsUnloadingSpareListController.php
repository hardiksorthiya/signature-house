<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\PiSpareList;
use App\Models\User;
use App\Support\MsUnloadingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MsUnloadingSpareListController extends Controller
{
    /** Fixed delivery document names (from reference list) */
    public const FIXED_DELIVERY_DOCUMENTS = [
        'Color Selector',
        'Feeders',
        'Feeder Controller',
        'Feeder Tensioners',
        'Feeder Stand',
        'Wastage Bucket',
        'Wastage Cutter',
        'Gripper Base Adjusters',
        'Leno Stand',
        'Leno Device',
        'Pipes (Steel)',
        'Cloth Roll',
        'Droppins',
        'Leno Stand Shaft',
        'Center Leno',
        'Controller',
        'Controller Stand',
        'Nut Bolts (Beam)',
        'Axle Shaft Cover + Support',
        'Emry Roll Cover',
        'Beam Set',
        'Wastage Collector',
        'Center Leno Accessories',
        'Toolbox',
        'Stock Setting',
        'Machine(Loom)',
        'Installed ON LOOM',
        'Gripper Set',
        'Tapes',
        'Main WEFT Cutter',
        'Opener',
        'Temple',
        'Spring',
    ];

    /**
     * Display index page: list PIs with spare list search (Sales Manager, PI, Contract).
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['piSpareLists', 'contract.creator', 'creator', 'seller'])
            ->orderBy('created_at', 'desc');

        MsUnloadingAssignment::applyVisibleScope($query);

        if ($request->filled('sales_manager_id')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('contract', function ($subQ) use ($request) {
                    $subQ->where('created_by', $request->sales_manager_id);
                })
                    ->orWhere('created_by', $request->sales_manager_id);
            });
        }

        if ($request->filled('pi_number')) {
            $query->where('proforma_invoice_number', $request->pi_number);
        }

        if ($request->filled('contract_number')) {
            $query->whereHas('contract', function ($subQ) use ($request) {
                $subQ->where('contract_number', $request->contract_number);
            });
        }

        $proformaInvoices = $query->paginate(15)->withQueryString();

        $salesManagers = User::whereHas('roles', fn($r) => $r->where('name', 'Sales Manager'))
            ->whereDoesntHave('roles', fn($r) => $r->where('name', 'Super Admin'))
            ->select('id', 'name')->orderBy('name')->get();

        return view('ms-unloading-spare-list.index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Show form to add/edit spare list for a PI.
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        if (! MsUnloadingAssignment::userCanAccessPi($proformaInvoice)) {
            abort(403, 'You are not assigned to this MS Unloading job.');
        }

        $proformaInvoice->load(['piSpareLists', 'contract.creator']);

        $fixedWithQuantity = [];
        $sortOrder = 0;
        foreach (self::FIXED_DELIVERY_DOCUMENTS as $name) {
            $existing = $proformaInvoice->piSpareLists
                ->where('document_name', $name)
                ->where('is_custom', false)
                ->first();
            $fixedWithQuantity[] = [
                'document_name' => $name,
                'quantity' => $existing?->quantity ?? '',
                'is_fulfilled' => $existing ? (bool) $existing->is_fulfilled : false,
                'sort_order' => ++$sortOrder,
            ];
        }

        $customRows = $proformaInvoice->piSpareLists
            ->where('is_custom', true)
            ->sortBy('sort_order')
            ->values()
            ->all();

        return view('ms-unloading-spare-list.show', [
            'proformaInvoice' => $proformaInvoice,
            'fixedRows' => $fixedWithQuantity,
            'customRows' => $customRows,
        ]);
    }

    /**
     * Store spare list for a PI (fixed + custom rows).
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        MsUnloadingAssignment::ensureCanAccessPi($proformaInvoice);

        $request->validate([
            'spares' => 'required|array',
            'spares.*.document_name' => 'nullable|string|max:255',
            'spares.*.quantity' => 'nullable|string|max:255',
            'spares.*.is_custom' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            PiSpareList::where('proforma_invoice_id', $proformaInvoice->id)->delete();

            $sortOrder = 0;
            foreach ($request->spares as $row) {
                $name = trim($row['document_name'] ?? '');
                if ($name === '') {
                    continue;
                }
                PiSpareList::create([
                    'proforma_invoice_id' => $proformaInvoice->id,
                    'document_name' => $name,
                    'quantity' => isset($row['quantity']) ? trim((string) $row['quantity']) : null,
                    'is_custom' => !empty($row['is_custom']),
                    'is_fulfilled' => !empty($row['check']),
                    'sort_order' => ++$sortOrder,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('ms-unloading-spare-list.show', $proformaInvoice)
                ->with('success', 'Spare list saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Failed to save spare list: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function getPINumbersBySalesManager(Request $request)
    {
        $salesManagerId = $request->get('sales_manager_id');
        if (!$salesManagerId) {
            return response()->json([]);
        }
        $query = ProformaInvoice::query()->where(function ($q) use ($salesManagerId) {
            $q->where('created_by', $salesManagerId)
                ->orWhereHas('contract', function ($subQ) use ($salesManagerId) {
                    $subQ->where('created_by', $salesManagerId);
                });
        });

        MsUnloadingAssignment::applyVisibleScope($query);

        return response()->json(
            $query->orderBy('proforma_invoice_number')
                ->get(['id', 'proforma_invoice_number', 'buyer_company_name'])
        );
    }

    public function getContractsBySalesManager(Request $request)
    {
        if (!$request->filled('sales_manager_id')) {
            return response()->json([]);
        }
        $contracts = Contract::with(['creator'])
            ->where('created_by', $request->sales_manager_id)
            ->orderBy('contract_number')
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'buyer_name' => $contract->buyer_name,
                    'company_name' => $contract->company_name,
                ];
            });

        return response()->json($contracts);
    }
}
