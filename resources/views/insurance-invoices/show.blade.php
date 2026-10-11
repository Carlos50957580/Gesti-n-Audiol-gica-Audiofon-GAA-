<x-app-layout>
@section('title', 'Factura ' . $insuranceInvoice->number)

<div class="page-content">
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="ri-file-text-line me-1"></i>{{ $insuranceInvoice->number }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('insurance-invoices.index') }}">Facturas a Seguros</a></li>
                            <li class="breadcrumb-item active">{{ $insuranceInvoice->number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-check-line me-1"></i>{{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Acciones --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap">
                    @if($insuranceInvoice->canBeCancelled())
                        <form action="{{ route('insurance-invoices.cancel', $insuranceInvoice) }}" method="POST"
                              onsubmit="return confirm('¿Cancelar esta factura?')">
                            @csrf
                            <button class="btn btn-danger">
                                <i class="ri-close-circle-line me-1"></i>Cancelar
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('insurance-invoices.print', $insuranceInvoice) }}" target="_blank" class="btn btn-primary">
                        <i class="ri-printer-line me-1"></i>Imprimir
                    </a>

                    @if($ef2Configurado && $insuranceInvoice->puedeEnviarseADgii())
                        <form action="{{ route('insurance-invoices.enviar-ef2', $insuranceInvoice) }}" method="POST"
                              onsubmit="return confirm('¿Enviar esta factura a la DGII?')">
                            @csrf
                            <button class="btn btn-success">
                                <i class="ri-cloud-line me-1"></i>
                                {{ $insuranceInvoice->enviada_dgii ? 'Reenviar a DGII' : 'Enviar a DGII' }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('insurance-invoices.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Datos generales --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Información de la Factura</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Número</small>
                                <strong><code>{{ $insuranceInvoice->number }}</code></strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Fecha Emisión</small>
                                <strong>{{ $insuranceInvoice->invoice_date->format('d/m/Y') }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Seguro / ARS</small>
                                <strong>{{ $insuranceInvoice->insurance->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Sucursal</small>
                                <strong>{{ $insuranceInvoice->branch->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">RNC del Seguro</small>
                                <strong><code>{{ $insuranceInvoice->customer_rnc ?? '—' }}</code></strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Estado</small>
                                <span class="badge bg-{{ $insuranceInvoice->status_color }}-subtle text-{{ $insuranceInvoice->status_color }}">
                                    {{ $insuranceInvoice->status_label }}
                                </span>
                            </div>
                            @if($insuranceInvoice->concept)
                                <div class="col-12">
                                    <small class="text-muted d-block">Concepto</small>
                                    <p class="mb-0">{{ $insuranceInvoice->concept }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Comprobante electrónico --}}
                @if($insuranceInvoice->encf)
                    <div class="card border-primary">
                        <div class="card-header bg-primary-subtle d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="ri-shield-check-line me-1 text-primary"></i>
                                Comprobante Fiscal Electrónico
                            </h5>
                            <span class="badge bg-{{ $insuranceInvoice->estado_dgii_color }}">
                                {{ $insuranceInvoice->estado_dgii_label }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">e-NCF</small>
                                            <code class="fs-6 fw-bold text-primary">{{ $insuranceInvoice->encf }}</code>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Tipo</small>
                                            @switch($insuranceInvoice->ncf_type)
                                                @case('credito_fiscal') <span class="badge bg-primary">E31 - Crédito Fiscal</span> @break
                                                @case('gubernamental') <span class="badge bg-warning">E45 - Gubernamental</span> @break
                                                @case('consumidor_final') <span class="badge bg-info">E32 - Consumidor Final</span> @break
                                                @case('regimen_especial') <span class="badge bg-secondary">E44 - Régimen Especial</span> @break
                                            @endswitch
                                        </div>
                                        @if($insuranceInvoice->enviada_dgii_at)
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Enviado</small>
                                                <strong>{{ $insuranceInvoice->enviada_dgii_at->format('d/m/Y H:i') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 text-center border-start">
                                    @if($insuranceInvoice->qr_link)
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($insuranceInvoice->qr_link) }}"
                                             alt="QR DGII" class="img-fluid" style="max-width:180px;">
                                        <p class="small text-muted mb-0 mt-2">
                                            <i class="ri-qr-scan-line me-1"></i>Escanee para verificar
                                        </p>
                                    @endif
                                    @if($insuranceInvoice->pdf_cloud_url)
                                        <a href="{{ $insuranceInvoice->pdf_cloud_url }}" target="_blank"
                                           class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="ri-file-pdf-line me-1"></i>PDF DGII
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Columna derecha --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Resumen</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong>RD$ {{ number_format($insuranceInvoice->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ITBIS:</span>
                            <strong>RD$ {{ number_format($insuranceInvoice->tax_amount, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold fs-6">TOTAL:</span>
                            <span class="fw-bold fs-4 text-success">
                                RD$ {{ number_format($insuranceInvoice->total, 2) }}
                            </span>
                        </div>
                        @if($insuranceInvoice->with_tax)
                            <span class="badge bg-success-subtle text-success">Con ITBIS</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Sin ITBIS (Exento)</span>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Info Adicional</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Creado por:</small>
                            <strong>{{ $insuranceInvoice->user->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Creado:</small>
                            <strong>{{ $insuranceInvoice->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>