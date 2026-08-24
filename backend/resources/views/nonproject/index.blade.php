@extends('layouts.app')

@section('title', 'Non Project Administration')
@section('header-title', 'Non Project Administration')

@section('custom-head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('content')
<div class="actions-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <form action="{{ route('nonproject.index') }}" method="GET" style="display: flex; gap: 0.75rem; align-items: center;">
        <input type="text" name="search" class="form-control" placeholder="Cari kegiatan, lokasi, PIC, atau catatan..." value="{{ $searchKeyword }}" style="width: 320px;">
        <button type="submit" class="btn btn-secondary" style="padding: 0.65rem 1.25rem;">Cari</button>
        @if($searchKeyword)
            <a href="{{ route('nonproject.index') }}" class="btn btn-secondary" style="padding: 0.65rem 1rem;">Reset</a>
        @endif
    </form>

    <button class="btn btn-primary" onclick="openModal('createModal')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Non Project
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

<div class="master-section" style="margin-bottom: 2rem;">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>PIC</th>
                    <th>Catatan</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nonprojects as $index => $item)
                @php
                    // Format tanggal yyyymmdd to dd-mm-yyyy for display
                    $tglDisplay = '-';
                    if (!empty($item->tanggal) && strlen($item->tanggal) == 8) {
                        $tglDisplay = substr($item->tanggal, 6, 2) . '-' . substr($item->tanggal, 4, 2) . '-' . substr($item->tanggal, 0, 4);
                    } elseif (!empty($item->tanggal)) {
                        $tglDisplay = $item->tanggal;
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span style="font-weight: 600; color: var(--primary);">{{ $tglDisplay }}</span></td>
                    <td style="font-weight: 500;">{{ $item->kegiatan }}</td>
                    <td>{{ $item->lokasi ?? '-' }}</td>
                    <td>{{ $item->pic ?? '-' }}</td>
                    <td style="font-size: 0.85rem; color: var(--text-muted);">{{ $item->catatan ?? '-' }}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-edit" onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->kegiatan ?? '') }}', '{{ $tglDisplay }}', '{{ addslashes($item->lokasi ?? '') }}', '{{ addslashes($item->pic ?? '') }}', '{{ addslashes($item->catatan ?? '') }}')" title="Edit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('nonproject.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
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
                        Belum ada data Non Project. Silakan tambah data baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($nonprojects->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Menampilkan {{ $nonprojects->firstItem() ?? 0 }} - {{ $nonprojects->lastItem() ?? 0 }} dari {{ $nonprojects->total() }} total data
        </div>
        <div>
            {{ $nonprojects->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif
</div>

<!-- Create Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tambah Non Project</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form action="{{ route('nonproject.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem 1.5rem;">
                    <div class="form-group">
                        <label>Tanggal *</label>
                        <input type="text" name="tanggal" class="form-control datepicker" required placeholder="dd-mm-yyyy">
                    </div>
                    <div class="form-group">
                        <label>PIC</label>
                        <select name="pic" class="form-control">
                            <option value="">-- Pilih PIC --</option>
                            @foreach($penggunas as $p)
                                <option value="{{ $p->nama }}">{{ $p->nama }} ({{ $p->idpengguna }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Kegiatan *</label>
                        <input type="text" name="kegiatan" class="form-control" required placeholder="Deskripsi Kegiatan">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Lokasi kegiatan">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
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
            <h2>Edit Non Project</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem 1.5rem;">
                    <div class="form-group">
                        <label>Tanggal *</label>
                        <input type="text" id="edit_tanggal" name="tanggal" class="form-control datepicker" required placeholder="dd-mm-yyyy">
                    </div>
                    <div class="form-group">
                        <label>PIC</label>
                        <select id="edit_pic" name="pic" class="form-control">
                            <option value="">-- Pilih PIC --</option>
                            @foreach($penggunas as $p)
                                <option value="{{ $p->nama }}">{{ $p->nama }} ({{ $p->idpengguna }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Kegiatan *</label>
                        <input type="text" id="edit_kegiatan" name="kegiatan" class="form-control" required placeholder="Deskripsi Kegiatan">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Lokasi</label>
                        <input type="text" id="edit_lokasi" name="lokasi" class="form-control" placeholder="Lokasi kegiatan">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Catatan</label>
                        <textarea id="edit_catatan" name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
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

@section('custom-styles')
<style>
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

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
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

    .table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .table-container::-webkit-scrollbar-track {
        background: rgba(117, 95, 62, 0.05);
        border-radius: 10px;
    }
    .table-container::-webkit-scrollbar-thumb {
        background: rgba(117, 95, 62, 0.25);
        border-radius: 10px;
    }
    .table-container::-webkit-scrollbar-thumb:hover {
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
        width: 90%;
        max-width: 1100px;
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
        padding: 1.5rem 2rem;
        overflow-y: auto;
    }

    .modal-footer {
        padding: 1.25rem 2rem;
        border-top: 1px solid var(--glass-border);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
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
</style>
@endsection

@section('custom-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr Datepicker with dd-mm-yyyy format
        flatpickr('.datepicker', {
            dateFormat: 'd-m-Y',
            allowInput: true,
            defaultDate: 'today'
        });
    });

    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openEditModal(id, kegiatan, tanggal, lokasi, pic, catatan) {
        const form = document.getElementById('editForm');
        form.action = `/nonproject/${id}`;

        document.getElementById('edit_kegiatan').value = kegiatan;
        document.getElementById('edit_lokasi').value = lokasi;
        document.getElementById('edit_pic').value = pic;
        document.getElementById('edit_catatan').value = catatan;

        // Set datepicker value
        const editDateElem = document.getElementById('edit_tanggal');
        if (editDateElem._flatpickr) {
            editDateElem._flatpickr.setDate(tanggal || 'today');
        } else {
            editDateElem.value = tanggal;
        }

        openModal('editModal');
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }
</script>
@endsection
