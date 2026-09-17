@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                <i class="ri-edit-line me-1"></i> Editar Secuencia e-CF
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ecf-sequences.index') }}">Secuencias e-CF</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    E{{ $ecfSequence->tipo_ecf }} - {{ $ecfSequence->prefijo }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('ecf-sequences.update', $ecfSequence) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                                <label class="form-label">Tipo de e-CF</label>
                                <input type="text" class="form-control" 
                                       value="E{{ $ecfSequence->tipo_ecf }} - {{ $tiposEcf[$ecfSequence->tipo_ecf] ?? '' }}" 
                                       disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prefijo" class="form-label">Prefijo</label>
                                <input type="text" class="form-control" 
                                       id="prefijo" name="prefijo" 
                                       value="{{ old('prefijo', $ecfSequence->prefijo) }}" maxlength="5">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Desde</label>
                                <input type="number" class="form-control" 
                                       value="{{ $ecfSequence->desde }}" disabled>
                                <div class="form-text">No editable si ya tiene facturas</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="hasta" class="form-label">Hasta</label>
                                <input type="number" class="form-control @error('hasta') is-invalid @enderror"
                                       id="hasta" name="hasta" 
                                       value="{{ old('hasta', $ecfSequence->hasta) }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="secuencia_actual" class="form-label">Secuencia Actual</label>
                                <input type="number" class="form-control" 
                                       id="secuencia_actual" name="secuencia_actual" 
                                       value="{{ old('secuencia_actual', $ecfSequence->secuencia_actual) }}">
                            </div>
                        </div>

                        @if($ecfSequence->tipo_ecf !== '32')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                    <input type="date" class="form-control"
                                           id="fecha_vencimiento" name="fecha_vencimiento" 
                                           value="{{ old('fecha_vencimiento', $ecfSequence->fecha_vencimiento?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Sucursal</label>
                                <select class="form-select" id="branch_id" name="branch_id">
                                    <option value="">Global</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" 
                                            {{ old('branch_id', $ecfSequence->branch_id) == $branch->id ? 'selected' : '' }}>
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
                                           {{ old('estado', $ecfSequence->estado) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-14" for="estado">Activa</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea class="form-control" id="notas" name="notas" rows="2">{{ old('notas', $ecfSequence->notas) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Actualizar
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