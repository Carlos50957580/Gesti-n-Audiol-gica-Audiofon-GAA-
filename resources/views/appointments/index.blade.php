<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<!-- Título -->
<div class="row mb-3">
<div class="col-12">
<div class="page-title-box d-sm-flex align-items-center justify-content-between">

<h4 class="mb-0">Gestión de Citas</h4>

<div class="page-title-right">
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Citas</li>
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
<h5 class="card-title mb-0 flex-grow-1">Lista de Citas</h5>

<div class="flex-shrink-0">
<a href="{{ route('appointments.create') }}" class="btn btn-primary">
<i class="ri-add-line me-1"></i> Nueva Cita
</a>
</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-nowrap align-middle">

<thead class="table-light">

<tr>
<th>ID</th>
<th>Paciente</th>
<th>Audiólogo</th>
<th>Sucursal</th>
<th>Fecha</th>
<th>Hora</th>
<th>Estado</th>
<th class="text-center">Acciones</th>
</tr>

</thead>

<tbody>

@forelse($appointments as $appointment)

<tr>

<td>{{ $appointment->id }}</td>

<td>
{{ $appointment->patient->first_name }}
{{ $appointment->patient->last_name }}
</td>

<td>{{ $appointment->audiologist->name }}</td>

<td>{{ $appointment->branch->name }}</td>

<td>{{ $appointment->appointment_date }}</td>

<td>{{ $appointment->appointment_time }}</td>

<td>

<span class="badge 
@if($appointment->status=='programada') bg-info
@elseif($appointment->status=='completada') bg-success
@else bg-danger
@endif">

{{ ucfirst($appointment->status) }}

</span>

</td>

<td class="text-center">

<div class="hstack gap-2 justify-content-center">

<a href="{{ route('appointments.show',$appointment) }}"
class="btn btn-sm btn-soft-info">
<i class="ri-eye-fill"></i>
</a>

<a href="{{ route('appointments.edit',$appointment) }}"
class="btn btn-sm btn-soft-warning">
<i class="ri-pencil-fill"></i>
</a>

<form action="{{ route('appointments.destroy',$appointment) }}"
method="POST"
class="delete-form">

@csrf
@method('DELETE')

<button type="button"
class="btn btn-sm btn-soft-danger btn-delete"
data-name="Cita #{{ $appointment->id }}">

<i class="ri-delete-bin-fill"></i>

</button>

</form>

</div>

</td>

</tr>

@empty

<tr>
<td colspan="7" class="text-center py-4">
<i class="ri-calendar-line display-5 text-muted"></i>
<p class="text-muted mt-3">No hay citas registradas.</p>
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

@if($appointments->hasPages())
<div class="d-flex justify-content-end mt-3">
{{ $appointments->links() }}
</div>
@endif

</div>
</div>

</div>
</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.querySelectorAll('.btn-delete').forEach(function(btn){

btn.addEventListener('click',function(){

const form=this.closest('.delete-form');
const name=this.dataset.name;

Swal.fire({
title:'¿Eliminar?',
text:name,
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#dc3545',
confirmButtonText:'Eliminar',
cancelButtonText:'Cancelar'

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