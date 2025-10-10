<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baggage Track - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-blue:#1e40af; --light-blue:#3b82f6; --sky-blue:#60a5fa; --dark-bg:#0f172a; --card-bg:rgba(30,41,59,.9); --text-light:#e2e8f0; --text-muted:#94a3b8; --accent-gold:#fbbf24; }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#334155 100%);color:var(--text-light);min-height:100vh;overflow-x:hidden}
        /* Animated Background */
        .bg-animation{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
        .cloud{position:absolute;background:rgba(255,255,255,.05);border-radius:50%;animation:float 20s infinite ease-in-out;box-shadow:0 0 60px rgba(255,255,255,.1)}
        .cloud:nth-child(1){width:300px;height:300px;top:10%;left:-150px}
        .cloud:nth-child(2){width:200px;height:200px;top:40%;right:-100px;animation-delay:3s}
        .cloud:nth-child(3){width:250px;height:250px;bottom:20%;left:50%;animation-delay:6s}
        @keyframes float{0%,100%{transform:translateY(0) translateX(0)}50%{transform:translateY(-30px) translateX(30px)}}
        /* Top bar */
        .topbar{position:sticky;top:0;z-index:10;background:rgba(15,23,42,.95);backdrop-filter:blur(15px);padding:14px 6vw;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(59,130,246,.2);box-shadow:0 4px 30px rgba(0,0,0,.3)}
        .brand{display:flex;align-items:center;gap:10px;color:var(--sky-blue);font-weight:800;text-decoration:none}
        .brand i{color:var(--accent-gold)}
        .back-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;border:none;border-radius:10px;padding:10px 14px;text-decoration:none;box-shadow:0 10px 25px rgba(59,130,246,.25)}
        /* Content */
        .container{position:relative;z-index:1;max-width:1200px;margin:30px auto;padding:0 6vw}
        .section-title{font-size:1.6rem;margin:10px 0 16px;color:var(--sky-blue)}
        .card{background:var(--card-bg);border:1px solid rgba(59,130,246,.2);border-radius:16px;box-shadow:0 12px 35px rgba(2,6,23,.4);padding:18px;margin-bottom:18px}
        .hint{color:var(--text-muted);font-size:.95rem;margin-bottom:8px}
        .filter{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px}
        .filter input,.filter select{background:rgba(30,41,59,.7);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:#e2e8f0;padding:10px 12px}
        .table{width:100%;border-collapse:collapse}
        .table thead th{color:#e2e8f0;font-weight:700;text-align:left;border-bottom:1px solid rgba(59,130,246,.2);padding:12px 10px}
        .table tbody td{color:#94a3b8;padding:10px;border-bottom:1px dashed rgba(59,130,246,.15)}
        .badge{display:inline-block;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700}
        .badge-green{background:rgba(16,185,129,.15);color:#34d399}
        .badge-amber{background:rgba(245,158,11,.15);color:#f59e0b}
        .badge-red{background:rgba(239,68,68,.15);color:#f87171}
        .progress-wrap{display:flex;gap:8px;align-items:center}
        .progress{flex:1;height:8px;background:rgba(59,130,246,.15);border-radius:999px;overflow:hidden}
        .progress > span{display:block;height:100%;background:linear-gradient(90deg,#3b82f6,#60a5fa)}
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="cloud"></div>
        <div class="cloud"></div>
        <div class="cloud"></div>
    </div>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}"><i class="fa-solid fa-plane"></i> Lalon Airport</a>
        <a class="back-btn" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </header>

    <main class="container">
        <h1 class="section-title">Baggage Track</h1>
        <section class="card">
            <p class="hint"><i class="fa-solid fa-circle-info"></i> Search by Baggage Tag (e.g., LA-784512) or Flight No. (e.g., LA220) to view belt and progress updates.</p>
            <div class="filter">
                <input type="text" placeholder="Baggage Tag (e.g., LA-784512)">
                <input type="text" placeholder="Flight No (e.g., LA220)">
                <select>
                    <option value="all">All Terminals</option>
                    <option value="T1">Terminal T1</option>
                    <option value="T2">Terminal T2</option>
                </select>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Bag Tag</th>
                        <th>Passenger</th>
                        <th>Flight</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Terminal</th>
                        <th>Belt</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Last Seen</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>LA-784512</td>
                        <td>R. Ahmed</td>
                        <td>LA123</td>
                        <td>JFK</td>
                        <td>LAL</td>
                        <td>T1</td>
                        <td>Belt A</td>
                        <td><span class="badge badge-amber">On Belt</span></td>
                        <td>
                            <div class="progress-wrap">
                                <div class="progress"><span style="width:75%"></span></div>
                                <small>75%</small>
                            </div>
                        </td>
                        <td>10:42 - Belt A Camera</td>
                    </tr>
                    <tr>
                        <td>LA-784513</td>
                        <td>S. Kumar</td>
                        <td>BA452</td>
                        <td>LHR</td>
                        <td>LAL</td>
                        <td>T2</td>
                        <td>Belt B</td>
                        <td><span class="badge badge-green">Collected</span></td>
                        <td>
                            <div class="progress-wrap">
                                <div class="progress"><span style="width:100%"></span></div>
                                <small>100%</small>
                            </div>
                        </td>
                        <td>11:05 - Exit Gate</td>
                    </tr>
                    <tr>
                        <td>LA-784514</td>
                        <td>M. Wong</td>
                        <td>QR782</td>
                        <td>DOH</td>
                        <td>LAL</td>
                        <td>T1</td>
                        <td>Belt A</td>
                        <td><span class="badge badge-red">Delayed</span></td>
                        <td>
                            <div class="progress-wrap">
                                <div class="progress"><span style="width:30%"></span></div>
                                <small>30%</small>
                            </div>
                        </td>
                        <td>11:10 - Sorting Area</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
