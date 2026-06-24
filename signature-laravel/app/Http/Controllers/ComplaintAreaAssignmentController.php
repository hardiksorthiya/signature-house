<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Support\ComplaintAreaAssignment;
use Illuminate\Http\Request;

class ComplaintAreaAssignmentController extends Controller
{
    public function index()
    {
        $this->authorize('view complain');

        if (! ComplaintAreaAssignment::userManagesAreaAssignments()) {
            abort(403, 'Only Admin or Super Admin can manage complaint area assignments.');
        }

        $users = ComplaintAreaAssignment::assignableUsers();
        $areas = Area::with(['city.state'])
            ->orderBy('name')
            ->get();

        return view('complaints.area-assignment', compact('users', 'areas'));
    }

    public function update(Request $request)
    {
        $this->authorize('edit complain');

        if (! ComplaintAreaAssignment::userManagesAreaAssignments()) {
            abort(403, 'Only Admin or Super Admin can manage complaint area assignments.');
        }

        $request->validate([
            'assignments' => 'nullable|array',
            'assignments.*' => 'nullable|array',
            'assignments.*.*' => 'integer|exists:areas,id',
        ]);

        $assignments = $request->input('assignments', []);

        foreach (ComplaintAreaAssignment::assignableUsers() as $user) {
            $areaIds = collect($assignments[$user->id] ?? [])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
            $user->complaintAreas()->sync($areaIds);
        }

        return redirect()
            ->route('complaints.area-assignment')
            ->with('success', 'Complaint area assignments saved successfully.');
    }
}
