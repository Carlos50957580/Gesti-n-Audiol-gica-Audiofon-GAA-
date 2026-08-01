@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Nuevo Usuario</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.usuarios.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmar Password -->
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <!-- Rol -->
                        <div class="col-md-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" id="roleSelect">
                                <option value="">Seleccionar rol</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sucursal -->
                        <div class="col-md-6">
                            <label class="form-label">Sucursal <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                                <option value="">Seleccionar sucursal</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ✅ NUEVO: Es médico -->
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Usuario</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="is_doctor" value="0">
                                <input type="checkbox" name="is_doctor" class="form-check-input" id="isDoctor" 
                                       value="1" {{ old('is_doctor') ? 'checked' : '' }}>
                                <label class="form-check-label" for="isDoctor">
                                    Es Médico
                                </label>
                                <small class="d-block text-muted">
                                    <i class="ri-information-line"></i> 
                                    Marque si este usuario es un médico (podrá ver citas y crear historias clínicas)
                                </small>
                            </div>
                            @error('is_doctor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Guardar Usuario
                            </button>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="ri-information-line me-1"></i>
                    <strong>Roles disponibles:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Admin</strong> - Acceso total al sistema</li>
                        <li><strong>Médico</strong> - Ver citas, crear historias clínicas</li>
                        <li><strong>Recepcionista</strong> - Gestión de pacientes, citas y facturación</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning" id="doctorInfo" style="display: none;">
                    <i class="ri-alert-line me-1"></i>
                    <strong>Nota:</strong> Los médicos solo pueden tener roles de <strong>Admin</strong> o <strong>Médico</strong>.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('roleSelect');
        const isDoctorCheckbox = document.getElementById('isDoctor');
        const doctorInfo = document.getElementById('doctorInfo');
        const allowedDoctorRoles = [1, 4]; // Admin (1) y Médico (4)

        // Validar si el rol seleccionado puede ser médico
        function validateDoctorRole() {
            const roleId = parseInt(roleSelect.value);
            
            if (isDoctorCheckbox.checked && !allowedDoctorRoles.includes(roleId)) {
                doctorInfo.style.display = 'block';
                isDoctorCheckbox.disabled = true;
                isDoctorCheckbox.checked = false;
                alert('El rol seleccionado no puede ser médico. Selecciona Admin o Médico.');
            } else if (isDoctorCheckbox.checked && allowedDoctorRoles.includes(roleId)) {
                doctorInfo.style.display = 'none';
                isDoctorCheckbox.disabled = false;
            } else {
                doctorInfo.style.display = 'none';
                isDoctorCheckbox.disabled = false;
            }
        }

        // Evento cuando cambia el rol
        roleSelect.addEventListener('change', function() {
            validateDoctorRole();
        });

        // Evento cuando cambia el checkbox de médico
        isDoctorCheckbox.addEventListener('change', function() {
            const roleId = parseInt(roleSelect.value);
            if (this.checked && !allowedDoctorRoles.includes(roleId)) {
                doctorInfo.style.display = 'block';
                this.checked = false;
                alert('El rol seleccionado no puede ser médico. Selecciona Admin o Médico.');
            } else {
                doctorInfo.style.display = 'none';
            }
        });

        // Inicializar validación
        validateDoctorRole();
    });
</script>
@endpush
@endsection