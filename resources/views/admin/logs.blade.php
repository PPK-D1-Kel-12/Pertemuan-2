@extends('layouts.app')

@section('title', 'Jara — Log Aktivitas Admin')

@section('header_title', 'Log Audit & Aktivitas Admin (SRS-F-16)')
@section('header_subtitle', 'Pencatatan riwayat aksi administratif pada pengelolaan akun pengguna')

@section('content')
<div class="container-fluid px-0">

    <!-- Admin Sub-Navigation -->
    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
        <ul class="nav nav-pills gap-1">
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 text-secondary" href="{{ route('admin.users') }}">
                    <i class="bi bi-people-fill me-1"></i> Daftar Pengguna ({{ count($users) }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-sm py-1 px-3 active" href="{{ route('admin.logs') }}">
                    <i class="bi bi-journal-text me-1"></i> Log Aktivitas Admin ({{ count($logs) }})
                </a>
            </li>
        </ul>
    </div>

    <!-- Logs Table -->
    <div class="card border rounded-3 shadow-sm bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">Riwayat Log Aktivitas Akun</h6>
            <span class="badge bg-secondary-subtle text-secondary">{{ count($logs) }} Entri Log</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr style="font-size: 0.78rem;">
                        <th class="ps-3" style="width: 180px;">Waktu Eksekusi</th>
                        <th>Pelaksana (Admin)</th>
                        <th>Jenis Aksi</th>
                        <th>Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $actionBadge = 'bg-primary-subtle text-primary border-primary-subtle';
                            if (str_contains($log['action'], 'DELETE')) $actionBadge = 'bg-danger-subtle text-danger border-danger-subtle';
                            if (str_contains($log['action'], 'INACTIVE')) $actionBadge = 'bg-warning-subtle text-warning border-warning-subtle';
                            if (str_contains($log['action'], 'ACTIVATE')) $actionBadge = 'bg-success-subtle text-success border-success-subtle';
                        @endphp
                        <tr style="font-size: 0.85rem;">
                            <td class="ps-3 text-muted small">
                                <i class="bi bi-clock me-1"></i>
                                {{ date('d M Y, H:i', strtotime($log['timestamp'])) }}
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">
                                    <i class="bi bi-shield-check text-primary me-1"></i>
                                    {{ $log['admin_name'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $actionBadge }} border" style="font-size: 0.7rem;">
                                    {{ $log['action'] }}
                                </span>
                            </td>
                            <td class="text-secondary">
                                {{ $log['description'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4 small">
                                <i class="bi bi-journal-x fs-3 d-block mb-1"></i>
                                Belum ada log aktivitas admin yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

