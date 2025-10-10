<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root{--bg:#0f172a;--card:rgba(30,41,59,.92);--b:#1e40af;--b2:#3b82f6;--txt:#e2e8f0;--mut:#94a3b8}
        *{box-sizing:border-box}
        body{margin:0;background:linear-gradient(135deg,#0f172a,#1e293b,#334155);color:var(--txt);font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif}
        .bg{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
        .cloud{position:absolute;background:rgba(255,255,255,.05);border-radius:50%;animation:float 20s infinite ease-in-out;box-shadow:0 0 60px rgba(255,255,255,.1)}
        .cloud:nth-child(1){width:300px;height:300px;top:10%;left:-150px}.cloud:nth-child(2){width:200px;height:200px;top:40%;right:-100px}.cloud:nth-child(3){width:250px;height:250px;bottom:20%;left:50%}
        @keyframes float{0%,100%{transform:translate(0,0)}50%{transform:translate(30px,-30px)}}
        .wrap{position:relative;z-index:2;min-height:100vh;display:grid;place-items:center;padding:2rem}
        .card{width:100%;max-width:760px;background:var(--card);border:1px solid rgba(59,130,246,.35);border-radius:18px;box-shadow:0 20px 45px rgba(0,0,0,.45);padding:1.4rem;position:relative}
        .card::before{content:'';position:absolute;top:0;left:0;width:100%;height:4px;background:linear-gradient(135deg,var(--b),var(--b2))}
        .head{display:flex;align-items:center;gap:12px;margin-bottom:.6rem}
        .logo{width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--b),var(--b2));display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(59,130,246,.45)}
        h1{font-size:1.3rem;color:#60a5fa;margin:0}
        label{display:block;color:var(--mut);font-size:.92rem;margin:.6rem 0 .3rem}
        input,select,textarea{width:100%;padding:.85rem 1rem;border-radius:12px;border:1px solid rgba(59,130,246,.35);background:rgba(15,23,42,.75);color:var(--txt);outline:none}
        input:focus,select:focus,textarea:focus{border-color:#60a5fa;box-shadow:0 0 0 3px rgba(59,130,246,.2);background:rgba(15,23,42,.92)}
        textarea{min-height:120px;resize:vertical}
        .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        @media (max-width:720px){.row{grid-template-columns:1fr}}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:.9rem 1rem;border-radius:12px;color:#fff;background:linear-gradient(135deg,var(--b),var(--b2));border:none;cursor:pointer;font-weight:800;text-decoration:none}
        .mut{color:var(--mut);font-size:.9rem}
        .success{margin-top:.8rem;color:#86efac;background:rgba(22,163,74,.15);border:1px solid rgba(22,163,74,.35);padding:.6rem .8rem;border-radius:10px}
        .errors{margin:.6rem 0;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.35);color:#fecaca;padding:.6rem .8rem;border-radius:10px}
        .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:.8rem}
        a.link{color:#93c5fd;text-decoration:none;font-weight:700}
        a.link:hover{text-decoration:underline}
    </style>
</head>
<body>
<div class="bg">
    <div class="cloud"></div><div class="cloud"></div><div class="cloud"></div>
</div>

<div class="wrap">
    <div class="card">
        <div class="top">
            <div class="head">
                <div class="logo"><i class="fas fa-id-badge"></i></div>
                <div>
                    <h1>Flight Check-In</h1>
                    <div class="mut">Choose a terminal (1..5) and complete your check-in.</div>
                </div>
            </div>
            <div>
                <a class="link" href="{{ Route::has('home') ? route('home') : url('/') }}"><i class="fas fa-home"></i> Home</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="errors">
                <ul style="margin:0 0 0 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('checkin.store') }}">
            @csrf
            <div class="row">
                <div>
                    <label for="booking_reference">Booking Reference</label>
                    <input id="booking_reference" name="booking_reference" type="text" value="{{ old('booking_reference') }}" required>
                </div>
                <div>
                    <label for="email">Booking Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="row">
                <div>
                    <label for="check_in_method">Check-In Method</label>
                    <select id="check_in_method" name="check_in_method" required>
                        <option value="online" {{ old('check_in_method')==='online'?'selected':'' }}>Online</option>
                        <option value="mobile" {{ old('check_in_method')==='mobile'?'selected':'' }}>Mobile</option>
                        <option value="kiosk" {{ old('check_in_method')==='kiosk'?'selected':'' }}>Kiosk</option>
                        <option value="counter" {{ old('check_in_method')==='counter'?'selected':'' }}>Counter</option>
                    </select>
                </div>
                <div>
                    <label for="terminal_number">Terminal</label>
                    <select id="terminal_number" name="terminal_number" required>
                        @for($t=1;$t<=5;$t++)
                            <option value="{{ $t }}" {{ (old('terminal_number')==$t) || (!old('terminal_number') && request('terminal')==$t) ? 'selected' : '' }}>Terminal {{ $t }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="row">
                <div>
                    <label for="seat_number">Preferred Seat (optional)</label>
                    <input id="seat_number" name="seat_number" type="text" value="{{ old('seat_number') }}" placeholder="e.g., 12A">
                </div>
                <div>
                    <label>
                        <input type="checkbox" name="priority_boarding" value="1" {{ old('priority_boarding')?'checked':'' }}>
                        Priority boarding
                    </label>
                </div>
            </div>
            <label for="special_assistance">Special Assistance (optional)</label>
            <textarea id="special_assistance" name="special_assistance" placeholder="Wheelchair, medical, etc.">{{ old('special_assistance') }}</textarea>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:.8rem">
                <a class="btn" href="{{ route('checkin.create') }}"><i class="fas fa-rotate"></i> Reset</a>
                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Check-In</button>
            </div>
        </form>

        @if(session('bp'))
            <div class="success" style="margin-top:12px">
                <i class="fas fa-ticket"></i> Boarding Pass: <strong>{{ session('bp') }}</strong>
            </div>
        @endif
    </div>
</div>
</body>
</html>
