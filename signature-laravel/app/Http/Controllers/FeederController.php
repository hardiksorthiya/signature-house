<?php

namespace App\Http\Controllers;

use App\Models\Feeder;
use App\Models\FeederBrand;
use App\Models\MachineCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeederController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Feeder::with(['feederBrand', 'machineCategories']);
        if ($request->filled('search')) {
            $query->where('feeder', 'like', '%' . $request->search . '%');
        }
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc': $query->orderBy('feeder', 'desc'); break;
            case 'date_asc': $query->orderBy('created_at', 'asc'); break;
            case 'date_desc': $query->orderBy('created_at', 'desc'); break;
            default: $query->orderBy('feeder', 'asc'); break;
        }
        $feeders = $query->paginate(10)->withQueryString();
        $feederBrands = FeederBrand::orderBy('name')->get();
        $categories = MachineCategory::orderBy('name')->get();
        return view('feeders.index', compact('feeders', 'feederBrands', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'feeder' => [
                'required',
                'string',
                'max:255',
                // Check for unique combination of feeder + feeder_brand_id
                Rule::unique('feeders')->where(function ($query) use ($request) {
                    return $query->where('feeder_brand_id', $request->feeder_brand_id);
                }),
            ],
            'feeder_brand_id' => 'required|exists:feeder_brands,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $feeder = Feeder::create([
            'feeder' => $request->feeder,
            'feeder_brand_id' => $request->feeder_brand_id,
        ]);

        // Attach categories
        $feeder->machineCategories()->attach($request->categories);

        return redirect()->route('feeders.index')
            ->with('success', 'Feeder added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feeder $feeder)
    {
        $request->validate([
            'feeder' => [
                'required',
                'string',
                'max:255',
                // Check for unique combination of feeder + feeder_brand_id, ignoring current record
                Rule::unique('feeders')->where(function ($query) use ($request) {
                    return $query->where('feeder_brand_id', $request->feeder_brand_id);
                })->ignore($feeder->id),
            ],
            'feeder_brand_id' => 'required|exists:feeder_brands,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $feeder->update([
            'feeder' => $request->feeder,
            'feeder_brand_id' => $request->feeder_brand_id,
        ]);

        // Sync categories
        $feeder->machineCategories()->sync($request->categories);

        return redirect()->route('feeders.index')
            ->with('success', 'Feeder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feeder $feeder)
    {
        $feeder->delete();

        return redirect()->route('feeders.index')
            ->with('success', 'Feeder deleted successfully.');
    }
}
