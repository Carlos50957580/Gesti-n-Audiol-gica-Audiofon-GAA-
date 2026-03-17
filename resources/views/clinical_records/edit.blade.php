<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
    .hc-layout { display:grid; grid-template-columns:1fr 340px; gap:1.5rem; align-items:start; }
    @media(max-width:991px){ .hc-layout { grid-template-columns:1fr; } }
    .hc-card { border:none; border-radius:.75rem; overflow:hidden; box-shadow:0 2px 12px rgba(64,81,137,.08); }
    .hc-card-header { padding:1.1rem 1.5rem; display:flex; align-items:center; gap:.75rem; }
    .hc-card-header .icon-wrap { width:38px; height:38px; border-radius:.5rem; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:1.1rem; color:#fff; flex-shrink:0; }
    .hc-card-header h5 { color:#fff; margin:0; font-size:.97rem; font-weight:700; }
    .hc-card-header p  { color:rgba(255,255,255,.75); margin:0; font-size:.78rem; }
    .section-label { font-size:.68rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:#8098bb; display:flex; align-items:center; gap:.4rem; border-bottom:1px solid #f0f2f7; padding-bottom:.5rem; margin-bottom:1.1rem; }
    .hc-textarea { width:100%; border:1.5px solid #e2e8f0; border-radius:.6rem; padding:.85rem 1rem; font-size:.9rem; color:#344563; resize:vertical; min-height:110px; transition:border-color .2s,box-shadow .2s; background:#fdfdff; line-height:1.65; font-family:inherit; }
    .hc-textarea:focus { outline:none; border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); background:#fff; }
    .hc-textarea.is-invalid { border-color:#dc3545; }
    .hc-textarea::placeholder { color:#b0bec5; }
    .field-wrap { margin-bottom:1.25rem; }
    .field-label { display:flex; align-items:center; gap:.4rem; font-size:.82rem; font-weight:700; color:#405189; margin-bottom:.45rem; }
    .field-label i { color:#8098bb; font-size:.9rem; }
    .field-hint { font-size:.75rem; color:#8098bb; margin-top:.3rem; }
    .invalid-feedback { font-size:.78rem; color:#dc3545; margin-top:.3rem; display:block; }
    .status-toggle-wrap { display:flex; align-items:center; justify-content:space-between; background:#f8faff; border:1.5px solid #e2e8f0; border-radius:.6rem; padding:.9rem 1.1rem; }
    .status-toggle-label { font-size:.88rem; font-weight:600; color:#344563; }
    .status-toggle-hint  { font-size:.75rem; color:#8098bb; margin-top:.1rem; }
    .form-check-input[type=checkbox] { width:2.6rem; height:1.35rem; cursor:pointer; }
    .form-check-input:checked { background-color:#0ab39c; border-color:#0ab39c; }
    .side-card { border:none; border-radius:.75rem; box-shadow:0 2px 12px rgba(64,81,137,.08); overflow:hidden; }
    .side-header { padding:1rem 1.25rem; background:linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06)); border-bottom:1px solid #f0f2f7; display:flex; align-items:center; gap:.6rem; }
    .side-header h6 { margin:0; font-size:.88rem; font-weight:700; color:#344563; }
    .side-header i   { color:#405189; font-size:1.1rem; }
    .patient-badge { display:flex; align-items:center; gap:.85rem; padding:1rem 1.25rem; border-bottom:1px solid #f0f2f7; }
    .pat-avatar { width:44px; height:44px; border-radius:.5rem; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1rem; color:#fff; font-weight:700; background:linear-gradient(135deg,#405189 0%,#0ab39c 100%); }
    .pat-name   { font-size:.92rem; font-weight:700; color:#344563; }
    .pat-cedula { font-size:.76rem; color:#8098bb; margin-top:.1rem; }
    .info-row { display:flex; align-items:center; justify-content:space-between; padding:.65rem 1.25rem; border-bottom:1px solid #f3f5f9; font-size:.83rem; }
    .info-row:last-child { border-bottom:none; }
    .info-row .lbl { color:#8098bb; font-weight:600; display:flex; align-items:center; gap:.35rem; }
    .info-row .val { color:#344563; font-weight:600; text-align:right; }
    .inv-chip { display:inline-flex; align-items:center; gap:.3rem; background:linear-gradient(135deg,rgba(64,81,137,.1),rgba(10,179,156,.1)); color:#405189; font-weight:700; font-size:.82rem; padding:.22rem .65rem; border-radius:2rem; border:1px solid rgba(64,81,137,.15); font-family:monospace; }
    .svc-item { display:flex; align-items:center; gap:.65rem; padding:.6rem 1.25rem; border-bottom:1px solid #f3f5f9; font-size:.82rem; }
    .svc-item:last-child { border-bottom:none; }
    .svc-dot { width:8px; height:8px; border-radius:50%; background:linear-gradient(135deg,#405189,#0ab39c); flex-shrink:0; }
    .svc-name { flex-grow:1; color:#344563; font-weight:500; }
    .svc-price { color:#0ab39c; font-weight:700; font-size:.8rem; white-space:nowrap; }
    .status-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .7rem; border-radius:2rem; font-size:.75rem; font-weight:600; }
    .status-pill .dot { width:7px; height:7px; border-radius:50%; }
    .status-pendiente { background:#fef9c3; color:#92400e; }
    .status-pendiente .dot { background:#f59e0b; }
    .status-completada { background:#d1fae5; color:#065f46; }
    .status-completada .dot { background:#10b981; }
    .btn-save { background:linear-gradient(135deg,#405189,#0ab39c); border:none; color:#fff; border-radius:.6rem; padding:.7rem 1.75rem; font-weight:700; font-size:.92rem; display:inline-flex; align-items:center; gap:.5rem; transition:opacity .2s,transform .15s; width:100%; justify-content:center; }
    .btn-save:hover { opacity:.92; transform:translateY(-1px); color:#fff; }
    .btn-save:disabled { opacity:.65; transform:none; }
    .btn-back { display:inline-flex; align-items:center; gap:.4rem; color:#8098bb; font-size:.85rem; font-weight:600; text-decoration:none; padding:.35rem .75rem; border-radius:2rem; background:#f8faff; border:1px solid #e2e8f0; transition:all .15s; }
    .btn-back:hover { background:#405189; color:#fff; border-color:#405189; }
    .char-counter { font-size:.72rem; color:#b0bec5; text-align:right; margin-top:.25rem; }
    .char-counter.warn { color:#f59e0b; }
    #toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
    .toast-item { min-width:280px; padding:.85rem 1.1rem; border-radius:.5rem; color:#fff; font-size:.88rem; font-weight:500; display:flex; align-items:center; gap:.6rem; box-shadow:0 4px 20px rgba(0,0,0,.18); animation:toastIn .3s ease; }
    @keyframes toastIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
    .toast-error   { background:linear-gradient(135deg,#e74c3c,#c0392b); }
    @keyframes fadeInUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .fade-in   { animation:fadeInUp .35s ease both; }
    .fade-in-2 { animation:fadeInUp .35s ease .08s both; }
    .fade-in-3 { animation:fadeInUp .35s ease .16s both; }
</style>

<div id="toast-container"></div>

{{-- Breadcrumb --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('clinical-records.index') }}" class="btn-back">
                    <i class="ri-arrow-left-s-line"></i> Volver
                </a>
                <h4 class="mb-0">Historia Clínica</h4>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('clinical-records.index') }}">Historias Clínicas</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </div>
    </div>
</div>

<div class="hc-layout">

    {{-- ══════════════════════════════
         COLUMNA IZQUIERDA — Formulario
    ══════════════════════════════ --}}
    <div class="d-flex flex-column gap-4">

        {{-- Card: Motivo de consulta --}}
        <div class="hc-card fade-in">
            <div class="hc-card-header" style="background:linear-gradient(135deg,#405189 0%,#0ab39c 100%);">
                <div class="icon-wrap"><i class="ri-questionnaire-line"></i></div>
                <div>
                    <h5>Motivo de Consulta</h5>
                    <p>Anamnesis y síntomas referidos por el paciente</p>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="field-wrap">
                    <label class="field-label">
                        <i class="ri-chat-3-line"></i>Motivo de consulta / Anamnesis
                    </label>
                    <textarea
                        class="hc-textarea {{ $errors->has('reason_for_consultation') ? 'is-invalid' : '' }}"
                        id="f-reason" name="reason_for_consultation" maxlength="2000"
                        placeholder="Describa el motivo de consulta, síntomas principales, tiempo de evolución..."
                        oninput="updateCounter('f-reason','cnt-reason',2000)"
                    >{{ old('reason_for_consultation', $clinicalRecord->reason_for_consultation) }}</textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        @error('reason_for_consultation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="field-hint"><i class="ri-information-line me-1"></i>Incluya antecedentes auditivos, familiares y laborales si aplica.</div>
                        @enderror
                        <div class="char-counter" id="cnt-reason">{{ strlen(old('reason_for_consultation', $clinicalRecord->reason_for_consultation ?? '')) }}/2000</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Diagnóstico y plan --}}
        <div class="hc-card fade-in-2">
            <div class="hc-card-header" style="background:linear-gradient(135deg,#e74c3c 0%,#0ab39c 100%);">
                <div class="icon-wrap"><i class="ri-heart-pulse-line"></i></div>
                <div>
                    <h5>Diagnóstico y Plan de Tratamiento</h5>
                    <p>Impresión diagnóstica y conducta a seguir</p>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="field-wrap">
                    <label class="field-label"><i class="ri-stethoscope-line"></i>Diagnóstico</label>
                    <textarea
                        class="hc-textarea {{ $errors->has('diagnosis') ? 'is-invalid' : '' }}"
                        id="f-diagnosis" name="diagnosis" maxlength="2000"
                        placeholder="Impresión diagnóstica según hallazgos de la evaluación audiológica..."
                        oninput="updateCounter('f-diagnosis','cnt-diagnosis',2000)"
                    >{{ old('diagnosis', $clinicalRecord->diagnosis) }}</textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        @error('diagnosis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <span></span>
                        @enderror
                        <div class="char-counter" id="cnt-diagnosis">{{ strlen(old('diagnosis', $clinicalRecord->diagnosis ?? '')) }}/2000</div>
                    </div>
                </div>
                <div class="field-wrap mb-0">
                    <label class="field-label"><i class="ri-capsule-line"></i>Plan de tratamiento</label>
                    <textarea
                        class="hc-textarea {{ $errors->has('treatment_plan') ? 'is-invalid' : '' }}"
                        id="f-treatment" name="treatment_plan" maxlength="2000"
                        placeholder="Indicaciones, remisiones, uso de auxiliar auditivo, seguimiento programado..."
                        oninput="updateCounter('f-treatment','cnt-treatment',2000)"
                    >{{ old('treatment_plan', $clinicalRecord->treatment_plan) }}</textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        @error('treatment_plan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <span></span>
                        @enderror
                        <div class="char-counter" id="cnt-treatment">{{ strlen(old('treatment_plan', $clinicalRecord->treatment_plan ?? '')) }}/2000</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Notas --}}
        <div class="hc-card fade-in-3">
            <div class="hc-card-header" style="background:linear-gradient(135deg,#6c757d,#495057);">
                <div class="icon-wrap"><i class="ri-sticky-note-line"></i></div>
                <div>
                    <h5>Notas Adicionales</h5>
                    <p>Observaciones internas del audiólogo</p>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="field-wrap mb-0">
                    <label class="field-label">
                        <i class="ri-sticky-note-line"></i>Notas / Observaciones
                        <span class="text-muted fw-normal ms-1">(opcional)</span>
                    </label>
                    <textarea
                        class="hc-textarea {{ $errors->has('notes') ? 'is-invalid' : '' }}"
                        id="f-notes" name="notes" maxlength="1000" style="min-height:80px;"
                        placeholder="Observaciones adicionales, notas internas..."
                        oninput="updateCounter('f-notes','cnt-notes',1000)"
                    >{{ old('notes', $clinicalRecord->notes) }}</textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <span></span>
                        @enderror
                        <div class="char-counter" id="cnt-notes">{{ strlen(old('notes', $clinicalRecord->notes ?? '')) }}/1000</div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /left column --}}

    {{-- ══════════════════════════════
         COLUMNA DERECHA
    ══════════════════════════════ --}}
    <div class="d-flex flex-column gap-3 fade-in">

        {{-- Card: Guardar --}}
        <div class="side-card">
            <div class="side-header">
                <i class="ri-save-3-line"></i>
                <h6>Guardar Historia Clínica</h6>
            </div>
            <div class="card-body p-3">
                <div class="status-toggle-wrap mb-3">
                    <div>
                        <div class="status-toggle-label">Estado de la HC</div>
                        <div class="status-toggle-hint" id="status-hint">
                            {{ $clinicalRecord->status === 'completada' ? 'Marcada como completada' : 'Aún no completada' }}
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="f-status"
                               {{ $clinicalRecord->status === 'completada' ? 'checked' : '' }}
                               onchange="updateStatusHint()">
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.83rem;border-radius:.5rem;">
                        <i class="ri-error-warning-line me-1"></i>Por favor corrige los errores antes de guardar.
                    </div>
                @endif

                {{-- ✅ action apunta a $invoice->id --}}
                <form id="hc-form"
                      action="{{ route('clinical-records.update', $invoice->id) }}"
                      method="POST">
                    @csrf
                    @method('PUT')
                </form>

                <button type="button" class="btn-save" id="btn-save" onclick="submitForm()">
                    <span class="spinner-border spinner-border-sm d-none" id="save-spinner"></span>
                    <i class="ri-save-3-line" id="save-icon"></i>
                    Guardar Historia Clínica
                </button>
                <div class="text-center mt-2">
                    <a href="{{ route('clinical-records.index') }}" class="text-muted" style="font-size:.8rem;">
                        <i class="ri-close-line me-1"></i>Cancelar y volver
                    </a>
                </div>
            </div>
        </div>

        {{-- Card: Paciente — ahora usa $invoice->patient --}}
        <div class="side-card">
            <div class="side-header">
                <i class="ri-user-heart-line"></i>
                <h6>Paciente</h6>
            </div>
            <div class="patient-badge">
                <div class="pat-avatar">
                    {{ strtoupper(substr($invoice->patient->first_name,0,1) . substr($invoice->patient->last_name,0,1)) }}
                </div>
                <div>
                    <div class="pat-name">{{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}</div>
                    <div class="pat-cedula"><i class="ri-id-card-line me-1"></i>{{ $invoice->patient->cedula }}</div>
                </div>
            </div>
            <div class="info-row">
                <span class="lbl"><i class="ri-phone-line"></i>Teléfono</span>
                <span class="val">{{ $invoice->patient->phone ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="lbl"><i class="ri-user-line"></i>Género</span>
                <span class="val">
                    {{ $invoice->patient->gender === 'M' ? 'Masculino' : ($invoice->patient->gender === 'F' ? 'Femenino' : '—') }}
                </span>
            </div>
            <div class="info-row">
                <span class="lbl"><i class="ri-calendar-line"></i>Fecha nac.</span>
                <span class="val">
                    {{ $invoice->patient->birth_date
                        ? \Carbon\Carbon::parse($invoice->patient->birth_date)->format('d/m/Y')
                        : '—' }}
                </span>
            </div>
            @if($invoice->patient->insurance)
            <div class="info-row">
                <span class="lbl"><i class="ri-shield-check-line"></i>Seguro</span>
                <span class="val" style="font-size:.78rem;">{{ $invoice->patient->insurance->name }}</span>
            </div>
            @endif
        </div>

        {{-- Card: Factura — ahora usa $invoice directamente --}}
        <div class="side-card">
            <div class="side-header">
                <i class="ri-file-text-line"></i>
                <h6>Factura Asociada</h6>
            </div>
            <div class="info-row">
                <span class="lbl"><i class="ri-hashtag"></i>Número</span>
                <span class="inv-chip">
                    <i class="ri-file-text-line"></i>
                    FAC-{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
                </span>
            </div>
            <div class="info-row">
                <span class="lbl"><i class="ri-map-pin-line"></i>Sucursal</span>
                <span class="val">{{ $invoice->branch->name }}</span>
            </div>
            <div class="info-row">
                <span class="lbl"><i class="ri-money-dollar-circle-line"></i>Total</span>
                <span class="val text-success fw-bold">RD$ {{ number_format($invoice->total, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="lbl"><i class="ri-calendar-check-line"></i>Fecha pago</span>
                <span class="val">{{ $invoice->updated_at->format('d/m/Y') }}</span>
            </div>

            @if($invoice->items->count())
            <div style="padding:.6rem 1.25rem .3rem; border-top:1px solid #f0f2f7;">
                <div class="section-label" style="font-size:.65rem;">
                    <i class="ri-stethoscope-line"></i>Servicios facturados
                </div>
            </div>
            @foreach($invoice->items as $item)
            <div class="svc-item">
                <div class="svc-dot"></div>
                <div class="svc-name">{{ $item->service->name }}</div>
                <div class="svc-price">RD$ {{ number_format($item->price, 2) }}</div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Card: Estado actual --}}
        <div class="side-card">
            <div class="card-body p-3 d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div style="font-size:.72rem;font-weight:700;color:#8098bb;text-transform:uppercase;letter-spacing:.08em;">Estado actual</div>
                    <div class="mt-1">
                        @if($clinicalRecord->status === 'completada')
                            <span class="status-pill status-completada"><span class="dot"></span>Completada</span>
                        @else
                            <span class="status-pill status-pendiente"><span class="dot"></span>Pendiente</span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <div style="font-size:.72rem;font-weight:700;color:#8098bb;text-transform:uppercase;letter-spacing:.08em;">Última actualización</div>
                    <div style="font-size:.82rem;color:#344563;font-weight:600;margin-top:.15rem;">
                        {{ $clinicalRecord->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /right column --}}

</div>

</div>
</div>

@push('scripts')
<script>
function updateCounter(fieldId, counterId, max) {
    const len = document.getElementById(fieldId).value.length;
    const el  = document.getElementById(counterId);
    el.textContent = len + '/' + max;
    el.classList.toggle('warn', len > max * 0.85);
}

document.addEventListener('DOMContentLoaded', () => {
    [['f-reason','cnt-reason',2000],['f-diagnosis','cnt-diagnosis',2000],
     ['f-treatment','cnt-treatment',2000],['f-notes','cnt-notes',1000]].forEach(([f,c,m]) => {
        if (document.getElementById(f)) updateCounter(f, c, m);
    });
    @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
    @if($errors->any())    showToast('Por favor corrige los errores.', 'error'); @endif
});

function updateStatusHint() {
    document.getElementById('status-hint').textContent = document.getElementById('f-status').checked
        ? 'Se marcará como completada' : 'Se guardará como pendiente';
}

function submitForm() {
    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    document.getElementById('save-spinner').classList.remove('d-none');
    document.getElementById('save-icon').classList.add('d-none');

    const form = document.getElementById('hc-form');
    const fields = {
        reason_for_consultation : document.getElementById('f-reason').value,
        diagnosis               : document.getElementById('f-diagnosis').value,
        treatment_plan          : document.getElementById('f-treatment').value,
        notes                   : document.getElementById('f-notes').value,
        status                  : document.getElementById('f-status').checked ? 'completada' : 'pendiente',
    };

    form.querySelectorAll('input[data-dynamic]').forEach(el => el.remove());
    Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = name;
        input.value = value;
        input.setAttribute('data-dynamic', '1');
        form.appendChild(input);
    });
    form.submit();
}

function showToast(msg, type) {
    type = type || 'success';
    const div = document.createElement('div');
    div.className = 'toast-item toast-' + type;
    div.innerHTML = '<i class="ri-' + (type === 'success' ? 'checkbox-circle' : 'error-warning') + '-line fs-16"></i>' + msg;
    document.getElementById('toast-container').appendChild(div);
    setTimeout(() => { div.style.transition='opacity .4s'; div.style.opacity='0'; setTimeout(()=>div.remove(),400); }, 3500);
}
</script>
@endpush
</x-app-layout>