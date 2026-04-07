<?php

namespace App\Http\Controllers;

use App\Models\MachineERead;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineEReadController extends Controller
{
    public function index(Request $request)
    {
        $query = MachineERead::with('machineCategories');
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
        $machineEReads = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-e-reads.index', compact('machineEReads', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_e_reads,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineERead = MachineERead::create([
            'name' => $request->name,
        ]);

        $machineERead->machineCategories()->attach($request->categories);

        return redirect()->route('machine-e-reads.index')
            ->with('success', 'Machine e-read added successfully.');
    }

    public function update(Request $request, MachineERead $machineERead)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_e_reads,name,' . $machineERead->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineERead->update([
            'name' => $request->name,
        ]);

        $machineERead->machineCategories()->sync($request->categories);

        return redirect()->route('machine-e-reads.index')
            ->with('success', 'Machine e-read updated successfully.');
    }

    public function destroy(MachineERead $machineERead)
    {
        $machineERead->delete();

        return redirect()->route('machine-e-reads.index')
            ->with('success', 'Machine e-read deleted successfully.');
    }
}
