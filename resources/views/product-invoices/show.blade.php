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
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-check-line me-1"></i>{{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Acciones --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap">
                    @if($productInvoice->status === 'pendiente' || $productInvoice->status === 'pagada_parcial')
                        <a href="{{ url('/product-receipts/create/'.$productInvoice->id) }}" class="btn btn-success">
                            <i class="ri-money-dollar-circle-line me-1"></i>
                            {{ $productInvoice->status === 'pendiente' ? 'Registrar Pago' : 'Completar Pago' }}
                        </a>
                    @endif

                    @if($productInvoice->canBeCancelled())
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
            {{-- Columna izquierda --}}
            <div class="col-lg-8">
                {{-- Información de la factura --}}
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
                                <span class="badge bg-{{ $productInvoice->status_color }}-subtle text-{{ $productInvoice->status_color }}">
                                    {{ $productInvoice->status_label }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Creado por</small>
                                <strong>{{ $productInvoice->user->name }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Datos Fiscales (NCF) --}}
                @if($productInvoice->with_ncf && $productInvoice->ncf)
                    <div class="card border-primary">
                        <div class="card-header bg-primary-subtle">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-shield-2-line me-1 text-primary"></i>Comprobante Fiscal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">NCF</small>
                                    <strong><code style="font-size:1.05rem;color:#405189;">{{ $productInvoice->ncf }}</code></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tipo de Comprobante</small>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $productInvoice->ncf_type ?? '—')) }}</strong>
                                </div>

                                @if($productInvoice->ncfSequence)
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Secuencia</small>
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ri-bookmark-line me-1"></i>{{ $productInvoice->ncfSequence->name }}
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Rango NCF</small>
                                        <strong style="font-size:.85rem;">
                                            {{ $productInvoice->ncfSequence->prefix }}{{ $productInvoice->ncfSequence->serie }}
                                            {{ (int) $productInvoice->ncfSequence->start_number }} -
                                            {{ (int) $productInvoice->ncfSequence->end_number }}
                                        </strong>
                                    </div>
                                @endif

                                @if($productInvoice->customer_rnc)
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">RNC del Cliente</small>
                                        <strong><code>{{ $productInvoice->customer_rnc }}</code></strong>
                                    </div>
                                @endif
                                @if($productInvoice->customer_business_name)
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Razón Social</small>
                                        <strong>{{ $productInvoice->customer_business_name }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Productos --}}
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
                                        <td>
                                            <div class="fw-semibold">{{ $item->product->name }}</div>
                                            @if($item->product->unit)
                                                <small class="text-muted">{{ $item->product->unit }}</small>
                                            @endif
                                        </td>
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

                {{-- Notas --}}
                @if($productInvoice->notes)
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Notas</h5></div>
                        <div class="card-body">{{ $productInvoice->notes }}</div>
                    </div>
                @endif

                {{-- Historial de pagos --}}
                @if($productInvoice->receipts->count() > 0)
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="ri-history-line me-1"></i>Historial de Pagos
                            </h5>
                            <span class="badge bg-info-subtle text-info">
                                {{ $productInvoice->receipts->count() }} pago(s)
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Recibo</th>
                                            <th>Fecha</th>
                                            <th>Cobrado por</th>
                                            <th>Método</th>
                                            <th class="text-end">Monto</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productInvoice->receipts->sortByDesc('created_at') as $rec)
                                            <tr>
                                                <td><code class="fw-semibold">{{ $rec->number }}</code></td>
                                                <td>{{ $rec->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $rec->user->name }}</td>
                                                <td>
                                                    <small class="text-muted">{{ $rec->payment_summary }}</small>
                                                </td>
                                                <td class="text-end fw-semibold text-success">
                                                    RD$ {{ number_format($rec->total_paid, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="{{ url('/product-receipts/'.$rec->id) }}"
                                                           class="btn btn-sm btn-info" title="Ver recibo">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <a href="{{ url('/product-receipts/'.$rec->id.'/print') }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-secondary" title="Imprimir">
                                                            <i class="ri-printer-line"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Columna derecha --}}
            <div class="col-lg-4">
                {{-- Resumen financiero --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Resumen</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong>RD$ {{ number_format($productInvoice->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ITBIS:</span>
                            <strong>RD$ {{ number_format($productInvoice->tax_amount, 2) }}</strong>
                        </div>
                        @if($productInvoice->discount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Descuento:</span>
                                <strong class="text-danger">- RD$ {{ number_format($productInvoice->discount, 2) }}</strong>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold fs-6">TOTAL:</span>
                            <span class="fw-bold fs-4 text-success">RD$ {{ number_format($productInvoice->total, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Pagado:</span>
                            <strong class="text-success">RD$ {{ number_format($productInvoice->paid_amount, 2) }}</strong>
                        </div>

                        <div class="p-3 rounded mt-3"
                             style="background:{{ $productInvoice->balance > 0.01 ? '#fff8e1' : '#e8f8f0' }};border:1px solid {{ $productInvoice->balance > 0.01 ? '#f7b84b' : '#0ab39c' }};">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">BALANCE:</span>
                                <span class="fw-bold fs-4 {{ $productInvoice->balance > 0.01 ? 'text-danger' : 'text-success' }}">
                                    RD$ {{ number_format($productInvoice->balance, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estado del pago --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-money-dollar-circle-line me-1"></i>Estado del Pago
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($productInvoice->status === 'pagada')
                            <div class="alert alert-success mb-0 text-center">
                                <i class="ri-checkbox-circle-line fs-3 d-block mb-2"></i>
                                <strong>Factura Pagada Completa</strong>
                            </div>
                        @elseif($productInvoice->status === 'pagada_parcial')
                            <div class="alert alert-warning mb-3 text-center">
                                <i class="ri-time-line fs-3 d-block mb-2"></i>
                                <strong>Pago Parcial</strong>
                                <p class="mb-0 small mt-1">Faltan <strong>RD$ {{ number_format($productInvoice->balance, 2) }}</strong> por pagar</p>
                            </div>
                            <div class="d-grid">
                                <a href="{{ url('/product-receipts/create/'.$productInvoice->id) }}" class="btn btn-success">
                                    <i class="ri-add-circle-line me-1"></i>Completar Pago
                                </a>
                            </div>
                        @elseif($productInvoice->status === 'pendiente')
                            <div class="alert alert-info mb-3 text-center">
                                <i class="ri-information-line fs-3 d-block mb-2"></i>
                                <strong>Sin Pago</strong>
                                <p class="mb-0 small mt-1">Total pendiente: <strong>RD$ {{ number_format($productInvoice->balance, 2) }}</strong></p>
                            </div>
                            <div class="d-grid">
                                <a href="{{ url('/product-receipts/create/'.$productInvoice->id) }}" class="btn btn-success">
                                    <i class="ri-money-dollar-circle-line me-1"></i>Registrar Pago
                                </a>
                            </div>
                        @elseif($productInvoice->status === 'cancelada')
                            <div class="alert alert-danger mb-0 text-center">
                                <i class="ri-close-circle-line fs-3 d-block mb-2"></i>
                                <strong>Factura Cancelada</strong>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info adicional --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Info Adicional</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Items:</small>
                            <strong>{{ $productInvoice->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Creado por:</small>
                            <strong>{{ $productInvoice->user->name }}</strong>
                        </div>
                        @if($productInvoice->receipts->count() > 0)
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Último pago:</small>
                                <strong>{{ $productInvoice->receipts->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>