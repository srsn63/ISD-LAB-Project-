<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --primary-blue:#1e40af; --light-blue:#3b82f6; --sky-blue:#60a5fa; --dark-bg:#0f172a; --card-bg:rgba(30,41,59,0.9); --text-light:#e2e8f0; --text-muted:#94a3b8; --accent-gold:#fbbf24; --gradient-primary:linear-gradient(135deg,#1e40af,#3b82f6); }
        *{margin:0;padding:0;box-sizing:border-box}
        body{min-height:100vh;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#334155 100%);font-family:'Segoe UI',Tahoma,Verdana,sans-serif;color:var(--text-light);overflow:hidden}
        .bg-animation{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
        .cloud{position:absolute;background:rgba(255,255,255,0.05);border-radius:50%;animation:float 20s ease-in-out infinite;box-shadow:0 0 60px rgba(255,255,255,0.1)}
        .cloud:nth-child(1){width:300px;height:300px;top:10%;left:-150px}
        .cloud:nth-child(2){width:200px;height:200px;top:40%;right:-100px;animation-delay:3s}
        .cloud:nth-child(3){width:250px;height:250px;bottom:20%;left:50%;animation-delay:6s}
        .cloud:nth-child(4){width:180px;height:180px;top:60%;left:10%;animation-delay:9s}
        .cloud:nth-child(5){width:220px;height:220px;top:20%;right:20%;animation-delay:12s}
        @keyframes float{0%,100%{transform:translate(0,0)}50%{transform:translate(30px,-30px)}}
        .container{position:relative;z-index:2;min-height:100vh;display:grid;place-items:center;padding:2rem}
        .card{width:100%;max-width:460px;background:var(--card-bg);border:1px solid rgba(59,130,246,0.35);border-radius:18px;box-shadow:0 20px 45px rgba(0,0,0,0.45);padding:1.6rem 1.4rem;position:relative;overflow:hidden}
        .card::before{content:'';position:absolute;top:0;left:0;width:100%;height:4px;background:var(--gradient-primary)}
        .header{display:flex;align-items:center;gap:12px;margin-bottom:1rem}
        .logo-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:var(--gradient-primary);box-shadow:0 6px 20px rgba(59,130,246,0.45)}
        h1{font-size:1.4rem;color:var(--sky-blue)}.muted{color:var(--text-muted);font-size:.92rem}
        label{display:block;color:var(--text-muted);font-size:.9rem;margin-bottom:.35rem}
        .field{margin-top:.9rem}
        .input{width:100%;padding:.85rem 1rem;border-radius:12px;border:1px solid rgba(59,130,246,0.35);background:rgba(15,23,42,0.75);color:var(--text-light);outline:none;font-weight:500}
        .input:focus{border-color:var(--sky-blue);box-shadow:0 0 0 3px rgba(59,130,246,0.2);background:rgba(15,23,42,0.92)}
        .btn{width:100%;margin-top:1rem;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:.9rem 1rem;border-radius:12px;color:#fff;background:var(--gradient-primary);box-shadow:0 10px 28px rgba(59,130,246,0.35);border:none;cursor:pointer;font-weight:800}
        .btn:hover{transform:translateY(-1px);box-shadow:0 14px 34px rgba(59,130,246,0.55)}
        .row{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:1rem}
        .links a{color:var(--sky-blue);text-decoration:none;font-weight:600}
        .links a:hover{text-decoration:underline}
        .error{color:#fecaca;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.35);padding:.6rem .8rem;border-radius:10px;font-size:.9rem;margin-top:.8rem}
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="cloud"></div><div class="cloud"></div><div class="cloud"></div><div class="cloud"></div><div class="cloud"></div>
    </div>

    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo-icon"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <h1>Admin Login</h1>
                    <div class="muted">Access the Lalon Airport Admin Dashboard</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="field">
                    <label for="email">Admin Email</label>
                    <input id="email" type="email" name="email" class="input" placeholder="admin@example.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" class="input" placeholder="Enter password" required>
                </div>

                @if ($errors->any())
                    <div class="error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit" class="btn"><i class="fas fa-right-to-bracket"></i> Sign In</button>
            </form>

            <div class="row" style="margin-top:1rem;">
                <div class="links">
                    <a href="{{ Route::has('home') ? route('home') : url('/') }}"><i class="fas fa-house"></i> Back to Home</a>
                </div>
                <div class="links">
                    <a href="{{ route('admin.login') }}"><i class="fas fa-rotate-right"></i> Reset</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>