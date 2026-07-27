@extends('layouts.app')

@section('title', 'Katalog Administration')
@section('header-title', 'Katalog Administration')

@section('content')
<div class="actions-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <form action="{{ route('rcatalog.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center;">
        <label for="filter_direktorat" style="color: var(--text-muted); font-weight: 500;">Filter Direktorat:</label>
        <select name="filter_direktorat" id="filter_direktorat" class="form-control" style="width: auto; background-color: rgba(15, 23, 42, 0.8);" onchange="this.form.submit()">
            <option value="">-- Semua Direktorat --</option>
            @foreach($direktorats as $dir)
                <option value="{{ $dir->id }}" {{ $selectedDirektorat == $dir->id ? 'selected' : '' }}>{{ $dir->deskripsi }}</option>
            @endforeach
        </select>
    </form>

    <button class="btn btn-primary" onclick="openModal('createModal')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Katalog
    </button>
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

<div class="master-section" style="height: 60vh; overflow-y: auto; padding-right: 10px; margin-bottom: 2rem;">
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
                        <span style="color: #60a5fa; cursor: pointer; text-decoration: underline;" onclick="loadProjects({{ $cat->id }}, '{{ addslashes($cat->description) }}')">
                            {{ $cat->description }}
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
                            <a href="{{ $cat->url_base }}" target="_blank" style="color: #60a5fa; text-decoration: none;">Link &nearr;</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-edit" onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->description ?? '') }}', '{{ addslashes($cat->current_version ?? '') }}', '{{ addslashes($cat->last_update ?? '') }}', '{{ $cat->id_direktorat ?? '' }}', '{{ addslashes($cat->url_base ?? '') }}')" title="Edit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
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
</div>

<!-- Detail Section -->
<div class="detail-section" id="detailSection" style="display: none; padding-top: 1rem; border-top: 1px solid var(--glass-border);">
    <h3 style="margin-bottom: 1rem; font-weight: 600; color: #6ee7b7;">Projects untuk Katalog: <span id="detailCatalogName"></span></h3>
    <div class="table-container">
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
    .actions-bar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 2rem;
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
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.5);
    }

    .btn-secondary {
        background: var(--glass-bg);
        color: var(--text-main);
        border: 1px solid var(--glass-border);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.08);
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
        color: #6ee7b7;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }

    .table-container {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th, .data-table td {
        padding: 1.2rem 1.5rem;
        text-align: left;
        border-bottom: 1px solid var(--glass-border);
    }

    .data-table th {
        background: rgba(255, 255, 255, 0.02);
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .badge {
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
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

    .btn-edit { color: #60a5fa; }
    .btn-edit:hover { background: rgba(96, 165, 250, 0.15); border-color: rgba(96, 165, 250, 0.3); }

    .btn-delete { color: #f87171; }
    .btn-delete:hover { background: rgba(248, 113, 113, 0.15); border-color: rgba(248, 113, 113, 0.3); }

    .text-center { text-align: center; }
    .text-muted { color: var(--text-muted); font-size: 0.85rem; font-weight: normal; }

    /* Modals */
    .modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.8);
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
        background: #1e293b;
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
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

    .modal-header h2 { font-size: 1.25rem; font-weight: 600; }

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
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    
    .form-control[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
    }
    
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
    }
    
    select.form-control option {
        background: #1e293b;
        color: #f8fafc;
    }
    .master-section::-webkit-scrollbar {
        width: 8px;
    }
    .master-section::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 4px;
    }
    .master-section::-webkit-scrollbar-thumb {
        background: rgba(139, 92, 246, 0.3);
        border-radius: 4px;
    }
    .master-section::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.5);
    }
</style>
@endsection

@section('custom-scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openEditModal(id, description, current_version, last_update, id_direktorat, url_base) {
        // Set form action dynamically
        const form = document.getElementById('editForm');
        form.action = `/rcatalog/${id}`;

        // Populate fields
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_current_version').value = current_version;
        document.getElementById('edit_last_update').value = last_update;
        document.getElementById('edit_id_direktorat').value = id_direktorat;
        document.getElementById('edit_url_base').value = url_base;

        // Open Modal
        openModal('editModal');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }

    async function loadProjects(catalogId, catalogName) {
        // Show detail section
        const detailSection = document.getElementById('detailSection');
        const detailCatalogName = document.getElementById('detailCatalogName');
        const tbody = document.getElementById('detailTableBody');
        
        detailSection.style.display = 'block';
        detailCatalogName.textContent = catalogName;
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
        
        try {
            const response = await fetch(`/rcatalog/${catalogId}/projects`);
            const projects = await response.json();
            
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
            
        } catch (error) {
            console.error("Error fetching projects:", error);
            tbody.innerHTML = '<tr><td colspan="4" class="text-center" style="color: #fca5a5;">Gagal memuat data project.</td></tr>';
        }
    }
</script>
@endsection
