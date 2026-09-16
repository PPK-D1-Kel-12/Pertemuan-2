<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Akses Ditolak — Jara</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .error-card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            background-color: #fee2e2;
            color: #ef4444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    @php
        $mockService = app(\App\Services\MockDataService::class);
        $currentUser = $mockService->getCurrentUser();
        $users = $mockService->getUsers();
    @endphp

    <div class="error-card p-4 p-sm-5 text-center">
        <!-- Error Icon & Status -->
        <div class="icon-circle">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        
        <div>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 mb-2 fw-bold" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                ERROR 403 • FORBIDDEN
            </span>
        </div>

        <h3 class="fw-bold text-dark mb-2">Akses Ditolak</h3>
        
        <p class="text-secondary small mb-3">
            {{ $exception->getMessage() ?: 'Sistem mendeteksi bahwa Anda tidak memiliki hak akses atau wewenang kepemilikan yang sah untuk melakukan operasi ini.' }}
        </p>

        <div class="p-3 bg-light border rounded-3 text-start mb-4" style="font-size: 0.8rem;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="text-muted">Status Akun Anda:</span>
                <span class="badge {{ ($currentUser['role'] ?? '') === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                    {{ strtoupper($currentUser['role'] ?? 'USER') }}
                </span>
            </div>
            <div class="fw-semibold text-dark mb-1">
                <i class="bi bi-person-circle text-primary me-1"></i> {{ $currentUser['name'] ?? 'Pengguna' }} ({{ $currentUser['email'] ?? '-' }})
            </div>
            <div class="text-muted" style="font-size: 0.72rem;">
                <i class="bi bi-info-circle me-1"></i> <strong>Kebijakan Otorisasi (SRS-F-19):</strong> Modifikasi dan penghapusan list atau task dibatasi hanya untuk Pemilik Sah (Owner) atau Admin sistem.
            </div>
        </div>

        <!-- Quick Switch Persona for Demo -->
        <div class="mb-4">
            <div class="text-muted small fw-semibold mb-2" style="font-size: 0.75rem;">
                Coba Uji dengan Persona Pemilik / Admin:
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-1">
                @foreach($users as $u)
                    <a href="{{ route('switch-user', $u['id']) }}" class="btn btn-outline-secondary btn-sm py-1 px-2 text-decoration-none" style="font-size: 0.72rem;">
                        <span class="badge rounded-circle p-1 me-1" style="background-color: {{ $u['color'] }}; color: transparent;">•</span>
                        {{ $u['name'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-grid gap-2">
            <a href="{{ route('tasks.index') }}" class="btn btn-primary fw-semibold py-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard Task
            </a>
        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
