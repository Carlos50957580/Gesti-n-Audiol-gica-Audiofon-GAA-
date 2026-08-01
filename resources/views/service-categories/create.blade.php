@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Nueva Categoría de Servicio</h4>
            <div class="page-title-right">
                <a href="{{ route('service-categories.index') }}" class="btn btn-secondary">
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
                <form action="{{ route('service-categories.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Ej: Consultas Médicas" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Icono -->
                        <div class="col-md-6">
                            <label class="form-label">Icono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                       value="{{ old('icon', 'ri-folder-line') }}" placeholder="ri-folder-line">
                            </div>
                            <small class="text-muted">Usa iconos de <a href="https://remixicon.com/" target="_blank">Remix Icon</a></small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div class="col-md-6">
                            <label class="form-label">Color</label>
                            <div class="input-group">
                                <span class="input-group-text" id="colorPreview" style="background-color: {{ old('color', '#405189') }}; width: 40px;"></span>
                                <input type="color" name="color" class="form-control form-control-color" 
                                       value="{{ old('color', '#405189') }}" style="padding: 3px; max-width: 60px;">
                                <input type="text" id="colorText" class="form-control" 
                                       value="{{ old('color', '#405189') }}" placeholder="#405189">
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Historia Clínica -->
                        <div class="col-md-6">
                            <label class="form-label">Requisitos</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="requires_clinical_record" value="0">
                                <input type="checkbox" name="requires_clinical_record" class="form-check-input" id="requiresClinicalRecord" 
                                       value="1" {{ old('requires_clinical_record', 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requiresClinicalRecord">
                                    Requiere Historia Clínica
                                </label>
                                <small class="d-block text-muted">Los servicios de esta categoría requerirán historia clínica</small>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" 
                                       value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    Activo
                                </label>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" placeholder="Descripción de la categoría">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Guardar Categoría
                            </button>
                            <a href="{{ route('service-categories.index') }}" class="btn btn-secondary">
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
                <h5 class="card-title mb-0">Vista Previa</h5>
            </div>
            <div class="card-body text-center">
                <div class="card d-inline-block" style="min-width: 200px;">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle fs-16" id="previewBadge"
                                          style="background-color: {{ old('color', '#405189') }}20; color: {{ old('color', '#405189') }}">
                                        <i class="{{ old('icon', 'ri-folder-line') }}" id="previewIcon"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 text-start">
                                <h5 class="mb-1" id="previewName">{{ old('name', 'Nombre') }}</h5>
                                <p class="text-muted mb-0" id="previewDescription">{{ old('description', 'Descripción') }}</p>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Historia Clínica:</span>
                                <span class="badge {{ old('requires_clinical_record', 0) ? 'bg-success' : 'bg-secondary' }}" id="previewHc">
                                    {{ old('requires_clinical_record', 0) ? 'Sí' : 'No' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted">Estado:</span>
                                <span class="badge {{ old('is_active', 1) ? 'bg-success' : 'bg-danger' }}" id="previewStatus">
                                    {{ old('is_active', 1) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vista previa en tiempo real
        const nameInput = document.querySelector('input[name="name"]');
        const descInput = document.querySelector('textarea[name="description"]');
        const iconInput = document.querySelector('input[name="icon"]');
        const colorInput = document.querySelector('input[name="color"]');
        const colorText = document.getElementById('colorText');
        const previewName = document.getElementById('previewName');
        const previewDesc = document.getElementById('previewDescription');
        const previewIcon = document.getElementById('previewIcon');
        const previewBadge = document.getElementById('previewBadge');
        const previewColor = document.getElementById('colorPreview');
        const hcCheckbox = document.getElementById('requiresClinicalRecord');
        const previewHc = document.getElementById('previewHc');
        const activeCheckbox = document.getElementById('isActive');
        const previewStatus = document.getElementById('previewStatus');

        // Nombre
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                previewName.textContent = this.value || 'Nombre';
            });
        }

        // Descripción
        if (descInput) {
            descInput.addEventListener('input', function() {
                previewDesc.textContent = this.value || 'Descripción';
            });
        }

        // Icono
        if (iconInput) {
            iconInput.addEventListener('input', function() {
                previewIcon.className = this.value || 'ri-folder-line';
            });
        }

        // Color
        if (colorInput && colorText && previewBadge && previewColor) {
            colorInput.addEventListener('input', function() {
                const color = this.value || '#405189';
                previewBadge.style.backgroundColor = color + '20';
                previewBadge.style.color = color;
                previewColor.style.backgroundColor = color;
                colorText.value = color;
            });

            colorText.addEventListener('input', function() {
                const color = this.value || '#405189';
                colorInput.value = color;
                previewBadge.style.backgroundColor = color + '20';
                previewBadge.style.color = color;
                previewColor.style.backgroundColor = color;
            });
        }

        // Historia Clínica
        if (hcCheckbox && previewHc) {
            hcCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    previewHc.textContent = 'Sí';
                    previewHc.className = 'badge bg-success';
                } else {
                    previewHc.textContent = 'No';
                    previewHc.className = 'badge bg-secondary';
                }
            });
        }

        // Estado
        if (activeCheckbox && previewStatus) {
            activeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    previewStatus.textContent = 'Activo';
                    previewStatus.className = 'badge bg-success';
                } else {
                    previewStatus.textContent = 'Inactivo';
                    previewStatus.className = 'badge bg-danger';
                }
            });
        }

        // Inicializar valores
        if (hcCheckbox) {
            hcCheckbox.dispatchEvent(new Event('change'));
        }
        if (activeCheckbox) {
            activeCheckbox.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection