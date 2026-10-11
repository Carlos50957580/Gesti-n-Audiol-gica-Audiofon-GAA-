<x-app-layout>
@section('title', 'Facturas a Seguros')

<div class="page-content">
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="ri-shield-cross-line me-1"></i>Facturas a Seguros / ARS
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Facturas a Seguros</li>
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
                               value="{{ request('search') }}"
                               placeholder="Número, e-NCF o seguro">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Seguro</label>
                        <select name="insurance_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($insurances as $ins)
                                <option value="{{ $ins->id }}" {{ request('insurance_id') == $ins->id ? 'selected' : '' }}>
                                    {{ $ins->name }}
                                </option>
                            @endforeach
                        </select>
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
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Listado de Facturas</h5>
                <a href="{{ route('insurance-invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i>Nueva Factura
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
                                <th>e-NCF</th>
                                <th>Fecha</th>
                                <th>Seguro</th>
                                <th>Tipo</th>
                                <th class="text-end">Total</th>
                                <th>Estado</th>
                                <th>DGII</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                                <tr>
                                    <td><code class="fw-semibold">{{ $inv->number }}</code></td>
                                    <td>
                                        @if($inv->encf)
                                            <code class="text-primary fw-semibold">{{ $inv->encf }}</code>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $inv->insurance->name }}</div>
                                        <small class="text-muted">{{ $inv->customer_rnc ?? '—' }}</small>
                                    </td>
                                    <td>
                                        @switch($inv->ncf_type)
                                            @case('credito_fiscal') <span class="badge bg-primary-subtle text-primary">Crédito Fiscal</span> @break
                                            @case('gubernamental') <span class="badge bg-warning-subtle text-warning">Gubernamental</span> @break
                                            @case('consumidor_final') <span class="badge bg-info-subtle text-info">Consumidor Final</span> @break
                                            @case('regimen_especial') <span class="badge bg-secondary-subtle text-secondary">Régimen Especial</span> @break
                                        @endswitch
                                    </td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($inv->total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $inv->status_color }}-subtle text-{{ $inv->status_color }}">
                                            {{ $inv->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($inv->enviada_dgii)
                                            <span class="badge bg-{{ $inv->estado_dgii_color }}-subtle text-{{ $inv->estado_dgii_color }}">
                                                {{ $inv->estado_dgii_label }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">No enviada</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('insurance-invoices.show', $inv) }}" class="btn btn-sm btn-info">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('insurance-invoices.print', $inv) }}" target="_blank" class="btn btn-sm btn-secondary">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="ri-file-list-3-line fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted mb-0">No hay facturas a seguros registradas.</p>
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