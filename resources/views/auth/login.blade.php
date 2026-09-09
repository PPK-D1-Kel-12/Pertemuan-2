@extends('layouts.auth')

@section('title', 'Masuk — Jara')

@section('content')
<form action="{{ route('login.submit') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label small fw-semibold">Email Pengguna</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" id="emailInput" class="form-control border-start-0" placeholder="nama@jara.test" value="menza@jara.test" required>
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label small fw-semibold mb-0">Password</label>
            <span class="text-muted" style="font-size: 0.75rem;">(Bebas untuk mode mock)</span>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control border-start-0" value="password" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
        Masuk ke Jara
    </button>

    <div class="text-center mb-4">
        <span class="text-muted small">Belum punya akun?</span>
        <a href="{{ route('register') }}" class="small fw-semibold text-primary text-decoration-none ms-1">Daftar Akun Baru</a>
    </div>

    <!-- Quick Demo Login Presets -->
    <div class="pt-3 border-top">
        <div class="text-uppercase text-muted fw-bold text-center mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">
            Akses Cepat Demo Akun
        </div>
        <div class="d-grid gap-1">
            <button type="button" class="btn btn-outline-secondary btn-sm py-1 d-flex align-items-center justify-content-between" onclick="quickLogin('menza@jara.test')">
                <span class="small"><i class="bi bi-person-fill text-primary me-1"></i> Menza (Task Owner)</span>
                <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">Owner</span>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-1 d-flex align-items-center justify-content-between" onclick="quickLogin('budi@jara.test')">
                <span class="small"><i class="bi bi-person text-info me-1"></i> Budi Pratama (Kolaborator)</span>
                <span class="badge bg-info-subtle text-info" style="font-size: 0.65rem;">Editor</span>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-1 d-flex align-items-center justify-content-between" onclick="quickLogin('siti@jara.test')">
                <span class="small"><i class="bi bi-person text-secondary me-1"></i> Siti Rahma (Kolaborator)</span>
                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Viewer</span>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-1 d-flex align-items-center justify-content-between" onclick="quickLogin('iza.admin@jara.test')">
                <span class="small"><i class="bi bi-shield-lock-fill text-dark me-1"></i> Iza (Administrator)</span>
                <span class="badge bg-dark-subtle text-dark" style="font-size: 0.65rem;">Admin</span>
            </button>
        </div>
    </div>
</form>

<script>
    function quickLogin(email) {
        document.getElementById('emailInput').value = email;
        document.querySelector('form').submit();
    }
</script>
@endsection
