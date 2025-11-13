<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'AIU-MMS')</title>
  <style>
    :root{--bg:#f2f2f4;--sidebar:#ffffff;--accent:#6b46ff;--muted:#6b7280}
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,Segoe UI,Helvetica,Arial,sans-serif;background:var(--bg);color:#111827}
    .app{display:flex;min-height:100vh}
    .sidebar{width:280px;background:var(--sidebar);padding:28px;border-right:0;box-shadow:2px 0 8px rgba(0,0,0,0.05);position:fixed;left:0;top:0;bottom:0;overflow-y:auto;transition:all 0.3s ease;z-index:1000;flex-shrink:0}
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
    .nav{margin-top:18px;display:flex;flex-direction:column;gap:12px}
    .nav a{display:flex;align-items:center;gap:12px;padding:12px;border-radius:14px;color:#111827;text-decoration:none;transition:all 0.2s}
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
  </style>
  @stack('styles')
</head>
<body>
  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

  <div class="app">
    <aside id="sidebar" class="sidebar">
      <div class="profile">
        @if(auth()->user()->worker && auth()->user()->worker->avatar)
          <div class="avatar" style="background-image:url({{ auth()->user()->worker->avatar }});background-size:cover"></div>
        @else
          <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        @endif
        <div>
          <h4 style="margin:0;font-size:16px">{{ auth()->user()->name }}</h4>
          <p style="margin:0;color:var(--muted);font-size:13px">{{ auth()->user()->worker->trade ?? 'Worker' }}</p>
        </div>
      </div>
      <nav class="nav">
        <a href="{{ route('worker.dashboard') }}" class="{{ request()->routeIs('worker.dashboard') ? 'active' : '' }}">
          <span class="icon">🏠</span> Dashboard
        </a>
        <a href="{{ route('worker.tasks.index') }}" class="{{ request()->routeIs('worker.tasks.index') ? 'active' : '' }}">
          <span class="icon">📋</span> My Tasks
        </a>
        <a href="{{ route('worker.schedule') }}" class="{{ request()->routeIs('worker.schedule') ? 'active' : '' }}">
          <span class="icon">📅</span> Schedule
        </a>
        <a href="{{ route('worker.profile') }}" class="{{ request()->routeIs('worker.profile') ? 'active' : '' }}">
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
  </script>

  @stack('scripts')
</body>
</html>
