<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Editar Universidad / IES</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.universidades.index') }}">Universidades</a></li>
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
                                <i class="ri-building-line align-middle me-1"></i>
                                Información de la Institución
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.universidades.update', $universidad) }}" method="POST">
                                @csrf
                                @method('PUT')

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
                                               value="{{ old('nombre', $universidad->nombre) }}"
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
                                            @foreach($tipos as $key => $valor)
                                                <option value="{{ $key }}" {{ old('tipo', $universidad->tipo) === $key ? 'selected' : '' }}>
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
                                               value="{{ old('fecha_creacion', $universidad->fecha_creacion) }}">
                                        @error('fecha_creacion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                               value="{{ old('provincia', $universidad->provincia) }}"
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
                                               value="{{ old('municipio', $universidad->municipio) }}"
                                               placeholder="Ej: 0101"
                                               maxlength="4"
                                               required>
                                        @error('municipio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Estado -->
                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label">
                                            Estado <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('estado') is-invalid @enderror" 
                                                id="estado" 
                                                name="estado" 
                                                required>
                                            <option value="pendiente" {{ old('estado', $universidad->estado) === 'pendiente' ? 'selected' : '' }}>
                                                <i class="ri-time-line"></i> Pendiente
                                            </option>
                                            <option value="aprobado" {{ old('estado', $universidad->estado) === 'aprobado' ? 'selected' : '' }}>
                                                <i class="ri-checkbox-circle-line"></i> Aprobado
                                            </option>
                                            <option value="rechazado" {{ old('estado', $universidad->estado) === 'rechazado' ? 'selected' : '' }}>
                                                <i class="ri-close-circle-line"></i> Rechazado
                                            </option>
                                        </select>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Estado actual de aprobación</small>
                                    </div>

                                    <!-- Espacio para alineación -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado Actual</label>
                                        <div class="pt-2">
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
                                            <span class="badge {{ $estadoClasses[$universidad->estado] ?? 'bg-secondary-subtle text-secondary' }} fs-6">
                                                <i class="{{ $estadoIcons[$universidad->estado] ?? 'ri-question-line' }} align-middle me-1"></i>
                                                {{ ucfirst($universidad->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Códigos (solo lectura) -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="codigo_provisional" class="form-label">
                                            Código Provisional
                                        </label>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               id="codigo_provisional" 
                                               value="{{ $universidad->codigo_provisional }}" 
                                               readonly>
                                        <small class="text-muted">
                                            <i class="ri-lock-line"></i> Generado automáticamente
                                        </small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="codigo_definitivo" class="form-label">
                                            Código Definitivo
                                        </label>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               id="codigo_definitivo" 
                                               value="{{ $universidad->codigo_definitivo ?? '—' }}" 
                                               readonly>
                                        <small class="text-muted">
                                            <i class="ri-lock-line"></i> Se asigna al aprobar
                                        </small>
                                    </div>
                                </div>

                                <!-- Información de auditoría -->
                                <div class="alert alert-info border-0 mb-4" role="alert">
                                    <i class="ri-information-line me-2"></i>
                                    <strong>Registrada:</strong> {{ $universidad->created_at->format('d/m/Y H:i') }}
                                    @if($universidad->updated_at != $universidad->created_at)
                                        <span class="mx-2">|</span>
                                        <strong>Última actualización:</strong> {{ $universidad->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </div>

                                <!-- Botones de acción -->
                                <div class="text-end mt-4">
                                    <a href="{{ route('admin.universidades.index') }}" class="btn btn-light me-2">
                                        <i class="ri-close-line align-middle me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-refresh-line align-middle me-1"></i>
                                        Actualizar Universidad
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

        // Actualizar badge visual cuando cambia el estado
        const estadoSelect = document.getElementById('estado');
        const estadoBadge = document.querySelector('.badge.fs-6');
        
        estadoSelect.addEventListener('change', function() {
            const estado = this.value;
            
            // Remover clases anteriores
            estadoBadge.className = 'badge fs-6';
            estadoBadge.querySelector('i').className = '';
            
            // Agregar nuevas clases según el estado
            if (estado === 'pendiente') {
                estadoBadge.classList.add('bg-warning-subtle', 'text-warning');
                estadoBadge.querySelector('i').className = 'ri-time-line align-middle me-1';
                estadoBadge.lastChild.textContent = ' Pendiente';
            } else if (estado === 'aprobado') {
                estadoBadge.classList.add('bg-success-subtle', 'text-success');
                estadoBadge.querySelector('i').className = 'ri-checkbox-circle-line align-middle me-1';
                estadoBadge.lastChild.textContent = ' Aprobado';
            } else if (estado === 'rechazado') {
                estadoBadge.classList.add('bg-danger-subtle', 'text-danger');
                estadoBadge.querySelector('i').className = 'ri-close-circle-line align-middle me-1';
                estadoBadge.lastChild.textContent = ' Rechazado';
            }
        });
    </script>
    @endpush
</x-app-layout>