<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuadre de Caja - {{ $branch->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        
        .print-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        
        .header .company-name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .header .company-rnc {
            font-size: 12px;
            font-weight: 600;
        }
        
        .header .company-info {
            font-size: 11px;
            margin-top: 2px;
        }
        
        /* ── Título ── */
        .title-section {
            text-align: center;
            margin: 10px 0 15px 0;
        }
        
        .title-section h2 {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            display: inline-block;
        }
        
        .title-section .subtitle {
            font-size: 13px;
            font-weight: 600;
            margin-top: 5px;
        }
        
        /* ── Info periodo ── */
        .period-info {
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .period-info .recepcionista {
            font-weight: 400;
            font-size: 12px;
            color: #555;
        }
        
        /* ── Grid de KPIs ── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .kpi-item {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: center;
        }
        
        .kpi-item .label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
        }
        
        .kpi-item .value {
            font-size: 16px;
            font-weight: 800;
            margin-top: 2px;
        }
        
        .kpi-item .value.success { color: #0ab39c; }
        .kpi-item .value.warning { color: #f7b84b; }
        .kpi-item .value.danger  { color: #f06548; }
        .kpi-item .value.primary { color: #405189; }
        
        /* ── Secciones ── */
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 8px;
            background: #f0f0f0;
            padding: 4px 8px;
        }
        
        /* ── Tablas ── */
        .table-wrap {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        table th {
            background: #f5f5f5;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
        }
        
        table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }
        
        table .text-right { text-align: right; }
        table .text-center { text-align: center; }
        
        /* ── Totales ── */
        .totals-row td {
            font-weight: 700;
            background: #f5f5f5;
        }
        
        /* ── Footer ── */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
        }
        
        .footer .footer-text {
            font-style: italic;
        }
        
        .footer .footer-company {
            font-weight: 600;
            margin-top: 3px;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-box .line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 30px auto 5px auto;
        }
        
        .signature-box .label {
            font-size: 11px;
            font-weight: 600;
        }
        
        /* ── Print styles ── */
        @media print {
            body { padding: 10px; }
            .no-print { display: none !important; }
            .kpi-item { border: 1px solid #000 !important; }
            table th { background: #f5f5f5 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .section-title { background: #f0f0f0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
        
        /* ── Botón impresión ── */
        .print-btn {
            display: inline-block;
            background: #405189;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        
        .print-btn:hover { background: #2d3e7a; }
    </style>
</head>
<body>
    <div class="print-container">
        
        <!-- Botón de impresión -->
        <div class="no-print" style="text-align:center;margin-bottom:15px;">
            <button class="print-btn" onclick="window.print()">
                🖨️ Imprimir Cuadre de Caja
            </button>
            <a href="{{ route('receptionist.reports.index') }}" 
               class="print-btn" 
               style="background:#6c757d;text-decoration:none;display:inline-block;">
                Volver
            </a>
        </div>

        <!-- ============================================ -->
        <!-- HEADER - EMPRESA                            -->
        <!-- ============================================ -->
        <div class="header">
            <div class="company-name">{{ $company['name'] ?? 'Mi Clínica' }}</div>
            @if($company['rnc'])
                <div class="company-rnc">RNC: {{ $company['rnc'] }}</div>
            @endif
            @if($company['address'])
                <div class="company-info">{{ $company['address'] }}</div>
            @endif
            @if($company['phone'])
                <div class="company-info">Tel: {{ $company['phone'] }}</div>
            @endif
        </div>

        <!-- ============================================ -->
        <!-- TÍTULO                                      -->
        <!-- ============================================ -->
        <div class="title-section">
            <h2>Cuadre de Caja</h2>
            <div class="subtitle">{{ $branch->name ?? 'Sucursal' }}</div>
        </div>

        <!-- ============================================ -->
        <!-- PERIODO Y RECEPCIONISTA                     -->
        <!-- ============================================ -->
        <div class="period-info">
            Período: {{ \Carbon\Carbon::parse($range['from'])->format('d/m/Y H:i') }} 
            al {{ \Carbon\Carbon::parse($range['to'])->format('d/m/Y H:i') }}
            <br>
            <span class="recepcionista">
                Recepcionista: <strong>{{ $user->name }}</strong> · 
                Generado: {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>

        <!-- ============================================ -->
        <!-- KPIs - Resumen General                      -->
        <!-- ============================================ -->
        <div class="kpi-grid">
            <div class="kpi-item">
                <div class="label">Total Facturas</div>
                <div class="value primary">{{ $totalFacturas }}</div>
            </div>
            <div class="kpi-item">
                <div class="label">Pagadas</div>
                <div class="value success">{{ $pagadas }}</div>
            </div>
            <div class="kpi-item">
                <div class="label">Pendientes</div>
                <div class="value warning">{{ $pendientes }}</div>
            </div>
            <div class="kpi-item">
                <div class="label">Canceladas</div>
                <div class="value danger">{{ $canceladas }}</div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- RESUMEN FINANCIERO (Solo lo cobrado por el usuario) -->
        <!-- ============================================ -->
        <div class="section">
            <div class="section-title">Resumen Financiero - Cobros Realizados</div>
            <div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="kpi-item">
                    <div class="label">Subtotal</div>
                    <div class="value primary">RD$ {{ number_format($subtotal, 2, ',', '.') }}</div>
                </div>
                <div class="kpi-item">
                    <div class="label">Descuentos Seguros</div>
                    <div class="value warning">RD$ {{ number_format($descuentos, 2, ',', '.') }}</div>
                </div>
                <div class="kpi-item">
                    <div class="label">Total Facturado</div>
                    <div class="value success">RD$ {{ number_format($totalFacturado, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- COBROS POR MÉTODO                           -->
        <!-- ============================================ -->
        <div class="section">
            <div class="section-title">Cobros por Método de Pago</div>
            <div class="kpi-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="kpi-item">
                    <div class="label">Efectivo</div>
                    <div class="value success">RD$ {{ number_format($cobros->efectivo ?? 0, 2, ',', '.') }}</div>
                </div>
                <div class="kpi-item">
                    <div class="label">Tarjeta</div>
                    <div class="value primary">RD$ {{ number_format($cobros->tarjeta ?? 0, 2, ',', '.') }}</div>
                </div>
                <div class="kpi-item">
                    <div class="label">Transferencia</div>
                    <div class="value" style="color:#7c3aed;">RD$ {{ number_format($cobros->transferencia ?? 0, 2, ',', '.') }}</div>
                </div>
                <div class="kpi-item" style="background:#f0f4ff;border-color:#405189;">
                    <div class="label" style="font-size:11px;">Total Cobrado</div>
                    <div class="value" style="color:#405189;font-size:18px;">
                        RD$ {{ number_format($cobros->total_cobrado ?? 0, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- FACTURAS COBRADAS POR EL USUARIO            -->
        <!-- ============================================ -->
        <div class="section">
            <div class="section-title">Detalle de Facturas Cobradas</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Factura</th>
                            <th>Paciente</th>
                            <th>Médico</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right">Desc.</th>
                            <th class="text-right">Total</th>
                            <th>Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $index => $inv)
                            @php
                                $receipt = $inv->receipts->first();
                                $payMethods = [];
                                if ($receipt && $receipt->cash_amount > 0) $payMethods[] = 'Efectivo';
                                if ($receipt && $receipt->card_amount > 0) $payMethods[] = 'Tarjeta';
                                if ($receipt && $receipt->transfer_amount > 0) $payMethods[] = 'Transferencia';
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="font-weight:700;color:#405189;">{{ $inv->invoice_number }}</td>
                                <td>{{ $inv->patient->first_name }} {{ $inv->patient->last_name }}</td>
                                <td>{{ $inv->doctor->name ?? '—' }}</td>
                                <td class="text-right">RD$ {{ number_format($inv->subtotal, 2, ',', '.') }}</td>
                                <td class="text-right" style="color:#f7b84b;">RD$ {{ number_format($inv->insurance_discount, 2, ',', '.') }}</td>
                                <td class="text-right" style="font-weight:700;color:#0ab39c;">RD$ {{ number_format($inv->total, 2, ',', '.') }}</td>
                                <td style="font-size:10px;">{{ implode(' + ', $payMethods) ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding:15px;color:#999;">
                                    No has cobrado facturas en este período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($invoices->count() > 0)
                        <tfoot>
                            <tr class="totals-row">
                                <td colspan="4" class="text-right">TOTALES COBRADOS</td>
                                <td class="text-right">RD$ {{ number_format($invoices->sum('subtotal'), 2, ',', '.') }}</td>
                                <td class="text-right">RD$ {{ number_format($invoices->sum('insurance_discount'), 2, ',', '.') }}</td>
                                <td class="text-right">RD$ {{ number_format($invoices->sum('total'), 2, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- FIRMAS                                       -->
        <!-- ============================================ -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="line"></div>
                <div class="label">Firma del Recepcionista</div>
                <div style="font-size:11px;color:#666;margin-top:3px;">{{ $user->name }}</div>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <div class="label">Visto Bueno Administrador</div>
                <div style="font-size:11px;color:#666;margin-top:3px;">_________________________</div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- FOOTER                                       -->
        <!-- ============================================ -->
        <div class="footer">
            <div class="footer-text">Gracias por confiar en nosotros</div>
            <div class="footer-company">
                {{ $company['business_name'] ?? $company['name'] ?? 'Mi Clínica' }}
                @if($company['rnc'])
                    · RNC: {{ $company['rnc'] }}
                @endif
            </div>
            <div style="font-size:9px;color:#999;margin-top:3px;">
                Documento generado el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

    </div>
</body>
</html>