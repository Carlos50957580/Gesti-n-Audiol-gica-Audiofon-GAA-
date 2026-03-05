<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<div class="row mb-3">

<div class="col-12">

<div class="page-title-box d-sm-flex align-items-center justify-content-between">

<h4 class="mb-0">Editar Cita</h4>

<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Citas</a></li>
<li class="breadcrumb-item active">Editar</li>
</ol>

</div>

</div>
</div>

<div class="card">

<div class="card-body">

<form action="{{ route('appointments.update',$appointment) }}" method="POST">

@csrf
@method('PUT')

@include('appointments.form')

<div class="mt-3">

<button class="btn btn-warning">
Actualizar Cita
</button>

<a href="{{ route('appointments.index') }}" class="btn btn-light">
Cancelar
</a>

</div>

</form>

</div>
</div>

</div>
</div>

</x-app-layout>