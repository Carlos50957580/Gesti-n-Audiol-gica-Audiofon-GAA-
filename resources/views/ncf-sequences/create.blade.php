<x-app-layout>
@section('title', 'Nueva Secuencia NCF')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-add-line me-1"></i>Nueva Secuencia NCF</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/ncf-sequences') }}">Comprobantes NCF</a></li>
                            <li class="breadcrumb-item active">Nueva</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/ncf-sequences') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Información de la Secuencia</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ej: Facturas Consumidor Final 2026"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tipo de NCF <span class="text-danger">*</span></label>
                                    <select name="ncf_type_id" class="form-select" required>
                                        <option value="">Seleccionar tipo</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" {{ old('ncf_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->code }} - {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Sucursal</label>
                                    <select name="branch_id" class="form-select">
                                        <option value="">Todas las sucursales</option>
                                        @foreach($branches as $br)
                                            <option value="{{ $br->id }}" {{ old('branch_id') == $br->id ? 'selected' : '' }}>
                                                {{ $br->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Dejar vacío para aplicar a todas las sucursales</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Prefijo <span class="text-danger">*</span></label>
                                    <input type="text" name="prefix" class="form-control" 
                                           value="{{ old('prefix', 'B') }}" maxlength="10" required>
                                    <small class="text-muted">B para NCF, E para e-CF</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Serie <span class="text-danger">*</span></label>
                                    <input type="text" name="serie" class="form-control" 
                                           value="{{ old('serie', '01') }}" maxlength="3" required>
                                    <small class="text-muted">01, 02, etc.</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Número Inicial <span class="text-danger">*</span></label>
                                    <input type="number" name="start_number" class="form-control" 
                                           value="{{ old('start_number', '1') }}" min="1" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Número Final <span class="text-danger">*</span></label>
                                    <input type="number" name="end_number" class="form-control" 
                                           value="{{ old('end_number', '1000') }}" min="1" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Válido desde <span class="text-danger">*</span></label>
                                    <input type="date" name="valid_from" class="form-control" 
                                           value="{{ old('valid_from', now()->toDateString()) }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Vence el <span class="text-danger">*</span></label>
                                    <input type="date" name="valid_until" class="form-control" 
                                           value="{{ old('valid_until', now()->addYear()->toDateString()) }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Alerta cuando queden <span class="text-danger">*</span></label>
                                    <input type="number" name="alert_threshold" class="form-control" 
                                           value="{{ old('alert_threshold', 50) }}" min="1" required>
                                    <small class="text-muted">Se alertará cuando queden estos NCF</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="2" 
                                              placeholder="Observaciones...">{{ old('notes') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                               id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Activa</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Vista Previa del NCF</h5></div>
                        <div class="card-body text-center">
                            <div class="p-3 rounded" style="background:#f0f4ff;">
                                <small class="text-muted d-block mb-2">Formato del NCF:</small>
                                <code id="preview" style="font-size:1.2rem;font-weight:700;">B0100000001</code>
                            </div>
                            <p class="text-muted small mt-3 mb-0">
                                Formato: <strong>Prefijo + Serie + Número (8 dígitos)</strong>
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Guardar Secuencia
                                </button>
                                <a href="{{ url('/ncf-sequences') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updatePreview() {
        const prefix = document.querySelector('[name="prefix"]').value || 'B';
        const serie = document.querySelector('[name="serie"]').value || '01';
        const start = document.querySelector('[name="start_number"]').value || '1';
        const num = String(start).padStart(8, '0');
        document.getElementById('preview').textContent = prefix + serie + num;
    }

    document.querySelectorAll('[name="prefix"], [name="serie"], [name="start_number"]')
        .forEach(el => el.addEventListener('input', updatePreview));

    updatePreview();
</script>
@endpush
</x-app-layout>