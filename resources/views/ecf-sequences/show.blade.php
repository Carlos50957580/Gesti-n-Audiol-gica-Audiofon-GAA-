@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                <i class="ri-file-list-3-line me-1"></i> Detalle de Secuencia
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ecf-sequences.index') }}">Secuencias e-CF</a></li>
                    <li class="breadcrumb-item active">Detalle</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    E{{ $ecfSequence->tipo_ecf }} — {{ $ecfSequence->prefijo }}
                </h5>
                <span class="badge {{ $ecfSequence->estado ? 'bg-success' : 'bg-secondary' }} fs-12">
                    {{ $ecfSequence->estado ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted" width="200">Tipo e-CF:</th>
                                <td>E{{ $ecfSequence->tipo_ecf }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Prefijo:</th>
                                <td><code>{{ $ecfSequence->prefijo }}</code></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Rango:</th>
                                <td>{{ number_format($ecfSequence->desde) }} — {{ number_format($ecfSequence->hasta) }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Siguiente e-NCF:</th>
                                <td><code class="text-success fs-14">{{ $ecfSequence->siguiente_encf }}</code></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Secuencia Actual:</th>
                                <td>{{ $ecfSequence->secuencia_actual }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Disponibles:</th>
                                <td>{{ number_format($ecfSequence->secuencias_disponibles) }} de {{ number_format($ecfSequence->total_secuencias) }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">% Uso:</th>
                                <td>
                                    <div class="progress" style="height: 8px; max-width: 250px;">
                                        @php
                                            $porc = $ecfSequence->porcentaje_uso;
                                            $color = $porc >= 90 ? 'bg-danger' : ($porc >= 70 ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress-bar {{ $color }}" style="width: {{ $porc }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $porc }}%</small>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Fecha Vencimiento:</th>
                                <td>
                                    @if($ecfSequence->tipo_ecf === '32')
                                        <span class="text-muted">No requiere</span>
                                    @else
                                        {{ $ecfSequence->fecha_vencimiento?->format('d/m/Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Sucursal:</th>
                                <td>{{ $ecfSequence->branch->name ?? 'Global' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Notas:</th>
                                <td>{{ $ecfSequence->notas ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('ecf-sequences.edit', $ecfSequence) }}" class="btn btn-primary">
                    <i class="ri-edit-line me-1"></i> Editar
                </a>
                <a href="{{ route('ecf-sequences.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Uso de la Secuencia</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Facturas de Productos:</span>
                    <strong>{{ $facturasProductos }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Facturas de Servicios:</span>
                    <strong>{{ $facturasServicios }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Total emitidas:</span>
                    <strong class="text-primary">{{ $facturasProductos + $facturasServicios }}</strong>
                </div>
            </div>
        </div>

        @if($ecfSequence->enAlerta())
            <div class="alert alert-warning">
                <i class="ri-alert-line me-1"></i>
                <strong>Atención:</strong> Quedan solo {{ $ecfSequence->secuencias_disponibles }} secuencias disponibles.
                Considera solicitar un nuevo rango a la DGII.
            </div>
        @endif
    </div>
</div>
@endsection