<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Editar Usuario</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                                <li class="breadcrumb-item active">Editar</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-user-settings-line align-middle me-1"></i>
                                Información del Usuario
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Nombre -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            Nombre <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $usuario->name) }}"
                                               placeholder="Ingrese el nombre completo"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            Correo Electrónico <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $usuario->email) }}"
                                               placeholder="ejemplo@correo.com"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Contraseña -->
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            Nueva Contraseña 
                                            <span class="text-muted">(Opcional)</span>
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup">
                                            <input type="password" 
                                                   class="form-control pe-5 password-input @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password"
                                                   placeholder="Dejar en blanco para mantener la actual">
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                    type="button">
                                                <i class="ri-eye-fill align-middle"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Solo completar si desea cambiar la contraseña (mínimo 6 caracteres)</small>
                                    </div>

                                    <!-- Confirmar Contraseña -->
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            Confirmar Nueva Contraseña
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup">
                                            <input type="password" 
                                                   class="form-control pe-5 password-input" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation"
                                                   placeholder="Confirme la nueva contraseña">
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                    type="button">
                                                <i class="ri-eye-fill align-middle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Rol -->
                                    <div class="col-md-6 mb-3">
                                        <label for="role_id" class="form-label">
                                            Rol <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('role_id') is-invalid @enderror" 
                                                id="role_id" 
                                                name="role_id" 
                                                required>
                                            @foreach ($roles as $rol)
                                                <option value="{{ $rol->id }}" 
                                                        {{ old('role_id', $usuario->role_id) == $rol->id ? 'selected' : '' }}>
                                                    {{ ucfirst($rol->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                   

                                <!-- Información adicional -->
                                <div class="alert alert-info border-0 mb-4" role="alert">
                                    <i class="ri-information-line me-2"></i>
                                    <strong>Usuario creado:</strong> {{ $usuario->created_at->format('d/m/Y H:i') }}
                                    @if($usuario->updated_at != $usuario->created_at)
                                        <span class="mx-2">|</span>
                                        <strong>Última actualización:</strong> {{ $usuario->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </div>

                                <!-- Botones de acción -->
                                <div class="text-end mt-4">
                                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light me-2">
                                        <i class="ri-close-line align-middle me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-refresh-line align-middle me-1"></i>
                                        Actualizar Usuario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    @push('scripts')
    <script>
        // Toggle de visibilidad de contraseña
        document.querySelectorAll('.password-addon').forEach(function(button) {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.password-input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ri-eye-fill');
                    icon.classList.add('ri-eye-off-fill');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ri-eye-off-fill');
                    icon.classList.add('ri-eye-fill');
                }
            });
        });

        // Mostrar/ocultar campo de universidad según el rol seleccionado
        const roleSelect = document.getElementById('role_id');
        const universityField = document.getElementById('university_id').closest('.col-md-6');

        function toggleUniversityField() {
            const selectedRole = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
            
            if (selectedRole.includes('universidad')) {
                universityField.style.display = 'block';
            } else {
                universityField.style.display = 'none';
                document.getElementById('university_id').value = '';
            }
        }

        roleSelect.addEventListener('change', toggleUniversityField);

        // Ejecutar al cargar la página
        toggleUniversityField();
    </script>
    @endpush
</x-app-layout>