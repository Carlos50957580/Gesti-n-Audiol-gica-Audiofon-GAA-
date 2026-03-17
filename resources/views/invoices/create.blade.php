<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
/* ══════════════════════════════════════
   CARDS
══════════════════════════════════════ */
.inv-card {
    border: none; border-radius: .85rem;
    box-shadow: 0 2px 18px rgba(64,81,137,.09);
    background: #fff; margin-bottom: 1.25rem;
}
.inv-card-header {
    padding: .9rem 1.25rem;
    border-bottom: 1px solid #f0f2f7;
    display: flex; align-items: center; gap: .6rem;
}
.inv-card-header h6 {
    margin: 0; font-size: .88rem; font-weight: 700; color: #344563; flex-grow: 1;
}
.inv-card-header .card-icon {
    width: 32px; height: 32px; border-radius: .45rem; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .95rem;
}
.inv-card-body { padding: 1.25rem; overflow: visible; }

/* ══════════════════════════════════════
   PATIENT SEARCH
══════════════════════════════════════ */
.ps-wrap { position: relative; }
.ps-box {
    display: flex; align-items: center;
    border: 1.5px solid #e2e8f0; border-radius: .5rem; overflow: hidden;
    transition: border-color .2s, box-shadow .2s; background: #fff;
}
.ps-box.focused { border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.1); }
.ps-box .ps-icon { padding: 0 .7rem; color: #8098bb; font-size: 1rem; flex-shrink: 0; }
.ps-box input {
    flex: 1; border: none; outline: none; padding: .65rem .5rem .65rem 0;
    font-size: .88rem; background: transparent; min-width: 0;
}
.ps-box input::placeholder { color: #adb5bd; }
.ps-new-btn {
    flex-shrink: 0; border: none; border-left: 1.5px solid #e2e8f0;
    background: #f8faff; color: #405189; padding: 0 .85rem;
    font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .18s;
    display: flex; align-items: center; gap: .35rem; align-self: stretch;
}
.ps-new-btn:hover { background: #405189; color: #fff; }

.ps-dropdown {
    display: none; position: absolute; top: calc(100% + 3px); left: 0; right: 0; z-index: 99999;
    background: #fff; border: 1.5px solid #e2e8f0; border-radius: .5rem;
    max-height: 280px; overflow-y: auto;
    box-shadow: 0 12px 36px rgba(64,81,137,.18);
}
/* Ensure the ps-wrap creates a new stacking context so dropdown floats above everything */
.ps-wrap { position: relative; z-index: 10; }
.ps-item {
    display: flex; align-items: center; gap: .6rem;
    padding: .6rem .9rem; cursor: pointer; border-bottom: 1px solid #f3f5f9;
    transition: background .1s;
}
.ps-item:last-child { border-bottom: none; }
.ps-item:hover { background: #f0f4ff; }
.ps-item-av {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg,#405189,#0ab39c);
    display: flex; align-items: center; justify-content: center;
    font-size: .68rem; font-weight: 700; color: #fff;
}
.ps-item-name { font-size: .85rem; font-weight: 600; color: #344563; }
.ps-item-sub  { font-size: .73rem; color: #8098bb; }
.ps-msg { padding: .7rem .9rem; font-size: .83rem; color: #8098bb; text-align: center; }

/* Selected patient pill */
.patient-pill {
    display: none; align-items: center; gap: .75rem; margin-top: .6rem;
    background: linear-gradient(135deg,rgba(64,81,137,.05),rgba(10,179,156,.05));
    border: 1px solid rgba(64,81,137,.15); border-radius: .65rem; padding: .7rem 1rem;
}
.patient-pill.visible { display: flex; }
.patient-pill-av {
    width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg,#405189,#0ab39c);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 700; color: #fff;
}
.patient-pill-name { font-size: .9rem; font-weight: 700; color: #344563; }
.patient-pill-sub  { font-size: .75rem; color: #6b7a99; margin-top: .1rem; }
.ins-chip {
    font-size: .72rem; font-weight: 600; padding: .18rem .55rem;
    border-radius: 2rem; background: rgba(10,179,156,.1); color: #0ab39c;
    border: 1px solid rgba(10,179,156,.2); white-space: nowrap;
}
.patient-pill-x {
    margin-left: auto; background: none; border: none; color: #94a3b8;
    padding: .2rem .4rem; cursor: pointer; border-radius: .35rem; transition: all .15s;
    font-size: 1.1rem; line-height: 1;
}
.patient-pill-x:hover { color: #e74c3c; background: #fee2e2; }

/* ══════════════════════════════════════
   SERVICES TABLE
══════════════════════════════════════ */
.svc-table { width: 100%; border-collapse: collapse; }
.svc-table th {
    font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: #8098bb; padding: .75rem 1rem; border-bottom: 2px solid #e9ecef;
    background: #fafbff; white-space: nowrap;
}
.svc-table td { padding: .7rem 1rem; border-bottom: 1px solid #f3f5f9; vertical-align: middle; }
.svc-table tbody tr:last-child td { border-bottom: none; }
.svc-table tbody tr:hover { background: #fafbff; }
.svc-table .form-select,
.svc-table .form-control { border: 1.5px solid #e2e8f0; border-radius: .4rem; font-size: .85rem; }
.svc-table .form-select:focus,
.svc-table .form-control:focus { border-color: #405189; box-shadow: 0 0 0 2px rgba(64,81,137,.1); }

.subtotal-cell { font-size: .87rem; font-weight: 600; color: #344563; white-space: nowrap; }
.ins-cell  { font-size: .85rem; font-weight: 600; color: #0ab39c; white-space: nowrap; }
.pat-cell  { font-size: .87rem; font-weight: 700; color: #344563; white-space: nowrap; }

/* Coverage row toggle */
.cov-type-btn {
    padding: .2rem .48rem; font-size: .76rem; font-weight: 600;
    border: 1px solid #e2e8f0; background: #f8faff; color: #8098bb;
    cursor: pointer; transition: all .15s; line-height: 1.4;
}
.cov-type-btn:first-child { border-radius: .35rem 0 0 .35rem; }
.cov-type-btn:last-child  { border-radius: 0 .35rem .35rem 0; margin-left: -1px; }
.cov-type-btn.active { background: #405189; color: #fff; border-color: #405189; z-index: 1; position: relative; }

.btn-remove-row {
    width: 28px; height: 28px; padding: 0; border: none; border-radius: .35rem;
    background: rgba(231,76,60,.08); color: #e74c3c;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .15s; font-size: .9rem;
}
.btn-remove-row:hover { background: #e74c3c; color: #fff; }

/* Empty state */
.svc-empty {
    text-align: center; padding: 2.5rem 1rem; color: #94a3b8;
}
.svc-empty i { font-size: 2.5rem; opacity: .3; display: block; margin-bottom: .75rem; }
.svc-empty p { font-size: .85rem; margin: 0; }

/* ══════════════════════════════════════
   RIGHT SIDEBAR CARDS
══════════════════════════════════════ */
.sidebar-section-label {
    font-size: .68rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
    color: #8098bb; margin-bottom: .6rem;
}
.cov-toggle-group { display: flex; border-radius: .45rem; overflow: hidden; border: 1.5px solid #e2e8f0; }
.cov-toggle-btn {
    flex: 1; padding: .45rem 0; font-size: .82rem; font-weight: 600;
    border: none; background: #f8faff; color: #8098bb; cursor: pointer; transition: all .18s;
    display: flex; align-items: center; justify-content: center; gap: .3rem;
}
.cov-toggle-btn.active { background: #405189; color: #fff; }
.cov-toggle-btn:not(.active):hover { background: #f0f2f7; }

.form-floating>.form-control,
.form-floating>.form-select { border: 1.5px solid #e2e8f0; border-radius: .5rem; font-size: .88rem; }
.form-floating>.form-control:focus,
.form-floating>.form-select:focus { border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.1); }

/* ══════════════════════════════════════
   TOTALS BOX
══════════════════════════════════════ */
.totals-box {
    background: #f8faff; border: 1px solid #edf0f7; border-radius: .65rem; overflow: hidden;
}
.totals-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .6rem 1rem; border-bottom: 1px solid #f0f2f7; font-size: .87rem;
}
.totals-row:last-child { border-bottom: none; }
.totals-label { color: #6b7a99; }
.totals-value { font-weight: 600; color: #344563; }
.totals-discount { color: #0ab39c; font-weight: 700; }
.totals-row.totals-final {
    background: linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06));
    padding: .75rem 1rem;
}
.totals-final .totals-label { font-size: .92rem; font-weight: 800; color: #344563; }
.totals-final .totals-value { font-size: 1.1rem; font-weight: 800; color: #405189; }

/* ══════════════════════════════════════
   SUBMIT AREA
══════════════════════════════════════ */
.submit-card {
    border: none; border-radius: .85rem;
    box-shadow: 0 2px 18px rgba(64,81,137,.09);
    background: #fff; overflow: hidden;
}
.btn-submit-inv {
    width: 100%; padding: .75rem; font-size: .95rem; font-weight: 700;
    border: none; border-radius: .5rem; cursor: pointer; transition: all .2s;
    background: linear-gradient(135deg,#405189,#0ab39c);
    color: #fff; display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-submit-inv:hover { opacity: .92; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(64,81,137,.25); }
.btn-submit-inv:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* ══════════════════════════════════════
   MODAL
══════════════════════════════════════ */
.mh-primary { background: linear-gradient(135deg,#405189,#0ab39c); color: #fff; border-radius: .5rem .5rem 0 0; }
.mh-primary .btn-close { filter: invert(1); }

/* ══════════════════════════════════════
   TOAST
══════════════════════════════════════ */
#toast-container {
    position: fixed; top: 1.1rem; right: 1.1rem; z-index: 99999;
    display: flex; flex-direction: column; gap: .4rem;
}
.toast-item {
    min-width: 260px; padding: .78rem 1rem; border-radius: .5rem; color: #fff;
    font-size: .85rem; font-weight: 500; display: flex; align-items: center; gap: .5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,.2); animation: toastIn .25s ease;
}
@keyframes toastIn { from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:translateX(0)} }
.toast-success { background: linear-gradient(135deg,#0ab39c,#3d9f80); }
.toast-error   { background: linear-gradient(135deg,#e74c3c,#c0392b); }
</style>

<div id="toast-container"></div>

{{-- ── Breadcrumb ── --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Nueva Factura</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </div>
    </div>
</div>

<form id="invoice-form" action="{{ route('invoices.store') }}" method="POST" novalidate>
@csrf
<div class="row g-3 align-items-start">

    {{-- ════════════════════════════════
         COLUMNA IZQUIERDA
    ════════════════════════════════ --}}
    <div class="col-xl-8">

        {{-- 1. Paciente --}}
        <div class="inv-card">
            <div class="inv-card-header">
                <div class="card-icon bg-primary-subtle text-primary"><i class="ri-user-heart-line"></i></div>
                <h6>Paciente</h6>
            </div>
            <div class="inv-card-body">
                <input type="hidden" name="patient_id" id="patient_id">

                <div class="ps-wrap">
                    <div class="ps-box" id="ps-box">
                        <span class="ps-icon"><i class="ri-search-line"></i></span>
                        <input type="text" id="ps-input"
                               placeholder="Buscar paciente por nombre o cédula..."
                               autocomplete="off" spellcheck="false">
                        <button type="button" class="ps-new-btn"
                                onclick="openNewPatientModal()">
                            <i class="ri-user-add-line"></i>Nuevo paciente
                        </button>
                    </div>
                    <div class="ps-dropdown" id="ps-dropdown">
                        <div id="ps-results"></div>
                    </div>
                </div>

                <div class="patient-pill" id="patient-pill">
                    <div class="patient-pill-av" id="pill-av">?</div>
                    <div class="flex-grow-1">
                        <div class="patient-pill-name" id="pill-name">—</div>
                        <div class="patient-pill-sub" id="pill-sub">—</div>
                    </div>
                    <div id="pill-ins-chip"></div>
                    <button type="button" class="patient-pill-x" onclick="clearPatient()" title="Cambiar paciente">
                        <i class="ri-close-circle-line"></i>
                    </button>
                </div>

                <div class="text-danger mt-1" style="font-size:.8rem;" id="err-patient"></div>
                @error('patient_id')
                    <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 2. Servicios --}}
        <div class="inv-card">
            <div class="inv-card-header">
                <div class="card-icon bg-success-subtle text-success"><i class="ri-stethoscope-line"></i></div>
                <h6>Servicios</h6>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                        style="border-radius:2rem;padding:.32rem .85rem;font-size:.8rem;"
                        id="btn-add-svc">
                    <i class="ri-add-line"></i>Agregar
                </button>
            </div>
            <div class="table-responsive">
                <table class="svc-table">
                    <thead>
                        <tr>
                            <th style="min-width:200px;">Servicio</th>
                            <th style="width:110px;">Precio unit.</th>
                            <th style="width:70px;" class="text-center">Cant.</th>
                            <th style="width:110px;">Subtotal</th>
                            <th style="width:160px;" id="th-cov" class="d-none">Cobertura</th>
                            <th style="width:115px;" id="th-ins" class="d-none">Cubre seguro</th>
                            <th style="width:115px;" id="th-pat" class="d-none">Paga paciente</th>
                            <th style="width:42px;"></th>
                        </tr>
                    </thead>
                    <tbody id="svc-body"></tbody>
                </table>
            </div>
            <div id="svc-empty" class="svc-empty">
                <i class="ri-stethoscope-line"></i>
                <p>Agrega al menos un servicio para continuar.</p>
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════
         COLUMNA DERECHA
    ════════════════════════════════ --}}
    <div class="col-xl-4">

        {{-- 3. Seguro --}}
        <div class="inv-card">
            <div class="inv-card-header">
                <div class="card-icon bg-info-subtle text-info"><i class="ri-shield-check-line"></i></div>
                <h6>Seguro Médico</h6>
            </div>
            <div class="inv-card-body">
                <div class="form-floating mb-3">
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
                    <label>Seguro</label>
                </div>

                <div id="coverage-controls" class="d-none">
                    <div class="sidebar-section-label">Cobertura global</div>
                    <div class="cov-toggle-group mb-2">
                        <button type="button" class="cov-toggle-btn active" id="btn-cov-pct" onclick="setCovType('pct')">
                            <i class="ri-percent-line"></i>Porcentaje
                        </button>
                        <button type="button" class="cov-toggle-btn" id="btn-cov-amt" onclick="setCovType('amt')">
                            <i class="ri-money-dollar-circle-line"></i>Monto fijo
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="number" id="global-cov-input" class="form-control"
                               min="0" step="0.01" placeholder="0"
                               style="border:1.5px solid #e2e8f0;border-radius:.45rem 0 0 .45rem;">
                        <span class="input-group-text" id="cov-unit" style="border:1.5px solid #e2e8f0;border-left:none;">%</span>
                        <button type="button" class="btn btn-primary btn-sm" onclick="applyGlobalCoverage()"
                                style="border-radius:0 .45rem .45rem 0;font-size:.82rem;padding:.4rem .75rem;">
                            Aplicar
                        </button>
                    </div>
                    <p style="font-size:.75rem;color:#8098bb;margin-bottom:1rem;">
                        <i class="ri-information-line me-1"></i>También puedes ajustar por fila en la tabla.
                    </p>

                    <div class="form-floating">
                        <input type="text" name="authorization_number" id="authorization_number"
                               class="form-control @error('authorization_number') is-invalid @enderror"
                               placeholder="Ej. AUTH-2026-001"
                               value="{{ old('authorization_number') }}">
                        <label><i class="ri-file-check-line me-1 text-muted"></i>N° de autorización</label>
                        @error('authorization_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

       {{-- 4. Sucursal --}}
<div class="inv-card">
    <div class="inv-card-header">
        <div class="card-icon bg-warning-subtle text-warning"><i class="ri-building-2-line"></i></div>
        <h6>Sucursal</h6>
    </div>
    <div class="inv-card-body">
        <div class="form-floating">
            <select name="branch_id" id="branch_id"
                    class="form-select @error('branch_id') is-invalid @enderror">
                <option value="">— Seleccionar —</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}"
                            {{ old('branch_id', auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <label>Sucursal</label>
            @error('branch_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- 4b. Audiólogo --}}
<div class="inv-card">
    <div class="inv-card-header">
        <div class="card-icon bg-primary-subtle text-primary">
            <i class="ri-stethoscope-line"></i>
        </div>
        <h6>Audiólogo Asignado</h6>
    </div>
    <div class="inv-card-body">
        <div class="form-floating">
            <select name="audiologist_id" id="audiologist_id"
                    class="form-select @error('audiologist_id') is-invalid @enderror">
                <option value="">— Seleccionar audiólogo —</option>
                @foreach($audiologists as $aud)
                    <option value="{{ $aud->id }}"
                            data-branch="{{ $aud->branch_id }}"
                            {{ old('audiologist_id') == $aud->id ? 'selected' : '' }}>
                        {{ $aud->name }}
                        @if(auth()->user()->role->name === 'admin')
                            — {{ $aud->branch->name ?? '' }}
                        @endif
                    </option>
                @endforeach
            </select>
            <label><i class="ri-user-heart-line me-1 text-muted"></i>Audiólogo</label>
            @error('audiologist_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Pill informativo al seleccionar --}}
        <div id="aud-pill" class="d-none mt-2 p-2 rounded d-flex align-items-center gap-2"
             style="background:linear-gradient(135deg,rgba(64,81,137,.05),rgba(10,179,156,.05));
                    border:1px solid rgba(64,81,137,.15);">
            <div style="width:32px;height:32px;border-radius:.4rem;flex-shrink:0;
                        background:linear-gradient(135deg,#405189,#0ab39c);
                        display:flex;align-items:center;justify-content:center;
                        color:#fff;font-size:.8rem;font-weight:700;" id="aud-pill-av">
            </div>
            <div>
                <div style="font-size:.85rem;font-weight:700;color:#344563;" id="aud-pill-name"></div>
                <div style="font-size:.73rem;color:#8098bb;" id="aud-pill-branch"></div>
            </div>
        </div>
    </div>
</div>

        {{-- 5. Resumen + Submit --}}
        <div class="inv-card">
            <div class="inv-card-header">
                <div class="card-icon bg-primary-subtle text-primary"><i class="ri-calculator-line"></i></div>
                <h6>Resumen</h6>
            </div>
            <div class="inv-card-body pb-0">
                <div class="totals-box">
                    <div class="totals-row">
                        <span class="totals-label">Subtotal servicios</span>
                        <span class="totals-value" id="disp-subtotal">RD$ 0.00</span>
                    </div>
                    <div class="totals-row" id="discount-row" style="display:none;">
                        <span class="totals-label">Descuento seguro</span>
                        <span class="totals-discount" id="disp-discount">− RD$ 0.00</span>
                    </div>
                    <div class="totals-row totals-final">
                        <span class="totals-label">Total a pagar</span>
                        <span class="totals-value" id="disp-total">RD$ 0.00</span>
                    </div>
                </div>
            </div>
            <div class="inv-card-body pt-3">
                <button type="submit" class="btn-submit-inv" id="btn-submit">
                    <i class="ri-save-line fs-16"></i>Guardar Factura
                </button>
                <a href="{{ route('invoices.index') }}"
                   class="d-block text-center mt-2 text-muted"
                   style="font-size:.83rem;text-decoration:none;">
                    <i class="ri-arrow-left-line me-1"></i>Cancelar y volver
                </a>
            </div>
        </div>

    </div>
</div>
</form>

</div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Nuevo Paciente
════════════════════════════════════════ --}}
<div class="modal fade" id="newPatientModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-user-add-line fs-18"></i>Nuevo Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modal-alert" class="alert d-none mb-3"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_first_name" placeholder="Nombre">
                            <label>Nombre <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_last_name" placeholder="Apellido">
                            <label>Apellido <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_cedula" placeholder="000-0000000-0">
                            <label>Cédula <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_phone" placeholder="809-000-0000">
                            <label>Teléfono</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="m_email" placeholder="email@ejemplo.com">
                            <label>Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="m_birth_date" placeholder="Fecha">
                            <label>Fecha de nacimiento</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="m_gender">
                                <option value="">— Seleccionar —</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <label>Género</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="m_insurance_id">
                                <option value="">— Sin seguro —</option>
                                @foreach($insurances as $ins)
                                    <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                                @endforeach
                            </select>
                            <label>Seguro médico</label>
                        </div>
                    </div>
                    <div class="col-md-6 d-none" id="m_ins_num_wrap">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_insurance_number" placeholder="Número">
                            <label>N° de afiliado</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control" id="m_address" placeholder="Dirección" style="height:72px;"></textarea>
                            <label>Dirección</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal" style="border-radius:2rem;">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-save-patient" style="border-radius:2rem;padding:.42rem 1rem;">
                    <span class="spinner-border spinner-border-sm d-none" id="patient-save-spin"></span>
                    <i class="ri-save-line" id="patient-save-icon"></i>
                    <span>Guardar paciente</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
/* ══════════════════════════════════════
   BASE DATA
══════════════════════════════════════ */
const SERVICES_DATA = {{ Js::from($services->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'price' => (float)$s->price])) }};

const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const URL_SEARCH = '/api/patients/search';
const URL_STORE  = '/api/patients';

let rowIndex    = 0;
let covType     = 'pct';   // 'pct' | 'amt'
let hasInsurance = false;

/* ══════════════════════════════════════
   PATIENT SEARCH
══════════════════════════════════════ */
let psTimer      = null;
let psMouseInDrop = false;

function psInit() {
    const inp  = document.getElementById('ps-input');
    const drop = document.getElementById('ps-dropdown');

    inp.addEventListener('input', function () {
        clearTimeout(psTimer);
        const q = this.value.trim();
        document.getElementById('ps-results').innerHTML =
            '<div class="ps-msg"><span class="spinner-border spinner-border-sm me-1"></span>Buscando…</div>';
        if (q.length < 2) { drop.style.display = 'none'; return; }
        drop.style.display = 'block';
        psTimer = setTimeout(() => psFetch(q), 300);
    });

    inp.addEventListener('focus', () => document.getElementById('ps-box').classList.add('focused'));
    inp.addEventListener('blur',  () => {
        document.getElementById('ps-box').classList.remove('focused');
        setTimeout(() => { if (!psMouseInDrop) drop.style.display = 'none'; }, 220);
    });

    drop.addEventListener('mouseenter', () => psMouseInDrop = true);
    drop.addEventListener('mouseleave', () => psMouseInDrop = false);
}

async function psFetch(q) {
    try {
        const r = await fetch(URL_SEARCH + '?q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const list = await r.json();
        psRender(list);
    } catch {
        document.getElementById('ps-results').innerHTML =
            '<div class="ps-msg text-danger"><i class="ri-error-warning-line me-1"></i>Error al buscar</div>';
    }
}

function psRender(list) {
    const box = document.getElementById('ps-results');
    if (!list.length) {
        box.innerHTML = '<div class="ps-msg"><i class="ri-search-line me-1"></i>Sin resultados</div>';
    } else {
        window._psItems = {};
        box.innerHTML = list.map(p => {
            const key = 'ps_' + p.id;
            window._psItems[key] = p;
            const ini = ((p.full_name || '').split(' ').map(w => w[0]).slice(0,2).join('')).toUpperCase();
            const ins = p.insurance_name
                ? `<span class="ins-chip ms-1">${p.insurance_name}</span>` : '';
            return `<div class="ps-item" data-key="${key}" onmousedown="psSelect(this)">
                        <div class="ps-item-av">${ini}</div>
                        <div>
                            <div class="ps-item-name">${p.full_name}</div>
                            <div class="ps-item-sub">${p.cedula}${ins}</div>
                        </div>
                    </div>`;
        }).join('');
    }
    document.getElementById('ps-dropdown').style.display = 'block';
}

function psSelect(el) {
    const p = window._psItems[el.dataset.key] || {};
    selectPatient(p);
}

function selectPatient(p) {
    document.getElementById('patient_id').value = p.id;
    document.getElementById('ps-input').value   = '';
    document.getElementById('ps-dropdown').style.display = 'none';

    const ini = ((p.full_name || '').split(' ').map(w => w[0]).slice(0,2).join('')).toUpperCase();
    document.getElementById('pill-av').textContent   = ini;
    document.getElementById('pill-name').textContent = p.full_name || '—';
    document.getElementById('pill-sub').textContent  =
        'Cédula: ' + (p.cedula || '—') + (p.phone ? '  ·  Tel: ' + p.phone : '');

    const chip = document.getElementById('pill-ins-chip');
    if (p.insurance_name) {
        chip.innerHTML = `<span class="ins-chip"><i class="ri-shield-check-line me-1"></i>${p.insurance_name} ${p.insurance_coverage}%</span>`;
        // Auto-select insurance
        const sel = document.getElementById('insurance_id');
        for (const opt of sel.options) {
            if (opt.value == p.insurance_id) { opt.selected = true; break; }
        }
        sel.dispatchEvent(new Event('change'));
    } else {
        chip.innerHTML = '';
    }

    document.getElementById('patient-pill').classList.add('visible');
    document.getElementById('err-patient').textContent = '';
}

function clearPatient() {
    document.getElementById('patient_id').value = '';
    document.getElementById('ps-input').value   = '';
    document.getElementById('patient-pill').classList.remove('visible');
    document.getElementById('pill-ins-chip').innerHTML = '';
}

function openNewPatientModal() {
    new bootstrap.Modal(document.getElementById('newPatientModal')).show();
}

/* ══════════════════════════════════════
   INSURANCE
══════════════════════════════════════ */
document.getElementById('insurance_id').addEventListener('change', function () {
    const opt     = this.options[this.selectedIndex];
    const basePct = parseFloat(opt.dataset.coverage || 0);
    hasInsurance  = !!this.value;

    document.getElementById('coverage-controls').classList.toggle('d-none', !hasInsurance);
    ['th-cov','th-ins','th-pat'].forEach(id =>
        document.getElementById(id).classList.toggle('d-none', !hasInsurance));
    document.querySelectorAll('.td-cov,.td-ins,.td-pat').forEach(td =>
        td.classList.toggle('d-none', !hasInsurance));

    if (hasInsurance) {
        covType = 'pct';
        setCovType('pct');
        document.getElementById('global-cov-input').value = basePct;
        applyGlobalCoverage();
    } else {
        document.querySelectorAll('.svc-row').forEach(tr => recalcRow(tr));
        recalculate();
    }
});

function setCovType(type) {
    covType = type;
    document.getElementById('btn-cov-pct').classList.toggle('active', type === 'pct');
    document.getElementById('btn-cov-amt').classList.toggle('active', type === 'amt');
    document.getElementById('cov-unit').textContent = type === 'pct' ? '%' : 'RD$';

    document.querySelectorAll('.svc-row').forEach(tr => {
        tr.dataset.covType = type;
        tr.querySelector('.row-btn-pct')?.classList.toggle('active', type === 'pct');
        tr.querySelector('.row-btn-amt')?.classList.toggle('active', type === 'amt');
        updateRowCovLabel(tr);
        recalcRow(tr);
    });
    recalculate();
}

function applyGlobalCoverage() {
    const val = parseFloat(document.getElementById('global-cov-input').value) || 0;
    document.querySelectorAll('.svc-row').forEach(tr => {
        tr.dataset.covType = covType;
        const inp = tr.querySelector('.cov-input');
        if (inp) inp.value = val;
        updateRowCovLabel(tr);
        recalcRow(tr);
    });
    recalculate();
}

/* ══════════════════════════════════════
   SERVICES TABLE
══════════════════════════════════════ */
document.getElementById('btn-add-svc').addEventListener('click', () => addSvcRow());

function buildOptions(selId) {
    return SERVICES_DATA.map(s =>
        `<option value="${s.id}" data-price="${s.price}" ${s.id == selId ? 'selected' : ''}>
            ${s.name} — RD$ ${fmt(s.price)}
        </option>`
    ).join('');
}

function addSvcRow(selId, qty) {
    document.getElementById('svc-empty').style.display = 'none';
    selId = selId || (SERVICES_DATA[0]?.id || '');
    qty   = qty   || 1;

    const insOpt  = document.getElementById('insurance_id');
    const basePct = hasInsurance ? (parseFloat(insOpt.options[insOpt.selectedIndex].dataset.coverage) || 0) : 0;
    const initCov = hasInsurance ? (parseFloat(document.getElementById('global-cov-input').value) || basePct) : 0;
    const initType = covType;
    const hidden   = hasInsurance ? '' : 'd-none';
    const ri       = rowIndex++;

    const tr = document.createElement('tr');
    tr.className      = 'svc-row';
    tr.dataset.covType = initType;
    tr.innerHTML = `
        <td>
            <select name="services[${ri}][id]" class="form-select form-select-sm svc-select">
                ${buildOptions(selId)}
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm price-disp text-end"
                   style="background:#f8faff;" readonly>
        </td>
        <td class="text-center">
            <input type="number" name="services[${ri}][quantity]"
                   class="form-control form-control-sm qty-input text-center"
                   value="${qty}" min="1" max="99" style="width:60px;margin:0 auto;">
        </td>
        <td class="subtotal-cell">RD$ 0.00</td>
        <td class="td-cov ${hidden}">
            <div class="d-flex align-items-center gap-1">
                <div style="display:flex;">
                    <button type="button" class="cov-type-btn row-btn-pct ${initType === 'pct' ? 'active' : ''}"
                            onclick="setRowCovType(this.closest('tr'), 'pct')">%</button>
                    <button type="button" class="cov-type-btn row-btn-amt ${initType === 'amt' ? 'active' : ''}"
                            onclick="setRowCovType(this.closest('tr'), 'amt')">RD$</button>
                </div>
                <input type="number" class="form-control form-control-sm cov-input text-center"
                       value="${initCov}" min="0" step="0.01" style="width:68px;">
                <span class="cov-unit-label" style="font-size:.73rem;color:#8098bb;min-width:18px;">
                    ${initType === 'pct' ? '%' : 'RD$'}
                </span>
            </div>
        </td>
        <td class="td-ins ins-cell ${hidden}">RD$ 0.00</td>
        <td class="td-pat pat-cell ${hidden}">RD$ 0.00</td>
        <td class="text-center">
            <button type="button" class="btn-remove-row">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>`;

    document.getElementById('svc-body').appendChild(tr);

    tr.querySelector('.svc-select').addEventListener('change',  () => recalcRow(tr));
    tr.querySelector('.qty-input').addEventListener('input',    () => recalcRow(tr));
    tr.querySelector('.cov-input')?.addEventListener('input',   () => recalcRow(tr));
    tr.querySelector('.btn-remove-row').addEventListener('click', () => {
        tr.remove();
        if (!document.querySelectorAll('.svc-row').length)
            document.getElementById('svc-empty').style.display = '';
        recalculate();
    });

    recalcRow(tr);
    recalculate();
}

function setRowCovType(tr, type) {
    tr.dataset.covType = type;
    tr.querySelector('.row-btn-pct')?.classList.toggle('active', type === 'pct');
    tr.querySelector('.row-btn-amt')?.classList.toggle('active', type === 'amt');
    updateRowCovLabel(tr);
    recalcRow(tr);
}

function updateRowCovLabel(tr) {
    const lbl = tr.querySelector('.cov-unit-label');
    if (lbl) lbl.textContent = (tr.dataset.covType === 'pct') ? '%' : 'RD$';
}

function recalcRow(tr) {
    const sel      = tr.querySelector('.svc-select');
    const price    = parseFloat(sel.options[sel.selectedIndex]?.dataset.price || 0);
    const qty      = parseInt(tr.querySelector('.qty-input').value) || 1;
    const subtotal = price * qty;
    const covVal   = parseFloat(tr.querySelector('.cov-input')?.value || 0);
    const type     = tr.dataset.covType || 'pct';

    let insAmt = 0, patAmt = subtotal;
    if (hasInsurance && covVal > 0) {
        insAmt = type === 'pct'
            ? subtotal * (Math.min(covVal, 100) / 100)
            : Math.min(covVal, subtotal);
        patAmt = subtotal - insAmt;
    }

    tr.querySelector('.price-disp').value             = 'RD$ ' + fmt(price);
    tr.querySelector('.subtotal-cell').textContent     = 'RD$ ' + fmt(subtotal);
    const insCell = tr.querySelector('.ins-cell');
    const patCell = tr.querySelector('.pat-cell');
    if (insCell) insCell.textContent = 'RD$ ' + fmt(insAmt);
    if (patCell) patCell.textContent = 'RD$ ' + fmt(patAmt);

    recalculate();
}

function recalculate() {
    let subtotal = 0, totalIns = 0;
    document.querySelectorAll('.svc-row').forEach(tr => {
        const sel  = tr.querySelector('.svc-select');
        const price= parseFloat(sel?.options[sel?.selectedIndex]?.dataset.price || 0);
        const qty  = parseInt(tr.querySelector('.qty-input')?.value) || 1;
        const sub  = price * qty;
        let ins    = 0;
        if (hasInsurance) {
            const cov  = parseFloat(tr.querySelector('.cov-input')?.value || 0);
            const type = tr.dataset.covType || 'pct';
            ins = type === 'pct' ? sub * (Math.min(cov, 100) / 100) : Math.min(cov, sub);
        }
        subtotal += sub;
        totalIns += ins;
    });
    const total = subtotal - totalIns;
    document.getElementById('disp-subtotal').textContent = 'RD$ ' + fmt(subtotal);
    document.getElementById('disp-discount').textContent = '− RD$ ' + fmt(totalIns);
    document.getElementById('disp-total').textContent    = 'RD$ ' + fmt(total);
    document.getElementById('discount-row').style.display = totalIns > 0 ? 'flex' : 'none';
}

function fmt(n) {
    return parseFloat(n || 0).toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/* ══════════════════════════════════════
   MODAL — NUEVO PACIENTE
══════════════════════════════════════ */
document.getElementById('m_insurance_id').addEventListener('change', function () {
    document.getElementById('m_ins_num_wrap').classList.toggle('d-none', !this.value);
});

document.getElementById('btn-save-patient').addEventListener('click', async function () {
    const alertEl = document.getElementById('modal-alert');
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
        alertEl.className   = 'alert alert-danger';
        alertEl.textContent = 'Nombre, apellido y cédula son obligatorios.';
        return;
    }

    this.disabled = true;
    document.getElementById('patient-save-spin').classList.remove('d-none');
    document.getElementById('patient-save-icon').classList.add('d-none');

    try {
        const r = await fetch(URL_STORE, {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify(payload),
        });
        const data = await r.json();
        if (!r.ok) throw data;

        bootstrap.Modal.getInstance(document.getElementById('newPatientModal')).hide();
        selectPatient(data.patient);
        showToast('Paciente creado y seleccionado.', 'success');

        // Reset modal fields
        ['m_first_name','m_last_name','m_cedula','m_phone','m_email','m_birth_date','m_address','m_insurance_number']
            .forEach(id => document.getElementById(id).value = '');
        document.getElementById('m_gender').value       = '';
        document.getElementById('m_insurance_id').value = '';
        document.getElementById('m_ins_num_wrap').classList.add('d-none');
        alertEl.className = 'alert d-none';

    } catch (err) {
        const msg = err.errors
            ? Object.values(err.errors).flat().join(' ')
            : (err.message || 'Error al guardar el paciente.');
        alertEl.className   = 'alert alert-danger';
        alertEl.textContent = msg;
    } finally {
        this.disabled = false;
        document.getElementById('patient-save-spin').classList.add('d-none');
        document.getElementById('patient-save-icon').classList.remove('d-none');
    }
});

/* ══════════════════════════════════════
   SUBMIT VALIDATION
══════════════════════════════════════ */
document.getElementById('invoice-form').addEventListener('submit', function (e) {
    const errPat = document.getElementById('err-patient');
    if (!document.getElementById('patient_id').value) {
        e.preventDefault();
        errPat.textContent = 'Debes seleccionar un paciente.';
        document.getElementById('ps-input').focus();
        return;
    }
    if (!document.querySelectorAll('.svc-row').length) {
        e.preventDefault();
        showToast('Agrega al menos un servicio.', 'error');
        return;
    }
    if (!document.getElementById('branch_id').value) {
        e.preventDefault();
        showToast('Selecciona una sucursal.', 'error');
        return;
    }

    // Inject cov_value + cov_type as hidden inputs
    document.querySelectorAll('.svc-row').forEach(tr => {
        const sel   = tr.querySelector('.svc-select');
        const match = sel?.name?.match(/services\[(\d+)\]/);
        if (!match) return;
        const i = match[1];

        ['cov_value','cov_type'].forEach(field => {
            this.querySelector(`input[name="services[${i}][${field}]"]`)?.remove();
        });

        const covInput = tr.querySelector('.cov-input');
        const hVal = Object.assign(document.createElement('input'), {
            type: 'hidden', name: `services[${i}][cov_value]`, value: covInput?.value || 0
        });
        const hType = Object.assign(document.createElement('input'), {
            type: 'hidden', name: `services[${i}][cov_type]`, value: tr.dataset.covType || 'pct'
        });
        this.append(hVal, hType);
    });

    document.getElementById('btn-submit').disabled = true;
});

/* ══════════════════════════════════════
   TOAST
══════════════════════════════════════ */
function showToast(msg, type) {
    const d = document.createElement('div');
    d.className = 'toast-item toast-' + (type || 'success');
    d.innerHTML = `<i class="ri-${type === 'error' ? 'error-warning' : 'checkbox-circle'}-line fs-16"></i>${msg}`;
    document.getElementById('toast-container').appendChild(d);
    setTimeout(() => {
        d.style.transition = 'opacity .32s'; d.style.opacity = '0';
        setTimeout(() => d.remove(), 340);
    }, 3500);
}

/* ══════════════════════════════════════
   BOOT
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    psInit();
    addSvcRow();   // primera fila vacía
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});


/* ══════════════════════════════════════
   AUDIOLOGIST SELECTOR
══════════════════════════════════════ */
document.getElementById('audiologist_id').addEventListener('change', function () {
    const opt    = this.options[this.selectedIndex];
    const pill   = document.getElementById('aud-pill');
    const name   = opt.text?.split('—')[0]?.trim() || '';
    const branch = opt.text?.split('—')[1]?.trim() || '';

    if (!this.value) {
        pill.classList.add('d-none');
        return;
    }

    // Iniciales
    const initials = name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('');
    document.getElementById('aud-pill-av').textContent     = initials;
    document.getElementById('aud-pill-name').textContent   = name;
    document.getElementById('aud-pill-branch').textContent = branch
        ? '📍 ' + branch
        : '📍 ' + (document.getElementById('branch_id').options[document.getElementById('branch_id').selectedIndex]?.text || '');

    pill.classList.remove('d-none');
});

// Si hay un audiólogo pre-seleccionado (old values tras error de validación)
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('audiologist_id');
    if (sel.value) sel.dispatchEvent(new Event('change'));
});
</script>
@endpush

</x-app-layout>