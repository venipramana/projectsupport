@extends('layouts.app')

@section('title', 'Katalog Administration')
@section('header-title', 'Katalog Administration')

@section('custom-head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<!-- SECTION DONUT CHART SUMMARY -->
<div class="summary-section" style="margin-bottom: 2rem;">
    <div class="card" style="background: rgba(255, 255, 255, 0.65); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05);">
        <div class="card-header" style="font-size: 1.1rem; font-weight: 600; color: var(--text-main); margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
                Summary Jumlah Katalog Per-Direktorat
            </div>
            <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary); background: rgba(117, 95, 62, 0.12); padding: 0.25rem 0.75rem; border-radius: 20px;">
                Total: {{ $allCatalogs->count() }} Katalog
            </span>
        </div>
        
        <div class="summary-grid">
            <!-- Chart Container -->
            <div class="chart-container" style="position: relative; height: 240px; width: 100%; display: flex; justify-content: center;">
                <canvas id="catalogDirektoratChart"></canvas>
            </div>
            
            <!-- List Container -->
            <div class="list-container" style="max-height: 240px; overflow-y: auto; padding-right: 0.5rem;">
                @forelse($catalogs_by_direktorat as $item)
                    <div class="list-item interactive" onclick="filterByDirektoratId('{{ $item['id_direktorat'] }}')" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: 10px; background: rgba(255, 255, 255, 0.5); margin-bottom: 0.5rem; cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent;">
                        <span style="font-weight: 500; font-size: 0.95rem; color: var(--text-main);">{{ $item['direktorat'] }}</span>
                        <span style="background: rgba(117, 95, 62, 0.15); color: var(--primary); padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">{{ $item['total'] }} Katalog</span>
                    </div>
                @empty
                    <div class="empty-state" style="padding: 2rem; text-align: center; color: var(--text-muted); font-style: italic;">Tidak ada data katalog.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ACTIONS BAR -->
<div class="actions-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <form action="{{ route('rcatalog.index') }}" method="GET" id="filterForm" style="display: flex; gap: 1rem; align-items: center;">
        <label for="filter_direktorat" style="color: var(--text-muted); font-weight: 500;">Filter Direktorat:</label>
        <select name="filter_direktorat" id="filter_direktorat" class="form-control" style="width: auto; background-color: #ffffff;" onchange="this.form.submit()">
            <option value="">-- Semua Direktorat --</option>
            @foreach($direktorats as $dir)
                <option value="{{ $dir->id }}" {{ $selectedDirektorat == $dir->id ? 'selected' : '' }}>{{ $dir->deskripsi }}</option>
            @endforeach
        </select>
    </form>

    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <button type="button" class="btn-export-excel" onclick="exportCatalogExcel()" title="Save to Excel">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Save to Excel
        </button>
        <button class="btn btn-primary" onclick="openModal('createModal')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Katalog
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin-left: 1rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="master-section" style="margin-bottom: 2rem;">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="display: none;">ID</th>
                    <th>Deskripsi Katalog</th>
                    <th>Direktorat</th>
                    <th>Versi</th>
                    <th>Update Terakhir</th>
                    <th>Base URL</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rcatalogs as $cat)
                <tr>
                    <td style="display: none;">{{ $cat->id }}</td>
                    <td>
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="color: var(--primary); cursor: pointer; font-weight: 500; text-decoration: underline;" onclick="loadProjects({{ $cat->id }}, '{{ addslashes($cat->description) }}')">
                                {{ $cat->description }}
                            </span>
                            @php
                                $isDeveloping = false;
                                if(isset($cat->projects)) {
                                    foreach($cat->projects as $project) {
                                        // id 1 di tabel rproject adalah status DEVELOPMENT
                                        if ($project->rproject == 1) {
                                            $isDeveloping = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            @if($isDeveloping)
                                <span style="font-size: 0.75rem; background: rgba(245, 158, 11, 0.15); color: #d97706; padding: 0.2rem 0.6rem; border-radius: 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;" title="Ada project dalam tahap pengembangan">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    Pengembangan
                                </span>
                            @endif
                        </span>
                    </td>
                    <td>
                        @if($cat->direktorat)
                            {{ $cat->direktorat->deskripsi }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $cat->current_version }}</td>
                    <td>{{ $cat->last_update ? date('d-m-Y', strtotime($cat->last_update)) : '-' }}</td>
                    <td>
                        @if($cat->url_base)
                            <a href="{{ $cat->url_base }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500;">Link &nearr;</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-edit" onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->description ?? '') }}', '{{ addslashes($cat->current_version ?? '') }}', '{{ addslashes($cat->last_update ?? '') }}', '{{ $cat->id_direktorat ?? '' }}', '{{ addslashes($cat->url_base ?? '') }}')" title="Edit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('rcatalog.destroy', $cat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus katalog ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-delete" title="Delete">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 3rem; color: var(--text-muted);">
                        Belum ada data katalog. Silakan tambah katalog baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($rcatalogs->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Menampilkan {{ $rcatalogs->firstItem() ?? 0 }} - {{ $rcatalogs->lastItem() ?? 0 }} dari {{ $rcatalogs->total() }} total data
        </div>
        <div>
            {{ $rcatalogs->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif
</div>

<!-- Detail Section -->
<div class="detail-section" id="detailSection" style="display: none; padding-top: 1rem; border-top: 1px solid var(--glass-border); margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <h3 style="font-weight: 600; color: var(--primary); font-size: 1.1rem;">Projects untuk Katalog: <span id="detailCatalogName"></span></h3>
        <button type="button" class="btn-export-excel" onclick="exportDetailProjectsExcel()" title="Save to Excel">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Save Detail to Excel
        </button>
    </div>
    <div class="table-container" style="max-height: 350px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Tanggal</th>
                    <th>Versi Katalog</th>
                    <th>Project (Direktorat)</th>
                </tr>
            </thead>
            <tbody id="detailTableBody">
                <!-- Dynamically populated via JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tambah Katalog</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form action="{{ route('rcatalog.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Deskripsi Katalog</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Direktorat</label>
                    <select name="id_direktorat" class="form-control">
                        <option value="">-- Pilih Direktorat --</option>
                        @foreach($direktorats as $dir)
                            <option value="{{ $dir->id }}">{{ $dir->deskripsi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row" style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Versi (Current Version)</label>
                        <input type="text" name="current_version" class="form-control" placeholder="v1.0.0">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Update Terakhir</label>
                        <input type="date" name="last_update" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Base URL</label>
                    <input type="url" name="url_base" class="form-control" placeholder="https://example.com">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Katalog</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Deskripsi Katalog</label>
                    <input type="text" id="edit_description" name="description" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Direktorat</label>
                    <select id="edit_id_direktorat" name="id_direktorat" class="form-control">
                        <option value="">-- Pilih Direktorat --</option>
                        @foreach($direktorats as $dir)
                            <option value="{{ $dir->id }}">{{ $dir->deskripsi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row" style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label>Versi (Current Version)</label>
                        <input type="text" id="edit_current_version" name="current_version" class="form-control">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Update Terakhir</label>
                        <input type="date" id="edit_last_update" name="last_update" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Base URL</label>
                    <input type="url" id="edit_url_base" name="url_base" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('custom-styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 1.5rem;
        align-items: center;
    }
    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }

    .list-item.interactive:hover {
        background: rgba(117, 95, 62, 0.12) !important;
        border-color: rgba(117, 95, 62, 0.25) !important;
        transform: translateX(3px);
    }

    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 15px rgba(117, 95, 62, 0.25);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(117, 95, 62, 0.4);
    }

    .btn-secondary {
        background: var(--glass-bg);
        color: var(--text-main);
        border: 1px solid var(--glass-border);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    /* Save to Excel Button */
    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #059669;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-export-excel:hover {
        background: rgba(16, 185, 129, 0.22);
        border-color: rgba(16, 185, 129, 0.5);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #059669;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #dc2626;
    }

    .table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 520px;
        background: rgba(255, 255, 255, 0.75);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05);
    }

    /* Custom Scrollbar for table-container & list-container */
    .table-container::-webkit-scrollbar,
    .list-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .table-container::-webkit-scrollbar-track,
    .list-container::-webkit-scrollbar-track {
        background: rgba(117, 95, 62, 0.05);
        border-radius: 10px;
    }
    .table-container::-webkit-scrollbar-thumb,
    .list-container::-webkit-scrollbar-thumb {
        background: rgba(117, 95, 62, 0.25);
        border-radius: 10px;
    }
    .table-container::-webkit-scrollbar-thumb:hover,
    .list-container::-webkit-scrollbar-thumb:hover {
        background: rgba(117, 95, 62, 0.5);
    }

    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .data-table th, .data-table td {
        padding: 1rem 1.25rem;
        text-align: left;
        border-bottom: 1px solid var(--glass-border);
    }

    .data-table th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f3efe6;
        font-weight: 600;
        color: var(--text-main);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--glass-border);
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background: rgba(117, 95, 62, 0.04);
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
    }

    .btn-edit { color: var(--primary); }
    .btn-edit:hover { background: rgba(117, 95, 62, 0.15); border-color: rgba(117, 95, 62, 0.3); }

    .btn-delete { color: #dc2626; }
    .btn-delete:hover { background: rgba(220, 38, 38, 0.15); border-color: rgba(220, 38, 38, 0.3); }

    .text-center { text-align: center; }
    .text-muted { color: var(--text-muted); font-size: 0.85rem; font-weight: normal; }

    /* Modals */
    .modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(44, 39, 33, 0.5);
        backdrop-filter: blur(8px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal.active {
        display: flex;
        opacity: 1;
    }

    .modal-content {
        background: #ffffff;
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 25px 50px -12px rgba(117, 95, 62, 0.25);
        transform: translateY(20px);
        transition: transform 0.3s ease;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    .modal.active .modal-content {
        transform: translateY(0);
    }

    .modal-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 { font-size: 1.25rem; font-weight: 600; color: var(--text-main); }

    .btn-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    .btn-close:hover { color: var(--text-main); }

    .modal-body {
        padding: 2rem;
        overflow-y: auto;
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--glass-border);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(117, 95, 62, 0.2);
    }
    
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23755f3e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
    }
    
    select.form-control option {
        background: #ffffff;
        color: var(--text-main);
    }
</style>
@endsection

@section('custom-scripts')
<script>
    const rawCatalogData = @json($allCatalogs);
    const catalogData = Array.isArray(rawCatalogData) ? rawCatalogData : (rawCatalogData.data || []);
    const catalogDirektoratData = @json($catalogs_by_direktorat);
    let currentDetailProjectsData = [];
    let currentDetailCatalogName = '';

    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openEditModal(id, description, current_version, last_update, id_direktorat, url_base) {
        const form = document.getElementById('editForm');
        form.action = `/rcatalog/${id}`;

        document.getElementById('edit_description').value = description;
        document.getElementById('edit_current_version').value = current_version;
        document.getElementById('edit_last_update').value = last_update;
        document.getElementById('edit_id_direktorat').value = id_direktorat;
        document.getElementById('edit_url_base').value = url_base;

        openModal('editModal');
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }

    function filterByDirektoratId(dirId) {
        const select = document.getElementById('filter_direktorat');
        select.value = dirId || '';
        document.getElementById('filterForm').submit();
    }

    // Initialize Chart.js
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('catalogDirektoratChart').getContext('2d');
        const labels = catalogDirektoratData.map(d => d.direktorat);
        const dataValues = catalogDirektoratData.map(d => d.total);
        
        const colors = [
            '#755f3e', '#54422b', '#9c825e', '#b59c77', '#c7ab83', 
            '#3f3526', '#87704e', '#d8c2a3', '#614d33', '#ab9471'
        ];
        
        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                const total = dataValues.reduce((a, b) => a + (Number(b) || 0), 0);
                const meta = chart.getDatasetMeta(0);
                if (meta && meta.data && meta.data.length > 0) {
                    const x = meta.data[0].x;
                    const y = meta.data[0].y;
                    
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    ctx.font = '600 11px "Inter", sans-serif';
                    ctx.fillStyle = '#6e665d';
                    ctx.fillText('Total Katalog', x, y - 10);
                    
                    ctx.font = 'bold 20px "Inter", sans-serif';
                    ctx.fillStyle = '#755f3e';
                    ctx.fillText(total.toLocaleString('id-ID'), x, y + 10);
                }
                ctx.restore();
            }
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#6e665d',
                            font: { family: "'Inter', sans-serif", size: 11 },
                            padding: 12,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(44, 39, 33, 0.95)',
                        titleFont: { family: "'Inter', sans-serif" }, bodyFont: { family: "'Inter', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        borderColor: 'rgba(117, 95, 62, 0.2)',
                        borderWidth: 1
                    }
                },
                cutout: '65%',
                onClick: (e, activeElements) => {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const selectedItem = catalogDirektoratData[index];
                        if (selectedItem && selectedItem.id_direktorat) {
                            filterByDirektoratId(selectedItem.id_direktorat);
                        }
                    }
                }
            },
            plugins: [centerTextPlugin]
        });
    });

    // Excel Exporter Helper
    function exportToExcelHTML(headers, dataRows, filename) {
        let tableHTML = '<table border="1"><thead><tr>';
        headers.forEach(h => {
            tableHTML += `<th style="background:#755f3e;color:#ffffff;font-weight:bold;padding:6px;">${h}</th>`;
        });
        tableHTML += '</tr></thead><tbody>';
        
        dataRows.forEach(row => {
            tableHTML += '<tr>';
            row.forEach(cell => {
                tableHTML += `<td style="padding:5px;">${cell !== null && cell !== undefined ? cell : '-'}</td>`;
            });
            tableHTML += '</tr>';
        });
        tableHTML += '</tbody></table>';

        const dataType = 'application/vnd.ms-excel';
        const xTag = '<x' + ':';
        const htmlTemplate = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8"><!--[if gte mso 9]><xml>${xTag}ExcelWorkbook>${xTag}ExcelWorksheets>${xTag}ExcelWorksheet>${xTag}Name>Sheet1</${xTag}Name>${xTag}WorksheetOptions>${xTag}DisplayGridlines/></${xTag}WorksheetOptions></${xTag}ExcelWorksheet></${xTag}ExcelWorksheets></${xTag}ExcelWorkbook></xml><![endif]--></head><body>${tableHTML}</body></html>`;

        const downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        const fileNameWithExt = (filename || 'export') + '.xls';

        if (navigator.msSaveOrOpenBlob) {
            const blob = new Blob(['\ufeff', htmlTemplate], { type: dataType });
            navigator.msSaveOrOpenBlob(blob, fileNameWithExt);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + encodeURIComponent(htmlTemplate);
            downloadLink.download = fileNameWithExt;
            downloadLink.click();
        }
        document.body.removeChild(downloadLink);
    }

    window.exportCatalogExcel = function() {
        const headers = ['No', 'Deskripsi Katalog', 'Direktorat', 'Versi', 'Update Terakhir', 'Base URL'];
        const rows = catalogData.map((cat, idx) => [
            idx + 1,
            cat.description || '-',
            cat.direktorat ? cat.direktorat.deskripsi : '-',
            cat.current_version || '-',
            cat.last_update ? formatDate(cat.last_update) : '-',
            cat.url_base || '-'
        ]);
        exportToExcelHTML(headers, rows, 'List_Katalog_Administration');
    };

    window.exportDetailProjectsExcel = function() {
        const headers = ['No', 'Nama Project', 'Tanggal', 'Versi Katalog', 'Project (Direktorat)'];
        const rows = currentDetailProjectsData.map((p, idx) => [
            idx + 1,
            p.project_name || '-',
            p.tanggal || '-',
            p.catalog_version || '-',
            p.rproject_deskripsi || '-'
        ]);
        const safeTitle = (currentDetailCatalogName || 'Katalog').replace(/[^a-zA-Z0-9]/g, '_');
        exportToExcelHTML(headers, rows, 'Detail_Projects_' + safeTitle);
    };

    function formatDate(dateString) {
        if (!dateString) return '-';
        const d = new Date(dateString);
        if (isNaN(d.getTime())) return dateString;
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    }

    async function loadProjects(catalogId, catalogName) {
        const detailSection = document.getElementById('detailSection');
        const detailCatalogName = document.getElementById('detailCatalogName');
        const tbody = document.getElementById('detailTableBody');
        
        detailSection.style.display = 'block';
        detailCatalogName.textContent = catalogName;
        currentDetailCatalogName = catalogName;
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
        
        try {
            const response = await fetch(`/rcatalog/${catalogId}/projects`);
            const projects = await response.json();
            currentDetailProjectsData = projects;
            
            if (projects.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Belum ada data project untuk katalog ini.</td></tr>';
                return;
            }
            
            let rows = '';
            projects.forEach(project => {
                rows += `
                    <tr>
                        <td>${project.project_name || '-'}</td>
                        <td>${project.tanggal}</td>
                        <td>${project.catalog_version || '-'}</td>
                        <td>${project.rproject_deskripsi}</td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = rows;
            detailSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
        } catch (error) {
            console.error("Error fetching projects:", error);
            tbody.innerHTML = '<tr><td colspan="4" class="text-center" style="color: #dc2626;">Gagal memuat data project.</td></tr>';
        }
    }
</script>
@endsection
