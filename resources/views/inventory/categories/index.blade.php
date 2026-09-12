<x-app-layout>
@section('title', 'Categorías de Productos')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-price-tag-3-line me-1"></i>Categorías de Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Categorías</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista de Categorías</h5>
                <a href="{{ route('product-categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i>Nueva Categoría
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Productos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:8px;background:{{ $cat->color }};display:flex;align-items:center;justify-content:center;color:#fff;">
                                                <i class="{{ $cat->icon }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $cat->name }}</div>
                                                @if($cat->description)
                                                    <small class="text-muted">{{ Str::limit($cat->description, 40) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $cat->code ?? '—' }}</code></td>
                                    <td><span class="badge bg-info">{{ $cat->products_count }}</span></td>
                                    <td>
                                        @if($cat->is_active)
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-secondary">Inactiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('product-categories.edit', $cat) }}" class="btn btn-sm btn-warning">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <form action="{{ route('product-categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar categoría?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No hay categorías registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
</x-app-layout>