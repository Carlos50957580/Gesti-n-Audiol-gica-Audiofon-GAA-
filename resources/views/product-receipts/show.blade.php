<x-app-layout>
@section('title', 'Recibo ' . $productReceipt->number)

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-receipt-line me-1"></i>Recibo {{ $productReceipt->number }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/product-receipts') }}">Pagos</a></li>
                            <li class="breadcrumb-item active">{{ $productReceipt->number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Datos del Pago</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Recibo</small>
                                <strong><code>{{ $productReceipt->number }}</code></strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Fecha</small>
                                <strong>{{ $productReceipt->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Factura</small>
                                <a href="{{ url('/product-invoices/'.$productReceipt->invoice->id) }}">
                                    <code>{{ $productReceipt->invoice->number }}</code>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Sucursal</small>
                                <strong>{{ $productReceipt->branch->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Paciente</small>
                                <strong>{{ $productReceipt->invoice->patient->first_name }} {{ $productReceipt->invoice->patient->last_name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Cobrado por</small>
                                <strong>{{ $productReceipt->user->name }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Detalle de Pago</h5></div>
                    <div class="card-body">
                        @if($productReceipt->cash_amount)
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="ri-money-bill-line text-success me-1"></i>Efectivo:</span>
                                <strong>RD$ {{ number_format($productReceipt->cash_amount, 2) }}</strong>
                            </div>
                        @endif
                        @if($productReceipt->card_amount)
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="ri-bank-card-line text-primary me-1"></i>Tarjeta:</span>
                                <strong>RD$ {{ number_format($productReceipt->card_amount, 2) }}</strong>
                            </div>
                        @endif
                        @if($productReceipt->transfer_amount)
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="ri-exchange-dollar-line text-info me-1"></i>Transferencia:</span>
                                <strong>RD$ {{ number_format($productReceipt->transfer_amount, 2) }}</strong>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-5">TOTAL:</span>
                            <span class="fw-bold fs-4 text-success">RD$ {{ number_format($productReceipt->total_paid, 2) }}</span>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ url('/product-receipts/'.$productReceipt->id.'/print') }}" target="_blank" class="btn btn-primary">
                                <i class="ri-printer-line me-1"></i>Imprimir
                            </a>
                            <a href="{{ url('/product-receipts') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>