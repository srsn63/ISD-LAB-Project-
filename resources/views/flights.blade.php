<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flights - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;background:#0f172a;color:#e2e8f0;margin:0}
        .wrap{max-width:1100px;margin:0 auto;padding:2rem}
        header{display:flex;justify-content:space-between;align-items:center;padding:1rem 0}
        a.btn{display:inline-flex;gap:8px;align-items:center;padding:.6rem 1rem;border-radius:10px;text-decoration:none;color:#fff;background:linear-gradient(135deg,#1e40af,#3b82f6)}
        table{width:100%;border-collapse:collapse;margin-top:1rem}
        th,td{padding:.75rem;border-bottom:1px solid rgba(148,163,184,.2)}
        th{color:#60a5fa;text-align:left}
        .tag{padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:700;border:1px solid rgba(255,255,255,.15)}
        .tag.scheduled{background:rgba(59,130,246,.15);color:#93c5fd}
        .tag.delayed{background:rgba(245,158,11,.15);color:#fbbf24}
        .tag.departed{background:rgba(34,197,94,.15);color:#86efac}
        .tag.cancelled{background:rgba(239,68,68,.15);color:#fecaca}
        .actions .btn-sm{padding:.4rem .7rem;background:linear-gradient(135deg,#10b981,#059669);border:none;border-radius:8px;color:#fff;cursor:pointer}
        .muted{color:#94a3b8;font-size:.9rem;margin-top:1rem}
        .pagination{display:flex;gap:8px;flex-wrap:wrap;margin-top:1rem}
        .pagination a,.pagination span{padding:.35rem .6rem;border-radius:8px;border:1px solid rgba(148,163,184,.25);color:#cbd5e1;text-decoration:none}
        .pagination .active{background:#1e40af;border-color:#1e40af}
    .filters{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-top:1rem}
    .filters input,.filters select{padding:.6rem .8rem;border-radius:8px;border:1px solid rgba(148,163,184,.25);background:#0b1224;color:#e2e8f0}
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <h1><i class="fas fa-plane-departure"></i> All Flights</h1>
        <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="btn"><i class="fas fa-home"></i> Home</a>
    </header>

    @if(session('status'))
        <div class="muted">{{ session('status') }}</div>
    @endif

    <form class="filters" method="GET" action="{{ route('flights.index') }}">
        <input type="text" name="origin" value="{{ request('origin') }}" placeholder="Origin">
        <input type="text" name="destination" value="{{ request('destination') }}" placeholder="Destination">
        <input type="date" name="date" value="{{ request('date') }}">
        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min Price" step="0.01" min="0">
        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max Price" step="0.01" min="0">
        <button class="btn" type="submit"><i class="fas fa-filter"></i> Filter</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Flight</th>
                    <th>Airline</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Status</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($flights as $f)
                <tr>
                    <td>{{ $f->flight_number }}</td>
                    <td>{{ $f->airline }}</td>
                    <td>{{ $f->origin }}</td>
                    <td>{{ $f->destination }}</td>
                    <td>{{ \Carbon\Carbon::parse($f->departure_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($f->arrival_at)->format('Y-m-d H:i') }}</td>
                    <td><span class="tag {{ $f->status }}">{{ ucfirst($f->status) }}</span></td>
                    <td>{{ $f->seats }}</td>
                    <td>$ {{ number_format($f->price, 2) }}</td>
                    <td>
                        @if(in_array($f->status, ['scheduled','delayed']) && (int)$f->seats > 0)
                            <form method="POST" action="{{ route('bookings.store') }}" class="actions" style="display:flex;gap:6px;align-items:center;">
                                @csrf
                                <input type="hidden" name="flight_id" value="{{ $f->id }}">
                                <button type="submit" class="btn-sm"><i class="fas fa-ticket"></i> Book Ticket</button>
                            </form>
                        @else
                            <span class="muted">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="muted">No flights available.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($flights) && method_exists($flights, 'links'))
        <div class="pagination">
            {{ $flights->onEachSide(1)->links() }}
        </div>
    @endif
</div>
</body>
</html>
