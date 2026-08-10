@extends('layouts.app')

@section('title', 'Laporan Non Project')
@section('header-title', 'Laporan Non Project')

@section('content')

<!-- Summary Card -->
<div class="summary-cards" style="margin-bottom: 2rem;">
    <div class="summary-card" style="background: rgba(255, 255, 255, 0.65); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05); display: flex; align-items: center; gap: 1.25rem;">
        <div class="summary-icon" style="width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: #ffffff;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
        <div class="summary-content">
            <h3 style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Total Kegiatan Non Project</h3>
            <p style="font-size: 2.25rem; font-weight: 800; color: var(--text-main); margin-top: 0.2rem; line-height: 1;">{{ number_format($total_kegiatan, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section" style="background: rgba(255, 255, 255, 0.65); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05); margin-bottom: 2rem;">
    <form action="{{ route('laporan.nonproject') }}" method="GET" class="filter-form" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        
        <div class="form-group" style="flex: 1 1 250px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 0.35rem; display: block;">Cari Kegiatan / Lokasi / Catatan</label>
            <input type="text" name="search" class="form-control" placeholder="Ketik kata kunci pencarian..." value="{{ request('search') }}">
        </div>

        <div class="form-group" style="flex: 0 1 150px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 0.35rem; display: block;">Tahun</label>
            <select name="tahun" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @foreach($tahunOptions as $thn)
                    <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>
                        {{ $thn }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="flex: 0 1 160px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 0.35rem; display: block;">Bulan</label>
            <select name="bulan" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @foreach($bulanOptions as $num => $name)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="flex: 0 1 180px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 0.35rem; display: block;">PIC</label>
            <select name="pic" class="form-control" onchange="this.form.submit()">
                <option value="">Semua PIC</option>
                @foreach($picOptions as $p)
                    <option value="{{ $p->nama }}" {{ request('pic') == $p->nama ? 'selected' : '' }}>
                        {{ $p->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="submit" class="btn btn-secondary" style="padding: 0.75rem 1.25rem;">Filter</button>
            @if(request('search') || request('tahun') || request('bulan') || request('pic'))
                <a href="{{ route('laporan.nonproject') }}" class="btn btn-secondary" style="padding: 0.75rem 1rem;">Reset</a>
            @endif
            <button type="button" class="btn-export-excel" onclick="exportLaporanNonprojectExcel()" title="Save to Excel" style="padding: 0.75rem 1.25rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Save to Excel
            </button>
        </div>

    </form>
</div>

<!-- Table Section with Vertical Scroll -->
<div class="master-section" style="margin-bottom: 2rem;">
    <div class="table-container">
        <table class="data-table" id="laporanTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Kegiatan</th>
                    <th>Lokasi</th>
                    <th>PIC</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                @php
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
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 3rem; color: var(--text-muted);">
                        Tidak ada data Laporan Non Project yang sesuai filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
        padding: 0.65rem 1rem;
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

    .text-center { text-align: center; }
</style>
@endsection

@section('custom-scripts')
<script>
    const laporanData = @json($data);

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

    window.exportLaporanNonprojectExcel = function() {
        const headers = ['No', 'Tanggal', 'Kegiatan', 'Lokasi', 'PIC', 'Catatan'];
        const rows = laporanData.map((item, idx) => {
            let tglDisplay = '-';
            if (item.tanggal && item.tanggal.length === 8) {
                tglDisplay = item.tanggal.substring(6, 8) + '-' + item.tanggal.substring(4, 6) + '-' + item.tanggal.substring(0, 4);
            } else if (item.tanggal) {
                tglDisplay = item.tanggal;
            }
            return [
                idx + 1,
                tglDisplay,
                item.kegiatan || '-',
                item.lokasi || '-',
                item.pic || '-',
                item.catatan || '-'
            ];
        });
        exportToExcelHTML(headers, rows, 'Laporan_Non_Project');
    };
</script>
@endsection
