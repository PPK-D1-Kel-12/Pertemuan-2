@php
    $isCompleted = ($task['status'] === 'Selesai');
    $isOwner = ($task['owner_id'] === $currentUser['id']) || (($currentUser['role'] ?? '') === 'admin');
    
    $myCollabRole = null;
    foreach ($task['collaborators'] as $c) {
        if ($c['user_id'] === $currentUser['id']) {
            $myCollabRole = $c['role'];
            break;
        }
    }
    
    $canToggleStatus = $isOwner || ($myCollabRole === 'editor');
    $isOverdue = (strtotime($task['deadline']) < time() && !$isCompleted);
@endphp

<div class="task-item-row d-flex align-items-center justify-content-between gap-3 {{ $isCompleted ? 'completed' : '' }}"
     onclick="window.location='{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => $currentFilter, 'open_task' => $task['id']]) }}'">
    
    <!-- Left Section: Checkbox & Title -->
    <div class="d-flex align-items-center gap-3 overflow-hidden flex-grow-1">
        <!-- Quick Status Toggle Checkbox -->
        <div onclick="event.stopPropagation();">
            @if($canToggleStatus)
                <form action="{{ route('tasks.update-status', $task['id']) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="{{ $isCompleted ? 'To Do' : 'Selesai' }}">
                    <button type="submit" 
                            class="btn btn-sm p-0 border-0 text-decoration-none" 
                            title="{{ $isCompleted ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}"
                            style="color: {{ $isCompleted ? '#10b981' : '#cbd5e1' }}; font-size: 1.25rem; line-height: 1;">
                        <i class="bi {{ $isCompleted ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                    </button>
                </form>
            @else
                <button type="button" 
                        class="btn btn-sm p-0 border-0 text-decoration-none" 
                        disabled
                        title="Hanya Pemilik Task atau Editor yang dapat mengubah status (Viewer: Read-Only)"
                        style="color: {{ $isCompleted ? '#10b981' : '#cbd5e1' }}; font-size: 1.25rem; line-height: 1; cursor: not-allowed; opacity: 0.6;">
                    <i class="bi {{ $isCompleted ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                </button>
            @endif
        </div>

        <!-- Title & Subtitle -->
        <div class="overflow-hidden">
            <div class="d-flex align-items-center gap-2">
                <span class="task-title fw-semibold small text-truncate text-dark">
                    {{ $task['title'] }}
                </span>

                <!-- Owner vs Collaborator Badges -->
                @if($isOwner)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">
                        <i class="bi bi-person-fill"></i> Milik Anda
                    </span>
                @elseif($myCollabRole)
                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.65rem;">
                        <i class="bi bi-people-fill"></i> Anda ({{ ucfirst($myCollabRole) }})
                    </span>
                @endif
            </div>

            @if(!empty($task['description']))
                <div class="text-muted text-truncate" style="font-size: 0.75rem;">
                    {{ $task['description'] }}
                </div>
            @endif
        </div>
    </div>

    <!-- Right Section: Badges, Avatars, Deadline, Activities -->
    <div class="d-flex align-items-center gap-3 flex-shrink-0">
        <!-- Priority Badge (Joshua Scope) -->
        @php
            $prioClass = 'badge-priority-sedang';
            if ($task['priority'] === 'Tinggi') $prioClass = 'badge-priority-tinggi';
            if ($task['priority'] === 'Rendah') $prioClass = 'badge-priority-rendah';
        @endphp
        <span class="badge {{ $prioClass }} d-none d-sm-inline" style="font-size: 0.7rem;">
            {{ $task['priority'] }}
        </span>

        <!-- Deadline (Joshua Scope) -->
        <span class="small d-none d-md-inline {{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}" style="font-size: 0.75rem;">
            <i class="bi {{ $isOverdue ? 'bi-exclamation-circle-fill text-danger' : 'bi-calendar3' }} me-1"></i>
            {{ date('d M', strtotime($task['deadline'])) }}
        </span>

        <!-- Collaborators Avatar Stack (MENZA SCOPE: SRS-F-08 & SRS-F-09) -->
        <div class="d-none d-sm-flex align-items-center" onclick="event.stopPropagation();">
            @include('tasks.partials.avatar-stack', ['task' => $task])
        </div>

        <!-- Activity Count Badge (MENZA SCOPE: SRS-F-11) -->
        <span class="badge bg-light text-secondary border d-flex align-items-center gap-1" 
              style="font-size: 0.7rem;" 
              title="{{ count($task['activities'] ?? []) }} Riwayat Aktivitas & Progres">
            <i class="bi bi-clock-history"></i>
            <span>{{ count($task['activities'] ?? []) }}</span>
        </span>

        <!-- Chevron Action -->
        <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
    </div>
</div>

