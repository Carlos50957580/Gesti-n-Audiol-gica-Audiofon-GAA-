<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
:root {
    --ap:#405189; --at:#0ab39c; --aa:#f7b84b; --ar:#f06548; --av:#7066e0; --ab:#299cdb;
    --ink:#1e2535; --muted:#6b7a99; --border:#edf0f7; --surface:#f8faff; --radius:.85rem;
}

/* ── Stat cards ── */
.sc {
    background:#fff; border-radius:var(--radius);
    box-shadow:0 2px 20px rgba(64,81,137,.08);
    padding:1.2rem 1.3rem; border-left:4px solid transparent;
    position:relative; overflow:hidden;
    transition:transform .18s, box-shadow .18s;
}
.sc:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(64,81,137,.14); }
.sc.c-t { border-color:var(--at); }
.sc.c-p { border-color:var(--ap); }
.sc.c-a { border-color:var(--aa); }
.sc.c-r { border-color:var(--ar); }
.sc-ico { width:42px; height:42px; border-radius:.55rem; display:flex; align-items:center; justify-content:center; font-size:1.2rem; margin-bottom:.75rem; }
.c-t .sc-ico { background:rgba(10,179,156,.1);  color:var(--at); }
.c-p .sc-ico { background:rgba(64,81,137,.1);   color:var(--ap); }
.c-a .sc-ico { background:rgba(247,184,75,.12); color:var(--aa); }
.c-r .sc-ico { background:rgba(240,101,72,.1);  color:var(--ar); }
.sc-val  { font-size:1.55rem; font-weight:800; color:var(--ink); letter-spacing:-.04em; line-height:1; }
.sc-lbl  { font-size:.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-top:.2rem; }
.sc-ghost { position:absolute; right:-6px; bottom:-6px; font-size:4rem; opacity:.04; line-height:1; }

/* ── Main card ── */
.main-card { background:#fff; border-radius:var(--radius); box-shadow:0 2px 20px rgba(64,81,137,.08); overflow:hidden; }

/* ── Tabs ── */
.ap-tabs { display:flex; border-bottom:2px solid var(--border); padding:0 1.25rem; gap:.15rem; background:#fff; }
.ap-tab {
    padding:.7rem 1.1rem; font-size:.82rem; font-weight:700; color:var(--muted);
    border:none; background:none; cursor:pointer; border-bottom:2px solid transparent;
    margin-bottom:-2px; transition:color .18s, border-color .18s; white-space:nowrap;
    display:flex; align-items:center; gap:.45rem;
}
.ap-tab:hover { color:var(--ap); }
.ap-tab.active { color:var(--ap); border-bottom-color:var(--ap); }
.ap-tab .tab-badge {
    font-size:.65rem; font-weight:800; padding:.1rem .45rem; border-radius:2rem;
    background:rgba(64,81,137,.1); color:var(--ap);
}
.ap-tab.active .tab-badge { background:var(--ap); color:#fff; }

/* ── Search bar ── */
.search-bar {
    display:flex; align-items:center;
    background:var(--surface); border:1.5px solid var(--border);
    border-radius:2rem; padding:.42rem .75rem .42rem 1rem;
    transition:border-color .2s, box-shadow .2s;
}
.search-bar:focus-within { border-color:var(--ap); box-shadow:0 0 0 3px rgba(64,81,137,.1); }
.search-bar input { border:none; background:transparent; outline:none; font-size:.85rem; flex:1; min-width:0; }
.search-bar i { color:var(--muted); font-size:.95rem; margin-right:.4rem; }

/* ── Table ── */
.ap-table { width:100%; border-collapse:collapse; }
.ap-table th {
    font-size:.67rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
    color:var(--muted); padding:.75rem 1rem; border-bottom:2px solid var(--border);
    background:var(--surface); white-space:nowrap;
}
.ap-table td { padding:.85rem 1rem; border-bottom:1px solid var(--border); font-size:.85rem; color:var(--ink); vertical-align:middle; }
.ap-table tbody tr:last-child td { border-bottom:none; }
.ap-table tbody tr { transition:background .12s; }
.ap-table tbody tr:hover td { background:var(--surface); }

/* ── Patient avatar ── */
.pat-av {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.74rem; font-weight:800; color:#fff;
}
.av0{background:linear-gradient(135deg,#405189,#6b7fe0)}
.av1{background:linear-gradient(135deg,#0ab39c,#2dcfb5)}
.av2{background:linear-gradient(135deg,#f7b84b,#e8993a)}
.av3{background:linear-gradient(135deg,#f06548,#d44f36)}
.av4{background:linear-gradient(135deg,#7066e0,#9b8ff0)}
.av5{background:linear-gradient(135deg,#299cdb,#1a7fb0)}

/* ── Status badges ── */
.st-badge { font-size:.69rem; font-weight:700; padding:.22rem .65rem; border-radius:2rem; white-space:nowrap; }
.st-prog  { background:rgba(64,81,137,.1);  color:var(--ap); }
.st-comp  { background:rgba(10,179,156,.1);  color:var(--at); }
.st-canc  { background:rgba(240,101,72,.1);  color:var(--ar); }

/* ── Time display ── */
.ap-time { font-size:.82rem; font-weight:800; color:var(--ap); font-variant-numeric:tabular-nums; }
.ap-date { font-size:.78rem; color:var(--muted); margin-top:.1rem; }

/* ── Insurance pill ── */
.ins-pill { background:rgba(10,179,156,.08); color:var(--at); font-size:.71rem; font-weight:700; padding:.15rem .55rem; border-radius:2rem; }

/* ── Notes preview ── */
.notes-prev { font-size:.75rem; color:var(--muted); max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.notes-empty { font-size:.75rem; color:var(--border); font-style:italic; }

/* ── Action buttons ── */
.btn-action {
    width:30px; height:30px; border-radius:.4rem; border:none; cursor:pointer;
    display:inline-flex; align-items:center; justify-content:center; font-size:.9rem;
    transition:all .15s;
}
.btn-action:hover { transform:scale(1.12); }
.ba-view     { background:rgba(64,81,137,.1);  color:var(--ap); }
.ba-complete { background:rgba(10,179,156,.1);  color:var(--at); }
.ba-cancel   { background:rgba(240,101,72,.1);  color:var(--ar); }
.ba-note     { background:rgba(247,184,75,.12); color:var(--aa); }
.ba-undo     { background:rgba(112,102,224,.1); color:var(--av); }

/* ── Empty state ── */
.empty-state { text-align:center; padding:3.5rem 1rem; color:var(--muted); }
.empty-state i { font-size:2.8rem; opacity:.2; display:block; margin-bottom:.65rem; }
.empty-state p { font-size:.88rem; margin:0; }

/* ── TODAY empty / greeting ── */
.today-empty {
    text-align:center; padding:3rem 1rem;
    background:linear-gradient(135deg,rgba(64,81,137,.03),rgba(10,179,156,.03));
    border-radius:.65rem; margin:.75rem;
}
.today-empty i { font-size:3rem; color:var(--at); opacity:.35; display:block; margin-bottom:.6rem; }

/* ── MODAL ── */
.mh-ap   { background:linear-gradient(135deg,var(--ap),var(--at)); }
.mh-note { background:linear-gradient(135deg,var(--aa),#e8993a); }
.mh-ap .btn-close,
.mh-note .btn-close { filter:invert(1); }

.detail-row { display:flex; padding:.5rem 0; border-bottom:1px solid var(--border); align-items:flex-start; gap:1rem; }
.detail-row:last-child { border-bottom:none; }
.detail-lbl { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); min-width:110px; padding-top:.1rem; }
.detail-val { font-size:.88rem; color:var(--ink); font-weight:500; flex:1; }

.pat-banner {
    background:linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06));
    border-radius:.65rem; padding:1rem 1.1rem; border:1px solid rgba(64,81,137,.1);
    display:flex; align-items:center; gap:.85rem; margin-bottom:1.1rem;
}
.pat-banner-av {
    width:44px; height:44px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.9rem; font-weight:800; color:#fff;
    background:linear-gradient(135deg,var(--ap),#6b7fe0);
}

/* ── Notes textarea ── */
.notes-ta {
    width:100%; border:1.5px solid var(--border); border-radius:.55rem;
    padding:.65rem .85rem; font-size:.88rem; resize:vertical; min-height:110px;
    outline:none; transition:border-color .18s;
}
.notes-ta:focus { border-color:var(--ap); box-shadow:0 0 0 3px rgba(64,81,137,.1); }

/* ── Toast ── */
#toast-c { position:fixed; top:1.1rem; right:1.1rem; z-index:99999; display:flex; flex-direction:column; gap:.4rem; }
.t-item { min-width:240px; padding:.7rem 1rem; border-radius:.5rem; color:#fff; font-size:.83rem; font-weight:500; display:flex; align-items:center; gap:.5rem; box-shadow:0 4px 20px rgba(0,0,0,.2); animation:tin .25s ease; }
@keyframes tin { from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:none} }
.t-ok  { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
.t-err { background:linear-gradient(135deg,#f06548,#c0392b); }
.t-inf { background:linear-gradient(135deg,#405189,#2d3e7a); }

/* ── Animations ── */
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
.fa0{animation:fadeUp .38s ease both}
.fa1{animation:fadeUp .38s .06s ease both}
.fa2{animation:fadeUp .38s .12s ease both}
.fa3{animation:fadeUp .38s .18s ease both}

/* ── Today highlight ── */
.today-row { background:rgba(10,179,156,.02); }
.today-row td:first-child { border-left:3px solid var(--at); }
</style>

<div id="toast-c"></div>

{{-- ── Header ── --}}
<div class="row mb-3 align-items-center">
    <div class="col">
        <h4 class="mb-0" style="font-weight:800;letter-spacing:-.02em;">Mis Citas</h4>
        <ol class="breadcrumb mb-0 mt-1" style="font-size:.78rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mis Citas</li>
        </ol>
    </div>
    <div class="col-auto">
        <div style="background:rgba(64,81,137,.07);border-radius:2rem;padding:.4rem 1rem;font-size:.8rem;font-weight:700;color:var(--ap);">
            <i class="ri-user-star-line me-1"></i>{{ auth()->user()->name }}
            <span style="opacity:.6;font-weight:400;margin-left:.3rem;">· {{ auth()->user()->branch->name ?? '' }}</span>
        </div>
    </div>
</div>

{{-- ── Stat cards ── --}}
<div class="row g-4 mb-1">
    <div class="col-xl-3 col-md-6 fa0">
        <div class="sc c-t">
            <div class="sc-ico"><i class="ri-calendar-check-line"></i></div>
            <div class="sc-val">{{ $todayCount }}</div>
            <div class="sc-lbl">Citas hoy</div>
            <div class="sc-ghost"><i class="ri-calendar-check-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fa1">
        <div class="sc c-p">
            <div class="sc-ico"><i class="ri-calendar-2-line"></i></div>
            <div class="sc-val">{{ $totalMonth }}</div>
            <div class="sc-lbl">Total este mes</div>
            <div class="sc-ghost"><i class="ri-calendar-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fa2">
        <div class="sc c-a">
            <div class="sc-ico"><i class="ri-time-line"></i></div>
            <div class="sc-val">{{ $pendingMonth }}</div>
            <div class="sc-lbl">Programadas</div>
            <div class="sc-ghost"><i class="ri-time-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fa3">
        <div class="sc c-r" style="border-color:var(--at);">
            <div class="sc-ico" style="background:rgba(10,179,156,.1);color:var(--at);"><i class="ri-checkbox-circle-line"></i></div>
            <div class="sc-val">{{ $completedMonth }}</div>
            <div class="sc-lbl">Completadas</div>
            <div class="sc-ghost"><i class="ri-checkbox-circle-fill"></i></div>
        </div>
    </div>
</div>

{{-- ── Main card ── --}}
<div class="main-card fa1">

    {{-- Tabs --}}
    <div class="ap-tabs">
        <a href="{{ route('audiologist.appointments.index', ['tab'=>'today',    'q'=>request('q')]) }}"
           class="ap-tab {{ $tab==='today'    ? 'active' : '' }}">
            <i class="ri-sun-line"></i>Hoy
            @if($todayCount > 0)<span class="tab-badge">{{ $todayCount }}</span>@endif
        </a>
        <a href="{{ route('audiologist.appointments.index', ['tab'=>'upcoming', 'q'=>request('q')]) }}"
           class="ap-tab {{ $tab==='upcoming' ? 'active' : '' }}">
            <i class="ri-calendar-line"></i>Próximas
        </a>
        <a href="{{ route('audiologist.appointments.index', ['tab'=>'history',  'q'=>request('q')]) }}"
           class="ap-tab {{ $tab==='history'  ? 'active' : '' }}">
            <i class="ri-history-line"></i>Historial
        </a>
    </div>

    {{-- Filters --}}
    <div class="p-3 border-bottom" style="border-color:var(--border)!important;background:var(--surface);">
        <form method="GET" action="{{ route('audiologist.appointments.index') }}" id="filter-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="search-bar">
                        <i class="ri-search-line"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Buscar paciente por nombre o cédula…"
                               autocomplete="off">
                    </div>
                </div>
                @if($tab === 'history')
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm"
                            style="border-radius:2rem;border:1.5px solid var(--border);font-size:.83rem;"
                            onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="completada" {{ request('status')==='completada' ? 'selected':'' }}>Completadas</option>
                        <option value="cancelada"  {{ request('status')==='cancelada'  ? 'selected':'' }}>Canceladas</option>
                        <option value="programada" {{ request('status')==='programada' ? 'selected':'' }}>Programadas</option>
                    </select>
                </div>
                @endif
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm" style="border-radius:2rem;font-size:.8rem;">
                        <i class="ri-search-line me-1"></i>Buscar
                    </button>
                    @if(request('q') || request('status'))
                        <a href="{{ route('audiologist.appointments.index', ['tab'=>$tab]) }}"
                           class="btn btn-light btn-sm ms-1" style="border-radius:2rem;font-size:.8rem;">
                            <i class="ri-close-line"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="ap-table">
            <thead>
                <tr>
                    @if($tab !== 'today')<th>Fecha</th>@endif
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Seguro</th>
                    <th>Notas</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $ap)
                    @php
                        $fn  = $ap->patient->first_name ?? '';
                        $ln  = $ap->patient->last_name  ?? '';
                        $ini = strtoupper(substr($fn,0,1).substr($ln,0,1));
                        $ci  = $ap->id % 6;
                        $stc = match($ap->status) { 'completada'=>'st-comp','cancelada'=>'st-canc',default=>'st-prog' };
                        $isToday = \Carbon\Carbon::parse($ap->appointment_date)->isToday();
                    @endphp
                    <tr class="{{ $isToday && $tab !== 'today' ? 'today-row' : '' }}">
                        @if($tab !== 'today')
                        <td>
                            <div style="font-size:.83rem;font-weight:700;color:var(--ink);">
                                {{ \Carbon\Carbon::parse($ap->appointment_date)->format('d/m/Y') }}
                            </div>
                            <div style="font-size:.7rem;color:var(--muted);">
                                {{ \Carbon\Carbon::parse($ap->appointment_date)->locale('es')->isoFormat('dddd') }}
                            </div>
                            @if($isToday)
                                <span style="font-size:.65rem;font-weight:800;color:var(--at);text-transform:uppercase;">Hoy</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <div class="ap-time">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('g:i') }}</div>
                            <div class="ap-date">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('A') }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="pat-av av{{ $ci }}">{{ $ini ?: '?' }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:.87rem;">{{ $fn }} {{ $ln }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">{{ $ap->patient->cedula ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($ap->patient->insurance)
                                <span class="ins-pill">
                                    <i class="ri-shield-check-line me-1"></i>{{ $ap->patient->insurance->name }}
                                </span>
                            @else
                                <span style="color:var(--border);font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($ap->notes)
                                <span class="notes-prev" title="{{ $ap->notes }}">{{ $ap->notes }}</span>
                            @else
                                <span class="notes-empty">Sin notas</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="st-badge {{ $stc }}">{{ ucfirst($ap->status) }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                {{-- Ver paciente --}}
                                <button type="button" class="btn-action ba-view"
                                        onclick="openDetail({{ $ap->id }})"
                                        title="Ver detalle">
                                    <i class="ri-eye-line"></i>
                                </button>
                                {{-- Notas --}}
                                <button type="button" class="btn-action ba-note"
                                        onclick="openNotes({{ $ap->id }}, '{{ addslashes($ap->notes ?? '') }}')"
                                        title="Editar notas">
                                    <i class="ri-sticky-note-line"></i>
                                </button>
                                {{-- Completar (solo si programada) --}}
                                @if($ap->status === 'programada')
                                <button type="button" class="btn-action ba-complete"
                                        onclick="changeStatus({{ $ap->id }}, 'completada', this)"
                                        title="Marcar completada">
                                    <i class="ri-checkbox-circle-line"></i>
                                </button>
                                <button type="button" class="btn-action ba-cancel"
                                        onclick="changeStatus({{ $ap->id }}, 'cancelada', this)"
                                        title="Cancelar cita">
                                    <i class="ri-close-circle-line"></i>
                                </button>
                                @endif
                                {{-- Reactivar (solo si cancelada) --}}
                                @if($ap->status === 'cancelada')
                                <button type="button" class="btn-action ba-undo"
                                        onclick="changeStatus({{ $ap->id }}, 'programada', this)"
                                        title="Reactivar cita">
                                    <i class="ri-restart-line"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            @if($tab === 'today')
                                <div class="today-empty">
                                    <i class="ri-calendar-check-line"></i>
                                    <div style="font-size:.95rem;font-weight:700;color:var(--ink);margin-bottom:.25rem;">
                                        No tienes citas programadas para hoy
                                    </div>
                                    <div style="font-size:.82rem;color:var(--muted);">
                                        Revisa la pestaña <strong>Próximas</strong> para ver tus próximas citas.
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="ri-calendar-line"></i>
                                    <p>
                                        @if(request('q'))
                                            No se encontraron citas para "{{ request('q') }}"
                                        @elseif($tab === 'upcoming')
                                            No tienes citas próximas programadas.
                                        @else
                                            Sin registros en el historial.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($appointments->hasPages())
    <div class="p-3 border-top d-flex justify-content-center" style="border-color:var(--border)!important;">
        {{ $appointments->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

</div>
</div>

{{-- ════════════════════════
     MODAL DETALLE
════════════════════════ --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-ap py-3 px-4 text-white">
                <div>
                    <h5 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="ri-user-heart-line"></i>Detalle de cita
                    </h5>
                    <div style="font-size:.77rem;opacity:.8;margin-top:.1rem;" id="d-appt-ref">—</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="d-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" style="width:2rem;height:2rem;"></div>
                </div>
                <div id="d-body" style="display:none;">
                    {{-- Patient banner --}}
                    <div class="pat-banner">
                        <div class="pat-banner-av" id="d-av">?</div>
                        <div>
                            <div style="font-weight:800;font-size:1rem;color:var(--ink);" id="d-name">—</div>
                            <div style="font-size:.78rem;color:var(--muted);" id="d-cedula">—</div>
                        </div>
                        <div class="ms-auto text-end">
                            <span class="st-badge" id="d-status-badge">—</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:var(--muted);margin-bottom:.6rem;">
                                <i class="ri-calendar-event-line me-1"></i>Datos de la cita
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Fecha</span>
                                <span class="detail-val" id="d-date">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Hora</span>
                                <span class="detail-val" id="d-time">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Sucursal</span>
                                <span class="detail-val" id="d-branch">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Notas</span>
                                <span class="detail-val" id="d-notes" style="font-style:italic;color:var(--muted);">Sin notas</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.09em;color:var(--muted);margin-bottom:.6rem;">
                                <i class="ri-user-line me-1"></i>Datos del paciente
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Teléfono</span>
                                <span class="detail-val" id="d-phone">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Email</span>
                                <span class="detail-val" id="d-email">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Nacimiento</span>
                                <span class="detail-val" id="d-birth">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Género</span>
                                <span class="detail-val" id="d-gender">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Seguro</span>
                                <span class="detail-val" id="d-insurance">—</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-lbl">Dirección</span>
                                <span class="detail-val" id="d-address">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"
                        style="border-radius:2rem;font-size:.83rem;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════
     MODAL NOTAS
════════════════════════ --}}
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-note py-3 px-4 text-white">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-sticky-note-line"></i>Notas de la cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="n-appt-id">
                <label style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:.5rem;">
                    Observaciones / Notas clínicas
                </label>
                <textarea id="n-text" class="notes-ta"
                          placeholder="Escribe aquí tus observaciones sobre esta cita…"></textarea>
                <div style="font-size:.73rem;color:var(--muted);margin-top:.35rem;">Máx. 1000 caracteres</div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"
                        style="border-radius:2rem;font-size:.83rem;">Cancelar</button>
                <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-2"
                        id="btn-save-notes"
                        style="border-radius:2rem;font-size:.83rem;background:linear-gradient(135deg,var(--aa),#e8993a);border:none;color:#fff;"
                        onclick="saveNotes()">
                    <span class="spinner-border spinner-border-sm d-none" id="notes-spin"></span>
                    <i class="ri-save-line"></i>Guardar notas
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
const BASE_URL  = '{{ url("audiologist/appointments") }}';

/* ── Open detail modal ─────────────────────────────── */
async function openDetail(id) {
    document.getElementById('d-loading').style.display = '';
    document.getElementById('d-body').style.display    = 'none';
    document.getElementById('d-appt-ref').textContent  = '—';
    new bootstrap.Modal(document.getElementById('detailModal')).show();

    try {
        const r    = await fetch(`${BASE_URL}/${id}/show-data`, {
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await r.json();
        if (!r.ok) throw new Error('Error al cargar');

        const ap = data.appointment, p = data.patient;

        document.getElementById('d-appt-ref').textContent   = `${ap.date} · ${ap.time}`;
        document.getElementById('d-av').textContent          =
            p.full_name.split(' ').map(w=>w[0]||'').slice(0,2).join('').toUpperCase();
        document.getElementById('d-name').textContent        = p.full_name;
        document.getElementById('d-cedula').textContent      = 'Cédula: ' + p.cedula;
        document.getElementById('d-date').textContent        = ap.date;
        document.getElementById('d-time').textContent        = ap.time;
        document.getElementById('d-branch').textContent      = ap.branch;
        document.getElementById('d-notes').textContent       = ap.notes || 'Sin notas';
        document.getElementById('d-notes').style.fontStyle   = ap.notes ? 'normal' : 'italic';
        document.getElementById('d-phone').textContent       = p.phone    || '—';
        document.getElementById('d-email').textContent       = p.email    || '—';
        document.getElementById('d-birth').textContent       = p.birth_date || '—';
        document.getElementById('d-gender').textContent      = p.gender ? ucFirst(p.gender) : '—';
        document.getElementById('d-address').textContent     = p.address  || '—';

        // Insurance
        const insEl = document.getElementById('d-insurance');
        if (p.insurance_name) {
            insEl.innerHTML = `<span class="ins-pill"><i class="ri-shield-check-line me-1"></i>${p.insurance_name}</span>`;
            if (p.insurance_number) insEl.innerHTML += ` <span style="font-size:.74rem;color:var(--muted);">Nº ${p.insurance_number}</span>`;
        } else {
            insEl.textContent = 'Sin seguro';
        }

        // Status badge
        const sc = { programada:'st-prog', completada:'st-comp', cancelada:'st-canc' };
        const sb = document.getElementById('d-status-badge');
        sb.className = `st-badge ${sc[ap.status] || 'st-prog'}`;
        sb.textContent = ucFirst(ap.status);

        document.getElementById('d-loading').style.display = 'none';
        document.getElementById('d-body').style.display    = '';
    } catch(e) {
        showToast('Error al cargar el detalle.', 'err');
        bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();
    }
}

/* ── Open notes modal ──────────────────────────────── */
function openNotes(id, currentNotes) {
    document.getElementById('n-appt-id').value = id;
    document.getElementById('n-text').value    = currentNotes;
    new bootstrap.Modal(document.getElementById('notesModal')).show();
}

/* ── Save notes ────────────────────────────────────── */
async function saveNotes() {
    const id    = document.getElementById('n-appt-id').value;
    const notes = document.getElementById('n-text').value;
    const btn   = document.getElementById('btn-save-notes');
    const spin  = document.getElementById('notes-spin');

    btn.disabled = true;
    spin.classList.remove('d-none');

    try {
        const r = await fetch(`${BASE_URL}/${id}/notes`, {
            method: 'PATCH',
            headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ notes })
        });
        if (!r.ok) throw new Error();

        bootstrap.Modal.getInstance(document.getElementById('notesModal')).hide();
        showToast('Notas guardadas correctamente.', 'ok');

        // Update preview in table without reload
        document.querySelectorAll('[onclick*="openNotes(' + id + '"]').forEach(el => {
            const row  = el.closest('tr');
            const cell = row?.querySelector('.notes-prev, .notes-empty');
            if (cell) {
                if (notes.trim()) {
                    cell.className   = 'notes-prev';
                    cell.textContent = notes;
                    cell.title       = notes;
                } else {
                    cell.className   = 'notes-empty';
                    cell.textContent = 'Sin notas';
                }
            }
            // Update the onclick attribute with new notes
            el.setAttribute('onclick', `openNotes(${id}, '${notes.replace(/'/g,"\\'")}' )`);
        });
    } catch {
        showToast('Error al guardar las notas.', 'err');
    } finally {
        btn.disabled = false;
        spin.classList.add('d-none');
    }
}

/* ── Change status ─────────────────────────────────── */
async function changeStatus(id, status, btn) {
    const labels = { completada:'completada', cancelada:'cancelada', programada:'reactivada' };
    if (!confirm(`¿Marcar esta cita como ${labels[status]}?`)) return;

    btn.disabled = true;

    try {
        const r = await fetch(`${BASE_URL}/${id}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ status })
        });
        if (!r.ok) throw new Error();

        showToast(`Cita ${labels[status]} correctamente.`, 'ok');
        setTimeout(() => location.reload(), 900);
    } catch {
        showToast('Error al actualizar el estado.', 'err');
        btn.disabled = false;
    }
}

/* ── Helpers ───────────────────────────────────────── */
function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }

function showToast(msg, type) {
    const d = document.createElement('div');
    d.className = `t-item t-${type||'ok'}`;
    d.innerHTML = `<i class="ri-${type==='err'?'error-warning':'checkbox-circle'}-line"></i>${msg}`;
    document.getElementById('toast-c').appendChild(d);
    setTimeout(() => { d.style.transition='opacity .3s'; d.style.opacity='0'; setTimeout(()=>d.remove(),320); }, 3500);
}

@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast('{{ session('success') }}', 'ok'));
@endif
</script>
@endpush

</x-app-layout>