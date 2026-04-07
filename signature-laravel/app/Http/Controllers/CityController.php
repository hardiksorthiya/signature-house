<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::with('state');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('state', fn ($q) => $q->where('name', 'like', '%' . $search . '%'));
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

        $cities = $query->paginate(10)->withQueryString();
        $states = State::orderBy('name')->get();
        return view('cities.index', compact('cities', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        City::create([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return redirect()->route('cities.index')
            ->with('success', 'City added successfully.');
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $city->update([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return redirect()->route('cities.index')
            ->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('cities.index')
            ->with('success', 'City deleted successfully.');
    }
}
