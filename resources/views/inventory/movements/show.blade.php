<x-app-layout>
@section('title', 'Movimiento ' . $stockMovement->reference)

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-exchange-line me-1"></i>Movimiento {{ $stockMovement->reference }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('stock-movements.index') }}">Movimientos</a></li>
                            <li class="breadcrumb-item active">{{ $stockMovement->reference }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Acciones --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap">
                    @if($stockMovement->isDraft())
                        <form action="{{ route('stock-movements.confirm', $stockMovement) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success" onclick="return confirm('¿Confirmar movimiento? Se aplicarán los cambios al stock.')">
                                <i class="ri-check-double-line me-1"></i>Confirmar Movimiento
                            </button>
                        </form>
                    @endif
                    @if($stockMovement->status !== 'cancelado')
                        <form action="{{ route('stock-movements.cancel', $stockMovement) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-danger" onclick="return confirm('¿Cancelar movimiento?')">
                                <i class="ri-close-circle-line me-1"></i>Cancelar
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Datos del movimiento --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Información del Movimiento</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Referencia</small>
                                <code class="fs-5">{{ $stockMovement->reference }}</code>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Tipo</small>
                                <span class="badge bg-primary-subtle text-primary">{{ $stockMovement->type_label }}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Sucursal</small>
                                <strong>{{ $stockMovement->branch->name }}</strong>
                            </div>
                            @if($stockMovement->destinationBranch)
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Sucursal Destino</small>
                                    <strong>{{ $stockMovement->destinationBranch->name }}</strong>
                                </div>
                            @endif
                            @if($stockMovement->supplier)
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Proveedor</small>
                                    <strong>{{ $stockMovement->supplier->name }}</strong>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <small class="text-muted d-block">Fecha</small>
                                <strong>{{ $stockMovement->movement_date->format('d/m/Y') }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Registrado por</small>
                                <strong>{{ $stockMovement->user->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Estado</small>
                                @php
                                    $statusColors = ['borrador' => 'warning', 'confirmado' => 'success', 'cancelado' => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$stockMovement->status] }}-subtle text-{{ $statusColors[$stockMovement->status] }}">
                                    {{ $stockMovement->status_label }}
                                </span>
                            </div>
                            @if($stockMovement->notes)
                                <div class="col-12">
                                    <small class="text-muted d-block">Notas</small>
                                    <p class="mb-0">{{ $stockMovement->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Productos</h5></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center">Stock Anterior</th>
                                        <th class="text-center">Stock Nuevo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockMovement->items as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $item->product->name }}</div>
                                                <small class="text-muted">{{ $item->product->code }}</small>
                                            </td>
                                            <td class="text-center"><strong>{{ $item->quantity }}</strong></td>
                                            <td class="text-end">RD$ {{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-end fw-semibold">RD$ {{ number_format($item->subtotal, 2) }}</td>
                                            <td class="text-center text-muted">{{ $item->previous_stock }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info-subtle text-info">{{ $item->new_stock }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if($stockMovement->total > 0)
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">TOTAL</td>
                                            <td class="text-end fw-bold fs-5 text-success">
                                                RD$ {{ number_format($stockMovement->total, 2) }}
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Resumen --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Resumen</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total de items:</span>
                            <strong>{{ $stockMovement->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total unidades:</span>
                            <strong>{{ $stockMovement->items->sum('quantity') }}</strong>
                        </div>
                        @if($stockMovement->total > 0)
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total del movimiento:</span>
                                <strong class="text-success fs-5">RD$ {{ number_format($stockMovement->total, 2) }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>