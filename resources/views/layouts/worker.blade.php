<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <title>@yield('title', 'AIU-MMS')</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
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

    /* Mobile overlay for sidebar */
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;opacity:0;transition:opacity 0.3s ease}
    .sidebar-overlay.active{display:block;opacity:1}

    @media (max-width:900px){
      .sidebar{transform:translateX(-280px);z-index:1000}
      .sidebar.mobile-open{transform:translateX(0);box-shadow:4px 0 20px rgba(0,0,0,0.2)}
      .main-content{margin-left:0;width:100%}
      .sidebar-toggle{left:10px}
      .sidebar-toggle.shifted{left:290px}
    }

    /* Mobile-specific enhancements */
    @media (max-width:768px){
      body{font-size:16px} /* Prevents zoom on iOS */
      .main-content{padding:0 4px}
      .profile{flex-direction:column;text-align:center}
      .profile .avatar{width:80px;height:80px;font-size:32px}
      .nav a{padding:14px;min-height:48px} /* Touch-friendly */
      .nav a .icon{width:48px;height:48px;font-size:22px}
      .logout-btn{padding:14px;min-height:48px;font-size:16px}
      .sidebar-toggle{width:48px;height:48px;font-size:24px;top:12px}
      .input, .btn{min-height:44px;font-size:16px} /* Touch-friendly inputs */
    }

    /* Camera input styling for mobile */
    input[type="file"][accept*="image"]{
      padding:12px;
      border:2px dashed #7c3aed;
      border-radius:12px;
      background:linear-gradient(90deg, rgba(124,58,237,0.03), rgba(124,58,237,0.01));
      cursor:pointer;
      font-size:14px;
    }
    input[type="file"][accept*="image"]:hover{
      border-color:#6d28d9;
      background:linear-gradient(90deg, rgba(124,58,237,0.06), rgba(124,58,237,0.03));
    }

    /* Image preview styling */
    .image-preview{
      max-width:100%;
      height:auto;
      border-radius:8px;
      margin:12px 0;
      box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }

    /* Safe area for notched devices */
    @supports (padding:max(0px)){
      .main-content{
        padding-left:max(18px, env(safe-area-inset-left));
        padding-right:max(18px, env(safe-area-inset-right));
        padding-bottom:max(18px, env(safe-area-inset-bottom));
      }
      .sidebar{
        padding-top:max(28px, env(safe-area-inset-top));
      }
    }
  </style>
  @stack('styles')
</head>
<body>
  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

  <!-- Mobile Sidebar Overlay -->
  <div id="sidebarOverlay" class="sidebar-overlay" onclick="closeSidebarMobile()"></div>

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
      const overlay = document.getElementById('sidebarOverlay');
      const isMobile = window.innerWidth <= 900;

      if (isMobile) {
        // Mobile: toggle mobile-open class
        const isOpen = sidebar.classList.contains('mobile-open');
        if (isOpen) {
          sidebar.classList.remove('mobile-open');
          overlay.classList.remove('active');
          toggleBtn.innerHTML = '☰';
          document.body.style.overflow = '';
        } else {
          sidebar.classList.add('mobile-open');
          overlay.classList.add('active');
          toggleBtn.innerHTML = '✕';
          document.body.style.overflow = 'hidden'; // Prevent scrolling when sidebar open
        }
      } else {
        // Desktop: toggle collapsed class
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        toggleBtn.classList.toggle('shifted');

        // Save state to localStorage (desktop only)
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
        toggleBtn.innerHTML = isCollapsed ? '☰' : '✕';
      }
    }

    // Close sidebar on mobile
    function closeSidebarMobile() {
      const sidebar = document.getElementById('sidebar');
      const toggleBtn = document.getElementById('sidebarToggle');
      const overlay = document.getElementById('sidebarOverlay');

      sidebar.classList.remove('mobile-open');
      overlay.classList.remove('active');
      toggleBtn.innerHTML = '☰';
      document.body.style.overflow = '';
    }

    // Restore sidebar state on page load (desktop only)
    document.addEventListener('DOMContentLoaded', function() {
      const isMobile = window.innerWidth <= 900;
      if (!isMobile) {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
          document.getElementById('sidebar').classList.add('collapsed');
          document.getElementById('mainContent').classList.add('expanded');
          document.getElementById('sidebarToggle').classList.add('shifted');
          document.getElementById('sidebarToggle').innerHTML = '☰';
        }
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');
      const toggleBtn = document.getElementById('sidebarToggle');
      const overlay = document.getElementById('sidebarOverlay');
      const isMobile = window.innerWidth <= 900;

      if (!isMobile) {
        // Switching to desktop: remove mobile-open, restore localStorage state
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
          sidebar.classList.add('collapsed');
          mainContent.classList.add('expanded');
          toggleBtn.classList.add('shifted');
          toggleBtn.innerHTML = '☰';
        } else {
          sidebar.classList.remove('collapsed');
          mainContent.classList.remove('expanded');
          toggleBtn.classList.remove('shifted');
          toggleBtn.innerHTML = '✕';
        }
      } else {
        // Switching to mobile: remove desktop classes
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
        toggleBtn.classList.remove('shifted');
        overlay.classList.remove('active');
        toggleBtn.innerHTML = '☰';
        document.body.style.overflow = '';
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
