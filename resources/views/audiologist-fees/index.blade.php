{{-- resources/views/audiologist-fees/index.blade.php --}}
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
        .fee-table th {
            font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
            color:#8098bb; border-bottom:2px solid #e9ecef; padding:.85rem 1rem; white-space:nowrap;
        }
        .fee-table td { padding:.82rem 1rem; vertical-align:middle; }
        .fee-table tbody tr { transition:background .15s; border-bottom:1px solid #f3f5f9; }
        .fee-table tbody tr:hover { background:#f8faff; }

        /* ── Status pills ── */
        .status-pill {
            display:inline-flex; align-items:center; gap:.35rem;
            padding:.28rem .7rem; border-radius:2rem; font-size:.75rem; font-weight:600;
        }
        .status-pill .dot { width:7px; height:7px; border-radius:50%; }
        .status-pending { background:#fef3c7; color:#92400e; }
        .status-pending .dot { background:#f59e0b; }
        .status-paid { background:#d1fae5; color:#065f46; }
        .status-paid .dot { background:#10b981; }
        .status-cancelled { background:#fee2e2; color:#991b1b; }
        .status-cancelled .dot { background:#ef4444; }

        /* ── Action buttons ── */
        .btn-action {
            width:32px; height:32px; padding:0; border:none; border-radius:.4rem;
            display:inline-flex; align-items:center; justify-content:center; transition:all .15s;
        }
        .btn-action:hover { transform:scale(1.12); }

        /* ── Search & filters ── */
        #search-input, .filter-select {
            border-radius:2rem; padding-left:2.4rem;
            border:1.5px solid #e2e8f0; font-size:.9rem;
            transition:border-color .2s,box-shadow .2s;
        }
        #search-input:focus, .filter-select:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.12); }
        .search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#8098bb; pointer-events:none; }

        /* ── Modal headers ── */
        .mh-primary { background:linear-gradient(135deg,#405189,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-info { background:linear-gradient(135deg,#299cdb,#0ab39c); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-danger { background:linear-gradient(135deg,#e74c3c,#c0392b); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-warning { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; border-radius:.5rem .5rem 0 0; }
        .mh-primary .btn-close, .mh-info .btn-close, .mh-danger .btn-close, .mh-warning .btn-close { filter:invert(1); }

        /* ── Form fields ── */
        .form-floating>.form-control,
        .form-floating>.form-select { border:1.5px solid #e2e8f0; border-radius:.5rem; }
        .form-floating>.form-control:focus,
        .form-floating>.form-select:focus { border-color:#405189; box-shadow:0 0 0 3px rgba(64,81,137,.1); }
        .section-label {
            font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
            color:#8098bb; border-bottom:1px solid #f0f2f7; padding-bottom:.4rem; margin-bottom:.9rem;
        }

        /* ── Detail rows ── */
        .detail-row {
            display:flex; gap:.75rem; align-items:flex-start;
            padding:.7rem 0; border-bottom:1px solid #f3f5f9;
        }
        .detail-row:last-child { border-bottom:none; }
        .detail-icon { width:36px; height:36px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem; }
        .detail-lbl { font-size:.7rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#8098bb; }
        .detail-val { font-size:.91rem; font-weight:500; color:#344563; margin-top:.1rem; }

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
    </style>

    <div id="toast-container"></div>

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Honorarios</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Honorarios</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-money-dollar-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Total Honorarios</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">RD$ {{ number_format($stats['total_fees'], 2) }}</div>
                    </div>
                    <div class="stat-bg text-primary"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-time-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Pendientes</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">RD$ {{ number_format($stats['total_pending'], 2) }}</div>
                    </div>
                    <div class="stat-bg text-warning"><i class="ri-time-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-checkbox-circle-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Pagados</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">RD$ {{ number_format($stats['total_paid'], 2) }}</div>
                    </div>
                    <div class="stat-bg text-success"><i class="ri-checkbox-circle-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="stat-icon bg-info-subtle text-info"><i class="ri-user-star-line"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">Medicos</div>
                        <div class="fw-bold fs-4 lh-1 mt-1">{{ $stats['audiologists_count'] }}</div>
                    </div>
                    <div class="stat-bg text-info"><i class="ri-user-star-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main table card --}}
    <div class="card shadow-sm" style="border-radius:.75rem;border:none;">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap py-3" style="border-bottom:1px solid #f0f2f7;">
            <h5 class="card-title mb-0 flex-grow-1">Listado de Honorarios</h5>
            <div class="position-relative" style="width:230px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="search-input" class="form-control" placeholder="Buscar...">
            </div>
            <select id="audiologist-filter" class="form-select filter-select" style="width:180px;">
                <option value="">Todos los Medicos</option>
                @foreach($audiologists as $audiologist)
                    <option value="{{ $audiologist->id }}">{{ $audiologist->name }}</option>
                @endforeach
            </select>
            <select id="status-filter" class="form-select filter-select" style="width:140px;">
                <option value="">Todos</option>
                <option value="pending">Pendientes</option>
                <option value="paid">Pagados</option>
                <option value="cancelled">Cancelados</option>
            </select>
            <input type="date" id="date-from" class="form-control filter-select" style="width:150px;" placeholder="Desde">
            <input type="date" id="date-to" class="form-control filter-select" style="width:150px;" placeholder="Hasta">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table fee-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Factura</th>
                            <th>Medicos</th>
                            <th>Total Factura</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Honorario</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="fees-tbody">
                        @forelse($fees as $fee)
                        <tr class="anim-row"
                            data-id="{{ $fee->id }}"
                            data-audiologist="{{ strtolower($fee->audiologist->name) }}"
                            data-status="{{ $fee->status }}">
                            <td>#{{ $fee->id }}</td>
                            <td>
                                <span class="fw-semibold">
                                    FAC-{{ str_pad($fee->invoice_id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>{{ $fee->audiologist->name }}</td>
                            <td>RD$ {{ number_format($fee->invoice_total, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $fee->calculation_type === 'percentage' ? 'info' : 'primary' }}">
                                    {{ $fee->calculation_type === 'percentage' ? 'Porcentaje' : 'Monto Fijo' }}
                                </span>
                            </td>
                            <td>
                                {{ $fee->calculation_type === 'percentage' ? $fee->calculation_value . '%' : 'RD$ ' . number_format($fee->calculation_value, 2) }}
                            </td>
                            <td>
                                <span class="fw-bold text-primary">
                                    RD$ {{ number_format($fee->fee_amount, 2) }}
                                </span>
                                @if($fee->remaining_amount > 0 && $fee->remaining_amount < $fee->fee_amount)
                                    <br><small class="text-muted">Pagado: RD$ {{ number_format($fee->paid_amount, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'status-pending', 'text' => 'Pendiente', 'icon' => 'ri-time-line'],
                                        'paid' => ['class' => 'status-paid', 'text' => 'Pagado', 'icon' => 'ri-checkbox-circle-line'],
                                        'cancelled' => ['class' => 'status-cancelled', 'text' => 'Cancelado', 'icon' => 'ri-close-circle-line'],
                                    ][$fee->status];
                                @endphp
                                <span class="status-pill {{ $statusConfig['class'] }}">
                                    <span class="dot"></span>
                                    <i class="{{ $statusConfig['icon'] }} fs-12 me-1"></i>
                                    {{ $statusConfig['text'] }}
                                </span>
                            </td>
                            <td>{{ $fee->created_at->format('d/m/Y') }}</td>
                           <td class="text-center">
    <div class="d-flex gap-1 justify-content-center">
        <button type="button" class="btn btn-action bg-info-subtle text-info"
                title="Ver detalle" onclick="openShowModal({{ $fee->id }})">
            <i class="ri-eye-fill fs-13"></i>
        </button>
        @if($fee->status === 'pending')
        <button type="button" class="btn btn-action bg-danger-subtle text-danger"
                title="Eliminar" onclick="openDeleteModal({{ $fee->id }})">
            <i class="ri-delete-bin-fill fs-13"></i>
        </button>
        @endif
    </div>
</td>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                <div class="text-center py-5">
                                    <i class="ri-money-dollar-circle-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                                    <p class="text-muted mb-0">No hay honorarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="no-results" class="text-center py-5 d-none">
                <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
                <p class="text-muted mb-0">No se encontraron resultados con esos criterios.</p>
            </div>

            @if($fees->hasPages())
            <div class="d-flex justify-content-end px-3 py-2" style="border-top:1px solid #f0f2f7;">
                {{ $fees->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- ══════════════════════ MODAL: Ver Detalle (MEJORADO) ══════════════════════ --}}
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:700px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-info py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-money-dollar-circle-line fs-18"></i> Detalle del Honorario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="max-height:70vh; overflow-y:auto;">
                <div class="text-center mb-4" id="show-status-container"></div>
                
                {{-- Información del Honorario --}}
                <div class="section-label mb-2">Información del Honorario</div>
                <div class="detail-row">
                    <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-receipt-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Factura</div>
                        <div class="detail-val fw-semibold" id="show-invoice">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-success-subtle text-success"><i class="ri-user-star-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Audiólogo/Medico</div>
                        <div class="detail-val" id="show-audiologist">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-warning-subtle text-warning"><i class="ri-money-dollar-circle-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Total Factura (Subtotal)</div>
                        <div class="detail-val" id="show-invoice-total">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-info-subtle text-info"><i class="ri-calculator-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Cálculo del Honorario</div>
                        <div class="detail-val" id="show-calculation">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-danger-subtle text-danger"><i class="ri-wallet-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Monto del Honorario</div>
                        <div class="detail-val fs-5 fw-bold text-primary" id="show-amount">—</div>
                    </div>
                </div>
                
                {{-- Información del Paciente --}}
                <div class="section-label mt-3 mb-2">Información del Paciente</div>
                <div class="detail-row">
                    <div class="detail-icon bg-primary-subtle text-primary"><i class="ri-user-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Paciente</div>
                        <div class="detail-val fw-semibold" id="show-patient-name">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-secondary-subtle text-secondary"><i class="ri-id-card-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Cédula</div>
                        <div class="detail-val" id="show-patient-cedula">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-info-subtle text-info"><i class="ri-phone-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Teléfono</div>
                        <div class="detail-val" id="show-patient-phone">—</div>
                    </div>
                </div>
                
                {{-- Servicios Realizados --}}
                <div class="section-label mt-3 mb-2">Servicios Realizados</div>
                <div class="table-responsive">
                    <table class="table table-sm" style="font-size:.85rem;">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Cant.</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th>Seguro</th>
                                <th>Paciente</th>
                            </tr>
                        </thead>
                        <tbody id="show-services-list">
                            <tr><td colspan="6" class="text-center">Cargando...</td></tr>
                        </tbody>
                        <tfoot id="show-services-total" style="display:none;">
                            <tr>
                                <th colspan="3" class="text-end">Totales:</th>
                                <th id="services-subtotal">RD$ 0.00</th>
                                <th id="services-insurance">RD$ 0.00</th>
                                <th id="services-patient">RD$ 0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                {{-- Pagos Aplicados --}}
                <div class="section-label mt-3 mb-2" id="payment-section-label" style="display:none;">Pagos Aplicados</div>
                <div id="payment-details-container" style="display:none;"></div>
                
                {{-- Notas --}}
                <div class="detail-row" id="notes-row" style="display:none;">
                    <div class="detail-icon bg-dark-subtle text-dark"><i class="ri-file-text-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Notas</div>
                        <div class="detail-val" id="show-notes">—</div>
                    </div>
                </div>
                
                {{-- Información Adicional --}}
                <div class="section-label mt-3 mb-2">Información Adicional</div>
                <div class="detail-row">
                    <div class="detail-icon bg-secondary-subtle text-secondary"><i class="ri-store-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Sucursal</div>
                        <div class="detail-val" id="show-branch">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-dark-subtle text-dark"><i class="ri-user-settings-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Creado por</div>
                        <div class="detail-val" id="show-created-by">—</div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon bg-success-subtle text-success"><i class="ri-calendar-line"></i></div>
                    <div class="flex-grow-1">
                        <div class="detail-lbl">Fecha de Registro</div>
                        <div class="detail-val" id="show-created">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cerrar</button>
                @if(auth()->user()->role->name === 'admin')
                <button type="button" class="btn btn-warning btn-sm" onclick="switchToEdit()">
                    <i class="ri-pencil-line me-1"></i> Editar
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════ MODAL: Editar Estado ══════════════════════ --}}
<div class="modal fade" id="editModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-warning py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-pencil-line fs-18"></i> Editar Honorario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="edit-modal-alert" class="alert d-none mb-3"></div>
                <form id="edit-form" novalidate>
                    <input type="hidden" id="edit-fee-id">
                    
                    <p class="section-label"><i class="ri-information-line me-1"></i>Información General</p>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Estado</label>
                            <select id="edit-status" class="form-select">
                                <option value="pending">Pendiente</option>
                                <option value="paid">Pagado</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-12" id="payment-date-group" style="display:none;">
                            <label class="form-label fw-semibold">Fecha de Pago</label>
                            <input type="date" id="edit-payment-date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas</label>
                            <textarea id="edit-notes" class="form-control" rows="3" placeholder="Notas adicionales..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-2"
                        id="btn-update-fee" onclick="updateFee()">
                    <span class="spinner-border spinner-border-sm d-none" id="update-spinner"></span>
                    <i class="ri-save-line" id="update-icon"></i>
                    <span>Actualizar</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════ MODAL: Eliminar ══════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Eliminar Honorario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="stat-icon mx-auto mb-3"
                     style="width:60px;height:60px;font-size:1.5rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">
                    <i class="ri-money-dollar-circle-line"></i>
                </div>
                <p class="mb-2 fs-5 fw-semibold">¿Eliminar este honorario?</p>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Esta acción es <strong>irreversible</strong>. El honorario será eliminado permanentemente.
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
const URL_BASE = "{{ route('audiologist-fees.index') }}".replace(/\/+$/, '');
let showModal, editModal, deleteModal;
let currentFeeId = null;
let deleteFeeId = null;

document.addEventListener('DOMContentLoaded', () => {
    showModal = new bootstrap.Modal(document.getElementById('showModal'));
    editModal = new bootstrap.Modal(document.getElementById('editModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    document.getElementById('edit-status').addEventListener('change', function() {
        const paymentDateGroup = document.getElementById('payment-date-group');
        paymentDateGroup.style.display = this.value === 'paid' ? 'block' : 'none';
        if (this.value === 'paid' && !document.getElementById('edit-payment-date').value) {
            document.getElementById('edit-payment-date').value = new Date().toISOString().split('T')[0];
        }
    });
    
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});

// ── Search & filters ───────────────────────────────────────
document.getElementById('search-input').addEventListener('input', filterTable);
document.getElementById('audiologist-filter').addEventListener('change', filterTable);
document.getElementById('status-filter').addEventListener('change', filterTable);
document.getElementById('date-from').addEventListener('change', filterTable);
document.getElementById('date-to').addEventListener('change', filterTable);

function filterTable() {
    const search = document.getElementById('search-input').value.toLowerCase().trim();
    const audiologistId = document.getElementById('audiologist-filter').value;
    const status = document.getElementById('status-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    
    const rows = document.querySelectorAll('#fees-tbody tr[data-id]');
    let visible = 0;
    
    rows.forEach(tr => {
        let show = true;
        
        if (search && !tr.textContent.toLowerCase().includes(search)) show = false;
        if (audiologistId && !tr.cells[2]?.textContent.includes(document.querySelector('#audiologist-filter option:checked')?.text)) show = false;
        if (status && tr.dataset.status !== status) show = false;
        
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    
    document.getElementById('no-results').classList.toggle('d-none', visible > 0);
}

// ── SHOW DETAIL (MEJORADO) ───────────────────────────────────────────
async function openShowModal(id) {
    currentFeeId = id;
    showModal.show();
    
    try {
        const res = await fetch(`${URL_BASE}/${id}`);
        if (!res.ok) throw new Error();
        const data = await res.json();
        
        // Información del Honorario
        document.getElementById('show-invoice').innerHTML = `FAC-${String(data.invoice.id).padStart(6, '0')}`;
        document.getElementById('show-audiologist').textContent = data.audiologist?.name || '—';
        document.getElementById('show-invoice-total').textContent = `RD$ ${parseFloat(data.invoice_total).toLocaleString('es-DO', {minimumFractionDigits:2})}`;
        document.getElementById('show-calculation').innerHTML = `${data.calculation_type === 'percentage' ? data.calculation_value + '%' : 'RD$ ' + parseFloat(data.calculation_value).toLocaleString('es-DO', {minimumFractionDigits:2})} (${data.calculation_type === 'percentage' ? 'Porcentaje' : 'Monto Fijo'})`;
        document.getElementById('show-amount').innerHTML = `RD$ ${parseFloat(data.fee_amount).toLocaleString('es-DO', {minimumFractionDigits:2})}`;
        document.getElementById('show-created').textContent = new Date(data.created_at).toLocaleDateString('es-DO');
        
        // Información del Paciente
        if (data.patient) {
            document.getElementById('show-patient-name').textContent = `${data.patient.first_name} ${data.patient.last_name}`;
            document.getElementById('show-patient-cedula').textContent = data.patient.cedula || '—';
            document.getElementById('show-patient-phone').textContent = data.patient.phone || '—';
        } else {
            document.getElementById('show-patient-name').textContent = '—';
            document.getElementById('show-patient-cedula').textContent = '—';
            document.getElementById('show-patient-phone').textContent = '—';
        }
        
        // Servicios
        if (data.services && data.services.length > 0) {
            let servicesHtml = '';
            let subtotalTotal = 0;
            let insuranceTotal = 0;
            let patientTotal = 0;
            
            data.services.forEach(service => {
                subtotalTotal += parseFloat(service.subtotal);
                insuranceTotal += parseFloat(service.insurance_amount || 0);
                patientTotal += parseFloat(service.patient_amount || 0);
                
                servicesHtml += `
                    <tr>
                        <td>${service.service_name}</td>
                        <td class="text-center">${service.quantity}</td>
                        <td class="text-end">RD$ ${parseFloat(service.price).toLocaleString('es-DO', {minimumFractionDigits:2})}</td>
                        <td class="text-end">RD$ ${parseFloat(service.subtotal).toLocaleString('es-DO', {minimumFractionDigits:2})}</td>
                        <td class="text-end">RD$ ${parseFloat(service.insurance_amount || 0).toLocaleString('es-DO', {minimumFractionDigits:2})}</td>
                        <td class="text-end">RD$ ${parseFloat(service.patient_amount || 0).toLocaleString('es-DO', {minimumFractionDigits:2})}</td>
                    </tr>
                `;
            });
            
            document.getElementById('show-services-list').innerHTML = servicesHtml;
            document.getElementById('services-subtotal').innerHTML = `RD$ ${subtotalTotal.toLocaleString('es-DO', {minimumFractionDigits:2})}`;
            document.getElementById('services-insurance').innerHTML = `RD$ ${insuranceTotal.toLocaleString('es-DO', {minimumFractionDigits:2})}`;
            document.getElementById('services-patient').innerHTML = `RD$ ${patientTotal.toLocaleString('es-DO', {minimumFractionDigits:2})}`;
            document.getElementById('show-services-total').style.display = 'table-footer-group';
        } else {
            document.getElementById('show-services-list').innerHTML = '<tr><td colspan="6" class="text-center">No hay servicios registrados</td></tr>';
            document.getElementById('show-services-total').style.display = 'none';
        }
        
        // Información de la sucursal
        if (data.branch) {
            document.getElementById('show-branch').textContent = data.branch.name || '—';
        } else {
            document.getElementById('show-branch').textContent = '—';
        }
        
        // Creado por
        if (data.created_by) {
            document.getElementById('show-created-by').textContent = data.created_by.name || '—';
        } else {
            document.getElementById('show-created-by').textContent = '—';
        }
        
        // Notas
        if (data.notes) {
            document.getElementById('show-notes').textContent = data.notes;
            document.getElementById('notes-row').style.display = 'flex';
        } else {
            document.getElementById('notes-row').style.display = 'none';
        }
        
        // Estado
        const statusConfig = {
            pending: { class: 'status-pending', text: 'Pendiente', icon: 'ri-time-line' },
            paid: { class: 'status-paid', text: 'Pagado', icon: 'ri-checkbox-circle-line' },
            cancelled: { class: 'status-cancelled', text: 'Cancelado', icon: 'ri-close-circle-line' }
        }[data.status];
        
        document.getElementById('show-status-container').innerHTML = `
            <span class="status-pill ${statusConfig.class}" style="font-size:.9rem;padding:.5rem 1rem;">
                <span class="dot"></span>
                <i class="${statusConfig.icon} fs-14 me-1"></i>
                ${statusConfig.text}
            </span>
        `;
        
        // Pagos Aplicados
        if (data.payments && data.payments.length > 0) {
            let paymentsHtml = '';
            data.payments.forEach(p => {
                paymentsHtml += `
                    <div class="detail-row">
                        <div class="detail-icon bg-success-subtle text-success"><i class="ri-bank-card-line"></i></div>
                        <div class="flex-grow-1">
                            <div class="detail-lbl">Pago #${p.id}</div>
                            <div class="detail-val">
                                RD$ ${parseFloat(p.pivot.amount_applied).toLocaleString('es-DO', {minimumFractionDigits:2})}
                                <small class="text-muted"> - ${new Date(p.payment_date).toLocaleDateString('es-DO')}</small>
                                ${p.payment_method ? `<br><small>Método: ${p.payment_method}</small>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('payment-details-container').innerHTML = paymentsHtml;
            document.getElementById('payment-details-container').style.display = 'block';
            document.getElementById('payment-section-label').style.display = 'block';
        } else {
            document.getElementById('payment-details-container').style.display = 'none';
            document.getElementById('payment-section-label').style.display = 'none';
        }
        
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al cargar los datos.', 'error');
        showModal.hide();
    }
}

function switchToEdit() {
    showModal.hide();
    setTimeout(() => openEditModal(currentFeeId), 300);
}

// ── EDIT ──────────────────────────────────────────────────
async function openEditModal(id) {
    currentFeeId = id;
    
    try {
        const res = await fetch(`${URL_BASE}/${id}`);
        if (!res.ok) throw new Error();
        const fee = await res.json();
        
        document.getElementById('edit-fee-id').value = fee.id;
        document.getElementById('edit-status').value = fee.status;
        document.getElementById('edit-payment-date').value = fee.payment_date || '';
        document.getElementById('edit-notes').value = fee.notes || '';
        
        const paymentDateGroup = document.getElementById('payment-date-group');
        paymentDateGroup.style.display = fee.status === 'paid' ? 'block' : 'none';
        
        editModal.show();
    } catch {
        showToast('Error al cargar los datos.', 'error');
    }
}

async function updateFee() {
    const id = document.getElementById('edit-fee-id').value;
    const status = document.getElementById('edit-status').value;
    const paymentDate = document.getElementById('edit-payment-date').value;
    const notes = document.getElementById('edit-notes').value;
    
    setBtnLoading('btn-update-fee', 'update-spinner', 'update-icon', true);
    
    try {
        const res = await fetch(`${URL_BASE}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({
                status: status,
                payment_date: status === 'paid' ? paymentDate : null,
                notes: notes
            })
        });
        
        const data = await res.json();
        
        if (!res.ok) {
            showToast(data.message || 'Error al actualizar.', 'error');
            return;
        }
        
        editModal.hide();
        showToast(data.message || 'Honorario actualizado correctamente.', 'success');
        setTimeout(() => location.reload(), 800);
        
    } catch {
        showToast('Error de conexión.', 'error');
    } finally {
        setBtnLoading('btn-update-fee', 'update-spinner', 'update-icon', false);
    }
}

// ── DELETE ────────────────────────────────────────────────
function openDeleteModal(id) {
    deleteFeeId = id;
    deleteModal.show();
}

async function confirmDelete() {
    setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', true);
    
    try {
        const res = await fetch(`${URL_BASE}/${deleteFeeId}`, {
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
            showToast(data.message || 'Error al eliminar.', 'error');
            return;
        }
        
        showToast(data.message || 'Honorario eliminado correctamente.', 'success');
        
        const row = document.querySelector(`#fees-tbody tr[data-id="${deleteFeeId}"]`);
        if (row) {
            row.style.transition = 'opacity .3s,transform .3s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(30px)';
            setTimeout(() => row.remove(), 300);
        }
    } catch {
        showToast('Error de conexión.', 'error');
    } finally {
        setBtnLoading('btn-confirm-delete', 'delete-spinner', 'delete-icon', false);
    }
}

// ── Helpers ───────────────────────────────────────────────
function setBtnLoading(btnId, spinnerId, iconId, loading) {
    document.getElementById(btnId).disabled = loading;
    document.getElementById(spinnerId).classList.toggle('d-none', !loading);
    document.getElementById(iconId).classList.toggle('d-none', loading);
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