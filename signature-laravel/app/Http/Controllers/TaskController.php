<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $sort = $request->get('sort', 'date_desc');
        match ($sort) {
            'name_asc' => $query->orderBy('title', 'asc'),
            'name_desc' => $query->orderBy('title', 'desc'),
            'date_asc' => $query->orderBy('due_date', 'asc')->orderBy('created_at', 'asc'),
            'date_desc' => $query->orderBy('due_date', 'desc')->orderBy('created_at', 'desc'),
            default => $query->orderBy('due_date', 'asc')->orderBy('priority', 'desc')->orderBy('created_at', 'desc'),
        };

        $tasks = $query->paginate(10)->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:0,1,2',
        ]);

        Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Ensure user can only view their own tasks
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // Ensure user can only edit their own tasks
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Ensure user can only update their own tasks
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:0,1,2',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Ensure user can only delete their own tasks
        if ($task->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
