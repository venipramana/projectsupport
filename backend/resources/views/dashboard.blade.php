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

    // Function to render table
    function renderDetilTable(data, title) {
        currentDetilData = data;
        currentDetilTitle = title;
        document.getElementById('detil-title').innerText = title;
        const tbody = document.getElementById('detil-tbody');
        const emptyState = document.getElementById('detil-empty');
        const table = document.getElementById('detil-table');
        
        tbody.innerHTML = '';
        
        if (data.length === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }
        
        table.style.display = 'table';
        emptyState.style.display = 'none';
        
        data.forEach((proj, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
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
    }

    // Filter Function
    function filterDetil(direktorat, year = null) {
        let filtered = allProjects.filter(p => p.direktorat === direktorat);
        let title = direktorat;
        
        if (year) {
            filtered = filtered.filter(p => p.tanggal_awal && new Date(p.tanggal_awal).getFullYear() == year);
            title = direktorat + " (" + year + ")";
        }
        
        renderDetilTable(filtered, title);
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
        renderDetilTable(filtered, "Lead By: " + leadName);
        document.querySelector('.section-detil').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Show All
    window.showAllProjects = function() {
        renderDetilTable(allProjects, 'Semua Project');
    }

    // Initialize Chart.js
    document.addEventListener("DOMContentLoaded", function() {
        // Initial render: show all
        showAllProjects();

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
