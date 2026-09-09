@props(['task'])

<div class="avatar-group" title="Kolaborator pada task ini">
    @if(isset($task['owner']))
        <div class="avatar-circle" 
             style="background-color: {{ $task['owner']['color'] ?? '#4f46e5' }};" 
             data-bs-toggle="tooltip" 
             data-bs-title="Pemilik Task: {{ $task['owner']['name'] }}">
            {{ $task['owner']['avatar'] ?? 'OW' }}
        </div>
    @endif

    @foreach(array_slice($task['collaborators'] ?? [], 0, 3) as $collab)
        <div class="avatar-circle" 
             style="background-color: {{ $collab['user']['color'] ?? '#0ea5e9' }};" 
             data-bs-toggle="tooltip" 
             data-bs-title="{{ $collab['user']['name'] }} ({{ ucfirst($collab['role']) }})">
            {{ $collab['user']['avatar'] ?? 'U' }}
        </div>
    @endforeach

    @if(count($task['collaborators'] ?? []) > 3)
        <div class="avatar-circle bg-secondary" 
             data-bs-toggle="tooltip" 
             data-bs-title="+{{ count($task['collaborators']) - 3 }} Kolaborator Lainnya">
            +{{ count($task['collaborators']) - 3 }}
        </div>
    @endif
</div>

