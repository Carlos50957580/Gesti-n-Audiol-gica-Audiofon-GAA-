<x-app-layout>
@section('title', 'Factura ' . $productInvoice->number)

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-file-text-line me-1"></i>Factura {{ $productInvoice->number }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/product-invoices') }}">Facturas de Productos</a></li>
                            <li class="breadcrumb-item active">{{ $productInvoice->number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Acciones --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap">
                    @if($productInvoice->status === 'pendiente')
                        <a href="{{ url('/product-receipts/create/'.$productInvoice->id) }}" class="btn btn-success">
                            <i class="ri-money-dollar-circle-line me-1"></i>Registrar Pago
                        </a>
                        <form action="{{ url('/product-invoices/'.$productInvoice->id.'/cancel') }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Cancelar esta factura?')">
                            @csrf
                            <button class="btn btn-danger">
                                <i class="ri-close-circle-line me-1"></i>Cancelar
                            </button>
                        </form>
                    @endif
                    <a href="{{ url('/product-invoices/'.$productInvoice->id.'/print') }}" target="_blank" class="btn btn-primary">
                        <i class="ri-printer-line me-1"></i>Imprimir
                    </a>
                    <a href="{{ url('/product-invoices') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Información de la Factura</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Número</small>
                                <strong><code>{{ $productInvoice->number }}</code></strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Fecha</small>
                                <strong>{{ $productInvoice->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Paciente</small>
                                <strong>{{ $productInvoice->patient->first_name }} {{ $productInvoice->patient->last_name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Cédula</small>
                                <strong>{{ $productInvoice->patient->cedula ?? '—' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Sucursal</small>
                                <strong>{{ $productInvoice->branch->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Estado</small>
                                @php
                                    $colors = ['pendiente' => 'warning', 'pagada' => 'success', 'cancelada' => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $colors[$productInvoice->status] }}-subtle text-{{ $colors[$productInvoice->status] }}">
                                    {{ $productInvoice->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Productos</h5></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-end">ITBIS</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productInvoice->items as $item)
                                    <tr>
                                        <td><code>{{ $item->product->code }}</code></td>
                                        <td>{{ $item->product->name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">RD$ {{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">RD$ {{ number_format($item->subtotal, 2) }}</td>
                                        <td class="text-end">RD$ {{ number_format($item->tax_amount, 2) }}</td>
                                        <td class="text-end fw-semibold">RD$ {{ number_format($item->total_with_tax, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($productInvoice->notes)
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Notas</h5></div>
                        <div class="card-body">{{ $productInvoice->notes }}</div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Resumen</h5></div>
                    <div class="card-body">
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
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-5">TOTAL:</span>
                            <span class="fw-bold fs-4 text-success">RD$ {{ number_format($productInvoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($productInvoice->receipt)
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Pago</h5></div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Recibo:</strong> <code>{{ $productInvoice->receipt->number }}</code></p>
                            <p class="mb-2"><strong>Total pagado:</strong> RD$ {{ number_format($productInvoice->receipt->total_paid, 2) }}</p>
                            <p class="mb-0"><small class="text-muted">{{ $productInvoice->receipt->created_at->format('d/m/Y H:i') }}</small></p>
                            <a href="{{ url('/product-receipts/'.$productInvoice->receipt->id) }}" class="btn btn-sm btn-info mt-2 w-100">
                                Ver Recibo
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>