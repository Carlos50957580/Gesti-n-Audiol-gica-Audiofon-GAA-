<x-app-layout>
@section('title', 'Facturas de Productos')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-shopping-bag-3-line me-1"></i>Facturas de Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Facturas de Productos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Número, paciente o cédula">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                            <option value="pagada" {{ request('status') === 'pagada' ? 'selected' : '' }}>Pagadas</option>
                            <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Canceladas</option>
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
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1"></i>Filtrar
                        </button>
                        <a href="{{ url('/product-invoices') }}" class="btn btn-secondary">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Listado de Facturas</h5>
                <a href="{{ url('/product-invoices/create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i>Nueva Venta
                </a>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                @if($isAdmin)
                                    <th>Sucursal</th>
                                @endif
                                <th class="text-center">Items</th>
                                <th class="text-end">Total</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                                <tr>
                                    <td><code class="fw-semibold">{{ $inv->number }}</code></td>
                                    <td>{{ $inv->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $inv->patient->first_name }} {{ $inv->patient->last_name }}</div>
                                        <small class="text-muted">{{ $inv->patient->cedula ?? 'Sin cédula' }}</small>
                                    </td>
                                    @if($isAdmin)
                                        <td>{{ $inv->branch->name }}</td>
                                    @endif
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info">{{ $inv->items->count() }}</span>
                                    </td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($inv->total, 2) }}</td>
                                    <td>
                                        @php
                                            $colors = ['pendiente' => 'warning', 'pagada' => 'success', 'cancelada' => 'danger'];
                                        @endphp
                                        <span class="badge bg-{{ $inv->status_color }}-subtle text-{{ $inv->status_color }}">
    {{ $inv->status_label }}
</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ url('/product-invoices/'.$inv->id) }}" class="btn btn-sm btn-info" title="Ver">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            @if($inv->status === 'pendiente')
                                                <a href="{{ url('/product-receipts/create/'.$inv->id) }}" class="btn btn-sm btn-success" title="Pagar">
                                                    <i class="ri-money-dollar-circle-line"></i>
                                                </a>
                                            @endif
                                            <a href="{{ url('/product-invoices/'.$inv->id.'/print') }}" target="_blank" class="btn btn-sm btn-secondary" title="Imprimir">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdmin ? 8 : 7 }}" class="text-center py-5">
                                        <i class="ri-file-list-3-line fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted mb-0">No hay facturas de productos.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($invoices->hasPages())
                <div class="card-footer">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>