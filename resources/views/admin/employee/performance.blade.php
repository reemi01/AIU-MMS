@extends('layouts.admin')

@section('title', 'Worker Performance - ' . $employee->user->name)

@push('styles')
<style>
.perf-container { padding: 22px; }
.perf-header { background: white; padding: 24px; border-radius: 8px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 24px; }
.stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.stat-value { font-size: 32px; font-weight: 800; margin: 8px 0; }
.stat-label { font-size: 14px; color: #6b7280; font-weight: 600; }
.chart-container { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 24px; }
.bar-chart { display: flex; align-items: flex-end; gap: 12px; height: 200px; margin-top: 16px; }
.bar { flex: 1; background: linear-gradient(to top, #7c3aed, #a78bfa); border-radius: 4px 4px 0 0; position: relative; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; transition: all 0.3s; }
.bar:hover { opacity: 0.8; }
.bar-label { font-size: 11px; color: #6b7280; margin-top: 8px; text-align: center; }
.bar-value { position: absolute; top: -20px; font-size: 12px; font-weight: 700; }
.task-list { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.task-item { padding: 12px; border-bottom: 1px solid #f3f4f6; }
.task-item:last-child { border-bottom: 0; }
.status-badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.status-badge.pending { background: #fef3c7; color: #92400e; }
.status-badge.inprogress { background: #dbeafe; color: #1e40af; }
.status-badge.completed { background: #d1fae5; color: #065f46; }

@media (max-width: 768px) {
  .perf-container { padding: 12px; }
  .stat-grid { grid-template-columns: 1fr; }
  .bar-chart { height: 150px; }
}

@media print {
  body { background: white !important; }
  .perf-container { padding: 0 !important; }
  a, button, .no-print { display: none !important; }
  .perf-header, .stat-card, .chart-container, .task-list {
    box-shadow: none !important;
    border: 1px solid #e5e7eb;
    page-break-inside: avoid;
  }
  .stat-grid { grid-template-columns: repeat(3, 1fr); }
  @page { margin: 1cm; }
}
</style>
@endpush

@section('content')
<div class="perf-container">
  <div class="perf-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div>
        <h1 style="margin: 0; font-size: 28px; font-weight: 800;">{{ $employee->user->name }}</h1>
        <p style="margin: 4px 0 0; color: #6b7280;">{{ $employee->trade }} • Performance Dashboard</p>
      </div>
      <div style="display:flex;gap:8px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #10b981; color: white; border: 0; border-radius: 6px; cursor: pointer; font-weight: 600;">🖨️ Print</button>
        <a href="{{ route('admin.employees.index') }}" style="padding: 10px 20px; background: #6b7280; color: white; border-radius: 6px; text-decoration: none; font-weight: 600;">← Back</a>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-label">Total Tasks</div>
      <div class="stat-value" style="color: #7c3aed;">{{ $totalTasks }}</div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Completed</div>
      <div class="stat-value" style="color: #10b981;">{{ $completedTasks }}</div>
      <div style="font-size: 12px; color: #6b7280;">
        {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}% of total
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-label">In Progress</div>
      <div class="stat-value" style="color: #3b82f6;">{{ $inProgressTasks }}</div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Pending</div>
      <div class="stat-value" style="color: #f59e0b;">{{ $pendingTasks }}</div>
    </div>

    <div class="stat-card">
      <div class="stat-label">On-Time Rate</div>
      <div class="stat-value" style="color: {{ $onTimeRate >= 80 ? '#10b981' : ($onTimeRate >= 60 ? '#f59e0b' : '#ef4444') }};">{{ $onTimeRate }}%</div>
      <div style="font-size: 12px; color: #6b7280;">
        {{ $completedTasks > 0 ? round(($onTimeRate / 100) * $completedTasks) : 0 }} tasks on time
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Avg Completion</div>
      <div class="stat-value" style="color: #6366f1;">
        @if($avgCompletionDays < 0)
          {{ abs($avgCompletionDays) }}d early
        @elseif($avgCompletionDays > 0)
          {{ $avgCompletionDays }}d late
        @else
          On time
        @endif
      </div>
    </div>
  </div>

  <!-- Monthly Performance Chart -->
  <div class="chart-container">
    <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 700;">Monthly Performance (Last 6 Months)</h3>
    <p style="margin: 0; color: #6b7280; font-size: 14px;">Tasks completed vs total assigned</p>
    <div class="bar-chart">
      @foreach($monthlyStats as $stat)
      <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
        <div class="bar" style="height: {{ $stat['total'] > 0 ? ($stat['completed'] / max(array_column($monthlyStats, 'total'))) * 100 : 0 }}%;">
          <span class="bar-value">{{ $stat['completed'] }}/{{ $stat['total'] }}</span>
        </div>
        <div class="bar-label">{{ $stat['month'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  <!-- Recent Tasks -->
  <div class="task-list">
    <h3 style="margin: 0 0 16px; font-size: 18px; font-weight: 700;">Recent Tasks</h3>
    @forelse($recentTasks as $task)
    <div class="task-item">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 8px;">
        <div style="flex: 1; min-width: 200px;">
          <div style="font-weight: 700; margin-bottom: 4px;">{{ $task->title }}</div>
          <div style="font-size: 13px; color: #6b7280;">
            {{ $task->equipment }} • {{ ucfirst($task->type) }} • {{ ucfirst($task->frequency) }}
          </div>
          @if($task->reports->count() > 0)
          <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
            {{ $task->reports->count() }} report(s) submitted
          </div>
          @endif
        </div>
        <div style="text-align: right;">
          <span class="status-badge {{ $task->status }}">{{ ucfirst($task->status) }}</span>
          <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
            Due: {{ $task->scheduled_date->format('M d, Y') }}
          </div>
          @if($task->completed_at)
          <div style="font-size: 12px; color: #10b981; margin-top: 2px;">
            Completed: {{ $task->completed_at->format('M d, Y') }}
          </div>
          @endif
        </div>
      </div>
    </div>
    @empty
    <div style="text-align: center; color: #9ca3af; padding: 40px;">
      No tasks found for this worker
    </div>
    @endforelse
  </div>
</div>
@endsection
