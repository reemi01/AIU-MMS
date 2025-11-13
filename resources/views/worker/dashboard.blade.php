@extends('layouts.worker')

@section('title', 'Worker Dashboard')

@section('content')
<div class="main" style="padding:22px">
  <h1 style="text-align:center;font-size:32px;margin:6px 0 18px;font-weight:800">Welcome, {{ auth()->user()->name }}</h1>

  <div style="display:flex;gap:18px;margin-top:8px">
    <div style="flex:1;padding:28px;border-radius:12px;background:white;box-shadow:0 6px 18px rgba(2,6,23,.06)">
      <h3 style="margin:0 0 14px;font-size:14px;color:#111827;text-align:center">Assigned</h3>
      <div style="height:120px;border-radius:14px;color:#fff;background:#60a5fa;padding:18px;display:flex;flex-direction:column;justify-content:center;margin-top:12px">
        <div style="font-size:28px;font-weight:800;text-align:center">{{ $tasks->count() }}</div>
      </div>
    </div>

    <div style="flex:1;padding:28px;border-radius:12px;background:white;box-shadow:0 6px 18px rgba(2,6,23,.06)">
      <h3 style="margin:0 0 14px;font-size:14px;color:#111827;text-align:center">In Progress</h3>
      <div style="height:120px;border-radius:14px;color:#111;background:#fbbf24;padding:18px;display:flex;flex-direction:column;justify-content:center;margin-top:12px">
        <div style="font-size:28px;font-weight:800;text-align:center">{{ $inProgressTasks }}</div>
      </div>
    </div>

    <div style="flex:1;padding:28px;border-radius:12px;background:white;box-shadow:0 6px 18px rgba(2,6,23,.06)">
      <h3 style="margin:0 0 14px;font-size:14px;color:#111827;text-align:center">Completed</h3>
      <div style="height:120px;border-radius:14px;color:#fff;background:#34d399;padding:18px;display:flex;flex-direction:column;justify-content:center;margin-top:12px">
        <div style="font-size:28px;font-weight:800;text-align:center">{{ $completedTasks }}</div>
      </div>
    </div>
  </div>

  <div style="background:white;border-radius:8px;padding:24px;margin-top:26px;box-shadow:0 6px 18px rgba(2,6,23,.04)">
    <h3 style="margin-top:0">Assigned Tasks</h3>
    <div style="margin-top:12px">
      @forelse($tasks as $task)
      <div style="background:#fff;border-radius:8px;padding:12px;border:1px solid #f1f1f1;margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <div>
            <strong>{{ $task->title }}</strong>
            <div style="font-size:13px;color:#666">
              {{ $task->type }} — {{ $task->equipment }}
            </div>
          </div>
          <div style="text-align:right">
            @if($task->status === 'completed')
            <span style="background:#10b981;color:#fff;padding:6px 10px;border-radius:999px">Completed</span>
            @elseif($task->status === 'inprogress')
            <span style="background:#0ea5e9;color:#fff;padding:6px 10px;border-radius:999px">In Progress</span>
            @else
            <span style="background:#f59e0b;color:#422006;padding:6px 10px;border-radius:999px">Pending</span>
            @endif
          </div>
        </div>
        <div style="margin-top:8px;color:#444">{{ $task->description ?? 'No description' }}</div>

        @if($task->proof)
        <div style="margin-top:8px">
          <img src="{{ $task->proof }}" style="max-width:200px;border-radius:8px;border:2px solid #e5e7eb" alt="Proof image">
        </div>
        @endif

        <form action="{{ route('worker.tasks.update-status', $task) }}" method="POST" id="task-form-{{ $task->id }}" style="margin-top:12px">
          @csrf
          @method('PATCH')
          <input type="hidden" name="proof" id="proof-{{ $task->id }}">

          <div style="display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap">
            <select name="status" style="padding:8px;border-radius:6px;border:1px solid #e6eef6" required>
              <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="inprogress" {{ $task->status === 'inprogress' ? 'selected' : '' }}>In Progress</option>
              <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <input type="text" name="note" placeholder="Add note (optional)" style="flex:1;min-width:200px;padding:8px;border-radius:6px;border:1px solid #e6eef6">

            <div style="position:relative">
              <input type="file" id="proof-file-{{ $task->id }}" accept="image/*" capture="environment" style="display:none" onchange="handleProofUpload({{ $task->id }})">
              <button type="button" onclick="document.getElementById('proof-file-{{ $task->id }}').click()" style="background:#10b981;color:#fff;padding:8px 16px;border:0;border-radius:6px;cursor:pointer">
                📷 Upload Proof
              </button>
            </div>

            <button type="submit" style="background:#6b46ff;color:#fff;padding:8px 16px;border:0;border-radius:6px;cursor:pointer;font-weight:600">
              Update Status
            </button>
          </div>

          <div id="proof-preview-{{ $task->id }}" style="margin-top:8px;display:none">
            <img id="proof-img-{{ $task->id }}" src="" style="max-width:150px;border-radius:8px;border:2px solid #10b981" alt="Proof preview">
            <button type="button" onclick="removeProof({{ $task->id }})" style="background:#ef4444;color:#fff;padding:4px 8px;border:0;border-radius:4px;cursor:pointer;margin-left:8px;font-size:12px">Remove</button>
          </div>
        </form>
      </div>
      </div>
      @empty
      <div style="color:#999">No assigned tasks</div>
      @endforelse
    </div>
  </div>
</div>

<script>
function handleProofUpload(taskId) {
  const fileInput = document.getElementById('proof-file-' + taskId);
  const file = fileInput.files[0];

  if (!file) return;

  const reader = new FileReader();
  reader.onload = function(e) {
    const base64 = e.target.result;
    document.getElementById('proof-' + taskId).value = base64;
    document.getElementById('proof-img-' + taskId).src = base64;
    document.getElementById('proof-preview-' + taskId).style.display = 'block';
  };
  reader.readAsDataURL(file);
}

function removeProof(taskId) {
  document.getElementById('proof-' + taskId).value = '';
  document.getElementById('proof-file-' + taskId).value = '';
  document.getElementById('proof-preview-' + taskId).style.display = 'none';
}
</script>
@endsection
