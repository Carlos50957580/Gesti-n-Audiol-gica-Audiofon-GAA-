<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
    .stat-card { border:none; border-radius:.75rem; overflow:hidden; position:relative; transition:transform .2s,box-shadow .2s; }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 30px rgba(64,81,137,.15)!important; }
    .stat-icon { width:52px; height:52px; border-radius:.6rem; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
    .stat-bg { position:absolute; right:-10px; bottom:-10px; font-size:5rem; opacity:.05; line-height:1; pointer-events:none; }
    .cr-table th { font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#8098bb; border-bottom:2px solid #e9ecef; padding:.85rem 1rem; white-space:nowrap; }
    .cr-table td { padding:.82rem 1rem; vertical-align:middle; }
    .cr-table tbody tr { transition:background .15s; border-bottom:1px solid #f3f5f9; }
    .cr-table tbody tr:hover { background:#f8faff; }
    .cr-table tbody tr:last-child { border-bottom:none; }
    .pat-avatar { width:40px; height:40px; border-radius:.5rem; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; font-size:1rem; color:#fff; background:linear-gradient(135deg,#405189 0%,#0ab39c 100%); font-weight:700; }
    .status-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .7rem; border-radius:2rem; font-size:.75rem; font-weight:600; }
    .status-pill .dot { width:7px; height:7px; border-radius:50%; }
    .status-pendiente { background:#fef9c3; color:#92400e; }
    .status-pendiente .dot { background:#f59e0b; box-shadow:0 0 0 2px rgba(245,158,11,.25); }
    .status-completada { background:#d1fae5; color:#065f46; }
    .status-completada .dot { background:#10b981; }
    .btn-action { width:32px; height:32px; padding:0; border:none; border-radius:.4rem; display:inline-flex; align-items:center; justify-content:center; transition:all .15s; }
    .btn-action:hover { transform:scale(1.12); }
    #search-input { border-radius:2rem; padding-left:2.4rem; border:1.5px solid #e2e8f0; font-size:.9rem; transition:border-color .2s,box-shadow .2s; }
    #search-input:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.12); }
    .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#8098bb; pointer-events:none; }
    .inv-chip { display:inline-flex; align-items:center; gap:.3rem; background:linear-gradient(135deg,rgba(64,81,137,.1),rgba(10,179,156,.1)); color:#405189; font-weight:700; font-size:.82rem; padding:.22rem .65rem; border-radius:2rem; border:1px solid rgba(64,81,137,.15); font-family:monospace; }
    .mh-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); color:#fff; border-radius:.5rem .5rem 0 0; }
    .mh-info    { background:linear-gradient(135deg,#299cdb,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
    .mh-success .btn-close, .mh-info .btn-close { filter:invert(1); }
    .detail-row { display:flex; gap:.75rem; align-items:flex-start; padding:.7rem 0; border-bottom:1px solid #f3f5f9; }
    .detail-row:last-child { border-bottom:none; }
    .detail-icon { width:36px; height:36px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem; }
    .detail-lbl { font-size:.7rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#8098bb; }
    .detail-val { font-size:.91rem; font-weight:500; color:#344563; margin-top:.1rem; white-space:pre-wrap; word-break:break-word; }
    .section-label { font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#8098bb; border-bottom:1px solid #f0f2f7; padding-bottom:.4rem; margin-bottom:.9rem; }
    @keyframes fadeInUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .anim-row { animation:fadeInUp .3s ease both; }
    .anim-row:nth-child(1){animation-delay:.03s}.anim-row:nth-child(2){animation-delay:.07s}
    .anim-row:nth-child(3){animation-delay:.11s}.anim-row:nth-child(4){animation-delay:.15s}
    .anim-row:nth-child(5){animation-delay:.19s}.anim-row:nth-child(6){animation-delay:.23s}
    .anim-row:nth-child(7){animation-delay:.27s}.anim-row:nth-child(8){animation-delay:.31s}
    .anim-row:nth-child(9){animation-delay:.35s}.anim-row:nth-child(10){animation-delay:.39s}
    #toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
    .toast-item { min-width:280px; padding:.85rem 1.1rem; border-radius:.5rem; color:#fff; font-size:.88rem; font-weight:500; display:flex; align-items:center; gap:.6rem; box-shadow:0 4px 20px rgba(0,0,0,.18); animation:toastIn .3s ease; }
    @keyframes toastIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
    .toast-error   { background:linear-gradient(135deg,#e74c3c,#c0392b); }
    .timeline-item { position:relative; padding-left:1.5rem; padding-bottom:1.2rem; }
    .timeline-item:not(:last-child)::before { content:''; position:absolute; left:.4rem; top:1.2rem; bottom:0; width:2px; background:#e9ecef; }
    .timeline-dot { position:absolute; left:0; top:.3rem; width:14px; height:14px; border-radius:50%; border:2px solid #fff; }
    .timeline-dot.pendiente  { background:#f59e0b; box-shadow:0 0 0 2px rgba(245,158,11,.3); }
    .timeline-dot.completada { background:#10b981; box-shadow:0 0 0 2px rgba(16,185,129,.3); }
</style>

<div id="toast-container"></div>

{{-- Breadcrumb --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Historias Clínicas</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Historias Clínicas</li>
            </ol>
        </div>
    </div>
</div>

{{-- Stat cards --}}
@php
    $col         = $invoices->getCollection();
    $totalHC     = $invoices->total();
    $pendientes  = $col->filter(fn($i) => !$i->clinicalRecord || $i->clinicalRecord->status === 'pendiente')->count();
    $completadas = $col->filter(fn($i) => $i->clinicalRecord?->status === 'completada')->count();
    $pacientes   = $col->pluck('patient_id')->unique()->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-file-list-3-line"></i></div>
                <div>
                    <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Total</div>
                    <div class="fw-bold fs-4 lh-1 mt-1">{{ $totalHC }}</div>
                </div>
                <div class="stat-bg text-primary"><i class="ri-file-list-3-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-time-line"></i></div>
                <div>
                    <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Pendientes</div>
                    <div class="fw-bold fs-4 lh-1 mt-1">{{ $pendientes }}</div>
                </div>
                <div class="stat-bg text-warning"><i class="ri-time-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="stat-icon bg-success-subtle text-success"><i class="ri-checkbox-circle-line"></i></div>
                <div>
                    <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Completadas</div>
                    <div class="fw-bold fs-4 lh-1 mt-1">{{ $completadas }}</div>
                </div>
                <div class="stat-bg text-success"><i class="ri-checkbox-circle-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="stat-icon bg-info-subtle text-info"><i class="ri-group-line"></i></div>
                <div>
                    <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Pacientes</div>
                    <div class="fw-bold fs-4 lh-1 mt-1">{{ $pacientes }}</div>
                </div>
                <div class="stat-bg text-info"><i class="ri-group-line"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Main table card --}}
<div class="card shadow-sm" style="border-radius:.75rem;border:none;">
    <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
        <h5 class="card-title mb-0 flex-grow-1">Mis Historias Clínicas</h5>
        <div class="position-relative" style="width:230px;">
            <i class="ri-search-line search-icon"></i>
            <input type="text" id="search-input" class="form-control" placeholder="Buscar paciente...">
        </div>
        <select id="status-filter" class="form-select form-select-sm"
                style="width:160px;border-radius:2rem;border:1.5px solid #e2e8f0;font-size:.85rem;">
            <option value="">Todos</option>
            <option value="pendiente">Pendientes</option>
            <option value="completada">Completadas</option>
        </select>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table cr-table mb-0">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Factura</th>
                        <th>Sucursal</th>
                        <th>Fecha pago</th>
                        <th>Estado HC</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cr-tbody">
                    @forelse($invoices as $invoice)
                    @php $hcStatus = $invoice->clinicalRecord?->status ?? 'pendiente'; @endphp
                    <tr class="anim-row"
                        data-id="{{ $invoice->id }}"
                        data-patient="{{ strtolower($invoice->patient->first_name . ' ' . $invoice->patient->last_name . ' ' . $invoice->patient->cedula) }}"
                        data-status="{{ $hcStatus }}">

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="pat-avatar">
                                    {{ strtoupper(substr($invoice->patient->first_name,0,1) . substr($invoice->patient->last_name,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:.92rem;">
                                        {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}
                                    </div>
                                    <div class="text-muted" style="font-size:.75rem;">
                                        <i class="ri-id-card-line me-1"></i>{{ $invoice->patient->cedula }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="inv-chip">
                                <i class="ri-file-text-line"></i>
                                FAC-{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        <td>
                            <span class="text-muted" style="font-size:.87rem;">
                                <i class="ri-map-pin-line me-1"></i>{{ $invoice->branch->name }}
                            </span>
                        </td>

                        <td>
                            <span class="text-muted" style="font-size:.85rem;">
                                {{ $invoice->updated_at->format('d/m/Y') }}
                            </span>
                        </td>

                        <td>
                            @if($hcStatus === 'completada')
                                <span class="status-pill status-completada"><span class="dot"></span> Completada</span>
                            @else
                                <span class="status-pill status-pendiente"><span class="dot"></span> Pendiente</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                @if($invoice->clinicalRecord)
                                <button type="button" class="btn btn-action bg-info-subtle text-info"
                                        title="Ver HC"
                                        onclick="openShowModal({{ $invoice->id }})">
                                    <i class="ri-eye-fill fs-13"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-action bg-secondary-subtle text-secondary"
                                        title="Sin HC aún" disabled>
                                    <i class="ri-eye-off-line fs-13"></i>
                                </button>
                                @endif

                                <button type="button" class="btn btn-action bg-primary-subtle text-primary"
                                        title="Llenar / Editar HC"
                                        onclick="window.location='{{ route('clinical-records.edit', $invoice->id) }}'">
                                    <i class="ri-edit-2-fill fs-13"></i>
                                </button>

                                <button type="button" class="btn btn-action bg-success-subtle text-success"
                                        title="Ver historial del paciente"
                                        onclick="openHistoryModal({{ $invoice->patient_id }})">
                                    <i class="ri-history-line fs-13"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <i class="ri-file-list-3-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                <p class="text-muted mb-0">No tienes facturas pagadas asignadas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="no-results" class="text-center py-5 d-none">
            <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
            <p class="text-muted mb-0">No se encontraron resultados.</p>
        </div>

        @if($invoices->hasPages())
        <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>

</div>
</div>

{{-- ══════════════════════════════════
     MODAL: Ver Detalle HC
══════════════════════════════════ --}}
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-file-list-3-line fs-18"></i> Detalle Historia Clínica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="show-skeleton" class="d-flex flex-column gap-3">
                    <div class="placeholder-glow"><span class="placeholder col-8 rounded" style="height:18px;"></span></div>
                    <div class="placeholder-glow"><span class="placeholder col-5 rounded" style="height:14px;"></span></div>
                    <div class="placeholder-glow"><span class="placeholder col-10 rounded" style="height:60px;"></span></div>
                    <div class="placeholder-glow"><span class="placeholder col-10 rounded" style="height:60px;"></span></div>
                </div>
                <div id="show-content" class="d-none">
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded" style="background:#f8faff;">
                        <div class="pat-avatar" style="width:48px;height:48px;font-size:1.1rem;" id="show-avatar">--</div>
                        <div>
                            <div class="fw-bold" style="font-size:1rem;" id="show-patient-name">—</div>
                            <div class="text-muted" style="font-size:.8rem;" id="show-patient-cedula">—</div>
                        </div>
                        <div class="ms-auto" id="show-status-badge"></div>
                    </div>

                    <p class="section-label"><i class="ri-information-line me-1"></i>Información</p>
                    <div class="detail-row">
                        <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-file-text-line"></i></div>
                        <div><div class="detail-lbl">Factura</div><div class="detail-val" id="show-invoice">—</div></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-icon bg-info-subtle text-info"><i class="ri-map-pin-line"></i></div>
                        <div><div class="detail-lbl">Sucursal</div><div class="detail-val" id="show-branch">—</div></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-icon bg-success-subtle text-success"><i class="ri-calendar-line"></i></div>
                        <div><div class="detail-lbl">Fecha de pago</div><div class="detail-val" id="show-created">—</div></div>
                    </div>

                    <p class="section-label mt-3"><i class="ri-stethoscope-line me-1"></i>Contenido clínico</p>
                    <div class="detail-row">
                        <div class="detail-icon bg-warning-subtle text-warning"><i class="ri-questionnaire-line"></i></div>
                        <div class="flex-grow-1"><div class="detail-lbl">Motivo de consulta</div><div class="detail-val" id="show-reason">—</div></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-icon bg-danger-subtle text-danger"><i class="ri-heart-pulse-line"></i></div>
                        <div class="flex-grow-1"><div class="detail-lbl">Diagnóstico</div><div class="detail-val" id="show-diagnosis">—</div></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-icon bg-success-subtle text-success"><i class="ri-capsule-line"></i></div>
                        <div class="flex-grow-1"><div class="detail-lbl">Plan de tratamiento</div><div class="detail-val" id="show-treatment">—</div></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-icon bg-secondary-subtle text-secondary"><i class="ri-sticky-note-line"></i></div>
                        <div class="flex-grow-1"><div class="detail-lbl">Notas</div><div class="detail-val" id="show-notes">—</div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-go-edit">
                    <i class="ri-edit-2-line"></i> Editar HC
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL: Historial del Paciente
══════════════════════════════════ --}}
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:560px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-success py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-history-line fs-18"></i> Historial del Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="history-skeleton" class="d-flex flex-column gap-3">
                    <div class="placeholder-glow"><span class="placeholder col-9 rounded" style="height:16px;"></span></div>
                    <div class="placeholder-glow"><span class="placeholder col-6 rounded" style="height:14px;"></span></div>
                    <div class="placeholder-glow"><span class="placeholder col-10 rounded" style="height:14px;"></span></div>
                </div>
                <div id="history-content" class="d-none">
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded" style="background:#f8faff;">
                        <div class="pat-avatar" style="width:44px;height:44px;" id="hist-avatar">--</div>
                        <div>
                            <div class="fw-bold" id="hist-patient-name">—</div>
                            <div class="text-muted" style="font-size:.8rem;" id="hist-patient-cedula">—</div>
                        </div>
                        <span class="ms-auto badge bg-primary-subtle text-primary" id="hist-count">0 consultas</span>
                    </div>
                    <div id="hist-timeline"></div>
                </div>
                <div id="history-empty" class="text-center py-4 d-none">
                    <i class="ri-history-line d-block text-muted mb-3" style="font-size:3rem;opacity:.3;"></i>
                    <p class="text-muted mb-0">No hay historial disponible.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── URLs ─────────────────────────────────────────────────
const CSRF             = document.querySelector('meta[name="csrf-token"]').content;
const URL_BASE         = "{{ url('clinical-records') }}";
const URL_HISTORY_BASE = "{{ url('clinical-records/patient') }}";

function urlShowData(id)   { return URL_BASE + '/' + id + '/show-data'; }
function urlHistory(patId) { return URL_HISTORY_BASE + '/' + patId + '/history'; }

let showModal, historyModal;

document.addEventListener('DOMContentLoaded', () => {
    showModal    = new bootstrap.Modal(document.getElementById('showModal'));
    historyModal = new bootstrap.Modal(document.getElementById('historyModal'));

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
});

// ── Search & filter ──────────────────────────────────────
document.getElementById('search-input').addEventListener('input', filterTable);
document.getElementById('status-filter').addEventListener('change', filterTable);

function filterTable() {
    const q      = document.getElementById('search-input').value.toLowerCase().trim();
    const status = document.getElementById('status-filter').value;
    const rows   = document.querySelectorAll('#cr-tbody tr[data-id]');
    let visible  = 0;
    rows.forEach(tr => {
        const matchQ = !q      || (tr.dataset.patient || '').includes(q);
        const matchS = status === '' || tr.dataset.status === status;
        const show   = matchQ && matchS;
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('no-results').classList.toggle('d-none', visible > 0);
}

// ── SHOW DETAIL ──────────────────────────────────────────
async function openShowModal(invoiceId) {
    document.getElementById('show-skeleton').classList.remove('d-none');
    document.getElementById('show-content').classList.add('d-none');
    document.getElementById('btn-go-edit').onclick = () => {
        window.location = URL_BASE + '/' + invoiceId + '/edit';
    };
    showModal.show();

    try {
        const res = await fetch(urlShowData(invoiceId), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        const initials = (d.patient_name || '--')
            .split(' ').slice(0,2).map(w => w[0]?.toUpperCase() || '').join('');

        document.getElementById('show-avatar').textContent         = initials;
        document.getElementById('show-patient-name').textContent   = d.patient_name;
        document.getElementById('show-patient-cedula').textContent = d.patient_cedula;
        document.getElementById('show-invoice').textContent        = d.invoice_number;
        document.getElementById('show-branch').textContent         = d.branch;
        document.getElementById('show-created').textContent        = d.date;
        document.getElementById('show-reason').textContent         = d.reason_for_consultation || '—';
        document.getElementById('show-diagnosis').textContent      = d.diagnosis               || '—';
        document.getElementById('show-treatment').textContent      = d.treatment_plan           || '—';
        document.getElementById('show-notes').textContent          = d.notes                    || '—';

        document.getElementById('show-status-badge').innerHTML = d.hc_status === 'completada'
            ? '<span class="status-pill status-completada"><span class="dot"></span>Completada</span>'
            : '<span class="status-pill status-pendiente"><span class="dot"></span>Pendiente</span>';

        document.getElementById('show-skeleton').classList.add('d-none');
        document.getElementById('show-content').classList.remove('d-none');

    } catch {
        showToast('Error al cargar los datos.', 'error');
        showModal.hide();
    }
}

// ── HISTORY MODAL ────────────────────────────────────────
async function openHistoryModal(patientId) {
    document.getElementById('history-skeleton').classList.remove('d-none');
    document.getElementById('history-content').classList.add('d-none');
    document.getElementById('history-empty').classList.add('d-none');
    historyModal.show();

    try {
        const res = await fetch(urlHistory(patientId), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        document.getElementById('history-skeleton').classList.add('d-none');

        if (!d.records || d.records.length === 0) {
            document.getElementById('history-empty').classList.remove('d-none');
            return;
        }

        const initials = (d.patient_name || '--')
            .split(' ').slice(0,2).map(w => w[0]?.toUpperCase() || '').join('');
        document.getElementById('hist-avatar').textContent         = initials;
        document.getElementById('hist-patient-name').textContent   = d.patient_name;
        document.getElementById('hist-patient-cedula').textContent = d.patient_cedula;
        document.getElementById('hist-count').textContent =
            d.records.length + ' consulta' + (d.records.length !== 1 ? 's' : '');

        const timeline = document.getElementById('hist-timeline');
        timeline.innerHTML = '';
        d.records.forEach(rec => {
            const isComp = rec.status === 'completada';
            timeline.innerHTML += `
                <div class="timeline-item">
                    <div class="timeline-dot ${rec.status}"></div>
                    <div class="ms-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="inv-chip"><i class="ri-file-text-line"></i>${rec.invoice_number}</span>
                            ${isComp
                                ? '<span class="status-pill status-completada" style="font-size:.72rem;padding:.18rem .55rem;"><span class="dot"></span>Completada</span>'
                                : '<span class="status-pill status-pendiente"  style="font-size:.72rem;padding:.18rem .55rem;"><span class="dot"></span>Pendiente</span>'}
                        </div>
                        <div class="text-muted" style="font-size:.78rem;">${rec.date}</div>
                        ${rec.diagnosis
                            ? `<div class="mt-1 p-2 rounded" style="background:#f8faff;font-size:.82rem;color:#344563;">
                                   <i class="ri-heart-pulse-line me-1 text-danger"></i>${rec.diagnosis}
                               </div>`
                            : ''}
                        <a href="${rec.edit_url}" class="d-inline-flex align-items-center gap-1 mt-1"
                           style="font-size:.78rem;color:#405189;font-weight:600;">
                            <i class="ri-edit-2-line"></i> Abrir HC
                        </a>
                    </div>
                </div>`;
        });

        document.getElementById('history-content').classList.remove('d-none');

    } catch {
        showToast('Error al cargar el historial.', 'error');
        historyModal.hide();
    }
}

// ── Toast ────────────────────────────────────────────────
function showToast(msg, type) {
    type = type || 'success';
    const div = document.createElement('div');
    div.className = 'toast-item toast-' + type;
    div.innerHTML = '<i class="ri-' + (type === 'success' ? 'checkbox-circle' : 'error-warning') + '-line fs-16"></i>' + msg;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(() => {
        div.style.transition = 'opacity .4s'; div.style.opacity = '0';
        setTimeout(() => div.remove(), 400);
    }, 3500);
}
</script>
@endpush
</x-app-layout>