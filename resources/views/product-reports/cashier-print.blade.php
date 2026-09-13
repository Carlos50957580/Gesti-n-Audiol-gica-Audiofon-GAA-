<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuadre de Caja - Productos</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Times New Roman', serif; font-size:12px; padding:20px; }
        .container { max-width:1000px; margin:0 auto; }
        .header { text-align:center; border-bottom:3px double #000; padding-bottom:12px; margin-bottom:15px; }
        .header h1 { font-size:22px; text-transform:uppercase; letter-spacing:2px; }
        .header .rnc { font-weight:700; }
        .header .info { font-size:11px; }
        .title { text-align:center; margin:10px 0 15px; }
        .title h2 { font-size:18px; text-transform:uppercase; border-bottom:2px solid #000; display:inline-block; padding-bottom:3px; }
        .period { text-align:center; font-size:13px; font-weight:600; margin-bottom:15px; }
        .section { margin-bottom:15px; }
        .section-title { background:#f0f0f0; padding:5px 8px; font-weight:700; font-size:13px; text-transform:uppercase; border-bottom:1px solid #000; margin-bottom:8px; }
        .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:15px; }
        .kpi-item { border:1px solid #000; padding:8px 10px; text-align:center; }
        .kpi-item .label { font-size:10px; font-weight:700; text-transform:uppercase; }
        .kpi-item .value { font-size:16px; font-weight:800; margin-top:3px; }
        .kpi-item .value.success { color:#0ab39c; }
        .kpi-item .value.primary { color:#405189; }
        .kpi-item .value.info { color:#299cdb; }
        .kpi-item .value.danger { color:#e74c3c; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        table th { background:#f5f5f5; border:1px solid #000; padding:4px 6px; text-align:left; font-size:10px; text-transform:uppercase; }
        table td { border:1px solid #000; padding:4px 6px; }
        table .text-end { text-align:right; }
        table .text-center { text-align:center; }
        .totals-row td { font-weight:700; background:#f5f5f5; }
        .signature-section { display:flex; justify-content:space-between; margin-top:40px; }
        .signature-box { text-align:center; width:45%; }
        .signature-box .line { border-top:1px solid #000; width:80%; margin:40px auto 5px; }
        .signature-box .label { font-size:11px; font-weight:600; }
        .footer { margin-top:20px; padding-top:10px; border-top:1px solid #000; text-align:center; font-size:10px; }
        .no-print { text-align:center; margin-bottom:15px; }
        .print-btn { display:inline-block; background:#405189; color:#fff; padding:8px 20px; border-radius:5px; text-decoration:none; cursor:pointer; border:none; font-size:14px; }
        @media print { .no-print { display:none !important; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button class="print-btn" onclick="window.print()">🖨️ Imprimir</button>
            <a href="javascript:history.back()" class="print-btn" style="background:#6c757d;">Volver</a>
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
            <h2>Cuadre de Caja - Ventas de Productos</h2>
        </div>

        <div class="period">
            Período: {{ \Carbon\Carbon::parse($range['from'])->format('d/m/Y H:i') }}
            al {{ \Carbon\Carbon::parse($range['to'])->format('d/m/Y H:i') }}
            <br>
            <span style="font-weight:400;font-size:11px;">
                @if($branch) Sucursal: {{ $branch->name }} · @endif
                @if($paymentMethod) Método: {{ ucfirst($paymentMethod) }} · @endif
                @if(!empty($filterUserId) && $receipts->isNotEmpty())
                    Cobrado por: {{ $receipts->first()->user->name ?? '—' }} ·
                @endif
                Generado por: {{ $user->name }} · {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>

        <div class="section">
            <div class="section-title">Resumen de Cobros</div>
            <div class="kpi-grid">
                <div class="kpi-item">
                    <div class="label">Efectivo</div>
                    <div class="value success">RD$ {{ number_format($totals->efectivo, 2) }}</div>
                </div>
                <div class="kpi-item">
                    <div class="label">Tarjeta</div>
                    <div class="value primary">RD$ {{ number_format($totals->tarjeta, 2) }}</div>
                </div>
                <div class="kpi-item">
                    <div class="label">Transferencia</div>
                    <div class="value info">RD$ {{ number_format($totals->transferencia, 2) }}</div>
                </div>
                <div class="kpi-item" style="background:#f0f4ff;">
                    <div class="label" style="font-size:11px;">TOTAL COBRADO</div>
                    <div class="value" style="color:#405189;font-size:18px;">RD$ {{ number_format($totals->total, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Detalle de Recibos ({{ $receipts->count() }})</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Recibo</th>
                        <th>Fecha/Hora</th>
                        <th>Factura</th>
                        <th>Paciente</th>
                        <th>Cobrado por</th>
                        <th class="text-end">Efectivo</th>
                        <th class="text-end">Tarjeta</th>
                        <th class="text-end">Transfer.</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $i => $rec)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><code>{{ $rec->number }}</code></td>
                            <td>{{ $rec->created_at->format('d/m/Y H:i') }}</td>
                            <td><code>{{ $rec->invoice->number }}</code></td>
                            <td>{{ $rec->invoice->patient->first_name }} {{ $rec->invoice->patient->last_name }}</td>
                            <td>{{ $rec->user->name }}</td>
                            <td class="text-end">{{ $rec->cash_amount ? 'RD$ '.number_format($rec->cash_amount, 2) : '—' }}</td>
                            <td class="text-end">{{ $rec->card_amount ? 'RD$ '.number_format($rec->card_amount, 2) : '—' }}</td>
                            <td class="text-end">{{ $rec->transfer_amount ? 'RD$ '.number_format($rec->transfer_amount, 2) : '—' }}</td>
                            <td class="text-end fw-bold">RD$ {{ number_format($rec->total_paid, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center" style="padding:15px;color:#999;">
                                No hay cobros en el período con los filtros aplicados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($receipts->count() > 0)
                    <tfoot>
                        <tr class="totals-row">
                            <td colspan="6" class="text-end">TOTALES</td>
                            <td class="text-end">RD$ {{ number_format($totals->efectivo, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($totals->tarjeta, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($totals->transferencia, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($totals->total, 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

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

        <div class="footer">
            <div>{{ \App\Models\Setting::get('company_name') }}</div>
            <div style="font-size:9px;color:#999;margin-top:3px;">
                Documento generado el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</body>
</html>