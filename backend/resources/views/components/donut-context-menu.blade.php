{{-- Global Donut Context Menu Component --}}
<div id="donut-context-menu-root" class="donut-context-menu-root" style="display: none;" aria-hidden="true">
    {{-- Click-catcher backdrop --}}
    <div class="donut-backdrop" id="donut-backdrop"></div>

    {{-- Main Donut Floating Container --}}
    <div class="donut-container" id="donut-container">
        {{-- Floating Project Title Badge --}}
        <div class="donut-project-badge" id="donut-project-badge">
            <span class="donut-badge-indicator"></span>
            <span class="donut-badge-title" id="donut-badge-title">Project Name</span>
            <button type="button" class="donut-badge-close" id="donut-badge-close" title="Tutup Menu">&times;</button>
        </div>

        {{-- SVG Radial Donut --}}
        <div class="donut-svg-wrapper">
            <svg id="donut-svg" class="donut-svg" viewBox="0 0 420 420" width="420" height="420">
                <defs>
                    {{-- Default Slice Gradient --}}
                    <linearGradient id="donut-grad-default" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#2c241c" />
                        <stop offset="50%" stop-color="#231b14" />
                        <stop offset="100%" stop-color="#18130e" />
                    </linearGradient>

                    {{-- Hover Slice Gradient --}}
                    <linearGradient id="donut-grad-hover" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#936d3b" />
                        <stop offset="50%" stop-color="#73542a" />
                        <stop offset="100%" stop-color="#4e3717" />
                    </linearGradient>

                    {{-- Active Slice Gradient --}}
                    <linearGradient id="donut-grad-active" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#166534" />
                        <stop offset="50%" stop-color="#14532d" />
                        <stop offset="100%" stop-color="#052e16" />
                    </linearGradient>

                    {{-- Center Hole Ring Shadow --}}
                    <filter id="donut-glow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="rgba(0,0,0,0.5)" />
                    </filter>
                    
                    <filter id="slice-hover-glow" x="-30%" y="-30%" width="160%" height="160%">
                        <feDropShadow dx="0" dy="0" stdDeviation="5" flood-color="rgba(226, 184, 102, 0.7)" />
                    </filter>
                </defs>

                {{-- Slices will be rendered here dynamically --}}
                <g id="donut-slices-group" class="donut-slices-group"></g>
            </svg>

            {{-- Center Hub (The Donut Hole) --}}
            <div class="donut-center-hub" id="donut-center-hub" title="Klik untuk tutup">
                <div class="donut-center-content" id="donut-center-content">
                    <img id="donut-center-icon" class="donut-center-icon" src="" alt="" style="display: none;" />
                    <div id="donut-center-title" class="donut-center-title">PILIH STATUS</div>
                    <div id="donut-center-sub" class="donut-center-sub">Klik icon status</div>
                </div>

                {{-- Spinner Loader --}}
                <div class="donut-center-loader" id="donut-center-loader" style="display: none;">
                    <div class="donut-spinner"></div>
                    <span class="donut-loader-text">Menyimpan...</span>
                </div>
            </div>
        </div>

        {{-- Helper hint below donut --}}
        <div class="donut-hint-pill">
            <span>Klik icon untuk update status &bull; ESC untuk batal</span>
        </div>
    </div>

    {{-- Global Toast Notification --}}
    <div id="donut-toast" class="donut-toast" style="display: none;">
        <div class="donut-toast-icon-wrap" id="donut-toast-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div class="donut-toast-body">
            <div class="donut-toast-title" id="donut-toast-title">Status Berhasil Diperbarui</div>
            <div class="donut-toast-message" id="donut-toast-message">Project telah diperbarui ke status baru.</div>
        </div>
        <button type="button" class="donut-toast-close" onclick="window.DonutContextMenu.hideToast()">&times;</button>
    </div>
</div>

<style>
/* === DONUT CONTEXT MENU STYLES === */
.donut-context-menu-root {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 999999;
    pointer-events: none;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.donut-context-menu-root.active {
    pointer-events: auto;
}

.donut-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 12, 10, 0.28);
    backdrop-filter: blur(2px);
    opacity: 0;
    transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.donut-context-menu-root.active .donut-backdrop {
    opacity: 1;
}

.donut-container {
    position: absolute;
    width: 420px;
    height: 420px;
    user-select: none;
    opacity: 0;
    transform: scale(calc(0.6 * var(--donut-scale, 1))) rotate(-15deg);
    transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    transform-origin: center center;
    filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.65));
}

.donut-context-menu-root.active .donut-container {
    opacity: 1;
    transform: scale(var(--donut-scale, 1)) rotate(0deg);
}

/* Floating Project Badge */
.donut-project-badge {
    position: absolute;
    top: -44px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, rgba(38, 30, 23, 0.96), rgba(24, 18, 14, 0.98));
    border: 1.5px solid rgba(226, 184, 102, 0.45);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    padding: 8px 18px 8px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 330px;
    backdrop-filter: blur(10px);
    animation: donutBadgeSlideDown 0.3s ease-out;
}

@keyframes donutBadgeSlideDown {
    from { opacity: 0; transform: translate(-50%, -10px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}

.donut-badge-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 10px #10b981;
    flex-shrink: 0;
}

.donut-badge-title {
    color: #f7eedf;
    font-size: 0.98rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: 0.3px;
}

.donut-badge-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.6);
    font-size: 1.45rem;
    cursor: pointer;
    line-height: 1;
    padding: 0 4px;
    margin-left: 4px;
    transition: color 0.2s ease, transform 0.2s ease;
}

.donut-badge-close:hover {
    color: #ff6b6b;
    transform: scale(1.2);
}

/* SVG Wrapper */
.donut-svg-wrapper {
    position: relative;
    width: 420px;
    height: 420px;
}

.donut-svg {
    display: block;
    overflow: visible;
}

/* Donut Slice Graphics */
.donut-slice {
    cursor: pointer;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    transform-origin: 210px 210px;
}

.donut-slice-path {
    fill: url(#donut-grad-default);
    stroke: rgba(226, 184, 102, 0.25);
    stroke-width: 2.8;
    transition: fill 0.25s ease, stroke 0.25s ease, stroke-width 0.25s ease, filter 0.25s ease;
}

.donut-slice:hover .donut-slice-path {
    fill: url(#donut-grad-hover);
    stroke: #ffd479;
    stroke-width: 4.8;
    filter: url(#slice-hover-glow);
}

.donut-slice.is-active .donut-slice-path {
    fill: url(#donut-grad-active);
    stroke: #34d399;
    stroke-width: 4.5;
}

.donut-slice.is-active:hover .donut-slice-path {
    stroke: #6ee7b7;
    filter: drop-shadow(0 0 12px rgba(52, 211, 153, 0.8));
}

.donut-slice-icon {
    pointer-events: none;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), filter 0.25s ease;
    filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.55));
}

.donut-slice:hover .donut-slice-icon {
    transform: scale(1.15);
    filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.75));
}

.donut-slice:active {
    transform: scale(0.98);
}

/* Center Hub */
.donut-center-hub {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 165px;
    height: 165px;
    border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, rgba(48, 38, 29, 0.98), rgba(20, 16, 12, 0.99));
    border: 3.5px solid rgba(226, 184, 102, 0.45);
    box-shadow: inset 0 4px 14px rgba(0, 0, 0, 0.65), 0 12px 32px rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    z-index: 10;
    transition: border-color 0.25s ease, transform 0.2s ease, box-shadow 0.25s ease;
    backdrop-filter: blur(12px);
}

.donut-center-hub:hover {
    border-color: #ffd479;
    box-shadow: inset 0 4px 14px rgba(0, 0, 0, 0.65), 0 0 24px rgba(226, 184, 102, 0.5);
}

.donut-center-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px;
    width: 100%;
}

.donut-center-icon {
    width: 42px;
    height: 42px;
    object-fit: contain;
    margin-bottom: 8px;
    filter: drop-shadow(0 3px 6px rgba(0,0,0,0.45));
    transition: transform 0.2s ease;
}

.donut-center-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #ffd479;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    line-height: 1.2;
    margin-bottom: 4px;
    max-width: 143px;
    word-break: break-word;
}

.donut-center-sub {
    font-size: 0.9rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.7);
    line-height: 1.15;
    max-width: 135px;
}

/* Spinner */
.donut-center-loader {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.donut-spinner {
    width: 33px;
    height: 33px;
    border: 3.5px solid rgba(226, 184, 102, 0.2);
    border-top-color: #ffd479;
    border-radius: 50%;
    animation: donutSpin 0.7s linear infinite;
}

@keyframes donutSpin {
    to { transform: rotate(360deg); }
}

.donut-loader-text {
    font-size: 1.05rem;
    font-weight: 600;
    color: #ffd479;
}

/* Hint Pill below Donut */
.donut-hint-pill {
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(20, 16, 12, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    padding: 6px 18px;
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.7);
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(8px);
}

/* Toast Notification */
.donut-toast {
    position: fixed;
    top: 24px;
    right: 24px;
    background: linear-gradient(135deg, rgba(35, 28, 22, 0.96), rgba(20, 16, 12, 0.98));
    border: 1px solid rgba(226, 184, 102, 0.4);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 12px 18px 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 1000000;
    min-width: 300px;
    max-width: 440px;
    pointer-events: auto;
    backdrop-filter: blur(12px);
    animation: donutToastIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes donutToastIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.donut-toast.toast-error {
    border-color: rgba(239, 68, 68, 0.5);
}

.donut-toast-icon-wrap {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #34d399;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.donut-toast.toast-error .donut-toast-icon-wrap {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.35);
    color: #f87171;
}

.donut-toast-body {
    flex: 1;
    min-width: 0;
}

.donut-toast-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 2px;
}

.donut-toast-message {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.75);
    line-height: 1.35;
}

.donut-toast-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    font-size: 1.25rem;
    cursor: pointer;
    line-height: 1;
    padding: 2px 4px;
    transition: color 0.2s ease;
}

.donut-toast-close:hover {
    color: #fff;
}

/* Row Flash Highlight */
.row-status-updated {
    animation: rowUpdatedFlash 1.8s ease-out;
}

@keyframes rowUpdatedFlash {
    0% { background-color: rgba(226, 184, 102, 0.35) !important; }
    50% { background-color: rgba(226, 184, 102, 0.15) !important; }
    100% { background-color: transparent; }
}

/* Hover style for clickable project rows */
tr[data-idproject] {
    cursor: context-menu;
}
</style>

<script>
/**
 * Global Radial Donut Context Menu
 * Provides right-click interactive radial status switcher.
 */
(function() {
    // Shared or Fallback dataset of rprojects
    let rprojectsData = @json($donutRprojects ?? []);
    const ASSET_ICON_BASE = "{{ asset('icons') }}";
    const QUICK_UPDATE_URL = "{{ route('project.quick_status_update') }}";
    const DONUT_USER_LEVEL = @json(Auth::user()->levelpengguna ?? null);
    const DONUT_USER_ID = @json(Auth::user()->idpengguna ?? null);

    // Active state
    let activeProjectId = null;
    let activeProjectName = '';
    let activeCurrentStatus = null;
    let activeSourceElement = null;
    let isUpdating = false;
    let toastTimeout = null;

    // DOM References
    let rootEl, backdropEl, containerEl, slicesGroupEl, projectBadgeEl, projectTitleEl, centerHubEl, centerContentEl, centerIconEl, centerTitleEl, centerSubEl, centerLoaderEl, toastEl, toastTitleEl, toastMsgEl, toastIconEl;

    function initDOM() {
        rootEl = document.getElementById('donut-context-menu-root');
        backdropEl = document.getElementById('donut-backdrop');
        containerEl = document.getElementById('donut-container');
        slicesGroupEl = document.getElementById('donut-slices-group');
        projectBadgeEl = document.getElementById('donut-project-badge');
        projectTitleEl = document.getElementById('donut-badge-title');
        centerHubEl = document.getElementById('donut-center-hub');
        centerContentEl = document.getElementById('donut-center-content');
        centerIconEl = document.getElementById('donut-center-icon');
        centerTitleEl = document.getElementById('donut-center-title');
        centerSubEl = document.getElementById('donut-center-sub');
        centerLoaderEl = document.getElementById('donut-center-loader');
        toastEl = document.getElementById('donut-toast');
        toastTitleEl = document.getElementById('donut-toast-title');
        toastMsgEl = document.getElementById('donut-toast-message');
        toastIconEl = document.getElementById('donut-toast-icon');

        // Close bindings
        if (backdropEl) backdropEl.addEventListener('click', closeMenu);
        const closeBtn = document.getElementById('donut-badge-close');
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (centerHubEl) centerHubEl.addEventListener('click', function(e) {
            if (!isUpdating) closeMenu();
        });

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && rootEl.classList.contains('active')) {
                closeMenu();
            }
        });
    }

    /**
     * Fetch rproject list dynamically if empty
     */
    async function ensureRprojectsLoaded() {
        if (rprojectsData && rprojectsData.length > 0) return rprojectsData;
        try {
            const res = await fetch("{{ route('rproject.api_list') }}", {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success && Array.isArray(data.data)) {
                rprojectsData = data.data;
            }
        } catch (err) {
            console.error('Failed to load rproject list:', err);
        }
        return rprojectsData || [];
    }

    /**
     * Calculate SVG Arc Path for a Donut Wedge
     */
    function createDonutWedgePath(cx, cy, rOut, rIn, startAngleDeg, endAngleDeg) {
        const degToRad = (deg) => (deg * Math.PI) / 180;
        const startRad = degToRad(startAngleDeg);
        const endRad = degToRad(endAngleDeg);

        const x1Out = cx + rOut * Math.cos(startRad);
        const y1Out = cy + rOut * Math.sin(startRad);
        const x2Out = cx + rOut * Math.cos(endRad);
        const y2Out = cy + rOut * Math.sin(endRad);

        const x1In = cx + rIn * Math.cos(endRad);
        const y1In = cy + rIn * Math.sin(endRad);
        const x2In = cx + rIn * Math.cos(startRad);
        const y2In = cy + rIn * Math.sin(startRad);

        const largeArcFlag = (endAngleDeg - startAngleDeg) > 180 ? 1 : 0;

        return `M ${x1Out.toFixed(2)} ${y1Out.toFixed(2)} ` +
               `A ${rOut} ${rOut} 0 ${largeArcFlag} 1 ${x2Out.toFixed(2)} ${y2Out.toFixed(2)} ` +
               `L ${x1In.toFixed(2)} ${y1In.toFixed(2)} ` +
               `A ${rIn} ${rIn} 0 ${largeArcFlag} 0 ${x2In.toFixed(2)} ${y2In.toFixed(2)} ` +
               `Z`;
    }

    /**
     * Render the Donut slices symmetrically based on rprojects count
     */
    function renderSlices() {
        if (!slicesGroupEl) return;
        slicesGroupEl.innerHTML = '';

        const items = rprojectsData || [];
        const count = items.length;
        if (count === 0) return;

        const cx = 210;
        const cy = 210;
        const rOut = 198;
        const rIn = 97.5;
        const rMid = (rOut + rIn) / 2; // 147.75
        const iconSize = 51;

        const sliceAngle = 360 / count;
        // Subtle separation gap between wedges
        const gap = count > 1 ? Math.min(2.5, 360 / (count * 4)) : 0;

        items.forEach((item, index) => {
            // Symmetrical angle: starting from 12 o'clock (-90 degrees)
            const centerAngle = -90 + (index * sliceAngle);
            const startAngle = centerAngle - (sliceAngle / 2) + (gap / 2);
            const endAngle = centerAngle + (sliceAngle / 2) - (gap / 2);

            const pathD = createDonutWedgePath(cx, cy, rOut, rIn, startAngle, endAngle);

            // Icon position (mid-angle & mid-radius)
            const iconAngleRad = (centerAngle * Math.PI) / 180;
            const iconX = cx + rMid * Math.cos(iconAngleRad);
            const iconY = cy + rMid * Math.sin(iconAngleRad);

            // Group container for slice
            const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            group.setAttribute('class', 'donut-slice' + (activeCurrentStatus == item.id ? ' is-active' : ''));
            group.setAttribute('data-id', item.id);
            group.setAttribute('data-index', index);
            group.setAttribute('data-deskripsi', item.deskripsi);
            group.setAttribute('data-icon', item.icon || '');
            group.setAttribute('role', 'button');
            group.setAttribute('tabindex', '0');

            // Wedge path
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', pathD);
            path.setAttribute('class', 'donut-slice-path');

            // Icon Image
            const img = document.createElementNS('http://www.w3.org/2000/svg', 'image');
            const iconUrl = item.icon_url || (ASSET_ICON_BASE + '/' + item.icon);
            img.setAttributeNS('http://www.w3.org/1999/xlink', 'href', iconUrl);
            img.setAttribute('href', iconUrl);
            img.setAttribute('x', (iconX - iconSize / 2).toFixed(2));
            img.setAttribute('y', (iconY - iconSize / 2).toFixed(2));
            img.setAttribute('width', iconSize);
            img.setAttribute('height', iconSize);
            img.setAttribute('class', 'donut-slice-icon');
            img.setAttribute('preserveAspectRatio', 'xMidYMid meet');

            // Active badge dot indicator on outer edge if current status
            if (activeCurrentStatus == item.id) {
                const activeDotAngle = iconAngleRad;
                const dotR = rOut - 12;
                const dotX = cx + dotR * Math.cos(activeDotAngle);
                const dotY = cy + dotR * Math.sin(activeDotAngle);
                const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                dot.setAttribute('cx', dotX.toFixed(2));
                dot.setAttribute('cy', dotY.toFixed(2));
                dot.setAttribute('r', '7');
                dot.setAttribute('fill', '#34d399');
                dot.setAttribute('filter', 'drop-shadow(0 0 6px #10b981)');
                group.appendChild(path);
                group.appendChild(img);
                group.appendChild(dot);
            } else {
                group.appendChild(path);
                group.appendChild(img);
            }

            // Slice Hover Events: update center hub preview
            group.addEventListener('mouseenter', function() {
                showSlicePreview(item);
            });

            group.addEventListener('mouseleave', function() {
                resetCenterHub();
            });

            // Slice Click Event: trigger status update
            group.addEventListener('click', function(e) {
                e.stopPropagation();
                if (isUpdating) return;
                handleSliceSelect(item);
            });

            slicesGroupEl.appendChild(group);
        });
    }

    /**
     * Preview slice info in the center hub
     */
    function showSlicePreview(item) {
        if (isUpdating) return;
        const iconUrl = item.icon_url || (ASSET_ICON_BASE + '/' + item.icon);
        centerIconEl.src = iconUrl;
        centerIconEl.style.display = 'block';
        centerTitleEl.innerText = item.deskripsi || 'Status';
        if (activeCurrentStatus == item.id) {
            centerSubEl.innerText = '(Status Saat Ini)';
            centerSubEl.style.color = '#34d399';
        } else {
            centerSubEl.innerText = 'Klik untuk ubah';
            centerSubEl.style.color = '#ffd479';
        }
    }

    /**
     * Reset center hub to idle or current status
     */
    function resetCenterHub() {
        if (isUpdating) return;
        const currentItem = (rprojectsData || []).find(r => r.id == activeCurrentStatus);
        if (currentItem) {
            const iconUrl = currentItem.icon_url || (ASSET_ICON_BASE + '/' + currentItem.icon);
            centerIconEl.src = iconUrl;
            centerIconEl.style.display = 'block';
            centerTitleEl.innerText = currentItem.deskripsi;
            centerSubEl.innerText = 'Status saat ini';
            centerSubEl.style.color = '#34d399';
        } else {
            centerIconEl.style.display = 'none';
            centerTitleEl.innerText = 'PILIH STATUS';
            centerSubEl.innerText = 'Klik icon status';
            centerSubEl.style.color = 'rgba(255, 255, 255, 0.6)';
        }
    }

    /**
     * Handle slice selection and execute AJAX status update
     */
    async function handleSliceSelect(item) {
        isUpdating = true;
        centerContentEl.style.display = 'none';
        centerLoaderEl.style.display = 'flex';

        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

        try {
            const response = await fetch(QUICK_UPDATE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    idproject: activeProjectId,
                    rproject: item.id
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Success feedback in center hub
                centerLoaderEl.innerHTML = `
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span style="color:#34d399; font-weight:700; font-size:1.15rem;">Berhasil!</span>
                `;

                // Update DOM elements on page
                updateDOMProjectStatus(activeProjectId, item, result.data);

                // Show notification toast
                showToast(`Status project "${activeProjectName}" berhasil diubah menjadi "${item.deskripsi}".`, 'success');

                // Trigger global custom event
                window.dispatchEvent(new CustomEvent('project-status-updated', {
                    detail: result.data || { idproject: activeProjectId, rproject: item.id, status_name: item.deskripsi }
                }));

                // Auto close menu
                setTimeout(() => {
                    closeMenu();
                    isUpdating = false;
                    centerContentEl.style.display = 'flex';
                    centerLoaderEl.style.display = 'none';
                    centerLoaderEl.innerHTML = `
                        <div class="donut-spinner"></div>
                        <span class="donut-loader-text">Menyimpan...</span>
                    `;
                }, 400);

            } else {
                throw new Error(result.message || 'Gagal mengubah status project.');
            }
        } catch (err) {
            console.error('Update status error:', err);
            showToast(err.message || 'Terjadi kesalahan saat update status.', 'error');
            isUpdating = false;
            centerContentEl.style.display = 'flex';
            centerLoaderEl.style.display = 'none';
            resetCenterHub();
        }
    }

    /**
     * Dynamically update table row / UI without full reload
     */
    function updateDOMProjectStatus(idproject, targetStatus, fullData) {
        // Find all elements associated with this project ID
        const matchedElements = document.querySelectorAll(`[data-idproject="${idproject}"], [data-project-id="${idproject}"]`);

        matchedElements.forEach(el => {
            el.setAttribute('data-rproject', targetStatus.id);
            el.setAttribute('data-status-id', targetStatus.id);

            // Flash highlight row
            el.classList.remove('row-status-updated');
            void el.offsetWidth; // trigger reflow
            el.classList.add('row-status-updated');

            // 1. In project/index.blade.php (Column 5 is Status)
            const statusCell = el.querySelector('td.status-cell, td[data-col="status"]');
            if (statusCell) {
                statusCell.innerText = targetStatus.deskripsi;
            } else {
                // Table without explicit classes: check cells
                const tds = el.querySelectorAll('td');
                if (tds.length >= 5) {
                    // In project/index: th = [Nama Project, Tanggal, Direktorat, Status, RKAP, Aksi]
                    // If hidden ID column is present (length 7): Status is index 4
                    // If length 6: Status is index 3
                    const statusIndex = tds.length === 7 ? 4 : (tds.length === 6 ? 3 : -1);
                    if (statusIndex >= 0 && tds[statusIndex]) {
                        tds[statusIndex].innerText = targetStatus.deskripsi;
                    }
                }
            }

            // 2. In laporan/progress/index.blade.php (First column is Icon image, 11th is status text)
            const iconImg = el.querySelector('td img[title], td.icon-col img');
            if (iconImg) {
                iconImg.src = ASSET_ICON_BASE + '/' + targetStatus.icon;
                iconImg.alt = targetStatus.deskripsi;
                iconImg.title = targetStatus.deskripsi;
            }

            // 3. In kanban/index.blade.php
            const stepCells = el.querySelectorAll('.step-cell');
            if (stepCells.length > 0) {
                stepCells.forEach(cell => {
                    cell.innerHTML = `
                        <div class="step-empty-action">
                            <span>Pindah ke sini &rarr;</span>
                        </div>
                    `;
                });
                // Activate the new step column
                const targetStepCell = el.querySelector(`.step-cell[data-step-id="${targetStatus.id}"]`);
                if (targetStepCell) {
                    targetStepCell.innerHTML = `
                        <div class="step-active-icon">
                            <img src="${ASSET_ICON_BASE}/${targetStatus.icon}" alt="${targetStatus.deskripsi}" class="step-icon-img" title="${targetStatus.deskripsi}">
                        </div>
                    `;
                }
            }
        });
    }

    /**
     * Open Donut Context Menu at coordinates (x, y)
     */
    async function openMenu(options) {
        if (!rootEl) initDOM();

        await ensureRprojectsLoaded();

        activeProjectId = options.idproject;
        activeProjectName = options.projectName || `Project #${activeProjectId}`;
        activeCurrentStatus = options.currentStatus || null;
        activeSourceElement = options.sourceElement || null;
        isUpdating = false;

        // Set Project Badge Title
        if (projectTitleEl) {
            projectTitleEl.innerText = activeProjectName;
            projectTitleEl.title = activeProjectName;
        }

        // Render Slices
        renderSlices();
        resetCenterHub();

        // Calculate Position & Clamp within Viewport
        const donutSize = 420;
        const halfSize = donutSize / 2;
        const margin = 16;
        const totalHeight = donutSize + 86; // including badge and hint

        let posX = options.x - halfSize;
        let posY = options.y - halfSize;

        const maxPosX = window.innerWidth - donutSize - margin;
        const maxPosY = window.innerHeight - totalHeight - margin;

        posX = Math.max(margin, Math.min(posX, maxPosX));
        posY = Math.max(margin + 44, Math.min(posY, maxPosY));

        containerEl.style.left = `${posX}px`;
        containerEl.style.top = `${posY}px`;

        // Responsive auto-scale if viewport is smaller than donut menu
        const availW = window.innerWidth - (margin * 2);
        const availH = window.innerHeight - (margin * 2) - 60;
        let scaleFactor = 1;
        if (availW < donutSize || availH < donutSize) {
            scaleFactor = Math.min(1, Math.max(0.45, Math.min(availW / donutSize, availH / donutSize)));
        }
        containerEl.style.setProperty('--donut-scale', scaleFactor);

        // Reveal menu with animation
        rootEl.style.display = 'block';
        requestAnimationFrame(() => {
            rootEl.classList.add('active');
            rootEl.setAttribute('aria-hidden', 'false');
        });
    }

    /**
     * Close Donut Context Menu
     */
    function closeMenu() {
        if (!rootEl) return;
        rootEl.classList.remove('active');
        rootEl.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            if (!rootEl.classList.contains('active')) {
                rootEl.style.display = 'none';
            }
        }, 260);
    }

    /**
     * Show Toast Notification
     */
    function showToast(message, type = 'success') {
        if (!toastEl) initDOM();
        if (toastTimeout) clearTimeout(toastTimeout);

        toastEl.className = 'donut-toast' + (type === 'error' ? ' toast-error' : '');
        toastTitleEl.innerText = type === 'error' ? 'Gagal Update Status' : 'Status Berhasil Diperbarui';
        toastMsgEl.innerText = message;

        if (type === 'error') {
            toastIconEl.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
        } else {
            toastIconEl.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
        }

        toastEl.style.display = 'flex';

        toastTimeout = setTimeout(() => {
            hideToast();
        }, 4000);
    }

    function hideToast() {
        if (toastEl) {
            toastEl.style.display = 'none';
        }
    }

    /**
     * Global Right-Click Event Listener
     */
    function setupGlobalEventListener() {
        document.addEventListener('contextmenu', function(e) {
            // Find closest element with project ID info
            const projectRow = e.target.closest('[data-idproject], [data-project-id]');
            if (!projectRow) return;

            // Restriction: non-admin (level != 0) may only open donut menu on projects they lead
            const rowLeadBy = projectRow.getAttribute('data-leadby')
                || projectRow.getAttribute('data-lead-by')
                || projectRow.getAttribute('data-assign-to')
                || projectRow.getAttribute('data-assign_to');
            const isAdmin = (DONUT_USER_LEVEL === 0 || DONUT_USER_LEVEL === '0');
            if (!isAdmin && String(rowLeadBy ?? '') !== String(DONUT_USER_ID ?? '')) {
                return;
            }

            // Prevent default browser context menu
            e.preventDefault();

            const projectId = projectRow.getAttribute('data-idproject') || projectRow.getAttribute('data-project-id');
            if (!projectId) return;

            // Extract project name if available
            let projectName = projectRow.getAttribute('data-project-name');
            if (!projectName) {
                const nameLink = projectRow.querySelector('a[href*="hproject"], .project-name');
                if (nameLink) projectName = nameLink.innerText.trim();
                else projectName = `Project #${projectId}`;
            }

            const currentStatus = projectRow.getAttribute('data-rproject') || projectRow.getAttribute('data-status-id');

            openMenu({
                x: e.clientX,
                y: e.clientY,
                idproject: projectId,
                projectName: projectName,
                currentStatus: currentStatus,
                sourceElement: projectRow
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initDOM();
            setupGlobalEventListener();
        });
    } else {
        initDOM();
        setupGlobalEventListener();
    }

    // Public Global API
    window.DonutContextMenu = {
        open: openMenu,
        close: closeMenu,
        showToast: showToast,
        hideToast: hideToast,
        setRprojects: function(data) { rprojectsData = data; renderSlices(); }
    };
})();
</script>
