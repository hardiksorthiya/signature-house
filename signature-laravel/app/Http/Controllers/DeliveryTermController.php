<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTerm;
use Illuminate\Http\Request;

class DeliveryTermController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryTerm::query();
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
        $deliveryTerms = $query->paginate(10)->withQueryString();
        return view('delivery-terms.index', compact('deliveryTerms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_terms,name',
        ]);

        DeliveryTerm::create([
            'name' => $request->name,
        ]);

        return redirect()->route('delivery-terms.index')
            ->with('success', 'Delivery term added successfully.');
    }

    public function update(Request $request, DeliveryTerm $deliveryTerm)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_terms,name,' . $deliveryTerm->id,
        ]);

        $deliveryTerm->update([
            'name' => $request->name,
        ]);

        return redirect()->route('delivery-terms.index')
            ->with('success', 'Delivery term updated successfully.');
    }

    public function destroy(DeliveryTerm $deliveryTerm)
    {
        $deliveryTerm->delete();

        return redirect()->route('delivery-terms.index')
            ->with('success', 'Delivery term deleted successfully.');
    }
}
