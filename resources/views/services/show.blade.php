<x-app-layout>

<div class="page-content">
<div class="container-fluid">

<h4>Detalle del Servicio</h4>

<div class="card">

<div class="card-body">

<p><strong>Nombre:</strong> {{ $service->name }}</p>

<p><strong>Precio:</strong> RD$ {{ number_format($service->price,2) }}</p>

<p><strong>Descripción:</strong> {{ $service->description }}</p>

<p>
<strong>Estado:</strong>

<span class="badge {{ $service->active ? 'bg-success':'bg-danger' }}">
{{ $service->active ? 'Activo':'Inactivo' }}
</span>

</p>

<a href="{{ route('services.index') }}" class="btn btn-light">
Volver
</a>

</div>
</div>

</div>
</div>

</x-app-layout>