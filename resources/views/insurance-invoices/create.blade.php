<x-app-layout>
@section('title', 'Nueva Factura a Seguro')

<div class="page-content">
    <div class="container-fluid">

        {{-- Título --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="ri-shield-cross-line me-1"></i>Nueva Factura a Seguro / ARS
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('insurance-invoices.index') }}">Facturas a Seguros</a>
                            </li>
                            <li class="breadcrumb-item active">Nueva</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-1"></i>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('insurance-invoices.store') }}" method="POST" id="insuranceInvoiceForm">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    {{-- Datos de la factura --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-list-3-line me-1"></i>Datos de la Factura
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Fecha de Emisión <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="invoice_date" id="invoiceDate"
                                           class="form-control" required
                                           value="{{ old('invoice_date', now()->toDateString()) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Sucursal <span class="text-danger">*</span>
                                    </label>
                                    <select name="branch_id" class="form-select" required>
                                       
                                        @foreach($branches as $br)
                                            <option value="{{ $br->id }}"
                                                {{ old('branch_id', $defaultBranchId) == $br->id ? 'selected' : '' }}>
                                                {{ $br->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        Selecciona "Todas las sucursales" para facturas globales al seguro.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Seguro / ARS <span class="text-danger">*</span>
                                    </label>
                                    <select name="insurance_id" id="insuranceSelect" class="form-select" required>
                                        <option value="">Seleccionar seguro...</option>
                                        @foreach($insurances as $ins)
                                            <option value="{{ $ins->id }}"
                                                    data-rnc="{{ $ins->rnc ?? '' }}"
                                                    data-name="{{ $ins->name }}"
                                                    {{ old('insurance_id') == $ins->id ? 'selected' : '' }}>
                                                {{ $ins->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tipo de Comprobante <span class="text-danger">*</span>
                                    </label>
                                    <select name="ncf_type" id="ncfType" class="form-select" required>
                                        <option value="credito_fiscal" {{ old('ncf_type', 'credito_fiscal') == 'credito_fiscal' ? 'selected' : '' }}>
                                            Crédito Fiscal (B01 / e-CF 31)
                                        </option>
                                        <option value="gubernamental" {{ old('ncf_type') == 'gubernamental' ? 'selected' : '' }}>
                                            Gubernamental (B15 / e-CF 45)
                                        </option>
                                        <option value="consumidor_final" {{ old('ncf_type') == 'consumidor_final' ? 'selected' : '' }}>
                                            Consumidor Final (B02 / e-CF 32)
                                        </option>
                                        <option value="regimen_especial" {{ old('ncf_type') == 'regimen_especial' ? 'selected' : '' }}>
                                            Régimen Especial (B14 / e-CF 44)
                                        </option>
                                    </select>
                                    <div class="form-text">
                                        Para ARS/seguros privados usa Crédito Fiscal. Para el gobierno usa Gubernamental.
                                    </div>
                                </div>

                                {{-- RNC --}}
                                <div class="col-md-6">
                                    <label class="form-label">RNC del Seguro</label>
                                    <div class="input-group">
                                        <input type="text" name="customer_rnc" id="customerRnc"
                                               class="form-control" placeholder="000-0000000-0"
                                               value="{{ old('customer_rnc') }}">
                                        <button type="button" class="btn btn-primary"
                                                onclick="consultRncInvoice()" title="Consultar RNC">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                    <div id="rncStatus" class="mt-1 small"></div>
                                </div>

                                {{-- Razón Social --}}
                                <div class="col-md-6">
                                    <label class="form-label">Razón Social</label>
                                    <input type="text" name="customer_business_name" id="customerBusinessName"
                                           class="form-control" placeholder="Nombre del seguro"
                                           value="{{ old('customer_business_name') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Concepto / Descripción</label>
                                    <textarea name="concept" class="form-control" rows="2"
                                              placeholder="Ej: Reclamación de servicios médicos según factura #12345"
                                    >{{ old('concept') }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Monto e impuestos --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-money-dollar-circle-line me-1"></i>Monto e Impuestos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Monto Base <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">RD$</span>
                                        <input type="number" name="amount" id="amountInput"
                                               class="form-control" required min="0.01" step="0.01"
                                               value="{{ old('amount') }}" placeholder="0.00">
                                    </div>
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                               name="with_tax" value="1"
                                               id="withTax" onchange="updateTotals()"
                                               {{ old('with_tax') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="withTax">
                                            Aplicar <strong>ITBIS (18%)</strong>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="card">
                        <div class="card-body">
                            <label class="form-label">Notas internas</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Notas visibles solo para el personal"
                            >{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Columna derecha: resumen --}}
                <div class="col-lg-4">
                    <div class="card" style="position:sticky;top:80px;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Resumen</h5>
                        </div>
                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <strong id="sumSubtotal">RD$ 0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">ITBIS (18%):</span>
                                <strong id="sumTax">RD$ 0.00</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold fs-5">TOTAL:</span>
                                <span class="fw-bold fs-4 text-success" id="sumTotal">RD$ 0.00</span>
                            </div>

                            <div class="alert alert-info small mb-3">
                                <i class="ri-information-line me-1"></i>
                                Al guardar, la factura se enviará automáticamente a la DGII vía EF2.
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="ri-save-line me-1"></i>Guardar Factura
                                </button>
                                <a href="{{ route('insurance-invoices.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const insuranceSelect      = document.getElementById('insuranceSelect');
    const customerRnc          = document.getElementById('customerRnc');
    const customerBusinessName = document.getElementById('customerBusinessName');
    const amountInput          = document.getElementById('amountInput');
    const withTax              = document.getElementById('withTax');
    const rncStatus            = document.getElementById('rncStatus');

    const TAX_RATE = {{ (float) \App\Models\Setting::get('company_tax_rate', 18) }};

    // ═══════════════════════════════════════════
    // CONSULTAR RNC EN LA API
    // ═══════════════════════════════════════════
    async function consultRncInvoice(rnc = null) {
        const rncValue = (rnc ?? customerRnc.value).trim();

        if (!rncValue) {
            rncStatus.innerHTML = '';
            return;
        }

        rncStatus.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm me-1"></span>Consultando RNC...</span>';

        try {
            const response = await fetch('/api/rnc/' + encodeURIComponent(rncValue), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.error || !data.nombre_razon_social) {
                rncStatus.innerHTML = `<span class="text-danger"><i class="ri-close-line me-1"></i>${data.mensaje ?? 'RNC no encontrado.'}</span>`;
                return;
            }

            // ✅ Actualizar razón social (siempre, porque el RNC es la fuente oficial)
            customerBusinessName.value = data.nombre_razon_social;

            // ✅ Actualizar el RNC con el formato oficial si viene distinto
            if (data.rnc && data.rnc !== rncValue) {
                customerRnc.value = data.rnc;
            }

            rncStatus.innerHTML = `<span class="text-success"><i class="ri-check-line me-1"></i>${data.nombre_razon_social}</span>`;

        } catch (e) {
            rncStatus.innerHTML = '<span class="text-danger">Error consultando RNC.</span>';
        }
    }

    // ═══════════════════════════════════════════
    // AL CAMBIAR EL SEGURO
    // ═══════════════════════════════════════════
    insuranceSelect.addEventListener('change', async function () {
        const option = this.options[this.selectedIndex];
        const rnc    = option.getAttribute('data-rnc');
        const name   = option.getAttribute('data-name');

        // Autocompletar razón social con el nombre del seguro
        if (name) customerBusinessName.value = name;

        if (rnc) {
            // Autocompletar RNC del catálogo
            customerRnc.value = rnc;
            // Consultar la API para validar y traer la razón social oficial
            await consultRncInvoice(rnc);
        } else {
            // No hay RNC en el catálogo → limpiar y esperar input del usuario
            customerRnc.value = '';
            rncStatus.innerHTML = '<span class="text-warning small"><i class="ri-alert-line me-1"></i>Este seguro no tiene RNC en el catálogo. Ingrésalo manualmente.</span>';
        }
    });

    // ═══════════════════════════════════════════
    // CONSULTAR RNC AL SALIR DEL CAMPO
    // ═══════════════════════════════════════════
    customerRnc.addEventListener('blur', () => {
        if (customerRnc.value.trim()) {
            consultRncInvoice();
        }
    });

    // ═══════════════════════════════════════════
    // CÁLCULO DE TOTALES
    // ═══════════════════════════════════════════
    amountInput.addEventListener('input', updateTotals);
    withTax.addEventListener('change', updateTotals);

    function updateTotals() {
        const subtotal = parseFloat(amountInput.value) || 0;
        const tax      = withTax.checked ? subtotal * (TAX_RATE / 100) : 0;
        const total    = subtotal + tax;

        document.getElementById('sumSubtotal').textContent = 'RD$ ' + subtotal.toFixed(2);
        document.getElementById('sumTax').textContent      = 'RD$ ' + tax.toFixed(2);
        document.getElementById('sumTotal').textContent    = 'RD$ ' + total.toFixed(2);
    }

    // ═══════════════════════════════════════════
    // INICIALIZAR
    // ═══════════════════════════════════════════
    updateTotals();

    // Si ya hay un seguro seleccionado (old), disparar la carga
    if (insuranceSelect.value) {
        insuranceSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush

</x-app-layout>