<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Worker;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Build base query with optimized eager loading
        $query = Task::with([
            'worker:id,user_id,trade',
            'worker.user:id,name',
            'reports:id,task_id,note,created_at',
        ])->select('id', 'title', 'equipment', 'type', 'frequency', 'status', 'description', 'worker_id', 'completed_at', 'scheduled_date', 'proof');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }

        if ($request->filled('equipment')) {
            $query->where('equipment', 'like', '%'.$request->equipment.'%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        // If filters are applied, get filtered tasks (limited to 50 for better performance)
        if ($request->hasAny(['status', 'type', 'frequency', 'worker_id', 'equipment', 'date_from', 'date_to'])) {
            $tasks = $query->latest()->limit(50)->get();
            $liftWeekly = collect();
            $liftMonthly = collect();
            $chillerWeekly = collect();
            $chillerMonthly = collect();
        } else {
            // No filters: load tasks grouped by type/frequency (reduced limit for performance)
            $liftWeekly = Task::with([
                'worker:id,user_id,trade',
                'worker.user:id,name',
                'reports:id,task_id,note,created_at',
            ])
                ->select('id', 'title', 'equipment', 'type', 'frequency', 'status', 'description', 'worker_id', 'completed_at', 'scheduled_date', 'proof')
                ->where('type', 'Lift')
                ->where('frequency', 'weekly')
                ->latest()
                ->limit(25)
                ->get();

            $liftMonthly = Task::with([
                'worker:id,user_id,trade',
                'worker.user:id,name',
                'reports:id,task_id,note,created_at',
            ])
                ->select('id', 'title', 'equipment', 'type', 'frequency', 'status', 'description', 'worker_id', 'completed_at', 'scheduled_date', 'proof')
                ->where('type', 'Lift')
                ->where('frequency', 'monthly')
                ->latest()
                ->limit(25)
                ->get();

            $chillerWeekly = Task::with([
                'worker:id,user_id,trade',
                'worker.user:id,name',
                'reports:id,task_id,note,created_at',
            ])
                ->select('id', 'title', 'equipment', 'type', 'frequency', 'status', 'description', 'worker_id', 'completed_at', 'scheduled_date', 'proof')
                ->where('type', 'Chiller')
                ->where('frequency', 'weekly')
                ->latest()
                ->limit(25)
                ->get();

            $chillerMonthly = Task::with([
                'worker:id,user_id,trade',
                'worker.user:id,name',
                'reports:id,task_id,note,created_at',
            ])
                ->select('id', 'title', 'equipment', 'type', 'frequency', 'status', 'description', 'worker_id', 'completed_at', 'scheduled_date', 'proof')
                ->where('type', 'Chiller')
                ->where('frequency', 'monthly')
                ->latest()
                ->limit(25)
                ->get();

            $tasks = collect();
        }

        // Load workers list with only needed columns
        $workers = Worker::select('id', 'user_id', 'trade')->with('user:id,name')->get();

        return view('admin.reports.index', compact(
            'liftWeekly',
            'liftMonthly',
            'chillerWeekly',
            'chillerMonthly',
            'tasks',
            'workers'
        ));
    }
}
