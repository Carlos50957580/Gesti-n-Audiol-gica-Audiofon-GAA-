<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Factura #{{ $invoice->id }}</h4>
            <div class="page-title-right">
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
                <button type="button" class="btn btn-primary" onclick="printInvoice('pdf')">
                    <i class="ri-file-pdf-line"></i> PDF
                </button>
                <button type="button" class="btn btn-success" onclick="printInvoice('pos')">
                    <i class="ri-printer-line"></i> Ticket POS
                </button>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="invoice-print-area">
                    <!-- ENCABEZADO -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <h4 class="mb-1">{{ $settings['company_name'] ?? 'Clínica' }}</h4>
                            <p class="text-muted mb-0">{{ $settings['address'] ?? '' }}</p>
                            <p class="text-muted mb-0">Tel: {{ $settings['phone'] ?? '' }}</p>
                            <p class="text-muted mb-0">Email: {{ $settings['email'] ?? '' }}</p>
                        </div>
                        <div class="col-6 text-end">
                            <h2 class="text-primary">FACTURA</h2>
                            <p class="mb-0"><strong>N°:</strong> #{{ $invoice->id }}</p>
                            <p class="mb-0"><strong>Fecha:</strong> {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                            @if($invoice->ncf)
                                <p class="mb-0"><strong>NCF:</strong> {{ $invoice->ncf }}</p>
                            @endif
                            <p class="mb-0">
                                <strong>Estado:</strong>
                                <span class="badge bg-{{ $invoice->status === 'pendiente' ? 'warning' : ($invoice->status === 'pagada' ? 'success' : 'danger') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- DATOS DEL PACIENTE -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-primary">PACIENTE</h6>
                                <p class="mb-1"><strong>{{ $invoice->patient->full_name ?? 'N/A' }}</strong></p>
                                <p class="mb-1 text-muted">Cédula: {{ $invoice->patient->cedula ?? 'N/A' }}</p>
                                <p class="mb-1 text-muted">Teléfono: {{ $invoice->patient->phone ?? 'N/A' }}</p>
                                <p class="mb-0 text-muted">Email: {{ $invoice->patient->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-primary">MÉDICO / SUCURSAL</h6>
                                <p class="mb-1"><strong>{{ $invoice->doctor->name ?? 'N/A' }}</strong></p>
                                <p class="mb-1 text-muted">{{ $invoice->doctor->role->name ?? '' }}</p>
                                <p class="mb-0 text-muted">Sucursal: {{ $invoice->branch->name ?? 'N/A' }}</p>
                                @if($invoice->authorization_number)
                                    <p class="mb-0 text-muted">Autorización: {{ $invoice->authorization_number }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TABLA DE SERVICIOS -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" id="invoice-table">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Servicio</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-end">Impuesto</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $item->service->name ?? 'N/A' }}
                                        @if($item->service && $item->service->category)
                                            <br><small class="text-muted">{{ $item->service->category->name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">RD$ {{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">RD$ {{ number_format($item->subtotal, 2) }}</td>
                                    <td class="text-end">
                                        @if($item->tax_amount > 0)
                                            RD$ {{ number_format($item->tax_amount, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong>RD$ {{ number_format($item->subtotal + $item->tax_amount, 2) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end"><strong>RD$ {{ number_format($invoice->subtotal, 2) }}</strong></td>
                                    <td class="text-end"><strong>RD$ {{ number_format($invoice->tax_amount, 2) }}</strong></td>
                                    <td class="text-end"><strong>RD$ {{ number_format($invoice->subtotal + $invoice->tax_amount, 2) }}</strong></td>
                                </tr>
                                @if($invoice->insurance_discount > 0)
                                <tr>
                                    <td colspan="6" class="text-end text-success">
                                        <strong>Descuento seguro:</strong>
                                    </td>
                                    <td class="text-end text-success">
                                        <strong>− RD$ {{ number_format($invoice->insurance_discount, 2) }}</strong>
                                    </td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <td colspan="6" class="text-end" style="font-size: 1.2rem;">
                                        <strong>TOTAL A PAGAR:</strong>
                                    </td>
                                    <td class="text-end" style="font-size: 1.2rem;">
                                        <strong>RD$ {{ number_format($invoice->total, 2) }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- PIE DE PÁGINA -->
                    <div class="text-center text-muted" style="border-top: 1px solid #e9ecef; padding-top: 15px;">
                        <p class="mb-1">{{ $settings['company_name'] ?? 'Clínica' }} - {{ $settings['address'] ?? '' }}</p>
                        <p class="mb-0" style="font-size: 12px;">Gracias por su visita</p>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-primary" onclick="printInvoice('pdf')">
                        <i class="ri-file-pdf-line"></i> Descargar PDF
                    </button>
                    <button type="button" class="btn btn-success" onclick="printInvoice('pos')">
                        <i class="ri-printer-line"></i> Imprimir Ticket POS
                    </button>
                    @if($invoice->status === 'pendiente')
                        <a href="#" class="btn btn-warning">
                            <i class="ri-bank-card-line"></i> Registrar Pago
                        </a>
                    @endif
                    @if($invoice->status === 'pendiente' && auth()->user()->role->name === 'admin')
                        <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Cancelar esta factura?')">
                                <i class="ri-close-circle-line"></i> Cancelar Factura
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>

@push('scripts')
<script>
function printInvoice(type) {
    const printArea = document.getElementById('invoice-print-area');
    
    if (type === 'pos') {
        // Imprimir en formato ticket POS
        const win = window.open('', '_blank', 'width=400,height=600');
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Ticket POS - Factura #{{ $invoice->id }}</title>
                <style>
                    body {
                        font-family: 'Courier New', monospace;
                        font-size: 12px;
                        width: 320px;
                        margin: 0 auto;
                        padding: 10px;
                        background: white;
                    }
                    .text-center { text-align: center; }
                    .text-end { text-align: right; }
                    .fw-bold { font-weight: bold; }
                    .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; }
                    .divider { border-top: 1px dashed #000; margin: 5px 0; }
                    table { width: 100%; border-collapse: collapse; }
                    td { padding: 2px 0; }
                    .total { font-size: 16px; font-weight: bold; }
                    .text-success { color: green; }
                </style>
            </head>
            <body>
                <div class="text-center border-bottom">
                    <h4 style="margin: 0;">{{ $settings['company_name'] ?? 'Clínica' }}</h4>
                    <p style="margin: 2px 0; font-size: 10px;">{{ $settings['address'] ?? '' }}</p>
                    <p style="margin: 2px 0; font-size: 10px;">Tel: {{ $settings['phone'] ?? '' }}</p>
                    <p style="margin: 5px 0;">FACTURA #{{ $invoice->id }}</p>
                    <p style="margin: 2px 0; font-size: 10px;">{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                    @if($invoice->ncf)
                        <p style="margin: 2px 0; font-size: 10px;">NCF: {{ $invoice->ncf }}</p>
                    @endif
                </div>

                <div style="font-size: 11px; margin: 5px 0;">
                    <p style="margin: 2px 0;"><strong>Paciente:</strong> {{ $invoice->patient->full_name ?? 'N/A' }}</p>
                    <p style="margin: 2px 0;"><strong>Médico:</strong> {{ $invoice->doctor->name ?? 'N/A' }}</p>
                </div>

                <div class="divider"></div>

                <table>
                    <thead>
                        <tr>
                            <td class="text-start fw-bold">Servicio</td>
                            <td class="text-end fw-bold">Cant</td>
                            <td class="text-end fw-bold">Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="text-start" style="font-size: 11px;">{{ $item->service->name ?? 'N/A' }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">RD$ {{ number_format($item->subtotal + $item->tax_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="divider"></div>

                <div class="text-end">
                    <p style="margin: 2px 0;">Subtotal: RD$ {{ number_format($invoice->subtotal, 2) }}</p>
                    <p style="margin: 2px 0;">Impuestos: RD$ {{ number_format($invoice->tax_amount, 2) }}</p>
                    @if($invoice->insurance_discount > 0)
                        <p style="margin: 2px 0; color: green;">Descuento: - RD$ {{ number_format($invoice->insurance_discount, 2) }}</p>
                    @endif
                    <p class="total">TOTAL: RD$ {{ number_format($invoice->total, 2) }}</p>
                </div>

                <div class="divider"></div>

                <div class="text-center">
                    <p style="margin: 2px 0;">¡Gracias por su visita!</p>
                </div>
            </body>
            </html>
        `);
        win.document.close();
        win.focus();
        setTimeout(() => {
            win.print();
            setTimeout(() => win.close(), 1000);
        }, 500);
    } else {
        // PDF - Impresión estándar
        const content = printArea.outerHTML;
        const win = window.open('', '_blank', 'width=900,height=700');
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Factura #{{ $invoice->id }}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 30px; background: white; max-width: 900px; margin: 0 auto; }
                    .text-center { text-align: center; }
                    .text-end { text-align: right; }
                    .fw-bold { font-weight: bold; }
                    .text-primary { color: #405189; }
                    .text-muted { color: #6c757d; }
                    .text-success { color: #0ab39c; }
                    .bg-light { background: #f8f9fa; }
                    .rounded { border-radius: 8px; }
                    .p-3 { padding: 15px; }
                    .mb-1 { margin-bottom: 5px; }
                    .mb-4 { margin-bottom: 25px; }
                    table { width: 100%; border-collapse: collapse; }
                    .table-bordered { border: 1px solid #dee2e6; }
                    .table-bordered th, .table-bordered td { border: 1px solid #dee2e6; padding: 8px; }
                    .table-primary { background: #405189; color: white; }
                    .table-active { background: #f0f4ff; }
                    .badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 12px; }
                    .bg-warning { background: #f7b84d; }
                    .bg-success { background: #0ab39c; }
                    .bg-danger { background: #f06548; }
                    .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
                    .btn-primary { background: #405189; color: white; }
                    .btn-secondary { background: #6c757d; color: white; }
                    .no-print { margin-top: 20px; }
                    @media print { .no-print { display: none !important; } body { padding: 20px; } }
                </style>
            </head>
            <body>
                ${content}
                <div class="text-center no-print">
                    <button class="btn btn-primary" onclick="window.print()">🖨️ Imprimir</button>
                    <button class="btn btn-secondary" onclick="window.close()">❌ Cerrar</button>
                </div>
            </body>
            </html>
        `);
        win.document.close();
        win.focus();
    }
}
</script>
@endpush
</x-app-layout>