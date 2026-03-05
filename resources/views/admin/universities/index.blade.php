<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Gestión de Universidades / IES</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Universidades</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Mensaje de error --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Lista de Universidades</h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.universidades.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Nueva Universidad
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 60px;">ID</th>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Provincia</th>
                                            <th scope="col">Municipio</th>
                                            <th scope="col">Código Prov.</th>
                                            <th scope="col">Código Def.</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col" class="text-center" style="width: 200px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($universidades as $u)
                                        <tr>
                                            <td class="fw-medium">{{ $u->id }}</td>
                                            <td>{{ $u->nombre }}</td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    {{ $u->tipo }}
                                                </span>
                                            </td>
                                            <td>{{ $u->provincia }}</td>
                                            <td>{{ $u->municipio }}</td>
                                            <td>
                                                <code class="text-primary">{{ $u->codigo_provisional }}</code>
                                            </td>
                                            <td>
                                                @if($u->codigo_definitivo)
                                                    <code class="text-success">{{ $u->codigo_definitivo }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $estadoClasses = [
                                                        'pendiente' => 'bg-warning-subtle text-warning',
                                                        'aprobado' => 'bg-success-subtle text-success',
                                                        'rechazado' => 'bg-danger-subtle text-danger',
                                                    ];
                                                    $estadoIcons = [
                                                        'pendiente' => 'ri-time-line',
                                                        'aprobado' => 'ri-checkbox-circle-line',
                                                        'rechazado' => 'ri-close-circle-line',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $estadoClasses[$u->estado] ?? 'bg-secondary-subtle text-secondary' }}">
                                                    <i class="{{ $estadoIcons[$u->estado] ?? 'ri-question-line' }} align-middle me-1"></i>
                                                    {{ ucfirst($u->estado) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center flex-wrap">
                                                    {{-- Botón Editar --}}
                                                    <a href="{{ route('admin.universidades.edit', $u) }}" 
                                                       class="btn btn-sm btn-soft-warning"
                                                       data-bs-toggle="tooltip" 
                                                       data-bs-placement="top" 
                                                       title="Editar">
                                                        <i class="ri-pencil-fill"></i>
                                                    </a>

                                                    {{-- Botones Aprobar/Rechazar solo si está pendiente --}}
                                                    @if($u->estado === 'pendiente')
                                                        <form action="{{ route('admin.universidades.aprobar', $u) }}" 
                                                              method="POST" 
                                                              class="d-inline aprobar-form">
                                                            @csrf 
                                                            @method('PUT')
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-soft-success btn-aprobar"
                                                                    data-bs-toggle="tooltip" 
                                                                    data-bs-placement="top" 
                                                                    title="Aprobar"
                                                                    data-universidad-name="{{ $u->nombre }}">
                                                                <i class="ri-check-line"></i>
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('admin.universidades.rechazar', $u) }}" 
                                                              method="POST" 
                                                              class="d-inline rechazar-form">
                                                            @csrf 
                                                            @method('PUT')
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-soft-danger btn-rechazar"
                                                                    data-bs-toggle="tooltip" 
                                                                    data-bs-placement="top" 
                                                                    title="Rechazar"
                                                                    data-universidad-name="{{ $u->nombre }}">
                                                                <i class="ri-close-line"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Botón Eliminar --}}
                                                    <form action="{{ route('admin.universidades.destroy', $u) }}" 
                                                          method="POST" 
                                                          class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-soft-secondary btn-delete"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="top" 
                                                                title="Eliminar"
                                                                data-universidad-name="{{ $u->nombre }}">
                                                            <i class="ri-delete-bin-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="py-4">
                                                    <i class="ri-building-line display-5 text-muted"></i>
                                                    <p class="text-muted mt-3 mb-0">No hay universidades registradas.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Paginación --}}
                            @if(method_exists($universidades, 'hasPages') && $universidades->hasPages())
                            <div class="d-flex justify-content-end mt-3">
                                {{ $universidades->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    @push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // SweetAlert2 para aprobar universidad
        document.querySelectorAll('.btn-aprobar').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('.aprobar-form');
                const universidadName = this.getAttribute('data-universidad-name');
                
                Swal.fire({
                    title: '¿Aprobar universidad?',
                    html: `Se aprobará la universidad <strong>${universidadName}</strong>.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0ab39c',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-check-line me-1"></i> Sí, aprobar',
                    cancelButtonText: '<i class="ri-close-line me-1"></i> Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-success',
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

        // SweetAlert2 para rechazar universidad
        document.querySelectorAll('.btn-rechazar').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('.rechazar-form');
                const universidadName = this.getAttribute('data-universidad-name');
                
                Swal.fire({
                    title: '¿Rechazar universidad?',
                    html: `Se rechazará la universidad <strong>${universidadName}</strong>.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f06548',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-close-line me-1"></i> Sí, rechazar',
                    cancelButtonText: '<i class="ri-arrow-left-line me-1"></i> Cancelar',
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

        // SweetAlert2 para eliminar universidad
        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('.delete-form');
                const universidadName = this.getAttribute('data-universidad-name');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `Se eliminará la universidad <strong>${universidadName}</strong>.<br>Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
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