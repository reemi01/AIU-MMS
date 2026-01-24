@extends('layouts.admin')

@section('title', 'Weekly and Monthly Tasks')

@section('content')
<style>
:root{--bg:#efefef;--muted:#6b7280;--accent:#7c3aed}
.main{flex:1;padding:22px}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.title{font-size:30px;text-align:center;font-weight:800;margin:6px 0 18px}
.card{background:white;border-radius:8px;padding:18px;box-shadow:0 6px 18px rgba(2,6,23,.04);margin-bottom:18px}
.form-row{display:flex;gap:12px;align-items:center}
.form-row .box{flex:1;padding:14px;border-radius:6px;background:#efefef;border:1px solid #e1e1e1;text-align:center;font-weight:800}
.form-row .title-input{flex:2;padding:14px;border-radius:6px;border:1px solid #e6e6ef}
.add-btn{padding:12px 18px;background:#111;color:#fff;border:0;border-radius:6px;cursor:pointer}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.task-box{min-height:120px;border-radius:6px;background:white;padding:18px;box-shadow:0 4px 12px rgba(2,6,23,.04)}
.task-box h3{margin:0 0 12px}
.task{padding:12px;border-radius:6px;border:1px solid #f1f1f1;margin-bottom:8px;background:#fafafa}
.task .desc{color:var(--muted);margin-top:8px;font-size:14px}
.task .remove{float:right;background:transparent;border:0;color:#b91c1c;cursor:pointer;font-size:13px}
@media (max-width:900px){.grid{grid-template-columns:1fr}.form-row{flex-direction:column}}
</style>

<main class="main">
  <h1 class="title">Weekly and Monthly Task</h1>

  <div class="card">
    <h3 style="margin-top:0">Add New Task Template</h3>

    @if($errors->any())
    <div style="background:#fee2e2;color:#b91c1c;padding:12px;border-radius:6px;margin-bottom:12px;border-left:4px solid #ef4444">
      <strong>Please fix the following errors:</strong>
      <ul style="margin:8px 0 0 0;padding-left:20px;font-size:13px">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form action="{{ route('admin.weekly-monthly.store') }}" method="POST">
      @csrf
      <div class="form-row">
        <select name="type" class="box @error('type') error-input @enderror" required>
          <option value="Lift" {{ old('type') == 'Lift' ? 'selected' : '' }}>LIFT/ CHILLER - Lift</option>
          <option value="Chiller" {{ old('type') == 'Chiller' ? 'selected' : '' }}>LIFT/ CHILLER - Chiller</option>
        </select>
        <select name="frequency" class="box @error('frequency') error-input @enderror" required>
          <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>WEEKLY</option>
          <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>MONTHLY</option>
        </select>
        <input name="title" class="title-input @error('title') error-input @enderror" placeholder="Task Title *" required value="{{ old('title') }}" />
        <button type="submit" class="add-btn">ADD</button>
      </div>
      <div style="margin-top:12px">
        <textarea name="description" rows="3" class="@error('description') error-input @enderror" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e6e6ef" placeholder="Description (optional)">{{ old('description') }}</textarea>
        @error('description')<span style="color:#ef4444;font-size:13px;display:block;margin-top:4px">{{ $message }}</span>@enderror
      </div>
    </form>
  </div>

  <div class="grid">
    <div class="task-box">
      <h3>Lift Weekly Task</h3>
      <div>
        @forelse($liftWeekly as $template)
        <div class="task">
          <strong>{{ $template->title }}</strong>
          <form action="{{ route('admin.weekly-monthly.destroy', $template) }}" method="POST" style="display:inline;float:right">
            @csrf
            @method('DELETE')
            <button type="submit" class="remove" onclick="return confirm('Remove this template?')">Remove</button>
          </form>
          @if($template->description)
          <div class="desc">{{ $template->description }}</div>
          @endif
        </div>
        @empty
        <div style="color:#999;text-align:center;padding:20px">No templates added yet</div>
        @endforelse
      </div>
    </div>

    <div class="task-box">
      <h3>Lift Monthly Task</h3>
      <div>
        @forelse($liftMonthly as $template)
        <div class="task">
          <strong>{{ $template->title }}</strong>
          <form action="{{ route('admin.weekly-monthly.destroy', $template) }}" method="POST" style="display:inline;float:right">
            @csrf
            @method('DELETE')
            <button type="submit" class="remove" onclick="return confirm('Remove this template?')">Remove</button>
          </form>
          @if($template->description)
          <div class="desc">{{ $template->description }}</div>
          @endif
        </div>
        @empty
        <div style="color:#999;text-align:center;padding:20px">No templates added yet</div>
        @endforelse
      </div>
    </div>

    <div class="task-box">
      <h3>Chiller Weekly Task</h3>
      <div>
        @forelse($chillerWeekly as $template)
        <div class="task">
          <strong>{{ $template->title }}</strong>
          <form action="{{ route('admin.weekly-monthly.destroy', $template) }}" method="POST" style="display:inline;float:right">
            @csrf
            @method('DELETE')
            <button type="submit" class="remove" onclick="return confirm('Remove this template?')">Remove</button>
          </form>
          @if($template->description)
          <div class="desc">{{ $template->description }}</div>
          @endif
        </div>
        @empty
        <div style="color:#999;text-align:center;padding:20px">No templates added yet</div>
        @endforelse
      </div>
    </div>

    <div class="task-box">
      <h3>Chiller Monthly Task</h3>
      <div>
        @forelse($chillerMonthly as $template)
        <div class="task">
          <strong>{{ $template->title }}</strong>
          <form action="{{ route('admin.weekly-monthly.destroy', $template) }}" method="POST" style="display:inline;float:right">
            @csrf
            @method('DELETE')
            <button type="submit" class="remove" onclick="return confirm('Remove this template?')">Remove</button>
          </form>
          @if($template->description)
          <div class="desc">{{ $template->description }}</div>
          @endif
        </div>
        @empty
        <div style="color:#999;text-align:center;padding:20px">No templates added yet</div>
        @endforelse
      </div>
    </div>
  </div>

</main>
@endsection
