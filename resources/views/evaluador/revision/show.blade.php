<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">
                            <i class="ri-ruler-line align-bottom me-1 text-primary"></i>
                            Revisión Técnica: **{{ $asignacion->plan->nombre_programa }}**
                        </h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('evaluador.revision.index') }}">Mis Revisiones</a></li>
                                <li class="breadcrumb-item active">Revisión</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header bg-soft-info">
                            <h5 class="card-title mb-0">
                                <i class="ri-information-line me-1 align-bottom"></i>
                                Información General del Plan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="mb-0 text-muted">Universidad:</p>
                                    <h6 class="fw-semibold">{{ $asignacion->plan->university->nombre ?? '—' }}</h6>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-0 text-muted">Nivel Académico:</p>
                                    <h6 class="fw-semibold">{{ $asignacion->plan->nivel_academico }}</h6>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-0 text-muted">Modalidad:</p>
                                    <h6 class="fw-semibold">{{ $asignacion->plan->modalidad }}</h6>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-0 text-muted">Área de Conocimiento:</p>
                                    <h6 class="fw-semibold">{{ $asignacion->plan->area_conocimiento }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-folder-open-line me-1 align-bottom text-info"></i>
                                Documentos de Respaldo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 30%;">Nombre del Documento</th>
                                            <th scope="col">Archivo</th>
                                            <th scope="col" class="text-center" style="width: 1%;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($asignacion->plan->documentos as $doc)
                                        <tr>
                                            <td>{{ $doc->nombre }}</td>
                                            <td><span class="text-muted">{{ basename($doc->archivo) }}</span></td>
                                            <td class="text-center">
                                                <a href="{{ asset('storage/'.$doc->archivo) }}" target="_blank"
                                                   class="btn btn-sm btn-success waves-effect waves-light"
                                                   title="Ver o Descargar">
                                                    <i class="ri-download-line align-bottom me-1"></i> Ver / Descargar
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center p-4 text-muted">
                                                <i class="ri-file-line me-1"></i>
                                                El plan no tiene documentos adjuntos.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-soft-warning">
                            <h5 class="card-title mb-0">
                                <i class="ri-edit-line me-1 align-bottom text-warning"></i>
                                Observaciones Técnicas y Veredicto
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('evaluador.revision.update', $asignacion) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Área de Texto para Observaciones --}}
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Tus Observaciones</label>
                                    <textarea name="observaciones" id="observaciones" rows="5" class="form-control" placeholder="Escribe tus observaciones técnicas aquí..." required>{{ old('observaciones', $asignacion->observaciones) }}</textarea>
                                </div>

                                {{-- Select de Veredicto y Botón de Guardar --}}
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-grow-1">
                                        <label for="veredicto" class="form-label">Establecer Veredicto</label>
                                        <select name="veredicto" id="veredicto" class="form-select" required>
                                            <option value="pendiente" @selected($asignacion->veredicto == 'pendiente')>Pendiente</option>
                                            <option value="en_revision" @selected($asignacion->veredicto == 'en_revision')>En Revisión</option>
                                            <option value="aprobado" @selected($asignacion->veredicto == 'aprobado')>Aprobado</option>
                                            <option value="rechazado" @selected($asignacion->veredicto == 'rechazado')>Rechazado</option>
                                        </select>
                                    </div>
                                    
                                    {{-- Botón de Enviar --}}
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="ri-save-line align-bottom me-1"></i> Guardar Revisión
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-4 mb-4">
                        <a href="{{ route('evaluador.revision.index') }}" class="btn btn-secondary waves-effect">
                            <i class="ri-arrow-left-line align-bottom me-1"></i> Volver al listado de asignaciones
                        </a>
                    </div>

                </div>
            </div>

        </div>
        </div>
    </x-app-layout>