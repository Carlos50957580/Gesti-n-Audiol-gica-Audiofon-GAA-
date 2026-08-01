@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Editar Impuesto: {{ $tax->name }}</h4>
            <div class="page-title-right">
                <a href="{{ route('taxes.index') }}" class="btn btn-secondary">
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
                <form action="{{ route('taxes.update', $tax) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $tax->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Código -->
                        <div class="col-md-6">
                            <label class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                   value="{{ old('code', $tax->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Porcentaje -->
                        <div class="col-md-6">
                            <label class="form-label">Porcentaje <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="rate" class="form-control @error('rate') is-invalid @enderror" 
                                       value="{{ old('rate', $tax->rate) }}" step="0.01" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Por Defecto -->
                        <div class="col-md-6">
                            <label class="form-label">Configuración</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="is_default" value="0">
                                <input type="checkbox" name="is_default" class="form-check-input" id="isDefault" 
                                       value="1" {{ old('is_default', $tax->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isDefault">
                                    Impuesto por defecto
                                </label>
                                <small class="d-block text-muted">Se aplicará automáticamente a nuevos servicios</small>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" 
                                       value="1" {{ old('is_active', $tax->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    Activo
                                </label>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description', $tax->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Actualizar Impuesto
                            </button>
                            <a href="{{ route('taxes.index') }}" class="btn btn-secondary">
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
                        <h4 id="previewName" class="mb-1">{{ old('name', $tax->name) }}</h4>
                        <code id="previewCode" class="text-muted">{{ old('code', $tax->code) }}</code>
                        <div class="mt-2">
                            <span class="badge bg-info fs-14" id="previewRate">{{ old('rate', $tax->rate) }}%</span>
                        </div>
                        <div class="mt-2">
                            <span class="badge {{ old('is_default', $tax->is_default) ? 'bg-success' : 'bg-secondary' }}" id="previewDefault">
                                {{ old('is_default', $tax->is_default) ? '⭐ Por Defecto' : 'No es por defecto' }}
                            </span>
                        </div>
                        <div class="mt-1">
                            <span class="badge {{ old('is_active', $tax->is_active) ? 'bg-success' : 'bg-danger' }}" id="previewStatus">
                                {{ old('is_active', $tax->is_active) ? 'Activo' : 'Inactivo' }}
                            </span>
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
        const nameInput = document.querySelector('input[name="name"]');
        const codeInput = document.querySelector('input[name="code"]');
        const rateInput = document.querySelector('input[name="rate"]');
        const defaultCheckbox = document.getElementById('isDefault');
        const activeCheckbox = document.getElementById('isActive');
        const previewName = document.getElementById('previewName');
        const previewCode = document.getElementById('previewCode');
        const previewRate = document.getElementById('previewRate');
        const previewDefault = document.getElementById('previewDefault');
        const previewStatus = document.getElementById('previewStatus');

        if (nameInput) {
            nameInput.addEventListener('input', function() {
                previewName.textContent = this.value || 'ITBIS';
            });
        }

        if (codeInput) {
            codeInput.addEventListener('input', function() {
                previewCode.textContent = this.value || 'ITBIS';
            });
        }

        if (rateInput) {
            rateInput.addEventListener('input', function() {
                previewRate.textContent = (this.value || '0') + '%';
            });
        }

        if (defaultCheckbox) {
            defaultCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    previewDefault.textContent = '⭐ Por Defecto';
                    previewDefault.className = 'badge bg-success';
                } else {
                    previewDefault.textContent = 'No es por defecto';
                    previewDefault.className = 'badge bg-secondary';
                }
            });
        }

        if (activeCheckbox) {
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
    });
</script>
@endpush
@endsection