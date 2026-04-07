<?php

namespace App\Http\Controllers;

use App\Models\HsnCode;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class HsnCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = HsnCode::with('machineCategories');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'date_asc': $query->orderBy('created_at', 'asc'); break;
            case 'date_desc': $query->orderBy('created_at', 'desc'); break;
            default: $query->orderBy('name', 'asc'); break;
        }
        $hsnCodes = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('hsn-codes.index', compact('hsnCodes', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:hsn_codes,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $hsnCode = HsnCode::create([
            'name' => $request->name,
        ]);

        $hsnCode->machineCategories()->attach($request->categories);

        return redirect()->route('hsn-codes.index')
            ->with('success', 'HSN code added successfully.');
    }

    public function update(Request $request, HsnCode $hsnCode)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:hsn_codes,name,' . $hsnCode->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $hsnCode->update([
            'name' => $request->name,
        ]);

        $hsnCode->machineCategories()->sync($request->categories);

        return redirect()->route('hsn-codes.index')
            ->with('success', 'HSN code updated successfully.');
    }

    public function destroy(HsnCode $hsnCode)
    {
        $hsnCode->delete();

        return redirect()->route('hsn-codes.index')
            ->with('success', 'HSN code deleted successfully.');
    }
}
