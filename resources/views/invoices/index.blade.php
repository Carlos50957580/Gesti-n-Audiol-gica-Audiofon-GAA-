<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Facturas</h4>
            <div class="page-title-right">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Nueva Factura
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-check-line me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="row g-3 mb-3" id="filterForm">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="N° factura, paciente..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagada" {{ request('status') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                            <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    @if(auth()->user()->role->name === 'admin')
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
                    @endif
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line"></i>
                        </button>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary w-100">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th># Factura</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Sucursal</th>
                                <th>Fecha</th>
                                <th>Subtotal</th>
                                <th>Impuestos</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="fw-bold">#{{ $invoice->id }}</span>
                                    @if($invoice->ncf)
                                        <br><small class="text-muted">{{ $invoice->ncf }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                    {{ strtoupper(substr($invoice->patient->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($invoice->patient->last_name ?? '', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $invoice->patient->full_name ?? 'N/A' }}</span>
                                            <br><small class="text-muted">{{ $invoice->patient->cedula ?? 'Sin cédula' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $invoice->doctor->name ?? 'N/A' }}
                                    @if($invoice->doctor)
                                        <br><small class="text-muted">{{ $invoice->doctor->role->name ?? '' }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $invoice->branch->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    {{ $invoice->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <span class="text-muted">RD$ {{ number_format($invoice->subtotal, 2) }}</span>
                                </td>
                                <td>
                                    @if($invoice->tax_amount > 0)
                                        <span class="text-warning">RD$ {{ number_format($invoice->tax_amount, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold">RD$ {{ number_format($invoice->total, 2) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pendiente' => 'warning',
                                            'pagada' => 'success',
                                            'cancelada' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'pendiente' => 'Pendiente',
                                            'pagada' => 'Pagada',
                                            'cancelada' => 'Cancelada'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-soft-primary btn-sm" title="Ver">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @if($invoice->status === 'pendiente' && auth()->user()->role->name === 'admin')
                                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" 
                                                    onclick="return confirm('¿Eliminar esta factura?')" title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="ri-file-list-3-line" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                    No hay facturas registradas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>

{{-- Modal de impresión --}}
<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Factura #<span id="printInvoiceId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="printContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando factura...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="ri-printer-line"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printInvoice(invoiceId) {
    const modal = new bootstrap.Modal(document.getElementById('printModal'));
    const content = document.getElementById('printContent');
    
    // Mostrar loading
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando factura...</p>
        </div>
    `;
    
    document.getElementById('printInvoiceId').textContent = invoiceId;
    modal.show();
    
    // Cargar contenido
    fetch(`/invoices/${invoiceId}/print`)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="ri-error-warning-line" style="font-size: 48px;"></i>
                    <p class="mt-2">Error al cargar la factura</p>
                </div>
            `;
        });
}

// Imprimir al abrir el modal (opcional)
document.addEventListener('DOMContentLoaded', function() {
    // Auto-print cuando el modal se abre (opcional)
    // document.getElementById('printModal').addEventListener('shown.bs.modal', function() {
    //     setTimeout(() => window.print(), 500);
    // });
});
</script>

<style>
@media print {
    .page-content .container-fluid {
        padding: 0 !important;
    }
    .btn, .page-title-box, .breadcrumb, .card-header, .modal-footer {
        display: none !important;
    }
    .modal {
        position: static !important;
        display: block !important;
        background: white !important;
    }
    .modal-dialog {
        max-width: 100% !important;
        margin: 0 !important;
    }
    .modal-content {
        border: none !important;
        box-shadow: none !important;
    }
    .modal-header {
        display: none !important;
    }
    body {
        background: white !important;
    }
}
</style>
@endpush
</x-app-layout>