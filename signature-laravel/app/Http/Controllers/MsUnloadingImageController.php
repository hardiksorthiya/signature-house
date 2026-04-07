<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\MsUnloadingImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MsUnloadingImageController extends Controller
{
    /**
     * Display image uploading index page (list all PIs - with or without images)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['msUnloadingImages', 'contract.creator', 'creator', 'seller'])
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

        return view('ms-unloading-images.index', compact('proformaInvoices'));
    }

    /**
     * PI + contract rows for the searchable dropdown (same behaviour as Pre Erection list).
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
     * Show the form for uploading images for a proforma invoice
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load('msUnloadingImages');
        
        // Get existing images
        $existingImages = $proformaInvoice->msUnloadingImages;

        return view('ms-unloading-images.show', compact('proformaInvoice', 'existingImages'));
    }

    /**
     * Store uploaded images for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB per image
        ]);

        try {
            DB::beginTransaction();
            $this->persistUploadedImages($proformaInvoice, $request->file('images', []));
            DB::commit();

            return redirect()->route('ms-unloading-images.show', $proformaInvoice)
                ->with('success', 'Images uploaded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading images: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to upload images: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * @param  array<int, \Illuminate\Http\UploadedFile>  $files
     */
    protected function persistUploadedImages(ProformaInvoice $proformaInvoice, array $files): void
    {
        foreach ($files as $image) {
            if ($image->isValid()) {
                $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $filePath = $image->storeAs('ms-unloading-images/' . $proformaInvoice->id, $fileName, 'public');

                MsUnloadingImage::create([
                    'proforma_invoice_id' => $proformaInvoice->id,
                    'file_name' => $image->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $image->getMimeType(),
                    'file_size' => $image->getSize(),
                ]);
            }
        }
    }

    /**
     * Delete an uploaded image
     */
    public function destroy(MsUnloadingImage $msUnloadingImage)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($msUnloadingImage->file_path)) {
                Storage::disk('public')->delete($msUnloadingImage->file_path);
            }

            // Delete record from database
            $msUnloadingImage->delete();

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
}
