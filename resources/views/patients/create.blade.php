<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<div class="row mb-3">
<div class="col-12">

<div class="page-title-box d-sm-flex align-items-center justify-content-between">

<h4 class="mb-0">Nuevo Paciente</h4>

<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Pacientes</a></li>
<li class="breadcrumb-item active">Crear</li>
</ol>

</div>
</div>
</div>

<div class="card">

<div class="card-body">

<form action="{{ route('patients.store') }}" method="POST">

@csrf

@include('patients.form')

<div class="mt-3">

<button class="btn btn-primary">
Guardar Paciente
</button>

<a href="{{ route('patients.index') }}" class="btn btn-light">
Cancelar
</a>

</div>

</form>

</div>
</div>

</div>
</div>

</x-app-layout>