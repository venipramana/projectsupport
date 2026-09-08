@extends('layouts.app')

@section('title', 'Kanban Progress')
@section('header-title', 'Kanban Progress')

@section('custom-head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('custom-styles')
<style>
    /* Metric / KPI Cards */
    .kpi-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 4px 15px rgba(117, 95, 62, 0.05);
        backdrop-filter: blur(10px);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(117, 95, 62, 0.1);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kpi-icon.primary {
        background: rgba(117, 95, 62, 0.15);
        color: var(--primary);
    }

    .kpi-icon.success {
        background: rgba(34, 197, 94, 0.15);
        color: #16a34a;
    }

    .kpi-icon.warning {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }

    .kpi-info {
        display: flex;
        flex-direction: column;
    }

    .kpi-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    .kpi-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Filter & Actions Bar */
    .filter-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(117, 95, 62, 0.04);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        flex: 1;
        min-width: 180px;
    }

    .filter-group.search-group {
        flex: 2;
        min-width: 250px;
    }

    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid rgba(117, 95, 62, 0.25);
        color: var(--text-main);
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(117, 95, 62, 0.15);
    }

    .btn {
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        height: 40px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 12px rgba(117, 95, 62, 0.25);
    }

    .btn-primary:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.9);
        color: var(--text-main);
        border: 1px solid var(--glass-border);
    }

    .btn-secondary:hover {
        background: #ffffff;
        border-color: var(--primary);
    }

    /* Kanban Matrix Board */
    .kanban-board-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05);
        backdrop-filter: blur(10px);
    }

    .kanban-board-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.4);
    }

    .kanban-board-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .kanban-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .kanban-matrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
        text-align: left;
    }

    .kanban-matrix-table th {
        background: #e2dcd3;
        color: var(--text-main);
        font-weight: 700;
        font-size: 0.88rem;
        padding: 0.85rem 1rem;
        border: 1px solid rgba(117, 95, 62, 0.2);
        text-transform: lowercase;
        white-space: nowrap;
        text-align: center;
    }

    .kanban-matrix-table th.project-col-th {
        text-align: left;
        min-width: 260px;
        background: #d8d0c3;
    }

    .kanban-matrix-table td {
        padding: 0.75rem 0.85rem;
        border: 1px solid rgba(117, 95, 62, 0.18);
        vertical-align: middle;
        background: #ffffff;
    }

    .kanban-matrix-table tr:hover td {
        background-color: #faf8f5;
    }

    .project-cell {
        background: #ede8df !important;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .project-cell:hover {
        background-color: #e4ddd2 !important;
    }

    .project-title-box {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .project-name {
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .project-name:hover {
        color: var(--primary);
    }

    .project-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        align-items: center;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .badge-direktorat {
        background: rgba(117, 95, 62, 0.12);
        color: var(--primary);
        padding: 0.15rem 0.45rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.7rem;
    }

    /* Step Cell Styles */
    .step-cell {
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        min-width: 110px;
        height: 52px;
        padding: 0.35rem !important;
    }

    .step-cell:hover {
        background-color: rgba(117, 95, 62, 0.08) !important;
    }

    .step-active-block {
        background: #5cb85c;
        background: linear-gradient(135deg, #48bb78, #38a169);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 8px;
        padding: 0.6rem 0.75rem;
        box-shadow: 0 2px 8px rgba(56, 161, 105, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        height: 100%;
        animation: pulseGlow 2.5s infinite;
    }

    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 2px 8px rgba(56, 161, 105, 0.3);
        }
        50% {
            box-shadow: 0 3px 12px rgba(56, 161, 105, 0.5);
        }
    }

    .step-empty-action {
        color: transparent;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .step-cell:hover .step-empty-action {
        color: var(--primary);
        background: rgba(117, 95, 62, 0.1);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(44, 39, 33, 0.55);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        padding: 1rem;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-box {
        background: #ffffff;
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        box-shadow: 0 25px 50px -12px rgba(117, 95, 62, 0.25);
    }

    .modal-overlay.active .modal-box {
        transform: translateY(0);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(117, 95, 62, 0.15);
    }

    .modal-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 1.5rem;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .modal-close:hover {
        color: #dc2626;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .info-item.full-width {
        grid-column: span 2;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 0.92rem;
        color: var(--text-main);
        font-weight: 500;
        background: rgba(244, 240, 234, 0.4);
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        border: 1px solid rgba(117, 95, 62, 0.1);
    }

    .step-transition-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(117, 95, 62, 0.08);
        border: 1px solid rgba(117, 95, 62, 0.2);
        padding: 0.6rem 1rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main);
        width: 100%;
    }

    .step-target-name {
        color: #16a34a;
        font-weight: 700;
    }

    .form-group-modal {
        margin-bottom: 1rem;
    }

    .form-group-modal label {
        display: block;
        margin-bottom: 0.4rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-group-modal textarea {
        resize: vertical;
        min-height: 80px;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #15803d;
    }

    .empty-state {
        text-align: center;
        padding: 3.5rem 1.5rem;
        color: var(--text-muted);
    }

    .empty-state svg {
        margin-bottom: 1rem;
        color: var(--text-muted);
        opacity: 0.6;
    }
</style>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- KPI Summary Cards -->
<div class="kpi-container">
    <div class="kpi-card">
        <div class="kpi-icon primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
        </div>
        <div class="kpi-info">
            <span class="kpi-value">{{ $totalProjects }}</span>
            <span class="kpi-label">Total Projects</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div class="kpi-info">
            <span class="kpi-value">{{ $inProgressProjects }}</span>
            <span class="kpi-label">On Progress</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        <div class="kpi-info">
            <span class="kpi-value">{{ $completedProjects }}</span>
            <span class="kpi-label">Live / Selesai</span>
        </div>
    </div>
</div>

<!-- Filters & Search Bar -->
<div class="filter-card">
    <form action="{{ route('kanban.index') }}" method="GET" class="filter-form">
        <div class="filter-group search-group">
            <label for="search">Cari Project / PIC / No Surat</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ $searchKeyword }}">
        </div>

        <div class="filter-group">
            <label for="filter_direktorat">Direktorat</label>
            <select name="filter_direktorat" id="filter_direktorat" class="form-control">
                <option value="">Semua Direktorat</option>
                @foreach($direktorats as $dir)
                    <option value="{{ $dir->id }}" {{ $selectedDirektorat == $dir->id ? 'selected' : '' }}>
                        {{ $dir->direktorat }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label for="filter_tahun">Tahun (Tanggal Awal)</label>
            <select name="filter_tahun" id="filter_tahun" class="form-control">
                <option value="all" {{ $selectedTahun === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunList as $thn)
                    <option value="{{ $thn }}" {{ $selectedTahun == $thn ? 'selected' : '' }}>
                        {{ $thn }} {{ $thn == date('Y') ? '(Tahun Berjalan)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Filter
            </button>
            <a href="{{ route('kanban.index') }}" class="btn btn-secondary" title="Reset Filter">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Kanban Progress Matrix Board -->
<div class="kanban-board-card">
    <div class="kanban-board-header">
        <div class="kanban-board-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
            Kanban Matrix View
        </div>
        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Menampilkan <strong>{{ $projects->count() }}</strong> project
        </div>
    </div>

    <div class="kanban-table-wrapper">
        @if($projects->isEmpty())
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <h4>Tidak ada data project yang sesuai filter</h4>
                <p style="font-size: 0.85rem; margin-top: 0.5rem;">Coba sesuaikan filter Direktorat, Tahun, atau kata kunci pencarian Anda.</p>
            </div>
        @else
            <table class="kanban-matrix-table">
                <thead>
                    <tr>
                        <th class="project-col-th">PROJECT</th>
                        @foreach($rprojects as $rproj)
                            <th>{{ ($rproj->deskripsi) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <!-- Project Info Column (Clickable to show Project Details) -->
                            <td class="project-cell" onclick="openProjectDetailModal({{ json_encode($project) }})">
                                <div class="project-title-box">
                                    <div class="project-name" title="Klik untuk lihat detail project">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                        {{ $project->project_name }}
                                    </div>
                                    <div class="project-meta">
                                        @if($project->direktorat_rel)
                                            <span class="badge-direktorat">{{ $project->direktorat_rel->direktorat }}</span>
                                        @endif
                                        @if($project->pic_name)
                                            <span>PIC: {{ $project->pic_name }}</span>
                                        @endif
                                        @if($project->tanggal_awal)
                                            <span>&bull; {{ \Carbon\Carbon::parse($project->tanggal_awal)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Dynamic Step Columns -->
                            @foreach($rprojects as $rproj)
                                @php
                                    $isActive = ($project->rproject == $rproj->id);
                                @endphp
                                <td class="step-cell" 
                                    onclick="{{ $isActive ? 'openProjectDetailModal('.json_encode($project).')' : 'openStepChangeModal('.$project->id.', '.json_encode($project->project_name).', '.$project->rproject.', '.json_encode($project->rproject_relation->deskripsi ?? 'Unknown').', '.$rproj->id.', '.json_encode($rproj->deskripsi).')' }}"
                                    title="{{ $isActive ? 'Status saat ini: '.$rproj->deskripsi : 'Klik untuk pindah ke step: '.$rproj->deskripsi }}">
                                    @if($isActive)
                                        <div class="step-active-block">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            <span></span>
                                        </div>
                                    @else
                                        <div class="step-empty-action">
                                            <span>Pindah ke sini &rarr;</span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<!-- Modal 1: Detail Informasi Project -->
<div class="modal-overlay" id="projectDetailModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Informasi Detail Project
            </h3>
            <button class="modal-close" onclick="closeProjectDetailModal()">&times;</button>
        </div>
        
        <div class="info-grid">
            <div class="info-item full-width">
                <span class="info-label">Nama Project</span>
                <span class="info-value" id="detail_project_name" style="font-weight: 700; color: var(--primary);">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Direktorat</span>
                <span class="info-value" id="detail_direktorat">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Bagian</span>
                <span class="info-value" id="detail_bagian">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Nomor Surat</span>
                <span class="info-value" id="detail_no_surat">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">PIC Name</span>
                <span class="info-value" id="detail_pic_name">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Lead By</span>
                <span class="info-value" id="detail_leadby">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Support By</span>
                <span class="info-value" id="detail_supportby">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Tanggal Awal</span>
                <span class="info-value" id="detail_tanggal_awal">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Tanggal Akhir</span>
                <span class="info-value" id="detail_tanggal_akhir">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Status Saat Ini</span>
                <span class="info-value" id="detail_status" style="font-weight: 700; color: #15803d;">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">RKAP</span>
                <span class="info-value" id="detail_rkap">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Katalog / Versi</span>
                <span class="info-value" id="detail_catalog">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">By Vendor</span>
                <span class="info-value" id="detail_vendor">-</span>
            </div>

            <div class="info-item full-width">
                <span class="info-label">Catatan</span>
                <span class="info-value" id="detail_catatan" style="min-height: 50px;">-</span>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
            <a href="#" id="detail_history_link" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Lihat Riwayat & File Evidence
            </a>
            <button type="button" class="btn btn-primary" onclick="closeProjectDetailModal()">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal 2: Perpindahan Step Kanban -->
<div class="modal-overlay" id="stepChangeModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                Ubah Step Kanban
            </h3>
            <button class="modal-close" onclick="closeStepChangeModal()">&times;</button>
        </div>

        <form action="{{ route('kanban.update_step') }}" method="POST" enctype="multipart/form-data" id="stepChangeForm">
            @csrf
            <input type="hidden" name="idproject" id="step_idproject">
            <input type="hidden" name="rproject" id="step_target_rproject">

            <div class="step-transition-badge">
                <span>Perpindahan Step:</span>
                <span id="step_from_name" style="color: var(--text-muted); text-decoration: line-through;">-</span>
                <span>&rarr;</span>
                <span class="step-target-name" id="step_to_name">-</span>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.2rem;">Project</label>
                <div id="step_project_name" style="font-weight: 700; font-size: 1rem; color: var(--text-main);">-</div>
            </div>

            <div class="form-group-modal">
                <label for="modal_tanggal">Tanggal Perubahan <span style="color: #dc2626;">*</span></label>
                <input type="text" name="tanggal" id="modal_tanggal" class="form-control" required placeholder="DD-MM-YYYY">
            </div>

            <div class="form-group-modal">
                <label for="modal_progress">Persentase Progress (%)</label>
                <input type="number" name="progress" id="modal_progress" class="form-control" min="0" max="100" placeholder="Contoh: 50">
            </div>

            <div class="form-group-modal">
                <label for="modal_catatan">Catatan / Keterangan <span style="color: #dc2626;">*</span></label>
                <textarea name="catatan" id="modal_catatan" class="form-control" rows="3" maxlength="500" required placeholder="Masukkan catatan atau keterangan perubahan status step..."></textarea>
            </div>

            <div class="form-group-modal" style="background: rgba(117, 95, 62, 0.05); padding: 1rem; border-radius: 12px; border: 1px dashed var(--glass-border);">
                <label for="modal_evidence" style="color: var(--primary); display: flex; align-items: center; gap: 0.35rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Upload File Evidence (Simpan ke MinIO)
                </label>
                <input type="file" name="evidence_file" id="modal_evidence" class="form-control" style="margin-top: 0.35rem; background: #ffffff;" accept=".pdf,.png,.jpg,.jpeg,.docx,.zip,.rar,.xlsx,.pptx">
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem;">
                    *File evidence otomatis diupload dan tersimpan di MinIO bucket.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeStepChangeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-save-step">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('custom-scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Init Flatpickr for modal datepicker
        flatpickr("#modal_tanggal", {
            dateFormat: "d-m-Y",
            defaultDate: new Date(),
            allowInput: true
        });
    });

    // Modal 1: Project Details Logic
    function openProjectDetailModal(project) {
        document.getElementById('detail_project_name').innerText = project.project_name || '-';
        document.getElementById('detail_direktorat').innerText = project.direktorat_rel ? project.direktorat_rel.direktorat : '-';
        document.getElementById('detail_bagian').innerText = project.bagian || '-';
        document.getElementById('detail_no_surat').innerText = project.no_surat || '-';
        document.getElementById('detail_pic_name').innerText = project.pic_name || '-';
        document.getElementById('detail_leadby').innerText = project.leadby_rel ? project.leadby_rel.nama : (project.leadby || '-');
        document.getElementById('detail_supportby').innerText = project.supportby_rel ? project.supportby_rel.nama : (project.supportby || '-');
        document.getElementById('detail_tanggal_awal').innerText = project.tanggal_awal ? formatDate(project.tanggal_awal) : '-';
        document.getElementById('detail_tanggal_akhir').innerText = project.tanggal_akhir ? formatDate(project.tanggal_akhir) : '-';
        document.getElementById('detail_status').innerText = project.rproject_relation ? project.rproject_relation.deskripsi : '-';
        document.getElementById('detail_rkap').innerText = project.rkap || '-';
        document.getElementById('detail_catalog').innerText = (project.catalog_rel ? project.catalog_rel.nama_aplikasi : '') + (project.catalog_version ? ' (v' + project.catalog_version + ')' : '-');
        document.getElementById('detail_vendor').innerText = (project.byvendor == 1 ? 'Ya' : 'Tidak') + (project.vendorname ? ' - ' + project.vendorname : '');
        document.getElementById('detail_catatan').innerText = project.catatan || '-';

        // Link to History
        document.getElementById('detail_history_link').href = '/hproject/' + project.id;

        document.getElementById('projectDetailModal').classList.add('active');
    }

    function closeProjectDetailModal() {
        document.getElementById('projectDetailModal').classList.remove('active');
    }

    // Modal 2: Step Change Logic
    function openStepChangeModal(projectId, projectName, currentStepId, currentStepName, targetStepId, targetStepName) {
        document.getElementById('step_idproject').value = projectId;
        document.getElementById('step_target_rproject').value = targetStepId;
        document.getElementById('step_project_name').innerText = projectName;
        document.getElementById('step_from_name').innerText = currentStepName || 'Step Sebelumnya';
        document.getElementById('step_to_name').innerText = targetStepName;
        document.getElementById('modal_catatan').value = 'Perubahan status step ke ' + targetStepName;
        document.getElementById('modal_progress').value = '';
        document.getElementById('modal_evidence').value = '';

        document.getElementById('stepChangeModal').classList.add('active');
    }

    function closeStepChangeModal() {
        document.getElementById('stepChangeModal').classList.remove('active');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return dateStr;
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProjectDetailModal();
            closeStepChangeModal();
        }
    });

    // Close modal when clicking on overlay background
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
</script>
@endsection
