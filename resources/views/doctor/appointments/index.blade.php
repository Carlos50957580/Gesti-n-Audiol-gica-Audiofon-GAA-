@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Mis Citas</h4>
            <div class="page-title-right">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-3">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-16">
                                <i class="ri-calendar-check-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Total Citas</p>
                        <h4 class="mb-0">{{ $total }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title rounded-circle bg-info-subtle text-info fs-16">
                                <i class="ri-time-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Programadas</p>
                        <h4 class="mb-0">{{ $programadas }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title rounded-circle bg-success-subtle text-success fs-16">
                                <i class="ri-checkbox-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Completadas</p>
                        <h4 class="mb-0">{{ $completadas }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title rounded-circle bg-danger-subtle text-danger fs-16">
                                <i class="ri-close-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1">Canceladas</p>
                        <h4 class="mb-0">{{ $canceladas }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-centered">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Sucursal</th>
                        <th>Estado</th>
                        <th>Servicios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar-xs">
                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                            {{ strtoupper(substr($appt->patient->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($appt->patient->last_name ?? '', 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="fw-medium">{{ $appt->patient->full_name ?? 'N/A' }}</span>
                                    <br><small class="text-muted">{{ $appt->patient->cedula ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                        <td>{{ $appt->branch->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $statusColors = ['programada' => 'warning', 'completada' => 'success', 'cancelada' => 'danger'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$appt->status] ?? 'secondary' }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </td>
                        <td>
                            @foreach($appt->services as $service)
                                <span class="badge bg-info me-1">{{ $service->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('doctor.appointments.show', $appt) }}" class="btn btn-soft-primary btn-sm">
                                <i class="ri-eye-line"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="ri-calendar-line" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                            No tienes citas asignadas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection