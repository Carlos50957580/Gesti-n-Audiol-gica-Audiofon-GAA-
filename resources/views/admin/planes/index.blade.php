<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Gestión de Planes de Estudio</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Planes de Estudio</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Lista de Planes de Estudio</h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.planes.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Nuevo Plan
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 100px;">Código</th>
                                            <th scope="col">Programa</th>
                                            <th scope="col">Universidad</th>
                                            <th scope="col">Nivel</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col" class="text-center" style="width: 250px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($planes as $plan)
                                        <tr>
                                            <td class="fw-medium text-center">{{ $plan->codigo_plan }}</td>
                                            <td>{{ $plan->nombre_programa }}</td>
                                            <td>{{ $plan->university->nombre ?? '-' }}</td>
                                            <td>{{ $plan->nivel_academico ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $estado = $plan->estado;
                                                    $estadoClasses = [
                                                        'pendiente'    => 'bg-warning-subtle text-warning',
                                                        'en_revision'  => 'bg-info-subtle text-info',
                                                        'aprobado'     => 'bg-success-subtle text-success',
                                                        'rechazado'    => 'bg-danger-subtle text-danger',
                                                    ];
                                                    $estadoIcons = [
                                                        'pendiente'    => 'ri-time-line',
                                                        'en_revision'  => 'ri-eye-line',
                                                        'aprobado'     => 'ri-checkbox-circle-line',
                                                        'rechazado'    => 'ri-close-circle-line',
                                                    ];
                                                    $estadoTexto = ucwords(str_replace('_', ' ', $estado));
                                                @endphp
                                                <span class="badge {{ $estadoClasses[$estado] ?? 'bg-secondary-subtle text-secondary' }}">
                                                    <i class="{{ $estadoIcons[$estado] ?? 'ri-question-line' }} align-middle me-1"></i>
                                                    {{ $estadoTexto }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center flex-wrap">
                                                    
                                                    {{-- Botón Editar --}}
                                                    <a href="{{ route('admin.planes.edit', $plan) }}" 
                                                        class="btn btn-sm btn-soft-warning"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="Editar">
                                                        <i class="ri-pencil-fill"></i>
                                                    </a>

                                                    {{-- Botón Asignar Evaluador --}}
                                                    <a href="{{ route('admin.planes.asignar', $plan->id) }}" 
                                                        class="btn btn-sm btn-soft-info"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="Asignar Evaluador">
                                                        <i class="ri-user-add-line"></i>
                                                    </a>

                                                    {{-- Botón Eliminar (con SweetAlert2) --}}
                                                    <form action="{{ route('admin.planes.destroy', $plan) }}" 
                                                        method="POST" 
                                                        class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-soft-danger btn-delete"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="top" 
                                                                title="Eliminar"
                                                                data-plan-name="{{ $plan->nombre_programa }}">
                                                            <i class="ri-delete-bin-fill"></i>
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="py-4">
                                                    <i class="ri-survey-line display-5 text-muted"></i>
                                                    <p class="text-muted mt-3 mb-0">No hay planes de estudio registrados.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Paginación (si usas paginate() en tu controlador) --}}
                            @if(method_exists($planes, 'hasPages') && $planes->hasPages())
                            <div class="d-flex justify-content-end mt-3">
                                {{ $planes->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // SweetAlert2 para eliminar plan de estudio
        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('.delete-form');
                const planName = this.getAttribute('data-plan-name');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `Se eliminará el plan de estudio <strong>${planName}</strong>.<br>Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Color Danger de Bootstrap/Velzon
                    cancelButtonColor: '#6c757d', // Color Secondary de Bootstrap/Velzon
                    confirmButtonText: '<i class="ri-delete-bin-line me-1"></i> Sí, eliminar',
                    cancelButtonText: '<i class="ri-close-line me-1"></i> Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>