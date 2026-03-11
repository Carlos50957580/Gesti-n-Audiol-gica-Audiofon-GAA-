<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    <style>
        /* ── Stat cards ── */
        .stat-card {
            border:none; border-radius:.75rem; overflow:hidden; position:relative;
            transition:transform .2s,box-shadow .2s;
        }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 30px rgba(64,81,137,.15)!important; }
        .stat-icon {
            width:52px; height:52px; border-radius:.6rem; flex-shrink:0;
            display:flex; align-items:center; justify-content:center; font-size:1.4rem;
        }
        .stat-bg { position:absolute; right:-10px; bottom:-10px; font-size:5rem; opacity:.05; line-height:1; pointer-events:none; }

        /* ── Table ── */
        .svc-table th {
            font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
            color:#8098bb; border-bottom:2px solid #e9ecef; padding:.85rem 1rem; white-space:nowrap;
        }
        .svc-table td { padding:.82rem 1rem; vertical-align:middle; }
        .svc-table tbody tr { transition:background .15s; border-bottom:1px solid #f3f5f9; }
        .svc-table tbody tr:hover { background:#f8faff; }
        .svc-table tbody tr:last-child { border-bottom:none; }

        /* ── Service icon ── */
        .svc-icon {
            width:40px; height:40px; border-radius:.5rem; flex-shrink:0;
            display:inline-flex; align-items:center; justify-content:center;
            font-size:1.1rem; color:#fff;
            background:linear-gradient(135deg,#405189 0%,#0ab39c 100%);
        }

        /* ── Price chip ── */
        .price-chip {
            display:inline-flex; align-items:center; gap:.3rem;
            background:linear-gradient(135deg,rgba(64,81,137,.1),rgba(10,179,156,.1));
            color:#405189; font-weight:700; font-size:.88rem;
            padding:.28rem .75rem; border-radius:2rem;
            border:1px solid rgba(64,81,137,.15);
        }

        /* ── Status pill ── */
        .status-pill {
            display:inline-flex; align-items:center; gap:.35rem;
            padding:.28rem .7rem; border-radius:2rem; font-size:.75rem; font-weight:600;
        }
        .status-pill .dot { width:7px; height:7px; border-radius:50%; }
        .status-active   { background:#d1fae5; color:#065f46; }
        .status-active .dot   { background:#10b981; box-shadow:0 0 0 2px rgba(16,185,129,.25); }
        .status-inactive { background:#fee2e2; color:#991b1b; }
        .status-inactive .dot { background:#ef4444; }

        /* ── Action buttons ── */
        .btn-action {
            width:32px; height:32px; padding:0; border:none; border-radius:.4rem;
            display:inline-flex; align-items:center; justify-content:center; transition:all .15s;
        }
        .btn-action:hover { transform:scale(1.12); }

        /* ── Search ── */
        #search-input {
            border-radius:2rem; padding-left:2.4rem;
            border:1.5px solid #e2e8f0; font-size:.9rem;
            transition:border-color .2s,box-shadow .2s;
        }
        #search-input:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.12); }
        .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#8098bb; pointer-events:none; }

        /* ── Modal headers ── */
        .mh-primary { background:linear-gradient(135deg,#405189,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-info    { background:linear-gradient(135deg,#299cdb,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-danger  { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-primary .btn-close, .mh-info .btn-close, .mh-danger .btn-close { filter:invert(1); }

        /* ── Form fields ── */
        .form-floating>.form-control,
        .form-floating>.form-select { border:1.5px solid #e2e8f0; border-radius:.5rem; }
        .form-floating>.form-control:focus,
        .form-floating>.form-select:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
        .section-label {
            font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
            color:#8098bb; border-bottom:1px solid #f0f2f7; padding-bottom:.4rem; margin-bottom:.9rem;
        }

        /* ── Price input with prefix ── */
        .price-group .input-group-text {
            background:linear-gradient(135deg,#405189,#0ab39c); color:#fff;
            border:none; font-weight:700; font-size:.85rem; border-radius:.5rem 0 0 .5rem;
        }
        .price-group .form-control { border:1.5px solid #e2e8f0; border-left:none; border-radius:0 .5rem .5rem 0; }
        .price-group .form-control:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }

        /* ── Detail rows ── */
        .detail-row {
            display:flex; gap:.75rem; align-items:flex-start;
            padding:.7rem 0; border-bottom:1px solid #f3f5f9;
        }
        .detail-row:last-child { border-bottom:none; }
        .detail-icon { width:36px; height:36px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem; }
        .detail-lbl  { font-size:.7rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#8098bb; }
        .detail-val  { font-size:.91rem; font-weight:500; color:#344563; margin-top:.1rem; }

        /* ── Description textarea ── */
        #f-description { resize:vertical; min-height:80px; }

        /* ── Animations ── */
        @keyframes fadeInUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim-row { animation:fadeInUp .3s ease both; }
        .anim-row:nth-child(1){animation-delay:.03s}.anim-row:nth-child(2){animation-delay:.07s}
        .anim-row:nth-child(3){animation-delay:.11s}.anim-row:nth-child(4){animation-delay:.15s}
        .anim-row:nth-child(5){animation-delay:.19s}.anim-row:nth-child(6){animation-delay:.23s}
        .anim-row:nth-child(7){animation-delay:.27s}.anim-row:nth-child(8){animation-delay:.31s}
        .anim-row:nth-child(9){animation-delay:.35s}.anim-row:nth-child(10){animation-delay:.39s}

        /* ── Toast ── */
        #toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
        .toast-item {
            min-width:280px; padding:.85rem 1.1rem; border-radius:.5rem; color:#fff;
            font-size:.88rem; font-weight:500; display:flex; align-items:center; gap:.6rem;
            box-shadow:0 4px 20px rgba(0,0,0,.18); animation:toastIn .3s ease;
        }
        @keyframes toastIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
        .toast-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
        .toast-error   { background:linear-gradient(135deg,#e74c3c,#c0392b); }
    </style>

    <div id="toast-container"></div>

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Servicios</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Servicios</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-stethoscope-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Total</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-total">{{ $services->total() }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-stethoscope-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-checkbox-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Activos</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-active">{{ $services->getCollection()->where('active',1)->count() }}</div>
                    </div>
                    <div class="stat-bg text-success"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-danger-subtle text-danger"><i class="ri-close-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Inactivos</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-inactive">{{ $services->getCollection()->where('active',0)->count() }}</div>
                    </div>
                    <div class="stat-bg text-danger"><i class="ri-close-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-money-dollar-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Precio prom.</div>
                        @php
                            $col = $services->getCollection();
                            $avg = $col->count() ? round($col->avg('price'), 2) : 0;
                        @endphp
                        <div class="fw-bold fs-5 lh-1 mt-1" id="stat-avg" style="font-size:1.1rem!important;">
                            RD$ {{ number_format($avg, 2) }}
                        </div>
                    </div>
                    <div class="stat-bg text-warning"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main table card --}}
    <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
            <h5 class="card-title mb-0 flex-grow-1">Lista de Servicios</h5>
            <div class="position-relative" style="width:230px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="search-input" class="form-control" placeholder="Buscar servicio...">
            </div>
            <select id="status-filter" class="form-select form-select-sm"
                    style="width:140px;border-radius:2rem;border:1.5px solid #e2e8f0;font-size:.85rem;">
                <option value="">Todos</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                    style="border-radius:2rem;padding:.4rem 1rem;" onclick="openCreateModal()">
                <i class="ri-add-line"></i> Nuevo Servicio
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table svc-table mb-0">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="svc-tbody">
                        @forelse($services as $svc)
                        <tr class="anim-row"
                            data-id="{{ $svc->id }}"
                            data-name="{{ strtolower($svc->name) }}"
                            data-status="{{ $svc->active ? '1' : '0' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="svc-icon">
                                        <i class="ri-stethoscope-line"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.92rem;">{{ $svc->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">ID #{{ $svc->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($svc->description)
                                    <span class="text-muted" style="font-size:.85rem;">
                                        {{ Str::limit($svc->description, 50) }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="price-chip">
                                    <i class="ri-money-dollar-circle-line"></i>
                                    RD$ {{ number_format($svc->price, 2) }}
                                </span>
                            </td>
                            <td>
                                @if($svc->active)
                                    <span class="status-pill status-active">
                                        <span class="dot"></span> Activo
                                    </span>
                                @else
                                    <span class="status-pill status-inactive">
                                        <span class="dot"></span> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-action bg-info-subtle text-info"
                                            title="Ver detalle" onclick="openShowModal({{ $svc->id }})">
                                        <i class="ri-eye-fill fs-13"></i>
                                    </button>
                                    <button type="button" class="btn btn-action bg-warning-subtle text-warning"
                                            title="Editar" onclick="openEditModal({{ $svc->id }})">
                                        <i class="ri-pencil-fill fs-13"></i>
                                    </button>
                                    <button type="button" class="btn btn-action bg-danger-subtle text-danger"
                                            title="Eliminar" onclick="openDeleteModal({{ $svc->id }}, '{{ addslashes($svc->name) }}')">
                                        <i class="ri-delete-bin-fill fs-13"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-5">
                                    <i class="ri-stethoscope-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                    <p class="text-muted mb-0">No hay servicios registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="no-results" class="text-center py-5 d-none">
                <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0">No se encontraron servicios con ese criterio.</p>
            </div>

            @if($services->hasPages())
            <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                {{ $services->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- ══════════════════════
     MODAL: Crear / Editar
══════════════════════ --}}
<div class="modal fade" id="svcModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-primary py-3">
                <h5 class="modal-title d-flex align-items-center gap-2" id="svc-modal-title">
                    <i class="ri-stethoscope-line fs-18"></i> Nuevo Servicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="svc-modal-alert" class="alert d-none mb-3"></div>
                <form id="svc-form" novalidate>
                    <input type="hidden" id="svc-form-id">
                    <input type="hidden" id="svc-form-method" value="POST">

                    <p class="section-label"><i class="ri-information-line me-1"></i>Información del servicio</p>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-name" placeholder="Nombre del servicio">
                                <label><i class="ri-stethoscope-line me-1 text-muted"></i>Nombre del servicio</label>
                                <div class="invalid-feedback" id="err-name"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">
                                <i class="ri-file-text-line me-1 text-muted"></i>Descripción
                                <span class="text-muted fw-normal">(opcional)</span>
                            </label>
                            <textarea class="form-control" id="f-description" placeholder="Descripción del servicio..."
                                      style="border:1.5px solid #e2e8f0;border-radius:.5rem;font-size:.9rem;"></textarea>
                        </div>
                    </div>

                    <p class="section-label"><i class="ri-money-dollar-circle-line me-1"></i>Precio y estado</p>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">Precio</label>
                            <div class="input-group price-group">
                                <span class="input-group-text">RD$</span>
                                <input type="number" class="form-control" id="f-price"
                                       min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="invalid-feedback d-block" id="err-price"></div>
                        </div>
                        <div class="col-md-5 d-flex align-items-end pb-1">
                            <div class="d-flex align-items-center gap-3 p-3 rounded w-100"
                                 style="background:#f8faff;border:1.5px solid #e2e8f0;">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="font-size:.85rem;">Estado</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="f-active"
                                           style="width:2.5rem;height:1.3rem;" checked>
                                    <label class="form-check-label fw-semibold ms-1" id="active-label" for="f-active"
                                           style="font-size:.85rem;">Activo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-save-svc" onclick="saveService()">
                    <span class="spinner-border spinner-border-sm d-none" id="svc-save-spinner"></span>
                    <i class="ri-save-line" id="svc-save-icon"></i>
                    <span>Guardar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════
     MODAL: Ver Detalle
══════════════════════ --}}
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-stethoscope-line fs-18"></i> Detalle del Servicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="svc-icon mx-auto mb-2" style="width:56px;height:56px;font-size:1.5rem;">
                        <i class="ri-stethoscope-line"></i>
                    </div>
                    <h5 class="mb-1 fw-bold" id="show-name">—</h5>
                    <div id="show-status-badge"></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-warning-subtle text-warning"><i class="ri-money-dollar-circle-line"></i></div>
                    <div>
                        <div class="detail-lbl">Precio</div>
                        <div class="detail-val fw-bold text-primary fs-5" id="show-price">—</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-file-text-line"></i></div>
                    <div>
                        <div class="detail-lbl">Descripción</div>
                        <div class="detail-val" id="show-description">—</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-success-subtle text-success"><i class="ri-calendar-line"></i></div>
                    <div>
                        <div class="detail-lbl">Registrado</div>
                        <div class="detail-val" id="show-created">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="switchToEdit()">
                    <i class="ri-pencil-line me-1"></i> Editar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════
     MODAL: Eliminar
══════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="svc-icon mx-auto mb-3"
                     style="width:60px;height:60px;font-size:1.5rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">
                    <i class="ri-stethoscope-line"></i>
                </div>
                <p class="mb-1 fs-5 fw-semibold" id="delete-name-display">Servicio</p>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Esta acción es <strong>irreversible</strong>. ¿Confirmas la eliminación?
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="min-width:100px;">Cancelar</button>
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2"
                        id="btn-confirm-delete" style="min-width:120px;" onclick="confirmDelete()">
                    <span class="spinner-border spinner-border-sm d-none" id="delete-spinner"></span>
                    <i class="ri-delete-bin-line" id="delete-icon"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// URLs via route() de Laravel
const URL_STORE = "{{ route('services.store') }}";
const URL_BASE  = "{{ route('services.index') }}".replace(/\/+$/, '');
function urlUpdate(id)   { return URL_BASE + '/' + id; }
function urlDestroy(id)  { return URL_BASE + '/' + id; }
function urlShowData(id) { return URL_BASE + '/' + id + '/show-data'; }
function urlEditData(id) { return URL_BASE + '/' + id + '/edit-data'; }

let svcModal, showModal, deleteModal;
let deleteSvcId   = null;
let currentShowId = null;

document.addEventListener('DOMContentLoaded', () => {
    svcModal    = new bootstrap.Modal(document.getElementById('svcModal'));
    showModal   = new bootstrap.Modal(document.getElementById('showModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif

    document.getElementById('f-active').addEventListener('change', function () {
        document.getElementById('active-label').textContent = this.checked ? 'Activo' : 'Inactivo';
    });
});

// ── Search & filter ───────────────────────────────────────
document.getElementById('search-input').addEventListener('input', filterTable);
document.getElementById('status-filter').addEventListener('change', filterTable);

function filterTable() {
    const q      = document.getElementById('search-input').value.toLowerCase().trim();
    const status = document.getElementById('status-filter').value;
    const rows   = document.querySelectorAll('#svc-tbody tr[data-id]');
    let visible  = 0;
    rows.forEach(tr => {
        const matchQ = !q      || (tr.dataset.name || '').includes(q);
        const matchS = status === '' || tr.dataset.status === status;
        const show   = matchQ && matchS;
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('no-results').classList.toggle('d-none', visible > 0);
}

// ── CREATE ────────────────────────────────────────────────
function openCreateModal() {
    clearSvcForm();
    document.getElementById('svc-modal-title').innerHTML = '<i class="ri-stethoscope-line fs-18"></i> Nuevo Servicio';
    document.getElementById('svc-form-method').value     = 'POST';
    document.getElementById('svc-form-id').value         = '';
    svcModal.show();
}

// ── EDIT ──────────────────────────────────────────────────
async function openEditModal(id) {
    clearSvcForm();
    document.getElementById('svc-modal-title').innerHTML = '<i class="ri-pencil-line fs-18"></i> Editar Servicio';
    document.getElementById('svc-form-method').value     = 'PUT';
    document.getElementById('svc-form-id').value         = id;

    try {
        const res = await fetch(urlEditData(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        document.getElementById('f-name').value        = d.name        || '';
        document.getElementById('f-description').value = d.description || '';
        document.getElementById('f-price').value       = d.price       || '';

        const active = d.active == 1 || d.active === true;
        document.getElementById('f-active').checked            = active;
        document.getElementById('active-label').textContent    = active ? 'Activo' : 'Inactivo';

        svcModal.show();
    } catch {
        showToast('Error al cargar los datos.', 'error');
    }
}

// ── SAVE ──────────────────────────────────────────────────
async function saveService() {
    const id     = document.getElementById('svc-form-id').value;
    const method = document.getElementById('svc-form-method').value;
    const url    = id ? urlUpdate(id) : URL_STORE;

    const payload = {
        name        : document.getElementById('f-name').value.trim(),
        description : document.getElementById('f-description').value.trim() || null,
        price       : parseFloat(document.getElementById('f-price').value) || 0,
        active      : document.getElementById('f-active').checked ? 1 : 0,
    };

    clearSvcErrors();
    setBtnLoading('btn-save-svc', 'svc-save-spinner', 'svc-save-icon', true);

    try {
        const res  = await fetch(url, {
            method : 'POST',
            headers: {
                'Content-Type'           : 'application/json',
                'Accept'                 : 'application/json',
                'X-CSRF-TOKEN'           : CSRF,
                'X-HTTP-Method-Override' : method,
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (!res.ok) {
            if (data.errors) showSvcFieldErrors(data.errors);
            else showSvcAlert(data.message || 'Error al guardar.', 'danger');
            return;
        }

        svcModal.hide();
        showToast(data.message || 'Servicio guardado correctamente.', 'success');
        setTimeout(() => location.reload(), 800);

    } catch {
        showSvcAlert('Error de conexión.', 'danger');
    } finally {
        setBtnLoading('btn-save-svc', 'svc-save-spinner', 'svc-save-icon', false);
    }
}

// ── SHOW DETAIL ───────────────────────────────────────────
async function openShowModal(id) {
    currentShowId = id;
    document.getElementById('show-name').textContent        = '…';
    document.getElementById('show-price').textContent       = '…';
    document.getElementById('show-description').textContent = '…';
    document.getElementById('show-created').textContent     = '…';
    document.getElementById('show-status-badge').innerHTML  = '';
    showModal.show();

    try {
        const res = await fetch(urlShowData(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        document.getElementById('show-name').textContent        = d.name;
        document.getElementById('show-price').textContent       = 'RD$ ' + parseFloat(d.price).toLocaleString('es-DO', {minimumFractionDigits:2});
        document.getElementById('show-description').textContent = d.description || '—';
        document.getElementById('show-created').textContent     = d.created_at;

        const isActive = d.active == 1 || d.active === true;
        document.getElementById('show-status-badge').innerHTML = isActive
            ? '<span class="status-pill status-active"><span class="dot"></span>Activo</span>'
            : '<span class="status-pill status-inactive"><span class="dot"></span>Inactivo</span>';

    } catch {
        showToast('Error al cargar los datos.', 'error');
    }
}

function switchToEdit() {
    showModal.hide();
    setTimeout(() => openEditModal(currentShowId), 300);
}

// ── DELETE ────────────────────────────────────────────────
function openDeleteModal(id, name) {
    deleteSvcId = id;
    document.getElementById('delete-name-display').textContent = name;
    deleteModal.show();
}

async function confirmDelete() {
    setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', true);
    try {
        const res  = await fetch(urlDestroy(deleteSvcId), {
            method : 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-HTTP-Method-Override':'DELETE' },
            body   : JSON.stringify({}),
        });
        const data = await res.json();
        deleteModal.hide();

        if (!res.ok) { showToast(data.message || 'Error al eliminar.', 'error'); return; }

        showToast(data.message || 'Servicio eliminado correctamente.', 'success');

        const row = document.querySelector('#svc-tbody tr[data-id="' + deleteSvcId + '"]');
        if (row) {
            row.style.transition = 'opacity .3s,transform .3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(30px)';
            setTimeout(() => { row.remove(); }, 300);
        }
    } catch { showToast('Error de conexión.', 'error'); }
    finally  { setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', false); }
}

// ── Helpers ───────────────────────────────────────────────
function clearSvcForm() {
    ['f-name','f-description','f-price'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('f-active').checked         = true;
    document.getElementById('active-label').textContent = 'Activo';
    clearSvcErrors();
    document.getElementById('svc-modal-alert').className = 'alert d-none';
}

function clearSvcErrors() {
    document.querySelectorAll('#svc-form .invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('#svc-form .form-control').forEach(el => {
        el.classList.remove('is-invalid','is-valid');
    });
    document.getElementById('err-price').textContent = '';
}

function showSvcFieldErrors(errors) {
    const map = { name:'f-name', price:'f-price', description:'f-description' };
    Object.entries(errors).forEach(([field, msgs]) => {
        const input = document.getElementById(map[field]);
        const err   = document.getElementById('err-' + field);
        if (input) input.classList.add('is-invalid');
        if (err)   err.textContent = msgs[0];
    });
}

function showSvcAlert(msg, type) {
    const el = document.getElementById('svc-modal-alert');
    el.className   = 'alert alert-' + type;
    el.textContent = msg;
}

function setBtnLoading(btnId, spinnerId, iconId, loading) {
    document.getElementById(btnId).disabled = loading;
    document.getElementById(spinnerId).classList.toggle('d-none', !loading);
    document.getElementById(iconId).classList.toggle('d-none', loading);
}

function showToast(msg, type) {
    type = type || 'success';
    const div = document.createElement('div');
    div.className = 'toast-item toast-' + type;
    div.innerHTML = '<i class="ri-' + (type==='success'?'checkbox-circle':'error-warning') + '-line fs-16"></i>' + msg;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(function () {
        div.style.transition = 'opacity .4s'; div.style.opacity = '0';
        setTimeout(function () { div.remove(); }, 400);
    }, 3500);
}
</script>
@endpush

</x-app-layout>