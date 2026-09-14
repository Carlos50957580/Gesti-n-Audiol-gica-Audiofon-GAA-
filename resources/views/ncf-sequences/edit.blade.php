<x-app-layout>
@section('title', 'Editar Secuencia NCF')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-edit-line me-1"></i>Editar Secuencia NCF</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/ncf-sequences') }}">Comprobantes NCF</a></li>
                            <li class="breadcrumb-item active">Editar</li>
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

        <form action="{{ url('/ncf-sequences/'.$ncfSequence->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Información de la Secuencia</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name', $ncfSequence->name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tipo de NCF <span class="text-danger">*</span></label>
                                    <select name="ncf_type_id" class="form-select" required>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" 
                                                {{ old('ncf_type_id', $ncfSequence->ncf_type_id) == $type->id ? 'selected' : '' }}>
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
                                            <option value="{{ $br->id }}" 
                                                {{ old('branch_id', $ncfSequence->branch_id) == $br->id ? 'selected' : '' }}>
                                                {{ $br->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Prefijo <span class="text-danger">*</span></label>
                                    <input type="text" name="prefix" class="form-control" 
                                           value="{{ old('prefix', $ncfSequence->prefix) }}" maxlength="10" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Serie <span class="text-danger">*</span></label>
                                    <input type="text" name="serie" class="form-control" 
                                           value="{{ old('serie', $ncfSequence->serie) }}" maxlength="3" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Número Inicial</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $ncfSequence->start_number }}" disabled>
                                    <small class="text-muted">No editable</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Número Final <span class="text-danger">*</span></label>
                                    <input type="number" name="end_number" class="form-control" 
                                           value="{{ old('end_number', (int) $ncfSequence->end_number) }}" 
                                           min="{{ (int) $ncfSequence->current_number }}" required>
                                    <small class="text-muted">Mínimo: {{ (int) $ncfSequence->current_number }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Válido desde <span class="text-danger">*</span></label>
                                    <input type="date" name="valid_from" class="form-control" 
                                           value="{{ old('valid_from', $ncfSequence->valid_from->format('Y-m-d')) }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Vence el <span class="text-danger">*</span></label>
                                    <input type="date" name="valid_until" class="form-control" 
                                           value="{{ old('valid_until', $ncfSequence->valid_until->format('Y-m-d')) }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Alerta cuando queden <span class="text-danger">*</span></label>
                                    <input type="number" name="alert_threshold" class="form-control" 
                                           value="{{ old('alert_threshold', $ncfSequence->alert_threshold) }}" min="1" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $ncfSequence->notes) }}</textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                               id="is_active" {{ old('is_active', $ncfSequence->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Activa</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Estado Actual</h5></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">NCF Actual:</span>
                                <code class="fw-bold">{{ $ncfSequence->current_ncf }}</code>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Números Usados:</span>
                                <strong>{{ number_format($ncfSequence->used) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Disponibles:</span>
                                <strong class="text-success">{{ number_format($ncfSequence->remaining) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total:</span>
                                <strong>{{ number_format($ncfSequence->total_available) }}</strong>
                            </div>
                            <div class="progress mb-3" style="height:10px;">
                                <div class="progress-bar bg-{{ $ncfSequence->status_color }}" 
                                     style="width:{{ $ncfSequence->used_percent }}%"></div>
                            </div>
                            <div class="text-center">
                                <span class="badge bg-{{ $ncfSequence->status_color }}-subtle text-{{ $ncfSequence->status_color }} fs-6 px-3 py-2">
                                    {{ $ncfSequence->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Actualizar
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
</x-app-layout>