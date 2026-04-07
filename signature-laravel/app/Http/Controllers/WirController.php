<?php

namespace App\Http\Controllers;

use App\Models\Wir;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class WirController extends Controller
{
    public function index(Request $request)
    {
        $query = Wir::with('machineCategories');
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
        $wirs = $query->paginate(10)->withQueryString();
        $categories = MachineCategory::orderBy('name')->get();
        return view('wirs.index', compact('wirs', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:wirs,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $wir = Wir::create([
            'name' => $request->name,
        ]);

        $wir->machineCategories()->attach($request->categories);

        return redirect()->route('wirs.index')
            ->with('success', 'WIR added successfully.');
    }

    public function update(Request $request, Wir $wir)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:wirs,name,' . $wir->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $wir->update([
            'name' => $request->name,
        ]);

        $wir->machineCategories()->sync($request->categories);

        return redirect()->route('wirs.index')
            ->with('success', 'WIR updated successfully.');
    }

    public function destroy(Wir $wir)
    {
        $wir->delete();

        return redirect()->route('wirs.index')
            ->with('success', 'WIR deleted successfully.');
    }
}
