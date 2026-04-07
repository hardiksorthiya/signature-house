<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $query = Business::query();

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Sort
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

        $businesses = $query->paginate(10)->withQueryString();
        return view('businesses.index', compact('businesses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:businesses,name',
        ]);

        Business::create([
            'name' => $request->name,
        ]);

        return redirect()->route('businesses.index')
            ->with('success', 'Business added successfully.');
    }

    public function update(Request $request, Business $business)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:businesses,name,' . $business->id,
        ]);

        $business->update([
            'name' => $request->name,
        ]);

        return redirect()->route('businesses.index')
            ->with('success', 'Business updated successfully.');
    }

    public function destroy(Business $business)
    {
        $business->delete();

        return redirect()->route('businesses.index')
            ->with('success', 'Business deleted successfully.');
    }
}
