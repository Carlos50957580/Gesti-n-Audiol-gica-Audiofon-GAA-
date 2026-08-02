@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Detalle de Cita</h4>
            <div class="page-title-right">
                <a href="{{ route('doctor.appointments.index') }}" class="btn btn-secondary">
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
                <!-- Información de la cita -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Paciente</p>
                        <h5>{{ $appointment->patient->full_name ?? 'N/A' }}</h5>
                        <p class="text-muted">Cédula: {{ $appointment->patient->cedula ?? 'N/A' }}</p>
                        <p class="text-muted">Teléfono: {{ $appointment->patient->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Fecha y Hora</p>
                        <h5>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</h5>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                        <p class="text-muted">Sucursal: {{ $appointment->branch->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Servicios -->
                <div class="mb-4">
                    <p class="text-muted mb-2">Servicios a realizar</p>
                    @foreach($appointment->services as $service)
                        <span class="badge bg-primary me-1">{{ $service->name }}</span>
                    @endforeach
                </div>

                <!-- Estado -->
                <div class="mb-4">
                    <p class="text-muted mb-2">Estado actual</p>
                    @php
                        $statusColors = ['programada' => 'warning', 'completada' => 'success', 'cancelada' => 'danger'];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }} p-2 fs-6">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>

                <!-- Notas -->
                <div>
                    <p class="text-muted mb-2">Notas</p>
                    <p>{{ $appointment->notes ?? 'Sin notas' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar de acciones -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                <!-- Cambiar estado -->
                <form action="{{ route('doctor.appointments.status', $appointment) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Cambiar estado</label>
                        <select name="status" class="form-select">
                            <option value="programada" {{ $appointment->status == 'programada' ? 'selected' : '' }}>Programada</option>
                            <option value="completada" {{ $appointment->status == 'completada' ? 'selected' : '' }}>Completada</option>
                            <option value="cancelada" {{ $appointment->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Actualizar estado</button>
                </form>

                <!-- Actualizar notas -->
                <form action="{{ route('doctor.appointments.notes', $appointment) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $appointment->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">Actualizar notas</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection