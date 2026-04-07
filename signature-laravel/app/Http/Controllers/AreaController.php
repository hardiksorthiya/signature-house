<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $query = Area::with('city.state');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('city', fn ($q) => $q->where('name', 'like', '%' . $search . '%')
                      ->orWhereHas('state', fn ($q) => $q->where('name', 'like', '%' . $search . '%')));
            });
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

        $areas = $query->paginate(10)->withQueryString();
        $cities = City::with('state')->orderBy('name')->get();
        return view('areas.index', compact('areas', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        Area::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
        ]);

        return redirect()->route('areas.index')
            ->with('success', 'Area added successfully.');
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        $area->update([
            'name' => $request->name,
            'city_id' => $request->city_id,
        ]);

        return redirect()->route('areas.index')
            ->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Area deleted successfully.');
    }
}
