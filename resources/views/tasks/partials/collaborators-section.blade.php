@php
    $isOwner = (($task['owner_id'] ?? 0) === ($currentUser['id'] ?? 0)) || (($currentUser['role'] ?? '') === 'admin');
    $myCollabRecord = null;
    foreach ($task['collaborators'] ?? [] as $c) {
        if ($c['user_id'] === ($currentUser['id'] ?? 0)) {
            $myCollabRecord = $c;
            break;
        }
    }
    $isCollaborator = $myCollabRecord !== null;
    $myRole = $isOwner ? 'owner' : ($myCollabRecord['role'] ?? null);

    // Filter user yang belum jadi kolaborator dan bukan owner untuk dropdown tambah kolaborator
    $existingUserIds = array_merge([$task['owner_id']], array_column($task['collaborators'] ?? [], 'user_id'));
    $eligibleUsers = array_filter($users ?? [], fn($u) => !in_array($u['id'], $existingUserIds));
@endphp

<div class="card border rounded-3 mb-4 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-primary fs-5"></i>
            <div>
                <h6 class="mb-0 fw-bold">Kolaborator Task (SRS-F-08 & SRS-F-09)</h6>
                <small class="text-muted" style="font-size: 0.72rem;">Kelola kolaborator dan hak akses pada task ini</small>
            </div>
        </div>
        <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1" style="font-size: 0.75rem;">
            {{ count($task['collaborators'] ?? []) + 1 }} Anggota
        </span>
    </div>

    <div class="card-body p-3">
        <!-- Banner Hak Akses Pengguna Aktif -->
        <div class="p-2 mb-3 rounded-2 border d-flex align-items-center gap-2" 
             style="background-color: {{ $isOwner ? '#f0fdf4' : ($myRole === 'editor' ? '#eff6ff' : '#f8fafc') }};">
            <i class="bi {{ $isOwner ? 'bi-shield-check text-success' : ($myRole === 'editor' ? 'bi-pencil-square text-primary' : 'bi-eye text-secondary') }} fs-5"></i>
            <div style="font-size: 0.78rem;">
                @if($isOwner)
                    <strong class="text-success">Anda adalah Pemilik Task (Owner).</strong> Anda memiliki kontrol penuh untuk menambahkan dan menghapus kolaborator.
                @elseif($myRole === 'editor')
                    <strong class="text-primary">Anda adalah Kolaborator (Editor).</strong> Anda berhak memperbarui status task dan mengirim catatan progres.
                @elseif($myRole === 'viewer')
                    <strong class="text-secondary">Anda adalah Kolaborator (Viewer).</strong> Anda hanya memiliki hak untuk melihat task ini (Read-Only).
                @else
                    <span class="text-muted">Anda melihat task ini dalam mode pratinjau sistem.</span>
                @endif
            </div>
        </div>

        <!-- Form Tambah Kolaborator (Khusus Pemilik Task) -->
        @if($isOwner)
            <div class="p-3 bg-light rounded-3 mb-3 border">
                <label class="form-label small fw-bold mb-2 text-dark">
                    <i class="bi bi-person-plus-fill me-1 text-primary"></i> Tambah Kolaborator Baru
                </label>
                <form action="{{ route('tasks.collaborators.add', $task['id']) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <!-- Dropdown Pencarian/Pilihan Pengguna -->
                        <div class="col-md-7">
                            <select name="user_id" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Pilih Anggota Tim --</option>
                                @forelse($eligibleUsers as $user)
                                    <option value="{{ $user['id'] }}">
                                        {{ $user['name'] }} ({{ $user['email'] }})
                                    </option>
                                @empty
                                    <option disabled>Semua anggota tim sudah ditambahkan</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Dropdown Pilihan Peran (Viewer vs Editor) -->
                        <div class="col-md-5">
                            <select name="role" class="form-select form-select-sm" required>
                                <option value="editor" selected>Editor (Bisa Update Status)</option>
                                <option value="viewer">Viewer (Hanya Lihat)</option>
                            </select>
                        </div>

                        <div class="col-12 mt-2 text-end">
                            <button type="submit" class="btn btn-primary btn-sm px-3" {{ empty($eligibleUsers) ? 'disabled' : '' }}>
                                <i class="bi bi-plus-lg me-1"></i> Tambahkan ke Task
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Daftar Kolaborator Aktif -->
        <div class="list-group list-group-flush border rounded-3 overflow-hidden">
            <!-- Item Pemilik Task (Owner) -->
            <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-light-subtle">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-circle" style="background-color: {{ $task['owner']['color'] ?? '#4f46e5' }};">
                        {{ $task['owner']['avatar'] ?? 'OW' }}
                    </div>
                    <div>
                        <div class="fw-semibold small text-dark mb-0">
                            {{ $task['owner']['name'] ?? 'Owner' }}
                            @if($task['owner_id'] === $currentUser['id'])
                                <span class="text-muted fw-normal" style="font-size: 0.72rem;">(Anda)</span>
                            @endif
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $task['owner']['email'] ?? '' }}</small>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">
                    <i class="bi bi-star-fill me-1"></i> Pemilik (Owner)
                </span>
            </div>

            <!-- Item Setiap Kolaborator -->
            @forelse($task['collaborators'] ?? [] as $collab)
                <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-circle" style="background-color: {{ $collab['user']['color'] ?? '#0ea5e9' }};">
                            {{ $collab['user']['avatar'] ?? 'U' }}
                        </div>
                        <div>
                            <div class="fw-semibold small text-dark mb-0">
                                {{ $collab['user']['name'] ?? 'User' }}
                                @if($collab['user_id'] === $currentUser['id'])
                                    <span class="text-muted fw-normal" style="font-size: 0.72rem;">(Anda)</span>
                                @endif
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">
                                Ditambahkan: {{ date('d M, H:i', strtotime($collab['added_at'] ?? 'now')) }}
                            </small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Badge Peran (Editor / Viewer) -->
                        @if($collab['role'] === 'editor')
                            <span class="badge badge-role-editor">
                                <i class="bi bi-pencil-fill me-1"></i> Editor
                            </span>
                        @else
                            <span class="badge badge-role-viewer">
                                <i class="bi bi-eye-fill me-1"></i> Viewer
                            </span>
                        @endif

                        <!-- Aksi Hapus bagi Owner ATAU Keluar bagi Kolaborator -->
                        @if($isOwner)
                            <form action="{{ route('tasks.collaborators.remove', [$task['id'], $collab['user_id']]) }}" method="POST" onsubmit="return confirm('Keluarkan {{ $collab['user']['name'] }} dari task ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm p-1 rounded-2" style="font-size: 0.75rem;" title="Keluarkan Kolaborator">
                                    <i class="bi bi-person-x"></i>
                                </button>
                            </form>
                        @elseif($collab['user_id'] === $currentUser['id'])
                            <form action="{{ route('tasks.collaborators.remove', [$task['id'], $collab['user_id']]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari task ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.7rem;">
                                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-3 text-center text-muted small bg-white">
                    <i class="bi bi-people d-block fs-4 text-secondary mb-1"></i>
                    Belum ada kolaborator yang ditambahkan ke task ini.
                </div>
            @endforelse
        </div>
    </div>
</div>

