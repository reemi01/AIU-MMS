<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chiller;
use App\Models\Equipment;
use App\Models\Lift;
use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): Factory|View
    {
        $query = Task::with('worker.user');

        // Search by title or equipment
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('equipment', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by worker
        if ($request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Order by priority (urgent first) then by date
        $tasks = $query->orderByRaw("CASE priority
            WHEN 'urgent' THEN 1
            WHEN 'high' THEN 2
            WHEN 'normal' THEN 3
            WHEN 'low' THEN 4
            END")
            ->orderBy('scheduled_date')
            ->get();

        $workers = Worker::with('user')->get();
        $lifts = Lift::all();
        $chillers = Chiller::all();
        $equipments = Equipment::all();
        $taskTemplates = TaskTemplate::all();

        return view('admin.tasks.index', compact('tasks', 'workers', 'equipments', 'lifts', 'chillers', 'taskTemplates'));
    }

    public function show(Task $task): Factory|View
    {
        $task->load(['worker.user', 'lift', 'chiller']);

        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task): Factory|View
    {
        $workers = Worker::with('user')->get();
        $lifts = Lift::all();
        $chillers = Chiller::all();
        $taskTemplates = TaskTemplate::all();

        return view('admin.tasks.edit', compact('task', 'workers', 'lifts', 'chillers', 'taskTemplates'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'task_template_id' => 'required|integer|exists:task_templates,id',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:Lift,Chiller',
            'equipment' => 'required|string|max:255',
            'frequency' => 'required|in:weekly,monthly',
            'priority' => 'required|in:low,normal,high,urgent',
            'worker_id' => 'required|integer|exists:workers,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:pending,inprogress,completed',
        ]);

        // Get the task template to retrieve the title
        $taskTemplate = TaskTemplate::query()->find($validated['task_template_id']);
        if (! $taskTemplate) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['task_template_id' => 'Selected task template does not exist']);
        }

        $validated['title'] = $taskTemplate->title;

        // Use template description if no custom description provided
        if (empty($validated['description'])) {
            $validated['description'] = $taskTemplate->description;
        }

        // Get lift or chiller ID based on equipment name
        if ($validated['type'] === 'Lift') {
            $lift = Lift::query()->where('name', $validated['equipment'])->first();
            if (! $lift) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['equipment' => 'Selected lift does not exist']);
            }
            $validated['lift_id'] = $lift->id;
            $validated['chiller_id'] = null;
        } else {
            $chiller = Chiller::query()->where('name', $validated['equipment'])->first();
            if (! $chiller) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['equipment' => 'Selected chiller does not exist']);
            }
            $validated['chiller_id'] = $chiller->id;
            $validated['lift_id'] = null;
        }

        // Handle worker reassignment
        if ($task->worker_id !== $validated['worker_id']) {
            // Decrement old worker's task count
            if ($task->worker_id) {
                Worker::query()->find($task->worker_id)?->decrement('tasks_assigned');
            }
            // Increment new worker's task count
            Worker::query()->find($validated['worker_id'])?->increment('tasks_assigned');
        }

        // Handle status change to completed
        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('admin.tasks.show', $task)
            ->with('success', 'Task updated successfully!');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,inprogress,completed',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->back()
            ->with('success', 'Task status updated to '.ucfirst($validated['status']).'!');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_template_id' => 'required|integer|exists:task_templates,id',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:Lift,Chiller',
            'equipment' => 'required|string|max:255',
            'frequency' => 'required|in:weekly,monthly',
            'priority' => 'required|in:low,normal,high,urgent',
            'worker_id' => 'required|integer|exists:workers,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'nullable|date_format:H:i',
        ], [
            'task_template_id.required' => 'Please select a task template',
            'task_template_id.exists' => 'Selected task template does not exist',
            'type.required' => 'Please select equipment type',
            'equipment.required' => 'Please select equipment',
            'worker_id.required' => 'Please assign a worker',
            'worker_id.exists' => 'Selected worker does not exist',
            'scheduled_date.required' => 'Scheduled date is required',
            'scheduled_date.after_or_equal' => 'Scheduled date cannot be in the past',
        ]);

        // Get the task template to retrieve the title
        $taskTemplate = TaskTemplate::query()->find($validated['task_template_id']);
        if (! $taskTemplate) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['task_template_id' => 'Selected task template does not exist']);
        }

        $validated['title'] = $taskTemplate->title;

        // Use template description if no custom description provided
        if (empty($validated['description'])) {
            $validated['description'] = $taskTemplate->description;
        }

        // Get lift or chiller ID based on equipment name
        if ($validated['type'] === 'Lift') {
            $lift = Lift::query()->where('name', $validated['equipment'])->first();
            if (! $lift) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['equipment' => 'Selected lift does not exist']);
            }
            $validated['lift_id'] = $lift->id;
            $validated['chiller_id'] = null;
        } else {
            $chiller = Chiller::query()->where('name', $validated['equipment'])->first();
            if (! $chiller) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['equipment' => 'Selected chiller does not exist']);
            }
            $validated['chiller_id'] = $chiller->id;
            $validated['lift_id'] = null;
        }

        $validated['status'] = 'pending';
        $task = Task::query()->create($validated);

        // Update worker tasks_assigned count
        $worker = Worker::query()->find($validated['worker_id']);
        $worker?->increment('tasks_assigned');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Task created and assigned successfully!');
    }

    public function destroy(Task $task): RedirectResponse
    {
        if ($task->worker_id) {
            $worker = Worker::query()->find($task->worker_id);
            $worker->decrement('tasks_assigned');
        }

        $task->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Task removed successfully!');
    }

    public function calendar(Request $request): Factory|View
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startOfMonth = Carbon::create($year, $month)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month)->endOfMonth();

        // Get all tasks scheduled in this month with filters
        $scheduledQuery = Task::with(['worker.user'])
            ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth]);

        // Apply filters
        if ($request->filled('status')) {
            $scheduledQuery->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $scheduledQuery->where('type', $request->type);
        }
        if ($request->filled('frequency')) {
            $scheduledQuery->where('frequency', $request->frequency);
        }

        $scheduledTasks = $scheduledQuery->orderBy('scheduled_date')->get();

        // Get all recurring tasks (weekly and monthly) - fetch once with filters
        $weeklyQuery = Task::with(['worker.user'])->where('frequency', 'weekly');
        $monthlyQuery = Task::with(['worker.user'])->where('frequency', 'monthly');

        // Apply same filters to recurring tasks
        if ($request->filled('status')) {
            $weeklyQuery->where('status', $request->status);
            $monthlyQuery->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $weeklyQuery->where('type', $request->type);
            $monthlyQuery->where('type', $request->type);
        }

        $weeklyTasks = $weeklyQuery->get();
        $monthlyTasks = $monthlyQuery->get();

        // Generate recurring task instances
        $firstDayOfWeek = $startOfMonth->copy()->startOfWeek();
        $lastDayOfWeek = $endOfMonth->copy()->endOfWeek();

        $recurringTasks = collect();

        // For weekly tasks - appear every week on the same day of week as original
        // Calculate number of weeks in the calendar view
        $weeksInView = $firstDayOfWeek->diffInWeeks($lastDayOfWeek) + 1;

        foreach ($weeklyTasks as $task) {
            // Get the day of week from original scheduled date (0=Sunday, 6=Saturday)
            $taskDayOfWeek = $task->scheduled_date->dayOfWeek;

            // For each week in the calendar, create an instance on the same day of week
            for ($week = 0; $week < $weeksInView; $week++) {
                $weekStart = $firstDayOfWeek->copy()->addWeeks($week);
                $taskDate = $weekStart->copy()->addDays($taskDayOfWeek);

                if ($taskDate >= $firstDayOfWeek && $taskDate <= $lastDayOfWeek) {
                    $virtualTask = $task->replicate();
                    $virtualTask->id = $task->id;
                    $virtualTask->scheduled_date = $taskDate->copy();
                    $recurringTasks->push($virtualTask);
                }
            }
        }

        // For monthly tasks - appear once per month on the same date
        $currentDate = $firstDayOfWeek->copy();
        while ($currentDate <= $lastDayOfWeek) {
            foreach ($monthlyTasks as $task) {
                // Check if the task's original day of month matches current date
                $taskDayOfMonth = $task->scheduled_date->day;
                if ($currentDate->day === $taskDayOfMonth) {
                    // Create a virtual instance for this date
                    $virtualTask = $task->replicate();
                    $virtualTask->id = $task->id;
                    $virtualTask->scheduled_date = $currentDate->copy();
                    $recurringTasks->push($virtualTask);
                }
            }
            $currentDate->addDay();
        }

        // Merge scheduled and recurring tasks
        $allTasks = $scheduledTasks->merge($recurringTasks);

        // Group by date
        $tasks = $allTasks->groupBy(fn($task) => $task->scheduled_date->format('Y-m-d'));

        return view('admin.tasks.calendar', compact('tasks', 'month', 'year', 'startOfMonth', 'endOfMonth'));
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        $deletedCount = 0;
        foreach ($validated['task_ids'] as $taskId) {
            $task = Task::query()->find($taskId);
            if ($task && $task->worker_id) {
                $worker = Worker::query()->find($task->worker_id);
                $worker?->decrement('tasks_assigned');
            }
            $task?->delete();
            $deletedCount++;
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', "Successfully deleted $deletedCount task(s)");
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'exists:tasks,id',
            'status' => 'required|in:pending,inprogress,completed',
        ]);

        $updatedCount = Task::query()->whereIn('id', $validated['task_ids'])
            ->update([
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === 'completed' ? now() : null,
            ]);

        return redirect()->route('admin.tasks.index')
            ->with('success', "Successfully updated $updatedCount task(s) to ".ucfirst((string) $validated['status']));
    }

    public function bulkAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'exists:tasks,id',
            'worker_id' => 'required|exists:workers,id',
        ]);

        // Decrement old workers' task counts
        $tasks = Task::query()->whereIn('id', $validated['task_ids'])->get();
        foreach ($tasks as $task) {
            if ($task->worker_id) {
                Worker::query()->find($task->worker_id)?->decrement('tasks_assigned');
            }
        }

        // Update tasks with new worker
        $updatedCount = Task::query()->whereIn('id', $validated['task_ids'])
            ->update(['worker_id' => $validated['worker_id']]);

        // Increment new worker's task count
        Worker::query()->find($validated['worker_id'])?->increment('tasks_assigned', $updatedCount);

        $worker = Worker::with('user')->find($validated['worker_id']);

        return redirect()->route('admin.tasks.index')
            ->with('success', "Successfully assigned $updatedCount task(s) to ".$worker->user->name);
    }
}
