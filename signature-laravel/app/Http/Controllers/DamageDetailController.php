<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\DamageDetail;
use App\Models\DamageImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DamageDetailController extends Controller
{
    /**
     * Display damage details index page (list all PIs - with or without damage details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['damageDetails.images', 'contract.creator', 'creator', 'seller'])
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

        return view('damage-details.index', compact('proformaInvoices'));
    }

    /**
     * PI + contract rows for the searchable dropdown (same as Pre Erection / MS unloading lists).
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
     * Show the form for adding/viewing damage details for a proforma invoice
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load(['damageDetails.images']);
        
        // Get existing damage details
        $damageDetails = $proformaInvoice->damageDetails;

        return view('damage-details.show', compact('proformaInvoice', 'damageDetails'));
    }

    /**
     * Show the form for editing a damage detail
     */
    public function edit(ProformaInvoice $proformaInvoice, DamageDetail $damageDetail)
    {
        $this->assertDamageDetailBelongsToProforma($proformaInvoice, $damageDetail);
        $damageDetail->load(['images']);
        return view('damage-details.edit', compact('damageDetail', 'proformaInvoice'));
    }

    /**
     * Update a damage detail
     */
    public function update(Request $request, ProformaInvoice $proformaInvoice, DamageDetail $damageDetail)
    {
        $this->assertDamageDetailBelongsToProforma($proformaInvoice, $damageDetail);

        $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB per image
        ]);

        try {
            DB::beginTransaction();

            // Update damage detail
            $damageDetail->update([
                'title' => $request->title,
                'detail' => $request->detail,
            ]);

            foreach ($this->normalizedImageUploads($request) as $image) {
                if ($image->isValid()) {
                    $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                    $filePath = $image->storeAs('damage-images/' . $damageDetail->id, $fileName, 'public');

                    DamageImage::create([
                        'damage_detail_id' => $damageDetail->id,
                        'file_name' => $image->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                    ]);
                }
            }

            DB::commit();

            $damageDetail->loadMissing('proformaInvoice');

            return redirect()->route('damage-details.show', $proformaInvoice)
                ->with('success', 'Damage detail updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating damage detail: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('damage-details.edit', [$proformaInvoice, $damageDetail])
                ->withErrors(['error' => 'Failed to update damage detail: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Store a new damage detail for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB per image
        ]);

        try {
            DB::beginTransaction();

            // Create damage detail
            $damageDetail = DamageDetail::create([
                'proforma_invoice_id' => $proformaInvoice->id,
                'title' => $request->title,
                'detail' => $request->detail,
            ]);

            foreach ($this->normalizedImageUploads($request) as $image) {
                if ($image->isValid()) {
                    $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                    $filePath = $image->storeAs('damage-images/' . $damageDetail->id, $fileName, 'public');

                    DamageImage::create([
                        'damage_detail_id' => $damageDetail->id,
                        'file_name' => $image->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('damage-details.show', $proformaInvoice)
                ->with('success', 'Damage detail added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving damage detail: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save damage detail: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete a damage detail
     */
    public function destroy(DamageDetail $damageDetail)
    {
        try {
            DB::beginTransaction();

            // Delete all associated images
            foreach ($damageDetail->images as $image) {
                if (Storage::disk('public')->exists($image->file_path)) {
                    Storage::disk('public')->delete($image->file_path);
                }
                $image->delete();
            }

            // Delete damage detail
            $damageDetail->delete();

            DB::commit();

            return back()->with('success', 'Damage detail deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting damage detail: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete damage detail: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a damage image
     */
    public function destroyImage(DamageImage $damageImage)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($damageImage->file_path)) {
                Storage::disk('public')->delete($damageImage->file_path);
            }

            // Delete record from database
            $damageImage->delete();

            return back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete image: ' . $e->getMessage()]);
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

    protected function assertDamageDetailBelongsToProforma(ProformaInvoice $proformaInvoice, DamageDetail $damageDetail): void
    {
        if ((int) $damageDetail->proforma_invoice_id !== (int) $proformaInvoice->id) {
            abort(404);
        }
    }

    /**
     * @return list<UploadedFile>
     */
    protected function normalizedImageUploads(Request $request): array
    {
        if (! $request->hasFile('images')) {
            return [];
        }

        $files = $request->file('images');

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (! is_array($files)) {
            return [];
        }

        $out = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $out[] = $file;
            }
        }

        return $out;
    }
}
