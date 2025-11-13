@extends('layouts.admin')

@section('title', 'Equipment Management')

@push('styles')
<style>
.main{flex:1;padding:22px}
.header{background:white;padding:24px;border-radius:12px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.header h1{margin:0 0 8px;font-size:28px;font-weight:800}
.header p{margin:0;color:#6b7280;font-size:14px}
.card{background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.btn{padding:10px 20px;background:#7c3aed;color:white;border:0;border-radius:8px;cursor:pointer;font-weight:600;text-decoration:none;display:inline-block;transition:all 0.2s}
.btn:hover{background:#6d28d9;transform:translateY(-1px)}
.btn-secondary{background:#e5e7eb;color:#111}
.btn-secondary:hover{background:#d1d5db}
.btn-danger{background:#ef4444;color:white}
.btn-danger:hover{background:#dc2626}
.table{width:100%;border-collapse:collapse;margin-top:16px}
.table th{text-align:left;padding:12px;border-bottom:2px solid #e5e7eb;font-weight:700;color:#374151}
.table td{padding:12px;border-bottom:1px solid #f3f4f6}
.status-badge{padding:4px 12px;border-radius:12px;font-size:13px;font-weight:600;display:inline-block}
.status-operational{background:#d1fae5;color:#065f46}
.status-warning{background:#fef3c7;color:#92400e}
.status-maintenance{background:#fee2e2;color:#991b1b}
.empty{text-align:center;padding:40px;color:#9ca3af}
.pagination{display:flex;gap:8px;justify-content:center;margin-top:20px}
.pagination a,.pagination span{padding:8px 12px;border-radius:6px;background:white;border:1px solid #e5e7eb;text-decoration:none;color:#374151}
.pagination a:hover{background:#f3f4f6}
.pagination .active{background:#7c3aed;color:white;border-color:#7c3aed}
@media (max-width:768px){
  .main{padding:12px}
  .header,.card{padding:16px}
  .table{font-size:14px}
  .table th,.table td{padding:8px}
}
</style>
@endpush

@section('content')
<main class="main">
  <div class="header">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
      <div>
        <h1>Equipment Management</h1>
        <p>Track and manage all equipment</p>
      </div>
      <a href="{{ route('admin.equipment.create') }}" class="btn">+ Add Equipment</a>
    </div>
  </div>

  <div class="card">
    @if($equipment->count() > 0)
      <table class="table">
        <thead>
          <tr>
            <th>Equipment Code</th>
            <th>Type</th>
            <th>Location</th>
            <th>Status</th>
            <th>Last Maintenance</th>
            <th>Tasks</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($equipment as $item)
          <tr>
            <td><strong>{{ $item->equipment_code }}</strong></td>
            <td>{{ $item->type }}</td>
            <td>{{ $item->location }}</td>
            <td>
              <span class="status-badge status-{{ $item->status }}">
                {{ ucfirst($item->status) }}
              </span>
            </td>
            <td>{{ $item->last_maintenance_date ? $item->last_maintenance_date->format('M d, Y') : 'N/A' }}</td>
            <td>{{ $item->tasks_count }} tasks</td>
            <td>
              <div style="display:flex;gap:8px">
                <a href="{{ route('admin.equipment.show', $item) }}" class="btn btn-secondary" style="padding:6px 12px;font-size:13px">View</a>
                <a href="{{ route('admin.equipment.edit', $item) }}" class="btn btn-secondary" style="padding:6px 12px;font-size:13px">Edit</a>
                <form action="{{ route('admin.equipment.destroy', $item) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this equipment?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger" style="padding:6px 12px;font-size:13px">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      @if($equipment->hasPages())
      <div class="pagination">
        {{ $equipment->links() }}
      </div>
      @endif
    @else
      <div class="empty">
        <div style="font-size:48px;margin-bottom:16px">📦</div>
        <h3 style="margin:0 0 8px;font-size:18px">No equipment found</h3>
        <p style="margin:0 0 16px;color:#6b7280">Get started by adding your first equipment</p>
        <a href="{{ route('admin.equipment.create') }}" class="btn">+ Add Equipment</a>
      </div>
    @endif
  </div>
</main>
@endsection
