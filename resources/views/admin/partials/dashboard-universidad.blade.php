<div class="page-content" style="padding-top: 0;">
    <div class="container-fluid pt-3">

        {{-- 🔹 Encabezado --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="ri-building-line align-bottom me-1 text-primary"></i>
                        Panel de la Universidad
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                            <li class="breadcrumb-item active">Panel</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Información general --}}
        <div class="alert alert-info shadow-sm">
            <strong>{{ $user->university->nombre ?? 'Universidad sin nombre' }}</strong><br>
            <span class="text-muted">
                Total de programas registrados: <b>{{ $totalPlanes }}</b> |
                Estado general: 
                @if ($aprobados > 0)
                    <span class="badge bg-success-subtle text-success">Aprobado</span>
                @elseif ($revision > 0)
                    <span class="badge bg-info-subtle text-info">En revisión</span>
                @elseif ($rechazados > 0)
                    <span class="badge bg-danger-subtle text-danger">Con observaciones</span>
                @else
                    <span class="badge bg-warning-subtle text-warning">Pendiente</span>
                @endif
            </span>
        </div>

        {{-- 🔹 Tarjetas estadísticas --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body text-center">
                        <p class="text-uppercase fw-semibold text-muted mb-0">Total Planes</p>
                        <h4 class="mb-0">{{ $totalPlanes }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body text-center">
                        <p class="text-uppercase fw-semibold text-muted mb-0">Pendientes</p>
                        <h4 class="text-warning mb-0">{{ $pendientes }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body text-center">
                        <p class="text-uppercase fw-semibold text-muted mb-0">En Revisión</p>
                        <h4 class="text-info mb-0">{{ $revision }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body text-center">
                        <p class="text-uppercase fw-semibold text-muted mb-0">Aprobados</p>
                        <h4 class="text-success mb-0">{{ $aprobados }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Gráficas --}}
        <div class="row">
            <div class="col-xl-6">
                <div class="card h-100">
                    <div class="card-header bg-soft-primary">
                        <h5 class="card-title mb-0">
                            <i class="ri-bar-chart-line me-1 text-primary"></i> Programas por Estado
                        </h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="chartPlanesEstado"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card h-100">
                    <div class="card-header bg-soft-info">
                        <h5 class="card-title mb-0">
                            <i class="ri-pie-chart-2-line me-1 text-info"></i> Distribución de Programas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="chartPlanesUniversidad"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Tabla de últimos planes --}}
        <div class="row">
            <div class="col-12">
                <div class="card mt-4">
                    <div class="card-header bg-soft-secondary">
                        <h5 class="card-title mb-0">
                            <i class="ri-folder-line me-1 text-secondary"></i>
                            Últimos Planes Sometidos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre del Programa</th>
                                        <th>Estado</th>
                                        <th>Fecha de Creación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ultimosPlanes as $plan)
                                        <tr>
                                            <td>{{ $plan->codigo_plan }}</td>
                                            <td>{{ $plan->nombre_programa }}</td>
                                            <td>
                                                @php
                                                    $color = match($plan->estado) {
                                                        'pendiente' => 'warning',
                                                        'en_revision' => 'info',
                                                        'aprobado' => 'success',
                                                        'rechazado' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">
                                                    {{ ucfirst($plan->estado) }}
                                                </span>
                                            </td>
                                            <td>{{ $plan->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                No se han registrado planes aún.
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

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Planes por estado
    new Chart(document.getElementById('chartPlanesEstado'), {
        type: 'bar',
        data: {
            labels: ['Pendiente', 'En Revisión', 'Aprobado', 'Rechazado'],
            datasets: [{
                label: 'Cantidad',
                data: [
                    {{ $planesPorEstado['pendiente'] }},
                    {{ $planesPorEstado['revision'] }},
                    {{ $planesPorEstado['aprobado'] }},
                    {{ $planesPorEstado['rechazado'] }}
                ],
                backgroundColor: ['#f7b84b','#4b93e6','#28a745','#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

     // Gráfico circular (solo su universidad)
    new Chart(document.getElementById('chartPlanesUniversidad'), {
        type: 'pie',
        data: {
            labels: @json(array_keys($planesPorUniversidad)),
            datasets: [{
                data: @json(array_values($planesPorUniversidad)),
                backgroundColor: ['#3b82f6','#22c55e','#ef4444','#f59e0b']
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
