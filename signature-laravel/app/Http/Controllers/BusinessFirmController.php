<?php

namespace App\Http\Controllers;

use App\Models\BusinessFirm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessFirmController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessFirm::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }
        $sort = $request->get('sort', 'name_asc');
        match ($sort) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'date_asc' => $query->orderBy('created_at', 'asc'),
            'date_desc' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };
        $businessFirms = $query->paginate(10)->withQueryString();
        return view('business-firms.index', compact('businessFirms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_firms,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('business-firm-logos', 'public');
        }

        BusinessFirm::create([
            'name' => $request->name,
            'logo' => $logoPath,
            'address' => $request->address,
        ]);

        return redirect()->route('business-firms.index')
            ->with('success', 'Business firm added successfully.');
    }

    public function update(Request $request, BusinessFirm $businessFirm)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:business_firms,name,' . $businessFirm->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
        ]);

        $logoPath = $businessFirm->logo;
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($businessFirm->logo) {
                Storage::disk('public')->delete($businessFirm->logo);
            }
            $logoPath = $request->file('logo')->store('business-firm-logos', 'public');
        }

        $businessFirm->update([
            'name' => $request->name,
            'logo' => $logoPath,
            'address' => $request->address,
        ]);

        return redirect()->route('business-firms.index')
            ->with('success', 'Business firm updated successfully.');
    }

    public function destroy(BusinessFirm $businessFirm)
    {
        // Delete logo if exists
        if ($businessFirm->logo) {
            Storage::disk('public')->delete($businessFirm->logo);
        }

        $businessFirm->delete();

        return redirect()->route('business-firms.index')
            ->with('success', 'Business firm deleted successfully.');
    }
}
