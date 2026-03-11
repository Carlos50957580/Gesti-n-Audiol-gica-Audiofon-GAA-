<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    {{-- Título --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">Facturas</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Facturas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">Listado de Facturas</h5>
            <div class="flex-shrink-0">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i> Nueva Factura
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-nowrap align-middle">
                    <thead class="table-light">
                        <tr>
                            <th># Factura</th>
                            <th>Paciente</th>
                            <th>Sucursal</th>
                            <th>Seguro</th>
                            <th>Subtotal</th>
                            <th>Desc. Seguro</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        {{ $invoice->invoice_number }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}</span>
                                    <br><small class="text-muted">{{ $invoice->patient->cedula }}</small>
                                </td>
                                <td>{{ $invoice->branch->name }}</td>
                                <td>{{ $invoice->insurance?->name ?? '—' }}</td>
                                <td>RD$ {{ number_format($invoice->subtotal, 2) }}</td>
                                <td>
                                    @if($invoice->insurance_discount > 0)
                                        <span class="text-success">- RD$ {{ number_format($invoice->insurance_discount, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">RD$ {{ number_format($invoice->total, 2) }}</td>
                                <td>
                                    <span class="badge
                                        @if($invoice->status === 'pendiente') bg-warning
                                        @elseif($invoice->status === 'pagada') bg-success
                                        @else bg-danger
                                        @endif">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="hstack gap-2 justify-content-center">
                                        <a href="{{ route('invoices.show', $invoice) }}"
                                           class="btn btn-sm btn-soft-info">
                                            <i class="ri-eye-fill"></i>
                                        </a>
                                        @if($invoice->status === 'pendiente' && auth()->user()->role->name === 'admin')
                                            <button type="button"
                                                    class="btn btn-sm btn-soft-danger"
                                                    onclick="confirmCancel({{ $invoice->id }}, '{{ $invoice->invoice_number }}')">
                                                <i class="ri-close-circle-line"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="ri-file-list-3-line display-5 text-muted"></i>
                                    <p class="text-muted mt-3">No hay facturas registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
</div>

{{-- Modal confirmar cancelación --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="ri-error-warning-line display-5 text-warning"></i>
                <p class="mt-3 mb-1">¿Estás seguro de cancelar la factura <strong id="cancelInvoiceNum"></strong>?</p>
                <small class="text-muted">Esta acción no se puede deshacer.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No, volver</button>
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Sí, cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmCancel(id, num) {
    document.getElementById('cancelInvoiceNum').textContent = num;
    document.getElementById('cancelForm').action = '/invoices/' + id + '/cancel';
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}
</script>
@endpush

</x-app-layout>