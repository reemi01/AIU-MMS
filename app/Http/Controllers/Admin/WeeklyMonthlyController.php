<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;

class WeeklyMonthlyController extends Controller
{
    public function index()
    {
        $liftWeekly = TaskTemplate::where('type', 'Lift')->where('frequency', 'weekly')->get();
        $liftMonthly = TaskTemplate::where('type', 'Lift')->where('frequency', 'monthly')->get();
        $chillerWeekly = TaskTemplate::where('type', 'Chiller')->where('frequency', 'weekly')->get();
        $chillerMonthly = TaskTemplate::where('type', 'Chiller')->where('frequency', 'monthly')->get();

        return view('admin.weekly-monthly.index', compact(
            'liftWeekly',
            'liftMonthly',
            'chillerWeekly',
            'chillerMonthly'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:Lift,Chiller',
            'frequency' => 'required|in:weekly,monthly',
        ], [
            'title.required' => 'Task template title is required',
            'title.min' => 'Title must be at least 3 characters',
            'type.required' => 'Please select equipment type',
            'frequency.required' => 'Please select frequency',
        ]);

        TaskTemplate::create($validated);

        return redirect()->route('admin.weekly-monthly.index')
            ->with('success', 'Task template added successfully!');
    }

    public function destroy(TaskTemplate $taskTemplate)
    {
        $taskTemplate->delete();

        return redirect()->route('admin.weekly-monthly.index')
            ->with('success', 'Task template removed successfully!');
    }
}
