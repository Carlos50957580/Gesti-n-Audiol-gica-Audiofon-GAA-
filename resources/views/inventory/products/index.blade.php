<x-app-layout>
@section('title', 'Productos')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-box-3-line me-1"></i>Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Productos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Nombre, código o código de barras">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="ri-refresh-line me-1"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Catálogo de Productos</h5>
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i>Nuevo Producto
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Código</th>
                                <th>Categoría</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Precio Venta</th>
                                <th class="text-center">Stock Total</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:40px;height:40px;border-radius:8px;overflow:hidden;background:#f0f2f7;display:flex;align-items:center;justify-content:center;">
                                                @if($product->image)
                                                    <img src="{{ $product->image_url }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                                @else
                                                    <i class="ri-box-3-line text-muted"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $product->name }}</div>
                                                <small class="text-muted">{{ $product->unit }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $product->code }}</code></td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge" style="background:{{ $product->category->color }};color:#fff;">
                                                {{ $product->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">RD$ {{ number_format($product->cost_price, 2) }}</td>
                                    <td class="text-end fw-semibold text-success">RD$ {{ number_format($product->sale_price, 2) }}</td>
                                    <td class="text-center">
                                        @php $totalStock = $product->stocks->sum('quantity'); @endphp
                                        @if($totalStock <= $product->min_stock)
                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="ri-alert-line me-1"></i>{{ $totalStock }}
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">{{ $totalStock }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success-subtle text-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info" title="Ver">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" 
                                                  class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="ri-box-3-line fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted mb-0">No hay productos registrados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>