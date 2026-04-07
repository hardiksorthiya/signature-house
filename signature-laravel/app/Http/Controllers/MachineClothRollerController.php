<?php

namespace App\Http\Controllers;

use App\Models\MachineClothRoller;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineClothRollerController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineClothRoller::with('machineCategories');
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
        $machineClothRollers = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-cloth-rollers.index', compact('machineClothRollers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_cloth_rollers,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineClothRoller = MachineClothRoller::create([
            'name' => $request->name,
        ]);

        $machineClothRoller->machineCategories()->attach($request->categories);

        return redirect()->route('machine-cloth-rollers.index')
            ->with('success', 'Machine cloth roller added successfully.');
    }

    public function update(Request $request, MachineClothRoller $machineClothRoller)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_cloth_rollers,name,' . $machineClothRoller->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineClothRoller->update([
            'name' => $request->name,
        ]);

        $machineClothRoller->machineCategories()->sync($request->categories);

        return redirect()->route('machine-cloth-rollers.index')
            ->with('success', 'Machine cloth roller updated successfully.');
    }

    public function destroy(MachineClothRoller $machineClothRoller)
    {
        $machineClothRoller->delete();

        return redirect()->route('machine-cloth-rollers.index')
            ->with('success', 'Machine cloth roller deleted successfully.');
    }
}
