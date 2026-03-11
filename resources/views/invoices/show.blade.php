<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

    {{-- Título --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ $invoice->invoice_number }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
                        <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
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

    {{-- Botones de acción --}}
    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('invoices.index') }}" class="btn btn-light btn-sm">
            <i class="ri-arrow-left-line me-1"></i> Volver
        </a>
        <button type="button" class="btn btn-soft-info btn-sm" onclick="window.print()">
            <i class="ri-printer-line me-1"></i> Imprimir
        </button>
        @if($invoice->status === 'pendiente' && auth()->user()->role->name === 'admin')
            <button type="button" class="btn btn-soft-danger btn-sm ms-auto"
                    data-bs-toggle="modal" data-bs-target="#cancelModal">
                <i class="ri-close-circle-line me-1"></i> Cancelar Factura
            </button>
        @endif
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-9">
            <div class="card">
                <div class="card-body p-4">

                    {{-- Encabezado de factura --}}
                    <div class="row pb-3 mb-4" style="border-bottom: 3px solid #405189;">
                        <div class="col-sm-6">
                            <h3 class="text-primary fw-bold mb-1">{{ config('app.name') }}</h3>
                            @if($invoice->branch)
                                <p class="text-muted mb-0">{{ $invoice->branch->name }}</p>
                                @if($invoice->branch->address)
                                    <p class="text-muted mb-0 small">{{ $invoice->branch->address }}</p>
                                @endif
                                @if($invoice->branch->phone)
                                    <p class="text-muted mb-0 small">
                                        <i class="ri-phone-line me-1"></i>{{ $invoice->branch->phone }}
                                    </p>
                                @endif
                            @endif
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h4 class="mb-1 fw-bold">FACTURA</h4>
                            <p class="fs-5 text-primary fw-semibold mb-1">{{ $invoice->invoice_number }}</p>
                            <p class="text-muted mb-1 small">Fecha: {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                            <span class="badge
                                @if($invoice->status === 'pendiente') bg-warning
                                @elseif($invoice->status === 'pagada') bg-success
                                @else bg-danger
                                @endif fs-12">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Datos paciente y seguro --}}
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Facturar a</h6>
                            <p class="fw-bold mb-0 fs-5">{{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}</p>
                            <p class="text-muted mb-0 small">Cédula: {{ $invoice->patient->cedula }}</p>
                            @if($invoice->patient->phone)
                                <p class="text-muted mb-0 small">Tel: {{ $invoice->patient->phone }}</p>
                            @endif
                            @if($invoice->patient->email)
                                <p class="text-muted mb-0 small">{{ $invoice->patient->email }}</p>
                            @endif
                        </div>
                        <div class="col-sm-6 mt-3 mt-sm-0">
                            @if($invoice->insurance)
                                <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Seguro Médico</h6>
                                <p class="fw-bold mb-0">{{ $invoice->insurance->name }}</p>
                                <p class="text-muted mb-0 small">Cobertura: {{ $invoice->insurance->coverage_percentage }}%</p>
                                @if($invoice->authorization_number)
                                    <p class="text-muted mb-0 small">
                                        Autorización: <span class="fw-semibold text-dark">{{ $invoice->authorization_number }}</span>
                                    </p>
                                @endif
                            @endif
                            <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-1 mt-2">Emitida por</h6>
                            <p class="mb-0 small">{{ $invoice->user->name }}</p>
                        </div>
                    </div>

                    {{-- Tabla de ítems --}}
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Servicio</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Subtotal</th>
                                    @if($invoice->insurance)
                                        <th class="text-end">Cubre Seguro</th>
                                        <th class="text-end">Paga Paciente</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $item->service->name }}</span>
                                            @if($item->service->description)
                                                <br><small class="text-muted">{{ $item->service->description }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">RD$ {{ number_format($item->price, 2) }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">RD$ {{ number_format($item->subtotal, 2) }}</td>
                                        @if($invoice->insurance)
                                            <td class="text-end text-success">RD$ {{ number_format($item->insurance_amount ?? 0, 2) }}</td>
                                            <td class="text-end fw-semibold">RD$ {{ number_format($item->patient_amount ?? $item->subtotal, 2) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totales --}}
                    <div class="row justify-content-end">
                        <div class="col-sm-5">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th class="text-muted fw-normal">Subtotal:</th>
                                    <td class="text-end">RD$ {{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                @if($invoice->insurance_discount > 0)
                                    <tr class="text-success">
                                        <th class="fw-normal">Descuento seguro ({{ $invoice->insurance->coverage_percentage }}%):</th>
                                        <td class="text-end">- RD$ {{ number_format($invoice->insurance_discount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr style="border-top: 2px solid #dee2e6;">
                                    <th class="fs-5 pt-2">TOTAL:</th>
                                    <td class="text-end fs-5 fw-bold text-primary pt-2">
                                        RD$ {{ number_format($invoice->total, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Pie --}}
                    <div class="mt-4 pt-3 border-top text-center text-muted small">
                        <p class="mb-0">
                            Gracias por su preferencia. Factura generada el {{ $invoice->created_at->format('d/m/Y \a \l\a\s H:i') }}.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Modal cancelar --}}
@if($invoice->status === 'pendiente' && auth()->user()->role->name === 'admin')
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="ri-error-warning-line display-5 text-warning"></i>
                <p class="mt-3 mb-1">¿Cancelar la factura <strong>{{ $invoice->invoice_number }}</strong>?</p>
                <small class="text-muted">Esta acción no se puede deshacer.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No, volver</button>
                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger">Sí, cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

</x-app-layout>