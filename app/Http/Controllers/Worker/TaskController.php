<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $worker = auth()->user()->worker;
        $tasks = Task::where('worker_id', $worker->id)
            ->with('reports')
            ->orderBy('scheduled_date', 'asc')
            ->get();

        return view('worker.tasks', compact('tasks'));
    }

    public function schedule()
    {
        $worker = auth()->user()->worker;
        $tasks = Task::where('worker_id', $worker->id)
            ->with('reports')
            ->orderBy('scheduled_date', 'asc')
            ->get()
            ->groupBy(fn($task) => $task->scheduled_date->format('Y-m-d'));

        return view('worker.schedule', compact('tasks'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        // Ensure the task belongs to the authenticated worker
        $worker = auth()->user()->worker;

        if (! $worker || $task->worker_id !== $worker->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,inprogress,completed',
            'proof' => 'nullable|string',
            'note' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Please select a status',
            'note.max' => 'Note cannot exceed 500 characters',
        ]);

        $task->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
            'proof' => $validated['proof'] ?? $task->proof,
        ]);

        // Create report if note or proof is provided
        if (! empty($validated['note']) || ! empty($validated['proof'])) {
            $task->reports()->create([
                'worker_id' => $worker->id,
                'note' => $validated['note'] ?? null,
                'image' => $validated['proof'] ?? null,
            ]);
        }

        return redirect()->route('worker.dashboard')
            ->with('success', 'Task status updated successfully!');
    }
}
