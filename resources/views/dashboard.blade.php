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

/* ── Cards ── */
.dc {
    background:#fff; border:none; border-radius:var(--radius);
    box-shadow:0 2px 20px rgba(64,81,137,.08); overflow:hidden;
    margin-bottom:0; transition:box-shadow .22s;
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
    height:100%; transition:transform .2s, box-shadow .2s;
    border-left:4px solid transparent;
}
.kpi:hover { transform:translateY(-3px); box-shadow:0 10px 32px rgba(64,81,137,.15); }
.kpi.kp-t { border-color:var(--dt); }
.kpi.kp-p { border-color:var(--dp); }
.kpi.kp-a { border-color:var(--da); }
.kpi.kp-v { border-color:var(--dv); }

.kpi-ico {
    width:44px; height:44px; border-radius:.6rem; font-size:1.25rem;
    display:flex; align-items:center; justify-content:center; margin-bottom:.85rem;
}
.kp-t .kpi-ico { background:rgba(10,179,156,.1);  color:var(--dt); }
.kp-p .kpi-ico { background:rgba(64,81,137,.1);   color:var(--dp); }
.kp-a .kpi-ico { background:rgba(247,184,75,.12); color:var(--da); }
.kp-v .kpi-ico { background:rgba(112,102,224,.1); color:var(--dv); }

.kpi-val   { font-size:1.65rem; font-weight:800; color:var(--ink); letter-spacing:-.04em; line-height:1; }
.kpi-lbl   { font-size:.74rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin:.25rem 0 .5rem; }
.kpi-foot  { font-size:.76rem; color:var(--muted); display:flex; align-items:center; flex-wrap:wrap; gap:.3rem; }
.kpi-ghost { position:absolute; right:-8px; bottom:-8px; font-size:4.5rem; opacity:.04; line-height:1; }

/* ── Role banner ── */
.role-banner {
    border-radius:var(--radius); padding:1.1rem 1.5rem;
    display:flex; align-items:center; gap:1rem;
    position:relative; overflow:hidden; color:#fff;
}
.role-banner.admin-banner  { background:linear-gradient(135deg,#405189 0%,#2d3e7a 50%,#0ab39c 100%); }
.role-banner.branch-banner { background:linear-gradient(135deg,#1a2e5e 0%,#405189 60%,#3d7abf 100%); }
.role-banner.doctor-banner { background:linear-gradient(135deg,#0ab39c 0%,#2d7a6e 50%,#405189 100%); }
.role-banner h5 { margin:0; font-size:1.05rem; font-weight:800; color:#fff; }
.role-banner p  { margin:0; font-size:.82rem; opacity:.85; }
.role-banner-ico {
    width:48px; height:48px; border-radius:.8rem; flex-shrink:0;
    background:rgba(255,255,255,.15);
    display:flex; align-items:center; justify-content:center; font-size:1.5rem;
}
.banner-deco {
    position:absolute; right:-20px; top:-20px; width:140px; height:140px;
    border-radius:50%; background:rgba(255,255,255,.06); pointer-events:none;
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

/* ── Invoice rows ── */
.inv-row {
    display:flex; align-items:center; gap:.85rem;
    padding:.62rem .15rem; border-bottom:1px solid var(--border);
    text-decoration:none; transition:background .12s, padding-left .12s; border-radius:.35rem;
}
.inv-row:last-child { border-bottom:none; }
.inv-row:hover { background:var(--surface); padding-left:.4rem; }
.inv-num  { font-size:.74rem; font-weight:800; color:var(--dp); font-family:monospace; min-width:76px; }
.inv-pat  { font-size:.83rem; font-weight:600; color:var(--ink); }
.inv-br   { font-size:.72rem; color:var(--muted); }
.inv-amt  { margin-left:auto; font-size:.86rem; font-weight:800; color:var(--ink); white-space:nowrap; }
.inv-badge { flex-shrink:0; font-size:.67rem; font-weight:700; padding:.18rem .58rem; border-radius:2rem; }
.ib-pend { background:rgba(247,184,75,.15); color:#c9940a; }
.ib-paga { background:rgba(10,179,156,.12); color:var(--dt); }
.ib-canc { background:rgba(240,101,72,.12); color:var(--dr); }

/* ── Bars (top servicios / productos) ── */
.sb-row { margin-bottom:.85rem; }
.sb-row:last-child { margin-bottom:0; }
.sb-top { display:flex; justify-content:space-between; gap:.5rem; margin-bottom:.28rem; }
.sb-name { font-size:.82rem; font-weight:600; color:var(--ink); min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sb-val  { font-size:.78rem; font-weight:700; color:var(--dp); white-space:nowrap; }
.sb-track { height:6px; background:var(--border); border-radius:99px; overflow:hidden; }
.sb-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--dp),var(--dt)); transition:width .9s cubic-bezier(.23,1,.32,1); }
.sb-qty { font-size:.7rem; color:var(--muted); margin-top:.12rem; }

/* ── Branch table ── */
.br-table { width:100%; border-collapse:collapse; }
.br-table th { font-size:.67rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); padding:.65rem 1rem; border-bottom:2px solid var(--border); background:var(--surface); }
.br-table td { padding:.75rem 1rem; border-bottom:1px solid var(--border); font-size:.84rem; color:var(--ink); vertical-align:middle; }
.br-table tr:last-child td { border-bottom:none; }
.br-table tbody tr:hover td { background:var(--surface); }
.br-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:.5rem; }

/* ── Mini stats (acciones rápidas) ── */
.mini-stat { border-radius:.6rem; padding:.75rem .9rem; text-align:center; }
.mini-val  { font-size:1.3rem; font-weight:800; }
.mini-lbl  { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }

/* ── Pulse ── */
.pulse { width:8px; height:8px; border-radius:50%; background:var(--dt); display:inline-block; position:relative; }
.pulse::after { content:''; position:absolute; inset:-4px; border-radius:50%; background:rgba(10,179,156,.3); animation:pulse-ring 1.8s infinite; }
@keyframes pulse-ring { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.8);opacity:0} }

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

.chart-h220 { position:relative; height:230px; }
.empty-box { text-align:center; padding:2rem 0; color:var(--muted); font-size:.83rem; }
.empty-box i { font-size:2rem; opacity:.25; display:block; margin-bottom:.5rem; }
</style>

{{-- ══════════════════════════════════
     1. ENCABEZADO
══════════════════════════════════ --}}
<div class="row mb-3 align-items-center">
    <div class="col">
        <h4 class="mb-0" style="font-weight:800;letter-spacing:-.02em;">
            @if($isDoctor && !$isAdmin) Mis Citas @else Panel de Control @endif
        </h4>
        <p class="mb-0 text-muted" style="font-size:.8rem;">
            <span class="pulse me-2"></span>
            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </p>
    </div>
</div>

{{-- ══════════════════════════════════
     2. BANNER (compacto)
══════════════════════════════════ --}}
<div class="mb-4">
@if($isAdmin)
    <div class="role-banner admin-banner fa0">
        <div class="banner-deco"></div>
        <div class="role-banner-ico"><i class="ri-shield-star-line"></i></div>
        <div>
            <h5>Vista de Administrador</h5>
            <p>Datos consolidados de todas las sucursales · {{ $branchStats->count() }} sucursal(es) activa(s)</p>
        </div>
    </div>
@elseif($isDoctor)
    <div class="role-banner doctor-banner fa0">
        <div class="banner-deco"></div>
        <div class="role-banner-ico"><i class="ri-stethoscope-line"></i></div>
        <div>
            <h5>Bienvenido, Dr. {{ auth()->user()->name }}</h5>
            <p>{{ auth()->user()->branch->name ?? 'Mi Sucursal' }} · Citas asignadas a tu agenda</p>
        </div>
        <span class="ms-auto" style="background:rgba(255,255,255,.15);border-radius:2rem;padding:.25rem .75rem;font-size:.78rem;font-weight:700;">
            <i class="ri-calendar-check-line me-1"></i>{{ $apptToday }} citas hoy
        </span>
    </div>
@else
    <div class="role-banner branch-banner fa0">
        <div class="banner-deco"></div>
        <div class="role-banner-ico"><i class="ri-building-2-line"></i></div>
        <div>
            <h5>{{ auth()->user()->branch->name ?? 'Mi Sucursal' }}</h5>
            <p>
                {{ auth()->user()->name }} ·
                <span style="background:rgba(255,255,255,.2);border-radius:2rem;padding:.1rem .55rem;font-size:.75rem;font-weight:700;text-transform:capitalize;">
                    {{ auth()->user()->role->name }}
                </span>
                · Solo ves información de tu sucursal
            </p>
        </div>
    </div>
@endif
</div>

{{-- ══════════════════════════════════
     3. KPIs (4 tarjetas)
══════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Ingresos del mes (servicios + productos) --}}
    <div class="col-xl-3 col-md-6 fa0">
        <div class="kpi kp-t">
            <div class="kpi-ico"><i class="ri-money-dollar-circle-line"></i></div>
            <div class="kpi-val">RD$ {{ number_format($totalRevenueThisMonth, 0, ',', '.') }}</div>
            <div class="kpi-lbl">Ingresos del mes</div>
            <div class="kpi-foot">
                <i class="ri-wallet-3-line"></i>
                Total cobrado (servicios + productos)
            </div>
            <div class="kpi-ghost"><i class="ri-money-dollar-circle-fill"></i></div>
        </div>
    </div>

    {{-- Ingresos por servicios --}}
    <div class="col-xl-3 col-md-6 fa1">
        <div class="kpi kp-p">
            <div class="kpi-ico"><i class="ri-stethoscope-line"></i></div>
            <div class="kpi-val">RD$ {{ number_format($revenueThisMonth, 0, ',', '.') }}</div>
            <div class="kpi-lbl">Ingresos por servicios</div>
            <div class="kpi-foot">
                @if($revenueGrowth > 0)
                    <span style="color:var(--dt);font-weight:700;"><i class="ri-arrow-up-line"></i>{{ $revenueGrowth }}%</span> vs mes anterior
                @elseif($revenueGrowth < 0)
                    <span style="color:var(--dr);font-weight:700;"><i class="ri-arrow-down-line"></i>{{ abs($revenueGrowth) }}%</span> vs mes anterior
                @else
                    <span>Sin cambios vs mes anterior</span>
                @endif
            </div>
            <div class="kpi-ghost"><i class="ri-stethoscope-fill"></i></div>
        </div>
    </div>

    {{-- Ingresos por productos --}}
    <div class="col-xl-3 col-md-6 fa2">
        <div class="kpi kp-v">
            <div class="kpi-ico"><i class="ri-shopping-bag-3-line"></i></div>
            <div class="kpi-val">RD$ {{ number_format($productRevenueThisMonth, 0, ',', '.') }}</div>
            <div class="kpi-lbl">Ingresos por productos</div>
            <div class="kpi-foot">
                @if($productRevenueGrowth > 0)
                    <span style="color:var(--dt);font-weight:700;"><i class="ri-arrow-up-line"></i>{{ $productRevenueGrowth }}%</span> vs mes anterior
                @elseif($productRevenueGrowth < 0)
                    <span style="color:var(--dr);font-weight:700;"><i class="ri-arrow-down-line"></i>{{ abs($productRevenueGrowth) }}%</span> vs mes anterior
                @else
                    <span>Sin cambios vs mes anterior</span>
                @endif
            </div>
            <div class="kpi-ghost"><i class="ri-shopping-bag-3-fill"></i></div>
        </div>
    </div>

    {{-- Por cobrar --}}
    <div class="col-xl-3 col-md-6 fa1">
        <div class="kpi kp-a">
            <div class="kpi-ico"><i class="ri-file-list-3-line"></i></div>
            <div class="kpi-val">{{ $pendingInvoices + $productPendingInvoices }}</div>
            <div class="kpi-lbl">Facturas pendientes</div>
            <div class="kpi-foot">
                <i class="ri-coins-line"></i>
                RD$ {{ number_format($pendingAmount + $productPendingAmount, 0, ',', '.') }} por cobrar
            </div>
            <div class="kpi-ghost"><i class="ri-file-list-fill"></i></div>
        </div>
    </div>

    {{-- Citas hoy --}}
    <div class="col-xl-6 col-md-6 fa2">
        <div class="kpi kp-p">
            <div class="kpi-ico"><i class="ri-calendar-check-line"></i></div>
            <div class="kpi-val">{{ $apptToday }}</div>
            <div class="kpi-lbl">Citas hoy</div>
            <div class="kpi-foot">
                <i class="ri-time-line"></i>
                {{ $apptPending }} próximas · {{ $apptCompleted }} completadas en el mes
            </div>
            <div class="kpi-ghost"><i class="ri-calendar-fill"></i></div>
        </div>
    </div>

    {{-- Pacientes --}}
    <div class="col-xl-6 col-md-6 fa3">
        <div class="kpi kp-v">
            <div class="kpi-ico"><i class="ri-team-line"></i></div>
            <div class="kpi-val">{{ $totalPatients }}</div>
            <div class="kpi-lbl">Pacientes{{ $isAdmin ? '' : ' en sucursal' }}</div>
            <div class="kpi-foot">
                <i class="ri-user-add-line"></i>
                +{{ $newPatientsMonth }} nuevos · {{ $patientsWithIns }} con seguro
            </div>
            <div class="kpi-ghost"><i class="ri-team-fill"></i></div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     4. GRÁFICO + AGENDA DE HOY
══════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-xl-8 fa0">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(64,81,137,.1);color:var(--dp)"><i class="ri-bar-chart-2-line"></i></div>
                <h6>Ingresos — últimos 14 días{{ $isAdmin ? ' (todas las sucursales)' : '' }}</h6>
                <span style="font-size:.74rem;color:var(--muted)">Solo facturas pagadas</span>
            </div>
            <div class="dc-body">
                <div class="chart-h220"><canvas id="revChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(64,81,137,.1);color:var(--dp)"><i class="ri-calendar-event-line"></i></div>
                <h6>Agenda de hoy</h6>
                <a href="{{ route('appointments.index') }}" class="see-all">Ver todas →</a>
            </div>
            <div class="dc-body" style="padding-top:.35rem;max-height:290px;overflow-y:auto;">
                @forelse($todayAppointments as $ap)
                    @php
                        $fn  = $ap->patient->first_name ?? '';
                        $ln  = $ap->patient->last_name  ?? '';
                        $ini = strtoupper(substr($fn,0,1).substr($ln,0,1));
                        $ci  = $loop->index % 6;
                        $stc = match($ap->status) { 'completada'=>'ag-comp','cancelada'=>'ag-canc',default=>'ag-prog' };
                    @endphp
                    <div class="ag-item">
                        <div class="ag-time">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('g:i') }}<br>
                            <span style="font-size:.62rem;color:var(--muted);font-weight:400;">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('A') }}</span>
                        </div>
                        <div class="ag-av av{{ $ci }}">{{ $ini ?: '?' }}</div>
                        <div>
                            <div class="ag-name">{{ $fn }} {{ $ln }}</div>
                            <div class="ag-doc">{{ $ap->doctor->name ?? '—' }}</div>
                        </div>
                        <span class="ag-st {{ $stc }}">{{ ucfirst($ap->status) }}</span>
                    </div>
                @empty
                    <div class="empty-box">
                        <i class="ri-calendar-check-line"></i>
                        Sin citas para hoy
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     5. FACTURAS RECIENTES + (SUCURSALES | MIS CITAS | ACCIONES)
══════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-xl-{{ $isAdmin ? '7' : '8' }} fa0">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(64,81,137,.1);color:var(--dp)"><i class="ri-file-text-line"></i></div>
                <h6>Facturas recientes{{ $isAdmin ? '' : ' — mi sucursal' }}</h6>
                <a href="{{ route('invoices.index') }}" class="see-all">Ver todas →</a>
            </div>
            <div class="dc-body" style="padding-top:.3rem;">
                @forelse($recentInvoices as $inv)
                    <a href="{{ route('invoices.show', $inv) }}" class="inv-row">
                        <div class="inv-num">{{ $inv->invoice_number }}</div>
                        <div>
                            <div class="inv-pat">{{ $inv->patient->first_name ?? '' }} {{ $inv->patient->last_name ?? '' }}</div>
                            @if($isAdmin)<div class="inv-br">{{ $inv->branch->name ?? '—' }}</div>@endif
                        </div>
                        <div class="inv-amt">RD$ {{ number_format($inv->total, 0, ',', '.') }}</div>
                        <span class="inv-badge ib-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
                    </a>
                @empty
                    <div class="empty-box">
                        <i class="ri-file-list-line"></i>
                        Sin facturas registradas
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($isAdmin)
    {{-- Rendimiento por sucursal — solo admin --}}
    <div class="col-xl-5 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(240,101,72,.1);color:var(--dr)"><i class="ri-building-2-line"></i></div>
                <h6>Rendimiento por sucursal "SERVICIOS"</h6>
                <a href="{{ route('branches.index') }}" class="see-all">Ver →</a>
            </div>
            <div style="overflow-x:auto;">
                <table class="br-table">
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th class="text-end">Ingresos mes</th>
                            <th class="text-center">Facturas</th>
                            <th class="text-center">Hoy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $bc = ['#405189','#0ab39c','#f7b84b','#f06548','#7066e0','#299cdb']; @endphp
                        @forelse($branchStats as $i => $br)
                        <tr>
                            <td><span class="br-dot" style="background:{{ $bc[$i%6] }}"></span><strong>{{ $br->name }}</strong></td>
                            <td class="text-end" style="font-weight:700;color:var(--dt);">RD$ {{ number_format($br->revenue_month ?? 0, 0,',','.') }}</td>
                            <td class="text-center"><strong>{{ $br->invoices_month }}</strong></td>
                            <td class="text-center">
                                @if($br->appts_today > 0)
                                    <span style="background:rgba(64,81,137,.1);color:var(--dp);font-size:.74rem;font-weight:700;padding:.16rem .52rem;border-radius:2rem;">{{ $br->appts_today }}</span>
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:1.5rem;">Sin sucursales</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @elseif($isDoctor)
    {{-- Mis citas de hoy — solo médico --}}
    <div class="col-xl-4 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(10,179,156,.1);color:var(--dt)"><i class="ri-calendar-check-line"></i></div>
                <h6>Mis citas de hoy</h6>
                <a href="{{ route('doctor.appointments.index') }}" class="see-all">Ver todas →</a>
            </div>
            <div class="dc-body" style="padding-top:.35rem;">
                @forelse($myTodayAppointments as $ap)
                    @php
                        $fn  = $ap->patient->first_name ?? '';
                        $ln  = $ap->patient->last_name  ?? '';
                        $ini = strtoupper(substr($fn,0,1).substr($ln,0,1));
                        $ci  = $loop->index % 6;
                        $stc = match($ap->status) { 'completada'=>'ag-comp','cancelada'=>'ag-canc',default=>'ag-prog' };
                    @endphp
                    <div class="ag-item">
                        <div class="ag-time">{{ \Carbon\Carbon::parse($ap->appointment_time)->format('g:i') }}</div>
                        <div class="ag-av av{{ $ci }}">{{ $ini ?: '?' }}</div>
                        <div>
                            <div class="ag-name">{{ $fn }} {{ $ln }}</div>
                            <div class="ag-doc">{{ $ap->branch->name ?? '—' }}</div>
                        </div>
                        <span class="ag-st {{ $stc }}">{{ ucfirst($ap->status) }}</span>
                    </div>
                @empty
                    <div class="empty-box">
                        <i class="ri-calendar-check-line"></i>
                        No tienes citas para hoy
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @else
    {{-- Acciones rápidas — recepcionista --}}
    <div class="col-xl-4 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(64,81,137,.1);color:var(--dp)"><i class="ri-rocket-line"></i></div>
                <h6>Acciones rápidas</h6>
            </div>
            <div class="dc-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('patients.create') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;">
                            <i class="ri-user-add-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Nuevo paciente</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('appointments.create') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;">
                            <i class="ri-calendar-add-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Nueva cita</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('invoices.create') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;">
                            <i class="ri-file-add-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Nueva factura</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('receipts.index') }}" class="d-block text-center p-3 rounded" style="background:var(--surface);color:var(--ink);text-decoration:none;">
                            <i class="ri-bank-card-line" style="font-size:1.6rem;display:block;margin-bottom:.3rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;">Registrar pago</span>
                        </a>
                    </div>
                </div>

                <hr style="margin:1rem 0;border-color:var(--border);">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="mini-stat" style="background:var(--surface);">
                            <div class="mini-val" style="color:var(--dp);">{{ $invoicesThisMonth }}</div>
                            <div class="mini-lbl">Facturas mes</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini-stat" style="background:var(--surface);">
                            <div class="mini-val" style="color:var(--dt);">{{ $apptCompleted }}</div>
                            <div class="mini-lbl">Citas completadas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- ══════════════════════════════════
     6. TOP SERVICIOS + TOP PRODUCTOS
══════════════════════════════════ --}}
<div class="row g-3">

    <div class="col-xl-6 fa0">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(10,179,156,.1);color:var(--dt)"><i class="ri-stethoscope-line"></i></div>
                <h6>Top servicios del mes</h6>
            </div>
            <div class="dc-body">
                @forelse($topServices as $svc)
                    @php $pct = $maxServiceRevenue > 0 ? ($svc->revenue / $maxServiceRevenue * 100) : 0; @endphp
                    <div class="sb-row">
                        <div class="sb-top">
                            <span class="sb-name">{{ $svc->name }}</span>
                            <span class="sb-val">RD$ {{ number_format($svc->revenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="sb-track"><div class="sb-fill" style="width:{{ round($pct) }}%"></div></div>
                        <div class="sb-qty">{{ $svc->qty }} unidades</div>
                    </div>
                @empty
                    <div class="empty-box">
                        <i class="ri-bar-chart-line"></i>
                        Sin datos este mes
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-6 fa1">
        <div class="dc h-100">
            <div class="dc-head">
                <div class="dc-ico" style="background:rgba(112,102,224,.1);color:var(--dv)"><i class="ri-trophy-line"></i></div>
                <h6>Top productos del mes</h6>
                <a href="{{ url('/product-reports') }}" class="see-all">Ver reporte →</a>
            </div>
            <div class="dc-body">
                @if(isset($topProducts) && $topProducts->count() > 0)
                    @foreach($topProducts as $prod)
                        @php $pct = $maxProductRevenue > 0 ? ($prod->revenue / $maxProductRevenue * 100) : 0; @endphp
                        <div class="sb-row">
                            <div class="sb-top">
                                <span class="sb-name">{{ $prod->name }}</span>
                                <span class="sb-val" style="color:var(--dv);">RD$ {{ number_format($prod->revenue, 0, ',', '.') }}</span>
                            </div>
                            <div class="sb-track">
                                <div class="sb-fill" style="width:{{ round($pct) }}%;background:linear-gradient(90deg,var(--dv),var(--db));"></div>
                            </div>
                            <div class="sb-qty">{{ $prod->qty }} vendidos · {{ $prod->code }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-box">
                        <i class="ri-shopping-bag-3-line"></i>
                        Sin ventas de productos este mes
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div style="height:1.5rem;"></div>
</div>
</div>

@push('scripts')
{{-- Chart.js ya se carga en app.blade.php --}}
<script>
const CP='#405189', CBORDER='#edf0f7', CMUTED='#6b7a99';

const revData = @json($last14Days);

new Chart(document.getElementById('revChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels  : revData.map(d => d.label),
        datasets: [
            {
                label: 'Servicios',
                data: revData.map(d => d.services || 0),
                backgroundColor: 'rgba(64,81,137,.75)',
                borderColor: CP,
                borderWidth: 1.5,
                borderRadius: 0,
                borderSkipped: false,
                stack: 'revenue',
            },
            {
                label: 'Productos',
                data: revData.map(d => d.products || 0),
                backgroundColor: 'rgba(112,102,224,.75)',
                borderColor: '#7066e0',
                borderWidth: 1.5,
                borderRadius: 5,
                borderSkipped: false,
                stack: 'revenue',
            }
        ]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins: {
            legend: {
                display: true, position: 'top', align: 'end',
                labels: { boxWidth: 12, padding: 10, font: { size: 11, weight: '600' }, color: CMUTED }
            },
            tooltip: {
                backgroundColor:'#fff', borderColor:CBORDER, borderWidth:1,
                titleColor:'#1e2535', bodyColor:CMUTED, padding:10,
                callbacks: {
                    label: c => ' ' + c.dataset.label + ': RD$ ' + c.parsed.y.toLocaleString('es-DO',{minimumFractionDigits:2})
                }
            }
        },
        scales: {
            x: { stacked:true, grid:{display:false}, border:{display:false}, ticks:{color:CMUTED, font:{size:10,weight:'600'}} },
            y: {
                stacked:true, grid:{color:CBORDER}, border:{display:false, dash:[3,3]},
                ticks:{ color:CMUTED, font:{size:10}, callback: v => v>=1000 ? 'RD$'+(v/1000).toFixed(0)+'k' : 'RD$'+v }
            }
        }
    }
});
</script>
@endpush

</x-app-layout>