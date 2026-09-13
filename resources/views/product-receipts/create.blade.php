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
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Factura {{ $productInvoice->number }}</h5></div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Paciente:</strong> {{ $productInvoice->patient->first_name }} {{ $productInvoice->patient->last_name }}</p>
                        <p class="mb-2"><strong>Sucursal:</strong> {{ $productInvoice->branch->name }}</p>
                        <p class="mb-0"><strong>Fecha:</strong> {{ $productInvoice->created_at->format('d/m/Y H:i') }}</p>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>RD$ {{ number_format($productInvoice->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>ITBIS:</span>
                            <strong>RD$ {{ number_format($productInvoice->tax_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descuento:</span>
                            <strong class="text-danger">- RD$ {{ number_format($productInvoice->discount, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-5">TOTAL A PAGAR:</span>
                            <span class="fw-bold fs-4 text-success">RD$ {{ number_format($productInvoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <form action="{{ url('/product-receipts/'.$productInvoice->id) }}" method="POST" id="paymentForm">
                    @csrf
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Método de Pago</h5></div>
                        <div class="card-body">
                            <input type="hidden" id="totalToPay" value="{{ $productInvoice->total }}">

                            {{-- Efectivo --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-money-bill-line text-success me-1"></i>Efectivo
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" step="0.01" name="cash_amount" id="cashAmount"
                                           class="form-control" value="0" min="0" oninput="updatePaid()">
                                </div>
                            </div>

                            {{-- Tarjeta --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ri-bank-card-line text-primary me-1"></i>Tarjeta
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
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">RD$</span>
                                    <input type="number" step="0.01" name="transfer_amount" id="transferAmount"
                                           class="form-control" value="0" min="0" oninput="updatePaid()">
                                </div>
                                <input type="text" name="transfer_reference" class="form-control" 
                                       placeholder="Referencia de transferencia (opcional)">
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Total pagado:</span>
                                <span class="fw-bold fs-4" id="paidAmount">RD$ 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">Cambio:</span>
                                <span class="fw-bold fs-5 text-info" id="changeAmount">RD$ 0.00</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
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
function updatePaid() {
    const total = parseFloat(document.getElementById('totalToPay').value) || 0;
    const cash = parseFloat(document.getElementById('cashAmount').value) || 0;
    const card = parseFloat(document.getElementById('cardAmount').value) || 0;
    const transfer = parseFloat(document.getElementById('transferAmount').value) || 0;

    const paid = cash + card + transfer;
    const change = paid - total;

    document.getElementById('paidAmount').textContent = 'RD$ ' + paid.toFixed(2);
    document.getElementById('changeAmount').textContent = 'RD$ ' + Math.max(0, change).toFixed(2);

    // Habilitar/deshabilitar botón
    document.getElementById('submitBtn').disabled = paid < total - 0.01;
}

// Ejecutar una vez al cargar la página por si los navegadores
// restauran valores de los inputs (por ejemplo al volver con el botón "atrás")
document.addEventListener('DOMContentLoaded', updatePaid);
</script>
@endpush
</x-app-layout>