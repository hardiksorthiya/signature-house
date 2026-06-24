<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\MachineErectionDetail;
use App\Models\MachineErectionMachineSummary;
use App\Support\MsUnloadingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MachineErectionController extends Controller
{
    public const POINT_MACHINE_ERECTION_INSTALLATION = 'Machine Erection & Installation Completed';

    public const POINT_CERTIFICATE_RECEIVED = 'Certificate Received';

    public const CHECKLIST_POINT_COUNT = 33;
    /**
     * Display machine erection index page (list all PIs - with or without machine erection details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['machineErectionDetails', 'contract.creator', 'creator', 'seller', 'proformaInvoiceMachines.machineCategory'])
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

        return view('machine-erection.index', compact('proformaInvoices'));
    }

    /**
     * PI + contract rows for the searchable dropdown (same as Pre Erection / Serial numbers).
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
     * Show the form for creating/editing machine erection details
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        if (! MsUnloadingAssignment::userCanAccessPi($proformaInvoice)) {
            abort(403, 'You are not assigned to this MS Unloading job.');
        }

        $proformaInvoice->load([
            'machineErectionDetails.machineCategory',
            'machineErectionMachineSummaries',
            'proformaInvoiceMachines.machineCategory',
        ]);
        
        // Get unique machine categories from PI machines with quantities
        $machineCategoriesWithQuantity = $proformaInvoice->proformaInvoiceMachines()
            ->with('machineCategory')
            ->get()
            ->groupBy('machine_category_id')
            ->map(function ($machines) {
                $category = $machines->first()->machineCategory;
                $totalQuantity = $machines->sum('quantity');
                return [
                    'category' => $category,
                    'quantity' => $totalQuantity
                ];
            })
            ->filter();
        
        // Separate categories and quantities for easier access in view
        $machineCategories = $machineCategoriesWithQuantity->map(function($item) {
            return $item['category'];
        });

        // Define default points to follow (can be customized)
        $defaultPointsToFollow = [
            // First set of points
            'Loom Placed at Foundation',
            'Delivery List & Damage Check',
            'Liner Level & Packing',
            'U Clamp Fitting',
            'Air Valve-Caps Remove by User Level Person',
            'Salo Bolt Measurement Given',
            'Pullborn',
            'Beam Pipe Fitting',
            'Powder Stand Fitting',
            'Gripper & Tape Remover',
            'Back Rest Bolt Setting',
            'X-Axis Alignment Checking by Gauge',
            'Liner Level of Loom & JC Check',
            'Harness Hanging From JC',
            // Second set of points
            'Harness Hanging Frame JD',
            'Harness Filling From Carderboard',
            'Harness Under Motion Spring Filling',
            'Sate Shaft Filling & Harness Level Zero Check',
            'JD all Long all Boxer Height',
            'Electric Connections From Main Line',
            'Electric Connections Between JD & Loom',
            'Oil Pressure Check by Elec. Engineer',
            'Harness Fixing after Zero Level Check',
            'Empty Running & Harness Final Level Check',
            'Beam Drawing',
            'Reed Drawing',
            'Beam Grilling',
            'Warp Filling & Piecing',
            // Third set of points (some may overlap)
            'Beam Setting',
            'Warp Filling & Pinning',
            'Plain/Cloth Treding & Harness Mistake Check',
            'Final Cloth with Design',
            'Machine in Production @ 370 RPM',
        ];

        // Get existing machine erection details grouped by category and point
        $existingDetails = $proformaInvoice->machineErectionDetails->groupBy(function ($detail) {
            return $detail->machine_category_id . '_' . $detail->point_to_follow;
        });

        $machineSummariesByCategory = $proformaInvoice->machineErectionMachineSummaries
            ->groupBy('machine_category_id')
            ->map(fn ($rows) => $rows->keyBy('machine_number'));

        return view('machine-erection.show', compact(
            'proformaInvoice',
            'machineCategories',
            'machineCategoriesWithQuantity',
            'defaultPointsToFollow',
            'existingDetails',
            'machineSummariesByCategory'
        ))->with([
            'pointMachineErectionLabel' => self::POINT_MACHINE_ERECTION_INSTALLATION,
            'pointCertificateLabel' => self::POINT_CERTIFICATE_RECEIVED,
        ]);
    }

    /**
     * Store or update machine erection details for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        MsUnloadingAssignment::ensureCanAccessPi($proformaInvoice);

        $request->validate([
            'machine_erection_machines' => 'nullable|array',
            'machine_erection_machines.*' => 'nullable|array',
            'machine_erection_machines.*.*.machine_erection_date' => 'nullable|string|regex:/^(\d{1,2}-\d{1,2}(-\d{4})?)?$/',
            'machine_erection_machines.*.*.installation_completed_date' => 'nullable|string|regex:/^(\d{1,2}-\d{1,2}(-\d{4})?)?$/',
            'machine_erection_machines.*.*.certificate_received' => 'nullable|in:yes,no',
            'machine_erection_details' => 'nullable|array',
            'machine_erection_details.*' => 'nullable|array',
            'machine_erection_details.*.*.machine_category_id' => 'nullable|exists:machine_categories,id',
            'machine_erection_details.*.*.point_to_follow' => 'nullable|string|max:255',
            'machine_erection_details.*.*.machine_dates' => 'nullable|array',
            'machine_erection_details.*.*.machine_dates.*' => 'nullable|string|regex:/^(\d{1,2}-\d{1,2}(-\d{4})?)?$/',
        ]);

        try {
            DB::beginTransaction();

            MachineErectionMachineSummary::where('proforma_invoice_id', $proformaInvoice->id)->delete();
            MachineErectionDetail::where('proforma_invoice_id', $proformaInvoice->id)->delete();

            if ($request->has('machine_erection_machines') && is_array($request->machine_erection_machines)) {
                foreach ($request->machine_erection_machines as $categoryId => $machines) {
                    if (! is_array($machines)) {
                        continue;
                    }
                    foreach ($machines as $machineNumber => $row) {
                        $erectionDate = $this->parseErectionDate($row['machine_erection_date'] ?? null);
                        $installationDate = $this->parseErectionDate($row['installation_completed_date'] ?? null);
                        $certificate = $this->parseCertificateReceived($row['certificate_received'] ?? null);

                        if ($erectionDate || $installationDate || $certificate !== null) {
                            MachineErectionMachineSummary::create([
                                'proforma_invoice_id' => $proformaInvoice->id,
                                'machine_category_id' => (int) $categoryId,
                                'machine_number' => (int) $machineNumber,
                                'machine_erection_date' => $erectionDate,
                                'installation_completed_date' => $installationDate,
                                'certificate_received' => $certificate,
                            ]);
                        }
                    }
                }
            }

            $sortOrder = 0;
            if ($request->has('machine_erection_details') && is_array($request->machine_erection_details)) {
                foreach ($request->machine_erection_details as $categoryId => $categoryPoints) {
                    if (! is_array($categoryPoints)) {
                        continue;
                    }
                    foreach ($categoryPoints as $pointData) {
                        if (empty($pointData['point_to_follow']) || empty($pointData['machine_category_id'])) {
                            continue;
                        }

                        $pointToFollow = $pointData['point_to_follow'];
                        $machineCategoryId = $pointData['machine_category_id'];

                        if (isset($pointData['machine_dates']) && is_array($pointData['machine_dates'])) {
                            foreach ($pointData['machine_dates'] as $machineNumber => $date) {
                                $parsedDate = $this->parseErectionDate($date);
                                if ($parsedDate) {
                                    MachineErectionDetail::create([
                                        'proforma_invoice_id' => $proformaInvoice->id,
                                        'machine_category_id' => $machineCategoryId,
                                        'point_to_follow' => $pointToFollow,
                                        'machine_number' => (int) $machineNumber,
                                        'date' => $parsedDate,
                                        'sort_order' => $sortOrder,
                                    ]);
                                }
                            }
                        }
                        $sortOrder++;
                    }
                }
            }

            DB::commit();

            return redirect()->route('machine-erection.show', $proformaInvoice)
                ->with('success', 'Machine erection details saved successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving machine erection details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save machine erection details: ' . $e->getMessage()])
                ->withInput();
        }
    }

    protected function parseCertificateReceived(?string $value): ?bool
    {
        $value = strtolower(trim((string) $value));

        if ($value === 'yes') {
            return true;
        }

        if ($value === 'no') {
            return false;
        }

        return null;
    }

    protected function parseErectionDate(?string $date): ?string
    {
        $date = trim((string) $date);
        if ($date === '') {
            return null;
        }

        $dateParts = explode('-', $date);
        if (count($dateParts) === 2) {
            return date('Y') . '-' . $dateParts[1] . '-' . $dateParts[0];
        }
        if (count($dateParts) === 3) {
            return $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
        }

        return $date;
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
