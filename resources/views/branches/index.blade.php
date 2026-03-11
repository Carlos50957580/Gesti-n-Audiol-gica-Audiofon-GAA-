<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    <style>
        /* ── Page header ── */
        .branches-header { position: relative; }

        /* ── Stat cards ── */
        .stat-card {
            border: none; border-radius: .75rem;
            transition: transform .2s, box-shadow .2s; overflow: hidden; position: relative;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(64,81,137,.15) !important; }
        .stat-card .stat-icon {
            width: 52px; height: 52px; border-radius: .6rem;
            display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
        }
        .stat-bg { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: .05; line-height: 1; }

        /* ── Table ── */
        .branches-table th {
            font-size: .7rem; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: #8098bb;
            border-bottom: 2px solid #e9ecef; padding: .85rem 1rem; white-space: nowrap;
        }
        .branches-table td { padding: .85rem 1rem; vertical-align: middle; }
        .branches-table tbody tr {
            transition: background .15s; border-bottom: 1px solid #f3f5f9;
        }
        .branches-table tbody tr:hover { background: #f8faff; }
        .branches-table tbody tr:last-child { border-bottom: none; }

        /* ── Branch avatar ── */
        .branch-icon {
            width: 40px; height: 40px; border-radius: .5rem; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            color: #fff;
        }

        /* ── Action btns ── */
        .btn-action {
            width: 32px; height: 32px; padding: 0;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: .4rem; border: none; transition: all .15s;
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

        /* ── Empty ── */
        .empty-state { padding: 3rem 1rem; }

        /* ── Modal ── */
        .modal-header-gradient {
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            color: white; border-radius: .5rem .5rem 0 0;
        }
        .modal-header-gradient .btn-close { filter: invert(1); }
        .modal-header-info {
            background: linear-gradient(135deg, #299cdb 0%, #0ab39c 100%);
            color: white; border-radius: .5rem .5rem 0 0;
        }
        .modal-header-info .btn-close { filter: invert(1); }
        .modal-header-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white; border-radius: .5rem .5rem 0 0;
        }
        .modal-header-danger .btn-close { filter: invert(1); }

        /* ── Form fields ── */
        .form-floating > .form-control,
        .form-floating > .form-select {
            border: 1.5px solid #e2e8f0; border-radius: .5rem;
        }
        .form-floating > .form-control:focus,
        .form-floating > .form-select:focus {
            border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.1);
        }
        .form-section-title {
            font-size: .7rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #8098bb;
            border-bottom: 1px solid #f0f2f7; padding-bottom: .5rem; margin-bottom: 1rem;
        }

        /* ── Show detail card ── */
        .detail-item { display: flex; gap: .75rem; align-items: flex-start; padding: .75rem 0; border-bottom: 1px solid #f3f5f9; }
        .detail-item:last-child { border-bottom: none; }
        .detail-icon { width: 36px; height: 36px; border-radius: .4rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem; }
        .detail-label { font-size: .72rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: #8098bb; }
        .detail-value { font-size: .92rem; font-weight: 500; color: #344563; margin-top: .1rem; }

        /* ── Animations ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-row { animation: fadeInUp .3s ease both; }
        .anim-row:nth-child(1){ animation-delay:.03s }
        .anim-row:nth-child(2){ animation-delay:.07s }
        .anim-row:nth-child(3){ animation-delay:.11s }
        .anim-row:nth-child(4){ animation-delay:.15s }
        .anim-row:nth-child(5){ animation-delay:.19s }
        .anim-row:nth-child(6){ animation-delay:.23s }
        .anim-row:nth-child(7){ animation-delay:.27s }
        .anim-row:nth-child(8){ animation-delay:.31s }

        /* ── Toast ── */
        #toast-container {
            position: fixed; top: 1.2rem; right: 1.2rem; z-index: 9999;
            display: flex; flex-direction: column; gap: .5rem;
        }
        .toast-item {
            min-width: 280px; padding: .85rem 1.1rem; border-radius: .5rem;
            color: #fff; font-size: .88rem; font-weight: 500;
            display: flex; align-items: center; gap: .6rem;
            box-shadow: 0 4px 20px rgba(0,0,0,.18);
            animation: toastIn .3s ease;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .toast-success { background: linear-gradient(135deg,#0ab39c,#3d9f80); }
        .toast-error   { background: linear-gradient(135deg,#e74c3c,#c0392b); }
    </style>

    {{-- Toast container --}}
    <div id="toast-container"></div>

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Sucursales</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sucursales</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="ri-building-2-line"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">Total Sucursales</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-total">{{ $branches->total() }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-building-2-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">Con dirección</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-with-address">{{ $branches->getCollection()->whereNotNull('address')->count() }}</div>
                    </div>
                    <div class="stat-bg text-success"><i class="ri-map-pin-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="ri-phone-line"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">Con teléfono</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-with-phone">{{ $branches->getCollection()->whereNotNull('phone')->count() }}</div>
                    </div>
                    <div class="stat-bg text-info"><i class="ri-phone-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main card --}}
    <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
            <h5 class="card-title mb-0 flex-grow-1">Lista de Sucursales</h5>
            <div class="position-relative" style="width:230px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="search-input" class="form-control" placeholder="Buscar sucursal...">
            </div>
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                    style="border-radius:2rem;padding:.4rem 1rem;" onclick="openCreateModal()">
                <i class="ri-add-line"></i> Nueva Sucursal
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table branches-table mb-0">
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Registrada</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="branches-tbody">
                        @forelse($branches as $branch)
                        <tr class="anim-row"
                            data-id="{{ $branch->id }}"
                            data-name="{{ strtolower($branch->name) }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="branch-icon">
                                        <i class="ri-building-2-line"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.92rem;">{{ $branch->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">ID #{{ $branch->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($branch->address)
                                    <span style="font-size:.87rem;">
                                        <i class="ri-map-pin-line me-1 text-muted"></i>{{ $branch->address }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($branch->phone)
                                    <span style="font-size:.87rem;">
                                        <i class="ri-phone-line me-1 text-muted"></i>{{ $branch->phone }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:.82rem;">
                                    {{ $branch->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button"
                                            class="btn btn-action bg-info-subtle text-info"
                                            title="Ver detalle"
                                            onclick="openShowModal({{ $branch->id }})">
                                        <i class="ri-eye-fill fs-13"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-action bg-warning-subtle text-warning"
                                            title="Editar"
                                            onclick="openEditModal({{ $branch->id }})">
                                        <i class="ri-pencil-fill fs-13"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-action bg-danger-subtle text-danger"
                                            title="Eliminar"
                                            onclick="openDeleteModal({{ $branch->id }}, '{{ addslashes($branch->name) }}')">
                                        <i class="ri-delete-bin-fill fs-13"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state text-center">
                                    <i class="ri-building-2-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                    <p class="text-muted mb-0">No hay sucursales registradas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="no-results" class="empty-state text-center d-none">
                <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0">No se encontraron sucursales con ese nombre.</p>
            </div>

            @if($branches->hasPages())
            <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                {{ $branches->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Crear / Editar Sucursal
══════════════════════════════════════ --}}
<div class="modal fade" id="branchModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header modal-header-gradient py-3" id="branch-modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="branch-modal-title">
                    <i class="ri-building-2-line fs-18"></i> Nueva Sucursal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="branch-modal-alert" class="alert d-none mb-3"></div>
                <form id="branch-form" novalidate>
                    <input type="hidden" id="branch-form-id">
                    <input type="hidden" id="branch-form-method" value="POST">

                    <p class="form-section-title"><i class="ri-information-line me-1"></i>Información de la sucursal</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-name" placeholder="Nombre">
                                <label for="f-name"><i class="ri-building-2-line me-1 text-muted"></i>Nombre de la sucursal</label>
                                <div class="invalid-feedback" id="err-name"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-address" placeholder="Dirección">
                                <label for="f-address"><i class="ri-map-pin-line me-1 text-muted"></i>Dirección <span class="text-muted fw-normal">(opcional)</span></label>
                                <div class="invalid-feedback" id="err-address"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-phone" placeholder="Teléfono">
                                <label for="f-phone"><i class="ri-phone-line me-1 text-muted"></i>Teléfono <span class="text-muted fw-normal">(opcional)</span></label>
                                <div class="invalid-feedback" id="err-phone"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-save-branch" onclick="saveBranch()">
                    <span class="spinner-border spinner-border-sm d-none" id="branch-save-spinner"></span>
                    <i class="ri-save-line" id="branch-save-icon"></i>
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
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header modal-header-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-building-2-line fs-18"></i> Detalle de Sucursal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="branch-icon mx-auto mb-2" style="width:56px;height:56px;font-size:1.5rem;">
                        <i class="ri-building-2-line"></i>
                    </div>
                    <h5 class="mb-0 fw-bold" id="show-name">—</h5>
                    <small class="text-muted" id="show-id">ID #—</small>
                </div>
                <div id="show-details">
                    <div class="detail-item">
                        <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-map-pin-line"></i></div>
                        <div>
                            <div class="detail-label">Dirección</div>
                            <div class="detail-value" id="show-address">—</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon bg-success-subtle text-success"><i class="ri-phone-line"></i></div>
                        <div>
                            <div class="detail-label">Teléfono</div>
                            <div class="detail-value" id="show-phone">—</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon bg-warning-subtle text-warning"><i class="ri-calendar-line"></i></div>
                        <div>
                            <div class="detail-label">Registrada</div>
                            <div class="detail-value" id="show-created">—</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning btn-sm" id="btn-show-edit"
                        onclick="switchToEdit()">
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
            <div class="modal-header modal-header-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Sucursal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="branch-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.5rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">
                    <i class="ri-building-2-line"></i>
                </div>
                <p class="mb-1 fs-5 fw-semibold" id="delete-name-display">Sucursal</p>
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

// URLs desde route() de Laravel
const URL_STORE   = "{{ route('branches.store') }}";
const URL_INDEX   = "{{ route('branches.index') }}".replace(/\/+$/, '');
function urlUpdate(id)    { return URL_INDEX + '/' + id; }
function urlDestroy(id)   { return URL_INDEX + '/' + id; }
function urlShow(id)      { return URL_INDEX + '/' + id + '/show-data'; }
function urlEditData(id)  { return URL_INDEX + '/' + id + '/edit-data'; }

let branchModal, showModal, deleteModal;
let deleteBranchId  = null;
let currentShowId   = null;

document.addEventListener('DOMContentLoaded', () => {
    branchModal = new bootstrap.Modal(document.getElementById('branchModal'));
    showModal   = new bootstrap.Modal(document.getElementById('showModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});

// ── Search ───────────────────────────────────────────────
document.getElementById('search-input').addEventListener('input', function () {
    const q    = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#branches-tbody tr[data-id]');
    let visible = 0;
    rows.forEach(tr => {
        const show = !q || (tr.dataset.name || '').includes(q);
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('no-results').classList.toggle('d-none', visible > 0);
});

// ── CREATE modal ─────────────────────────────────────────
function openCreateModal() {
    clearBranchForm();
    document.getElementById('branch-modal-title').innerHTML = '<i class="ri-building-2-line fs-18"></i> Nueva Sucursal';
    document.getElementById('branch-form-method').value    = 'POST';
    document.getElementById('branch-form-id').value        = '';
    branchModal.show();
}

// ── EDIT modal ───────────────────────────────────────────
async function openEditModal(id) {
    clearBranchForm();
    document.getElementById('branch-modal-title').innerHTML = '<i class="ri-pencil-line fs-18"></i> Editar Sucursal';
    document.getElementById('branch-form-method').value    = 'PUT';
    document.getElementById('branch-form-id').value        = id;

    try {
        const res  = await fetch(urlEditData(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        document.getElementById('f-name').value    = data.name    || '';
        document.getElementById('f-address').value = data.address || '';
        document.getElementById('f-phone').value   = data.phone   || '';
        branchModal.show();
    } catch {
        showToast('Error al cargar los datos.', 'error');
    }
}

// ── SAVE (create or update) ──────────────────────────────
async function saveBranch() {
    const id     = document.getElementById('branch-form-id').value;
    const method = document.getElementById('branch-form-method').value;
    const url    = id ? urlUpdate(id) : URL_STORE;

    const payload = {
        name    : document.getElementById('f-name').value.trim(),
        address : document.getElementById('f-address').value.trim() || null,
        phone   : document.getElementById('f-phone').value.trim()   || null,
    };

    clearBranchErrors();
    setBtnLoading('btn-save-branch', 'branch-save-spinner', 'branch-save-icon', true);

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
            if (data.errors) showBranchFieldErrors(data.errors);
            else showBranchAlert(data.message || 'Error al guardar.', 'danger');
            return;
        }

        branchModal.hide();
        showToast(data.message || 'Sucursal guardada correctamente.', 'success');
        setTimeout(() => location.reload(), 800);

    } catch {
        showBranchAlert('Error de conexión.', 'danger');
    } finally {
        setBtnLoading('btn-save-branch', 'branch-save-spinner', 'branch-save-icon', false);
    }
}

// ── SHOW modal ───────────────────────────────────────────
async function openShowModal(id) {
    currentShowId = id;
    document.getElementById('show-name').textContent    = '…';
    document.getElementById('show-id').textContent      = 'ID #' + id;
    document.getElementById('show-address').textContent = '…';
    document.getElementById('show-phone').textContent   = '…';
    document.getElementById('show-created').textContent = '…';
    showModal.show();

    try {
        const res  = await fetch(urlShow(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        document.getElementById('show-name').textContent    = data.name;
        document.getElementById('show-id').textContent      = 'ID #' + data.id;
        document.getElementById('show-address').textContent = data.address || '—';
        document.getElementById('show-phone').textContent   = data.phone   || '—';
        document.getElementById('show-created').textContent = data.created_at;
    } catch {
        showToast('Error al cargar los datos.', 'error');
    }
}

// Botón "Editar" desde el modal de detalle
function switchToEdit() {
    showModal.hide();
    setTimeout(() => openEditModal(currentShowId), 300);
}

// ── DELETE ───────────────────────────────────────────────
function openDeleteModal(id, name) {
    deleteBranchId = id;
    document.getElementById('delete-name-display').textContent = name;
    deleteModal.show();
}

async function confirmDelete() {
    setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', true);
    try {
        const res  = await fetch(urlDestroy(deleteBranchId), {
            method : 'POST',
            headers: {
                'Content-Type'           : 'application/json',
                'Accept'                 : 'application/json',
                'X-CSRF-TOKEN'           : CSRF,
                'X-HTTP-Method-Override' : 'DELETE',
            },
            body: JSON.stringify({}),
        });
        const data = await res.json();
        deleteModal.hide();

        if (!res.ok) {
            showToast(data.message || 'Error al eliminar.', 'error');
            return;
        }

        showToast(data.message || 'Sucursal eliminada correctamente.', 'success');

        const row = document.querySelector('#branches-tbody tr[data-id="' + deleteBranchId + '"]');
        if (row) {
            row.style.transition = 'opacity .3s, transform .3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(30px)';
            setTimeout(() => { row.remove(); updateStats(); }, 300);
        }
    } catch {
        showToast('Error de conexión.', 'error');
    } finally {
        setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', false);
    }
}

// ── Helpers ──────────────────────────────────────────────
function clearBranchForm() {
    ['f-name', 'f-address', 'f-phone'].forEach(id => {
        document.getElementById(id).value = '';
    });
    clearBranchErrors();
    document.getElementById('branch-modal-alert').className = 'alert d-none';
}

function clearBranchErrors() {
    document.querySelectorAll('#branch-form .invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('#branch-form .form-control').forEach(el => {
        el.classList.remove('is-invalid', 'is-valid');
    });
}

function showBranchFieldErrors(errors) {
    const map = { name: 'f-name', address: 'f-address', phone: 'f-phone' };
    Object.entries(errors).forEach(([field, msgs]) => {
        const input = document.getElementById(map[field]);
        const err   = document.getElementById('err-' + field);
        if (input) input.classList.add('is-invalid');
        if (err)   err.textContent = msgs[0];
    });
}

function showBranchAlert(msg, type) {
    const el = document.getElementById('branch-modal-alert');
    el.className    = 'alert alert-' + type;
    el.textContent  = msg;
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
    div.innerHTML = '<i class="ri-' + (type === 'success' ? 'checkbox-circle' : 'error-warning') + '-line fs-16"></i>' + msg;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(function () {
        div.style.transition = 'opacity .4s';
        div.style.opacity    = '0';
        setTimeout(function () { div.remove(); }, 400);
    }, 3500);
}

function updateStats() {
    const rows = document.querySelectorAll('#branches-tbody tr[data-id]');
    document.getElementById('stat-total').textContent = rows.length;
}
</script>
@endpush

</x-app-layout>