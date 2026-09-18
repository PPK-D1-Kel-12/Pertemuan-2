@extends('layouts.app')

@section('title', 'Jara — ' . ($lists[$currentListId]['name'] ?? 'Task List'))

@section('header_title')
    <div class="d-flex align-items-center gap-2">
        <div class="d-inline-flex align-items-center justify-content-center rounded-2 p-1" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
            <i class="bi bi-folder2-open fs-5"></i>
        </div>
        <span class="fw-bold text-dark">{{ $lists[$currentListId]['name'] ?? 'Semua Task' }}</span>
        <span class="badge rounded-pill fw-medium" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; font-size: 0.72rem; padding: 0.35rem 0.65rem;">
            {{ count($tasks) }} Task
        </span>
    </div>
@endsection

@section('header_subtitle', 'Tampilan alur kerja tim dengan metrik produktivitas real-time & linimasa')

@section('content')
<div class="container-fluid px-0">

    @if(empty($lists) || count($lists) === 0)
        <div class="card border-0 shadow-sm rounded-4 text-center p-5 my-4 bg-white" style="border: 1px dashed #cbd5e1 !important;">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 68px; height: 68px; background: rgba(99, 102, 241, 0.08); color: #6366f1;">
                <i class="bi bi-folder-x fs-2"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Belum Ada List Tugas</h4>
            <p class="text-muted mx-auto" style="max-width: 450px; font-size: 0.9rem;">
                Seluruh list telah dihapus atau belum dibuat. Silakan buat list baru untuk mulai mengelola task dan berkolaborasi bersama tim.
            </p>
            <div>
                <button class="btn btn-sm px-4 py-2 text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#createListModal" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 8px; font-weight: 600;">
                    <i class="bi bi-plus-circle me-1"></i> Buat List Baru
                </button>
            </div>
        </div>
    @else
    @php
        $totalTasks = count($tasks);
        $inProgressCount = count($tasksByStatus['In Progress'] ?? []);
        $completedCount = count($tasksByStatus['Selesai'] ?? []);
        $todoCount = count($tasksByStatus['To Do'] ?? []);
        $highPriorityCount = count(array_filter($tasks, fn($t) => ($t['priority'] ?? '') === 'Tinggi' && ($t['status'] ?? '') !== 'Selesai'));
        $completionRate = $totalTasks > 0 ? round(($completedCount / $totalTasks) * 100) : 0;
    @endphp

    <!-- Productivity Stats Bar (Linear Metrik Cards) -->
    <div class="row g-3 mb-4">
        <!-- Metric 1: Total Tasks -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-3 p-3 h-100 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Total Tugas</span>
                    <div class="rounded-2 p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i class="bi bi-layers fs-6"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.03em;">{{ $totalTasks }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">task terdaftar</small>
                </div>
            </div>
        </div>

        <!-- Metric 2: In Progress -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-3 p-3 h-100 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">In Progress</span>
                    <div class="rounded-2 p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(99, 102, 241, 0.15); color: #6366f1;">
                        <i class="bi bi-lightning-charge-fill fs-6"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0" style="color: #6366f1; letter-spacing: -0.03em;">{{ $inProgressCount }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">sedang dikerjakan</small>
                </div>
            </div>
        </div>

        <!-- Metric 3: Completion Rate -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-3 p-3 h-100 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Tingkat Selesai</span>
                    <div class="rounded-2 p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="bi bi-check2-circle fs-6"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h3 class="fw-bold mb-0 text-success" style="letter-spacing: -0.03em;">{{ $completionRate }}%</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $completedCount }} / {{ $totalTasks }}</small>
                </div>
                <div class="progress" style="height: 4px; background: #f1f5f9; border-radius: 999px;">
                    <div class="progress-bar bg-success rounded-pill" style="width: {{ $completionRate }}%; transition: width 0.5s ease;"></div>
                </div>
            </div>
        </div>

        <!-- Metric 4: High Priority -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-3 p-3 h-100 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Prioritas Tinggi</span>
                    <div class="rounded-2 p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="bi bi-fire fs-6"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-danger" style="letter-spacing: -0.03em;">{{ $highPriorityCount }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">butuh perhatian</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Filter / Segmented Pills (Linear Style) -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 pb-3 border-bottom">
        <div class="p-1 rounded-3 d-inline-flex flex-wrap gap-1" style="background: #eef2f6; border: 1px solid #e2e8f0;">
            <a class="btn btn-sm py-1 px-3 border-0 rounded-2 text-decoration-none {{ $currentFilter === 'all' ? 'bg-white text-dark shadow-sm fw-semibold' : 'text-muted' }}" 
               style="font-size: 0.8rem; transition: all 0.15s ease;"
               href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'all']) }}">
                Semua <span class="badge rounded-pill ms-1 {{ $currentFilter === 'all' ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }}" style="font-size: 0.68rem;">{{ count($tasks) }}</span>
            </a>
            <a class="btn btn-sm py-1 px-3 border-0 rounded-2 text-decoration-none {{ $currentFilter === 'my_tasks' ? 'bg-white text-dark shadow-sm fw-semibold' : 'text-muted' }}" 
               style="font-size: 0.8rem; transition: all 0.15s ease;"
               href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'my_tasks']) }}">
                <i class="bi bi-person-fill text-warning me-1"></i> Milik Saya
            </a>
            <a class="btn btn-sm py-1 px-3 border-0 rounded-2 text-decoration-none {{ $currentFilter === 'assigned_to_me' ? 'bg-white text-dark shadow-sm fw-semibold' : 'text-muted' }}" 
               style="font-size: 0.8rem; transition: all 0.15s ease;"
               href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'assigned_to_me']) }}">
                <i class="bi bi-people-fill text-info me-1"></i> Kolaborator
            </a>
            <a class="btn btn-sm py-1 px-3 border-0 rounded-2 text-decoration-none {{ $currentFilter === 'high_priority' ? 'bg-white text-dark shadow-sm fw-semibold' : 'text-muted' }}" 
               style="font-size: 0.8rem; transition: all 0.15s ease;"
               href="{{ route('tasks.index', ['list_id' => $currentListId, 'filter' => 'high_priority']) }}">
                <i class="bi bi-flag-fill text-danger me-1"></i> Prioritas Tinggi
            </a>
        </div>

        <div class="text-muted small" style="font-size: 0.78rem;">
            <i class="bi bi-info-circle me-1 text-primary"></i> Klik task untuk membuka <span class="text-dark fw-medium">Drawer Detail & Kolaborator</span>
        </div>
    </div>

    <!-- TASK SECTIONS (Linear Grouped View) -->
    
    <!-- 1. SECTION: IN PROGRESS -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="status-dot progress"></span>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.82rem; letter-spacing: -0.01em;">
                    Sedang Dikerjakan (In Progress)
                </h6>
                <span class="badge rounded-pill text-muted px-2 py-0" style="background: #eef2f6; font-size: 0.7rem; font-weight: 600;">
                    {{ count($tasksByStatus['In Progress']) }}
                </span>
            </div>
        </div>

        @forelse($tasksByStatus['In Progress'] as $task)
            @include('tasks.partials.task-row', ['task' => $task])
        @empty
            <div class="p-3 bg-white border rounded-3 text-center text-muted small mb-2" style="border-style: dashed !important;">
                Tidak ada task yang sedang dikerjakan saat ini.
            </div>
        @endforelse
    </div>

    <!-- 2. SECTION: TO DO -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="status-dot todo"></span>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.82rem; letter-spacing: -0.01em;">
                    Belum Dikerjakan (To Do)
                </h6>
                <span class="badge rounded-pill text-muted px-2 py-0" style="background: #eef2f6; font-size: 0.7rem; font-weight: 600;">
                    {{ count($tasksByStatus['To Do']) }}
                </span>
            </div>
        </div>

        @forelse($tasksByStatus['To Do'] as $task)
            @include('tasks.partials.task-row', ['task' => $task])
        @empty
            <div class="p-3 bg-white border rounded-3 text-center text-muted small mb-2" style="border-style: dashed !important;">
                Tidak ada task to-do.
            </div>
        @endforelse
    </div>

    <!-- 3. SECTION: SELESAI -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="status-dot completed"></span>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.82rem; letter-spacing: -0.01em;">
                    Selesai (Completed)
                </h6>
                <span class="badge rounded-pill text-muted px-2 py-0" style="background: #eef2f6; font-size: 0.7rem; font-weight: 600;">
                    {{ count($tasksByStatus['Selesai']) }}
                </span>
            </div>
        </div>

        @forelse($tasksByStatus['Selesai'] as $task)
            @include('tasks.partials.task-row', ['task' => $task])
        @empty
            <div class="p-3 bg-white border rounded-3 text-center text-muted small mb-2" style="border-style: dashed !important;">
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

