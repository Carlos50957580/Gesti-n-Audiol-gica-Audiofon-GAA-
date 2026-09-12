<x-app-layout>
@section('title', $supplier->name)

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-truck-line me-1"></i>{{ $supplier->name }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Proveedores</a></li>
                            <li class="breadcrumb-item active">Detalle</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title rounded-circle bg-primary bg-soft text-primary fs-2 fw-bold">
                                {{ strtoupper(substr($supplier->name, 0, 1)) }}
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $supplier->name }}</h5>
                        @if($supplier->is_active)
                            <span class="badge bg-success-subtle text-success">Activo</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">Información de Contacto</h5>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">
                            <i class="ri-edit-line me-1"></i>Editar
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">RNC</small>
                                <strong>{{ $supplier->rnc ?? '—' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Contacto</small>
                                <strong>{{ $supplier->contact_name ?? '—' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Email</small>
                                <strong>{{ $supplier->email ?? '—' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Teléfono</small>
                                <strong>{{ $supplier->phone ?? '—' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Móvil</small>
                                <strong>{{ $supplier->mobile ?? '—' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Sitio Web</small>
                                <strong>
                                    @if($supplier->website)
                                        <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                                    @else
                                        —
                                    @endif
                                </strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Dirección</small>
                                <strong>{{ $supplier->address ?? '—' }}</strong>
                            </div>
                            @if($supplier->notes)
                            <div class="col-12">
                                <small class="text-muted d-block">Notas</small>
                                <p class="mb-0">{{ $supplier->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Últimos movimientos --}}
                @if($supplier->movements->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Últimos Movimientos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Referencia</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->movements as $mov)
                                    <tr>
                                        <td><code>{{ $mov->reference }}</code></td>
                                        <td>{{ $mov->movement_date->format('d/m/Y') }}</td>
                                        <td>RD$ {{ number_format($mov->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $mov->status === 'confirmado' ? 'success' : ($mov->status === 'cancelado' ? 'danger' : 'warning') }}-subtle">
                                                {{ $mov->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>