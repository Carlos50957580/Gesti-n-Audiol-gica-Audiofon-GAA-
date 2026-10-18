<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket {{ $productInvoice->number }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        /* ✅ Sin tamaño ni márgenes fijos: el ancho lo define la impresora/papel que elija el driver */
        @page {
            size: auto;
            margin: 0;
        }

        html, body {
            width: 100%;          /* ✅ ocupa todo el ancho disponible */
            min-height: 100%;
            height: auto;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 11px;
            color: #000;
            line-height: 1.25;
            background: #fff;
        }

        /* Pequeño respiro interno para que el texto no toque el borde del papel */
        body { padding: 2mm 3mm; }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .bold        { font-weight: bold; }
        .uppercase   { text-transform: uppercase; }

        .header {
            width: 100%;
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
            margin-bottom: 3px;
        }
        .header .logo { max-width: 70%; max-height: 16mm; margin-bottom: 2px; }
        .header h1 { font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 1px; }
        .header .info { font-size: 9px; }
        .header .doc-type { font-size: 10px; font-weight: bold; margin-top: 2px; }

        .separator { width: 100%; border-top: 1px dashed #000; margin: 2px 0; }

        .section-title {
            width: 100%;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 0;
            margin: 4px 0 2px 0;
            background: transparent;
            color: #000;
        }

        .client { width: 100%; font-size: 10px; margin-bottom: 3px; }
        .client .row { display: flex; justify-content: space-between; gap: 6px; }
        .client .lbl { font-weight: bold; }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 3px;
        }
        table.items th {
            border-bottom: 1px dashed #000;
            padding: 2px 0;
            text-align: left;
            font-size: 9px;
        }
        table.items th.text-right { text-align: right; }
        table.items td { padding: 1px 0; vertical-align: top; }
        table.items .product-name { font-weight: bold; }
        table.items .product-code { font-size: 8px; color: #333; }

        .totals { width: 100%; font-size: 10px; margin-top: 2px; }
        .totals .row { display: flex; justify-content: space-between; padding: 1px 0; }

        .totals .row.grand {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-top: 2px;
            padding: 3px 0;
            font-size: 12px;
            font-weight: bold;
        }

        .qr-section { width: 100%; text-align: center; margin: 4px 0; }
        .qr-section img { width: 110px; height: 110px; max-width: 60%; height: auto; }
        .qr-section .qr-label { font-size: 8px; margin-top: 2px; }
        .qr-section .sec-info { font-size: 8px; margin-top: 1px; word-break: break-all; }

        .footer {
            width: 100%;
            text-align: center;
            font-size: 9px;
            border-top: 1px dashed #000;
            padding-top: 3px;
            margin-top: 4px;
        }
        .footer .dgi-info { font-size: 8px; margin-top: 2px; }
        .footer .thanks   { font-weight: bold; margin-bottom: 2px; }

        .no-print {
            width: 100%;
            text-align: center;
            margin-bottom: 8px;
            padding: 4px;
        }
        .print-btn {
            display: inline-block;
            background: #405189;
            color: #fff;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 11px;
            margin: 0 2px;
        }

        @media print {
            .no-print { display: none !important; }
            html, body {
                width: 100%;
                height: auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    {{-- Botones (no se imprimen) --}}
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Imprimir Ticket</button>
        <a href="{{ url('/product-invoices/'.$productInvoice->id) }}" class="print-btn" style="background:#6c757d;">Volver</a>
    </div>

    {{-- HEADER --}}
    <div class="header">
        @if(!empty($company['logo']))
            <img src="{{ asset('storage/' . $company['logo']) }}" alt="Logo" class="logo">
        @endif
        <h1>{{ $company['name'] ?? 'Mi Empresa' }}</h1>
        @if(!empty($company['rnc']))
            <div class="info">RNC: {{ $company['rnc'] }}</div>
        @endif
        @if(!empty($company['address']))
            <div class="info">{{ $company['address'] }}</div>
        @endif
        @if(!empty($company['phone']))
            <div class="info">Tel: {{ $company['phone'] }}</div>
        @endif

        <div class="doc-type">
            @switch($productInvoice->ncf_type)
                @case('consumidor_final') FACTURA DE CONSUMO @break
                @case('credito_fiscal')   FACTURA DE CRÉDITO FISCAL @break
                @case('gubernamental')    COMPROBANTE GUBERNAMENTAL @break
                @case('regimen_especial') RÉGIMEN ESPECIAL @break
                @default                  COMPROBANTE FISCAL
            @endswitch
        </div>
        @if($productInvoice->encf)
            <div class="info bold">{{ $productInvoice->encf }}</div>
        @endif
    </div>

    {{-- DATOS DE LA FACTURA --}}
    <div class="client">
        <div class="row">
            <span class="lbl">Factura N°:</span>
            <span>{{ $productInvoice->number }}</span>
        </div>
        <div class="row">
            <span class="lbl">Fecha:</span>
            <span>{{ $productInvoice->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @if($productInvoice->ecfSequence && $productInvoice->ecfSequence->fecha_vencimiento)
            <div class="row">
                <span class="lbl">Vence:</span>
                <span>{{ $productInvoice->ecfSequence->fecha_vencimiento->format('d/m/Y') }}</span>
            </div>
        @endif
        <div class="row">
            <span class="lbl">Sucursal:</span>
            <span>{{ $productInvoice->branch->name }}</span>
        </div>
        <div class="row">
            <span class="lbl">Cajero:</span>
            <span>{{ $productInvoice->user->name }}</span>
        </div>
    </div>

    <div class="separator"></div>

    {{-- CLIENTE --}}
    <div class="client">
        <div class="row">
            <span class="lbl">Cliente:</span>
            <span>{{ $productInvoice->customer_business_name 
                   ?? ($productInvoice->patient->first_name . ' ' . $productInvoice->patient->last_name) }}</span>
        </div>
        <div class="row">
            <span class="lbl">RNC/Cédula:</span>
            <span>{{ $productInvoice->customer_rnc ?? $productInvoice->patient->cedula ?? '—' }}</span>
        </div>
    </div>

    {{-- ITEMS --}}
    <div class="section-title">Productos</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width:10%;">Cant</th>
                <th style="width:55%;">Descripción</th>
                <th class="text-right" style="width:35%;">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productInvoice->items as $item)
                <tr>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        <div class="product-name">{{ $item->product->name ?? 'Producto' }}</div>
                        <div class="product-code">{{ $item->product->code ?? '' }}</div>
                    </td>
                    <td class="text-right">RD$ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALES --}}
    <div class="totals">
        <div class="row">
            <span>Subtotal:</span>
            <span>RD$ {{ number_format($productInvoice->subtotal, 2) }}</span>
        </div>
        @if($productInvoice->tax_amount > 0)
            <div class="row">
                <span>ITBIS (18%):</span>
                <span>RD$ {{ number_format($productInvoice->tax_amount, 2) }}</span>
            </div>
        @endif
        @if($productInvoice->discount > 0)
            <div class="row">
                <span>Descuento:</span>
                <span>- RD$ {{ number_format($productInvoice->discount, 2) }}</span>
            </div>
        @endif
        <div class="row grand">
            <span>TOTAL:</span>
            <span>RD$ {{ number_format($productInvoice->total, 2) }}</span>
        </div>
    </div>

    {{-- QR + CÓDIGO DE SEGURIDAD --}}
    @if($productInvoice->qr_link)
        @php
            $qrParams = [];
            $queryString = parse_url($productInvoice->qr_link, PHP_URL_QUERY);
            if ($queryString) {
                parse_str($queryString, $qrParams);
            }
            $codigoSeguridad = $qrParams['CodigoSeguridad'] ?? null;
            $fechaFirmaRaw   = $qrParams['FechaFirma'] ?? null;
            $fechaFirma      = $fechaFirmaRaw
                ? \Carbon\Carbon::parse(urldecode($fechaFirmaRaw))->format('d/m/Y H:i:s')
                : null;
        @endphp

        <div class="qr-section">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($productInvoice->qr_link) }}"
                 alt="QR DGII">
            <div class="qr-label">Verifique en dgii.gov.do</div>

            @if($codigoSeguridad)
                <div class="sec-info">
                    <strong>Código de Seguridad:</strong> {{ $codigoSeguridad }}
                </div>
            @endif

            @if($fechaFirma)
                <div class="sec-info">
                    <strong>Fecha Firma:</strong> {{ $fechaFirma }}
                </div>
            @endif
        </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <div class="thanks">{{ $company['footer_text'] ?? '¡GRACIAS POR SU COMPRA!' }}</div>
        <div>{{ $company['name'] }}</div>

        @if($productInvoice->encf)
            <div class="dgi-info">
                e-CF: {{ $productInvoice->encf }}
                @if($productInvoice->track_id)
                    <br>Track ID: {{ Str::limit($productInvoice->track_id, 20) }}
                @endif
            </div>
        @endif
    </div>
</body>
</html>