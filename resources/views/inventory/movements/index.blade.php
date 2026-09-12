<x-app-layout>
@section('title', 'Movimientos de Stock')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-exchange-line me-1"></i>Movimientos de Stock</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Movimientos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('stock-movements.entry.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-line me-1"></i>Registrar Entrada
                    </a>
                    <a href="{{ route('stock-movements.exit.create') }}" class="btn btn-warning">
                        <i class="ri-indeterminate-circle-line me-1"></i>Registrar Salida
                    </a>
                    <a href="{{ route('stock-movements.transfer.create') }}" class="btn btn-info">
                        <i class="ri-arrow-left-right-line me-1"></i>Transferencia
                    </a>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select">
                            <option value="">Todos</option>
                            <option value="entrada" {{ request('type') === 'entrada' ? 'selected' : '' }}>Entradas</option>
                            <option value="salida" {{ request('type') === 'salida' ? 'selected' : '' }}>Salidas</option>
                            <option value="ajuste" {{ request('type') === 'ajuste' ? 'selected' : '' }}>Ajustes</option>
                            <option value="transferencia" {{ request('type') === 'transferencia' ? 'selected' : '' }}>Transferencias</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sucursal</label>
                        <select name="branch_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="borrador" {{ request('status') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="confirmado" {{ request('status') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                            <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line me-1"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Historial de Movimientos</h5>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Referencia</th>
                                <th>Tipo</th>
                                <th>Sucursal</th>
                                <th>Proveedor/Destino</th>
                                <th>Fecha</th>
                                <th>Items</th>
                                <th class="text-end">Total</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $mov)
                                <tr>
                                    <td><code class="fw-semibold">{{ $mov->reference }}</code></td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'entrada'       => 'success',
                                                'salida'        => 'warning',
                                                'ajuste'        => 'info',
                                                'transferencia' => 'primary',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $typeColors[$mov->type] ?? 'secondary' }}-subtle text-{{ $typeColors[$mov->type] ?? 'secondary' }}">
                                            {{ $mov->type_label }}
                                        </span>
                                    </td>
                                    <td>{{ $mov->branch->name }}</td>
                                    <td>
                                        @if($mov->type === 'transferencia')
                                            <i class="ri-arrow-right-line text-muted me-1"></i>{{ $mov->destinationBranch->name ?? '—' }}
                                        @elseif($mov->supplier)
                                            {{ $mov->supplier->name }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $mov->movement_date->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $mov->items->count() }}</span></td>
                                    <td class="text-end fw-semibold">RD$ {{ number_format($mov->total, 2) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'borrador'   => 'warning',
                                                'confirmado' => 'success',
                                                'cancelado'  => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$mov->status] }}-subtle text-{{ $statusColors[$mov->status] }}">
                                            {{ $mov->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('stock-movements.show', $mov) }}" class="btn btn-sm btn-info">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="ri-exchange-line fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted mb-0">No hay movimientos registrados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($movements->hasPages())
                <div class="card-footer">
                    {{ $movements->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>