<x-app-layout>
@section('title', 'Registrar Transferencia')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-arrow-left-right-line me-1"></i>Transferir Productos entre Sucursales</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('stock-movements.index') }}">Movimientos</a></li>
                            <li class="breadcrumb-item active">Nueva Transferencia</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('stock-movements.transfer.store') }}" method="POST" id="transferForm">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Datos de la Transferencia</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Sucursal Origen <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="originSelect" class="form-select" required>
                                        <option value="">Seleccionar origen</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sucursal Destino <span class="text-danger">*</span></label>
                                    <select name="destination_branch_id" id="destSelect" class="form-select" required>
                                        <option value="">Seleccionar destino</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Productos a Transferir</h5>
                            <button type="button" class="btn btn-sm btn-info" onclick="addProductRow()">
                                <i class="ri-add-line me-1"></i>Agregar Producto
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:55%">Producto</th>
                                            <th style="width:20%">Cantidad</th>
                                            <th style="width:20%">Stock Origen</th>
                                            <th style="width:5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsBody"></tbody>
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
                                Los productos se restarán de la sucursal origen y se sumarán a la sucursal destino al confirmar.
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Guardar Transferencia
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
            <select name="items[${rowIndex}][product_id]" class="form-select" onchange="updateStock(${rowIndex})" required>
                <option value="">Seleccionar producto</option>
                ${PRODUCTS.map(p => `<option value="${p.id}">${p.code} - ${p.name}</option>`).join('')}
            </select>
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][quantity]" class="form-control" 
                   value="1" min="1" required>
        </td>
        <td class="text-center fw-semibold" id="stock-${rowIndex}">—</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(${rowIndex})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    rowIndex++;
}

function updateStock(idx) {
    const row = document.getElementById(`row-${idx}`);
    const productId = row.querySelector(`[name="items[${idx}][product_id]"]`).value;
    const originId = document.getElementById('originSelect').value;
    
    if (!productId || !originId) {
        document.getElementById(`stock-${idx}`).textContent = '—';
        return;
    }
    
    const product = PRODUCTS.find(p => p.id == productId);
    const stock = product?.stocks[originId] || 0;
    document.getElementById(`stock-${idx}`).textContent = stock;
}

function removeRow(idx) {
    document.getElementById(`row-${idx}`).remove();
}

document.getElementById('originSelect').addEventListener('change', function() {
    document.querySelectorAll('[id^="row-"]').forEach(row => {
        const idx = row.id.replace('row-', '');
        updateStock(idx);
    });
});

// Validar que origen y destino sean diferentes
document.getElementById('transferForm').addEventListener('submit', function(e) {
    const origin = document.getElementById('originSelect').value;
    const dest = document.getElementById('destSelect').value;
    if (origin && dest && origin === dest) {
        e.preventDefault();
        alert('La sucursal de origen y destino deben ser diferentes.');
    }
});

addProductRow();
</script>
@endpush
</x-app-layout>