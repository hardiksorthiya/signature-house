<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $query = Status::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
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

        $statuses = $query->paginate(10)->withQueryString();
        return view('statuses.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name',
            'requires_scheduling' => 'nullable|boolean',
        ]);

        Status::create([
            'name' => $request->name,
            'requires_scheduling' => $request->has('requires_scheduling') ? (bool)$request->requires_scheduling : false,
        ]);

        return redirect()->route('statuses.index')
            ->with('success', 'Status added successfully.');
    }

    public function update(Request $request, Status $status)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name,' . $status->id,
            'requires_scheduling' => 'nullable|boolean',
        ]);

        $status->update([
            'name' => $request->name,
            'requires_scheduling' => $request->has('requires_scheduling') ? (bool)$request->requires_scheduling : false,
        ]);

        return redirect()->route('statuses.index')
            ->with('success', 'Status updated successfully.');
    }

    public function destroy(Status $status)
    {
        $status->delete();

        return redirect()->route('statuses.index')
            ->with('success', 'Status deleted successfully.');
    }
}
