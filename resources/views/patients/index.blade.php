<x-app-layout>
<div class="page-content" style="padding-top: 0;">
<div class="container-fluid pt-3">

<!-- PAGE TITLE -->
<div class="row mb-3">
<div class="col-12">
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
<h4 class="mb-0">Gestión de Pacientes</h4>

<div class="page-title-right">
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Pacientes</li>
</ol>
</div>

</div>
</div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
<i class="ri-check-line me-2"></i>{{ session('success') }}
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">

<div class="card-header d-flex align-items-center">
<h5 class="card-title mb-0 flex-grow-1">Lista de Pacientes</h5>

<div class="flex-shrink-0">
<a href="{{ route('patients.create') }}" class="btn btn-primary">
<i class="ri-add-line align-bottom me-1"></i> Nuevo Paciente
</a>
</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-nowrap align-middle mb-0">

<thead class="table-light">
<tr>
<th style="width:60px">ID</th>
<th>Nombre</th>
<th>Cédula</th>
<th>Teléfono</th>
<th>Sucursal</th>
<th>Seguro</th>
<th>Email</th>
<th class="text-center" style="width:200px">Acciones</th>
</tr>
</thead>

<tbody>

@forelse($patients as $patient)

<tr>

<td class="fw-medium">{{ $patient->id }}</td>

<td>
{{ $patient->first_name }} {{ $patient->last_name }}
</td>

<td>{{ $patient->cedula }}</td>

<td>{{ $patient->phone }}</td>

<td>{{ $patient->branch->name ?? '-' }}</td>

<td>{{ $patient->insurance->name ?? 'Privado' }} </td>

<td>{{ $patient->email }}</td>

<td class="text-center">

<div class="hstack gap-2 justify-content-center">

<a href="{{ route('patients.show',$patient) }}" class="btn btn-sm btn-soft-info">
<i class="ri-eye-fill"></i>
</a>

<a href="{{ route('patients.edit',$patient) }}" class="btn btn-sm btn-soft-warning">
<i class="ri-pencil-fill"></i>
</a>

<form action="{{ route('patients.destroy',$patient) }}" method="POST" class="d-inline delete-form">

@csrf
@method('DELETE')

<button type="button" class="btn btn-sm btn-soft-danger btn-delete"
data-name="{{ $patient->first_name }} {{ $patient->last_name }}">

<i class="ri-delete-bin-fill"></i>

</button>

</form>

</div>

</td>

</tr>

@empty

<tr>
<td colspan="6" class="text-center py-4">
<i class="ri-user-line display-5 text-muted"></i>
<p class="text-muted mt-3">No hay pacientes registrados.</p>
</td>
</tr>

@endforelse

</tbody>
</table>

</div>

@if($patients->hasPages())

<div class="d-flex justify-content-end mt-3">
{{ $patients->links() }}
</div>

@endif

</div>
</div>

</div>
</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.querySelectorAll('.btn-delete').forEach(function(button){

button.addEventListener('click',function(){

const form=this.closest('.delete-form');
const name=this.getAttribute('data-name');

Swal.fire({

title:'¿Eliminar paciente?',
html:`Se eliminará el paciente <strong>${name}</strong>`,
icon:'warning',
showCancelButton:true,
confirmButtonText:'Sí, eliminar',
cancelButtonText:'Cancelar',
confirmButtonColor:'#dc3545'

}).then((result)=>{

if(result.isConfirmed){
form.submit();
}

});

});

});

</script>

@endpush

</x-app-layout>