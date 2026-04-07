<?php

namespace App\Http\Controllers;

use App\Models\MachineSize;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MachineSize::with('machineCategories');
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
        $machineSizes = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-sizes.index', compact('machineSizes', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_sizes,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineSize = MachineSize::create([
            'name' => $request->name,
        ]);

        // Attach categories
        $machineSize->machineCategories()->attach($request->categories);

        return redirect()->route('machine-sizes.index')
            ->with('success', 'Machine size added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineSize $machineSize)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_sizes,name,' . $machineSize->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineSize->update([
            'name' => $request->name,
        ]);

        // Sync categories
        $machineSize->machineCategories()->sync($request->categories);

        return redirect()->route('machine-sizes.index')
            ->with('success', 'Machine size updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineSize $machineSize)
    {
        $machineSize->delete();

        return redirect()->route('machine-sizes.index')
            ->with('success', 'Machine size deleted successfully.');
    }
}
