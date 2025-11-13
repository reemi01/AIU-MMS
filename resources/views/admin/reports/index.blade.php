@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<style>
:root{--bg:#efefef;--muted:#6b7280;--accent:#7c3aed}
.main{flex:1;padding:22px}
.title{font-size:30px;text-align:center;font-weight:800;margin:6px 0 18px}
.section{background:white;border-radius:8px;padding:18px;box-shadow:0 6px 18px rgba(2,6,23,.04);margin-bottom:18px}
.section h3{margin:0 0 12px;font-size:18px;font-weight:700}
.task-row{display:flex;align-items:center;padding:12px;border-radius:6px;border:1px solid #f1f1f1;margin-bottom:8px;background:#fafafa;gap:12px}
.task-row .equipment{font-weight:700;min-width:100px}
.task-row .worker{color:var(--muted);min-width:120px}
.task-row .status{padding:4px 12px;border-radius:4px;font-size:13px;font-weight:600}
.status.pending{background:#fef3c7;color:#92400e}
.status.inprogress{background:#fed7aa;color:#9a3412}
.status.completed{background:#d1fae5;color:#065f46}
.task-row .notes{flex:1;color:var(--muted);font-size:14px}
.task-row .date{color:var(--muted);font-size:13px;min-width:100px}
.task-row .proof-img{width:60px;height:60px;border-radius:6px;object-fit:cover;cursor:pointer;border:2px solid #e5e7eb;background:#f3f4f6}
.proof-img.lazy{opacity:0;transition:opacity 0.3s}
.proof-img.loaded{opacity:1}
.loading-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.9);display:none;align-items:center;justify-content:center;z-index:9998}
.loading-overlay.active{display:flex}
.spinner{width:40px;height:40px;border:4px solid #e5e7eb;border-top-color:#7c3aed;border-radius:50%;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.empty{text-align:center;color:#999;padding:20px}
.modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:9999;align-items:center;justify-content:center}
.modal-overlay.active{display:flex}
.modal-content{max-width:90%;max-height:90%;position:relative}
.modal-content img{max-width:100%;max-height:90vh;border-radius:8px}
.modal-close{position:absolute;top:-40px;right:0;background:white;border:0;padding:8px 16px;border-radius:4px;cursor:pointer;font-weight:700}

@media (max-width: 768px) {
  .main { padding: 12px !important; }
  .title { font-size: 22px !important; }
  .section { padding: 12px !important; }
  .section h3 { font-size: 16px !important; }
  .task-row {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 8px !important;
    padding: 12px !important;
  }
  .task-row .equipment,
  .task-row .worker,
  .task-row .date {
    min-width: auto !important;
  }
  form > div[style*="grid"] {
    grid-template-columns: 1fr !important;
  }
  .proof-img {
    width: 100%;
    height: auto;
    max-width: 200px;
  }
}

@media print {
  body { background: white !important; }
  .main { padding: 0 !important; }
  form, .no-print, button, .modal-overlay { display: none !important; }
  .section { box-shadow: none !important; border: 1px solid #e5e7eb; page-break-inside: avoid; margin-bottom: 20px; }
  .task-row { border-bottom: 1px solid #e5e7eb !important; }
  .proof-img { max-width: 100px; max-height: 100px; }
  @page { margin: 1cm; }
}
</style>

<main class="main">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 class="title" style="margin:0;">Reports</h1>
    <button onclick="window.print()" class="no-print" style="padding:10px 20px;background:#10b981;color:white;border:0;border-radius:6px;cursor:pointer;font-weight:600;">🖨️ Print Report</button>
  </div>

  <!-- Filters -->
  <div class="section" style="margin-bottom:24px">
    <h3>Filter Reports</h3>
    <form method="GET" action="{{ route('admin.reports.index') }}">
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-top:16px">
        <select name="status" class="input" style="padding:10px" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="inprogress" {{ request('status') == 'inprogress' ? 'selected' : '' }}>In Progress</option>
          <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        </select>

        <select name="type" class="input" style="padding:10px" onchange="this.form.submit()">
          <option value="">All Types</option>
          <option value="Lift" {{ request('type') == 'Lift' ? 'selected' : '' }}>Lift</option>
          <option value="Chiller" {{ request('type') == 'Chiller' ? 'selected' : '' }}>Chiller</option>
        </select>

        <select name="frequency" class="input" style="padding:10px" onchange="this.form.submit()">
          <option value="">All Frequencies</option>
          <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
          <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
        </select>

        <select name="worker_id" class="input" style="padding:10px" onchange="this.form.submit()">
          <option value="">All Workers</option>
          @foreach($workers as $worker)
          <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
            {{ $worker->user->name }}
          </option>
          @endforeach
        </select>

        <input type="text" name="equipment" class="input" style="padding:10px" placeholder="Search equipment..." value="{{ request('equipment') }}">

        <input type="date" name="date_from" class="input" style="padding:10px" placeholder="From Date" value="{{ request('date_from') }}">

        <input type="date" name="date_to" class="input" style="padding:10px" placeholder="To Date" value="{{ request('date_to') }}">

        <button type="submit" style="padding:10px 16px;background:#7c3aed;color:#fff;border:0;border-radius:8px;cursor:pointer;font-weight:600">Apply Filters</button>

        @if(request()->hasAny(['status', 'type', 'frequency', 'worker_id', 'equipment', 'date_from', 'date_to']))
        <a href="{{ route('admin.reports.index') }}" style="padding:10px 16px;background:#fff;color:#111;border:1px solid #e5e7eb;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center">Clear Filters</a>
        @endif
      </div>
    </form>
  </div>

  @if(request()->hasAny(['status', 'type', 'frequency', 'worker_id', 'equipment', 'date_from', 'date_to']))
  <!-- Filtered Results -->
  <div class="section">
    <h3>Filtered Results ({{ $tasks->count() }} tasks)</h3>
    @forelse($tasks as $task)
    <div class="task-row">
      <div class="equipment">{{ $task->equipment }}</div>
      <div class="worker">{{ $task->worker->user->name ?? 'Unassigned' }}</div>
      <span class="status {{ $task->status }}">{{ ucfirst($task->status) }}</span>
      <div style="font-size:12px;color:#6b7280;min-width:80px">
        {{ ucfirst($task->type) }} - {{ ucfirst($task->frequency) }}
      </div>
      <div class="notes">
        @if($task->description)
          {{ $task->description }}
        @endif
        @foreach($task->reports as $report)
          <div style="margin-top:4px;font-size:12px">
            <strong>{{ $report->created_at->format('M d, Y H:i') }}</strong>: {{ $report->note }}
          </div>
        @endforeach
      </div>
      @if($task->completed_at)
      <div class="date">{{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y') }}</div>
      @endif
      @if($task->proof)
      <img data-src="{{ $task->proof }}" class="proof-img lazy" onclick="showImage('{{ $task->proof }}')" alt="Proof" loading="lazy">
      @endif
    </div>
    @empty
    <div class="empty">No tasks match your filters</div>
    @endforelse
  </div>
  @else
  <!-- Default Grouped View -->
  <div class="section">
    <h3>Lift - Weekly Tasks</h3>
    @forelse($liftWeekly as $task)
    <div class="task-row">
      <div class="equipment">{{ $task->equipment }}</div>
      <div class="worker">{{ $task->worker->user->name ?? 'Unassigned' }}</div>
      <span class="status {{ $task->status }}">{{ ucfirst($task->status) }}</span>
      <div class="notes">
        @if($task->description)
          {{ $task->description }}
        @endif
        @foreach($task->reports as $report)
          <div style="margin-top:4px;font-size:12px">
            <strong>{{ $report->created_at->format('M d, Y H:i') }}</strong>: {{ $report->note }}
          </div>
        @endforeach
      </div>
      @if($task->completed_at)
      <div class="date">{{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y') }}</div>
      @endif
      @if($task->proof)
      <img data-src="{{ $task->proof }}" class="proof-img lazy" onclick="showImage('{{ $task->proof }}')" alt="Proof" loading="lazy">
      @endif
    </div>
    @empty
    <div class="empty">No weekly lift tasks found</div>
    @endforelse
  </div>

  <div class="section">
    <h3>Lift - Monthly Tasks</h3>
    @forelse($liftMonthly as $task)
    <div class="task-row">
      <div class="equipment">{{ $task->equipment }}</div>
      <div class="worker">{{ $task->worker->user->name ?? 'Unassigned' }}</div>
      <span class="status {{ $task->status }}">{{ ucfirst($task->status) }}</span>
      <div class="notes">
        @if($task->description)
          {{ $task->description }}
        @endif
        @foreach($task->reports as $report)
          <div style="margin-top:4px;font-size:12px">
            <strong>{{ $report->created_at->format('M d, Y H:i') }}</strong>: {{ $report->note }}
          </div>
        @endforeach
      </div>
      @if($task->completed_at)
      <div class="date">{{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y') }}</div>
      @endif
      @if($task->proof)
      <img data-src="{{ $task->proof }}" class="proof-img lazy" onclick="showImage('{{ $task->proof }}')" alt="Proof" loading="lazy">
      @endif
    </div>
    @empty
    <div class="empty">No monthly lift tasks found</div>
    @endforelse
  </div>

  <div class="section">
    <h3>Chiller - Weekly Tasks</h3>
    @forelse($chillerWeekly as $task)
    <div class="task-row">
      <div class="equipment">{{ $task->equipment }}</div>
      <div class="worker">{{ $task->worker->user->name ?? 'Unassigned' }}</div>
      <span class="status {{ $task->status }}">{{ ucfirst($task->status) }}</span>
      <div class="notes">
        @if($task->description)
          {{ $task->description }}
        @endif
        @foreach($task->reports as $report)
          <div style="margin-top:4px;font-size:12px">
            <strong>{{ $report->created_at->format('M d, Y H:i') }}</strong>: {{ $report->note }}
          </div>
        @endforeach
      </div>
      @if($task->completed_at)
      <div class="date">{{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y') }}</div>
      @endif
      @if($task->proof)
      <img data-src="{{ $task->proof }}" class="proof-img lazy" onclick="showImage('{{ $task->proof }}')" alt="Proof" loading="lazy">
      @endif
    </div>
    @empty
    <div class="empty">No weekly chiller tasks found</div>
    @endforelse
  </div>

  <div class="section">
    <h3>Chiller - Monthly Tasks</h3>
    @forelse($chillerMonthly as $task)
    <div class="task-row">
      <div class="equipment">{{ $task->equipment }}</div>
      <div class="worker">{{ $task->worker->user->name ?? 'Unassigned' }}</div>
      <span class="status {{ $task->status }}">{{ ucfirst($task->status) }}</span>
      <div class="notes">
        @if($task->description)
          {{ $task->description }}
        @endif
        @foreach($task->reports as $report)
          <div style="margin-top:4px;font-size:12px">
            <strong>{{ $report->created_at->format('M d, Y H:i') }}</strong>: {{ $report->note }}
          </div>
        @endforeach
      </div>
      @if($task->completed_at)
      <div class="date">{{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y') }}</div>
      @endif
      @if($task->proof)
      <img data-src="{{ $task->proof }}" class="proof-img lazy" onclick="showImage('{{ $task->proof }}')" alt="Proof" loading="lazy">
      @endif
    </div>
    @empty
    <div class="empty">No monthly chiller tasks found</div>
    @endforelse
  </div>
  @endif

</main>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
  <div class="spinner"></div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal-overlay" onclick="closeModal()">
  <div class="modal-content" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="closeModal()">Close</button>
    <img id="modalImage" src="" alt="Proof Image">
  </div>
</div>

<script>
// Lazy load images to prevent freezing
document.addEventListener('DOMContentLoaded', function() {
  const lazyImages = document.querySelectorAll('img.lazy');

  // Use Intersection Observer for efficient lazy loading
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          const img = entry.target;
          const src = img.getAttribute('data-src');
          if (src) {
            img.src = src;
            img.classList.remove('lazy');
            img.classList.add('loaded');
            imageObserver.unobserve(img);
          }
        }
      });
    }, {
      rootMargin: '50px 0px', // Start loading 50px before image is visible
      threshold: 0.01
    });

    lazyImages.forEach(function(img) {
      imageObserver.observe(img);
    });
  } else {
    // Fallback for older browsers
    lazyImages.forEach(function(img) {
      const src = img.getAttribute('data-src');
      if (src) {
        img.src = src;
        img.classList.remove('lazy');
        img.classList.add('loaded');
      }
    });
  }
});

function showImage(src) {
  const modalImage = document.getElementById('modalImage');
  // Only load the full image when modal is opened
  modalImage.src = src;
  document.getElementById('imageModal').classList.add('active');
}

function closeModal() {
  document.getElementById('imageModal').classList.remove('active');
  // Clear modal image to free memory
  document.getElementById('modalImage').src = '';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeModal();
  }
});

// Show loading overlay on filter change
document.querySelectorAll('select[onchange*="submit"], input[type="date"], input[name="equipment"]').forEach(function(el) {
  el.addEventListener('change', function() {
    document.getElementById('loadingOverlay').classList.add('active');
  });
});

// Show loading when form submits
const filterForm = document.querySelector('form[method="GET"]');
if (filterForm) {
  filterForm.addEventListener('submit', function() {
    document.getElementById('loadingOverlay').classList.add('active');
  });
}
</script>
@endsection
