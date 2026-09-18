<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jara — Manajemen Task')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --bg-light: #f8fafc;
            --sidebar-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: 260px;
            padding: 1.5rem 2rem;
            min-height: 100vh;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.625rem 0.875rem;
            color: var(--text-muted);
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            margin-bottom: 2px;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        .nav-link-custom i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Avatar Stack */
        .avatar-group {
            display: flex;
            align-items: center;
        }

        .avatar-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #ffffff;
            border: 2px solid #ffffff;
            margin-left: -8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
            position: relative;
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .avatar-circle:first-child {
            margin-left: 0;
        }

        .avatar-circle:hover {
            transform: translateY(-2px) scale(1.1);
            z-index: 5;
        }

        .avatar-lg {
            width: 40px;
            height: 40px;
            font-size: 0.95rem;
            border-width: 2px;
        }

        /* Task Row / Todoist Style */
        .task-item-row {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 0.625rem;
            padding: 0.875rem 1.125rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }

        .task-item-row:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .task-item-row.completed {
            background-color: #f8fafc;
            opacity: 0.75;
        }

        .task-item-row.completed .task-title {
            text-decoration: line-through;
            color: var(--text-muted);
        }

        /* Timeline Styling */
        .timeline {
            position: relative;
            padding-left: 1.75rem;
            margin-top: 1rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .timeline-icon {
            position: absolute;
            left: -1.75rem;
            top: 2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: var(--primary-color);
        }

        .timeline-icon.status {
            border-color: #0ea5e9;
            color: #0ea5e9;
        }

        .timeline-icon.collab {
            border-color: #10b981;
            color: #10b981;
        }

        .timeline-icon.note {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        .timeline-date {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Custom Badges */
        .badge-priority-tinggi {
            background-color: #fee2e2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .badge-priority-sedang {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .badge-priority-rendah {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .badge-role-editor {
            background-color: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .badge-role-viewer {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-size: 0.72rem;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar p-3" id="mainSidebar">
        <!-- Brand Header -->
        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="bi bi-check2-all fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold tracking-tight">Jara</h5>
                    <small class="text-muted" style="font-size: 0.7rem;">Task & Kolaborasi Tim</small>
                </div>
            </div>
            <button class="btn btn-sm btn-light d-lg-none" id="closeSidebarBtn">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Current Persona / Profile Card -->
        <div class="card border-0 bg-light rounded-3 p-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-circle avatar-lg" style="background-color: {{ $currentUser['color'] ?? '#4f46e5' }};">
                    {{ $currentUser['avatar'] ?? 'ME' }}
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="fw-semibold text-truncate small">{{ $currentUser['name'] ?? 'Menza' }}</div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-white text-primary border" style="font-size: 0.65rem;">
                            {{ $currentUser['id'] === 1 ? 'Pemilik Task' : ($currentUser['role'] === 'admin' ? 'Admin' : 'User') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-grow-1 overflow-auto pe-1">
            <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Tampilan Utama</div>
            <nav class="nav flex-column mb-3">
                <a class="nav-link-custom {{ ($currentFilter ?? 'all') === 'all' ? 'active' : '' }}" href="{{ route('tasks.index', ['list_id' => $currentListId ?? 1, 'filter' => 'all']) }}">
                    <i class="bi bi-list-task"></i> Semua Task
                </a>
                <a class="nav-link-custom {{ ($currentFilter ?? '') === 'my_tasks' ? 'active' : '' }}" href="{{ route('tasks.index', ['list_id' => $currentListId ?? 1, 'filter' => 'my_tasks']) }}">
                    <i class="bi bi-person-check"></i> Dibuat oleh Saya
                </a>
                <a class="nav-link-custom {{ ($currentFilter ?? '') === 'assigned_to_me' ? 'active' : '' }}" href="{{ route('tasks.index', ['list_id' => $currentListId ?? 1, 'filter' => 'assigned_to_me']) }}">
                    <i class="bi bi-people"></i> Ditugaskan ke Saya
                </a>
                <a class="nav-link-custom {{ ($currentFilter ?? '') === 'high_priority' ? 'active' : '' }}" href="{{ route('tasks.index', ['list_id' => $currentListId ?? 1, 'filter' => 'high_priority']) }}">
                    <i class="bi bi-exclamation-triangle text-danger"></i> Prioritas Tinggi
                </a>
            </nav>

            <!-- Lists Section (SRS-F-03 - Tugas Novelya) -->
            <div class="d-flex align-items-center justify-content-between text-uppercase text-muted fw-bold mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                <span>Daftar / List</span>
                <button class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#createListModal" title="Buat List Baru">
                    <i class="bi bi-plus-circle-fill"></i> Tambah
                </button>
            </div>
            <nav class="nav flex-column mb-3">
                @foreach($lists ?? [] as $list)
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <a class="nav-link-custom flex-grow-1 {{ ($currentListId ?? 1) == $list['id'] && !request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('tasks.index', ['list_id' => $list['id'], 'filter' => $currentFilter ?? 'all']) }}">
                            <i class="bi bi-folder2{{ ($currentListId ?? 1) == $list['id'] && !request()->routeIs('admin.*') ? '-open text-primary' : '' }}"></i> 
                            <span class="text-truncate me-1">{{ $list['name'] }}</span>
                        </a>
                        <div class="d-flex align-items-center gap-1 ms-1">
                            @if(!empty($list['is_owner']))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1 py-0 rounded" style="font-size: 0.62rem;" title="Anda adalah Pemilik List ini (SRS-F-17)">
                                    <i class="bi bi-star-fill text-warning me-1"></i>Owner
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-1 py-0 rounded" style="font-size: 0.62rem;" title="List dibagikan kepada Anda">
                                    Member
                                </span>
                            @endif
                            @php
                                $canDeleteList = (!empty($list['is_owner'])) || (($currentUser['role'] ?? '') === 'admin');
                            @endphp
                            @if($canDeleteList)
                                <button type="button" class="btn btn-link btn-sm text-danger p-1 text-decoration-none opacity-50 hover-opacity-100" 
                                        onclick="openDeleteListModal({{ $list['id'] }}, '{{ addslashes($list['name']) }}')" 
                                        title="Hapus List Beserta Seluruh Isinya (SRS-F-18)">
                                    <i class="bi bi-trash text-danger" style="font-size: 0.75rem;"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </nav>

            <!-- Panel Admin (SRS-F-14, SRS-F-15, SRS-F-16 - Tugas Iza) -->
            <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Panel Admin (Iza)</div>
            <nav class="nav flex-column mb-3">
                <a class="nav-link-custom {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                    <i class="bi bi-people-fill text-dark"></i> Kelola Pengguna
                </a>
                <a class="nav-link-custom {{ request()->routeIs('admin.logs') ? 'active' : '' }}" href="{{ route('admin.logs') }}">
                    <i class="bi bi-journal-text text-dark"></i> Log Aktivitas Admin
                </a>
            </nav>

            <!-- Team Collaboration Info -->
            <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Tim Terhubung</div>
            <div class="p-2 border rounded-3 bg-white mb-3" style="font-size: 0.8rem;">
                <div class="fw-semibold text-truncate mb-1"><i class="bi bi-shield-check text-primary me-1"></i> Tim Praktikum PPK</div>
                <div class="text-muted" style="font-size: 0.72rem;">Menza, Budi, Siti, Joshua, Novelya, Iza</div>
            </div>
        </div>

        <!-- Footer / Reset Data -->
        <div class="pt-3 border-top mt-auto">
            <form action="{{ route('reset-data') }}" method="POST" onsubmit="return confirm('Reset semua data sampel ke awal?')">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm w-100 py-1" style="font-size: 0.75rem;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Data Sampel
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" id="openSidebarBtn">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h4 class="fw-bold mb-0">
                        @yield('header_title', 'Manajemen Task')
                    </h4>
                    <p class="text-muted small mb-0">
                        @yield('header_subtitle', 'Pantau progres task, prioritas, dan kolaborator tim')
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Persona Switcher Dropdown (Fitur Demo Praktikum) -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-bounding-box"></i>
                        <span class="d-none d-md-inline">Lihat Sebagai:</span>
                        <strong class="text-dark">{{ $currentUser['name'] }}</strong>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 250px;">
                        <li class="dropdown-header text-uppercase" style="font-size: 0.65rem;">Ganti Akun Demo (Uji Hak Akses)</li>
                        @foreach($users ?? [] as $user)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ $currentUser['id'] === $user['id'] ? 'active' : '' }}" href="{{ route('switch-user', $user['id']) }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle" style="background-color: {{ $user['color'] }}; width: 22px; height: 22px; font-size: 0.6rem;">
                                            {{ $user['avatar'] }}
                                        </div>
                                        <span>{{ $user['name'] }}</span>
                                    </div>
                                    <small class="badge {{ $user['id'] === 1 ? 'bg-primary' : ($user['role'] === 'admin' ? 'bg-dark' : 'bg-secondary') }}" style="font-size: 0.6rem;">
                                        {{ $user['id'] === 1 ? 'Owner' : ($user['role'] === 'admin' ? 'Admin' : 'Member') }}
                                    </small>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Notifications Dropdown (SRS-F-10 Preview) -->
                <div class="dropdown">
                    <button class="btn btn-light btn-sm position-relative rounded-3 p-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-6"></i>
                        @if(count($notifications ?? []) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Notifikasi Baru</span>
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="width: 320px;">
                        <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom mb-2">
                            <span class="fw-semibold small">Notifikasi Kolaborator</span>
                            <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">{{ count($notifications ?? []) }} Baru</span>
                        </div>
                        @forelse($notifications ?? [] as $notif)
                            <div class="p-2 mb-1 bg-light rounded-2" style="font-size: 0.78rem;">
                                <div class="text-dark">{{ $notif['message'] }}</div>
                                <div class="text-muted" style="font-size: 0.68rem;"><i class="bi bi-clock me-1"></i>{{ $notif['timestamp'] }}</div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3 small">
                                <i class="bi bi-bell-slash fs-4 d-block mb-1"></i>
                                Tidak ada notifikasi baru
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-3 p-2" title="Keluar / Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>

                <!-- Quick Add Task Button -->
                <button class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-sm-inline">Task Baru</span>
                </button>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="small flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-octagon-fill fs-5 text-danger"></i>
                <div class="small flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Dynamic Content Slot -->
        @yield('content')
    </div>

    <!-- Modal Buat List Baru (SRS-F-17 - Auto-Ownership) -->
    @include('tasks.partials.modal-create-list')

    <!-- Modal Safeguard Hapus List Kaskade & Rollback (SRS-F-18) -->
    @include('tasks.partials.modal-delete-list')

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar on Mobile
        const mainSidebar = document.getElementById('mainSidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');

        if (openBtn) {
            openBtn.addEventListener('click', () => mainSidebar.classList.add('show'));
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', () => mainSidebar.classList.remove('show'));
        }
    </script>
    @stack('scripts')
</body>
</html>

