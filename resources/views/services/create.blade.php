<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<h4>Nuevo Servicio</h4>

<div class="card">

<div class="card-body">

<form action="{{ route('services.store') }}" method="POST">

@csrf

@include('services.form')

<button class="btn btn-primary">Guardar</button>

<a href="{{ route('services.index') }}" class="btn btn-light">
Cancelar
</a>

</form>

</div>
</div>

</div>
</div>

</x-app-layout>