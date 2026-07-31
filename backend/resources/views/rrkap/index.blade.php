@extends('layouts.app')

@section('title', 'RKAP Administration')
@section('header-title', 'RKAP Administration')

@section('content')
<div class="actions-bar">
    <button class="btn btn-primary" onclick="openModal('createModal')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah RKAP
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

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID RKAP</th>
                <th>Tahun</th>
                <th>Nama RKAP</th>
                <th>Kode RKAP</th>
                <th>BSU RKAP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rrkaps as $rkap)
            <tr>
                <td>{{ $rkap->idrkap }}</td>
                <td>{{ $rkap->tahun_rkap }}</td>
                <td>{{ $rkap->nama_rkap }}</td>
                <td>{{ $rkap->kode_rkap }}</td>
                <td>Rp {{ number_format($rkap->bsu_rkap, 0, ',', '.') }}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon btn-edit" onclick="openEditModal({{ $rkap->idrkap }}, '{{ addslashes($rkap->tahun_rkap ?? '') }}', '{{ addslashes($rkap->nama_rkap ?? '') }}', '{{ addslashes($rkap->kode_rkap ?? '') }}', '{{ $rkap->bsu_rkap ?? 0 }}')" title="Edit">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <form action="{{ route('rrkap.destroy', $rkap->idrkap) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus RKAP ini?');">
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
                <td colspan="6" class="text-center" style="padding: 3rem; color: var(--text-muted);">
                    Belum ada data RKAP. Silakan tambah RKAP baru.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tambah RKAP</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form action="{{ route('rrkap.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>ID RKAP (Manual)</label>
                    <input type="number" name="idrkap" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tahun RKAP</label>
                    <input type="text" name="tahun_rkap" class="form-control" maxlength="4">
                </div>
                <div class="form-group">
                    <label>Nama RKAP</label>
                    <input type="text" name="nama_rkap" class="form-control">
                </div>
                <div class="form-group">
                    <label>Kode RKAP</label>
                    <input type="text" name="kode_rkap" class="form-control">
                </div>
                <div class="form-group">
                    <label>BSU RKAP (Rp)</label>
                    <input type="number" step="0.01" name="bsu_rkap" class="form-control">
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
            <h2>Edit RKAP</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>ID RKAP (Manual)</label>
                    <input type="number" id="edit_idrkap" name="idrkap" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tahun RKAP</label>
                    <input type="text" id="edit_tahun_rkap" name="tahun_rkap" class="form-control" maxlength="4">
                </div>
                <div class="form-group">
                    <label>Nama RKAP</label>
                    <input type="text" id="edit_nama_rkap" name="nama_rkap" class="form-control">
                </div>
                <div class="form-group">
                    <label>Kode RKAP</label>
                    <input type="text" id="edit_kode_rkap" name="kode_rkap" class="form-control">
                </div>
                <div class="form-group">
                    <label>BSU RKAP (Rp)</label>
                    <input type="number" step="0.01" id="edit_bsu_rkap" name="bsu_rkap" class="form-control">
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

    function openEditModal(idrkap, tahun_rkap, nama_rkap, kode_rkap, bsu_rkap) {
        // Set form action dynamically
        const form = document.getElementById('editForm');
        form.action = `/rrkap/${idrkap}`;

        // Populate fields
        document.getElementById('edit_idrkap').value = idrkap;
        document.getElementById('edit_tahun_rkap').value = tahun_rkap;
        document.getElementById('edit_nama_rkap').value = nama_rkap;
        document.getElementById('edit_kode_rkap').value = kode_rkap;
        document.getElementById('edit_bsu_rkap').value = bsu_rkap;

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
