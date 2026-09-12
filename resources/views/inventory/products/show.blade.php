<x-app-layout>
@section('title', $product->name)

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-box-3-line me-1"></i>{{ $product->name }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                            <li class="breadcrumb-item active">Detalle</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="width:150px;height:150px;margin:0 auto 1rem;border-radius:12px;overflow:hidden;background:#f0f2f7;display:flex;align-items:center;justify-content:center;">
                            @if($product->image)
                                <img src="{{ $product->image_url }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="ri-box-3-line" style="font-size:4rem;color:#cbd5e1;"></i>
                            @endif
                        </div>
                        <h5 class="mb-1">{{ $product->name }}</h5>
                        <p class="text-muted mb-2"><code>{{ $product->code }}</code></p>
                        @if($product->category)
                            <span class="badge" style="background:{{ $product->category->color }};color:#fff;">
                                {{ $product->category->name }}
                            </span>
                        @endif
                        @if($product->is_active)
                            <span class="badge bg-success-subtle text-success">Activo</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                                <i class="ri-edit-line me-1"></i>Editar
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Eliminar producto?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger w-100">
                                    <i class="ri-delete-bin-line me-1"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Información del Producto</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Código de Barras</small>
                                <strong>{{ $product->barcode ?? '—' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Unidad</small>
                                <strong>{{ $product->unit }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Precio de Costo</small>
                                <strong class="text-danger">RD$ {{ number_format($product->cost_price, 2) }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Precio de Venta</small>
                                <strong class="text-success">RD$ {{ number_format($product->sale_price, 2) }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Margen</small>
                                @php 
                                    $margin = $product->sale_price > 0 
                                        ? (($product->sale_price - $product->cost_price) / $product->sale_price) * 100 
                                        : 0;
                                @endphp
                                <strong>{{ number_format($margin, 1) }}%</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Stock Mínimo</small>
                                <strong>{{ $product->min_stock }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Aplica ITBIS</small>
                                <strong>{{ $product->has_tax ? 'Sí' : 'No' }}</strong>
                            </div>
                            @if($product->description)
                                <div class="col-12">
                                    <small class="text-muted d-block">Descripción</small>
                                    <p class="mb-0">{{ $product->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Stock por Sucursal</h5></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sucursal</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Reservado</th>
                                        <th class="text-center">Disponible</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->stocks as $stock)
                                        <tr>
                                            <td>{{ $stock->branch->name }}</td>
                                            <td class="text-center"><strong>{{ $stock->quantity }}</strong></td>
                                            <td class="text-center text-muted">{{ $stock->reserved_quantity }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info-subtle text-info">
                                                    {{ $stock->available_quantity }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($stock->quantity <= $product->min_stock)
                                                    <span class="badge bg-danger-subtle text-danger">
                                                        <i class="ri-alert-line me-1"></i>Stock bajo
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Este producto no tiene stock registrado en ninguna sucursal.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>