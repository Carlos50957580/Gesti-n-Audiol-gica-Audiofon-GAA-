<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    <style>
        /* ── Page ── */
        .users-header { position: relative; overflow: hidden; }
        .users-header::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            opacity: .06;
            border-radius: .75rem;
        }

        /* ── Stat cards ── */
        .stat-card {
            border: none;
            border-radius: .75rem;
            transition: transform .2s, box-shadow .2s;
            overflow: hidden;
            position: relative;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(64,81,137,.15) !important; }
        .stat-card .stat-icon {
            width: 52px; height: 52px; border-radius: .6rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .stat-card .stat-bg {
            position: absolute; right: -12px; bottom: -12px;
            font-size: 5rem; opacity: .06; line-height: 1;
        }

        /* ── Table ── */
        .users-table th {
            font-size: .7rem; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: #8098bb;
            border-bottom: 2px solid #e9ecef; padding: .85rem 1rem;
            white-space: nowrap;
        }
        .users-table td { padding: .85rem 1rem; vertical-align: middle; }
        .users-table tbody tr {
            transition: background .15s;
            border-bottom: 1px solid #f3f5f9;
        }
        .users-table tbody tr:hover { background: #f8faff; }
        .users-table tbody tr:last-child { border-bottom: none; }

        /* ── Avatar ── */
        .user-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .9rem; flex-shrink: 0;
            background: linear-gradient(135deg, #405189, #0ab39c);
            color: #fff;
        }

        /* ── Role badge ── */
        .role-badge {
            font-size: .7rem; font-weight: 600; letter-spacing: .05em;
            padding: .3rem .7rem; border-radius: 2rem; text-transform: uppercase;
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
            border-radius: 2rem;
            padding-left: 2.4rem;
            border: 1.5px solid #e2e8f0;
            transition: border-color .2s, box-shadow .2s;
            font-size: .9rem;
        }
        #search-input:focus { border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.12); }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #8098bb; pointer-events: none; }

        /* ── Empty state ── */
        .empty-state { padding: 3rem 1rem; }
        .empty-state i { font-size: 3.5rem; opacity: .3; }

        /* ── Modal ── */
        .modal-header-gradient {
            background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
            color: white; border-radius: .5rem .5rem 0 0;
        }
        .modal-header-gradient .btn-close { filter: invert(1); }
        .modal-header-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white; border-radius: .5rem .5rem 0 0;
        }
        .modal-header-danger .btn-close { filter: invert(1); }

        /* ── Form ── */
        .form-floating>.form-control, .form-floating>.form-select {
            border: 1.5px solid #e2e8f0; border-radius: .5rem;
        }
        .form-floating>.form-control:focus, .form-floating>.form-select:focus {
            border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.1);
        }
        .form-section-title {
            font-size: .7rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #8098bb;
            border-bottom: 1px solid #f0f2f7; padding-bottom: .5rem; margin-bottom: 1rem;
        }

        /* ── Animations ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-row { animation: fadeInUp .3s ease both; }
        .anim-row:nth-child(1){ animation-delay: .03s }
        .anim-row:nth-child(2){ animation-delay: .07s }
        .anim-row:nth-child(3){ animation-delay: .11s }
        .anim-row:nth-child(4){ animation-delay: .15s }
        .anim-row:nth-child(5){ animation-delay: .19s }
        .anim-row:nth-child(6){ animation-delay: .23s }
        .anim-row:nth-child(7){ animation-delay: .27s }
        .anim-row:nth-child(8){ animation-delay: .31s }
        .anim-row:nth-child(9){ animation-delay: .35s }
        .anim-row:nth-child(10){ animation-delay: .39s }

        /* ── Toast ── */
        #toast-container {
            position: fixed; top: 1.2rem; right: 1.2rem; z-index: 9999;
            display: flex; flex-direction: column; gap: .5rem;
        }
        .toast-item {
            min-width: 280px; padding: .85rem 1.1rem;
            border-radius: .5rem; color: #fff; font-size: .88rem; font-weight: 500;
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

        /* ── Password strength ── */
        .pwd-strength-bar { height: 4px; border-radius: 2px; transition: width .3s, background .3s; }
    </style>

    {{-- Toast container --}}
    <div id="toast-container"></div>

    {{-- Page header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Gestión de Usuarios</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4" id="stats-row">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="ri-team-line"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase">Total Usuarios</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-total">{{ $users->total() }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-team-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="ri-shield-star-line"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase">Admins</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-admin">{{ $users->getCollection()->where('role.name','admin')->count() }}</div>
                    </div>
                    <div class="stat-bg text-danger"><i class="ri-shield-star-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="ri-customer-service-2-line"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase">Recepcionistas</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-recep">{{ $users->getCollection()->where('role.name','recepcionista')->count() }}</div>
                    </div>
                    <div class="stat-bg text-warning"><i class="ri-customer-service-2-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="ri-stethoscope-line"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase">Audiólogos</div>
                        <div class="fw-bold fs-4 lh-1 mt-1" id="stat-audio">{{ $users->getCollection()->where('role.name','audiologo')->count() }}</div>
                    </div>
                    <div class="stat-bg text-success"><i class="ri-stethoscope-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main card --}}
    <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
            <h5 class="card-title mb-0 flex-grow-1">Lista de Usuarios</h5>
            {{-- Search --}}
            <div class="position-relative" style="width:240px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="search-input" class="form-control" placeholder="Buscar usuario...">
            </div>
            {{-- Filter by role --}}
            <select id="role-filter" class="form-select form-select-sm" style="width:150px;border-radius:2rem;border:1.5px solid #e2e8f0;font-size:.85rem;">
                <option value="">Todos los roles</option>
                <option value="admin">Admin</option>
                <option value="recepcionista">Recepcionista</option>
                <option value="audiologo">Audiólogo</option>
            </select>
            {{-- New user --}}
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                    style="border-radius:2rem;padding:.4rem 1rem;" onclick="openCreateModal()">
                <i class="ri-add-line"></i> Nuevo Usuario
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table users-table mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Sucursal</th>
                            <th>Registrado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody">
                        @forelse($users as $user)
                        <tr class="anim-row"
                            data-id="{{ $user->id }}"
                            data-name="{{ strtolower($user->name) }}"
                            data-email="{{ strtolower($user->email) }}"
                            data-role="{{ strtolower($user->role->name) }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                    <div>
                                        <div class="fw-semibold lh-sm" style="font-size:.92rem;">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size:.78rem;">ID #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:.88rem;">{{ $user->email }}</span>
                            </td>
                            <td>
                                @php
                                    $roleColors = ['admin'=>'danger','recepcionista'=>'warning','audiologo'=>'success'];
                                    $rc = $roleColors[$user->role->name] ?? 'secondary';
                                    $roleIcons = ['admin'=>'ri-shield-star-line','recepcionista'=>'ri-customer-service-2-line','audiologo'=>'ri-stethoscope-line'];
                                    $ri = $roleIcons[$user->role->name] ?? 'ri-user-line';
                                @endphp
                                <span class="role-badge bg-{{ $rc }}-subtle text-{{ $rc }}">
                                    <i class="{{ $ri }} me-1"></i>{{ ucfirst($user->role->name) }}
                                </span>
                            </td>
                            <td>
                                @if($user->branch)
                                    <span class="badge bg-info-subtle text-info" style="font-size:.78rem;">
                                        <i class="ri-building-2-line me-1"></i>{{ $user->branch->name }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:.82rem;">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button"
                                            class="btn btn-action bg-warning-subtle text-warning"
                                            title="Editar"
                                            onclick="openEditModal({{ $user->id }})">
                                        <i class="ri-pencil-fill fs-13"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button type="button"
                                            class="btn btn-action bg-danger-subtle text-danger"
                                            title="Eliminar"
                                            onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i class="ri-delete-bin-fill fs-13"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-row">
                            <td colspan="6">
                                <div class="empty-state text-center">
                                    <i class="ri-user-line d-block text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No hay usuarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- No results search --}}
            <div id="no-results" class="empty-state text-center d-none">
                <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0">No se encontraron usuarios con ese criterio.</p>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
            <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Crear / Editar Usuario
════════════════════════════════════════ --}}
<div class="modal fade" id="userModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header modal-header-gradient py-3" id="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modal-title">
                    <i class="ri-user-add-line fs-18"></i> Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modal-alert" class="alert d-none mb-3"></div>

                <form id="user-form" novalidate>
                    <input type="hidden" id="form-user-id">
                    <input type="hidden" id="form-method" value="POST">

                    {{-- Información básica --}}
                    <p class="form-section-title"><i class="ri-information-line me-1"></i>Información básica</p>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-name" placeholder="Nombre completo">
                                <label for="f-name"><i class="ri-user-line me-1 text-muted"></i>Nombre completo</label>
                                <div class="invalid-feedback" id="err-name"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="f-email" placeholder="Email">
                                <label for="f-email"><i class="ri-mail-line me-1 text-muted"></i>Correo electrónico</label>
                                <div class="invalid-feedback" id="err-email"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <p class="form-section-title"><i class="ri-lock-line me-1"></i>Contraseña <span id="pwd-optional" class="fw-normal text-muted">(dejar vacío para no cambiar)</span></p>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control" id="f-password" placeholder="Contraseña">
                                <label for="f-password">Contraseña</label>
                                <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2 p-1 text-muted" style="background:none;border:none;" onclick="togglePwd('f-password','icon-pwd1')">
                                    <i class="ri-eye-off-line" id="icon-pwd1"></i>
                                </button>
                                <div class="invalid-feedback" id="err-password"></div>
                            </div>
                            {{-- Strength bar --}}
                            <div class="mt-1 px-1">
                                <div style="height:4px;background:#e9ecef;border-radius:2px;">
                                    <div id="pwd-strength-bar" class="pwd-strength-bar" style="width:0;background:#e9ecef;"></div>
                                </div>
                                <small id="pwd-strength-label" class="text-muted"></small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control" id="f-password-confirm" placeholder="Confirmar contraseña">
                                <label for="f-password-confirm">Confirmar contraseña</label>
                                <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2 p-1 text-muted" style="background:none;border:none;" onclick="togglePwd('f-password-confirm','icon-pwd2')">
                                    <i class="ri-eye-off-line" id="icon-pwd2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Rol y sucursal --}}
                    <p class="form-section-title"><i class="ri-settings-3-line me-1"></i>Permisos y asignación</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="f-role">
                                    <option value="">Seleccionar rol</option>
                                    @foreach(\App\Models\Role::orderBy('name')->get() as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                <label for="f-role">Rol</label>
                                <div class="invalid-feedback" id="err-role"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="f-branch">
                                    <option value="">Sin sucursal</option>
                                    @foreach(\App\Models\Branch::orderBy('name')->get() as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <label for="f-branch">Sucursal</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" id="btn-save-user" onclick="saveUser()">
                    <span class="spinner-border spinner-border-sm d-none" id="save-spinner"></span>
                    <i class="ri-save-line" id="save-icon"></i>
                    <span id="save-label">Guardar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Confirmar Eliminar
════════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header modal-header-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3" style="font-size:3.5rem;line-height:1;">
                    <span id="delete-avatar" class="user-avatar" style="width:64px;height:64px;font-size:1.6rem;display:inline-flex;">A</span>
                </div>
                <p class="mb-1 fs-5 fw-semibold" id="delete-name-display">Usuario</p>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Esta acción es <strong>irreversible</strong>. ¿Confirmas la eliminación?
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="min-width:100px;">Cancelar</button>
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2" id="btn-confirm-delete" style="min-width:120px;" onclick="confirmDelete()">
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

// ── URLs desde Laravel route() — sin hardcodear prefijos ──
const URL_STORE    = "{{ route('admin.usuarios.store') }}";
const URL_BASE     = "{{ route('admin.usuarios.index') }}".replace(/\/+$/, '');
// Para update, destroy y edit-data usamos el base + /{id}
// ya que route() con parámetro numérico es más seguro pasarlo así:
function urlUpdate(id)   { return URL_BASE + '/' + id; }
function urlDestroy(id)  { return URL_BASE + '/' + id; }
function urlEditData(id) { return URL_BASE + '/' + id + '/edit-data'; }

let deleteUserId = null;
let userModal, deleteModal;

document.addEventListener('DOMContentLoaded', () => {
    userModal   = new bootstrap.Modal(document.getElementById('userModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});

// ── Search & Filter ──────────────────────────────────────
document.getElementById('search-input').addEventListener('input', filterTable);
document.getElementById('role-filter').addEventListener('change', filterTable);

function filterTable() {
    const q    = document.getElementById('search-input').value.toLowerCase().trim();
    const role = document.getElementById('role-filter').value.toLowerCase();
    const rows = document.querySelectorAll('#users-tbody tr[data-id]');
    let visible = 0;
    rows.forEach(tr => {
        const name  = tr.dataset.name  || '';
        const email = tr.dataset.email || '';
        const r     = tr.dataset.role  || '';
        const show  = (!q || name.includes(q) || email.includes(q)) && (!role || r === role);
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('no-results').classList.toggle('d-none', visible > 0);
}

// ── Open Create Modal ────────────────────────────────────
function openCreateModal() {
    clearForm();
    document.getElementById('modal-title').innerHTML = '<i class="ri-user-add-line fs-18"></i> Nuevo Usuario';
    document.getElementById('form-method').value     = 'POST';
    document.getElementById('form-user-id').value    = '';
    document.getElementById('pwd-optional').style.display = 'none';
    document.getElementById('f-password').required   = true;
    userModal.show();
}

// ── Open Edit Modal ──────────────────────────────────────
async function openEditModal(id) {
    clearForm();
    document.getElementById('modal-title').innerHTML = '<i class="ri-pencil-line fs-18"></i> Editar Usuario';
    document.getElementById('form-method').value     = 'PUT';
    document.getElementById('form-user-id').value    = id;
    document.getElementById('pwd-optional').style.display = '';
    document.getElementById('f-password').required   = false;

    try {
        const res  = await fetch(urlEditData(id), {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();
        document.getElementById('f-name').value   = data.name;
        document.getElementById('f-email').value  = data.email;
        document.getElementById('f-role').value   = data.role_id;
        document.getElementById('f-branch').value = data.branch_id || '';
        userModal.show();
    } catch(e) {
        showToast('Error al cargar los datos del usuario.', 'error');
    }
}

// ── Save (Create or Update) ──────────────────────────────
async function saveUser() {
    const id     = document.getElementById('form-user-id').value;
    const method = document.getElementById('form-method').value;
    const url    = id ? urlUpdate(id) : URL_STORE;

    const payload = {
        name      : document.getElementById('f-name').value.trim(),
        email     : document.getElementById('f-email').value.trim(),
        role_id   : document.getElementById('f-role').value,
        branch_id : document.getElementById('f-branch').value || null,
    };
    const pwd  = document.getElementById('f-password').value;
    const pwd2 = document.getElementById('f-password-confirm').value;
    if (pwd) {
        payload.password              = pwd;
        payload.password_confirmation = pwd2;
    }

    clearErrors();
    setBtnLoading('btn-save-user', 'save-spinner', 'save-icon', true);

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
            if (data.errors) showFieldErrors(data.errors);
            else showModalAlert(data.message || 'Error al guardar.', 'danger');
            return;
        }

        userModal.hide();
        showToast(data.message || 'Usuario guardado correctamente.', 'success');
        setTimeout(() => location.reload(), 900);

    } catch(e) {
        showModalAlert('Error de conexión.', 'danger');
    } finally {
        setBtnLoading('btn-save-user', 'save-spinner', 'save-icon', false);
    }
}

// ── Delete ───────────────────────────────────────────────
function openDeleteModal(id, name) {
    deleteUserId = id;
    document.getElementById('delete-name-display').textContent = name;
    document.getElementById('delete-avatar').textContent       = name.charAt(0).toUpperCase();
    deleteModal.show();
}

async function confirmDelete() {
    setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', true);
    try {
        const res  = await fetch(urlDestroy(deleteUserId), {
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

        showToast(data.message || 'Usuario eliminado correctamente.', 'success');

        const row = document.querySelector('#users-tbody tr[data-id="' + deleteUserId + '"]');
        if (row) {
            row.style.transition = 'opacity .3s, transform .3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(30px)';
            setTimeout(() => { row.remove(); updateStats(); }, 300);
        }

    } catch(e) {
        showToast('Error de conexión.', 'error');
    } finally {
        setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', false);
    }
}

// ── Helpers ──────────────────────────────────────────────
function clearForm() {
    ['f-name','f-email','f-password','f-password-confirm'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('f-role').value   = '';
    document.getElementById('f-branch').value = '';
    document.getElementById('pwd-strength-bar').style.width = '0';
    document.getElementById('pwd-strength-label').textContent = '';
    clearErrors();
    document.getElementById('modal-alert').className = 'alert d-none';
}

function clearErrors() {
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('#user-form .form-control, #user-form .form-select').forEach(el => {
        el.classList.remove('is-invalid', 'is-valid');
    });
}

function showFieldErrors(errors) {
    const map = { name: 'f-name', email: 'f-email', password: 'f-password', role_id: 'f-role' };
    Object.entries(errors).forEach(([field, msgs]) => {
        const inputId = map[field];
        if (inputId) {
            const input = document.getElementById(inputId);
            const errId = 'err-' + field.replace('_id', '');
            const err   = document.getElementById(errId);
            if (input) input.classList.add('is-invalid');
            if (err)   err.textContent = msgs[0];
        }
    });
}

function showModalAlert(msg, type) {
    const el = document.getElementById('modal-alert');
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
    div.innerHTML = '<i class="ri-' + (type === 'success' ? 'checkbox-circle' : 'error-warning') + '-line fs-16"></i>' + msg;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(function() {
        div.style.transition = 'opacity .4s';
        div.style.opacity    = '0';
        setTimeout(function() { div.remove(); }, 400);
    }, 3500);
}

function updateStats() {
    const rows = document.querySelectorAll('#users-tbody tr[data-id]');
    let total = rows.length, admin = 0, recep = 0, audio = 0;
    rows.forEach(function(tr) {
        const r = tr.dataset.role;
        if (r === 'admin') admin++;
        else if (r === 'recepcionista') recep++;
        else if (r === 'audiologo') audio++;
    });
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-admin').textContent = admin;
    document.getElementById('stat-recep').textContent = recep;
    document.getElementById('stat-audio').textContent = audio;
}

function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ri-eye-line';
    } else {
        input.type = 'password';
        icon.className = 'ri-eye-off-line';
    }
}

document.getElementById('f-password').addEventListener('input', function () {
    const v   = this.value;
    const bar = document.getElementById('pwd-strength-bar');
    const lbl = document.getElementById('pwd-strength-label');
    if (!v) { bar.style.width = '0'; lbl.textContent = ''; return; }
    let score = 0;
    if (v.length >= 6)           score++;
    if (v.length >= 10)          score++;
    if (/[A-Z]/.test(v))         score++;
    if (/[0-9]/.test(v))         score++;
    if (/[^A-Za-z0-9]/.test(v))  score++;
    const levels = [
        { w: '20%',  c: '#e74c3c', t: 'Muy débil' },
        { w: '40%',  c: '#e67e22', t: 'Débil' },
        { w: '60%',  c: '#f1c40f', t: 'Regular' },
        { w: '80%',  c: '#2ecc71', t: 'Fuerte' },
        { w: '100%', c: '#0ab39c', t: 'Muy fuerte' },
    ];
    const l = levels[(score - 1)] || levels[0];
    bar.style.width      = l.w;
    bar.style.background = l.c;
    lbl.style.color      = l.c;
    lbl.textContent      = l.t;
});
</script>
@endpush

</x-app-layout>