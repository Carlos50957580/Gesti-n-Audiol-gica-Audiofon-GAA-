<x-app-layout>

<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<div class="row mb-3">
<div class="col-12">

<div class="page-title-box d-sm-flex align-items-center justify-content-between">

<h4 class="mb-0">Sucursales</h4>

<div class="page-title-right">
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Sucursales</li>
</ol>
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

<div class="card-header d-flex align-items-center">

<h5 class="card-title flex-grow-1 mb-0">Lista de Sucursales</h5>

<a href="{{ route('branches.create') }}" class="btn btn-primary">
    <i class="ri-add-line"></i> Nueva Sucursal
</a>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead class="table-light">
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Dirección</th>
<th>Teléfono</th>
<th class="text-center">Acciones</th>
</tr>
</thead>

<tbody>

@forelse($branches as $branch)

<tr>
<td>{{ $branch->id }}</td>
<td>{{ $branch->name }}</td>
<td>{{ $branch->address }}</td>
<td>{{ $branch->phone }}</td>

<td class="text-center">

<a href="{{ route('branches.show',$branch) }}" class="btn btn-sm btn-soft-info">
    <i class="ri-eye-fill"></i>
</a>

<a href="{{ route('branches.edit',$branch) }}" class="btn btn-sm btn-soft-warning">
    <i class="ri-pencil-fill"></i>
</a>

<form action="{{ route('branches.destroy',$branch) }}" method="POST" class="d-inline">
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
<td colspan="5" class="text-center">
    No hay sucursales registradas
</td>
</tr>
@endforelse

</tbody>

</table>

</div>

{{ $branches->links() }}

</div>
</div>

</div>
</div>

</x-app-layout>
