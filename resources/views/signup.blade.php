<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --primary-blue:#1e40af; --light-blue:#3b82f6; --sky-blue:#60a5fa; --dark-bg:#0f172a; --card-bg:rgba(30,41,59,0.9);
            --text-light:#e2e8f0; --text-muted:#94a3b8; --accent-gold:#fbbf24; --gradient-primary:linear-gradient(135deg,#1e40af,#3b82f6); --gradient-secondary:linear-gradient(135deg,#3b82f6,#60a5fa);
        }
        body { font-family:'Segoe UI',Tahoma,Verdana,sans-serif; background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#334155 100%); color:var(--text-light); min-height:100vh; display:flex; align-items:center; justify-content:center; overflow-x:hidden; position:relative; }
        .bg-animation { position:fixed; inset:0; z-index:0; overflow:hidden; pointer-events:none; }
        .cloud { position:absolute; background:rgba(255,255,255,0.05); border-radius:50%; animation:float 20s ease-in-out infinite; box-shadow:0 0 60px rgba(255,255,255,0.1);}        
        .cloud:nth-child(1){width:300px;height:300px;top:10%;left:-150px;} .cloud:nth-child(2){width:200px;height:200px;top:40%;right:-100px;animation-delay:3s;} .cloud:nth-child(3){width:250px;height:250px;bottom:20%;left:50%;animation-delay:6s;} .cloud:nth-child(4){width:180px;height:180px;top:60%;left:10%;animation-delay:9s;} .cloud:nth-child(5){width:220px;height:220px;top:20%;right:20%;animation-delay:12s;}
        @keyframes float {0%,100%{transform:translate(0,0);}50%{transform:translate(30px,-30px);} }
        .container { position:relative; z-index:10; width:100%; max-width:520px; padding:2rem 2rem 3rem; animation:fadeIn 0.7s ease; }
        @keyframes fadeIn { from {opacity:0; transform:translateY(30px);} to {opacity:1; transform:translateY(0);} }
        .header { text-align:center; margin-bottom:2rem; }
        .logo-icon { width:60px; height:60px; background:var(--gradient-primary); border-radius:15px; display:flex; align-items:center; justify-content:center; font-size:2rem; box-shadow:0 8px 25px rgba(59,130,246,0.6); animation:logoPulse 2.5s ease-in-out infinite; margin:0 auto 1rem; }
        @keyframes logoPulse {0%,100%{box-shadow:0 8px 25px rgba(59,130,246,0.6);}50%{box-shadow:0 8px 35px rgba(59,130,246,0.8);} }
        .brand { font-size:2rem; font-weight:800; background:var(--gradient-secondary); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        h1 { font-size:1.9rem; margin:0.5rem 0 0.5rem; }
        .subtitle { color:var(--text-muted); font-size:0.95rem; }
        .card { background:var(--card-bg); backdrop-filter:blur(15px); padding:2.5rem 2.3rem; border-radius:22px; border:1px solid rgba(59,130,246,0.3); box-shadow:0 15px 50px rgba(0,0,0,0.5),0 0 100px rgba(59,130,246,0.15); position:relative; overflow:hidden; }
        .card::before { content:''; position:absolute; top:0; left:-100%; width:100%; height:100%; background:linear-gradient(90deg,transparent,rgba(59,130,246,0.12),transparent); animation:shimmer 3.2s linear infinite; }
        @keyframes shimmer {0%{left:-100%;}100%{left:100%;}}
        .form-group { margin-bottom:1.3rem; }
        label { display:block; margin-bottom:0.45rem; font-weight:600; font-size:0.9rem; }
        .input-wrapper { position:relative; }
        .input-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:1rem; transition:0.3s; }
        .input { width:100%; padding:0.95rem 1rem 0.95rem 2.9rem; border:2px solid rgba(59,130,246,0.3); border-radius:12px; background:rgba(15,23,42,0.6); color:var(--text-light); font-size:0.95rem; transition:0.3s; }
        .input:focus { outline:none; border-color:var(--sky-blue); background:rgba(15,23,42,0.8); box-shadow:0 0 0 4px rgba(59,130,246,0.15); }
        .input:focus + .input-icon { color:var(--sky-blue); }
        .two-col { display:flex; gap:1rem; }
        @media (max-width:560px){ .two-col { flex-direction:column; } }
        .actions { margin-top:1.5rem; }
        .btn-primary { width:100%; padding:1rem; background:var(--gradient-primary); color:#fff; font-weight:700; font-size:1.05rem; border:none; border-radius:14px; cursor:pointer; box-shadow:0 8px 25px rgba(59,130,246,0.5); transition:0.35s; position:relative; overflow:hidden; }
        .btn-primary::before { content:''; position:absolute; top:0; left:-100%; width:100%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.3),transparent); transition:left .6s; }
        .btn-primary:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(59,130,246,0.6); }
        .btn-primary:hover::before { left:100%; }
        .links { text-align:center; margin-top:1.8rem; font-size:0.9rem; color:var(--text-muted); }
        .links a { color:var(--sky-blue); text-decoration:none; font-weight:600; transition:0.3s; }
        .links a:hover { color:var(--accent-gold); text-decoration:underline; }
        .back-buttons { display:flex; justify-content:space-between; gap:1rem; margin-top:1.5rem; }
        .back-btn { flex:1; display:inline-flex; justify-content:center; align-items:center; gap:0.4rem; padding:0.75rem 1rem; background:rgba(30,41,59,0.85); border:1px solid rgba(59,130,246,0.35); border-radius:12px; color:var(--text-light); text-decoration:none; font-weight:600; font-size:0.85rem; transition:0.35s; }
        .back-btn:hover { background:rgba(30,41,59,1); border-color:var(--sky-blue); transform:translateY(-3px); box-shadow:0 8px 25px rgba(59,130,246,0.4); }
        .success-msg { background:linear-gradient(135deg,#10b981,#059669); color:white; padding:1rem 1.5rem; border-radius:12px; margin-bottom:1.5rem; display:flex; align-items:center; gap:10px; animation:slideDown 0.5s ease; }
        @keyframes slideDown { from { transform:translateY(-20px); opacity:0; } to { transform:translateY(0); opacity:1; } }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="cloud"></div><div class="cloud"></div><div class="cloud"></div><div class="cloud"></div><div class="cloud"></div>
    </div>
    <div class="container">
        <div class="header">
            <div class="logo-icon"><i class="fas fa-plane"></i></div>
            <div class="brand">Lalon Airport</div>
            <h1>Create Account</h1>
            <div class="subtitle">Join the Lalon Airport platform</div>
        </div>
        <div class="card">
            @if(session('success'))
                <div class="success-msg">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" class="input" placeholder="Your full name" value="{{ old('name') }}" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    @error('name')
                        <small style="color: #ff4444; display: block; margin-top: 0.5rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="input" placeholder="Your email" value="{{ old('email') }}" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <small style="color: #ff4444; display: block; margin-top: 0.5rem;">{{ $message }}</small>
                    @enderror
                </div>
                <div class="two-col">
                    <div class="form-group" style="flex:1;">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="input" placeholder="Create password (min 8 chars)" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        @error('password')
                            <small style="color: #ff4444; display: block; margin-top: 0.5rem;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="input" placeholder="Repeat password" required>
                            <i class="fas fa-shield-halved input-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-user-plus"></i> Create Account</button>
                </div>
            </form>
            <div class="links">Already have an account? <a href="{{ route('login.dashboard') }}">Log In</a></div>
            <div class="back-buttons">
                <a href="{{ route('home') }}" class="back-btn"><i class="fas fa-house"></i> Home</a>
                <a href="{{ route('login.dashboard') }}" class="back-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
            </div>
        </div>
    </div>
</body>
</html>