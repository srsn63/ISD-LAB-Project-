<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Media - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-blue:#1e40af; --light-blue:#3b82f6; --sky-blue:#60a5fa; --dark-bg:#0f172a; --card-bg:rgba(30,41,59,.9); --text-light:#e2e8f0; --text-muted:#94a3b8; --gradient-primary:linear-gradient(135deg,#1e40af,#3b82f6);} *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#334155 100%);color:var(--text-light);min-height:100vh}
        .topbar{position:sticky;top:0;z-index:10;background:rgba(15,23,42,.95);border-bottom:1px solid rgba(59,130,246,.2);padding:14px 6vw;display:flex;align-items:center;justify-content:space-between}
        .brand{display:flex;align-items:center;gap:10px;color:var(--sky-blue);font-weight:800;text-decoration:none}
        .back-btn{display:inline-flex;align-items:center;gap:8px;background:var(--gradient-primary);color:#fff;border:none;border-radius:10px;padding:10px 14px;text-decoration:none;box-shadow:0 10px 25px rgba(59,130,246,.25)}
        .container{max-width:1100px;margin:40px auto;padding:0 6vw}
        .card{background:var(--card-bg);border:1px solid rgba(59,130,246,.2);border-radius:16px;box-shadow:0 12px 35px rgba(2,6,23,.4);padding:28px}
        h1{font-size:2.1rem;margin-bottom:10px;color:var(--sky-blue)} p{color:var(--text-muted);line-height:1.7}
        .feed{display:grid;gap:14px;margin-top:16px}
        @media(min-width:900px){.feed{grid-template-columns:1fr 1fr}}
        .post{background:rgba(30,41,59,.7);border:1px solid rgba(59,130,246,.15);border-radius:14px;padding:18px}
        .post h3{color:#fff;margin-bottom:6px}
        .meta{font-size:.9rem;color:var(--text-muted);margin-bottom:8px}
        .footer{color:var(--text-muted);text-align:center;margin:28px 0 20px}
    </style>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @endif
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('home') }}"><i class="fa-solid fa-plane"></i> Lalon Airport</a>
    <a class="back-btn" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
</header>
<main class="container">
    <section class="card">
        <h1>News & Media</h1>
        <p>Stay updated with announcements, press releases, and media coverage from Lalon Airport.</p>
        <div class="feed">
            <article class="post">
                <h3>New International Route Announced</h3>
                <div class="meta">Oct 10, {{ date('Y') }} • Press Release</div>
                <p>We are excited to expand our global network with new direct routes launching next quarter.</p>
            </article>
            <article class="post">
                <h3>Terminal Upgrade Milestone</h3>
                <div class="meta">Sep 22, {{ date('Y') }} • News</div>
                <p>Phase one of our terminal modernization is complete, enhancing comfort and capacity.</p>
            </article>
        </div>
    </section>
    <p class="footer">&copy; {{ date('Y') }} Lalon Airport. All rights reserved.</p>
</main>
</body>
</html>
