<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\SerialNumber;
use App\Support\MsUnloadingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SerialNumberController extends Controller
{
    /**
     * Display serial numbers index page (list all PIs)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['serialNumbers.machineCategory', 'contract.creator', 'creator', 'seller'])
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

        return view('serial-numbers.index', compact('proformaInvoices'));
    }

    /**
     * PI + contract rows for the searchable dropdown (same as Pre Erection / Damage details).
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
     * Show the form for adding serial numbers for a proforma invoice
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        if (! MsUnloadingAssignment::userCanAccessPi($proformaInvoice)) {
            abort(403, 'You are not assigned to this MS Unloading job.');
        }

        $proformaInvoice->load([
            'proformaInvoiceMachines.machineCategory',
            'proformaInvoiceMachines.brand',
            'proformaInvoiceMachines.machineModel',
            'proformaInvoiceMachines.serialNumbers',
            'serialNumbers.machineCategory'
        ]);
        
        // Group machines by category with their quantities
        $machinesByCategory = $proformaInvoice->proformaInvoiceMachines
            ->groupBy('machine_category_id')
            ->map(function($machines) {
                $category = $machines->first()->machineCategory;
                $totalQuantity = $machines->sum('quantity');
                
                // Get existing serial numbers for these machines
                $machineIds = $machines->pluck('id');
                $existingSerialNumbers = SerialNumber::whereIn('proforma_invoice_machine_id', $machineIds)
                    ->get()
                    ->groupBy('proforma_invoice_machine_id');
                
                return [
                    'category' => $category,
                    'machines' => $machines->map(function($machine) use ($existingSerialNumbers) {
                        $serialNumbers = $existingSerialNumbers->get($machine->id, collect())->values();
                        // Create array indexed by position for easy access in view
                        $serialNumbersArray = [];
                        foreach ($serialNumbers as $index => $serial) {
                            $serialNumbersArray[$index] = $serial;
                        }
                        return [
                            'machine' => $machine,
                            'serial_numbers' => $serialNumbersArray,
                        ];
                    }),
                    'total_quantity' => $totalQuantity,
                ];
            });

        return view('serial-numbers.show', compact('proformaInvoice', 'machinesByCategory'));
    }

    /**
     * Store serial numbers for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        MsUnloadingAssignment::ensureCanAccessPi($proformaInvoice);

        $validator = Validator::make($request->all(), [
            'serial_numbers' => 'required|array',
            'serial_numbers.*.*.machine_id' => 'required|exists:proforma_invoice_machines,id',
            'serial_numbers.*.*.serial_number' => 'nullable|string|max:255',
            'serial_numbers.*.*.khata_number' => 'nullable|string|max:255',
            'serial_numbers.*.*.production_date' => 'nullable|date',
            'serial_numbers.*.*.name_plate' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'serial_numbers.*.*.keep_name_plate_path' => 'nullable|string|max:500',
        ]);

        // When Khata number is provided, Serial number is compulsory
        $validator->after(function ($validator) use ($request) {
            foreach ($request->serial_numbers ?? [] as $machineId => $instances) {
                if (empty($machineId) || !is_array($instances)) {
                    continue;
                }
                foreach ($instances as $index => $instance) {
                    $khata = trim((string) ($instance['khata_number'] ?? ''));
                    $serial = trim((string) ($instance['serial_number'] ?? ''));
                    if ($khata !== '' && $serial === '') {
                        $validator->errors()->add(
                            "serial_numbers.{$machineId}.{$index}.serial_number",
                            'Serial number is required when Khata number is provided.'
                        );
                    }
                }
            }
        });

        $validator->validate();

        try {
            DB::beginTransaction();

            $existingByMachine = SerialNumber::where('proforma_invoice_id', $proformaInvoice->id)
                ->orderBy('id')
                ->get()
                ->groupBy('proforma_invoice_machine_id')
                ->map(fn ($rows) => $rows->values());

            $pathsToDelete = SerialNumber::where('proforma_invoice_id', $proformaInvoice->id)
                ->whereNotNull('name_plate_path')
                ->pluck('name_plate_path')
                ->all();

            SerialNumber::where('proforma_invoice_id', $proformaInvoice->id)->delete();

            $keptPaths = [];

            foreach ($request->serial_numbers as $machineId => $instances) {
                if (empty($machineId) || ! is_array($instances)) {
                    continue;
                }

                $machine = $proformaInvoice->proformaInvoiceMachines()
                    ->where('id', $machineId)
                    ->first();

                if (! $machine) {
                    continue;
                }

                foreach ($instances as $index => $instanceData) {
                    $serial = trim((string) ($instanceData['serial_number'] ?? ''));
                    $khata = trim((string) ($instanceData['khata_number'] ?? ''));
                    $productionDate = $instanceData['production_date'] ?? null;
                    $hasProductionDate = $productionDate !== null && $productionDate !== '';
                    $hasFile = $request->hasFile("serial_numbers.{$machineId}.{$index}.name_plate");

                    if ($serial === '' && $khata === '' && ! $hasProductionDate && ! $hasFile
                        && empty($instanceData['keep_name_plate_path'])) {
                        continue;
                    }

                    $namePlatePath = $this->resolveNamePlatePath(
                        $request,
                        $proformaInvoice,
                        (string) $machineId,
                        (int) $index,
                        $instanceData,
                        $existingByMachine->get($machineId)
                    );

                    if ($namePlatePath) {
                        $keptPaths[] = $namePlatePath;
                    }

                    SerialNumber::create([
                        'proforma_invoice_id' => $proformaInvoice->id,
                        'proforma_invoice_machine_id' => $machineId,
                        'machine_category_id' => $machine->machine_category_id,
                        'serial_number' => $serial !== '' ? $serial : null,
                        'khata_number' => $khata !== '' ? $khata : null,
                        'production_date' => $hasProductionDate ? $productionDate : null,
                        'name_plate_path' => $namePlatePath,
                    ]);
                }
            }

            foreach ($pathsToDelete as $oldPath) {
                if (! in_array($oldPath, $keptPaths, true) && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            DB::commit();

            return redirect()->route('serial-numbers.show', $proformaInvoice)
                ->with('success', 'Serial numbers saved successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving serial numbers: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save serial numbers: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\SerialNumber>|null  $existingRows
     */
    protected function resolveNamePlatePath(
        Request $request,
        ProformaInvoice $proformaInvoice,
        string $machineId,
        int $index,
        array $instanceData,
        $existingRows
    ): ?string {
        $fileKey = "serial_numbers.{$machineId}.{$index}.name_plate";

        if ($request->hasFile($fileKey)) {
            $image = $request->file($fileKey);
            if ($image->isValid()) {
                $fileName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $image->getClientOriginalName());

                return $image->storeAs(
                    'serial-numbers/name-plates/' . $proformaInvoice->id,
                    $fileName,
                    'public'
                );
            }
        }

        $keep = trim((string) ($instanceData['keep_name_plate_path'] ?? ''));
        if ($keep !== '' && Storage::disk('public')->exists($keep)) {
            return $keep;
        }

        if ($existingRows && isset($existingRows[$index])) {
            return $existingRows[$index]->name_plate_path;
        }

        return null;
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
