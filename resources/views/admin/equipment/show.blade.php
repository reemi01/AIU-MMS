@extends('layouts.admin')

@section('title', 'Equipment Details')

@push('styles')
<style>
.main{flex:1;padding:22px}
.container{max-width:1000px;margin:0 auto}
.card{background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05);margin-bottom:24px}
.card h2{margin:0 0 8px;font-size:22px;font-weight:700}
.card h3{margin:0 0 16px;font-size:18px;font-weight:700}
.card p{margin:0 0 24px;color:#6b7280;font-size:14px}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:24px}
.info-item{background:#f9fafb;padding:16px;border-radius:8px}
.info-label{font-size:13px;color:#6b7280;margin-bottom:4px}
.info-value{font-size:16px;font-weight:600;color:#374151}
.status-badge{padding:4px 12px;border-radius:12px;font-size:13px;font-weight:600;display:inline-block}
.status-operational{background:#d1fae5;color:#065f46}
.status-warning{background:#fef3c7;color:#92400e}
.status-maintenance{background:#fee2e2;color:#991b1b}
.btn{padding:10px 20px;background:#7c3aed;color:white;border:0;border-radius:8px;cursor:pointer;font-weight:600;text-decoration:none;display:inline-block;transition:all 0.2s}
.btn:hover{background:#6d28d9}
.btn-secondary{background:#e5e7eb;color:#111}
.btn-secondary:hover{background:#d1d5db}
.table{width:100%;border-collapse:collapse;margin-top:16px}
.table th{text-align:left;padding:12px;border-bottom:2px solid #e5e7eb;font-weight:700;color:#374151;font-size:14px}
.table td{padding:12px;border-bottom:1px solid #f3f4f6}
.empty{text-align:center;padding:40px;color:#9ca3af}
@media (max-width:768px){
  .main{padding:12px}
  .card{padding:16px}
  .info-grid{grid-template-columns:1fr}
}
</style>
@endpush

@section('content')
<main class="main">
  <div class="container">
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px">
        <div>
          <h2>{{ $equipment->equipment_code }}</h2>
          <p style="margin:0">Equipment details and associated tasks</p>
        </div>
        <div style="display:flex;gap:8px">
          <a href="{{ route('admin.equipment.edit', $equipment) }}" class="btn">Edit</a>
          <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary">Back</a>
        </div>
      </div>

      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Equipment Code</div>
          <div class="info-value">{{ $equipment->equipment_code }}</div>
        </div>

        <div class="info-item">
          <div class="info-label">Type</div>
          <div class="info-value">{{ $equipment->type }}</div>
        </div>

        <div class="info-item">
          <div class="info-label">Location</div>
          <div class="info-value">{{ $equipment->location }}</div>
        </div>

        <div class="info-item">
          <div class="info-label">Status</div>
          <div class="info-value">
            <span class="status-badge status-{{ $equipment->status }}">
              {{ ucfirst($equipment->status) }}
            </span>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">Last Maintenance</div>
          <div class="info-value">
            {{ $equipment->last_maintenance_date ? $equipment->last_maintenance_date->format('M d, Y') : 'Not recorded' }}
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">Created</div>
          <div class="info-value">{{ $equipment->created_at->format('M d, Y') }}</div>
        </div>
      </div>
    </div>

    <div class="card">
      <h3>Associated Tasks ({{ $equipment->tasks->count() }})</h3>

      @if($equipment->tasks->count() > 0)
        <table class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Type</th>
              <th>Worker</th>
              <th>Status</th>
              <th>Scheduled Date</th>
              <th>Priority</th>
            </tr>
          </thead>
          <tbody>
            @foreach($equipment->tasks as $task)
            <tr>
              <td><strong>{{ $task->title }}</strong></td>
              <td>{{ $task->type }}</td>
              <td>{{ $task->worker->user->name ?? 'Unassigned' }}</td>
              <td>
                <span class="status-badge status-{{ $task->status }}">
                  {{ ucfirst($task->status) }}
                </span>
              </td>
              <td>{{ $task->scheduled_date->format('M d, Y') }}</td>
              <td>{{ ucfirst($task->priority) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div class="empty">
          <div style="font-size:48px;margin-bottom:16px">📋</div>
          <h3 style="margin:0 0 8px;font-size:18px">No tasks assigned</h3>
          <p style="margin:0;color:#6b7280">No tasks have been assigned to this equipment yet</p>
        </div>
      @endif
    </div>
  </div>
</main>
@endsection
