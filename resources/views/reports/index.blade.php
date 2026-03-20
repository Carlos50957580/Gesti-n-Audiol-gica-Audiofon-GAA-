<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
    /* ── Tabs ── */
    .rpt-tabs { display:flex; gap:.35rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .rpt-tab {
        display:inline-flex; align-items:center; gap:.5rem;
        padding:.55rem 1.2rem; border-radius:2rem; font-size:.85rem; font-weight:600;
        border:1.5px solid #e2e8f0; background:#fff; color:#8098bb;
        cursor:pointer; transition:all .2s;
    }
    .rpt-tab:hover { border-color:#405189; color:#405189; background:#f0f4ff; }
    .rpt-tab.active {
        background:linear-gradient(135deg,#405189,#0ab39c);
        border-color:transparent; color:#fff;
        box-shadow:0 4px 14px rgba(64,81,137,.25);
    }
    .rpt-tab i { font-size:1rem; }

    /* ── Filter bar ── */
    .filter-bar {
        background:#fff; border-radius:.75rem; padding:1rem 1.25rem;
        box-shadow:0 2px 12px rgba(64,81,137,.07);
        display:flex; flex-wrap:wrap; gap:.75rem; align-items:flex-end;
        margin-bottom:1.5rem;
    }
    .filter-bar .form-label { font-size:.72rem; font-weight:700; color:#8098bb; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem; }
    .filter-bar .form-control,
    .filter-bar .form-select { border:1.5px solid #e2e8f0; border-radius:.5rem; font-size:.87rem; }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
    .btn-apply {
        background:linear-gradient(135deg,#405189,#0ab39c);
        border:none; color:#fff; border-radius:.5rem;
        padding:.52rem 1.25rem; font-weight:700; font-size:.87rem;
        display:inline-flex; align-items:center; gap:.4rem; white-space:nowrap;
        transition:opacity .2s;
    }
    .btn-apply:hover { opacity:.9; color:#fff; }
    .btn-clear { border:1.5px solid #e2e8f0; background:#f8faff; color:#8098bb; border-radius:.5rem; padding:.52rem .9rem; font-size:.87rem; font-weight:600; transition:all .15s; white-space:nowrap; }
    .btn-clear:hover { background:#e2e8f0; color:#344563; }

    /* ── KPI cards ── */
    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .kpi-card {
        background:#fff; border-radius:.75rem; padding:1rem 1.25rem;
        box-shadow:0 2px 12px rgba(64,81,137,.07); position:relative; overflow:hidden;
        border-left:4px solid transparent;
    }
    .kpi-card.primary   { border-left-color:#405189; }
    .kpi-card.success   { border-left-color:#0ab39c; }
    .kpi-card.warning   { border-left-color:#f59e0b; }
    .kpi-card.danger    { border-left-color:#e74c3c; }
    .kpi-card.info      { border-left-color:#299cdb; }
    .kpi-card.purple    { border-left-color:#7c3aed; }
    .kpi-lbl { font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#8098bb; }
    .kpi-val { font-size:1.65rem; font-weight:800; color:#344563; line-height:1.1; margin-top:.2rem; }
    .kpi-sub { font-size:.75rem; color:#8098bb; margin-top:.15rem; }
    .kpi-icon { position:absolute; right:-.5rem; bottom:-.5rem; font-size:3.5rem; opacity:.06; color:#405189; }

    /* ── Chart cards ── */
    .chart-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
    .chart-grid-1 { margin-bottom:1.25rem; }
    @media(max-width:767px) { .chart-grid-2 { grid-template-columns:1fr; } }
    .chart-card {
        background:#fff; border-radius:.75rem;
        box-shadow:0 2px 12px rgba(64,81,137,.07); overflow:hidden;
    }
    .chart-card-header {
        padding:.85rem 1.25rem; border-bottom:1px solid #f0f2f7;
        display:flex; align-items:center; gap:.5rem;
    }
    .chart-card-header h6 { margin:0; font-size:.88rem; font-weight:700; color:#344563; flex-grow:1; }
    .chart-card-header i  { color:#405189; font-size:1rem; }
    .chart-card-body { padding:1.25rem; }

    /* ── Table inside card ── */
    .rpt-table { width:100%; border-collapse:collapse; }
    .rpt-table th { font-size:.68rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#8098bb; border-bottom:2px solid #e9ecef; padding:.65rem .85rem; white-space:nowrap; }
    .rpt-table td { padding:.62rem .85rem; border-bottom:1px solid #f3f5f9; font-size:.85rem; vertical-align:middle; }
    .rpt-table tbody tr:last-child td { border-bottom:none; }
    .rpt-table tbody tr:hover { background:#f8faff; }
    .rank-badge { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; font-size:.7rem; font-weight:700; background:#f0f4ff; color:#405189; }
    .rank-badge.gold   { background:#fef9c3; color:#92400e; }
    .rank-badge.silver { background:#f3f4f6; color:#6b7280; }
    .rank-badge.bronze { background:#fef0e7; color:#92400e; }

    /* ── Progress bar ── */
    .prog-bar-wrap { height:6px; background:#f0f2f7; border-radius:3px; overflow:hidden; margin-top:.3rem; }
    .prog-bar { height:100%; border-radius:3px; background:linear-gradient(90deg,#405189,#0ab39c); transition:width .6s ease; }

    /* ── Loading overlay ── */
    .tab-pane { position:relative; min-height:200px; }
    .loading-overlay {
        position:absolute; inset:0; background:rgba(255,255,255,.8);
        display:flex; align-items:center; justify-content:center;
        z-index:10; border-radius:.75rem;
    }
    .spinner-ring { width:40px; height:40px; border:3px solid #e2e8f0; border-top-color:#405189; border-radius:50%; animation:spin .7s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }

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
            <h4 class="mb-0">Reportes</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Reportes</li>
            </ol>
        </div>
    </div>
</div>

{{-- Filter bar --}}
<div class="filter-bar">
    <div>
        <div class="form-label">Desde</div>
        <input type="date" id="f-date-from" class="form-control form-control-sm" style="width:145px;">
    </div>
    <div>
        <div class="form-label">Hasta</div>
        <input type="date" id="f-date-to" class="form-control form-control-sm" style="width:145px;">
    </div>
    <div>
        <div class="form-label">Sucursal</div>
        <select id="f-branch" class="form-select form-select-sm" style="width:170px;">
            <option value="">Todas</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <div class="form-label">Audiólogo</div>
        <select id="f-audiologist" class="form-select form-select-sm" style="width:190px;">
            <option value="">Todos</option>
            @foreach($audiologists as $a)
                <option value="{{ $a->id }}">{{ $a->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="d-flex gap-2 align-items-end">
        <button class="btn-apply" onclick="applyFilters()">
            <i class="ri-search-line"></i> Aplicar
        </button>
        <button class="btn-clear btn" onclick="clearFilters()">
            <i class="ri-refresh-line"></i> Limpiar
        </button>
    </div>
</div>

{{-- Tabs --}}
<div class="rpt-tabs">
    <button class="rpt-tab active" onclick="switchTab('invoices', this)">
        <i class="ri-file-text-line"></i> Facturación
    </button>

    <button class="rpt-tab" onclick="switchTab('byuser', this)">
    <i class="ri-user-2-line"></i> Por Recepcionista
</button>

    <button class="rpt-tab" onclick="switchTab('appointments', this)">
        <i class="ri-calendar-check-line"></i> Citas
    </button>
    <button class="rpt-tab" onclick="switchTab('clinical', this)">
        <i class="ri-stethoscope-line"></i> Historias Clínicas
    </button>
    <button class="rpt-tab" onclick="switchTab('patients', this)">
        <i class="ri-group-line"></i> Pacientes
    </button>
</div>

{{-- ════════════════════════════
     TAB: FACTURACIÓN
════════════════════════════ --}}
<div id="tab-invoices" class="tab-pane fade-up">
    <div id="loading-invoices" class="loading-overlay"><div class="spinner-ring"></div></div>

    <div class="kpi-grid" id="kpi-invoices"></div>

    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-bar-chart-2-line"></i>
                <h6>Ingresos por Mes</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-inv-month" height="200"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-pie-chart-line"></i>
                <h6>Estado de Facturas</h6>
            </div>
            <div class="chart-card-body d-flex align-items-center justify-content-center" style="min-height:240px;">
                <canvas id="chart-inv-status" style="max-width:240px;max-height:240px;"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-building-2-line"></i>
                <h6>Facturación por Sucursal</h6>
            </div>
            <div class="chart-card-body">
                <div class="table-responsive">
                    <table class="rpt-table" id="table-inv-branch"></table>
                </div>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-stethoscope-line"></i>
                <h6>Servicios más Facturados</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-inv-services" height="220"></canvas></div>
        </div>
    </div>

    <div class="chart-card chart-grid-1">
        <div class="chart-card-header">
            <i class="ri-shield-check-line"></i>
            <h6>Facturación por Seguro</h6>
        </div>
        <div class="chart-card-body">
            <div class="table-responsive">
                <table class="rpt-table" id="table-inv-insurance"></table>
            </div>
        </div>
    </div>
</div>
{{-- ↑ CIERRE CORRECTO de tab-invoices --}}

{{-- ════════════════════════════
     TAB: POR RECEPCIONISTA
════════════════════════════ --}}
<div id="tab-byuser" class="tab-pane fade-up d-none">
    <div id="loading-byuser" class="loading-overlay"><div class="spinner-ring"></div></div>

    <div style="background:#f8faff;border:1px solid #e2e8f0;border-radius:.65rem;padding:.85rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="font-size:.7rem;font-weight:700;color:#8098bb;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.25rem;">Recepcionista</div>
            <select id="f-user" class="form-select form-select-sm" style="width:220px;border:1.5px solid #e2e8f0;border-radius:.5rem;">
                <option value="">Todos</option>
                @foreach($receptionists as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-top:1.2rem;">
            <button class="btn-apply" onclick="applyByUser()">
                <i class="ri-search-line"></i> Filtrar
            </button>
        </div>
        <div style="margin-top:1.2rem;margin-left:auto;">
            <button class="btn btn-sm btn-light d-flex align-items-center gap-1" onclick="exportByUser()"
                    style="border:1.5px solid #e2e8f0;border-radius:.45rem;font-size:.83rem;font-weight:600;">
                <i class="ri-download-line"></i> Exportar CSV
            </button>
        </div>
    </div>

    <div class="kpi-grid" id="kpi-byuser"></div>

    <div class="chart-card chart-grid-1">
        <div class="chart-card-header">
            <i class="ri-user-2-line"></i>
            <h6>Resumen por Recepcionista</h6>
        </div>
        <div class="chart-card-body">
            <div class="table-responsive">
                <table class="rpt-table" id="table-byuser-summary"></table>
            </div>
        </div>
    </div>

    <div class="chart-card chart-grid-1">
        <div class="chart-card-header">
            <i class="ri-file-list-3-line"></i>
            <h6>Detalle de Facturas</h6>
            <span id="byuser-inv-count" class="badge bg-primary-subtle text-primary ms-auto" style="font-size:.78rem;"></span>
            <div class="position-relative ms-2">
                <i class="ri-search-line" style="position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:#8098bb;font-size:.85rem;"></i>
                <input type="text" id="byuser-search" placeholder="Buscar..."
                       oninput="filterByUserTable()"
                       style="border:1.5px solid #e2e8f0;border-radius:2rem;padding:.3rem .8rem .3rem 2rem;font-size:.82rem;width:190px;">
            </div>
        </div>
        <div class="table-responsive">
            <table class="rpt-table w-100">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Factura</th>
                        <th>Recepcionista</th>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Sucursal</th>
                        <th>Audiólogo</th>
                        <th>Seguro</th>
                        <th>Subtotal</th>
                        <th>Desc.</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="tbody-byuser"></tbody>
            </table>
        </div>
        <div id="byuser-empty" class="text-center py-4 d-none">
            <i class="ri-file-list-3-line d-block text-muted mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="text-muted mb-0" style="font-size:.88rem;">No hay facturas en este período.</p>
        </div>
    </div>
</div>
{{-- ↑ CIERRE CORRECTO de tab-byuser --}}


{{-- ════════════════════════════
     TAB: CITAS
════════════════════════════ --}}
<div id="tab-appointments" class="tab-pane fade-up d-none">
    <div id="loading-appointments" class="loading-overlay"><div class="spinner-ring"></div></div>

    <div class="kpi-grid" id="kpi-appointments"></div>

    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-line-chart-line"></i>
                <h6>Citas por Mes</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-apt-month" height="200"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-bar-chart-horizontal-line"></i>
                <h6>Citas por Día de la Semana</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-apt-weekday" height="200"></canvas></div>
        </div>
    </div>

    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-user-star-line"></i>
                <h6>Top Audiólogos por Citas</h6>
            </div>
            <div class="chart-card-body">
                <div class="table-responsive">
                    <table class="rpt-table" id="table-apt-audiologists"></table>
                </div>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-building-2-line"></i>
                <h6>Citas por Sucursal</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-apt-branch" height="220"></canvas></div>
        </div>
    </div>
</div>

{{-- ════════════════════════════
     TAB: HISTORIAS CLÍNICAS
════════════════════════════ --}}
<div id="tab-clinical" class="tab-pane fade-up d-none">
    <div id="loading-clinical" class="loading-overlay"><div class="spinner-ring"></div></div>

    <div class="kpi-grid" id="kpi-clinical"></div>

    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-line-chart-line"></i>
                <h6>HCs Completadas por Mes</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-hc-month" height="200"></canvas></div>
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
                <i class="ri-user-star-line"></i>
                <h6>Top Audiólogos por HCs Completadas</h6>
            </div>
            <div class="chart-card-body">
                <div class="table-responsive">
                    <table class="rpt-table" id="table-hc-audiologists"></table>
                </div>
            </div>
        </div>
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
    </div>
</div>

{{-- ════════════════════════════
     TAB: PACIENTES
════════════════════════════ --}}
<div id="tab-patients" class="tab-pane fade-up d-none">
    <div id="loading-patients" class="loading-overlay"><div class="spinner-ring"></div></div>

    <div class="kpi-grid" id="kpi-patients"></div>

    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-line-chart-line"></i>
                <h6>Nuevos Pacientes por Mes</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-pat-month" height="200"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-pie-chart-line"></i>
                <h6>Pacientes por Seguro</h6>
            </div>
            <div class="chart-card-body d-flex align-items-center justify-content-center" style="min-height:240px;">
                <canvas id="chart-pat-insurance" style="max-width:240px;max-height:240px;"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-building-2-line"></i>
                <h6>Pacientes por Sucursal</h6>
            </div>
            <div class="chart-card-body"><canvas id="chart-pat-branch" height="220"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="chart-card-header">
                <i class="ri-vip-crown-line"></i>
                <h6>Pacientes con más Visitas</h6>
            </div>
            <div class="chart-card-body">
                <div class="table-responsive">
                    <table class="rpt-table" id="table-pat-top"></table>
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
// ── Config ───────────────────────────────────────────────
const URLS = {
    invoices    : "{{ route('reports.invoices') }}",
    appointments: "{{ route('reports.appointments') }}",
    clinical    : "{{ route('reports.clinical-records') }}",
    patients    : "{{ route('reports.patients') }}",
    byuser      : "{{ route('reports.by-user') }}",
};
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Chart.js defaults
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color       = '#8098bb';
Chart.defaults.plugins.legend.labels.boxWidth = 12;

const COLORS = {
    primary  : '#405189',
    teal     : '#0ab39c',
    warning  : '#f59e0b',
    danger   : '#e74c3c',
    info     : '#299cdb',
    purple   : '#7c3aed',
    gradient1: ['#405189','#0ab39c','#f59e0b','#e74c3c','#299cdb','#7c3aed','#10b981','#f97316'],
};

// Chart instances (para destruir antes de recrear)
const charts = {};

let activeTab    = 'invoices';
let loadedTabs   = {};

// ── Filters ──────────────────────────────────────────────
function getFilters() {
    return {
        date_from     : document.getElementById('f-date-from').value,
        date_to       : document.getElementById('f-date-to').value,
        branch_id     : document.getElementById('f-branch').value,
        audiologist_id: document.getElementById('f-audiologist').value,
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
    ['f-date-from','f-date-to','f-branch','f-audiologist']
        .forEach(id => { const el = document.getElementById(id); el.value = ''; });
    applyFilters();
}

// ── Tabs ─────────────────────────────────────────────────
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('d-none'));
    document.querySelectorAll('.rpt-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.remove('d-none');
    btn.classList.add('active');
    activeTab = tab;
    if (!loadedTabs[tab]) loadTab(tab);
}

async function loadTab(tab) {
    showLoading(tab, true);
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
    }
}

function showLoading(tab, show) {
    const el = document.getElementById('loading-' + tab);
    if (el) el.style.display = show ? 'flex' : 'none';
}

// ── Render dispatcher ────────────────────────────────────
function renderTab(tab, d) {
    if (tab === 'invoices')     renderInvoices(d);
    if (tab === 'appointments') renderAppointments(d);
    if (tab === 'clinical')     renderClinical(d);
    if (tab === 'patients')     renderPatients(d);
    if (tab === 'byuser')       renderByUser(d);
}

function applyByUser() {
    loadedTabs['byuser'] = false;
    loadTab('byuser');
}

let allByUserInvoices = [];

function renderByUser(d) {
    const t = d.totals;

    // ── KPIs globales ────────────────────────────────────
    document.getElementById('kpi-byuser').innerHTML = `
        ${kpiCard('Total Facturas',    t.total,                              'ri-file-text-line',          'primary')}
        ${kpiCard('Total Cobrado',     'RD$ '+fmtNum(t.total_cobrado),      'ri-money-dollar-circle-line','success')}
        ${kpiCard('Pagadas',           t.pagadas,                            'ri-checkbox-circle-line',    'success')}
        ${kpiCard('Pendientes',        t.pendientes,                         'ri-time-line',               'warning')}
        ${kpiCard('Canceladas',        t.canceladas,                         'ri-close-circle-line',       'danger')}
        ${kpiCard('Desc. Seguros',     'RD$ '+fmtNum(t.total_descuentos),   'ri-shield-check-line',       'info')}
    `;

    // ── Resumen por recepcionista ─────────────────────────
    const maxCobrado = Math.max(...d.kpi_by_user.map(r => parseFloat(r.total_cobrado)), 1);
    document.getElementById('table-byuser-summary').innerHTML =
        tableHead(['#','Recepcionista','Facturas','Pagadas','Pendientes','Canceladas','Total Cobrado','%']) +
        '<tbody>' + d.kpi_by_user.map((r, i) => `
            <tr>
                <td><span class="rank-badge ${rankClass(i)}">${i+1}</span></td>
                <td class="fw-semibold">${r.user_name}</td>
                <td>${r.total_facturas}</td>
                <td><span style="color:#0ab39c;font-weight:700;">${r.pagadas}</span></td>
                <td><span style="color:#f59e0b;font-weight:700;">${r.pendientes}</span></td>
                <td><span style="color:#e74c3c;font-weight:700;">${r.canceladas}</span></td>
                <td class="fw-bold text-success">RD$ ${fmtNum(r.total_cobrado)}</td>
                <td style="min-width:120px;">
                    <div class="prog-bar-wrap">
                        <div class="prog-bar" style="width:${Math.round(parseFloat(r.total_cobrado)/maxCobrado*100)}%"></div>
                    </div>
                    <span style="font-size:.72rem;color:#8098bb;">${Math.round(parseFloat(r.total_cobrado)/maxCobrado*100)}%</span>
                </td>
            </tr>`).join('') + '</tbody>';

    // ── Detalle facturas ──────────────────────────────────
    allByUserInvoices = d.invoices;
    renderByUserTable(allByUserInvoices);
}

function renderByUserTable(invoices) {
    const tbody = document.getElementById('tbody-byuser');
    const empty = document.getElementById('byuser-empty');
    const badge = document.getElementById('byuser-inv-count');

    badge.textContent = invoices.length + ' factura' + (invoices.length !== 1 ? 's' : '');

    if (!invoices.length) {
        tbody.innerHTML = '';
        empty.classList.remove('d-none');
        return;
    }
    empty.classList.add('d-none');

    const statusBadge = {
        pagada   : '<span style="background:#d1fae5;color:#065f46;padding:.18rem .55rem;border-radius:2rem;font-size:.72rem;font-weight:700;">Pagada</span>',
        pendiente: '<span style="background:#fef9c3;color:#92400e;padding:.18rem .55rem;border-radius:2rem;font-size:.72rem;font-weight:700;">Pendiente</span>',
        cancelada: '<span style="background:#fee2e2;color:#991b1b;padding:.18rem .55rem;border-radius:2rem;font-size:.72rem;font-weight:700;">Cancelada</span>',
    };

    tbody.innerHTML = invoices.map(inv => `
        <tr data-search="${(inv.receptionist+inv.patient+inv.invoice_number+inv.cedula+inv.branch).toLowerCase()}">
            <td style="font-size:.82rem;color:#8098bb;white-space:nowrap;">
                ${inv.created_at ? inv.created_at.substring(0,10).split('-').reverse().join('/') : '—'}
                <br><span style="font-size:.75rem;">${inv.created_at ? inv.created_at.substring(11,16) : ''}</span>
            </td>
            <td style="font-family:monospace;font-weight:700;color:#405189;">${inv.invoice_number}</td>
            <td class="fw-semibold" style="font-size:.84rem;">${inv.receptionist}</td>
            <td>
                <div style="font-size:.84rem;font-weight:600;">${inv.patient}</div>
                <div style="font-size:.75rem;color:#8098bb;">${inv.cedula}</div>
            </td>
            <td style="font-size:.82rem;color:#8098bb;">${inv.cedula}</td>
            <td style="font-size:.82rem;">${inv.branch}</td>
            <td style="font-size:.82rem;">${inv.audiologist}</td>
            <td style="font-size:.78rem;color:#8098bb;">${inv.insurance}</td>
            <td style="font-size:.84rem;">RD$ ${fmtNum(inv.subtotal)}</td>
            <td style="font-size:.84rem;color:#f59e0b;">RD$ ${fmtNum(inv.insurance_discount)}</td>
            <td class="fw-bold ${inv.status === 'pagada' ? 'text-success' : ''}">
                RD$ ${fmtNum(inv.total)}
            </td>
            <td>${statusBadge[inv.status] ?? inv.status}</td>
        </tr>`).join('');
}

function filterByUserTable() {
    const q = document.getElementById('byuser-search').value.toLowerCase().trim();
    if (!q) { renderByUserTable(allByUserInvoices); return; }
    renderByUserTable(allByUserInvoices.filter(inv =>
        (inv.receptionist + inv.patient + inv.invoice_number + inv.cedula + inv.branch)
            .toLowerCase().includes(q)
    ));
}

// ── Exportar CSV ──────────────────────────────────────────
function exportByUser() {
    const rows = [
        ['Fecha','Factura','Recepcionista','Paciente','Cédula','Sucursal','Audiólogo','Seguro','Subtotal','Descuento','Total','Estado']
    ];
    allByUserInvoices.forEach(inv => {
        rows.push([
            inv.created_at?.substring(0,10) ?? '',
            inv.invoice_number,
            inv.receptionist,
            inv.patient,
            inv.cedula,
            inv.branch,
            inv.audiologist,
            inv.insurance,
            inv.subtotal,
            inv.insurance_discount,
            inv.total,
            inv.status,
        ]);
    });
    const csv  = rows.map(r => r.map(v => `"${v}"`).join(',')).join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = 'facturas_por_recepcionista.csv';
    a.click();
    URL.revokeObjectURL(url);
}

function getFilters() {
    return {
        date_from     : document.getElementById('f-date-from').value,
        date_to       : document.getElementById('f-date-to').value,
        branch_id     : document.getElementById('f-branch').value,
        audiologist_id: document.getElementById('f-audiologist').value,
        user_id       : document.getElementById('f-user')?.value ?? '',
    };
}

// ── RENDER: Facturación ───────────────────────────────────
function renderInvoices(d) {
    const k = d.kpis;
    document.getElementById('kpi-invoices').innerHTML = `
        ${kpiCard('Facturas Totales',   k.total,      'ri-file-text-line',          'primary')}
        ${kpiCard('Ingresos (RD$)',      'RD$ '+k.ingresos, 'ri-money-dollar-circle-line','success')}
        ${kpiCard('Pagadas',             k.pagadas,    'ri-checkbox-circle-line',    'teal')}
        ${kpiCard('Pendientes',          k.pendientes, 'ri-time-line',               'warning')}
        ${kpiCard('Canceladas',          k.canceladas, 'ri-close-circle-line',       'danger')}
        ${kpiCard('Desc. Seguros (RD$)', 'RD$ '+k.descuentos, 'ri-shield-check-line','info')}
    `;

    // Ingresos por mes
    makeBarChart('chart-inv-month',
        d.by_month.map(r => fmtMonth(r.month)),
        d.by_month.map(r => parseFloat(r.total)),
        'Ingresos RD$', COLORS.primary
    );

    // Estado de facturas (donut)
    makeDonutChart('chart-inv-status',
        ['Pagadas','Pendientes','Canceladas'],
        [k.pagadas, k.pendientes, k.canceladas],
        [COLORS.teal, COLORS.warning, COLORS.danger]
    );

    // Servicios top (horizontal bar)
    makeHBarChart('chart-inv-services',
        d.top_services.map(r => r.service),
        d.top_services.map(r => parseInt(r.qty)),
        'Cantidad', COLORS.teal
    );

    // Tabla por sucursal
    const maxInv = Math.max(...d.by_branch.map(r => parseFloat(r.total)), 1);
    document.getElementById('table-inv-branch').innerHTML =
        tableHead(['Sucursal','Facturas','Total','%']) +
        '<tbody>' + d.by_branch.map((r,i) => `
            <tr>
                <td><span class="rank-badge ${rankClass(i)}">${i+1}</span> <span class="ms-2">${r.branch}</span></td>
                <td>${r.count}</td>
                <td class="fw-bold text-success">RD$ ${fmtNum(r.total)}</td>
                <td style="min-width:120px;">
                    <div class="prog-bar-wrap"><div class="prog-bar" style="width:${Math.round(parseFloat(r.total)/maxInv*100)}%"></div></div>
                    <span style="font-size:.72rem;color:#8098bb;">${Math.round(parseFloat(r.total)/maxInv*100)}%</span>
                </td>
            </tr>`).join('') + '</tbody>';

    // Tabla por seguro
    const maxIns = Math.max(...d.by_insurance.map(r => parseFloat(r.total)), 1);
    document.getElementById('table-inv-insurance').innerHTML =
        tableHead(['Seguro','Facturas','Total Cubierto']) +
        '<tbody>' + d.by_insurance.map((r,i) => `
            <tr>
                <td><span class="rank-badge ${rankClass(i)}">${i+1}</span> <span class="ms-2">${r.insurance}</span></td>
                <td>${r.count}</td>
                <td class="fw-bold text-info">RD$ ${fmtNum(r.total)}</td>
            </tr>`).join('') + '</tbody>';
}

// ── RENDER: Citas ─────────────────────────────────────────
function renderAppointments(d) {
    const k = d.kpis;
    document.getElementById('kpi-appointments').innerHTML = `
        ${kpiCard('Citas Totales',   k.total,       'ri-calendar-line',        'primary')}
        ${kpiCard('Completadas',     k.completadas, 'ri-checkbox-circle-line', 'success')}
        ${kpiCard('Programadas',      k.programadas,  'ri-time-line',            'warning')}
        ${kpiCard('Canceladas',      k.canceladas,  'ri-close-circle-line',    'danger')}
        ${kpiCard('Tasa Compleción', k.tasa+'%',    'ri-percent-line',         'purple')}
    `;

    // Citas por mes
    makeLineChart('chart-apt-month',
        d.by_month.map(r => fmtMonth(r.month)),
        d.by_month.map(r => parseInt(r.count)),
        'Citas', COLORS.primary
    );

    // Por día de semana
    makeBarChart('chart-apt-weekday',
        d.by_weekday.map(r => r.day),
        d.by_weekday.map(r => r.count),
        'Citas', COLORS.teal
    );

    // Por sucursal (donut)
    makeDonutChart('chart-apt-branch',
        d.by_branch.map(r => r.branch),
        d.by_branch.map(r => r.count),
        COLORS.gradient1
    );

    // Top audiólogos
    const maxApt = Math.max(...d.top_audiologists.map(r => r.count), 1);
    document.getElementById('table-apt-audiologists').innerHTML =
        tableHead(['#','Audiólogo','Total Citas','Completadas','%']) +
        '<tbody>' + d.top_audiologists.map((r,i) => `
            <tr>
                <td><span class="rank-badge ${rankClass(i)}">${i+1}</span></td>
                <td class="fw-semibold">${r.audiologist}</td>
                <td>${r.count}</td>
                <td>${r.completadas}</td>
                <td>
                    <div class="prog-bar-wrap"><div class="prog-bar" style="width:${Math.round(r.count/maxApt*100)}%"></div></div>
                    <span style="font-size:.72rem;color:#8098bb;">${Math.round(r.count/maxApt*100)}%</span>
                </td>
            </tr>`).join('') + '</tbody>';
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

    // HCs completadas por mes
    makeLineChart('chart-hc-month',
        d.by_month.map(r => fmtMonth(r.month)),
        d.by_month.map(r => parseInt(r.count)),
        'HCs Completadas', COLORS.teal
    );

    // Estado (donut)
    makeDonutChart('chart-hc-status',
        ['Completadas','Pendientes'],
        [k.completadas, k.pendientes],
        [COLORS.teal, COLORS.warning]
    );

    // Top audiólogos HCs
    const maxHC = Math.max(...d.top_audiologists.map(r => r.count), 1);
    document.getElementById('table-hc-audiologists').innerHTML =
        tableHead(['#','Audiólogo','HCs Completadas','%']) +
        '<tbody>' + d.top_audiologists.map((r,i) => `
            <tr>
                <td><span class="rank-badge ${rankClass(i)}">${i+1}</span></td>
                <td class="fw-semibold">${r.audiologist}</td>
                <td>${r.count}</td>
                <td>
                    <div class="prog-bar-wrap"><div class="prog-bar" style="width:${Math.round(r.count/maxHC*100)}%"></div></div>
                    <span style="font-size:.72rem;color:#8098bb;">${Math.round(r.count/maxHC*100)}%</span>
                </td>
            </tr>`).join('') + '</tbody>';

    // Por sucursal
    document.getElementById('table-hc-branch').innerHTML =
        tableHead(['Sucursal','Total','Completadas','Pendientes']) +
        '<tbody>' + d.by_branch.map(r => `
            <tr>
                <td class="fw-semibold">${r.branch}</td>
                <td>${r.total}</td>
                <td><span style="color:#0ab39c;font-weight:700;">${r.completadas}</span></td>
                <td><span style="color:#f59e0b;font-weight:700;">${r.pendientes}</span></td>
            </tr>`).join('') + '</tbody>';
}

// ── RENDER: Pacientes ─────────────────────────────────────
function renderPatients(d) {
    const k = d.kpis;
    document.getElementById('kpi-patients').innerHTML = `
        ${kpiCard('Total Pacientes', k.total,      'ri-group-line',          'primary')}
        ${kpiCard('Con Seguro',      k.conSeguro,  'ri-shield-check-line',   'success')}
        ${kpiCard('Sin Seguro',      k.sinSeguro,  'ri-shield-cross-line',   'warning')}
        ${kpiCard('Masculino',       k.masculino,  'ri-men-line',            'info')}
        ${kpiCard('Femenino',        k.femenino,   'ri-women-line',          'purple')}
    `;

    // Nuevos por mes
    makeLineChart('chart-pat-month',
        d.by_month.map(r => fmtMonth(r.month)),
        d.by_month.map(r => parseInt(r.count)),
        'Pacientes', COLORS.primary
    );

    // Por seguro (donut)
    const insLabels = d.by_insurance.length
        ? [...d.by_insurance.map(r => r.insurance), 'Sin seguro']
        : ['Sin seguro'];
    const insData = d.by_insurance.length
        ? [...d.by_insurance.map(r => r.count), k.sinSeguro]
        : [k.sinSeguro];
    makeDonutChart('chart-pat-insurance', insLabels, insData, COLORS.gradient1);

    // Por sucursal (bar)
    makeBarChart('chart-pat-branch',
        d.by_branch.map(r => r.branch),
        d.by_branch.map(r => r.count),
        'Pacientes', COLORS.info
    );

    // Top pacientes
    document.getElementById('table-pat-top').innerHTML =
        tableHead(['#','Paciente','Cédula','Visitas','Total Pagado']) +
        '<tbody>' + d.top_patients.map((r,i) => `
            <tr>
                <td><span class="rank-badge ${rankClass(i)}">${i+1}</span></td>
                <td class="fw-semibold">${r.patient}</td>
                <td class="text-muted" style="font-size:.82rem;">${r.cedula}</td>
                <td>${r.visits}</td>
                <td class="fw-bold text-success">RD$ ${fmtNum(r.total)}</td>
            </tr>`).join('') + '</tbody>';
}

// ── Chart helpers ─────────────────────────────────────────
function destroyChart(id) {
    if (charts[id]) { charts[id].destroy(); delete charts[id]; }
}

function makeBarChart(id, labels, data, label, color) {
    destroyChart(id);
    const ctx = document.getElementById(id).getContext('2d');
    charts[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label, data, backgroundColor: color + 'cc', borderColor: color, borderWidth:1.5, borderRadius:5 }]
        },
        options: {
            responsive:true, plugins:{ legend:{display:false} },
            scales:{ y:{ beginAtZero:true, grid:{color:'#f0f2f7'} }, x:{ grid:{display:false} } }
        }
    });
}

function makeHBarChart(id, labels, data, label, color) {
    destroyChart(id);
    const ctx = document.getElementById(id).getContext('2d');
    charts[id] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label, data, backgroundColor: color + 'cc', borderColor: color, borderWidth:1.5, borderRadius:4 }]
        },
        options: {
            indexAxis:'y', responsive:true,
            plugins:{ legend:{display:false} },
            scales:{ x:{ beginAtZero:true, grid:{color:'#f0f2f7'} }, y:{ grid:{display:false} } }
        }
    });
}

function makeLineChart(id, labels, data, label, color) {
    destroyChart(id);
    const ctx = document.getElementById(id).getContext('2d');
    charts[id] = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label, data,
                borderColor: color, backgroundColor: color + '18',
                borderWidth:2.5, fill:true, tension:.4,
                pointBackgroundColor: color, pointRadius:4
            }]
        },
        options: {
            responsive:true, plugins:{ legend:{display:false} },
            scales:{ y:{ beginAtZero:true, grid:{color:'#f0f2f7'} }, x:{ grid:{display:false} } }
        }
    });
}

function makeDonutChart(id, labels, data, colors) {
    destroyChart(id);
    const ctx = document.getElementById(id).getContext('2d');
    charts[id] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: Array.isArray(colors) ? colors : [colors],
                borderWidth:2, borderColor:'#fff', hoverOffset:6
            }]
        },
        options: {
            responsive:true, cutout:'65%',
            plugins:{ legend:{ position:'bottom', labels:{ padding:12 } } }
        }
    });
}

// ── HTML helpers ──────────────────────────────────────────
function kpiCard(label, value, icon, type) {
    return `
    <div class="kpi-card ${type}">
        <div class="kpi-lbl">${label}</div>
        <div class="kpi-val">${value}</div>
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
    return parseFloat(n || 0).toLocaleString('es-DO', { minimumFractionDigits:2, maximumFractionDigits:2 });
}

function fmtMonth(m) {
    if (!m) return '—';
    const [y, mo] = m.split('-');
    const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    return (months[parseInt(mo)-1] || mo) + ' ' + y;
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
    loadTab('invoices');
});
</script>
@endpush
</x-app-layout>