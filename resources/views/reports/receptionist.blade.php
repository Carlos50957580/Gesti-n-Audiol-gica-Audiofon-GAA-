<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
    /* ── Filter bar ── */
    .filter-bar { background:#fff; border-radius:.75rem; padding:1rem 1.25rem; box-shadow:0 2px 12px rgba(64,81,137,.07); display:flex; flex-wrap:wrap; gap:.75rem; align-items:flex-end; margin-bottom:1.5rem; }
    .filter-bar .form-label { font-size:.72rem; font-weight:700; color:#8098bb; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem; }
    .filter-bar .form-control { border:1.5px solid #e2e8f0; border-radius:.5rem; font-size:.87rem; }
    .filter-bar .form-control:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
    .btn-apply { background:linear-gradient(135deg,#405189,#0ab39c); border:none; color:#fff; border-radius:.5rem; padding:.52rem 1.25rem; font-weight:700; font-size:.87rem; display:inline-flex; align-items:center; gap:.4rem; white-space:nowrap; transition:opacity .2s; }
    .btn-apply:hover { opacity:.9; color:#fff; }
    .btn-clear { border:1.5px solid #e2e8f0; background:#f8faff; color:#8098bb; border-radius:.5rem; padding:.52rem .9rem; font-size:.87rem; font-weight:600; transition:all .15s; white-space:nowrap; }
    .btn-clear:hover { background:#e2e8f0; color:#344563; }

    /* ── Shortcut buttons ── */
    .shortcut-bar { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .btn-shortcut { padding:.35rem .85rem; border-radius:2rem; font-size:.8rem; font-weight:600; border:1.5px solid #e2e8f0; background:#fff; color:#8098bb; cursor:pointer; transition:all .15s; }
    .btn-shortcut:hover, .btn-shortcut.active { background:#405189; color:#fff; border-color:#405189; }

    /* ── KPI cards ── */
    .kpi-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.25rem; }
    .kpi-grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:991px){ .kpi-grid-3,.kpi-grid-4 { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:575px){ .kpi-grid-3,.kpi-grid-4 { grid-template-columns:1fr; } }

    .kpi-card { background:#fff; border-radius:.75rem; padding:1rem 1.25rem; box-shadow:0 2px 12px rgba(64,81,137,.07); position:relative; overflow:hidden; border-left:4px solid transparent; transition:transform .2s; }
    .kpi-card:hover { transform:translateY(-2px); }
    .kpi-card.primary { border-left-color:#405189; }
    .kpi-card.success { border-left-color:#0ab39c; }
    .kpi-card.warning { border-left-color:#f59e0b; }
    .kpi-card.danger  { border-left-color:#e74c3c; }
    .kpi-card.info    { border-left-color:#299cdb; }
    .kpi-card.purple  { border-left-color:#7c3aed; }
    .kpi-card.cash    { border-left-color:#10b981; }
    .kpi-lbl { font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#8098bb; }
    .kpi-val { font-size:1.5rem; font-weight:800; color:#344563; line-height:1.1; margin-top:.2rem; }
    .kpi-sub { font-size:.75rem; color:#8098bb; margin-top:.15rem; }
    .kpi-icon { position:absolute; right:-.5rem; bottom:-.5rem; font-size:3.5rem; opacity:.06; color:#405189; }

    /* ── Section divider ── */
    .section-title { font-size:.72rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#8098bb; display:flex; align-items:center; gap:.5rem; margin-bottom:.85rem; padding-bottom:.4rem; border-bottom:1px solid #f0f2f7; }

    /* ── Chart cards ── */
    .chart-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
    @media(max-width:767px) { .chart-grid-2 { grid-template-columns:1fr; } }
    .chart-card { background:#fff; border-radius:.75rem; box-shadow:0 2px 12px rgba(64,81,137,.07); overflow:hidden; }
    .chart-card-header { padding:.85rem 1.25rem; border-bottom:1px solid #f0f2f7; display:flex; align-items:center; gap:.5rem; }
    .chart-card-header h6 { margin:0; font-size:.88rem; font-weight:700; color:#344563; flex-grow:1; }
    .chart-card-header i  { color:#405189; font-size:1rem; }
    .chart-card-body { padding:1.25rem; }

    /* ── Payment breakdown ── */
    .pay-breakdown { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
    @media(max-width:575px){ .pay-breakdown { grid-template-columns:1fr; } }
    .pay-item { border-radius:.65rem; padding:.85rem 1rem; text-align:center; }
    .pay-item.cash     { background:linear-gradient(135deg,rgba(16,185,129,.08),rgba(10,179,156,.08)); border:1px solid rgba(16,185,129,.2); }
    .pay-item.card     { background:linear-gradient(135deg,rgba(64,81,137,.08),rgba(41,156,219,.08)); border:1px solid rgba(64,81,137,.2); }
    .pay-item.transfer { background:linear-gradient(135deg,rgba(124,58,237,.08),rgba(64,81,137,.08)); border:1px solid rgba(124,58,237,.2); }
    .pay-item-icon { font-size:1.4rem; margin-bottom:.3rem; }
    .pay-item-lbl  { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#8098bb; }
    .pay-item-val  { font-size:1.2rem; font-weight:800; color:#344563; margin-top:.15rem; }
    .cash .pay-item-val     { color:#10b981; }
    .card .pay-item-val     { color:#405189; }
    .transfer .pay-item-val { color:#7c3aed; }

    /* ── Invoice table ── */
    .inv-table th { font-size:.67rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#8098bb; border-bottom:2px solid #e9ecef; padding:.7rem .85rem; white-space:nowrap; }
    .inv-table td { padding:.65rem .85rem; border-bottom:1px solid #f3f5f9; font-size:.84rem; vertical-align:middle; }
    .inv-table tbody tr:last-child td { border-bottom:none; }
    .inv-table tbody tr:hover { background:#f8faff; }

    /* ── Status badges ── */
    .badge-pagada    { background:#d1fae5; color:#065f46; padding:.22rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700; }
    .badge-pendiente { background:#fef9c3; color:#92400e; padding:.22rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700; }
    .badge-cancelada { background:#fee2e2; color:#991b1b; padding:.22rem .6rem; border-radius:2rem; font-size:.72rem; font-weight:700; }

    /* ── Pay chips ── */
    .pay-chip { display:inline-flex; align-items:center; gap:.2rem; font-size:.72rem; font-weight:600; padding:.15rem .5rem; border-radius:2rem; margin:.1rem; }
    .pay-chip.cash     { background:rgba(16,185,129,.1); color:#065f46; }
    .pay-chip.card     { background:rgba(64,81,137,.1); color:#405189; }
    .pay-chip.transfer { background:rgba(124,58,237,.1); color:#7c3aed; }

    /* ── Progress bar ── */
    .prog-bar-wrap { height:6px; background:#f0f2f7; border-radius:3px; overflow:hidden; margin-top:.3rem; }
    .prog-bar { height:100%; border-radius:3px; background:linear-gradient(90deg,#405189,#0ab39c); }

    /* ── Tabs ── */
    .rpt-tabs { display:flex; gap:.35rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .rpt-tab { display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1.1rem; border-radius:2rem; font-size:.84rem; font-weight:600; border:1.5px solid #e2e8f0; background:#fff; color:#8098bb; cursor:pointer; transition:all .2s; }
    .rpt-tab:hover { border-color:#405189; color:#405189; }
    .rpt-tab.active { background:linear-gradient(135deg,#405189,#0ab39c); border-color:transparent; color:#fff; box-shadow:0 4px 14px rgba(64,81,137,.25); }

    /* ── Loading ── */
    .loading-overlay { display:flex; align-items:center; justify-content:center; padding:3rem; }
    .spinner-ring { width:36px; height:36px; border:3px solid #e2e8f0; border-top-color:#405189; border-radius:50%; animation:spin .7s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }

    /* ── Animations ── */
    @keyframes fadeInUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .fade-up { animation:fadeInUp .35s ease both; }

    /* ── Toast ── */
    #toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
    .toast-item { min-width:260px; padding:.8rem 1rem; border-radius:.5rem; color:#fff; font-size:.85rem; font-weight:500; display:flex; align-items:center; gap:.5rem; box-shadow:0 4px 20px rgba(0,0,0,.18); animation:toastIn .3s ease; }
    @keyframes toastIn { from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
    .toast-error   { background:linear-gradient(135deg,#e74c3c,#c0392b); }

    /* ── Print ── */
    @media print {
        .filter-bar, .shortcut-bar, .rpt-tabs, #toast-container { display:none !important; }
        .chart-card, .kpi-card { box-shadow:none !important; border:1px solid #dee2e6 !important; }
    }

    /* ── Search in table ── */
    #inv-search { border:1.5px solid #e2e8f0; border-radius:2rem; padding:.4rem 1rem .4rem 2.2rem; font-size:.85rem; width:220px; }
    #inv-search:focus { outline:none; border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
</style>

<div id="toast-container"></div>

{{-- Breadcrumb --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-0">Cuadre de Caja</h4>
                <div class="text-muted" style="font-size:.82rem;margin-top:.15rem;">
                    <i class="ri-building-2-line me-1"></i>
                    {{ auth()->user()->branch->name ?? 'Mi Sucursal' }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light d-flex align-items-center gap-1" onclick="window.print()">
                    <i class="ri-printer-line"></i> Imprimir
                </button>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Cuadre de Caja</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Shortcuts --}}
<div class="shortcut-bar">
    <button class="btn-shortcut active" id="sc-today" onclick="setShortcut('today', this)">
        <i class="ri-sun-line me-1"></i>Hoy
    </button>
    <button class="btn-shortcut" id="sc-yesterday" onclick="setShortcut('yesterday', this)">
        Ayer
    </button>
    <button class="btn-shortcut" id="sc-week" onclick="setShortcut('week', this)">
        Esta semana
    </button>
    <button class="btn-shortcut" id="sc-month" onclick="setShortcut('month', this)">
        Este mes
    </button>
    <button class="btn-shortcut" id="sc-custom" onclick="setShortcut('custom', this)">
        <i class="ri-calendar-line me-1"></i>Personalizado
    </button>
</div>

{{-- Filter bar --}}
<div class="filter-bar" id="filter-bar">
    <div>
        <div class="form-label">Fecha desde</div>
        <input type="date" id="f-date-from" class="form-control form-control-sm" style="width:145px;">
    </div>
    <div>
        <div class="form-label">Hora desde</div>
        <input type="time" id="f-time-from" class="form-control form-control-sm" value="00:00" style="width:115px;">
    </div>
    <div>
        <div class="form-label">Fecha hasta</div>
        <input type="date" id="f-date-to" class="form-control form-control-sm" style="width:145px;">
    </div>
    <div>
        <div class="form-label">Hora hasta</div>
        <input type="time" id="f-time-to" class="form-control form-control-sm" value="23:59" style="width:115px;">
    </div>
    <div class="d-flex gap-2 align-items-end">
        <button class="btn-apply" onclick="applyFilters()">
            <i class="ri-search-line"></i> Aplicar
        </button>
        <button class="btn-clear btn" onclick="clearFilters()">
            <i class="ri-refresh-line"></i>
        </button>
    </div>
</div>

{{-- Tabs --}}
<div class="rpt-tabs">
    <button class="rpt-tab active" onclick="switchTab('summary', this)">
        <i class="ri-dashboard-line"></i> Resumen
    </button>
    <button class="rpt-tab" onclick="switchTab('invoices', this)">
        <i class="ri-file-list-3-line"></i> Detalle Facturas
    </button>
    <button class="rpt-tab" onclick="switchTab('services', this)">
        <i class="ri-stethoscope-line"></i> Servicios
    </button>
</div>

{{-- ════════════════════════════
     TAB: RESUMEN
════════════════════════════ --}}
<div id="tab-summary" class="fade-up">

    <div id="loading-summary" class="loading-overlay"><div class="spinner-ring"></div></div>
    <div id="content-summary" class="d-none">

        {{-- Facturación --}}
        <div class="section-title">
            <i class="ri-file-text-line"></i> Facturación
        </div>
        <div class="kpi-grid-4" id="kpi-facturas"></div>

        {{-- Cobros --}}
        <div class="section-title mt-2">
            <i class="ri-money-dollar-circle-line"></i> Cobros Recibidos
        </div>

        {{-- Resumen métodos de pago --}}
        <div class="chart-card mb-3">
            <div class="chart-card-header">
                <i class="ri-bank-card-line"></i>
                <h6>Desglose por Método de Pago</h6>
                <span id="total-cobrado-badge" class="badge"
                      style="background:linear-gradient(135deg,#405189,#0ab39c);color:#fff;font-size:.8rem;padding:.35rem .75rem;border-radius:2rem;">
                    Total: RD$ 0.00
                </span>
            </div>
            <div class="chart-card-body">
                <div class="pay-breakdown" id="pay-breakdown"></div>
            </div>
        </div>

        {{-- Gráficas --}}
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-pie-chart-line"></i>
                    <h6>Métodos de Pago</h6>
                </div>
                <div class="chart-card-body d-flex align-items-center justify-content-center" style="min-height:240px;">
                    <canvas id="chart-pay-method" style="max-width:240px;max-height:240px;"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-time-line"></i>
                    <h6>Facturas por Hora del Día</h6>
                </div>
                <div class="chart-card-body">
                    <canvas id="chart-by-hour" height="210"></canvas>
                </div>
            </div>
        </div>

        {{-- Por audiólogo --}}
        <div class="chart-card mb-3">
            <div class="chart-card-header">
                <i class="ri-user-star-line"></i>
                <h6>Facturación por Audiólogo</h6>
            </div>
            <div class="chart-card-body">
                <div class="table-responsive">
                    <table class="inv-table w-100" id="table-by-audiologist"></table>
                </div>
            </div>
        </div>

        {{-- Estado facturas --}}
        <div class="kpi-grid-3" id="kpi-estados"></div>

    </div>
</div>

{{-- ════════════════════════════
     TAB: DETALLE FACTURAS
════════════════════════════ --}}
<div id="tab-invoices" class="fade-up d-none">

    <div id="loading-invoices" class="loading-overlay"><div class="spinner-ring"></div></div>
    <div id="content-invoices" class="d-none">

        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-file-list-3-line"></i>
                <h6>Facturas del Período</h6>
                <div class="ms-auto position-relative">
                    <i class="ri-search-line" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#8098bb;font-size:.9rem;"></i>
                    <input type="text" id="inv-search" placeholder="Buscar..." oninput="filterInvTable()">
                </div>
                <span id="inv-count-badge" class="badge bg-primary-subtle text-primary ms-2" style="font-size:.78rem;">0 facturas</span>
            </div>
            <div class="table-responsive">
                <table class="inv-table w-100">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Factura</th>
                            <th>Paciente</th>
                            <th>Audiólogo</th>
                            <th>Servicios</th>
                            <th>Subtotal</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="inv-tbody"></tbody>
                </table>
            </div>
            <div id="inv-empty" class="text-center py-4 d-none">
                <i class="ri-file-list-3-line d-block text-muted mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0" style="font-size:.88rem;">No hay facturas en este período.</p>
            </div>
        </div>

    </div>
</div>

{{-- ════════════════════════════
     TAB: SERVICIOS
════════════════════════════ --}}
<div id="tab-services" class="fade-up d-none">

    <div id="loading-services" class="loading-overlay"><div class="spinner-ring"></div></div>
    <div id="content-services" class="d-none">

        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-bar-chart-horizontal-line"></i>
                    <h6>Servicios por Cantidad</h6>
                </div>
                <div class="chart-card-body">
                    <canvas id="chart-svc-qty" height="260"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <i class="ri-money-dollar-circle-line"></i>
                    <h6>Servicios por Ingresos</h6>
                </div>
                <div class="chart-card-body">
                    <canvas id="chart-svc-total" height="260"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-stethoscope-line"></i>
                <h6>Detalle de Servicios Cobrados</h6>
            </div>
            <div class="table-responsive">
                <table class="inv-table w-100" id="table-services"></table>
            </div>
        </div>

    </div>
</div>

</div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// ── Config ───────────────────────────────────────────────
const URLS = {
    summary : "{{ route('receptionist.reports.summary') }}",
    invoices: "{{ route('receptionist.reports.invoices') }}",
    services: "{{ route('receptionist.reports.services') }}",
};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color       = '#8098bb';

const COLORS = ['#405189','#0ab39c','#f59e0b','#e74c3c','#299cdb','#7c3aed','#10b981','#f97316'];
const charts = {};

let activeTab  = 'summary';
let loadedTabs = {};
let allInvoices = [];

// ── Date helpers ──────────────────────────────────────────
function today()     { return new Date().toISOString().split('T')[0]; }
function yesterday() { const d = new Date(); d.setDate(d.getDate()-1); return d.toISOString().split('T')[0]; }
function weekStart() { const d = new Date(); d.setDate(d.getDate() - d.getDay() + 1); return d.toISOString().split('T')[0]; }
function monthStart(){ const d = new Date(); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-01'; }

function setShortcut(type, btn) {
    document.querySelectorAll('.btn-shortcut').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filterBar = document.getElementById('filter-bar');

    switch (type) {
        case 'today':
            setDates(today(), today(), '00:00', '23:59');
            filterBar.style.display = 'none';
            break;
        case 'yesterday':
            setDates(yesterday(), yesterday(), '00:00', '23:59');
            filterBar.style.display = 'none';
            break;
        case 'week':
            setDates(weekStart(), today(), '00:00', '23:59');
            filterBar.style.display = 'none';
            break;
        case 'month':
            setDates(monthStart(), today(), '00:00', '23:59');
            filterBar.style.display = 'none';
            break;
        case 'custom':
            filterBar.style.display = 'flex';
            return; // No auto-apply
    }
    applyFilters();
}

function setDates(from, to, tFrom, tTo) {
    document.getElementById('f-date-from').value = from;
    document.getElementById('f-date-to').value   = to;
    document.getElementById('f-time-from').value = tFrom;
    document.getElementById('f-time-to').value   = tTo;
}

// ── Filters ──────────────────────────────────────────────
function getFilters() {
    return {
        date_from: document.getElementById('f-date-from').value,
        date_to  : document.getElementById('f-date-to').value,
        time_from: document.getElementById('f-time-from').value + ':00',
        time_to  : document.getElementById('f-time-to').value + ':59',
    };
}

function buildParams(filters) {
    const p = new URLSearchParams();
    Object.entries(filters).forEach(([k,v]) => { if (v) p.set(k, v); });
    return p.toString();
}

function applyFilters() {
    loadedTabs = {};
    loadTab(activeTab);
}

function clearFilters() {
    setDates(today(), today(), '00:00', '23:59');
    applyFilters();
}

// ── Tabs ─────────────────────────────────────────────────
function switchTab(tab, btn) {
    ['summary','invoices','services'].forEach(t => {
        document.getElementById('tab-' + t).classList.add('d-none');
    });
    document.querySelectorAll('.rpt-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.remove('d-none');
    btn.classList.add('active');
    activeTab = tab;
    if (!loadedTabs[tab]) loadTab(tab);
}

async function loadTab(tab) {
    showLoading(tab, true);
    hideContent(tab);
    try {
        const params = buildParams(getFilters());
        const res    = await fetch(URLS[tab] + (params ? '?' + params : ''), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
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
    const el = document.getElementById('loading-' + tab);
    if (el) el.style.display = show ? 'flex' : 'none';
}
function hideContent(tab) {
    const el = document.getElementById('content-' + tab);
    if (el) el.classList.add('d-none');
}
function showContent(tab) {
    const el = document.getElementById('content-' + tab);
    if (el) el.classList.remove('d-none');
}

// ── Render dispatcher ────────────────────────────────────
function renderTab(tab, d) {
    if (tab === 'summary')  renderSummary(d);
    if (tab === 'invoices') renderInvoices(d);
    if (tab === 'services') renderServices(d);
}

// ── RENDER: Resumen ───────────────────────────────────────
function renderSummary(d) {
    const k = d.kpis;

    // KPIs facturación
    document.getElementById('kpi-facturas').innerHTML = `
        ${kpiCard('Total Facturas',    k.total_facturas,           'ri-file-text-line',          'primary')}
        ${kpiCard('Subtotal (RD$)',     'RD$ '+k.subtotal,          'ri-receipt-line',            'info')}
        ${kpiCard('Desc. Seguros',      'RD$ '+k.descuentos,        'ri-shield-check-line',       'warning')}
        ${kpiCard('Total Facturado',    'RD$ '+k.total_facturado,   'ri-money-dollar-circle-line','success','Mayor ingreso del período')}
    `;

    // KPIs estados
    document.getElementById('kpi-estados').innerHTML = `
        ${kpiCard('Pagadas',    k.pagadas,    'ri-checkbox-circle-line', 'success')}
        ${kpiCard('Pendientes', k.pendientes, 'ri-time-line',            'warning')}
        ${kpiCard('Canceladas', k.canceladas, 'ri-close-circle-line',    'danger')}
    `;

    // Total cobrado badge
    document.getElementById('total-cobrado-badge').textContent = 'Total cobrado: RD$ ' + k.total_cobrado;

    // Desglose métodos de pago
    document.getElementById('pay-breakdown').innerHTML = `
        <div class="pay-item cash">
            <div class="pay-item-icon text-success"><i class="ri-money-bill-line"></i></div>
            <div class="pay-item-lbl">Efectivo</div>
            <div class="pay-item-val">RD$ ${k.efectivo}</div>
        </div>
        <div class="pay-item card">
            <div class="pay-item-icon text-primary"><i class="ri-bank-card-line"></i></div>
            <div class="pay-item-lbl">Tarjeta</div>
            <div class="pay-item-val">RD$ ${k.tarjeta}</div>
        </div>
        <div class="pay-item transfer">
            <div class="pay-item-icon text-purple" style="color:#7c3aed;"><i class="ri-exchange-dollar-line"></i></div>
            <div class="pay-item-lbl">Transferencia</div>
            <div class="pay-item-val">RD$ ${k.transferencia}</div>
        </div>
    `;

    // Gráfica métodos de pago (donut)
    makeDonutChart('chart-pay-method',
        ['Efectivo','Tarjeta','Transferencia'],
        [parseFloat(k.efectivo.replace(/,/g,'')), parseFloat(k.tarjeta.replace(/,/g,'')), parseFloat(k.transferencia.replace(/,/g,''))],
        ['#10b981','#405189','#7c3aed']
    );

    // Por hora
    const hours = Array.from({length:24}, (_,i) => i);
    const hourMap = {};
    d.by_hour.forEach(r => { hourMap[r.hour] = r.count; });
    makeBarChart('chart-by-hour',
        hours.map(h => String(h).padStart(2,'0') + ':00'),
        hours.map(h => hourMap[h] ?? 0),
        'Facturas', '#405189'
    );

    // Por audiólogo
    const maxAud = Math.max(...d.by_audiologist.map(r => parseFloat(r.total)), 1);
    document.getElementById('table-by-audiologist').innerHTML =
        '<thead><tr>' +
            ['Audiólogo','Facturas','Total','Participación'].map(c => `<th>${c}</th>`).join('') +
        '</tr></thead>' +
        '<tbody>' + d.by_audiologist.map((r,i) => `
            <tr>
                <td>
                    <span style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#405189,#0ab39c);
                        color:#fff;font-size:.68rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;margin-right:.5rem;">
                        ${i+1}
                    </span>
                    <span class="fw-semibold">${r.audiologist}</span>
                </td>
                <td>${r.count}</td>
                <td class="fw-bold text-success">RD$ ${fmtNum(r.total)}</td>
                <td style="min-width:130px;">
                    <div class="prog-bar-wrap">
                        <div class="prog-bar" style="width:${Math.round(parseFloat(r.total)/maxAud*100)}%"></div>
                    </div>
                    <span style="font-size:.72rem;color:#8098bb;">${Math.round(parseFloat(r.total)/maxAud*100)}%</span>
                </td>
            </tr>`).join('') +
        '</tbody>';
}

// ── RENDER: Detalle facturas ──────────────────────────────
function renderInvoices(d) {
    allInvoices = d.invoices;
    renderInvTable(allInvoices);
}

function renderInvTable(invoices) {
    const tbody = document.getElementById('inv-tbody');
    const empty = document.getElementById('inv-empty');
    const badge = document.getElementById('inv-count-badge');

    badge.textContent = invoices.length + ' factura' + (invoices.length !== 1 ? 's' : '');

    if (!invoices.length) {
        tbody.innerHTML = '';
        empty.classList.remove('d-none');
        return;
    }
    empty.classList.add('d-none');

    tbody.innerHTML = invoices.map(inv => {
        const payChips = [
            parseFloat(inv.efectivo) > 0
                ? `<span class="pay-chip cash"><i class="ri-money-bill-line"></i>RD$ ${inv.efectivo}</span>` : '',
            parseFloat(inv.tarjeta) > 0
                ? `<span class="pay-chip card"><i class="ri-bank-card-line"></i>RD$ ${inv.tarjeta}</span>` : '',
            parseFloat(inv.transferencia) > 0
                ? `<span class="pay-chip transfer"><i class="ri-exchange-dollar-line"></i>RD$ ${inv.transferencia}</span>` : '',
        ].filter(Boolean).join('');

        const statusBadge = {
            pagada   : `<span class="badge-pagada">Pagada</span>`,
            pendiente: `<span class="badge-pendiente">Pendiente</span>`,
            cancelada: `<span class="badge-cancelada">Cancelada</span>`,
        }[inv.status] || inv.status;

        return `<tr data-search="${(inv.patient+inv.number+inv.audiologist+inv.services).toLowerCase()}">
            <td style="font-family:monospace;color:#8098bb;">${inv.time}</td>
            <td><span style="font-family:monospace;font-weight:700;color:#405189;">${inv.number}</span></td>
            <td>
                <div class="fw-semibold" style="font-size:.87rem;">${inv.patient}</div>
                <div class="text-muted" style="font-size:.75rem;">${inv.cedula}</div>
            </td>
            <td style="font-size:.84rem;">${inv.audiologist}</td>
            <td style="font-size:.78rem;color:#6b7a99;max-width:160px;">${inv.services || '—'}</td>
            <td>RD$ ${inv.subtotal}</td>
            <td class="text-warning">RD$ ${inv.descuento}</td>
            <td class="fw-bold text-success">RD$ ${inv.total}</td>
            <td>${payChips || '<span class="text-muted" style="font-size:.78rem;">—</span>'}</td>
            <td>${statusBadge}</td>
        </tr>`;
    }).join('');
}

function filterInvTable() {
    const q = document.getElementById('inv-search').value.toLowerCase().trim();
    if (!q) { renderInvTable(allInvoices); return; }
    renderInvTable(allInvoices.filter(inv =>
        (inv.patient + inv.number + inv.audiologist + inv.services + inv.cedula)
            .toLowerCase().includes(q)
    ));
}

// ── RENDER: Servicios ────────────────────────────────────
function renderServices(d) {
    const svcs = d.services;

    if (!svcs.length) {
        document.getElementById('content-services').innerHTML =
            '<div class="text-center py-5"><i class="ri-stethoscope-line d-block text-muted mb-2" style="font-size:3rem;opacity:.3;"></i><p class="text-muted">No hay servicios en este período.</p></div>';
        return;
    }

    // Gráfica por cantidad
    makeHBarChart('chart-svc-qty',
        svcs.map(s => s.service),
        svcs.map(s => parseInt(s.qty)),
        'Cantidad', '#0ab39c'
    );

    // Gráfica por ingresos
    makeHBarChart('chart-svc-total',
        svcs.map(s => s.service),
        svcs.map(s => parseFloat(s.subtotal)),
        'RD$', '#405189'
    );

    // Tabla detalle
    const maxSvc = Math.max(...svcs.map(s => parseFloat(s.subtotal)), 1);
    document.getElementById('table-services').innerHTML =
        '<thead><tr>' +
            ['#','Servicio','Cant.','Paga Paciente','Cubre Seguro','Total','%'].map(c => `<th>${c}</th>`).join('') +
        '</tr></thead>' +
        '<tbody>' + svcs.map((s,i) => `
            <tr>
                <td><span style="width:22px;height:22px;border-radius:50%;background:${['#fef9c3','#f3f4f6','#fef0e7'][i] || '#f0f4ff'};
                    color:${['#92400e','#6b7280','#92400e'][i] || '#405189'};font-size:.7rem;font-weight:700;
                    display:inline-flex;align-items:center;justify-content:center;">${i+1}</span>
                </td>
                <td class="fw-semibold">${s.service}</td>
                <td>${s.qty}</td>
                <td class="fw-bold text-success">RD$ ${fmtNum(s.patient_total)}</td>
                <td class="text-info">RD$ ${fmtNum(s.insurance_total)}</td>
                <td class="fw-bold">RD$ ${fmtNum(s.subtotal)}</td>
                <td style="min-width:120px;">
                    <div class="prog-bar-wrap">
                        <div class="prog-bar" style="width:${Math.round(parseFloat(s.subtotal)/maxSvc*100)}%"></div>
                    </div>
                    <span style="font-size:.72rem;color:#8098bb;">${Math.round(parseFloat(s.subtotal)/maxSvc*100)}%</span>
                </td>
            </tr>`).join('') +
        '</tbody>';
}

// ── Chart helpers ─────────────────────────────────────────
function destroyChart(id) {
    if (charts[id]) { charts[id].destroy(); delete charts[id]; }
}

function makeBarChart(id, labels, data, label, color) {
    destroyChart(id);
    const ctx = document.getElementById(id)?.getContext('2d');
    if (!ctx) return;
    charts[id] = new Chart(ctx, {
        type:'bar',
        data:{ labels, datasets:[{ label, data, backgroundColor:color+'cc', borderColor:color, borderWidth:1.5, borderRadius:4 }] },
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
    return `
    <div class="kpi-card ${type}">
        <div class="kpi-lbl">${label}</div>
        <div class="kpi-val">${value}</div>
        ${sub ? `<div class="kpi-sub">${sub}</div>` : ''}
        <i class="${icon} kpi-icon"></i>
    </div>`;
}

function fmtNum(n) {
    return parseFloat(n||0).toLocaleString('es-DO',{minimumFractionDigits:2,maximumFractionDigits:2});
}

// ── Toast ─────────────────────────────────────────────────
function showToast(msg, type) {
    const div = document.createElement('div');
    div.className = 'toast-item toast-' + (type || 'error');
    div.innerHTML = `<i class="ri-error-warning-line fs-16"></i>${msg}`;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(() => { div.style.opacity='0'; div.style.transition='opacity .4s'; setTimeout(()=>div.remove(),400); }, 3500);
}

// ── Boot ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Cargar con "Hoy" por defecto
    setDates(today(), today(), '00:00', '23:59');
    document.getElementById('filter-bar').style.display = 'none';
    loadTab('summary');
});
</script>
@endpush
</x-app-layout>
