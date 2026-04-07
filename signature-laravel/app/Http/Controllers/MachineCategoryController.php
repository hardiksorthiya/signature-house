<?php

namespace App\Http\Controllers;

use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MachineCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $categories = $query->paginate(10)->withQueryString();
        return view('machine-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_categories,name',
        ]);

        MachineCategory::create([
            'name' => $request->name,
        ]);

        return redirect()->route('machine-categories.index')
            ->with('success', 'Machine category added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MachineCategory $machineCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineCategory $machineCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_categories,name,' . $machineCategory->id,
        ]);

        $machineCategory->update([
            'name' => $request->name,
        ]);

        return redirect()->route('machine-categories.index')
            ->with('success', 'Machine category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineCategory $machineCategory)
    {
        $machineCategory->delete();

        return redirect()->route('machine-categories.index')
            ->with('success', 'Machine category deleted successfully.');
    }
}
