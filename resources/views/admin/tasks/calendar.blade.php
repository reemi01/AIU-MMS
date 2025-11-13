@extends('layouts.admin')

@section('title', 'Task Calendar')

@push('styles')
<style>
.calendar-container { padding: 22px; }
.calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; background: white; padding: 18px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.calendar-nav { display: flex; gap: 12px; align-items: center; }
.calendar-nav button, .calendar-nav a { padding: 8px 16px; background: #7c3aed; color: white; border: 0; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 600; }
.calendar-nav button:hover, .calendar-nav a:hover { background: #6d28d9; }
.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background: #e5e7eb; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.calendar-day-header { background: #f9fafb; padding: 12px; text-align: center; font-weight: 700; font-size: 14px; color: #6b7280; }
.calendar-day { background: white; min-height: 120px; padding: 8px; position: relative; }
.calendar-day.other-month { background: #f9fafb; color: #9ca3af; }
.calendar-day.today { background: #ede9fe; border: 2px solid #7c3aed; }
.day-number { font-weight: 700; font-size: 14px; margin-bottom: 6px; }
.task-item { background: #dbeafe; color: #1e40af; padding: 4px 6px; border-radius: 4px; font-size: 11px; margin-bottom: 4px; cursor: pointer; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; border-left: 3px solid #3b82f6; position: relative; }
.task-item.pending { background: #fef3c7; color: #92400e; border-left-color: #f59e0b; }
.task-item.inprogress { background: #dbeafe; color: #1e40af; border-left-color: #3b82f6; }
.task-item.completed { background: #d1fae5; color: #065f46; border-left-color: #10b981; }
.task-item.overdue { background: #fee2e2; color: #991b1b; border-left-color: #ef4444; }
.task-item.weekly::after { content: "W"; position: absolute; top: 1px; right: 2px; font-size: 8px; font-weight: 800; background: #8b5cf6; color: white; border-radius: 3px; padding: 1px 3px; }
.task-item.monthly::after { content: "M"; position: absolute; top: 1px; right: 2px; font-size: 8px; font-weight: 800; background: #ec4899; color: white; border-radius: 3px; padding: 1px 3px; }
.task-item:hover { opacity: 0.8; }
.task-count { font-size: 10px; color: #6b7280; margin-top: 4px; }

@media (max-width: 768px) {
  .calendar-container { padding: 12px; }
  .calendar-header { flex-direction: column; gap: 12px; }
  .calendar-day { min-height: 80px; padding: 4px; }
  .day-number { font-size: 12px; }
  .task-item { font-size: 10px; padding: 2px 4px; }
}
</style>
@endpush

@section('content')
<div class="calendar-container">
  <div class="calendar-header">
    <h1 style="margin: 0; font-size: 28px; font-weight: 800;">Task Calendar</h1>
    <div class="calendar-nav">
      <a href="{{ route('admin.tasks.index') }}" style="background:#6b7280">← Back to Tasks</a>
      <a href="{{ route('admin.tasks.calendar', ['month' => $startOfMonth->copy()->subMonth()->month, 'year' => $startOfMonth->copy()->subMonth()->year]) }}">← Previous</a>
      <span style="font-weight: 700; font-size: 18px; padding: 0 12px;">{{ $startOfMonth->format('F Y') }}</span>
      <a href="{{ route('admin.tasks.calendar', ['month' => $startOfMonth->copy()->addMonth()->month, 'year' => $startOfMonth->copy()->addMonth()->year]) }}">Next →</a>
      <a href="{{ route('admin.tasks.calendar') }}">Today</a>
    </div>
  </div>

  <!-- Calendar Filters -->
  <form method="GET" style="background:white;padding:16px;border-radius:8px;margin-bottom:16px;box-shadow:0 2px 8px rgba(0,0,0,0.05)">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px">
      <input type="hidden" name="month" value="{{ $month }}">
      <input type="hidden" name="year" value="{{ $year }}">

      <select name="status" class="input" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:6px" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="inprogress" {{ request('status') == 'inprogress' ? 'selected' : '' }}>In Progress</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
      </select>

      <select name="type" class="input" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:6px" onchange="this.form.submit()">
        <option value="">All Equipment</option>
        <option value="Lift" {{ request('type') == 'Lift' ? 'selected' : '' }}>Lift</option>
        <option value="Chiller" {{ request('type') == 'Chiller' ? 'selected' : '' }}>Chiller</option>
      </select>

      <select name="frequency" class="input" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:6px" onchange="this.form.submit()">
        <option value="">All Frequencies</option>
        <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
        <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
      </select>

      @if(request()->hasAny(['status', 'type', 'frequency']))
      <a href="{{ route('admin.tasks.calendar', ['month' => $month, 'year' => $year]) }}" style="padding:8px 16px;background:#6b7280;color:white;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-weight:600">Clear Filters</a>
      @endif
    </div>
  </form>

  <div class="calendar-grid">
    <!-- Day headers -->
    <div class="calendar-day-header">Sun</div>
    <div class="calendar-day-header">Mon</div>
    <div class="calendar-day-header">Tue</div>
    <div class="calendar-day-header">Wed</div>
    <div class="calendar-day-header">Thu</div>
    <div class="calendar-day-header">Fri</div>
    <div class="calendar-day-header">Sat</div>

    @php
    $firstDayOfWeek = $startOfMonth->copy()->startOfWeek();
    $lastDayOfWeek = $endOfMonth->copy()->endOfWeek();
    $currentDate = $firstDayOfWeek->copy();
    @endphp

    @while($currentDate <= $lastDayOfWeek)
      @php
      $isToday = $currentDate->isToday();
      $isCurrentMonth = $currentDate->month == $month;
      $dateKey = $currentDate->format('Y-m-d');
      $dayTasks = $tasks->get($dateKey, collect());
      @endphp

      <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
        <div class="day-number">{{ $currentDate->day }}</div>

        @foreach($dayTasks->take(3) as $task)
          @php
          $isOverdue = in_array($task->status, ['pending', 'inprogress']) && $task->scheduled_date->isPast();
          $statusClass = $isOverdue ? 'overdue' : $task->status;
          $frequencyClass = $task->frequency ?? '';
          @endphp
          <a href="{{ route('admin.tasks.show', ['task' => $task->id]) }}" class="task-item {{ $statusClass }} {{ $frequencyClass }}" title="{{ $task->title }} - {{ $task->equipment }} ({{ ucfirst($task->frequency) }})" style="text-decoration: none;">
            {{ $task->equipment }}
          </a>
        @endforeach

        @if($dayTasks->count() > 3)
          <div class="task-count">+{{ $dayTasks->count() - 3 }} more</div>
        @endif
      </div>

      @php
      $currentDate->addDay();
      @endphp
    @endwhile
  </div>

  <!-- Legend -->
  <div style="background: white; padding: 18px; border-radius: 8px; margin-top: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <h3 style="margin: 0 0 12px; font-size: 16px; font-weight: 700;">Legend</h3>
    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <div style="width: 24px; height: 24px; background: #fee2e2; border-left: 3px solid #ef4444; border-radius: 4px;"></div>
        <span>Overdue</span>
      </div>
      <div style="display: flex; align-items: center; gap: 8px;">
        <div style="width: 24px; height: 24px; background: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 4px;"></div>
        <span>Pending</span>
      </div>
      <div style="display: flex; align-items: center; gap: 8px;">
        <div style="width: 24px; height: 24px; background: #dbeafe; border-left: 3px solid #3b82f6; border-radius: 4px;"></div>
        <span>In Progress</span>
      </div>
      <div style="display: flex; align-items: center; gap: 8px;">
        <div style="width: 24px; height: 24px; background: #d1fae5; border-left: 3px solid #10b981; border-radius: 4px;"></div>
        <span>Completed</span>
      </div>
      <div style="display: flex; align-items: center; gap: 8px;">
        <div style="width: 24px; height: 24px; background: #dbeafe; border-left: 3px solid #3b82f6; border-radius: 4px; position: relative;">
          <span style="position: absolute; top: 1px; right: 2px; font-size: 8px; font-weight: 800; background: #8b5cf6; color: white; border-radius: 3px; padding: 1px 3px;">W</span>
        </div>
        <span>Weekly Recurring</span>
      </div>
      <div style="display: flex; align-items: center; gap: 8px;">
        <div style="width: 24px; height: 24px; background: #dbeafe; border-left: 3px solid #3b82f6; border-radius: 4px; position: relative;">
          <span style="position: absolute; top: 1px; right: 2px; font-size: 8px; font-weight: 800; background: #ec4899; color: white; border-radius: 3px; padding: 1px 3px;">M</span>
        </div>
        <span>Monthly Recurring</span>
      </div>
    </div>
  </div>
</div>
@endsection
