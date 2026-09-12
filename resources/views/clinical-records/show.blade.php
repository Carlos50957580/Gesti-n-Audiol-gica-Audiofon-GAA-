<x-app-layout>
@section('title', 'Historia Clínica #' . $clinicalRecord->id)

<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ri-file-history-line me-1"></i> Historia Clínica #{{ $clinicalRecord->id }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('clinical-records.index') }}">Historias Clínicas</a></li>
                            <li class="breadcrumb-item active">#{{ $clinicalRecord->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Acciones -->
                        <div class="d-flex justify-content-end gap-2 mb-3">
                            <a href="{{ route('clinical-records.index') }}" class="btn btn-secondary btn-sm">
                                <i class="ri-arrow-left-line me-1"></i> Volver
                            </a>
                            @if($clinicalRecord->canBeEdited())
                                <a href="{{ route('clinical-records.edit', $clinicalRecord) }}" class="btn btn-warning btn-sm">
                                    <i class="ri-edit-line me-1"></i> Editar
                                </a>
                            @endif

                            <a href="{{ route('clinical-records.print', $clinicalRecord) }}" 
   class="btn btn-info btn-sm" target="_blank">
    <i class="ri-printer-line me-1"></i> Imprimir historia
</a>
                            @if(auth()->user()->role_id === 1 && auth()->user()->is_doctor == 1)
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete()">
                                    <i class="ri-delete-bin-line me-1"></i> Eliminar
                                </button>
                            @endif

                        </div>

                        <!-- Información del Paciente -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="ri-user-line me-1"></i> Datos del Paciente</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar-md me-3">
                                                <span class="avatar-title rounded-circle bg-primary bg-soft text-primary fs-3">
                                                    {{ strtoupper(substr($clinicalRecord->patient->first_name ?? 'P', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">{{ $clinicalRecord->patient->full_name ?? 'N/A' }}</h5>
                                                <p class="text-muted mb-0">
                                                    <small>Cédula: {{ $clinicalRecord->patient->cedula ?? 'N/A' }}</small>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <small class="text-muted">Teléfono:</small>
                                                <p class="mb-0">{{ $clinicalRecord->patient->phone ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Email:</small>
                                                <p class="mb-0">{{ $clinicalRecord->patient->email ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-12">
                                                <small class="text-muted">Dirección:</small>
                                                <p class="mb-0">{{ $clinicalRecord->patient->address ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="ri-information-line me-1"></i> Datos de la Consulta</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Fecha:</small>
                                                <p class="mb-2"><strong>{{ $clinicalRecord->consultation_date ? $clinicalRecord->consultation_date->format('d/m/Y') : 'N/A' }}</strong></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Tipo:</small>
                                                <p class="mb-2"><span class="badge bg-info">{{ $clinicalRecord->consultation_type_label }}</span></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Médico:</small>
                                                <p class="mb-2"><strong>{{ $clinicalRecord->doctor->name ?? 'N/A' }}</strong></p>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Sucursal:</small>
                                                <p class="mb-2"><strong>{{ $clinicalRecord->branch->name ?? 'N/A' }}</strong></p>
                                            </div>
                                            <div class="col-12">
                                                <small class="text-muted">Factura:</small>
                                                <p class="mb-0"><strong>{{ $clinicalRecord->invoice->invoice_number ?? 'N/A' }}</strong></p>
                                            </div>
                                            @if($clinicalRecord->consultation_reason)
                                                <div class="col-12 mt-2">
                                                    <small class="text-muted">Motivo:</small>
                                                    <p class="mb-0"><strong>{{ $clinicalRecord->consultation_reason }}</strong></p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Signos Vitales -->
                        @if($clinicalRecord->vital_signs && array_filter($clinicalRecord->vital_signs))
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="ri-heart-pulse-line me-1"></i> Signos Vitales y Medidas</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($clinicalRecord->formatted_vital_signs as $key => $value)
                                                <div class="col-md-3 col-6 mb-2">
                                                    <small class="text-muted d-block">{{ $key }}:</small>
                                                    <strong>{{ $value }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Historia Clínica -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="ri-file-list-line me-1"></i> Historia Clínica</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($clinicalRecord->reason_for_consultation)
                                            <div class="mb-3">
                                                <small class="text-muted">Motivo de Consulta:</small>
                                                <p class="mb-0">{{ $clinicalRecord->reason_for_consultation }}</p>
                                            </div>
                                            <hr>
                                        @endif

                                        @if($clinicalRecord->anamnesis)
                                            <div class="mb-3">
                                                <small class="text-muted">Anamnesis:</small>
                                                <p class="mb-0">{{ $clinicalRecord->anamnesis }}</p>
                                            </div>
                                            <hr>
                                        @endif

                                        @if($clinicalRecord->physical_exam)
                                            <div class="mb-3">
                                                <small class="text-muted">Exploración Física:</small>
                                                <p class="mb-0">{{ $clinicalRecord->physical_exam }}</p>
                                            </div>
                                            <hr>
                                        @endif

                                        @if($clinicalRecord->presumptive_diagnosis)
                                            <div class="mb-3">
                                                <small class="text-muted">Diagnóstico Presuntivo:</small>
                                                <p class="mb-0">{{ $clinicalRecord->presumptive_diagnosis }}</p>
                                            </div>
                                            <hr>
                                        @endif

                                        @if($clinicalRecord->treatment)
                                            <div class="mb-3">
                                                <small class="text-muted">Tratamiento:</small>
                                                <p class="mb-0">{{ $clinicalRecord->treatment }}</p>
                                            </div>
                                            <hr>
                                        @endif

                                        @if($clinicalRecord->evolution)
                                            <div class="mb-3">
                                                <small class="text-muted">Evolución:</small>
                                                <p class="mb-0">{{ $clinicalRecord->evolution }}</p>
                                            </div>
                                            <hr>
                                        @endif

                                        @if($clinicalRecord->observations)
                                            <div class="mb-3">
                                                <small class="text-muted">Observaciones:</small>
                                                <p class="mb-0">{{ $clinicalRecord->observations }}</p>
                                            </div>
                                            <hr>
                                        @endif

                                        @if($clinicalRecord->recommendations)
                                            <div class="mb-3">
                                                <small class="text-muted">Recomendaciones:</small>
                                                <p class="mb-0">{{ $clinicalRecord->recommendations }}</p>
                                            </div>
                                        @endif

                                        @if(!$clinicalRecord->anamnesis && !$clinicalRecord->reason_for_consultation && !$clinicalRecord->physical_exam)
                                            <p class="text-muted text-center mb-0">No hay información registrada</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documentos -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="ri-file-pdf-line me-1"></i> Documentos</h6>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                            <i class="ri-upload-line me-1"></i> Subir Documento
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        @if($clinicalRecord->documents->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Nombre</th>
                                                            <th>Archivo</th>
                                                            <th>Tamaño</th>
                                                            <th>Subido por</th>
                                                            <th>Fecha</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="documents-list">
                                                        @foreach($clinicalRecord->documents as $document)
                                                            <tr id="doc-{{ $document->id }}">
                                                                <td>{{ $document->name }}</td>
                                                                <td>{{ $document->file_name }}</td>
                                                                <td>{{ $document->file_size_formatted }}</td>
                                                                <td>{{ $document->uploader->name ?? 'Sistema' }}</td>
                                                                <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <a href="{{ $document->file_url }}" target="_blank" class="btn btn-sm btn-info">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                        <a href="{{ $document->file_url }}" download class="btn btn-sm btn-success">
                                                                            <i class="ri-download-line"></i>
                                                                        </a>
                                                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteDocument({{ $document->id }})">
                                                                            <i class="ri-delete-bin-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center mb-0">No hay documentos asociados</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Subir Documento -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Subir Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Documento <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document_file" name="document" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Formatos: PDF, DOC, DOCX, JPG, PNG (Max 10MB)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="document_name" name="name" placeholder="Nombre del documento">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="document_description" name="description" rows="2" placeholder="Descripción del documento"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="ri-upload-line me-1"></i> Subir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Eliminar historia clínica
    function confirmDelete() {
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
                form.action = '{{ route("clinical-records.destroy", $clinicalRecord) }}';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Subir documento
    document.getElementById('uploadDocumentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="ri-loader-4-line ri-spin me-1"></i> Subiendo...';
        
        fetch('{{ route("clinical-records.upload-document", $clinicalRecord) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Agregar documento a la tabla
                const tbody = document.getElementById('documents-list');
                const row = document.createElement('tr');
                row.id = 'doc-' + data.document.id;
                row.innerHTML = `
                    <td>${data.document.name}</td>
                    <td>${data.document.file_name}</td>
                    <td>${data.document.file_size_formatted}</td>
                    <td>${data.document.uploader_name || 'Sistema'}</td>
                    <td>${data.document.created_at}</td>
                    <td>
                        <div class="btn-group">
                            <a href="${data.document.file_url}" target="_blank" class="btn btn-sm btn-info">
                                <i class="ri-eye-line"></i>
                            </a>
                            <a href="${data.document.file_url}" download class="btn btn-sm btn-success">
                                <i class="ri-download-line"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteDocument(${data.document.id})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
                
                // Limpiar formulario
                document.getElementById('document_file').value = '';
                document.getElementById('document_name').value = '';
                document.getElementById('document_description').value = '';
                
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal')).hide();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Documento subido',
                    text: 'El documento se ha subido exitosamente.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Error al subir el documento.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al subir el documento: ' + error.message
            });
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="ri-upload-line me-1"></i> Subir';
        });
    });

    // Eliminar documento
    function deleteDocument(documentId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Este documento será eliminado permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ url("clinical-records/documents") }}/' + documentId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('doc-' + documentId).remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'Documento eliminado exitosamente.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al eliminar el documento.'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al eliminar el documento: ' + error.message
                    });
                });
            }
        });
    }
</script>
@endpush
</x-app-layout>