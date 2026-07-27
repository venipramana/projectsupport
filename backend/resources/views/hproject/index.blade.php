@extends('layouts.app')

@section('title', 'Progress Project')
@section('header-title', 'Progress Project: ' . $project->project_name)

@section('custom-head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="actions-bar">
    <a href="{{ route('project.index') }}" class="btn btn-secondary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali ke Data Project
    </a>
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

<!-- Master Section: Form Entri Progress -->
<div class="master-section">
    <div class="section-header">
        <h2>Entri Progress Baru</h2>
    </div>
    <form action="{{ route('hproject.store') }}" method="POST">
        @csrf
        <input type="hidden" name="idproject" value="{{ $project->id }}">
        
        <div class="form-grid">
            <div class="form-group">
                <label>Status Project</label>
                <select name="rproject" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    @foreach($rprojects as $rproj)
                        <option value="{{ $rproj->id }}">{{ $rproj->deskripsi }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Tanggal Perubahan Status</label>
                <input type="text" name="tanggal" class="form-control datepicker default-today" required>
            </div>
            
            <div class="form-group">
                <label>Persentase Progress (%)</label>
                <input type="number" name="progress" class="form-control" min="0" max="100" placeholder="0 - 100">
            </div>

            <div class="form-group full-width">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="3" maxlength="150" required></textarea>
            </div>
        </div>
        
        <div style="text-align: right; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Simpan Progress
            </button>
        </div>
    </form>
</div>

<!-- Detail Section: History Progress -->
<div class="detail-section">
    <div class="section-header">
        <h2>Riwayat Progress</h2>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Status Project</th>
                    <th>Progress (%)</th>
                    <th>Catatan</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hprojects as $hproj)
                <tr>
                    <td>{{ $hproj->tanggal ? date('d-m-Y', strtotime($hproj->tanggal)) : '-' }}</td>
                    <td>
                        @if($hproj->rproject_rel)
                            <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #c4b5fd;">{{ $hproj->rproject_rel->deskripsi }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ $hproj->progress ?? 0 }}%;"></div>
                            <span class="progress-text">{{ $hproj->progress ?? 0 }}%</span>
                        </div>
                    </td>
                    <td>{{ $hproj->catatan }}</td>
                    <td>
                        <div class="action-buttons">
                            @php
                                $hprojData = json_encode([
                                    'id' => $hproj->id,
                                    'rproject' => $hproj->rproject,
                                    'tanggal' => $hproj->tanggal ? date('d-m-Y', strtotime($hproj->tanggal)) : '',
                                    'progress' => $hproj->progress,
                                    'catatan' => $hproj->catatan
                                ]);
                            @endphp
                            <button class="btn-icon btn-edit" data-hproj="{{ $hprojData }}" onclick="openEditModal(this.dataset.hproj)" title="Edit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('hproject.destroy', $hproj->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus record progress ini?');">
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
                    <td colspan="5" class="text-center" style="padding: 3rem; color: var(--text-muted);">
                        Belum ada data progress untuk project ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Progress</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Status Project</label>
                        <select id="edit_rproject" name="rproject" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            @foreach($rprojects as $rproj)
                                <option value="{{ $rproj->id }}">{{ $rproj->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Perubahan Status</label>
                        <input type="text" id="edit_tanggal" name="tanggal" class="form-control datepicker" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Persentase Progress (%)</label>
                        <input type="number" id="edit_progress" name="progress" class="form-control" min="0" max="100">
                    </div>

                    <div class="form-group full-width">
                        <label>Catatan</label>
                        <textarea id="edit_catatan" name="catatan" class="form-control" rows="3" maxlength="150" required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Update Progress</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('custom-styles')
<style>
    .actions-bar {
        margin-bottom: 2rem;
    }

    .master-section {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
    }
    
    .detail-section {
        margin-bottom: 2rem;
    }

    .section-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--glass-border);
    }

    .section-header h2 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--primary);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem 1.5rem;
    }
    
    .full-width {
        grid-column: span 3;
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

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
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
    
    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        position: relative;
        margin-top: 10px;
        margin-bottom: 5px;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--secondary), var(--primary));
        border-radius: 10px;
    }
    
    .progress-text {
        font-size: 0.75rem;
        position: absolute;
        top: -15px;
        right: 0;
        color: var(--text-muted);
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
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--glass-border);
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }
</style>
@endsection

@section('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
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
            if(!input.value) {
                input.value = formattedToday;
                if (input._flatpickr) {
                    input._flatpickr.setDate(formattedToday);
                }
            }
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
                console.error("Could not parse hproject data JSON");
                return;
            }
        }

        const form = document.getElementById('editForm');
        form.action = `/hproject/${data.id}`;

        document.getElementById('edit_rproject').value = data.rproject || '';
        
        const dateInput = document.getElementById('edit_tanggal');
        dateInput.value = data.tanggal || '';
        if (dateInput._flatpickr) {
            dateInput._flatpickr.setDate(data.tanggal || '');
        }
        
        document.getElementById('edit_progress').value = data.progress || 0;
        document.getElementById('edit_catatan').value = data.catatan || '';

        openModal('editModal');
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }
</script>
@endsection
