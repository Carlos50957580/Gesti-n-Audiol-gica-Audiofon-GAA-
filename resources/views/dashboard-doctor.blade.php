<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
:root {
    --dp: #405189; --dt: #0ab39c; --da: #f7b84b;
    --dr: #f06548; --dv: #7066e0; --db: #299cdb;
    --ink: #1e2535; --muted: #6b7a99;
    --border: #edf0f7; --surface: #f8faff;
    --radius: .85rem;
}

.dc {
    background:#fff; border:none; border-radius:var(--radius);
    box-shadow:0 2px 20px rgba(64,81,137,.08); overflow:hidden;
    margin-bottom:1.25rem; transition:box-shadow .22s, transform .22s;
}
.dc:hover { box-shadow:0 8px 32px rgba(64,81,137,.13); }
.dc-head {
    display:flex; align-items:center; gap:.65rem;
    padding:.9rem 1.25rem; border-bottom:1px solid var(--border);
}
.dc-head h6 { margin:0; font-size:.87rem; font-weight:700; color:var(--ink); flex-grow:1; }
.dc-head .see-all { font-size:.73rem; font-weight:600; color:var(--dp); text-decoration:none; opacity:.8; }
.dc-head .see-all:hover { opacity:1; }
.dc-body { padding:1.25rem; }
.dc-ico {
    width:30px; height:30px; border-radius:.4rem; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:.95rem;
}

/* ── KPI cards ── */
.kpi {
    background:#fff; border-radius:var(--radius);
    box-shadow:0 2px 20px rgba(64,81,137,.08);
    padding:1.3rem; position:relative; overflow:hidden;
    margin-bottom:1.25rem; transition:transform .2s, box-shadow .2s;
    border-left:4px solid transparent;
}
.kpi:hover { transform:translateY(-3px); box-shadow:0 10px 32px rgba(64,81,137,.15); }
.kpi.kp-t { border-color:var(--dt); }
.kpi.kp-p { border-color:var(--dp); }
.kpi.kp-a { border-color:var(--da); }
.kpi.kp-r { border-color:var(--dr); }
.kpi.kp-v { border-color:var(--dv); }
.kpi.kp-b { border-color:var(--db); }

.kpi-ico {
    width:44px; height:44px; border-radius:.6rem; font-size:1.25rem;
    display:flex; align-items:center; justify-content:center; margin-bottom:.85rem;
}
.kp-t .kpi-ico { background:rgba(10,179,156,.1);  color:var(--dt); }
.kp-p .kpi-ico { background:rgba(64,81,137,.1);   color:var(--dp); }
.kp-a .kpi-ico { background:rgba(247,184,75,.12); color:var(--da); }
.kp-r .kpi-ico { background:rgba(240,101,72,.1);  color:var(--dr); }
.kp-v .kpi-ico { background:rgba(112,102,224,.1); color:var(--dv); }
.kp-b .kpi-ico { background:rgba(41,156,219,.1);  color:var(--db); }

.kpi-val   { font-size:1.65rem; font-weight:800; color:var(--ink); letter-spacing:-.04em; line-height:1; }
.kpi-lbl   { font-size:.74rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin:.25rem 0 .5rem; }
.kpi-foot  { font-size:.76rem; color:var(--muted); display:flex; align-items:center; gap:.3rem; }
.up        { color:var(--dt); font-weight:700; }
.down      { color:var(--dr); font-weight:700; }
.kpi-ghost { position:absolute; right:-8px; bottom:-8px; font-size:4.5rem; opacity:.04; line-height:1; }

/* ── Role banner ── */
.role-banner {
    border-radius:var(--radius); padding:1.25rem 1.5rem;
    display:flex; align-items:center; gap:1rem;
    margin-bottom:1.25rem; position:relative; overflow:hidden;
}
.role-banner.doctor-banner {
    background:linear-gradient(135deg,#0ab39c 0%,#2d7a6e 50%,#405189 100%);
    color:#fff;
}
.role-banner h5 { margin:0; font-size:1.1rem; font-weight:800; }
.role-banner p  { margin:0; font-size:.82rem; opacity:.82; }
.role-banner-ico {
    width:54px; height:54px; border-radius:.8rem; flex-shrink:0;
    background:rgba(255,255,255,.15); backdrop-filter:blur(8px);
    display:flex; align-items:center; justify-content:center; font-size:1.6rem;
}
.banner-deco {
    position:absolute; right:-20px; top:-20px; width:140px; height:140px;
    border-radius:50%; background:rgba(255,255,255,.06); pointer-events:none;
}
.banner-deco2 {
    position:absolute; right:40px; bottom:-40px; width:100px; height:100px;
    border-radius:50%; background:rgba(255,255,255,.04); pointer-events:none;
}

/* ── Agenda ── */
.ag-item {
    display:flex; align-items:center; gap:.8rem;
    padding:.65rem .15rem; border-bottom:1px solid var(--border);
    transition:background .12s, padding-left .12s; border-radius:.35rem;
}
.ag-item:last-child { border-bottom:none; }
.ag-item:hover { background:var(--surface); padding-left:.4rem; }
.ag-time { font-size:.76rem; font-weight:800; color:var(--dp); min-width:40px; text-align:center; font-variant-numeric:tabular-nums; }
.ag-av {
    width:32px; height:32px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.69rem; font-weight:800; color:#fff;
}
.ag-name { font-size:.83rem; font-weight:600; color:var(--ink); }
.ag-doc  { font-size:.72rem; color:var(--muted); }
.ag-st   { margin-left:auto; font-size:.67rem; font-weight:700; padding:.18rem .6rem; border-radius:2rem; white-space:nowrap; }
.ag-prog { background:rgba(64,81,137,.1);  color:var(--dp); }
.ag-comp { background:rgba(10,179,156,.1);  color:var(--dt); }
.ag-canc { background:rgba(240,101,72,.1);  color:var(--dr); }

/* ── Pacientes recientes ── */
.pat-row {
    display:flex; align-items:center; gap:.85rem;
    padding:.62rem .15rem; border-bottom:1px solid var(--border);
    text-decoration:none; transition:background .12s, padding-left .12s; border-radius:.35rem;
}
.pat-row:last-child { border-bottom:none; }
.pat-row:hover { background:var(--surface); padding-left:.4rem; }
.pat-name  { font-size:.83rem; font-weight:600; color:var(--ink); }
.pat-info  { font-size:.72rem; color:var(--muted); }

/* ── Service bars ── */
.sb-row { margin-bottom:.85rem; }
.sb-row:last-child { margin-bottom:0; }
.sb-top { display:flex; justify-content:space-between; margin-bottom:.28rem; }
.sb-name { font-size:.82rem; font-weight:600; color:var(--ink); }
.sb-val  { font-size:.78rem; font-weight:700; color:var(--dp); }
.sb-track { height:6px; background:var(--border); border-radius:99px; overflow:hidden; }
.sb-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--dp),var(--dt)); transition:width .9s cubic-bezier(.23,1,.32,1); }
.sb-qty { font-size:.7rem; color:var(--muted); margin-top:.12rem; }

/* ── Donut ── */
.donut-wrap { display:flex; align-items:center; gap:1.2rem; }
.donut-cvs  { width:100px; height:100px; flex-shrink:0; }
.leg-item   { display:flex; align-items:center; gap:.45rem; margin-bottom:.4rem; font-size:.79rem; }
.leg-dot    { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.leg-lbl    { color:var(--muted); flex:1; }
.leg-val    { font-weight:700; color:var(--ink); }

/* ── Avatars ── */
.av0{background:linear-gradient(135deg,#405189,#6b7fe0)}
.av1{background:linear-gradient(135deg,#0ab39c,#2dcfb5)}
.av2{background:linear-gradient(135deg,#f7b84b,#e8993a)}
.av3{background:linear-gradient(135deg,#f06548,#d44f36)}
.av4{background:linear-gradient(135deg,#7066e0,#9b8ff0)}
.av5{background:linear-gradient(135deg,#299cdb,#1a7fb0)}

/* ── Animations ── */
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
.fa0{animation:fadeUp .4s ease both}
.fa1{animation:fadeUp .4s .06s ease both}
.fa2{animation:fadeUp .4s .12s ease both}
.fa3{animation:fadeUp .4s .18s ease both}
.fa4{animation:fadeUp .4s .24s ease both}

/* ── Chart container ── */
.chart-h160 { position:relative; height:165px; }
.chart-h200 { position:relative; height:200px; }
</style>

{{-- ══════════════════════════════════
     HEADER
══════════════════════════════════ --}}
<div class="row mb-3 align-items-center">
    <div class="col">
        <h4 class="mb-0" style="font-weight:800;letter-spacing:-.02em;">
            <i class="ri-stethoscope-line me-2"></i>Mis Citas
        </h4>
        <p class="mb-0 text-muted" style="font-size:.8rem;">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--dt);margin-right:.5rem;"></span>
            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </p>
    </div>
</div>

{{-- ══════════════════════════════════
     ROLE BANNER
══════════════════════════════════ --}}
<div class="role-banner doctor-banner fa0">
    <div class="banner-deco"></div>
    <div class="banner-deco2"></div>
    <div class="role-banner-ico"><i class="ri-user-heart-line"></i></div>
    <div>
        <h5>Bienvenido, Dr. {{ $user->name }}</h5>
        <p>{{ $user->branch->name ?? 'Mi Sucursal' }} · <span style="background:rgba(255,255,255,.2);border-radius:2rem;padding:.1rem .55rem;font-size:.75rem;font-weight:700;">{{ $apptToday }} citas hoy</span></p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <span style="background:rgba(255,255,255,.15);border-radius:2rem;padding:.25rem .75rem;font-size:.78rem;font-weight:700;">
            <i class="ri-file-history-line me-1"></i>{{ $clinicalRecords }} H.C.
        </span>
        <span style="background:rgba(255,255,255,.15);border-radius:2rem;padding:.25rem .75rem;font-size:.78rem;font-weight:700;">
            <i class="ri-user-line me-1"></i>{{ $patientsAttended }} pacientes
        </span>
    </div>
</div>

{{-- ══════════════════════════════════
     KPI FILA (solo datos del médico)
══════════════════════════════════ --}}
<div class="row g-3">
    <div class="col-xl-3 col-md-6 fa0">
        <div class="kpi kp-p">
            <div class="kpi-ico"><i class="ri-calendar-check-line"></i></div>
            <div class="kpi-val">{{ $apptToday }}</div>
            <div class="kpi-lbl">Citas hoy</div>
            <div class="kpi-foot">
                <i class="ri-time-line"></i>
                {{ $apptPending }} pendientes
            </div>
            <div class="kpi-ghost"><i class="ri-calendar-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fa1">
        <div class="kpi kp-t">
            <div class="kpi-ico"><i class="ri-calendar-event-line"></i></div>
            <div class="kpi-val">{{ $apptThisMonth }}</div>
            <div class="kpi-lbl">Citas este mes</div>
            <div class="kpi-foot">
                <i class="ri-check-double-line"></i>
                {{ $apptCompleted }} completadas
            </div>
            <div class="kpi-ghost"><i class="ri-calendar-event-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fa2">
        <div class="kpi kp-v">
            <div class="kpi-ico"><i class="ri-user-line"></i></div>
            <div class="kpi-val">{{ $patientsAttended }}</div>
            <div class="kpi-lbl">Pacientes atendidos</div>
            <div class="kpi-foot">
                <i class="ri-user-add-line"></i>
                +{{ $newPatientsMonth }} este mes
            </div>
            <div class="kpi-ghost"><i class="ri-user-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 fa3">
        <div class="kpi kp-b">
            <div class="kpi-ico"><i class="ri-file-history-line"></i></div>
            <div class="kpi-val">{{ $clinicalRecords }}</div>
            <div class="kpi-lbl">Historias Clínicas</div>
            <div class="kpi-foot">
                <i class="ri-file-add-line"></i>
                +{{ $clinicalRecordsMonth }} este mes
            </div>
            <div class="kpi-ghost"><i class="ri-file-history-fill"></i></div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     ROW 2: Gráfico + Agenda de hoy
══════════════════════════════════ --}}
<div class="row g-3">
    <div class="col-xl-8 fa0">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(64,81,137,.1);color:var(--dp)"><i class="ri-bar-chart-2-line"></i></div>
                <h6>Citas completadas — últimos 7 días</h6>
                <span style="font-size:.74rem;color:var(--muted)">Pacientes atendidos</span>
            </div>
            <div class="dc-body">
                <div class="chart-h160"><canvas id="apptChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(10,179,156,.1);color:var(--dt)"><i class="ri-calendar-event-line"></i></div>
                <h6>Agenda de hoy</h6>
                <a href="{{ route('doctor.appointments.index') }}" class="see-all">Ver todas →</a>
            </div>
            <div class="dc-body" style="padding-top:.35rem;">
                @forelse($todayAppointments as $ap)
                    @php
                        $fn  = $ap->patient->first_name ?? '';
                        $ln  = $ap->patient->last_name  ?? '';
                        $ini = strtoupper(substr($fn,0,1).substr($ln,0,1));
                        $ci  = $loop->index % 6;
                        $stc = match($ap->status) { 'completada'=>'ag-comp','cancelada'=>'ag-canc',default=>'ag-prog' };
                    @endphp
                    <div class="ag-item">
                        <div class="ag-time">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('g:i A') }}</div>
                        <div class="ag-av av{{ $ci }}">{{ $ini ?: '?' }}</div>
                        <div>
                            <div class="ag-name">{{ $fn }} {{ $ln }}</div>
                            <div class="ag-doc">{{ $ap->branch->name ?? '—' }}</div>
                        </div>
                        <span class="ag-st {{ $stc }}">{{ ucfirst($ap->status) }}</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:2rem 0;color:var(--muted);font-size:.83rem;">
                        <i class="ri-calendar-check-line" style="font-size:2rem;opacity:.25;display:block;margin-bottom:.5rem;"></i>
                        No tienes citas para hoy
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     ROW 3: Servicios + Donut citas + Próximas citas
══════════════════════════════════ --}}
<div class="row g-3">

    {{-- Top servicios que atiende --}}
    <div class="col-xl-4 fa0">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(10,179,156,.1);color:var(--dt)"><i class="ri-stethoscope-line"></i></div>
                <h6>Servicios más atendidos</h6>
            </div>
            <div class="dc-body">
                @forelse($topServices as $svc)
                    @php $pct = $maxServices > 0 ? ($svc->total / $maxServices * 100) : 0; @endphp
                    <div class="sb-row">
                        <div class="sb-top">
                            <span class="sb-name">{{ $svc->name }}</span>
                            <span class="sb-val">{{ $svc->total }}</span>
                        </div>
                        <div class="sb-track"><div class="sb-fill" style="width:{{ round($pct) }}%"></div></div>
                    </div>
                @empty
                    <div style="text-align:center;color:var(--muted);padding:1.5rem 0;font-size:.83rem;">
                        <i class="ri-bar-chart-line" style="font-size:1.8rem;opacity:.25;display:block;margin-bottom:.4rem;"></i>
                        Sin datos este mes
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Donut citas --}}
    <div class="col-xl-4 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(112,102,224,.1);color:var(--dv)"><i class="ri-pie-chart-2-line"></i></div>
                <h6>Estado de citas — este mes</h6>
                <span style="font-size:.8rem;font-weight:700;color:var(--ink);">{{ $apptThisMonth }}</span>
            </div>
            <div class="dc-body">
                <div class="donut-wrap mb-3">
                    <div class="donut-cvs"><canvas id="apptDonut"></canvas></div>
                    <div style="flex:1;">
                        @foreach([
                            ['programada','Programadas','#405189'],
                            ['completada','Completadas','#0ab39c'],
                            ['cancelada', 'Canceladas', '#f06548'],
                        ] as [$key,$lbl,$col])
                        <div class="leg-item">
                            <div class="leg-dot" style="background:{{ $col }}"></div>
                            <span class="leg-lbl">{{ $lbl }}</span>
                            <span class="leg-val">{{ $apptByStatus[$key] ?? 0 }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="mini-stat" style="background:rgba(10,179,156,.06);border-radius:.6rem;padding:.75rem .9rem;text-align:center;">
                            <div class="mini-val" style="font-size:1.3rem;font-weight:800;color:var(--dt);">{{ $apptCompleted }}</div>
                            <div class="mini-lbl" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);">Completadas</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini-stat" style="background:rgba(247,184,75,.08);border-radius:.6rem;padding:.75rem .9rem;text-align:center;">
                            <div class="mini-val" style="font-size:1.3rem;font-weight:800;color:var(--da);">{{ $apptPending }}</div>
                            <div class="mini-lbl" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);">Pendientes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Próximas citas --}}
    <div class="col-xl-4 fa2">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(247,184,75,.12);color:var(--da)"><i class="ri-calendar-2-line"></i></div>
                <h6>Próximas citas</h6>
                <a href="{{ route('doctor.appointments.index') }}" class="see-all">Ver todas →</a>
            </div>
            <div class="dc-body" style="padding-top:.35rem;">
                @forelse($upcomingAppointments as $ap)
                    @php
                        $fn  = $ap->patient->first_name ?? '';
                        $ln  = $ap->patient->last_name  ?? '';
                        $ini = strtoupper(substr($fn,0,1).substr($ln,0,1));
                        $ci  = $loop->index % 6;
                    @endphp
                    <div class="ag-item">
                        <div class="ag-time" style="min-width:55px;">
                            {{ \Carbon\Carbon::parse($ap->appointment_date)->format('d/m') }}
                            <br>
                            <span style="font-size:.62rem;color:var(--muted);font-weight:400;">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('g:i A') }}</span>
                        </div>
                        <div class="ag-av av{{ $ci }}">{{ $ini ?: '?' }}</div>
                        <div>
                            <div class="ag-name">{{ $fn }} {{ $ln }}</div>
                            <div class="ag-doc">{{ $ap->branch->name ?? '—' }}</div>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:2rem 0;color:var(--muted);font-size:.83rem;">
                        <i class="ri-calendar-2-line" style="font-size:2rem;opacity:.25;display:block;margin-bottom:.5rem;"></i>
                        No hay próximas citas
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     ROW 4: Pacientes recientes + Accesos rápidos
══════════════════════════════════ --}}
<div class="row g-3">

    {{-- Pacientes recientes --}}
    <div class="col-xl-8 fa0">
        <div class="dc">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(64,81,137,.1);color:var(--dp)"><i class="ri-user-line"></i></div>
                <h6>Pacientes atendidos recientemente</h6>
                <a href="{{ route('patients.index') }}" class="see-all">Ver todos →</a>
            </div>
            <div class="dc-body" style="padding-top:.3rem;">
                @forelse($recentPatients as $ap)
                    <a href="{{ route('patients.show', $ap->patient_id) }}" class="pat-row">
                        @php
                            $fn  = $ap->patient->first_name ?? '';
                            $ln  = $ap->patient->last_name  ?? '';
                            $ini = strtoupper(substr($fn,0,1).substr($ln,0,1));
                            $ci  = $loop->index % 6;
                        @endphp
                        <div class="ag-av av{{ $ci }}">{{ $ini ?: '?' }}</div>
                        <div>
                            <div class="pat-name">{{ $fn }} {{ $ln }}</div>
                            <div class="pat-info">Cédula: {{ $ap->patient->cedula ?? 'N/A' }} · {{ $ap->branch->name ?? '—' }}</div>
                        </div>
                        <div style="margin-left:auto;font-size:.75rem;color:var(--muted);">
                            {{ $ap->updated_at->diffForHumans() }}
                        </div>
                    </a>
                @empty
                    <div style="text-align:center;color:var(--muted);font-size:.83rem;padding:2rem 0;">
                        <i class="ri-user-line" style="font-size:1.8rem;opacity:.25;display:block;margin-bottom:.4rem;"></i>
                        Aún no has atendido pacientes
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Accesos rápidos para médico --}}
    <div class="col-xl-4 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(10,179,156,.1);color:var(--dt)"><i class="ri-rocket-line"></i></div>
                <h6>Acciones rápidas</h6>
            </div>
            <div class="dc-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('doctor.appointments.index') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;transition:background .2s;">
                            <i class="ri-calendar-check-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Mis Citas</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('clinical-records.create') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;transition:background .2s;">
                            <i class="ri-file-add-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Nueva H.C.</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('patients.index') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;transition:background .2s;">
                            <i class="ri-user-search-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Buscar Paciente</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('clinical-records.index') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;transition:background .2s;">
                            <i class="ri-file-history-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Mis H.C.</span>
                        </a>
                    </div>
                </div>

                <hr style="margin:1rem 0;border-color:var(--border);">

                <div class="row g-2">
                    <div class="col-6">
                        <div class="mini-stat" style="background:var(--surface);border-radius:.6rem;padding:.75rem .9rem;text-align:center;">
                            <div class="mini-val" style="font-size:1.3rem;font-weight:800;color:var(--dp);">{{ $apptThisMonth }}</div>
                            <div class="mini-lbl" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);">Citas mes</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini-stat" style="background:var(--surface);border-radius:.6rem;padding:.75rem .9rem;text-align:center;">
                            <div class="mini-val" style="font-size:1.3rem;font-weight:800;color:var(--dt);">{{ $apptCompleted }}</div>
                            <div class="mini-lbl" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);">Completadas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="height:1.5rem;"></div>
</div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const CP='#405189', CT='#0ab39c', CA='#f7b84b', CR='#f06548', CBORDER='#edf0f7', CMUTED='#6b7a99';

// ── Citas por día ────────────────────────────────────
const apptData = @json($last7Days);
const apptCtx = document.getElementById('apptChart').getContext('2d');

new Chart(apptCtx, {
    type: 'bar',
    data: {
        labels  : apptData.map(d => d.label),
        datasets: [{
            data           : apptData.map(d => d.total),
            backgroundColor: apptData.map(d => d.total > 0 ? 'rgba(10,179,156,.72)' : 'rgba(10,179,156,.12)'),
            borderColor    : apptData.map(d => d.total > 0 ? CT : 'transparent'),
            borderWidth    : 1.5,
            borderRadius   : 5,
            borderSkipped  : false,
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins: {
            legend: {display:false},
            tooltip: {
                backgroundColor:'#fff', borderColor:CBORDER, borderWidth:1,
                titleColor:'#1e2535', bodyColor:CMUTED, padding:10,
                callbacks: { label: c => c.parsed.y + ' citas' }
            }
        },
        scales: {
            x: { grid:{display:false}, border:{display:false}, ticks:{color:CMUTED, font:{size:10,weight:'600'}} },
            y: {
                grid:{color:CBORDER}, border:{display:false, dash:[3,3]},
                ticks:{ color:CMUTED, font:{size:10}, stepSize:1 }
            }
        }
    }
});

// ── Appointments donut ───────────────────────────────────
const prog = {{ $apptByStatus['programada'] ?? 0 }};
const comp = {{ $apptByStatus['completada'] ?? 0 }};
const canc = {{ $apptByStatus['cancelada']  ?? 0 }};
const tot  = prog + comp + canc;

const doCtx = document.getElementById('apptDonut').getContext('2d');
new Chart(doCtx, {
    type: 'doughnut',
    data: {
        labels  : ['Programadas','Completadas','Canceladas'],
        datasets: [{
            data           : [prog, comp, canc],
            backgroundColor: [CP, CT, CR],
            borderColor    : '#fff',
            borderWidth    : 3,
            hoverOffset    : 4,
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:true, cutout:'70%',
        plugins: {
            legend:{display:false},
            tooltip:{ backgroundColor:'#fff', borderColor:CBORDER, borderWidth:1, titleColor:'#1e2535', bodyColor:CMUTED, padding:8 }
        }
    },
    plugins: [{
        id: 'center',
        afterDraw(chart) {
            const {ctx, chartArea:{width,height,left,top}} = chart;
            const cx = left+width/2, cy = top+height/2;
            ctx.save();
            ctx.textAlign='center'; ctx.textBaseline='middle';
            ctx.fillStyle='#1e2535';
            ctx.font=`800 ${Math.min(width,height)*.22}px sans-serif`;
            ctx.fillText(tot, cx, cy-3);
            ctx.fillStyle=CMUTED;
            ctx.font=`600 ${Math.min(width,height)*.1}px sans-serif`;
            ctx.fillText('citas', cx, cy+Math.min(width,height)*.14);
            ctx.restore();
        }
    }]
});
</script>
@endpush
</x-app-layout>