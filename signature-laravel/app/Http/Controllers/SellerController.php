<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\MachineCategory;
use App\Models\SellerBankDetail;
use App\Models\Country;
use App\Models\PILayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{
    /**
     * Filter blank bank detail rows coming from UI hidden inputs.
     * Allows saving sellers when user didn't add any bank details.
     */
    protected function normalizeBankDetails(Request $request): void
    {
        $bankDetails = $request->input('bank_details');
        if (!is_array($bankDetails)) {
            return;
        }

        $filtered = [];
        foreach ($bankDetails as $row) {
            if (!is_array($row)) {
                continue;
            }

            $bankName = trim((string) ($row['bank_name'] ?? ''));
            $accountNumber = trim((string) ($row['account_number'] ?? ''));
            $ifscCode = trim((string) ($row['ifsc_code'] ?? ''));

            // Keep row only if at least one important field is filled.
            if ($bankName === '' && $accountNumber === '' && $ifscCode === '') {
                continue;
            }

            $filtered[] = $row;
        }

        $request->merge(['bank_details' => array_values($filtered)]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Seller::with(['country', 'machineCategories', 'bankDetails', 'piLayout']);
        if ($request->filled('search')) {
            $query->where('seller_name', 'like', '%' . $request->search . '%');
        }
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc': $query->orderBy('seller_name', 'desc'); break;
            case 'date_asc': $query->orderBy('created_at', 'asc'); break;
            case 'date_desc': $query->orderBy('created_at', 'desc'); break;
            default: $query->orderBy('seller_name', 'asc'); break;
        }
        $sellers = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        return view('sellers.index', compact('sellers', 'categories', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->normalizeBankDetails($request);

        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
            'seller_name' => 'required|string|max:255',
            'email' => 'required|email|unique:sellers,email',
            'mobile' => 'required|string|max:20',
            'address' => 'required|string',
            'pi_short_name' => 'required|string|max:255',
            'gst_no' => 'nullable|string|max:255',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bank_details' => 'nullable|array',
            'bank_details.*.bank_name' => 'required_with:bank_details|string|max:255',
            'bank_details.*.account_number' => 'required_with:bank_details|string|max:255',
            'bank_details.*.ifsc_code' => 'required_with:bank_details|string|max:255',
            'bank_details.*.branch_name' => 'nullable|string|max:255',
            'bank_details.*.bank_address' => 'nullable|string',
            'bank_details.*.account_holder_name' => 'nullable|string|max:255',
        ]);

        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('seller-logos', 'public');
        }

        $seller = Seller::create([
            'country_id' => $request->country_id,
            'seller_name' => $request->seller_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'pi_short_name' => $request->pi_short_name,
            'gst_no' => $request->gst_no,
            'signature' => $signaturePath,
            'logo' => $logoPath,
        ]);

        // Attach categories
        $seller->machineCategories()->attach($request->categories);

        // Create bank details
        if ($request->has('bank_details') && is_array($request->bank_details)) {
            foreach ($request->bank_details as $bankDetail) {
                if (!empty($bankDetail['bank_name'])) {
                    SellerBankDetail::create([
                        'seller_id' => $seller->id,
                        'bank_name' => $bankDetail['bank_name'],
                        'account_number' => $bankDetail['account_number'],
                        'ifsc_code' => $bankDetail['ifsc_code'],
                        'branch_name' => $bankDetail['branch_name'] ?? null,
                        'bank_address' => $bankDetail['bank_address'] ?? null,
                        'account_holder_name' => $bankDetail['account_holder_name'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('sellers.index')
            ->with('success', 'Seller added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seller $seller)
    {
        $this->normalizeBankDetails($request);

        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
            'seller_name' => 'required|string|max:255',
            'email' => 'required|email|unique:sellers,email,' . $seller->id,
            'mobile' => 'required|string|max:20',
            'address' => 'required|string',
            'pi_short_name' => 'required|string|max:255',
            'gst_no' => 'nullable|string|max:255',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bank_details' => 'nullable|array',
            'bank_details.*.bank_name' => 'required_with:bank_details|string|max:255',
            'bank_details.*.account_number' => 'required_with:bank_details|string|max:255',
            'bank_details.*.ifsc_code' => 'required_with:bank_details|string|max:255',
            'bank_details.*.branch_name' => 'nullable|string|max:255',
            'bank_details.*.bank_address' => 'nullable|string',
            'bank_details.*.account_holder_name' => 'nullable|string|max:255',
        ]);

        $signaturePath = $seller->signature;
        if ($request->hasFile('signature')) {
            // Delete old signature if exists
            if ($seller->signature) {
                Storage::disk('public')->delete($seller->signature);
            }
            $signaturePath = $request->file('signature')->store('signatures', 'public');
        }

        $logoPath = $seller->logo;
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($seller->logo) {
                Storage::disk('public')->delete($seller->logo);
            }
            $logoPath = $request->file('logo')->store('seller-logos', 'public');
        }

        $seller->update([
            'country_id' => $request->country_id,
            'seller_name' => $request->seller_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'pi_short_name' => $request->pi_short_name,
            'gst_no' => $request->gst_no,
            'signature' => $signaturePath,
            'logo' => $logoPath,
        ]);

        // Sync categories
        $seller->machineCategories()->sync($request->categories);

        // Delete existing bank details
        $seller->bankDetails()->delete();

        // Create new bank details
        if ($request->has('bank_details') && is_array($request->bank_details)) {
            foreach ($request->bank_details as $bankDetail) {
                if (!empty($bankDetail['bank_name'])) {
                    SellerBankDetail::create([
                        'seller_id' => $seller->id,
                        'bank_name' => $bankDetail['bank_name'],
                        'account_number' => $bankDetail['account_number'],
                        'ifsc_code' => $bankDetail['ifsc_code'],
                        'branch_name' => $bankDetail['branch_name'] ?? null,
                        'bank_address' => $bankDetail['bank_address'] ?? null,
                        'account_holder_name' => $bankDetail['account_holder_name'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('sellers.index')
            ->with('success', 'Seller updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seller $seller)
    {
        $seller->delete();

        return redirect()->route('sellers.index')
            ->with('success', 'Seller deleted successfully.');
    }
}
