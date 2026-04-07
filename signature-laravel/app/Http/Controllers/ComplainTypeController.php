<?php

namespace App\Http\Controllers;

use App\Models\ComplainType;
use Illuminate\Http\Request;

class ComplainTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ComplainType::query();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where('name', 'like', "%{$term}%");
        }

        $complainTypes = $query->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();

        return view('complain-types.index', compact('complainTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:complain_types,name',
        ]);

        $maxOrder = ComplainType::max('sort_order') ?? 0;
        ComplainType::create([
            'name' => $request->name,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('complain-types.index')
            ->with('success', 'Complain type added successfully.');
    }

    public function update(Request $request, ComplainType $complainType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:complain_types,name,' . $complainType->id,
        ]);

        $complainType->update(['name' => $request->name]);

        return redirect()->route('complain-types.index')
            ->with('success', 'Complain type updated successfully.');
    }

    public function destroy(ComplainType $complainType)
    {
        if ($complainType->complaints()->exists()) {
            return redirect()->route('complain-types.index')
                ->with('error', 'Cannot delete: this type is used by one or more complaints.');
        }
        $complainType->delete();

        return redirect()->route('complain-types.index')
            ->with('success', 'Complain type deleted successfully.');
    }
}
