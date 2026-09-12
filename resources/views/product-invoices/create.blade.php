<x-app-layout>
@section('title', 'Nueva Venta de Productos')

<div class="page-content">
    <div class="container-fluid">
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
                <div class="col-lg-8">

                    {{-- Datos básicos --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Datos de la Venta</h5></div>
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
                                    <div class="position-relative">
                                        <input type="text" id="patientSearch" class="form-control" 
                                               placeholder="Buscar por nombre o cédula..." autocomplete="off">
                                        <div id="patientResults" class="position-absolute w-100 bg-white border rounded mt-1 d-none" 
                                             style="z-index:999;max-height:250px;overflow-y:auto;box-shadow:0 4px 15px rgba(0,0,0,.1);"></div>
                                    </div>
                                    <div id="patientSelected" class="alert alert-info d-none mt-2 mb-0 py-2">
                                        <strong id="patientName"></strong>
                                        <small class="d-block" id="patientCedula"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Selección de productos --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Productos</h5></div>
                        <div class="card-body">
                            
                            {{-- Selector de categoría --}}
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select id="categorySelect" class="form-select">
                                        <option value="">Seleccionar categoría</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary" onclick="loadProducts()">
                                        <i class="ri-search-line me-1"></i>Cargar Productos
                                    </button>
                                </div>
                            </div>

                            {{-- Lista de productos --}}
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

                            {{-- Items agregados --}}
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

                    {{-- NCF opcional --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-shield-line me-1"></i>Comprobante Fiscal (Opcional)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="with_ncf" value="1" id="withNcf" onchange="toggleNcf()">
                                <label class="form-check-label" for="withNcf">Emitir con NCF</label>
                            </div>
                            <div id="ncfFields" class="d-none">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo de NCF</label>
                                        <select name="ncf_type" class="form-select">
                                            <option value="consumidor_final">Consumidor Final</option>
                                            <option value="credito_fiscal">Crédito Fiscal</option>
                                            <option value="gubernamental">Gubernamental</option>
                                            <option value="regimen_especial">Régimen Especial</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">NCF</label>
                                        <input type="text" name="ncf" class="form-control" placeholder="B0100000001">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">RNC del Cliente</label>
                                        <input type="text" name="customer_rnc" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Razón Social</label>
                                        <input type="text" name="customer_business_name" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen lateral --}}
                <div class="col-lg-4">
                    <div class="card" style="position:sticky;top:80px;">
                        <div class="card-header"><h5 class="card-title mb-0">Resumen</h5></div>
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
                                <input type="number" name="discount" id="discountInput" class="form-control form-control-sm" 
                                       value="0" min="0" step="0.01" onchange="updateTotals()">
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

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const URL_SEARCH_PATIENTS = "{{ url('/api/product-invoices/patients/search') }}";
const URL_PRODUCTS_BY_CAT = "{{ url('/api/product-invoices/products/by-category') }}";

let items = [];
let itemCounter = 0;

// ═══════════════════════════════════════════
// BÚSQUEDA DE PACIENTES
// ═══════════════════════════════════════════
const patientSearch = document.getElementById('patientSearch');
const patientResults = document.getElementById('patientResults');
let searchTimer = null;

patientSearch.addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 2) { patientResults.classList.add('d-none'); return; }
    
    searchTimer = setTimeout(async () => {
        const r = await fetch(URL_SEARCH_PATIENTS + '?q=' + encodeURIComponent(q));
        const list = await r.json();
        if (!list.length) {
            patientResults.innerHTML = '<div class="p-3 text-muted">Sin resultados</div>';
        } else {
            patientResults.innerHTML = list.map(p => `
                <div class="p-2 border-bottom" style="cursor:pointer;"
                     onclick="selectPatient(${p.id}, '${p.first_name} ${p.last_name}', '${p.cedula || ''}')">
                    <div class="fw-semibold">${p.first_name} ${p.last_name}</div>
                    <small class="text-muted">${p.cedula || 'Sin cédula'}</small>
                </div>
            `).join('');
        }
        patientResults.classList.remove('d-none');
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#patientSearch') && !e.target.closest('#patientResults')) {
        patientResults.classList.add('d-none');
    }
});

function selectPatient(id, name, cedula) {
    document.getElementById('patientId').value = id;
    document.getElementById('patientName').textContent = name;
    document.getElementById('patientCedula').textContent = cedula ? 'Cédula: ' + cedula : '';
    document.getElementById('patientSelected').classList.remove('d-none');
    patientSearch.value = name;
    patientResults.classList.add('d-none');
}

// ═══════════════════════════════════════════
// CARGA DE PRODUCTOS POR CATEGORÍA
// ═══════════════════════════════════════════
async function loadProducts() {
    const catId = document.getElementById('categorySelect').value;
    const branchId = document.getElementById('branchSelect').value;
    
    if (!catId) { alert('Selecciona una categoría'); return; }
    if (!branchId) { alert('Selecciona una sucursal'); return; }
    
    const r = await fetch(URL_PRODUCTS_BY_CAT + '?category_id=' + catId + '&branch_id=' + branchId);
    const products = await r.json();
    
    document.getElementById('productsList').classList.remove('d-none');
    const tbody = document.getElementById('productsBody');
    
    if (!products.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay productos en esta categoría</td></tr>';
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
}

// ═══════════════════════════════════════════
// AGREGAR ITEM
// ═══════════════════════════════════════════
function addItem(product) {
    // Verificar si ya existe
    const existing = items.find(i => i.product_id === product.id);
    if (existing) {
        existing.quantity++;
    } else {
        items.push({
            product_id: product.id,
            code: product.code,
            name: product.name,
            price: product.price,
            has_tax: product.has_tax,
            quantity: 1,
            max_stock: product.stock,
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
                       onchange="updateQuantity(${idx}, this.value)">
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
    items[idx].quantity = parseInt(qty) || 1;
    renderItems();
}

function removeItem(idx) {
    items.splice(idx, 1);
    renderItems();
}

// ═══════════════════════════════════════════
// CÁLCULO DE TOTALES
// ═══════════════════════════════════════════
function updateTotals() {
    let subtotal = 0;
    let tax = 0;
    
    const taxRate = {{ (float) \App\Models\Setting::get('company_tax_rate', 18) }};
    
    items.forEach(item => {
        const itemSubtotal = item.price * item.quantity;
        subtotal += itemSubtotal;
        if (item.has_tax) {
            tax += itemSubtotal * (taxRate / 100);
        }
    });
    
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = subtotal + tax - discount;
    
    document.getElementById('sumSubtotal').textContent = 'RD$ ' + subtotal.toFixed(2);
    document.getElementById('sumTax').textContent = 'RD$ ' + tax.toFixed(2);
    document.getElementById('sumTotal').textContent = 'RD$ ' + total.toFixed(2);
}

// ═══════════════════════════════════════════
// NCF
// ═══════════════════════════════════════════
function toggleNcf() {
    const checked = document.getElementById('withNcf').checked;
    document.getElementById('ncfFields').classList.toggle('d-none', !checked);
}

// ═══════════════════════════════════════════
// VALIDACIÓN
// ═══════════════════════════════════════════
document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    if (!document.getElementById('patientId').value) {
        e.preventDefault();
        alert('Debes seleccionar un paciente.');
        return;
    }
    if (!items.length) {
        e.preventDefault();
        alert('Debes agregar al menos un producto.');
        return;
    }
});

// Re-cargar productos cuando cambia la sucursal
document.getElementById('branchSelect').addEventListener('change', function() {
    if (document.getElementById('categorySelect').value) {
        loadProducts();
    }
});
</script>
@endpush
</x-app-layout>