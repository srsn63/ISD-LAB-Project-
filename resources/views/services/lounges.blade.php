<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lounges - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-blue:#1e40af; --light-blue:#3b82f6; --sky-blue:#60a5fa; --dark-bg:#0f172a; --card-bg:rgba(30,41,59,.9); --text-light:#e2e8f0; --text-muted:#94a3b8; --accent-gold:#fbbf24; }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#334155 100%);color:var(--text-light);min-height:100vh;overflow-x:hidden}
        .bg-animation{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
        .cloud{position:absolute;background:rgba(255,255,255,.05);border-radius:50%;animation:float 20s infinite ease-in-out;box-shadow:0 0 60px rgba(255,255,255,.1)}
        .cloud:nth-child(1){width:300px;height:300px;top:10%;left:-150px}
        .cloud:nth-child(2){width:200px;height:200px;top:40%;right:-100px;animation-delay:3s}
        .cloud:nth-child(3){width:250px;height:250px;bottom:20%;left:50%;animation-delay:6s}
        @keyframes float{0%,100%{transform:translateY(0) translateX(0)}50%{transform:translateY(-30px) translateX(30px)}}
        .topbar{position:sticky;top:0;z-index:10;background:rgba(15,23,42,.95);backdrop-filter:blur(15px);padding:14px 6vw;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(59,130,246,.2);box-shadow:0 4px 30px rgba(0,0,0,.3)}
        .brand{display:flex;align-items:center;gap:10px;color:var(--sky-blue);font-weight:800;text-decoration:none}
        .brand i{color:var(--accent-gold)}
        .back-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;border:none;border-radius:10px;padding:10px 14px;text-decoration:none;box-shadow:0 10px 25px rgba(59,130,246,.25)}
        .container{position:relative;z-index:1;max-width:1100px;margin:40px auto;padding:0 6vw}
        .card{background:var(--card-bg);border:1px solid rgba(59,130,246,.2);border-radius:16px;box-shadow:0 12px 35px rgba(2,6,23,.4);padding:28px}
        h1{font-size:2rem;color:var(--sky-blue);margin-bottom:10px}
        p{color:var(--text-muted);line-height:1.7;margin:8px 0}
        .grid{display:grid;gap:16px;margin-top:16px}
        @media(min-width:900px){.grid{grid-template-columns:1fr 1fr}}
        .feature{background:rgba(30,41,59,.7);border:1px solid rgba(59,130,246,.15);border-radius:14px;padding:18px}
        .feature h3{color:#fff;margin-bottom:6px;font-size:1.05rem}
        .muted{color:var(--text-muted)}
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="cloud"></div>
        <div class="cloud"></div>
        <div class="cloud"></div>
    </div>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}"><i class="fa-solid fa-couch"></i> Lalon Airport</a>
        <a class="back-btn" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </header>
    <main class="container">
        <section class="card">
            <h1>Lounges</h1>
            <p>Relax, refresh, and recharge before your flight in our premium lounges designed for comfort and productivity.</p>
            <div class="grid">
                <div class="feature">
                    <h3>Comfort & Privacy</h3>
                    <p class="muted">Enjoy quiet zones, comfortable seating, and dedicated workspaces.</p>
                </div>
                <div class="feature">
                    <h3>Food & Beverages</h3>
                    <p class="muted">Complimentary snacks, hot meals, and a selection of beverages.</p>
                </div>
                <div class="feature">
                    <h3>Showers & Amenities</h3>
                    <p class="muted">Freshen up with showers, toiletries, and nap pods in select lounges.</p>
                </div>
                <div class="feature">
                    <h3>Access Options</h3>
                    <p class="muted">Access with eligible tickets, memberships, or paid entry.</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
