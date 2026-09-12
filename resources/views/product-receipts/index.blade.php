<x-app-layout>
@section('title', 'Pagos de Productos')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-bank-card-line me-1"></i>Pagos de Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pagos de Productos</li>
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
                               value="{{ request('search') }}" placeholder="Número o paciente">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
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
                <h5 class="card-title mb-0">Listado de Pagos</h5>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Recibo</th>
                                <th>Fecha</th>
                                <th>Factura</th>
                                <th>Paciente</th>
                                <th class="text-end">Total</th>
                                <th>Métodos</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $rec)
                                <tr>
                                    <td><code class="fw-semibold">{{ $rec->number }}</code></td>
                                    <td>{{ $rec->created_at->format('d/m/Y H:i') }}</td>
                                    <td><code>{{ $rec->invoice->number }}</code></td>
                                    <td>
                                        <div class="fw-semibold">{{ $rec->invoice->patient->first_name }} {{ $rec->invoice->patient->last_name }}</div>
                                        <small class="text-muted">{{ $rec->invoice->patient->cedula ?? '' }}</small>
                                    </td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($rec->total_paid, 2) }}</td>
                                    <td>
                                        <small>{{ $rec->payment_summary }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ url('/product-receipts/'.$rec->id) }}" class="btn btn-sm btn-info">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ url('/product-receipts/'.$rec->id.'/print') }}" target="_blank" class="btn btn-sm btn-secondary">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="ri-bank-card-line fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted mb-0">No hay pagos registrados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($receipts->hasPages())
                <div class="card-footer">
                    {{ $receipts->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>