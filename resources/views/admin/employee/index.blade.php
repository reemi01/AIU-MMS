@extends('layouts.admin')

@section('title', 'Employee Management')

@section('content')
<style>
:root{--bg:#ececec;--muted:#7b7b7b;--accent:#7c3aed}
.main{flex:1;padding:22px}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.small-input{padding:12px 16px;border-radius:28px;border:1px solid rgba(0,0,0,0.06);width:520px}
.title{font-size:34px;text-align:center;font-weight:800;margin:8px 0 18px}
.btn{padding:12px 20px;border-radius:6px;border:0;background:#111;color:white;cursor:pointer}
.kpi-card{margin-top:28px;width:420px;height:160px;border-radius:16px;background:linear-gradient(180deg, rgba(124,58,237,0.06), rgba(245,230,255,0.6));display:flex;align-items:center;justify-content:center;flex-direction:column;color:var(--accent);font-weight:800}
.kpi-card h3{margin:0;font-size:22px}
.kpi-card .value{font-size:34px;margin-top:8px}
.list-section{margin-top:38px}
table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden}
thead th{background:#fafafa;padding:12px;text-align:left;border-bottom:1px solid #f0f0f0}
tbody td{padding:12px;border-bottom:1px solid #f6f6f6}
.action-btn{padding:6px 10px;border-radius:6px;border:0;cursor:pointer;font-size:13px}
.edit-btn{background:#fff;border:1px solid #e6e6ef;margin-right:6px}
.del-btn{background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c}
.eye-btn{background:transparent;border:0;cursor:pointer;font-size:16px;margin-left:4px}
@media (max-width:900px){.small-input{width:100%}.kpi-card{width:100%}}
</style>

<main class="main">
  <div class="topbar">
    <form method="GET" style="display:flex;align-items:center;gap:12px;flex:1">
      <input name="search" class="small-input" placeholder="Search by name, username, or trade" value="{{ request('search') }}" style="flex:1" />
      <select name="trade" class="input" style="padding:10px 14px;border-radius:8px;border:1px solid #e5e7eb;width:200px" onchange="this.form.submit()">
        <option value="">All Trades</option>
        @foreach($trades ?? [] as $trade)
        <option value="{{ $trade }}" {{ request('trade') == $trade ? 'selected' : '' }}>{{ $trade }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn" style="background:#4f46e5;color:#fff">Search</button>
      @if(request('search') || request('trade'))
      <a href="{{ route('admin.employees.index') }}" class="btn" style="background:#fff;color:#111;border:1px solid #e5e7eb;text-decoration:none">Clear</a>
      @endif
    </form>
    <button onclick="openModal()" class="btn" style="margin-left:12px">Add employee</button>
  </div>

  <h2 class="title">Employee Management</h2>

  <div class="kpi-card">
    <h3>Total employees</h3>
    <div class="value">{{ $totalEmployees }}</div>
  </div>

  <section class="list-section">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>
            Username
            <button class="eye-btn" onclick="toggleUsername(this)" title="Toggle username visibility">
              <span class="eye-icon">👁️</span>
            </button>
          </th>
          <th>Email</th>
          <th>Password</th>
          <th>Phone</th>
          <th>Trade</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="employeesBody">
        @forelse($workers as $worker)
        <tr data-name="{{ strtolower($worker->user->name) }}">
          <td>{{ $worker->user->name }}</td>
          <td><span class="username-text">{{ $worker->user->username }}</span></td>
          <td>{{ $worker->email ?? 'N/A' }}</td>
          <td>
            <span class="pwd-text" data-hidden="true" data-password="{{ $worker->user->plain_password ?? 'Not set' }}">••••••••</span>
            <button class="eye-btn" onclick="togglePassword(this)" title="Toggle password visibility">
              <span class="pwd-icon">👁️</span>
            </button>
          </td>
          <td>{{ $worker->phone ?? 'N/A' }}</td>
          <td>{{ $worker->trade }}</td>
          <td>
            <a href="{{ route('admin.employees.performance', $worker) }}" class="action-btn" style="background:#10b981;text-decoration:none;display:inline-block">Performance</a>
            <button class="action-btn edit-btn" onclick='openEditModal(@json($worker))'>Edit</button>
            <form action="{{ route('admin.employees.destroy', $worker) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this employee?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="action-btn del-btn">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;color:#999">No employees found</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </section>

  <div id="modalOverlay" style="display:{{ $errors->any() ? 'flex' : 'none' }};position:fixed;inset:0;background:rgba(2,6,23,0.45);backdrop-filter:blur(3px);align-items:center;justify-content:center;z-index:1000">
    <div style="background:#fff;width:680px;max-width:96%;border-radius:12px;padding:20px;box-shadow:0 20px 50px rgba(2,6,23,.3)">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
        <h3 style="margin:0" id="modalTitle">Add Employee</h3>
        <button onclick="closeModal()" style="background:transparent;border:0;font-size:20px;cursor:pointer">✕</button>
      </div>

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

      <form id="employeeForm" method="POST" action="{{ route('admin.employees.store') }}">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <div style="display:flex;gap:12px;margin-bottom:8px">
          <div style="flex:1">
            <input name="name" id="m_name" placeholder="Full name *" value="{{ old('name') }}" class="input @error('name') error-input @enderror" required />
            @error('name')<span style="color:#ef4444;font-size:12px">{{ $message }}</span>@enderror
          </div>
          <div style="width:200px">
            <input name="phone" id="m_phone" placeholder="Telephone" value="{{ old('phone') }}" class="input @error('phone') error-input @enderror" />
            @error('phone')<span style="color:#ef4444;font-size:12px">{{ $message }}</span>@enderror
          </div>
        </div>
        <div style="display:flex;gap:12px;margin-bottom:8px">
          <div style="flex:1">
            <input name="username" id="m_username" placeholder="Username *" value="{{ old('username') }}" class="input @error('username') error-input @enderror" required />
            @error('username')<span style="color:#ef4444;font-size:12px">{{ $message }}</span>@enderror
          </div>
          <div style="width:200px">
            <input name="password" id="m_password" type="password" placeholder="Password *" class="input @error('password') error-input @enderror" />
            @error('password')<span style="color:#ef4444;font-size:12px">{{ $message }}</span>@enderror
          </div>
        </div>
        <div style="display:flex;gap:12px;margin-bottom:8px">
          <div style="flex:1">
            <input name="email" id="m_email" type="email" placeholder="Email" value="{{ old('email') }}" class="input @error('email') error-input @enderror" />
            @error('email')<span style="color:#ef4444;font-size:12px">{{ $message }}</span>@enderror
          </div>

        </div>
        <div style="display:flex;gap:12px;margin-bottom:8px">
          <div style="flex:1">
            <input name="dob" id="m_dob" type="date" value="{{ old('dob') }}" class="input @error('dob') error-input @enderror" />
            @error('dob')<span style="color:#ef4444;font-size:12px">{{ $message }}</span>@enderror
          </div>
          <div style="flex:1">
            <input name="trade" id="m_trade" placeholder="Trade *" value="{{ old('trade') }}" class="input @error('trade') error-input @enderror" required />
            @error('trade')<span style="color:#ef4444;font-size:12px">{{ $message }}</span>@enderror
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:10px">
          <button type="button" onclick="closeModal()" style="padding:10px 16px;border-radius:8px;border:1px solid #e6e6ef;background:#fff;cursor:pointer;transition:all 0.2s">Cancel</button>
          <button type="submit" style="padding:10px 16px;border-radius:8px;border:0;background:#4f46e5;color:#fff;cursor:pointer;font-weight:600;transition:all 0.2s">Save Employee</button>
        </div>
      </form>
    </div>
  </div>
</main>

<script>
function openModal() {
  document.getElementById('modalTitle').textContent = 'Add Employee';
  document.getElementById('employeeForm').action = '{{ route("admin.employees.store") }}';
  document.getElementById('formMethod').value = 'POST';
  document.getElementById('employeeForm').reset();
  document.getElementById('m_password').required = true;
  document.getElementById('modalOverlay').style.display = 'flex';
}

function openEditModal(worker) {
  document.getElementById('modalTitle').textContent = 'Edit Employee';
  document.getElementById('employeeForm').action = `/admin/employees/${worker.id}`;
  document.getElementById('formMethod').value = 'PUT';
  document.getElementById('m_name').value = worker.user.name;
  document.getElementById('m_username').value = worker.user.username;
  document.getElementById('m_email').value = worker.email || '';
  document.getElementById('m_phone').value = worker.phone || '';
  document.getElementById('m_dob').value = worker.dob || '';
  document.getElementById('m_trade').value = worker.trade;
  document.getElementById('m_password').value = '';
  document.getElementById('m_password').required = false;
  document.getElementById('m_password').placeholder = 'Leave blank to keep current';
  document.getElementById('modalOverlay').style.display = 'flex';
}

function closeModal() {
  document.getElementById('modalOverlay').style.display = 'none';
}

function searchEmployee() {
  const search = document.getElementById('empSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#employeesBody tr');
  rows.forEach(row => {
    const name = row.getAttribute('data-name');
    if (name && name.includes(search)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function toggleUsername(btn) {
  const usernameTexts = document.querySelectorAll('.username-text');
  const eyeIcon = btn.querySelector('.eye-icon');

  usernameTexts.forEach(span => {
    if (span.style.filter === 'blur(5px)') {
      span.style.filter = 'none';
      eyeIcon.textContent = '👁️';
    } else {
      span.style.filter = 'blur(5px)';
      eyeIcon.textContent = '🙈';
    }
  });
}

function togglePassword(btn) {
  const pwdText = btn.previousElementSibling;
  const pwdIcon = btn.querySelector('.pwd-icon');
  const isHidden = pwdText.getAttribute('data-hidden') === 'true';
  const actualPassword = pwdText.getAttribute('data-password');

  if (isHidden) {
    pwdText.textContent = actualPassword;
    pwdText.setAttribute('data-hidden', 'false');
    pwdIcon.textContent = '🙈';
  } else {
    pwdText.textContent = '••••••••';
    pwdText.setAttribute('data-hidden', 'true');
    pwdIcon.textContent = '👁️';
  }
}

</script>
@endsection
