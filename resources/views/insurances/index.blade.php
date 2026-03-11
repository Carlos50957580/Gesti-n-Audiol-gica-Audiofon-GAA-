<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    <style>
        /* ── Stat cards ── */
        .stat-card {
            border: none; border-radius: .75rem; overflow: hidden; position: relative;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(64,81,137,.15) !important; }
        .stat-icon {
            width: 52px; height: 52px; border-radius: .6rem;
            display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;
        }
        .stat-bg { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: .05; line-height: 1; pointer-events: none; }

        /* ── Table ── */
        .ins-table th {
            font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
            color: #8098bb; border-bottom: 2px solid #e9ecef; padding: .85rem 1rem; white-space: nowrap;
        }
        .ins-table td { padding: .82rem 1rem; vertical-align: middle; }
        .ins-table tbody tr { transition: background .15s; border-bottom: 1px solid #f3f5f9; }
        .ins-table tbody tr:hover { background: #f8faff; }
        .ins-table tbody tr:last-child { border-bottom: none; }

        /* ── Insurance logo/avatar ── */
        .ins-avatar {
            width: 40px; height: 40px; border-radius: .5rem; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .95rem; color: #fff;
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
        }

        /* ── Coverage bar ── */
        .coverage-wrap { display: flex; align-items: center; gap: .5rem; min-width: 110px; }
        .coverage-bar-bg { flex: 1; height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; }
        .coverage-bar-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #405189, #0ab39c); transition: width .6s ease; }
        .coverage-pct { font-size: .8rem; font-weight: 700; color: #405189; white-space: nowrap; }

        /* ── Status badge ── */
        .status-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .28rem .7rem; border-radius: 2rem; font-size: .75rem; font-weight: 600;
        }
        .status-pill .dot { width: 7px; height: 7px; border-radius: 50%; }
        .status-active   { background: #d1fae5; color: #065f46; }
        .status-active .dot   { background: #10b981; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .status-inactive .dot { background: #ef4444; }

        /* ── Action buttons ── */
        .btn-action {
            width: 32px; height: 32px; padding: 0; border: none; border-radius: .4rem;
            display: inline-flex; align-items: center; justify-content: center; transition: all .15s;
        }
        .btn-action:hover { transform: scale(1.12); }

        /* ── Search ── */
        #search-input {
            border-radius: 2rem; padding-left: 2.4rem;
            border: 1.5px solid #e2e8f0; font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        #search-input:focus { border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.12); }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #8098bb; pointer-events: none; }

        /* ── Modal headers ── */
        .mh-primary { background: linear-gradient(135deg,#405189,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-info    { background: linear-gradient(135deg,#299cdb,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-danger  { background: linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-primary .btn-close,
        .mh-info .btn-close,
        .mh-danger .btn-close { filter: invert(1); }

        /* ── Form ── */
        .form-floating > .form-control,
        .form-floating > .form-select {
            border: 1.5px solid #e2e8f0; border-radius: .5rem;
        }
        .form-floating > .form-control:focus,
        .form-floating > .form-select:focus {
            border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.1);
        }
        .section-label {
            font-size: .7rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            color: #8098bb; border-bottom: 1px solid #f0f2f7; padding-bottom: .4rem; margin-bottom: .9rem;
        }

        /* ── Detail view ── */
        .detail-row {
            display: flex; gap: .75rem; align-items: flex-start;
            padding: .7rem 0; border-bottom: 1px solid #f3f5f9;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-icon { width: 36px; height: 36px; border-radius: .4rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem; }
        .detail-lbl  { font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #8098bb; }
        .detail-val  { font-size: .91rem; font-weight: 500; color: #344563; margin-top: .1rem; }

        /* ── Animations ── */
        @keyframes fadeInUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim-row { animation: fadeInUp .3s ease both; }
        .anim-row:nth-child(1){animation-delay:.03s}.anim-row:nth-child(2){animation-delay:.07s}
        .anim-row:nth-child(3){animation-delay:.11s}.anim-row:nth-child(4){animation-delay:.15s}
        .anim-row:nth-child(5){animation-delay:.19s}.anim-row:nth-child(6){animation-delay:.23s}
        .anim-row:nth-child(7){animation-delay:.27s}.anim-row:nth-child(8){animation-delay:.31s}

        /* ── Toast ── */
        #toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
        .toast-item {
            min-width:280px; padding:.85rem 1.1rem; border-radius:.5rem; color:#fff;
            font-size:.88rem; font-weight:500; display:flex; align-items:center; gap:.6rem;
            box-shadow:0 4px 20px rgba(0,0,0,.18);
            animation: toastIn .3s ease;
        }
        @keyframes toastIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
        .toast-success { background: linear-gradient(135deg,#0ab39c,#3d9f80); }
        .toast-error   { background: linear-gradient(135deg,#e74c3c,#c0392b); }

        /* ── Coverage slider ── */
        input[type=range].coverage-slider {
            -webkit-appearance: none; width: 100%; height: 6px;
            border-radius: 3px; background: #e9ecef; outline: none;
        }
        input[type=range].coverage-slider::-webkit-slider-thumb {
            -webkit-appearance: none; width: 20px; height: 20px; border-radius: 50%;
            background: linear-gradient(135deg,#405189,#0ab39c); cursor: pointer;
            box-shadow: 0 2px 8px rgba(64,81,137,.4);
        }
    </style>

    <div id="toast-container"></div>

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Aseguradoras</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Aseguradoras</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-shield-check-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Total</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-total">{{ $insurances->total() }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-shield-check-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-checkbox-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Activas</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-active">{{ $insurances->getCollection()->where('active',1)->count() }}</div>
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
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Inactivas</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-inactive">{{ $insurances->getCollection()->where('active',0)->count() }}</div>
                    </div>
                    <div class="stat-bg text-danger"><i class="ri-close-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-percent-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Cobertura prom.</div>
                        @php
                            $col = $insurances->getCollection();
                            $avg = $col->count() ? round($col->avg('coverage_percentage'), 1) : 0;
                        @endphp
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-avg">{{ $avg }}%</div>
                    </div>
                    <div class="stat-bg text-warning"><i class="ri-percent-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main table card --}}
    <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
            <h5 class="card-title mb-0 flex-grow-1">Lista de Aseguradoras</h5>
            <div class="position-relative" style="width:230px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="search-input" class="form-control" placeholder="Buscar aseguradora...">
            </div>
            <select id="status-filter" class="form-select form-select-sm"
                    style="width:140px;border-radius:2rem;border:1.5px solid #e2e8f0;font-size:.85rem;">
                <option value="">Todos</option>
                <option value="1">Activas</option>
                <option value="0">Inactivas</option>
            </select>
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                    style="border-radius:2rem;padding:.4rem 1rem;" onclick="openCreateModal()">
                <i class="ri-add-line"></i> Nueva
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table ins-table mb-0">
                    <thead>
                        <tr>
                            <th>Aseguradora</th>
                            <th>Contacto</th>
                            <th>Tel. Autorización</th>
                            <th>Cobertura</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ins-tbody">
                        @forelse($insurances as $ins)
                        <tr class="anim-row"
                            data-id="{{ $ins->id }}"
                            data-name="{{ strtolower($ins->name) }}"
                            data-status="{{ $ins->active ? '1' : '0' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="ins-avatar">{{ strtoupper(substr($ins->name,0,2)) }}</div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.92rem;">{{ $ins->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">ID #{{ $ins->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:.85rem;">
                                    @if($ins->phone)
                                        <div><i class="ri-phone-line me-1 text-muted"></i>{{ $ins->phone }}</div>
                                    @endif
                                    @if($ins->email)
                                        <div class="text-muted mt-1"><i class="ri-mail-line me-1"></i>{{ $ins->email }}</div>
                                    @endif
                                    @if(!$ins->phone && !$ins->email)
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($ins->authorization_phone)
                                    <span style="font-size:.85rem;">
                                        <i class="ri-phone-lock-line me-1 text-muted"></i>{{ $ins->authorization_phone }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="coverage-wrap">
                                    <div class="coverage-bar-bg">
                                        <div class="coverage-bar-fill" style="width:{{ $ins->coverage_percentage }}%"></div>
                                    </div>
                                    <span class="coverage-pct">{{ $ins->coverage_percentage }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($ins->active)
                                    <span class="status-pill status-active">
                                        <span class="dot"></span> Activa
                                    </span>
                                @else
                                    <span class="status-pill status-inactive">
                                        <span class="dot"></span> Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-action bg-info-subtle text-info"
                                            title="Ver detalle" onclick="openShowModal({{ $ins->id }})">
                                        <i class="ri-eye-fill fs-13"></i>
                                    </button>
                                    <button type="button" class="btn btn-action bg-warning-subtle text-warning"
                                            title="Editar" onclick="openEditModal({{ $ins->id }})">
                                        <i class="ri-pencil-fill fs-13"></i>
                                    </button>
                                    <button type="button" class="btn btn-action bg-danger-subtle text-danger"
                                            title="Eliminar" onclick="openDeleteModal({{ $ins->id }}, '{{ addslashes($ins->name) }}')">
                                        <i class="ri-delete-bin-fill fs-13"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center py-5">
                                    <i class="ri-shield-check-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                    <p class="text-muted mb-0">No hay aseguradoras registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="no-results" class="text-center py-5 d-none">
                <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0">No se encontraron aseguradoras con ese criterio.</p>
            </div>

            @if($insurances->hasPages())
            <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                {{ $insurances->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Crear / Editar
══════════════════════════════════════ --}}
<div class="modal fade" id="insModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-primary py-3">
                <h5 class="modal-title d-flex align-items-center gap-2" id="ins-modal-title">
                    <i class="ri-shield-check-line fs-18"></i> Nueva Aseguradora
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="ins-modal-alert" class="alert d-none mb-3"></div>
                <form id="ins-form" novalidate>
                    <input type="hidden" id="ins-form-id">
                    <input type="hidden" id="ins-form-method" value="POST">

                    {{-- Información general --}}
                    <p class="section-label"><i class="ri-information-line me-1"></i>Información general</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-name" placeholder="Nombre">
                                <label><i class="ri-shield-check-line me-1 text-muted"></i>Nombre de la aseguradora</label>
                                <div class="invalid-feedback" id="err-name"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="f-email" placeholder="Email">
                                <label><i class="ri-mail-line me-1 text-muted"></i>Correo electrónico</label>
                                <div class="invalid-feedback" id="err-email"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-phone" placeholder="Teléfono">
                                <label><i class="ri-phone-line me-1 text-muted"></i>Teléfono</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-authorization-phone" placeholder="Tel. Autorización">
                                <label><i class="ri-phone-lock-line me-1 text-muted"></i>Teléfono de autorización</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-address" placeholder="Dirección">
                                <label><i class="ri-map-pin-line me-1 text-muted"></i>Dirección <span class="text-muted fw-normal">(opcional)</span></label>
                            </div>
                        </div>
                    </div>

                    {{-- Cobertura y estado --}}
                    <p class="section-label"><i class="ri-percent-line me-1"></i>Cobertura y estado</p>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold mb-1">
                                Porcentaje de cobertura:
                                <span class="text-primary fw-bold ms-1" id="coverage-display">0%</span>
                            </label>
                            <input type="range" class="coverage-slider" id="f-coverage"
                                   min="0" max="100" step="1" value="0">
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0%</small>
                                <small class="text-muted">50%</small>
                                <small class="text-muted">100%</small>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-floating w-100">
                                <input type="number" class="form-control" id="f-coverage-input"
                                       min="0" max="100" step="0.01" placeholder="0">
                                <label>% exacto</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-3 p-3 rounded"
                                 style="background:#f8faff;border:1.5px solid #e2e8f0;">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="font-size:.9rem;">Estado de la aseguradora</div>
                                    <small class="text-muted">Controla si aparece disponible al facturar</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="f-active"
                                           style="width:2.5rem;height:1.3rem;" checked>
                                    <label class="form-check-label fw-semibold ms-2" id="active-label" for="f-active">Activa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-save-ins" onclick="saveInsurance()">
                    <span class="spinner-border spinner-border-sm d-none" id="ins-save-spinner"></span>
                    <i class="ri-save-line" id="ins-save-icon"></i>
                    <span>Guardar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Ver Detalle
══════════════════════════════════════ --}}
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-shield-check-line fs-18"></i> Detalle de Aseguradora
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Header --}}
                <div class="text-center mb-4">
                    <div class="ins-avatar mx-auto mb-2" style="width:58px;height:58px;font-size:1.3rem;" id="show-avatar">AS</div>
                    <h5 class="mb-0 fw-bold" id="show-name">—</h5>
                    <div class="mt-1" id="show-status-badge"></div>
                </div>
                {{-- Details --}}
                <div class="detail-row">
                    <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-phone-line"></i></div>
                    <div><div class="detail-lbl">Teléfono</div><div class="detail-val" id="show-phone">—</div></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-info-subtle text-info"><i class="ri-mail-line"></i></div>
                    <div><div class="detail-lbl">Correo</div><div class="detail-val" id="show-email">—</div></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-warning-subtle text-warning"><i class="ri-phone-lock-line"></i></div>
                    <div><div class="detail-lbl">Tel. Autorización</div><div class="detail-val" id="show-auth-phone">—</div></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-success-subtle text-success"><i class="ri-map-pin-line"></i></div>
                    <div><div class="detail-lbl">Dirección</div><div class="detail-val" id="show-address">—</div></div>
                </div>
                <div class="detail-row">
                    <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-percent-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Cobertura</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="coverage-bar-bg flex-grow-1">
                                <div class="coverage-bar-fill" id="show-coverage-bar" style="width:0%"></div>
                            </div>
                            <span class="coverage-pct" id="show-coverage-pct">0%</span>
                        </div>
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

{{-- ══════════════════════════════════════
     MODAL: Confirmar Eliminar
══════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Aseguradora</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="ins-avatar mx-auto mb-3" style="width:60px;height:60px;font-size:1.3rem;background:linear-gradient(135deg,#e74c3c,#c0392b);" id="delete-avatar">AS</div>
                <p class="mb-1 fs-5 fw-semibold" id="delete-name-display">Aseguradora</p>
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
const URL_STORE  = "{{ route('insurances.store') }}";
const URL_BASE   = "{{ route('insurances.index') }}".replace(/\/+$/, '');
function urlUpdate(id)   { return URL_BASE + '/' + id; }
function urlDestroy(id)  { return URL_BASE + '/' + id; }
function urlShowData(id) { return URL_BASE + '/' + id + '/show-data'; }
function urlEditData(id) { return URL_BASE + '/' + id + '/edit-data'; }

let insModal, showModal, deleteModal;
let deleteInsId   = null;
let currentShowId = null;

document.addEventListener('DOMContentLoaded', () => {
    insModal    = new bootstrap.Modal(document.getElementById('insModal'));
    showModal   = new bootstrap.Modal(document.getElementById('showModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif

    // Coverage slider <-> input sync
    const slider = document.getElementById('f-coverage');
    const numInput = document.getElementById('f-coverage-input');
    slider.addEventListener('input', function () {
        numInput.value = this.value;
        document.getElementById('coverage-display').textContent = this.value + '%';
    });
    numInput.addEventListener('input', function () {
        let v = Math.min(100, Math.max(0, parseFloat(this.value) || 0));
        slider.value = v;
        document.getElementById('coverage-display').textContent = v + '%';
    });

    // Active toggle label
    document.getElementById('f-active').addEventListener('change', function () {
        document.getElementById('active-label').textContent = this.checked ? 'Activa' : 'Inactiva';
    });
});

// ── Search & filter ───────────────────────────────────────
document.getElementById('search-input').addEventListener('input', filterTable);
document.getElementById('status-filter').addEventListener('change', filterTable);

function filterTable() {
    const q      = document.getElementById('search-input').value.toLowerCase().trim();
    const status = document.getElementById('status-filter').value;
    const rows   = document.querySelectorAll('#ins-tbody tr[data-id]');
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
    clearInsForm();
    document.getElementById('ins-modal-title').innerHTML = '<i class="ri-shield-check-line fs-18"></i> Nueva Aseguradora';
    document.getElementById('ins-form-method').value     = 'POST';
    document.getElementById('ins-form-id').value         = '';
    insModal.show();
}

// ── EDIT ──────────────────────────────────────────────────
async function openEditModal(id) {
    clearInsForm();
    document.getElementById('ins-modal-title').innerHTML = '<i class="ri-pencil-line fs-18"></i> Editar Aseguradora';
    document.getElementById('ins-form-method').value     = 'PUT';
    document.getElementById('ins-form-id').value         = id;

    try {
        const res  = await fetch(urlEditData(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        document.getElementById('f-name').value                = d.name || '';
        document.getElementById('f-email').value               = d.email || '';
        document.getElementById('f-phone').value               = d.phone || '';
        document.getElementById('f-authorization-phone').value = d.authorization_phone || '';
        document.getElementById('f-address').value             = d.address || '';

        const cov = parseFloat(d.coverage_percentage) || 0;
        document.getElementById('f-coverage').value       = cov;
        document.getElementById('f-coverage-input').value = cov;
        document.getElementById('coverage-display').textContent = cov + '%';

        const active = d.active == 1 || d.active === true;
        document.getElementById('f-active').checked = active;
        document.getElementById('active-label').textContent = active ? 'Activa' : 'Inactiva';

        insModal.show();
    } catch {
        showToast('Error al cargar los datos.', 'error');
    }
}

// ── SAVE ──────────────────────────────────────────────────
async function saveInsurance() {
    const id     = document.getElementById('ins-form-id').value;
    const method = document.getElementById('ins-form-method').value;
    const url    = id ? urlUpdate(id) : URL_STORE;

    const covVal = parseFloat(document.getElementById('f-coverage-input').value) || 0;

    const payload = {
        name                : document.getElementById('f-name').value.trim(),
        email               : document.getElementById('f-email').value.trim()               || null,
        phone               : document.getElementById('f-phone').value.trim()               || null,
        authorization_phone : document.getElementById('f-authorization-phone').value.trim() || null,
        address             : document.getElementById('f-address').value.trim()             || null,
        coverage_percentage : Math.min(100, Math.max(0, covVal)),
        active              : document.getElementById('f-active').checked ? 1 : 0,
    };

    clearInsErrors();
    setBtnLoading('btn-save-ins', 'ins-save-spinner', 'ins-save-icon', true);

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
            if (data.errors) showInsFieldErrors(data.errors);
            else showInsAlert(data.message || 'Error al guardar.', 'danger');
            return;
        }

        insModal.hide();
        showToast(data.message || 'Aseguradora guardada correctamente.', 'success');
        setTimeout(() => location.reload(), 800);

    } catch {
        showInsAlert('Error de conexión.', 'danger');
    } finally {
        setBtnLoading('btn-save-ins', 'ins-save-spinner', 'ins-save-icon', false);
    }
}

// ── SHOW DETAIL ───────────────────────────────────────────
async function openShowModal(id) {
    currentShowId = id;
    // Reset
    ['show-name','show-phone','show-email','show-auth-phone','show-address'].forEach(i => {
        document.getElementById(i).textContent = '…';
    });
    document.getElementById('show-coverage-bar').style.width = '0%';
    document.getElementById('show-coverage-pct').textContent = '…';
    document.getElementById('show-avatar').textContent       = '…';
    document.getElementById('show-status-badge').innerHTML   = '';
    showModal.show();

    try {
        const res  = await fetch(urlShowData(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        document.getElementById('show-name').textContent      = d.name;
        document.getElementById('show-avatar').textContent    = d.name.substring(0,2).toUpperCase();
        document.getElementById('show-phone').textContent     = d.phone      || '—';
        document.getElementById('show-email').textContent     = d.email      || '—';
        document.getElementById('show-auth-phone').textContent= d.authorization_phone || '—';
        document.getElementById('show-address').textContent   = d.address    || '—';

        const cov = parseFloat(d.coverage_percentage) || 0;
        document.getElementById('show-coverage-bar').style.width = cov + '%';
        document.getElementById('show-coverage-pct').textContent = cov + '%';

        const isActive = d.active == 1 || d.active === true;
        document.getElementById('show-status-badge').innerHTML = isActive
            ? '<span class="status-pill status-active"><span class="dot"></span>Activa</span>'
            : '<span class="status-pill status-inactive"><span class="dot"></span>Inactiva</span>';

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
    deleteInsId = id;
    document.getElementById('delete-name-display').textContent = name;
    document.getElementById('delete-avatar').textContent       = name.substring(0,2).toUpperCase();
    deleteModal.show();
}

async function confirmDelete() {
    setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', true);
    try {
        const res  = await fetch(urlDestroy(deleteInsId), {
            method : 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-HTTP-Method-Override':'DELETE' },
            body   : JSON.stringify({}),
        });
        const data = await res.json();
        deleteModal.hide();

        if (!res.ok) { showToast(data.message || 'Error al eliminar.', 'error'); return; }

        showToast(data.message || 'Aseguradora eliminada correctamente.', 'success');

        const row = document.querySelector('#ins-tbody tr[data-id="' + deleteInsId + '"]');
        if (row) {
            row.style.transition = 'opacity .3s, transform .3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(30px)';
            setTimeout(() => { row.remove(); }, 300);
        }

    } catch { showToast('Error de conexión.', 'error'); }
    finally  { setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', false); }
}

// ── Helpers ───────────────────────────────────────────────
function clearInsForm() {
    ['f-name','f-email','f-phone','f-authorization-phone','f-address'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('f-coverage').value       = 0;
    document.getElementById('f-coverage-input').value = 0;
    document.getElementById('coverage-display').textContent = '0%';
    document.getElementById('f-active').checked = true;
    document.getElementById('active-label').textContent = 'Activa';
    clearInsErrors();
    document.getElementById('ins-modal-alert').className = 'alert d-none';
}

function clearInsErrors() {
    document.querySelectorAll('#ins-form .invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('#ins-form .form-control').forEach(el => {
        el.classList.remove('is-invalid','is-valid');
    });
}

function showInsFieldErrors(errors) {
    const map = { name:'f-name', email:'f-email', phone:'f-phone', authorization_phone:'f-authorization-phone', coverage_percentage:'f-coverage-input' };
    Object.entries(errors).forEach(([field, msgs]) => {
        const input = document.getElementById(map[field]);
        const err   = document.getElementById('err-' + field);
        if (input) input.classList.add('is-invalid');
        if (err)   err.textContent = msgs[0];
    });
}

function showInsAlert(msg, type) {
    const el = document.getElementById('ins-modal-alert');
    el.className = 'alert alert-' + type;
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