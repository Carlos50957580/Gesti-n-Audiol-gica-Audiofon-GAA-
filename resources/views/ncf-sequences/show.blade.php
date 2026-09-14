<x-app-layout>
@section('title', 'Secuencia NCF')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-file-shield-2-line me-1"></i>{{ $ncfSequence->name }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/ncf-sequences') }}">Comprobantes NCF</a></li>
                            <li class="breadcrumb-item active">Detalle</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Información de la Secuencia</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Nombre</small>
                                <strong>{{ $ncfSequence->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Estado</small>
                                <span class="badge bg-{{ $ncfSequence->status_color }}-subtle text-{{ $ncfSequence->status_color }}">
                                    {{ $ncfSequence->status_label }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Tipo NCF</small>
                                <strong>{{ $ncfSequence->type->code }} - {{ $ncfSequence->type->name }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Sucursal</small>
                                <strong>{{ $ncfSequence->branch ? $ncfSequence->branch->name : 'Todas' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Prefijo</small>
                                <strong>{{ $ncfSequence->prefix }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Serie</small>
                                <strong>{{ $ncfSequence->serie }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Válido desde</small>
                                <strong>{{ $ncfSequence->valid_from->format('d/m/Y') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Vence el</small>
                                <strong class="{{ $ncfSequence->days_left < 30 ? 'text-danger' : '' }}">
                                    {{ $ncfSequence->valid_until->format('d/m/Y') }}
                                </strong>
                            </div>
                            @if($ncfSequence->notes)
                                <div class="col-12">
                                    <small class="text-muted d-block">Notas</small>
                                    <p class="mb-0">{{ $ncfSequence->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Rango de NCF</h5></div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background:#f0f4ff;">
                                    <small class="text-muted d-block mb-1">Inicio</small>
                                    <code style="font-size:.95rem;">{{ $ncfSequence->start_ncf }}</code>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background:#fff8e1;">
                                    <small class="text-muted d-block mb-1">Actual</small>
                                    <code style="font-size:.95rem;font-weight:700;">{{ $ncfSequence->current_ncf }}</code>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background:#e8f8f0;">
                                    <small class="text-muted d-block mb-1">Fin</small>
                                    <code style="font-size:.95rem;">{{ $ncfSequence->end_ncf }}</code>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded" style="background:#f0f2f7;">
                                    <small class="text-muted d-block mb-1">Disponibles</small>
                                    <strong class="fs-5">{{ number_format($ncfSequence->remaining) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Estadísticas</h5></div>
                    <div class="card-body text-center">
                        <div style="position:relative;display:inline-block;">
                            <svg width="150" height="150" viewBox="0 0 150 150">
                                <circle cx="75" cy="75" r="65" fill="none" stroke="#f0f2f7" stroke-width="12"/>
                                <circle cx="75" cy="75" r="65" fill="none" 
                                        stroke="{{ $ncfSequence->used_percent > 80 ? '#e74c3c' : '#405189' }}"
                                        stroke-width="12"
                                        stroke-dasharray="{{ $ncfSequence->used_percent * 4.08 }} 408"
                                        stroke-linecap="round"
                                        transform="rotate(-90 75 75)"/>
                                <text x="75" y="80" text-anchor="middle" font-size="24" font-weight="800" fill="#344563">
                                    {{ $ncfSequence->used_percent }}%
                                </text>
                                <text x="75" y="100" text-anchor="middle" font-size="11" fill="#6b7a99">
                                    usado
                                </text>
                            </svg>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Usados:</span>
                                <strong>{{ number_format($ncfSequence->used) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Disponibles:</span>
                                <strong class="text-success">{{ number_format($ncfSequence->remaining) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total:</span>
                                <strong>{{ number_format($ncfSequence->total_available) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ url('/ncf-sequences/'.$ncfSequence->id.'/edit') }}" class="btn btn-warning">
                                <i class="ri-edit-line me-1"></i>Editar
                            </a>
                            <a href="{{ url('/ncf-sequences') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>