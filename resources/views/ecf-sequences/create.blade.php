@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                <i class="ri-add-circle-line me-1"></i> Nueva Secuencia e-CF
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ecf-sequences.index') }}">Secuencias e-CF</a></li>
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información de la Secuencia</h5>
                <p class="card-text text-muted small mb-0">
                    Registra un rango de secuencias autorizado por la DGII.
                </p>
            </div>
            <div class="card-body">
                <form action="{{ route('ecf-sequences.store') }}" method="POST">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_ecf" class="form-label">
                                    Tipo de e-CF <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo_ecf') is-invalid @enderror"
                                        id="tipo_ecf" name="tipo_ecf" required>
                                    <option value="">Seleccione un tipo...</option>
                                    @foreach($tiposEcf as $code => $nombre)
                                        <option value="{{ $code }}" {{ old('tipo_ecf') == $code ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Según tabla de tipos de e-CF de la DGII</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prefijo" class="form-label">
                                    Prefijo <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('prefijo') is-invalid @enderror"
                                       id="prefijo" name="prefijo" 
                                       value="{{ old('prefijo', 'E') }}" 
                                       placeholder="E31" maxlength="5" required>
                                <div class="form-text">Ej: E31, E32, E34, E45</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="desde" class="form-label">
                                    Desde <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('desde') is-invalid @enderror"
                                       id="desde" name="desde" 
                                       value="{{ old('desde', 1) }}" min="1" required>
                                <div class="form-text">Número inicial del rango</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="hasta" class="form-label">
                                    Hasta <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('hasta') is-invalid @enderror"
                                       id="hasta" name="hasta" 
                                       value="{{ old('hasta') }}" min="1" required>
                                <div class="form-text">Número final del rango</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="secuencia_actual" class="form-label">
                                    Secuencia Actual
                                </label>
                                <input type="number" class="form-control @error('secuencia_actual') is-invalid @enderror"
                                       id="secuencia_actual" name="secuencia_actual" 
                                       value="{{ old('secuencia_actual', 0) }}" min="0">
                                <div class="form-text">0 = comenzar desde el inicio</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_vencimiento" class="form-label">
                                    Fecha de Vencimiento
                                </label>
                                <input type="date" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                                       id="fecha_vencimiento" name="fecha_vencimiento" 
                                       value="{{ old('fecha_vencimiento') }}">
                                <div class="form-text">No requerida para E32 (Consumidor Final)</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valid_from" class="form-label">
                                    Válida Desde
                                </label>
                                <input type="date" class="form-control @error('valid_from') is-invalid @enderror"
                                       id="valid_from" name="valid_from" 
                                       value="{{ old('valid_from', now()->toDateString()) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Sucursal</label>
                                <select class="form-select @error('branch_id') is-invalid @enderror"
                                        id="branch_id" name="branch_id">
                                    <option value="">Global (todas las sucursales)</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <div class="form-check form-switch fs-20">
                                    <input class="form-check-input" type="checkbox" 
                                           id="estado" name="estado" value="1" 
                                           {{ old('estado', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-14" for="estado">
                                        Activar esta secuencia
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea class="form-control @error('notas') is-invalid @enderror"
                                          id="notas" name="notas" rows="2" 
                                          placeholder="Observaciones opcionales">{{ old('notas') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="ri-information-line me-1"></i>
                        <strong>Regla de continuidad:</strong> El campo <em>Desde</em> debe ser 
                        <code>último_hasta + 1</code> del mismo tipo de e-CF.
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Guardar Secuencia
                        </button>
                        <a href="{{ route('ecf-sequences.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-llenar el prefijo según el tipo seleccionado
    document.getElementById('tipo_ecf')?.addEventListener('change', function() {
        const prefijoInput = document.getElementById('prefijo');
        if (this.value) {
            prefijoInput.value = 'E' + this.value;
        }
    });

    // Ocultar fecha de vencimiento si es E32
    document.getElementById('tipo_ecf')?.addEventListener('change', function() {
        const fechaInput = document.getElementById('fecha_vencimiento');
        const fechaHelp = fechaInput.nextElementSibling;
        if (this.value === '32') {
            fechaInput.value = '';
            fechaInput.disabled = true;
            fechaHelp.textContent = 'No requerida para E32 (se asigna +100 años)';
        } else {
            fechaInput.disabled = false;
            fechaHelp.textContent = 'Requerida para este tipo de e-CF';
        }
    });
</script>
@endpush