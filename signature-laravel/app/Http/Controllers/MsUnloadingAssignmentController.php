<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Support\MsUnloadingAssignment;
use Illuminate\Http\Request;

class MsUnloadingAssignmentController extends Controller
{
    public function assignableUsers()
    {
        return response()->json(
            MsUnloadingAssignment::assignableUsers()->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
            ])
        );
    }

    public function assign(Request $request, ProformaInvoice $proformaInvoice)
    {
        if (! MsUnloadingAssignment::userSeesAllMsUnloading()) {
            abort(403, 'Only Admin or Super Admin can assign MS Unloading users.');
        }

        $request->validate([
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $userIds = collect($request->input('user_ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $allowedIds = MsUnloadingAssignment::assignableUsers()->pluck('id');

        $invalid = $userIds->diff($allowedIds);
        if ($invalid->isNotEmpty()) {
            return response()->json(['message' => 'One or more selected users cannot be assigned to MS Unloading.'], 422);
        }

        $proformaInvoice->msUnloadingAssignedUsers()->sync($userIds->all());
        $proformaInvoice->load('msUnloadingAssignedUsers');

        $assignedUsers = $proformaInvoice->msUnloadingAssignedUsers->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => $userIds->isEmpty()
                ? 'MS Unloading assignments removed.'
                : 'MS Unloading user(s) assigned successfully.',
            'assigned_users' => $assignedUsers,
            'assigned_label' => $assignedUsers->pluck('name')->join(', ') ?: '—',
        ]);
    }
}
