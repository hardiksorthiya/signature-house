<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\IAFittingDetail;
use App\Support\MsUnloadingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IAFittingController extends Controller
{
    /**
     * Display IA Fitting index page (list all PIs - with or without IA fitting details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['iaFittingDetails', 'contract.creator', 'creator', 'seller', 'proformaInvoiceMachines.machineCategory'])
            ->orderBy('created_at', 'desc');

        MsUnloadingAssignment::applyVisibleScope($query);

        if ($request->filled('pi_number')) {
            $query->where('proforma_invoice_number', trim($request->pi_number));
        } elseif ($request->filled('contract_number')) {
            $cn = trim($request->contract_number);
            $query->whereHas('contract', function ($sub) use ($cn) {
                $sub->where('contract_number', $cn);
            });
        } elseif ($request->filled('search')) {
            $term = trim($request->search);
            if ($term !== '') {
                $like = '%' . $term . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('proforma_invoice_number', 'like', $like)
                        ->orWhere('buyer_company_name', 'like', $like)
                        ->orWhereHas('creator', function ($u) use ($like) {
                            $u->where('name', 'like', $like);
                        })
                        ->orWhereHas('contract', function ($sub) use ($like) {
                            $sub->where('contract_number', 'like', $like)
                                ->orWhere('buyer_name', 'like', $like)
                                ->orWhere('company_name', 'like', $like)
                                ->orWhereHas('creator', function ($u) use ($like) {
                                    $u->where('name', 'like', $like);
                                });
                        });
                });
            }
        }

        $proformaInvoices = $query->paginate(15)->withQueryString();

        return view('ia-fitting.index', compact('proformaInvoices'));
    }

    /**
     * PI + contract rows for the searchable dropdown (same as Machine Erection / Serial numbers).
     */
    public function unifiedSearchItems(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $like = $q !== '' ? '%' . $q . '%' : null;

        $piQuery = ProformaInvoice::query()
            ->with(['contract.creator', 'creator'])
            ->orderByDesc('created_at');

        MsUnloadingAssignment::applyVisibleScope($piQuery);

        if ($like !== null) {
            $piQuery->where(function ($w) use ($like) {
                $w->where('proforma_invoice_number', 'like', $like)
                    ->orWhere('buyer_company_name', 'like', $like)
                    ->orWhereHas('creator', function ($u) use ($like) {
                        $u->where('name', 'like', $like);
                    })
                    ->orWhereHas('contract', function ($c) use ($like) {
                        $c->where('contract_number', 'like', $like)
                            ->orWhere('buyer_name', 'like', $like)
                            ->orWhere('company_name', 'like', $like)
                            ->orWhereHas('creator', function ($u) use ($like) {
                                $u->where('name', 'like', $like);
                            });
                    });
            });
        }

        $pis = $piQuery->limit(80)->get()->map(function (ProformaInvoice $pi) {
            $contract = $pi->contract;
            $sm = $contract?->creator?->name ?? $pi->creator?->name;

            return [
                'kind' => 'pi',
                'id' => $pi->id,
                'proforma_invoice_number' => $pi->proforma_invoice_number,
                'buyer_company_name' => $pi->buyer_company_name,
                'contract_number' => $contract?->contract_number,
                'buyer_name' => $contract?->buyer_name,
                'company_name' => $contract?->company_name,
                'sales_manager_name' => $sm,
            ];
        });

        $contractQuery = Contract::query()
            ->with('creator')
            ->orderByDesc('updated_at');

        if ($like !== null) {
            $contractQuery->where(function ($w) use ($like) {
                $w->where('contract_number', 'like', $like)
                    ->orWhere('buyer_name', 'like', $like)
                    ->orWhere('company_name', 'like', $like)
                    ->orWhereHas('creator', function ($u) use ($like) {
                        $u->where('name', 'like', $like);
                    });
            });
        }

        $contracts = $contractQuery->limit(50)->get()->map(function (Contract $c) {
            return [
                'kind' => 'contract',
                'id' => $c->id,
                'contract_number' => $c->contract_number,
                'buyer_name' => $c->buyer_name,
                'company_name' => $c->company_name,
                'sales_manager_name' => $c->creator?->name,
            ];
        });

        return response()->json([
            'proforma_invoices' => $pis,
            'contracts' => $contracts,
        ]);
    }

    /**
     * Show the form for creating/editing IA fitting details
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        if (! MsUnloadingAssignment::userCanAccessPi($proformaInvoice)) {
            abort(403, 'You are not assigned to this MS Unloading job.');
        }

        $proformaInvoice->load(['iaFittingDetails.machineCategory', 'serialNumbers']);
        
        // Get all serial numbers that have both serial_number and khata_number
        $allSerialNumbers = $proformaInvoice->serialNumbers()
            ->whereNotNull('serial_number')
            ->whereNotNull('khata_number')
            ->where('serial_number', '!=', '')
            ->where('khata_number', '!=', '')
            ->with('machineCategory')
            ->orderBy('machine_category_id')
            ->orderBy('serial_number')
            ->get();
        
        // Group serial numbers by category for dropdown organization
        $serialNumbersByCategory = $allSerialNumbers->groupBy('machine_category_id');
        
        // Get unique machine categories from serial numbers that have both values
        $machineCategories = $allSerialNumbers->pluck('machineCategory')->unique('id')->filter();

        // Define default details (can be customized)
        $defaultDetails = [
            [
                'name' => 'Running Speed',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'name' => 'Efficiency',
                'type' => 'text',
                'sort_order' => 2,
            ],
            [
                'name' => 'Master',
                'type' => 'radio',
                'sort_order' => 3,
            ],
            [
                'name' => 'Complain',
                'type' => 'textarea',
                'sort_order' => 4,
            ],
        ];

        // Get existing IA fitting details grouped by category, machine number, and detail name
        $existingDetails = $proformaInvoice->iaFittingDetails->groupBy(function ($detail) {
            return $detail->machine_category_id . '_' . $detail->machine_number . '_' . $detail->detail_name;
        });

        return view('ia-fitting.show', compact('proformaInvoice', 'machineCategories', 'defaultDetails', 'existingDetails', 'allSerialNumbers', 'serialNumbersByCategory'));
    }

    /**
     * View IA fitting details in read-only mode
     */
    public function view(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load(['iaFittingDetails.machineCategory', 'serialNumbers']);
        
        // Get all serial numbers that have both serial_number and khata_number
        $allSerialNumbers = $proformaInvoice->serialNumbers()
            ->whereNotNull('serial_number')
            ->whereNotNull('khata_number')
            ->where('serial_number', '!=', '')
            ->where('khata_number', '!=', '')
            ->with('machineCategory')
            ->orderBy('machine_category_id')
            ->orderBy('serial_number')
            ->get();
        
        // Group serial numbers by category
        $serialNumbersByCategory = $allSerialNumbers->groupBy('machine_category_id');
        
        // Get existing IA fitting details grouped by category and machine number
        $existingDetailsByMachine = $proformaInvoice->iaFittingDetails->groupBy(function ($detail) {
            return $detail->machine_category_id . '_' . $detail->machine_number;
        });
        
        // Get existing IA fitting details grouped by category, machine number, and detail name
        $existingDetails = $proformaInvoice->iaFittingDetails->groupBy(function ($detail) {
            return $detail->machine_category_id . '_' . $detail->machine_number . '_' . $detail->detail_name;
        });
        
        // Define default details for display
        $defaultDetails = [
            [
                'name' => 'Running Speed',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'name' => 'Efficiency',
                'type' => 'text',
                'sort_order' => 2,
            ],
            [
                'name' => 'Master',
                'type' => 'radio',
                'sort_order' => 3,
            ],
            [
                'name' => 'Complain',
                'type' => 'textarea',
                'sort_order' => 4,
            ],
        ];

        return view('ia-fitting.view', compact('proformaInvoice', 'defaultDetails', 'existingDetails', 'existingDetailsByMachine', 'allSerialNumbers', 'serialNumbersByCategory'));
    }

    /**
     * Store or update IA fitting details for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        MsUnloadingAssignment::ensureCanAccessPi($proformaInvoice);

        $request->validate([
            'serial_number_id' => 'required|exists:serial_numbers,id',
            'ia_fitting_details' => 'required|array',
            'ia_fitting_details.*.machine_category_id' => 'required|exists:machine_categories,id',
            'ia_fitting_details.*.detail_name' => 'required|string|max:255',
            'ia_fitting_details.*.value' => 'nullable|string',
            'ia_fitting_details.*.value_type' => 'required|in:text,radio,textarea',
            'ia_fitting_details.*.sort_order' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Get the serial number to find machine category and number
            $serialNumber = \App\Models\SerialNumber::findOrFail($request->serial_number_id);
            
            if ($serialNumber->proforma_invoice_id != $proformaInvoice->id) {
                throw new \Exception('Serial number does not belong to this proforma invoice.');
            }

            // Calculate machine number based on serial number position within same category
            $allSerialNumbers = \App\Models\SerialNumber::where('proforma_invoice_id', $proformaInvoice->id)
                ->where('machine_category_id', $serialNumber->machine_category_id)
                ->whereNotNull('serial_number')
                ->whereNotNull('khata_number')
                ->where('serial_number', '!=', '')
                ->where('khata_number', '!=', '')
                ->orderBy('id')
                ->get();
            
            $machineNumberIndex = $allSerialNumbers->search(function($sn) use ($serialNumber) {
                return $sn->id == $serialNumber->id;
            });
            $machineNumber = $machineNumberIndex !== false ? $machineNumberIndex + 1 : 1;

            // Delete existing details for this specific machine
            IAFittingDetail::where('proforma_invoice_id', $proformaInvoice->id)
                ->where('machine_category_id', $serialNumber->machine_category_id)
                ->where('machine_number', $machineNumber)
                ->delete();

            // Save new details
            foreach ($request->ia_fitting_details as $detailIndex => $detailData) {
                if (!empty($detailData['detail_name']) && !empty($detailData['machine_category_id'])) {
                    // Only save if value is provided (not empty)
                    if (!empty($detailData['value']) && trim($detailData['value']) !== '') {
                        IAFittingDetail::create([
                            'proforma_invoice_id' => $proformaInvoice->id,
                            'machine_category_id' => $serialNumber->machine_category_id,
                            'machine_number' => $machineNumber,
                            'detail_name' => $detailData['detail_name'],
                            'value' => trim($detailData['value']),
                            'value_type' => $detailData['value_type'] ?? 'text',
                            'sort_order' => $detailData['sort_order'] ?? ($detailIndex + 1),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('ia-fitting.show', $proformaInvoice)
                ->with('success', 'IA Fitting details saved successfully.')
                ->with('selected_serial_number_id', $request->serial_number_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving IA fitting details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save IA fitting details: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Get PIs by Sales Manager (AJAX)
     */
    public function getPINumbersBySalesManager(Request $request)
    {
        $salesManagerId = $request->get('sales_manager_id');
        
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

    /**
     * Get Customers by Sales Manager (AJAX)
     */
    public function getCustomersBySalesManager(Request $request)
    {
        $salesManagerId = $request->get('sales_manager_id');
        
        $customers = ProformaInvoice::where(function($q) use ($salesManagerId) {
            $q->where('created_by', $salesManagerId)
              ->orWhereHas('contract', function($subQ) use ($salesManagerId) {
                  $subQ->where('created_by', $salesManagerId);
              });
        })
        ->select('buyer_company_name')
        ->distinct()
        ->whereNotNull('buyer_company_name')
        ->orderBy('buyer_company_name')
        ->get()
        ->pluck('buyer_company_name')
        ->unique()
        ->values();

        return response()->json($customers);
    }

    /**
     * Get Contracts by Sales Manager (AJAX) for search dropdown
     */
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
