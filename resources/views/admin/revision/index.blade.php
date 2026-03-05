<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Panel de Revisión de Planes (MESCyT / Administrador)</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Revisión de Planes</li>
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
                                <i class="ri-list-check-3 align-middle me-1"></i>
                                Listado de Planes de Estudio para Revisión
                            </h5>
                        </div>
                        <div class="card-body">
                            
                            {{-- Contenedor Responsivo para la Tabla --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="text-center">Código</th>
                                            <th scope="col">Nombre del Programa</th>
                                            <th scope="col">Universidad</th>
                                            <th scope="col">Nivel</th>
                                            <th scope="col" class="text-center">Estado</th>
                                            <th scope="col" class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($planes as $plan)
                                        <tr>
                                            <td class="text-center fw-medium">{{ $plan->codigo_plan }}</td>
                                            <td>{{ $plan->nombre_programa }}</td>
                                            <td>{{ $plan->university->nombre ?? '—' }}</td>
                                            <td>{{ $plan->nivel_academico ?? '-' }}</td>
                                            <td class="text-center">
                                                {{-- Lógica de Badges (etiquetas de estado) de Bootstrap/Velzon --}}
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
                                                <span class="badge bg-{{ $estado_clase }}-subtle text-{{ $estado_clase }} p-2">
                                                    {{ $estado_texto }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.revision.show', $plan) }}" 
                                                    class="btn btn-sm btn-info waves-effect waves-light" 
                                                    title="Revisar Plan">
                                                    <i class="ri-search-line align-bottom"></i> Revisar
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-4 text-muted">
                                                <i class="ri-folder-open-line me-1"></i>
                                                No hay planes de estudio pendientes de revisión. ¡A descansar!
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{-- Fin table-responsive --}}

                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </x-app-layout>