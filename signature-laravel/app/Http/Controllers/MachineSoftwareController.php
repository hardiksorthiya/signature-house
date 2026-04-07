<?php

namespace App\Http\Controllers;

use App\Models\MachineSoftware;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineSoftwareController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineSoftware::with('machineCategories');
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
        $machineSoftwares = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-softwares.index', compact('machineSoftwares', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_softwares,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineSoftware = MachineSoftware::create([
            'name' => $request->name,
        ]);

        $machineSoftware->machineCategories()->attach($request->categories);

        return redirect()->route('machine-softwares.index')
            ->with('success', 'Machine software added successfully.');
    }

    public function update(Request $request, MachineSoftware $machineSoftware)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_softwares,name,' . $machineSoftware->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineSoftware->update([
            'name' => $request->name,
        ]);

        $machineSoftware->machineCategories()->sync($request->categories);

        return redirect()->route('machine-softwares.index')
            ->with('success', 'Machine software updated successfully.');
    }

    public function destroy(MachineSoftware $machineSoftware)
    {
        $machineSoftware->delete();

        return redirect()->route('machine-softwares.index')
            ->with('success', 'Machine software deleted successfully.');
    }
}
