<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>AIU MMS — Login</title>
  <style>
    /* Reset & base */
    :root{--accent:#2b6ef6;--muted:#9aa4b2;--bg-overlay:rgba(0,0,0,.45)}
    *{box-sizing:border-box}
    html,body{height:100%;margin:0;font-family:Inter,Segoe UI,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased}
    body{
      background-image: url('{{ asset('images/background.jpg') }}');
      background-size:cover;background-position:center;position:relative;color:#fff;
    }
    /* dark overlay */
    .bg-overlay{position:fixed;inset:0;background:linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,.45));backdrop-filter:blur(2px)}
    .wrap{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px}
    .card{width:760px;max-width:96%;display:flex;gap:40px;align-items:center;background:rgba(255,255,255,0.03);border-radius:12px;padding:36px;box-shadow:0 10px 40px rgba(2,6,23,.5)}
    .hero{flex:1}
    .hero h1{margin:0 0 18px;font-size:28px;line-height:1.05;font-weight:700;text-shadow:0 4px 12px rgba(0,0,0,.5)}
    .hero p{margin:0;color:var(--muted);opacity:.9}
    .form{width:360px}
    .logo{width:60px;height:60px;border-radius:8px;background:rgba(255,255,255,.07);display:flex;align-items:center;justify-content:center;margin-bottom:18px}
    label{display:block;font-size:12px;font-weight:600;color:rgba(255,255,255,.85);margin-bottom:6px}
    .input{width:100%;padding:12px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,0.02);color:#fff;margin-bottom:12px}
    .input:focus{outline:2px solid rgba(43,110,246,.14);border-color:rgba(43,110,246,.7)}
    .btn{display:inline-block;padding:12px 18px;border-radius:10px;border:0;background:var(--accent);color:#fff;font-weight:700;cursor:pointer;width:100%}
    .muted{color:var(--muted);font-size:13px;margin-top:8px;text-align:center}
    .small{font-size:13px}
    .error{color:#ffb4b4;background:rgba(255,0,0,0.16);padding:10px;border-radius:6px;margin-bottom:12px}
    .forgot{display:block;text-align:right;margin-top:6px;color:rgba(255,255,255,.8);text-decoration:none;font-size:13px}
    /* small footer note */
    .samples{margin-top:10px;font-size:13px;color:var(--muted);background:rgba(0,0,0,.12);padding:8px;border-radius:6px}
    /* responsive */
    @media (max-width:720px){ .card{flex-direction:column;padding:20px} .hero{display:none} .form{width:100%} }
  </style>
</head>
<body>
  <div class="bg-overlay" aria-hidden="true"></div>
  <div class="wrap">
    <main class="card" role="main" aria-labelledby="title">
      <section class="hero" aria-hidden="true">
        <h1 id="title">AIU Maintenance Management System</h1>
        <p>Secure admin access to manage tasks, schedules, technicians and equipment monitoring.</p>
      </section>

      <section class="form" aria-labelledby="loginHeading">
        <div class="logo" aria-hidden="true"></div>
        <h2 id="loginHeading" style="margin:0 0 12px">Sign in</h2>

        @if ($errors->any())
        <div class="error" role="alert">
          {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
          @csrf

          <label for="username">Username</label>
          <input id="username" class="input" name="username" autocomplete="username" required value="{{ old('username') }}" placeholder="Enter username" />

          <label for="password">Password</label>
          <input id="password" class="input" name="password" type="password" autocomplete="current-password" required placeholder="••••••••" />

          <button type="submit" class="btn">LOGIN</button>
        </form>

        <a class="forgot small" href="#" onclick="event.preventDefault();alert('Contact IT to reset your password');">Forgot password?</a>
      </section>
    </main>
  </div>

  <script>
    window.addEventListener('load', ()=>{
      document.getElementById('username').focus();
    });
  </script>
</body>
</html>
