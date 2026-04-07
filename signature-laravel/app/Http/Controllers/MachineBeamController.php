<?php

namespace App\Http\Controllers;

use App\Models\MachineBeam;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineBeamController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineBeam::with('machineCategories');
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
        $machineBeams = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-beams.index', compact('machineBeams', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_beams,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineBeam = MachineBeam::create([
            'name' => $request->name,
        ]);

        $machineBeam->machineCategories()->attach($request->categories);

        return redirect()->route('machine-beams.index')
            ->with('success', 'Machine beam added successfully.');
    }

    public function update(Request $request, MachineBeam $machineBeam)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_beams,name,' . $machineBeam->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineBeam->update([
            'name' => $request->name,
        ]);

        $machineBeam->machineCategories()->sync($request->categories);

        return redirect()->route('machine-beams.index')
            ->with('success', 'Machine beam updated successfully.');
    }

    public function destroy(MachineBeam $machineBeam)
    {
        $machineBeam->delete();

        return redirect()->route('machine-beams.index')
            ->with('success', 'Machine beam deleted successfully.');
    }
}
