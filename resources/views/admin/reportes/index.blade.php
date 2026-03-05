<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Reporte de Planes</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Reportes</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    {{-- FILTROS Y EXPORTACIÓN --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-filter-3-line align-middle me-1"></i>
                                Opciones de Filtrado y Exportación
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <div class="row g-3">
                                    {{-- Filtro por Estado --}}
                                    <div class="col-md-3">
                                        <label for="filter_estado" class="form-label">Estado</label>
                                        <select name="estado" id="filter_estado" class="form-select">
                                            <option value="">-- Todos --</option>
                                            @php $estados = ['pendiente', 'en_revision', 'aprobado', 'rechazado']; @endphp
                                            @foreach ($estados as $est)
                                                <option value="{{ $est }}" {{ request('estado') == $est ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $est)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filtro por Universidad --}}
                                    <div class="col-md-3">
                                        <label for="filter_universidad" class="form-label">Universidad</label>
                                        <select name="universidad_id" id="filter_universidad" class="form-select">
                                            <option value="">-- Todas --</option>
                                            @foreach ($universidades as $u)
                                                <option value="{{ $u->id }}" {{ request('universidad_id') == $u->id ? 'selected' : '' }}>
                                                    {{ $u->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filtro por Nivel --}}
                                    <div class="col-md-3">
                                        <label for="filter_nivel" class="form-label">Nivel Académico</label>
                                        <select name="nivel_academico" id="filter_nivel" class="form-select">
                                            <option value="">-- Todos --</option>
                                            @php $niveles = ['Grado', 'Maestría', 'Doctorado']; @endphp
                                            @foreach ($niveles as $nivel)
                                                <option value="{{ $nivel }}" {{ request('nivel_academico') == $nivel ? 'selected' : '' }}>
                                                    {{ $nivel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    {{-- Botón de Filtrar --}}
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-search-line align-bottom me-1"></i> Aplicar Filtros
                                        </button>
                                    </div>

                                    {{-- Botones de Exportar --}}
                                    <div class="col-md-12 mt-3 pt-2 border-top">
                                        @php
                                            $prefix = auth()->user()->role->name === 'admin' ? 'admin' : 'mescyt';
                                            $current_params = http_build_query(request()->except(['page'])); // Mantener filtros al exportar
                                        @endphp
                                        
                                        <h6 class="mb-2">Exportar Resultados:</h6>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route($prefix.'.reportes.excel', $current_params) }}" 
                                               class="btn btn-success waves-effect waves-light">
                                                <i class="ri-file-excel-line align-bottom me-1"></i> Exportar Excel
                                            </a>

                                            <a href="{{ route($prefix.'.reportes.pdf', $current_params) }}" 
                                               class="btn btn-danger waves-effect waves-light">
                                                <i class="ri-file-pdf-line align-bottom me-1"></i> Exportar PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- TABLA DE RESULTADOS --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="text-center">Código</th>
                                            <th scope="col">Programa</th>
                                            <th scope="col">Universidad</th>
                                            <th scope="col">Evaluador</th>
                                            <th scope="col" class="text-center">Veredicto (Evaluador)</th>
                                            <th scope="col" class="text-center">Estado (MESCyT)</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($planes as $p)

                                        @php
                                            // Asignación y Veredicto
                                            $asig = $p->asignacion;
                                            $veredicto = $asig->veredicto ?? 'sin asignar';
                                            $evaluador = $asig->evaluador->name ?? 'Sin asignar';

                                            // Clases de Badge para Veredicto
                                            $veredicto_clase = match($veredicto) {
                                                'pendiente' => 'warning',
                                                'en_revision' => 'info',
                                                'aprobado' => 'success',
                                                'rechazado' => 'danger',
                                                default => 'secondary',
                                            };

                                            // Clases de Badge para Estado (MESCyT)
                                            $estado_clase = match($p->estado) {
                                                'pendiente' => 'warning',
                                                'en_revision' => 'info',
                                                'aprobado' => 'success',
                                                'rechazado' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp

                                        <tr>
                                            <td class="text-center">{{ $p->codigo_plan }}</td>
                                            <td>{{ $p->nombre_programa }}</td>
                                            <td>{{ $p->university->nombre }}</td>
                                            <td>{{ $evaluador }}</td>

                                            {{-- Veredicto con Badge --}}
                                            <td class="text-center">
                                                <span class="badge bg-{{ $veredicto_clase }}-subtle text-{{ $veredicto_clase }} p-2">
                                                    {{ ucwords(str_replace('_', ' ', $veredicto)) }}
                                                </span>
                                            </td>

                                            {{-- Estado del plan con Badge --}}
                                            <td class="text-center">
                                                <span class="badge bg-{{ $estado_clase }}-subtle text-{{ $estado_clase }} p-2">
                                                    {{ ucwords(str_replace('_', ' ', $p->estado)) }}
                                                </span>
                                            </td>

                                        </tr>

                                        @empty
                                        <tr>
                                            <td colspan="6" class="p-4 text-center text-muted">
                                                <i class="ri-file-excel-2-line me-1"></i> No se encontraron planes que coincidan con los filtros.
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