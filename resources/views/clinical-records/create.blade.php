<x-app-layout>
@section('title', 'Nueva Historia Clínica')

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ri-file-add-line me-1"></i> Nueva Historia Clínica
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('clinical-records.index') }}">Historias Clínicas</a></li>
                            <li class="breadcrumb-item active">Nueva</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($selectedInvoice)
                            <div class="alert alert-info">
                                <i class="ri-information-line me-1"></i>
                                Creando historia clínica para la factura <strong>{{ $selectedInvoice->invoice_number }}</strong>
                                del paciente <strong>{{ $selectedInvoice->patient->full_name ?? 'N/A' }}</strong>
                            </div>
                        @endif

                        <form action="{{ route('clinical-records.store') }}" method="POST" id="clinicalRecordForm">
                            @csrf

                            <!-- Información Básica -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="ri-information-line me-1"></i> Información Básica</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="invoice_id" class="form-label">Factura <span class="text-danger">*</span></label>
                                        <select class="form-select @error('invoice_id') is-invalid @enderror" 
                                                id="invoice_id" name="invoice_id" required>
                                            <option value="">Seleccionar factura</option>
                                            @foreach($invoices as $invoice)
                                                <option value="{{ $invoice->id }}" 
                                                    data-patient-id="{{ $invoice->patient_id }}"
                                                    data-patient-name="{{ $invoice->patient->full_name ?? '' }}"
                                                    data-doctor-id="{{ $invoice->doctor_id ?? '' }}"
                                                    data-doctor-name="{{ $invoice->doctor->name ?? '' }}"
                                                    data-branch-id="{{ $invoice->branch_id ?? '' }}"
                                                    data-branch-name="{{ $invoice->branch->name ?? '' }}"
                                                    data-date="{{ $invoice->created_at->format('Y-m-d') }}"
                                                    {{ old('invoice_id', $selectedInvoice->id ?? '') == $invoice->id ? 'selected' : '' }}>
                                                    {{ $invoice->invoice_number }} - {{ $invoice->patient->full_name ?? 'N/A' }} ({{ $invoice->created_at->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('invoice_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Solo facturas pagadas que requieren historia clínica</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="patient_id" class="form-label">Paciente <span class="text-danger">*</span></label>
                                        <select class="form-select @error('patient_id') is-invalid @enderror" 
                                                id="patient_id" name="patient_id" required>
                                            <option value="">Seleccionar paciente</option>
                                            @foreach($patients as $patient)
                                                <option value="{{ $patient->id }}" 
                                                    {{ old('patient_id', $selectedInvoice->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
                                                    {{ $patient->full_name }} - {{ $patient->cedula ?? 'Sin cédula' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('patient_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="doctor_id" class="form-label">Médico <span class="text-danger">*</span></label>
                                        <select class="form-select @error('doctor_id') is-invalid @enderror" 
                                                id="doctor_id" name="doctor_id" required disabled>
                                            <option value="">Seleccionar médico</option>
                                            @foreach($doctors as $doctor)
                                                <option value="{{ $doctor->id }}" 
                                                    {{ old('doctor_id', $selectedInvoice->doctor_id ?? '') == $doctor->id ? 'selected' : '' }}>
                                                    {{ $doctor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="doctor_id" id="doctor_id_hidden" value="{{ old('doctor_id', $selectedInvoice->doctor_id ?? '') }}">
                                        @error('doctor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">El médico está asignado automáticamente desde la factura</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="branch_id" class="form-label">Sucursal <span class="text-danger">*</span></label>
                                        <select class="form-select @error('branch_id') is-invalid @enderror" 
                                                id="branch_id" name="branch_id" required disabled>
                                            <option value="">Seleccionar sucursal</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" 
                                                    {{ old('branch_id', $selectedInvoice->branch_id ?? auth()->user()->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="branch_id" id="branch_id_hidden" value="{{ old('branch_id', $selectedInvoice->branch_id ?? auth()->user()->branch_id ?? '') }}">
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">La sucursal está asignada automáticamente desde la factura</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="consultation_date" class="form-label">Fecha de Consulta <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('consultation_date') is-invalid @enderror" 
                                               id="consultation_date" name="consultation_date" 
                                               value="{{ old('consultation_date', $selectedInvoice ? $selectedInvoice->created_at->format('Y-m-d') : date('Y-m-d')) }}" required>
                                        @error('consultation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="consultation_type" class="form-label">Tipo de Consulta <span class="text-danger">*</span></label>
                                        <select class="form-select @error('consultation_type') is-invalid @enderror" 
                                                id="consultation_type" name="consultation_type" required>
                                            <option value="primera_vez" {{ old('consultation_type') == 'primera_vez' ? 'selected' : '' }}>Primera Vez</option>
                                            <option value="seguimiento" {{ old('consultation_type') == 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                                            <option value="urgencia" {{ old('consultation_type') == 'urgencia' ? 'selected' : '' }}>Urgencia</option>
                                            <option value="control" {{ old('consultation_type') == 'control' ? 'selected' : '' }}>Control</option>
                                        </select>
                                        @error('consultation_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Estado</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status">
                                            <option value="pendiente" {{ old('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="completada" {{ old('status') == 'completada' ? 'selected' : '' }}>Completada</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="consultation_reason" class="form-label">Motivo de Consulta</label>
                                        <input type="text" class="form-control @error('consultation_reason') is-invalid @enderror" 
                                               id="consultation_reason" name="consultation_reason" 
                                               value="{{ old('consultation_reason') }}" 
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
                                               value="{{ old('blood_pressure_systolic') }}" placeholder="120">
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
                                               value="{{ old('blood_pressure_diastolic') }}" placeholder="80">
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
                                               value="{{ old('heart_rate') }}" placeholder="72">
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
                                               value="{{ old('respiratory_rate') }}" placeholder="16">
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
                                               value="{{ old('oxygen_saturation') }}" placeholder="98">
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
                                               value="{{ old('temperature') }}" placeholder="36.5">
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
                                               value="{{ old('weight') }}" placeholder="70.5">
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
                                               value="{{ old('height') }}" placeholder="170">
                                        @error('height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Signos Obstétricos (opcional) -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="mb-3 text-muted"><i class="ri-baby-line me-1"></i> Signos Obstétricos (Opcional)</h6>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="fetal_heart_rate" class="form-label">FC Fetal (lpm)</label>
                                        <input type="number" class="form-control @error('fetal_heart_rate') is-invalid @enderror" 
                                               id="fetal_heart_rate" name="fetal_heart_rate" 
                                               value="{{ old('fetal_heart_rate') }}" placeholder="140">
                                        @error('fetal_heart_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="uterine_height" class="form-label">Altura Uterina (cm)</label>
                                        <input type="number" step="0.1" class="form-control @error('uterine_height') is-invalid @enderror" 
                                               id="uterine_height" name="uterine_height" 
                                               value="{{ old('uterine_height') }}" placeholder="25">
                                        @error('uterine_height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="edema" class="form-label">Edema</label>
                                        <input type="text" class="form-control @error('edema') is-invalid @enderror" 
                                               id="edema" name="edema" 
                                               value="{{ old('edema') }}" placeholder="Sin edema">
                                        @error('edema')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="fetal_movements" class="form-label">Movimientos Fetales</label>
                                        <input type="text" class="form-control @error('fetal_movements') is-invalid @enderror" 
                                               id="fetal_movements" name="fetal_movements" 
                                               value="{{ old('fetal_movements') }}" placeholder="Activos, normales">
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
                                                  id="reason_for_consultation" name="reason_for_consultation" rows="2">{{ old('reason_for_consultation') }}</textarea>
                                        @error('reason_for_consultation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="anamnesis" class="form-label">Anamnesis</label>
                                        <textarea class="form-control @error('anamnesis') is-invalid @enderror" 
                                                  id="anamnesis" name="anamnesis" rows="3">{{ old('anamnesis') }}</textarea>
                                        @error('anamnesis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="physical_exam" class="form-label">Exploración Física</label>
                                        <textarea class="form-control @error('physical_exam') is-invalid @enderror" 
                                                  id="physical_exam" name="physical_exam" rows="3">{{ old('physical_exam') }}</textarea>
                                        @error('physical_exam')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="presumptive_diagnosis" class="form-label">Diagnóstico Presuntivo</label>
                                        <textarea class="form-control @error('presumptive_diagnosis') is-invalid @enderror" 
                                                  id="presumptive_diagnosis" name="presumptive_diagnosis" rows="2">{{ old('presumptive_diagnosis') }}</textarea>
                                        @error('presumptive_diagnosis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="treatment" class="form-label">Tratamiento</label>
                                        <textarea class="form-control @error('treatment') is-invalid @enderror" 
                                                  id="treatment" name="treatment" rows="3">{{ old('treatment') }}</textarea>
                                        @error('treatment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="evolution" class="form-label">Evolución</label>
                                        <textarea class="form-control @error('evolution') is-invalid @enderror" 
                                                  id="evolution" name="evolution" rows="2">{{ old('evolution') }}</textarea>
                                        @error('evolution')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="observations" class="form-label">Observaciones</label>
                                        <textarea class="form-control @error('observations') is-invalid @enderror" 
                                                  id="observations" name="observations" rows="2">{{ old('observations') }}</textarea>
                                        @error('observations')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="recommendations" class="form-label">Recomendaciones</label>
                                        <textarea class="form-control @error('recommendations') is-invalid @enderror" 
                                                  id="recommendations" name="recommendations" rows="2">{{ old('recommendations') }}</textarea>
                                        @error('recommendations')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i> Guardar Historia Clínica
                                    </button>
                                    <a href="{{ route('clinical-records.index') }}" class="btn btn-secondary">
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
document.addEventListener('DOMContentLoaded', function() {
    const invoiceSelect = document.getElementById('invoice_id');
    const patientSelect = document.getElementById('patient_id');
    const doctorSelect = document.getElementById('doctor_id');
    const doctorHidden = document.getElementById('doctor_id_hidden');
    const branchSelect = document.getElementById('branch_id');
    const branchHidden = document.getElementById('branch_id_hidden');
    const consultationDate = document.getElementById('consultation_date');

    // Función para auto-llenar campos al seleccionar factura
    function autoFillFromInvoice() {
        const selectedOption = invoiceSelect.options[invoiceSelect.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            patientSelect.value = '';
            doctorSelect.value = '';
            doctorHidden.value = '';
            branchSelect.value = '';
            branchHidden.value = '';
            consultationDate.value = '{{ date('Y-m-d') }}';
            return;
        }

        const patientId = selectedOption.getAttribute('data-patient-id');
        const doctorId = selectedOption.getAttribute('data-doctor-id');
        const doctorName = selectedOption.getAttribute('data-doctor-name');
        const branchId = selectedOption.getAttribute('data-branch-id');
        const branchName = selectedOption.getAttribute('data-branch-name');
        const date = selectedOption.getAttribute('data-date');

        if (patientId) {
            patientSelect.value = patientId;
        }

        if (doctorId) {
            doctorSelect.value = doctorId;
            doctorHidden.value = doctorId;
        }

        if (branchId) {
            branchSelect.value = branchId;
            branchHidden.value = branchId;
        }

        if (date) {
            consultationDate.value = date;
        }
    }

    // Evento change en el select de factura
    invoiceSelect.addEventListener('change', autoFillFromInvoice);

    // Si ya hay una factura seleccionada
    if (invoiceSelect.value) {
        autoFillFromInvoice();
    }

    // ============================================
    // Calcular IMC automáticamente
    // ============================================
    const weightInput = document.getElementById('weight');
    const heightInput = document.getElementById('height');

    function calculateBMI() {
        const weight = parseFloat(weightInput.value);
        const height = parseFloat(heightInput.value);
        
        if (weight && height && height > 0) {
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            
            let bmiDisplay = document.getElementById('bmi_display');
            if (!bmiDisplay) {
                bmiDisplay = document.createElement('span');
                bmiDisplay.id = 'bmi_display';
                bmiDisplay.className = 'text-muted ms-2';
                const parent = weightInput.parentElement;
                parent.appendChild(bmiDisplay);
            }
            bmiDisplay.textContent = 'IMC: ' + bmi.toFixed(2);
        }
    }

    weightInput.addEventListener('input', calculateBMI);
    heightInput.addEventListener('input', calculateBMI);

    // ============================================
    // Validación: La factura debe estar seleccionada
    // ============================================
    document.getElementById('clinicalRecordForm').addEventListener('submit', function(e) {
        if (!invoiceSelect.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes seleccionar una factura para continuar.'
            });
            invoiceSelect.focus();
        }
    });
});
</script>
@endpush
</x-app-layout>