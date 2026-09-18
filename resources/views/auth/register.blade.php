@extends('layouts.auth')

@section('title', 'Daftar Akun Baru — Jara')

@section('content')
<form action="{{ route('register.submit') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
            <input type="text" name="name" class="form-control border-start-0" placeholder="Nama Anda" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Alamat Email <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control border-start-0" placeholder="nama@email.com" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control border-start-0" placeholder="Minimal 4 karakter" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold">Konfirmasi Password</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-check"></i></span>
            <input type="password" name="password_confirmation" class="form-control border-start-0" placeholder="Ulangi password" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
        Daftar Akun Sekarang
    </button>

    <div class="text-center">
        <span class="text-muted small">Sudah punya akun?</span>
        <a href="{{ route('login') }}" class="small fw-semibold text-primary text-decoration-none ms-1">Masuk ke Akun</a>
    </div>
</form>
@endsection

