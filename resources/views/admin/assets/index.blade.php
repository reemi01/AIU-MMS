@extends('layouts.admin')

@section('title', 'Add Lift And Chiller')

@section('content')
<style>
:root{--bg:#ececec;--dark:#26292c;--muted:#999;--accent:#111}
.main{flex:1;padding:22px}
h1{font-size:26px;margin:6px 0 18px;font-weight:800}
.panel{background:var(--dark);color:white;padding:26px;border-radius:6px;margin-bottom:28px}
.panel h2{margin:0 0 20px;font-size:22px}
.row{display:flex;gap:12px;align-items:center}
.input{padding:14px 18px;border-radius:18px;border:0;width:420px;font-size:16px}
.add-btn{padding:10px 16px;border-radius:14px;border:0;background:white;color:var(--dark);cursor:pointer;font-weight:600}
.add-btn:hover{background:#f1f1f1}
.list{margin-top:28px;background:#fff;padding:12px;border-radius:6px;color:#111}
.item{padding:8px 10px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center}
.item:last-child{border-bottom:0}
.remove-btn{background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:6px 8px;border-radius:6px;cursor:pointer}
.remove-btn:hover{background:#fecaca}
.empty{text-align:center;color:#999;padding:20px}
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,0.5);animation:fadeIn 0.3s}
.modal-content{background-color:#fff;margin:10% auto;padding:24px;border-radius:12px;width:90%;max-width:500px;box-shadow:0 4px 20px rgba(0,0,0,0.15);animation:slideIn 0.3s}
.modal-header{font-size:20px;font-weight:700;margin-bottom:16px;color:#111}
.modal-body{font-size:15px;color:#6b7280;margin-bottom:24px;line-height:1.6}
.modal-footer{display:flex;gap:12px;justify-content:flex-end}
.modal-btn{padding:10px 20px;border-radius:8px;border:0;cursor:pointer;font-weight:600;transition:all 0.2s}
.modal-btn-primary{background:#7c3aed;color:white}
.modal-btn-primary:hover{background:#6d28d9}
.modal-btn-danger{background:#ef4444;color:white}
.modal-btn-danger:hover{background:#dc2626}
.modal-btn-secondary{background:#f3f4f6;color:#374151}
.modal-btn-secondary:hover{background:#e5e7eb}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes slideIn{from{transform:translateY(-50px);opacity:0}to{transform:translateY(0);opacity:1}}
.task-count-badge{background:#dbeafe;color:#1e40af;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600;margin-left:8px}
@media (max-width:900px){.input{width:100%}.row{flex-direction:column;align-items:flex-start}.modal-content{width:95%;margin:20% auto}}
</style>

<main class="main">
  <h1>Add Lift And Chiller</h1>

  @if(session('success'))
  <div style="background:#d1fae5;color:#065f46;padding:12px;border-radius:6px;margin-bottom:18px">
    {{ session('success') }}
  </div>
  @endif

  <div class="panel">
    <h2>LIFT</h2>
    <form action="{{ route('admin.assets.store-lift') }}" method="POST">
      @csrf
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:32px;margin-top:24px">
        <div>
          <input name="name" class="input @error('name') error-input @enderror" placeholder="Name of the lift *" required value="{{ old('name') }}" style="width:100%" />
          @error('name')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="location" class="input @error('location') error-input @enderror" placeholder="Location" value="{{ old('location') }}" style="width:100%" />
          @error('location')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="model_number" class="input @error('model_number') error-input @enderror" placeholder="Model Number" value="{{ old('model_number') }}" style="width:100%" />
          @error('model_number')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="serial_number" class="input @error('serial_number') error-input @enderror" placeholder="Serial Number" value="{{ old('serial_number') }}" style="width:100%" />
          @error('serial_number')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="last_maintenance_date" type="date" class="input @error('last_maintenance_date') error-input @enderror" placeholder="Last Maintenance Date" value="{{ old('last_maintenance_date') }}" style="width:100%" />
          @error('last_maintenance_date')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div style="display:flex;align-items:flex-start">
          <button type="submit" class="add-btn" style="height:48px">Add Lift</button>
        </div>
      </div>
    </form>
    <div class="list">
      <table style="width:100%;border-collapse:collapse">
        <thead style="border-bottom:2px solid #e5e7eb">
          <tr>
            <th style="padding:10px;text-align:left;font-weight:700">Name</th>
            <th style="padding:10px;text-align:left;font-weight:700">Location</th>
            <th style="padding:10px;text-align:left;font-weight:700">Model</th>
            <th style="padding:10px;text-align:left;font-weight:700">Serial</th>
            <th style="padding:10px;text-align:left;font-weight:700">Last Maintenance</th>
            <th style="padding:10px;text-align:left;font-weight:700">Tasks</th>
            <th style="padding:10px;text-align:left;font-weight:700">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lifts as $lift)
          <tr style="border-bottom:1px solid #f3f4f6" id="lift-row-{{ $lift->id }}">
            <td style="padding:10px;font-weight:600" class="editable-cell" data-field="name">{{ $lift->name }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="location">{{ $lift->location ?? '-' }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="model_number">{{ $lift->model_number ?? '-' }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="serial_number">{{ $lift->serial_number ?? '-' }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="last_maintenance_date" data-value="{{ $lift->last_maintenance_date?->format('Y-m-d') }}">
              {{ $lift->last_maintenance_date ? $lift->last_maintenance_date->format('M d, Y') : '-' }}
            </td>
            <td style="padding:10px">
              <span class="task-count-badge">{{ $lift->tasks_count }} {{ Str::plural('task', $lift->tasks_count) }}</span>
            </td>
            <td style="padding:10px">
              <button type="button" class="edit-btn" onclick="confirmEdit('lift', {{ $lift->id }}, {{ json_encode($lift) }})" style="background:#dbeafe;border:1px solid #3b82f6;color:#1e40af;padding:6px 12px;border-radius:6px;cursor:pointer;margin-right:6px">Edit</button>
              <button type="button" class="remove-btn" onclick="confirmDelete('lift', {{ $lift->id }}, '{{ addslashes($lift->name) }}', {{ $lift->tasks_count }})">Delete</button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="empty">No lifts added yet</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <h2>Chiller</h2>
    <form action="{{ route('admin.assets.store-chiller') }}" method="POST">
      @csrf
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:32px;margin-top:24px">
        <div>
          <input name="name" class="input @error('name') error-input @enderror" placeholder="Name of the chiller *" required value="{{ old('name') }}" style="width:100%" />
          @error('name')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="location" class="input @error('location') error-input @enderror" placeholder="Location" value="{{ old('location') }}" style="width:100%" />
          @error('location')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="model_number" class="input @error('model_number') error-input @enderror" placeholder="Model Number" value="{{ old('model_number') }}" style="width:100%" />
          @error('model_number')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="serial_number" class="input @error('serial_number') error-input @enderror" placeholder="Serial Number" value="{{ old('serial_number') }}" style="width:100%" />
          @error('serial_number')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div>
          <input name="last_maintenance_date" type="date" class="input @error('last_maintenance_date') error-input @enderror" placeholder="Last Maintenance Date" value="{{ old('last_maintenance_date') }}" style="width:100%" />
          @error('last_maintenance_date')
          <div style="color:#ef4444;margin-top:6px;font-size:13px">{{ $message }}</div>
          @enderror
        </div>
        <div style="display:flex;align-items:flex-start">
          <button type="submit" class="add-btn" style="height:48px">Add Chiller</button>
        </div>
      </div>
    </form>
    <div class="list">
      <table style="width:100%;border-collapse:collapse">
        <thead style="border-bottom:2px solid #e5e7eb">
          <tr>
            <th style="padding:10px;text-align:left;font-weight:700">Name</th>
            <th style="padding:10px;text-align:left;font-weight:700">Location</th>
            <th style="padding:10px;text-align:left;font-weight:700">Model</th>
            <th style="padding:10px;text-align:left;font-weight:700">Serial</th>
            <th style="padding:10px;text-align:left;font-weight:700">Last Maintenance</th>
            <th style="padding:10px;text-align:left;font-weight:700">Tasks</th>
            <th style="padding:10px;text-align:left;font-weight:700">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($chillers as $chiller)
          <tr style="border-bottom:1px solid #f3f4f6" id="chiller-row-{{ $chiller->id }}">
            <td style="padding:10px;font-weight:600" class="editable-cell" data-field="name">{{ $chiller->name }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="location">{{ $chiller->location ?? '-' }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="model_number">{{ $chiller->model_number ?? '-' }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="serial_number">{{ $chiller->serial_number ?? '-' }}</td>
            <td style="padding:10px;color:#6b7280" class="editable-cell" data-field="last_maintenance_date" data-value="{{ $chiller->last_maintenance_date?->format('Y-m-d') }}">
              {{ $chiller->last_maintenance_date ? $chiller->last_maintenance_date->format('M d, Y') : '-' }}
            </td>
            <td style="padding:10px">
              <span class="task-count-badge">{{ $chiller->tasks_count }} {{ Str::plural('task', $chiller->tasks_count) }}</span>
            </td>
            <td style="padding:10px">
              <button type="button" class="edit-btn" onclick="confirmEdit('chiller', {{ $chiller->id }}, {{ json_encode($chiller) }})" style="background:#dbeafe;border:1px solid #3b82f6;color:#1e40af;padding:6px 12px;border-radius:6px;cursor:pointer;margin-right:6px">Edit</button>
              <button type="button" class="remove-btn" onclick="confirmDelete('chiller', {{ $chiller->id }}, '{{ addslashes($chiller->name) }}', {{ $chiller->tasks_count }})">Delete</button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="empty">No chillers added yet</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Edit Confirmation Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">Confirm Edit</div>
      <div class="modal-body">
        Are you sure you want to edit <strong id="editItemName"></strong>?
      </div>
      <div class="modal-footer">
        <button class="modal-btn modal-btn-secondary" onclick="closeModal('editModal')">Cancel</button>
        <button class="modal-btn modal-btn-primary" id="confirmEditBtn">Continue to Edit</button>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">Confirm Delete</div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
        <p id="deleteWarning" style="color:#ef4444;font-weight:600;margin-top:12px"></p>
        <p style="color:#6b7280;font-size:14px;margin-top:8px">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button class="modal-btn modal-btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
        <button class="modal-btn modal-btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>

  <!-- Save Confirmation Modal -->
  <div id="saveModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">Confirm Changes</div>
      <div class="modal-body">
        Are you sure you want to save these changes?
      </div>
      <div class="modal-footer">
        <button class="modal-btn modal-btn-secondary" onclick="closeModal('saveModal')">Cancel</button>
        <button class="modal-btn modal-btn-primary" id="confirmSaveBtn">Save Changes</button>
      </div>
    </div>
  </div>

</main>

<script>
let currentEditType = null;
let currentEditId = null;
let currentEditData = null;
let currentSaveId = null;
let currentSaveType = null;

// Modal functions
function openModal(modalId) {
  document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.style.display = 'none';
  }
}

// Confirm edit
function confirmEdit(type, id, data) {
  currentEditType = type;
  currentEditId = id;
  currentEditData = data;
  document.getElementById('editItemName').textContent = data.name;
  openModal('editModal');
}

document.getElementById('confirmEditBtn').onclick = function() {
  closeModal('editModal');
  if (currentEditType === 'lift') {
    editLift(currentEditId, currentEditData);
  } else {
    editChiller(currentEditId, currentEditData);
  }
}

// Confirm delete
function confirmDelete(type, id, name, taskCount) {
  const warning = document.getElementById('deleteWarning');
  if (taskCount > 0) {
    warning.textContent = `⚠️ Warning: This ${type} has ${taskCount} task(s) assigned. Deleting it may affect those tasks.`;
    warning.style.display = 'block';
  } else {
    warning.style.display = 'none';
  }

  document.getElementById('deleteItemName').textContent = name;

  document.getElementById('confirmDeleteBtn').onclick = function() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/assets/${type}/${id}`;
    form.innerHTML = `
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
  };

  openModal('deleteModal');
}

// Confirm save
function confirmSave(type, id) {
  currentSaveType = type;
  currentSaveId = id;
  openModal('saveModal');
}

document.getElementById('confirmSaveBtn').onclick = function() {
  closeModal('saveModal');
  if (currentSaveType === 'lift') {
    saveLift(currentSaveId);
  } else {
    saveChiller(currentSaveId);
  }
}

// Edit functions
function editLift(id, lift) {
  const row = document.getElementById(`lift-row-${id}`);
  const cells = row.querySelectorAll('.editable-cell');

  cells.forEach(cell => {
    const field = cell.dataset.field;
    const value = field === 'last_maintenance_date' ? (cell.dataset.value || '') : (lift[field] || '');

    if (field === 'last_maintenance_date') {
      cell.innerHTML = `<input type="date" value="${value}" class="input" style="padding:8px;width:100%;max-width:200px" data-field="${field}">`;
    } else {
      cell.innerHTML = `<input type="text" value="${value}" class="input" style="padding:8px;width:100%" data-field="${field}">`;
    }
  });

  const actionCell = row.querySelector('td:last-child');
  actionCell.innerHTML = `
    <button type="button" onclick="confirmSave('lift', ${id})" style="background:#10b981;border:1px solid #059669;color:white;padding:6px 12px;border-radius:6px;cursor:pointer;margin-right:6px">Save</button>
    <button type="button" onclick="location.reload()" style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:6px 12px;border-radius:6px;cursor:pointer">Cancel</button>
  `;
}

function editChiller(id, chiller) {
  const row = document.getElementById(`chiller-row-${id}`);
  const cells = row.querySelectorAll('.editable-cell');

  cells.forEach(cell => {
    const field = cell.dataset.field;
    const value = field === 'last_maintenance_date' ? (cell.dataset.value || '') : (chiller[field] || '');

    if (field === 'last_maintenance_date') {
      cell.innerHTML = `<input type="date" value="${value}" class="input" style="padding:8px;width:100%;max-width:200px" data-field="${field}">`;
    } else {
      cell.innerHTML = `<input type="text" value="${value}" class="input" style="padding:8px;width:100%" data-field="${field}">`;
    }
  });

  const actionCell = row.querySelector('td:last-child');
  actionCell.innerHTML = `
    <button type="button" onclick="confirmSave('chiller', ${id})" style="background:#10b981;border:1px solid #059669;color:white;padding:6px 12px;border-radius:6px;cursor:pointer;margin-right:6px">Save</button>
    <button type="button" onclick="location.reload()" style="background:#f3f4f6;border:1px solid #e5e7eb;color:#374151;padding:6px 12px;border-radius:6px;cursor:pointer">Cancel</button>
  `;
}

// Save functions
function saveLift(id) {
  const row = document.getElementById(`lift-row-${id}`);
  const inputs = row.querySelectorAll('input[data-field]');

  const formData = new FormData();
  formData.append('_token', '{{ csrf_token() }}');
  formData.append('_method', 'PATCH');

  inputs.forEach(input => {
    formData.append(input.dataset.field, input.value);
  });

  fetch(`/admin/assets/lift/${id}`, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    const contentType = response.headers.get('content-type');
    if (!response.ok) {
      if (contentType && contentType.includes('application/json')) {
        return response.json().then(data => {
          throw new Error(JSON.stringify(data));
        });
      } else {
        throw new Error('Error updating lift. Please try again.');
      }
    }
    // Success - just reload
    location.reload();
  })
  .catch(error => {
    try {
      const errorData = JSON.parse(error.message);
      if (errorData.errors) {
        let errorMessage = 'Validation errors:\n';
        Object.values(errorData.errors).forEach(msgs => {
          msgs.forEach(msg => errorMessage += '• ' + msg + '\n');
        });
        alert(errorMessage);
      } else {
        alert(errorData.message || 'Error updating lift. Please try again.');
      }
    } catch (e) {
      alert(error.message || 'Error updating lift. Please try again.');
    }
    console.error('Error:', error);
  });
}

function saveChiller(id) {
  const row = document.getElementById(`chiller-row-${id}`);
  const inputs = row.querySelectorAll('input[data-field]');

  const formData = new FormData();
  formData.append('_token', '{{ csrf_token() }}');
  formData.append('_method', 'PATCH');

  inputs.forEach(input => {
    formData.append(input.dataset.field, input.value);
  });

  fetch(`/admin/assets/chiller/${id}`, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    const contentType = response.headers.get('content-type');
    if (!response.ok) {
      if (contentType && contentType.includes('application/json')) {
        return response.json().then(data => {
          throw new Error(JSON.stringify(data));
        });
      } else {
        throw new Error('Error updating chiller. Please try again.');
      }
    }
    // Success - just reload
    location.reload();
  })
  .catch(error => {
    try {
      const errorData = JSON.parse(error.message);
      if (errorData.errors) {
        let errorMessage = 'Validation errors:\n';
        Object.values(errorData.errors).forEach(msgs => {
          msgs.forEach(msg => errorMessage += '• ' + msg + '\n');
        });
        alert(errorMessage);
      } else {
        alert(errorData.message || 'Error updating chiller. Please try again.');
      }
    } catch (e) {
      alert(error.message || 'Error updating chiller. Please try again.');
    }
    console.error('Error:', error);
  });
}
</script>

@endsection
