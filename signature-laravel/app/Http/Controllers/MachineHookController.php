<?php

namespace App\Http\Controllers;

use App\Models\MachineHook;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineHookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MachineHook::with('machineCategories');
        if ($request->filled('search')) {
            $query->where('hook', 'like', '%' . $request->search . '%');
        }
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc': $query->orderBy('hook', 'desc'); break;
            case 'date_asc': $query->orderBy('created_at', 'asc'); break;
            case 'date_desc': $query->orderBy('created_at', 'desc'); break;
            default: $query->orderBy('hook', 'asc'); break;
        }
        $machineHooks = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-hooks.index', compact('machineHooks', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hook' => 'required|string|max:255|unique:machine_hooks,hook',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineHook = MachineHook::create([
            'hook' => $request->hook,
        ]);

        // Attach categories
        $machineHook->machineCategories()->attach($request->categories);

        return redirect()->route('machine-hooks.index')
            ->with('success', 'Machine hook added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineHook $machineHook)
    {
        $request->validate([
            'hook' => 'required|string|max:255|unique:machine_hooks,hook,' . $machineHook->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineHook->update([
            'hook' => $request->hook,
        ]);

        // Sync categories
        $machineHook->machineCategories()->sync($request->categories);

        return redirect()->route('machine-hooks.index')
            ->with('success', 'Machine hook updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineHook $machineHook)
    {
        $machineHook->delete();

        return redirect()->route('machine-hooks.index')
            ->with('success', 'Machine hook deleted successfully.');
    }
}
