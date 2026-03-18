<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
    /* ── Filter bar ── */
    .filter-bar { background:#fff; border-radius:.75rem; padding:1rem 1.25rem; box-shadow:0 2px 12px rgba(64,81,137,.07); display:flex; flex-wrap:wrap; gap:.75rem; align-items:flex-end; margin-bottom:1.5rem; }
    .filter-bar .form-label { font-size:.72rem; font-weight:700; color:#8098bb; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem; }
    .filter-bar .form-control { border:1.5px solid #e2e8f0; border-radius:.5rem; font-size:.87rem; }
    .filter-bar .form-control:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
    .btn-apply { background:linear-gradient(135deg,#405189,#0ab39c); border:none; color:#fff; border-radius:.5rem; padding:.52rem 1.25rem; font-weight:700; font-size:.87rem; display:inline-flex; align-items:center; gap:.4rem; white-space:nowrap; transition:opacity .2s; cursor:pointer; }
    .btn-apply:hover { opacity:.9; color:#fff; }
    .btn-clear { border:1.5px solid #e2e8f0; background:#f8faff; color:#8098bb; border-radius:.5rem; padding:.52rem .9rem; font-size:.87rem; font-weight:600; transition:all .15s; white-space:nowrap; cursor:pointer; }
    .btn-clear:hover { background:#e2e8f0; color:#344563; }

    /* ── Shortcut buttons ── */
    .shortcut-bar { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .btn-shortcut { padding:.35rem .85rem; border-radius:2rem; font-size:.8rem; font-weight:600; border:1.5px solid #e2e8f0; background:#fff; color:#8098bb; cursor:pointer; transition:all .15s; }
    .btn-shortcut:hover, .btn-shortcut.active { background:#405189; color:#fff; border-color:#405189; }

    /* ── Tabs ── */
    .rpt-tabs { display:flex; gap:.35rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .rpt-tab { display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1.1rem; border-radius:2rem; font-size:.84rem; font-weight:600; border:1.5px solid #e2e8f0; background:#fff; color:#8098bb; cursor:pointer; transition:all .2s; }
    .rpt-tab:hover { border-color:#405189; color:#405189; }
    .rpt-tab.active { background:linear-gradient(135deg,#405189,#0ab39c); border-color:transparent; color:#fff; box-shadow:0 4px 14px rgba(64,81,137,.25); }

    /* ── KPI cards ── */
    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(155px,1fr)); gap:1rem; margin-bottom:1.25rem; }
    .kpi-card { background:#fff; border-radius:.75rem; padding:1rem 1.25rem; box-shadow:0 2px 12px rgba(64,81,137,.07); position:relative; overflow:hidden; border-left:4px solid transparent; transition:transform .2s; }
    .kpi-card:hover { transform:translateY(-2px); }
    .kpi-card.primary { border-left-color:#405189; }
    .kpi-card.success { border-left-color:#0ab39c; }
    .kpi-card.warning { border-left-color:#f59e0b; }
    .kpi-card.danger  { border-left-color:#e74c3c; }
    .kpi-card.info    { border-left-color:#299cdb; }
    .kpi-card.purple  { border-left-color:#7c3aed; }
    .kpi-lbl  { font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#8098bb; }
    .kpi-val  { font-size:1.5rem; font-weight:800; color:#344563; line-height:1.1; margin-top:.2rem; }
    .kpi-sub  { font-size:.75rem; color:#8098bb; margin-top:.15rem; }
    .kpi-icon { position:absolute; right:-.5rem; bottom:-.5rem; font-size:3.5rem; opacity:.06; color:#405189; }

    /* ── Chart cards ── */
    .chart-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
    @media(max-width:767px) { .chart-grid-2 { grid-template-columns:1fr; } }
    .chart-card { background:#fff; border-radius:.75rem; box-shadow:0 2px 12px rgba(64,81,137,.07); overflow:hidden; margin-bottom:0; }
    .chart-card-header { padding:.85rem 1.25rem; border-bottom:1px solid #f0f2f7; display:flex; align-items:center; gap:.5rem; }
    .chart-card-header h6 { margin:0; font-size:.88rem; font-weight:700; color:#344563; flex-grow:1; }
    .chart-card-header i  { color:#405189; font-size:1rem; }
    .chart-card-body { padding:1.25rem; }

    /* ── Section title ── */
    .section-title { font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#8098bb; display:flex; align-items:center; gap:.5rem; margin-bottom:.85rem; padding-bottom:.4rem; border-bottom:1px solid #f0f2f7; }

    /* ── Tables ── */
    .rpt-table { width:100%; border-collapse:collapse; }
    .rpt-table th { font-size:.67rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#8098bb; border-bottom:2px solid #e9ecef; padding:.7rem .85rem; white-space:nowrap; }
    .rpt-table td { padding:.65rem .85rem; border-bottom:1px solid #f3f5f9; font-size:.84rem; vertical-align:middle; }
    .rpt-table tbody tr:last-child td { border-bottom:none; }
    .rpt-table tbody tr:hover { background:#f8faff; }

    /* ── Status badges ── */
    .badge-completada { background:#d1fae5; color:#065f46; padding:.22rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700; }
    .badge-pendiente  { background:#fef9c3; color:#92400e; padding:.22rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700; }
    .badge-confirmada { background:#dbeafe; color:#1e40af; padding:.22rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700; }
    .badge-cancelada  { background:#fee2e2; color:#991b1b; padding:.22rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700; }

    /* ── Progress bar ── */
    .prog-bar-wrap { height:6px; background:#f0f2f7; border-radius:3px; overflow:hidden; margin-top:.3rem; }
    .prog-bar { height:100%; border-radius:3px; background:linear-gradient(90deg,#405189,#0ab39c); }

    /* ── Alert pending ── */
    .pending-alert { background:linear-gradient(135deg,rgba(245,158,11,.08),rgba(239,68,68,.05)); border:1px solid rgba(245,158,11,.25); border-radius:.65rem; padding:.75rem 1rem; display:flex; align-items:center; gap:.65rem; font-size:.85rem; color:#92400e; font-weight:600; margin-bottom:1rem; }

    /* ── Days pending chip ── */
    .days-chip { display:inline-flex; align-items:center; padding:.18rem .55rem; border-radius:2rem; font-size:.72rem; font-weight:700; }
    .days-chip.ok      { background:#d1fae5; color:#065f46; }
    .days-chip.warn    { background:#fef9c3; color:#92400e; }
    .days-chip.danger  { background:#fee2e2; color:#991b1b; }

    /* ── Rank badge ── */
    .rank-badge { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; font-size:.7rem; font-weight:700; background:#f0f4ff; color:#405189; }
    .rank-badge.gold   { background:#fef9c3; color:#92400e; }
    .rank-badge.silver { background:#f3f4f6; color:#6b7280; }
    .rank-badge.bronze { background:#fef0e7; color:#92400e; }

    /* ── Loading ── */
    .loading-overlay { display:flex; align-items:center; justify-content:center; padding:3rem; }
    .spinner-ring { width:36px; height:36px; border:3px solid #e2e8f0; border-top-color:#405189; border-radius:50%; animation:spin .7s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* ── Tasa circle ── */
    .tasa-circle { width:90px; height:90px; border-radius:50%; background:conic-gradient(#0ab39c var(--pct), #f0f2f7 var(--pct)); display:flex; align-items:center; justify-content:center; position:relative; }
    .tasa-inner  { width:68px; height:68px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; font-size:.95rem; font-weight:800; color:#344563; }

    /* ── Animations ── */
    @keyframes fadeInUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .fade-up { animation:fadeInUp .35s ease both; }

    /* ── Toast ── */
    #toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
    .toast-item { min-width:260px; padding:.8rem 1rem; border-radius:.5rem; color:#fff; font-size:.85rem; font-weight:500; display:flex; align-items:center; gap:.5rem; box-shadow:0 4px 20px rgba(0,0,0,.18); animation:toastIn .3s ease; }
    @keyframes toastIn { from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:translateX(0)} }
    .toast-error { background:linear-gradient(135deg,#e74c3c,#c0392b); }
</style>

<div id="toast-container"></div>

{{-- Breadcrumb --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Mis Reportes</h4>
                <div class="text-muted" style="font-size:.82rem;margin-top:.15rem;">
                    <i class="ri-user-heart-line me-1"></i>{{ auth()->user()->name }}
                    @if(auth()->user()->branch)
                        &nbsp;·&nbsp;<i class="ri-building-2-line me-1"></i>{{ auth()->user()->branch->name }}
                    @endif
                </div>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Mis Reportes</li>
            </ol>
        </div>
    </div>
</div>

{{-- Shortcuts --}}
<div class="shortcut-bar">
    <button class="btn-shortcut" onclick="setShortcut('week', this)">Esta semana</button>
    <button class="btn-shortcut active" id="sc-month" onclick="setShortcut('month', this)">Este mes</button>
    <button class="btn-shortcut" onclick="setShortcut('last_month', this)">Mes anterior</button>
    <button class="btn-shortcut" onclick="setShortcut('year', this)">Este año</button>
    <button class="btn-shortcut" onclick="setShortcut('custom', this)"><i class="ri-calendar-line me-1"></i>Personalizado</button>
</div>

{{-- Filter bar --}}
<div class="filter-bar" id="filter-bar" style="display:none;">
    <div>
        <div class="form-label">Desde</div>
        <input type="date" id="f-date-from" class="form-control form-control-sm" style="width:145px;">
    </div>
    <div>
        <div class="form-label">Hasta</div>
        <input type="date" id="f-date-to" class="form-control form-control-sm" style="width:145px;">
    </div>
    <div class="d-flex gap-2 align-items-end">
        <button class="btn-apply" onclick="applyFilters()">
            <i class="ri-search-line"></i> Aplicar
        </button>
        <button class="btn-clear" onclick="clearFilters()">
            <i class="ri-refresh-line"></i>
        </button>
    </div>
</div>

{{-- Tabs --}}
{{-- Tabs --}}
<div class="rpt-tabs">
    <button class="rpt-tab active" onclick="switchTab('appointments', this)">
        <i class="ri-calendar-check-line"></i> Mis Citas
    </button>
    <button class="rpt-tab" onclick="switchTab('clinical', this)">
        <i class="ri-stethoscope-line"></i> Historias Clínicas
    </button>
</div>

{{-- ════════════════════════════
     TAB: CITAS
════════════════════════════ --}}
<div id="tab-appointments" class="fade-up">
    <div id="loading-appointments" class="loading-overlay"><div class="spinner-ring"></div></div>
    <div id="content-appointments" class="d-none">

        <div class="kpi-grid" id="kpi-appointments"></div>

        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-line-chart-line"></i>
                    <h6>Citas por Mes</h6>
                </div>
                <div class="chart-card-body">
                    <canvas id="chart-apt-month" height="200"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-bar-chart-horizontal-line"></i>
                    <h6>Citas por Día de la Semana</h6>
                </div>
                <div class="chart-card-body">
                    <canvas id="chart-apt-weekday" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-pie-chart-line"></i>
                    <h6>Estado de Citas</h6>
                </div>
                <div class="chart-card-body d-flex align-items-center justify-content-center" style="min-height:240px;">
                    <canvas id="chart-apt-status" style="max-width:240px;max-height:240px;"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-building-2-line"></i>
                    <h6>Citas por Sucursal</h6>
                </div>
                <div class="chart-card-body">
                    <div class="table-responsive">
                        <table class="rpt-table" id="table-apt-branch"></table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Próximas citas --}}
        <div class="chart-card" style="margin-bottom:1.25rem;">
            <div class="chart-card-header">
                <i class="ri-calendar-todo-line"></i>
                <h6>Próximas Citas (7 días)</h6>
                <span id="upcoming-count" class="badge bg-primary-subtle text-primary ms-auto" style="font-size:.78rem;"></span>
            </div>
            <div class="table-responsive">
                <table class="rpt-table w-100">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-upcoming"></tbody>
                </table>
            </div>
            <div id="upcoming-empty" class="text-center py-4 d-none">
                <i class="ri-calendar-check-line d-block text-muted mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0" style="font-size:.87rem;">No tienes citas próximas.</p>
            </div>
        </div>

    </div>
</div>

{{-- ════════════════════════════
     TAB: HISTORIAS CLÍNICAS
════════════════════════════ --}}
<div id="tab-clinical" class="fade-up d-none">
    <div id="loading-clinical" class="loading-overlay"><div class="spinner-ring"></div></div>
    <div id="content-clinical" class="d-none">

        <div class="kpi-grid" id="kpi-clinical"></div>

        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-line-chart-line"></i>
                    <h6>HCs Completadas por Mes</h6>
                </div>
                <div class="chart-card-body">
                    <canvas id="chart-hc-month" height="200"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-pie-chart-line"></i>
                    <h6>Estado de Historias Clínicas</h6>
                </div>
                <div class="chart-card-body d-flex align-items-center justify-content-center" style="min-height:240px;">
                    <canvas id="chart-hc-status" style="max-width:240px;max-height:240px;"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-building-2-line"></i>
                    <h6>HCs por Sucursal</h6>
                </div>
                <div class="chart-card-body">
                    <div class="table-responsive">
                        <table class="rpt-table" id="table-hc-branch"></table>
                    </div>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-time-line"></i>
                    <h6>HCs Pendientes por Completar</h6>
                    <span id="pending-hc-count" class="badge bg-warning-subtle text-warning ms-auto" style="font-size:.78rem;"></span>
                </div>
                <div class="chart-card-body">
                    <div id="pending-alert-wrap"></div>
                    <div class="table-responsive">
                        <table class="rpt-table w-100">
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Factura</th>
                                    <th>Sucursal</th>
                                    <th>Fecha</th>
                                    <th>Días</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-pending-hc"></tbody>
                        </table>
                    </div>
                    <div id="pending-hc-empty" class="text-center py-3 d-none">
                        <i class="ri-checkbox-circle-line d-block text-success mb-1" style="font-size:2rem;"></i>
                        <p class="text-muted mb-0" style="font-size:.85rem;">¡No tienes HCs pendientes!</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


</div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const URLS = {
    appointments: "{{ route('audiologist.reports.appointments') }}",
    clinical    : "{{ route('audiologist.reports.clinical-records') }}",
};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color       = '#8098bb';

const COLORS = ['#405189','#0ab39c','#f59e0b','#e74c3c','#299cdb','#7c3aed','#10b981','#f97316'];
const charts = {};
let activeTab  = 'appointments';
let loadedTabs = {};

// ── Date helpers ──────────────────────────────────────────
function today()      { return new Date().toISOString().split('T')[0]; }
function weekStart()  { const d=new Date(); d.setDate(d.getDate()-d.getDay()+1); return d.toISOString().split('T')[0]; }
function monthStart() { const d=new Date(); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-01'; }
function lastMonthStart() {
    const d=new Date(); d.setMonth(d.getMonth()-1); d.setDate(1);
    return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-01';
}
function lastMonthEnd() {
    const d=new Date(); d.setDate(0);
    return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
}
function yearStart()  { return new Date().getFullYear()+'-01-01'; }

function setShortcut(type, btn) {
    document.querySelectorAll('.btn-shortcut').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const bar = document.getElementById('filter-bar');
    switch(type) {
        case 'week':      setDates(weekStart(), today());      bar.style.display='none'; break;
        case 'month':     setDates(monthStart(), today());     bar.style.display='none'; break;
        case 'last_month':setDates(lastMonthStart(), lastMonthEnd()); bar.style.display='none'; break;
        case 'year':      setDates(yearStart(), today());      bar.style.display='none'; break;
        case 'custom':    bar.style.display='flex'; return;
    }
    applyFilters();
}

function setDates(from, to) {
    document.getElementById('f-date-from').value = from;
    document.getElementById('f-date-to').value   = to;
}

// ── Filters ──────────────────────────────────────────────
function getFilters() {
    return {
        date_from: document.getElementById('f-date-from').value,
        date_to  : document.getElementById('f-date-to').value,
    };
}

function buildParams(f) {
    const p = new URLSearchParams();
    Object.entries(f).forEach(([k,v]) => { if(v) p.set(k,v); });
    return p.toString();
}

function applyFilters() { loadedTabs={}; loadTab(activeTab); }
function clearFilters()  { setDates(monthStart(), today()); applyFilters(); }

// ── Tabs ─────────────────────────────────────────────────
function switchTab(tab, btn) {
    ['appointments','clinical'].forEach(t => {
        document.getElementById('tab-'+t).classList.add('d-none');
    });
    document.querySelectorAll('.rpt-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-'+tab).classList.remove('d-none');
    btn.classList.add('active');
    activeTab = tab;
    if (!loadedTabs[tab]) loadTab(tab);
}

async function loadTab(tab) {
    showLoading(tab, true);
    hideContent(tab);
    try {
        const params = buildParams(getFilters());
        const res = await fetch(URLS[tab]+(params?'?'+params:''), {
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();
        renderTab(tab, d);
        loadedTabs[tab] = true;
    } catch {
        showToast('Error al cargar los datos.', 'error');
    } finally {
        showLoading(tab, false);
        showContent(tab);
    }
}

function showLoading(tab, show) {
    const el = document.getElementById('loading-'+tab);
    if (el) el.style.display = show ? 'flex' : 'none';
}
function hideContent(tab) {
    const el = document.getElementById('content-'+tab);
    if (el) el.classList.add('d-none');
}
function showContent(tab) {
    const el = document.getElementById('content-'+tab);
    if (el) el.classList.remove('d-none');
}

function renderTab(tab, d) {
    if (tab === 'appointments') renderAppointments(d);
    if (tab === 'clinical')     renderClinical(d);
}

// ── RENDER: Citas ─────────────────────────────────────────
function renderAppointments(d) {
    const k = d.kpis;

    // ── KPIs — ahora con "programadas" en vez de pendientes/confirmadas ──
    document.getElementById('kpi-appointments').innerHTML = `
        ${kpiCard('Total Citas',    k.total,       'ri-calendar-line',        'primary')}
        ${kpiCard('Completadas',    k.completadas, 'ri-checkbox-circle-line', 'success')}
        ${kpiCard('Programadas',    k.programadas, 'ri-calendar-check-line',  'info')}
        ${kpiCard('Canceladas',     k.canceladas,  'ri-close-circle-line',    'danger')}
        ${kpiCard('Tasa Compleción',k.tasa+'%',    'ri-percent-line',         'purple')}
    `;

    // ── Línea por mes ────────────────────────────────────
    makeLineChart('chart-apt-month',
        d.by_month.map(r => fmtMonth(r.month)),
        d.by_month.map(r => parseInt(r.count)),
        'Citas', '#405189'
    );

    // ── Barras día de la semana ──────────────────────────
    makeBarChart('chart-apt-weekday',
        d.by_weekday.map(r => r.day),
        d.by_weekday.map(r => r.count),
        'Citas', '#0ab39c'
    );

    // ── Donut estado ─────────────────────────────────────
    const statusData  = [k.completadas, k.programadas, k.canceladas];
    const statusTotal = statusData.reduce((a, b) => Number(a) + Number(b), 0);
    const statusWrap  = document.getElementById('chart-apt-status')?.closest('.d-flex');

    if (statusTotal > 0) {
        // Restaurar canvas si fue reemplazado antes
        if (statusWrap && !statusWrap.querySelector('canvas')) {
            statusWrap.innerHTML = '<canvas id="chart-apt-status" style="max-width:240px;max-height:240px;"></canvas>';
        }
        makeDonutChart('chart-apt-status',
            ['Completadas', 'Programadas', 'Canceladas'],
            statusData,
            ['#0ab39c', '#299cdb', '#e74c3c']
        );
    } else {
        if (statusWrap) {
            statusWrap.innerHTML = `
                <div class="text-center py-4">
                    <i class="ri-pie-chart-line d-block text-muted mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                    <p class="text-muted mb-0" style="font-size:.85rem;">Sin datos en este período.</p>
                </div>`;
        }
    }

    // ── Tabla por sucursal ────────────────────────────────
    const branches = Array.isArray(d.by_branch) ? d.by_branch : [];
    if (branches.length > 0) {
        const maxB = Math.max(...branches.map(r => Number(r.count)), 1);
        document.getElementById('table-apt-branch').innerHTML =
            tableHead(['Sucursal', 'Total', 'Completadas', '%']) +
            '<tbody>' + branches.map(r => `
                <tr>
                    <td class="fw-semibold">${r.branch}</td>
                    <td>${r.count}</td>
                    <td><span style="color:#0ab39c;font-weight:700;">${r.completadas}</span></td>
                    <td style="min-width:110px;">
                        <div class="prog-bar-wrap">
                            <div class="prog-bar" style="width:${Math.round(Number(r.count)/maxB*100)}%"></div>
                        </div>
                        <span style="font-size:.72rem;color:#8098bb;">${Math.round(Number(r.count)/maxB*100)}%</span>
                    </td>
                </tr>`).join('') + '</tbody>';
    } else {
        document.getElementById('table-apt-branch').innerHTML =
            '<tbody><tr><td colspan="4" class="text-center text-muted py-3" style="font-size:.85rem;">Sin datos en este período.</td></tr></tbody>';
    }

    // ── Próximas citas ────────────────────────────────────
    const upcoming = Array.isArray(d.upcoming) ? d.upcoming : [];
    const badge    = document.getElementById('upcoming-count');
    const tbody    = document.getElementById('tbody-upcoming');
    const empty    = document.getElementById('upcoming-empty');
    const table    = tbody?.closest('table');

    badge.textContent = upcoming.length + ' cita' + (upcoming.length !== 1 ? 's' : '');

    if (upcoming.length === 0) {
        tbody.innerHTML   = '';
        if (table) table.style.display = 'none';
        empty.classList.remove('d-none');
    } else {
        if (table) table.style.display = '';
        empty.classList.add('d-none');

        // "programada" es el único status activo ahora
        const statusBadges = {
            programada: '<span class="badge-confirmada">Programada</span>',
            completada: '<span class="badge-completada">Completada</span>',
            cancelada : '<span class="badge-cancelada">Cancelada</span>',
        };

        tbody.innerHTML = upcoming.map(a => `
            <tr>
                <td class="fw-semibold">${a.date ?? '—'}</td>
                <td style="font-family:monospace;color:#405189;">${a.time ?? '—'}</td>
                <td>${a.patient ?? '—'}</td>
                <td class="text-muted">${a.branch ?? '—'}</td>
                <td>${statusBadges[a.status] ?? a.status}</td>
                <td class="text-muted" style="font-size:.8rem;max-width:180px;">${a.notes || '—'}</td>
            </tr>`).join('');
    }
}

// ── RENDER: Historias Clínicas ────────────────────────────
function renderClinical(d) {
    const k = d.kpis;

    document.getElementById('kpi-clinical').innerHTML = `
        ${kpiCard('Total HCs',    k.total,       'ri-file-list-3-line',     'primary')}
        ${kpiCard('Completadas',  k.completadas, 'ri-checkbox-circle-line', 'success')}
        ${kpiCard('Pendientes',   k.pendientes,  'ri-time-line',            'warning')}
        ${kpiCard('Tasa Compleción', k.tasa+'%', 'ri-percent-line',         'purple')}
    `;

    makeLineChart('chart-hc-month',
        d.by_month.map(r => fmtMonth(r.month)),
        d.by_month.map(r => parseInt(r.count)),
        'HCs Completadas', '#0ab39c'
    );

    makeDonutChart('chart-hc-status',
        ['Completadas','Pendientes'],
        [k.completadas, k.pendientes],
        ['#0ab39c','#f59e0b']
    );

    // Tabla por sucursal
    document.getElementById('table-hc-branch').innerHTML =
        tableHead(['Sucursal','Total','Completadas','Pendientes']) +
        '<tbody>' + d.by_branch.map(r => `
            <tr>
                <td class="fw-semibold">${r.branch}</td>
                <td>${r.total}</td>
                <td><span style="color:#0ab39c;font-weight:700;">${r.completadas}</span></td>
                <td><span style="color:#f59e0b;font-weight:700;">${r.pendientes}</span></td>
            </tr>`).join('') + '</tbody>';

    // HCs pendientes
    const pending = d.pending_list;
    const badge   = document.getElementById('pending-hc-count');
    badge.textContent = pending.length + ' pendiente' + (pending.length !== 1 ? 's' : '');

    const alertWrap = document.getElementById('pending-alert-wrap');
    alertWrap.innerHTML = pending.length > 0
        ? `<div class="pending-alert">
               <i class="ri-error-warning-line fs-18"></i>
               Tienes ${pending.length} historia${pending.length !== 1 ? 's' : ''} clínica${pending.length !== 1 ? 's' : ''} pendiente${pending.length !== 1 ? 's' : ''} de completar.
           </div>`
        : '';

    const tbody = document.getElementById('tbody-pending-hc');
    const empty = document.getElementById('pending-hc-empty');

    if (!pending.length) {
        tbody.innerHTML = '';
        empty.classList.remove('d-none');
    } else {
        empty.classList.add('d-none');
        tbody.innerHTML = pending.map(r => {
            const chipClass = r.days_pending <= 2 ? 'ok' : r.days_pending <= 7 ? 'warn' : 'danger';
            return `<tr>
                <td>
                    <div class="fw-semibold" style="font-size:.87rem;">${r.patient}</div>
                    <div class="text-muted" style="font-size:.75rem;">${r.cedula}</div>
                </td>
                <td style="font-family:monospace;color:#405189;font-weight:700;">${r.invoice_number}</td>
                <td class="text-muted">${r.branch}</td>
                <td class="text-muted">${r.created_at}</td>
                <td><span class="days-chip ${chipClass}">${r.days_pending}d</span></td>
                <td>
                    <a href="${r.edit_url}" class="btn btn-sm" style="background:linear-gradient(135deg,#405189,#0ab39c);color:#fff;border-radius:.4rem;padding:.25rem .65rem;font-size:.78rem;font-weight:600;text-decoration:none;">
                        <i class="ri-edit-2-line me-1"></i>Completar
                    </a>
                </td>
            </tr>`;
        }).join('');
    }
}



// ── Chart helpers ─────────────────────────────────────────
function destroyChart(id) { if(charts[id]){ charts[id].destroy(); delete charts[id]; } }

function makeBarChart(id, labels, data, label, color) {
    destroyChart(id);
    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;
    charts[id] = new Chart(ctx, {
        type:'bar',
        data:{ labels, datasets:[{ label, data, backgroundColor:color+'cc', borderColor:color, borderWidth:1.5, borderRadius:5 }] },
        options:{ responsive:true, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,grid:{color:'#f0f2f7'}}, x:{grid:{display:false}} } }
    });
}

function makeHBarChart(id, labels, data, label, color) {
    destroyChart(id);
    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;
    charts[id] = new Chart(ctx, {
        type:'bar',
        data:{ labels, datasets:[{ label, data, backgroundColor:color+'cc', borderColor:color, borderWidth:1.5, borderRadius:4 }] },
        options:{ indexAxis:'y', responsive:true, plugins:{legend:{display:false}}, scales:{ x:{beginAtZero:true,grid:{color:'#f0f2f7'}}, y:{grid:{display:false}} } }
    });
}

function makeLineChart(id, labels, data, label, color) {
    destroyChart(id);
    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;
    charts[id] = new Chart(ctx, {
        type:'line',
        data:{ labels, datasets:[{ label, data, borderColor:color, backgroundColor:color+'18', borderWidth:2.5, fill:true, tension:.4, pointBackgroundColor:color, pointRadius:4 }] },
        options:{ responsive:true, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,grid:{color:'#f0f2f7'}}, x:{grid:{display:false}} } }
    });
}

function makeDonutChart(id, labels, data, colors) {
    destroyChart(id);
    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;
    charts[id] = new Chart(ctx, {
        type:'doughnut',
        data:{ labels, datasets:[{ data, backgroundColor:colors, borderWidth:2, borderColor:'#fff', hoverOffset:6 }] },
        options:{ responsive:true, cutout:'65%', plugins:{ legend:{ position:'bottom', labels:{padding:14} } } }
    });
}

// ── HTML helpers ──────────────────────────────────────────
function kpiCard(label, value, icon, type, sub) {
    return `<div class="kpi-card ${type}">
        <div class="kpi-lbl">${label}</div>
        <div class="kpi-val">${value}</div>
        ${sub ? `<div class="kpi-sub">${sub}</div>` : ''}
        <i class="${icon} kpi-icon"></i>
    </div>`;
}

function tableHead(cols) {
    return '<thead><tr>' + cols.map(c => `<th>${c}</th>`).join('') + '</tr></thead>';
}

function rankClass(i) {
    return i === 0 ? 'gold' : i === 1 ? 'silver' : i === 2 ? 'bronze' : '';
}

function fmtNum(n) {
    return parseFloat(n||0).toLocaleString('es-DO',{minimumFractionDigits:2,maximumFractionDigits:2});
}

function fmtMonth(m) {
    if (!m) return '—';
    const [y,mo] = m.split('-');
    const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    return (months[parseInt(mo)-1] || mo) + ' ' + y;
}

function showToast(msg, type) {
    const div = document.createElement('div');
    div.className = 'toast-item toast-' + (type || 'error');
    div.innerHTML = `<i class="ri-error-warning-line fs-16"></i>${msg}`;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(() => { div.style.opacity='0'; div.style.transition='opacity .4s'; setTimeout(()=>div.remove(),400); }, 3500);
}

// ── Boot ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    setDates(monthStart(), today());
    loadTab('appointments');
});
</script>
@endpush
</x-app-layout>