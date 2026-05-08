<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\PreErectionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreErectionController extends Controller
{
    /**
     * Canonical checklist items (must match the form on the pre-erection detail page).
     *
     * @return list<string>
     */
    public static function technicalSpecificationsList(): array
    {
        return [
            'Space for No. of Machines',
            'Factory Construction Ready',
            'Foundation Ready',
            'Gantry Ready',
            'Floor Cutting Ready',
            'Customer and Signature Whatsapp Group Ready',
            'Availability of Route to Factory Entry',
            'Grease Pump and Air Pressure Blower Ready',
            'Marking for Machines Done',
            'Camera',
        ];
    }

    /**
     * Display pre-erection index page (list all PIs - with or without pre-erection details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['preErectionDetails', 'contract.creator', 'creator', 'seller'])
            ->orderBy('created_at', 'desc');

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

        $preErectionSpecLabels = self::technicalSpecificationsList();

        return view('pre-erection.index', compact('proformaInvoices', 'preErectionSpecLabels'));
    }

    /**
     * PI + contract rows for the searchable dropdown (optional q; matches sales manager names too).
     */
    public function unifiedSearchItems(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $like = $q !== '' ? '%' . $q . '%' : null;

        $piQuery = ProformaInvoice::query()
            ->with(['contract.creator', 'creator'])
            ->orderByDesc('created_at');

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
     * Show the form for creating/editing pre-erection details
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load('preErectionDetails');

        $technicalSpecifications = self::technicalSpecificationsList();

        // Get existing pre-erection details indexed by technical specification
        $existingDetails = $proformaInvoice->preErectionDetails->keyBy('technical_specification');

        return view('pre-erection.show', compact('proformaInvoice', 'technicalSpecifications', 'existingDetails'));
    }

    /**
     * Store or update pre-erection details for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'pre_erection_details' => 'required|array',
            'pre_erection_details.*.technical_specification' => 'required|string|max:255',
            'pre_erection_details.*.details' => 'nullable|string',
            'pre_erection_details.*.is_completed' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // Get existing pre-erection details indexed by technical specification
            $existingDetails = $proformaInvoice->preErectionDetails->keyBy('technical_specification');

            // Update or create pre-erection details
            $sortOrder = 0;
            foreach ($request->pre_erection_details as $index => $detail) {
                if (!empty($detail['technical_specification'])) {
                    $technicalSpecification = $detail['technical_specification'];
                    
                    // Check if this row has any data to save (details or checkbox)
                    $hasDetails = !empty($detail['details']) && trim($detail['details']) !== '';
                    $hasCheckbox = isset($detail['is_completed']);
                    
                    // Only save if there's at least one field filled
                    if ($hasDetails || $hasCheckbox) {
                        // Checkbox: if not set, it means unchecked (false)
                        $isCompleted = isset($detail['is_completed']) && ($detail['is_completed'] == '1' || $detail['is_completed'] === true || $detail['is_completed'] === 'on');
                        
                        // Check if this detail already exists
                        if ($existingDetails->has($technicalSpecification)) {
                            // Update existing detail
                            $existingDetail = $existingDetails->get($technicalSpecification);
                            $existingDetail->update([
                                'details' => $hasDetails ? trim($detail['details']) : $existingDetail->details,
                                'is_completed' => $hasCheckbox ? $isCompleted : $existingDetail->is_completed,
                            ]);
                        } else {
                            // Create new detail
                            PreErectionDetail::create([
                                'proforma_invoice_id' => $proformaInvoice->id,
                                'technical_specification' => $technicalSpecification,
                                'details' => $hasDetails ? trim($detail['details']) : null,
                                'is_completed' => $hasCheckbox ? $isCompleted : false,
                                'sort_order' => $sortOrder++,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('pre-erection.index')
                ->with('success', 'Pre-erection details saved successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving pre-erection details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save Pre Errection details: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Get PIs by Sales Manager (AJAX)
     */
    public function getPINumbersBySalesManager(Request $request)
    {
        $salesManagerId = $request->get('sales_manager_id');
        
        $pis = ProformaInvoice::where(function($q) use ($salesManagerId) {
            $q->where('created_by', $salesManagerId)
              ->orWhereHas('contract', function($subQ) use ($salesManagerId) {
                  $subQ->where('created_by', $salesManagerId);
              });
        })
        ->orderBy('proforma_invoice_number')
        ->get(['id', 'proforma_invoice_number', 'buyer_company_name']);

        return response()->json($pis);
    }

    /**
     * Get Customers by Sales Manager (AJAX) - kept for backward compatibility
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
