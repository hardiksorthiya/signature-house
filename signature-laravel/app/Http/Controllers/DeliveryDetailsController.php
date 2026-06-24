<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\User;
use App\Support\MsUnloadingAssignment;
use Illuminate\Http\Request;

class DeliveryDetailsController extends Controller
{
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['deliveryDetails', 'contract.creator', 'creator', 'seller', 'msUnloadingAssignedUsers'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('sales_manager_id')) {
            $isAdminOrSuper = User::where('id', $request->sales_manager_id)
                ->whereHas('roles', function ($r) {
                    $r->whereIn('name', ['Admin', 'Super Admin']);
                })
                ->exists();
            if (! $isAdminOrSuper) {
                $query->where(function ($q) use ($request) {
                    $q->whereHas('contract', function ($subQ) use ($request) {
                        $subQ->where('created_by', $request->sales_manager_id);
                    })
                        ->orWhere('created_by', $request->sales_manager_id);
                });
            }
        }

        if ($request->filled('pi_number')) {
            $query->where('proforma_invoice_number', 'like', '%' . $request->pi_number . '%');
        }

        if ($request->filled('customer_name')) {
            $customerName = trim($request->customer_name);
            $query->where(function ($q) use ($customerName) {
                $q->where('buyer_company_name', 'like', '%' . $customerName . '%')
                    ->orWhereHas('contract', function ($subQ) use ($customerName) {
                        $subQ->where('buyer_name', 'like', '%' . $customerName . '%')
                            ->orWhere('company_name', 'like', '%' . $customerName . '%');
                    });
            });
        }

        $proformaInvoices = $query->paginate(15)->withQueryString();

        $salesManagers = User::whereHas('roles', function ($r) {
            $r->whereIn('name', ['Sales Manager', 'Admin', 'Super Admin']);
        })->select('id', 'name')->orderBy('name')->get();

        $canAssignMsUnloading = MsUnloadingAssignment::userSeesAllMsUnloading();

        return view('proforma-invoices.delivery-details-index', compact(
            'proformaInvoices',
            'salesManagers',
            'canAssignMsUnloading'
        ));
    }

    public function getProformaInvoicesBySalesManager(Request $request)
    {
        if (! $request->filled('sales_manager_id')) {
            return response()->json([]);
        }

        $salesManagerId = $request->sales_manager_id;
        $isAdminOrSuper = User::where('id', $salesManagerId)
            ->whereHas('roles', function ($r) {
                $r->whereIn('name', ['Admin', 'Super Admin']);
            })
            ->exists();

        $query = ProformaInvoice::with(['contract'])
            ->orderByDesc('proforma_invoice_number');

        if (! $isAdminOrSuper) {
            $query->where(function ($q) use ($salesManagerId) {
                $q->whereHas('contract', function ($subQ) use ($salesManagerId) {
                    $subQ->where('created_by', $salesManagerId);
                })
                    ->orWhere('created_by', $salesManagerId);
            });
        }

        return response()->json(
            $query->get()->map(function (ProformaInvoice $pi) {
                return [
                    'id' => $pi->id,
                    'proforma_invoice_number' => $pi->proforma_invoice_number,
                    'buyer_company_name' => $pi->buyer_company_name,
                    'buyer_name' => $pi->contract?->buyer_name,
                    'contract' => $pi->contract ? [
                        'contract_number' => $pi->contract->contract_number,
                        'company_name' => $pi->contract->company_name,
                    ] : null,
                ];
            })
        );
    }
}
