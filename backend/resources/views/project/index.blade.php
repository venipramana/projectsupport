@extends('layouts.app')

@section('title', 'Data Project')
@section('header-title', 'Data Project')

@section('custom-head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('custom-styles')
<style>
    .actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filter-group {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-item label {
        color: var(--text-muted);
        font-weight: 500;
        font-size: 0.9rem;
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

    .master-section {
        height: 95vh;
        overflow-y: auto;
        padding-right: 10px;
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

    .btn-progress { color: #10b981; }
    .btn-progress:hover { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); }

    .btn-delete { color: #f87171; }
    .btn-delete:hover { background: rgba(248, 113, 113, 0.15); border-color: rgba(248, 113, 113, 0.3); }

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
        max-width: 900px;
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
    
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    .modal-body::-webkit-scrollbar-track {
        background: rgba(117, 95, 62, 0.05);
        border-radius: 4px;
    }
    .modal-body::-webkit-scrollbar-thumb {
        background: rgba(117, 95, 62, 0.25);
        border-radius: 4px;
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--glass-border);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 0.5rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem 1.5rem;
    }
    
    .full-width {
        grid-column: span 3;
    }
    
    .two-thirds {
        grid-column: span 2;
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

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(117, 95, 62, 0.2);
    }
</style>
@endsection

@section('content')

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

<div class="master-section">
    <div class="actions-bar">
        <form action="{{ route('project.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <input type="text" name="search" class="form-control" placeholder="Search Project Name..." value="{{ $searchKeyword }}" style="width: 250px;">
            </div>
            <div class="filter-item">
                <label>Direktorat:</label>
                <select name="filter_direktorat" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($direktorats as $dir)
                        <option value="{{ $dir->id }}" {{ $selectedDirektorat == $dir->id ? 'selected' : '' }}>{{ $dir->deskripsi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Status:</label>
                <select name="filter_status" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($rprojects as $rproj)
                        <option value="{{ $rproj->id }}" {{ $selectedStatus == $rproj->id ? 'selected' : '' }}>{{ $rproj->deskripsi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Tahun:</label>
                <select name="filter_tahun" class="form-control" style="width: 120px;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ $selectedTahun == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary" style="padding: 0.6rem 1rem;">Go</button>
        </form>

        <button class="btn btn-primary" onclick="openModal('createModal')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Project
        </button>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="display:none;">ID</th>
                    <th>Nama Project</th>
                    <th>Tanggal</th>
                    <th>Direktorat</th>
                    <th>Status</th>
                    <th>RKAP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $proj)
                <tr>
                    <td style="display:none;">{{ $proj->id }}</td>
                    <td>
                        <a href="{{ route('hproject.index', $proj->id) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;" title="Buka Progress Project">
                            {{ $proj->project_name }}
                        </a>
                    </td>
                    <td>{{ $proj->tanggal ? date('d-m-Y', strtotime($proj->tanggal)) : '-' }}</td>
                    <td>{{ $proj->direktorat_rel ? $proj->direktorat_rel->deskripsi : '-' }}</td>
                    <td>{{ $proj->rproject_relation ? $proj->rproject_relation->deskripsi : '-' }}</td>
                    <td>{{ $proj->rkap_rel ? $proj->rkap_rel->kode_rkap . ' - ' . $proj->rkap_rel->nama_rkap : '-' }}</td>
                    <td>
                        <div class="action-buttons">
                            <!-- Serialize data for JS function -->
                            @php
                                $projData = json_encode([
                                    'id' => $proj->id,
                                    'project_name' => $proj->project_name,
                                    'direktorat' => $proj->direktorat,
                                    'bagian' => $proj->bagian,
                                    'pic_name' => $proj->pic_name,
                                    'no_surat' => $proj->no_surat,
                                    'tanggal' => $proj->tanggal ? date('d-m-Y', strtotime($proj->tanggal)) : '',
                                    'tanggal_awal' => $proj->tanggal_awal ? date('d-m-Y', strtotime($proj->tanggal_awal)) : '',
                                    'tanggal_akhir' => $proj->tanggal_akhir ? date('d-m-Y', strtotime($proj->tanggal_akhir)) : '',
                                    'catatan' => $proj->catatan,
                                    'rproject' => $proj->rproject,
                                    'leadby' => $proj->leadby,
                                    'supportby' => $proj->supportby,
                                    'tgl_update' => $proj->tgl_update ? date('d-m-Y', strtotime($proj->tgl_update)) : '',
                                    'vul_passed' => $proj->vul_passed,
                                    'id_catalog' => $proj->id_catalog,
                                    'catalog_version' => $proj->catalog_version,
                                    'byvendor' => $proj->byvendor,
                                    'vendorname' => $proj->vendorname,
                                    'rkap' => $proj->rkap,
                                    'bsurkap' => $proj->bsurkap ? number_format($proj->bsurkap, 0, '', '.') : ''
                                ]);
                            @endphp
                            <a href="{{ route('hproject.index', $proj->id) }}" class="btn-icon btn-progress" title="Progress Project" style="color: #10b981;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                            </a>
                            <button class="btn-icon btn-edit" data-project="{{ $projData }}" onclick="openEditModal(this.dataset.project)" title="Edit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('project.destroy', $proj->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus project ini?');">
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
                        Belum ada data Project.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tambah Project</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form action="{{ route('project.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group two-thirds">
                        <label>Nama Project *</label>
                        <input type="text" name="project_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" name="tanggal" class="form-control datepicker default-today">
                    </div>

                    <div class="form-group">
                        <label>Direktorat</label>
                        <select name="direktorat" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($direktorats as $dir)
                                <option value="{{ $dir->id }}">{{ $dir->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Project</label>
                        <select name="rproject" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($rprojects as $rproj)
                                <option value="{{ $rproj->id }}">{{ $rproj->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Vulnerability Passed</label>
                        <select name="vul_passed" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($vulnerabilities as $v)
                                <option value="{{ $v->id }}">{{ $v->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Bagian</label>
                        <input type="text" name="bagian" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>PIC Name</label>
                        <input type="text" name="pic_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>No Surat</label>
                        <input type="text" name="no_surat" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Awal</label>
                        <input type="text" name="tanggal_awal" class="form-control datepicker default-today">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Akhir</label>
                        <input type="text" name="tanggal_akhir" class="form-control datepicker default-today">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Update</label>
                        <input type="text" name="tgl_update" class="form-control datepicker default-today">
                    </div>

                    <div class="form-group">
                        <label>Lead By</label>
                        <select name="leadby" class="form-control">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach($penggunas as $p)
                                <option value="{{ $p->idpengguna }}">{{ $p->nama }} ({{ $p->idpengguna }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Support By</label>
                        <select name="supportby" class="form-control">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach($penggunas as $p)
                                <option value="{{ $p->idpengguna }}">{{ $p->nama }} ({{ $p->idpengguna }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>By Vendor</label>
                        <select name="byvendor" class="form-control">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>

                    <div class="form-group two-thirds">
                        <label>Catalog</label>
                        <select name="id_catalog" class="form-control">
                            <option value="">-- Pilih Catalog --</option>
                            @foreach($rcatalogs as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->description }} - {{ $cat->current_version }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catalog Version</label>
                        <input type="text" name="catalog_version" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Vendor Name</label>
                        <input type="text" name="vendorname" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>RKAP</label>
                        <select name="rkap" class="form-control">
                            <option value="">-- Pilih RKAP --</option>
                            @foreach($rrkaps as $rkap)
                                <option value="{{ $rkap->nama_rkap }}">{{ $rkap->kode_rkap }} - {{ $rkap->nama_rkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>BSU RKAP (Rp)</label>
                        <input type="text" name="bsurkap" class="form-control currency-input">
                    </div>

                    <div class="form-group full-width">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>
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
            <h2>Edit Project</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group two-thirds">
                        <label>Nama Project *</label>
                        <input type="text" id="edit_project_name" name="project_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" id="edit_tanggal" name="tanggal" class="form-control datepicker">
                    </div>

                    <div class="form-group">
                        <label>Direktorat</label>
                        <select id="edit_direktorat" name="direktorat" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($direktorats as $dir)
                                <option value="{{ $dir->id }}">{{ $dir->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status Project</label>
                        <select id="edit_rproject" name="rproject" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($rprojects as $rproj)
                                <option value="{{ $rproj->id }}">{{ $rproj->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Vulnerability Passed</label>
                        <select id="edit_vul_passed" name="vul_passed" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($vulnerabilities as $v)
                                <option value="{{ $v->id }}">{{ $v->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Bagian</label>
                        <input type="text" id="edit_bagian" name="bagian" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>PIC Name</label>
                        <input type="text" id="edit_pic_name" name="pic_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>No Surat</label>
                        <input type="text" id="edit_no_surat" name="no_surat" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Awal</label>
                        <input type="text" id="edit_tanggal_awal" name="tanggal_awal" class="form-control datepicker">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Akhir</label>
                        <input type="text" id="edit_tanggal_akhir" name="tanggal_akhir" class="form-control datepicker">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Update</label>
                        <input type="text" id="edit_tgl_update" name="tgl_update" class="form-control datepicker">
                    </div>

                    <div class="form-group">
                        <label>Lead By</label>
                        <select id="edit_leadby" name="leadby" class="form-control">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach($penggunas as $p)
                                <option value="{{ $p->idpengguna }}">{{ $p->nama }} ({{ $p->idpengguna }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Support By</label>
                        <select id="edit_supportby" name="supportby" class="form-control">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach($penggunas as $p)
                                <option value="{{ $p->idpengguna }}">{{ $p->nama }} ({{ $p->idpengguna }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>By Vendor</label>
                        <select id="edit_byvendor" name="byvendor" class="form-control">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>

                    <div class="form-group two-thirds">
                        <label>Catalog</label>
                        <select id="edit_id_catalog" name="id_catalog" class="form-control">
                            <option value="">-- Pilih Catalog --</option>
                            @foreach($rcatalogs as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->description }} - {{ $cat->current_version }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catalog Version</label>
                        <input type="text" id="edit_catalog_version" name="catalog_version" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Vendor Name</label>
                        <input type="text" id="edit_vendorname" name="vendorname" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>RKAP</label>
                        <select id="edit_rkap" name="rkap" class="form-control">
                            <option value="">-- Pilih RKAP --</option>
                            @foreach($rrkaps as $rkap)
                                <option value="{{ $rkap->nama_rkap }}">{{ $rkap->kode_rkap }} - {{ $rkap->nama_rkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>BSU RKAP (Rp)</label>
                        <input type="text" id="edit_bsurkap" name="bsurkap" class="form-control currency-input">
                    </div>

                    <div class="form-group full-width">
                        <label>Catatan</label>
                        <textarea id="edit_catatan" name="catatan" class="form-control" rows="2"></textarea>
                    </div>
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

@section('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize datepickers
    flatpickr(".datepicker", {
        dateFormat: "d-m-Y",
        allowInput: true
    });

    // Default date fields to current date
    document.addEventListener("DOMContentLoaded", function() {
        const defaultDateInputs = document.querySelectorAll('.default-today');
        const today = new Date();
        const yyyy = today.getFullYear();
        let mm = today.getMonth() + 1;
        let dd = today.getDate();
        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;
        const formattedToday = dd + '-' + mm + '-' + yyyy;
        
        defaultDateInputs.forEach(input => {
            input.value = formattedToday;
            // Also update flatpickr instance if already bound
            if (input._flatpickr) {
                input._flatpickr.setDate(formattedToday);
            }
        });
    });

    // Thousand separator for currency inputs
    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            value = value.replace(/\D/g, ''); // Remove non-digits
            if (value !== '') {
                value = parseInt(value, 10).toLocaleString('id-ID'); // Format with dots
            }
            e.target.value = value;
        });
    });

    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openEditModal(dataStr) {
        let data = dataStr;
        if (typeof dataStr === 'string') {
            try {
                data = JSON.parse(dataStr);
            } catch (e) {
                console.error("Could not parse project data JSON");
                return;
            }
        }

        // Set form action dynamically
        const form = document.getElementById('editForm');
        form.action = `/project/${data.id}`;

        // Populate fields
        document.getElementById('edit_project_name').value = data.project_name || '';
        document.getElementById('edit_tanggal').value = data.tanggal || '';
        document.getElementById('edit_direktorat').value = data.direktorat || '';
        document.getElementById('edit_rproject').value = data.rproject || '';
        document.getElementById('edit_bagian').value = data.bagian || '';
        document.getElementById('edit_pic_name').value = data.pic_name || '';
        document.getElementById('edit_no_surat').value = data.no_surat || '';
        document.getElementById('edit_tanggal_awal').value = data.tanggal_awal || '';
        document.getElementById('edit_tanggal_akhir').value = data.tanggal_akhir || '';
        document.getElementById('edit_leadby').value = data.leadby || '';
        document.getElementById('edit_supportby').value = data.supportby || '';
        document.getElementById('edit_vul_passed').value = data.vul_passed || '';
        document.getElementById('edit_tgl_update').value = data.tgl_update || '';
        document.getElementById('edit_id_catalog').value = data.id_catalog || '';
        document.getElementById('edit_catalog_version').value = data.catalog_version || '';
        document.getElementById('edit_byvendor').value = data.byvendor !== null ? data.byvendor : '0';
        document.getElementById('edit_vendorname').value = data.vendorname || '';
        document.getElementById('edit_rkap').value = data.rkap || '';
        document.getElementById('edit_bsurkap').value = data.bsurkap || '';
        document.getElementById('edit_catatan').value = data.catatan || '';

        // Open Modal
        openModal('editModal');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }
</script>
@endsection
