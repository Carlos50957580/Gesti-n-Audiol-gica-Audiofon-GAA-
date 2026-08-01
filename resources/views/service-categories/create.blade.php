@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Nuevo Servicio / Estudio</h4>
            <div class="page-title-right">
                <a href="{{ route('services.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('services.store') }}" method="POST" id="serviceForm">
                    @csrf

                    <div class="row g-3">
                        <!-- Categoría -->
                        <div class="col-md-4">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" id="categorySelect">
                                <option value="">Seleccionar categoría</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        data-requires-hc="{{ $category->requires_clinical_record ? '1' : '0' }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                    @if($category->requires_clinical_record) 🔬 @endif
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Código -->
                        <div class="col-md-2">
                            <label class="form-label">Código</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                   value="{{ old('code') }}" placeholder="Ej: CON-001">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Ej: Consulta General" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="2" placeholder="Descripción del servicio">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Precio -->
                        <div class="col-md-3">
                            <label class="form-label">Precio <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">RD$</span>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price') }}" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duración -->
                        <div class="col-md-3">
                            <label class="form-label">Duración (minutos)</label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                   value="{{ old('duration_minutes') }}" min="0" placeholder="30">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Requiere Autorización -->
                        <div class="col-md-3">
                            <label class="form-label">Autorización</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="requires_authorization" value="0">
                                <input type="checkbox" name="requires_authorization" class="form-check-input" id="requiresAuth" 
                                       value="1" {{ old('requires_authorization') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requiresAuth">
                                    Requiere autorización
                                </label>
                            </div>
                        </div>

                        <!-- Requiere Historia Clínica -->
                        <div class="col-md-3">
                            <label class="form-label">Historia Clínica</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="requires_clinical_record" value="0">
                                <input type="checkbox" name="requires_clinical_record" class="form-check-input" id="requiresHC" 
                                       value="1" {{ old('requires_clinical_record') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requiresHC">
                                    Requiere Historia Clínica
                                </label>
                            </div>
                            <small class="text-muted" id="hcInheritInfo">
                                <i class="ri-information-line"></i> Hereda de la categoría si no se especifica
                            </small>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-3">
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

                        <!-- Cobertura por Seguro -->
                        <div class="col-12">
                            <hr>
                            <h5 class="mb-3">Cobertura por Seguro Médico</h5>
                            <div id="insuranceCoverageContainer">
                                @if(old('insurance_coverage'))
                                    @foreach(old('insurance_coverage') as $index => $coverage)
                                    <div class="row g-2 mb-2 coverage-row">
                                        <div class="col-md-3">
                                            <select name="insurance_coverage[{{ $index }}][insurance_id]" class="form-select">
                                                <option value="">Seleccionar seguro</option>
                                                @foreach($insurances as $insurance)
                                                <option value="{{ $insurance->id }}" {{ ($coverage['insurance_id'] ?? '') == $insurance->id ? 'selected' : '' }}>
                                                    {{ $insurance->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="insurance_coverage[{{ $index }}][coverage_percentage]" 
                                                   class="form-control" placeholder="% Cobertura" step="0.01" min="0" max="100"
                                                   value="{{ $coverage['coverage_percentage'] ?? '' }}">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <span class="input-group-text">RD$</span>
                                                <input type="number" name="insurance_coverage[{{ $index }}][fixed_amount]" 
                                                       class="form-control" placeholder="Monto fijo" step="0.01" min="0"
                                                       value="{{ $coverage['fixed_amount'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch mt-2">
                                                <input type="hidden" name="insurance_coverage[{{ $index }}][requires_authorization]" value="0">
                                                <input type="checkbox" name="insurance_coverage[{{ $index }}][requires_authorization]" 
                                                       class="form-check-input" id="authCoverage{{ $index }}"
                                                       value="1" {{ isset($coverage['requires_authorization']) && $coverage['requires_authorization'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="authCoverage{{ $index }}">
                                                    Requiere Auth
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-coverage">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="addCoverage">
                                <i class="ri-add-line"></i> Agregar Cobertura
                            </button>
                        </div>

                        <!-- Botones -->
                        <div class="col-12">
                            <hr>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Guardar Servicio
                            </button>
                            <a href="{{ route('services.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cuando cambia la categoría, actualizar el estado de Historia Clínica
        const categorySelect = document.getElementById('categorySelect');
        const hcCheckbox = document.getElementById('requiresHC');
        const hcInfo = document.getElementById('hcInheritInfo');

        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const requiresHC = selected.dataset.requiresHc === '1';
                
                if (requiresHC) {
                    hcCheckbox.checked = true;
                    hcCheckbox.disabled = true;
                    hcInfo.innerHTML = '<i class="ri-information-line"></i> Hereda de la categoría (requiere HC)';
                } else if (this.value === '') {
                    hcCheckbox.disabled = false;
                    hcInfo.innerHTML = '<i class="ri-information-line"></i> Hereda de la categoría si no se especifica';
                } else {
                    hcCheckbox.disabled = false;
                    hcInfo.innerHTML = '<i class="ri-information-line"></i> La categoría no requiere HC, pero puede activarse individualmente';
                }
            });

            // Inicializar el estado de la categoría
            categorySelect.dispatchEvent(new Event('change'));
        }

        // Agregar cobertura de seguro
        let coverageIndex = {{ count(old('insurance_coverage', [])) }};
        const addCoverageBtn = document.getElementById('addCoverage');
        const container = document.getElementById('insuranceCoverageContainer');

        if (addCoverageBtn && container) {
            addCoverageBtn.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'row g-2 mb-2 coverage-row';
                row.innerHTML = `
                    <div class="col-md-3">
                        <select name="insurance_coverage[${coverageIndex}][insurance_id]" class="form-select">
                            <option value="">Seleccionar seguro</option>
                            @foreach($insurances as $insurance)
                            <option value="{{ $insurance->id }}">{{ $insurance->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="insurance_coverage[${coverageIndex}][coverage_percentage]" 
                               class="form-control" placeholder="% Cobertura" step="0.01" min="0" max="100">
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="insurance_coverage[${coverageIndex}][fixed_amount]" 
                                   class="form-control" placeholder="Monto fijo" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="insurance_coverage[${coverageIndex}][requires_authorization]" value="0">
                            <input type="checkbox" name="insurance_coverage[${coverageIndex}][requires_authorization]" 
                                   class="form-check-input" id="authCoverage${coverageIndex}"
                                   value="1">
                            <label class="form-check-label" for="authCoverage${coverageIndex}">Requiere Auth</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-coverage">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
                coverageIndex++;
                
                row.querySelector('.remove-coverage').addEventListener('click', function() {
                    row.remove();
                });
            });
        }

        // Evento para eliminar filas existentes
        document.querySelectorAll('.remove-coverage').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.coverage-row').remove();
            });
        });
    });
</script>
@endpush
@endsection