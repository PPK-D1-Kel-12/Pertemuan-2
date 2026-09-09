@if($activeTask)
<div class="offcanvas offcanvas-end show shadow" tabindex="-1" id="taskDetailOffcanvas" style="width: 600px; max-width: 95vw;" data-bs-backdrop="true">
    <div class="offcanvas-header border-bottom bg-light py-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.7rem;">
                TASK-{{ $activeTask['id'] }}
            </span>
            <span class="text-muted small">|</span>
            <!-- Pindah List Dropdown (SRS-F-04 - Novelya) -->
            <form action="{{ route('tasks.move-list', $activeTask['id']) }}" method="POST" class="d-inline-flex align-items-center gap-1">
                @csrf
                <i class="bi bi-folder2-open text-primary" style="font-size: 0.85rem;"></i>
                <select name="list_id" class="form-select form-select-sm py-0 px-2 border-0 bg-white shadow-none text-dark fw-semibold" style="font-size: 0.75rem; width: auto;" onchange="this.form.submit()" title="Pindah List (SRS-F-04)">
                    @foreach($lists as $l)
                        <option value="{{ $l['id'] }}" {{ $l['id'] === $activeTask['list_id'] ? 'selected' : '' }}>
                            {{ $l['name'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(($activeTask['owner_id'] ?? 0) === ($currentUser['id'] ?? 0))
                <!-- Tombol Hapus Task (SRS-F-02 - Novelya) -->
                <form action="{{ route('tasks.destroy', $activeTask['id']) }}" method="POST" onsubmit="return confirm('Hapus task ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.75rem;" title="Hapus Task">
                        <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                </form>
            @endif
            <a href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => $currentFilter]) }}" class="btn-close" aria-label="Close"></a>
        </div>
    </div>

    <div class="offcanvas-body p-4">
        <!-- Status & Priority Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
            @php
                $canChangeStatus = ($activeTask['owner_id'] ?? 0) === ($currentUser['id'] ?? 0);
                if (!$canChangeStatus) {
                    foreach ($activeTask['collaborators'] ?? [] as $c) {
                        if ($c['user_id'] === ($currentUser['id'] ?? 0) && $c['role'] === 'editor') {
                            $canChangeStatus = true;
                            break;
                        }
                    }
                }
            @endphp

            <!-- Status Selector (SRS-F-07 - Joshua) -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold" style="font-size: 0.78rem;">Status:</span>
                @if($canChangeStatus)
                    <form action="{{ route('tasks.update-status', $activeTask['id']) }}" method="POST" class="d-inline">
                        @csrf
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="submit" name="status" value="To Do" class="btn {{ $activeTask['status'] === 'To Do' ? 'btn-secondary text-white' : 'btn-outline-secondary' }}">
                                To Do
                            </button>
                            <button type="submit" name="status" value="In Progress" class="btn {{ $activeTask['status'] === 'In Progress' ? 'btn-primary text-white' : 'btn-outline-primary' }}">
                                In Progress
                            </button>
                            <button type="submit" name="status" value="Selesai" class="btn {{ $activeTask['status'] === 'Selesai' ? 'btn-success text-white' : 'btn-outline-success' }}">
                                <i class="bi bi-check2"></i> Selesai
                            </button>
                        </div>
                    </form>
                @else
                    <span class="badge {{ $activeTask['status'] === 'Selesai' ? 'bg-success' : ($activeTask['status'] === 'In Progress' ? 'bg-primary' : 'bg-secondary') }}">
                        {{ $activeTask['status'] }}
                    </span>
                    <small class="text-muted" style="font-size: 0.7rem;">(Viewer: Read-only)</small>
                @endif
            </div>

            <!-- Priority Badge (SRS-F-05 - Joshua) -->
            <div class="d-flex align-items-center gap-2">
                @php
                    $prioClass = 'badge-priority-sedang';
                    if ($activeTask['priority'] === 'Tinggi') $prioClass = 'badge-priority-tinggi';
                    if ($activeTask['priority'] === 'Rendah') $prioClass = 'badge-priority-rendah';
                @endphp
                <span class="badge {{ $prioClass }}" style="font-size: 0.75rem;">
                    <i class="bi bi-flag-fill me-1"></i> Prioritas {{ $activeTask['priority'] }}
                </span>
            </div>
        </div>

        <!-- Task Title & Edit Toggle -->
        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <h5 class="fw-bold mb-0 text-dark">{{ $activeTask['title'] }}</h5>
            @if(($activeTask['owner_id'] ?? 0) === ($currentUser['id'] ?? 0))
                <button class="btn btn-link btn-sm p-0 text-primary text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#editTaskCollapse" aria-expanded="false">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </button>
            @endif
        </div>

        <!-- Form Edit Task (SRS-F-02 - Novelya & Joshua) -->
        @if(($activeTask['owner_id'] ?? 0) === ($currentUser['id'] ?? 0))
            <div class="collapse mb-3" id="editTaskCollapse">
                <div class="card card-body bg-light border p-3 rounded-3">
                    <h6 class="fw-bold small mb-2"><i class="bi bi-pencil me-1"></i> Edit Informasi Task (SRS-F-02)</h6>
                    <form action="{{ route('tasks.update', $activeTask['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="list_id" value="{{ $activeTask['list_id'] }}">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold mb-1">Judul Task</label>
                            <input type="text" name="title" class="form-control form-control-sm" value="{{ $activeTask['title'] }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold mb-1">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-sm" rows="2">{{ $activeTask['description'] }}</textarea>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold mb-1">Prioritas (SRS-F-05)</label>
                                <select name="priority" class="form-select form-select-sm" required>
                                    <option value="Tinggi" {{ $activeTask['priority'] === 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                    <option value="Sedang" {{ $activeTask['priority'] === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="Rendah" {{ $activeTask['priority'] === 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold mb-1">Deadline (SRS-F-06)</label>
                                <input type="date" name="deadline" class="form-control form-control-sm" value="{{ $activeTask['deadline'] }}" required>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Task Description Display -->
        <div class="p-3 bg-light-subtle rounded-3 border mb-4">
            <div class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.65rem;">Deskripsi Task</div>
            <p class="mb-0 text-secondary" style="font-size: 0.88rem; line-height: 1.6;">
                {{ $activeTask['description'] ?: 'Tidak ada deskripsi rinci untuk task ini.' }}
            </p>
        </div>

        <!-- Meta Info (Deadline & Pembuat) -->
        <div class="row g-2 mb-4">
            <div class="col-6">
                <div class="p-2 border rounded-2 bg-white">
                    <div class="text-muted" style="font-size: 0.68rem;">Deadline (Batas Waktu)</div>
                    <div class="fw-semibold small text-dark mt-1">
                        <i class="bi bi-calendar-event text-danger me-1"></i>
                        {{ date('d F Y', strtotime($activeTask['deadline'])) }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 border rounded-2 bg-white">
                    <div class="text-muted" style="font-size: 0.68rem;">Dibuat Oleh</div>
                    <div class="fw-semibold small text-dark mt-1 text-truncate">
                        <i class="bi bi-person-fill text-primary me-1"></i>
                        {{ $activeTask['owner']['name'] ?? 'Menza' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 1 (TUGAS INTI MENZA): Manajemen Kolaborator Task (SRS-F-08 & SRS-F-09) -->
        @include('tasks.partials.collaborators-section', ['task' => $activeTask])

        <!-- SECTION 2 (TUGAS INTI MENZA): Pemantauan Progres & Linimasa Kronologis (SRS-F-11) -->
        @include('tasks.partials.activity-timeline', ['task' => $activeTask])

    </div>
</div>
<!-- Backdrop for active offcanvas -->
<div class="offcanvas-backdrop fade show"></div>
@endif
