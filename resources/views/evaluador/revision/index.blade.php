<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Planes Asignados para Revisión Técnica</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Mis Revisiones</li>
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

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-table-line align-bottom me-1 text-primary"></i>
                                Listado de Asignaciones Pendientes
                            </h5>
                        </div>
                        <div class="card-body">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="text-center" style="width: 1%;">Código</th>
                                            <th scope="col">Programa</th>
                                            <th scope="col">Universidad</th>
                                            <th scope="col" class="text-center">Veredicto Actual</th>
                                            <th scope="col" class="text-center" style="width: 1%;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($asignaciones as $asignacion)
                                        @php
                                            $veredicto = $asignacion->veredicto;
                                            // Lógica de color para el Veredicto
                                            $veredicto_clase = match($veredicto) {
                                                'pendiente' => 'warning',
                                                'en_revision' => 'info',
                                                'aprobado' => 'success',
                                                'rechazado' => 'danger',
                                                default => 'secondary',
                                            };
                                            $veredicto_texto = ucwords(str_replace('_', ' ', $veredicto));
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-medium">{{ $asignacion->plan->codigo_plan }}</td>
                                            <td>{{ $asignacion->plan->nombre_programa }}</td>
                                            <td>{{ $asignacion->plan->university->nombre ?? '—' }}</td>
                                            <td class="text-center">
                                                {{-- Insignia de estado (Badge) --}}
                                                <span class="badge bg-{{ $veredicto_clase }}-subtle text-{{ $veredicto_clase }} p-2">
                                                    {{ $veredicto_texto }}
                                                </span>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                {{-- Botón de Revisar --}}
                                                <a href="{{ route('evaluador.revision.show', $asignacion) }}" 
                                                   class="btn btn-sm btn-primary waves-effect waves-light"
                                                   title="Revisar Plan">
                                                    <i class="ri-eye-line align-bottom me-1"></i> Revisar
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="p-4 text-center text-muted">
                                                <i class="ri-inbox-line me-1"></i>
                                                ¡Felicidades! No tienes planes de estudio pendientes de revisión.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </x-app-layout>