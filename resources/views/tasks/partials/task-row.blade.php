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
    
    $prioritySlug = 'rendah';
    if ($task['priority'] === 'Tinggi') $prioritySlug = 'tinggi';
    if ($task['priority'] === 'Sedang') $prioritySlug = 'sedang';
@endphp

<div class="task-item-row d-flex align-items-center justify-content-between gap-3 {{ $isCompleted ? 'completed' : '' }} priority-{{ $prioritySlug }}"
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
                            class="btn btn-sm p-0 border-0 text-decoration-none d-flex align-items-center justify-content-center" 
                            title="{{ $isCompleted ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}"
                            style="color: {{ $isCompleted ? '#10b981' : '#cbd5e1' }}; font-size: 1.25rem; line-height: 1; transition: color 0.15s ease;">
                        <i class="bi {{ $isCompleted ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                    </button>
                </form>
            @else
                <button type="button" 
                        class="btn btn-sm p-0 border-0 text-decoration-none d-flex align-items-center justify-content-center" 
                        disabled
                        title="Hanya Pemilik Task atau Editor yang dapat mengubah status (Viewer: Read-Only)"
                        style="color: {{ $isCompleted ? '#10b981' : '#cbd5e1' }}; font-size: 1.25rem; line-height: 1; cursor: not-allowed; opacity: 0.5;">
                    <i class="bi {{ $isCompleted ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                </button>
            @endif
        </div>

        <!-- Title & Subtitle -->
        <div class="overflow-hidden flex-grow-1">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="task-title fw-semibold text-truncate text-dark" style="font-size: 0.88rem;">
                    {{ $task['title'] }}
                </span>

                <!-- Owner vs Collaborator Badges -->
                @if($isOwner)
                    <span class="badge" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; border: 1px solid rgba(99, 102, 241, 0.2); font-size: 0.63rem; padding: 0.2rem 0.45rem;">
                        <i class="bi bi-star-fill text-warning me-1"></i>Milik Anda
                    </span>
                @elseif($myCollabRole)
                    <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: #0891b2; border: 1px solid rgba(6, 182, 212, 0.2); font-size: 0.63rem; padding: 0.2rem 0.45rem;">
                        <i class="bi bi-people-fill me-1"></i>{{ ucfirst($myCollabRole) }}
                    </span>
                @endif
            </div>

            @if(!empty($task['description']))
                <div class="text-muted text-truncate mt-1" style="font-size: 0.74rem;">
                    {{ $task['description'] }}
                </div>
            @endif
        </div>
    </div>

    <!-- Right Section: Badges, Avatars, Deadline, Activities -->
    <div class="d-flex align-items-center gap-3 flex-shrink-0">
        <!-- Priority Badge (Linear Pill) -->
        @php
            $prioClass = 'badge-priority-rendah';
            if ($task['priority'] === 'Tinggi') $prioClass = 'badge-priority-tinggi';
            if ($task['priority'] === 'Sedang') $prioClass = 'badge-priority-sedang';
        @endphp
        <span class="badge {{ $prioClass }} d-none d-sm-inline rounded-pill px-2" style="font-size: 0.68rem;">
            {{ $task['priority'] }}
        </span>

        <!-- Deadline Indicator -->
        <span class="small d-none d-md-inline {{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}" style="font-size: 0.75rem;">
            <i class="bi {{ $isOverdue ? 'bi-exclamation-triangle-fill text-danger' : 'bi-calendar3' }} me-1"></i>
            {{ date('d M', strtotime($task['deadline'])) }}
        </span>

        <!-- Collaborators Avatar Stack (MENZA SCOPE: SRS-F-08 & SRS-F-09) -->
        <div class="d-none d-sm-flex align-items-center" onclick="event.stopPropagation();">
            @include('tasks.partials.avatar-stack', ['task' => $task])
        </div>

        <!-- Activity Count Badge (MENZA SCOPE: SRS-F-11) -->
        <span class="badge d-flex align-items-center gap-1 rounded-pill" 
              style="font-size: 0.68rem; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;" 
              title="{{ count($task['activities'] ?? []) }} Riwayat Aktivitas & Progres">
            <i class="bi bi-clock-history"></i>
            <span>{{ count($task['activities'] ?? []) }}</span>
        </span>

        <!-- Hover Action CTA & Chevron -->
        <div class="d-flex align-items-center gap-1">
            <div class="task-actions d-none d-md-block">
                <span class="btn btn-sm btn-light border py-0 px-2 text-primary fw-medium" style="font-size: 0.72rem; border-radius: 6px;">
                    Detail <i class="bi bi-arrow-right small"></i>
                </span>
            </div>
            <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
        </div>
    </div>
</div>
