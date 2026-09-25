@extends('layouts.app')

@section('title', 'Backup Database')
@section('header-title', 'Backup Database')

@section('custom-styles')
<style>
    .backup-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .info-card {
        background: #ffffff;
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 14px rgba(117, 95, 62, 0.05);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .info-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: rgba(117, 95, 62, 0.1);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .info-content h4 {
        margin: 0;
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-content p {
        margin: 0.35rem 0 0 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
    }
    .card-section {
        background: #ffffff;
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.75rem;
        box-shadow: 0 4px 16px rgba(117, 95, 62, 0.06);
        margin-bottom: 2rem;
    }
    .card-section h3 {
        margin-top: 0;
        margin-bottom: 1.25rem;
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }
    .form-group-flex {
        flex: 1;
        min-width: 250px;
    }
    .form-group-flex label {
        display: block;
        margin-bottom: 0.45rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
    }
    .form-control-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        background: #fbf9f6;
        border: 1px solid rgba(117, 95, 62, 0.25);
        color: var(--text-main);
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }
    .form-control-input:focus {
        outline: none;
        border-color: var(--primary);
        background: #ffffff;
    }
    .form-checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-main);
        cursor: pointer;
        user-select: none;
        padding-bottom: 0.6rem;
    }
    .btn-backup {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #ffffff;
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: opacity 0.2s, transform 0.1s;
    }
    .btn-backup:hover {
        opacity: 0.92;
    }
    .btn-backup:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .btn-action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-action-icon.download {
        color: #0284c7;
        background: rgba(2, 132, 199, 0.08);
        border-color: rgba(2, 132, 199, 0.2);
    }
    .btn-action-icon.download:hover {
        background: rgba(2, 132, 199, 0.18);
    }
    .btn-action-icon.delete {
        color: #dc2626;
        background: rgba(220, 38, 38, 0.08);
        border-color: rgba(220, 38, 38, 0.2);
    }
    .btn-action-icon.delete:hover {
        background: rgba(220, 38, 38, 0.18);
    }
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .alert-success {
        background: rgba(16, 185, 129, 0.12);
        color: #065f46;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .alert-danger {
        background: rgba(239, 68, 68, 0.12);
        color: #991b1b;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
</style>
@endsection

@section('content')
<div style="max-width: 1200px; margin: 0 auto; width: 100%;">

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Info Overview Cards -->
    <div class="backup-grid">
        <div class="info-card">
            <div class="info-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
            </div>
            <div class="info-content">
                <h4>Database Aktif</h4>
                <p>{{ $dbName }}</p>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Host: {{ $dbHost }}</small>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div class="info-content">
                <h4>Jadwal Otomatis</h4>
                <p>15:00 WIB</p>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Setiap Hari (Scheduler)</small>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            </div>
            <div class="info-content">
                <h4>Email Tujuan</h4>
                <p style="font-size: 0.95rem; word-break: break-all;">{{ $defaultEmail }}</p>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Default Recipient</small>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
            <div class="info-content">
                <h4>Total Arsip Tersimpan</h4>
                <p>{{ count($backups) }} File</p>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Ukuran Total: {{ $totalSizeFormatted }}</small>
            </div>
        </div>
    </div>

    <!-- Manual Backup Card -->
    <div class="card-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
            Eksekusi Backup Manual
        </h3>
        <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: -0.5rem; margin-bottom: 1.5rem;">
            Klik tombol di bawah ini untuk membuat dump database MySQL sekarang dan mengirimkannya langsung ke email Anda.
        </p>

        <form action="{{ route('backup.run') }}" method="POST" id="form-run-backup">
            @csrf
            <div class="form-row">
                <div class="form-group-flex">
                    <label for="input-email">Kirimkan Ke Alamat Email</label>
                    <input type="email" name="email" id="input-email" class="form-control-input" value="{{ $defaultEmail }}" required>
                </div>
                <div>
                    <label class="form-checkbox-label">
                        <input type="checkbox" name="send_email" value="1" checked style="width: 18px; height: 18px; accent-color: var(--primary);">
                        Kirim file backup ke email
                    </label>
                </div>
                <div>
                    <button type="submit" class="btn-backup" id="btn-trigger-backup">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                        <span id="btn-backup-text">Backup & Kirim Sekarang</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Backups Table -->
    <div class="card-section">
        <h3>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            Daftar File Backup di Server
        </h3>

        <div class="table-container" style="overflow-x: auto;">
            <table class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama File Backup (.sql.gz)</th>
                        <th style="width: 140px;">Ukuran File</th>
                        <th style="width: 200px;">Waktu Backup</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $index => $b)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.6rem;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.9rem; font-weight: 500;">{{ $b['filename'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(117, 95, 62, 0.1); color: var(--text-main); font-weight: 600;">
                                    {{ $b['size_formatted'] }}
                                </span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.88rem;">{{ $b['created_at'] }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                    <a href="{{ route('backup.download', $b['filename']) }}" class="btn-action-icon download" title="Download File">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    </a>
                                    <form action="{{ route('backup.destroy', $b['filename']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file backup {{ $b['filename'] }}?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon delete" title="Hapus File">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 0.5rem; opacity: 0.5;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <p style="margin: 0; font-size: 0.95rem;">Belum ada arsip backup database yang tersimpan di server.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom-scripts')
<script>
    document.getElementById('form-run-backup').addEventListener('submit', function() {
        const btn = document.getElementById('btn-trigger-backup');
        const text = document.getElementById('btn-backup-text');
        btn.disabled = true;
        text.innerText = 'Memproses Backup... Mohon tunggu';
    });
</script>
@endsection

