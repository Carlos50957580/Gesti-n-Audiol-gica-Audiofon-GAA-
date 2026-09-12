<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo {{ $productReceipt->number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Times New Roman', serif; font-size:12px; padding:20px; }
        .container { max-width:700px; margin:0 auto; }
        .header { text-align:center; border-bottom:2px double #000; padding-bottom:12px; margin-bottom:15px; }
        .header h1 { font-size:20px; text-transform:uppercase; }
        .header .rnc { font-weight:700; }
        .header .info { font-size:11px; }
        .title { text-align:center; margin:15px 0; }
        .title h2 { font-size:16px; text-transform:uppercase; border-bottom:2px solid #000; display:inline-block; padding-bottom:3px; }
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; padding:10px; background:#f8f8f8; margin-bottom:15px; }
        .info-grid .lbl { font-weight:700; }
        table { width:100%; border-collapse:collapse; margin-bottom:15px; }
        table th, table td { border:1px solid #000; padding:6px; font-size:11px; }
        table th { background:#f0f0f0; text-transform:uppercase; font-size:10px; }
        .text-end { text-align:right; }
        .totals { margin-top:15px; }
        .totals .row { display:flex; justify-content:space-between; padding:4px 0; }
        .totals .grand { border-top:2px solid #000; margin-top:5px; padding-top:5px; font-size:16px; font-weight:700; }
        .footer { margin-top:25px; padding-top:10px; border-top:1px solid #000; text-align:center; font-size:10px; }
        .print-btn { display:inline-block; background:#405189; color:#fff; padding:8px 20px; border-radius:5px; text-decoration:none; cursor:pointer; border:none; font-size:14px; margin-bottom:15px; }
        @media print { .no-print { display:none !important; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print" style="text-align:center;">
            <button class="print-btn" onclick="window.print()">🖨️ Imprimir</button>
            <a href="{{ url('/product-receipts/'.$productReceipt->id) }}" class="print-btn" style="background:#6c757d;">Volver</a>
        </div>

        <div class="header">
            <h1>{{ \App\Models\Setting::get('company_name', 'Mi Clínica') }}</h1>
            @if(\App\Models\Setting::get('company_rnc'))
                <div class="rnc">RNC: {{ \App\Models\Setting::get('company_rnc') }}</div>
            @endif
            @if(\App\Models\Setting::get('company_address'))
                <div class="info">{{ \App\Models\Setting::get('company_address') }}</div>
            @endif
            @if(\App\Models\Setting::get('company_phone'))
                <div class="info">Tel: {{ \App\Models\Setting::get('company_phone') }}</div>
            @endif
        </div>

        <div class="title">
            <h2>Recibo de Pago</h2>
        </div>

        <div class="info-grid">
            <div><span class="lbl">Recibo:</span> {{ $productReceipt->number }}</div>
            <div><span class="lbl">Fecha:</span> {{ $productReceipt->created_at->format('d/m/Y H:i') }}</div>
            <div><span class="lbl">Factura:</span> {{ $productReceipt->invoice->number }}</div>
            <div><span class="lbl">Sucursal:</span> {{ $productReceipt->branch->name }}</div>
            <div><span class="lbl">Paciente:</span> {{ $productReceipt->invoice->patient->first_name }} {{ $productReceipt->invoice->patient->last_name }}</div>
            <div><span class="lbl">Cobrado por:</span> {{ $productReceipt->user->name }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productReceipt->invoice->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">RD$ {{ number_format($item->price, 2) }}</td>
                        <td class="text-end">RD$ {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            @if($productReceipt->cash_amount)
                <div class="row">
                    <span>Efectivo:</span>
                    <span>RD$ {{ number_format($productReceipt->cash_amount, 2) }}</span>
                </div>
            @endif
            @if($productReceipt->card_amount)
                <div class="row">
                    <span>Tarjeta:</span>
                    <span>RD$ {{ number_format($productReceipt->card_amount, 2) }}</span>
                </div>
            @endif
            @if($productReceipt->transfer_amount)
                <div class="row">
                    <span>Transferencia:</span>
                    <span>RD$ {{ number_format($productReceipt->transfer_amount, 2) }}</span>
                </div>
            @endif
            <div class="row grand">
                <span>TOTAL PAGADO:</span>
                <span>RD$ {{ number_format($productReceipt->total_paid, 2) }}</span>
            </div>
        </div>

        <div class="footer">
            <div>Gracias por su preferencia</div>
            <div style="margin-top:3px;">{{ \App\Models\Setting::get('company_name') }}</div>
        </div>
    </div>
</body>
</html>