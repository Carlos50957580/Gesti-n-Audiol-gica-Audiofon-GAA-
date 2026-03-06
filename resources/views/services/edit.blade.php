<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<h4>Editar Servicio</h4>

<div class="card">

<div class="card-body">

<form action="{{ route('services.update',$service) }}" method="POST">

@csrf
@method('PUT')

@include('services.form')

<button class="btn btn-success">Actualizar</button>

<a href="{{ route('services.index') }}" class="btn btn-light">
Cancelar
</a>

</form>

</div>
</div>

</div>
</div>

</x-app-layout>