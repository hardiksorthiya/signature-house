<?php

namespace App\Http\Controllers;

use App\Models\MachineLever;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineLeverController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineLever::with('machineCategories');
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
        $machineLevers = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-levers.index', compact('machineLevers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_levers,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineLever = MachineLever::create([
            'name' => $request->name,
        ]);

        $machineLever->machineCategories()->attach($request->categories);

        return redirect()->route('machine-levers.index')
            ->with('success', 'Machine lever added successfully.');
    }

    public function update(Request $request, MachineLever $machineLever)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_levers,name,' . $machineLever->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineLever->update([
            'name' => $request->name,
        ]);

        $machineLever->machineCategories()->sync($request->categories);

        return redirect()->route('machine-levers.index')
            ->with('success', 'Machine lever updated successfully.');
    }

    public function destroy(MachineLever $machineLever)
    {
        $machineLever->delete();

        return redirect()->route('machine-levers.index')
            ->with('success', 'Machine lever deleted successfully.');
    }
}
