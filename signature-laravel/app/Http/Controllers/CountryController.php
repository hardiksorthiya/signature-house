<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Country::query();
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
        $countries = $query->paginate(10)->withQueryString();
        return view('countries.index', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'currency' => 'nullable|string|max:10',
        ]);

        $country = Country::create([
            'name' => $request->name,
            'currency' => $request->currency,
        ]);

        // Return JSON response for AJAX requests (from sellers page)
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'country' => $country,
                'message' => 'Country added successfully.'
            ]);
        }

        // Redirect for form submissions (from countries index page)
        return redirect()->route('countries.index')
            ->with('success', 'Country added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'currency' => 'nullable|string|max:10',
        ]);

        $country->update([
            'name' => $request->name,
            'currency' => $request->currency,
        ]);

        return redirect()->route('countries.index')
            ->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('countries.index')
            ->with('success', 'Country deleted successfully.');
    }
}
