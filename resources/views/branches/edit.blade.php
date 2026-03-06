<x-app-layout>

<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<div class="row mb-3">
<div class="col-12">

<div class="page-title-box d-sm-flex align-items-center justify-content-between">

<h4 class="mb-0">Editar Sucursal</h4>

<div class="page-title-right">
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Sucursales</a></li>
<li class="breadcrumb-item active">Editar</li>
</ol>
</div>

</div>
</div>
</div>

<div class="card">
<div class="card-body">

<form action="{{ route('branches.update',$branch) }}" method="POST">
    @csrf
    @method('PUT')

    @include('branches.form')

    <button class="btn btn-success">
        <i class="ri-check-line"></i> Actualizar
    </button>
</form>

</div>
</div>

</div>
</div>

</x-app-layout>
