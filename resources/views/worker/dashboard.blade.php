@extends('layouts.worker')

@section('title', 'Worker Dashboard')

@push('styles')
<style>
  .kpi-container{display:flex;gap:18px;margin-top:8px;flex-wrap:wrap}
  .kpi-card{flex:1;min-width:250px;padding:28px;border-radius:12px;background:white;box-shadow:0 6px 18px rgba(2,6,23,.06)}
  .kpi-metric{height:120px;border-radius:14px;color:#fff;padding:18px;display:flex;flex-direction:column;justify-content:center;margin-top:12px}
  .kpi-number{font-size:28px;font-weight:800;text-align:center}
  .task-item{background:#fff;border-radius:8px;padding:12px;border:1px solid #f1f1f1;margin-bottom:10px}
  .task-header{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
  .task-controls{display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap;margin-top:12px}
  .proof-btn{background:#10b981;color:#fff;padding:10px 16px;border:0;border-radius:8px;cursor:pointer;font-weight:600;min-height:44px;display:inline-flex;align-items:center;gap:6px}
  .update-btn{background:#6b46ff;color:#fff;padding:10px 16px;border:0;border-radius:8px;cursor:pointer;font-weight:600;min-height:44px}

  /* Camera Modal */
  .camera-modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);z-index:9999;justify-content:center;align-items:center}
  .camera-modal.active{display:flex}
  .camera-container{width:100%;max-width:640px;padding:20px;position:relative}
  .camera-video{width:100%;border-radius:12px;background:#000;display:block}
  .camera-canvas{display:none}
  .camera-controls{display:flex;gap:12px;margin-top:20px;justify-content:center;flex-wrap:wrap}
  .camera-btn{padding:14px 24px;border-radius:50px;border:0;font-weight:700;cursor:pointer;font-size:16px;min-width:120px;transition:all 0.2s}
  .capture-btn{background:#10b981;color:white;box-shadow:0 4px 12px rgba(16,185,129,0.4)}
  .capture-btn:hover{background:#059669;transform:scale(1.05)}
  .btn-secondary{background:#6b7280;color:white}
  .btn-secondary:hover{background:#4b5563}
  .camera-error{color:#ef4444;background:rgba(239,68,68,0.1);padding:16px;border-radius:8px;margin-top:12px;text-align:center}
  .camera-flip{position:absolute;top:30px;right:30px;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.2);border:2px solid white;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center}
  .camera-flip:hover{background:rgba(255,255,255,0.3)}

  @media (max-width:768px){
    .kpi-container{flex-direction:column;gap:12px}
    .kpi-card{min-width:100%;padding:20px}
    .kpi-metric{height:100px}
    .kpi-number{font-size:36px}
    .task-header{flex-direction:column;align-items:flex-start}
    .task-controls{flex-direction:column;width:100%}
    .task-controls select, .task-controls input{width:100%;min-height:44px;font-size:16px}
    .proof-btn, .update-btn{width:100%;justify-content:center}
    .camera-container{padding:12px}
    .camera-controls{flex-direction:column}
    .camera-btn{width:100%}
    .camera-flip{top:20px;right:20px;width:44px;height:44px}
  }
</style>
@endpush

@section('content')
<div class="main" style="padding:22px">
  <h1 style="text-align:center;font-size:32px;margin:6px 0 18px;font-weight:800">Welcome, {{ auth()->user()->name }}</h1>

  <div class="kpi-container">
    <div class="kpi-card">
      <h3 style="margin:0 0 14px;font-size:14px;color:#111827;text-align:center">Assigned</h3>
      <div class="kpi-metric" style="background:#60a5fa">
        <div class="kpi-number">{{ $tasks->count() }}</div>
      </div>
    </div>

    <div class="kpi-card">
      <h3 style="margin:0 0 14px;font-size:14px;color:#111827;text-align:center">In Progress</h3>
      <div class="kpi-metric" style="background:#fbbf24;color:#111">
        <div class="kpi-number">{{ $inProgressTasks }}</div>
      </div>
    </div>

    <div class="kpi-card">
      <h3 style="margin:0 0 14px;font-size:14px;color:#111827;text-align:center">Completed</h3>
      <div class="kpi-metric" style="background:#34d399">
        <div class="kpi-number">{{ $completedTasks }}</div>
      </div>
    </div>
  </div>

  <div style="background:white;border-radius:8px;padding:24px;margin-top:26px;box-shadow:0 6px 18px rgba(2,6,23,.04)">
    <h3 style="margin-top:0">Assigned Tasks</h3>
    <div style="margin-top:12px">
      @forelse($tasks as $task)
      <div class="task-item">
        <div class="task-header">
          <div>
            <strong>{{ $task->title }}</strong>
            <div style="font-size:13px;color:#666">
              {{ $task->type }} — {{ $task->equipment }}
            </div>
          </div>
          <div style="text-align:right">
            @if($task->status === 'completed')
            <span style="background:#10b981;color:#fff;padding:6px 10px;border-radius:999px;font-size:13px;white-space:nowrap">Completed</span>
            @elseif($task->status === 'inprogress')
            <span style="background:#0ea5e9;color:#fff;padding:6px 10px;border-radius:999px;font-size:13px;white-space:nowrap">In Progress</span>
            @else
            <span style="background:#f59e0b;color:#422006;padding:6px 10px;border-radius:999px;font-size:13px;white-space:nowrap">Pending</span>
            @endif
          </div>
        </div>
        <div style="margin-top:8px;color:#444">{{ $task->description ?? 'No description' }}</div>

        @if($task->proof)
        <div style="margin-top:8px">
          <img src="{{ $task->proof }}" class="image-preview" style="max-width:200px" alt="Proof image">
        </div>
        @endif

        <form action="{{ route('worker.tasks.update-status', $task) }}" method="POST" id="task-form-{{ $task->id }}" style="margin-top:12px">
          @csrf
          @method('PATCH')
          <input type="hidden" name="proof" id="proof-{{ $task->id }}">

          <div class="task-controls">
            <select name="status" style="padding:10px;border-radius:8px;border:1px solid #e6eef6;font-size:14px" required>
              <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="inprogress" {{ $task->status === 'inprogress' ? 'selected' : '' }}>In Progress</option>
              <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <input type="text" name="note" placeholder="Add note (optional)" style="flex:1;min-width:200px;padding:10px;border-radius:8px;border:1px solid #e6eef6;font-size:14px">

            <button type="button" onclick="openCamera({{ $task->id }})" class="proof-btn">
              📷 Take Photo
            </button>

            <input type="file" id="file-{{ $task->id }}" accept="image/*" style="display:none" onchange="handleFileUpload({{ $task->id }})">
            <button type="button" onclick="document.getElementById('file-{{ $task->id }}').click()" class="proof-btn" style="background:#3b82f6">
              📁 Upload Photo
            </button>

            <button type="submit" class="update-btn">
              Update Status
            </button>
          </div>

          <div id="proof-preview-{{ $task->id }}" style="margin-top:12px;display:none">
            <img id="proof-img-{{ $task->id }}" src="" class="image-preview" style="max-width:200px" alt="Proof preview">
            <button type="button" onclick="removeProof({{ $task->id }})" style="background:#ef4444;color:#fff;padding:8px 12px;border:0;border-radius:8px;cursor:pointer;margin-top:8px;font-size:14px">Remove Photo</button>
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

<!-- Camera Modal -->
<div id="camera-modal" class="camera-modal">
  <div class="camera-container">
    <button class="camera-flip" id="flip-camera" title="Switch Camera" style="display:none">🔄</button>
    <video id="camera-video" class="camera-video" autoplay playsinline></video>
    <canvas id="camera-canvas" class="camera-canvas"></canvas>
    <div id="camera-error" class="camera-error" style="display:none"></div>
    <div class="camera-controls">
      <button class="camera-btn capture-btn" id="capture-btn">📸 Capture Photo</button>
      <button class="camera-btn btn-secondary" id="close-camera">✕ Cancel</button>
    </div>
  </div>
</div>

<script>
let currentStream = null;
let currentTaskId = null;
let facingMode = 'environment'; // Start with back camera on mobile

function openCamera(taskId) {
  currentTaskId = taskId;
  const modal = document.getElementById('camera-modal');
  const video = document.getElementById('camera-video');
  const errorDiv = document.getElementById('camera-error');
  const flipBtn = document.getElementById('flip-camera');

  modal.classList.add('active');
  errorDiv.style.display = 'none';

  startCamera();

  // Show flip button only on mobile devices
  if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
    flipBtn.style.display = 'flex';
  }
}

async function startCamera() {
  const video = document.getElementById('camera-video');
  const errorDiv = document.getElementById('camera-error');

  try {
    // Stop existing stream if any
    if (currentStream) {
      currentStream.getTracks().forEach(track => track.stop());
    }

    const constraints = {
      video: {
        facingMode: facingMode,
        width: { ideal: 1920 },
        height: { ideal: 1080 }
      },
      audio: false
    };

    currentStream = await navigator.mediaDevices.getUserMedia(constraints);
    video.srcObject = currentStream;
    errorDiv.style.display = 'none';
  } catch (err) {
    console.error('Camera error:', err);
    errorDiv.textContent = 'Unable to access camera. Please allow camera permissions and try again.';
    errorDiv.style.display = 'block';
  }
}

function closeCamera() {
  const modal = document.getElementById('camera-modal');
  const video = document.getElementById('camera-video');

  if (currentStream) {
    currentStream.getTracks().forEach(track => track.stop());
    currentStream = null;
  }

  video.srcObject = null;
  modal.classList.remove('active');
  currentTaskId = null;
}

function capturePhoto() {
  if (!currentTaskId) return;

  const video = document.getElementById('camera-video');
  const canvas = document.getElementById('camera-canvas');
  const context = canvas.getContext('2d');

  // Set canvas size to match video
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;

  // Draw video frame to canvas
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  // Convert to base64
  const imageData = canvas.toDataURL('image/jpeg', 0.8);

  // Update form
  document.getElementById('proof-' + currentTaskId).value = imageData;
  document.getElementById('proof-img-' + currentTaskId).src = imageData;
  document.getElementById('proof-preview-' + currentTaskId).style.display = 'block';

  // Close camera
  closeCamera();
}

function flipCamera() {
  facingMode = facingMode === 'environment' ? 'user' : 'environment';
  startCamera();
}

function removeProof(taskId) {
  document.getElementById('proof-' + taskId).value = '';
  document.getElementById('file-' + taskId).value = '';
  document.getElementById('proof-preview-' + taskId).style.display = 'none';
}

function handleFileUpload(taskId) {
  const file = document.getElementById('file-' + taskId).files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById('proof-' + taskId).value = e.target.result;
    document.getElementById('proof-img-' + taskId).src = e.target.result;
    document.getElementById('proof-preview-' + taskId).style.display = 'block';
  };
  reader.readAsDataURL(file);
}

// Event listeners
document.getElementById('capture-btn').addEventListener('click', capturePhoto);
document.getElementById('close-camera').addEventListener('click', closeCamera);
document.getElementById('flip-camera').addEventListener('click', flipCamera);

// Close modal when clicking outside
document.getElementById('camera-modal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeCamera();
  }
});
</script>
@endsection
