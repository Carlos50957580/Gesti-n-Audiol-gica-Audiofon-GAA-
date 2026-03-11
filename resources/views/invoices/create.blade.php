<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    {{-- Título --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Nueva Factura</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ── Patient search ── */
        #patient-search-wrapper { position: relative; }
        #patient-results {
            position: absolute; top: 100%; left: 0; right: 0; z-index: 1050;
            background: #fff; border: 1px solid #dee2e6; border-radius: .375rem;
            box-shadow: 0 4px 20px rgba(0,0,0,.12); max-height: 280px; overflow-y: auto;
            display: none;
        }
        .patient-result-item { padding: .65rem 1rem; cursor: pointer; border-bottom: 1px solid #f3f3f3; transition: background .15s; }
        .patient-result-item:last-child { border-bottom: none; }
        .patient-result-item:hover { background: #f0f6ff; }
        .patient-result-item .p-name   { font-weight: 600; font-size: .9rem; }
        .patient-result-item .p-cedula { font-size: .8rem; color: #6c757d; }

        /* ── Services table ── */
        #services-body tr td { vertical-align: middle; }
        .cov-override-cell input { width: 80px; }

        /* ── Coverage badge ── */
        .cov-badge { font-size: .75rem; cursor: pointer; }

        /* ── Totals ── */
        .total-row { display: flex; justify-content: space-between; padding: .35rem 0; border-bottom: 1px solid #f3f3f3; }
        .total-row:last-child { border-bottom: none; font-size: 1.1rem; font-weight: 700; padding-top: .6rem; }

        /* ── Coverage type toggle ── */
        .cov-type-btn { padding: .2rem .55rem; font-size: .8rem; }
        .cov-type-btn.active { background: #405189; color: #fff; border-color: #405189; }
    </style>

    <form id="invoice-form" action="{{ route('invoices.store') }}" method="POST">
        @csrf

        <div class="row g-3">

            {{-- ══ COLUMNA IZQUIERDA ══ --}}
            <div class="col-xl-8">

                {{-- 1. Paciente --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-user-line me-2 text-primary"></i>Paciente</h5>
                    </div>
                    <div class="card-body">
                        <div id="patient-search-wrapper">
                            <label class="form-label fw-semibold">Buscar por nombre o cédula</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="text" id="patient-search" class="form-control"
                                       placeholder="Escribe el nombre o cédula del paciente..."
                                       autocomplete="off">
                                <button type="button" class="btn btn-outline-primary"
                                        data-bs-toggle="modal" data-bs-target="#newPatientModal">
                                    <i class="ri-user-add-line me-1"></i> Nuevo Paciente
                                </button>
                            </div>
                            <div id="patient-results"></div>
                            @error('patient_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="patient_id" id="patient_id">

                        <div id="patient-info" class="mt-3 d-none">
                            <div class="alert alert-primary d-flex align-items-center gap-3 mb-0">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded-circle bg-primary fs-18" id="patient-avatar">A</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0" id="patient-name-display">—</h6>
                                    <small class="text-muted">
                                        Cédula: <span id="patient-cedula-display">—</span>
                                        &nbsp;|&nbsp; Tel: <span id="patient-phone-display">—</span>
                                    </small>
                                </div>
                                <div id="patient-insurance-badge"></div>
                                <button type="button" class="btn btn-sm btn-light" id="btn-clear-patient">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Servicios --}}
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            <i class="ri-stethoscope-line me-2 text-primary"></i>Servicios
                        </h5>
                        <button type="button" class="btn btn-sm btn-soft-primary" id="btn-add-service">
                            <i class="ri-add-line me-1"></i> Agregar Servicio
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:200px">Servicio</th>
                                        <th style="width:100px">Precio</th>
                                        <th style="width:70px">Cant.</th>
                                        <th style="width:100px">Subtotal</th>
                                        {{-- Columna cobertura — visible solo si hay seguro --}}
                                        <th style="width:155px" id="th-coverage" class="d-none">
                                            Cobertura
                                            <i class="ri-information-line text-muted ms-1"
                                               data-bs-toggle="tooltip"
                                               title="Puedes editar la cobertura por servicio en % o monto fijo"></i>
                                        </th>
                                        <th style="width:115px" id="th-insurance-amt" class="d-none">Cubre Seguro</th>
                                        <th style="width:115px" id="th-patient-amt" class="d-none">Paga Paciente</th>
                                        <th style="width:45px"></th>
                                    </tr>
                                </thead>
                                <tbody id="services-body"></tbody>
                            </table>
                        </div>
                        <div id="no-services-msg" class="text-center py-4 text-muted">
                            <i class="ri-service-line display-6 d-block mb-2"></i>
                            Agrega al menos un servicio para continuar.
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══ COLUMNA DERECHA ══ --}}
            <div class="col-xl-4">

                {{-- 3. Seguro --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-shield-check-line me-2 text-primary"></i>Seguro Médico</h5>
                    </div>
                    <div class="card-body">

                        {{-- Selector de seguro --}}
                        <div class="mb-3">
                            <label class="form-label">Seguro</label>
                            <select name="insurance_id" id="insurance_id" class="form-select">
                                <option value="">— Sin seguro —</option>
                                @foreach($insurances as $ins)
                                    <option value="{{ $ins->id }}"
                                            data-coverage="{{ $ins->coverage_percentage }}"
                                            {{ old('insurance_id') == $ins->id ? 'selected' : '' }}>
                                        {{ $ins->name }} ({{ $ins->coverage_percentage }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Controles de cobertura global — aparecen al seleccionar seguro --}}
                        <div id="coverage-controls" class="d-none">
                            <hr class="my-2">
                            <label class="form-label fw-semibold mb-2">
                                Cobertura general
                                <span class="text-muted fw-normal small">(aplica a todos los servicios)</span>
                            </label>

                            {{-- Tipo: % o monto --}}
                            <div class="btn-group w-100 mb-2" role="group">
                                <button type="button" class="btn btn-outline-secondary cov-type-btn active"
                                        id="btn-cov-pct" onclick="setCovType('pct')">
                                    <i class="ri-percent-line me-1"></i> Porcentaje
                                </button>
                                <button type="button" class="btn btn-outline-secondary cov-type-btn"
                                        id="btn-cov-amt" onclick="setCovType('amt')">
                                    <i class="ri-money-dollar-circle-line me-1"></i> Monto fijo
                                </button>
                            </div>

                            {{-- Input de cobertura global --}}
                            <div class="input-group mb-1">
                                <input type="number" id="global-coverage-input" class="form-control"
                                       min="0" step="0.01" placeholder="0">
                                <span class="input-group-text" id="cov-unit-label">%</span>
                                <button type="button" class="btn btn-primary btn-sm" onclick="applyGlobalCoverage()">
                                    Aplicar
                                </button>
                            </div>
                            <small class="text-muted d-block mb-3">
                                <i class="ri-information-line me-1"></i>
                                También puedes editar la cobertura fila por fila en la tabla.
                            </small>

                            {{-- Autorización --}}
                            <div id="authorization-field">
                                <label class="form-label">Número de autorización</label>
                                <input type="text" name="authorization_number" id="authorization_number"
                                       class="form-control @error('authorization_number') is-invalid @enderror"
                                       placeholder="Ej. AUTH-20260311-001"
                                       value="{{ old('authorization_number') }}">
                                @error('authorization_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- 4. Sucursal --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-building-2-line me-2 text-primary"></i>Sucursal</h5>
                    </div>
                    <div class="card-body">
                        <select name="branch_id" id="branch_id"
                                class="form-select @error('branch_id') is-invalid @enderror">
                            <option value="">Seleccionar sucursal</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                        {{ (old('branch_id', auth()->user()->branch_id) == $branch->id) ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 5. Resumen --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-calculator-line me-2 text-primary"></i>Resumen</h5>
                    </div>
                    <div class="card-body">
                        <div class="total-row">
                            <span class="text-muted">Subtotal servicios</span>
                            <span id="display-subtotal">RD$ 0.00</span>
                        </div>
                        <div class="total-row text-success" id="discount-row" style="display:none!important">
                            <span>Descuento seguro</span>
                            <span id="display-discount">- RD$ 0.00</span>
                        </div>
                        <div class="total-row">
                            <span>Total paciente</span>
                            <span id="display-total" class="text-primary">RD$ 0.00</span>
                        </div>
                    </div>
                    <div class="card-footer d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Guardar Factura
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>
</div>

{{-- ════════════════════════════
     MODAL: Nuevo Paciente
════════════════════════════ --}}
<div class="modal fade" id="newPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title"><i class="ri-user-add-line me-2"></i>Nuevo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal-alert" class="alert d-none"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="m_first_name" placeholder="Nombre">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="m_last_name" placeholder="Apellido">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cédula <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="m_cedula" placeholder="000-0000000-0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="m_phone" placeholder="809-000-0000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="m_email" placeholder="email@ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="m_birth_date">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Género</label>
                        <select class="form-select" id="m_gender">
                            <option value="">— Seleccionar —</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Seguro Médico</label>
                        <select class="form-select" id="m_insurance_id">
                            <option value="">— Sin seguro —</option>
                            @foreach($insurances as $ins)
                                <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-none" id="m_insurance_number_field">
                        <label class="form-label">Número de afiliado</label>
                        <input type="text" class="form-control" id="m_insurance_number" placeholder="Número de afiliado">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" id="m_address" rows="2" placeholder="Dirección completa"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-save-patient">
                    <i class="ri-save-line me-1" id="btn-save-icon"></i>
                    <span id="btn-save-text">Guardar Paciente</span>
                    <span class="spinner-border spinner-border-sm d-none" id="btn-save-spinner"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ════════════════════════════════════════════════════════
// DATOS BASE
// ════════════════════════════════════════════════════════
const SERVICES = @json($services->map(fn($s) => ['id'=>$s->id,'name'=>$s->name,'price'=>(float)$s->price]));

let rowIndex  = 0;
let covType   = 'pct';       // 'pct' | 'amt'
let hasInsurance = false;

// ════════════════════════════════════════════════════════
// BÚSQUEDA DE PACIENTE
// ════════════════════════════════════════════════════════
const searchInput    = document.getElementById('patient-search');
const resultsBox     = document.getElementById('patient-results');
const patientIdInput = document.getElementById('patient_id');
const patientInfo    = document.getElementById('patient-info');
let debounceTimer;

searchInput.addEventListener('input', function () {
    const q = this.value.trim();
    clearTimeout(debounceTimer);
    if (q.length < 2) { resultsBox.style.display = 'none'; return; }
    debounceTimer = setTimeout(() => {
        fetch(`/api/patients/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(renderPatientResults);
    }, 300);
});

function renderPatientResults(patients) {
    resultsBox.innerHTML = '';
    if (!patients.length) {
        resultsBox.innerHTML = '<div class="patient-result-item text-muted">No se encontraron pacientes.</div>';
        resultsBox.style.display = 'block';
        return;
    }
    patients.forEach(p => {
        const div = document.createElement('div');
        div.className = 'patient-result-item';
        div.innerHTML = `
            <div class="p-name">${p.full_name}</div>
            <div class="p-cedula">Cédula: ${p.cedula}
                ${p.insurance_name ? `&nbsp;·&nbsp;<span class="badge bg-info-subtle text-info">${p.insurance_name}</span>` : ''}
            </div>`;
        div.addEventListener('click', () => selectPatient(p));
        resultsBox.appendChild(div);
    });
    resultsBox.style.display = 'block';
}

function selectPatient(p) {
    patientIdInput.value = p.id;
    resultsBox.style.display = 'none';
    searchInput.value = '';

    document.getElementById('patient-name-display').textContent   = p.full_name;
    document.getElementById('patient-cedula-display').textContent = p.cedula;
    document.getElementById('patient-phone-display').textContent  = p.phone || '—';
    document.getElementById('patient-avatar').textContent         = p.full_name.charAt(0).toUpperCase();

    const badge = document.getElementById('patient-insurance-badge');
    if (p.insurance_name) {
        badge.innerHTML = `<span class="badge bg-info">${p.insurance_name} (${p.insurance_coverage}%)</span>`;
        const sel = document.getElementById('insurance_id');
        for (let opt of sel.options) {
            if (opt.value == p.insurance_id) { opt.selected = true; break; }
        }
        sel.dispatchEvent(new Event('change'));
    } else {
        badge.innerHTML = '';
    }
    patientInfo.classList.remove('d-none');
}

document.getElementById('btn-clear-patient').addEventListener('click', () => {
    patientIdInput.value = '';
    patientInfo.classList.add('d-none');
    searchInput.value = '';
    recalculate();
});

document.addEventListener('click', e => {
    if (!document.getElementById('patient-search-wrapper').contains(e.target))
        resultsBox.style.display = 'none';
});

// ════════════════════════════════════════════════════════
// SEGURO — lógica principal
// ════════════════════════════════════════════════════════
document.getElementById('insurance_id').addEventListener('change', function () {
    const opt      = this.options[this.selectedIndex];
    const basePct  = parseFloat(opt.dataset.coverage || 0);
    hasInsurance   = !!this.value;

    // Mostrar/ocultar controles de cobertura
    document.getElementById('coverage-controls').classList.toggle('d-none', !hasInsurance);

    // Mostrar/ocultar columnas de la tabla
    ['th-coverage','th-insurance-amt','th-patient-amt'].forEach(id => {
        document.getElementById(id).classList.toggle('d-none', !hasInsurance);
    });
    document.querySelectorAll('.td-coverage,.td-insurance-amt,.td-patient-amt').forEach(td => {
        td.classList.toggle('d-none', !hasInsurance);
    });

    if (hasInsurance) {
        // Cargar el % base del seguro en el input global
        covType = 'pct';
        setCovType('pct');
        document.getElementById('global-coverage-input').value = basePct;
        applyGlobalCoverage();   // aplicar a todas las filas existentes
    } else {
        // Sin seguro: resetear coberturas a 0
        document.querySelectorAll('.service-row').forEach(tr => {
            setRowCoverage(tr, 0, 'pct');
        });
        recalculate();
    }
});

// ── Tipo de cobertura global (%  o monto fijo) ───────────
function setCovType(type) {
    covType = type;
    document.getElementById('btn-cov-pct').classList.toggle('active', type === 'pct');
    document.getElementById('btn-cov-amt').classList.toggle('active', type === 'amt');
    document.getElementById('cov-unit-label').textContent = type === 'pct' ? '%' : 'RD$';
    // Sincronizar tipo en cada fila
    document.querySelectorAll('.service-row').forEach(tr => {
        const btnPct = tr.querySelector('.row-btn-pct');
        const btnAmt = tr.querySelector('.row-btn-amt');
        if (btnPct) btnPct.classList.toggle('active', type === 'pct');
        if (btnAmt) btnAmt.classList.toggle('active', type === 'amt');
        tr.dataset.covType = type;
        updateCovLabel(tr);
        recalcRow(tr);
    });
    recalculate();
}

// ── Aplicar cobertura global a todas las filas ──────────
function applyGlobalCoverage() {
    const val = parseFloat(document.getElementById('global-coverage-input').value) || 0;
    document.querySelectorAll('.service-row').forEach(tr => {
        tr.dataset.covType = covType;
        const input = tr.querySelector('.cov-input');
        if (input) input.value = val;
        updateCovLabel(tr);
        recalcRow(tr);
    });
    recalculate();
}

// ════════════════════════════════════════════════════════
// TABLA DE SERVICIOS
// ════════════════════════════════════════════════════════
document.getElementById('btn-add-service').addEventListener('click', () => addServiceRow());

function buildOptions(selectedId) {
    return SERVICES.map(s =>
        `<option value="${s.id}" data-price="${s.price}" ${s.id==selectedId?'selected':''}>${s.name} — RD$ ${fmt(s.price)}</option>`
    ).join('');
}

function addServiceRow(selectedId, qty) {
    document.getElementById('no-services-msg').style.display = 'none';
    selectedId = selectedId || (SERVICES[0]?.id || '');
    qty        = qty || 1;

    // Cobertura inicial = la del seguro seleccionado (si aplica)
    const insOpt   = document.getElementById('insurance_id');
    const basePct  = hasInsurance ? (parseFloat(insOpt.options[insOpt.selectedIndex].dataset.coverage) || 0) : 0;
    const globalInput = document.getElementById('global-coverage-input');
    const initCov  = hasInsurance ? (parseFloat(globalInput.value) || basePct) : 0;
    const initType = covType;

    const tr = document.createElement('tr');
    tr.className   = 'service-row';
    tr.dataset.covType = initType;
    tr.innerHTML   = `
        <td>
            <select name="services[${rowIndex}][id]" class="form-select form-select-sm service-select">
                ${buildOptions(selectedId)}
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm price-display text-end bg-light" readonly>
        </td>
        <td>
            <input type="number" name="services[${rowIndex}][quantity]"
                   class="form-control form-control-sm qty-input text-center"
                   value="${qty}" min="1" max="99">
        </td>
        <td class="subtotal-cell text-end fw-medium">RD$ 0.00</td>

        {{-- Cobertura editable por fila --}}
        <td class="td-coverage ${hasInsurance ? '' : 'd-none'}">
            <div class="d-flex align-items-center gap-1">
                {{-- Mini toggle %/RD$ por fila --}}
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary cov-type-btn row-btn-pct ${initType==='pct'?'active':''}"
                            onclick="setRowCovType(this.closest('tr'), 'pct')">%</button>
                    <button type="button" class="btn btn-outline-secondary cov-type-btn row-btn-amt ${initType==='amt'?'active':''}"
                            onclick="setRowCovType(this.closest('tr'), 'amt')">RD$</button>
                </div>
                <input type="number" class="form-control form-control-sm cov-input text-center"
                       value="${initCov}" min="0" step="0.01" style="width:72px"
                       placeholder="0">
                <span class="cov-unit-label text-muted small">${initType==='pct'?'%':'RD$'}</span>
            </div>
        </td>
        <td class="td-insurance-amt insurance-cell text-end text-success ${hasInsurance ? '' : 'd-none'}">RD$ 0.00</td>
        <td class="td-patient-amt patient-cell text-end fw-semibold ${hasInsurance ? '' : 'd-none'}">RD$ 0.00</td>

        <td class="text-center">
            <button type="button" class="btn btn-sm btn-soft-danger btn-remove-row">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>`;

    document.getElementById('services-body').appendChild(tr);

    // Eventos
    tr.querySelector('.service-select').addEventListener('change', () => recalcRow(tr));
    tr.querySelector('.qty-input').addEventListener('input',       () => recalcRow(tr));
    tr.querySelector('.cov-input').addEventListener('input',       () => recalcRow(tr));
    tr.querySelector('.btn-remove-row').addEventListener('click', () => {
        tr.remove();
        if (!document.querySelectorAll('.service-row').length)
            document.getElementById('no-services-msg').style.display = '';
        recalculate();
    });

    rowIndex++;
    recalcRow(tr);
    recalculate();
}

// ── Cambiar tipo de cobertura de UNA fila ────────────────
function setRowCovType(tr, type) {
    tr.dataset.covType = type;
    tr.querySelector('.row-btn-pct').classList.toggle('active', type === 'pct');
    tr.querySelector('.row-btn-amt').classList.toggle('active', type === 'amt');
    updateCovLabel(tr);
    recalcRow(tr);
}

function updateCovLabel(tr) {
    const lbl = tr.querySelector('.cov-unit-label');
    if (lbl) lbl.textContent = tr.dataset.covType === 'pct' ? '%' : 'RD$';
}

// ── Recalcular una fila ──────────────────────────────────
function recalcRow(tr) {
    const sel      = tr.querySelector('.service-select');
    const price    = parseFloat(sel.options[sel.selectedIndex].dataset.price || 0);
    const qty      = parseInt(tr.querySelector('.qty-input').value) || 1;
    const subtotal = price * qty;
    const covVal   = parseFloat(tr.querySelector('.cov-input')?.value || 0);
    const type     = tr.dataset.covType || 'pct';

    let insAmt, patAmt;
    if (!hasInsurance || covVal <= 0) {
        insAmt = 0;
        patAmt = subtotal;
    } else if (type === 'pct') {
        const pct  = Math.min(covVal, 100);
        insAmt = subtotal * (pct / 100);
        patAmt = subtotal - insAmt;
    } else {
        // Monto fijo — no puede superar el subtotal
        insAmt = Math.min(covVal, subtotal);
        patAmt = subtotal - insAmt;
    }

    tr.querySelector('.price-display').value        = 'RD$ ' + fmt(price);
    tr.querySelector('.subtotal-cell').textContent  = 'RD$ ' + fmt(subtotal);

    const insCell = tr.querySelector('.insurance-cell');
    const patCell = tr.querySelector('.patient-cell');
    if (insCell) insCell.textContent = 'RD$ ' + fmt(insAmt);
    if (patCell) patCell.textContent = 'RD$ ' + fmt(patAmt);

    recalculate();
}

// ── Recalcular totales globales ──────────────────────────
function recalculate() {
    let subtotal   = 0;
    let totalIns   = 0;

    document.querySelectorAll('.service-row').forEach(tr => {
        const sel   = tr.querySelector('.service-select');
        const price = parseFloat(sel?.options[sel?.selectedIndex]?.dataset.price || 0);
        const qty   = parseInt(tr.querySelector('.qty-input')?.value) || 1;
        const sub   = price * qty;

        let insAmt = 0;
        if (hasInsurance) {
            const covVal = parseFloat(tr.querySelector('.cov-input')?.value || 0);
            const type   = tr.dataset.covType || 'pct';
            insAmt = type === 'pct'
                ? sub * (Math.min(covVal, 100) / 100)
                : Math.min(covVal, sub);
        }

        subtotal += sub;
        totalIns += insAmt;
    });

    const total = subtotal - totalIns;

    document.getElementById('display-subtotal').textContent = 'RD$ ' + fmt(subtotal);
    document.getElementById('display-discount').textContent = '- RD$ ' + fmt(totalIns);
    document.getElementById('display-total').textContent    = 'RD$ ' + fmt(total);
    document.getElementById('discount-row').style.setProperty('display', totalIns > 0 ? 'flex' : 'none', 'important');
}

function fmt(n) {
    return parseFloat(n || 0).toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Agregar primera fila al cargar
addServiceRow();

// ════════════════════════════════════════════════════════
// MODAL NUEVO PACIENTE
// ════════════════════════════════════════════════════════
document.getElementById('m_insurance_id').addEventListener('change', function () {
    document.getElementById('m_insurance_number_field').classList.toggle('d-none', !this.value);
});

document.getElementById('btn-save-patient').addEventListener('click', function () {
    const alertEl = document.getElementById('modal-alert');
    const spinner = document.getElementById('btn-save-spinner');
    const icon    = document.getElementById('btn-save-icon');
    const btn     = this;

    alertEl.className = 'alert d-none';

    const payload = {
        first_name      : document.getElementById('m_first_name').value.trim(),
        last_name       : document.getElementById('m_last_name').value.trim(),
        cedula          : document.getElementById('m_cedula').value.trim(),
        phone           : document.getElementById('m_phone').value.trim(),
        email           : document.getElementById('m_email').value.trim(),
        birth_date      : document.getElementById('m_birth_date').value,
        gender          : document.getElementById('m_gender').value,
        insurance_id    : document.getElementById('m_insurance_id').value,
        insurance_number: document.getElementById('m_insurance_number').value.trim(),
        address         : document.getElementById('m_address').value.trim(),
    };

    if (!payload.first_name || !payload.last_name || !payload.cedula) {
        alertEl.className = 'alert alert-danger';
        alertEl.textContent = 'Nombre, apellido y cédula son obligatorios.';
        return;
    }

    btn.disabled = true;
    icon.classList.add('d-none');
    spinner.classList.remove('d-none');

    fetch('/api/patients', {
        method : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
    })
    .then(async r => { const d = await r.json(); if (!r.ok) throw d; return d; })
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('newPatientModal')).hide();
        selectPatient(data.patient);
        ['m_first_name','m_last_name','m_cedula','m_phone','m_email','m_birth_date','m_address','m_insurance_number']
            .forEach(id => document.getElementById(id).value = '');
        document.getElementById('m_gender').value       = '';
        document.getElementById('m_insurance_id').value = '';
        document.getElementById('m_insurance_number_field').classList.add('d-none');
        alertEl.className = 'alert d-none';
    })
    .catch(err => {
        const msg = err.errors
            ? Object.values(err.errors).flat().join(' ')
            : (err.message || 'Error al guardar el paciente.');
        alertEl.className = 'alert alert-danger';
        alertEl.textContent = msg;
    })
    .finally(() => {
        btn.disabled = false;
        icon.classList.remove('d-none');
        spinner.classList.add('d-none');
    });
});

// ════════════════════════════════════════════════════════
// VALIDACIÓN SUBMIT
// ════════════════════════════════════════════════════════
document.getElementById('invoice-form').addEventListener('submit', function (e) {
    if (!document.getElementById('patient_id').value) {
        e.preventDefault(); alert('Debes seleccionar un paciente.'); return;
    }
    if (!document.querySelectorAll('.service-row').length) {
        e.preventDefault(); alert('Debes agregar al menos un servicio.'); return;
    }
    if (!document.getElementById('branch_id').value) {
        e.preventDefault(); alert('Debes seleccionar una sucursal.'); return;
    }

    // Inyectar cov_value y cov_type como hidden inputs antes de enviar
    document.querySelectorAll('.service-row').forEach((tr, idx) => {
        const covInput = tr.querySelector('.cov-input');
        const covType  = tr.dataset.covType || 'pct';
        const covVal   = covInput ? covInput.value : 0;

        // Buscar el índice real del row por el name del select
        const sel   = tr.querySelector('.service-select');
        const match = sel?.name?.match(/services\[(\d+)\]/);
        const i     = match ? match[1] : idx;

        // Evitar duplicados
        ['cov_value','cov_type'].forEach(field => {
            const existing = this.querySelector(`input[name="services[${i}][${field}]"]`);
            if (existing) existing.remove();
        });

        const hVal  = document.createElement('input');
        hVal.type   = 'hidden';
        hVal.name   = `services[${i}][cov_value]`;
        hVal.value  = covVal;

        const hType = document.createElement('input');
        hType.type  = 'hidden';
        hType.name  = `services[${i}][cov_type]`;
        hType.value = covType;

        this.appendChild(hVal);
        this.appendChild(hType);
    });
});

// Init tooltips
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
@endpush

</x-app-layout>