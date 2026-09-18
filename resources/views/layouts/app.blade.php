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
            --primary-color: #6366f1; /* Electric Indigo (Linear style) */
            --primary-hover: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.25);
            --bg-light: #f8fafc;
            --sidebar-bg: #090d16; /* Obsidian / Deep Charcoal */
            --sidebar-border: rgba(255, 255, 255, 0.08);
            --sidebar-text: #94a3b8;
            --sidebar-text-muted: #64748b;
            --sidebar-text-active: #ffffff;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
            --sidebar-active-bg: rgba(99, 102, 241, 0.15);
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            letter-spacing: -0.01em;
        }

        /* Sleek Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .sidebar ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); }

        /* Sidebar Styling (Linear Dark Obsidian) */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0d121f 0%, #090d16 100%);
            border-right: 1px solid var(--sidebar-border);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            color: var(--sidebar-text);
        }

        .main-content {
            margin-left: 260px;
            padding: 1.75rem 2.25rem;
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
            padding: 0.55rem 0.8rem;
            color: var(--sidebar-text);
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.86rem;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            margin-bottom: 2px;
        }

        .nav-link-custom:hover {
            background-color: var(--sidebar-hover-bg);
            color: #ffffff;
        }

        .nav-link-custom.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.18) 0%, rgba(99, 102, 241, 0.05) 100%);
            color: #ffffff;
            font-weight: 600;
            border-left: 3px solid #6366f1;
            padding-left: calc(0.8rem - 3px);
        }

        .nav-link-custom i {
            margin-right: 0.65rem;
            font-size: 1.05rem;
            opacity: 0.85;
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
            font-size: 0.68rem;
            font-weight: 700;
            color: #ffffff;
            border: 2px solid #ffffff;
            margin-left: -8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
            cursor: pointer;
            transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .avatar-circle:first-child {
            margin-left: 0;
        }

        .avatar-circle:hover {
            transform: translateY(-2px) scale(1.15);
            z-index: 5;
        }

        .avatar-lg {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
            border-width: 2px;
        }

        /* Task Row / Linear Style */
        .task-item-row {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 0.625rem;
            padding: 0.85rem 1.15rem;
            margin-bottom: 0.45rem;
            transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s ease, border-color 0.18s ease;
            cursor: pointer;
            position: relative;
        }

        .task-item-row:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 4px 6px -2px rgba(15, 23, 42, 0.02);
            transform: translateY(-2px);
        }

        .task-item-row.priority-tinggi {
            border-left: 4px solid #ef4444 !important;
        }

        .task-item-row.priority-sedang {
            border-left: 4px solid #f59e0b !important;
        }

        .task-item-row.priority-rendah {
            border-left: 4px solid #94a3b8 !important;
        }

        .task-item-row.completed {
            background-color: #f8fafc;
            opacity: 0.75;
            border-left: 4px solid #10b981 !important;
        }

        .task-item-row.completed .task-title {
            text-decoration: line-through;
            color: var(--text-muted);
        }

        .task-actions {
            opacity: 0;
            transform: translateX(4px);
            transition: opacity 0.15s ease, transform 0.15s ease;
        }

        .task-item-row:hover .task-actions {
            opacity: 1;
            transform: translateX(0);
        }

        /* Status Dot Indicator */
        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-dot.todo { background-color: #94a3b8; }
        .status-dot.progress { 
            background-color: #6366f1; 
            box-shadow: 0 0 8px rgba(99, 102, 241, 0.7);
        }
        .status-dot.completed { background-color: #10b981; }

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

        /* Refined Custom Badges */
        .badge-priority-tinggi {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
            font-weight: 600;
        }

        .badge-priority-sedang {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
            font-weight: 600;
        }

        .badge-priority-rendah {
            background-color: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            font-weight: 600;
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

    <!-- Sidebar (Linear Obsidian Theme) -->
    <aside class="sidebar p-3" id="mainSidebar">
        <!-- Brand Header -->
        <div class="d-flex align-items-center justify-content-between pb-3 mb-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 0 16px rgba(99, 102, 241, 0.45);">
                    <i class="bi bi-check2-all fs-5 text-white"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-white tracking-tight" style="letter-spacing: -0.02em;">Jara<span style="color: #818cf8;">.</span></h5>
                    <small style="color: #64748b; font-size: 0.7rem; font-weight: 500;">Task & Kolaborasi Tim</small>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-secondary border-0 text-muted d-lg-none" id="closeSidebarBtn">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Current Persona / Profile Card -->
        <div class="rounded-3 p-2 mb-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.07);">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-circle avatar-lg" style="background-color: {{ $currentUser['color'] ?? '#6366f1' }};">
                    {{ $currentUser['avatar'] ?? 'ME' }}
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="fw-semibold text-truncate small text-white">{{ $currentUser['name'] ?? 'Menza' }}</div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.35); font-size: 0.62rem;">
                            {{ $currentUser['id'] === 1 ? 'Pemilik Task' : ($currentUser['role'] === 'admin' ? 'Admin' : 'User') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-grow-1 overflow-auto pe-1">
            <div class="fw-bold mb-2" style="color: #475569; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase;">Tampilan Utama</div>
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

            <!-- Lists Section (SRS-F-03 - Tugas Novelya; SRS-F-17/F-18 - Joshua/Menza) -->
            <div class="d-flex align-items-center justify-content-between fw-bold mb-2" style="color: #475569; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase;">
                <span>Daftar / List</span>
                <button class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" style="color: #818cf8; font-size: 0.72rem;" data-bs-toggle="modal" data-bs-target="#createListModal" title="Buat List Baru (SRS-F-17)">
                    <i class="bi bi-plus-circle-fill"></i> Tambah
                </button>
            </div>
            <nav class="nav flex-column mb-3">
                @foreach($lists ?? [] as $list)
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <a class="nav-link-custom flex-grow-1 {{ ($currentListId ?? 1) == $list['id'] && !request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('tasks.index', ['list_id' => $list['id'], 'filter' => $currentFilter ?? 'all']) }}">
                            <i class="bi bi-folder2{{ ($currentListId ?? 1) == $list['id'] && !request()->routeIs('admin.*') ? '-open text-info' : '' }}"></i> 
                            <span class="text-truncate me-1">{{ $list['name'] }}</span>
                        </a>
                        <div class="d-flex align-items-center gap-1 ms-1">
                            @if(!empty($list['is_owner']))
                                <span class="badge" style="background: rgba(99, 102, 241, 0.25); color: #c7d2fe; border: 1px solid rgba(99, 102, 241, 0.4); font-size: 0.62rem;" title="Anda adalah Pemilik List ini (SRS-F-17)">
                                    <i class="bi bi-star-fill text-warning me-1"></i>Owner
                                </span>
                            @else
                                <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.62rem;" title="List dibagikan kepada Anda">
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
            <div class="fw-bold mb-2" style="color: #475569; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase;">Panel Admin (Iza)</div>
            <nav class="nav flex-column mb-3">
                <a class="nav-link-custom {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                    <i class="bi bi-people-fill"></i> Kelola Pengguna
                </a>
                <a class="nav-link-custom {{ request()->routeIs('admin.logs') ? 'active' : '' }}" href="{{ route('admin.logs') }}">
                    <i class="bi bi-journal-text"></i> Log Aktivitas Admin
                </a>
            </nav>

            <!-- Team Collaboration Info -->
            <div class="fw-bold mb-2" style="color: #475569; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase;">Tim Terhubung</div>
            <div class="p-2 rounded-3 mb-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06); font-size: 0.78rem;">
                <div class="fw-semibold text-truncate mb-1 text-white"><i class="bi bi-shield-check text-info me-1"></i> Tim Praktikum PPK</div>
                <div style="color: #64748b; font-size: 0.72rem;">Menza, Budi, Siti, Joshua, Novelya, Iza</div>
            </div>
        </div>

        <!-- Footer / Reset Data -->
        <div class="pt-3 mt-auto" style="border-top: 1px solid rgba(255, 255, 255, 0.08);">
            <form action="{{ route('reset-data') }}" method="POST" onsubmit="return confirm('Reset semua data sampel ke awal?')">
                @csrf
                <button type="submit" class="btn btn-sm w-100 py-1" style="background: transparent; border: 1px solid rgba(255, 255, 255, 0.1); color: #94a3b8; font-size: 0.75rem; transition: all 0.2s;">
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
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                        @yield('header_title', 'Manajemen Task')
                    </h4>
                    <p class="text-muted small mb-0">
                        @yield('header_subtitle', 'Pantau progres task, prioritas, dan kolaborator tim')
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Tambah Task CTA (Linear Glow Button) -->
                <button class="btn btn-sm d-flex align-items-center gap-1 px-3 py-1 text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#createTaskModal" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-sm-inline">Task Baru</span>
                </button>

                <!-- Persona Switcher Dropdown (Fitur Demo Praktikum) -->
                <div class="dropdown">
                    <button class="btn btn-sm d-flex align-items-center gap-2 shadow-sm" type="button" data-bs-toggle="dropdown" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 500;">
                        <i class="bi bi-person-bounding-box text-primary"></i>
                        <span class="d-none d-md-inline text-muted small">Lihat:</span>
                        <strong class="text-dark small">{{ $currentUser['name'] }}</strong>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1" style="min-width: 250px; border-radius: 10px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;">
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
                    <button class="btn btn-sm position-relative rounded-3 p-2 shadow-sm" type="button" data-bs-toggle="dropdown" style="background: #ffffff; border: 1px solid #e2e8f0;">
                        <i class="bi bi-bell text-secondary fs-6"></i>
                        @if(count($notifications ?? []) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Notifikasi Baru</span>
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2 mt-1" style="width: 320px; border-radius: 10px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;">
                        <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom mb-2">
                            <span class="fw-semibold small text-dark">Notifikasi Kolaborator</span>
                            <span class="badge" style="background: #e0e7ff; color: #4338ca; font-size: 0.65rem;">{{ count($notifications ?? []) }} Baru</span>
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

