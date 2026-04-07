<?php

namespace App\Http\Controllers;

use App\Models\MachineHealdWire;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineHealdWireController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineHealdWire::with('machineCategories');
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
        $machineHealdWires = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-heald-wires.index', compact('machineHealdWires', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_heald_wires,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineHealdWire = MachineHealdWire::create([
            'name' => $request->name,
        ]);

        $machineHealdWire->machineCategories()->attach($request->categories);

        return redirect()->route('machine-heald-wires.index')
            ->with('success', 'Machine heald wire added successfully.');
    }

    public function update(Request $request, MachineHealdWire $machineHealdWire)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_heald_wires,name,' . $machineHealdWire->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineHealdWire->update([
            'name' => $request->name,
        ]);

        $machineHealdWire->machineCategories()->sync($request->categories);

        return redirect()->route('machine-heald-wires.index')
            ->with('success', 'Machine heald wire updated successfully.');
    }

    public function destroy(MachineHealdWire $machineHealdWire)
    {
        $machineHealdWire->delete();

        return redirect()->route('machine-heald-wires.index')
            ->with('success', 'Machine heald wire deleted successfully.');
    }
}
