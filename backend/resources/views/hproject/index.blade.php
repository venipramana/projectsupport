@extends('layouts.app')

@section('title', 'Progress Project')
@section('header-title', 'Progress Project: ' . $project->project_name)

@section('custom-head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
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
    <form action="{{ route('hproject.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="idproject" value="{{ $project->id }}">
        
        <div class="form-grid">
            <div class="form-group full-width" style="background: rgba(117, 95, 62, 0.05); padding: 1rem; border-radius: 12px; border: 1px dashed var(--glass-border);">
                <label style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.4rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Upload File Progress (NDE) & Ekstrak Otomatis
                </label>
                <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; margin-top: 0.35rem;">
                    <input type="file" name="progress_file" id="progress_file" accept=".pdf,.png,.jpg,.jpeg,.docx" class="form-control" style="flex: 1; background: #ffffff;" onchange="handleNdeFileSelect(this)">
                    <button type="button" class="btn btn-secondary" onclick="triggerParseNde()" style="padding: 0.5rem 1rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Scan / Parse NDE
                    </button>
                </div>
                <div id="nde_status_box" style="display: none; margin-top: 0.5rem; font-size: 0.8rem; padding: 0.5rem 0.75rem; border-radius: 8px;"></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem;">
                    *Tipe file didukung: <strong>PDF (.pdf)</strong>, <strong>Gambar (.png, .jpg, .jpeg)</strong>, <strong>Word (.docx)</strong>. File otomatis di-scan & di-upload ke MinIO saat progress disimpan.
                </div>
            </div>

            <div class="form-group">
                <label style="font-weight: 600;">Status Project <span style="color:#dc2626;">*</span></label>
                <select name="rproject" class="form-control" required>
                    <option value="">-- Pilih Status Project --</option>
                    @foreach($rprojects as $rproj)
                        <option value="{{ $rproj->id }}">{{ $rproj->deskripsi }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label style="font-weight: 600;">Tanggal Perubahan Status <span style="color:#dc2626;">*</span></label>
                <input type="text" name="tanggal" id="entry_tanggal" class="form-control datepicker default-today" required>
            </div>
            
            <div class="form-group">
                <label style="font-weight: 600;">Persentase Progress (%)</label>
                <input type="number" name="progress" class="form-control" min="0" max="100" placeholder="0 - 100">
            </div>

            <div class="form-group full-width">
                <label style="font-weight: 600;">Catatan <span style="color:#dc2626;">*</span></label>
                <textarea name="catatan" id="entry_catatan" class="form-control" rows="3" maxlength="500" placeholder="Format otomatis: NDE [Nomor] tanggal [Tanggal] : [Perihal]" required></textarea>
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

<!-- Evidence Section: Project Files in MinIO -->
<div class="evidence-section master-section" style="margin-top: 2.5rem; margin-bottom: 2.5rem;">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.2rem; font-weight: 600; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                Evidence Project (MinIO Storage)
            </h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.3rem 0 0 0;">Folder Bucket: <span style="font-family: monospace; color: #8c734e;">ises / {{ $project->folder_evidence ?: ('evidence_project_' . $project->id) }}</span></p>
        </div>
        <button class="btn btn-primary" onclick="openUploadEvidenceModal()" style="padding: 0.6rem 1.2rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Upload Evidence
        </button>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Nama File</th>
                    <th style="width: 20%;">Ukuran</th>
                    <th style="width: 20%;">Terakhir Diperbarui</th>
                    <th style="width: 130px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evidenceFiles ?? [] as $ev)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: rgba(117, 95, 62, 0.15); color: var(--primary);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                            </span>
                            <span style="font-weight: 500; color: var(--text-main);">{{ $ev['name'] }}</span>
                        </div>
                    </td>
                    <td><span class="badge" style="background: rgba(117, 95, 62, 0.1); color: var(--text-muted); border: 1px solid var(--glass-border);">{{ $ev['size'] }}</span></td>
                    <td style="color: var(--text-muted); font-size: 0.9rem;">{{ $ev['date'] }}</td>
                    <td>
                        <div class="action-buttons" style="justify-content: center; gap: 0.5rem;">
                            <!-- View Button -->
                            <a href="{{ route('hproject.evidence.view', ['project_id' => $project->id, 'filename' => $ev['name']]) }}" target="_blank" class="btn-icon" title="View / Preview" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1); display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; text-decoration: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </a>
                            <!-- Download Button -->
                            <a href="{{ route('hproject.evidence.download', ['project_id' => $project->id, 'filename' => $ev['name']]) }}" class="btn-icon" title="Download" style="color: #10b981; background: rgba(16, 185, 129, 0.1); display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; text-decoration: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            </a>
                            <!-- Delete Button -->
                            <form action="{{ route('hproject.evidence.destroy', ['project_id' => $project->id, 'filename' => $ev['name']]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file evidence \'' + '{{ $ev['name'] }}' + '\'?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-delete" title="Delete" style="color: #ef4444; background: rgba(239, 68, 68, 0.1); display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: none; cursor: pointer;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 3.5rem 2rem; color: var(--text-muted);">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(117, 95, 62, 0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            <span>Belum ada file evidence untuk project ini di dalam bucket MinIO.</span>
                            <button type="button" onclick="openUploadEvidenceModal()" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.4rem 1rem; margin-top: 0.25rem;">
                                Upload Sekarang
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Upload Evidence Modal -->
<div id="uploadEvidenceModal" class="modal">
    <div class="modal-content" style="max-width: 550px; border: 1px solid var(--glass-border); border-radius: 20px; background: #ffffff; padding: 1.5rem 2rem;">
        <div class="modal-header" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-size: 1.25rem; margin: 0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Upload File Evidence
            </h2>
            <button type="button" class="btn-close" onclick="closeModal('uploadEvidenceModal')" style="background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('hproject.evidence.store', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="padding: 1.5rem 0;">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="color: var(--text-main); margin-bottom: 0.75rem; display: block; font-weight: 500;">Pilih File (Bisa upload banyak file sekaligus):</label>
                    <div class="file-drop-area" style="position: relative; padding: 2.5rem 1.5rem; border: 2px dashed rgba(117, 95, 62, 0.4); border-radius: 14px; background: rgba(244, 240, 234, 0.6); text-align: center; transition: all 0.2s ease; cursor: pointer;">
                        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#755f3e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem; opacity: 0.9;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                        <p style="margin: 0; color: var(--text-main); font-weight: 500; font-size: 1rem;">Klik daerah ini untuk memilih file dari komputer Anda</p>
                        <p style="margin: 0.4rem 0 0 0; color: var(--text-muted); font-size: 0.85rem;">Mendukung upload multiple file sekaligus (Max 50MB per file)</p>
                        <input type="file" name="evidences[]" multiple class="form-control" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" required onchange="updateFileList(this)">
                    </div>
                    <div id="selectedFilesList" style="margin-top: 1.2rem; font-size: 0.85rem; color: var(--text-muted); max-height: 160px; overflow-y: auto; background: rgba(255, 255, 255, 0.8); border: 1px solid var(--glass-border); border-radius: 10px; padding: 0.8rem 1rem; display: none;"></div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 1.2rem; border-top: 1px solid var(--glass-border); display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('uploadEvidenceModal')">Batal</button>
                <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Mulai Upload ke MinIO
                </button>
            </div>
        </form>
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
        background: rgba(255, 255, 255, 0.65);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05);
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
        background: rgba(255, 255, 255, 0.95);
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
        box-shadow: 0 6px 20px rgba(117, 95, 62, 0.35);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.8);
        color: var(--text-main);
        border: 1px solid var(--glass-border);
    }

    .btn-secondary:hover {
        background: rgba(117, 95, 62, 0.1);
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
        background: rgba(255, 255, 255, 0.75);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(117, 95, 62, 0.06);
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
        background: rgba(117, 95, 62, 0.07);
        font-weight: 600;
        color: var(--text-main);
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
        background: rgba(117, 95, 62, 0.04);
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
        background: rgba(117, 95, 62, 0.15);
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
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid var(--glass-border);
    }

    .btn-edit { color: #3b82f6; }
    .btn-edit:hover { background: rgba(59, 130, 246, 0.15); border-color: rgba(59, 130, 246, 0.3); }

    .btn-delete { color: #ef4444; }
    .btn-delete:hover { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); }

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
        width: 100%;
        max-width: 600px;
        box-shadow: 0 25px 50px -12px rgba(117, 95, 62, 0.25);
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

    function openUploadEvidenceModal() {
        openModal('uploadEvidenceModal');
    }

    function updateFileList(input) {
        const container = document.getElementById('selectedFilesList');
        if (input.files && input.files.length > 0) {
            let html = '<div style="font-weight: 600; color: #a78bfa; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg> File terpilih (' + input.files.length + ' file):</div>';
            html += '<ul style="margin: 0; padding-left: 1.2rem; list-style: disc;">';
            for (let i = 0; i < input.files.length; i++) {
                let sizeKB = (input.files[i].size / 1024).toFixed(1) + ' KB';
                if (input.files[i].size >= 1024 * 1024) {
                    sizeKB = (input.files[i].size / (1024 * 1024)).toFixed(2) + ' MB';
                }
                html += '<li style="margin-bottom: 0.25rem; color: var(--text-main); font-weight: 400;">' + input.files[i].name + ' <span style="color: #64748b; font-size: 0.8rem;">(' + sizeKB + ')</span></li>';
            }
            html += '</ul>';
            container.innerHTML = html;
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    }

    // NDE Auto Extraction Logic
    if (window['pdfjs-dist/build/pdf']) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    }

    function showNdeStatus(type, message) {
        const box = document.getElementById('nde_status_box');
        if (!box) return;
        box.style.display = 'block';
        if (type === 'loading') {
            box.style.background = 'rgba(2, 132, 199, 0.12)';
            box.style.border = '1px solid rgba(2, 132, 199, 0.3)';
            box.style.color = '#0284c7';
            box.innerHTML = '⌛ <em>Mengekstrak data NDE (Nomor, Tanggal, Perihal)...</em>';
        } else if (type === 'success') {
            box.style.background = 'rgba(16, 185, 129, 0.12)';
            box.style.border = '1px solid rgba(16, 185, 129, 0.3)';
            box.style.color = '#059669';
            box.innerHTML = '✔ ' + message;
        } else {
            box.style.background = 'rgba(217, 119, 6, 0.12)';
            box.style.border = '1px solid rgba(217, 119, 6, 0.3)';
            box.style.color = '#b45309';
            box.innerHTML = '⚠️ ' + message;
        }
    }

    async function handleNdeFileSelect(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];
        showNdeStatus('loading');

        const ext = file.name.split('.').pop().toLowerCase();

        if (ext === 'pdf') {
            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                let fullText = '';
                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const textContent = await page.getTextContent();
                    const pageText = textContent.items.map(item => item.str).join(' ');
                    fullText += pageText + ' ';
                }
                if (fullText && fullText.trim().length > 10) {
                    const parsed = processExtractedText(fullText, file.name);
                    // If client extraction found the NDE nomor, we apply and finish!
                    if (parsed && parsed.nomor) {
                        applyParsedNdeData(parsed.nomor, parsed.tanggalStr, parsed.perihal, parsed.formattedCatatan, parsed.formattedTanggal);
                        return;
                    }
                }
            } catch (e) {
                console.warn('PDF.js client extraction fallback to server:', e);
            }
        }

        // Fallback to Server AJAX Endpoint (extract_pdf.py / HprojectController)
        triggerParseNdeServer(file);
    }

    function triggerParseNde() {
        const input = document.getElementById('progress_file');
        if (!input.files || input.files.length === 0) {
            alert('Silakan pilih file progress NDE terlebih dahulu.');
            return;
        }
        handleNdeFileSelect(input);
    }

    function triggerParseNdeServer(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("hproject.parse_nde") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                applyParsedNdeData(data.nomor, data.tanggal, data.perihal, data.formatted_catatan, data.formatted_tanggal);
            } else {
                showNdeStatus('warning', 'File di-upload. Teks NDE tidak terdeteksi otomatis, silakan lengkapi Catatan NDE jika diperlukan.');
            }
        })
        .catch(err => {
            console.error(err);
            showNdeStatus('warning', 'File siap di-upload. Silakan lengkapi Catatan NDE jika diperlukan.');
        });
    }

    function processExtractedText(text, filename) {
        let nomor = '';
        let tanggalStr = '';
        let perihal = '';

        // 1. Extract Nomor (e.g. 74790/DS.02.03/VIII/2026 or 81519/DS.02/VIII/2026)
        let nomorMatch = text.match(/Nomor\s*[:\=]?\s*([0-9A-Za-z\/\.\_\-\s]+?)(?=\s+(?:Lampiran|Perihal|Kepada|Bandung|Jakarta|$))/i) ||
                         text.match(/Nomor\s*[:\=]?\s*([0-9A-Za-z\/\.\_\-]+)/i) ||
                         text.match(/No\.?\s*[:\=]?\s*([0-9A-Za-z\/\.\_\-]+)/i) ||
                         text.match(/([0-9]{3,}\s*\/\s*[A-Za-z0-9\.\_\-]+\s*\/\s*[IVXLCDM0-9]+\s*\/\s*[0-9]{2,4})/i) ||
                         text.match(/([0-9]{3,}\s*\/\s*[A-Za-z0-9\.\_\-]+\s*\/\s*[0-9]{2,4})/i) ||
                         text.match(/([0-9]{3,}\/[A-Za-z0-9\.\_\-]+)/i);
        if (nomorMatch) {
            let cand = nomorMatch[1].trim().replace(/\s+(?:Lampiran|Perihal|Kepada).*$/i, '').trim();
            if (!/^(?:19|20)\d{2}$/.test(cand)) {
                nomor = cand;
            }
        }

        // 2. Extract Tanggal (e.g. Bandung, 6 Agustus 2026 or 6 Agustus 2026)
        let tglMatch = text.match(/(?:Bandung|Jakarta|Surakarta|Semarang|Surabaya|Yogyakarta|[\w\s]+)?,\s*(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i) ||
                       text.match(/(\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4})/i) ||
                       text.match(/(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4})/);
        if (tglMatch) {
            tanggalStr = tglMatch[1].trim();
        }

        // 3. Extract Perihal
        let perihalMatch = text.match(/Perihal\s*[:\=]?\s*([^\r\n]+?)(?=\s+(?:Kepada|Menunjuk|Dengan|Sehubungan|Lampiran|Diberitahukan|Bandung|Jakarta|1\.|2\.|3\.|$))/i) ||
                           text.match(/Perihal\s*[:\=]?\s*([^\r\n]+)/i);
        if (perihalMatch) {
            perihal = perihalMatch[1].trim();
            perihal = perihal.replace(/\s+(?:Kepada|Bandung|Jakarta|Diberitahukan):?.*$/i, '').trim();
        }

        // Fallback for Nomor & Tanggal from filename if missing
        const cleanFilename = filename ? filename.replace(/\.[^/.]+$/, "") : "";
        if (!nomor && cleanFilename) {
            let fnNomor = cleanFilename.match(/([0-9]{3,}\s*\/\s*[A-Za-z0-9\.\_\-]+\s*\/\s*[IVXLCDM0-9]+\s*\/\s*[0-9]{2,4})/i) ||
                          cleanFilename.match(/([0-9]{3,}\s*\/\s*[A-Za-z0-9\.\_\-]+\s*\/\s*[0-9]{2,4})/i) ||
                          cleanFilename.match(/([0-9]{3,}\/[A-Za-z0-9\.\_\-]+)/i);
            if (fnNomor) {
                let cand = fnNomor[1].trim();
                if (!/^(?:19|20)\d{2}$/.test(cand)) {
                    nomor = cand;
                }
            }
        }
        if (!tanggalStr && cleanFilename) {
            let fnTgl = cleanFilename.match(/(\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4})/i);
            if (fnTgl) tanggalStr = fnTgl[1].trim();
        }

        // Convert tanggalStr to DMY format if possible, or fallback to today
        let formattedTanggal = '';
        if (tanggalStr) {
            const months = {
                'januari':'01', 'februari':'02', 'maret':'03', 'april':'04',
                'mei':'05', 'juni':'06', 'juli':'07', 'agustus':'08',
                'september':'09', 'oktober':'10', 'november':'11', 'desember':'12'
            };
            const dm = tanggalStr.match(/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/);
            if (dm) {
                const day = dm[1].padStart(2, '0');
                const mName = dm[2].toLowerCase();
                const year = dm[3];
                if (months[mName]) {
                    formattedTanggal = `${day}-${months[mName]}-${year}`;
                }
            }
        }

        if (!formattedTanggal) {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            formattedTanggal = `${day}-${month}-${year}`;
            if (!tanggalStr) {
                const monthsArr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                tanggalStr = `${today.getDate()} ${monthsArr[today.getMonth()]} ${year}`;
            }
        }

        let catatanParts = ['NDE'];
        if (nomor) {
            catatanParts.push(nomor);
        } else {
            catatanParts.push('[Nomor]');
        }
        if (tanggalStr) catatanParts.push(`tanggal ${tanggalStr}`);
        if (perihal) catatanParts.push(`: ${perihal}`);

        const formattedCatatan = catatanParts.join(' ');

        return { nomor, tanggalStr, perihal, formattedCatatan, formattedTanggal };
    }

    function applyParsedNdeData(nomor, tanggalStr, perihal, formattedCatatan, formattedTanggal) {
        const catatanEl = document.getElementById('entry_catatan');
        const tanggalEl = document.getElementById('entry_tanggal');

        if (catatanEl && formattedCatatan) {
            catatanEl.value = formattedCatatan;
            if (!nomor) {
                // Automatically select '[Nomor]' placeholder so user can immediately type the NDE number
                const idx = formattedCatatan.indexOf('[Nomor]');
                if (idx !== -1) {
                    setTimeout(() => {
                        catatanEl.focus();
                        catatanEl.setSelectionRange(idx, idx + 7);
                    }, 100);
                }
            }
        }

        if (tanggalEl && formattedTanggal) {
            tanggalEl.value = formattedTanggal;
            if (tanggalEl._flatpickr) {
                tanggalEl._flatpickr.setDate(formattedTanggal);
            }
        }

        if (nomor) {
            showNdeStatus('success', `Data NDE berhasil diekstrak! <strong>Nomor:</strong> ${nomor}, <strong>Tanggal:</strong> ${tanggalStr || '-'}, <strong>Perihal:</strong> ${perihal || '-'}`);
        } else {
            showNdeStatus('warning', `File PDF merupakan dokumen hasil scan (gambar). <strong>Nomor NDE</strong> tidak terdeteksi otomatis, silakan lengkapi Nomor NDE pada Catatan.`);
        }
    }
</script>
@endsection
