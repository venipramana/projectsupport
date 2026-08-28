@extends('layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Overview')

@section('custom-head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* Dashboard Layout Grid */
    .dashboard-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Section Master */
    .section-master {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Top Row: Blocks 1, 2, 3 */
    .top-row {
        display: grid;
        grid-template-columns: 1fr 2fr 1.5fr;
        gap: 1.5rem;
    }

    /* Middle Row: Blocks 4 & 5 */
    .middle-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    /* Lead By Row: Block 6 */
    .leadby-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .leadby-widget-content {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 1.5rem;
        align-items: center;
    }

    @media (max-width: 992px) {
        .leadby-widget-content {
            grid-template-columns: 1fr;
        }
    }

    /* Section Detil */
    .section-detil {
        margin-top: 1rem;
    }

    /* Universal Card Styles */
    .card {
        background: rgba(255, 255, 255, 0.65);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    
    .card:hover {
        border-color: rgba(117, 95, 62, 0.35);
    }

    .card-header {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 0.75rem;
    }

    /* Block 1: Total Projects */
    .total-projects-card {
        justify-content: center;
        align-items: center;
        text-align: center;
        background: linear-gradient(135deg, rgba(117, 95, 62, 0.12), rgba(156, 130, 94, 0.18));
        border: 1px solid rgba(117, 95, 62, 0.3);
    }
    .total-title {
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    .total-value {
        font-size: 4rem;
        font-weight: 800;
        background: linear-gradient(to right, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1;
    }

    /* Block 2: Chart Container */
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    /* Block 3, 4, 5: Lists */
    .list-container {
        flex: 1;
        overflow-y: auto;
        max-height: 250px;
        padding-right: 0.5rem;
    }
    
    /* Custom Scrollbar for lists */
    .list-container::-webkit-scrollbar {
        width: 6px;
    }
    .list-container::-webkit-scrollbar-track {
        background: rgba(117, 95, 62, 0.05);
        border-radius: 10px;
    }
    .list-container::-webkit-scrollbar-thumb {
        background: rgba(117, 95, 62, 0.25);
        border-radius: 10px;
    }
    .list-container::-webkit-scrollbar-thumb:hover {
        background: rgba(117, 95, 62, 0.5);
    }

    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.5);
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .list-item:hover {
        background: rgba(117, 95, 62, 0.12);
        border-color: rgba(117, 95, 62, 0.25);
        transform: translateX(3px);
    }
    .list-item.interactive:hover {
        background: rgba(117, 95, 62, 0.15);
        border-color: rgba(117, 95, 62, 0.35);
    }
    .list-item.danger-row {
        background: #dc2626 !important;
        color: #ffffff !important;
        border: 1px solid #ef4444 !important;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
    }
    .list-item.danger-row:hover {
        background: #b91c1c !important;
        border-color: #f87171 !important;
    }
    .list-item.danger-row .list-item-title {
        color: #ffffff !important;
    }
    .list-item-title {
        font-weight: 500;
        font-size: 0.95rem;
        color: var(--text-main);
    }
    .list-item-badge {
        background: rgba(117, 95, 62, 0.15);
        color: var(--primary);
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Detil Section */
    .table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 520px;
        background: rgba(255, 255, 255, 0.75);
        border-radius: 12px;
        border: 1px solid var(--glass-border);
        box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05);
    }
    
    /* Custom Scrollbar for Detil Table */
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

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.9rem;
    }
    th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f3efe6;
        color: var(--text-main);
        font-weight: 600;
        text-align: left;
        padding: 1rem;
        white-space: nowrap;
        border-bottom: 1px solid var(--glass-border);
    }
    td {
        padding: 1rem;
        border-bottom: 1px solid var(--glass-border);
        color: var(--text-main);
    }
    tr:hover td {
        background: rgba(117, 95, 62, 0.04);
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(117, 95, 62, 0.15);
        color: var(--primary);
        border: 1px solid rgba(117, 95, 62, 0.3);
    }
    #detil-title {
        color: var(--primary);
    }

    /* Export Excel Button */
    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #059669;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 8px;
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
    
    /* Empty state */
    .empty-state {
        padding: 3rem;
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .top-row {
            grid-template-columns: 1fr 1fr;
        }
        .total-projects-card {
            grid-column: span 2;
        }
    }
    @media (max-width: 768px) {
        .top-row, .middle-row {
            grid-template-columns: 1fr;
        }
        .total-projects-card {
            grid-column: span 1;
        }
    }
    /* Calendar Widget Styles */
    .calendar-card {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .calendar-month-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        min-width: 160px;
        text-align: center;
    }

    .btn-cal-nav {
        background: rgba(117, 95, 62, 0.1);
        border: 1px solid var(--glass-border);
        color: var(--primary);
        border-radius: 8px;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-cal-nav:hover {
        background: rgba(117, 95, 62, 0.2);
        transform: translateY(-1px);
    }

    .calendar-legend {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .legend-dot.available { background-color: #10b981; }
    .legend-dot.due-normal { background-color: #0284c7; }
    .legend-dot.due-urgent { background-color: #d97706; }
    .legend-dot.due-overdue { background-color: #dc2626; }

    /* Calendar Grid */
    .calendar-grid-container {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
        background: rgba(255, 255, 255, 0.35);
        padding: 0.75rem;
        border-radius: 14px;
        border: 1px solid var(--glass-border);
    }

    .cal-day-header {
        text-align: center;
        font-weight: 700;
        font-size: 0.8rem;
        color: var(--primary);
        padding: 0.4rem 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cal-day-cell {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(117, 95, 62, 0.12);
        border-radius: 10px;
        min-height: 95px;
        padding: 0.4rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .cal-day-cell:hover {
        background: rgba(255, 255, 255, 0.98);
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(117, 95, 62, 0.12);
        transform: translateY(-2px);
    }
    .cal-day-cell.other-month {
        opacity: 0.35;
        background: rgba(240, 237, 230, 0.2);
        cursor: default;
    }
    .cal-day-cell.other-month:hover {
        transform: none;
        box-shadow: none;
        border-color: rgba(117, 95, 62, 0.12);
    }
    .cal-day-cell.today {
        border: 2px solid var(--primary);
        background: rgba(117, 95, 62, 0.08);
    }
    .cal-day-cell.past-date {
        background: rgba(226, 232, 240, 0.65) !important;
        border-color: rgba(203, 213, 225, 0.6) !important;
        opacity: 0.65;
        cursor: not-allowed;
    }
    .cal-day-cell.past-date:hover {
        transform: none !important;
        box-shadow: none !important;
        background: rgba(226, 232, 240, 0.8) !important;
    }
    .cal-day-cell.past-date .cal-day-num {
        color: #94a3b8;
    }
    .cal-day-badge-past {
        font-size: 0.6rem;
        font-weight: 600;
        color: #64748b;
        background: rgba(148, 163, 184, 0.15);
        padding: 1px 4px;
        border-radius: 4px;
        border: 1px solid rgba(148, 163, 184, 0.3);
    }

    .cal-day-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cal-day-num {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-main);
    }
    .cal-day-cell.today .cal-day-num {
        background: var(--primary);
        color: #ffffff;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    .cal-day-badge-avail {
        font-size: 0.65rem;
        font-weight: 600;
        color: #059669;
        background: rgba(16, 185, 129, 0.12);
        padding: 1px 5px;
        border-radius: 4px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .cal-events-list {
        display: flex;
        flex-direction: column;
        gap: 3px;
        margin-top: 4px;
        overflow-y: auto;
        max-height: 58px;
    }
    .cal-event-pill {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 5px;
        border-radius: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 3px;
        transition: filter 0.2s;
    }
    .cal-event-pill:hover {
        filter: brightness(0.95);
    }
    .cal-event-pill.normal {
        background: #e0f2fe;
        color: #0369a1;
        border-left: 3px solid #0284c7;
    }
    .cal-event-pill.today-due {
        background: #fef3c7;
        color: #b45309;
        border-left: 3px solid #d97706;
    }
    .cal-event-pill.overdue {
        background: #fee2e2;
        color: #b91c1c;
        border-left: 3px solid #dc2626;
    }

    /* Modal Detil Kalender */
    .cal-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .cal-modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .cal-modal-card {
        background: #faf7f2;
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        width: 90%;
        max-width: 520px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        transform: translateY(20px);
        transition: transform 0.25s ease;
    }
    .cal-modal-overlay.active .cal-modal-card {
        transform: translateY(0);
    }
    .cal-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 0.75rem;
    }
    .cal-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-muted);
        line-height: 1;
    }
    .cal-modal-close:hover { color: var(--text-main); }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    
    <!-- SECTION MASTER -->
    <div class="section-master">
        
        <!-- ROW 1 -->
        <div class="top-row">
            <!-- Block 1: Total Projects -->
            <div class="card total-projects-card">
                <div class="total-title">Total Seluruh Project</div>
                <div class="total-value">{{ number_format($total_projects, 0, ',', '.') }}</div>
            </div>

            <!-- Block 2: Donut Chart -->
            <div class="card">
                <div class="card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
                    Summary Project Per-Direktorat
                </div>
                <div class="chart-container">
                    <canvas id="direktoratChart"></canvas>
                </div>
                <div style="text-align: center; font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                    *Klik pada bagian chart untuk melihat detil
                </div>
            </div>

            <!-- Block 3: Summary This Year -->
            <div class="card">
                <div class="card-header" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Summary Direktorat ({{ $currentYear }})
                    </div>
                    <button type="button" class="btn-export-excel" onclick="exportDirektoratExcel()" title="Save to Excel">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Excel
                    </button>
                </div>
                <div class="list-container">
                    @forelse($projects_this_year as $item)
                        <div class="list-item interactive" onclick="filterDetil('{{ $item->direktorat }}', {{ $currentYear }})">
                            <span class="list-item-title">{{ $item->direktorat }}</span>
                            <span class="list-item-badge">{{ $item->total }}</span>
                        </div>
                    @empty
                        <div class="empty-state">Data tahun ini kosong.</div>
                    @endforelse
                </div>
                <div style="text-align: center; font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                    *Klik item untuk melihat detil
                </div>
            </div>
        </div>

        <!-- ROW 2 -->
        <div class="middle-row">
            <!-- Block 4: Development Projects -->
            <div class="card">
                <div class="card-header" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                        Status Development ({{ $dev_projects->count() }})
                    </div>
                    <button type="button" class="btn-export-excel" onclick="exportDevProjectsExcel()" title="Save to Excel">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Excel
                    </button>
                </div>
                <div class="list-container">
                    @forelse($dev_projects as $proj)
                        @php
                            $today = \Carbon\Carbon::today();
                            $tglAkhir = $proj->tanggal_akhir ? \Carbon\Carbon::parse($proj->tanggal_akhir)->startOfDay() : null;
                            if ($tglAkhir) {
                                $diffDays = (int) $today->diffInDays($tglAkhir, false);
                                if ($diffDays > 0) {
                                    $statusJatuhTempo = $diffDays . " hari lagi";
                                    $colorJatuhTempo = "#059669";
                                } elseif ($diffDays === 0) {
                                    $statusJatuhTempo = "Hari ini";
                                    $colorJatuhTempo = "#d97706";
                                } else {
                                    $statusJatuhTempo = "Terlambat " . abs($diffDays) . " hari";
                                    $colorJatuhTempo = "#dc2626";
                                }
                                $infoJatuhTempo = $tglAkhir->format('d-m-Y') . " (" . $statusJatuhTempo . ")";
                            } else {
                                $infoJatuhTempo = "-";
                                $colorJatuhTempo = "var(--text-muted)";
                            }
                        @endphp
                        <div class="list-item" title="{{ $proj->project_name }}">
                            <div style="display: flex; flex-direction: column; overflow: hidden; gap: 0.2rem;">
                                <span class="list-item-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $proj->project_name }}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $proj->direktorat }} - {{ $proj->pic_name }}</span>
                                <span style="font-size: 0.75rem; color: {{ $colorJatuhTempo }}; font-weight: 500;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline; vertical-align: -1px; margin-right: 2px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    Jatuh Tempo: {{ $infoJatuhTempo }}
                                </span>
                            </div>
                            <span class="list-item-badge" style="background: rgba(5, 150, 105, 0.15); color: #059669; align-self: flex-start;">DEV</span>
                        </div>
                    @empty
                        <div class="empty-state">Tidak ada project Development.</div>
                    @endforelse
                </div>
            </div>

            <!-- Block 5: QA Projects -->
            <div class="card">
                <div class="card-header" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #d97706;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        Status QA / UAT ({{ $qa_projects->count() }})
                    </div>
                    <button type="button" class="btn-export-excel" onclick="exportQaProjectsExcel()" title="Save to Excel">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Excel
                    </button>
                </div>
                <div class="list-container">
                    @forelse($qa_projects as $proj)
                        @php
                            $today = \Carbon\Carbon::today();
                            $tglUpdate = $proj->tgl_update ? \Carbon\Carbon::parse($proj->tgl_update)->startOfDay() : null;
                            $durasiHari = $tglUpdate ? (int) $tglUpdate->diffInDays($today) : 0;
                            $isOver14Days = $durasiHari > 14;
                            $tglUpdateFormatted = $tglUpdate ? $tglUpdate->format('d-m-Y') : '-';
                        @endphp
                        <div class="list-item {{ $isOver14Days ? 'danger-row' : '' }}" title="{{ $proj->project_name }}">
                            <div style="display: flex; flex-direction: column; overflow: hidden; gap: 0.2rem;">
                                <span class="list-item-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $proj->project_name }}</span>
                                <span style="font-size: 0.75rem; {{ $isOver14Days ? 'color: rgba(255,255,255,0.9);' : 'color: var(--text-muted);' }}">
                                    {{ $proj->direktorat }} - {{ $proj->pic_name }}
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 500; {{ $isOver14Days ? 'color: #ffffff;' : 'color: #d97706;' }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline; vertical-align: -1px; margin-right: 2px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                                    Durasi: {{ $durasiHari }} hari (Tgl Update: {{ $tglUpdateFormatted }})
                                </span>
                            </div>
                            <span class="list-item-badge" style="align-self: flex-start; {{ $isOver14Days ? 'background: rgba(255,255,255,0.25); color: #ffffff; border: 1px solid rgba(255,255,255,0.4);' : 'background: rgba(217, 119, 6, 0.15); color: #d97706;' }}">
                                QA
                            </span>
                        </div>
                    @empty
                        <div class="empty-state">Tidak ada project QA / UAT.</div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- ROW 3: Project Berdasarkan Lead By -->
        <div class="leadby-row" style="margin-top: 1.5rem;">
            <div class="card">
                <div class="card-header" style="justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        Jumlah Project Berdasarkan Lead By
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;">
                            *Klik item list/grafik untuk detil
                        </span>
                        <button type="button" class="btn-export-excel" onclick="exportLeadByExcel()" title="Save to Excel">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Excel
                        </button>
                    </div>
                </div>
                
                <div class="leadby-widget-content">
                    <!-- Format List -->
                    <div class="list-container" style="max-height: 260px;">
                        @forelse($projects_by_lead as $item)
                            <div class="list-item interactive" data-lead="{{ $item->lead_by }}" onclick="filterDetilByLead(this.getAttribute('data-lead'))">
                                <span class="list-item-title">{{ $item->lead_by }}</span>
                                <span class="list-item-badge">{{ $item->total }} Project</span>
                            </div>
                        @empty
                            <div class="empty-state">Data Lead By kosong.</div>
                        @endforelse
                    </div>

                    <!-- Format Grafik Batang (Bar Chart) -->
                    <div class="chart-container" style="height: 260px;">
                        <canvas id="leadByChart"></canvas>
                    </div>
                </div>
        <!-- ROW 4: Kalender Jatuh Tempo Project Development & Available Slot -->
        <div class="calendar-row" style="margin-top: 1.5rem;">
            <div class="card calendar-card">
                <div class="card-header" style="justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span style="font-weight: 700;">Kalender Jatuh Tempo Project Development (Available Dates)</span>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <!-- Filter Cepat Lead By -->
                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                            <label for="calLeadFilter" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Filter Lead:</label>
                            <select id="calLeadFilter" onchange="onCalLeadFilterChange(this.value)" style="background: rgba(255,255,255,0.8); border: 1px solid var(--glass-border); border-radius: 8px; padding: 0.25rem 0.5rem; font-size: 0.75rem; color: var(--text-main); outline: none;">
                                <option value="ALL">Semua Lead By</option>
                                @foreach($projects_by_lead as $lead)
                                    <option value="{{ $lead->lead_by }}">{{ $lead->lead_by }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Navigation -->
                        <div class="calendar-nav-group">
                            <button type="button" class="btn-cal-nav" onclick="changeCalMonth(-1)" title="Bulan Sebelumnya">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                Prev
                            </button>
                            <span id="calendarMonthTitle" class="calendar-month-title">Agustus 2026</span>
                            <button type="button" class="btn-cal-nav" onclick="changeCalMonth(1)" title="Bulan Selanjutnya">
                                Next
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                            <button type="button" class="btn-cal-nav" onclick="jumpCalToday()" style="margin-left: 0.25rem;">
                                Hari Ini
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Subheader & Legend -->
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; padding: 0.5rem 0.75rem; background: rgba(117, 95, 62, 0.05); border-radius: 10px;">
                    <div class="calendar-legend">
                        <span class="legend-item">
                            <span class="legend-dot" style="background-color: #94a3b8;"></span> Un-available (Tanggal Berlalu)
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot available"></span> Available (Siap Request Project)
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot" style="background: linear-gradient(135deg, #3b82f6, #d97706, #10b981);"></span> Rentang Project Dev (Warna Unik per Project)
                        </span>
                    </div>
                    <div id="calMonthSummaryBadge" style="font-size: 0.75rem; font-weight: 600; color: var(--primary);">
                        <!-- Dynamic text populated via JS -->
                    </div>
                </div>

                <!-- Grid Kalender -->
                <div id="calendarGridContainer" class="calendar-grid-container">
                    <!-- Rendered dynamically by JavaScript -->
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Detil Tanggal Kalender -->
    <div id="calDateModal" class="cal-modal-overlay" onclick="closeCalModalOutside(event)">
        <div class="cal-modal-card">
            <div class="cal-modal-header">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span id="calModalTitle" style="font-weight: 700; font-size: 1rem; color: var(--text-main);">Detil Tanggal</span>
                </div>
                <button type="button" class="cal-modal-close" onclick="closeCalModal()">&times;</button>
            </div>
            <div id="calModalBody" style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 380px; overflow-y: auto;">
                <!-- Dynamic content injected here -->
            </div>
            <div style="text-align: right; border-top: 1px solid var(--glass-border); padding-top: 0.75rem;">
                <button type="button" onclick="closeCalModal()" style="background: var(--primary); color: #ffffff; border: none; padding: 0.4rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">Tutup</button>
            </div>
        </div>
    </div>

    <!-- SECTION DETIL -->
    <div class="section-detil">
        <div class="card">
            <div class="card-header" style="flex-wrap: wrap; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                Section Detil: <span id="detil-title" style="margin-left: 0.5rem;">Pilih Direktorat / Lead By pada Chart atau List di atas</span>
                <div style="margin-left: auto; display: flex; gap: 0.5rem; align-items: center;">
                    <button onclick="showAllProjects()" style="background: transparent; border: 1px solid var(--glass-border); color: var(--text-main); padding: 0.25rem 0.75rem; border-radius: 8px; cursor: pointer; font-size: 0.8rem; transition: background 0.2s;">Tampilkan Semua</button>
                    <button type="button" class="btn-export-excel" onclick="exportDetilExcel()" title="Save to Excel">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Save to Excel
                    </button>
                </div>
            </div>
            
            <div class="table-container">
                <table id="detil-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Project</th>
                            <th>Nama Project</th>
                            <th>Direktorat</th>
                            <th>Lead By</th>
                            <th>Tgl Awal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="detil-tbody">
                        <!-- Content injected via JS -->
                    </tbody>
                </table>
                <div id="detil-empty" class="empty-state" style="display: none;">
                    Tidak ada data yang sesuai.
                </div>
            </div>
            <div id="detil-pagination" class="pagination-container" style="display: none; margin-top: 1rem;"></div>
        </div>
    </div>

</div>
@endsection

@section('custom-scripts')
<script>
    // Data dari Controller
    const allProjects = @json($all_projects);
    const chartData = @json($projects_by_direktorat);
    const projectsThisYear = @json($projects_this_year);
    const leadByData = @json($projects_by_lead);
    const devProjects = @json($dev_projects);
    const qaProjects = @json($qa_projects);

    let currentDetilData = allProjects;
    let currentDetilTitle = 'Semua Project';
    let detilCurrentPage = 1;
    const detilPageSize = 10; // 10 data per halaman pada Section Detil

    // Excel Exporter Helper
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

    window.exportDirektoratExcel = function() {
        const headers = ['No', 'Direktorat', 'Jumlah Project'];
        const rows = projectsThisYear.map((item, idx) => [idx + 1, item.direktorat, item.total]);
        exportToExcelHTML(headers, rows, 'Summary_Project_Per_Direktorat_' + (new Date().getFullYear()));
    };

    window.exportLeadByExcel = function() {
        const headers = ['No', 'Lead By', 'Jumlah Project'];
        const rows = leadByData.map((item, idx) => [idx + 1, item.lead_by, item.total]);
        exportToExcelHTML(headers, rows, 'Summary_Project_Per_Lead_By');
    };

    window.exportDevProjectsExcel = function() {
        const headers = ['No', 'ID Project', 'Nama Project', 'Direktorat', 'PIC Name', 'Status Jatuh Tempo'];
        const today = new Date();
        today.setHours(0,0,0,0);
        
        const rows = devProjects.map((item, idx) => {
            let infoJatuhTempo = '-';
            if (item.tanggal_akhir) {
                const tglAkhir = new Date(item.tanggal_akhir);
                tglAkhir.setHours(0,0,0,0);
                const diffDays = Math.round((tglAkhir - today) / (1000 * 60 * 60 * 24));
                const formattedDate = formatDate(item.tanggal_akhir);
                if (diffDays > 0) {
                    infoJatuhTempo = `${formattedDate} (${diffDays} hari lagi)`;
                } else if (diffDays === 0) {
                    infoJatuhTempo = `${formattedDate} (Hari ini)`;
                } else {
                    infoJatuhTempo = `${formattedDate} (Terlambat ${Math.abs(diffDays)} hari)`;
                }
            }
            return [
                idx + 1,
                item.idproject || '-',
                item.project_name || '-',
                item.direktorat || '-',
                item.pic_name || '-',
                infoJatuhTempo
            ];
        });
        exportToExcelHTML(headers, rows, 'Project_Status_Development');
    };

    window.exportQaProjectsExcel = function() {
        const headers = ['No', 'ID Project', 'Nama Project', 'Direktorat', 'PIC Name', 'Tgl Update', 'Durasi (Hari)'];
        const today = new Date();
        today.setHours(0,0,0,0);
        
        const rows = qaProjects.map((item, idx) => {
            let durasi = 0;
            if (item.tgl_update) {
                const tglUpdate = new Date(item.tgl_update);
                tglUpdate.setHours(0,0,0,0);
                durasi = Math.floor((today - tglUpdate) / (1000 * 60 * 60 * 24));
            }
            return [
                idx + 1,
                item.idproject || '-',
                item.project_name || '-',
                item.direktorat || '-',
                item.pic_name || '-',
                formatDate(item.tgl_update),
                durasi + ' hari'
            ];
        });
        exportToExcelHTML(headers, rows, 'Project_Status_QA_UAT');
    };

    // Save to Excel: Selalu mengeksport SELURUH data terfilter (currentDetilData), tidak terpengaruh paginasi
    window.exportDetilExcel = function() {
        const headers = ['No', 'ID Project', 'Nama Project', 'Direktorat', 'Lead By', 'Tanggal Awal', 'Status Project'];
        const rows = currentDetilData.map((item, idx) => [
            idx + 1,
            item.idproject || '-',
            item.project_name || '-',
            item.direktorat || '-',
            item.assign_to_name || '-',
            formatDate(item.tanggal_awal),
            item.status_project || '-'
        ]);
        const safeTitle = (currentDetilTitle || 'Semua_Project').replace(/[^a-zA-Z0-9]/g, '_');
        exportToExcelHTML(headers, rows, 'Detil_Project_' + safeTitle);
    };
    
    // Formatting date
    function formatDate(dateString) {
        if (!dateString) return '-';
        const d = new Date(dateString);
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    }

    // Function to render table dengan client-side pagination
    function renderDetilTable(data, title, page = 1) {
        currentDetilData = data;
        currentDetilTitle = title;
        detilCurrentPage = page;

        document.getElementById('detil-title').innerText = title;
        const tbody = document.getElementById('detil-tbody');
        const emptyState = document.getElementById('detil-empty');
        const table = document.getElementById('detil-table');
        const paginationContainer = document.getElementById('detil-pagination');
        
        tbody.innerHTML = '';
        
        if (!data || data.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
            if (paginationContainer) paginationContainer.style.display = 'none';
            return;
        }
        
        table.style.display = 'table';
        emptyState.style.display = 'none';
        
        const totalItems = data.length;
        const totalPages = Math.ceil(totalItems / detilPageSize);
        const currentPage = Math.max(1, Math.min(page, totalPages));
        detilCurrentPage = currentPage;

        const startIndex = (currentPage - 1) * detilPageSize;
        const endIndex = Math.min(startIndex + detilPageSize, totalItems);
        const pageData = data.slice(startIndex, endIndex);

        pageData.forEach((proj, idx) => {
            const tr = document.createElement('tr');
            const rowNumber = startIndex + idx + 1;
            tr.innerHTML = `
                <td>${rowNumber}</td>
                <td>${proj.idproject || '-'}</td>
                <td>
                    <a href="/hproject/${proj.idproject || ''}" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                        ${proj.project_name || '-'}
                    </a>
                </td>
                <td>${proj.direktorat || '-'}</td>
                <td>${proj.assign_to_name || '-'}</td>
                <td>${formatDate(proj.tanggal_awal)}</td>
                <td><span class="status-badge">${proj.status_project || '-'}</span></td>
            `;
            tbody.appendChild(tr);
        });

        // Controls Paginasi Section Detil
        if (paginationContainer) {
            if (totalPages > 1) {
                paginationContainer.style.display = 'flex';
                let pagHTML = `
                    <div class="pagination-info">
                        Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalItems} total data (${title})
                    </div>
                    <ul class="pagination">
                `;

                if (currentPage > 1) {
                    pagHTML += `<li><a href="javascript:void(0)" onclick="changeDetilPage(${currentPage - 1})">&laquo; Prev</a></li>`;
                } else {
                    pagHTML += `<li class="disabled"><span>&laquo; Prev</span></li>`;
                }

                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                        if (i === currentPage) {
                            pagHTML += `<li class="active"><span>${i}</span></li>`;
                        } else {
                            pagHTML += `<li><a href="javascript:void(0)" onclick="changeDetilPage(${i})">${i}</a></li>`;
                        }
                    } else if (i === currentPage - 2 || i === currentPage + 2) {
                        pagHTML += `<li class="disabled"><span>...</span></li>`;
                    }
                }

                if (currentPage < totalPages) {
                    pagHTML += `<li><a href="javascript:void(0)" onclick="changeDetilPage(${currentPage + 1})">Next &raquo;</a></li>`;
                } else {
                    pagHTML += `<li class="disabled"><span>Next &raquo;</span></li>`;
                }

                pagHTML += `</ul>`;
                paginationContainer.innerHTML = pagHTML;
            } else {
                paginationContainer.style.display = 'none';
            }
        }
    }

    window.changeDetilPage = function(page) {
        renderDetilTable(currentDetilData, currentDetilTitle, page);
    };

    // Filter Function
    function filterDetil(direktorat, year = null) {
        let filtered = allProjects.filter(p => p.direktorat === direktorat);
        let title = direktorat;
        
        if (year) {
            filtered = filtered.filter(p => p.tanggal_awal && new Date(p.tanggal_awal).getFullYear() == year);
            title = direktorat + " (" + year + ")";
        }
        
        renderDetilTable(filtered, title, 1);
        // Scroll to detil section smoothly
        document.querySelector('.section-detil').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter Function by Lead By
    function filterDetilByLead(leadName) {
        let filtered;
        if (leadName === 'Belum Ditugaskan') {
            filtered = allProjects.filter(p => !p.assign_to_name || p.assign_to_name.trim() === '');
        } else {
            filtered = allProjects.filter(p => p.assign_to_name === leadName);
        }
        renderDetilTable(filtered, "Lead By: " + leadName, 1);
        document.querySelector('.section-detil').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Show All
    window.showAllProjects = function() {
        renderDetilTable(allProjects, 'Semua Project', 1);
    }

    // ==========================================
    // KALENDER JATUH TEMPO PROJECT DEVELOPMENT
    // ==========================================
    let calCurrentYear = new Date().getFullYear();
    let calCurrentMonth = new Date().getMonth(); // 0-indexed
    let selectedCalLeadFilter = 'ALL';

    function parseYMD(dateStr) {
        if (!dateStr) return null;
        const str = String(dateStr).trim();
        if (str.length >= 10 && str.charAt(4) === '-' && str.charAt(7) === '-') {
            return str.substring(0, 10);
        }
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return null;
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function getTodayYMD() {
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    // Preset Palette Warna Unik per Project
    const projectColorPalette = [
        { bg: '#dbeafe', border: '#3b82f6', text: '#1e40af' }, // Blue
        { bg: '#fef3c7', border: '#d97706', text: '#92400e' }, // Gold/Amber
        { bg: '#d1fae5', border: '#10b981', text: '#065f46' }, // Emerald
        { bg: '#ede9fe', border: '#8b5cf6', text: '#5b21b6' }, // Purple
        { bg: '#fce7f3', border: '#ec4899', text: '#9d174d' }, // Pink
        { bg: '#cffaff', border: '#06b6d4', text: '#155e75' }, // Cyan
        { bg: '#ffedd5', border: '#f97316', text: '#9a3412' }, // Orange
        { bg: '#e0e7ff', border: '#6366f1', text: '#3730a3' }, // Indigo
        { bg: '#ccfbf1', border: '#14b8a6', text: '#115e59' }, // Teal
        { bg: '#fae8ff', border: '#d946ef', text: '#86198f' }  // Fuchsia
    ];

    function getProjectColor(project, index) {
        const idStr = String(project.idproject || project.id || index || '0');
        let hash = 0;
        for (let i = 0; i < idStr.length; i++) {
            hash = idStr.charCodeAt(i) + ((hash << 5) - hash);
        }
        const paletteIndex = Math.abs(hash) % projectColorPalette.length;
        return projectColorPalette[paletteIndex];
    }

    function renderDevelopmentCalendar() {
        const gridContainer = document.getElementById('calendarGridContainer');
        const monthTitle = document.getElementById('calendarMonthTitle');
        const monthSummaryBadge = document.getElementById('calMonthSummaryBadge');
        if (!gridContainer) return;

        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dayHeaderNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

        monthTitle.innerText = `${monthNames[calCurrentMonth]} ${calCurrentYear}`;

        const todayYMD = getTodayYMD();

        // Filter & siapkan data devProjects
        const activeDevProjects = devProjects.filter(p => {
            if (!p.tanggal_akhir) return false;
            if (selectedCalLeadFilter !== 'ALL') {
                const leadName = (p.assign_to_name && p.assign_to_name.trim()) ? p.assign_to_name.trim() : 'Belum Ditugaskan';
                if (leadName !== selectedCalLeadFilter) return false;
            }
            return true;
        }).map((p, idx) => {
            const endYMD = parseYMD(p.tanggal_akhir);
            const rawStartYMD = parseYMD(p.tanggal_awal);
            const startYMD = (rawStartYMD && rawStartYMD > todayYMD) ? rawStartYMD : todayYMD;
            const color = getProjectColor(p, idx);
            return {
                ...p,
                startYMD,
                endYMD,
                rawStartYMD,
                color
            };
        });

        const firstDay = new Date(calCurrentYear, calCurrentMonth, 1).getDay();
        const totalDays = new Date(calCurrentYear, calCurrentMonth + 1, 0).getDate();
        const prevMonthDays = new Date(calCurrentYear, calCurrentMonth, 0).getDate();

        let html = '';

        // Headers
        dayHeaderNames.forEach(h => {
            html += `<div class="cal-day-header">${h}</div>`;
        });

        // Filler bulan sebelumnya
        for (let i = firstDay - 1; i >= 0; i--) {
            const pDay = prevMonthDays - i;
            html += `
                <div class="cal-day-cell other-month">
                    <div class="cal-day-top">
                        <span class="cal-day-num">${pDay}</span>
                    </div>
                </div>
            `;
        }

        let devDueThisMonthCount = 0;
        let availableDaysCount = 0;

        // Sel untuk bulan ini
        for (let d = 1; d <= totalDays; d++) {
            const monthStr = String(calCurrentMonth + 1).padStart(2, '0');
            const dayStr = String(d).padStart(2, '0');
            const cellYMD = `${calCurrentYear}-${monthStr}-${dayStr}`;
            const cellDate = new Date(calCurrentYear, calCurrentMonth, d);
            const dayOfWeek = cellDate.getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
            const isToday = (cellYMD === todayYMD);
            const isPast = (cellYMD < todayYMD);

            if (isPast) {
                // Tanggal < current_date: state un-available / disable
                html += `
                    <div class="cal-day-cell past-date" onclick="openCalModal('${cellYMD}')" title="Tanggal telah berlalu (Un-available)">
                        <div class="cal-day-top">
                            <span class="cal-day-num">${d}</span>
                            <span class="cal-day-badge-past">Un-available</span>
                        </div>
                        <div style="margin-top:auto; font-size:0.6rem; color:#94a3b8; font-weight:500;">
                            Past Date
                        </div>
                    </div>
                `;
            } else {
                // Tanggal >= current_date
                // Cari project yang aktif pada tanggal ini (startYMD <= cellYMD && cellYMD <= endYMD)
                const runningProjects = activeDevProjects.filter(p => p.startYMD <= cellYMD && cellYMD <= p.endYMD);
                
                // Cari project yang jatuh tempo persis pada tanggal ini
                const dueProjectsOnDate = activeDevProjects.filter(p => p.endYMD === cellYMD);
                if (dueProjectsOnDate.length > 0) {
                    devDueThisMonthCount += dueProjectsOnDate.length;
                }

                let cellClasses = 'cal-day-cell';
                if (isToday) cellClasses += ' today';

                html += `<div class="${cellClasses}" onclick="openCalModal('${cellYMD}')">`;
                html += `
                    <div class="cal-day-top">
                        <span class="cal-day-num">${d}</span>
                        ${runningProjects.length === 0 
                            ? `<span class="cal-day-badge-avail" title="Tanggal ini bebas beban project (Siap pengembangan)">Available</span>` 
                            : `<span style="font-size:0.65rem; font-weight:700; color:var(--primary); background:rgba(117,95,62,0.12); padding:1px 5px; border-radius:4px;">${runningProjects.length} Active</span>`}
                    </div>
                `;

                if (runningProjects.length > 0) {
                    html += `<div class="cal-events-list">`;
                    runningProjects.slice(0, 3).forEach(p => {
                        const isDueDate = (p.endYMD === cellYMD);
                        const c = p.color;
                        html += `
                            <div class="cal-event-pill" style="background:${c.bg}; border-left:3px solid ${c.border}; color:${c.text}; font-size:0.65rem; padding:2px 4px; border-radius:4px; display:flex; justify-content:space-between; align-items:center;" title="${p.project_name} (Jatuh Tempo: ${formatDate(p.endYMD)})">
                                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${p.project_name || 'Project'}</span>
                                ${isDueDate ? `<span style="font-size:0.55rem; font-weight:800; background:${c.border}; color:#ffffff; padding:0 3px; border-radius:3px; margin-left:2px;">DUE</span>` : ''}
                            </div>
                        `;
                    });
                    if (runningProjects.length > 3) {
                        html += `<div style="font-size:0.62rem; font-weight:700; color:var(--primary); margin-top:1px;">+${runningProjects.length - 3} project lagi...</div>`;
                    }
                    html += `</div>`;
                } else {
                    if (!isWeekend) availableDaysCount++;
                    html += `
                        <div style="margin-top:auto; font-size:0.65rem; color:#059669; font-weight:600; opacity:0.9; display:flex; align-items:center; gap:2px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 12 12 14 14"></polyline></svg>
                            Available
                        </div>
                    `;
                }

                html += `</div>`;
            }
        }

        // Filler bulan selanjutnya
        const totalRenderedSoFar = firstDay + totalDays;
        const remainder = (7 - (totalRenderedSoFar % 7)) % 7;
        for (let n = 1; n <= remainder; n++) {
            html += `
                <div class="cal-day-cell other-month">
                    <div class="cal-day-top">
                        <span class="cal-day-num">${n}</span>
                    </div>
                </div>
            `;
        }

        gridContainer.innerHTML = html;

        monthSummaryBadge.innerHTML = `
            Bulan Ini: <strong>${devDueThisMonthCount}</strong> Project Dev Jatuh Tempo | <strong>${availableDaysCount}</strong> Hari Kerja Available
        `;
    }

    window.openCalModal = function(cellYMD) {
        const modal = document.getElementById('calDateModal');
        const titleEl = document.getElementById('calModalTitle');
        const bodyEl = document.getElementById('calModalBody');
        if (!modal || !titleEl || !bodyEl) return;

        const todayYMD = getTodayYMD();
        const formattedDate = formatDate(cellYMD);

        titleEl.innerText = `Detil Tanggal: ${formattedDate}`;

        let bodyHTML = '';

        if (cellYMD < todayYMD) {
            bodyHTML = `
                <div style="background: rgba(148, 163, 184, 0.12); border: 1px solid rgba(148, 163, 184, 0.3); border-radius: 12px; padding: 1.25rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                    <div style="width: 40px; height: 40px; background: rgba(148, 163, 184, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #475569;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div style="font-weight: 700; font-size: 1rem; color: #475569;">Un-available / Tanggal Berlalu</div>
                    <div style="font-size: 0.825rem; color: var(--text-muted); max-width: 420px; line-height: 1.4;">
                        Tanggal <strong>${formattedDate}</strong> sudah berlalu sebelum tanggal hari ini (${formatDate(todayYMD)}). Tanggal ini tidak dapat dialokasikan untuk permintaan pengembangan baru.
                    </div>
                </div>
            `;
        } else {
            // Tanggal >= todayYMD
            const activeDevProjects = devProjects.filter(p => {
                if (!p.tanggal_akhir) return false;
                if (selectedCalLeadFilter !== 'ALL') {
                    const leadName = (p.assign_to_name && p.assign_to_name.trim()) ? p.assign_to_name.trim() : 'Belum Ditugaskan';
                    if (leadName !== selectedCalLeadFilter) return false;
                }
                return true;
            }).map((p, idx) => {
                const endYMD = parseYMD(p.tanggal_akhir);
                const rawStartYMD = parseYMD(p.tanggal_awal);
                const startYMD = (rawStartYMD && rawStartYMD > todayYMD) ? rawStartYMD : todayYMD;
                const color = getProjectColor(p, idx);
                return { ...p, startYMD, endYMD, color };
            });

            const runningOnDate = activeDevProjects.filter(p => p.startYMD <= cellYMD && cellYMD <= p.endYMD);

            if (runningOnDate.length > 0) {
                bodyHTML += `
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                        Terdapat <strong>${runningOnDate.length}</strong> project status <strong>DEVELOPMENT</strong> yang aktif/berjalan pada tanggal ini:
                    </div>
                `;

                runningOnDate.forEach(p => {
                    const isDueDate = (p.endYMD === cellYMD);
                    const c = p.color;

                    bodyHTML += `
                        <div style="background: ${c.bg}; border: 1px solid ${c.border}; border-radius: 12px; padding: 1rem; display: flex; flex-direction: column; gap: 0.4rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                <a href="/hproject/${p.idproject || ''}" style="font-weight: 700; font-size: 0.95rem; color: ${c.text}; text-decoration: none;">
                                    ${p.project_name || 'Tanpa Nama Project'}
                                </a>
                                ${isDueDate ? `<span style="font-size: 0.7rem; font-weight: 800; background: ${c.border}; color: #ffffff; padding: 0.2rem 0.5rem; border-radius: 20px; white-space: nowrap;">JATUH TEMPO HARI INI</span>` : `<span style="font-size: 0.7rem; font-weight: 700; background: rgba(255,255,255,0.7); color: ${c.text}; border: 1px solid ${c.border}; padding: 0.2rem 0.5rem; border-radius: 20px; white-space: nowrap;">Aktif Dev</span>`}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-main); display: grid; grid-template-columns: 1fr 1fr; gap: 0.25rem 0.75rem; margin-top: 0.25rem;">
                                <div><strong>ID Project:</strong> ${p.idproject || '-'}</div>
                                <div><strong>Direktorat:</strong> ${p.direktorat || '-'}</div>
                                <div><strong>Lead By:</strong> ${p.assign_to_name || 'Belum Ditugaskan'}</div>
                                <div><strong>Tgl Awal:</strong> ${formatDate(p.tanggal_awal)}</div>
                            </div>
                            <div style="font-size: 0.75rem; color: ${c.text}; font-style: italic; margin-top: 0.25rem; border-top: 1px dashed ${c.border}; padding-top: 0.35rem;">
                                Target Jatuh Tempo: <strong>${formatDate(p.endYMD)}</strong>
                            </div>
                        </div>
                    `;
                });
            } else {
                bodyHTML += `
                    <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 12px; padding: 1.25rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                        <div style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #059669;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div style="font-weight: 700; font-size: 1rem; color: #059669;">Slot Available / Tanggal Bebas</div>
                        <div style="font-size: 0.825rem; color: var(--text-main); max-width: 420px; line-height: 1.4;">
                            Tidak ada beban project <strong>DEVELOPMENT</strong> yang aktif pada tanggal <strong>${formattedDate}</strong>.
                            Tanggal ini <strong>Available / Bebas</strong> dan siap dialokasikan untuk <strong>request pengembangan atau project baru</strong>.
                        </div>
                    </div>
                `;
            }
        }

        bodyEl.innerHTML = bodyHTML;
        modal.classList.add('active');
    };

    window.closeCalModal = function() {
        const modal = document.getElementById('calDateModal');
        if (modal) modal.classList.remove('active');
    };

    window.closeCalModalOutside = function(e) {
        if (e.target.id === 'calDateModal') {
            closeCalModal();
        }
    };

    window.changeCalMonth = function(delta) {
        calCurrentMonth += delta;
        if (calCurrentMonth > 11) {
            calCurrentMonth = 0;
            calCurrentYear++;
        } else if (calCurrentMonth < 0) {
            calCurrentMonth = 11;
            calCurrentYear--;
        }
        renderDevelopmentCalendar();
    };

    window.jumpCalToday = function() {
        const d = new Date();
        calCurrentYear = d.getFullYear();
        calCurrentMonth = d.getMonth();
        renderDevelopmentCalendar();
    };

    window.onCalLeadFilterChange = function(val) {
        selectedCalLeadFilter = val;
        renderDevelopmentCalendar();
    };

    // Initialize Chart.js & Calendar
    document.addEventListener("DOMContentLoaded", function() {
        // Initial render: show all detil table & calendar
        showAllProjects();
        renderDevelopmentCalendar();

        // Chart implementation: Donut Chart Direktorat
        const ctx = document.getElementById('direktoratChart').getContext('2d');
        
        const labels = chartData.map(d => d.direktorat);
        const dataValues = chartData.map(d => d.total);
        
        // Generate nice vibrant colors
        const colors = [
            '#755f3e', '#54422b', '#9c825e', '#b59c77', '#c7ab83', 
            '#3f3526', '#87704e', '#d8c2a3', '#614d33', '#ab9471',
            '#4a3c28', '#8f7754', '#e2d1ba', '#5e4e37'
        ];
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#6e665d',
                            font: { family: "'Inter', sans-serif", size: 11 },
                            padding: 15,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(44, 39, 33, 0.95)',
                        titleFont: { family: "'Inter', sans-serif" },
                        bodyFont: { family: "'Inter', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        borderColor: 'rgba(117, 95, 62, 0.2)',
                        borderWidth: 1
                    }
                },
                cutout: '70%',
                onClick: (e, activeElements) => {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const selectedDirektorat = labels[index];
                        filterDetil(selectedDirektorat);
                    }
                }
            }
        });

        // Chart implementation: Bar Chart Lead By
        const leadCtx = document.getElementById('leadByChart').getContext('2d');
        const leadLabels = leadByData.map(d => d.lead_by);
        const leadValues = leadByData.map(d => d.total);

        const leadChart = new Chart(leadCtx, {
            type: 'bar',
            data: {
                labels: leadLabels,
                datasets: [{
                    label: 'Jumlah Project',
                    data: leadValues,
                    backgroundColor: 'rgba(117, 95, 62, 0.75)',
                    borderColor: '#755f3e',
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(117, 95, 62, 0.95)'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(44, 39, 33, 0.95)',
                        titleFont: { family: "'Inter', sans-serif" },
                        bodyFont: { family: "'Inter', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        borderColor: 'rgba(117, 95, 62, 0.2)',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#6e665d',
                            font: { family: "'Inter', sans-serif", size: 11 }
                        },
                        grid: {
                            color: 'rgba(117, 95, 62, 0.08)'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#6e665d',
                            font: { family: "'Inter', sans-serif", size: 11 }
                        },
                        grid: { display: false }
                    }
                },
                onClick: (e, activeElements) => {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const selectedLead = leadLabels[index];
                        filterDetilByLead(selectedLead);
                    }
                }
            }
        });
    });
</script>
@endsection
