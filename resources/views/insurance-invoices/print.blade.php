<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $insuranceInvoice->number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Arial', sans-serif; font-size:12px; color:#000; padding:20px; }
        .container { max-width:800px; margin:0 auto; }
        .header { display:flex; justify-content:space-between; align-items:flex-start;
                  border-bottom:2px solid #333; padding-bottom:12px; margin-bottom:15px; }
        .header-left { display:flex; gap:15px; align-items:center; }
        .header-left .logo { max-width:80px; max-height:80px; }
        .header-left .company-info h1 { font-size:16px; text-transform:uppercase; margin-bottom:3px; }
        .header-left .company-info p { font-size:10px; margin:1px 0; color:#333; }
        .header-right { text-align:right; }
        .header-right h2 { font-size:18px; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px; }
        .header-right .encf { font-family:monospace; font-size:14px; font-weight:700; color:#405189; }
        .header-right .doc-type { font-size:10px; color:#666; }

        .client-box { background:#f8f9fa; border:1px solid #ddd; padding:8px 12px;
                      border-radius:4px; margin-bottom:15px; }
        .client-box .row { display:flex; gap:15px; font-size:11px; }
        .client-box .row > div { flex:1; }
        .client-box .lbl { font-weight:700; color:#555; }

        table.items { width:100%; border-collapse:collapse; margin-bottom:15px; }
        table.items th { background:#333; color:#fff; padding:6px 8px; text-align:left;
                         font-size:10px; text-transform:uppercase; }
        table.items td { border-bottom:1px solid #ddd; padding:6px 8px; font-size:11px; }
        table.items .text-end { text-align:right; }

        .summary-section { display:flex; justify-content:space-between;
                           align-items:flex-start; gap:20px; margin-top:15px; }
        .qr-box { text-align:center; }
        .qr-box img { width:120px; height:120px; }
        .qr-box .qr-label { font-size:9px; color:#666; margin-top:3px; }

        .totals-box { flex:1; max-width:320px; margin-left:auto; }
        .totals-box .row { display:flex; justify-content:space-between;
                           padding:3px 0; font-size:11px; }
        .totals-box .row.grand { border-top:2px solid #333; margin-top:5px;
                                 padding-top:6px; font-size:14px; font-weight:700; }

        .footer { margin-top:25px; padding-top:10px; border-top:1px solid #999;
                  text-align:center; font-size:9px; color:#666; }
        .footer .dgi-info { margin-top:6px; font-size:9px; color:#405189; }

        .print-btn { display:inline-block; background:#405189; color:#fff; padding:8px 20px;
                     border-radius:5px; text-decoration:none; cursor:pointer; border:none;
                     font-size:14px; margin-bottom:15px; }
        @media print { .no-print { display:none !important; } body { padding:0; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print" style="text-align:center;">
            <button class="print-btn" onclick="window.print()">🖨️ Imprimir</button>
            <a href="{{ route('insurance-invoices.show', $insuranceInvoice) }}" class="print-btn" style="background:#6c757d;">Volver</a>
        </div>

        {{-- HEADER --}}
        <div class="header">
            <div class="header-left">
                @if(!empty($company['logo']))
                    <img src="{{ asset('storage/' . $company['logo']) }}" alt="Logo" class="logo">
                @endif
                <div class="company-info">
                    <h1>{{ $company['name'] ?? 'Mi Empresa' }}</h1>
                    @if(!empty($company['rnc']))
                        <p><strong>RNC:</strong> {{ $company['rnc'] }}</p>
                    @endif
                    @if(!empty($company['address']))
                        <p>{{ $company['address'] }}</p>
                    @endif
                    @if(!empty($company['phone']))
                        <p><strong>Tel:</strong> {{ $company['phone'] }}</p>
                    @endif
                </div>
            </div>
            <div class="header-right">
                <h2>Factura a Seguro</h2>
                <div class="doc-type">
                    @switch($insuranceInvoice->ncf_type)
                        @case('credito_fiscal') Factura de Crédito Fiscal Electrónica @break
                        @case('gubernamental') Comprobante Gubernamental Electrónico @break
                        @case('consumidor_final') Factura de Consumo Electrónica @break
                        @case('regimen_especial') Comprobante Régimen Especial Electrónico @break
                        @default Comprobante Fiscal Electrónico
                    @endswitch
                </div>
                @if($insuranceInvoice->encf)
                    <div class="encf">{{ $insuranceInvoice->encf }}</div>
                @endif
                @if($insuranceInvoice->enviada_dgii_at)
                    <p style="font-size:10px; margin-top:3px;">
                        <strong>Fecha Emisión:</strong> {{ $insuranceInvoice->invoice_date->format('d/m/Y') }}
                    </p>
                @endif
                <p style="font-size:10px;">
                    <strong>Factura N°:</strong> {{ $insuranceInvoice->number }}
                </p>
            </div>
        </div>

        {{-- CLIENTE --}}
        <div class="client-box">
            <div class="row">
                <div>
                    <span class="lbl">Seguro / ARS:</span>
                    {{ $insuranceInvoice->customer_business_name ?? $insuranceInvoice->insurance->name }}
                </div>
                <div>
                    <span class="lbl">RNC:</span>
                    {{ $insuranceInvoice->customer_rnc ?? '—' }}
                </div>
            </div>
        </div>

        {{-- CONCEPTO --}}
        @if($insuranceInvoice->concept)
            <div class="client-box">
                <div class="row">
                    <div>
                        <span class="lbl">Concepto:</span>
                        {{ $insuranceInvoice->concept }}
                    </div>
                </div>
            </div>
        @endif

        {{-- ITEMS --}}
        <table class="items">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-end" style="width:20%;">Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $insuranceInvoice->concept ?? 'Servicios médicos' }}</td>
                    <td class="text-end">RD$ {{ number_format($insuranceInvoice->subtotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- QR + TOTALES --}}
        <div class="summary-section">
            <div class="qr-box">
                @if($insuranceInvoice->qr_link)
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($insuranceInvoice->qr_link) }}"
                         alt="Código QR DGII">
                    <div class="qr-label">Verifique en dgii.gov.do</div>
                @else
                    <div style="width:120px; height:120px; border:1px dashed #ccc;
                                display:flex; align-items:center; justify-content:center;
                                font-size:9px; color:#999;">
                        QR pendiente
                    </div>
                @endif
            </div>

            <div class="totals-box">
                <div class="row">
                    <span>Subtotal:</span>
                    <span>RD$ {{ number_format($insuranceInvoice->subtotal, 2) }}</span>
                </div>
                @if($insuranceInvoice->with_tax)
                    <div class="row">
                        <span>ITBIS (18%):</span>
                        <span>RD$ {{ number_format($insuranceInvoice->tax_amount, 2) }}</span>
                    </div>
                @endif
                <div class="row grand">
                    <span>TOTAL:</span>
                    <span>RD$ {{ number_format($insuranceInvoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <div>{{ $company['footer_text'] ?? '¡Gracias por su preferencia!' }}</div>
            <div style="margin-top:3px;">{{ $company['name'] }}</div>
            @if($insuranceInvoice->encf)
                <div class="dgi-info">
                    <strong>Comprobante Fiscal Electrónico (e-CF) {{ $insuranceInvoice->encf }}</strong>
                </div>
            @endif
        </div>
    </div>
</body>
</html>