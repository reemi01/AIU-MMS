@extends('layouts.admin')

@section('title', 'Task Details')

@push('styles')
<style>
.detail-container { max-width: 900px; margin: 0 auto; padding: 24px; }
.detail-header { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 24px; }
.detail-title { font-size: 28px; font-weight: 800; margin: 0 0 12px; color: #111; }
.detail-meta { display: flex; gap: 16px; flex-wrap: wrap; align-items: center; }
.status-badge { padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
.status-badge.pending { background: #fef3c7; color: #92400e; }
.status-badge.inprogress { background: #dbeafe; color: #1e40af; }
.status-badge.completed { background: #d1fae5; color: #065f46; }
.frequency-badge { padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600; background: #e0e7ff; color: #3730a3; }
.priority-badge { padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600; }
.priority-badge.low { background: #f3f4f6; color: #6b7280; }
.priority-badge.normal { background: #dbeafe; color: #1e40af; }
.priority-badge.high { background: #fed7aa; color: #92400e; }
.priority-badge.urgent { background: #fee2e2; color: #991b1b; }
.detail-section { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 24px; }
.detail-section h2 { font-size: 20px; font-weight: 700; margin: 0 0 16px; color: #111; }
.detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
.detail-item { }
.detail-label { font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.detail-value { font-size: 16px; color: #111; font-weight: 500; }
.btn-group { display: flex; gap: 12px; }
.btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; border: 0; cursor: pointer; transition: all 0.2s; }
.btn-primary { background: #7c3aed; color: white; }
.btn-primary:hover { background: #6d28d9; }
.btn-secondary { background: #f3f4f6; color: #374151; }
.btn-secondary:hover { background: #e5e7eb; }
.btn-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.btn-danger:hover { background: #fecaca; }
</style>
@endpush

@section('content')
<div class="detail-container">
  @if(session('success'))
  <div style="background:#d1fae5;color:#065f46;padding:16px;border-radius:8px;margin-bottom:24px;border-left:4px solid #10b981">
    <strong>✓ Success!</strong> {{ session('success') }}
  </div>
  @endif

  <div class="detail-header">
    <div class="detail-title">{{ $task->title }}</div>
    <div class="detail-meta">
      <span class="status-badge {{ $task->status }}">{{ ucfirst($task->status) }}</span>
      <span class="frequency-badge">{{ ucfirst($task->frequency) }}</span>
      <span class="priority-badge {{ $task->priority }}">{{ ucfirst($task->priority) }} Priority</span>
    </div>
  </div>

  <div class="detail-section">
    <h2>Task Information</h2>
    <div class="detail-grid">
      <div class="detail-item">
        <div class="detail-label">Equipment Type</div>
        <div class="detail-value">{{ $task->type }}</div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Equipment Name</div>
        <div class="detail-value">{{ $task->equipment }}</div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Scheduled Date</div>
        <div class="detail-value">{{ $task->scheduled_date->format('F d, Y') }}</div>
      </div>
      @if($task->scheduled_time)
      <div class="detail-item">
        <div class="detail-label">Scheduled Time</div>
        <div class="detail-value">{{ $task->scheduled_time }}</div>
      </div>
      @endif
      @if($task->completed_at)
      <div class="detail-item">
        <div class="detail-label">Completed At</div>
        <div class="detail-value">{{ $task->completed_at->format('F d, Y h:i A') }}</div>
      </div>
      @endif
    </div>
  </div>

  @if($task->description)
  <div class="detail-section">
    <h2>Description</h2>
    <div class="detail-value" style="line-height: 1.6;">{{ $task->description }}</div>
  </div>
  @endif

  <div class="detail-section">
    <h2>Assigned Worker</h2>
    <div class="detail-grid">
      <div class="detail-item">
        <div class="detail-label">Name</div>
        <div class="detail-value">{{ $task->worker->user->name }}</div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Email</div>
        <div class="detail-value">{{ $task->worker->user->email }}</div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Trade</div>
        <div class="detail-value">{{ $task->worker->trade->name ?? 'N/A' }}</div>
      </div>
      <div class="detail-item">
        <div class="detail-label">Tasks Assigned</div>
        <div class="detail-value">{{ $task->worker->tasks_assigned }}</div>
      </div>
    </div>
  </div>

  @if($task->lift || $task->chiller)
  <div class="detail-section">
    <h2>Equipment Details</h2>
    <div class="detail-grid">
      @if($task->lift)
        <div class="detail-item">
          <div class="detail-label">Location</div>
          <div class="detail-value">{{ $task->lift->location ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Model Number</div>
          <div class="detail-value">{{ $task->lift->model_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Serial Number</div>
          <div class="detail-value">{{ $task->lift->serial_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Last Maintenance</div>
          <div class="detail-value">{{ $task->lift->last_maintenance_date ? $task->lift->last_maintenance_date->format('F d, Y') : 'N/A' }}</div>
        </div>
      @elseif($task->chiller)
        <div class="detail-item">
          <div class="detail-label">Location</div>
          <div class="detail-value">{{ $task->chiller->location ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Model Number</div>
          <div class="detail-value">{{ $task->chiller->model_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Serial Number</div>
          <div class="detail-value">{{ $task->chiller->serial_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
          <div class="detail-label">Last Maintenance</div>
          <div class="detail-value">{{ $task->chiller->last_maintenance_date ? $task->chiller->last_maintenance_date->format('F d, Y') : 'N/A' }}</div>
        </div>
      @endif
    </div>
  </div>
  @endif

  <!-- Quick Status Update -->
  @if($task->status !== 'completed')
  <div class="detail-section">
    <h2>Quick Actions</h2>
    <div class="btn-group">
      @if($task->status === 'pending')
      <form action="{{ route('admin.tasks.update-status', $task) }}" method="POST" style="display:inline">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="inprogress">
        <button type="submit" class="btn btn-primary">Mark as In Progress</button>
      </form>
      @endif

      @if($task->status === 'inprogress' || $task->status === 'pending')
      <form action="{{ route('admin.tasks.update-status', $task) }}" method="POST" style="display:inline">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="completed">
        <button type="submit" class="btn" style="background:#10b981;color:white">✓ Mark as Completed</button>
      </form>
      @endif

      @if($task->status === 'inprogress')
      <form action="{{ route('admin.tasks.update-status', $task) }}" method="POST" style="display:inline">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="pending">
        <button type="submit" class="btn btn-secondary">← Back to Pending</button>
      </form>
      @endif
    </div>
  </div>
  @endif

  <div class="btn-group">
    <a href="{{ route('admin.tasks.calendar') }}" class="btn btn-secondary">← Back to Calendar</a>
    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">View All Tasks</a>
    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete Task</button>
    </form>
  </div>
</div>
@endsection
