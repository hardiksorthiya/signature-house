<?php

namespace App\Http\Controllers;

use App\Models\PortOfDestination;
use Illuminate\Http\Request;

class PortOfDestinationController extends Controller
{
    /**
     * Display a listing of port of destinations
     */
    public function index(Request $request)
    {
        $query = PortOfDestination::query();

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $sort = $request->get('sort', 'name_asc');
        match ($sort) {
            'name_desc' => $query->orderBy('name', 'desc'),
            'date_asc' => $query->orderBy('created_at', 'asc'),
            'date_desc' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };

        $ports = $query->paginate(10)->withQueryString();
        return view('port-of-destinations.index', compact('ports'));
    }

    /**
     * Store a newly created port of destination
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:port_of_destinations,name',
            'code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $port = PortOfDestination::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'port' => $port,
                'message' => 'Port of Destination added successfully.'
            ]);
        }

        return redirect()->route('port-of-destinations.index')
            ->with('success', 'Port of Destination added successfully.');
    }

    /**
     * Update the specified port of destination
     */
    public function update(Request $request, PortOfDestination $portOfDestination)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:port_of_destinations,name,' . $portOfDestination->id,
            'code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $portOfDestination->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->route('port-of-destinations.index')
            ->with('success', 'Port of Destination updated successfully.');
    }

    /**
     * Remove the specified port of destination
     */
    public function destroy(PortOfDestination $portOfDestination)
    {
        $portOfDestination->delete();

        return redirect()->route('port-of-destinations.index')
            ->with('success', 'Port of Destination deleted successfully.');
    }
}
