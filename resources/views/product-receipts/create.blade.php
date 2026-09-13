<x-app-layout>
@section('title', 'Registrar Pago')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-money-dollar-circle-line me-1"></i>Registrar Pago</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/product-invoices') }}">Facturas</a></li>
                            <li class="breadcrumb-item active">Pago</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Columna izquierda: Resumen de la factura --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Factura {{ $productInvoice->number }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Paciente:</strong> {{ $productInvoice->patient->first_name }} {{ $productInvoice->patient->last_name }}</p>
                        <p class="mb-2"><strong>Sucursal:</strong> {{ $productInvoice->branch->name }}</p>
                        <p class="mb-0"><strong>Fecha:</strong> {{ $productInvoice->created_at->format('d/m/Y H:i') }}</p>
                        
                        <hr>

                        {{-- Productos --}}
                        <h6 class="mb-2">Productos:</h6>
                        <table class="table table-sm">
                            <tbody>
                                @foreach($productInvoice->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product->name }}
                                            <small class="text-muted d-block">x {{ $item->quantity }}</small>
                                        </td>
                                        <td class="text-end">RD$ {{ number_format($item->total_with_tax, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr>

                        {{-- Resumen financiero --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>RD$ {{ number_format($productInvoice->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>ITBIS:</span>
                            <strong>RD$ {{ number_format($productInvoice->tax_amount, 2) }}</strong>
                        </div>
                        @if($productInvoice->discount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Descuento:</span>
                                <strong class="text-danger">- RD$ {{ number_format($productInvoice->discount, 2) }}</strong>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">TOTAL FACTURA:</span>
                            <span class="fw-bold fs-5">RD$ {{ number_format($productInvoice->total, 2) }}</span>
                        </div>

                        @if($productInvoice->paid_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success">Pagado anteriormente:</span>
                                <strong class="text-success">RD$ {{ number_format($productInvoice->paid_amount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between p-2 rounded" style="background:#fff8e1;">
                            <span class="fw-bold">BALANCE PENDIENTE:</span>
                            <span class="fw-bold fs-5 text-danger">RD$ {{ number_format($productInvoice->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: Formulario de pago --}}
            <div class="col-lg-7">
                <form action="{{ url('/product-receipts/'.$productInvoice->id) }}" method="POST" id="paymentForm">
                    @csrf

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Método de Pago</h5>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="totalToPay" value="{{ $productInvoice->balance }}">

                            {{-- Balance destacado --}}
                            <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                                <span><i class="ri-information-line me-1"></i>Balance pendiente:</span>
                                <strong class="fs-4">RD$ {{ number_format($productInvoice->balance, 2) }}</strong>
                            </div>

                            {{-- Efectivo --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-money-bill-line text-success me-1"></i>Efectivo
                                    <small class="text-muted ms-1">(puede exceder el balance, se calcula cambio)</small>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" step="0.01" name="cash_amount" id="cashAmount"
                                           class="form-control" value="0" min="0" oninput="updatePaid()">
                                </div>
                                <small class="text-muted">
                                    <i class="ri-information-line"></i> 
                                    Si el paciente paga con más dinero, el sistema calcula el cambio automáticamente.
                                </small>
                            </div>

                            {{-- Tarjeta --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-bank-card-line text-primary me-1"></i>Tarjeta
                                    <small class="text-muted ms-1">(no puede exceder el balance)</small>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" step="0.01" name="card_amount" id="cardAmount"
                                           class="form-control" value="0" min="0" oninput="updatePaid()">
                                </div>
                                <input type="text" name="card_reference" class="form-control" 
                                       placeholder="Referencia de tarjeta (opcional)">
                            </div>

                            {{-- Transferencia --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-exchange-dollar-line text-info me-1"></i>Transferencia
                                    <small class="text-muted ms-1">(no puede exceder el balance)</small>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" step="0.01" name="transfer_amount" id="transferAmount"
                                           class="form-control" value="0" min="0" oninput="updatePaid()">
                                </div>
                                <input type="text" name="transfer_reference" class="form-control" 
                                       placeholder="Referencia de transferencia (opcional)">
                            </div>

                            {{-- Botones rápidos --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Montos rápidos (en efectivo)</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillCash('full')">
                                        Balance completo
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillCash('half')">
                                        50%
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillCash('quarter')">
                                        25%
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAmounts()">
                                        Limpiar
                                    </button>
                                </div>
                            </div>

                            <hr>

                            {{-- Resumen del pago --}}
                            <div class="p-3 rounded mb-3" style="background:#f0f4ff;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Monto recibido:</span>
                                    <strong id="receivedAmount" class="text-muted">RD$ 0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Aplicado a la factura:</span>
                                    <span class="fw-bold fs-4" id="appliedAmount">RD$ 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Balance restante:</span>
                                    <span class="fw-bold fs-5" id="remainingBalance">RD$ {{ number_format($productInvoice->balance, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between" id="changeRow" style="display:none!important;">
                                    <span class="fw-bold text-info">Cambio:</span>
                                    <span class="fw-bold fs-5 text-info" id="changeAmount">RD$ 0.00</span>
                                </div>
                            </div>

                            <div id="paymentError" class="alert alert-danger d-none">
                                <i class="ri-error-warning-line me-1"></i>
                                <span id="paymentErrorText"></span>
                            </div>

                            <div id="paymentInfo" class="alert alert-info d-none">
                                <i class="ri-information-line me-1"></i>
                                <span id="paymentInfoText"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Observaciones del pago..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                    <i class="ri-save-line me-1"></i>Registrar Pago
                                </button>
                                <a href="{{ url('/product-invoices/'.$productInvoice->id) }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const BALANCE = parseFloat(document.getElementById('totalToPay').value) || 0;

    function updatePaid() {
        const cash = parseFloat(document.getElementById('cashAmount').value) || 0;
        const card = parseFloat(document.getElementById('cardAmount').value) || 0;
        const transfer = parseFloat(document.getElementById('transferAmount').value) || 0;

        // Total recibido del paciente
        const received = cash + card + transfer;

        // 🔑 REGLA CLAVE:
        // - El monto aplicado a la factura = min(recibido, balance)
        // - El cambio solo puede generarse por efectivo excedente
        // - Tarjeta y transferencia NUNCA pueden exceder el balance

        // Validación 1: Tarjeta + Transferencia no pueden exceder el balance
        const nonCash = card + transfer;
        const nonCashExceeds = nonCash > BALANCE + 0.01;

        // Validación 2: El monto aplicado nunca puede ser mayor al balance
        const applied = Math.min(received, BALANCE);

        // Cálculo del cambio:
        // Si lo que pagó con efectivo + otros métodos cubre el balance,
        // el excedente SOLO puede venir del efectivo
        let change = 0;
        if (!nonCashExceeds) {
            // Cuánto se necesita en efectivo para cubrir el balance
            const cashNeeded = Math.max(0, BALANCE - nonCash);
            // Si el efectivo ingresado supera lo que se necesita, hay cambio
            if (cash > cashNeeded) {
                change = cash - cashNeeded;
            }
        }

        const remaining = Math.max(0, BALANCE - applied);

        // Actualizar UI
        document.getElementById('receivedAmount').textContent = 'RD$ ' + received.toFixed(2);
        document.getElementById('appliedAmount').textContent = 'RD$ ' + applied.toFixed(2);
        document.getElementById('remainingBalance').textContent = 'RD$ ' + remaining.toFixed(2);
        document.getElementById('changeAmount').textContent = 'RD$ ' + change.toFixed(2);

        // Mostrar/ocultar fila de cambio
        const changeRow = document.getElementById('changeRow');
        if (change > 0.009) {
            changeRow.style.display = 'flex';
        } else {
            changeRow.style.display = 'none';
        }

        const errorBox = document.getElementById('paymentError');
        const errorText = document.getElementById('paymentErrorText');
        const infoBox = document.getElementById('paymentInfo');
        const infoText = document.getElementById('paymentInfoText');
        const submitBtn = document.getElementById('submitBtn');

        // Resetear
        errorBox.classList.add('d-none');
        infoBox.classList.add('d-none');

        // Validaciones
        if (nonCashExceeds) {
            errorBox.classList.remove('d-none');
            errorText.textContent = 'Tarjeta + Transferencia (RD$ ' + nonCash.toFixed(2) + ') no pueden exceder el balance pendiente (RD$ ' + BALANCE.toFixed(2) + ').';
            submitBtn.disabled = true;
            return;
        }

        if (received <= 0) {
            submitBtn.disabled = true;
            return;
        }

        // Todo bien
        submitBtn.disabled = false;

        if (change > 0.009) {
            infoBox.classList.remove('d-none');
            infoText.textContent = 'El paciente pagó con excedente. Se entregará un cambio de RD$ ' + change.toFixed(2) + '.';
        }
    }

    function fillCash(mode) {
        let amount = 0;
        if (mode === 'full') amount = BALANCE;
        else if (mode === 'half') amount = BALANCE / 2;
        else if (mode === 'quarter') amount = BALANCE / 4;

        document.getElementById('cashAmount').value = amount.toFixed(2);
        document.getElementById('cardAmount').value = 0;
        document.getElementById('transferAmount').value = 0;
        updatePaid();
    }

    function clearAmounts() {
        document.getElementById('cashAmount').value = 0;
        document.getElementById('cardAmount').value = 0;
        document.getElementById('transferAmount').value = 0;
        updatePaid();
    }

    // Inicializar
    updatePaid();
</script>
@endpush
</x-app-layout>