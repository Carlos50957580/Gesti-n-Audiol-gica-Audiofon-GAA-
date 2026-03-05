<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">📋 Historial de Revisiones</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Mis Asignaciones</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-filter-3-line align-bottom me-1 text-info"></i>
                                Opciones de Filtrado
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- FILTRO --}}
                            <form method="GET" class="row g-3">

                                {{-- Columna de Filtro de Veredicto --}}
                                <div class="col-lg-3 col-md-6">
                                    <label for="select-veredicto" class="form-label">Filtrar por Veredicto</label>
                                    <select name="veredicto" id="select-veredicto" class="form-select">
                                        {{-- Mantener la selección actual --}}
                                        <option value="" @selected(request('veredicto') == '')>-- Todos --</option>
                                        <option value="pendiente" @selected(request('veredicto') == 'pendiente')>Pendiente</option>
                                        <option value="en_revision" @selected(request('veredicto') == 'en_revision')>En Revisión</option>
                                        <option value="aprobado" @selected(request('veredicto') == 'aprobado')>Aprobado</option>
                                        <option value="rechazado" @selected(request('veredicto') == 'rechazado')>Rechazado</option>
                                    </select>
                                </div>
                                
                                {{-- Fila de Botones (Columna de acciones para alineación) --}}
                                <div class="col-lg-9 col-md-6 d-flex align-items-end justify-content-end gap-2">
                                    
                                    {{-- Botón de Filtrar --}}
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="ri-search-line align-bottom"></i> Aplicar Filtros
                                    </button>

                                    {{-- Botones de Exportación (IMPORTANTE: Manda los filtros en la URL) --}}
                                    <a href="{{ route('evaluador.reportes.excel', request()->all()) }}" 
                                       class="btn btn-success waves-effect waves-light">
                                        <i class="ri-file-excel-2-line align-bottom me-1"></i> Exportar Excel
                                    </a>

                                    <a href="{{ route('evaluador.reportes.pdf', request()->all()) }}" 
                                       class="btn btn-danger waves-effect waves-light">
                                        <i class="ri-file-pdf-line align-bottom me-1"></i> Exportar PDF
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                Resultados de Revisiones
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
                                            <th scope="col" class="text-center">Veredicto</th>
                                            <th scope="col" class="text-center">Última Revisión</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($asignaciones as $a)
                                            @php
                                                $plan = $a->plan; 
                                                $uni = $plan->university->nombre ?? '—';
                                                $veredicto = $a->veredicto ?? 'pendiente';
                                                
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
                                                <td class="text-center fw-medium">{{ $plan->codigo_plan }}</td>
                                                <td>{{ $plan->nombre_programa }}</td>
                                                <td>{{ $uni }}</td>
                                                
                                                {{-- Veredicto con Badges --}}
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $veredicto_clase }}-subtle text-{{ $veredicto_clase }} p-2">
                                                        {{ $veredicto_texto }}
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    {{ $a->updated_at ? $a->updated_at->format('d/m/Y H:i') : '—' }}
                                                </td>
                                            </tr>

                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-4 text-muted">
                                                <i class="ri-search-eye-line me-1"></i>
                                                No se encontraron asignaciones que coincidan con los filtros.
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