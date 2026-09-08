@extends('layouts.app')

@section('title', 'All Project')
@section('header-title', 'Laporan Project')

@section('content')

<!-- Card Informasi Total -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
        </div>
        <div class="summary-content">
            <h3>Total Project</h3>
            <p>{{ number_format($total_projects, 0, ',', '.') }}</p>
        </div>
    </div>
    <!--div class="summary-card">
        <div class="summary-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
        <div class="summary-content">
            <h3>Total BSU RKAP</h3>
            <p>Rp {{ number_format($total_bsurkap, 0, ',', '.') }}</p>
        </div>
    </div-->
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form action="{{ route('laporan.progress') }}" method="GET" class="filter-form">
        
        <div class="form-group" style="flex: 1 1 100%;">
            <label>Cari Project</label>
            <input type="text" name="search" class="form-control" placeholder="Ketik nama project lalu tekan Enter..." value="{{ request('search') }}">
        </div>

        <div class="form-group">
            <label>Direktorat</label>
            <select name="direktorat" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Direktorat</option>
                @foreach($direktoratOptions as $dir)
                    <option value="{{ $dir->deskripsi }}" {{ request('direktorat') == $dir->deskripsi ? 'selected' : '' }}>
                        {{ $dir->deskripsi }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Progress (Status)</label>
            <select name="progress" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach($progressOptions as $prog)
                    <option value="{{ $prog->id }}" {{ request('progress') == $prog->id ? 'selected' : '' }}>
                        {{ $prog->deskripsi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Tahun (Tanggal Awal)</label>
            <select name="tahun" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @foreach($tahunOptions as $thn)
                    <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>
                        {{ $thn }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Bulan (Tanggal Awal)</label>
            <select name="bulan" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @foreach($bulanOptions as $num => $name)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>RKAP</label>
            <select name="rkap" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Pembiayaan</option>
                @foreach($rkapOptions as $rk)
                    <option value="{{ $rk->nama_rkap }}" {{ request('rkap') == $rk->nama_rkap ? 'selected' : '' }}>
                        {{ $rk->nama_rkap }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Lead By</label>
            <select name="pic" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Lead By</option>
                @foreach($picOptions as $pc)
                    <option value="{{ $pc->nama }}" {{ request('pic') == $pc->nama ? 'selected' : '' }}>
                        {{ $pc->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-actions">
            <a href="{{ route('laporan.progress') }}" class="btn btn-secondary">Reset Filter</a>
            <button type="button" class="btn btn-success" onclick="exportLaporanProgressExcel()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Export Excel
            </button>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="table-container">
    <div class="table-responsive">
        <table class="data-table" id="laporanTable">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>ID</th>
                    <th>ID Project</th>
                    <th>Nama Project</th>
                    <th>Direktorat</th>
                    <th>Bagian</th>
                    <th>PIC Name</th>
                    <th>No Surat</th>
                    <th>Tgl Surat</th>                    
                    <th>Tgl Awal</th>
                    <th>Tgl Akhir</th>
                    <th>Rproject</th>
                    <th>Status Project</th>
                    <th>Assign To</th>
                    <th>Assign To Name</th>
                    <th>Support To</th>
                    <th>Support To Name</th>
                    <th>Telpon</th>
                    <th>Tgl Update</th>
                    <th>Token</th>
                    <th>Durasi Permintaan</th>
                    <th>Vul Passed</th>
                    <th>ID Catalog</th>
                    <th>Nama Katalog</th>
                    <th>Catalog Version</th>
                    <th>By Vendor</th>
                    <th>Vendor Name</th>
                    <th>URL Base</th>
                    <th>RKAP</th>
                    <th style="text-align: right;">BSU RKAP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                <tr>
                    <td class="text-center">
                        @if($row->icon)
                            <img src="{{ asset('icons/' . $row->icon) }}" alt="{{ $row->status_project }}" style="width: 24px; height: 24px; object-fit: contain;" title="{{ $row->status_project }}">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->idproject }}</td>
                    <td>
                        @if(Auth::user() && Auth::user()->levelpengguna == 3)
                            @if($row->assign_to == Auth::user()->idpengguna)
                                <a href="{{ route('hproject.index', $row->idproject) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;" title="Buka Historis Progress">
                                    {{ $row->project_name }}
                                </a>
                            @else
                                <span style="color: var(--text-muted); cursor: not-allowed; font-weight: 500;" title="Link tidak aktif (Project ini ditugaskan ke PIC lain)">
                                    {{ $row->project_name }}
                                </span>
                            @endif
                        @else
                            <a href="{{ route('hproject.index', $row->idproject) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;" title="Buka Historis Progress">
                                {{ $row->project_name }}
                            </a>
                        @endif
                    </td>
                    <td>{{ $row->direktorat }}</td>
                    <td>{{ $row->bagian }}</td>
                    <td>{{ $row->pic_name }}</td>
                    <td>{{ $row->no_surat }}</td>
                    <td>{{ $row->tgl_surat ? date('d-m-Y', strtotime($row->tgl_surat)) : '-' }}</td>                    
                    <td>{{ $row->tanggal_awal ? date('d-m-Y', strtotime($row->tanggal_awal)) : '-' }}</td>
                    <td>{{ $row->tanggal_akhir ? date('d-m-Y', strtotime($row->tanggal_akhir)) : '-' }}</td>
                    <td>{{ $row->rproject }}</td>
                    <td>
                        <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #c4b5fd;">
                            {{ $row->status_project }}
                        </span>
                    </td>
                    <td>{{ $row->assign_to }}</td>
                    <td>{{ $row->assign_to_name }}</td>
                    <td>{{ $row->support_to }}</td>
                    <td>{{ $row->support_to_name }}</td>
                    <td>{{ $row->telpon }}</td>
                    <td>{{ $row->tgl_update ? date('d-m-Y H:i:s', strtotime($row->tgl_update)) : '-' }}</td>
                    <td>{{ $row->token }}</td>
                    <td>{{ $row->durasipermintaan }}</td>
                    <td>{{ $row->vul_passed }}</td>
                    <td>{{ $row->id_catalog }}</td>
                    <td>{{ $row->nama_katalog }}</td>
                    <td>{{ $row->catalog_version }}</td>
                    <td>{{ $row->byvendor }}</td>
                    <td>{{ $row->vendorname }}</td>
                    <td>{{ $row->url_base }}</td>
                    <td>{{ $row->rkap }}</td>
                    <td style="text-align: right;">{{ $row->bsurkap ? number_format($row->bsurkap, 0, ',', '.') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="30" class="text-center" style="padding: 3rem; color: var(--text-muted);">
                        Data tidak ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: rgba(117, 95, 62, 0.1); font-weight: bold;">
                    <td colspan="29" style="text-align: right; color: var(--text-main);">TOTAL BSU RKAP</td>
                    <td style="text-align: right; color: var(--primary);">Rp {{ number_format($total_bsurkap, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @if($data->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} total data
        </div>
        <div>
            {{ $data->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif
</div>
@endsection

@section('custom-styles')
<style>
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, rgba(30, 41, 59, 0.8) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.2);
    }

    .summary-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
    }

    .summary-content h3 {
        margin: 0;
        font-size: 1rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-content p {
        margin: 0.5rem 0 0 0;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .filter-section {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem 1.5rem;
        align-items: flex-end;
    }

    .form-group {
        flex: 1;
        min-width: 150px;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.7rem 1rem;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }

    .form-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn {
        padding: 0.7rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-secondary {
        background: var(--glass-bg);
        color: var(--text-main);
        border: 1px solid var(--glass-border);
    }
    .btn-secondary:hover { background: rgba(255, 255, 255, 0.8); }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .table-container {
        background: rgba(255, 255, 255, 0.65);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .table-responsive {
        overflow-x: auto;
        overflow-y: auto;
        max-height: calc(100vh - 380px);
        min-height: 300px;
    }

    /* Custom Scrollbar for table-responsive */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 10px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: rgba(117, 95, 62, 0.05);
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: rgba(117, 95, 62, 0.25);
        border-radius: 10px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: rgba(117, 95, 62, 0.4);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    .data-table th, .data-table td {
        padding: 1rem 1.2rem;
        text-align: left;
        border-bottom: 1px solid var(--glass-border);
        font-size: 0.9rem;
    }

    .data-table th {
        background: rgba(255, 255, 255, 0.95);
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(5px);
        box-shadow: 0 1px 0 var(--glass-border);
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background: rgba(117, 95, 62, 0.06);
    }

    .badge {
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .text-center { text-align: center; }
</style>
@endsection

@section('custom-scripts')
<script>
    const exportProgressData = @json($allData);
    const totalBsurkapValue = {{ (float) $total_bsurkap }};

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    }

    function formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear() + ' ' +
               ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2) + ':' + ('0' + d.getSeconds()).slice(-2);
    }

    function exportToExcelHTML(headers, dataRows, filename, summaryTotal) {
        let tableHTML = '<table border="1"><thead><tr>';
        headers.forEach(h => {
            tableHTML += `<th style="background:#755f3e;color:#ffffff;font-weight:bold;padding:6px;">${h}</th>`;
        });
        tableHTML += '</tr></thead><tbody>';
        
        dataRows.forEach(row => {
            tableHTML += '<tr>';
            row.forEach((cell, idx) => {
                const align = idx === row.length - 1 ? 'right' : 'left';
                tableHTML += `<td style="padding:5px;text-align:${align};">${cell !== null && cell !== undefined ? cell : '-'}</td>`;
            });
            tableHTML += '</tr>';
        });

        if (summaryTotal !== undefined && summaryTotal !== null) {
            const formattedTotal = Number(summaryTotal).toLocaleString('id-ID');
            tableHTML += `
                <tr style="background: #f4f0ea; font-weight: bold;">
                    <td colspan="${headers.length - 1}" style="text-align: right; padding: 6px; color: #2c2721;">TOTAL BSU RKAP</td>
                    <td style="text-align: right; color: #755f3e; padding: 6px;">Rp ${formattedTotal}</td>
                </tr>
            `;
        }

        tableHTML += '</tbody></table>';

        const dataType = 'application/vnd.ms-excel;charset=utf-8';
        const xTag = '<x' + ':';
        const htmlTemplate = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8"><!--[if gte mso 9]><xml>${xTag}ExcelWorkbook>${xTag}ExcelWorksheets>${xTag}ExcelWorksheet>${xTag}Name>Progres Project</${xTag}Name>${xTag}WorksheetOptions>${xTag}DisplayGridlines/></${xTag}WorksheetOptions></${xTag}ExcelWorksheet></${xTag}ExcelWorksheets></${xTag}ExcelWorkbook></xml><![endif]--></head><body>${tableHTML}</body></html>`;

        const downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        const fileNameWithExt = (filename || 'Laporan_Progres_Project') + '.xls';

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

    window.exportLaporanProgressExcel = function() {
        const headers = [
            'Icon', 'ID', 'ID Project', 'Nama Project', 'Direktorat', 'Bagian',
            'PIC Name', 'No Surat', 'Tgl Surat', 'Tgl Awal', 'Tgl Akhir',
            'Rproject', 'Status Project', 'Assign To', 'Assign To Name', 'Support To',
            'Support To Name', 'Telpon', 'Tgl Update', 'Token', 'Durasi Permintaan',
            'Vul Passed', 'ID Catalog', 'Nama Katalog', 'Catalog Version',
            'By Vendor', 'Vendor Name', 'URL Base', 'RKAP', 'BSU RKAP'
        ];

        const rows = exportProgressData.map(row => [
            row.icon || '-',
            row.id || '-',
            row.idproject || '-',
            row.project_name || '-',
            row.direktorat || '-',
            row.bagian || '-',
            row.pic_name || '-',
            row.no_surat || '-',
            formatDate(row.tgl_surat),
            formatDate(row.tanggal_awal),
            formatDate(row.tanggal_akhir),
            row.rproject || '-',
            row.status_project || '-',
            row.assign_to || '-',
            row.assign_to_name || '-',
            row.support_to || '-',
            row.support_to_name || '-',
            row.telpon || '-',
            formatDateTime(row.tgl_update),
            row.token || '-',
            row.durasipermintaan || '-',
            row.vul_passed || '-',
            row.id_catalog || '-',
            row.nama_katalog || '-',
            row.catalog_version || '-',
            row.byvendor || '-',
            row.vendorname || '-',
            row.url_base || '-',
            row.rkap || '-',
            row.bsurkap ? Number(row.bsurkap).toLocaleString('id-ID') : '-'
        ]);

        exportToExcelHTML(headers, rows, 'Laporan_Progres_Project', totalBsurkapValue);
    };
</script>
@endsection
