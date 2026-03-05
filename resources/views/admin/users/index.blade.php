<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Gestión de Usuarios</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Usuarios</li>
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

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Lista de Usuarios</h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Nuevo Usuario
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
                                            <th scope="col">Email</th>
                                            <th scope="col">Rol</th>
                                            <th scope="col" class="text-center" style="width: 200px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                        <tr>
                                            <td class="fw-medium">{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary text-uppercase">
                                                    {{ $user->role->name }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center">
                                                    {{-- Botón Editar --}}
                                                    <a href="{{ route('admin.usuarios.edit', $user) }}" 
                                                       class="btn btn-sm btn-soft-warning"
                                                       data-bs-toggle="tooltip" 
                                                       data-bs-placement="top" 
                                                       title="Editar">
                                                        <i class="ri-pencil-fill"></i>
                                                    </a>

                                                    {{-- Botón Eliminar --}}
                                                    <form action="{{ route('admin.usuarios.destroy', $user) }}" 
                                                          method="POST" 
                                                          class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-soft-danger btn-delete"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="top" 
                                                                title="Eliminar"
                                                                data-user-name="{{ $user->name }}">
                                                            <i class="ri-delete-bin-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="py-4">
                                                    <i class="ri-user-line display-5 text-muted"></i>
                                                    <p class="text-muted mt-3 mb-0">No hay usuarios registrados.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Paginación (solo si usas paginate() en el controlador) --}}
                            @if(method_exists($users, 'hasPages') && $users->hasPages())
                            <div class="d-flex justify-content-end mt-3">
                                {{ $users->links() }}
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
    
    <style>
    /* Separación entre botones en SweetAlert2 */
    .swal2-actions .btn {
        margin: 0 5px;
    }
</style>

    <script>
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // SweetAlert2 para eliminar usuario
        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = this.closest('.delete-form');
                const userName = this.getAttribute('data-user-name');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `Se eliminará el usuario <strong>${userName}</strong>.<br>Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ri-delete-bin-line me-3"></i> Sí, eliminar',
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