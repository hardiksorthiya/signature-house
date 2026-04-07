<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read and redirect to the relevant page.
     */
    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $data = $notification->data ?? [];
        $contractId = $data['contract_id'] ?? null;
        $taskId = $data['task_id'] ?? null;
        $leadId = $data['lead_id'] ?? null;

        if ($contractId) {
            return redirect()->route('contracts.pending-approval');
        }
        if ($taskId) {
            return redirect()->route('tasks.show', $taskId);
        }
        if ($leadId) {
            return redirect()->route('leads.show', $leadId);
        }

        return redirect()->route('notifications.index');
    }
}
