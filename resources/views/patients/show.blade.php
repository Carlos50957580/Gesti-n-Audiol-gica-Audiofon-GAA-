<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<div class="card">

<div class="card-header">
<h4>Información del Paciente</h4>
</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">
<strong>Nombre:</strong><br>
{{ $patient->first_name }} {{ $patient->last_name }}
</div>

<div class="col-md-6 mb-3">
<strong>Cédula:</strong><br>
{{ $patient->cedula }}
</div>

<div class="col-md-6 mb-3">
<strong>Teléfono:</strong><br>
{{ $patient->phone }}
</div>

<div class="col-md-6 mb-3">
<strong>Email:</strong><br>
{{ $patient->email }}
</div>

<div class="col-md-6 mb-3">
<strong>Fecha de nacimiento:</strong><br>
{{ $patient->birth_date }}
</div>

<div class="col-md-6 mb-3">
<strong>Dirección:</strong><br>
{{ $patient->address }}
</div>

</div>

<a href="{{ route('patients.index') }}" class="btn btn-light mt-3">
Volver
</a>

</div>
</div>

</div>
</div>

</x-app-layout>