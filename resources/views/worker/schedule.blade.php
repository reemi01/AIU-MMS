@extends('layouts.worker')

@section('title', 'My Schedule')

@push('styles')
<style>
.main{flex:1;padding:22px}
.header{background:white;padding:24px;border-radius:12px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.schedule-card{background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05);margin-bottom:24px}
.day-group{margin-bottom:32px;display:flex;gap:20px;align-items:flex-start}
.day-group:last-child{margin-bottom:0}
.date-label{width:180px;flex-shrink:0}
.date-label .day-name{font-size:18px;font-weight:700;color:#111827;margin-bottom:4px}
.date-label .date-text{font-size:14px;color:#6b7280}
.task-list{flex:1;display:flex;flex-direction:column;gap:12px}
.task-item{background:white;border:1px solid #e5e7eb;padding:16px;border-radius:10px;display:flex;justify-content:space-between;align-items:center;transition:all 0.2s}
.task-item:hover{box-shadow:0 4px 12px rgba(0,0,0,0.08);border-color:#7c3aed}
.task-left{display:flex;gap:14px;align-items:center;flex:1;min-width:0}
.task-icon{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,#eef2ff,#f8fafc);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:#7c3aed;flex-shrink:0}
.task-info{min-width:0;flex:1}
.task-title{font-weight:700;font-size:16px;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.task-meta{display:flex;gap:12px;font-size:13px;color:#6b7280;flex-wrap:wrap}
.task-right{display:flex;gap:12px;align-items:center;flex-shrink:0}
.status-badge{padding:8px 14px;border-radius:20px;font-size:13px;font-weight:700;white-space:nowrap}
.status-pending{background:#fef3c7;color:#92400e}
.status-inprogress{background:#dbeafe;color:#1e40af}
.status-completed{background:#d1fae5;color:#065f46}
.open-link{color:#7c3aed;font-size:14px;font-weight:600;text-decoration:none;padding:8px 12px;border-radius:6px;transition:all 0.2s}
.open-link:hover{background:#f3f4f6}
.tips-card{background:linear-gradient(135deg,#eef2ff,#fef3c7);padding:20px;border-radius:12px;margin-top:24px}
.empty-state{text-align:center;padding:60px 20px;background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.empty-state .icon{font-size:64px;margin-bottom:16px}
@media (max-width:768px){
  .main{padding:12px}
  .header,.schedule-card{padding:16px}
  .day-group{flex-direction:column;gap:12px}
  .date-label{width:100%}
  .task-item{flex-direction:column;align-items:flex-start;gap:12px}
  .task-right{width:100%;justify-content:space-between}
  .task-meta{flex-direction:column;gap:4px}
}
</style>
@endpush

@section('content')
<main class="main">
  <div class="header">
    <h1 style="margin:0;font-size:28px;font-weight:800">My Schedule</h1>
    <p style="margin:8px 0 0;color:#6b7280">Tasks assigned to you, organized by date</p>
  </div>

  @if($tasks->count() > 0)
  <div class="schedule-card">
    @foreach($tasks as $date => $dayTasks)
    <div class="day-group">
      <div class="date-label">
        <div class="day-name">
          @php
            $dateObj = \Carbon\Carbon::parse($date);
            $today = \Carbon\Carbon::today();
            $tomorrow = \Carbon\Carbon::tomorrow();
            $yesterday = \Carbon\Carbon::yesterday();
          @endphp

          @if($dateObj->isSameDay($today))
            Today
          @elseif($dateObj->isSameDay($tomorrow))
            Tomorrow
          @elseif($dateObj->isSameDay($yesterday))
            Yesterday
          @else
            {{ $dateObj->format('D, M j') }}
          @endif
        </div>
        <div class="date-text">{{ $dateObj->format('Y-m-d') }}</div>
      </div>

      <div class="task-list">
        @foreach($dayTasks as $task)
        <div class="task-item">
          <div class="task-left">
            <div class="task-icon">
              {{ strtoupper(substr($task->equipment ?? 'T', 0, 2)) }}
            </div>
            <div class="task-info">
              <div class="task-title">{{ $task->title }}</div>
              <div class="task-meta">
                <span><strong>Equipment:</strong> {{ $task->equipment }}</span>
                <span><strong>Type:</strong> {{ $task->type }}</span>
                <span><strong>Frequency:</strong> {{ ucfirst($task->frequency) }}</span>
                @if($task->priority)
                <span><strong>Priority:</strong> {{ ucfirst($task->priority) }}</span>
                @endif
              </div>
            </div>
          </div>

          <div class="task-right">
            <span class="status-badge status-{{ $task->status }}">
              {{ ucfirst($task->status) }}
            </span>
            <a href="{{ route('worker.tasks.index') }}#task-{{ $task->id }}" class="open-link">Open →</a>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>

  <div class="tips-card">
    <strong style="font-size:16px">💡 Tips</strong>
    <p style="margin:8px 0 0;color:#6b7280;line-height:1.6">
      Click "Open →" to view task details and update status. Use <a href="{{ route('worker.tasks.index') }}" style="color:#7c3aed;font-weight:600">My Tasks</a> to manage all your tasks in one place. Check the <a href="{{ route('worker.dashboard') }}" style="color:#7c3aed;font-weight:600">Dashboard</a> for performance metrics.
    </p>
  </div>
  @else
  <div class="empty-state">
    <div class="icon">📅</div>
    <h3 style="margin:0;font-size:22px;font-weight:700">No Scheduled Tasks</h3>
    <p style="margin:12px 0 0;color:#6b7280;max-width:400px;margin-left:auto;margin-right:auto">
      You don't have any scheduled tasks yet. New tasks will appear here when they're assigned to you.
    </p>
    <a href="{{ route('worker.dashboard') }}" style="display:inline-block;margin-top:20px;padding:12px 24px;background:#7c3aed;color:white;text-decoration:none;border-radius:8px;font-weight:600">
      Go to Dashboard
    </a>
  </div>
  @endif
</main>

@push('scripts')
<script>
// Add smooth scroll to task anchor
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('a[href*="#task-"]').forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href.includes('#')) {
        // Let the browser handle the navigation to tasks page with hash
        // The tasks page will handle scrolling to the specific task
      }
    });
  });
});
</script>
@endpush
@endsection
