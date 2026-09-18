@extends('layouts.app')

@section('title', 'Jara — Kelola Pengguna (Admin)')

@section('header_title', 'Panel Kelola Pengguna (SRS-F-14 & SRS-F-15)')
@section('header_subtitle', 'Kelola akun pengguna dalam sistem Jara (Tugas Iza - Admin)')

@section('content')
<div class="container-fluid px-0">

    <!-- Admin Sub-Navigation -->
    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
        <ul class="nav nav-pills gap-1">
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 active" href="{{ route('admin.users') }}">
                    <i class="bi bi-people-fill me-1"></i> Daftar Pengguna ({{ count($users) }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 text-secondary" href="{{ route('admin.logs') }}">
                    <i class="bi bi-journal-text me-1"></i> Log Aktivitas Admin
                </a>
            </li>
        </ul>

        <button class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Total Pengguna</div>
                        <h4 class="fw-bold mb-0 mt-1">{{ count($users) }}</h4>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Pengguna Aktif</div>
                        @php $activeCount = count(array_filter($users, fn($u) => ($u['status'] ?? 'active') === 'active')); @endphp
                        <h4 class="fw-bold mb-0 mt-1 text-success">{{ $activeCount }}</h4>
                    </div>
                    <div class="bg-success-subtle text-success rounded-3 p-2">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Dinonaktifkan</div>
                        <h4 class="fw-bold mb-0 mt-1 text-danger">{{ count($users) - $activeCount }}</h4>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-3 p-2">
                        <i class="bi bi-person-slash fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border rounded-3 p-3 bg-white shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Role Administrator</div>
                        @php $adminCount = count(array_filter($users, fn($u) => ($u['role'] ?? '') === 'admin')); @endphp
                        <h4 class="fw-bold mb-0 mt-1 text-dark">{{ $adminCount }}</h4>
                    </div>
                    <div class="bg-dark-subtle text-dark rounded-3 p-2">
                        <i class="bi bi-shield-lock fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border rounded-3 shadow-sm bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">Daftar Akun Pengguna Terdaftar</h6>
            <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Admin hanya mengelola akun, tidak mengelola isi task pengguna</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr style="font-size: 0.78rem;">
                        <th class="ps-3">Pengguna</th>
                        <th>Alamat Email</th>
                        <th>Role Sistem</th>
                        <th>Status Akun</th>
                        <th>Terdaftar Sejak</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php $isActive = ($user['status'] ?? 'active') === 'active'; @endphp
                        <tr style="font-size: 0.85rem;">
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle" style="background-color: {{ $user['color'] ?? '#4f46e5' }};">
                                        {{ $user['avatar'] ?? 'U' }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $user['name'] }}</div>
                                        @if($user['id'] === $currentUser['id'])
                                            <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user['email'] }}</td>
                            <td>
                                @if(($user['role'] ?? '') === 'admin')
                                    <span class="badge bg-dark-subtle text-dark border"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>
                                @else
                                    <span class="badge bg-light text-secondary border"><i class="bi bi-person me-1"></i> User</span>
                                @endif
                            </td>
                            <td>
                                @if($isActive)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-dot"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="bi bi-dot"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ date('d M Y', strtotime($user['created_at'] ?? 'now')) }}</td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <!-- Toggle Status Button (SRS-F-15) -->
                                    <form action="{{ route('admin.users.toggle-status', $user['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $isActive ? 'btn-outline-warning' : 'btn-outline-success' }} py-0 px-2" 
                                                style="font-size: 0.75rem;" 
                                                title="{{ $isActive ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                {{ $user['id'] === $currentUser['id'] ? 'disabled' : '' }}>
                                            <i class="bi {{ $isActive ? 'bi-pause-circle' : 'bi-play-circle' }} me-1"></i>
                                            {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <!-- Delete Button (SRS-F-15) -->
                                    <form action="{{ route('admin.users.destroy', $user['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen akun {{ $user['name'] }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger py-0 px-2" 
                                                style="font-size: 0.75rem;" 
                                                title="Hapus Akun"
                                                {{ $user['id'] === $currentUser['id'] ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun Baru oleh Admin (SRS-F-14) -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold" id="createUserModalLabel">
                    <i class="bi bi-person-plus-fill text-primary me-1"></i> Tambah Pengguna Baru (Admin)
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: Rian Hidayat" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="rian@jara.test" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Role Pengguna</label>
                        <select name="role" class="form-select form-select-sm" required>
                            <option value="user" selected>User (Pengguna Reguler)</option>
                            <option value="admin">Admin (Pengelola Sistem)</option>
                        </select>
                    </div>
                    <div class="p-2 bg-light rounded-2 border small text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-check text-primary me-1"></i> Aksi ini akan otomatis dicatat ke dalam <strong>Log Aktivitas Admin (SRS-F-16)</strong>.
                    </div>
                </div>
                <div class="modal-footer border-top py-2 bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

