<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airport Status - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-blue:#1e40af; --light-blue:#3b82f6; --sky-blue:#60a5fa; --dark-bg:#0f172a; --card-bg:rgba(30,41,59,.9); --text-light:#e2e8f0; --text-muted:#94a3b8; --gradient-primary:linear-gradient(135deg,#1e40af,#3b82f6);} *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#334155 100%);color:var(--text-light);min-height:100vh}
        .topbar{position:sticky;top:0;z-index:10;background:rgba(15,23,42,.95);border-bottom:1px solid rgba(59,130,246,.2);padding:14px 6vw;display:flex;align-items:center;justify-content:space-between}
        .brand{display:flex;align-items:center;gap:10px;color:var(--sky-blue);font-weight:800;text-decoration:none}
        .back-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#1e40af,#3b82f6);color:#fff;border:none;border-radius:10px;padding:10px 14px;text-decoration:none;box-shadow:0 10px 25px rgba(59,130,246,.25)}
        .container{max-width:1200px;margin:30px auto;padding:0 6vw}
        .section-title{font-size:1.6rem;margin:10px 0 16px;color:var(--sky-blue)}
        .grid{display:grid;gap:16px}
        @media(min-width:900px){.grid{grid-template-columns:repeat(3,1fr)}}
        .card{background:var(--card-bg);border:1px solid rgba(59,130,246,.2);border-radius:16px;box-shadow:0 12px 35px rgba(2,6,23,.4);padding:18px}
        .card h3{color:#fff;margin-bottom:8px;font-size:1rem}
        .summary{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:8px}
        .pill{display:inline-block;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700}
        .pill-green{background:rgba(16,185,129,.15);color:#34d399}
        .pill-amber{background:rgba(245,158,11,.15);color:#f59e0b}
        .pill-blue{background:rgba(59,130,246,.15);color:#60a5fa}
        .list{list-style:none;padding:0;margin:0}
        .list li{display:flex;justify-content:space-between;gap:10px;padding:6px 0;border-bottom:1px dashed rgba(59,130,246,.15);color:var(--text-muted)}
        .list li:last-child{border-bottom:none}
        .code{color:#60a5fa;font-weight:700}
        .status{font-weight:700}
    </style>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('home') }}"><i class="fa-solid fa-plane"></i> Lalon Airport</a>
    <a class="back-btn" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
  </header>

  <main class="container">
    <h1 class="section-title">Airport Status</h1>

    <!-- Entrance & Check-in -->
    <section>
      <h2 class="section-title">Entrance & Check‑in</h2>
      <div class="grid">
        <div class="card">
          <h3>Check‑in Counters</h3>
          <div class="summary">
            @php $s = $summary['checkin'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
            <span class="pill pill-green">Active: {{ $s['active'] }}/{{ $s['total'] }}</span>
            <span class="pill pill-blue">PAX today: {{ $s['countToday'] }}</span>
          </div>
          <ul class="list">
            @foreach(($facilities['checkin'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst(str_replace('_',' ',$f->status)) }}</span><span>{{ $f->today_count }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Self Check‑in Kiosks</h3>
          <div class="summary">
            @php $s = $summary['kiosk'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
            <span class="pill pill-amber">Active: {{ $s['active'] }}/{{ $s['total'] }}</span>
            <span class="pill pill-blue">PAX today: {{ $s['countToday'] }}</span>
          </div>
          <ul class="list">
            @foreach(($facilities['kiosk'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst(str_replace('_',' ',$f->status)) }}</span><span>{{ $f->today_count }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Baggage Drop‑off</h3>
          <div class="summary">
            @php $s = $summary['baggage_drop'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
            <span class="pill pill-green">Active: {{ $s['active'] }}/{{ $s['total'] }}</span>
            <span class="pill pill-blue">Bags today: {{ $s['countToday'] }}</span>
          </div>
          <ul class="list">
            @foreach(($facilities['baggage_drop'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst(str_replace('_',' ',$f->status)) }}</span><span>{{ $f->today_count }}</span></li>
            @endforeach
          </ul>
        </div>
      </div>
    </section>

    <!-- Security & Immigration -->
    <section>
      <h2 class="section-title">Security & Immigration</h2>
      <div class="grid">
        <div class="card">
          <h3>Security Lanes</h3>
          @php $s = $summary['security'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span><span class="pill pill-blue">Avg wait: ~{{ max(3, (int) (($facilities['security'] ?? collect())->avg('meta.wait_min') ?? 0)) }}m</span></div>
          <ul class="list">
            @foreach(($facilities['security'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span>{{ $f->meta['wait_min'] ?? '-' }}m</span><span class="status">{{ ucfirst($f->status) }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Departure Immigration</h3>
          @php $s = $summary['immigration_dep'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span><span class="pill pill-blue">Avg wait: ~{{ max(3, (int) (($facilities['immigration_dep'] ?? collect())->avg('meta.wait_min') ?? 0)) }}m</span></div>
          <ul class="list">
            @foreach(($facilities['immigration_dep'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span>{{ $f->meta['wait_min'] ?? '-' }}m</span><span class="status">{{ ucfirst(str_replace('_',' ',$f->status)) }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Customs (Arrival)</h3>
          @php $s = $summary['customs'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-amber">Open: {{ $s['active'] }}/{{ $s['total'] }}</span><span class="pill pill-blue">Avg wait: ~{{ max(2, (int) (($facilities['customs'] ?? collect())->avg('meta.wait_min') ?? 0)) }}m</span></div>
          <ul class="list">
            @foreach(($facilities['customs'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span>{{ $f->meta['wait_min'] ?? '-' }}m</span><span class="status">{{ ucfirst($f->status) }}</span></li>
            @endforeach
          </ul>
        </div>
      </div>
    </section>

    <!-- Departure Area -->
    <section>
      <h2 class="section-title">Departure Area</h2>
      <div class="grid">
        <div class="card">
          <h3>Departure Gates</h3>
          @php $gates = $facilities['gate'] ?? collect(); @endphp
          <div class="summary">
              <span class="pill pill-blue">Total: {{ $gates->count() }}</span>
              <span class="pill pill-green">Boarding: {{ $gates->where('status','boarding')->count() }}</span>
              <span class="pill pill-amber">Final Call: {{ $gates->where('status','final_call')->count() }}</span>
              <span class="pill pill-blue">On Time: {{ $gates->where('status','on_time')->count() }}</span>
          </div>
          <ul class="list">
            @foreach($gates as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst(str_replace('_',' ',$f->status)) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Duty‑Free Shops</h3>
          @php $s = $summary['duty_free'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['duty_free'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Restaurants & Cafes</h3>
          @php $s = $summary['restaurant'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['restaurant'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>VIP/Business Lounges</h3>
          @php $s = $summary['lounge'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['lounge'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>Occupancy: {{ $f->meta['occupancy'] ?? '-' }}%</span></li>
            @endforeach
          </ul>
        </div>
      </div>
    </section>

    <!-- Airside Areas -->
    <section>
      <h2 class="section-title">Airside Areas</h2>
      <div class="grid">
        <div class="card">
          <h3>Jet Bridges</h3>
          @php $s = $summary['jet_bridge'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-amber">In use: {{ ($facilities['jet_bridge'] ?? collect())->where('status','busy')->count() }} / {{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['jet_bridge'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Apron / Tarmac Stands</h3>
          @php $s = $summary['apron'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-amber">Occupied: {{ ($facilities['apron'] ?? collect())->where('status','busy')->count() }} / {{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['apron'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
      </div>
    </section>

    <!-- Arrival Side -->
    <section>
      <h2 class="section-title">Arrival Side</h2>
      <div class="grid">
        <div class="card">
          <h3>Arrival Immigration</h3>
          @php $s = $summary['immigration_arr'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['immigration_arr'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Baggage Belts</h3>
          @php $s = $summary['baggage_belt'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-amber">Running: {{ ($facilities['baggage_belt'] ?? collect())->where('status','busy')->count() }} / {{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['baggage_belt'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ ($f->meta['inbound'] ?? false) ? 'Inbound' : 'Idle' }}</span></li>
            @endforeach
          </ul>
        </div>
        <div class="card">
          <h3>Arrival Customs</h3>
          @php $s = $summary['customs'] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities['customs'] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
      </div>
    </section>

    <!-- Additional Facilities -->
    <section>
      <h2 class="section-title">Additional Facilities</h2>
      <div class="grid">
        @foreach(['info'=>'Information Desks','currency'=>'Currency Exchange','atm'=>'ATMs','medical'=>'Medical Facility','prayer'=>'Prayer Rooms','car_rental'=>'Car Rental','taxi'=>'Taxi Counters','shuttle'=>'Shuttle Services'] as $t => $label)
        <div class="card">
          <h3>{{ $label }}</h3>
          @php $s = $summary[$t] ?? ['active'=>0,'total'=>0,'countToday'=>0]; @endphp
          <div class="summary"><span class="pill pill-green">Open: {{ $s['active'] }}/{{ $s['total'] }}</span></div>
          <ul class="list">
            @foreach(($facilities[$t] ?? []) as $f)
            <li><span class="code">{{ $f->code }}</span><span class="status">{{ ucfirst($f->status) }}</span><span>{{ $f->name }}</span></li>
            @endforeach
          </ul>
        </div>
        @endforeach
      </div>
    </section>
  </main>
</body>
</html>
