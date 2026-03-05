<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<div class="card">

<div class="card-header">
<h4>Detalle de la Cita</h4>
</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">
<strong>Paciente</strong><br>
{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
</div>

<div class="col-md-6 mb-3">
<strong>Audiólogo</strong><br>
{{ $appointment->audiologist->name }}
</div>

<div class="col-md-6 mb-3">
<strong>Fecha</strong><br>
{{ $appointment->appointment_date }}
</div>

<div class="col-md-6 mb-3">
<strong>Hora</strong><br>
{{ $appointment->appointment_time }}
</div>

<div class="col-md-6 mb-3">
<strong>Estado</strong><br>
{{ ucfirst($appointment->status) }}
</div>

<div class="col-md-12 mb-3">
<strong>Notas</strong><br>
{{ $appointment->notes }}
</div>

</div>

<a href="{{ route('appointments.index') }}" class="btn btn-light">
Volver
</a>

</div>
</div>

</div>
</div>

</x-app-layout>