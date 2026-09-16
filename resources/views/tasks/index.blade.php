@extends('layouts.app')

@section('title', 'Jara — ' . ($lists[$currentListId]['name'] ?? 'Task List'))

@section('header_title')
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-folder2-open text-primary"></i>
        <span>{{ $lists[$currentListId]['name'] ?? 'Semua Task' }}</span>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 py-1 px-2" style="font-size: 0.75rem !important;">
            {{ count($tasks) }} Task
        </span>
    </div>
@endsection

@section('header_subtitle', 'Tampilan List ala Todoist & Notion dengan pemantauan kolaborator aktif')

@section('content')
<div class="container-fluid px-0">

    @if(empty($lists) || count($lists) === 0)
        <div class="card border-0 shadow-sm rounded-4 text-center p-5 my-4 bg-white">
            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                <i class="bi bi-folder-x fs-2"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Belum Ada List Tugas</h4>
            <p class="text-muted mx-auto" style="max-width: 450px;">
                Seluruh list telah dihapus atau belum dibuat. Silakan buat list baru untuk mulai mengelola task dan berkolaborasi bersama tim.
            </p>
            <div>
                <button class="btn btn-primary px-4 py-2 rounded-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createListModal">
                    <i class="bi bi-plus-circle me-1"></i> Buat List Baru
                </button>
            </div>
        </div>
    @else
    <!-- Quick Filter / Scope Tabs -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 pb-2 border-bottom">
        <ul class="nav nav-pills gap-1">
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 {{ $currentFilter === 'all' ? 'active' : 'text-secondary' }}" 
                   href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'all']) }}">
                    Semua ({{ count($tasks) }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 {{ $currentFilter === 'my_tasks' ? 'active' : 'text-secondary' }}" 
                   href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'my_tasks']) }}">
                    <i class="bi bi-star-fill text-warning me-1"></i> Dibuat oleh Saya
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 {{ $currentFilter === 'assigned_to_me' ? 'active' : 'text-secondary' }}" 
                   href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'assigned_to_me']) }}">
                    <i class="bi bi-people-fill text-info me-1"></i> Saya sebagai Kolaborator
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 {{ $currentFilter === 'high_priority' ? 'active' : 'text-secondary' }}" 
                   href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'high_priority']) }}">
                    <i class="bi bi-flag-fill text-danger me-1"></i> Prioritas Tinggi
                </a>
            </li>
        </ul>

        <div class="text-muted small">
            <i class="bi bi-info-circle me-1"></i> Klik baris task untuk membuka <strong>Detail, Kolaborator, & Linimasa Progres</strong>
        </div>
    </div>

    <!-- TASK SECTIONS (List/Table View ala Todoist/Notion) -->
    
    <!-- 1. SECTION: IN PROGRESS -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-uppercase text-primary mb-0 d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.05em;">
                <span class="badge bg-primary text-white rounded-pill px-2">
                    {{ count($tasksByStatus['In Progress']) }}
                </span>
                <span>Sedang Dikerjakan (In Progress)</span>
            </h6>
        </div>

        @forelse($tasksByStatus['In Progress'] as $task)
            @include('tasks.partials.task-row', ['task' => $task])
        @empty
            <div class="p-3 bg-white border rounded-3 text-center text-muted small mb-2">
                Tidak ada task yang sedang dikerjakan saat ini.
            </div>
        @endforelse
    </div>

    <!-- 2. SECTION: TO DO -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-uppercase text-secondary mb-0 d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.05em;">
                <span class="badge bg-secondary text-white rounded-pill px-2">
                    {{ count($tasksByStatus['To Do']) }}
                </span>
                <span>Belum Dikerjakan (To Do)</span>
            </h6>
        </div>

        @forelse($tasksByStatus['To Do'] as $task)
            @include('tasks.partials.task-row', ['task' => $task])
        @empty
            <div class="p-3 bg-white border rounded-3 text-center text-muted small mb-2">
                Tidak ada task to-do.
            </div>
        @endforelse
    </div>

    <!-- 3. SECTION: SELESAI -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-uppercase text-success mb-0 d-flex align-items-center gap-2" style="font-size: 0.78rem; letter-spacing: 0.05em;">
                <span class="badge bg-success text-white rounded-pill px-2">
                    {{ count($tasksByStatus['Selesai']) }}
                </span>
                <span>Selesai (Completed)</span>
            </h6>
        </div>

        @forelse($tasksByStatus['Selesai'] as $task)
            @include('tasks.partials.task-row', ['task' => $task])
        @empty
            <div class="p-3 bg-white border rounded-3 text-center text-muted small mb-2">
                Belum ada task yang diselesaikan.
            </div>
        @endforelse
    </div>
    @endif

</div>

<!-- MODAL TAMBAH TASK -->
@include('tasks.partials.modal-create')

<!-- OFFCANVAS DETAIL TASK (DRAWER SAMPING BOOTSTRAP) -->
@include('tasks.partials.offcanvas-detail')

@endsection

@push('scripts')
<script>
    // Inisialisasi tooltip Bootstrap untuk avatar kolaborator
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

