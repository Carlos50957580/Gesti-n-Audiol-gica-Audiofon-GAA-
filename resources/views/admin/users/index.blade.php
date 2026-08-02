@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Usuarios del Sistema</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Nuevo Usuario
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-check-line me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" placeholder="Nombre o email..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rol</label>
                        <select name="role_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="is_doctor" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_doctor') == '1' ? 'selected' : '' }}>Médicos</option>
                            <option value="0" {{ request('is_doctor') == '0' ? 'selected' : '' }}>No Médicos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="is_active" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line"></i>
                        </button>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary w-100">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Tipo</th>
                                <th>Sucursal</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <img class="rounded-circle avatar-xs" 
                                                 src="{{ $user->profile_photo 
                                                    ? asset('storage/' . $user->profile_photo) 
                                                    : asset('velzon/assets/images/users/avatar-1.jpg') }}" 
                                                 alt="{{ $user->name }}">
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            @if($user->is_doctor)
                                                <span class="badge bg-success ms-1"><i class="ri-stethoscope-line"></i></span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ ucfirst($user->role->name ?? 'Sin rol') }}</span>
                                </td>
                                <td>
                                    @if($user->is_doctor)
                                        <span class="badge bg-success">
                                            <i class="ri-stethoscope-line me-1"></i> Médico
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="ri-user-line me-1"></i> No Médico
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $user->branch->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($user->trashed())
                                        <span class="badge bg-danger">Eliminado</span>
                                    @elseif($user->is_active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-warning">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Ver -->
                                        <button type="button" class="btn btn-soft-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewUserModal"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role->name ?? 'Sin rol' }}"
                                                data-branch="{{ $user->branch->name ?? 'N/A' }}"
                                                data-is_doctor="{{ $user->is_doctor ? 'Sí' : 'No' }}"
                                                data-is_active="{{ $user->is_active ? 'Activo' : 'Inactivo' }}"
                                                title="Ver">
                                            <i class="ri-eye-line"></i>
                                        </button>

                                        <!-- Editar -->
                                        @if(!$user->trashed())
                                        <a href="{{ route('admin.usuarios.edit', $user) }}" class="btn btn-soft-primary btn-sm" title="Editar">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                        @endif

                                        <!-- Activar/Desactivar -->
                                        @if(!$user->trashed() && $user->id !== auth()->id())
                                            @if($user->is_active)
                                            <form action="{{ route('admin.usuarios.deactivate', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-soft-warning btn-sm" 
                                                        onclick="return confirm('¿Desactivar este usuario?')" title="Desactivar">
                                                    <i class="ri-pause-circle-line"></i>
                                                </button>
                                            </form>
                                            @else
                                            <form action="{{ route('admin.usuarios.activate', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-soft-success btn-sm" 
                                                        onclick="return confirm('¿Activar este usuario?')" title="Activar">
                                                    <i class="ri-play-circle-line"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif

                                        <!-- Eliminar (Soft Delete) -->
                                        @if(!$user->trashed() && $user->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" 
                                                    onclick="return confirm('¿Eliminar este usuario?')" title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                        @endif

                                        <!-- Restaurar (si está eliminado) -->
                                        @if($user->trashed())
                                        <form action="{{ route('admin.usuarios.restore', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-soft-success btn-sm" 
                                                    onclick="return confirm('¿Restaurar este usuario?')" title="Restaurar">
                                                <i class="ri-restart-line"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.usuarios.force-delete', $user->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" 
                                                    onclick="return confirm('¿Eliminar permanentemente este usuario?')" title="Eliminar permanentemente">
                                                <i class="ri-skull-line"></i>
                                            </button>
                                        </form>
                                        @endif

                                        @if($user->id === auth()->id())
                                        <button type="button" class="btn btn-soft-secondary btn-sm" title="No puedes eliminarte a ti mismo" disabled>
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="ri-user-line" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                    No hay usuarios registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img class="rounded-circle avatar-lg" id="viewUserPhoto" 
                         src="{{ asset('velzon/assets/images/users/avatar-1.jpg') }}" 
                         alt="Avatar">
                    <h5 class="mt-2" id="viewUserName">Nombre</h5>
                    <span class="badge bg-primary" id="viewUserRole">Rol</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="fw-medium">Email:</td>
                                <td id="viewUserEmail">email@example.com</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Tipo:</td>
                                <td id="viewUserType"><span class="badge bg-success">Médico</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Estado:</td>
                                <td id="viewUserStatus"><span class="badge bg-success">Activo</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Sucursal:</td>
                                <td id="viewUserBranch">N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal para ver usuario
        const viewModal = document.getElementById('viewUserModal');
        if (viewModal) {
            viewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const name = button.getAttribute('data-name');
                const email = button.getAttribute('data-email');
                const role = button.getAttribute('data-role');
                const branch = button.getAttribute('data-branch');
                const isDoctor = button.getAttribute('data-is_doctor');
                const isActive = button.getAttribute('data-is_active');
                
                document.getElementById('viewUserName').textContent = name;
                document.getElementById('viewUserEmail').textContent = email;
                document.getElementById('viewUserRole').textContent = role;
                
                const typeElement = document.getElementById('viewUserType');
                if (isDoctor === 'Sí') {
                    typeElement.innerHTML = '<span class="badge bg-success"><i class="ri-stethoscope-line me-1"></i> Médico</span>';
                } else {
                    typeElement.innerHTML = '<span class="badge bg-secondary"><i class="ri-user-line me-1"></i> No Médico</span>';
                }
                
                const statusElement = document.getElementById('viewUserStatus');
                if (isActive === 'Activo') {
                    statusElement.innerHTML = '<span class="badge bg-success">Activo</span>';
                } else {
                    statusElement.innerHTML = '<span class="badge bg-warning">Inactivo</span>';
                }
                
                document.getElementById('viewUserBranch').textContent = branch;
                
                // Foto (usar avatar por defecto)
                const photoElement = document.getElementById('viewUserPhoto');
                photoElement.src = "{{ asset('velzon/assets/images/users/avatar-1.jpg') }}";
            });
        }
    });
</script>
@endpush
@endsection