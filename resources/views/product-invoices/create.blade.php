<x-app-layout>

@section('title', 'Nueva Venta de Productos')

<div class="page-content">
    <div class="container-fluid">
        <!-- Título y Breadcrumb -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-shopping-bag-3-line me-1"></i>Nueva Venta de Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/product-invoices') }}">Facturas de Productos</a></li>
                            <li class="breadcrumb-item active">Nueva Venta</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ url('/product-invoices') }}" method="POST" id="invoiceForm">
            @csrf
            <div class="row">
                <!-- Columna Izquierda -->
                <div class="col-lg-8">

                    <!-- Datos Básicos -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Datos de la Venta</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Sucursal <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branchSelect" class="form-select" required>
                                        <option value="">Seleccionar sucursal</option>
                                        @foreach($branches as $br)
                                            <option value="{{ $br->id }}" {{ $defaultBranchId == $br->id ? 'selected' : '' }}>
                                                {{ $br->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Paciente <span class="text-danger">*</span></label>
                                    <input type="hidden" name="patient_id" id="patientId">
                                    <div class="input-group">
                                        <div class="position-relative flex-grow-1">
                                            <input type="text" id="patientSearch" class="form-control"
                                                   placeholder="Buscar por nombre o cédula..." autocomplete="off">
                                            <div id="patientResults"
                                                 class="position-absolute w-100 bg-white border rounded mt-1 d-none"
                                                 style="z-index:999;max-height:250px;overflow-y:auto;box-shadow:0 4px 15px rgba(0,0,0,.1);"></div>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="openNewPatientModal()">
                                            <i class="ri-user-add-line"></i>
                                        </button>
                                    </div>
                                    <div id="patientSelected" class="alert alert-info d-none mt-2 mb-0 py-2">
                                        <strong id="patientName"></strong>
                                        <small class="d-block" id="patientCedula"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de Productos -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Productos</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label">Categoría</label>
                                    <select id="categorySelect" class="form-select">
                                        <option value="">Todas las categorías</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label">Buscar producto</label>
                                    <div class="position-relative">
                                        <i class="ri-search-line position-absolute"
                                           style="left:10px;top:50%;transform:translateY(-50%);color:#999;"></i>
                                        <input type="text" id="productSearch" class="form-control ps-4"
                                               placeholder="Buscar por nombre o código..." autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de productos -->
                            <div id="productsList" class="d-none">
                                <h6 class="mb-2">Productos disponibles</h6>
                                <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th class="text-end">Precio</th>
                                                <th class="text-center">Stock</th>
                                                <th class="text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productsBody"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Items agregados -->
                            <hr class="my-4">
                            <h6 class="mb-2">Items en la factura</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th style="width:100px;">Cantidad</th>
                                            <th style="width:130px;" class="text-end">Precio</th>
                                            <th style="width:130px;" class="text-end">Subtotal</th>
                                            <th style="width:50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <tr id="noItemsRow">
                                            <td colspan="5" class="text-center text-muted py-3">No hay productos agregados</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Comprobante Fiscal --}}
                    @if(\App\Models\Setting::get('ef2_activo') === '1')
                        <div class="alert alert-info d-flex align-items-start">
                            <i class="ri-cloud-line fs-20 me-2 mt-1"></i>
                            <div class="flex-grow-1">
                                <strong>Facturación Electrónica Activa</strong>
                                <p class="mb-0 small">
                                    Todas las ventas se emitirán automáticamente como <strong>Consumidor Final</strong> y
                                    se enviarán a la DGII.
                                    <br>
                                    Si el cliente necesita un <strong>Crédito Fiscal (B01)</strong>, activa la opción de abajo.
                                </p>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-file-shield-line me-1"></i>Comprobante Fiscal
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="with_ncf" value="1"
                                           id="withNcf" onchange="toggleNcf()">
                                    <label class="form-check-label" for="withNcf">
                                        Emitir <strong>Crédito Fiscal (B01 / e-CF 31)</strong> en lugar de Consumidor Final
                                    </label>
                                </div>

                                <div id="ncfFields" class="d-none">
                                    <div class="mb-3">
                                        <label for="customerRnc" class="form-label">
                                            RNC del Cliente <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="customer_rnc" id="customerRnc" class="form-control"
                                                   placeholder="000-0000000-0" onblur="searchRnc()">
                                            <button type="button" class="btn btn-primary" onclick="searchRnc()">
                                                <i class="ri-search-line"></i>
                                            </button>
                                        </div>
                                        <div id="rncStatus" class="mt-1 small"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="customerBusinessName" class="form-label">Razón Social</label>
                                        <input type="text" name="customer_business_name" id="customerBusinessName"
                                               class="form-control" readonly
                                               placeholder="Se llenará automáticamente al consultar el RNC">
                                    </div>

                                    <input type="hidden" name="ncf_type" id="ncfType" value="">
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="ri-alert-line me-1"></i>
                            La facturación electrónica no está configurada. Configúrala en
                            <a href="{{ route('settings.company') }}">Ajustes de Empresa</a>.
                        </div>
                    @endif

                </div>

                <!-- Columna Derecha: Resumen -->
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
                                <span class="text-muted">ITBIS:</span>
                                <strong id="sumTax">RD$ 0.00</strong>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Descuento</label>
                                <input type="number" name="discount" id="discountInput"
                                       class="form-control form-control-sm"
                                       value="0" min="0" step="0.01" oninput="updateTotals()">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold fs-5">TOTAL:</span>
                                <span class="fw-bold fs-4 text-success" id="sumTotal">RD$ 0.00</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="ri-save-line me-1"></i>Guardar Factura
                                </button>
                                <a href="{{ url('/product-invoices') }}" class="btn btn-secondary">
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

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- Modal: Nuevo Paciente --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="newPatientModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header py-3" style="background:linear-gradient(135deg,#405189,#0ab39c);color:#fff;">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="ri-user-add-line fs-18"></i>Nuevo Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modal-alert" class="alert d-none mb-3"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_first_name" placeholder="Nombre">
                            <label>Nombre <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_last_name" placeholder="Apellido">
                            <label>Apellido <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_cedula" placeholder="000-0000000-0">
                            <label>Cédula <span class="text-muted fw-normal">(opcional)</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_phone" placeholder="809-000-0000">
                            <label>Teléfono</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="m_email" placeholder="email@ejemplo.com">
                            <label>Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="m_birth_date" placeholder="Fecha">
                            <label>Fecha de nacimiento</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="m_gender">
                                <option value="">— Seleccionar —</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <label>Género</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="m_insurance_id">
                                <option value="">— Sin seguro —</option>
                                @foreach($insurances ?? [] as $ins)
                                    <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                                @endforeach
                            </select>
                            <label>Seguro médico</label>
                        </div>
                    </div>
                    <div class="col-md-6 d-none" id="m_ins_num_wrap">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="m_insurance_number" placeholder="Número">
                            <label>N° de afiliado</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control" id="m_address" placeholder="Dirección" style="height:72px;"></textarea>
                            <label>Dirección</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-3 px-4">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"
                        style="border-radius:2rem;">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-save-patient" style="border-radius:2rem;padding:.42rem 1rem;">
                    <span class="spinner-border spinner-border-sm d-none" id="patient-save-spin"></span>
                    <i class="ri-save-line" id="patient-save-icon"></i>
                    <span>Guardar paciente</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ============================================
    // CONSTANTES
    // ============================================
    const CSRF                 = document.querySelector('meta[name="csrf-token"]').content;
    const URL_SEARCH_PATIENTS  = "{{ url('/api/product-invoices/patients/search') }}";
    const URL_PRODUCTS_BY_CAT  = "{{ url('/api/product-invoices/products/by-category') }}";
    const URL_STORE            = "{{ url('/product-invoices') }}";
    const URL_INDEX            = "{{ url('/product-invoices') }}";
    const URL_PATIENT_STORE    = "{{ route('api.patients.store') }}";

    let items = [];

    // ============================================
    // BÚSQUEDA DE PACIENTES
    // ============================================
    const patientSearch  = document.getElementById('patientSearch');
    const patientResults = document.getElementById('patientResults');
    let patientSearchTimer = null;

    patientSearch.addEventListener('input', function () {
        clearTimeout(patientSearchTimer);
        const q = this.value.trim();
        if (q.length < 2) { patientResults.classList.add('d-none'); return; }

        patientSearchTimer = setTimeout(async () => {
            try {
                const r = await fetch(URL_SEARCH_PATIENTS + '?q=' + encodeURIComponent(q));
                const list = await r.json();

                if (!list.length) {
                    patientResults.innerHTML = '<div class="p-3 text-muted">Sin resultados</div>';
                } else {
                    patientResults.innerHTML = list.map(p => {
                        const fullName    = `${p.first_name} ${p.last_name}`;
                        const safeName    = fullName.replace(/'/g, "\\'");
                        const safeCedula  = (p.cedula || '').replace(/'/g, "\\'");
                        return `
                            <div class="p-2 border-bottom" style="cursor:pointer;"
                                 onclick="selectPatient(${p.id}, '${safeName}', '${safeCedula}')">
                                <div class="fw-semibold">${fullName}</div>
                                <small class="text-muted">${p.cedula || 'Sin cédula'}</small>
                            </div>`;
                    }).join('');
                }
                patientResults.classList.remove('d-none');
            } catch (e) {
                patientResults.innerHTML = '<div class="p-3 text-danger">Error al buscar</div>';
                patientResults.classList.remove('d-none');
            }
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#patientSearch') && !e.target.closest('#patientResults')) {
            patientResults.classList.add('d-none');
        }
    });

    function selectPatient(id, name, cedula) {
        document.getElementById('patientId').value        = id;
        document.getElementById('patientName').textContent = name;
        document.getElementById('patientCedula').textContent = cedula ? 'Cédula: ' + cedula : '';
        document.getElementById('patientSelected').classList.remove('d-none');
        patientSearch.value = name;
        patientResults.classList.add('d-none');
    }

    // ============================================
    // CARGA DE PRODUCTOS
    // ============================================
    let productLoadTimer       = null;
    let productFetchController = null;

    function scheduleLoadProducts(delay = 350) {
        clearTimeout(productLoadTimer);
        productLoadTimer = setTimeout(loadProducts, delay);
    }

    async function loadProducts() {
        const branchId = document.getElementById('branchSelect').value;
        const catId    = document.getElementById('categorySelect').value;
        const q        = document.getElementById('productSearch').value.trim();

        const productsList = document.getElementById('productsList');
        const tbody        = document.getElementById('productsBody');

        if (!branchId) {
            productsList.classList.add('d-none');
            return;
        }

        if (productFetchController) productFetchController.abort();
        productFetchController = new AbortController();

        const params = new URLSearchParams({ branch_id: branchId });
        if (catId) params.append('category_id', catId);
        if (q)     params.append('q', q);

        productsList.classList.remove('d-none');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm me-2"></span>Buscando...</td></tr>';

        try {
            const r = await fetch(URL_PRODUCTS_BY_CAT + '?' + params.toString(), {
                signal: productFetchController.signal,
            });
            const products = await r.json();

            if (!products.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No se encontraron productos</td></tr>';
                return;
            }

            tbody.innerHTML = products.map(p => `
                <tr>
                    <td><code>${p.code}</code></td>
                    <td>${p.name}</td>
                    <td class="text-end">RD$ ${p.price.toFixed(2)}</td>
                    <td class="text-center">
                        <span class="badge bg-${p.stock > 0 ? 'success' : 'danger'}-subtle text-${p.stock > 0 ? 'success' : 'danger'}">
                            ${p.stock}
                        </span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success"
                                onclick='addItem(${JSON.stringify(p)})'
                                ${p.stock <= 0 ? 'disabled' : ''}>
                            <i class="ri-add-line"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (err) {
            if (err.name !== 'AbortError') {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3">Error al cargar productos</td></tr>';
            }
        }
    }

    document.getElementById('branchSelect').addEventListener('change',   () => scheduleLoadProducts(0));
    document.getElementById('categorySelect').addEventListener('change', () => scheduleLoadProducts(0));
    document.getElementById('productSearch').addEventListener('input',   () => scheduleLoadProducts(350));

    // ============================================
    // ITEMS
    // ============================================
    function addItem(product) {
        const existing = items.find(i => i.product_id === product.id);
        if (existing) {
            if (existing.quantity < product.stock) existing.quantity++;
        } else {
            items.push({
                product_id : product.id,
                code       : product.code,
                name       : product.name,
                price      : product.price,
                has_tax    : product.has_tax,
                quantity   : 1,
                max_stock  : product.stock,
            });
        }
        renderItems();
    }

    function renderItems() {
        const tbody = document.getElementById('itemsBody');

        if (!items.length) {
            tbody.innerHTML = '<tr id="noItemsRow"><td colspan="5" class="text-center text-muted py-3">No hay productos agregados</td></tr>';
            updateTotals();
            return;
        }

        tbody.innerHTML = items.map((item, idx) => `
            <tr>
                <td>
                    <div class="fw-semibold">${item.name}</div>
                    <small class="text-muted">${item.code}</small>
                    <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${idx}][price]" value="${item.price}">
                </td>
                <td>
                    <input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm"
                           value="${item.quantity}" min="1" max="${item.max_stock}"
                           oninput="updateQuantity(${idx}, this.value)">
                </td>
                <td class="text-end">RD$ ${item.price.toFixed(2)}</td>
                <td class="text-end fw-semibold" id="subtotal-${idx}">RD$ ${(item.price * item.quantity).toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${idx})">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        updateTotals();
    }

    function updateQuantity(idx, qty) {
        let value = parseInt(qty);

        if (isNaN(value) || value < 1) {
            items[idx].quantity = qty === '' ? '' : value;
        } else {
            if (value > items[idx].max_stock) value = items[idx].max_stock;
            items[idx].quantity = value;
        }

        const subtotalCell = document.getElementById(`subtotal-${idx}`);
        const qtyForCalc   = parseFloat(items[idx].quantity) || 0;
        subtotalCell.textContent = 'RD$ ' + (items[idx].price * qtyForCalc).toFixed(2);

        updateTotals();
    }

    function removeItem(idx) {
        items.splice(idx, 1);
        renderItems();
    }

    // ============================================
    // TOTALES
    // ============================================
    function updateTotals() {
        let subtotal = 0;
        let tax      = 0;
        const taxRate = {{ (float) \App\Models\Setting::get('company_tax_rate', 18) }};

        items.forEach(item => {
            const qty          = parseFloat(item.quantity) || 0;
            const itemSubtotal = item.price * qty;
            subtotal += itemSubtotal;
            if (item.has_tax) tax += itemSubtotal * (taxRate / 100);
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const total    = subtotal + tax - discount;

        document.getElementById('sumSubtotal').textContent = 'RD$ ' + subtotal.toFixed(2);
        document.getElementById('sumTax').textContent      = 'RD$ ' + tax.toFixed(2);
        document.getElementById('sumTotal').textContent    = 'RD$ ' + total.toFixed(2);
    }

    // ============================================
    // NCF
    // ============================================
    function toggleNcf() {
        const checked = document.getElementById('withNcf').checked;
        document.getElementById('ncfFields').classList.toggle('d-none', !checked);
        document.getElementById('ncfType').value = checked ? 'credito_fiscal' : '';
    }

    async function searchRnc() {
        const rnc = document.getElementById('customerRnc').value.trim();
        if (!rnc) {
            alert('Ingrese un RNC.');
            return;
        }
        const status = document.getElementById('rncStatus');
        status.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm me-1"></span>Consultando...</span>';
        try {
            const response = await fetch('/api/rnc/' + encodeURIComponent(rnc));
            const data = await response.json();
            if (data.error) {
                status.innerHTML = `<span class="text-danger">${data.mensaje}</span>`;
                document.getElementById('customerBusinessName').value = '';
                return;
            }
            document.getElementById('customerBusinessName').value = data.nombre_razon_social ?? '';
            status.innerHTML = `<span class="text-success">✓ ${data.estado}</span>`;
        } catch (e) {
            status.innerHTML = '<span class="text-danger">Error consultando RNC.</span>';
        }
    }

    // ============================================
    // ENVÍO DEL FORMULARIO
    // ============================================
    const invoiceForm = document.getElementById('invoiceForm');

    invoiceForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!document.getElementById('patientId').value) {
            alert('Debes seleccionar un paciente.');
            return;
        }
        if (!items.length) {
            alert('Debes agregar al menos un producto.');
            return;
        }

        const invalidItem = items.find(i => !i.quantity || parseInt(i.quantity) < 1);
        if (invalidItem) {
            alert(`La cantidad de "${invalidItem.name}" debe ser al menos 1.`);
            return;
        }

        const submitBtn    = invoiceForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled  = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

        try {
            const formData = new FormData(invoiceForm);

            const r = await fetch(URL_STORE, {
                method : 'POST',
                headers: {
                    'Accept'       : 'application/json',
                    'X-CSRF-TOKEN' : CSRF,
                },
                body: formData,
            });

            const data = await r.json().catch(() => null);

            if (r.ok && data?.success) {
                window.location.href = data.redirect;
                return;
            }

            let message = data?.message || 'Ocurrió un error al guardar la factura.';
            if (data?.errors) {
                message = Object.values(data.errors).flat().join('\n');
            }
            alert(message);
        } catch (err) {
            alert('Error de conexión. Intenta de nuevo.');
        } finally {
            submitBtn.disabled  = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // ============================================
    // MODAL: NUEVO PACIENTE
    // ============================================
    function openNewPatientModal() {
        new bootstrap.Modal(document.getElementById('newPatientModal')).show();
    }

    document.getElementById('m_insurance_id').addEventListener('change', function () {
        document.getElementById('m_ins_num_wrap').classList.toggle('d-none', !this.value);
    });

    // Reset al cerrar el modal
    document.getElementById('newPatientModal').addEventListener('hidden.bs.modal', function () {
        ['m_first_name', 'm_last_name', 'm_cedula', 'm_phone', 'm_email',
         'm_birth_date', 'm_address', 'm_insurance_number'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('m_gender').value        = '';
        document.getElementById('m_insurance_id').value  = '';
        document.getElementById('m_ins_num_wrap').classList.add('d-none');
        document.getElementById('modal-alert').className = 'alert d-none';
    });

    document.getElementById('btn-save-patient').addEventListener('click', async function () {
        const alertEl = document.getElementById('modal-alert');
        alertEl.className = 'alert d-none';

        const payload = {
            first_name      : document.getElementById('m_first_name').value.trim(),
            last_name       : document.getElementById('m_last_name').value.trim(),
            cedula          : document.getElementById('m_cedula').value.trim() || null,
            phone           : document.getElementById('m_phone').value.trim(),
            email           : document.getElementById('m_email').value.trim(),
            birth_date      : document.getElementById('m_birth_date').value,
            gender          : document.getElementById('m_gender').value,
            insurance_id    : document.getElementById('m_insurance_id').value,
            insurance_number: document.getElementById('m_insurance_number').value.trim(),
            address         : document.getElementById('m_address').value.trim(),
        };

        if (!payload.first_name || !payload.last_name) {
            alertEl.className   = 'alert alert-danger';
            alertEl.textContent = 'Nombre y apellido son obligatorios.';
            return;
        }

        this.disabled = true;
        document.getElementById('patient-save-spin').classList.remove('d-none');
        document.getElementById('patient-save-icon').classList.add('d-none');

        try {
            const r = await fetch(URL_PATIENT_STORE, {
                method : 'POST',
                headers: {
                    'Content-Type' : 'application/json',
                    'Accept'       : 'application/json',
                    'X-CSRF-TOKEN' : CSRF,
                },
                body: JSON.stringify(payload),
            });
            const data = await r.json();
            if (!r.ok) throw data;

            bootstrap.Modal.getInstance(document.getElementById('newPatientModal')).hide();

            // Selecciona automáticamente al paciente recién creado
           selectPatient(
    data.patient.id,
    data.patient.full_name || '—',
    data.patient.cedula || ''
);
        } catch (err) {
            alertEl.className   = 'alert alert-danger';
            alertEl.textContent = err.errors
                ? Object.values(err.errors).flat().join(' ')
                : (err.message || 'Error al guardar paciente.');
        } finally {
            this.disabled = false;
            document.getElementById('patient-save-spin').classList.add('d-none');
            document.getElementById('patient-save-icon').classList.remove('d-none');
        }
    });
</script>
@endpush

</x-app-layout>