<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $productInvoice->number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Times New Roman', serif; font-size:12px; color:#000; padding:20px; }
        .container { max-width:800px; margin:0 auto; }
        .header { text-align:center; border-bottom:2px double #000; padding-bottom:12px; margin-bottom:15px; }
        .header h1 { font-size:20px; text-transform:uppercase; letter-spacing:2px; }
        .header .rnc { font-weight:700; }
        .header .info { font-size:11px; margin-top:2px; }
        .title { text-align:center; margin:15px 0; }
        .title h2 { font-size:16px; text-transform:uppercase; border-bottom:2px solid #000; display:inline-block; padding-bottom:3px; }
        .section { margin-bottom:15px; }
        .section-title { background:#f0f0f0; padding:5px 8px; font-weight:700; font-size:12px; text-transform:uppercase; border-bottom:1px solid #000; margin-bottom:8px; }
        .section-fiscal { border:1px solid #405189; border-radius:4px; overflow:hidden; }
        .section-fiscal .section-title { background:#405189; color:#fff; border-bottom:none; }
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; padding:5px 8px; }
        .info-grid .lbl { font-weight:700; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        table th { background:#f5f5f5; border:1px solid #000; padding:5px; text-align:left; font-size:10px; text-transform:uppercase; }
        table td { border:1px solid #000; padding:5px; }
        table .text-end { text-align:right; }
        table .text-center { text-align:center; }
        .totals { margin-top:10px; }
        .totals .row { display:flex; justify-content:flex-end; padding:3px 0; }
        .totals .row .lbl { width:150px; font-weight:700; text-align:right; margin-right:10px; }
        .totals .row .val { width:120px; text-align:right; font-weight:700; }
        .totals .grand { border-top:2px solid #000; margin-top:5px; padding-top:5px; font-size:14px; }
        .footer { margin-top:25px; padding-top:10px; border-top:1px solid #000; text-align:center; font-size:10px; }
        .print-btn { display:inline-block; background:#405189; color:#fff; padding:8px 20px; border-radius:5px; text-decoration:none; cursor:pointer; border:none; font-size:14px; margin-bottom:15px; }
        @media print { .no-print { display:none !important; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print" style="text-align:center;">
            <button class="print-btn" onclick="window.print()">🖨️ Imprimir</button>
            <a href="{{ url('/product-invoices/'.$productInvoice->id) }}" class="print-btn" style="background:#6c757d;">Volver</a>
        </div>

        {{-- Header --}}
        <div class="header">
            <h1>{{ $company['name'] ?? 'Mi Clínica' }}</h1>
            @if($company['rnc'])
                <div class="rnc">RNC: {{ $company['rnc'] }}</div>
            @endif
            @if($company['address'])
                <div class="info">{{ $company['address'] }}</div>
            @endif
            @if($company['phone'])
                <div class="info">Tel: {{ $company['phone'] }}</div>
            @endif
        </div>

        <div class="title">
            <h2>Factura de Productos</h2>
        </div>

        {{-- Info --}}
        <div class="section">
            <div class="section-title">Información de la Factura</div>
            <div class="info-grid">
                <div><span class="lbl">Número:</span> {{ $productInvoice->number }}</div>
                <div><span class="lbl">Fecha:</span> {{ $productInvoice->created_at->format('d/m/Y H:i') }}</div>
                <div><span class="lbl">Paciente:</span> {{ $productInvoice->patient->first_name }} {{ $productInvoice->patient->last_name }}</div>
                <div><span class="lbl">Cédula:</span> {{ $productInvoice->patient->cedula ?? '—' }}</div>
                <div><span class="lbl">Sucursal:</span> {{ $productInvoice->branch->name }}</div>
                <div><span class="lbl">Atendido por:</span> {{ $productInvoice->user->name }}</div>
            </div>
        </div>

        {{-- Datos Fiscales (NCF) --}}
        @if($productInvoice->with_ncf && $productInvoice->ncf)
            <div class="section section-fiscal">
                <div class="section-title">Comprobante Fiscal</div>
                <div class="info-grid">
                    <div><span class="lbl">NCF:</span> <strong style="font-family:monospace;">{{ $productInvoice->ncf }}</strong></div>
                    <div><span class="lbl">Tipo:</span> {{ ucfirst(str_replace('_', ' ', $productInvoice->ncf_type ?? '—')) }}</div>
                    @if($productInvoice->customer_rnc)
                        <div><span class="lbl">RNC Cliente:</span> {{ $productInvoice->customer_rnc }}</div>
                    @endif
                    @if($productInvoice->customer_business_name)
                        <div><span class="lbl">Razón Social:</span> {{ $productInvoice->customer_business_name }}</div>
                    @endif
                    @if($productInvoice->ncfSequence)
                        <div style="grid-column: span 2;">
                            <span class="lbl">Secuencia:</span> {{ $productInvoice->ncfSequence->name }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Items --}}
        <div class="section">
            <div class="section-title">Productos</div>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productInvoice->items as $item)
                        <tr>
                            <td>{{ $item->product->code }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">RD$ {{ number_format($item->price, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($item->subtotal, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($item->total_with_tax, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totales --}}
        <div class="totals">
            <div class="row">
                <span class="lbl">Subtotal:</span>
                <span class="val">RD$ {{ number_format($productInvoice->subtotal, 2) }}</span>
            </div>
            <div class="row">
                <span class="lbl">ITBIS:</span>
                <span class="val">RD$ {{ number_format($productInvoice->tax_amount, 2) }}</span>
            </div>
            @if($productInvoice->discount > 0)
                <div class="row">
                    <span class="lbl">Descuento:</span>
                    <span class="val">- RD$ {{ number_format($productInvoice->discount, 2) }}</span>
                </div>
            @endif
            <div class="row grand">
                <span class="lbl">TOTAL:</span>
                <span class="val">RD$ {{ number_format($productInvoice->total, 2) }}</span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div>{{ $company['footer_text'] ?? 'Gracias por su preferencia' }}</div>
            <div style="margin-top:3px;">{{ $company['name'] }}</div>
        </div>
    </div>
</body>
</html>