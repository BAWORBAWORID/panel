@extends('layouts.admin')

@section('title')
    Admin Alerts
@endsection

@section('content-header')
    <h1>Admin Alerts<small>Broadcast a system-wide notice to all users.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Alerts</li>
    </ol>
@endsection

@section('scripts')
    @parent
    <style>
        .alert-page { max-width: 100%; }
        .content-wrapper, .content { overflow-x: hidden !important; }

        /* ── Layout ─────────────────────────────────────────────────── */
        .alert-layout {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .alert-form-col {
            flex: 1;
            min-width: 0;
        }
        .alert-preview-col {
            width: 320px;
            flex-shrink: 0;
            position: sticky;
            top: 20px;
        }
        @media (max-width: 991px) {
            .alert-layout { flex-direction: column; }
            .alert-preview-col { width: 100%; position: static; }
        }

        /* ── Section divider ─────────────────────────────────────────── */
        .section-sep {
            font-size: 11px;
            font-weight: 700;
            color: #aab2bd;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin: 20px 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-sep::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #2b3a4a;
        }

        /* ── Icon picker ─────────────────────────────────────────────── */
        .icon-picker {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .icon-pick-item input[type="radio"] { display: none; }
        .icon-pick-item label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 62px;
            height: 62px;
            border-radius: 5px;
            background: #1a2332;
            border: 2px solid #2b3a4a;
            cursor: pointer;
            transition: border-color .15s, background .15s, opacity .15s;
            opacity: 0.55;
            gap: 4px;
        }
        .icon-pick-item label img { width: 24px; height: 24px; object-fit: contain; }
        .icon-pick-item label span { font-size: 9px; color: #aab2bd; text-transform: capitalize; }
        .icon-pick-item input:checked + label {
            opacity: 1;
            background: #1e2d3d;
            border-color: #3498db;
        }
        .icon-pick-item label:hover { opacity: 0.85; background: #1e2d3d; }

        /* ── Position / dismiss pickers ─────────────────────────────── */
        .pos-picker, .dismiss-picker { display: flex; gap: 10px; flex-wrap: wrap; }
        .pos-item, .dismiss-item { position: relative; flex: 1; min-width: 120px; }
        .pos-item input[type="radio"], .dismiss-item input[type="radio"] { display: none; }
        .pos-item label, .dismiss-item label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            padding: 10px;
            border-radius: 5px;
            background: #1a2332;
            border: 2px solid #2b3a4a;
            cursor: pointer;
            transition: border-color .15s, background .15s, opacity .15s;
            opacity: 0.55;
        }
        .pos-item label img { width: 100%; border-radius: 3px; aspect-ratio: 16/9; object-fit: cover; }
        .pos-item label span, .dismiss-item label span { font-size: 12px; color: #aab2bd; font-weight: 500; }
        .dismiss-item label { flex-direction: row; gap: 10px; }
        .dismiss-item label i { font-size: 17px; color: #6b7a8d; }
        .dismiss-item label .d-text strong { display: block; font-size: 13px; color: #c8cfd8; }
        .dismiss-item label .d-text { font-size: 11px; color: #aab2bd; }
        .pos-item input:checked + label,
        .dismiss-item input:checked + label {
            opacity: 1;
            background: #1e2d3d;
            border-color: #3498db;
        }
        .pos-item label:hover, .dismiss-item label:hover { opacity: 0.85; background: #1e2d3d; }
        .dismiss-item input:checked + label i { color: #3498db; }

        /* ── Color Section ───────────────────────────────────────────── */
        .color-section { }

        /* Palette presets */
        .palette-label {
            font-size: 11px;
            color: #6b7a8d;
            margin: 0 0 8px;
        }
        .palette-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 16px;
        }
        .palette-swatch {
            width: 32px;
            height: 32px;
            border-radius: 5px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: transform .15s, border-color .15s, box-shadow .15s;
            position: relative;
            flex-shrink: 0;
        }
        .palette-swatch:hover {
            transform: scale(1.15);
            border-color: rgba(255,255,255,0.25);
        }
        .palette-swatch.active {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,.35);
            transform: scale(1.1);
        }

        /* Tooltip on hover */
        .palette-swatch::after {
            content: attr(data-label);
            position: absolute;
            bottom: calc(100% + 5px);
            left: 50%;
            transform: translateX(-50%);
            background: #0f1b27;
            color: #c8cfd8;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 3px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity .12s;
            border: 1px solid #2b3a4a;
        }
        .palette-swatch:hover::after { opacity: 1; }

        /* Color tabs */
        .color-tabs {
            display: flex;
            gap: 0;
            border-bottom: 1px solid #2b3a4a;
            margin-bottom: 14px;
        }
        .color-tab-btn {
            padding: 7px 14px;
            font-size: 11px;
            font-weight: 600;
            color: #6b7a8d;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: color .15s, border-color .15s;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: -1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .color-tab-btn .tab-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .color-tab-btn.active {
            color: #c8cfd8;
            border-bottom-color: #3498db;
        }
        .color-tab-btn:hover:not(.active) { color: #aab2bd; }

        /* HSL Sliders panel */
        .hsl-panel {
            background: #1a2332;
            border-radius: 6px;
            border: 1px solid #2b3a4a;
            padding: 14px;
        }

        /* Color preview bar at top of panel */
        .hsl-preview-bar {
            width: 100%;
            height: 36px;
            border-radius: 5px;
            margin-bottom: 14px;
            border: 1px solid #2b3a4a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            letter-spacing: 0.05em;
            transition: background .1s, color .1s;
            position: relative;
            cursor: pointer;
            user-select: none;
        }
        .hsl-preview-bar-copy {
            position: absolute;
            right: 8px;
            font-size: 9px;
            opacity: 0.5;
        }

        /* Slider rows */
        .hsl-row {
            margin-bottom: 12px;
        }
        .hsl-row:last-child { margin-bottom: 0; }
        .hsl-row-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .hsl-row-label {
            font-size: 10px;
            font-weight: 700;
            color: #6b7a8d;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .hsl-row-value {
            font-size: 11px;
            font-family: 'Courier New', monospace;
            color: #aab2bd;
            background: #0f1b27;
            padding: 1px 6px;
            border-radius: 3px;
            border: 1px solid #2b3a4a;
            min-width: 38px;
            text-align: center;
        }

        /* Custom slider track */
        .hsl-slider {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 10px;
            border-radius: 5px;
            outline: none;
            cursor: pointer;
            border: 1px solid #2b3a4a;
        }
        .hsl-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            cursor: pointer;
            border: 2px solid #0f1b27;
            box-shadow: 0 1px 4px rgba(0,0,0,.5);
            transition: transform .1s, box-shadow .1s;
        }
        .hsl-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
            box-shadow: 0 2px 8px rgba(0,0,0,.6);
        }
        .hsl-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            cursor: pointer;
            border: 2px solid #0f1b27;
            box-shadow: 0 1px 4px rgba(0,0,0,.5);
        }
        /* Hue slider */
        #slider-h {
            background: linear-gradient(to right,
                hsl(0,100%,50%), hsl(30,100%,50%), hsl(60,100%,50%),
                hsl(90,100%,50%), hsl(120,100%,50%), hsl(150,100%,50%),
                hsl(180,100%,50%), hsl(210,100%,50%), hsl(240,100%,50%),
                hsl(270,100%,50%), hsl(300,100%,50%), hsl(330,100%,50%),
                hsl(360,100%,50%));
        }

        /* Hex input under sliders */
        .hsl-hex-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #2b3a4a;
        }
        .hsl-hex-label {
            font-size: 10px;
            font-weight: 700;
            color: #6b7a8d;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
        }
        .hsl-hex-input {
            flex: 1;
            background: #0f1b27;
            color: #c8cfd8;
            border: 1px solid #2b3a4a;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: border-color .15s;
        }
        .hsl-hex-input:focus { outline: none; border-color: #3498db; }
        .hsl-hex-input.invalid { border-color: #e74c3c; }
        /* Hidden native color input (for clipboard/fallback) */
        .color-native-hidden {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        /* ── Publish button ──────────────────────────────────────────── */
        .publish-btn {
            width: 100%;
            padding: 10px;
            margin-top: 18px;
            background: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .publish-btn:hover { background: #2980b9; }

        /* ── Preview card ────────────────────────────────────────────── */
        .preview-card {
            background: #1e2d3d;
            border-radius: 6px;
            border: 1px solid #2b3a4a;
            padding: 14px;
        }
        .preview-card-label {
            font-size: 10px;
            font-weight: 700;
            color: #6b7a8d;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .preview-shell {
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #2b3a4a;
            background: #0f1b27;
        }
        .preview-navbar {
            background: #1a2332;
            height: 30px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            gap: 5px;
            border-bottom: 1px solid #2b3a4a;
        }
        .preview-navbar-dot { width: 7px; height: 7px; border-radius: 50%; background: #2b3a4a; }
        .preview-navbar-bar { flex: 1; height: 5px; border-radius: 3px; background: #2b3a4a; }
        #live-preview-banner {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 9px 11px;
            border-bottom: 2px solid;
            transition: background .2s, border-color .2s, color .2s;
        }
        #live-preview-banner img { width: 16px; height: 16px; object-fit: contain; flex-shrink: 0; margin-top: 1px; }
        .pv-content { flex: 1; min-width: 0; }
        .pv-title { font-size: 10px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
        .pv-msg { font-size: 10px; opacity: 0.75; margin-top: 2px; word-break: break-word; line-height: 1.4; }
        .pv-by { font-size: 9px; opacity: 0.4; margin-top: 3px; font-style: italic; }
        .pv-dismiss {
            font-size: 9px;
            padding: 2px 7px;
            border-radius: 3px;
            border: 1px solid currentColor;
            opacity: 0.6;
            flex-shrink: 0;
            margin-top: 1px;
            white-space: nowrap;
            cursor: pointer;
        }
        .preview-mock { padding: 10px 10px; }
        .preview-mock-bar { height: 6px; border-radius: 3px; background: #1a2332; margin-bottom: 6px; }
        .preview-mock-bar.med { width: 80%; }
        .preview-mock-bar.short { width: 55%; }
        .pv-pos-label {
            text-align: center;
            font-size: 10px;
            color: #6b7a8d;
            padding: 8px 0 2px;
        }

        /* Color chips row */
        .pv-chips {
            display: flex;
            gap: 5px;
            margin-top: 10px;
            align-items: center;
        }
        .pv-chip {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 1px solid #2b3a4a;
            flex-shrink: 0;
            transition: background .15s;
        }
        .pv-chip-label {
            font-size: 9px;
            color: #6b7a8d;
            font-family: monospace;
            flex: 1;
        }

        /* ── Table ───────────────────────────────────────────────────── */
        .alerts-table-wrap {
            background: #1e2d3d;
            border-radius: 6px;
            border: 1px solid #2b3a4a;
            margin-top: 22px;
            width: 100%;
        }
        .alerts-table-head {
            padding: 13px 16px;
            border-bottom: 1px solid #2b3a4a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .alerts-table-head h3 { font-size: 14px; font-weight: 600; color: #c8cfd8; margin: 0; }
        .alert-count-badge {
            font-size: 11px;
            background: #0f1b27;
            color: #6b7a8d;
            padding: 2px 8px;
            border-radius: 20px;
            border: 1px solid #2b3a4a;
        }
        .table-scroll-wrap { overflow-x: auto; }
        table.alerts-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 780px;
        }
        table.alerts-table thead th {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7a8d;
            padding: 8px 13px;
            border-bottom: 1px solid #2b3a4a;
            text-align: left;
            background: #0f1b27;
            white-space: nowrap;
        }
        table.alerts-table tbody tr {
            border-bottom: 1px solid #1a2332;
            transition: background .1s;
        }
        table.alerts-table tbody tr:last-child { border-bottom: none; }
        table.alerts-table tbody tr:hover { background: #1a2332; }
        table.alerts-table tbody tr.is-active { background: rgba(46,204,113,.06); }
        table.alerts-table tbody tr.is-active:hover { background: rgba(46,204,113,.09); }
        table.alerts-table td {
            padding: 9px 13px;
            font-size: 12px;
            color: #aab2bd;
            vertical-align: middle;
        }
        .tbl-icon-wrap {
            width: 28px; height: 28px;
            border-radius: 5px;
            background: #0f1b27;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #2b3a4a;
        }
        .tbl-icon-wrap img { width: 15px; height: 15px; object-fit: contain; }
        .tbl-title { font-size: 13px; font-weight: 600; color: #c8cfd8; }
        .tbl-msg { font-size: 11px; color: #6b7a8d; margin-top: 2px; }
        .tbl-color-bar { display: flex; gap: 4px; align-items: center; }
        .tbl-color-dot {
            width: 13px; height: 13px;
            border-radius: 3px;
            border: 1px solid rgba(255,255,255,.1);
        }
        .status-dot { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; }
        .status-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .status-dot.active { color: #2ecc71; }
        .status-dot.active::before { background: #2ecc71; box-shadow: 0 0 5px rgba(46,204,113,.5); }
        .status-dot.inactive { color: #4a5568; }
        .status-dot.inactive::before { background: #4a5568; }
        .dismiss-pill {
            font-size: 10px; padding: 2px 8px;
            border-radius: 20px; font-weight: 600;
            display: inline-block; white-space: nowrap;
        }
        .dismiss-pill.yes { background: rgba(52,152,219,.15); color: #3498db; }
        .dismiss-pill.no  { background: rgba(231,76,60,.15);  color: #e74c3c; }
        .tbl-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px; height: 28px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            transition: filter .15s, transform .15s;
        }
        .tbl-action-btn.toggle-on  { background: rgba(46,204,113,.15); color: #2ecc71; }
        .tbl-action-btn.toggle-off { background: rgba(243,156,18,.15);  color: #f39c12; }
        .tbl-action-btn.del        { background: rgba(231,76,60,.15);   color: #e74c3c; }
        .tbl-action-btn:hover { filter: brightness(1.3); transform: scale(1.06); }
        .tbl-actions { display: flex; gap: 5px; justify-content: flex-end; }
        .empty-state { padding: 36px 20px; text-align: center; }
        .empty-state img { width: 36px; opacity: 0.12; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto; }
        .empty-state p { color: #4a5568; font-size: 13px; margin: 0; }
    </style>
@endsection

@section('content')
<div class="alert-page">
<div class="alert-layout">

    {{-- ── FORM COL ─────────────────────────────────────────────────── --}}
    <div class="alert-form-col">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bullhorn" style="margin-right:6px;"></i>Create Alert</h3>
                <p style="margin:4px 0 0;font-size:12px;color:#aab2bd;">Only one alert is active at a time. Publishing deactivates the current one.</p>
            </div>
            <div class="box-body">
            <form method="POST" action="{{ route('admin.alerts.store') }}" id="alert-create-form">
                @csrf

                {{-- Title --}}
                <div class="form-group">
                    <label for="f-title">Title <span style="color:#e74c3c;">*</span></label>
                    <input type="text" id="f-title" name="title" class="form-control" placeholder="e.g. Scheduled Maintenance" maxlength="120" required value="{{ old('title') }}">
                </div>

                {{-- Message --}}
                <div class="form-group">
                    <label for="f-message">Message <span style="color:#e74c3c;">*</span></label>
                    <textarea id="f-message" name="message" class="form-control" placeholder="Write your announcement..." maxlength="1000" required rows="3">{{ old('message') }}</textarea>
                    <p class="help-block" style="margin-bottom:0;">Max 1000 characters. Footer shows "By: {{ Auth::user()->name_first }} {{ Auth::user()->name_last }}" automatically.</p>
                </div>

                {{-- Type --}}
                <div class="form-group">
                    <label for="f-type">Alert Type <span style="color:#e74c3c;">*</span></label>
                    <select id="f-type" name="type" class="form-control">
                        <option value="info"        {{ old('type','info') === 'info'        ? 'selected' : '' }}>Info</option>
                        <option value="warning"     {{ old('type') === 'warning'     ? 'selected' : '' }}>Warning</option>
                        <option value="danger"      {{ old('type') === 'danger'      ? 'selected' : '' }}>Danger</option>
                        <option value="success"     {{ old('type') === 'success'     ? 'selected' : '' }}>Success</option>
                        <option value="maintenance" {{ old('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>

                {{-- Icon --}}
                <div class="section-sep">Icon</div>
                <div class="icon-picker">
                    @foreach (['megaphone','warning','success','database','message','gear','rocket','reception'] as $ic)
                        <div class="icon-pick-item">
                            <input type="radio" name="icon" id="icon-{{ $ic }}" value="{{ $ic }}" {{ old('icon','megaphone') === $ic ? 'checked' : '' }}>
                            <label for="icon-{{ $ic }}">
                                <img src="/assets/alert-icons/{{ $ic }}.png" alt="{{ $ic }}">
                                <span>{{ $ic }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                {{-- Position --}}
                <div class="section-sep">Position</div>
                <div class="pos-picker">
                    <div class="pos-item">
                        <input type="radio" name="position" id="pos-sticky" value="sticky" {{ old('position','sticky') === 'sticky' ? 'checked' : '' }}>
                        <label for="pos-sticky">
                            <img src="/assets/alertposition/sticky.png" alt="Sticky" onerror="this.style.display='none'">
                            <span>Sticky</span>
                        </label>
                    </div>
                    <div class="pos-item">
                        <input type="radio" name="position" id="pos-static" value="static" {{ old('position') === 'static' ? 'checked' : '' }}>
                        <label for="pos-static">
                            <img src="/assets/alertposition/static.png" alt="Static" onerror="this.style.display='none'">
                            <span>Static</span>
                        </label>
                    </div>
                </div>

                {{-- Dismissable --}}
                <div class="section-sep">Dismissable</div>
                <div class="dismiss-picker">
                    <div class="dismiss-item">
                        <input type="radio" name="dismissable" id="dismiss-yes" value="1" {{ old('dismissable','1') === '1' ? 'checked' : '' }}>
                        <label for="dismiss-yes">
                            <i class="fa fa-times-circle-o"></i>
                            <span class="d-text"><strong>Dismissable</strong>Users can close it</span>
                        </label>
                    </div>
                    <div class="dismiss-item">
                        <input type="radio" name="dismissable" id="dismiss-no" value="0" {{ old('dismissable') === '0' ? 'checked' : '' }}>
                        <label for="dismiss-no">
                            <i class="fa fa-ban"></i>
                            <span class="d-text"><strong>Persistent</strong>Always visible</span>
                        </label>
                    </div>
                </div>

                {{-- Colors --}}
                <div class="section-sep">Colors</div>
                <div class="color-section">

                    {{-- Palette presets --}}
                    <p class="palette-label">Quick presets:</p>
                    <div class="palette-grid" id="palette-grid"></div>

                    {{-- Color tabs --}}
                    <div class="color-tabs">
                        <button type="button" class="color-tab-btn active" data-tab="bg">
                            <span class="tab-dot" id="tab-dot-bg"></span> Background
                        </button>
                        <button type="button" class="color-tab-btn" data-tab="border">
                            <span class="tab-dot" id="tab-dot-border"></span> Border
                        </button>
                        <button type="button" class="color-tab-btn" data-tab="text">
                            <span class="tab-dot" id="tab-dot-text"></span> Text
                        </button>
                    </div>

                    {{-- HSL Sliders panel --}}
                    <div class="hsl-panel">
                        {{-- Preview bar --}}
                        <div class="hsl-preview-bar" id="hsl-preview-bar" title="Click to copy hex">
                            <span id="hsl-preview-hex">#1A2332</span>
                            <span class="hsl-preview-bar-copy"><i class="fa fa-clipboard"></i></span>
                        </div>

                        {{-- Hue --}}
                        <div class="hsl-row">
                            <div class="hsl-row-head">
                                <span class="hsl-row-label">Hue</span>
                                <span class="hsl-row-value" id="val-h">210°</span>
                            </div>
                            <input type="range" class="hsl-slider" id="slider-h" min="0" max="360" value="210">
                        </div>

                        {{-- Saturation --}}
                        <div class="hsl-row">
                            <div class="hsl-row-head">
                                <span class="hsl-row-label">Saturation</span>
                                <span class="hsl-row-value" id="val-s">33%</span>
                            </div>
                            <input type="range" class="hsl-slider" id="slider-s" min="0" max="100" value="33">
                        </div>

                        {{-- Lightness --}}
                        <div class="hsl-row">
                            <div class="hsl-row-head">
                                <span class="hsl-row-label">Lightness</span>
                                <span class="hsl-row-value" id="val-l">16%</span>
                            </div>
                            <input type="range" class="hsl-slider" id="slider-l" min="0" max="100" value="16">
                        </div>

                        {{-- Hex input --}}
                        <div class="hsl-hex-row">
                            <span class="hsl-hex-label">Hex</span>
                            <input type="text" class="hsl-hex-input" id="hsl-hex-input" maxlength="7" placeholder="#1A2332" value="#1A2332">
                        </div>
                    </div>

                    {{-- Hidden actual form inputs --}}
                    <input type="hidden" name="bg_color"     id="val-bg"     value="{{ old('bg_color','#1a2332') }}">
                    <input type="hidden" name="border_color" id="val-border" value="{{ old('border_color','#3498db') }}">
                    <input type="hidden" name="text_color"   id="val-text"   value="{{ old('text_color','#c8cfd8') }}">
                </div>

                <button type="submit" class="publish-btn">
                    <i class="fa fa-bullhorn"></i> Publish Alert
                </button>
            </form>
            </div>
        </div>
    </div>

    {{-- ── PREVIEW COL ──────────────────────────────────────────────── --}}
    <div class="alert-preview-col">
        <div class="preview-card">
            <p class="preview-card-label"><i class="fa fa-eye" style="margin-right:4px;"></i>Live Preview</p>
            <div class="preview-shell">
                <div class="preview-navbar">
                    <div class="preview-navbar-dot"></div>
                    <div class="preview-navbar-dot"></div>
                    <div class="preview-navbar-bar"></div>
                </div>
                <div id="live-preview-banner">
                    <img id="pv-icon" src="/assets/alert-icons/megaphone.png" alt="icon">
                    <div class="pv-content">
                        <div class="pv-title" id="pv-title">Alert Title</div>
                        <div class="pv-msg" id="pv-msg">Your message here...</div>
                        <div class="pv-by">By {{ Auth::user()->name_first }} {{ Auth::user()->name_last }}</div>
                    </div>
                    <span class="pv-dismiss" id="pv-dismiss">✕ Dismiss</span>
                </div>
                <div class="preview-mock">
                    <div class="preview-mock-bar med"></div>
                    <div class="preview-mock-bar short"></div>
                    <div class="preview-mock-bar med"></div>
                </div>
            </div>
            <div class="pv-pos-label" id="pv-position-label">
                <i class="fa fa-thumb-tack"></i> Sticky
            </div>
            <div class="pv-chips">
                <div class="pv-chip" id="chip-bg"     title="Background"></div>
                <div class="pv-chip" id="chip-border" title="Border"></div>
                <div class="pv-chip" id="chip-text"   title="Text"></div>
                <span class="pv-chip-label" id="chip-labels"></span>
            </div>
        </div>
    </div>

</div>

{{-- ── TABLE ────────────────────────────────────────────────────────── --}}
<div class="alerts-table-wrap">
    <div class="alerts-table-head">
        <h3><i class="fa fa-list" style="margin-right:6px;"></i>All Alerts</h3>
        <span class="alert-count-badge">{{ $alerts->total() }} total</span>
    </div>
    <div class="table-scroll-wrap">
        <table class="alerts-table">
            <thead>
                <tr>
                    <th style="width:36px;"></th>
                    <th>Alert</th>
                    <th style="width:80px;">Colors</th>
                    <th style="width:80px;">Position</th>
                    <th style="width:75px;">Dismiss</th>
                    <th style="width:75px;">Status</th>
                    <th style="width:110px;">By</th>
                    <th style="width:90px;">Date</th>
                    <th style="width:72px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($alerts as $row)
                    <tr class="{{ $row->active ? 'is-active' : '' }}">
                        <td>
                            <div class="tbl-icon-wrap">
                                <img src="/assets/alert-icons/{{ $row->icon ?? 'megaphone' }}.png" alt="{{ $row->icon }}">
                            </div>
                        </td>
                        <td>
                            <div class="tbl-title">{{ $row->title }}</div>
                            <div class="tbl-msg">{{ Str::limit($row->message, 65) }}</div>
                        </td>
                        <td>
                            <div class="tbl-color-bar">
                                <div class="tbl-color-dot" style="background:{{ $row->bg_color ?? '#1a2332' }};" title="BG: {{ $row->bg_color ?? '#1a2332' }}"></div>
                                <div class="tbl-color-dot" style="background:{{ $row->border_color ?? '#3498db' }};" title="Border: {{ $row->border_color ?? '#3498db' }}"></div>
                                <div class="tbl-color-dot" style="background:{{ $row->text_color ?? '#c8cfd8' }};" title="Text: {{ $row->text_color ?? '#c8cfd8' }}"></div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:11px;color:#aab2bd;">
                                <i class="fa fa-{{ ($row->position ?? 'sticky') === 'sticky' ? 'thumb-tack' : 'minus' }}" style="margin-right:3px;"></i>
                                {{ ucfirst($row->position ?? 'sticky') }}
                            </span>
                        </td>
                        <td>
                            <span class="dismiss-pill {{ $row->dismissable ? 'yes' : 'no' }}">
                                {{ $row->dismissable ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="status-dot {{ $row->active ? 'active' : 'inactive' }}">
                                {{ $row->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="font-size:11px;">{{ $row->creator ? $row->creator->name_first . ' ' . $row->creator->name_last : '—' }}</td>
                        <td style="font-size:11px;color:#aab2bd;">
                            {{ $row->created_at->format('d M Y') }}<br>
                            <span style="color:#4a5568;">{{ $row->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            <div class="tbl-actions">
                                <form method="POST" action="{{ route('admin.alerts.toggle', $row->id) }}" style="display:contents;">
                                    @csrf
                                    <button type="submit" class="tbl-action-btn {{ $row->active ? 'toggle-off' : 'toggle-on' }}" title="{{ $row->active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fa fa-{{ $row->active ? 'toggle-off' : 'toggle-on' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.alerts.destroy', $row->id) }}" style="display:contents;" class="delete-alert-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tbl-action-btn del" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <img src="/assets/alert-icons/megaphone.png" alt="">
                                <p>No alerts yet. Create one above.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($alerts->hasPages())
        <div style="padding:11px 16px;border-top:1px solid #2b3a4a;">{{ $alerts->links() }}</div>
    @endif
</div>
</div>
@endsection

@section('footer-scripts')
@parent
<script>
(function () {

    // ── Palette presets ──────────────────────────────────────────────────
    var PALETTES = [
        { label: 'Midnight',  bg: '#0f1e35', border: '#3b82f6', text: '#bfdbfe' },
        { label: 'Amber',     bg: '#1f1200', border: '#f59e0b', text: '#fde68a' },
        { label: 'Crimson',   bg: '#1a0000', border: '#ef4444', text: '#fecaca' },
        { label: 'Emerald',   bg: '#021a0c', border: '#22c55e', text: '#bbf7d0' },
        { label: 'Violet',    bg: '#0d0b1e', border: '#a855f7', text: '#e9d5ff' },
        { label: 'Slate',     bg: '#1a1a2e', border: '#4a5568', text: '#e2e8f0' },
        { label: 'Rose',      bg: '#1a0010', border: '#fb7185', text: '#fce7f3' },
        { label: 'Cyan',      bg: '#001a1f', border: '#22d3ee', text: '#cffafe' },
        { label: 'Orange',    bg: '#1a0e00', border: '#f97316', text: '#ffedd5' },
        { label: 'Indigo',    bg: '#0a0f2a', border: '#6366f1', text: '#e0e7ff' },
        { label: 'Teal',      bg: '#001a18', border: '#2dd4bf', text: '#ccfbf1' },
        { label: 'Gold',      bg: '#1a1200', border: '#eab308', text: '#fef08a' },
        { label: 'Ocean',     bg: '#001530', border: '#0ea5e9', text: '#e0f2fe' },
        { label: 'Dusk',      bg: '#1a0f24', border: '#c084fc', text: '#f3e8ff' },
        { label: 'Charcoal',  bg: '#111111', border: '#666666', text: '#eeeeee' },
        { label: 'Forest',    bg: '#001a08', border: '#4ade80', text: '#dcfce7' },
        { label: 'Blood',     bg: '#220000', border: '#dc2626', text: '#fee2e2' },
        { label: 'Sky',       bg: '#001224', border: '#38bdf8', text: '#e0f2fe' },
        { label: 'Sand',      bg: '#1a1500', border: '#d97706', text: '#fef3c7' },
        { label: 'Pink',      bg: '#1a0018', border: '#ec4899', text: '#fce7f3' },
    ];

    // Render palette
    var grid = document.getElementById('palette-grid');
    if (grid) {
        PALETTES.forEach(function (p, idx) {
            var btn = document.createElement('div');
            btn.className = 'palette-swatch';
            btn.setAttribute('data-label', p.label);
            btn.style.background = 'linear-gradient(135deg, ' + p.bg + ' 0% 40%, ' + p.border + ' 40% 70%, ' + p.text + ' 70% 100%)';
            btn.title = p.label;
            btn.addEventListener('click', function () {
                applyPalette(p);
                document.querySelectorAll('.palette-swatch').forEach(function (s) { s.classList.remove('active'); });
                btn.classList.add('active');
            });
            grid.appendChild(btn);
        });
    }

    function applyPalette(p) {
        colors.bg     = p.bg;
        colors.border = p.border;
        colors.text   = p.text;
        // update form values
        document.getElementById('val-bg').value     = p.bg;
        document.getElementById('val-border').value = p.border;
        document.getElementById('val-text').value   = p.text;
        // update tab dots
        updateTabDots();
        // update sliders for active tab
        loadSliderFromColor(activeTab);
        updatePreview();
    }

    // ── State ────────────────────────────────────────────────────────────
    var activeTab = 'bg';
    var colors = {
        bg:     document.getElementById('val-bg').value     || '#1a2332',
        border: document.getElementById('val-border').value || '#3498db',
        text:   document.getElementById('val-text').value   || '#c8cfd8',
    };

    // ── HSL ↔ Hex converters ─────────────────────────────────────────────
    function hexToRgb(hex) {
        var r = parseInt(hex.slice(1,3),16);
        var g = parseInt(hex.slice(3,5),16);
        var b = parseInt(hex.slice(5,7),16);
        return [r, g, b];
    }
    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var max = Math.max(r,g,b), min = Math.min(r,g,b);
        var h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0;
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch(max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                case b: h = ((r - g) / d + 4) / 6; break;
            }
        }
        return [Math.round(h * 360), Math.round(s * 100), Math.round(l * 100)];
    }
    function hslToHex(h, s, l) {
        s /= 100; l /= 100;
        var a = s * Math.min(l, 1 - l);
        var f = function(n) {
            var k = (n + h / 30) % 12;
            var color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
            return Math.round(255 * color).toString(16).padStart(2, '0');
        };
        return '#' + f(0) + f(8) + f(4);
    }
    function isValidHex(h) { return /^#[0-9a-fA-F]{6}$/.test(h); }

    // ── Slider setup ─────────────────────────────────────────────────────
    var sliderH = document.getElementById('slider-h');
    var sliderS = document.getElementById('slider-s');
    var sliderL = document.getElementById('slider-l');
    var valH    = document.getElementById('val-h');
    var valS    = document.getElementById('val-s');
    var valL    = document.getElementById('val-l');
    var hexInp  = document.getElementById('hsl-hex-input');
    var previewBar = document.getElementById('hsl-preview-bar');
    var previewHex = document.getElementById('hsl-preview-hex');

    function getSliderHSL() {
        return [parseInt(sliderH.value), parseInt(sliderS.value), parseInt(sliderL.value)];
    }

    function updateSliderTracks() {
        var h = parseInt(sliderH.value);
        var s = parseInt(sliderS.value);
        var l = parseInt(sliderL.value);
        // Saturation track: grey → full color at current H & L
        sliderS.style.background = 'linear-gradient(to right, hsl(' + h + ',0%,' + l + '%), hsl(' + h + ',100%,' + l + '%))';
        // Lightness track: black → color → white
        sliderL.style.background = 'linear-gradient(to right, #000, hsl(' + h + ',' + s + '%,50%), #fff)';
    }

    function updateSliderUI() {
        var hsl = getSliderHSL();
        var hex = hslToHex(hsl[0], hsl[1], hsl[2]).toUpperCase();

        valH.textContent = hsl[0] + '°';
        valS.textContent = hsl[1] + '%';
        valL.textContent = hsl[2] + '%';

        hexInp.value = hex;
        hexInp.classList.remove('invalid');

        // Preview bar colors
        var textColor = hsl[2] > 50 ? '#0f1b27' : '#ffffff';
        previewBar.style.background = hex;
        previewBar.style.color      = textColor;
        previewHex.textContent      = hex;

        updateSliderTracks();

        // Save to state
        colors[activeTab] = hex;
        document.getElementById('val-' + activeTab).value = hex;
        updateTabDots();
        updatePreview();
    }

    function loadSliderFromColor(key) {
        var hex = colors[key] || '#1a2332';
        if (!isValidHex(hex)) return;
        var rgb = hexToRgb(hex);
        var hsl = rgbToHsl(rgb[0], rgb[1], rgb[2]);
        sliderH.value = hsl[0];
        sliderS.value = hsl[1];
        sliderL.value = hsl[2];
        updateSliderUI();
    }

    [sliderH, sliderS, sliderL].forEach(function(sl) {
        sl.addEventListener('input', updateSliderUI);
    });

    // Hex input → sliders
    hexInp.addEventListener('input', function() {
        var val = hexInp.value;
        if (!val.startsWith('#')) val = '#' + val;
        if (isValidHex(val)) {
            hexInp.classList.remove('invalid');
            var rgb = hexToRgb(val);
            var hsl = rgbToHsl(rgb[0], rgb[1], rgb[2]);
            sliderH.value = hsl[0];
            sliderS.value = hsl[1];
            sliderL.value = hsl[2];
            colors[activeTab] = val.toUpperCase();
            document.getElementById('val-' + activeTab).value = val.toUpperCase();
            updateSliderUI();
        } else {
            hexInp.classList.add('invalid');
        }
    });

    // Copy hex on click
    previewBar.addEventListener('click', function() {
        var hex = previewHex.textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(hex).then(function() {
                previewHex.textContent = 'Copied!';
                setTimeout(function() { previewHex.textContent = hex; }, 1200);
            });
        }
    });

    // ── Tab switching ─────────────────────────────────────────────────────
    function updateTabDots() {
        document.getElementById('tab-dot-bg').style.background     = colors.bg;
        document.getElementById('tab-dot-border').style.background = colors.border;
        document.getElementById('tab-dot-text').style.background   = colors.text;
    }

    document.querySelectorAll('.color-tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            activeTab = btn.getAttribute('data-tab');
            document.querySelectorAll('.color-tab-btn').forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            loadSliderFromColor(activeTab);
        });
    });

    // ── Live preview ──────────────────────────────────────────────────────
    function updatePreview() {
        var title     = (document.getElementById('f-title')?.value)   || 'Alert Title';
        var msg       = (document.getElementById('f-message')?.value) || 'Your message here...';
        var iconEl    = document.querySelector('input[name="icon"]:checked');
        var posEl     = document.querySelector('input[name="position"]:checked');
        var dismissEl = document.querySelector('input[name="dismissable"]:checked');
        var icon    = iconEl    ? iconEl.value    : 'megaphone';
        var pos     = posEl     ? posEl.value     : 'sticky';
        var dismiss = dismissEl ? dismissEl.value : '1';

        var bg     = colors.bg;
        var border = colors.border;
        var text   = colors.text;

        var banner = document.getElementById('live-preview-banner');
        if (banner) {
            banner.style.background        = bg;
            banner.style.borderBottomColor = border;
            banner.style.color             = text;
        }
        var pvIcon = document.getElementById('pv-icon');
        if (pvIcon) pvIcon.src = '/assets/alert-icons/' + icon + '.png';

        var pvTitle = document.getElementById('pv-title');
        if (pvTitle) pvTitle.textContent = title.substring(0, 40);
        var pvMsg = document.getElementById('pv-msg');
        if (pvMsg) pvMsg.textContent = msg.substring(0, 80);

        var pvDismiss = document.getElementById('pv-dismiss');
        if (pvDismiss) {
            pvDismiss.style.display     = dismiss === '1' ? 'inline-block' : 'none';
            pvDismiss.style.color       = text;
            pvDismiss.style.borderColor = border;
        }

        var posLabel = document.getElementById('pv-position-label');
        if (posLabel) {
            posLabel.innerHTML = pos === 'sticky'
                ? '<i class="fa fa-thumb-tack"></i> Sticky'
                : '<i class="fa fa-minus"></i> Static';
        }

        var chipBg     = document.getElementById('chip-bg');
        var chipBorder = document.getElementById('chip-border');
        var chipText   = document.getElementById('chip-text');
        var chipLabels = document.getElementById('chip-labels');
        if (chipBg)     chipBg.style.background     = bg;
        if (chipBorder) chipBorder.style.background = border;
        if (chipText)   chipText.style.background   = text;
        if (chipLabels) chipLabels.textContent       = bg + ' · ' + border + ' · ' + text;
    }

    // Bind form inputs to preview
    ['f-title','f-message'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', updatePreview);
    });
    var ftype = document.getElementById('f-type');
    if (ftype) ftype.addEventListener('change', updatePreview);
    document.querySelectorAll('input[name="icon"],input[name="position"],input[name="dismissable"]')
        .forEach(function(el) { el.addEventListener('change', updatePreview); });

    // Init
    updateTabDots();
    loadSliderFromColor('bg');
    updatePreview();

    // ── Delete confirm ────────────────────────────────────────────────────
    document.querySelectorAll('.delete-alert-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (typeof swal !== 'undefined') {
                swal({
                    title: 'Delete this alert?',
                    text: 'This cannot be undone.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7f1d1d',
                    confirmButtonText: 'Yes, delete',
                }, function() { form.submit(); });
            } else {
                if (confirm('Delete this alert? This cannot be undone.')) form.submit();
            }
        });
    });

})();
</script>
@endsection

