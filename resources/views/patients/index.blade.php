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
        .pat-table th {
            font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
            color:#8098bb; border-bottom:2px solid #e9ecef; padding:.85rem 1rem; white-space:nowrap;
        }
        .pat-table td { padding:.8rem 1rem; vertical-align:middle; }
        .pat-table tbody tr { transition:background .15s; border-bottom:1px solid #f3f5f9; }
        .pat-table tbody tr:hover { background:#f8faff; }
        .pat-table tbody tr:last-child { border-bottom:none; }

        /* ── Patient avatar ── */
        .pat-avatar {
            width:38px; height:38px; border-radius:50%; flex-shrink:0;
            display:inline-flex; align-items:center; justify-content:center;
            font-weight:700; font-size:.88rem; color:#fff;
        }
        .pat-avatar.male    { background:linear-gradient(135deg,#405189,#6674c5); }
        .pat-avatar.female  { background:linear-gradient(135deg,#e91e8c,#f06292); }
        .pat-avatar.unknown { background:linear-gradient(135deg,#64748b,#94a3b8); }

        /* ── Gender badges ── */
        .gender-m { background:#dbeafe; color:#1d4ed8; padding:.15rem .5rem; border-radius:.3rem; font-size:.72rem; font-weight:700; }
        .gender-f { background:#fce7f3; color:#be185d; padding:.15rem .5rem; border-radius:.3rem; font-size:.72rem; font-weight:700; }

        /* ── Chips ── */
        .ins-badge {
            display:inline-flex; align-items:center; gap:.3rem;
            background:linear-gradient(135deg,rgba(64,81,137,.08),rgba(10,179,156,.08));
            color:#405189; font-size:.75rem; font-weight:600;
            padding:.22rem .6rem; border-radius:2rem; border:1px solid rgba(64,81,137,.12);
        }
        .ins-private { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }
        .branch-chip {
            display:inline-flex; align-items:center; gap:.3rem;
            background:#f0fdf4; color:#166534; font-size:.75rem; font-weight:600;
            padding:.22rem .6rem; border-radius:2rem; border:1px solid #bbf7d0;
        }
        .cedula-chip {
            font-family:monospace; background:#f8faff; border:1px solid #e2e8f0;
            padding:.2rem .6rem; border-radius:.35rem; font-size:.85rem; color:#405189; font-weight:600;
        }

        /* ── Action buttons ── */
        .btn-action {
            width:32px; height:32px; padding:0; border:none; border-radius:.4rem;
            display:inline-flex; align-items:center; justify-content:center; transition:all .15s;
        }
        .btn-action:hover { transform:scale(1.12); }

        /* ── Search ── */
        #search-input {
            border-radius:2rem; padding-left:2.4rem; border:1.5px solid #e2e8f0; font-size:.9rem;
            transition:border-color .2s,box-shadow .2s;
        }
        #search-input:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.12); }
        .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#8098bb; pointer-events:none; }

        /* ── Modal headers ── */
        .mh-primary { background:linear-gradient(135deg,#405189,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-info    { background:linear-gradient(135deg,#299cdb,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-danger  { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-primary .btn-close, .mh-info .btn-close, .mh-danger .btn-close { filter:invert(1); }

        /* ── Form ── */
        .form-floating>.form-control,
        .form-floating>.form-select { border:1.5px solid #e2e8f0; border-radius:.5rem; }
        .form-floating>.form-control:focus,
        .form-floating>.form-select:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
        .section-label {
            font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
            color:#8098bb; border-bottom:1px solid #f0f2f7; padding-bottom:.4rem; margin-bottom:.9rem;
        }

        /* ── Gender selector ── */
        .gender-selector { display:flex; gap:.75rem; }
        .gender-btn {
            flex:1; padding:.65rem .5rem; border-radius:.5rem; border:1.5px solid #e2e8f0;
            cursor:pointer; text-align:center; transition:all .18s; background:#fff; user-select:none;
        }
        .gender-btn:hover { border-color:#405189; background:#f8faff; }
        .gender-btn.active-m    { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; font-weight:700; }
        .gender-btn.active-f    { border-color:#ec4899; background:#fdf2f8; color:#be185d; font-weight:700; }
        .gender-btn.active-none { border-color:#94a3b8; background:#f8fafc; color:#475569; font-weight:700; }
        .gender-btn i { display:block; font-size:1.4rem; margin-bottom:.25rem; }
        .gender-btn span { font-size:.82rem; }

        /* ── Detail modal ── */
        .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:.5rem 1.2rem; }
        @media(max-width:576px) { .detail-grid { grid-template-columns:1fr; } }
        .detail-cell { padding:.6rem 0; border-bottom:1px solid #f3f5f9; }
        .detail-cell:nth-last-child(-n+2) { border-bottom:none; }
        .detail-lbl { font-size:.68rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#8098bb; }
        .detail-val { font-size:.88rem; font-weight:500; color:#344563; margin-top:.15rem; }
        .detail-full { grid-column:1/-1; }

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

        /* ── Responsive ── */
        @media(max-width:768px) { .col-hide-md { display:none; } }
        @media(max-width:576px) { .col-hide-sm { display:none; } }
    </style>

    <div id="toast-container"></div>

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Gestión de Pacientes</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pacientes</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-user-heart-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Total Pacientes</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $patients->total() }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-user-heart-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-info-subtle text-info"><i class="ri-men-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Masculino</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $patients->getCollection()->where('gender','M')->count() }}</div>
                    </div>
                    <div class="stat-bg text-info"><i class="ri-men-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon" style="background:rgba(233,30,140,.1);"><i class="ri-women-line" style="color:#e91e8c;"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Femenino</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $patients->getCollection()->where('gender','F')->count() }}</div>
                    </div>
                    <div class="stat-bg" style="color:#e91e8c;"><i class="ri-women-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-shield-check-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Con seguro</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $patients->getCollection()->whereNotNull('insurance_id')->count() }}</div>
                    </div>
                    <div class="stat-bg text-success"><i class="ri-shield-check-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main card --}}
    <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
            <h5 class="card-title mb-0 flex-grow-1">Lista de Pacientes</h5>
            <div class="position-relative" style="width:230px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="search-input" class="form-control" placeholder="Nombre o cédula...">
            </div>
            <select id="gender-filter" class="form-select form-select-sm"
                    style="width:135px;border-radius:2rem;border:1.5px solid #e2e8f0;font-size:.85rem;">
                <option value="">Todos</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
            </select>
            <select id="branch-filter" class="form-select form-select-sm"
                    style="width:160px;border-radius:2rem;border:1.5px solid #e2e8f0;font-size:.85rem;">
                <option value="">Todas las sucursales</option>
                @foreach(\App\Models\Branch::orderBy('name')->get() as $br)
                    <option value="{{ $br->id }}">{{ $br->name }}</option>
                @endforeach
                <option value="0">Sin sucursal</option>
            </select>
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                    style="border-radius:2rem;padding:.4rem 1rem;" onclick="openCreateModal()">
                <i class="ri-add-line"></i>
                <span class="d-none d-sm-inline">Nuevo Paciente</span>
                <span class="d-inline d-sm-none">Nuevo</span>
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table pat-table mb-0">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th class="col-hide-sm">Cédula</th>
                            <th>Teléfono</th>
                            <th class="col-hide-md">Sucursal</th>
                            <th class="col-hide-md">Seguro</th>
                            <th class="col-hide-md">Email</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pat-tbody">
                        @forelse($patients as $pat)
                        @php
                            $gClass   = $pat->gender === 'M' ? 'male' : ($pat->gender === 'F' ? 'female' : 'unknown');
                            $initials = strtoupper(substr($pat->first_name,0,1).substr($pat->last_name,0,1));
                        @endphp
                        <tr class="anim-row"
                            data-id="{{ $pat->id }}"
                            data-name="{{ strtolower($pat->first_name.' '.$pat->last_name) }}"
                            data-cedula="{{ strtolower($pat->cedula) }}"
                            data-gender="{{ $pat->gender }}"
                            data-branch="{{ $pat->branch_id ?? '0' }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pat-avatar {{ $gClass }}">{{ $initials }}</div>
                                    <div>
                                        <div class="fw-semibold lh-sm" style="font-size:.9rem;">
                                            {{ $pat->first_name }} {{ $pat->last_name }}
                                        </div>
                                        <div class="mt-1">
                                            @if($pat->gender === 'M')
                                                <span class="gender-m"><i class="ri-men-line me-1"></i>M</span>
                                            @elseif($pat->gender === 'F')
                                                <span class="gender-f"><i class="ri-women-line me-1"></i>F</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-hide-sm">
                                <span class="cedula-chip">{{ $pat->cedula }}</span>
                            </td>
                            <td>
                                @if($pat->phone)
                                    <span style="font-size:.85rem;"><i class="ri-phone-line me-1 text-muted"></i>{{ $pat->phone }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="col-hide-md">
                                @if($pat->branch)
                                    <span class="branch-chip"><i class="ri-building-2-line"></i>{{ $pat->branch->name }}</span>
                                @else
                                    <span class="text-muted" style="font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td class="col-hide-md">
                                @if($pat->insurance)
                                    <span class="ins-badge"><i class="ri-shield-check-line"></i>{{ $pat->insurance->name }}</span>
                                @else
                                    <span class="ins-badge ins-private"><i class="ri-user-line"></i>Privado</span>
                                @endif
                            </td>
                            <td class="col-hide-md">
                                <span style="font-size:.83rem;" class="text-muted">{{ $pat->email ?: '—' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-action bg-info-subtle text-info"
                                            title="Ver detalle" onclick="openShowModal({{ $pat->id }})">
                                        <i class="ri-eye-fill fs-13"></i>
                                    </button>
                                    <button type="button" class="btn btn-action bg-warning-subtle text-warning"
                                            title="Editar" onclick="openEditModal({{ $pat->id }})">
                                        <i class="ri-pencil-fill fs-13"></i>
                                    </button>
                                    <button type="button" class="btn btn-action bg-danger-subtle text-danger"
                                            title="Eliminar"
                                            onclick="openDeleteModal({{ $pat->id }}, '{{ addslashes($pat->first_name.' '.$pat->last_name) }}')">
                                        <i class="ri-delete-bin-fill fs-13"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <i class="ri-user-heart-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                    <p class="text-muted mb-0">No hay pacientes registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="no-results" class="text-center py-5 d-none">
                <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0">No se encontraron pacientes.</p>
            </div>

            @if($patients->hasPages())
            <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                {{ $patients->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Crear / Editar Paciente  (modal-xl)
════════════════════════════════════════════════ --}}
<div class="modal fade" id="patModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-primary py-3">
                <h5 class="modal-title d-flex align-items-center gap-2" id="pat-modal-title">
                    <i class="ri-user-add-line fs-18"></i> Nuevo Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="pat-modal-alert" class="alert d-none mb-3"></div>
                <form id="pat-form" novalidate>
                    <input type="hidden" id="pat-form-id">
                    <input type="hidden" id="pat-form-method" value="POST">

                    {{-- ── Sección 1: Datos personales ── --}}
                    <p class="section-label"><i class="ri-user-line me-1"></i>Datos personales</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-first-name" placeholder="Nombre">
                                <label>Nombre</label>
                                <div class="invalid-feedback" id="err-first_name"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-last-name" placeholder="Apellido">
                                <label>Apellido</label>
                                <div class="invalid-feedback" id="err-last_name"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-cedula" placeholder="Cédula">
                                <label><i class="ri-id-card-line me-1 text-muted"></i>Cédula</label>
                                <div class="invalid-feedback" id="err-cedula"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-phone" placeholder="Teléfono">
                                <label><i class="ri-phone-line me-1 text-muted"></i>Teléfono</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="f-email" placeholder="Email">
                                <label><i class="ri-mail-line me-1 text-muted"></i>Email</label>
                                <div class="invalid-feedback" id="err-email"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="f-birth-date" placeholder="Nacimiento">
                                <label><i class="ri-cake-line me-1 text-muted"></i>Fecha de nacimiento</label>
                            </div>
                        </div>

                        {{-- Género --}}
                        <div class="col-12">
                            <div class="mb-2" style="font-size:.82rem;font-weight:600;color:#495057;">
                                <i class="ri-user-line me-1 text-muted"></i>Género
                                <span class="text-muted fw-normal">(opcional)</span>
                            </div>
                            <div class="gender-selector">
                                <div class="gender-btn" id="btn-gender-m" onclick="selectGender('M')">
                                    <i class="ri-men-line text-primary"></i>
                                    <span>Masculino</span>
                                </div>
                                <div class="gender-btn" id="btn-gender-f" onclick="selectGender('F')">
                                    <i class="ri-women-line" style="color:#e91e8c;"></i>
                                    <span>Femenino</span>
                                </div>
                                <div class="gender-btn" id="btn-gender-none" onclick="selectGender('')">
                                    <i class="ri-user-line text-muted"></i>
                                    <span>No especificar</span>
                                </div>
                            </div>
                            <input type="hidden" id="f-gender" value="">
                        </div>

                        {{-- Dirección --}}
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-address" placeholder="Dirección">
                                <label><i class="ri-map-pin-line me-1 text-muted"></i>Dirección <span class="fw-normal text-muted">(opcional)</span></label>
                            </div>
                        </div>
                    </div>

                    {{-- ── Sección 2: Seguro médico ── --}}
                    <p class="section-label"><i class="ri-shield-check-line me-1"></i>Seguro médico</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="f-insurance">
                                    <option value="">Sin seguro (Privado)</option>
                                    @foreach(\App\Models\Insurance::where('active',1)->orderBy('name')->get() as $ins)
                                        <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                                    @endforeach
                                </select>
                                <label><i class="ri-shield-check-line me-1 text-muted"></i>Aseguradora</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="f-insurance-number" placeholder="Número de póliza">
                                <label><i class="ri-file-list-3-line me-1 text-muted"></i>Número de póliza</label>
                            </div>
                        </div>
                    </div>

                    {{-- ── Sección 3: Sucursal (solo admin) ── --}}
                    @if(auth()->user()->role->name === 'admin')
                    <p class="section-label"><i class="ri-building-2-line me-1"></i>Sucursal</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="f-branch">
                                    <option value="">Sin asignar</option>
                                    @foreach(\App\Models\Branch::orderBy('name')->get() as $br)
                                        <option value="{{ $br->id }}">{{ $br->name }}</option>
                                    @endforeach
                                </select>
                                <label><i class="ri-building-2-line me-1 text-muted"></i>Sucursal</label>
                            </div>
                        </div>
                    </div>
                    @else
                    <input type="hidden" id="f-branch" value="{{ auth()->user()->branch_id }}">
                    @endif

                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-save-pat" onclick="savePatient()">
                    <span class="spinner-border spinner-border-sm d-none" id="pat-save-spinner"></span>
                    <i class="ri-save-line" id="pat-save-icon"></i>
                    <span>Guardar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Ver Detalle
════════════════════════════════════════════════ --}}
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-user-heart-line fs-18"></i> Detalle del Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded"
                     style="background:linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06));border:1px solid rgba(64,81,137,.1);">
                    <div class="pat-avatar unknown" id="show-avatar" style="width:56px;height:56px;font-size:1.2rem;">??</div>
                    <div>
                        <h5 class="mb-1 fw-bold" id="show-fullname">—</h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="cedula-chip" id="show-cedula">—</span>
                            <span id="show-gender-badge"></span>
                            <span id="show-insurance-badge"></span>
                        </div>
                    </div>
                </div>
                <div class="detail-grid">
                    <div class="detail-cell">
                        <div class="detail-lbl"><i class="ri-phone-line me-1"></i>Teléfono</div>
                        <div class="detail-val" id="show-phone">—</div>
                    </div>
                    <div class="detail-cell">
                        <div class="detail-lbl"><i class="ri-mail-line me-1"></i>Email</div>
                        <div class="detail-val" id="show-email">—</div>
                    </div>
                    <div class="detail-cell">
                        <div class="detail-lbl"><i class="ri-cake-line me-1"></i>Fecha de nacimiento</div>
                        <div class="detail-val" id="show-birth">—</div>
                    </div>
                    <div class="detail-cell">
                        <div class="detail-lbl"><i class="ri-building-2-line me-1"></i>Sucursal</div>
                        <div class="detail-val" id="show-branch">—</div>
                    </div>
                    <div class="detail-cell">
                        <div class="detail-lbl"><i class="ri-shield-check-line me-1"></i>Aseguradora</div>
                        <div class="detail-val" id="show-insurance">—</div>
                    </div>
                    <div class="detail-cell">
                        <div class="detail-lbl"><i class="ri-file-list-3-line me-1"></i>Número de póliza</div>
                        <div class="detail-val" id="show-ins-number">—</div>
                    </div>
                    <div class="detail-cell detail-full">
                        <div class="detail-lbl"><i class="ri-map-pin-line me-1"></i>Dirección</div>
                        <div class="detail-val" id="show-address">—</div>
                    </div>
                    <div class="detail-cell">
                        <div class="detail-lbl"><i class="ri-calendar-line me-1"></i>Registrado</div>
                        <div class="detail-val" id="show-created">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="switchToEdit()">
                    <i class="ri-pencil-line me-1"></i>Editar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════
     MODAL: Confirmar Eliminar
════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="pat-avatar unknown mx-auto mb-3" id="delete-avatar"
                     style="width:62px;height:62px;font-size:1.3rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">??</div>
                <p class="mb-1 fs-5 fw-semibold" id="delete-name-display">Paciente</p>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Esta acción es <strong>irreversible</strong>. ¿Confirmas la eliminación?
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="min-width:100px;">Cancelar</button>
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2"
                        id="btn-confirm-delete" style="min-width:120px;" onclick="confirmDelete()">
                    <span class="spinner-border spinner-border-sm d-none" id="delete-spinner"></span>
                    <i class="ri-delete-bin-line" id="delete-icon"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

const URL_STORE = "{{ route('patients.store') }}";
const URL_BASE  = "{{ route('patients.index') }}".replace(/\/+$/, '');
function urlUpdate(id)   { return URL_BASE + '/' + id; }
function urlDestroy(id)  { return URL_BASE + '/' + id; }
function urlShowData(id) { return URL_BASE + '/' + id + '/show-data'; }
function urlEditData(id) { return URL_BASE + '/' + id + '/edit-data'; }

let patModal, showModal, deleteModal;
let deletePatId   = null;
let currentShowId = null;

document.addEventListener('DOMContentLoaded', () => {
    patModal    = new bootstrap.Modal(document.getElementById('patModal'));
    showModal   = new bootstrap.Modal(document.getElementById('showModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});

// ── Search & filter ───────────────────────────────────────
document.getElementById('search-input').addEventListener('input', filterTable);
document.getElementById('gender-filter').addEventListener('change', filterTable);
document.getElementById('branch-filter').addEventListener('change', filterTable);

function filterTable() {
    const q      = document.getElementById('search-input').value.toLowerCase().trim();
    const gender = document.getElementById('gender-filter').value;
    const branch = document.getElementById('branch-filter').value;
    const rows   = document.querySelectorAll('#pat-tbody tr[data-id]');
    let visible  = 0;
    rows.forEach(tr => {
        const matchQ = !q      || (tr.dataset.name||'').includes(q) || (tr.dataset.cedula||'').includes(q);
        const matchG = !gender || tr.dataset.gender === gender;
        const matchB = !branch || tr.dataset.branch === branch;
        const show   = matchQ && matchG && matchB;
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('no-results').classList.toggle('d-none', visible > 0);
}

// ── Gender selector ───────────────────────────────────────
function selectGender(val) {
    document.getElementById('f-gender').value = val;
    document.getElementById('btn-gender-m').className    = 'gender-btn' + (val === 'M' ? ' active-m'    : '');
    document.getElementById('btn-gender-f').className    = 'gender-btn' + (val === 'F' ? ' active-f'    : '');
    document.getElementById('btn-gender-none').className = 'gender-btn' + (val === ''  ? ' active-none' : '');
}

// ── CREATE ────────────────────────────────────────────────
function openCreateModal() {
    clearPatForm();
    document.getElementById('pat-modal-title').innerHTML = '<i class="ri-user-add-line fs-18"></i> Nuevo Paciente';
    document.getElementById('pat-form-method').value     = 'POST';
    document.getElementById('pat-form-id').value         = '';
    patModal.show();
}

// ── EDIT ──────────────────────────────────────────────────
async function openEditModal(id) {
    clearPatForm();
    document.getElementById('pat-modal-title').innerHTML = '<i class="ri-pencil-line fs-18"></i> Editar Paciente';
    document.getElementById('pat-form-method').value     = 'PUT';
    document.getElementById('pat-form-id').value         = id;

    try {
        const res = await fetch(urlEditData(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        document.getElementById('f-first-name').value        = d.first_name       || '';
        document.getElementById('f-last-name').value         = d.last_name        || '';
        document.getElementById('f-cedula').value            = d.cedula           || '';
        document.getElementById('f-phone').value             = d.phone            || '';
        document.getElementById('f-email').value             = d.email            || '';
        document.getElementById('f-birth-date').value        = d.birth_date_raw   || '';
        document.getElementById('f-address').value           = d.address          || '';
        document.getElementById('f-insurance').value         = d.insurance_id     || '';
        document.getElementById('f-insurance-number').value  = d.insurance_number || '';
        const branchEl = document.getElementById('f-branch');
        if (branchEl && branchEl.tagName === 'SELECT') branchEl.value = d.branch_id || '';
        selectGender(d.gender || '');
        patModal.show();
    } catch {
        showToast('Error al cargar los datos del paciente.', 'error');
    }
}

// ── SAVE ──────────────────────────────────────────────────
async function savePatient() {
    const id     = document.getElementById('pat-form-id').value;
    const method = document.getElementById('pat-form-method').value;
    const url    = id ? urlUpdate(id) : URL_STORE;

    const branchEl  = document.getElementById('f-branch');
    const branchVal = branchEl ? (branchEl.value || null) : null;

    const payload = {
        first_name       : document.getElementById('f-first-name').value.trim(),
        last_name        : document.getElementById('f-last-name').value.trim(),
        cedula           : document.getElementById('f-cedula').value.trim(),
        phone            : document.getElementById('f-phone').value.trim()            || null,
        email            : document.getElementById('f-email').value.trim()            || null,
        birth_date       : document.getElementById('f-birth-date').value              || null,
        gender           : document.getElementById('f-gender').value                  || null,
        address          : document.getElementById('f-address').value.trim()          || null,
        insurance_id     : document.getElementById('f-insurance').value               || null,
        insurance_number : document.getElementById('f-insurance-number').value.trim() || null,
        branch_id        : branchVal,
    };

    clearPatErrors();
    setBtnLoading('btn-save-pat', 'pat-save-spinner', 'pat-save-icon', true);

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
            if (data.errors) showPatFieldErrors(data.errors);
            else showPatAlert(data.message || 'Error al guardar.', 'danger');
            return;
        }

        patModal.hide();
        showToast(data.message || 'Paciente guardado correctamente.', 'success');
        setTimeout(() => location.reload(), 800);

    } catch {
        showPatAlert('Error de conexión.', 'danger');
    } finally {
        setBtnLoading('btn-save-pat', 'pat-save-spinner', 'pat-save-icon', false);
    }
}

// ── SHOW DETAIL ───────────────────────────────────────────
async function openShowModal(id) {
    currentShowId = id;
    ['show-fullname','show-cedula','show-phone','show-email','show-birth',
     'show-branch','show-insurance','show-ins-number','show-address','show-created']
        .forEach(i => document.getElementById(i).textContent = '…');
    document.getElementById('show-avatar').textContent        = '…';
    document.getElementById('show-gender-badge').innerHTML    = '';
    document.getElementById('show-insurance-badge').innerHTML = '';
    showModal.show();

    try {
        const res = await fetch(urlShowData(id), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        const d = await res.json();

        const initials = (d.first_name.charAt(0) + d.last_name.charAt(0)).toUpperCase();
        const av = document.getElementById('show-avatar');
        av.textContent  = initials;
        av.className    = 'pat-avatar ' + (d.gender==='M'?'male':d.gender==='F'?'female':'unknown');
        av.style.cssText= 'width:56px;height:56px;font-size:1.2rem;';

        document.getElementById('show-fullname').textContent    = d.first_name + ' ' + d.last_name;
        document.getElementById('show-cedula').textContent      = d.cedula;
        document.getElementById('show-phone').textContent       = d.phone            || '—';
        document.getElementById('show-email').textContent       = d.email            || '—';
        document.getElementById('show-birth').textContent       = d.birth_date       || '—';
        document.getElementById('show-branch').textContent      = d.branch           || '—';
        document.getElementById('show-insurance').textContent   = d.insurance        || 'Privado';
        document.getElementById('show-ins-number').textContent  = d.insurance_number || '—';
        document.getElementById('show-address').textContent     = d.address          || '—';
        document.getElementById('show-created').textContent     = d.created_at;

        if (d.gender === 'M') {
            document.getElementById('show-gender-badge').innerHTML =
                '<span class="gender-m"><i class="ri-men-line me-1"></i>Masculino</span>';
        } else if (d.gender === 'F') {
            document.getElementById('show-gender-badge').innerHTML =
                '<span class="gender-f"><i class="ri-women-line me-1"></i>Femenino</span>';
        }

        document.getElementById('show-insurance-badge').innerHTML = d.insurance
            ? '<span class="ins-badge"><i class="ri-shield-check-line me-1"></i>' + d.insurance + '</span>'
            : '<span class="ins-badge ins-private"><i class="ri-user-line me-1"></i>Privado</span>';

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
    deletePatId = id;
    const initials = name.split(' ').map(w => w.charAt(0)).slice(0,2).join('').toUpperCase();
    document.getElementById('delete-name-display').textContent = name;
    document.getElementById('delete-avatar').textContent       = initials;
    deleteModal.show();
}

async function confirmDelete() {
    setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', true);
    try {
        const res  = await fetch(urlDestroy(deletePatId), {
            method : 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-HTTP-Method-Override':'DELETE' },
            body   : JSON.stringify({}),
        });
        const data = await res.json();
        deleteModal.hide();
        if (!res.ok) { showToast(data.message || 'Error al eliminar.', 'error'); return; }
        showToast(data.message || 'Paciente eliminado correctamente.', 'success');
        const row = document.querySelector('#pat-tbody tr[data-id="' + deletePatId + '"]');
        if (row) {
            row.style.transition = 'opacity .3s,transform .3s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(30px)';
            setTimeout(() => row.remove(), 300);
        }
    } catch { showToast('Error de conexión.', 'error'); }
    finally  { setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', false); }
}

// ── Helpers ───────────────────────────────────────────────
function clearPatForm() {
    ['f-first-name','f-last-name','f-cedula','f-phone','f-email',
     'f-birth-date','f-address','f-insurance-number'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('f-insurance').value = '';
    const branchEl = document.getElementById('f-branch');
    if (branchEl && branchEl.tagName === 'SELECT') branchEl.value = '';
    selectGender('');
    clearPatErrors();
    document.getElementById('pat-modal-alert').className = 'alert d-none';
}

function clearPatErrors() {
    document.querySelectorAll('#pat-form .invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('#pat-form .form-control, #pat-form .form-select').forEach(el => {
        el.classList.remove('is-invalid','is-valid');
    });
}

function showPatFieldErrors(errors) {
    const map = {
        first_name: 'f-first-name', last_name: 'f-last-name',
        cedula: 'f-cedula', email: 'f-email',
    };
    Object.entries(errors).forEach(([field, msgs]) => {
        const input = document.getElementById(map[field]);
        const err   = document.getElementById('err-' + field);
        if (input) input.classList.add('is-invalid');
        if (err)   err.textContent = msgs[0];
    });
}

function showPatAlert(msg, type) {
    const el = document.getElementById('pat-modal-alert');
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