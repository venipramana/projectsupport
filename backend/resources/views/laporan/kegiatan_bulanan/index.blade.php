@extends('layouts.app')

@section('title', 'Laporan Kegiatan Bulanan')
@section('header-title', 'Laporan Kegiatan Bulanan')

@section('content')

@php
    $bulanNama = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $periodeLabel = '';
    if (!empty($selectedBulan) && isset($bulanNama[(int)$selectedBulan])) {
        $periodeLabel = $bulanNama[(int)$selectedBulan] . ' ';
    }
    $periodeLabel .= !empty($selectedTahun) ? $selectedTahun : 'Semua Tahun';
@endphp

<!-- Summary Cards -->
<div class="summary-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <!-- Total Semua Kegiatan -->
    <div class="summary-card" style="background: rgba(255, 255, 255, 0.65); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05); display: flex; align-items: center; gap: 1.25rem;">
        <div class="summary-icon" style="width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="summary-content">
            <h3 style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Total Kegiatan</h3>
            <p style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-top: 0.2rem; line-height: 1;">{{ number_format($total_kegiatan, 0, ',', '.') }}</p>
            <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600; margin-top: 0.35rem; display: block;">{{ $periodeLabel }}</span>
        </div>
    </div>

    <!-- Total Kegiatan Project -->
    <div class="summary-card" style="background: rgba(255, 255, 255, 0.65); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05); display: flex; align-items: center; gap: 1.25rem;">
        <div class="summary-icon" style="width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1d4ed8); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
            </svg>
        </div>
        <div class="summary-content">
            <h3 style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Kegiatan Project</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #1e40af; margin-top: 0.2rem; line-height: 1;">{{ number_format($total_project, 0, ',', '.') }}</p>
            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem; display: block;">Dari riwayat status project</span>
        </div>
    </div>

    <!-- Total Kegiatan Non Project -->
    <div class="summary-card" style="background: rgba(255, 255, 255, 0.65); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05); display: flex; align-items: center; gap: 1.25rem;">
        <div class="summary-icon" style="width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, #d97706, #b45309); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>
        <div class="summary-content">
            <h3 style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Kegiatan Non Project</h3>
            <p style="font-size: 2rem; font-weight: 800; color: #92400e; margin-top: 0.2rem; line-height: 1;">{{ number_format($total_nonproject, 0, ',', '.') }}</p>
            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem; display: block;">Lain-lain / Aktivitas umum</span>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section" style="background: rgba(255, 255, 255, 0.65); border: 1px solid var(--glass-border); border-radius: 20px; padding: 1.5rem; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05); margin-bottom: 2rem;">
    <form action="{{ route('laporan.kegiatan_bulanan') }}" method="GET" class="filter-form" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        
        <!-- Parameter Tahun (Listbox) -->
        <div class="form-group" style="flex: 0 1 160px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.4rem; display: block;">
                Tahun <span style="color: var(--primary);">*</span>
            </label>
            <select name="tahun" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @foreach($tahunOptions as $thn)
                    <option value="{{ $thn }}" {{ $selectedTahun === (string) $thn ? 'selected' : '' }}>
                        {{ $thn }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Parameter Bulan (Listbox) -->
        <div class="form-group" style="flex: 0 1 180px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.4rem; display: block;">
                Bulan <span style="color: var(--primary);">*</span>
            </label>
            <select name="bulan" class="form-control" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @foreach($bulanOptions as $num => $name)
                    <option value="{{ $num }}" {{ $selectedBulan === (string) $num ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter Kategori / Sumber -->
        <div class="form-group" style="flex: 0 1 180px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.4rem; display: block;">
                Kategori
            </label>
            <select name="sumber" class="form-control" onchange="this.form.submit()">
                <option value="" {{ $selectedSumber === '' ? 'selected' : '' }}>Semua Kategori</option>
                <option value="project" {{ $selectedSumber === 'project' ? 'selected' : '' }}>Project</option>
                <option value="nonproject" {{ $selectedSumber === 'nonproject' ? 'selected' : '' }}>Non Project</option>
            </select>
        </div>

        <!-- Search Input -->
        <div class="form-group" style="flex: 1 1 240px; margin-bottom: 0;">
            <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.4rem; display: block;">
                Cari Project / Kegiatan / Catatan
            </label>
            <input type="text" name="search" class="form-control" placeholder="Ketik kata kunci pencarian..." value="{{ $selectedSearch }}">
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="submit" class="btn btn-secondary" style="padding: 0.75rem 1.25rem;">Filter</button>
            
            @if(request()->has('search') || request()->has('tahun') || request()->has('bulan') || request()->has('sumber'))
                <a href="{{ route('laporan.kegiatan_bulanan') }}" class="btn btn-secondary" style="padding: 0.75rem 1rem;">Reset</a>
            @endif

            <button type="button" class="btn-export-excel" onclick="exportLaporanKegiatanBulananExcel()" title="Save to Excel" style="padding: 0.75rem 1.25rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Save to Excel
            </button>
        </div>

    </form>
</div>

<!-- Table Section -->
<div class="master-section" style="margin-bottom: 2rem;">
    <div class="table-container">
        <table class="data-table" id="kegiatanTable">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th style="width: 320px;">Project / Kegiatan</th>
                    <th style="width: 250px;">Deskripsi Kegiatan</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                @php
                    $tglDisplay = '-';
                    if (!empty($item->tanggal)) {
                        try {
                            $tglDisplay = date('d-m-Y', strtotime($item->tanggal));
                        } catch (\Exception $e) {
                            $tglDisplay = $item->tanggal;
                        }
                    }
                    $rowNumber = ($data->currentPage() - 1) * $data->perPage() + $loop->iteration;
                @endphp
                <tr>
                    <td class="text-center" style="font-weight: 500; color: var(--text-muted);">{{ $rowNumber }}</td>
                    <td style="white-space: nowrap; font-weight: 600; color: var(--text-main);">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); opacity: 0.8;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>{{ $tglDisplay }}</span>
                        </div>
                    </td>
                    <td>
                        @if($item->sumber === 'project')
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-weight: 600; color: var(--text-main); font-size: 0.95rem; line-height: 1.4;">
                                    {{ $item->project_kegiatan }}
                                </span>
                                <div>
                                    <span class="badge badge-project">Project</span>
                                </div>
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-weight: 700; color: #b45309; font-size: 0.95rem; letter-spacing: 0.5px;">
                                    {{ $item->project_kegiatan }}
                                </span>
                                <div>
                                    <span class="badge badge-nonproject">Non Project</span>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($item->sumber === 'project')
                            <span class="status-badge status-project">
                                {{ $item->deskripsi_kegiatan }}
                            </span>
                        @else
                            <span style="color: var(--text-main); font-weight: 500; line-height: 1.4; display: inline-block;">
                                {{ $item->deskripsi_kegiatan }}
                            </span>
                        @endif
                    </td>
                    <td style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.45;">
                        @if(!empty($item->catatan))
                            {!! nl2br(e($item->catatan)) !!}
                        @else
                            <span style="color: #a8a29e;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 3.5rem 1.5rem;">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem; color: var(--text-muted);">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #cbd5e1;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <p style="font-weight: 600; font-size: 1.05rem; color: var(--text-main); margin: 0;">Tidak ada kegiatan pada periode ini</p>
                            <p style="font-size: 0.85rem; margin: 0;">Silakan pilih parameter bulan atau tahun lain melalui filter di atas.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($data->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} total kegiatan
        </div>
        <div>
            {{ $data->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif
</div>

<style>
    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-family: inherit;
        font-weight: 600;
        font-size: 0.875rem;
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
        cursor: pointer;
    }

    .table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 560px;
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
        vertical-align: middle;
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

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-project {
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        border: 1px solid rgba(37, 99, 235, 0.2);
    }

    .badge-nonproject {
        background: rgba(217, 119, 6, 0.12);
        color: #b45309;
        border: 1px solid rgba(217, 119, 6, 0.25);
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .status-project {
        background: rgba(117, 95, 62, 0.08);
        color: var(--secondary);
        border: 1px solid rgba(117, 95, 62, 0.2);
    }

    .text-center { text-align: center; }
</style>
@endsection

@section('custom-scripts')
<script>
    const rawKegiatanData = @json($allData);
    const kegiatanData = Array.isArray(rawKegiatanData) ? rawKegiatanData : (rawKegiatanData.data || []);

    function exportToExcelHTML(headers, dataRows, filename) {
        let tableHTML = '<table border="1"><thead><tr>';
        headers.forEach(h => {
            tableHTML += `<th style="background:#755f3e;color:#ffffff;font-weight:bold;padding:7px;text-align:left;">${h}</th>`;
        });
        tableHTML += '</tr></thead><tbody>';
        
        dataRows.forEach(row => {
            tableHTML += '<tr>';
            row.forEach((cell, cellIdx) => {
                const align = cellIdx === 0 ? 'center' : 'left';
                tableHTML += `<td style="padding:6px;text-align:${align};">${cell !== null && cell !== undefined ? cell : '-'}</td>`;
            });
            tableHTML += '</tr>';
        });
        tableHTML += '</tbody></table>';

        const dataType = 'application/vnd.ms-excel';
        const xTag = '<x' + ':';
        const htmlTemplate = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8"><!--[if gte mso 9]><xml>${xTag}ExcelWorkbook>${xTag}ExcelWorksheets>${xTag}ExcelWorksheet>${xTag}Name>Kegiatan Bulanan</${xTag}Name>${xTag}WorksheetOptions>${xTag}DisplayGridlines/></${xTag}WorksheetOptions></${xTag}ExcelWorksheet></${xTag}ExcelWorksheets></${xTag}ExcelWorkbook></xml><![endif]--></head><body>${tableHTML}</body></html>`;

        const downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        const fileNameWithExt = (filename || 'Laporan_Kegiatan_Bulanan') + '.xls';

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

    window.exportLaporanKegiatanBulananExcel = function() {
        const headers = ['No', 'Tanggal', 'Kategori', 'Project / Kegiatan', 'Deskripsi Kegiatan', 'Catatan'];
        const rows = kegiatanData.map((item, idx) => {
            let tglDisplay = '-';
            if (item.tanggal) {
                const d = new Date(item.tanggal);
                if (!isNaN(d.getTime())) {
                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const year = d.getFullYear();
                    tglDisplay = `${day}-${month}-${year}`;
                } else {
                    tglDisplay = item.tanggal;
                }
            }
            const kategori = item.sumber === 'project' ? 'Project' : 'Non Project';
            return [
                idx + 1,
                tglDisplay,
                kategori,
                item.project_kegiatan || '-',
                item.deskripsi_kegiatan || '-',
                item.catatan || '-'
            ];
        });
        
        const tahunStr = "{{ $selectedTahun ?: 'SemuaTahun' }}";
        const bulanStr = "{{ $selectedBulan ?: 'SemuaBulan' }}";
        exportToExcelHTML(headers, rows, `Laporan_Kegiatan_Bulanan_${tahunStr}_${bulanStr}`);
    };
</script>
@endsection
