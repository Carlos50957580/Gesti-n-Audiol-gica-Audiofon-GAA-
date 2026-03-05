<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Registrar Nueva Universidad / IES</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.universidades.index') }}">Universidades</a></li>
                                <li class="breadcrumb-item active">Nueva</li>
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
                                <i class="ri-building-line align-middle me-1"></i>
                                Información de la Institución
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.universidades.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <!-- Nombre de la institución -->
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label">
                                            Nombre de la Institución <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" 
                                               name="nombre" 
                                               value="{{ old('nombre') }}"
                                               placeholder="Ej: Universidad Autónoma de Santo Domingo"
                                               required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Tipo de institución -->
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo" class="form-label">
                                            Tipo de Institución <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('tipo') is-invalid @enderror" 
                                                id="tipo" 
                                                name="tipo" 
                                                required>
                                            <option value="">-- Selecciona tipo --</option>
                                            @foreach($tipos as $key => $valor)
                                                <option value="{{ $key }}" {{ old('tipo') == $key ? 'selected' : '' }}>
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Fecha de creación -->
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_creacion" class="form-label">
                                            Fecha de Creación
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('fecha_creacion') is-invalid @enderror" 
                                               id="fecha_creacion" 
                                               name="fecha_creacion"
                                               value="{{ old('fecha_creacion') }}">
                                        @error('fecha_creacion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Fecha oficial de creación de la institución</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Provincia -->
                                    <div class="col-md-6 mb-3">
                                        <label for="provincia" class="form-label">
                                            Provincia <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('provincia') is-invalid @enderror" 
                                               id="provincia" 
                                               name="provincia" 
                                               value="{{ old('provincia') }}"
                                               placeholder="Ej: Santo Domingo"
                                               required>
                                        @error('provincia')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Municipio -->
                                    <div class="col-md-6 mb-3">
                                        <label for="municipio" class="form-label">
                                            Municipio (código) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('municipio') is-invalid @enderror" 
                                               id="municipio" 
                                               name="municipio" 
                                               value="{{ old('municipio') }}"
                                               placeholder="Ej: 0101"
                                               maxlength="4"
                                               required>
                                        @error('municipio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Código de 4 dígitos del municipio</small>
                                    </div>
                                </div>

                                <!-- Información adicional -->
                                <div class="alert alert-info border-0 mb-4" role="alert">
                                    <i class="ri-information-line me-2"></i>
                                    <strong>Nota:</strong> El código provisional se generará automáticamente al guardar. El estado inicial será "Pendiente" hasta su aprobación.
                                </div>

                                <!-- Botones de acción -->
                                <div class="text-end mt-4">
                                    <a href="{{ route('admin.universidades.index') }}" class="btn btn-light me-2">
                                        <i class="ri-close-line align-middle me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line align-middle me-1"></i>
                                        Guardar Universidad
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
        // Validación del código de municipio (solo números)
        const municipioInput = document.getElementById('municipio');
        
        municipioInput.addEventListener('input', function(e) {
            // Remover caracteres no numéricos
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limitar a 4 dígitos
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });

        // Auto-capitalizar el nombre de la provincia
        const provinciaInput = document.getElementById('provincia');
        
        provinciaInput.addEventListener('blur', function() {
            this.value = this.value
                .toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        });
    </script>
    @endpush
</x-app-layout>