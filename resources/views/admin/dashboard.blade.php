@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
.kpis{display:flex;gap:18px;align-items:flex-start;margin-top:8px}
.kpi{flex:1;padding:28px;border-radius:12px;background:white;box-shadow:0 6px 18px rgba(2,6,23,.06)}
.kpi h3{margin:0 0 14px;font-size:14px;color:#111827;text-align:center}
.tile{height:120px;border-radius:14px;color:#fff;padding:18px;display:flex;flex-direction:column;justify-content:center}
.tile.purple{background:#c4b5fd}
.tile.yellow{background:#fde68a;color:#111}
.tile.green{background:#34d399}
.card{background:white;border-radius:8px;padding:24px;min-height:140px;margin-bottom:24px;box-shadow:0 6px 18px rgba(2,6,23,.04)}
.notifs{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px}
.notif-item{display:flex;gap:12px;align-items:flex-start}
.notif-icon{width:40px;height:40px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:18px;background:#f3f4ff;color:#4c1d95;font-weight:700}
.notif-title{font-weight:700}
.notif-meta{color:#6b7280;font-size:13px}
@media (max-width:900px){
  .kpis{flex-direction:column}
}
@media (max-width: 768px) {
  .main { padding: 12px !important; }
  h1 { font-size: 24px !important; }
  .kpi { padding: 18px !important; }
  .kpi h3 { font-size: 13px !important; }
  .tile { height: 100px !important; }
  .tile div { font-size: 22px !important; }
  .card { padding: 16px !important; }
  .notif-item { flex-direction: column; align-items: flex-start !important; }
  .notif-icon { margin-bottom: 8px; }
}
</style>

<div class="main" style="padding:22px">
  <h1 style="text-align:center;font-size:36px;margin:6px 0 18px;font-weight:800">Welcome {{ auth()->user()->name }}</h1>

  <div class="kpis">
    <div class="kpi">
      <h3>Total asset of lifts</h3>
      <div class="tile purple" style="margin-top:12px">
        <div style="font-size:28px;font-weight:800;text-align:center">{{ \App\Models\Lift::count() }}</div>
      </div>
    </div>

    <div class="kpi">
      <h3>Total asset of chillers</h3>
      <div class="tile yellow" style="margin-top:12px">
        <div style="font-size:28px;font-weight:800;text-align:center">{{ \App\Models\Chiller::count() }}</div>
      </div>
    </div>

    <div class="kpi">
      <h3>Task completed</h3>
      <div class="tile green" style="margin-top:12px">
        <div style="font-size:28px;font-weight:800;text-align:center">{{ \App\Models\Task::where('status', 'completed')->count() }}</div>
      </div>
    </div>

    @php
    $overdueTasks = \App\Models\Task::whereIn('status', ['pending', 'inprogress'])
      ->whereDate('scheduled_date', '<', now())
      ->count();
    @endphp
    <div class="kpi">
      <h3>Overdue Tasks</h3>
      <div class="tile" style="margin-top:12px;background:#fee2e2;color:#991b1b">
        <div style="font-size:28px;font-weight:800;text-align:center">{{ $overdueTasks }}</div>
      </div>
    </div>
  </div>

  <div style="margin-top:26px">
    @php
    $overdueTasksList = \App\Models\Task::whereIn('status', ['pending', 'inprogress'])
      ->whereDate('scheduled_date', '<', now())
      ->with(['worker.user'])
      ->orderBy('scheduled_date', 'asc')
      ->take(5)
      ->get();
    @endphp
    @if($overdueTasksList->count() > 0)
    <div class="card" style="background:#fef2f2;border-left:4px solid #ef4444">
      <h3 style="margin:0 0 12px;font-size:16px;color:#991b1b">⚠️ Overdue Tasks</h3>
      <ul class="notifs">
        @foreach($overdueTasksList as $task)
        <li class="notif-item">
          <span class="notif-icon" style="background:#fee2e2;color:#991b1b">⚠️</span>
          <div>
            <div class="notif-title">{{ $task->equipment }} — {{ $task->title }}</div>
            <div class="notif-meta">
              Assigned to {{ $task->worker->user->name ?? 'Unassigned' }} •
              Was due {{ $task->scheduled_date->diffForHumans() }} •
              {{ ucfirst($task->status) }}
            </div>
          </div>
        </li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="card">
      <h3 style="margin:0 0 12px;font-size:16px">Notifications & Alerts</h3>
      <ul class="notifs">
        @php
        $pendingTasks = \App\Models\Task::where('status', 'pending')->with(['worker.user'])->latest()->take(3)->get();
        @endphp
        @forelse($pendingTasks as $task)
        <li class="notif-item">
          <span class="notif-icon">
            @if($task->frequency === 'weekly')📅@else📆@endif
          </span>
          <div>
            <div class="notif-title">
              @if($task->frequency === 'weekly')
              <span style="background:#dbeafe;color:#1e40af;padding:2px 6px;border-radius:4px;font-size:11px;margin-right:4px">WEEKLY</span>
              @else
              <span style="background:#fef3c7;color:#92400e;padding:2px 6px;border-radius:4px;font-size:11px;margin-right:4px">MONTHLY</span>
              @endif
              {{ $task->equipment }} — {{ $task->title }}
            </div>
            <div class="notif-meta">
              Assigned to {{ $task->worker->user->name ?? 'Unassigned' }} •
              {{ $task->type }} •
              {{ ucfirst($task->frequency) }} maintenance
            </div>
          </div>
        </li>
        @empty
        <li class="notif-item">
          <span class="notif-icon">✅</span>
          <div>
            <div class="notif-title">All tasks assigned</div>
            <div class="notif-meta">No pending tasks at the moment.</div>
          </div>
        </li>
        @endforelse
      </ul>
    </div>

    <div class="card">
      <h3 style="margin:0 0 12px;font-size:16px">Upcoming Tasks</h3>
      <ul class="notifs">
        @php
        $upcomingTasks = \App\Models\Task::whereIn('status', ['pending', 'inprogress'])
            ->with(['worker.user'])
            ->orderBy('scheduled_date', 'asc')
            ->take(5)
            ->get();
        @endphp
        @forelse($upcomingTasks as $task)
        <li class="notif-item">
          <span class="notif-icon">
            @if($task->type === 'Lift')🔧@else🧊@endif
          </span>
          <div>
            <div class="notif-title">
              @if($task->frequency === 'weekly')
              <span style="background:#dbeafe;color:#1e40af;padding:2px 6px;border-radius:4px;font-size:11px;margin-right:4px">WEEKLY</span>
              @else
              <span style="background:#fef3c7;color:#92400e;padding:2px 6px;border-radius:4px;font-size:11px;margin-right:4px">MONTHLY</span>
              @endif
              {{ $task->equipment }} — {{ $task->title }}
            </div>
            <div class="notif-meta">
              Due: {{ $task->scheduled_date->format('M d, Y') }}
              @if($task->worker)
              — Assigned to {{ $task->worker->user->name }}
              @endif
              • {{ $task->type }}
            </div>
          </div>
        </li>
        @empty
        <li class="notif-item">
          <span class="notif-icon">✅</span>
          <div>
            <div class="notif-title">No upcoming tasks</div>
            <div class="notif-meta">All tasks are completed.</div>
          </div>
        </li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

@endsection
