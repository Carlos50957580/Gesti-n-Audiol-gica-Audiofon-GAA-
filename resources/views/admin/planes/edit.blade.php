<x-app-layout>
    <div class="page-content" style="padding-top: 0;">
        <div class="container-fluid pt-3">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Editar Plan de Estudio</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.planes.index') }}">Planes de Estudio</a></li>
                                <li class="breadcrumb-item active">Editar</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-edit-line align-middle me-1"></i>
                                Modificar Detalles del Plan
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Se usa @method('PUT') para la actualización en Laravel --}}
                            <form action="{{ route('admin.planes.update', $plan) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="university_id" class="form-label">
                                            Universidad <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('university_id') is-invalid @enderror" 
                                                id="university_id" 
                                                name="university_id" 
                                                required>
                                            <option value="">-- Selecciona una Universidad --</option>
                                            @foreach ($universidades as $uni)
                                                {{-- Se mantiene la lógica de seleccionar el valor actual o el 'old' --}}
                                                <option value="{{ $uni->id }}" 
                                                    {{ (old('university_id', $plan->university_id) == $uni->id) ? 'selected' : '' }}>
                                                    {{ $uni->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('university_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre_programa" class="form-label">
                                            Nombre del Programa <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nombre_programa') is-invalid @enderror" 
                                               id="nombre_programa" 
                                               name="nombre_programa" 
                                               value="{{ old('nombre_programa', $plan->nombre_programa) }}"
                                               placeholder="Ej: Ingeniería de Software"
                                               required>
                                        @error('nombre_programa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nivel_academico" class="form-label">
                                            Nivel Académico
                                        </label>
                                        <select class="form-select @error('nivel_academico') is-invalid @enderror" 
                                                id="nivel_academico" 
                                                name="nivel_academico">
                                            <option value="">-- Selecciona --</option>
                                            @php $niveles = ['Grado', 'Maestría', 'Doctorado']; @endphp
                                            @foreach ($niveles as $nivel)
                                                <option value="{{ $nivel }}" 
                                                    {{ (old('nivel_academico', $plan->nivel_academico) == $nivel) ? 'selected' : '' }}>
                                                    {{ $nivel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('nivel_academico')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="modalidad" class="form-label">
                                            Modalidad
                                        </label>
                                        <select class="form-select @error('modalidad') is-invalid @enderror" 
                                                id="modalidad" 
                                                name="modalidad">
                                            <option value="">-- Selecciona --</option>
                                            @php $modalidades = ['Presencial', 'Virtual', 'Mixta']; @endphp
                                            @foreach ($modalidades as $modalidad)
                                                <option value="{{ $modalidad }}" 
                                                    {{ (old('modalidad', $plan->modalidad) == $modalidad) ? 'selected' : '' }}>
                                                    {{ $modalidad }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('modalidad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="facultad" class="form-label">
                                            Facultad
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('facultad') is-invalid @enderror" 
                                               id="facultad" 
                                               name="facultad" 
                                               value="{{ old('facultad', $plan->facultad) }}"
                                               placeholder="Ej: Ciencias y Tecnología">
                                        @error('facultad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="duracion_meses" class="form-label">
                                            Duración (meses)
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('duracion_meses') is-invalid @enderror" 
                                               id="duracion_meses" 
                                               name="duracion_meses" 
                                               value="{{ old('duracion_meses', $plan->duracion_meses) }}"
                                               placeholder="Ej: 48">
                                        @error('duracion_meses')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="area_conocimiento" class="form-label">
                                            Área de Conocimiento
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('area_conocimiento') is-invalid @enderror" 
                                               id="area_conocimiento" 
                                               name="area_conocimiento" 
                                               value="{{ old('area_conocimiento', $plan->area_conocimiento) }}"
                                               placeholder="Ej: Ingeniería, Industria y Construcción">
                                        @error('area_conocimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">
                                            Descripción
                                        </label>
                                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                                  id="descripcion" 
                                                  name="descripcion" 
                                                  rows="4" 
                                                  placeholder="Descripción breve del plan de estudio">{{ old('descripcion', $plan->descripcion) }}</textarea>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label">
                                            Estado <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('estado') is-invalid @enderror" 
                                                id="estado" 
                                                name="estado" 
                                                required>
                                            @php $estados = ['pendiente' => 'Pendiente', 'en_revision' => 'En Revisión', 'aprobado' => 'Aprobado', 'rechazado' => 'Rechazado']; @endphp
                                            @foreach ($estados as $key => $valor)
                                                <option value="{{ $key }}" 
                                                    {{ (old('estado', $plan->estado) == $key) ? 'selected' : '' }}>
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- El otro col-md-6 queda libre, podrías poner una fecha de última actualización si la tienes. --}}
                                </div>


                                <div class="text-end mt-4">
                                    <a href="{{ route('admin.planes.index') }}" class="btn btn-light me-2">
                                        <i class="ri-close-line align-middle me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line align-middle me-1"></i>
                                        Actualizar Plan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </x-app-layout>