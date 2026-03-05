
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            {{-- 🔹 Encabezado --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">
                            <i class="ri-dashboard-line align-bottom me-1 text-primary"></i>
                            Panel de Control
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

            {{-- 🔹 Tarjetas Resumen --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-semibold text-muted mb-0">Total Planes</p>
                                    <h4 class="mb-0"><span class="counter-value" data-target="{{ $totalPlanes }}">{{ $totalPlanes }}</span></h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-2">
                                        <i class="ri-book-2-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-semibold text-muted mb-0">Universidades</p>
                                    <h4 class="mb-0"><span class="counter-value" data-target="{{ $totalUniversidades }}">{{ $totalUniversidades }}</span></h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success text-success rounded-circle fs-2">
                                        <i class="ri-bank-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-semibold text-muted mb-0">Planes Aprobados</p>
                                    <h4 class="mb-0 text-success"><span class="counter-value" data-target="{{ $aprobados }}">{{ $aprobados }}</span></h4>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success text-success rounded-circle fs-2">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Puedes agregar más tarjetas si lo deseas --}}
            </div>

            {{-- 🔹 Gráficos: Filas de misma altura forzada --}}
            <div class="row">
                <div class="col-xl-6">
                    {{-- Clase h-100 para estirar la tarjeta --}}
                    <div class="card h-100"> 
                        <div class="card-header bg-soft-info">
                            <h5 class="card-title mb-0">
                                <i class="ri-bar-chart-2-line me-1 text-info"></i>
                                Planes por Estado
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Contenedor con altura forzada para que Chart.js se ajuste --}}
                            <div style="height: 350px;"> 
                                <canvas id="chartEstados"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    {{-- Clase h-100 para estirar la tarjeta --}}
                    <div class="card h-100"> 
                        <div class="card-header bg-soft-primary">
                            <h5 class="card-title mb-0">
                                <i class="ri-pie-chart-2-line me-1 text-primary"></i>
                                Planes por Universidad
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Mismo contenedor con altura forzada --}}
                            <div style="height: 350px;"> 
                                <canvas id="chartUniversidades"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            {{-- 🔹 Últimos Planes --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-soft-secondary">
                            <h5 class="card-title mb-0">
                                <i class="ri-timeline-view me-1 text-secondary"></i>
                                Últimos Planes Sometidos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Programa</th>
                                            <th>Universidad</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ultimosPlanes as $plan)
                                            <tr>
                                                <td>{{ $plan->nombre_programa }}</td>
                                                <td>{{ $plan->university->nombre }}</td>
                                                <td>
                                                    @php
                                                        $estadoColor = match($plan->estado) {
                                                            'pendiente' => 'warning',
                                                            'en_revision' => 'info',
                                                            'aprobado' => 'success',
                                                            'rechazado' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $estadoColor }}-subtle text-{{ $estadoColor }}">
                                                        {{ ucfirst($plan->estado) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Script de Chart.js y Lógica de Altura --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ----------- PLANES POR ESTADO -------------
        const ctx1 = document.getElementById('chartEstados');
        new Chart(ctx1, {
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
                maintainAspectRatio: false, // <-- CLAVE para que tome el alto del DIV (350px)
                plugins: { legend: { display: false } }
            }
        });

        // ----------- PLANES POR UNIVERSIDAD -------------
        const ctx2 = document.getElementById('chartUniversidades');
        const labelsU = [
            @foreach($planesPorUniversidad as $p)
                "{{ $p->university->nombre }}",
            @endforeach
        ];
        const dataU = [
            @foreach($planesPorUniversidad as $p)
                {{ $p->total }},
            @endforeach
        ];

        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: labelsU,
                datasets: [{
                    data: dataU,
                    backgroundColor: ['#3b82f6','#22c55e','#ef4444','#f59e0b','#6366f1']
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false // <-- CLAVE para que tome el alto del DIV (350px)
            }
        });
    </script>
