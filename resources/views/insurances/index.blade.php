<x-app-layout>

<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<div class="row mb-3">
<div class="col-12">
<div class="page-title-box d-sm-flex align-items-center justify-content-between">

<h4 class="mb-0">Aseguradoras</h4>

<div>
<a href="{{ route('insurances.create') }}" class="btn btn-primary">
<i class="ri-add-line"></i> Nueva Aseguradora
</a>
</div>

</div>
</div>
</div>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<div class="card">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead class="table-light">
<tr>

<th>ID</th>
<th>Nombre</th>
<th>Teléfono</th>
<th>Correo</th>
<th>Cobertura</th>
<th>Estado</th>
<th class="text-center">Acciones</th>

</tr>
</thead>

<tbody>

@forelse($insurances as $insurance)

<tr>

<td>{{ $insurance->id }}</td>

<td>{{ $insurance->name }}</td>

<td>{{ $insurance->phone }}</td>

<td>{{ $insurance->email }}</td>

<td>{{ $insurance->coverage_percentage }} %</td>

<td>

@if($insurance->active)

<span class="badge bg-success">Activa</span>

@else

<span class="badge bg-danger">Inactiva</span>

@endif

</td>

<td class="text-center">

<a href="{{ route('insurances.show',$insurance) }}" class="btn btn-sm btn-soft-info">
<i class="ri-eye-fill"></i>
</a>

<a href="{{ route('insurances.edit',$insurance) }}" class="btn btn-sm btn-soft-warning">
<i class="ri-pencil-fill"></i>
</a>

<form action="{{ route('insurances.destroy',$insurance) }}" method="POST" class="d-inline">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-soft-danger">
<i class="ri-delete-bin-fill"></i>
</button>

</form>

</td>

</tr>

@empty

<tr>
<td colspan="7" class="text-center">No hay aseguradoras registradas</td>
</tr>

@endforelse

</tbody>

</table>

</div>

<div class="mt-3">
{{ $insurances->links() }}
</div>

</div>
</div>

</div>
</div>

</x-app-layout>