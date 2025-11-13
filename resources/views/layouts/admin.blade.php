<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'AIU-MMS')</title>
  <style>
    :root{--bg:#ececec;--dark:#26292c;--muted:#999;--accent:#7c3aed}
    body{margin:0;font-family:Inter,Segoe UI,Helvetica,Arial,sans-serif;background:var(--bg);color:#111}
    .app{display:flex;min-height:100vh}
    .sidebar{width:280px;background:#fff;padding:28px;border-right:0;box-shadow:2px 0 8px rgba(0,0,0,0.05);position:fixed;left:0;top:0;bottom:0;overflow-y:auto;transition:all 0.3s ease;z-index:1000;flex-shrink:0}
    .sidebar.collapsed{width:0;padding:0;transform:translateX(-280px)}
    .sidebar::-webkit-scrollbar{width:6px}
    .sidebar::-webkit-scrollbar-track{background:#f1f1f1}
    .sidebar::-webkit-scrollbar-thumb{background:#7c3aed;border-radius:3px}
    .sidebar::-webkit-scrollbar-thumb:hover{background:#6d28d9}
    .main-content{flex:1;margin-left:280px;transition:all 0.3s ease;width:calc(100% - 280px)}
    .main-content.expanded{margin-left:0;width:100%}
    .sidebar-toggle{position:fixed;top:20px;left:290px;z-index:1001;background:#7c3aed;color:white;border:0;width:40px;height:40px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(124,58,237,0.3);transition:all 0.3s ease;font-size:20px}
    .sidebar-toggle:hover{background:#6d28d9;transform:scale(1.1)}
    .sidebar-toggle.shifted{left:10px}
    .profile{display:flex;align-items:center;gap:14px;margin-bottom:22px;padding:12px;border-radius:12px;background:linear-gradient(90deg, rgba(124,58,237,0.06), rgba(124,58,237,0.02))}
    .avatar{width:64px;height:64px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:20px}
    .nav{margin-top:24px}
    .nav a{display:flex;align-items:center;gap:12px;padding:12px;border-radius:14px;color:#111;text-decoration:none;margin-bottom:6px;transition:all 0.2s}
    .nav a:hover{background:linear-gradient(90deg, rgba(124,58,237,0.06), rgba(124,58,237,0.02))}
    .nav a .icon{width:44px;height:44px;border-radius:10px;background:linear-gradient(90deg, rgba(124,58,237,0.06), rgba(124,58,237,0.02));display:inline-flex;align-items:center;justify-content:center;font-size:20px}
    .nav a.active{background:linear-gradient(90deg, rgba(124,58,237,0.12), rgba(124,58,237,0.06));font-weight:700}
    .nav a.active .icon{background:linear-gradient(135deg,#7c3aed,#a78bfa);color:white}
    .logout-btn{width:100%;margin-top:20px;padding:12px;border:0;border-radius:12px;background:#fee2e2;color:#b91c1c;font-weight:600;cursor:pointer;transition:all 0.2s}
    .logout-btn:hover{background:#fecaca}

    /* Form styling */
    .input{width:100%;padding:10px 14px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;transition:all 0.2s;font-size:14px}
    .input:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,0.1)}
    .error-input{border-color:#ef4444!important}
    .error-input:focus{box-shadow:0 0 0 3px rgba(239,68,68,0.1)!important}
    .btn:hover{opacity:0.9;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,0.15)}
    .btn:active{transform:translateY(0)}

    @media (max-width:900px){
      .sidebar{transform:translateX(-280px)}
      .sidebar.mobile-open{transform:translateX(0)}
      .main-content{margin-left:0}
      .sidebar-toggle{left:10px}
      .sidebar-toggle.shifted{left:290px}
    }

    /* Confirmation Modal */
    .confirm-modal{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:10000;align-items:center;justify-content:center}
    .confirm-modal.active{display:flex}
    .confirm-modal-content{background:white;border-radius:12px;padding:24px;max-width:450px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:modalSlideIn 0.3s ease}
    @keyframes modalSlideIn{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
    .confirm-modal-header{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .confirm-modal-icon{width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px}
    .confirm-modal-icon.danger{background:#fee2e2;color:#ef4444}
    .confirm-modal-icon.warning{background:#fef3c7;color:#f59e0b}
    .confirm-modal-icon.info{background:#dbeafe;color:#3b82f6}
    .confirm-modal-title{font-size:20px;font-weight:700;margin:0}
    .confirm-modal-body{color:#6b7280;line-height:1.6;margin-bottom:20px}
    .confirm-modal-footer{display:flex;gap:12px;justify-content:flex-end}
    .confirm-btn{padding:10px 20px;border-radius:8px;border:0;font-weight:600;cursor:pointer;transition:all 0.2s}
    .confirm-btn-cancel{background:#e5e7eb;color:#374151}
    .confirm-btn-cancel:hover{background:#d1d5db}
    .confirm-btn-confirm{background:#ef4444;color:white}
    .confirm-btn-confirm:hover{background:#dc2626}
  </style>
  @stack('styles')
</head>
<body>
  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

  <div class="app">
    <aside id="sidebar" class="sidebar">
      <div class="profile">
        <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div>
          <h4 style="margin:0;font-size:16px">{{ auth()->user()->name }}</h4>
          <p style="margin:0;color:var(--muted);font-size:13px">Administrator</p>
        </div>
      </div>
      <nav class="nav">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <span class="icon">🏠</span> Home
        </a>
        <a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
          <span class="icon">👥</span> Employee
        </a>
        <a href="{{ route('admin.weekly-monthly.index') }}" class="{{ request()->routeIs('admin.weekly-monthly.*') ? 'active' : '' }}">
          <span class="icon">🗓️</span> Weekly/Monthly Tasks
        </a>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
          <span class="icon">📊</span> Report
        </a>
        <a href="{{ route('admin.equipment.index') }}" class="{{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}">
          <span class="icon">🔧</span> Equipment
        </a>
        <a href="{{ route('admin.tasks.index') }}" class="{{ request()->routeIs('admin.tasks.index') ? 'active' : '' }}">
          <span class="icon">📋</span> Task Management
        </a>
        <a href="{{ route('admin.tasks.calendar') }}" class="{{ request()->routeIs('admin.tasks.calendar') ? 'active' : '' }}">
          <span class="icon">📅</span> Task Calendar
        </a>
        <a href="{{ route('admin.assets.index') }}" class="{{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">
          <span class="icon">➕</span> Add Lift/Chiller
        </a>
        <a href="{{ route('admin.profile.index') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
          <span class="icon">⚙️</span> Manage Account
        </a>
      </nav>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </aside>

    <div id="mainContent" class="main-content">
      @if(session('success'))
      <div style="background:#d1fae5;color:#065f46;padding:12px;margin:18px 18px 0;border-radius:6px">
        {{ session('success') }}
      </div>
      @endif

      @if(session('error'))
      <div style="background:#fee2e2;color:#b91c1c;padding:12px;margin:18px 18px 0;border-radius:6px">
        {{ session('error') }}
      </div>
      @endif

      @yield('content')
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div id="confirmModal" class="confirm-modal">
    <div class="confirm-modal-content">
      <div class="confirm-modal-header">
        <div id="confirmIcon" class="confirm-modal-icon danger">⚠️</div>
        <h3 id="confirmTitle" class="confirm-modal-title">Confirm Action</h3>
      </div>
      <div id="confirmBody" class="confirm-modal-body">
        Are you sure you want to proceed with this action?
      </div>
      <div class="confirm-modal-footer">
        <button onclick="closeConfirmModal()" class="confirm-btn confirm-btn-cancel">Cancel</button>
        <button id="confirmButton" onclick="confirmAction()" class="confirm-btn confirm-btn-confirm">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    // Sidebar toggle functionality
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');
      const toggleBtn = document.getElementById('sidebarToggle');

      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
      toggleBtn.classList.toggle('shifted');

      // Save state to localStorage
      const isCollapsed = sidebar.classList.contains('collapsed');
      localStorage.setItem('sidebarCollapsed', isCollapsed);

      // Change icon
      toggleBtn.innerHTML = isCollapsed ? '☰' : '✕';
    }

    // Restore sidebar state on page load
    document.addEventListener('DOMContentLoaded', function() {
      const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
      if (isCollapsed) {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('mainContent').classList.add('expanded');
        document.getElementById('sidebarToggle').classList.add('shifted');
        document.getElementById('sidebarToggle').innerHTML = '☰';
      }
    });

    // Confirmation Modal functionality
    let confirmCallback = null;

    function showConfirmModal(options = {}) {
      const modal = document.getElementById('confirmModal');
      const icon = document.getElementById('confirmIcon');
      const title = document.getElementById('confirmTitle');
      const body = document.getElementById('confirmBody');
      const confirmBtn = document.getElementById('confirmButton');

      // Set content
      title.textContent = options.title || 'Confirm Action';
      body.textContent = options.message || 'Are you sure you want to proceed with this action?';
      confirmBtn.textContent = options.confirmText || 'Confirm';

      // Set icon type
      icon.className = 'confirm-modal-icon ' + (options.type || 'danger');
      icon.textContent = options.icon || '⚠️';

      // Set button color
      confirmBtn.className = 'confirm-btn confirm-btn-confirm';
      if (options.type === 'warning') {
        confirmBtn.style.background = '#f59e0b';
      } else if (options.type === 'info') {
        confirmBtn.style.background = '#3b82f6';
      } else {
        confirmBtn.style.background = '#ef4444';
      }

      // Store callback
      confirmCallback = options.onConfirm || null;

      // Show modal
      modal.classList.add('active');
    }

    function closeConfirmModal() {
      document.getElementById('confirmModal').classList.remove('active');
      confirmCallback = null;
    }

    function confirmAction() {
      if (confirmCallback) {
        confirmCallback();
      }
      closeConfirmModal();
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeConfirmModal();
      }
    });

    // Helper function for delete confirmations
    function confirmDelete(form, itemName = 'item') {
      showConfirmModal({
        title: 'Delete ' + itemName + '?',
        message: 'Are you sure you want to delete this ' + itemName + '? This action cannot be undone.',
        icon: '🗑️',
        type: 'danger',
        confirmText: 'Delete',
        onConfirm: function() {
          form.submit();
        }
      });
      return false;
    }
  </script>

  @stack('scripts')
</body>
</html>
