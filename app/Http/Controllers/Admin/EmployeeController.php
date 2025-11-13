<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Worker::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })->orWhere('trade', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        // Filter by trade
        if ($request->filled('trade')) {
            $query->where('trade', $request->trade);
        }

        $workers = $query->get();
        $totalEmployees = Worker::count();
        $trades = Worker::select('trade')->distinct()->pluck('trade');

        return view('admin.employee.index', compact('workers', 'totalEmployees', 'trades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'username' => 'required|string|min:3|max:255|unique:users|alpha_dash',
            'password' => 'required|string|min:6|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
            'dob' => 'nullable|date|before:today|after:1940-01-01',
            'trade' => 'required|string|min:2|max:255',
        ], [
            'name.required' => 'Full name is required',
            'name.min' => 'Name must be at least 2 characters',
            'username.required' => 'Username is required',
            'username.min' => 'Username must be at least 3 characters',
            'username.unique' => 'This username is already taken',
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'email.email' => 'Please enter a valid email address',
            'phone.regex' => 'Please enter a valid phone number',
            'dob.before' => 'Date of birth must be in the past',
            'dob.after' => 'Please enter a valid date of birth',
            'trade.required' => 'Trade/Role is required',
        ]);

        // Create user
        $user = User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
            'email' => $validated['email'] ?? null,
            'role' => 'worker',
        ]);

        // Create worker
        Worker::query()->create([
            'user_id' => $user->id,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'trade' => $validated['trade'],
            'tasks_assigned' => 0,
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee added successfully!');
    }

    public function update(Request $request, Worker $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'username' => 'required|string|min:3|max:255|alpha_dash|unique:users,username,'.$employee->user_id,
            'password' => 'nullable|string|min:6|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
            'dob' => 'nullable|date|before:today|after:1940-01-01',
            'trade' => 'required|string|min:2|max:255',
        ], [
            'name.required' => 'Full name is required',
            'name.min' => 'Name must be at least 2 characters',
            'username.required' => 'Username is required',
            'username.min' => 'Username must be at least 3 characters',
            'username.unique' => 'This username is already taken',
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores',
            'password.min' => 'Password must be at least 6 characters',
            'email.email' => 'Please enter a valid email address',
            'phone.regex' => 'Please enter a valid phone number',
            'dob.before' => 'Date of birth must be in the past',
            'dob.after' => 'Please enter a valid date of birth',
            'trade.required' => 'Trade/Role is required',
        ]);

        // Update user
        $employee->user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
        ]);

        // Update password if provided
        if (! empty($validated['password'])) {
            $employee->user->update([
                'password' => Hash::make($validated['password']),
                'plain_password' => $validated['password'],
            ]);
        }

        // Update worker
        $employee->update([
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'trade' => $validated['trade'],
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    public function destroy(Worker $employee)
    {
        $employee->user->delete(); // This will cascade delete the worker

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully!');
    }

    public function performance(Worker $employee)
    {
        $employee->load('user');

        // Get all tasks for this worker
        $allTasks = Task::where('worker_id', $employee->id)->get();

        // Calculate statistics
        $totalTasks = $allTasks->count();
        $completedTasks = $allTasks->where('status', 'completed')->count();
        $inProgressTasks = $allTasks->where('status', 'inprogress')->count();
        $pendingTasks = $allTasks->where('status', 'pending')->count();

        // Calculate on-time completion rate
        $completedOnTime = $allTasks->filter(fn($task) => $task->status === 'completed' &&
               $task->completed_at &&
               $task->completed_at->lte($task->scheduled_date))->count();

        $onTimeRate = $completedTasks > 0 ? round(($completedOnTime / $completedTasks) * 100) : 0;

        // Calculate average completion time
        $avgCompletionDays = $allTasks->where('status', 'completed')
            ->filter(fn($task) => $task->completed_at && $task->scheduled_date)
            ->avg(fn($task) => $task->scheduled_date->diffInDays($task->completed_at, false));

        $avgCompletionDays = $avgCompletionDays ? round($avgCompletionDays, 1) : 0;

        // Recent tasks
        $recentTasks = Task::where('worker_id', $employee->id)
            ->with('reports')
            ->latest()
            ->take(10)
            ->get();

        // Monthly performance (last 6 months)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthTasks = Task::where('worker_id', $employee->id)
                ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
                ->get();

            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'total' => $monthTasks->count(),
                'completed' => $monthTasks->where('status', 'completed')->count(),
            ];
        }

        return view('admin.employee.performance', compact(
            'employee',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'pendingTasks',
            'onTimeRate',
            'avgCompletionDays',
            'recentTasks',
            'monthlyStats'
        ));
    }
}
