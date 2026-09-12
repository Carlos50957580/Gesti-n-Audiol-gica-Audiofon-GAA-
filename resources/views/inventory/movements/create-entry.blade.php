<x-app-layout>
@section('title', 'Registrar Entrada')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-add-circle-line me-1"></i>Registrar Entrada de Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('stock-movements.index') }}">Movimientos</a></li>
                            <li class="breadcrumb-item active">Nueva Entrada</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('stock-movements.entry.store') }}" method="POST" id="entryForm">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Datos del Movimiento</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Sucursal Destino <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-select" required>
                                        <option value="">Seleccionar sucursal</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Proveedor</label>
                                    <select name="supplier_id" class="form-select">
                                        <option value="">Sin proveedor</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" name="movement_date" class="form-control" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Observaciones..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Productos</h5>
                            <button type="button" class="btn btn-sm btn-success" onclick="addProductRow()">
                                <i class="ri-add-line me-1"></i>Agregar Producto
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:45%">Producto</th>
                                            <th style="width:15%">Cantidad</th>
                                            <th style="width:20%">Precio Unit.</th>
                                            <th style="width:15%" class="text-end">Subtotal</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsBody"></tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                                            <td class="text-end fw-bold fs-5" id="grandTotal">RD$ 0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="ri-information-line me-1"></i>
                                La entrada se registra en estado <strong>Borrador</strong>. Deberás confirmarla para que los productos se sumen al inventario.
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Guardar Entrada
                                </button>
                                <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
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
const PRODUCTS = @json($productsData);

let rowIndex = 0;

function addProductRow() {
    const tbody = document.getElementById('productsBody');
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    row.innerHTML = `
        <td>
            <select name="items[${rowIndex}][product_id]" class="form-select" onchange="updateProduct(${rowIndex}, this)" required>
                <option value="">Seleccionar producto</option>
                ${PRODUCTS.map(p => `<option value="${p.id}" data-cost="${p.cost}">${p.code} - ${p.name}</option>`).join('')}
            </select>
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][quantity]" class="form-control" 
                   value="1" min="1" onchange="updateSubtotal(${rowIndex})" required>
        </td>
        <td>
            <input type="number" step="0.01" name="items[${rowIndex}][unit_price]" class="form-control" 
                   value="0" min="0" onchange="updateSubtotal(${rowIndex})" id="price-${rowIndex}" required>
        </td>
        <td class="text-end fw-semibold" id="subtotal-${rowIndex}">RD$ 0.00</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    rowIndex++;
}

function updateProduct(idx, select) {
    const option = select.options[select.selectedIndex];
    const cost = option.dataset.cost || 0;
    document.getElementById(`price-${idx}`).value = cost;
    updateSubtotal(idx);
}

function updateSubtotal(idx) {
    const row = document.getElementById(`row-${idx}`);
    const qty = parseFloat(row.querySelector(`[name="items[${idx}][quantity]"]`).value) || 0;
    const price = parseFloat(row.querySelector(`[name="items[${idx}][unit_price]"]`).value) || 0;
    const subtotal = qty * price;
    document.getElementById(`subtotal-${idx}`).textContent = 'RD$ ' + subtotal.toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal-"]').forEach(el => {
        const val = parseFloat(el.textContent.replace('RD$ ', '')) || 0;
        total += val;
    });
    document.getElementById('grandTotal').textContent = 'RD$ ' + total.toFixed(2);
}

function removeRow(idx) {
    document.getElementById(`row-${idx}`).remove();
    updateGrandTotal();
}

// Agregar una fila al inicio
addProductRow();

document.getElementById('entryForm').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('#productsBody tr');
    if (rows.length === 0) {
        e.preventDefault();
        alert('Debes agregar al menos un producto.');
    }
});
</script>
@endpush
</x-app-layout>