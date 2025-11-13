<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Worker;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard(Request $request)
    {
        $totalTasks = Task::count();
        $totalWorkers = Worker::count();
        $completedTasks = Task::completed()->count();

        return view('admin.dashboard', compact(
            'totalTasks',
            'totalWorkers',
            'completedTasks'
        ));
    }

    public function workerDashboard(): Factory|View
    {
        $user = auth()->user();
        $worker = $user->worker;

        if (! $worker) {
            abort(403, 'Worker profile not found');
        }

        $tasks = Task::where('worker_id', $worker->id)
            ->latest()
            ->get();

        $inProgressTasks = $tasks->where('status', 'inprogress')->count();
        $completedTasks = $tasks->where('status', 'completed')->count();

        return view('worker.dashboard', compact(
            'tasks',
            'inProgressTasks',
            'completedTasks',
            'worker'
        ));
    }
}
