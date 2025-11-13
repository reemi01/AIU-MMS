<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'AIU-MMS')</title>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
  @stack('styles')
</head>
<body>
  <div class="container">
    <div class="topbar header">
      <div class="flex">
        <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div>
          <div><strong>{{ auth()->user()->name }}</strong></div>
          <div class="small-muted">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
      </div>
      <div class="flex">
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
          @csrf
          <button type="submit" class="btn">Logout</button>
        </form>
      </div>
    </div>

    @if(session('success'))
    <div class="note" style="background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin-bottom:12px">
      {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="note" style="background:#f8d7da;color:#721c24;padding:12px;border-radius:8px;margin-bottom:12px">
      {{ session('error') }}
    </div>
    @endif

    @yield('content')
  </div>

  @stack('scripts')
</body>
</html>
