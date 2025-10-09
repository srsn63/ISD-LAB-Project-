<?php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lalon Airport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary-blue: #1e40af;
            --light-blue: #3b82f6;
            --sky-blue: #60a5fa;
            --dark-bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.9);
            --text-light: #e2e8f0;
            --text-muted: #94a3b8;
            --accent-gold: #fbbf24;
            --accent-teal: #2dd4bf;
            --gradient-primary: linear-gradient(135deg, #1e40af, #3b82f6);
            --gradient-secondary: linear-gradient(135deg, #3b82f6, #60a5fa);
            --gradient-accent: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            color: var(--text-light);
            overflow-x: hidden;
        }

        /* Background animation (clouds) */
        .bg-animation { position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none; }
        .cloud {
            position: absolute; background: rgba(255,255,255,0.05); border-radius: 50%;
            animation: float 20s infinite ease-in-out;
            box-shadow: 0 0 60px rgba(255,255,255,0.1);
        }
        .cloud:nth-child(1){ width:300px;height:300px; top:10%; left:-150px; animation-delay:0s;}
        .cloud:nth-child(2){ width:200px;height:200px; top:40%; right:-100px; animation-delay:3s;}
        .cloud:nth-child(3){ width:250px;height:250px; bottom:20%; left:50%; animation-delay:6s;}
        .cloud:nth-child(4){ width:180px;height:180px; top:60%; left:10%; animation-delay:9s;}
        .cloud:nth-child(5){ width:220px;height:220px; top:20%; right:20%; animation-delay:12s;}
        @keyframes float { 0%,100%{transform:translateY(0) translateX(0);} 50%{transform:translateY(-30px) translateX(30px);} }

        /* Layout */
        .container { position: relative; z-index: 2; padding: 7rem 5% 4rem; }
        header.topbar {
            position: fixed; top:0; left:0; right:0;
            background: rgba(15,23,42,0.95); backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(59,130,246,0.25);
            padding: 1rem 5%; display:flex; align-items:center; justify-content:space-between; z-index: 5;
            box-shadow: 0 6px 30px rgba(59,130,246,0.25);
        }
        .brand { display:flex; align-items:center; gap:12px; font-weight:800; color: var(--sky-blue); text-decoration:none; }
        .brand .logo-icon {
            width:44px;height:44px;border-radius:12px; display:flex; align-items:center; justify-content:center;
            background: var(--gradient-primary); box-shadow: 0 6px 20px rgba(59,130,246,0.45);
        }
        .top-actions { display:flex; align-items:center; gap:12px; }
        .btn {
            display:inline-flex; align-items:center; gap:8px; padding:0.65rem 1.1rem;
            border-radius:10px; color:#fff; text-decoration:none; font-weight:700; border:none; cursor:pointer;
            background: var(--gradient-primary); box-shadow: 0 8px 25px rgba(59,130,246,0.35); transition: all .25s ease;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(59,130,246,0.55); }
        .btn.alt { background: linear-gradient(135deg, #334155, #1f2937); box-shadow: 0 8px 25px rgba(0,0,0,.35); }
        .btn.danger { background: linear-gradient(135deg, #dc2626, #b91c1c); }
        .btn.success { background: linear-gradient(135deg, #10b981, #059669); }
        .btn.warning { background: var(--gradient-accent); color:#0b1020; }

        .grid {
            display:grid; grid-template-columns: 1.2fr .8fr; gap: 2rem; align-items: start;
        }
        @media (max-width: 1100px){ .grid{ grid-template-columns: 1fr; } }

        .card {
            background: var(--card-bg); border: 1px solid rgba(59,130,246,0.35);
            border-radius: 18px; padding: 1.4rem; box-shadow: 0 10px 35px rgba(0,0,0,0.35);
            overflow: hidden; position: relative;
        }
        .card::before {
            content:''; position:absolute; top:0; left:0; width:100%; height:4px; background: var(--gradient-primary);
        }
        .card h2 { color: var(--sky-blue); font-size:1.4rem; margin-bottom: 1rem; display:flex; align-items:center; gap:10px; }

        /* Toolbar */
        .toolbar { display:flex; gap:10px; flex-wrap: wrap; margin-bottom: 1rem; align-items:center; }
        .field { position:relative; flex:1; min-width: 220px; }
        .field input, .field select {
            width:100%; padding: 0.9rem 1rem 0.9rem 2.4rem; border-radius: 12px;
            border:1px solid rgba(59,130,246,0.35); background: rgba(15,23,42,0.75); color: var(--text-light);
            outline:none; transition: all .2s ease; font-weight:500;
        }
        .field input:focus, .field select:focus {
            border-color: var(--sky-blue); box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
            background: rgba(15,23,42,0.92);
        }
        .field .icon {
            position:absolute; left:10px; top:50%; transform: translateY(-50%); color: var(--text-muted);
        }

        /* Table */
        .table-wrap { overflow:auto; border-radius: 12px; border:1px solid rgba(59,130,246,0.25); }
        table { width:100%; border-collapse: collapse; min-width: 800px; }
        thead th {
            position:sticky; top:0; background: rgba(15,23,42,0.95);
            text-align:left; padding: 0.9rem; font-size:0.9rem; color: var(--sky-blue); border-bottom:1px solid rgba(59,130,246,0.25);
        }
        tbody td { padding: 0.85rem; border-bottom: 1px solid rgba(148,163,184,0.15); color: var(--text-light); }
        tbody tr:hover { background: rgba(59,130,246,0.08); }

        .tag { padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 700; border:1px solid rgba(255,255,255,.15); }
        .tag.admin { background: rgba(59,130,246,0.15); color: #93c5fd; }
        .tag.user { background: rgba(16,185,129,0.15); color: #6ee7b7; }

        .actions { display:flex; gap:8px; flex-wrap: wrap; }
        .icon-btn { display:inline-flex; align-items:center; justify-content:center; width:36px;height:36px; border-radius:10px; border:none; cursor:pointer; background: rgba(30,41,59,0.9); color:#cbd5e1; }
        .icon-btn.edit { background: rgba(59,130,246,0.18); color:#93c5fd; }
        .icon-btn.delete { background: rgba(239,68,68,0.18); color:#fecaca; }

        /* Section titles */
        .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-muted); letter-spacing: .04em; text-transform: uppercase; margin: 1rem 0 .5rem; }

        /* Modals */
        .modal {
            position: fixed; inset:0; display:none; align-items:center; justify-content:center; z-index: 50;
            background: rgba(2,6,23,0.65); backdrop-filter: blur(6px);
        }
        .modal.active { display:flex; }
        .modal .modal-card { width: 100%; max-width: 560px; }
        .modal .modal-body { padding: 1.2rem; }
        .modal .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding: 0 1.2rem 1.2rem; }

        .form-grid { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
        .form-grid .full { grid-column: 1 / -1; }
        label { font-size:.9rem; color: var(--text-muted); margin-bottom:.35rem; display:block; }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="datetime-local"], select, textarea {
            width:100%; padding: .85rem 1rem; border-radius: 10px; border:1px solid rgba(59,130,246,0.35);
            background: rgba(15,23,42,0.75); color: var(--text-light); outline:none; transition: all .2s ease; font-weight:500;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--sky-blue); box-shadow: 0 0 0 3px rgba(59,130,246,0.2); background: rgba(15,23,42,0.92); }

        .muted { color: var(--text-muted); font-size: .9rem; }

        /* Small helpers */
        .spacer { height: 16px; }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="bg-animation">
        <div class="cloud"></div><div class="cloud"></div><div class="cloud"></div><div class="cloud"></div><div class="cloud"></div>
    </div>

    <!-- Top bar -->
    <header class="topbar">
        <a class="brand" href="#">
            <div class="logo-icon"><i class="fas fa-plane"></i></div>
            <span>Lalon Airport Admin</span>
        </a>
        <div class="top-actions">
            <a href="{{ route('home') }}" class="btn alt"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
    </header>

    <div class="container">
        <div class="grid">
            <!-- Users Management -->
            <section class="card">
                <h2><i class="fas fa-users"></i> Users Management</h2>

                <div class="toolbar">
                    <div class="field">
                        <i class="fas fa-magnifying-glass icon"></i>
                        <input id="userSearch" type="text" placeholder="Search users by name or email...">
                    </div>
                    <div class="field" style="max-width:180px">
                        <i class="fas fa-filter icon"></i>
                        <select id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <button id="btnOpenCreateUser" class="btn"><i class="fas fa-plus"></i> New User</button>
                </div>

                <div class="table-wrap">
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th style="width:80px">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th style="width:140px">Role</th>
                                <th style="width:180px">Created</th>
                                <th style="width:160px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hasUsers = isset($users) && count($users) > 0;
                            @endphp

                            @if($hasUsers)
                                @foreach($users as $user)
                                    <tr data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" data-role="{{ $user->role ?? 'user' }}">
                                        <td>#{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="tag {{ ($user->role ?? 'user') === 'admin' ? 'admin' : 'user' }}">
                                                {{ strtoupper($user->role ?? 'user') }}
                                            </span>
                                        </td>
                                        <td>{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="actions">
                                                <button
                                                    class="icon-btn edit"
                                                    data-action="edit-user"
                                                    data-id="{{ $user->id }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-role="{{ $user->role ?? 'user' }}"
                                                    aria-label="Edit user"
                                                    title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </button>

                                                <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('Delete this user?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="icon-btn delete" aria-label="Delete user" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" style="text-align:center; padding: 1.25rem; color: var(--text-muted);">
                                        <i class="fas fa-circle-info"></i> No users to display. Create a new user to get started.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(isset($users) && method_exists($users, 'links'))
                    <div class="spacer"></div>
                    <div>
                        {{ $users->links() }}
                    </div>
                @endif
            </section>

            <!-- Flights: Add New -->
            <section class="card">
                <h2><i class="fas fa-plane-departure"></i> Add New Flight</h2>

                <form method="POST" action="{{ route('flights.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="full">
                            <label for="flight_number">Flight Number</label>
                            <input type="text" id="flight_number" name="flight_number" placeholder="e.g., LA-204" required>
                        </div>

                        <div>
                            <label for="airline">Airline</label>
                            <input type="text" id="airline" name="airline" placeholder="e.g., Lalon Air" required>
                        </div>
                        <div>
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="boarding">Boarding</option>
                                <option value="departed">Departed</option>
                                <option value="delayed">Delayed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label for="origin">Origin</label>
                            <input type="text" id="origin" name="origin" placeholder="From (City/Code)" required>
                        </div>
                        <div>
                            <label for="destination">Destination</label>
                            <input type="text" id="destination" name="destination" placeholder="To (City/Code)" required>
                        </div>

                        <div>
                            <label for="departure_at">Departure</label>
                            <input type="datetime-local" id="departure_at" name="departure_at" required>
                        </div>
                        <div>
                            <label for="arrival_at">Arrival</label>
                            <input type="datetime-local" id="arrival_at" name="arrival_at" required>
                        </div>

                        <div>
                            <label for="price">Price (USD)</label>
                            <input type="number" min="0" step="0.01" id="price" name="price" placeholder="e.g., 249.99" required>
                        </div>
                        <div>
                            <label for="seats">Total Seats</label>
                            <input type="number" min="1" step="1" id="seats" name="seats" placeholder="e.g., 180" required>
                        </div>

                        <div class="full" style="display:flex; gap:10px; justify-content:flex-end; margin-top:.25rem;">
                            <button type="reset" class="btn alt"><i class="fas fa-rotate-left"></i> Reset</button>
                            <button type="submit" class="btn success"><i class="fas fa-plus-circle"></i> Add Flight</button>
                        </div>
                    </div>
                </form>

                <p class="muted" style="margin-top:.75rem;">
                    Note: This form only creates flights. Listing/editing flights can be added later.
                </p>
            </section>
        </div>
    </div>

    <!-- Create User Modal -->
    <div id="modalCreateUser" class="modal" aria-hidden="true">
        <div class="modal-card card" role="dialog" aria-modal="true" aria-labelledby="createUserTitle">
            <h2 id="createUserTitle"><i class="fas fa-user-plus"></i> Create User</h2>
            <div class="modal-body">
                <form id="formCreateUser" method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="full">
                            <label for="c_name">Full Name</label>
                            <input id="c_name" type="text" name="name" placeholder="Enter full name" required>
                        </div>
                        <div class="full">
                            <label for="c_email">Email</label>
                            <input id="c_email" type="email" name="email" placeholder="name@example.com" required>
                        </div>
                        <div>
                            <label for="c_password">Password</label>
                            <input id="c_password" type="password" name="password" placeholder="Minimum 8 characters" required>
                        </div>
                        <div>
                            <label for="c_role">Role</label>
                            <select id="c_role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn alt" data-close="#modalCreateUser"><i class="fas fa-xmark"></i> Cancel</button>
                <button class="btn success" form="formCreateUser" type="submit"><i class="fas fa-check"></i> Create</button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="modalEditUser" class="modal" aria-hidden="true" data-update-template="{{ route('users.update', '__ID__') }}">
        <div class="modal-card card" role="dialog" aria-modal="true" aria-labelledby="editUserTitle">
            <h2 id="editUserTitle"><i class="fas fa-user-pen"></i> Edit User</h2>
            <div class="modal-body">
                <form id="formEditUser" method="POST" action="#">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <input type="hidden" id="e_id" name="id">
                        <div class="full">
                            <label for="e_name">Full Name</label>
                            <input id="e_name" type="text" name="name" required>
                        </div>
                        <div class="full">
                            <label for="e_email">Email</label>
                            <input id="e_email" type="email" name="email" required>
                        </div>
                        <div class="full">
                            <label for="e_role">Role</label>
                            <select id="e_role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="full">
                            <label for="e_password">Password (optional)</label>
                            <input id="e_password" type="password" name="password" placeholder="Leave blank to keep current">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn alt" data-close="#modalEditUser"><i class="fas fa-xmark"></i> Cancel</button>
                <button class="btn" form="formEditUser" type="submit"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        // Open/Close Modal helpers
        function openModal(sel){ document.querySelector(sel).classList.add('active'); }
        function closeModal(sel){ document.querySelector(sel).classList.remove('active'); }
        document.querySelectorAll('[data-close]').forEach(btn=>{
            btn.addEventListener('click', e => {
                e.preventDefault();
                closeModal(btn.getAttribute('data-close'));
            });
        });

        // Create user modal open
        document.getElementById('btnOpenCreateUser')?.addEventListener('click', () => {
            openModal('#modalCreateUser');
            document.getElementById('formCreateUser').reset();
            document.getElementById('c_name').focus();
        });

        // Edit user modal wiring (single modal, populated from row button)
        document.querySelectorAll('[data-action="edit-user"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name') || '';
                const email = btn.getAttribute('data-email') || '';
                const role = btn.getAttribute('data-role') || 'user';

                const modal = document.getElementById('modalEditUser');
                const template = modal.getAttribute('data-update-template'); // "/users/__ID__"
                const action = template.replace('__ID__', id);

                document.getElementById('formEditUser').setAttribute('action', action);
                document.getElementById('e_id').value = id;
                document.getElementById('e_name').value = name;
                document.getElementById('e_email').value = email;
                document.getElementById('e_role').value = role;

                openModal('#modalEditUser');
                document.getElementById('e_name').focus();
            });
        });

        // Basic client-side filter for Users table
        const userSearch = document.getElementById('userSearch');
        const roleFilter = document.getElementById('roleFilter');
        const rows = Array.from(document.querySelectorAll('#usersTable tbody tr'));

        function applyFilter(){
            const q = (userSearch?.value || '').trim().toLowerCase();
            const role = roleFilter?.value || '';
            rows.forEach(row => {
                if(!row.dataset) return;
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const r = row.getAttribute('data-role') || '';
                const matchesText = !q || name.includes(q) || email.includes(q);
                const matchesRole = !role || r === role;
                row.style.display = (matchesText && matchesRole) ? '' : 'none';
            });
        }
        userSearch?.addEventListener('input', applyFilter);
        roleFilter?.addEventListener('change', applyFilter);

        // Close modal on backdrop click
        document.querySelectorAll('.modal').forEach(m => {
            m.addEventListener('click', (e) => {
                if(e.target === m) closeModal('#' + m.id);
            });
        });

        // Accessibility: ESC to close top-most modal
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape'){
                const active = document.querySelector('.modal.active');
                if(active) closeModal('#' + active.id);
            }
        });
    </script>
</body>
</html>