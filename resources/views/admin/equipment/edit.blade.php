@extends('layouts.admin')

@section('title', 'Edit Equipment')

@push('styles')
<style>
.main{flex:1;padding:22px}
.container{max-width:800px;margin:0 auto}
.card{background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.card h2{margin:0 0 8px;font-size:22px;font-weight:700}
.card p{margin:0 0 24px;color:#6b7280;font-size:14px}
.form-group{margin-bottom:20px}
label{display:block;margin-bottom:6px;font-weight:600;color:#374151;font-size:14px}
.input{width:100%;padding:10px 14px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;transition:all 0.2s;font-size:14px}
.input:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,0.1)}
.error-msg{color:#ef4444;font-size:13px;margin-top:4px}
.btn{padding:10px 20px;background:#7c3aed;color:white;border:0;border-radius:8px;cursor:pointer;font-weight:600;text-decoration:none;display:inline-block;transition:all 0.2s}
.btn:hover{background:#6d28d9}
.btn-secondary{background:#e5e7eb;color:#111}
.btn-secondary:hover{background:#d1d5db}
@media (max-width:768px){
  .main{padding:12px}
  .card{padding:16px}
}
</style>
@endpush

@section('content')
<main class="main">
  <div class="container">
    <div class="card">
      <h2>Edit Equipment</h2>
      <p>Update equipment details</p>

      <form action="{{ route('admin.equipment.update', $equipment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="equipment_code">Equipment Code <span style="color:#ef4444">*</span></label>
          <input type="text" id="equipment_code" name="equipment_code" class="input @error('equipment_code') error-input @enderror" value="{{ old('equipment_code', $equipment->equipment_code) }}" required>
          @error('equipment_code')
          <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="type">Type <span style="color:#ef4444">*</span></label>
          <input type="text" id="type" name="type" class="input @error('type') error-input @enderror" value="{{ old('type', $equipment->type) }}" required>
          @error('type')
          <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="location">Location <span style="color:#ef4444">*</span></label>
          <input type="text" id="location" name="location" class="input @error('location') error-input @enderror" value="{{ old('location', $equipment->location) }}" required>
          @error('location')
          <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="status">Status <span style="color:#ef4444">*</span></label>
          <select id="status" name="status" class="input @error('status') error-input @enderror" required>
            <option value="">Select Status</option>
            <option value="operational" {{ old('status', $equipment->status) == 'operational' ? 'selected' : '' }}>Operational</option>
            <option value="warning" {{ old('status', $equipment->status) == 'warning' ? 'selected' : '' }}>Warning</option>
            <option value="maintenance" {{ old('status', $equipment->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
          </select>
          @error('status')
          <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="last_maintenance_date">Last Maintenance Date</label>
          <input type="date" id="last_maintenance_date" name="last_maintenance_date" class="input @error('last_maintenance_date') error-input @enderror" value="{{ old('last_maintenance_date', $equipment->last_maintenance_date ? $equipment->last_maintenance_date->format('Y-m-d') : '') }}">
          @error('last_maintenance_date')
          <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>

        <div style="display:flex;gap:12px;margin-top:24px">
          <button type="submit" class="btn">Update Equipment</button>
          <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection
