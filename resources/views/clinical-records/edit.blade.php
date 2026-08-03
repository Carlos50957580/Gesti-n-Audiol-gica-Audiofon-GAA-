<x-app-layout>
@section('title', 'Editar Historia Clínica #' . $clinicalRecord->id)

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ri-edit-line me-1"></i> Editar Historia Clínica #{{ $clinicalRecord->id }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('clinical-records.index') }}">Historias Clínicas</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-1"></i>
                            Esta historia clínica está en estado <strong>{{ $clinicalRecord->status_label }}</strong>.
                            @if(!$clinicalRecord->canBeEdited())
                                <span class="text-danger">No puede ser editada.</span>
                            @endif
                        </div>

                        <form action="{{ route('clinical-records.update', $clinicalRecord) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Paciente</label>
                                        <p class="form-control-static"><strong>{{ $clinicalRecord->patient->full_name }}</strong></p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Factura</label>
                                        <p class="form-control-static"><strong>{{ $clinicalRecord->invoice->invoice_number ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="doctor_id" class="form-label">Médico <span class="text-danger">*</span></label>
                                        <select class="form-select @error('doctor_id') is-invalid @enderror" 
                                                id="doctor_id" name="doctor_id" required>
                                            <option value="">Seleccionar médico</option>
                                            @foreach($doctors as $doctor)
                                                <option value="{{ $doctor->id }}" 
                                                    {{ old('doctor_id', $clinicalRecord->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                                    {{ $doctor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('doctor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="branch_id" class="form-label">Sucursal <span class="text-danger">*</span></label>
                                        <select class="form-select @error('branch_id') is-invalid @enderror" 
                                                id="branch_id" name="branch_id" required>
                                            <option value="">Seleccionar sucursal</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" 
                                                    {{ old('branch_id', $clinicalRecord->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="consultation_date" class="form-label">Fecha de Consulta <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('consultation_date') is-invalid @enderror" 
                                               id="consultation_date" name="consultation_date" 
                                               value="{{ old('consultation_date', $clinicalRecord->consultation_date ? $clinicalRecord->consultation_date->format('Y-m-d') : '') }}" required>
                                        @error('consultation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="consultation_type" class="form-label">Tipo de Consulta <span class="text-danger">*</span></label>
                                        <select class="form-select @error('consultation_type') is-invalid @enderror" 
                                                id="consultation_type" name="consultation_type" required>
                                            <option value="primera_vez" {{ old('consultation_type', $clinicalRecord->consultation_type) == 'primera_vez' ? 'selected' : '' }}>Primera Vez</option>
                                            <option value="seguimiento" {{ old('consultation_type', $clinicalRecord->consultation_type) == 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                                            <option value="urgencia" {{ old('consultation_type', $clinicalRecord->consultation_type) == 'urgencia' ? 'selected' : '' }}>Urgencia</option>
                                            <option value="control" {{ old('consultation_type', $clinicalRecord->consultation_type) == 'control' ? 'selected' : '' }}>Control</option>
                                        </select>
                                        @error('consultation_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="consultation_reason" class="form-label">Motivo de Consulta</label>
                                        <input type="text" class="form-control @error('consultation_reason') is-invalid @enderror" 
                                               id="consultation_reason" name="consultation_reason" 
                                               value="{{ old('consultation_reason', $clinicalRecord->consultation_reason) }}" 
                                               placeholder="Motivo breve de la consulta">
                                        @error('consultation_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Signos Vitales -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="ri-heart-pulse-line me-1"></i> Signos Vitales y Medidas</h5>
                                    <hr>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="blood_pressure_systolic" class="form-label">Presión Sistólica (mmHg)</label>
                                        <input type="number" class="form-control @error('blood_pressure_systolic') is-invalid @enderror" 
                                               id="blood_pressure_systolic" name="blood_pressure_systolic" 
                                               value="{{ old('blood_pressure_systolic', $clinicalRecord->vital_signs['blood_pressure_systolic'] ?? '') }}" placeholder="120">
                                        @error('blood_pressure_systolic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="blood_pressure_diastolic" class="form-label">Presión Diastólica (mmHg)</label>
                                        <input type="number" class="form-control @error('blood_pressure_diastolic') is-invalid @enderror" 
                                               id="blood_pressure_diastolic" name="blood_pressure_diastolic" 
                                               value="{{ old('blood_pressure_diastolic', $clinicalRecord->vital_signs['blood_pressure_diastolic'] ?? '') }}" placeholder="80">
                                        @error('blood_pressure_diastolic')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="heart_rate" class="form-label">FC (lpm)</label>
                                        <input type="number" class="form-control @error('heart_rate') is-invalid @enderror" 
                                               id="heart_rate" name="heart_rate" 
                                               value="{{ old('heart_rate', $clinicalRecord->vital_signs['heart_rate'] ?? '') }}" placeholder="72">
                                        @error('heart_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="respiratory_rate" class="form-label">FR (rpm)</label>
                                        <input type="number" class="form-control @error('respiratory_rate') is-invalid @enderror" 
                                               id="respiratory_rate" name="respiratory_rate" 
                                               value="{{ old('respiratory_rate', $clinicalRecord->vital_signs['respiratory_rate'] ?? '') }}" placeholder="16">
                                        @error('respiratory_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="oxygen_saturation" class="form-label">SO (%)</label>
                                        <input type="number" class="form-control @error('oxygen_saturation') is-invalid @enderror" 
                                               id="oxygen_saturation" name="oxygen_saturation" 
                                               value="{{ old('oxygen_saturation', $clinicalRecord->vital_signs['oxygen_saturation'] ?? '') }}" placeholder="98">
                                        @error('oxygen_saturation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="temperature" class="form-label">Temperatura (°C)</label>
                                        <input type="number" step="0.1" class="form-control @error('temperature') is-invalid @enderror" 
                                               id="temperature" name="temperature" 
                                               value="{{ old('temperature', $clinicalRecord->vital_signs['temperature'] ?? '') }}" placeholder="36.5">
                                        @error('temperature')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="weight" class="form-label">Peso (kg)</label>
                                        <input type="number" step="0.1" class="form-control @error('weight') is-invalid @enderror" 
                                               id="weight" name="weight" 
                                               value="{{ old('weight', $clinicalRecord->vital_signs['weight'] ?? '') }}" placeholder="70.5">
                                        @error('weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="height" class="form-label">Talla (cm)</label>
                                        <input type="number" step="0.1" class="form-control @error('height') is-invalid @enderror" 
                                               id="height" name="height" 
                                               value="{{ old('height', $clinicalRecord->vital_signs['height'] ?? '') }}" placeholder="170">
                                        @error('height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Signos Obstétricos -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-3 text-muted"><i class="ri-baby-line me-1"></i> Signos Obstétricos (Opcional)</h6>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="fetal_heart_rate" class="form-label">FC Fetal (lpm)</label>
                                        <input type="number" class="form-control @error('fetal_heart_rate') is-invalid @enderror" 
                                               id="fetal_heart_rate" name="fetal_heart_rate" 
                                               value="{{ old('fetal_heart_rate', $clinicalRecord->vital_signs['fetal_heart_rate'] ?? '') }}" placeholder="140">
                                        @error('fetal_heart_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="uterine_height" class="form-label">Altura Uterina (cm)</label>
                                        <input type="number" step="0.1" class="form-control @error('uterine_height') is-invalid @enderror" 
                                               id="uterine_height" name="uterine_height" 
                                               value="{{ old('uterine_height', $clinicalRecord->vital_signs['uterine_height'] ?? '') }}" placeholder="25">
                                        @error('uterine_height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edema" class="form-label">Edema</label>
                                        <input type="text" class="form-control @error('edema') is-invalid @enderror" 
                                               id="edema" name="edema" 
                                               value="{{ old('edema', $clinicalRecord->vital_signs['edema'] ?? '') }}" placeholder="Sin edema">
                                        @error('edema')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="fetal_movements" class="form-label">Movimientos Fetales</label>
                                        <input type="text" class="form-control @error('fetal_movements') is-invalid @enderror" 
                                               id="fetal_movements" name="fetal_movements" 
                                               value="{{ old('fetal_movements', $clinicalRecord->vital_signs['fetal_movements'] ?? '') }}" placeholder="Activos, normales">
                                        @error('fetal_movements')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Historia Clínica -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="ri-file-list-line me-1"></i> Historia Clínica</h5>
                                    <hr>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="reason_for_consultation" class="form-label">Motivo de Consulta (Detallado)</label>
                                        <textarea class="form-control @error('reason_for_consultation') is-invalid @enderror" 
                                                  id="reason_for_consultation" name="reason_for_consultation" rows="2">{{ old('reason_for_consultation', $clinicalRecord->reason_for_consultation) }}</textarea>
                                        @error('reason_for_consultation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="anamnesis" class="form-label">Anamnesis</label>
                                        <textarea class="form-control @error('anamnesis') is-invalid @enderror" 
                                                  id="anamnesis" name="anamnesis" rows="3">{{ old('anamnesis', $clinicalRecord->anamnesis) }}</textarea>
                                        @error('anamnesis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="physical_exam" class="form-label">Exploración Física</label>
                                        <textarea class="form-control @error('physical_exam') is-invalid @enderror" 
                                                  id="physical_exam" name="physical_exam" rows="3">{{ old('physical_exam', $clinicalRecord->physical_exam) }}</textarea>
                                        @error('physical_exam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="presumptive_diagnosis" class="form-label">Diagnóstico Presuntivo</label>
                                        <textarea class="form-control @error('presumptive_diagnosis') is-invalid @enderror" 
                                                  id="presumptive_diagnosis" name="presumptive_diagnosis" rows="2">{{ old('presumptive_diagnosis', $clinicalRecord->presumptive_diagnosis) }}</textarea>
                                        @error('presumptive_diagnosis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="treatment" class="form-label">Tratamiento</label>
                                        <textarea class="form-control @error('treatment') is-invalid @enderror" 
                                                  id="treatment" name="treatment" rows="3">{{ old('treatment', $clinicalRecord->treatment) }}</textarea>
                                        @error('treatment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="evolution" class="form-label">Evolución</label>
                                        <textarea class="form-control @error('evolution') is-invalid @enderror" 
                                                  id="evolution" name="evolution" rows="2">{{ old('evolution', $clinicalRecord->evolution) }}</textarea>
                                        @error('evolution')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="observations" class="form-label">Observaciones</label>
                                        <textarea class="form-control @error('observations') is-invalid @enderror" 
                                                  id="observations" name="observations" rows="2">{{ old('observations', $clinicalRecord->observations) }}</textarea>
                                        @error('observations')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="recommendations" class="form-label">Recomendaciones</label>
                                        <textarea class="form-control @error('recommendations') is-invalid @enderror" 
                                                  id="recommendations" name="recommendations" rows="2">{{ old('recommendations', $clinicalRecord->recommendations) }}</textarea>
                                        @error('recommendations')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" {{ !$clinicalRecord->canBeEdited() ? 'disabled' : '' }}>
                                        <i class="ri-save-line me-1"></i> Actualizar Historia Clínica
                                    </button>
                                    <a href="{{ route('clinical-records.show', $clinicalRecord) }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line me-1"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Calcular IMC automáticamente
    document.getElementById('weight').addEventListener('input', calculateBMI);
    document.getElementById('height').addEventListener('input', calculateBMI);

    function calculateBMI() {
        const weight = parseFloat(document.getElementById('weight').value);
        const height = parseFloat(document.getElementById('height').value);
        
        if (weight && height && height > 0) {
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            // Mostrar IMC en un campo oculto o en un span
            const bmiDisplay = document.getElementById('bmi_display');
            if (bmiDisplay) {
                bmiDisplay.textContent = 'IMC: ' + bmi.toFixed(2);
            }
        }
    }
</script>
@endpush
</x-app-layout>