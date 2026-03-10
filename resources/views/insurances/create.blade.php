<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<div class="row">
<div class="col-lg-12">

<div class="card">

<div class="card-header">
<h5 class="card-title">Registrar Aseguradora</h5>
</div>

<div class="card-body">

<form action="{{ route('insurances.store') }}" method="POST">

@csrf

@include('insurances.form')

<button class="btn btn-primary">
Guardar
</button>

<a href="{{ route('insurances.index') }}" class="btn btn-light">
Cancelar
</a>

</form>

</div>

</div>

</div>
</div>

</div>
</div>

</x-app-layout>