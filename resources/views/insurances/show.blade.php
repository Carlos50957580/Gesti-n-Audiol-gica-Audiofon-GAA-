<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<div class="card">

<div class="card-header">
<h5 class="card-title">Detalle de Aseguradora</h5>
</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">
<strong>Nombre:</strong><br>
{{ $insurance->name }}
</div>

<div class="col-md-6 mb-3">
<strong>Teléfono:</strong><br>
{{ $insurance->phone }}
</div>

<div class="col-md-6 mb-3">
<strong>Teléfono autorización:</strong><br>
{{ $insurance->authorization_phone }}
</div>

<div class="col-md-6 mb-3">
<strong>Correo:</strong><br>
{{ $insurance->email }}
</div>

<div class="col-md-6 mb-3">
<strong>Cobertura base:</strong><br>
{{ $insurance->coverage_percentage }} %
</div>

<div class="col-md-6 mb-3">
<strong>Estado:</strong><br>

@if($insurance->active)
<span class="badge bg-success">Activa</span>
@else
<span class="badge bg-danger">Inactiva</span>
@endif

</div>

<div class="col-md-12 mb-3">
<strong>Dirección:</strong><br>
{{ $insurance->address }}
</div>

</div>

<a href="{{ route('insurances.index') }}" class="btn btn-light">
Volver
</a>

</div>

</div>

</div>
</div>

</x-app-layout>