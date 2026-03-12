<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
/* ══════════════════════════════════════
   LAYOUT
══════════════════════════════════════ */
.appt-grid {
    display: grid;
    grid-template-columns: 1fr 370px;
    gap: 1.25rem;
    align-items: start;
}
@media(max-width:1199px) { .appt-grid { grid-template-columns: 1fr; } }

/* ══════════════════════════════════════
   CALENDAR CARD
══════════════════════════════════════ */
.calendar-card {
    border: none; border-radius: .75rem;
    box-shadow: 0 2px 20px rgba(64,81,137,.1);
    overflow: hidden;
    background: #fff;
}
.calendar-header {
    background: linear-gradient(135deg,#405189 0%,#0ab39c 100%);
    padding: 1rem 1.4rem;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
}
.calendar-header h5 { color:#fff; margin:0; font-weight:700; font-size:.95rem; }
.btn-cal {
    background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
    color: #fff; border-radius: .4rem; padding: .32rem .75rem;
    font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .18s;
    display: flex; align-items: center; gap: .3rem;
}
.btn-cal:hover { background: rgba(255,255,255,.32); }
.legend-dot { width:9px; height:9px; border-radius:50%; display:inline-block; }

/* FullCalendar overrides */
#appt-cal { padding: .6rem .75rem .75rem; }
.fc .fc-toolbar.fc-header-toolbar { margin-bottom: .75rem; }
.fc .fc-toolbar-title { font-size: .95rem; font-weight: 700; color: #344563; }
.fc .fc-button {
    background: #f0f2f7 !important; border: 1px solid #e2e8f0 !important;
    color: #405189 !important; font-size: .78rem !important;
    border-radius: .35rem !important; font-weight: 600;
    box-shadow: none !important; padding: .3rem .6rem !important;
}
.fc .fc-button:hover  { background: #405189 !important; color: #fff !important; border-color: #405189 !important; }
.fc .fc-button-active { background: #405189 !important; color: #fff !important; border-color: #405189 !important; }
.fc .fc-today-button  {
    background: linear-gradient(135deg,#405189,#0ab39c) !important;
    color: #fff !important; border: none !important;
}
.fc .fc-col-header-cell { background: #f8faff; }
.fc .fc-col-header-cell-cushion {
    font-size: .7rem; font-weight: 700; letter-spacing: .06em;
    text-transform: uppercase; color: #8098bb; padding: .45rem 0; text-decoration: none;
}
.fc .fc-daygrid-day-number {
    font-size: .8rem; font-weight: 600; color: #344563;
    padding: .35rem .45rem; text-decoration: none;
}
.fc .fc-day-today { background: rgba(64,81,137,.05) !important; }
.fc .fc-day-today .fc-daygrid-day-number {
    background: linear-gradient(135deg,#405189,#0ab39c);
    color: #fff; border-radius: 50%;
    width: 24px; height: 24px;
    display: flex; align-items: center; justify-content: center; padding: 0;
}
.fc-event {
    border-radius: .3rem !important; border: none !important;
    font-size: .72rem !important; font-weight: 600 !important;
    cursor: pointer; padding: .1rem .3rem !important;
}
.ev-programada { background: linear-gradient(135deg,#299cdb,#3dcfcf) !important; }
.ev-completada { background: linear-gradient(135deg,#0ab39c,#3d9f80) !important; }
.ev-cancelada  { background: linear-gradient(135deg,#e74c3c,#c0392b) !important; }
.fc .fc-daygrid-event-dot { display: none; }

/* ══════════════════════════════════════
   SIDEBAR
══════════════════════════════════════ */
.sidebar-card {
    border: none; border-radius: .75rem;
    box-shadow: 0 2px 20px rgba(64,81,137,.1);
    overflow: hidden; background: #fff;
}
.mini-stat {
    display: flex; align-items: center; gap: .75rem;
    padding: .7rem 1rem; border-bottom: 1px solid #f0f2f7;
}
.mini-stat:last-child { border-bottom: none; }
.mini-stat-icon {
    width:38px; height:38px; border-radius:.45rem; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:1rem;
}
.mini-stat-label { font-size:.7rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#8098bb; }
.mini-stat-value { font-size:1.25rem; font-weight:800; color:#344563; line-height:1.1; }

.upcoming-item {
    display:flex; align-items:flex-start; gap:.7rem;
    padding:.7rem 1rem; border-bottom:1px solid #f3f5f9; transition:background .15s; cursor:pointer;
}
.upcoming-item:last-child { border-bottom:none; }
.upcoming-item:hover { background:#f8faff; }
.appt-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; margin-top:.3rem; }
.dot-programada { background:#299cdb; box-shadow:0 0 0 3px rgba(41,156,219,.2); }
.dot-completada { background:#0ab39c; box-shadow:0 0 0 3px rgba(10,179,156,.2); }
.dot-cancelada  { background:#e74c3c; box-shadow:0 0 0 3px rgba(231,76,60,.2); }
.upcoming-patient { font-size:.86rem; font-weight:600; color:#344563; }
.upcoming-meta    { font-size:.73rem; color:#8098bb; margin-top:.1rem; }
.upcoming-time {
    margin-left:auto; flex-shrink:0; font-size:.7rem; font-weight:700;
    background:#f0f2f7; color:#405189; padding:.18rem .5rem; border-radius:2rem;
}

/* ══════════════════════════════════════
   STATUS PILLS
══════════════════════════════════════ */
.status-pill {
    display:inline-flex; align-items:center; gap:.28rem;
    padding:.2rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700;
}
.status-pill .dot-sm { width:5px; height:5px; border-radius:50%; }
.pill-programada { background:#e0f3fb; color:#1a7faa; }
.pill-programada .dot-sm { background:#299cdb; }
.pill-completada { background:#d1fae5; color:#065f46; }
.pill-completada .dot-sm { background:#0ab39c; }
.pill-cancelada  { background:#fee2e2; color:#991b1b; }
.pill-cancelada  .dot-sm { background:#e74c3c; }

/* ══════════════════════════════════════
   TABLE
══════════════════════════════════════ */
.appt-table th {
    font-size:.69rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
    color:#8098bb; border-bottom:2px solid #e9ecef; padding:.8rem 1rem; white-space:nowrap;
}
.appt-table td { padding:.75rem 1rem; vertical-align:middle; }
.appt-table tbody tr { border-bottom:1px solid #f3f5f9; transition:background .15s; }
.appt-table tbody tr:hover { background:#f8faff; }
.appt-table tbody tr:last-child { border-bottom:none; }

.pat-av {
    width:34px; height:34px; border-radius:50%; flex-shrink:0;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.76rem; font-weight:700; color:#fff;
    background:linear-gradient(135deg,#405189,#0ab39c);
}
.btn-action {
    width:30px; height:30px; padding:0; border:none; border-radius:.35rem;
    display:inline-flex; align-items:center; justify-content:center; transition:all .15s;
}
.btn-action:hover { transform:scale(1.12); }

/* ══════════════════════════════════════
   MODALS
══════════════════════════════════════ */
.mh-primary { background:linear-gradient(135deg,#405189,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
.mh-info    { background:linear-gradient(135deg,#299cdb,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
.mh-danger  { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-radius:.5rem .5rem 0 0; }
.mh-primary .btn-close,.mh-info .btn-close,.mh-danger .btn-close { filter:invert(1); }

.form-floating>.form-control,
.form-floating>.form-select { border:1.5px solid #e2e8f0; border-radius:.5rem; }
.form-floating>.form-control:focus,
.form-floating>.form-select:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
.section-label {
    font-size:.69rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
    color:#8098bb; border-bottom:1px solid #f0f2f7; padding-bottom:.35rem; margin-bottom:.85rem;
}
.detail-row { display:flex; gap:.7rem; align-items:flex-start; padding:.6rem 0; border-bottom:1px solid #f3f5f9; }
.detail-row:last-child { border-bottom:none; }
.detail-icon { width:32px; height:32px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.detail-lbl { font-size:.67rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#8098bb; }
.detail-val { font-size:.87rem; font-weight:500; color:#344563; margin-top:.1rem; }

/* ══════════════════════════════════════
   PATIENT SEARCH WIDGET
══════════════════════════════════════ */
.ps-wrap { position:relative; }
.ps-box {
    display:flex; align-items:center;
    border:1.5px solid #e2e8f0; border-radius:.5rem; overflow:hidden;
    transition:border-color .2s, box-shadow .2s;
    background:#fff;
}
.ps-box.focused { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
.ps-box .ps-icon { padding:0 .65rem; color:#8098bb; font-size:1rem; flex-shrink:0; }
.ps-box input {
    flex:1; border:none; outline:none; padding:.65rem .5rem .65rem 0;
    font-size:.88rem; background:transparent; color:#344563;
    min-width:0;
}
.ps-box input::placeholder { color:#adb5bd; }
.ps-dropdown {
    display:none; position:absolute; top:calc(100% + 2px); left:0; right:0; z-index:9999;
    background:#fff; border:1.5px solid #e2e8f0; border-radius:.5rem;
    max-height:230px; overflow-y:auto;
    box-shadow:0 8px 30px rgba(64,81,137,.15);
}
.ps-item {
    display:flex; align-items:center; gap:.6rem;
    padding:.6rem .9rem; cursor:pointer; border-bottom:1px solid #f3f5f9;
    transition:background .1s;
}
.ps-item:last-child { border-bottom:none; }
.ps-item:hover { background:#f0f4ff; }
.ps-item-av {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#405189,#0ab39c);
    display:flex; align-items:center; justify-content:center;
    font-size:.68rem; font-weight:700; color:#fff;
}
.ps-item-name { font-size:.85rem; font-weight:600; color:#344563; }
.ps-item-ced  { font-size:.73rem; color:#8098bb; font-family:monospace; }
.ps-msg { padding:.7rem .9rem; font-size:.83rem; color:#8098bb; text-align:center; }

.ps-pill {
    display:none; align-items:center; gap:.45rem; margin-top:.45rem;
    background:linear-gradient(135deg,rgba(64,81,137,.07),rgba(10,179,156,.07));
    border:1px solid rgba(64,81,137,.18); border-radius:2rem;
    padding:.28rem .7rem .28rem .4rem; width:fit-content;
}
.ps-pill.visible { display:flex; }
.ps-pill-av {
    width:24px; height:24px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#405189,#0ab39c);
    display:flex; align-items:center; justify-content:center;
    font-size:.65rem; font-weight:700; color:#fff;
}
.ps-pill-name { font-size:.83rem; font-weight:600; color:#344563; }
.ps-pill-ced  { font-size:.72rem; color:#8098bb; font-family:monospace; }
.ps-pill-x {
    background:none; border:none; color:#94a3b8; padding:0; cursor:pointer;
    line-height:1; font-size:.95rem; margin-left:.1rem;
}
.ps-pill-x:hover { color:#e74c3c; }

/* ══════════════════════════════════════
   TOAST & ANIMATIONS
══════════════════════════════════════ */
#toast-container {
    position:fixed; top:1.1rem; right:1.1rem; z-index:99999;
    display:flex; flex-direction:column; gap:.4rem;
}
.toast-item {
    min-width:270px; padding:.8rem 1rem; border-radius:.5rem; color:#fff;
    font-size:.86rem; font-weight:500; display:flex; align-items:center; gap:.55rem;
    box-shadow:0 4px 20px rgba(0,0,0,.18);
    animation:toastIn .28s ease;
}
@keyframes toastIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
.toast-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
.toast-error   { background:linear-gradient(135deg,#e74c3c,#c0392b); }

@keyframes fadeInUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.anim-row { animation:fadeInUp .28s ease both; }
.anim-row:nth-child(1){animation-delay:.03s}.anim-row:nth-child(2){animation-delay:.07s}
.anim-row:nth-child(3){animation-delay:.11s}.anim-row:nth-child(4){animation-delay:.15s}
.anim-row:nth-child(5){animation-delay:.19s}.anim-row:nth-child(6){animation-delay:.23s}
</style>

<div id="toast-container"></div>

{{-- Breadcrumb --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Gestión de Citas</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Citas</li>
            </ol>
        </div>
    </div>
</div>

{{-- ══ TOP GRID ══ --}}
<div class="appt-grid mb-4">

    {{-- Calendario --}}
    <div class="calendar-card">
        <div class="calendar-header">
            <h5><i class="ri-calendar-2-line me-2"></i>Calendario de Citas</h5>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2" style="font-size:.73rem;color:rgba(255,255,255,.85);">
                    <span class="legend-dot" style="background:#299cdb;"></span>Programada
                    <span class="legend-dot" style="background:#0ab39c;"></span>Completada
                    <span class="legend-dot" style="background:#e74c3c;"></span>Cancelada
                </div>
                <button class="btn-cal" onclick="openCreateModal()">
                    <i class="ri-add-line"></i>Nueva Cita
                </button>
            </div>
        </div>
        <div id="appt-cal"></div>
    </div>

    {{-- Sidebar --}}
    <div class="d-flex flex-column gap-3">

        {{-- Stats --}}
        <div class="sidebar-card">
            <div class="p-3" style="border-bottom:1px solid #f0f2f7;">
                <h6 class="fw-bold mb-0" style="font-size:.83rem;color:#344563;">
                    <i class="ri-bar-chart-line me-1 text-primary"></i>Resumen
                </h6>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-icon bg-primary-subtle text-primary"><i class="ri-calendar-check-line"></i></div>
                <div>
                    <div class="mini-stat-label">Total</div>
                    <div class="mini-stat-value">{{ $appointments->total() }}</div>
                </div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-icon" style="background:#e0f3fb;color:#1a7faa;"><i class="ri-time-line"></i></div>
                <div>
                    <div class="mini-stat-label">Programadas</div>
                    <div class="mini-stat-value" style="color:#1a7faa;">{{ $appointments->getCollection()->where('status','programada')->count() }}</div>
                </div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-icon bg-success-subtle text-success"><i class="ri-checkbox-circle-line"></i></div>
                <div>
                    <div class="mini-stat-label">Completadas</div>
                    <div class="mini-stat-value" style="color:#0ab39c;">{{ $appointments->getCollection()->where('status','completada')->count() }}</div>
                </div>
            </div>
            <div class="mini-stat">
                <div class="mini-stat-icon bg-danger-subtle text-danger"><i class="ri-close-circle-line"></i></div>
                <div>
                    <div class="mini-stat-label">Canceladas</div>
                    <div class="mini-stat-value" style="color:#e74c3c;">{{ $appointments->getCollection()->where('status','cancelada')->count() }}</div>
                </div>
            </div>
        </div>

        {{-- Próximas --}}
        <div class="sidebar-card">
            <div class="p-3" style="border-bottom:1px solid #f0f2f7;">
                <h6 class="fw-bold mb-0" style="font-size:.83rem;color:#344563;">
                    <i class="ri-calendar-todo-line me-1 text-primary"></i>Próximas citas
                </h6>
            </div>
            @php
                $upcoming = $appointments->getCollection()
                    ->where('status','programada')
                    ->sortBy('appointment_date')
                    ->take(6);
            @endphp
            @forelse($upcoming as $u)
            <div class="upcoming-item" onclick="openShowModal({{ $u->id }})">
                <div class="appt-dot dot-programada"></div>
                <div class="flex-grow-1">
                    <div class="upcoming-patient">{{ $u->patient->first_name }} {{ $u->patient->last_name }}</div>
                    <div class="upcoming-meta"><i class="ri-user-voice-line me-1"></i>{{ $u->audiologist->name }}</div>
                    <div class="upcoming-meta"><i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($u->appointment_date)->translatedFormat('d M Y') }}</div>
                </div>
                <span class="upcoming-time">{{ \Carbon\Carbon::parse($u->appointment_time)->format('g:i A') }}</span>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="ri-calendar-line text-muted" style="font-size:2rem;opacity:.3;"></i>
                <p class="text-muted mb-0 mt-2" style="font-size:.8rem;">Sin citas próximas</p>
            </div>
            @endforelse
        </div>

    </div>
</div>

{{-- ══ TABLA ══ --}}
<div class="card shadow-sm" style="border-radius:.75rem;border:none;">
    <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
        <h5 class="card-title mb-0 flex-grow-1">Todas las Citas</h5>
        <select id="status-filter" class="form-select form-select-sm"
                style="width:155px;border-radius:2rem;border:1.5px solid #e2e8f0;font-size:.84rem;">
            <option value="">Todos los estados</option>
            <option value="programada">Programadas</option>
            <option value="completada">Completadas</option>
            <option value="cancelada">Canceladas</option>
        </select>
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                style="border-radius:2rem;padding:.38rem .9rem;" onclick="openCreateModal()">
            <i class="ri-add-line"></i>Nueva Cita
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table appt-table mb-0">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Audiólogo</th>
                        <th class="d-none d-md-table-cell">Sucursal</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="appt-tbody">
                    @forelse($appointments as $appt)
                    <tr class="anim-row" data-id="{{ $appt->id }}" data-status="{{ $appt->status }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="pat-av">{{ strtoupper(substr($appt->patient->first_name,0,1).substr($appt->patient->last_name,0,1)) }}</div>
                                <span class="fw-semibold" style="font-size:.87rem;">{{ $appt->patient->first_name }} {{ $appt->patient->last_name }}</span>
                            </div>
                        </td>
                        <td><span style="font-size:.84rem;"><i class="ri-user-voice-line me-1 text-muted"></i>{{ $appt->audiologist->name }}</span></td>
                        <td class="d-none d-md-table-cell"><span class="text-muted" style="font-size:.82rem;">{{ $appt->branch->name }}</span></td>
                        <td><span style="font-size:.84rem;font-weight:600;">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d/m/Y') }}</span></td>
                        <td>
                            <span style="font-size:.8rem;background:#f0f2f7;color:#405189;padding:.16rem .5rem;border-radius:2rem;font-weight:700;">
                                {{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}
                            </span>
                        </td>
                        <td>
                            <span class="status-pill pill-{{ $appt->status }}">
                                <span class="dot-sm"></span>{{ ucfirst($appt->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-action bg-info-subtle text-info" title="Ver"
                                        onclick="openShowModal({{ $appt->id }})">
                                    <i class="ri-eye-fill fs-13"></i>
                                </button>
                                <button class="btn btn-action bg-warning-subtle text-warning" title="Editar"
                                        onclick="openEditModal({{ $appt->id }})">
                                    <i class="ri-pencil-fill fs-13"></i>
                                </button>
                                <button class="btn btn-action bg-danger-subtle text-danger" title="Eliminar"
                                        onclick="openDeleteModal({{ $appt->id }},'{{ addslashes($appt->patient->first_name.' '.$appt->patient->last_name) }}','{{ $appt->appointment_date }}')">
                                    <i class="ri-delete-bin-fill fs-13"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5">
                                <i class="ri-calendar-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                <p class="text-muted mb-0">No hay citas registradas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($appointments->hasPages())
        <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>
</div>

</div><!-- container -->
</div><!-- page-content -->

{{-- ══════════════════════════════════════════
     MODAL: Crear / Editar
══════════════════════════════════════════ --}}
<div class="modal fade" id="apptModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:510px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-primary py-3">
                <h5 class="modal-title d-flex align-items-center gap-2" id="appt-modal-title">
                    <i class="ri-calendar-add-line fs-18"></i>Nueva Cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="appt-alert" class="alert d-none mb-3"></div>
                <form id="appt-form" novalidate autocomplete="off">
                    <input type="hidden" id="f-id">
                    <input type="hidden" id="f-method" value="POST">

                    {{-- Sección 1 --}}
                    <p class="section-label"><i class="ri-user-heart-line me-1"></i>Paciente y Audiólogo</p>
                    <div class="row g-3 mb-4">

                        {{-- PATIENT SEARCH --}}
                        <div class="col-12">
                            <input type="hidden" id="f-patient-id">
                            <div class="ps-wrap">
                                {{-- Input box (visible cuando no hay paciente seleccionado) --}}
                                <div class="ps-box" id="ps-box">
                                    <span class="ps-icon"><i class="ri-search-line"></i></span>
                                    <input type="text" id="ps-input"
                                           placeholder="Buscar paciente por nombre o cédula..."
                                           autocomplete="off" spellcheck="false">
                                </div>
                                {{-- Dropdown --}}
                                <div class="ps-dropdown" id="ps-dropdown">
                                    <div id="ps-results"></div>
                                </div>
                                {{-- Pill (visible cuando hay paciente seleccionado) --}}
                                <div class="ps-pill" id="ps-pill">
                                    <div class="ps-pill-av" id="ps-pill-av">?</div>
                                    <span class="ps-pill-name" id="ps-pill-name">—</span>
                                    <span class="ps-pill-ced" id="ps-pill-ced"></span>
                                    <button type="button" class="ps-pill-x" onclick="psClear()" title="Cambiar paciente">
                                        <i class="ri-close-circle-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-danger mt-1" style="font-size:.8rem;" id="err-patient_id"></div>
                        </div>

                        {{-- Audiólogo --}}
                        <div class="col-12">
                            <div class="form-floating">
                                <select class="form-select" id="f-audiologist">
                                    <option value="">— Seleccionar —</option>
                                    @foreach(\App\Models\User::whereHas('role', fn($q) => $q->where('name','audiologo'))->orderBy('name')->get() as $au)
                                        <option value="{{ $au->id }}">{{ $au->name }}</option>
                                    @endforeach
                                </select>
                                <label><i class="ri-user-voice-line me-1 text-muted"></i>Audiólogo</label>
                                <div class="invalid-feedback" id="err-audiologist_id"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección 2 --}}
                    <p class="section-label"><i class="ri-calendar-event-line me-1"></i>Fecha y hora</p>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="f-date" placeholder="Fecha">
                                <label>Fecha</label>
                                <div class="invalid-feedback" id="err-appointment_date"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="f-time" placeholder="Hora">
                                <label>Hora</label>
                                <div class="invalid-feedback" id="err-appointment_time"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección 3: Estado (solo en edición) --}}
                    <div id="status-wrap" style="display:none;">
                        <p class="section-label"><i class="ri-flag-line me-1"></i>Estado</p>
                        <div class="form-floating">
                            <select class="form-select" id="f-status">
                                <option value="programada">Programada</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                            <label>Estado de la cita</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-save" onclick="saveAppt()">
                    <span class="spinner-border spinner-border-sm d-none" id="save-spin"></span>
                    <i class="ri-save-line" id="save-icon"></i><span>Guardar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Ver detalle
══════════════════════════════════════════ --}}
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:430px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-calendar-event-line fs-18"></i>Detalle de Cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="pat-av mx-auto mb-2" style="width:50px;height:50px;font-size:1.05rem;" id="show-av">??</div>
                    <h5 class="mb-1 fw-bold" id="show-patient">—</h5>
                    <div id="show-status-badge"></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-info-subtle text-info"><i class="ri-user-voice-line"></i></div>
                    <div><div class="detail-lbl">Audiólogo</div><div class="detail-val" id="show-audiologist">—</div></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-building-2-line"></i></div>
                    <div><div class="detail-lbl">Sucursal</div><div class="detail-val" id="show-branch">—</div></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-warning-subtle text-warning"><i class="ri-calendar-line"></i></div>
                    <div><div class="detail-lbl">Fecha</div><div class="detail-val" id="show-date">—</div></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-success-subtle text-success"><i class="ri-time-line"></i></div>
                    <div><div class="detail-lbl">Hora</div><div class="detail-val" id="show-time">—</div></div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="switchToEdit()">
                    <i class="ri-pencil-line me-1"></i>Editar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Eliminar
══════════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:390px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="pat-av mx-auto mb-3" id="del-av"
                     style="width:56px;height:56px;font-size:1.15rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">?</div>
                <p class="mb-1 fs-5 fw-semibold" id="del-name">—</p>
                <p class="text-muted mb-0" style="font-size:.85rem;" id="del-date"></p>
                <p class="text-muted mt-1 mb-0" style="font-size:.84rem;">
                    Esta acción es <strong>irreversible</strong>. ¿Confirmas?
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="min-width:95px;">Cancelar</button>
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2"
                        id="btn-del" style="min-width:115px;" onclick="confirmDelete()">
                    <span class="spinner-border spinner-border-sm d-none" id="del-spin"></span>
                    <i class="ri-delete-bin-line" id="del-icon"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- FullCalendar v6 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/es.global.min.js"></script>

<script>
/* ══════════════════════════════════════
   CONSTANTS
══════════════════════════════════════ */
const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
const URL_STORE   = "{{ route('appointments.store') }}";
const URL_BASE    = "{{ route('appointments.index') }}".replace(/\/+$/, '');
const URL_SEARCH  = "{{ route('api.patients.search') }}";

const urlShow    = id => `${URL_BASE}/${id}/show-data`;
const urlEdit    = id => `${URL_BASE}/${id}/edit-data`;
const urlUpdate  = id => `${URL_BASE}/${id}`;
const urlDestroy = id => `${URL_BASE}/${id}`;

/* ══════════════════════════════════════
   STATE
══════════════════════════════════════ */
let apptModal, showModal, deleteModal;
let delId = null, showId = null;

/* ══════════════════════════════════════
   CALENDAR EVENTS (PHP → JS)
══════════════════════════════════════ */
const CAL_EVENTS = [
    @foreach($appointments->getCollection() as $a)
    {
        id        : {{ $a->id }},
        title     : '{{ addslashes($a->patient->first_name) }} {{ \Carbon\Carbon::parse($a->appointment_time)->format("g:iA") }}',
        start     : '{{ $a->appointment_date }}T{{ $a->appointment_time }}',
        classNames: ['ev-{{ $a->status }}'],
    },
    @endforeach
];

/* ══════════════════════════════════════
   BOOT
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {

    // Modals
    apptModal  = new bootstrap.Modal(document.getElementById('apptModal'));
    showModal  = new bootstrap.Modal(document.getElementById('showModal'));
    deleteModal= new bootstrap.Modal(document.getElementById('deleteModal'));

    // Flash messages
    @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
    @if(session('error'))   showToast("{{ session('error') }}",   'error');   @endif

    // FullCalendar
    const cal = new FullCalendar.Calendar(document.getElementById('appt-cal'), {
        locale      : 'es',
        initialView : 'dayGridMonth',
        height      : 'auto',
        headerToolbar: {
            left  : 'prev,next today',
            center: 'title',
            right : 'dayGridMonth,timeGridWeek,listWeek',
        },
        buttonText: { today:'Hoy', month:'Mes', week:'Semana', list:'Lista' },
        events      : CAL_EVENTS,
        eventClick  : info => openShowModal(info.event.id),
        dateClick   : info => openCreateModal(info.dateStr),
    });
    cal.render();

    // Table filter
    document.getElementById('status-filter').addEventListener('change', function () {
        const val = this.value;
        document.querySelectorAll('#appt-tbody tr[data-id]').forEach(tr => {
            tr.style.display = (!val || tr.dataset.status === val) ? '' : 'none';
        });
    });

    // Patient search
    psInit();
});

/* ══════════════════════════════════════
   PATIENT SEARCH — autónomo, sin blur race
══════════════════════════════════════ */
function psInit() {
    const inp  = document.getElementById('ps-input');
    const drop = document.getElementById('ps-dropdown');
    let timer  = null;
    let open   = false;

    inp.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { psCloseDrop(); return; }
        // Loading
        document.getElementById('ps-results').innerHTML =
            '<div class="ps-msg"><span class="spinner-border spinner-border-sm me-1"></span>Buscando…</div>';
        drop.style.display = 'block';
        open = true;
        timer = setTimeout(() => psFetch(q), 320);
    });

    inp.addEventListener('focus', function () {
        document.getElementById('ps-box').classList.add('focused');
        const q = this.value.trim();
        if (q.length >= 2 && !open) psFetch(q);
    });

    inp.addEventListener('blur', function () {
        document.getElementById('ps-box').classList.remove('focused');
        // Delay to allow mousedown on item to fire first
        setTimeout(psCloseDrop, 250);
    });

    // Close when clicking outside
    document.addEventListener('mousedown', function (e) {
        if (!e.target.closest('.ps-wrap')) {
            psCloseDrop();
        }
    });
}

async function psFetch(q) {
    try {
        const r = await fetch(URL_SEARCH + '?q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!r.ok) throw new Error('HTTP ' + r.status);
        const list = await r.json();
        psRender(list);
    } catch (e) {
        document.getElementById('ps-results').innerHTML =
            '<div class="ps-msg text-danger"><i class="ri-error-warning-line me-1"></i>Error al buscar</div>';
        console.error('Patient search:', e);
    }
}

function psRender(list) {
    const box = document.getElementById('ps-results');

    if (!list.length) {
        box.innerHTML = '<div class="ps-msg"><i class="ri-search-line me-1"></i>Sin resultados</div>';
    } else {

        box.innerHTML = list.map(p => {

            const name = p.full_name || '';
            const ced  = p.cedula || '';

            const parts = name.split(' ');
            const ini = ((parts[0] || '')[0] || '') + ((parts[1] || '')[0] || '');

            return `
                <div class="ps-item"
                    data-id="${p.id}"
                    data-name="${name}"
                    data-ced="${ced}"
                    data-ini="${ini.toUpperCase()}"
                    onmousedown="psSelect(this)">

                    <div class="ps-item-av">${ini.toUpperCase()}</div>

                    <div>
                        <div class="ps-item-name">${name}</div>
                        <div class="ps-item-ced">${ced}</div>
                    </div>

                </div>
            `;
        }).join('');
    }

    document.getElementById('ps-dropdown').style.display = 'block';
}

// KEY FIX: onmousedown fires BEFORE blur, so the selection always registers
function psSelect(el) {
    document.getElementById('f-patient-id').value  = el.dataset.id;
    document.getElementById('ps-pill-av').textContent   = el.dataset.ini;
    document.getElementById('ps-pill-name').textContent = el.dataset.name;
    document.getElementById('ps-pill-ced').textContent  = el.dataset.ced;
    document.getElementById('ps-pill').classList.add('visible');
    document.getElementById('ps-box').style.display    = 'none';
    document.getElementById('err-patient_id').textContent = '';
    psCloseDrop();
}

function psClear() {
    document.getElementById('f-patient-id').value = '';
    document.getElementById('ps-input').value      = '';
    document.getElementById('ps-pill').classList.remove('visible');
    document.getElementById('ps-box').style.display = '';
    psCloseDrop();
    document.getElementById('ps-input').focus();
}

function psCloseDrop() {
    document.getElementById('ps-dropdown').style.display = 'none';
}

function psSetPill(id, name, cedula) {
    // Used when loading edit modal
    if (!id) return;
    const parts = name.trim().split(' ');
    const ini   = ((parts[0]||'')[0]||'') + ((parts[1]||'')[0]||'');
    document.getElementById('f-patient-id').value       = id;
    document.getElementById('ps-pill-av').textContent   = ini.toUpperCase();
    document.getElementById('ps-pill-name').textContent = name;
    document.getElementById('ps-pill-ced').textContent  = cedula || '';
    document.getElementById('ps-pill').classList.add('visible');
    document.getElementById('ps-box').style.display    = 'none';
}

function psReset() {
    document.getElementById('f-patient-id').value = '';
    document.getElementById('ps-input').value     = '';
    document.getElementById('ps-pill').classList.remove('visible');
    document.getElementById('ps-box').style.display = '';
    psCloseDrop();
}

/* ══════════════════════════════════════
   MODALS — CREATE / EDIT / SHOW / DELETE
══════════════════════════════════════ */
function openCreateModal(dateStr) {
    clearForm();
    document.getElementById('appt-modal-title').innerHTML = '<i class="ri-calendar-add-line fs-18"></i>Nueva Cita';
    document.getElementById('f-method').value = 'POST';
    document.getElementById('f-id').value     = '';
    document.getElementById('status-wrap').style.display = 'none';
    if (dateStr) document.getElementById('f-date').value = dateStr;
    apptModal.show();
}

async function openEditModal(id) {
    clearForm();
    document.getElementById('appt-modal-title').innerHTML = '<i class="ri-pencil-line fs-18"></i>Editar Cita';
    document.getElementById('f-method').value = 'PUT';
    document.getElementById('f-id').value     = id;
    document.getElementById('status-wrap').style.display = '';
    apptModal.show();

    try {
        const r = await fetch(urlEdit(id), { headers: {'Accept':'application/json','X-CSRF-TOKEN':CSRF} });
        if (!r.ok) throw new Error();
        const d = await r.json();
        psSetPill(d.patient_id, d.patient_name, d.patient_cedula);
        document.getElementById('f-audiologist').value = d.audiologist_id   || '';
        document.getElementById('f-date').value        = d.appointment_date_raw || '';
        document.getElementById('f-time').value        = d.appointment_time_raw || '';
        document.getElementById('f-status').value      = d.status           || 'programada';
    } catch {
        showToast('Error al cargar la cita.', 'error');
    }
}

async function saveAppt() {
    const id     = document.getElementById('f-id').value;
    const method = document.getElementById('f-method').value;
    const url    = id ? urlUpdate(id) : URL_STORE;

    const patId = document.getElementById('f-patient-id').value;
    if (!patId) {
        document.getElementById('err-patient_id').textContent = 'Debes seleccionar un paciente.';
        return;
    }

    const payload = {
        patient_id       : patId,
        audiologist_id   : document.getElementById('f-audiologist').value,
        appointment_date : document.getElementById('f-date').value,
        appointment_time : document.getElementById('f-time').value,
        status           : document.getElementById('f-status').value || 'programada',
    };

    clearErrors();
    setLoading('btn-save', 'save-spin', 'save-icon', true);

    try {
        const r    = await fetch(url, {
            method : 'POST',
            headers: {
                'Content-Type':'application/json', 'Accept':'application/json',
                'X-CSRF-TOKEN': CSRF, 'X-HTTP-Method-Override': method,
            },
            body: JSON.stringify(payload),
        });
        const data = await r.json();

        if (!r.ok) {
            if (data.errors) showFieldErrors(data.errors);
            else setAlert(data.message || 'Error al guardar.', 'danger');
            return;
        }

        apptModal.hide();
        showToast(data.message || 'Cita guardada.', 'success');
        setTimeout(() => location.reload(), 750);

    } catch {
        setAlert('Error de conexión.', 'danger');
    } finally {
        setLoading('btn-save', 'save-spin', 'save-icon', false);
    }
}

async function openShowModal(id) {
    showId = id;
    ['show-patient','show-audiologist','show-branch','show-date','show-time']
        .forEach(i => document.getElementById(i).textContent = '…');
    document.getElementById('show-av').textContent        = '…';
    document.getElementById('show-status-badge').innerHTML = '';
    showModal.show();

    try {
        const r = await fetch(urlShow(id), { headers: {'Accept':'application/json','X-CSRF-TOKEN':CSRF} });
        if (!r.ok) throw new Error();
        const d = await r.json();

        const ini = d.patient_name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();
        const av = document.getElementById('show-av');
        av.textContent  = ini;
        av.style.width  = '50px';
        av.style.height = '50px';
        av.style.fontSize = '1.05rem';

        document.getElementById('show-patient').textContent     = d.patient_name;
        document.getElementById('show-audiologist').textContent  = d.audiologist_name;
        document.getElementById('show-branch').textContent      = d.branch_name;
        document.getElementById('show-date').textContent        = d.appointment_date;
        document.getElementById('show-time').textContent        = d.appointment_time;

        const pillCls = {programada:'pill-programada', completada:'pill-completada', cancelada:'pill-cancelada'};
        document.getElementById('show-status-badge').innerHTML =
            `<span class="status-pill ${pillCls[d.status]||''}"><span class="dot-sm"></span>${d.status.charAt(0).toUpperCase()+d.status.slice(1)}</span>`;

    } catch { showToast('Error al cargar.', 'error'); }
}

function switchToEdit() {
    showModal.hide();
    setTimeout(() => openEditModal(showId), 280);
}

function openDeleteModal(id, name, date) {
    delId = id;
    const ini = name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();
    document.getElementById('del-av').textContent   = ini;
    document.getElementById('del-name').textContent = name;
    document.getElementById('del-date').textContent = date;
    deleteModal.show();
}

async function confirmDelete() {
    setLoading('btn-del', 'del-spin', 'del-icon', true);
    try {
        const r    = await fetch(urlDestroy(delId), {
            method : 'POST',
            headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-HTTP-Method-Override':'DELETE'},
            body   : '{}',
        });
        const data = await r.json();
        deleteModal.hide();
        if (!r.ok) { showToast(data.message || 'Error al eliminar.', 'error'); return; }
        showToast(data.message || 'Cita eliminada.', 'success');
        const row = document.querySelector(`#appt-tbody tr[data-id="${delId}"]`);
        if (row) {
            row.style.transition = 'opacity .28s, transform .28s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(30px)';
            setTimeout(() => row.remove(), 300);
        }
    } catch { showToast('Error de conexión.', 'error'); }
    finally  { setLoading('btn-del', 'del-spin', 'del-icon', false); }
}

/* ══════════════════════════════════════
   HELPERS
══════════════════════════════════════ */
function clearForm() {
    psReset();
    document.getElementById('f-audiologist').value = '';
    document.getElementById('f-date').value        = '';
    document.getElementById('f-time').value        = '';
    document.getElementById('f-status').value      = 'programada';
    clearErrors();
    document.getElementById('appt-alert').className = 'alert d-none';
}

function clearErrors() {
    ['err-patient_id','err-audiologist_id','err-appointment_date','err-appointment_time'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '';
    });
    document.querySelectorAll('#appt-form .is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showFieldErrors(errors) {
    const map = {
        patient_id:'err-patient_id', audiologist_id:'err-audiologist_id',
        appointment_date:'err-appointment_date', appointment_time:'err-appointment_time',
    };
    Object.entries(errors).forEach(([f, msgs]) => {
        const el = document.getElementById(map[f]);
        if (el) el.textContent = msgs[0];
        const inp = document.getElementById({patient_id:'f-patient-id',audiologist_id:'f-audiologist',appointment_date:'f-date',appointment_time:'f-time'}[f]);
        if (inp) inp.classList.add('is-invalid');
    });
}

function setAlert(msg, type) {
    const el = document.getElementById('appt-alert');
    el.className   = 'alert alert-' + type;
    el.textContent = msg;
}

function setLoading(btnId, spinId, iconId, on) {
    document.getElementById(btnId).disabled = on;
    document.getElementById(spinId).classList.toggle('d-none', !on);
    document.getElementById(iconId).classList.toggle('d-none', on);
}

function showToast(msg, type) {
    const d = document.createElement('div');
    d.className = 'toast-item toast-' + (type || 'success');
    d.innerHTML = `<i class="ri-${type==='error'?'error-warning':'checkbox-circle'}-line fs-16"></i>${msg}`;
    document.getElementById('toast-container').appendChild(d);
    setTimeout(() => {
        d.style.transition = 'opacity .35s'; d.style.opacity = '0';
        setTimeout(() => d.remove(), 380);
    }, 3400);
}
</script>
@endpush

</x-app-layout>