@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                <i class="ri-store-3-line me-1"></i> Configuración de la Empresa
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Empresa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información General de la Empresa</h5>
                <p class="card-title-desc mb-0">Configura los datos que aparecerán en facturas, recibos y otros documentos.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-check-double-line me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-line me-1"></i> Por favor corrige los siguientes errores:
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Información Básica -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="ri-information-line me-1"></i> Información Básica
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">
                                    Nombre de la Empresa <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" 
                                       value="{{ old('company_name', $company['name']) }}" 
                                       placeholder="Mi Clínica Médica" required>
                            </div>

                            <div class="mb-3">
                                <label for="company_business_name" class="form-label">
                                    Razón Social <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('company_business_name') is-invalid @enderror" 
                                       id="company_business_name" name="company_business_name" 
                                       value="{{ old('company_business_name', $company['business_name']) }}" 
                                       placeholder="Mi Clínica Médica SRL" required>
                            </div>

                            <div class="mb-3">
                                <label for="company_rnc" class="form-label">RNC / NIT</label>
                                <input type="text" class="form-control @error('company_rnc') is-invalid @enderror" 
                                       id="company_rnc" name="company_rnc" 
                                       value="{{ old('company_rnc', $company['rnc']) }}" 
                                       placeholder="401-123456-7">
                                <div class="form-text">Número de Registro Nacional del Contribuyente</div>
                            </div>

                            <div class="mb-3">
                                <label for="company_slogan" class="form-label">Eslogan</label>
                                <input type="text" class="form-control @error('company_slogan') is-invalid @enderror" 
                                       id="company_slogan" name="company_slogan" 
                                       value="{{ old('company_slogan', $company['slogan']) }}" 
                                       placeholder="Tu salud es nuestra prioridad">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_logo" class="form-label">Logo de la Empresa</label>
                                <input type="file" class="form-control @error('company_logo') is-invalid @enderror" 
                                       id="company_logo" name="company_logo" accept="image/*">
                                @if($company['logo'])
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $company['logo']) }}" 
                                             alt="Logo" height="60" class="img-thumbnail">
                                        <span class="text-muted small">Logo actual</span>
                                    </div>
                                @else
                                    <div class="mt-2 text-muted small">
                                        <i class="ri-info-line"></i> No hay logo cargado
                                    </div>
                                @endif
                                <div class="form-text">Formatos permitidos: PNG, JPG, SVG, WEBP (Max 2MB)</div>
                            </div>

                            <div class="mb-3">
                                <label for="company_favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control @error('company_favicon') is-invalid @enderror" 
                                       id="company_favicon" name="company_favicon" accept="image/*">
                                @if($company['favicon'])
                                    <div class="mt-2 d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $company['favicon']) }}" 
                                             alt="Favicon" height="32" class="img-thumbnail">
                                        <span class="text-muted small">Favicon actual</span>
                                    </div>
                                @else
                                    <div class="mt-2 text-muted small">
                                        <i class="ri-info-line"></i> No hay favicon cargado
                                    </div>
                                @endif
                                <div class="form-text">Formatos permitidos: PNG, ICO, SVG (Max 1MB)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="ri-contacts-book-line me-1"></i> Información de Contacto
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="company_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('company_email') is-invalid @enderror" 
                                       id="company_email" name="company_email" 
                                       value="{{ old('company_email', $company['email']) }}" 
                                       placeholder="contacto@miclinica.com">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="company_phone" class="form-label">Teléfono Fijo</label>
                                <input type="text" class="form-control @error('company_phone') is-invalid @enderror" 
                                       id="company_phone" name="company_phone" 
                                       value="{{ old('company_phone', $company['phone']) }}" 
                                       placeholder="(809) 555-0100">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="company_mobile" class="form-label">Teléfono Móvil</label>
                                <input type="text" class="form-control @error('company_mobile') is-invalid @enderror" 
                                       id="company_mobile" name="company_mobile" 
                                       value="{{ old('company_mobile', $company['mobile']) }}" 
                                       placeholder="(809) 555-0200">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_website" class="form-label">Sitio Web</label>
                                <input type="url" class="form-control @error('company_website') is-invalid @enderror" 
                                       id="company_website" name="company_website" 
                                       value="{{ old('company_website', $company['website']) }}" 
                                       placeholder="https://miclinica.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_address" class="form-label">Dirección</label>
                                <input type="text" class="form-control @error('company_address') is-invalid @enderror" 
                                       id="company_address" name="company_address" 
                                       value="{{ old('company_address', $company['address']) }}" 
                                       placeholder="Calle Principal #123, Santo Domingo">
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Facturación -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="ri-bill-line me-1"></i> Configuración de Facturación
                            </h6>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="company_currency" class="form-label">Moneda <span class="text-danger">*</span></label>
                                <select class="form-select @error('company_currency') is-invalid @enderror" 
                                        id="company_currency" name="company_currency" required>
                                    <option value="DOP" {{ $company['currency'] == 'DOP' ? 'selected' : '' }}>Peso Dominicano (DOP)</option>
                                    <option value="USD" {{ $company['currency'] == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
                                    <option value="EUR" {{ $company['currency'] == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="company_tax_rate" class="form-label">Tasa de Impuesto (%)</label>
                                <input type="number" step="0.01" class="form-control @error('company_tax_rate') is-invalid @enderror" 
                                       id="company_tax_rate" name="company_tax_rate" 
                                       value="{{ old('company_tax_rate', $company['tax_rate']) }}" 
                                       placeholder="18">
                                <div class="form-text">ITBIS / IVA</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="company_invoice_prefix" class="form-label">Prefijo de Facturas</label>
                                <input type="text" class="form-control @error('company_invoice_prefix') is-invalid @enderror" 
                                       id="company_invoice_prefix" name="company_invoice_prefix" 
                                       value="{{ old('company_invoice_prefix', $company['invoice_prefix']) }}" 
                                       placeholder="FAC-">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="company_receipt_prefix" class="form-label">Prefijo de Recibos</label>
                                <input type="text" class="form-control @error('company_receipt_prefix') is-invalid @enderror" 
                                       id="company_receipt_prefix" name="company_receipt_prefix" 
                                       value="{{ old('company_receipt_prefix', $company['receipt_prefix']) }}" 
                                       placeholder="REC-">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="company_ncf_type" class="form-label">Tipo de NCF por Defecto</label>
                                <select class="form-select @error('company_ncf_type') is-invalid @enderror" 
                                        id="company_ncf_type" name="company_ncf_type">
                                    <option value="consumidor_final" {{ $company['ncf_type'] == 'consumidor_final' ? 'selected' : '' }}>Consumidor Final</option>
                                    <option value="credito_fiscal" {{ $company['ncf_type'] == 'credito_fiscal' ? 'selected' : '' }}>Crédito Fiscal</option>
                                    <option value="gubernamental" {{ $company['ncf_type'] == 'gubernamental' ? 'selected' : '' }}>Gubernamental</option>
                                    <option value="regimen_especial" {{ $company['ncf_type'] == 'regimen_especial' ? 'selected' : '' }}>Régimen Especial</option>
                                </select>
                                <div class="form-text">NCF = Nuevo Comprobante Fiscal</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="company_ncf_sequence" class="form-label">Secuencia NCF Actual</label>
                                <input type="number" class="form-control @error('company_ncf_sequence') is-invalid @enderror" 
                                       id="company_ncf_sequence" name="company_ncf_sequence" 
                                       value="{{ old('company_ncf_sequence', $company['ncf_sequence']) }}" 
                                       placeholder="1" min="1">
                                <div class="form-text">Número de secuencia para la próxima factura</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="company_footer_text" class="form-label">Texto de Pie de Página</label>
                                <input type="text" class="form-control @error('company_footer_text') is-invalid @enderror" 
                                       id="company_footer_text" name="company_footer_text" 
                                       value="{{ old('company_footer_text', $company['footer_text']) }}" 
                                       placeholder="Gracias por confiar en nosotros">
                                <div class="form-text">Aparecerá en el footer de facturas y documentos</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Guardar Configuración
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection