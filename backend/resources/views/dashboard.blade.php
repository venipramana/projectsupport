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

    /* Section Detil */
    .section-detil {
        margin-top: 1rem;
    }

    /* Universal Card Styles */
    .card {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    
    .card:hover {
        border-color: rgba(139, 92, 246, 0.3);
    }

    .card-header {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        padding-bottom: 0.75rem;
    }

    /* Block 1: Total Projects */
    .total-projects-card {
        justify-content: center;
        align-items: center;
        text-align: center;
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(59, 130, 246, 0.2));
        border: 1px solid rgba(139, 92, 246, 0.3);
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
        background: linear-gradient(to right, #a855f7, #3b82f6);
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
        background: rgba(255, 255, 255, 0.02);
        border-radius: 10px;
    }
    .list-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .list-container::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.5);
    }

    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.03);
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .list-item:hover {
        background: rgba(139, 92, 246, 0.1);
        border-color: rgba(139, 92, 246, 0.2);
        transform: translateX(3px);
    }
    .list-item.interactive:hover {
        background: rgba(59, 130, 246, 0.15);
        border-color: rgba(59, 130, 246, 0.3);
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
        background: rgba(139, 92, 246, 0.2);
        color: #c4b5fd;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Detil Section */
    .table-container {
        overflow-x: auto;
        background: rgba(15, 23, 42, 0.6);
        border-radius: 12px;
        border: 1px solid var(--glass-border);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    th {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        font-weight: 600;
        text-align: left;
        padding: 1rem;
        white-space: nowrap;
    }
    td {
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        color: var(--text-main);
    }
    tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(139, 92, 246, 0.15);
        color: #c4b5fd;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }
    #detil-title {
        color: #60a5fa;
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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #8b5cf6;"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
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
                <div class="card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Summary Direktorat ({{ $currentYear }})
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
                <div class="card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #10b981;"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                    Status Development ({{ $dev_projects->count() }})
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
                                    $colorJatuhTempo = "#34d399";
                                } elseif ($diffDays === 0) {
                                    $statusJatuhTempo = "Hari ini";
                                    $colorJatuhTempo = "#fbbf24";
                                } else {
                                    $statusJatuhTempo = "Terlambat " . abs($diffDays) . " hari";
                                    $colorJatuhTempo = "#f87171";
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
                            <span class="list-item-badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; align-self: flex-start;">DEV</span>
                        </div>
                    @empty
                        <div class="empty-state">Tidak ada project Development.</div>
                    @endforelse
                </div>
            </div>

            <!-- Block 5: QA Projects -->
            <div class="card">
                <div class="card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #f59e0b;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Status QA / UAT ({{ $qa_projects->count() }})
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
                                <span style="font-size: 0.75rem; font-weight: 500; {{ $isOver14Days ? 'color: #ffffff;' : 'color: #fde68a;' }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline; vertical-align: -1px; margin-right: 2px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    Durasi: {{ $durasiHari }} hari (Tgl Update: {{ $tglUpdateFormatted }})
                                </span>
                            </div>
                            <span class="list-item-badge" style="align-self: flex-start; {{ $isOver14Days ? 'background: rgba(255,255,255,0.25); color: #ffffff; border: 1px solid rgba(255,255,255,0.4);' : 'background: rgba(245, 158, 11, 0.15); color: #fbbf24;' }}">
                                QA
                            </span>
                        </div>
                    @empty
                        <div class="empty-state">Tidak ada project QA / UAT.</div>
                    @endforelse
                </div>
            </div>
        </div>
        
    </div>

    <!-- SECTION DETIL -->
    <div class="section-detil">
        <div class="card">
            <div class="card-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #ec4899;"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                Section Detil: <span id="detil-title" style="margin-left: 0.5rem;">Pilih Direktorat pada Chart atau List di atas</span>
                <button onclick="showAllProjects()" style="margin-left: auto; background: transparent; border: 1px solid var(--glass-border); color: var(--text-main); padding: 0.25rem 0.75rem; border-radius: 8px; cursor: pointer; font-size: 0.8rem; transition: background 0.2s;">Tampilkan Semua</button>
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
    
    // Formatting date
    function formatDate(dateString) {
        if (!dateString) return '-';
        const d = new Date(dateString);
        return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
    }

    // Function to render table
    function renderDetilTable(data, title) {
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
    
    // Show All
    window.showAllProjects = function() {
        renderDetilTable(allProjects, 'Semua Direktorat');
    }

    // Initialize Chart.js
    document.addEventListener("DOMContentLoaded", function() {
        // Initial render: show all
        showAllProjects();

        // Chart implementation
        const ctx = document.getElementById('direktoratChart').getContext('2d');
        
        const labels = chartData.map(d => d.direktorat);
        const dataValues = chartData.map(d => d.total);
        
        // Generate nice vibrant colors
        const colors = [
            '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
            '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#84cc16',
            '#06b6d4', '#d946ef', '#f43f5e', '#0ea5e9'
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
                            color: '#94a3b8',
                            font: { family: "'Inter', sans-serif", size: 11 },
                            padding: 15,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { family: "'Inter', sans-serif" },
                        bodyFont: { family: "'Inter', sans-serif" },
                        padding: 12,
                        cornerRadius: 8,
                        borderColor: 'rgba(255,255,255,0.1)',
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
    });
</script>
@endsection
