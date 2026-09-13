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

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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

                {{-- Estado de la factura --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Estado de la Factura</h5></div>
                    <div class="card-body">
                        @php $invoice = $productReceipt->invoice; @endphp
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background:#f0f4ff;">
                                    <small class="text-muted d-block">Total Factura</small>
                                    <strong class="fs-4">RD$ {{ number_format($invoice->total, 2) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background:#e8f8f0;">
                                    <small class="text-muted d-block">Total Pagado</small>
                                    <strong class="fs-4 text-success">RD$ {{ number_format($invoice->paid_amount, 2) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded text-center" style="background:{{ $invoice->balance > 0 ? '#fff8e1' : '#e8f8f0' }};">
                                    <small class="text-muted d-block">Balance Pendiente</small>
                                    <strong class="fs-4 {{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                                        RD$ {{ number_format($invoice->balance, 2) }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="text-center mt-3">
                            @php
                                $statusColors = ['pendiente' => 'warning', 'pagada_parcial' => 'info', 'pagada' => 'success', 'cancelada' => 'danger'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$invoice->status] }}-subtle text-{{ $statusColors[$invoice->status] }} fs-6 px-3 py-2">
                                {{ $invoice->status_label }}
                            </span>
                        </div>

                        @if($invoice->balance > 0)
                            <div class="d-grid mt-3">
                                <a href="{{ url('/product-receipts/create/'.$invoice->id) }}" class="btn btn-primary">
                                    <i class="ri-add-circle-line me-1"></i>Registrar Otro Pago
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Historial de pagos --}}
                @if($invoice->receipts->count() > 1)
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Historial de Pagos</h5></div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Recibo</th>
                                        <th>Fecha</th>
                                        <th class="text-end">Monto</th>
                                        <th>Cobrado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->receipts as $rec)
                                        <tr>
                                            <td><code>{{ $rec->number }}</code></td>
                                            <td>{{ $rec->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-end fw-semibold">RD$ {{ number_format($rec->total_paid, 2) }}</td>
                                            <td>{{ $rec->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Detalle de Este Pago</h5></div>
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
                            <span class="fw-bold fs-5">PAGADO:</span>
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