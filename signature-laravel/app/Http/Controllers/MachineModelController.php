<?php

namespace App\Http\Controllers;

use App\Models\MachineModel;
use App\Models\Brand;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MachineModel::with(['brands', 'machineCategories']);
        if ($request->filled('search')) {
            $query->where('model_no', 'like', '%' . $request->search . '%');
        }
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc': $query->orderBy('model_no', 'desc'); break;
            case 'date_asc': $query->orderBy('created_at', 'asc'); break;
            case 'date_desc': $query->orderBy('created_at', 'desc'); break;
            default: $query->orderBy('model_no', 'asc'); break;
        }
        $machineModels = $query->paginate(10)->withQueryString();
        $brands = Brand::orderBy('name')->get();
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-models.index', compact('machineModels', 'brands', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'model_no' => 'required|string|max:255|unique:machine_models,model_no',
            'brands' => 'required|array|min:1',
            'brands.*' => 'exists:brands,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $primaryBrandId = $request->brands[0] ?? null;

        $machineModel = MachineModel::create([
            'model_no' => $request->model_no,
            // `machine_models.brand_id` is NOT NULL in DB, so we keep backward compatibility
            // by storing the first selected brand here.
            'brand_id' => $primaryBrandId,
        ]);

        // Attach multiple brands
        $machineModel->brands()->attach($request->brands);
        $machineModel->machineCategories()->attach($request->categories);

        return redirect()->route('machine-models.index')
            ->with('success', 'Machine model added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineModel $machineModel)
    {
        $request->validate([
            'model_no' => 'required|string|max:255|unique:machine_models,model_no,' . $machineModel->id,
            'brands' => 'required|array|min:1',
            'brands.*' => 'exists:brands,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $primaryBrandId = $request->brands[0] ?? null;

        $machineModel->update([
            'model_no' => $request->model_no,
            'brand_id' => $primaryBrandId,
        ]);

        // Sync multiple brands (replace all existing brands with new ones)
        $machineModel->brands()->sync($request->brands);
        $machineModel->machineCategories()->sync($request->categories);

        return redirect()->route('machine-models.index')
            ->with('success', 'Machine model updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineModel $machineModel)
    {
        $machineModel->delete();

        return redirect()->route('machine-models.index')
            ->with('success', 'Machine model deleted successfully.');
    }
}
