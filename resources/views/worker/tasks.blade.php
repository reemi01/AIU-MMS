@extends('layouts.worker')

@section('title', 'My Tasks')

@push('styles')
<style>
.main{flex:1;padding:22px}
.header{background:white;padding:24px;border-radius:12px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.task-card{background:white;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 2px 8px rgba(0,0,0,0.05);border-left:4px solid #e5e7eb}
.task-card.pending{border-left-color:#f59e0b}
.task-card.inprogress{border-left-color:#3b82f6}
.task-card.completed{border-left-color:#10b981}
.status-badge{padding:6px 12px;border-radius:12px;font-size:13px;font-weight:600;display:inline-block}
.status-pending{background:#fef3c7;color:#92400e}
.status-inprogress{background:#dbeafe;color:#1e40af}
.status-completed{background:#d1fae5;color:#065f46}
.task-meta{display:flex;gap:16px;margin-top:12px;font-size:14px;color:#6b7280}
.task-actions{display:flex;gap:8px;margin-top:16px;flex-wrap:wrap}
.btn{padding:10px 16px;border-radius:8px;border:0;font-weight:600;cursor:pointer;transition:all 0.2s}
.btn-primary{background:#7c3aed;color:white}
.btn-primary:hover{background:#6d28d9}
.btn-success{background:#10b981;color:white}
.btn-success:hover{background:#059669}
.input{padding:10px;border-radius:8px;border:1px solid #e5e7eb;font-size:14px}
.proof-img{max-width:200px;border-radius:8px;margin-top:12px;border:2px solid #e5e7eb}
@media (max-width:768px){
  .main{padding:12px}
  .header,.task-card{padding:16px}
  .task-meta{flex-direction:column;gap:8px}
}
</style>
@endpush

@section('content')
<main class="main">
  <div class="header">
    <h1 style="margin:0;font-size:28px;font-weight:800">My Tasks</h1>
    <p style="margin:8px 0 0;color:#6b7280">Manage and update your assigned tasks</p>
  </div>

  @forelse($tasks as $task)
  <div id="task-{{ $task->id }}" class="task-card {{ $task->status }}">
    <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:12px">
      <div style="flex:1">
        <h3 style="margin:0;font-size:20px;font-weight:700">{{ $task->title }}</h3>
        <span class="status-badge status-{{ $task->status }}" style="margin-top:8px;display:inline-block">
          {{ ucfirst($task->status) }}
        </span>
      </div>
      <div style="text-align:right">
        <div style="font-size:14px;color:#6b7280">Scheduled</div>
        <div style="font-weight:700;font-size:16px">{{ $task->scheduled_date->format('M d, Y') }}</div>
      </div>
    </div>

    <div class="task-meta">
      <div><strong>Type:</strong> {{ $task->type }}</div>
      <div><strong>Equipment:</strong> {{ $task->equipment }}</div>
      <div><strong>Frequency:</strong> {{ ucfirst($task->frequency) }}</div>
      <div><strong>Priority:</strong> {{ ucfirst($task->priority) }}</div>
    </div>

    @if($task->description)
    <div style="margin-top:12px;padding:12px;background:#f9fafb;border-radius:8px">
      <strong>Description:</strong>
      <p style="margin:4px 0 0">{{ $task->description }}</p>
    </div>
    @endif

    @if($task->proof)
    <div style="margin-top:12px">
      <strong>Current Proof:</strong>
      <img src="{{ $task->proof }}" class="proof-img" alt="Task proof">
    </div>
    @endif

    @if($task->reports->count() > 0)
    <div style="margin-top:12px">
      <strong>Reports ({{ $task->reports->count() }}):</strong>
      @foreach($task->reports->take(3) as $report)
      <div style="padding:8px;background:#f9fafb;border-radius:6px;margin-top:8px;font-size:13px">
        <strong>{{ $report->created_at->format('M d, Y H:i') }}:</strong> {{ $report->note }}
      </div>
      @endforeach
    </div>
    @endif

    <form action="{{ route('worker.tasks.update-status', $task) }}" method="POST" id="form-{{ $task->id }}">
      @csrf
      @method('PATCH')
      <input type="hidden" name="proof" id="proof-{{ $task->id }}">

      <div class="task-actions">
        <select name="status" class="input" style="width:150px" required>
          <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="inprogress" {{ $task->status === 'inprogress' ? 'selected' : '' }}>In Progress</option>
          <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
        </select>

        <input type="text" name="note" placeholder="Add note (optional)" class="input" style="flex:1;min-width:200px">

        <input type="file" id="file-{{ $task->id }}" accept="image/*" capture="environment" style="display:none" onchange="handleProof({{ $task->id }})">
        <button type="button" onclick="document.getElementById('file-{{ $task->id }}').click()" class="btn btn-success">
          📷 Upload Proof
        </button>

        <button type="submit" class="btn btn-primary">Update Status</button>
      </div>

      <div id="preview-{{ $task->id }}" style="display:none;margin-top:12px">
        <img id="img-{{ $task->id }}" src="" class="proof-img">
        <button type="button" onclick="removeProof({{ $task->id }})" style="margin-left:8px;padding:6px 12px;background:#ef4444;color:white;border:0;border-radius:6px;cursor:pointer">Remove</button>
      </div>
    </form>
  </div>
  @empty
  <div style="text-align:center;padding:40px;background:white;border-radius:12px">
    <div style="font-size:48px;margin-bottom:16px">📋</div>
    <h3 style="margin:0;font-size:20px">No tasks assigned</h3>
    <p style="margin:8px 0 0;color:#6b7280">You don't have any tasks assigned yet</p>
  </div>
  @endforelse
</main>

<script>
function handleProof(id) {
  const file = document.getElementById('file-' + id).files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById('proof-' + id).value = e.target.result;
    document.getElementById('img-' + id).src = e.target.result;
    document.getElementById('preview-' + id).style.display = 'block';
  };
  reader.readAsDataURL(file);
}

function removeProof(id) {
  document.getElementById('proof-' + id).value = '';
  document.getElementById('file-' + id).value = '';
  document.getElementById('preview-' + id).style.display = 'none';
}

// Handle anchor scroll from schedule page
document.addEventListener('DOMContentLoaded', function() {
  if (window.location.hash) {
    const targetId = window.location.hash.substring(1);
    const targetElement = document.getElementById(targetId);

    if (targetElement) {
      setTimeout(function() {
        targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        targetElement.style.animation = 'highlight 2s ease';
      }, 100);
    }
  }
});

// Add highlight animation
const style = document.createElement('style');
style.textContent = `
  @keyframes highlight {
    0%, 100% { box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    50% { box-shadow: 0 0 0 4px rgba(124,58,237,0.3), 0 2px 8px rgba(0,0,0,0.05); }
  }
`;
document.head.appendChild(style);
</script>
@endsection
