@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Reportes - Administrador</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Reportes</li>
            </ol>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.reports.invoices') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-sm mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-20">
                            <i class="ri-file-text-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Facturación</h5>
                    <p class="card-text text-muted small">Ingresos, pagos, seguros y más</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.reports.appointments') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-sm mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-success-subtle text-success fs-20">
                            <i class="ri-calendar-check-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Citas</h5>
                    <p class="card-text text-muted small">Programadas, completadas, canceladas</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.reports.patients') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-sm mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-warning-subtle text-warning fs-20">
                            <i class="ri-team-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Pacientes</h5>
                    <p class="card-text text-muted small">Nuevos, por seguro, por género</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.reports.services') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-sm mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-info-subtle text-info fs-20">
                            <i class="ri-stethoscope-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Servicios</h5>
                    <p class="card-text text-muted small">Más facturados, por categoría</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.reports.fees') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-sm mx-auto mb-3">
                        <span class="avatar-title rounded-circle bg-danger-subtle text-danger fs-20">
                            <i class="ri-money-dollar-circle-line"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Honorarios</h5>
                    <p class="card-text text-muted small">Médicos, pagados, pendientes</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection