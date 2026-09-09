@php
    $canAddNote = ($task['owner_id'] ?? 0) === ($currentUser['id'] ?? 0);
    if (!$canAddNote) {
        foreach ($task['collaborators'] ?? [] as $c) {
            if ($c['user_id'] === ($currentUser['id'] ?? 0) && $c['role'] === 'editor') {
                $canAddNote = true;
                break;
            }
        }
    }
@endphp

<div class="card border rounded-3 mb-4 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary fs-5"></i>
            <div>
                <h6 class="mb-0 fw-bold">Pemantauan Progres & Linimasa (SRS-F-11)</h6>
                <small class="text-muted" style="font-size: 0.72rem;">Catatan kronologis perubahan status dan update pekerjaan</small>
            </div>
        </div>
        <span class="badge bg-secondary-subtle text-secondary fw-semibold px-2 py-1" style="font-size: 0.75rem;">
            {{ count($task['activities'] ?? []) }} Aktivitas
        </span>
    </div>

    <div class="card-body p-3">
        <!-- Input Catatan Progres / Update Singkat -->
        @if($canAddNote)
            <form action="{{ route('tasks.progress-notes.add', $task['id']) }}" method="POST" class="mb-4">
                @csrf
                <div class="input-group">
                    <input type="text" 
                           name="note" 
                           class="form-control form-control-sm" 
                           placeholder="Ketik catatan progres atau update pekerjaan..." 
                           required 
                           autocomplete="off">
                    <button type="submit" class="btn btn-outline-primary btn-sm px-3">
                        <i class="bi bi-send-fill me-1"></i> Kirim Update
                    </button>
                </div>
                <div class="form-text mt-1" style="font-size: 0.7rem;">
                    <i class="bi bi-info-circle me-1"></i> Pemilik task dan editor dapat menambahkan catatan progres langsung ke linimasa.
                </div>
            </form>
        @else
            <div class="alert alert-light border py-2 px-3 mb-3 small text-muted" style="font-size: 0.75rem;">
                <i class="bi bi-lock-fill me-1"></i> Mode hanya lihat (Viewer). Hanya pemilik task atau editor yang dapat mengirim update progres.
            </div>
        @endif

        <!-- Linimasa Vertikal Kronologis -->
        <div class="timeline">
            @forelse(array_reverse($task['activities'] ?? []) as $activity)
                @php
                    $iconClass = 'bi-circle';
                    $badgeType = '';
                    $bgColor = '#ffffff';

                    switch ($activity['type'] ?? '') {
                        case 'created':
                            $iconClass = 'bi-plus-circle';
                            $badgeType = 'collab';
                            break;
                        case 'collab_add':
                            $iconClass = 'bi-person-plus-fill';
                            $badgeType = 'collab';
                            break;
                        case 'collab_remove':
                            $iconClass = 'bi-person-dash-fill';
                            $badgeType = 'status';
                            break;
                        case 'status_change':
                            $iconClass = 'bi-arrow-repeat';
                            $badgeType = 'status';
                            break;
                        case 'progress_note':
                            $iconClass = 'bi-chat-left-text-fill';
                            $badgeType = 'note';
                            break;
                    }
                @endphp

                <div class="timeline-item">
                    <div class="timeline-icon {{ $badgeType }}">
                        <i class="bi {{ $iconClass }}"></i>
                    </div>
                    <div class="bg-light p-2 rounded-3 border" style="font-size: 0.8rem;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-semibold text-dark">
                                {{ $activity['user']['name'] ?? 'Sistem' }}
                            </span>
                            <span class="timeline-date">
                                <i class="bi bi-clock me-1"></i>{{ date('d M Y, H:i', strtotime($activity['timestamp'] ?? 'now')) }}
                            </span>
                        </div>
                        <div class="text-secondary">
                            {{ $activity['description'] }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-3 small">
                    <i class="bi bi-hourglass-split fs-4 d-block mb-1"></i>
                    Belum ada riwayat aktivitas pada task ini.
                </div>
            @endforelse
        </div>
    </div>
</div>

