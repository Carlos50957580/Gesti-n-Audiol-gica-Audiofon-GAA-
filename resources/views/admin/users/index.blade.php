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
                    <div class="col-md-3">
                        <label class="form-label">Rol</label>
                        <select name="role_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($roles ?? [] as $role)
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
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary w-100">
                            <i class="ri-refresh-line"></i> Limpiar
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
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-soft-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewUserModal"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role->name ?? 'Sin rol' }}"
                                                data-branch="{{ $user->branch->name ?? 'N/A' }}"
                                                data-is_doctor="{{ $user->is_doctor ? 'Sí' : 'No' }}"
                                                title="Ver">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <a href="{{ route('admin.usuarios.edit', $user) }}" class="btn btn-soft-primary btn-sm" title="Editar">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" 
                                                    onclick="return confirm('¿Estás seguro de eliminar este usuario?')" title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" class="btn btn-soft-secondary btn-sm" title="No puedes eliminarte a ti mismo" disabled>
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
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
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const email = button.getAttribute('data-email');
                const role = button.getAttribute('data-role');
                const branch = button.getAttribute('data-branch');
                const isDoctor = button.getAttribute('data-is_doctor');
                
                document.getElementById('viewUserName').textContent = name;
                document.getElementById('viewUserEmail').textContent = email;
                document.getElementById('viewUserRole').textContent = role;
                
                const typeElement = document.getElementById('viewUserType');
                if (isDoctor === 'Sí') {
                    typeElement.innerHTML = '<span class="badge bg-success"><i class="ri-stethoscope-line me-1"></i> Médico</span>';
                } else {
                    typeElement.innerHTML = '<span class="badge bg-secondary"><i class="ri-user-line me-1"></i> No Médico</span>';
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