<x-app-layout>
@section('title', 'Historias Clínicas')

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ri-file-history-line me-1"></i> Historias Clínicas
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Historias Clínicas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- TURNOS / FACTURAS PENDIENTES DE ATENCIÓN      -->
        <!-- ============================================ -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="ri-user-follow-line me-1"></i> Turnos de Atención
                            </h5>
                            <span class="badge bg-light text-dark">
                                {{ $pendingInvoices->count() }} pendientes
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pendingInvoices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50px;">#</th>
                                            <th>Paciente</th>
                                            <th>Factura</th>
                                            <th>Fecha Factura</th>
                                            <th>Médico</th>
                                            <th>Sucursal</th>
                                            <th style="width:120px;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingInvoices as $index => $invoice)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary rounded-pill fs-6">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                                {{ strtoupper(substr($invoice->patient->first_name ?? 'P', 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $invoice->patient->full_name ?? 'N/A' }}</h6>
                                                        <small class="text-muted">Cédula: {{ $invoice->patient->cedula ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $invoice->invoice_number }}</span>
                                            </td>
                                            <td>
                                                {{ $invoice->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>{{ $invoice->doctor->name ?? 'N/A' }}</td>
                                            <td>{{ $invoice->branch->name ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('clinical-records.create', ['invoice_id' => $invoice->id]) }}" 
                                                   class="btn btn-success btn-sm w-100">
                                                    <i class="ri-play-circle-line me-1"></i> Iniciar Atención
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ri-check-double-line fs-1 text-success"></i>
                                <p class="text-muted mt-2">No hay turnos pendientes de atención.</p>
                                <small class="text-muted">Todas las facturas que requieren historia clínica ya han sido atendidas.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- HISTORIAS CLÍNICAS COMPLETADAS                -->
        <!-- ============================================ -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0 text-white">
                            <i class="ri-file-list-line me-1"></i> Historias Clínicas Completadas
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <form method="GET" class="row g-3 mb-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Buscar paciente...">
                            </div>
                            
                            <div class="col-md-2">
                                <select class="form-select" name="patient_id">
                                    <option value="">Todos los pacientes</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" 
                                            {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select class="form-select" name="status">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="completada" {{ request('status') == 'completada' ? 'selected' : '' }}>Completada</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <input type="date" class="form-control" name="date_from" 
                                       value="{{ request('date_from') }}" placeholder="Fecha desde">
                            </div>
                            
                            <div class="col-md-2">
                                <input type="date" class="form-control" name="date_to" 
                                       value="{{ request('date_to') }}" placeholder="Fecha hasta">
                            </div>
                            
                            @if($isAdmin)
                            <div class="col-md-2">
                                <select class="form-select" name="branch_id">
                                    <option value="">Todas las sucursales</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" 
                                            {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-search-line me-1"></i> Filtrar
                                </button>
                                <a href="{{ route('clinical-records.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-refresh-line me-1"></i> Limpiar
                                </a>
                            </div>
                        </form>

                        <!-- Tabla de historias completadas -->
                        @if($records->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Paciente</th>
                                            <th>Médico</th>
                                            <th>Fecha Consulta</th>
                                            <th>Factura</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($records as $record)
                                        <tr>
                                            <td>{{ $record->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-2">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                                {{ strtoupper(substr($record->patient->first_name ?? 'P', 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $record->patient->full_name ?? 'N/A' }}</h6>
                                                        <small class="text-muted">Cédula: {{ $record->patient->cedula ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $record->doctor->name ?? 'N/A' }}</td>
                                            <td>{{ $record->consultation_date ? $record->consultation_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $record->invoice->invoice_number ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $record->consultation_type_label }}</span>
                                            </td>
                                            <td>
                                                @if($record->status === 'completada')
                                                    <span class="badge bg-success">Completada</span>
                                                @else
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('clinical-records.show', $record) }}" 
                                                       class="btn btn-sm btn-info" title="Ver">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    @if($record->canBeEdited())
                                                        <a href="{{ route('clinical-records.edit', $record) }}" 
                                                           class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="ri-edit-line"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->role_id === 1 && auth()->user()->is_doctor == 1)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Eliminar"
                                                                onclick="confirmDelete('{{ route('clinical-records.destroy', $record) }}')">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                {{ $records->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ri-file-list-line fs-1 text-muted"></i>
                                <p class="text-muted mt-2">No hay historias clínicas registradas.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(url) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer. Se eliminará la historia clínica y todos sus documentos.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Auto-refresh cada 30 segundos para actualizar turnos
setTimeout(function() {
    location.reload();
}, 30000);
</script>
@endpush
</x-app-layout>