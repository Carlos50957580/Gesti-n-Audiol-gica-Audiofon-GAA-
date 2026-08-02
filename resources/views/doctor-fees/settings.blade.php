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
        .settings-table th {
            font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
            color:#8098bb; border-bottom:2px solid #e9ecef; padding:.85rem 1rem; white-space:nowrap;
        }
        .settings-table td { padding:.82rem 1rem; vertical-align:middle; }
        .settings-table tbody tr { transition:background .15s; border-bottom:1px solid #f3f5f9; }
        .settings-table tbody tr:hover { background:#f8faff; }

        /* ── Status pills ── */
        .status-pill {
            display:inline-flex; align-items:center; gap:.35rem;
            padding:.28rem .7rem; border-radius:2rem; font-size:.75rem; font-weight:600;
        }
        .status-pill .dot { width:7px; height:7px; border-radius:50%; }
        .status-active { background:#d1fae5; color:#065f46; }
        .status-active .dot { background:#10b981; }
        .status-inactive { background:#fee2e2; color:#991b1b; }
        .status-inactive .dot { background:#ef4444; }

        /* ── Action buttons ── */
        .btn-action {
            width:32px; height:32px; padding:0; border:none; border-radius:.4rem;
            display:inline-flex; align-items:center; justify-content:center; transition:all .15s;
        }
        .btn-action:hover { transform:scale(1.12); }

        /* ── Modal headers ── */
        .mh-primary { background:linear-gradient(135deg,#405189,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-danger { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-warning { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-primary .btn-close, .mh-danger .btn-close, .mh-warning .btn-close { filter:invert(1); }

        /* ── Form fields ── */
        .form-floating>.form-control,
        .form-floating>.form-select { border:1.5px solid #e2e8f0; border-radius:.5rem; }
        .form-floating>.form-control:focus,
        .form-floating>.form-select:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
        .section-label {
            font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
            color:#8098bb; border-bottom:1px solid #f0f2f7; padding-bottom:.4rem; margin-bottom:.9rem;
        }

        /* ── Value input with prefix ── */
        .value-group .input-group-text {
            background:linear-gradient(135deg,#405189,#0ab39c); color:#fff;
            border:none; font-weight:700; font-size:.85rem; border-radius:.5rem 0 0 .5rem;
        }
        .value-group .form-control { border:1.5px solid #e2e8f0; border-left:none; border-radius:0 .5rem .5rem 0; }
        .value-group .form-control:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }

        /* ── Scope badge ── */
        .scope-badge {
            display:inline-flex; align-items:center; gap:.3rem;
            padding:.2rem .6rem; border-radius:2rem; font-size:.7rem; font-weight:600;
        }
        .scope-general { background:#e5e7eb; color:#374151; }
        .scope-category { background:#dbeafe; color:#1e40af; }
        .scope-service { background:#d1fae5; color:#065f46; }

        /* ── Toast ── */
        #toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
        .toast-item {
            min-width:280px; padding:.85rem 1.1rem; border-radius:.5rem; color:#fff;
            font-size:.88rem; font-weight:500; display:flex; align-items:center; gap:.6rem;
            box-shadow:0 4px 20px rgba(0,0,0,.18); animation:toastIn .3s ease;
        }
        @keyframes toastIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
        .toast-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
        .toast-error { background:linear-gradient(135deg,#e74c3c,#c0392b); }
        .toast-info { background:linear-gradient(135deg,#299cdb,#0ab39c); }

        /* ── Animations ── */
        @keyframes fadeInUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim-row { animation:fadeInUp .3s ease both; }

        /* ── Search ── */
        #search-settings {
            border-radius:2rem; padding-left:2.4rem;
            border:1.5px solid #e2e8f0; font-size:.9rem;
        }
        .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#8098bb; pointer-events:none; }
    </style>

    <div id="toast-container"></div>

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Configuración de Honorarios Médicos</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('doctor-fees.index') }}">Honorarios</a></li>
                    <li class="breadcrumb-item active">Configuración</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-user-star-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">Médicos</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $doctors->count() }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-user-star-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-checkbox-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">Configurados</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $settings->total() }}</div>
                    </div>
                    <div class="stat-bg text-success"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-info-subtle text-info"><i class="ri-percent-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">% Promedio</div>
                        @php
                            $avgPercentage = $settings->getCollection()->where('calculation_type', 'percentage')->avg('value');
                        @endphp
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ number_format($avgPercentage, 1) }}%</div>
                    </div>
                    <div class="stat-bg text-info"><i class="ri-percent-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-money-dollar-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">Monto Promedio</div>
                        @php
                            $avgFixed = $settings->getCollection()->where('calculation_type', 'fixed')->avg('value');
                        @endphp
                        <div class="fw-bold fs-4 lh-1 mt-1">RD$ {{ number_format($avgFixed, 0) }}</div>
                    </div>
                    <div class="stat-bg text-warning"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Left column: Formulario --}}
        <div class="col-lg-5">
            <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
                <div class="card-header" style="border-bottom:1px solid #f0f2f7;background:#fff;">
                    <h5 class="card-title mb-0">
                        <i class="ri-settings-4-line me-2"></i>Configurar Honorario
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="form-alert" class="alert d-none mb-3"></div>
                    <form id="settings-form" novalidate>
                        <input type="hidden" id="setting-id">
                        <input type="hidden" id="form-method" value="POST">
                        
                        <p class="section-label"><i class="ri-user-settings-line me-1"></i>Médico</p>
                        <div class="mb-4">
                            <select id="doctor-id" class="form-select" required>
                                <option value="">-- Seleccione un Médico --</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="err-doctor_id"></div>
                        </div>
                        
                        <p class="section-label"><i class="ri-filter-3-line me-1"></i>Alcance de la Configuración</p>
                        <div class="mb-4">
                            <div class="row g-2">
                                <div class="col-12">
                                    <select id="scope-type" class="form-select" onchange="toggleScopeFields()">
                                        <option value="general">General (todos los servicios)</option>
                                        <option value="category">Por Categoría</option>
                                        <option value="service">Por Servicio Específico</option>
                                    </select>
                                </div>
                                <div class="col-12" id="category-field" style="display:none;">
                                    <select id="category-id" class="form-select">
                                        <option value="">-- Seleccione una Categoría --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12" id="service-field" style="display:none;">
                                    <select id="service-id" class="form-select">
                                        <option value="">-- Seleccione un Servicio --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-category="{{ $service->category_id }}">
                                                {{ $service->name }} ({{ $service->category->name ?? 'Sin categoría' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <p class="section-label"><i class="ri-calculator-line me-1"></i>Tipo de Cálculo</p>
                        <div class="mb-4">
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="calculation_type" id="type-percentage" value="percentage" checked>
                                    <label class="form-check-label" for="type-percentage">
                                        <i class="ri-percent-line me-1"></i> Porcentaje
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="calculation_type" id="type-fixed" value="fixed">
                                    <label class="form-check-label" for="type-fixed">
                                        <i class="ri-money-dollar-circle-line me-1"></i> Monto Fijo
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <p class="section-label"><i class="ri-slider-line me-1"></i>Valor</p>
                        <div class="mb-4">
                            <div class="input-group value-group">
                                <span class="input-group-text" id="value-prefix">%</span>
                                <input type="number" class="form-control" id="value" 
                                       step="0.01" min="0" max="100" placeholder="0.00">
                            </div>
                            <div class="invalid-feedback" id="err-value"></div>
                            <small class="text-muted" id="value-help">
                                Porcentaje del total de la factura (0-100%)
                            </small>
                        </div>
                        
                        <p class="section-label"><i class="ri-toggle-line me-1"></i>Estado</p>
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#f8faff;border:1.5px solid #e2e8f0;">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="font-size:.85rem;">Activar configuración</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="is-active" style="width:2.5rem;height:1.3rem;" checked>
                                    <label class="form-check-label fw-semibold ms-1" id="active-label" for="is-active" style="font-size:.85rem;">Activo</label>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2"
                                    id="btn-save" onclick="saveSetting()">
                                <span class="spinner-border spinner-border-sm d-none" id="save-spinner"></span>
                                <i class="ri-save-line" id="save-icon"></i>
                                <span>Guardar Configuración</span>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearForm()">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- Right column: Listado --}}
        <div class="col-lg-7">
            <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;background:#fff;">
                    <h5 class="card-title mb-0">
                        <i class="ri-list-settings-line me-2"></i>Configuraciones Existentes
                    </h5>
                    <div class="position-relative" style="width:200px;">
                        <i class="ri-search-line search-icon"></i>
                        <input type="text" id="search-settings" class="form-control" placeholder="Buscar médico...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table settings-table mb-0">
                            <thead>
                                <tr>
                                    <th>Médico</th>
                                    <th>Alcance</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="settings-tbody">
                                @forelse($settings as $setting)
                                <tr class="anim-row" data-id="{{ $setting->id }}" data-doctor="{{ strtolower($setting->doctor->name) }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="stat-icon bg-primary-subtle text-primary" style="width:32px;height:32px;font-size:.9rem;">
                                                <i class="ri-user-star-line"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size:.9rem;">{{ $setting->doctor->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($setting->service_id)
                                            <span class="scope-badge scope-service">
                                                <i class="ri-stethoscope-line"></i> {{ $setting->service->name ?? 'N/A' }}
                                            </span>
                                        @elseif($setting->category_id)
                                            <span class="scope-badge scope-category">
                                                <i class="ri-folder-line"></i> {{ $setting->category->name ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="scope-badge scope-general">
                                                <i class="ri-global-line"></i> General
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $setting->calculation_type === 'percentage' ? 'info' : 'success' }}">
                                            {{ $setting->calculation_type === 'percentage' ? 'Porcentaje' : 'Monto Fijo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            {{ $setting->calculation_type === 'percentage' ? $setting->value . '%' : 'RD$ ' . number_format($setting->value, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($setting->is_active)
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
                                            <button type="button" class="btn btn-action bg-primary-subtle text-primary"
                                                    title="Editar" onclick="editSetting({{ $setting->id }})">
                                                <i class="ri-pencil-fill fs-13"></i>
                                            </button>
                                            <button type="button" class="btn btn-action bg-danger-subtle text-danger"
                                                    title="Eliminar" onclick="openDeleteModal({{ $setting->id }}, '{{ addslashes($setting->doctor->name) }}')">
                                                <i class="ri-delete-bin-fill fs-13"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="text-center py-5">
                                            <i class="ri-settings-4-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                            <p class="text-muted mb-0">No hay configuraciones registradas.</p>
                                            <p class="text-muted small mt-1">Seleccione un Médico y configure su honorario</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div id="no-results" class="text-center py-5 d-none">
                        <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                        <p class="text-muted mb-0">No se encontraron configuraciones</p>
                    </div>
                    
                    @if($settings->hasPages())
                    <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                        {{ $settings->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- MODAL: Eliminar Configuración --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Configuración</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="stat-icon mx-auto mb-3"
                     style="width:60px;height:60px;font-size:1.5rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">
                    <i class="ri-settings-4-line"></i>
                </div>
                <p class="mb-1 fs-5 fw-semibold" id="delete-name-display">Configuración</p>
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
const URL_BASE = "{{ route('doctor-fees.settings') }}";
let deleteSettingId = null;

document.addEventListener('DOMContentLoaded', () => {
    // Tipo de cálculo change handler
    document.querySelectorAll('input[name="calculation_type"]').forEach(radio => {
        radio.addEventListener('change', updateValueField);
    });
    
    updateValueField();
    
    // Search filter
    document.getElementById('search-settings').addEventListener('input', filterSettings);
    
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});

function toggleScopeFields() {
    const scopeType = document.getElementById('scope-type').value;
    document.getElementById('category-field').style.display = scopeType === 'category' ? 'block' : 'none';
    document.getElementById('service-field').style.display = scopeType === 'service' ? 'block' : 'none';
}

function updateValueField() {
    const type = document.querySelector('input[name="calculation_type"]:checked').value;
    const valueInput = document.getElementById('value');
    const valuePrefix = document.getElementById('value-prefix');
    const valueHelp = document.getElementById('value-help');
    
    if (type === 'percentage') {
        valuePrefix.textContent = '%';
        valueInput.max = 100;
        valueInput.step = '0.01';
        valueInput.placeholder = '0.00';
        valueHelp.innerHTML = 'Porcentaje del total de la factura (0-100%)';
    } else {
        valuePrefix.textContent = 'RD$';
        valueInput.max = 999999;
        valueInput.step = '0.01';
        valueInput.placeholder = '0.00';
        valueHelp.innerHTML = 'Monto fijo por factura (no puede exceder el total de la factura)';
    }
}

function filterSettings() {
    const search = document.getElementById('search-settings').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#settings-tbody tr[data-id]');
    let visible = 0;
    
    rows.forEach(tr => {
        const doctorName = tr.dataset.doctor || '';
        const match = !search || doctorName.includes(search);
        tr.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    
    document.getElementById('no-results').classList.toggle('d-none', visible > 0);
}

async function editSetting(id) {
    try {
        const res = await fetch(`${URL_BASE}/${id}`);
        if (!res.ok) throw new Error();
        const setting = await res.json();
        
        document.getElementById('setting-id').value = setting.id;
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('doctor-id').value = setting.doctor_id;
        document.getElementById('doctor-id').disabled = true;
        
        // Seleccionar el tipo de alcance
        const scopeType = document.getElementById('scope-type');
        if (setting.service_id) {
            scopeType.value = 'service';
            document.getElementById('service-id').value = setting.service_id;
            // Forzar la carga del select de servicios con el valor correcto
            const serviceSelect = document.getElementById('service-id');
            Array.from(serviceSelect.options).forEach(opt => {
                opt.selected = opt.value == setting.service_id;
            });
        } else if (setting.category_id) {
            scopeType.value = 'category';
            document.getElementById('category-id').value = setting.category_id;
        } else {
            scopeType.value = 'general';
        }
        toggleScopeFields();
        
        const radioType = document.querySelector(`input[name="calculation_type"][value="${setting.calculation_type}"]`);
        if (radioType) radioType.checked = true;
        
        document.getElementById('value').value = setting.value;
        document.getElementById('is-active').checked = setting.is_active == 1;
        document.getElementById('active-label').textContent = setting.is_active ? 'Activo' : 'Inactivo';
        
        updateValueField();
        
        const saveBtn = document.getElementById('btn-save');
        saveBtn.querySelector('span:last-child').textContent = 'Actualizar';
        saveBtn.classList.remove('btn-primary');
        saveBtn.classList.add('btn-warning');
        
        // Scroll al formulario
        document.querySelector('.card-header h5').scrollIntoView({ behavior: 'smooth' });
        
        showToast('Configuración cargada para edición', 'info');
        
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al cargar la configuración', 'error');
    }
}

async function saveSetting() {
    const id = document.getElementById('setting-id').value;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `${URL_BASE}/${id}` : URL_BASE;
    
    const doctorId = document.getElementById('doctor-id').value;
    if (!doctorId) {
        document.getElementById('err-doctor_id').textContent = 'Seleccione un Médico';
        document.getElementById('doctor-id').classList.add('is-invalid');
        return;
    }
    
    const scopeType = document.getElementById('scope-type').value;
    let categoryId = null;
    let serviceId = null;
    
    if (scopeType === 'category') {
        categoryId = document.getElementById('category-id').value;
        if (!categoryId) {
            showFormAlert('Seleccione una categoría', 'danger');
            return;
        }
    } else if (scopeType === 'service') {
        serviceId = document.getElementById('service-id').value;
        if (!serviceId) {
            showFormAlert('Seleccione un servicio', 'danger');
            return;
        }
    }
    
    const calculationType = document.querySelector('input[name="calculation_type"]:checked').value;
    const value = parseFloat(document.getElementById('value').value);
    
    if (!value || value <= 0) {
        document.getElementById('err-value').textContent = 'Ingrese un valor válido';
        document.getElementById('value').classList.add('is-invalid');
        return;
    }
    
    if (calculationType === 'percentage' && value > 100) {
        document.getElementById('err-value').textContent = 'El porcentaje no puede ser mayor a 100';
        document.getElementById('value').classList.add('is-invalid');
        return;
    }
    
    clearErrors();
    setBtnLoading(true);
    
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-HTTP-Method-Override': method,
            },
            body: JSON.stringify({
                doctor_id: doctorId,
                category_id: categoryId,
                service_id: serviceId,
                calculation_type: calculationType,
                value: value,
                is_active: document.getElementById('is-active').checked ? 1 : 0,
            })
        });
        
        const data = await res.json();
        
        if (!res.ok) {
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, msgs]) => {
                    const errElement = document.getElementById(`err-${field}`);
                    if (errElement) errElement.textContent = msgs[0];
                });
            } else {
                showFormAlert(data.message || 'Error al guardar.', 'danger');
            }
            return;
        }
        
        showToast(data.message || 'Configuración guardada correctamente.', 'success');
        setTimeout(() => location.reload(), 800);
        
    } catch {
        showFormAlert('Error de conexión.', 'danger');
    } finally {
        setBtnLoading(false);
    }
}

function clearForm() {
    document.getElementById('setting-id').value = '';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('doctor-id').value = '';
    document.getElementById('doctor-id').disabled = false;
    document.getElementById('scope-type').value = 'general';
    toggleScopeFields();
    document.getElementById('value').value = '';
    document.querySelector('input[name="calculation_type"][value="percentage"]').checked = true;
    document.getElementById('is-active').checked = true;
    document.getElementById('active-label').textContent = 'Activo';
    updateValueField();
    clearErrors();
    
    const saveBtn = document.getElementById('btn-save');
    saveBtn.querySelector('span:last-child').textContent = 'Guardar Configuración';
    saveBtn.classList.remove('btn-warning');
    saveBtn.classList.add('btn-primary');
    
    document.getElementById('form-alert').classList.add('d-none');
}

function openDeleteModal(id, name) {
    deleteSettingId = id;
    document.getElementById('delete-name-display').innerHTML = `Configuración de <strong>${name}</strong>`;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

async function confirmDelete() {
    setBtnLoading(true, 'btn-confirm-delete', 'delete-spinner', 'delete-icon');
    
    try {
        const res = await fetch(`${URL_BASE}/${deleteSettingId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            }
        });
        
        const data = await res.json();
        
        if (!res.ok) {
            showToast(data.message || 'Error al eliminar.', 'error');
            return;
        }
        
        showToast(data.message || 'Configuración eliminada correctamente.', 'success');
        setTimeout(() => location.reload(), 800);
        
    } catch {
        showToast('Error de conexión.', 'error');
    } finally {
        setBtnLoading(false, 'btn-confirm-delete', 'delete-spinner', 'delete-icon');
        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    }
}

function clearErrors() {
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-control, .form-select').forEach(el => {
        el.classList.remove('is-invalid');
    });
}

function showFormAlert(msg, type) {
    const alertDiv = document.getElementById('form-alert');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = msg;
    alertDiv.classList.remove('d-none');
    setTimeout(() => alertDiv.classList.add('d-none'), 5000);
}

function setBtnLoading(loading, btnId = 'btn-save', spinnerId = 'save-spinner', iconId = 'save-icon') {
    const btn = document.getElementById(btnId);
    const spinner = document.getElementById(spinnerId);
    const icon = document.getElementById(iconId);
    
    if (btn) btn.disabled = loading;
    if (spinner) spinner.classList.toggle('d-none', !loading);
    if (icon) icon.classList.toggle('d-none', loading);
}

function showToast(msg, type) {
    type = type || 'success';
    const div = document.createElement('div');
    div.className = `toast-item toast-${type}`;
    const icon = type === 'success' ? 'checkbox-circle' : (type === 'error' ? 'error-warning' : 'information-line');
    div.innerHTML = `<i class="ri-${icon}-line fs-16"></i>${msg}`;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(() => {
        div.style.transition = 'opacity .4s';
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 400);
    }, 3500);
}
</script>
@endpush

</x-app-layout>