<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Revisión del Plan: {{ $plan->nombre_programa }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.revision.index') }}">Revisión de Planes</a></li>
                                <li class="breadcrumb-item active">Detalle</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    {{-- Mensaje de Éxito (Velzon style) --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-check-line me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                <div class="col-xl-8">
                    
                    {{-- Bloque 1: Detalles del Plan --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-information-line align-middle me-1 text-primary"></i>
                                Información General del Programa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <p class="mb-0 text-muted">Universidad:</p>
                                    <h6 class="fs-15">{{ $plan->university->nombre ?? '—' }}</h6>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <p class="mb-0 text-muted">Nivel Académico:</p>
                                    <h6 class="fs-15">{{ $plan->nivel_academico }}</h6>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <p class="mb-0 text-muted">Modalidad:</p>
                                    <h6 class="fs-15">{{ $plan->modalidad }}</h6>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <p class="mb-0 text-muted">Duración:</p>
                                    <h6 class="fs-15">{{ $plan->duracion_meses }} meses</h6>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <p class="mb-0 text-muted">Área de Conocimiento:</p>
                                    <h6 class="fs-15">{{ $plan->area_conocimiento }}</h6>
                                </div>
                                <div class="col-md-12">
                                    <p class="mb-0 text-muted">Descripción:</p>
                                    <p class="text-wrap">{{ $plan->descripcion }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Bloque 2: Documentos Asociados --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-text-line align-middle me-1 text-info"></i>
                                Documentos asociados
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Archivo</th>
                                            <th scope="col" class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($plan->documentos as $doc)
                                        <tr>
                                            <td>{{ $doc->nombre }}</td>
                                            <td><span class="text-muted">{{ basename($doc->archivo) }}</span></td>
                                            <td class="text-center">
                                                <a href="{{ asset('storage/'.$doc->archivo) }}" target="_blank"
                                                    class="btn btn-sm btn-success waves-effect waves-light"
                                                    title="Ver/Descargar">
                                                    <i class="ri-download-line align-bottom"></i> Descargar
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center p-4 text-muted">
                                                <i class="ri-attachment-line me-1"></i> No hay documentos cargados para este plan.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    {{-- Bloque 3: Estado Actual --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-check-double-line align-middle me-1 text-success"></i>
                                Estado Actual
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $estado_clase = match($plan->estado) {
                                    'pendiente' => 'warning',
                                    'en_revision' => 'info',
                                    'aprobado' => 'success',
                                    'rechazado' => 'danger',
                                    default => 'secondary',
                                };
                                $estado_texto = ucwords(str_replace('_', ' ', $plan->estado));
                            @endphp
                            <p class="text-muted mb-2">El plan se encuentra:</p>
                            <h4 class="text-{{ $estado_clase }}">
                                <span class="badge bg-{{ $estado_clase }} fs-14 p-2">{{ $estado_texto }}</span>
                            </h4>
                        </div>
                    </div>

                    {{-- Bloque 4: Actualizar Estado del Plan (Formulario de Acción) --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-repeat-line align-middle me-1 text-danger"></i>
                                Cambiar Estado
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.revision.updateEstado', $plan) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="estado" class="form-label">Nuevo Estado</label>
                                    <select name="estado" id="estado" class="form-select" required>
                                        <option value="pendiente" {{ $plan->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_revision" {{ $plan->estado == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                        <option value="aprobado" {{ $plan->estado == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                        <option value="rechazado" {{ $plan->estado == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mt-2">
                                    <i class="ri-save-line align-middle me-1"></i> Actualizar Estado
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    {{-- Bloque 5: Volver --}}
                    <a href="{{ route('admin.revision.index') }}" class="btn btn-soft-dark w-100 mt-3">
                        <i class="ri-arrow-left-line align-middle me-1"></i> Volver al listado
                    </a>

                </div>
                </div>

        </div>
        </div>
    </x-app-layout>