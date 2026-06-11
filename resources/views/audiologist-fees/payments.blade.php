{{-- resources/views/audiologist-fees/payments.blade.php --}}
<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    <style>
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

        .payments-table th {
            font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
            color:#8098bb; border-bottom:2px solid #e9ecef; padding:.85rem 1rem; white-space:nowrap;
        }
        .payments-table td { padding:.82rem 1rem; vertical-align:middle; }
        .payments-table tbody tr { transition:background .15s; border-bottom:1px solid #f3f5f9; }
        .payments-table tbody tr:hover { background:#f8faff; }

        .method-badge {
            display:inline-flex; align-items:center; gap:.4rem;
            padding:.25rem .7rem; border-radius:2rem; font-size:.7rem; font-weight:600;
        }
        .method-bank_transfer { background:#e0e7ff; color:#3730a3; }
        .method-cash { background:#d1fae5; color:#065f46; }
        .method-check { background:#fef3c7; color:#92400e; }
        .method-other { background:#e5e7eb; color:#374151; }

        .btn-action {
            width:32px; height:32px; padding:0; border:none; border-radius:.4rem;
            display:inline-flex; align-items:center; justify-content:center; transition:all .15s;
        }
        .btn-action:hover { transform:scale(1.12); }

        .mh-primary { background:linear-gradient(135deg,#405189,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-info { background:linear-gradient(135deg,#299cdb,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-danger { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-success { background:linear-gradient(135deg,#0ab39c,#3d9f80); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-primary .btn-close, .mh-info .btn-close, .mh-danger .btn-close, .mh-success .btn-close { filter:invert(1); }

        .section-label {
            font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
            color:#8098bb; border-bottom:1px solid #f0f2f7; padding-bottom:.4rem; margin-bottom:.9rem;
        }

        .detail-row {
            display:flex; gap:.75rem; align-items:flex-start;
            padding:.7rem 0; border-bottom:1px solid #f3f5f9;
        }
        .detail-row:last-child { border-bottom:none; }
        .detail-icon { width:36px; height:36px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem; }
        .detail-lbl { font-size:.7rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#8098bb; }
        .detail-val { font-size:.91rem; font-weight:500; color:#344563; margin-top:.1rem; }

        .fee-select-table {
            font-size:.85rem;
        }
        .fee-select-table th {
            font-size:.7rem;
            padding:.5rem;
        }
        .fee-select-table td {
            padding:.5rem;
        }

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

        @keyframes fadeInUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .anim-row { animation:fadeInUp .3s ease both; }
        
        .total-card {
            background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%);
            border-radius: 0.75rem;
            padding: 1rem;
        }
    </style>

    <div id="toast-container"></div>

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Pagos a medicos</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('audiologist-fees.index') }}">Honorarios</a></li>
                    <li class="breadcrumb-item active">Pagos</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-bank-card-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">Total Pagado</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">RD$ {{ number_format($payments->sum('amount'), 2) }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-bank-card-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-cash-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">Efectivo</div>
                        <div class="fw-bold fs-5 lh-1 mt-1">RD$ {{ number_format($payments->where('payment_method', 'cash')->sum('amount'), 2) }}</div>
                    </div>
                    <div class="stat-bg text-success"><i class="ri-cash-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-info-subtle text-info"><i class="ri-bank-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">Transferencia</div>
                        <div class="fw-bold fs-5 lh-1 mt-1">RD$ {{ number_format($payments->where('payment_method', 'bank_transfer')->sum('amount'), 2) }}</div>
                    </div>
                    <div class="stat-bg text-info"><i class="ri-bank-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-survey-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;">Transacciones</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $payments->count() }}</div>
                    </div>
                    <div class="stat-bg text-warning"><i class="ri-survey-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main table card --}}
    <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
            <h5 class="card-title mb-0 flex-grow-1">Registro de Pagos</h5>
            <div class="position-relative" style="width:230px;">
                <i class="ri-search-line search-icon" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:#8098bb;"></i>
                <input type="text" id="search-input" class="form-control" placeholder="Buscar...">
            </div>
            <select id="audiologist-filter" class="form-select" style="width:180px;">
                <option value="">Todos los Medicos</option>
                @foreach($audiologists as $audiologist)
                    <option value="{{ $audiologist->id }}">{{ $audiologist->name }}</option>
                @endforeach
            </select>
            <input type="date" id="date-from" class="form-control" style="width:150px;">
            <input type="date" id="date-to" class="form-control" style="width:150px;">
            <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1" id="btn-new-payment">
                <i class="ri-add-line"></i> Nuevo Pago
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table payments-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Medico</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th>Fecha Pago</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="payments-tbody">
                        @forelse($payments as $payment)
                        <tr class="anim-row"
                            data-id="{{ $payment->id }}"
                            data-audiologist="{{ strtolower($payment->audiologist->name) }}"
                            data-date="{{ $payment->payment_date }}">
                            <td>#{{ $payment->id }}</td>
                            <td>{{ $payment->audiologist->name }}</td>
                            <td><span class="fw-bold text-primary">RD$ {{ number_format($payment->amount, 2) }}</span></td>
                            <td>
                                @php
                                    $methodConfig = [
                                        'bank_transfer' => ['class' => 'method-bank_transfer', 'icon' => 'ri-bank-line', 'text' => 'Transferencia'],
                                        'cash' => ['class' => 'method-cash', 'icon' => 'ri-cash-line', 'text' => 'Efectivo'],
                                        'check' => ['class' => 'method-check', 'icon' => 'ri-survey-line', 'text' => 'Cheque'],
                                        'other' => ['class' => 'method-other', 'icon' => 'ri-more-line', 'text' => 'Otro'],
                                    ][$payment->payment_method];
                                @endphp
                                <span class="method-badge {{ $methodConfig['class'] }}">
                                    <i class="{{ $methodConfig['icon'] }}"></i> {{ $methodConfig['text'] }}
                                </span>
                            </td>
                            <td>{{ $payment->reference_number ?? '—' }}</td>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-action bg-info-subtle text-info"
                                            title="Ver detalle" onclick="showPaymentDetail({{ $payment->id }})">
                                        <i class="ri-eye-fill fs-13"></i>
                                    </button>
                                    <button type="button" class="btn btn-action bg-danger-subtle text-danger"
                                            title="Eliminar" onclick="openDeleteModal({{ $payment->id }})">
                                        <i class="ri-delete-bin-fill fs-13"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <i class="ri-bank-card-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                    <p class="text-muted mb-0">No hay pagos registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="no-results" class="text-center py-5 d-none">
                <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0">No se encontraron pagos con esos criterios.</p>
            </div>

            @if($payments->hasPages())
            <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- MODAL: Crear Pago --}}
<div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:900px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-success py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-bank-card-line fs-18"></i> Registrar Pago a Medico
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="payment-alert" class="alert d-none mb-3"></div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Medico *</label>
                        <select id="payment-audiologist" class="form-select" required>
                            <option value="">-- Seleccione --</option>
                            @foreach($audiologists as $audiologist)
                                <option value="{{ $audiologist->id }}">{{ $audiologist->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="err-audiologist_id"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Método de Pago *</label>
                        <select id="payment-method" class="form-select" required>
                            <option value="cash">Efectivo</option>
                            <option value="bank_transfer">Transferencia Bancaria</option>
                            <option value="check">Cheque</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de Pago *</label>
                        <input type="date" id="payment-date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <div class="invalid-feedback" id="err-payment_date"></div>
                    </div>
                    <div class="col-md-6" id="reference-group">
                        <label class="form-label fw-semibold">Número de Referencia</label>
                        <input type="text" id="reference-number" class="form-control" placeholder="N° cheque, transferencia, etc.">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notas</label>
                        <textarea id="payment-notes" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
                    </div>
                </div>
                
                <div class="section-label"><i class="ri-receipt-line me-1"></i>Honorarios Pendientes</div>
                <div id="fees-container">
                    <div class="text-center py-4" id="loading-fees" style="display:none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando honorarios pendientes...</p>
                    </div>
                    <div class="text-center py-3" id="no-fees-message">
                        <i class="ri-information-line text-muted fs-4"></i>
                        <p class="text-muted mt-2 mb-0">Seleccione un Medico para ver los honorarios pendientes</p>
                    </div>
                    <div id="fees-table-container" style="display:none;">
                        <div class="table-responsive">
                            <table class="table fee-select-table">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">
                                            <input type="checkbox" id="select-all-fees">
                                        </th>
                                        <th>Factura</th>
                                        <th>Honorario</th>
                                        <th>Pagado</th>
                                        <th>Pendiente</th>
                                    </tr>
                                </thead>
                                <tbody id="pending-fees-list"></tbody>
                            </table>
                        </div>
                        <div class="total-card mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Total a Pagar:</span>
                                    <strong class="fs-3 text-primary ms-2" id="total-selected">RD$ 0.00</strong>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">Se pagará el monto total de las facturas seleccionadas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-2" id="btn-save-payment">
                    <span class="spinner-border spinner-border-sm d-none" id="payment-spinner"></span>
                    <i class="ri-save-line" id="payment-icon"></i>
                    <span>Registrar Pago</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Ver Detalle Pago --}}
<div class="modal fade" id="showDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-bank-card-line fs-18"></i> Detalle del Pago
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detail-content">
                <div class="text-center mb-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Eliminar Pago --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="stat-icon mx-auto mb-3" style="width:60px;height:60px;font-size:1.5rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">
                    <i class="ri-bank-card-line"></i>
                </div>
                <p class="mb-2 fs-5 fw-semibold">¿Eliminar este pago?</p>
                <p class="text-muted mb-0">Esta acción es <strong>irreversible</strong>. Los honorarios volverán a estado pendiente.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="min-width:100px;">Cancelar</button>
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2" id="btn-confirm-delete" style="min-width:120px;">
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
const URL_BASE = "{{ route('audiologist-fees.payments') }}";
const URL_PENDING_FEES_BASE = "{{ url('audiologist-fees/payments/pending') }}";
let paymentModal, showDetailModal, deleteModal;
let deletePaymentId = null;

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar modales
    paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    showDetailModal = new bootstrap.Modal(document.getElementById('showDetailModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Botón Nuevo Pago
    const btnNewPayment = document.getElementById('btn-new-payment');
    if (btnNewPayment) {
        btnNewPayment.addEventListener('click', function(e) {
            e.preventDefault();
            openCreatePaymentModal();
        });
    }
    
    // Selector de audiólogo
    const audiologistSelect = document.getElementById('payment-audiologist');
    if (audiologistSelect) {
        audiologistSelect.addEventListener('change', loadPendingFees);
    }
    
    // Método de pago
    const paymentMethodSelect = document.getElementById('payment-method');
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            const refGroup = document.getElementById('reference-group');
            if (refGroup) {
                refGroup.style.display = this.value === 'cash' ? 'none' : 'block';
            }
        });
        paymentMethodSelect.dispatchEvent(new Event('change'));
    }
    
    // Select all fees
    const selectAllCheckbox = document.getElementById('select-all-fees');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleAllFees);
    }
    
    // Botón guardar pago
    const saveBtn = document.getElementById('btn-save-payment');
    if (saveBtn) {
        saveBtn.addEventListener('click', savePayment);
    }
    
    // Botón confirmar eliminar
    const confirmDeleteBtn = document.getElementById('btn-confirm-delete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', confirmDeletePayment);
    }
    
    // Filtros
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.addEventListener('input', filterTable);
    
    const audiologistFilter = document.getElementById('audiologist-filter');
    if (audiologistFilter) audiologistFilter.addEventListener('change', filterTable);
    
    const dateFrom = document.getElementById('date-from');
    if (dateFrom) dateFrom.addEventListener('change', filterTable);
    
    const dateTo = document.getElementById('date-to');
    if (dateTo) dateTo.addEventListener('change', filterTable);
    
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});

// ── Search & filters ───────────────────────────────────────
function filterTable() {
    const search = document.getElementById('search-input')?.value.toLowerCase().trim() || '';
    const audiologistId = document.getElementById('audiologist-filter')?.value || '';
    const dateFrom = document.getElementById('date-from')?.value || '';
    const dateTo = document.getElementById('date-to')?.value || '';
    
    const rows = document.querySelectorAll('#payments-tbody tr[data-id]');
    let visible = 0;
    
    rows.forEach(tr => {
        let show = true;
        
        if (search && !tr.textContent.toLowerCase().includes(search)) show = false;
        if (dateFrom && tr.dataset.date < dateFrom) show = false;
        if (dateTo && tr.dataset.date > dateTo) show = false;
        
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    
    const noResults = document.getElementById('no-results');
    if (noResults) noResults.classList.toggle('d-none', visible > 0);
}

// ── Create Payment ─────────────────────────────────────────
function openCreatePaymentModal() {
    // Resetear formulario
    const form = document.getElementById('payment-form');
    if (form) form.reset();
    
    const paymentDate = document.getElementById('payment-date');
    if (paymentDate) paymentDate.value = new Date().toISOString().split('T')[0];
    
    const pendingList = document.getElementById('pending-fees-list');
    if (pendingList) pendingList.innerHTML = '';
    
    const feesTable = document.getElementById('fees-table-container');
    if (feesTable) feesTable.style.display = 'none';
    
    const noFeesMsg = document.getElementById('no-fees-message');
    if (noFeesMsg) {
        noFeesMsg.style.display = 'block';
        noFeesMsg.innerHTML = `
            <i class="ri-information-line text-muted fs-4"></i>
            <p class="text-muted mt-2 mb-0">Seleccione un Medico para ver los honorarios pendientes</p>
        `;
    }
    
    const selectAll = document.getElementById('select-all-fees');
    if (selectAll) selectAll.checked = false;
    
    clearPaymentErrors();
    paymentModal.show();
}

async function loadPendingFees() {
    const audiologistId = document.getElementById('payment-audiologist')?.value;
    
    if (!audiologistId) {
        const feesTable = document.getElementById('fees-table-container');
        if (feesTable) feesTable.style.display = 'none';
        
        const noFeesMsg = document.getElementById('no-fees-message');
        if (noFeesMsg) {
            noFeesMsg.style.display = 'block';
            noFeesMsg.innerHTML = `
                <i class="ri-information-line text-muted fs-4"></i>
                <p class="text-muted mt-2 mb-0">Seleccione un Medico para ver los honorarios pendientes</p>
            `;
        }
        return;
    }
    
    // Mostrar loading
    const feesTable = document.getElementById('fees-table-container');
    if (feesTable) feesTable.style.display = 'none';
    
    const noFeesMsg = document.getElementById('no-fees-message');
    if (noFeesMsg) noFeesMsg.style.display = 'none';
    
    const loadingFees = document.getElementById('loading-fees');
    if (loadingFees) loadingFees.style.display = 'block';
    
    try {
        const url = `${URL_PENDING_FEES_BASE}/${audiologistId}`;
        const res = await fetch(url);
        
        if (!res.ok) {
            throw new Error('Error al cargar los datos');
        }
        
        const fees = await res.json();
        
        if (loadingFees) loadingFees.style.display = 'none';
        
        if (!fees || fees.length === 0) {
            if (feesTable) feesTable.style.display = 'none';
            if (noFeesMsg) {
                noFeesMsg.style.display = 'block';
                noFeesMsg.innerHTML = `
                    <i class="ri-check-line text-success fs-4"></i>
                    <p class="text-success mt-2 mb-0">No hay honorarios pendientes para este Medico</p>
                `;
            }
            return;
        }
        
        renderPendingFeesTable(fees);
        if (feesTable) feesTable.style.display = 'block';
        if (noFeesMsg) noFeesMsg.style.display = 'none';
        
    } catch (error) {
        console.error('Error loading pending fees:', error);
        if (loadingFees) loadingFees.style.display = 'none';
        if (feesTable) feesTable.style.display = 'none';
        if (noFeesMsg) {
            noFeesMsg.style.display = 'block';
            noFeesMsg.innerHTML = `
                <i class="ri-error-warning-line text-danger fs-4"></i>
                <p class="text-danger mt-2 mb-0">Error al cargar los honorarios pendientes</p>
            `;
        }
        showToast('Error al cargar los honorarios pendientes', 'error');
    }
}

function renderPendingFeesTable(fees) {
    const tbody = document.getElementById('pending-fees-list');
    if (!tbody) return;
    tbody.innerHTML = '';
    
    fees.forEach(fee => {
        const remaining = parseFloat(fee.remaining_amount) || 0;
        const feeAmount = parseFloat(fee.fee_amount) || 0;
        const paidAmount = parseFloat(fee.paid_amount) || 0;
        
        const row = document.createElement('tr');
        row.setAttribute('data-fee-id', fee.id);
        row.innerHTML = `
            <td class="text-center">
                <input type="checkbox" class="fee-checkbox" data-fee-id="${fee.id}" data-amount="${remaining}">
            </td>
            <td>FAC-${String(fee.invoice_id).padStart(6, '0')}</td>
            <td>RD$ ${feeAmount.toLocaleString('es-DO', {minimumFractionDigits:2})}</td>
            <td>RD$ ${paidAmount.toLocaleString('es-DO', {minimumFractionDigits:2})}</td>
            <td class="fw-bold text-warning">RD$ ${remaining.toLocaleString('es-DO', {minimumFractionDigits:2})}</td>
        `;
        tbody.appendChild(row);
    });
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.fee-checkbox').forEach(checkbox => {
        checkbox.removeEventListener('change', updateTotalSelected);
        checkbox.addEventListener('change', updateTotalSelected);
    });
    
    // Seleccionar todas por defecto
    const selectAll = document.getElementById('select-all-fees');
    if (selectAll) selectAll.checked = true;
    document.querySelectorAll('.fee-checkbox').forEach(cb => cb.checked = true);
    updateTotalSelected();
}

function toggleAllFees() {
    const selectAll = document.getElementById('select-all-fees');
    if (!selectAll) return;
    
    const isChecked = selectAll.checked;
    document.querySelectorAll('.fee-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    updateTotalSelected();
}

function updateTotalSelected() {
    let total = 0;
    document.querySelectorAll('.fee-checkbox:checked').forEach(checkbox => {
        const amount = parseFloat(checkbox.dataset.amount) || 0;
        total += amount;
    });
    
    const totalSelectedSpan = document.getElementById('total-selected');
    if (totalSelectedSpan) {
        totalSelectedSpan.innerHTML = `RD$ ${total.toLocaleString('es-DO', {minimumFractionDigits:2})}`;
    }
}

async function savePayment() {
    const audiologistId = document.getElementById('payment-audiologist')?.value;
    if (!audiologistId) {
        const errElement = document.getElementById('err-audiologist_id');
        if (errElement) {
            errElement.textContent = 'Seleccione un Medico';
            document.getElementById('payment-audiologist')?.classList.add('is-invalid');
        }
        return;
    }
    
    const selectedFees = [];
    const amounts = [];
    let totalAmount = 0;
    
    document.querySelectorAll('.fee-checkbox:checked').forEach(checkbox => {
        const feeId = checkbox.dataset.feeId;
        const amount = parseFloat(checkbox.dataset.amount) || 0;
        
        if (amount > 0) {
            selectedFees.push(feeId);
            amounts.push(amount);
            totalAmount += amount;
        }
    });
    
    if (selectedFees.length === 0) {
        showPaymentAlert('Seleccione al menos un honorario para pagar', 'warning');
        return;
    }
    
    if (totalAmount <= 0) {
        showPaymentAlert('El monto total debe ser mayor a 0', 'warning');
        return;
    }
    
    const payload = {
        audiologist_id: parseInt(audiologistId),
        amount: totalAmount,
        payment_date: document.getElementById('payment-date')?.value,
        payment_method: document.getElementById('payment-method')?.value,
        reference_number: document.getElementById('reference-number')?.value || null,
        fee_ids: selectedFees.map(id => parseInt(id)),
        amounts: amounts,
        notes: document.getElementById('payment-notes')?.value || null,
    };
    
    setBtnLoading(true);
    
    try {
        const res = await fetch(URL_BASE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify(payload)
        });
        
        const data = await res.json();
        
        if (!res.ok) {
            if (data.errors) {
                showPaymentFieldErrors(data.errors);
            } else {
                showPaymentAlert(data.message || 'Error al registrar el pago', 'danger');
            }
            return;
        }
        
        paymentModal.hide();
        showToast(data.message || 'Pago registrado correctamente', 'success');
        setTimeout(() => location.reload(), 800);
        
    } catch (error) {
        console.error('Error saving payment:', error);
        showPaymentAlert('Error de conexión. Intente nuevamente.', 'danger');
    } finally {
        setBtnLoading(false);
    }
}

// ── Show Payment Detail ───────────────────────────────────
async function showPaymentDetail(id) {
    showDetailModal.show();
    const detailContent = document.getElementById('detail-content');
    if (detailContent) {
        detailContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando detalles del pago...</p>
            </div>
        `;
    }
    
    try {
        const res = await fetch(`${URL_BASE}/${id}`);
        if (!res.ok) throw new Error('Error al cargar los detalles');
        
        const payment = await res.json();
        
        const methodNames = {
            bank_transfer: 'Transferencia Bancaria',
            cash: 'Efectivo',
            check: 'Cheque',
            other: 'Otro'
        };
        
        const methodIcons = {
            bank_transfer: 'ri-bank-line',
            cash: 'ri-cash-line',
            check: 'ri-survey-line',
            other: 'ri-more-line'
        };
        
        let feesHtml = '';
        if (payment.fees && payment.fees.length > 0) {
            payment.fees.forEach(fee => {
                feesHtml += `
                    <div class="detail-row">
                        <div class="detail-icon bg-primary-subtle text-primary">
                            <i class="ri-receipt-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="detail-lbl">Factura FAC-${String(fee.invoice_id).padStart(6, '0')}</div>
                            <div class="detail-val">RD$ ${parseFloat(fee.pivot.amount_applied).toLocaleString('es-DO', {minimumFractionDigits:2})}</div>
                        </div>
                    </div>
                `;
            });
        }
        
        if (detailContent) {
            detailContent.innerHTML = `
                <div class="text-center mb-4">
                    <div class="stat-icon mx-auto mb-2 bg-success-subtle text-success" style="width:60px;height:60px;font-size:1.5rem;">
                        <i class="ri-bank-card-line"></i>
                    </div>
                    <h5 class="mb-1">Pago #${payment.id}</h5>
                    <span class="method-badge method-${payment.payment_method}">
                        <i class="${methodIcons[payment.payment_method]}"></i>
                        ${methodNames[payment.payment_method]}
                    </span>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-warning-subtle text-warning"><i class="ri-user-star-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Medico</div>
                        <div class="detail-val">${payment.audiologist?.name || 'N/A'}</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-danger-subtle text-danger"><i class="ri-money-dollar-circle-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Monto Total</div>
                        <div class="detail-val fs-4 fw-bold text-primary">RD$ ${parseFloat(payment.amount).toLocaleString('es-DO', {minimumFractionDigits:2})}</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-info-subtle text-info"><i class="ri-calendar-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Fecha de Pago</div>
                        <div class="detail-val">${new Date(payment.payment_date).toLocaleDateString('es-DO')}</div>
                    </div>
                </div>
                
                ${payment.reference_number ? `
                <div class="detail-row">
                    <div class="detail-icon bg-secondary-subtle text-secondary"><i class="ri-survey-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Referencia</div>
                        <div class="detail-val">${escapeHtml(payment.reference_number)}</div>
                    </div>
                </div>
                ` : ''}
                
                ${payment.notes ? `
                <div class="detail-row">
                    <div class="detail-icon bg-dark-subtle text-dark"><i class="ri-file-text-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Notas</div>
                        <div class="detail-val">${escapeHtml(payment.notes)}</div>
                    </div>
                </div>
                ` : ''}
                
                ${feesHtml ? `
                <div class="section-label mt-3"><i class="ri-receipt-line me-1"></i>Honorarios Pagados</div>
                ${feesHtml}
                ` : ''}
                
                <div class="detail-row">
                    <div class="detail-icon bg-success-subtle text-success"><i class="ri-time-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Registrado</div>
                        <div class="detail-val">${new Date(payment.created_at).toLocaleString('es-DO')}</div>
                    </div>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Error loading payment detail:', error);
        if (detailContent) {
            detailContent.innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="ri-error-warning-line fs-1"></i>
                    <p class="mt-2">Error al cargar los detalles del pago</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="showPaymentDetail(${id})">
                        <i class="ri-refresh-line"></i> Reintentar
                    </button>
                </div>
            `;
        }
    }
}

// ── Delete Payment ─────────────────────────────────────────
function openDeleteModal(id) {
    deletePaymentId = id;
    deleteModal.show();
}

async function confirmDeletePayment() {
    if (!deletePaymentId) return;
    
    setBtnLoading(true, 'btn-confirm-delete', 'delete-spinner', 'delete-icon');
    
    try {
        const res = await fetch(`${URL_BASE}/${deletePaymentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            }
        });
        
        const data = await res.json();
        deleteModal.hide();
        
        if (!res.ok) {
            showToast(data.message || 'Error al eliminar el pago', 'error');
            return;
        }
        
        showToast(data.message || 'Pago eliminado correctamente', 'success');
        setTimeout(() => location.reload(), 800);
        
    } catch (error) {
        console.error('Error deleting payment:', error);
        showToast('Error de conexión', 'error');
    } finally {
        setBtnLoading(false, 'btn-confirm-delete', 'delete-spinner', 'delete-icon');
        deletePaymentId = null;
    }
}

// ── Helpers ───────────────────────────────────────────────
function clearPaymentErrors() {
    document.querySelectorAll('#payment-form .invalid-feedback').forEach(el => {
        if (el) el.textContent = '';
    });
    document.querySelectorAll('#payment-form .form-control, #payment-form .form-select').forEach(el => {
        if (el) el.classList.remove('is-invalid');
    });
    const alertDiv = document.getElementById('payment-alert');
    if (alertDiv) alertDiv.classList.add('d-none');
}

function showPaymentFieldErrors(errors) {
    const map = {
        audiologist_id: 'err-audiologist_id',
        payment_date: 'err-payment_date'
    };
    Object.entries(errors).forEach(([field, msgs]) => {
        const errElement = document.getElementById(map[field]);
        if (errElement) {
            errElement.textContent = msgs[0];
            const inputElement = document.getElementById(field);
            if (inputElement) inputElement.classList.add('is-invalid');
        }
    });
}

function showPaymentAlert(msg, type) {
    const alertDiv = document.getElementById('payment-alert');
    if (alertDiv) {
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = msg;
        alertDiv.classList.remove('d-none');
        setTimeout(() => alertDiv.classList.add('d-none'), 5000);
    }
}

function setBtnLoading(loading, btnId = 'btn-save-payment', spinnerId = 'payment-spinner', iconId = 'payment-icon') {
    const btn = document.getElementById(btnId);
    const spinner = document.getElementById(spinnerId);
    const icon = document.getElementById(iconId);
    
    if (btn) btn.disabled = loading;
    if (spinner) spinner.classList.toggle('d-none', !loading);
    if (icon) icon.classList.toggle('d-none', loading);
}

function showToast(msg, type) {
    type = type || 'success';
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const div = document.createElement('div');
    div.className = `toast-item toast-${type}`;
    const icon = type === 'success' ? 'checkbox-circle' : (type === 'error' ? 'error-warning' : 'information-line');
    div.innerHTML = `<i class="ri-${icon}-line fs-16"></i>${escapeHtml(msg)}`;
    toastContainer.appendChild(div);
    
    setTimeout(() => {
        div.style.transition = 'opacity .4s';
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 400);
    }, 3500);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endpush

</x-app-layout>