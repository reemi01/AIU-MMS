@extends('layouts.admin')

@section('title', 'Task Management')

@push('styles')
<style>
@media (max-width: 768px) {
  .main { padding: 12px !important; }
  h1 { font-size: 24px !important; }
  .form { padding: 12px !important; }
  form > div[style*="grid"] {
    grid-template-columns: 1fr !important;
  }
  table {
    display: block;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    font-size: 13px;
  }
  th, td {
    white-space: nowrap;
    padding: 8px !important;
  }
}

@media print {
  body { background: white !important; }
  .main { padding: 0 !important; }
  .form, form, button, a, #bulkActions, #bulkAssignModal, .no-print { display: none !important; }
  .board { box-shadow: none !important; border: 1px solid #e5e7eb; }
  table { font-size: 11px; }
  th, td { padding: 8px !important; }
  th:first-child, td:first-child { display: none; } /* Hide checkboxes */
  th:last-child, td:last-child { display: none; } /* Hide actions */
  @page { margin: 1cm; }
}
</style>
@endpush

@section('content')
<div class="main" style="padding:22px">
  <h1 style="text-align:center;font-size:36px;margin:6px 0 18px;font-weight:800">Task Management</h1>

  <!-- Quick Actions Section -->
  <div id="assign-task" style="font-size:22px;font-weight:800;margin-top:32px;margin-bottom:12px">Quick Actions - Assign Task to Worker</div>

  <div class="form" style="background:#fff;padding:18px;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,0.05)">
    @if($errors->any())
    <div style="background:#fee2e2;color:#b91c1c;padding:12px;border-radius:6px;margin-bottom:18px;border-left:4px solid #ef4444">
      <strong>Please fix the following errors:</strong>
      <ul style="margin:8px 0 0 0;padding-left:20px">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form action="{{ route('admin.tasks.store') }}" method="POST">
      @csrf

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Frequency *</label>
        <select name="frequency" id="taskFrequency" class="input @error('frequency') error-input @enderror" required onchange="updateTaskTemplates()">
          <option value="">Select frequency</option>
          <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
          <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
        </select>
        @error('frequency')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Equipment Type *</label>
        <select name="type" id="taskType" class="input @error('type') error-input @enderror" required onchange="updateTaskTemplates()">
          <option value="">Select type</option>
          <option value="Lift" {{ old('type') == 'Lift' ? 'selected' : '' }}>Lift</option>
          <option value="Chiller" {{ old('type') == 'Chiller' ? 'selected' : '' }}>Chiller</option>
        </select>
        @error('type')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Task Title *</label>
        <select name="task_template_id" id="taskTemplate" class="input @error('task_template_id') error-input @enderror" required onchange="updateTaskDescription()">
          <option value="">Select frequency and equipment type first</option>
        </select>
        @error('task_template_id')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Equipment *</label>
        <select name="equipment" id="equipment" class="input @error('equipment') error-input @enderror" required>
          <option value="">Select equipment type first</option>
        </select>
        @error('equipment')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Priority *</label>
        <select name="priority" class="input @error('priority') error-input @enderror" required>
          <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🟢 Low</option>
          <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>🔵 Normal</option>
          <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🟠 High</option>
          <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
        </select>
        @error('priority')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Assign Worker *</label>
        <select name="worker_id" class="input @error('worker_id') error-input @enderror" required>
          <option value="">Assign task to worker</option>
          @foreach($workers as $worker)
            <option value="{{ $worker->id }}" {{ old('worker_id') == $worker->id ? 'selected' : '' }}>
              {{ $worker->user->name }} ({{ $worker->trade }})
            </option>
          @endforeach
        </select>
        @error('worker_id')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Scheduled Date *</label>
        <input name="scheduled_date" type="date" class="input @error('scheduled_date') error-input @enderror" required value="{{ old('scheduled_date', date('Y-m-d')) }}" />
        @error('scheduled_date')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="display:block;margin-bottom:6px;font-weight:600;font-size:14px">Description (Auto-filled from template)</label>
        <textarea name="description" id="taskDescription" rows="3" class="input @error('description') error-input @enderror" placeholder="Description will be auto-filled" readonly style="background:#f9fafb">{{ old('description') }}</textarea>
        @error('description')<span style="color:#ef4444;font-size:13px">{{ $message }}</span>@enderror
      </div>

      <div style="text-align:center">
        <button type="submit" class="btn" style="background:#4f46e5;color:#fff;padding:12px 24px;border:0;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s">Create Task</button>
      </div>
    </form>
  </div>

  <div class="board" style="background:#fff;padding:18px;margin-top:18px;border-radius:6px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
      <h3 style="margin:0">Task Board</h3>
      <div style="display:flex;gap:8px;align-items:center;">
        <button onclick="window.print()" class="no-print" style="padding:8px 16px;background:#10b981;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600;">🖨️ Print</button>
        <div id="bulkActions" style="display:none;gap:8px;">
        <button onclick="bulkUpdateStatus('pending')" style="padding:8px 12px;background:#f59e0b;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600;">Set Pending</button>
        <button onclick="bulkUpdateStatus('inprogress')" style="padding:8px 12px;background:#3b82f6;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600;">Set In Progress</button>
        <button onclick="bulkUpdateStatus('completed')" style="padding:8px 12px;background:#10b981;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600;">Set Completed</button>
        <button onclick="showBulkAssignModal()" style="padding:8px 12px;background:#7c3aed;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600;">Reassign</button>
        <button onclick="bulkDelete()" style="padding:8px 12px;background:#ef4444;color:#fff;border:0;border-radius:6px;cursor:pointer;font-weight:600;">Delete Selected</button>
        </div>
      </div>
    </div>

    <!-- Task Filters -->
    <form method="GET" style="background:#f9fafb;padding:16px;border-radius:8px;margin-bottom:16px">
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px">
        <input name="search" placeholder="Search tasks..." value="{{ request('search') }}" class="input" style="padding:8px 12px"/>

        <select name="status" class="input" style="padding:8px 12px" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="inprogress" {{ request('status') == 'inprogress' ? 'selected' : '' }}>In Progress</option>
          <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        </select>

        <select name="type" class="input" style="padding:8px 12px" onchange="this.form.submit()">
          <option value="">All Types</option>
          <option value="Lift" {{ request('type') == 'Lift' ? 'selected' : '' }}>Lift</option>
          <option value="Chiller" {{ request('type') == 'Chiller' ? 'selected' : '' }}>Chiller</option>
        </select>

        <select name="worker_id" class="input" style="padding:8px 12px" onchange="this.form.submit()">
          <option value="">All Workers</option>
          @foreach($workers as $worker)
          <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
            {{ $worker->user->name }}
          </option>
          @endforeach
        </select>

        <select name="priority" class="input" style="padding:8px 12px" onchange="this.form.submit()">
          <option value="">All Priorities</option>
          <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>🟢 Low</option>
          <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>🔵 Normal</option>
          <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>🟠 High</option>
          <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
        </select>

        <input name="date_from" type="date" placeholder="From Date" value="{{ request('date_from') }}" class="input" style="padding:8px 12px"/>

        <input name="date_to" type="date" placeholder="To Date" value="{{ request('date_to') }}" class="input" style="padding:8px 12px"/>

        <button type="submit" style="padding:8px 16px;background:#4f46e5;color:#fff;border:0;border-radius:8px;cursor:pointer;font-weight:600">Filter</button>

        @if(request()->hasAny(['search', 'status', 'type', 'worker_id', 'priority', 'date_from', 'date_to']))
        <a href="{{ route('admin.tasks.index') }}" style="padding:8px 16px;background:#fff;color:#111;border:1px solid #e5e7eb;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center">Clear Filters</a>
        @endif
      </div>
    </form>

    <div style="overflow:auto;margin-top:12px">
      <table style="width:100%;border-collapse:collapse">
        <thead style="font-weight:800">
          <tr>
            <th style="padding:12px;text-align:left;width:40px;">
              <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" style="cursor:pointer;">
            </th>
            <th style="padding:12px;text-align:left">Task</th>
            <th style="padding:12px;text-align:left">Worker</th>
            <th style="padding:12px;text-align:left">Type</th>
            <th style="padding:12px;text-align:left">Equipment</th>
            <th style="padding:12px;text-align:left">Frequency</th>
            <th style="padding:12px;text-align:left">Priority</th>
            <th style="padding:12px;text-align:left">Status</th>
            <th style="padding:12px;text-align:left">Deadline</th>
            <th style="padding:12px;text-align:left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $task)
          @php
          $isOverdue = in_array($task->status, ['pending', 'inprogress']) && $task->scheduled_date->isPast();
          @endphp
          <tr style="border-bottom:1px solid #f1f1f1;{{ $isOverdue ? 'background:#fef2f2' : '' }}">
            <td style="padding:12px;">
              <input type="checkbox" class="task-checkbox" value="{{ $task->id }}" onchange="updateBulkActions()" style="cursor:pointer;">
            </td>
            <td style="padding:12px">
              {{ $task->title }}
              @if($isOverdue)
              <span style="background:#ef4444;color:#fff;padding:2px 6px;border-radius:4px;font-size:11px;margin-left:6px">⚠️ OVERDUE</span>
              @endif
            </td>
            <td style="padding:12px">{{ $task->worker->user->name ?? 'Unassigned' }}</td>
            <td style="padding:12px">{{ $task->type }}</td>
            <td style="padding:12px">
              <span style="font-weight:600;color:#374151">{{ $task->equipment }}</span>
              @if($task->lift || $task->chiller)
              <br>
              <span style="font-size:12px;color:#6b7280">
                @if($task->lift)
                  📍 {{ $task->lift->location ?? 'No location' }}
                @elseif($task->chiller)
                  📍 {{ $task->chiller->location ?? 'No location' }}
                @endif
              </span>
              @endif
            </td>
            <td style="padding:12px">{{ ucfirst($task->frequency) }}</td>
            <td style="padding:12px">
              @php
              $priorityColors = [
                'low' => ['bg' => '#d1fae5', 'text' => '#065f46', 'icon' => '🟢'],
                'normal' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'icon' => '🔵'],
                'high' => ['bg' => '#fed7aa', 'text' => '#9a3412', 'icon' => '🟠'],
                'urgent' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'icon' => '🔴'],
              ];
              $priority = $task->priority ?? 'normal';
              $pColor = $priorityColors[$priority];
              @endphp
              <span style="background:{{ $pColor['bg'] }};color:{{ $pColor['text'] }};padding:4px 8px;border-radius:12px;font-size:12px;font-weight:600">
                {{ $pColor['icon'] }} {{ ucfirst($priority) }}
              </span>
            </td>
            <td style="padding:12px">
              @if($task->status === 'completed')
              <span style="background:#10b981;color:#fff;padding:4px 8px;border-radius:12px;font-size:12px">Completed</span>
              @elseif($task->status === 'inprogress')
              <span style="background:#0ea5e9;color:#fff;padding:4px 8px;border-radius:12px;font-size:12px">In Progress</span>
              @else
              <span style="background:#f59e0b;color:#422006;padding:4px 8px;border-radius:12px;font-size:12px">Pending</span>
              @endif
            </td>
            <td style="padding:12px">{{ $task->scheduled_date->format('M d, Y') }}</td>
            <td style="padding:12px">
              <a href="{{ route('admin.tasks.show', $task) }}" style="background:#10b981;color:#fff;padding:6px 12px;border:0;border-radius:4px;text-decoration:none;display:inline-block;margin-right:4px">View</a>
              <button type="button" onclick="confirmTaskDelete({{ $task->id }}, '{{ addslashes($task->title) }}')" style="background:#ef4444;color:#fff;padding:6px 12px;border:0;border-radius:4px;cursor:pointer">Remove</button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" style="padding:24px;text-align:center;color:#999">No tasks created yet</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
const lifts = @json($lifts->pluck('name'));
const chillers = @json($chillers->pluck('name'));
const taskTemplates = @json($taskTemplates);

function updateTaskTemplates() {
  const frequency = document.getElementById('taskFrequency').value;
  const type = document.getElementById('taskType').value;
  const templateSelect = document.getElementById('taskTemplate');
  const equipmentSelect = document.getElementById('equipment');

  // Clear task template dropdown
  templateSelect.innerHTML = '<option value="">Select task template</option>';

  // Update equipment dropdown based on type
  equipmentSelect.innerHTML = '<option value="">Select equipment</option>';

  if (type === 'Lift') {
    lifts.forEach(lift => {
      equipmentSelect.appendChild(new Option(lift, lift));
    });
  } else if (type === 'Chiller') {
    chillers.forEach(chiller => {
      equipmentSelect.appendChild(new Option(chiller, chiller));
    });
  }

  // Filter task templates based on frequency and type
  if (frequency && type) {
    const filteredTemplates = taskTemplates.filter(template =>
      template.frequency === frequency && template.type === type
    );

    if (filteredTemplates.length > 0) {
      filteredTemplates.forEach(template => {
        const option = new Option(template.title, template.id);
        option.setAttribute('data-description', template.description || '');
        templateSelect.appendChild(option);
      });
    } else {
      templateSelect.innerHTML = '<option value="">No templates found for this combination</option>';
    }
  }
}

function updateTaskDescription() {
  const templateSelect = document.getElementById('taskTemplate');
  const descriptionField = document.getElementById('taskDescription');
  const selectedOption = templateSelect.options[templateSelect.selectedIndex];

  if (selectedOption && selectedOption.hasAttribute('data-description')) {
    descriptionField.value = selectedOption.getAttribute('data-description');
  } else {
    descriptionField.value = '';
  }
}

// Bulk operations
function toggleSelectAll(checkbox) {
  const checkboxes = document.querySelectorAll('.task-checkbox');
  checkboxes.forEach(cb => cb.checked = checkbox.checked);
  updateBulkActions();
}

function updateBulkActions() {
  const checkboxes = document.querySelectorAll('.task-checkbox:checked');
  const bulkActions = document.getElementById('bulkActions');
  if (checkboxes.length > 0) {
    bulkActions.style.display = 'flex';
  } else {
    bulkActions.style.display = 'none';
  }
}

function getSelectedTaskIds() {
  const checkboxes = document.querySelectorAll('.task-checkbox:checked');
  return Array.from(checkboxes).map(cb => cb.value);
}

function bulkUpdateStatus(status) {
  const taskIds = getSelectedTaskIds();
  if (taskIds.length === 0) return;

  if (!confirm(`Update ${taskIds.length} task(s) to ${status}?`)) return;

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("admin.tasks.bulk-status") }}';

  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = '{{ csrf_token() }}';
  form.appendChild(csrfInput);

  const statusInput = document.createElement('input');
  statusInput.type = 'hidden';
  statusInput.name = 'status';
  statusInput.value = status;
  form.appendChild(statusInput);

  taskIds.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'task_ids[]';
    input.value = id;
    form.appendChild(input);
  });

  document.body.appendChild(form);
  form.submit();
}

function bulkDelete() {
  const taskIds = getSelectedTaskIds();
  if (taskIds.length === 0) return;

  if (!confirm(`Delete ${taskIds.length} task(s)? This cannot be undone.`)) return;

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("admin.tasks.bulk-delete") }}';

  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = '{{ csrf_token() }}';
  form.appendChild(csrfInput);

  taskIds.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'task_ids[]';
    input.value = id;
    form.appendChild(input);
  });

  document.body.appendChild(form);
  form.submit();
}

function showBulkAssignModal() {
  const taskIds = getSelectedTaskIds();
  if (taskIds.length === 0) return;

  document.getElementById('bulkAssignModal').style.display = 'flex';
  document.getElementById('bulkTaskCount').textContent = taskIds.length;
}

function closeBulkAssignModal() {
  document.getElementById('bulkAssignModal').style.display = 'none';
}

function submitBulkAssign() {
  const taskIds = getSelectedTaskIds();
  const workerId = document.getElementById('bulkWorkerId').value;

  if (!workerId) {
    alert('Please select a worker');
    return;
  }

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("admin.tasks.bulk-assign") }}';

  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = '{{ csrf_token() }}';
  form.appendChild(csrfInput);

  const workerInput = document.createElement('input');
  workerInput.type = 'hidden';
  workerInput.name = 'worker_id';
  workerInput.value = workerId;
  form.appendChild(workerInput);

  taskIds.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'task_ids[]';
    input.value = id;
    form.appendChild(input);
  });

  document.body.appendChild(form);
  form.submit();
}

// Task delete confirmation
let currentDeleteTaskId = null;

function confirmTaskDelete(taskId, taskTitle) {
  currentDeleteTaskId = taskId;
  document.getElementById('deleteTaskName').textContent = taskTitle;
  document.getElementById('taskDeleteModal').style.display = 'flex';
}

function closeTaskDeleteModal() {
  document.getElementById('taskDeleteModal').style.display = 'none';
  currentDeleteTaskId = null;
}

function submitTaskDelete() {
  if (!currentDeleteTaskId) return;

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/admin/tasks/${currentDeleteTaskId}`;

  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = '{{ csrf_token() }}';
  form.appendChild(csrfInput);

  const methodInput = document.createElement('input');
  methodInput.type = 'hidden';
  methodInput.name = '_method';
  methodInput.value = 'DELETE';
  form.appendChild(methodInput);

  document.body.appendChild(form);
  form.submit();
}
</script>

<!-- Bulk Assign Modal -->
<div id="bulkAssignModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:24px;border-radius:8px;max-width:400px;width:90%;">
    <h3 style="margin:0 0 16px;">Bulk Assign Tasks</h3>
    <p style="margin:0 0 16px;color:#6b7280;">Assign <span id="bulkTaskCount"></span> selected task(s) to:</p>
    <select id="bulkWorkerId" class="input" style="margin-bottom:16px;">
      <option value="">Select worker</option>
      @foreach($workers as $worker)
      <option value="{{ $worker->id }}">{{ $worker->user->name }} ({{ $worker->trade }})</option>
      @endforeach
    </select>
    <div style="display:flex;gap:12px;justify-content:flex-end;">
      <button onclick="closeBulkAssignModal()" style="padding:10px 20px;background:#6b7280;color:#fff;border:0;border-radius:6px;cursor:pointer;">Cancel</button>
      <button onclick="submitBulkAssign()" style="padding:10px 20px;background:#7c3aed;color:#fff;border:0;border-radius:6px;cursor:pointer;">Assign</button>
    </div>
  </div>
</div>

<!-- Task Delete Confirmation Modal -->
<div id="taskDeleteModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;padding:24px;border-radius:12px;max-width:450px;width:90%;box-shadow:0 4px 20px rgba(0,0,0,0.15);">
    <h3 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#111;">Confirm Delete</h3>
    <p style="margin:0 0 8px;color:#6b7280;font-size:15px;line-height:1.6;">Are you sure you want to delete this task?</p>
    <p style="margin:0 0 16px;color:#111;font-weight:600;"><strong id="deleteTaskName"></strong></p>
    <p style="color:#ef4444;font-size:14px;margin:0 0 24px;">This action cannot be undone.</p>
    <div style="display:flex;gap:12px;justify-content:flex-end;">
      <button onclick="closeTaskDeleteModal()" style="padding:10px 20px;background:#f3f4f6;color:#374151;border:0;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.2s;">Cancel</button>
      <button onclick="submitTaskDelete()" style="padding:10px 20px;background:#ef4444;color:#fff;border:0;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.2s;">Delete</button>
    </div>
  </div>
</div>
@endsection
