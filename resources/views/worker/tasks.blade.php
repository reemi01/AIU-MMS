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
.btn{padding:10px 16px;border-radius:8px;border:0;font-weight:600;cursor:pointer;transition:all 0.2s;min-height:44px}
.btn-primary{background:#7c3aed;color:white}
.btn-primary:hover{background:#6d28d9}
.btn-success{background:#10b981;color:white;display:inline-flex;align-items:center;gap:6px}
.btn-success:hover{background:#059669}
.btn-danger{background:#ef4444;color:white}
.btn-danger:hover{background:#dc2626}
.btn-secondary{background:#6b7280;color:white}
.btn-secondary:hover{background:#4b5563}
.input{padding:10px;border-radius:8px;border:1px solid #e5e7eb;font-size:14px;min-height:44px}
.proof-img{max-width:100%;height:auto;border-radius:8px;margin-top:12px;border:2px solid #e5e7eb}

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
.camera-error{color:#ef4444;background:rgba(239,68,68,0.1);padding:16px;border-radius:8px;margin-top:12px;text-align:center}
.camera-flip{position:absolute;top:30px;right:30px;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.2);border:2px solid white;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center}
.camera-flip:hover{background:rgba(255,255,255,0.3)}

@media (max-width:768px){
  body{font-size:16px} /* Prevents zoom on iOS */
  .main{padding:12px}
  .header,.task-card{padding:16px}
  .task-meta{flex-direction:column;gap:8px}
  .task-actions{flex-direction:column}
  .task-actions select, .task-actions input{width:100%!important;font-size:16px}
  .btn{width:100%;justify-content:center;font-size:16px}
  .proof-img{max-width:100%}
  .camera-container{padding:12px}
  .camera-controls{flex-direction:column}
  .camera-btn{width:100%}
  .camera-flip{top:20px;right:20px;width:44px;height:44px}
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

        <button type="button" onclick="openCamera({{ $task->id }})" class="btn btn-success">
          📷 Take Photo
        </button>

        <input type="file" id="file-{{ $task->id }}" accept="image/*" style="display:none" onchange="handleFileUpload({{ $task->id }})">
        <button type="button" onclick="document.getElementById('file-{{ $task->id }}').click()" class="btn btn-success" style="background:#3b82f6">
          📁 Upload Photo
        </button>

        <button type="submit" class="btn btn-primary">Update Status</button>
      </div>

      <div id="preview-{{ $task->id }}" style="display:none;margin-top:12px">
        <img id="img-{{ $task->id }}" src="" class="proof-img">
        <button type="button" onclick="removeProof({{ $task->id }})" class="btn btn-danger" style="margin-top:8px">Remove Photo</button>
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
  document.getElementById('img-' + currentTaskId).src = imageData;
  document.getElementById('preview-' + currentTaskId).style.display = 'block';

  // Close camera
  closeCamera();
}

function flipCamera() {
  facingMode = facingMode === 'environment' ? 'user' : 'environment';
  startCamera();
}

function removeProof(id) {
  document.getElementById('proof-' + id).value = '';
  document.getElementById('file-' + id).value = '';
  document.getElementById('preview-' + id).style.display = 'none';
}

function handleFileUpload(id) {
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
