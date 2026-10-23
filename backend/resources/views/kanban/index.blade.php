@extends('layouts.app')

@section('title', 'Kanban Progress')
@section('header-title', 'Kanban Progress')

@section('custom-head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endsection

@section('custom-styles')
<style>
    /* Metric / KPI Cards */
    .kpi-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 4px 15px rgba(117, 95, 62, 0.05);
        backdrop-filter: blur(10px);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        text-decoration: none;
        color: inherit;
        position: relative;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(117, 95, 62, 0.1);
    }

    /* KPI Card Dev Focus */
    .kpi-card.dev-focus {
        background: linear-gradient(135deg, rgba(254, 243, 199, 0.65), rgba(255, 255, 255, 0.9));
        border: 2px solid rgba(217, 119, 6, 0.5);
        box-shadow: 0 6px 20px rgba(217, 119, 6, 0.12);
        overflow: hidden;
    }

    .kpi-card.dev-focus::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #d97706, #f59e0b, #b45309);
    }

    .kpi-card.dev-focus:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(217, 119, 6, 0.2);
        border-color: #d97706;
    }

    .kpi-badge-focus {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        background: #d97706;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 0.25rem;
        width: fit-content;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kpi-icon.primary {
        background: rgba(117, 95, 62, 0.15);
        color: var(--primary);
    }

    .kpi-icon.dev {
        background: rgba(217, 119, 6, 0.2);
        color: #d97706;
    }

    .kpi-icon.warning {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }

    .kpi-icon.success {
        background: rgba(34, 197, 94, 0.15);
        color: #16a34a;
    }

    .kpi-info {
        display: flex;
        flex-direction: column;
    }

    .kpi-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    .kpi-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Quick Filter Pills */
    .quick-filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .quick-filter-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-right: 0.25rem;
    }

    .filter-pill {
        padding: 0.45rem 0.95rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid var(--glass-border);
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(117, 95, 62, 0.04);
    }

    .filter-pill:hover {
        background: #ffffff;
        border-color: var(--primary);
        transform: translateY(-1px);
        color: var(--primary);
    }

    .filter-pill.active {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(117, 95, 62, 0.25);
    }

    .filter-pill.dev-pill {
        background: rgba(254, 243, 199, 0.8);
        border: 1px solid rgba(217, 119, 6, 0.4);
        color: #92400e;
        font-weight: 700;
    }

    .filter-pill.dev-pill:hover {
        background: #fef3c7;
        border-color: #d97706;
        color: #78350f;
    }

    .filter-pill.dev-pill.active {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35);
    }

    .pill-count {
        background: rgba(0, 0, 0, 0.08);
        padding: 0.1rem 0.45rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .filter-pill.active .pill-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Filter & Actions Bar */
    .filter-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(117, 95, 62, 0.04);
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        flex: 1;
        min-width: 170px;
    }

    .filter-group.search-group {
        flex: 2;
        min-width: 230px;
    }

    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid rgba(117, 95, 62, 0.25);
        color: var(--text-main);
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control option {
        background-color: #ffffff;
        color: var(--text-main);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(117, 95, 62, 0.15);
    }

    .btn {
        width: auto;
        margin: 0;
        padding: 0.65rem 1.25rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        height: 40px;
        box-sizing: border-box;
    }

    .btn-primary {
        width: auto !important;
        margin: 0 !important;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 12px rgba(117, 95, 62, 0.25);
    }

    .btn-primary:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.9);
        color: var(--text-main);
        border: 1px solid var(--glass-border);
    }

    .btn-secondary:hover {
        background: #ffffff;
        border-color: var(--primary);
    }

    /* Kanban Matrix Board */
    .kanban-board-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(117, 95, 62, 0.05);
        backdrop-filter: blur(10px);
    }

    .kanban-board-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.4);
    }

    .kanban-board-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .kanban-table-wrapper {
        width: 100%;
        overflow-x: auto;
        background: #ffffff;
    }

    /* Kanban Grid Table matching user specification */
    .kanban-board-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        min-width: 960px;
        background: #ffffff;
        border: 2px solid #2c2721;
    }

    .kanban-board-table th,
    .kanban-board-table td {
        border: 1px solid #2c2721;
        box-sizing: border-box;
    }

    /* Header Row Merged: Judul kolom dan icon dalam 1 cell */
    .header-row-merged th {
        background: #ede8df;
        color: #2c2721;
        font-weight: 800;
        font-size: 0.88rem;
        padding: 0.85rem 0.5rem;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 5;
        vertical-align: middle;
        transition: background-color 0.2s ease, outline 0.2s ease;
    }

    .header-row-merged th.col-dev-header {
        background: #fde68a !important;
        color: #92400e !important;
        border-top: 3px solid #d97706 !important;
    }

    .header-cell-merged {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
    }

    .header-title-box {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .header-stage-name {
        font-weight: 800;
        font-size: 0.86rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }

    .header-stage-badge {
        background: rgba(44, 39, 33, 0.14);
        color: #2c2721;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.1rem 0.45rem;
        border-radius: 999px;
    }

    .col-dev-header .header-stage-badge {
        background: #d97706;
        color: #ffffff;
    }

    .header-icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 42px;
    }

    .header-step-icon {
        width: 38px;
        height: 38px;
        max-width: 42px;
        max-height: 42px;
        object-fit: contain;
        display: inline-block;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .header-step-icon:hover {
        transform: scale(1.18);
    }

    .header-icon-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(117, 95, 62, 0.15);
        color: var(--primary);
        font-weight: 800;
        font-size: 0.85rem;
    }

    /* Drag and Drop Visual Feedback */
    .draggable-card {
        cursor: grab;
        user-select: none;
        -webkit-user-drag: element;
        transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease, background-color 0.15s ease;
        border-radius: 6px;
        padding: 0.25rem 0.35rem;
        position: relative;
    }

    .draggable-card:hover {
        background: rgba(117, 95, 62, 0.08);
    }

    .draggable-card:active {
        cursor: grabbing;
    }

    .draggable-card.is-dragging {
        opacity: 0.4 !important;
        transform: scale(0.96);
        background: #fef3c7 !important;
        border: 1.5px dashed #d97706 !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .draggable-card.is-updating {
        pointer-events: none;
        opacity: 0.6;
        background: #fef9c3 !important;
    }

    .project-drag-grip {
        display: inline-flex;
        align-items: center;
        color: #a8a29e;
        margin-right: 0.3rem;
        vertical-align: middle;
        cursor: grab;
    }

    .draggable-card:hover .project-drag-grip {
        color: #78716c;
    }

    /* Drop Target Highlights */
    .drag-active-col {
        background-color: rgba(245, 158, 11, 0.04) !important;
    }

    .kanban-table-cell.drop-target-hover,
    .kanban-col-header.drop-target-hover {
        background-color: rgba(217, 119, 6, 0.2) !important;
        outline: 2.5px dashed #d97706 !important;
        outline-offset: -2px !important;
    }

    /* Floating Drag-Drop Toast Banner */
    .kanban-dnd-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #2c2721;
        color: #ffffff;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        z-index: 9999;
        font-size: 0.88rem;
        font-weight: 600;
        transform: translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .kanban-dnd-toast.active {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .kanban-dnd-toast.toast-success {
        border-left: 4px solid #16a34a;
    }

    .kanban-dnd-toast.toast-error {
        border-left: 4px solid #dc2626;
    }

    .kanban-dnd-toast.toast-info {
        border-left: 4px solid #d97706;
    }

    /* Table Data Cells */
    .kanban-table-cell {
        padding: 0.6rem 0.65rem;
        min-height: 48px;
        height: 48px;
        vertical-align: top;
        background: #ffffff;
        transition: background-color 0.15s ease;
    }

    .kanban-table-cell.has-project {
        cursor: pointer;
    }

    .kanban-table-cell.has-project:hover {
        background-color: #faf6f0;
    }

    .kanban-table-cell.empty-cell {
        background: #ffffff;
    }

    .project-card-box {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        width: 100%;
    }

    /* Requirement 2: Text-wrap jika nama project melebihi lebar kolom */
    .project-name-wrap {
        font-size: 0.85rem;
        font-weight: 600;
        color: #2c2721;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        text-wrap: wrap !important;
        line-height: 1.35;
    }

    .kanban-table-cell.has-project:hover .project-name-wrap {
        color: var(--primary, #755f3e);
    }

    .project-sub-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        align-items: center;
        font-size: 0.7rem;
        color: #78716c;
        margin-top: 2px;
    }

    .sub-badge-dir {
        background: rgba(117, 95, 62, 0.1);
        color: #755f3e;
        padding: 0.08rem 0.35rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.67rem;
    }

    .sub-pic {
        color: #6b7280;
    }

    /* Development Column Subtle Highlight */
    .kanban-table-cell.col-dev-cell.has-project {
        background-color: rgba(254, 243, 199, 0.22);
    }

    .kanban-table-cell.col-dev-cell.has-project:hover {
        background-color: rgba(254, 243, 199, 0.45);
    }

    /* Modern Development Duration Badge */
    .dev-duration-badge-wrapper {
        margin: 0.35rem 0 0.15rem 0;
        display: flex;
        align-items: center;
    }

    .dev-duration-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.22rem 0.6rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: 0.2px;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: default;
        position: relative;
    }

    .dev-duration-badge:hover {
        transform: translateY(-1px) scale(1.02);
    }

    /* Normal Tier (<= 14 days) - Warm Amber Glow */
    .dev-duration-badge.is-normal {
        background: linear-gradient(135deg, rgba(254, 243, 199, 0.95), rgba(253, 230, 138, 0.9));
        border: 1px solid rgba(217, 119, 6, 0.4);
        color: #92400e;
        box-shadow: 0 2px 8px rgba(217, 119, 6, 0.16);
    }

    .dev-duration-badge.is-normal:hover {
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.28);
        border-color: #d97706;
    }

    /* Warning Tier (15 - 30 days) - Energetic Warm Orange */
    .dev-duration-badge.is-warning {
        background: linear-gradient(135deg, rgba(255, 237, 213, 0.95), rgba(254, 215, 170, 0.9));
        border: 1px solid rgba(234, 88, 12, 0.45);
        color: #9a3412;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.18);
    }

    .dev-duration-badge.is-warning:hover {
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.32);
        border-color: #ea580c;
    }

    /* Critical / Long Running Tier (> 30 days) - High Visibility Rose-Crimson */
    .dev-duration-badge.is-critical {
        background: linear-gradient(135deg, rgba(254, 226, 226, 0.95), rgba(254, 202, 202, 0.9));
        border: 1px solid rgba(220, 38, 38, 0.45);
        color: #991b1b;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.18);
    }

    .dev-duration-badge.is-critical:hover {
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.32);
        border-color: #dc2626;
    }

    /* Pulsing Active Status Dot */
    .dev-badge-pulse-dot {
        width: 6.5px;
        height: 6.5px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        position: relative;
    }

    .dev-duration-badge.is-normal .dev-badge-pulse-dot {
        background: #d97706;
        animation: devPulseAmber 2s infinite ease-in-out;
    }

    .dev-duration-badge.is-warning .dev-badge-pulse-dot {
        background: #ea580c;
        animation: devPulseOrange 2s infinite ease-in-out;
    }

    .dev-duration-badge.is-critical .dev-badge-pulse-dot {
        background: #dc2626;
        animation: devPulseRed 1.8s infinite ease-in-out;
    }

    @keyframes devPulseAmber {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
        70% { transform: scale(1.15); box-shadow: 0 0 0 5px rgba(217, 119, 6, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
    }

    @keyframes devPulseOrange {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.7); }
        70% { transform: scale(1.15); box-shadow: 0 0 0 5px rgba(234, 88, 12, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 88, 12, 0); }
    }

    @keyframes devPulseRed {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.75); }
        70% { transform: scale(1.2); box-shadow: 0 0 0 6px rgba(220, 38, 38, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }

    .dev-badge-icon {
        display: inline-flex;
        align-items: center;
        flex-shrink: 0;
        opacity: 0.9;
    }

    .dev-badge-label {
        font-size: 0.63rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        opacity: 0.85;
    }

    .dev-badge-sep {
        font-size: 0.55rem;
        opacity: 0.45;
        margin: 0 -0.1rem;
    }

    .dev-badge-count {
        font-weight: 800;
        font-size: 0.73rem;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(44, 39, 33, 0.55);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        padding: 1rem;
    }

    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-box {
        background: #ffffff;
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.75rem 2rem;
        width: 100%;
        max-width: 720px;
        max-height: 90vh;
        overflow-y: auto;
        overflow-x: hidden !important;
        transform: translateY(20px);
        transition: transform 0.3s ease;
        box-shadow: 0 25px 50px -12px rgba(117, 95, 62, 0.25);
    }

    .modal-overlay.active .modal-box {
        transform: translateY(0);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(117, 95, 62, 0.15);
    }

    .modal-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 1.5rem;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .modal-close:hover {
        color: #dc2626;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .info-item.full-width {
        grid-column: span 2;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 0.92rem;
        color: var(--text-main);
        font-weight: 500;
        background: rgba(244, 240, 234, 0.4);
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        border: 1px solid rgba(117, 95, 62, 0.1);
    }

    .step-transition-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(117, 95, 62, 0.08);
        border: 1px solid rgba(117, 95, 62, 0.2);
        padding: 0.6rem 1rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main);
        width: 100%;
    }

    .step-target-name {
        color: #16a34a;
        font-weight: 700;
    }

    .form-group-modal {
        margin-bottom: 1rem;
    }

    .form-group-modal label {
        display: block;
        margin-bottom: 0.4rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-group-modal textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* Modal Footer & Buttons Proportional Layout */
    .modal-footer-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: nowrap !important;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(117, 95, 62, 0.15);
        box-sizing: border-box;
        width: 100%;
    }

    .modal-footer-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: nowrap !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        width: auto !important;
        margin: 0 !important;
    }

    .modal-footer-box .btn {
        white-space: nowrap !important;
        height: 38px !important;
        min-height: 38px !important;
        max-height: 38px !important;
        padding: 0 0.85rem !important;
        font-size: 0.83rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box !important;
        line-height: 1 !important;
        border-radius: 8px !important;
        flex: 0 0 auto !important;
        width: auto !important;
        margin: 0 !important;
    }

    .btn-modal-history {
        flex: 0 0 auto !important;
        width: auto !important;
        margin: 0 !important;
    }

    .btn-status-change {
        background: linear-gradient(135deg, #d97706, #b45309) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25) !important;
        flex: 0 0 auto !important;
        width: auto !important;
        margin: 0 !important;
    }

    .btn-status-change:hover {
        background: linear-gradient(135deg, #b45309, #92400e) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35) !important;
        opacity: 0.95;
    }

    .btn-modal-close {
        min-width: 75px !important;
        width: auto !important;
        margin: 0 !important;
        flex: 0 0 auto !important;
        padding: 0 1.1rem !important;
    }

    @media (max-width: 520px) {
        .modal-footer-box {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
        }
        .modal-footer-right {
            flex-direction: column !important;
            width: 100% !important;
            gap: 0.5rem !important;
        }
        .modal-footer-box .btn {
            width: 100% !important;
        }
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.3);
        color: #15803d;
    }

    .empty-state {
        text-align: center;
        padding: 3.5rem 1.5rem;
        color: var(--text-muted);
    }

    .empty-state svg {
        margin-bottom: 1rem;
        color: var(--text-muted);
        opacity: 0.6;
    }
</style>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@php
    $buildFilterUrl = function($status) use ($selectedTahun, $selectedDirektorat, $selectedSort, $searchKeyword) {
        $params = [];
        if (!empty($status) && $status !== 'all') {
            $params['filter_status'] = $status;
        }
        if (!empty($selectedTahun) && $selectedTahun !== 'all') {
            $params['filter_tahun'] = $selectedTahun;
        }
        if (!empty($selectedDirektorat)) {
            $params['filter_direktorat'] = $selectedDirektorat;
        }
        if (!empty($selectedSort) && $selectedSort !== 'dev_first') {
            $params['sort_by'] = $selectedSort;
        }
        if (!empty($searchKeyword)) {
            $params['search'] = $searchKeyword;
        }
        return route('kanban.index', $params);
    };
@endphp

<!-- KPI Summary Cards -->
<div class="kpi-container">
    <!-- Card 1: Total Projects -->
    <a href="{{ $buildFilterUrl('all') }}" class="kpi-card" title="Klik untuk lihat semua project">
        <div class="kpi-icon primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
        </div>
        <div class="kpi-info">
            <span class="kpi-value">{{ $totalProjects }}</span>
            <span class="kpi-label">Total Projects</span>
        </div>
    </a>

    <!-- Card 2: Tahap Development (FOKUS UTAMA) -->
    <a href="{{ $buildFilterUrl('dev') }}" class="kpi-card dev-focus" title="Klik untuk memfilter khusus tahap Development">
        <div class="kpi-icon dev">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        </div>
        <div class="kpi-info">
            <span class="kpi-value" style="color: #b45309;">{{ $devProjectsCount }}</span>
            <span class="kpi-label" style="font-weight: 700; color: #92400e;">Tahap Development</span>
            <span class="kpi-badge-focus">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                Fokus Utama
            </span>
        </div>
    </a>

    <!-- Card 3: Tahap Lain On Progress -->
    <a href="{{ $buildFilterUrl('progress') }}" class="kpi-card" title="Klik untuk lihat semua project On-Progress">
        <div class="kpi-icon warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div class="kpi-info">
            <span class="kpi-value">{{ $inProgressProjects }}</span>
            <span class="kpi-label">Total On-Progress</span>
        </div>
    </a>

    <!-- Card 4: Live / Selesai -->
    <a href="{{ $buildFilterUrl('live') }}" class="kpi-card" title="Klik untuk lihat project Live / Selesai">
        <div class="kpi-icon success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        <div class="kpi-info">
            <span class="kpi-value">{{ $completedProjects }}</span>
            <span class="kpi-label">Live / Selesai</span>
        </div>
    </a>
</div>

<!-- Quick Status Filter Pills -->
<div class="quick-filter-bar">
    <div class="quick-filter-label">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        <span>Akses Cepat:</span>
    </div>
    
    <a href="{{ $buildFilterUrl('all') }}" class="filter-pill {{ $selectedStatus === 'all' ? 'active' : '' }}">
        <span>Semua Project</span>
        <span class="pill-count">{{ $totalProjects }}</span>
    </a>

    <a href="{{ $buildFilterUrl('dev') }}" class="filter-pill dev-pill {{ $selectedStatus === 'dev' ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        <span>Fokus: Development</span>
        <span class="pill-count">{{ $devProjectsCount }}</span>
    </a>

    <a href="{{ $buildFilterUrl('progress') }}" class="filter-pill {{ $selectedStatus === 'progress' ? 'active' : '' }}">
        <span>Semua On-Progress</span>
        <span class="pill-count">{{ $inProgressProjects }}</span>
    </a>

    <a href="{{ $buildFilterUrl('live') }}" class="filter-pill {{ $selectedStatus === 'live' ? 'active' : '' }}">
        <span>Live / Selesai</span>
        <span class="pill-count">{{ $completedProjects }}</span>
    </a>
</div>

<!-- Filters & Search Bar -->
<div class="filter-card">
    <form action="{{ route('kanban.index') }}" method="GET" class="filter-form">
        <div class="filter-group search-group">
            <label for="search">Cari Project / PIC / No Surat</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ $searchKeyword }}">
        </div>

        <div class="filter-group">
            <label for="filter_status">Status / Tahapan</label>
            <select name="filter_status" id="filter_status" class="form-control">
                <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="dev" {{ $selectedStatus === 'dev' ? 'selected' : '' }}>⚡ Tahap DEVELOPMENT (Fokus)</option>
                <option value="progress" {{ $selectedStatus === 'progress' ? 'selected' : '' }}>Semua On-Progress (Step 1-5)</option>
                <option value="live" {{ $selectedStatus === 'live' ? 'selected' : '' }}>Live / Selesai (Step 6)</option>
                <optgroup label="Pilih Step Spesifik:">
                    @foreach($rprojects as $rproj)
                        <option value="{{ $rproj->id }}" {{ $selectedStatus == (string)$rproj->id ? 'selected' : '' }}>
                            Step {{ $loop->iteration }}: {{ $rproj->deskripsi }}
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        <div class="filter-group">
            <label for="sort_by">Urutkan</label>
            <select name="sort_by" id="sort_by" class="form-control">
                <option value="dev_first" {{ $selectedSort === 'dev_first' ? 'selected' : '' }}>⚡ Tahap Development Teratas (Default)</option>
                <option value="progress_first" {{ $selectedSort === 'progress_first' ? 'selected' : '' }}>Semua On-Progress Teratas</option>
                <option value="latest" {{ $selectedSort === 'latest' ? 'selected' : '' }}>Project Terbaru (ID Terakhir)</option>
                <option value="oldest" {{ $selectedSort === 'oldest' ? 'selected' : '' }}>Project Terlama (ID Pertama)</option>
                <option value="name_asc" {{ $selectedSort === 'name_asc' ? 'selected' : '' }}>Nama Project (A - Z)</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="filter_direktorat">Direktorat</label>
            <select name="filter_direktorat" id="filter_direktorat" class="form-control">
                <option value="">Semua Direktorat</option>
                @foreach($direktorats as $dir)
                    <option value="{{ $dir->id }}" {{ $selectedDirektorat == $dir->id ? 'selected' : '' }}>
                        {{ $dir->deskripsi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label for="filter_tahun">Tahun (Tanggal Akhir)</label>
            <select name="filter_tahun" id="filter_tahun" class="form-control">
                <option value="all" {{ $selectedTahun === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($tahunList as $thn)
                    <option value="{{ $thn }}" {{ $selectedTahun == $thn ? 'selected' : '' }}>
                        {{ $thn }} {{ $thn == date('Y') ? '(Tahun Berjalan)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Filter
            </button>
            <a href="{{ route('kanban.index') }}" class="btn btn-secondary" title="Reset Filter">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Kanban Progress Matrix Board -->
<div class="kanban-board-card">
    <div class="kanban-board-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="kanban-board-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                Kanban Matrix View
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
                <span>Menampilkan <strong>{{ $projects->count() }}</strong> project</span>
                @if($selectedSort === 'dev_first')
                    <span style="background: rgba(217, 119, 6, 0.12); color: #b45309; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">
                        ⚡ Fokus: Dev Teratas
                    </span>
                @endif
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="saveKanbanToPDF()" style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Save to PDF
        </button>
    </div>

    <div class="kanban-table-wrapper">
        @php
            // Group projects by rproject ID
            $projectsByStep = [];
            foreach ($rprojects as $rp) {
                $projectsByStep[$rp->id] = [];
            }
            foreach ($projects as $proj) {
                if (isset($projectsByStep[$proj->rproject])) {
                    $projectsByStep[$proj->rproject][] = $proj;
                }
            }

            // Max projects count across all columns
            $maxProjectsInCol = 0;
            foreach ($rprojects as $rp) {
                $count = count($projectsByStep[$rp->id] ?? []);
                if ($count > $maxProjectsInCol) {
                    $maxProjectsInCol = $count;
                }
            }

            // At least 8 rows to match the clean grid aesthetic of the diagram
            $displayRows = max($maxProjectsInCol, 8);

            // Check if there are projects with unassigned status (e.g. rproject = 0)
            $unassignedProjects = $projects->filter(function($p) use ($rprojects) {
                return !in_array($p->rproject, $rprojects->pluck('id')->toArray());
            });
        @endphp

        <table class="kanban-board-table" id="kanbanBoardTable">
            <thead>
                <!-- Merged Header Row: Judul kolom dan Icon dijadikan 1 cell (merge) -->
                <tr class="header-row-merged">
                    @foreach($rprojects as $rproj)
                        @php
                            $isDevCol = ($rproj->id == 1 || strtoupper($rproj->deskripsi) === 'DEVELOPMENT');
                            $colCount = count($projectsByStep[$rproj->id] ?? []);
                        @endphp
                        <th class="kanban-col-header {{ $isDevCol ? 'col-dev-header' : '' }}" 
                            data-step-id="{{ $rproj->id }}" 
                            data-step-name="{{ $rproj->deskripsi }}"
                            title="Kolom {{ $rproj->deskripsi }} (Drop project ke sini untuk ubah status)">
                            <div class="header-cell-merged">
                                <div class="header-title-box">
                                    <span class="header-stage-name">{{ strtoupper($rproj->deskripsi) }}</span>
                                    <span class="header-stage-badge" title="{{ $colCount }} project">{{ $colCount }}</span>
                                </div>
                                <div class="header-icon-box">
                                    @if(!empty($rproj->icon))
                                        <img src="{{ asset('icons/' . $rproj->icon) }}" 
                                             alt="{{ $rproj->deskripsi }}" 
                                             class="header-step-icon" 
                                             title="{{ $rproj->deskripsi }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                        <span class="header-icon-fallback" style="display:none;">{{ substr($rproj->deskripsi, 0, 2) }}</span>
                                    @else
                                        <span class="header-icon-fallback">{{ substr($rproj->deskripsi, 0, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for($rowIndex = 0; $rowIndex < $displayRows; $rowIndex++)
                    <tr>
                        @foreach($rprojects as $rproj)
                            @php
                                $proj = $projectsByStep[$rproj->id][$rowIndex] ?? null;
                                $isDevCol = ($rproj->id == 1 || strtoupper($rproj->deskripsi) === 'DEVELOPMENT');
                            @endphp
                            <td class="kanban-table-cell {{ $proj ? 'has-project' : 'empty-cell' }} {{ $isDevCol ? 'col-dev-cell' : '' }}" 
                                data-step-id="{{ $rproj->id }}" 
                                data-step-name="{{ $rproj->deskripsi }}"
                                data-col-index="{{ $loop->index }}">
                                @if($proj)
                                    @php
                                        $durasiDevDays = null;
                                        $formattedDevDate = '-';
                                        if ($isDevCol) {
                                            $lastDevDate = $proj->last_dev_date 
                                                ?? $proj->tanggal_awal 
                                                ?? $proj->tanggal 
                                                ?? ($proj->tgl_update ? \Carbon\Carbon::parse($proj->tgl_update)->format('Y-m-d') : null);
                                            if ($lastDevDate) {
                                                try {
                                                    $startDate = \Carbon\Carbon::parse($lastDevDate)->startOfDay();
                                                    $today = \Carbon\Carbon::today();
                                                    $durasiDevDays = $startDate->isFuture() ? 0 : (int) $startDate->diffInDays($today);
                                                    $formattedDevDate = $startDate->format('d-m-Y');
                                                } catch (\Exception $e) {
                                                    $durasiDevDays = null;
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="project-card-box draggable-card {{ $isDevCol ? 'is-dev-card' : '' }}" 
                                        draggable="true"
                                        data-idproject="{{ $proj->id }}" 
                                        data-rproject="{{ $proj->rproject }}" 
                                        data-project-name="{{ $proj->project_name }}"
                                        data-leadby="{{ $proj->leadby }}"
                                        onclick="handleProjectCardClick(event, {{ json_encode($proj) }})"
                                        title="Tarik / drag untuk pindah status. Klik untuk detail. Klik kanan untuk Donut Menu.">
                                        <div class="project-name-wrap">
                                            <span class="project-drag-grip" title="Tarik untuk pindah status">
                                                <svg width="10" height="13" viewBox="0 0 10 16" fill="currentColor">
                                                    <circle cx="3" cy="3" r="1.5"></circle>
                                                    <circle cx="7" cy="3" r="1.5"></circle>
                                                    <circle cx="3" cy="8" r="1.5"></circle>
                                                    <circle cx="7" cy="8" r="1.5"></circle>
                                                    <circle cx="3" cy="13" r="1.5"></circle>
                                                    <circle cx="7" cy="13" r="1.5"></circle>
                                                </svg>
                                            </span>
                                            <span>{{ $proj->project_name }}</span>
                                        </div>

                                        @if($isDevCol && $durasiDevDays !== null)
                                            <div class="dev-duration-badge-wrapper">
                                                <span class="dev-duration-badge {{ $durasiDevDays > 30 ? 'is-critical' : ($durasiDevDays > 14 ? 'is-warning' : 'is-normal') }}" 
                                                      title="Durasi Development: {{ $durasiDevDays }} hari (Tanggal terakhir status Development: {{ $formattedDevDate }} &bull; Current date: {{ date('d-m-Y') }})">
                                                    <span class="dev-badge-pulse-dot"></span>
                                                    <svg class="dev-badge-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                    <span class="dev-badge-label">Dev</span>
                                                    <span class="dev-badge-sep">&bull;</span>
                                                    <span class="dev-badge-count">{{ $durasiDevDays }} hari</span>
                                                </span>
                                            </div>
                                        @endif

                                        @if($proj->direktorat_rel || $proj->pic_name)
                                            <div class="project-sub-meta">
                                                @if($proj->direktorat_rel)
                                                    <span class="sub-badge-dir">{{ $proj->direktorat_rel->direktorat }}</span>
                                                @endif
                                                @if($proj->pic_name)
                                                    <span class="sub-pic">PIC: {{ $proj->pic_name }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    &nbsp;
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>

        @if($unassignedProjects->isNotEmpty())
            <div style="padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.08); border-top: 1px solid rgba(245, 158, 11, 0.2); font-size: 0.8rem; color: #92400e; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                <span><strong>Catatan:</strong> Terdapat {{ $unassignedProjects->count() }} project berstatus belum ditentukan (rproject = 0).</span>
                <span style="font-size: 0.75rem; color: #b45309;">(Dapat diatur melalui halaman Manajemen Project)</span>
            </div>
        @endif
    </div>
</div>

<!-- Modal 1: Detail Informasi Project -->
<div class="modal-overlay" id="projectDetailModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Informasi Detail Project
            </h3>
            <button class="modal-close" onclick="closeProjectDetailModal()">&times;</button>
        </div>
        
        <div class="info-grid">
            <div class="info-item full-width">
                <span class="info-label">Nama Project</span>
                <span class="info-value" id="detail_project_name" style="font-weight: 700; color: var(--primary);">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Direktorat</span>
                <span class="info-value" id="detail_direktorat">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Bagian</span>
                <span class="info-value" id="detail_bagian">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Nomor Surat</span>
                <span class="info-value" id="detail_no_surat">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">PIC Name</span>
                <span class="info-value" id="detail_pic_name">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Lead By</span>
                <span class="info-value" id="detail_leadby">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Support By</span>
                <span class="info-value" id="detail_supportby">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Tanggal Awal</span>
                <span class="info-value" id="detail_tanggal_awal">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Tanggal Akhir</span>
                <span class="info-value" id="detail_tanggal_akhir">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Status Saat Ini</span>
                <span class="info-value" id="detail_status" style="font-weight: 700; color: #15803d;">-</span>
            </div>

            <div class="info-item" id="detail_dev_duration_wrap" style="display: none;">
                <span class="info-label">Durasi Development</span>
                <span class="info-value" id="detail_dev_duration" style="font-weight: 700; color: #d97706;">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">RKAP</span>
                <span class="info-value" id="detail_rkap">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">Katalog / Versi</span>
                <span class="info-value" id="detail_catalog">-</span>
            </div>

            <div class="info-item">
                <span class="info-label">By Vendor</span>
                <span class="info-value" id="detail_vendor">-</span>
            </div>

            <div class="info-item full-width">
                <span class="info-label">Catatan</span>
                <span class="info-value" id="detail_catatan" style="min-height: 50px;">-</span>
            </div>
        </div>

        <div class="modal-footer-box">
            <a href="#" id="detail_history_link" class="btn btn-secondary btn-modal-history">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>Lihat Riwayat & File Evidence</span>
            </a>
            <div class="modal-footer-right">
                <button type="button" class="btn btn-status-change" onclick="openStepChangeModalFromDetail()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    <span>Pindah Step / Status</span>
                </button>
                <button type="button" class="btn btn-primary btn-modal-close" onclick="closeProjectDetailModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Perpindahan Step Kanban -->
<div class="modal-overlay" id="stepChangeModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                Ubah Step Kanban
            </h3>
            <button class="modal-close" onclick="closeStepChangeModal()">&times;</button>
        </div>

        <form action="{{ route('kanban.update_step') }}" method="POST" enctype="multipart/form-data" id="stepChangeForm">
            @csrf
            <input type="hidden" name="idproject" id="step_idproject">

            <div class="step-transition-badge">
                <span>Perpindahan Step:</span>
                <span id="step_from_name" style="color: var(--text-muted); text-decoration: line-through;">-</span>
                <span>&rarr;</span>
                <span class="step-target-name" id="step_to_name">-</span>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.2rem;">Project</label>
                <div id="step_project_name" style="font-weight: 700; font-size: 1rem; color: var(--text-main);">-</div>
            </div>

            <div class="form-group-modal">
                <label for="step_target_rproject">Target Step Baru <span style="color: #dc2626;">*</span></label>
                <select name="rproject" id="step_target_rproject" class="form-control" required>
                    @foreach($rprojects as $rp)
                        <option value="{{ $rp->id }}">{{ $rp->deskripsi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group-modal">
                <label for="modal_tanggal">Tanggal Perubahan <span style="color: #dc2626;">*</span></label>
                <input type="text" name="tanggal" id="modal_tanggal" class="form-control" required placeholder="DD-MM-YYYY">
            </div>

            <div class="form-group-modal">
                <label for="modal_progress">Persentase Progress (%)</label>
                <input type="number" name="progress" id="modal_progress" class="form-control" min="0" max="100" placeholder="Contoh: 50">
            </div>

            <div class="form-group-modal">
                <label for="modal_catatan">Catatan / Keterangan <span style="color: #dc2626;">*</span></label>
                <textarea name="catatan" id="modal_catatan" class="form-control" rows="3" maxlength="500" required placeholder="Masukkan catatan atau keterangan perubahan status step..."></textarea>
            </div>

            <div class="form-group-modal" style="background: rgba(117, 95, 62, 0.05); padding: 1rem; border-radius: 12px; border: 1px dashed var(--glass-border);">
                <label for="modal_evidence" style="color: var(--primary); display: flex; align-items: center; gap: 0.35rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Upload File Evidence (Simpan ke MinIO)
                </label>
                <input type="file" name="evidence_file" id="modal_evidence" class="form-control" style="margin-top: 0.35rem; background: #ffffff;" accept=".pdf,.png,.jpg,.jpeg,.docx,.zip,.rar,.xlsx,.pptx">
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem;">
                    *File evidence otomatis diupload dan tersimpan di MinIO bucket.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeStepChangeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-save-step">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('custom-scripts')
<script>
    let currentDetailProject = null;
    let isDraggingProject = false;
    let dndToastTimeout = null;

    function saveKanbanToPDF() {
        const element = document.querySelector('.kanban-board-card');
        const opt = {
            margin:       0.2,
            filename:     'Kanban_Progress.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a3', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Init Drag and Drop
        initKanbanDragAndDrop();

        // Init Flatpickr for modal datepicker
        flatpickr("#modal_tanggal", {
            dateFormat: "d-m-Y",
            defaultDate: new Date(),
            allowInput: true
        });

        // Target step dropdown change handler in Step Modal
        const stepSelect = document.getElementById('step_target_rproject');
        if (stepSelect) {
            stepSelect.addEventListener('change', function() {
                const selectedText = this.options[this.selectedIndex] ? this.options[this.selectedIndex].text : '';
                const toNameEl = document.getElementById('step_to_name');
                if (toNameEl) toNameEl.innerText = selectedText;
                const noteEl = document.getElementById('modal_catatan');
                if (noteEl && (!noteEl.value || noteEl.value.startsWith('Perubahan status step'))) {
                    noteEl.value = 'Perubahan status step ke ' + selectedText;
                }
            });
        }

        // Auto reload after Donut Context Menu quick update
        window.addEventListener('project-status-updated', function(e) {
            setTimeout(function() {
                window.location.reload();
            }, 600);
        });
    });

    /* ========================================================
       DRAG AND DROP KANBAN LOGIC
       ======================================================== */
    function initKanbanDragAndDrop() {
        let draggedCard = null;
        let draggedProjectId = null;
        let draggedProjectName = '';
        let draggedSourceStepId = null;

        const cards = document.querySelectorAll('.draggable-card');
        cards.forEach(card => {
            card.addEventListener('dragstart', function(e) {
                isDraggingProject = true;
                draggedCard = this;
                draggedProjectId = this.getAttribute('data-idproject');
                draggedProjectName = this.getAttribute('data-project-name') || ('Project #' + draggedProjectId);
                draggedSourceStepId = this.getAttribute('data-rproject');

                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', JSON.stringify({
                    id: draggedProjectId,
                    name: draggedProjectName,
                    fromStep: draggedSourceStepId
                }));

                setTimeout(() => {
                    if (draggedCard) draggedCard.classList.add('is-dragging');
                }, 0);
            });

            card.addEventListener('dragend', function(e) {
                if (draggedCard) {
                    draggedCard.classList.remove('is-dragging');
                }
                clearDropHighlights();
                setTimeout(() => {
                    isDraggingProject = false;
                    draggedCard = null;
                    draggedProjectId = null;
                    draggedProjectName = '';
                    draggedSourceStepId = null;
                }, 180);
            });
        });

        // Target drop zones: any element with [data-step-id] (cells or headers)
        const dropTargets = document.querySelectorAll('.kanban-board-table [data-step-id]');
        dropTargets.forEach(target => {
            target.addEventListener('dragover', function(e) {
                if (!draggedProjectId) return;
                const targetStepId = this.getAttribute('data-step-id');
                // Only allow dropping into different columns
                if (targetStepId && String(targetStepId) !== String(draggedSourceStepId)) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                }
            });

            target.addEventListener('dragenter', function(e) {
                if (!draggedProjectId) return;
                const targetStepId = this.getAttribute('data-step-id');
                if (targetStepId && String(targetStepId) !== String(draggedSourceStepId)) {
                    clearDropHighlights();
                    highlightColumn(targetStepId);
                    this.classList.add('drop-target-hover');
                }
            });

            target.addEventListener('dragleave', function(e) {
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drop-target-hover');
                }
            });

            target.addEventListener('drop', async function(e) {
                e.preventDefault();
                clearDropHighlights();

                if (!draggedProjectId) return;

                const targetStepId = this.getAttribute('data-step-id');
                const targetStepName = this.getAttribute('data-step-name') || ('Step #' + targetStepId);

                if (!targetStepId || String(targetStepId) === String(draggedSourceStepId)) {
                    return;
                }

                await executeStatusUpdateViaDrop(draggedProjectId, draggedProjectName, targetStepId, targetStepName, draggedCard);
            });
        });

        function highlightColumn(stepId) {
            document.querySelectorAll(`.kanban-board-table [data-step-id="${stepId}"]`).forEach(el => {
                el.classList.add('drag-active-col');
            });
        }

        function clearDropHighlights() {
            document.querySelectorAll('.drag-active-col').forEach(el => el.classList.remove('drag-active-col'));
            document.querySelectorAll('.drop-target-hover').forEach(el => el.classList.remove('drop-target-hover'));
        }
    }

    async function executeStatusUpdateViaDrop(projectId, projectName, targetStepId, targetStepName, cardElement) {
        if (cardElement) {
            cardElement.classList.add('is-updating');
        }

        showDndToast(`Memindahkan "${projectName}" ke ${targetStepName}...`, 'info');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const response = await fetch("{{ route('project.quick_status_update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    idproject: projectId,
                    rproject: targetStepId
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showDndToast(`Status "${projectName}" berhasil diubah menjadi "${targetStepName}".`, 'success');

                // Trigger global update event
                window.dispatchEvent(new CustomEvent('project-status-updated', {
                    detail: result.data || { idproject: projectId, rproject: targetStepId, status_name: targetStepName }
                }));

                // Reload page to reflect column counts and positions
                setTimeout(() => {
                    window.location.reload();
                }, 450);
            } else {
                throw new Error(result.message || 'Gagal mengubah status project.');
            }
        } catch (err) {
            console.error('Drag-drop status update error:', err);
            showDndToast(err.message || 'Terjadi kesalahan saat memindahkan project.', 'error');
            if (cardElement) {
                cardElement.classList.remove('is-updating');
            }
        }
    }

    function showDndToast(message, type = 'info') {
        let toast = document.getElementById('kanban-dnd-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'kanban-dnd-toast';
            toast.className = 'kanban-dnd-toast';
            document.body.appendChild(toast);
        }

        toast.className = `kanban-dnd-toast toast-${type} active`;
        
        let iconSvg = '';
        if (type === 'success') {
            iconSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        } else if (type === 'error') {
            iconSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        } else {
            iconSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
        }

        toast.innerHTML = `${iconSvg}<span>${message}</span>`;

        if (dndToastTimeout) clearTimeout(dndToastTimeout);
        dndToastTimeout = setTimeout(() => {
            toast.classList.remove('active');
        }, 3500);
    }

    function handleProjectCardClick(event, project) {
        if (isDraggingProject) {
            return;
        }
        openProjectDetailModal(project);
    }

    // Modal 1: Project Details Logic
    function openProjectDetailModal(project) {
        currentDetailProject = project;
        document.getElementById('detail_project_name').innerText = project.project_name || '-';
        // Direktorat
        let dirText = '-';
        if (project.direktorat_rel) {
            dirText = project.direktorat_rel.deskripsi || project.direktorat_rel.direktorat || '-';
        } else if (project.direktorat) {
            dirText = project.direktorat;
        }
        document.getElementById('detail_direktorat').innerText = dirText;

        document.getElementById('detail_bagian').innerText = project.bagian || '-';
        document.getElementById('detail_no_surat').innerText = project.no_surat || '-';
        document.getElementById('detail_pic_name').innerText = project.pic_name || '-';
        document.getElementById('detail_leadby').innerText = project.leadby_rel ? project.leadby_rel.nama : (project.leadby || '-');
        document.getElementById('detail_supportby').innerText = project.supportby_rel ? project.supportby_rel.nama : (project.supportby || '-');
        document.getElementById('detail_tanggal_awal').innerText = project.tanggal_awal ? formatDate(project.tanggal_awal) : '-';
        document.getElementById('detail_tanggal_akhir').innerText = project.tanggal_akhir ? formatDate(project.tanggal_akhir) : '-';
        document.getElementById('detail_status').innerText = project.rproject_relation ? project.rproject_relation.deskripsi : ('Step #' + (project.rproject || '0'));

        // Durasi Development di Modal Detail
        const devDurationWrap = document.getElementById('detail_dev_duration_wrap');
        const devDateStr = project.last_dev_date || project.tanggal_awal || project.tanggal;
        if (project.rproject == 1 && devDateStr) {
            try {
                const devStart = new Date(devDateStr);
                const today = new Date();
                devStart.setHours(0, 0, 0, 0);
                today.setHours(0, 0, 0, 0);
                const diffTime = today - devStart;
                const diffDays = Math.max(0, Math.floor(diffTime / (1000 * 60 * 60 * 24)));
                
                const formattedDate = formatDate(devDateStr);
                const tierClass = diffDays > 30 ? 'is-critical' : (diffDays > 14 ? 'is-warning' : 'is-normal');
                
                document.getElementById('detail_dev_duration').innerHTML = `
                    <span class="dev-duration-badge ${tierClass}" style="box-shadow: none;">
                        <span class="dev-badge-pulse-dot"></span>
                        <svg class="dev-badge-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span class="dev-badge-count">${diffDays} hari</span>
                        <span style="font-size: 0.72rem; font-weight: 500; opacity: 0.85;">(terhitung sejak ${formattedDate})</span>
                    </span>
                `;
                if (devDurationWrap) devDurationWrap.style.display = 'flex';
            } catch (e) {
                if (devDurationWrap) devDurationWrap.style.display = 'none';
            }
        } else {
            if (devDurationWrap) devDurationWrap.style.display = 'none';
        }

        document.getElementById('detail_rkap').innerText = project.rkap || '-';

        // Catalog
        let catalogTitle = '';
        if (project.catalog_rel) {
            catalogTitle = project.catalog_rel.description || project.catalog_rel.nama_aplikasi || '';
        }
        let catalogVer = project.catalog_version ? ` (v${project.catalog_version})` : '';
        document.getElementById('detail_catalog').innerText = (catalogTitle || catalogVer) ? `${catalogTitle}${catalogVer}`.trim() : '-';

        document.getElementById('detail_vendor').innerText = (project.byvendor == 1 ? 'Ya' : 'Tidak') + (project.vendorname ? ' - ' + project.vendorname : '');
        document.getElementById('detail_catatan').innerText = project.catatan || '-';

        // Link to History
        document.getElementById('detail_history_link').href = '/hproject/' + project.id;

        document.getElementById('projectDetailModal').classList.add('active');
    }

    function closeProjectDetailModal() {
        document.getElementById('projectDetailModal').classList.remove('active');
    }

    // Modal 2: Step Change Logic
    function openStepChangeModalFromDetail() {
        if (!currentDetailProject) return;
        const proj = currentDetailProject;
        closeProjectDetailModal();
        const currentStepName = proj.rproject_relation ? proj.rproject_relation.deskripsi : ('Step #' + (proj.rproject || '0'));
        openStepChangeModal(proj.id, proj.project_name, proj.rproject, currentStepName);
    }

    function openStepChangeModal(projectId, projectName, currentStepId, currentStepName, targetStepId, targetStepName) {
        document.getElementById('step_idproject').value = projectId;
        document.getElementById('step_project_name').innerText = projectName;
        document.getElementById('step_from_name').innerText = currentStepName || 'Step Sebelumnya';
        
        const select = document.getElementById('step_target_rproject');
        if (select) {
            select.value = targetStepId || currentStepId || select.options[0].value;
            const toNameEl = document.getElementById('step_to_name');
            if (toNameEl) {
                toNameEl.innerText = targetStepName || (select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '-');
            }
        }
        
        document.getElementById('modal_catatan').value = 'Perubahan status step ke ' + (targetStepName || (select && select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : 'tahap baru'));
        document.getElementById('modal_progress').value = '';
        document.getElementById('modal_evidence').value = '';

        document.getElementById('stepChangeModal').classList.add('active');
    }

    function closeStepChangeModal() {
        document.getElementById('stepChangeModal').classList.remove('active');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return dateStr;
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProjectDetailModal();
            closeStepChangeModal();
        }
    });

    // Close modal when clicking on overlay background
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
</script>
@endsection
