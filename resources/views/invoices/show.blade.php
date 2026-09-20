<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
:root {
    --rp:#405189; --rt:#0ab39c; --ra:#f7b84b; --rr:#f06548; --rv:#7066e0;
    --ink:#1e2535; --muted:#6b7a99; --border:#edf0f7; --surface:#f8faff;
}

.btn-print-thermal {
    background: linear-gradient(135deg, #0ab39c, #089b86);
    color: #fff; border: none; border-radius: 2rem;
    padding: .5rem 1.2rem; font-size: .85rem; font-weight: 700;
    cursor: pointer; transition: opacity .18s;
    display: flex; align-items: center; gap: .4rem;
}
.btn-print-thermal:hover { opacity: .88; }

/* ── Action bar (hidden on print) ── */
.action-bar {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem;
}
.btn-print {
    background:linear-gradient(135deg,var(--rp),var(--rt));
    color:#fff; border:none; border-radius:2rem;
    padding:.5rem 1.2rem; font-size:.85rem; font-weight:700;
    cursor:pointer; transition:opacity .18s; display:flex; align-items:center; gap:.4rem;
}
.btn-print:hover { opacity:.88; }

/* ── Invoice paper ── */
.invoice-paper {
    background:#fff; border-radius:.85rem;
    box-shadow:0 4px 40px rgba(64,81,137,.1);
    overflow:hidden; max-width:780px; margin:0 auto;
}

/* ── Header band ── */
.inv-header {
    background:linear-gradient(135deg,#405189 0%,#2d3e7a 50%,#0ab39c 100%);
    padding:2rem 2.25rem; color:#fff; position:relative; overflow:hidden;
}
.inv-header::before {
    content:''; position:absolute; right:-30px; top:-30px;
    width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.07);
}
.inv-header::after {
    content:''; position:absolute; right:40px; bottom:-50px;
    width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,.05);
}
.inv-brand { 
    font-size:1.4rem; font-weight:900; letter-spacing:-.03em; position:relative; z-index:1; 
}
.inv-brand span { 
    opacity:.7; font-weight:400; font-size:.9rem; display:block; margin-top:.1rem; 
}
.inv-num { font-family:monospace; font-size:1.5rem; font-weight:800; letter-spacing:.05em; position:relative; z-index:1; }
.inv-num small { font-size:.72rem; opacity:.7; display:block; font-family:sans-serif; font-weight:400; letter-spacing:0; }

/* Status pill */
.status-pill {
    display:inline-flex; align-items:center; gap:.4rem;
    border-radius:2rem; padding:.3rem .9rem; font-size:.8rem; font-weight:700;
    position:relative; z-index:1;
}
.status-pill.pendiente { background:rgba(247,184,77,.2); color:#f7b84d; border:1px solid rgba(247,184,77,.3); }
.status-pill.pagada { background:rgba(10,179,156,.2); color:#7fffea; border:1px solid rgba(10,179,156,.3); }
.status-pill.cancelada { background:rgba(240,101,72,.2); color:#ffb3a3; border:1px solid rgba(240,101,72,.3); }

/* DGII pill */
.dgii-pill {
    display:inline-flex; align-items:center; gap:.4rem;
    border-radius:2rem; padding:.3rem .9rem; font-size:.78rem; font-weight:700;
    position:relative; z-index:1; margin-left:.4rem;
}
.dgii-pill.aceptado { background:rgba(10,179,156,.25); color:#7fffea; border:1px solid rgba(10,179,156,.4); }
.dgii-pill.enviado { background:rgba(112,102,224,.25); color:#c9c4ff; border:1px solid rgba(112,102,224,.4); }
.dgii-pill.rechazado, .dgii-pill.error { background:rgba(240,101,72,.25); color:#ffb3a3; border:1px solid rgba(240,101,72,.4); }
.dgii-pill.pendiente { background:rgba(247,184,77,.25); color:#ffd989; border:1px solid rgba(247,184,77,.4); }

/* ── Body ── */
.inv-body { padding:1.75rem 2.25rem; }

/* Info grid */
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.5rem; }
@media(max-width:580px) { .info-grid { grid-template-columns:1fr; } }

.info-box { background:var(--surface); border-radius:.6rem; padding:.9rem 1rem; border:1px solid var(--border); }
.info-box-lbl { font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.35rem; }
.info-box-val { font-size:.9rem; font-weight:700; color:var(--ink); }
.info-box-sub { font-size:.75rem; color:var(--muted); margin-top:.12rem; }

/* ── e-CF block ── */
.ecf-block {
    background:linear-gradient(135deg, rgba(64,81,137,.04), rgba(10,179,156,.04));
    border:1px dashed rgba(64,81,137,.25);
    border-radius:.65rem; padding:1rem 1.15rem; margin-bottom:1.5rem;
}
.ecf-block-title {
    font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em;
    color:var(--rp); margin-bottom:.75rem; display:flex; align-items:center; gap:.4rem;
}
.ecf-grid { display:grid; grid-template-columns:1fr 1fr; gap:.85rem; }
@media(max-width:580px) { .ecf-grid { grid-template-columns:1fr; } }
.ecf-item-lbl { font-size:.65rem; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); margin-bottom:.2rem; }
.ecf-item-val { font-size:.85rem; font-weight:700; color:var(--ink); font-family:monospace; word-break:break-all; }
.ecf-qr {
    display:flex; align-items:center; gap:1rem;
    padding:.85rem 1rem; background:#fff; border-radius:.5rem;
    border:1px solid var(--border); margin-top:.85rem;
}
.ecf-qr-img {
    width:90px; height:90px; border-radius:.4rem; background:#fff;
    border:1px solid var(--border); padding:.25rem; flex-shrink:0;
}
.ecf-qr-info { font-size:.78rem; color:var(--muted); line-height:1.5; }
.ecf-qr-info strong { color:var(--ink); }

/* Estado pill en bloque e-CF */
.ecf-status {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.22rem .7rem; border-radius:2rem; font-size:.72rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.04em;
}
.ecf-status.aceptado { background:rgba(10,179,156,.15); color:#067a6b; }
.ecf-status.enviado { background:rgba(112,102,224,.15); color:#4f44b8; }
.ecf-status.rechazado, .ecf-status.error { background:rgba(240,101,72,.15); color:#c0392b; }
.ecf-status.pendiente, .ecf-status.procesando { background:rgba(247,184,77,.15); color:#a4730a; }
.ecf-status.sin_enviar { background:#f0f2f7; color:#6b7a99; }

/* ── Divider ── */
.inv-divider { border:none; border-top:1px dashed var(--border); margin:1.25rem 0; }

/* ── Totals ── */
.totals-box { background:var(--surface); border-radius:.65rem; overflow:hidden; border:1px solid var(--border); }
.tot-row { display:flex; justify-content:space-between; padding:.6rem 1rem; border-bottom:1px solid var(--border); font-size:.85rem; }
.tot-row:last-child { border-bottom:none; background:linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06)); padding:.75rem 1rem; }
.tot-lbl { color:var(--muted); }
.tot-val { font-weight:600; color:var(--ink); }
.tot-row:last-child .tot-lbl,
.tot-row:last-child .tot-val { font-size:1rem; font-weight:800; color:var(--rp); }

/* ── Invoice items ── */
.items-table { width:100%; border-collapse:collapse; }
.items-table th { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); padding:.55rem .75rem; border-bottom:2px solid var(--border); }
.items-table td { padding:.6rem .75rem; border-bottom:1px solid var(--border); font-size:.83rem; color:var(--ink); }
.items-table tr:last-child td { border-bottom:none; }

/* ── Footer ── */
.inv-footer {
    background:var(--surface); border-top:1px solid var(--border);
    padding:1rem 2.25rem; text-align:center;
    font-size:.75rem; color:var(--muted);
}

/* ── Print styles ── */
@media print {
    .action-bar, .page-title-box, nav, .navbar, .sidebar, .breadcrumb,
    .no-print, .ecf-actions { display:none!important; }
    body, .page-content, .container-fluid { padding:0!important; margin:0!important; background:#fff!important; }
    .invoice-paper { box-shadow:none!important; border-radius:0!important; max-width:100%!important; }
    .inv-header { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .info-box, .totals-box, .items-table th, .ecf-block { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .collapse:not(.show) { display:block!important; }
}
</style>

{{-- ── Action bar ── --}}
<div class="action-bar">
    <div>
        <h4 class="mb-0" style="font-weight:800;">
            Factura {{ $invoice->invoice_number }}
        </h4>
        <ol class="breadcrumb mb-0 mt-1" style="font-size:.78rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
            <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
        </ol>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('invoices.index') }}"
           class="btn btn-light btn-sm d-flex align-items-center gap-1"
           style="border-radius:2rem;font-size:.82rem;">
            <i class="ri-arrow-left-line"></i>Volver
        </a>

        <button onclick="window.print()" class="btn-print">
            <i class="ri-printer-line"></i>Imprimir
        </button>
        <button type="button" class="btn-print-thermal" onclick="printThermal()">
            <i class="ri-receipt-line"></i>Ticket POS
        </button>

        {{-- ✅ Botón Enviar a DGII --}}
        @if($invoice->ncf_type && $invoice->status !== 'cancelada')
            @if(!$invoice->enviada_dgii || ($invoice->estado_dgii && !in_array($invoice->estado_dgii, ['aceptado'])))
                <form action="{{ route('invoices.enviar-ef2', $invoice) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                            style="border-radius:2rem;font-size:.82rem;background:linear-gradient(135deg,#7066e0,#405189);border:none;"
                            onclick="return confirm('¿Enviar esta factura a la DGII?')">
                        <i class="ri-send-plane-line"></i>
                        {{ $invoice->enviada_dgii ? 'Reenviar a DGII' : 'Enviar a DGII' }}
                    </button>
                </form>
            @endif
        @endif

        @if($invoice->status === 'pendiente')
            <a href="{{ route('invoices.cancel', $invoice) }}"
               class="btn btn-danger btn-sm d-flex align-items-center gap-1"
               style="border-radius:2rem;font-size:.82rem;"
               onclick="return confirm('¿Estás seguro de cancelar esta factura?')">
                <i class="ri-close-circle-line"></i>Cancelar
            </a>
        @endif
    </div>
</div>

{{-- Alertas de sesión --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-check-double-line me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ri-information-line me-1"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ══════════════════════
     INVOICE PAPER
══════════════════════ --}}
<div class="invoice-paper">

    {{-- Header con datos de la empresa --}}
    <div class="inv-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="inv-brand">
                    @if($company['logo'])
                        <img src="{{ asset('storage/' . $company['logo']) }}"
                             alt="{{ $company['name'] }}" height="40" style="filter:brightness(0) invert(1);margin-right:10px;">
                    @else
                        <i class="ri-heart-pulse-line me-2" style="opacity:.8;"></i>
                    @endif
                    {{ $company['name'] }}
                    <span>{{ $company['slogan'] }}</span>
                </div>
                <div style="font-size:.78rem;opacity:.8;margin-top:.3rem;position:relative;z-index:1;">
                    {{ $company['address'] }}
                </div>
                <div style="font-size:.75rem;opacity:.7;margin-top:.1rem;position:relative;z-index:1;">
                    RNC: {{ $company['rnc'] }} | Tel: {{ $company['phone'] }}
                    @if($company['email'])
                        | {{ $company['email'] }}
                    @endif
                </div>
                <div class="mt-2">
                    <span class="status-pill {{ $invoice->status }}">
                        <i class="ri-checkbox-circle-line"></i>
                        {{ ucfirst($invoice->status) }}
                    </span>

                    {{-- ✅ Estado DGII en el header --}}
                    @if($invoice->ncf_type)
                        @php
                            $dgiiClass = $invoice->enviada_dgii
                                ? ($invoice->estado_dgii ?? 'pendiente')
                                : 'sin_enviar';
                            $dgiiLabel = $invoice->enviada_dgii
                                ? ucfirst(str_replace('_', ' ', $invoice->estado_dgii ?? 'enviado'))
                                : 'Sin enviar a DGII';
                            $dgiiIcon = match($dgiiClass) {
                                'aceptado' => 'ri-shield-check-line',
                                'enviado' => 'ri-send-plane-line',
                                'rechazado', 'error' => 'ri-error-warning-line',
                                default => 'ri-time-line',
                            };
                        @endphp
                        <span class="dgii-pill {{ $dgiiClass }}">
                            <i class="{{ $dgiiIcon }}"></i>
                            {{ $dgiiLabel }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-end">
                <div class="inv-num">
                    {{ $invoice->invoice_number }}
                    <small>Factura</small>
                </div>
                <div style="font-size:.78rem;opacity:.75;margin-top:.3rem;position:relative;z-index:1;">
                    {{ $invoice->created_at->format('d/m/Y — g:i A') }}
                </div>
                @if($invoice->encf)
                    <div style="font-size:.8rem;opacity:.9;margin-top:.15rem;position:relative;z-index:1;font-family:monospace;font-weight:700;">
                        e-NCF: {{ $invoice->encf }}
                    </div>
                @elseif($invoice->ncf)
                    <div style="font-size:.75rem;opacity:.8;margin-top:.1rem;position:relative;z-index:1;">
                        NCF: {{ $invoice->ncf }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="inv-body">

        {{-- Info grid --}}
        <div class="info-grid">
            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-user-line me-1"></i>Paciente</div>
                <div class="info-box-val">
                    {{ $invoice->patient->first_name }}
                    {{ $invoice->patient->last_name }}
                </div>
                <div class="info-box-sub">
                    Cédula: {{ $invoice->patient->cedula ?? '—' }}
                    @if($invoice->patient->phone)
                        &nbsp;·&nbsp; {{ $invoice->patient->phone }}
                    @endif
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-user-voice-line me-1"></i>Médico</div>
                <div class="info-box-val">{{ $invoice->doctor->name ?? 'N/A' }}</div>
                <div class="info-box-sub">{{ $invoice->doctor->branch->name ?? '' }}</div>
            </div>

            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-building-2-line me-1"></i>Sucursal / Emitido por</div>
                <div class="info-box-val">{{ $invoice->branch->name }}</div>
                <div class="info-box-sub" style="font-weight:600;color:#344563;">
                    {{ $invoice->user->name }}
                </div>
            </div>

            @if($invoice->insurance)
            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-shield-check-line me-1"></i>Seguro médico</div>
                <div class="info-box-val">{{ $invoice->insurance->name }}</div>
                @if($invoice->authorization_number)
                    <div class="info-box-sub">Autorización: {{ $invoice->authorization_number }}</div>
                @endif
                <div class="info-box-sub" style="color:var(--rt);">
                    Descuento: {{ $company['currency'] }} {{ number_format($invoice->insurance_discount, 2, ',', '.') }}
                </div>
            </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════ --}}
        {{-- ✅ BLOQUE COMPROBANTE ELECTRÓNICO (e-CF)       --}}
        {{-- ══════════════════════════════════════════════ --}}
        @if($invoice->ncf_type)
        <div class="ecf-block">
            <div class="ecf-block-title">
                <i class="ri-shield-check-line"></i>
                Comprobante Electrónico (e-CF)
                @php
                    $ecfStatusClass = $invoice->enviada_dgii
                        ? ($invoice->estado_dgii ?? 'pendiente')
                        : 'sin_enviar';
                    $ecfStatusLabel = $invoice->enviada_dgii
                        ? ($invoice->estado_dgii ?? 'enviado')
                        : 'sin enviar';
                @endphp
                <span class="ecf-status {{ $ecfStatusClass }} ms-auto">
                    <i class="ri-radio-button-line"></i>{{ $ecfStatusLabel }}
                </span>
            </div>

            <div class="ecf-grid">
                <div>
                    <div class="ecf-item-lbl">Tipo de e-CF</div>
                    <div class="ecf-item-val">
                        @switch($invoice->ncf_type)
                            @case('credito_fiscal') e-CF 31 — Crédito Fiscal @break
                            @case('consumidor_final') e-CF 32 — Consumidor Final @break
                            @case('gubernamental') e-CF 45 — Gubernamental @break
                            @case('regimen_especial') e-CF 44 — Régimen Especial @break
                            @default {{ $invoice->ncf_type }}
                        @endswitch
                    </div>
                </div>

                <div>
                    <div class="ecf-item-lbl">e-NCF</div>
                    <div class="ecf-item-val">{{ $invoice->encf ?? '—' }}</div>
                </div>

                @if($invoice->track_id)
                <div>
                    <div class="ecf-item-lbl">Track ID (DGII)</div>
                    <div class="ecf-item-val" style="font-size:.78rem;">{{ $invoice->track_id }}</div>
                </div>
                @endif

                @if($invoice->enviada_dgii_at)
                <div>
                    <div class="ecf-item-lbl">Enviado a DGII</div>
                    <div class="ecf-item-val" style="font-family:sans-serif;font-size:.82rem;">
                        {{ $invoice->enviada_dgii_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                @endif

                @if($invoice->customer_rnc)
                <div>
                    <div class="ecf-item-lbl">RNC / Cédula del cliente</div>
                    <div class="ecf-item-val">{{ $invoice->customer_rnc }}</div>
                </div>
                @endif

                @if($invoice->customer_business_name)
                <div>
                    <div class="ecf-item-lbl">Razón Social</div>
                    <div class="ecf-item-val" style="font-family:sans-serif;">{{ $invoice->customer_business_name }}</div>
                </div>
                @endif
            </div>

            {{-- QR + Links --}}
            @if($invoice->qr_link || $invoice->pdf_cloud_url)
            <div class="ecf-qr">
                @if($invoice->qr_link)
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($invoice->qr_link) }}"
                         alt="QR DGII" class="ecf-qr-img">
                @endif
                <div class="ecf-qr-info">
                    @if($invoice->qr_link)
                        <div style="margin-bottom:.35rem;">
                            <strong><i class="ri-qr-code-line me-1"></i>Consulta en DGII:</strong>
                        </div>
                        <div style="word-break:break-all;font-size:.72rem;">
                            <a href="{{ $invoice->qr_link }}" target="_blank" style="color:var(--rp);">
                                {{ $invoice->qr_link }}
                            </a>
                        </div>
                    @endif
                    @if($invoice->pdf_cloud_url)
                        <div style="margin-top:.5rem;">
                            <a href="{{ $invoice->pdf_cloud_url }}" target="_blank"
                               class="btn btn-sm btn-outline-primary" style="border-radius:2rem;font-size:.75rem;">
                                <i class="ri-file-pdf-line me-1"></i>Ver PDF en la nube
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Si tiene error, mostrar --}}
            @if($invoice->ecfDocuments->where('estado', 'error')->count() > 0)
                @php $lastError = $invoice->ecfDocuments->where('estado', 'error')->sortByDesc('created_at')->first(); @endphp
                <div class="alert alert-danger mt-3 mb-0" style="font-size:.8rem;border-radius:.5rem;">
                    <strong><i class="ri-error-warning-line me-1"></i>Último error:</strong>
                    {{ $lastError->error_message ?? 'Sin detalles' }}
                    @if($lastError->error_code)
                        <span class="badge bg-danger ms-2">Código: {{ $lastError->error_code }}</span>
                    @endif
                </div>
            @endif
        </div>
        @endif

        <hr class="inv-divider">

        {{-- Items --}}
        <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
            Servicios facturados
        </p>
        <div class="table-responsive mb-0">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="text-align:left;">Servicio</th>
                        <th style="text-align:center;width:60px;">Cant.</th>
                        <th style="text-align:right;width:100px;">Precio</th>
                        <th style="text-align:right;width:120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->service->name }}</td>
                        <td style="text-align:center;">{{ $item->quantity }}</td>
                        <td style="text-align:right;">{{ $company['currency'] }} {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;">{{ $company['currency'] }} {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr class="inv-divider">

        {{-- Totals --}}
        <div class="row g-3">
            <div class="col-md-6">
                @if($invoice->status === 'pagada' && $invoice->receipt)
                    <div class="info-box" style="background:rgba(10,179,156,.05);border-color:rgba(10,179,156,.2);">
                        <div class="info-box-lbl" style="color:var(--rt);">
                            <i class="ri-check-double-line me-1"></i>Pago registrado
                        </div>
                        <div class="info-box-val" style="font-family:monospace;color:var(--rt);">
                            {{ $invoice->receipt->receipt_number }}
                        </div>
                        <div class="info-box-sub">
                            Pagado el {{ $invoice->receipt->created_at->format('d/m/Y') }}
                            por {{ $invoice->receipt->user->name }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
                    Resumen
                </p>
                <div class="totals-box">
                    <div class="tot-row">
                        <span class="tot-lbl">Subtotal servicios</span>
                        <span class="tot-val">{{ $company['currency'] }} {{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
                    </div>
                    @if($invoice->tax_amount > 0)
                    <div class="tot-row">
                        <span class="tot-lbl">Impuestos ({{ $company['tax_rate'] }}%)</span>
                        <span class="tot-val">{{ $company['currency'] }} {{ number_format($invoice->tax_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($invoice->insurance_discount > 0)
                    <div class="tot-row">
                        <span class="tot-lbl" style="color:var(--rt);">Descuento seguro</span>
                        <span class="tot-val" style="color:var(--rt);">− {{ $company['currency'] }} {{ number_format($invoice->insurance_discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="tot-row">
                        <span class="tot-lbl">Total factura</span>
                        <span class="tot-val">{{ $company['currency'] }} {{ number_format($invoice->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Footer con datos de la empresa --}}
    <div class="inv-footer">
        <i class="ri-heart-pulse-line me-1"></i>
        {{ $company['name'] }} · {{ $company['footer_text'] }} ·
        Factura generada el {{ $invoice->created_at->format('d/m/Y') }} a las {{ $invoice->created_at->format('g:i A') }}
        <br>
        <small>{{ $company['business_name'] }} · RNC: {{ $company['rnc'] }} · {{ $company['address'] }}</small>
        @if($invoice->customer_business_name)
            <br><small>Cliente: {{ $invoice->customer_business_name }} (RNC: {{ $invoice->customer_rnc }})</small>
        @endif
        @if($invoice->encf)
            <br><small><strong>e-NCF:</strong> {{ $invoice->encf }} · <strong>Track ID:</strong> {{ $invoice->track_id }}</small>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════ --}}
{{-- ✅ HISTORIAL DE ENVÍOS A DGII (ecf_documents) --}}
{{-- ══════════════════════════════════════════════ --}}
@if($invoice->ecfDocuments->count() > 0)
<div class="invoice-paper mt-4" style="max-width:780px;">
    <div class="inv-body">
        <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
            <i class="ri-history-line me-1"></i>Historial de envíos a DGII
        </p>
        <div class="table-responsive">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="text-align:left;">Fecha</th>
                        <th style="text-align:left;">Estado</th>
                        <th style="text-align:left;">e-NCF</th>
                        <th style="text-align:left;">Track ID</th>
                        <th style="text-align:left;">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->ecfDocuments->sortByDesc('created_at') as $doc)
                    <tr>
                        <td style="font-size:.78rem;">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="ecf-status {{ $doc->estado ?? 'sin_enviar' }}">
                                {{ $doc->estado ?? 'pendiente' }}
                            </span>
                        </td>
                        <td style="font-family:monospace;font-size:.78rem;">{{ $doc->encf ?? '—' }}</td>
                        <td style="font-family:monospace;font-size:.72rem;word-break:break-all;">
                            {{ $doc->track_id ? \Illuminate\Support\Str::limit($doc->track_id, 30) : '—' }}
                        </td>
                        <td style="font-size:.78rem;">{{ $doc->user->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif<div style="height:2rem;"></div>
</div>
</div>

<iframe id="thermal-frame" style="position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none;"></iframe>

@push('scripts')
<script>
function printThermal() {
    const frame = document.getElementById('thermal-frame');
    const doc   = frame.contentDocument || frame.contentWindow.document;

    // Datos de la empresa desde PHP
    const company = @json($company);
    const invoice = @json($invoice);
    const qrLink  = @json($invoice->qr_link);

    // Generar URL del QR usando api.qrserver.com
    const qrImgUrl = qrLink
        ? 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrLink)
        : null;

    doc.open();
    doc.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * {
    margin:0;
    padding:0;
    box-sizing:border-box;
    -webkit-font-smoothing: none;
    text-rendering: optimizeSpeed;
  }

  body {
    font-family: monospace;
    font-size: 14px;
    font-weight: 700;
    color: #000;
    background: #fff;
    padding: 6px 8px;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .center { text-align: center; }
  .right  { text-align: right; }

  .bold { font-weight: 900; }
  .big  { font-size: 17px; font-weight: 900; letter-spacing: 1px; }

  .small { font-size: 12px; font-weight: 700; }
  .lbl   { font-size: 12px; font-weight: 700; }

  .sep  { border-top: 1px dashed #000; margin: 6px 0; }
  .sep2 { border-top: 2px solid #000;  margin: 6px 0; }

  table { width: 100%; border-collapse: collapse; }
  td    { padding: 2px 0; vertical-align: top; }
  .td-r { text-align: right; white-space: nowrap; }

  .total-row td {
    font-size: 16px;
    font-weight: 900;
    padding-top: 6px;
    border-top: 2px solid #000;
  }

  .badge-status {
    display: inline-block;
    border: 2px solid #000;
    padding: 1px 5px;
    font-size: 12px;
    font-weight: 900;
  }
  .badge-status.pendiente { border-color: #f7b84d; color: #f7b84d; }
  .badge-status.pagada   { border-color: #0ab39c; color: #0ab39c; }
  .badge-status.cancelada { border-color: #f06548; color: #f06548; }

  .company-logo {
    max-height: 40px;
    margin-bottom: 4px;
  }

  .ecf-box {
    border: 2px solid #000;
    padding: 6px;
    margin: 6px 0;
    text-align: center;
  }

  .qr-img {
    width: 130px;
    height: 130px;
    margin: 4px auto;
    display: block;
  }

  @media print {
    @page { margin: 0; }
    body  { padding: 4px 6px; }
  }
</style>
</head>
<body>

{{-- Header con datos de la empresa --}}
<div class="center">
    @if($company['logo'])
        <img src="{{ public_path('storage/' . $company['logo']) }}"
             alt="{{ $company['name'] }}" class="company-logo">
    @endif
    <div class="bold big">{{ strtoupper($company['name']) }}</div>
    <div class="small">{{ $company['slogan'] }}</div>
    <div class="small">{{ $company['address'] }}</div>
    <div class="small">RNC: {{ $company['rnc'] }} | Tel: {{ $company['phone'] }}</div>
    @if($company['email'])
        <div class="small">{{ $company['email'] }}</div>
    @endif
</div>

<div class="sep2"></div>

{{-- ✅ Bloque e-CF destacado (si existe) --}}
@if($invoice->encf)
<div class="ecf-box">
    <div class="bold" style="font-size:13px;">COMPROBANTE ELECTRÓNICO</div>
    <div style="font-size:12px;margin-top:2px;">
        @switch($invoice->ncf_type)
            @case('credito_fiscal') e-CF 31 — Crédito Fiscal @break
            @case('consumidor_final') e-CF 32 — Consumidor Final @break
            @case('gubernamental') e-CF 45 — Gubernamental @break
            @case('regimen_especial') e-CF 44 — Régimen Especial @break
        @endswitch
    </div>
    <div class="bold" style="font-size:15px;font-family:monospace;margin-top:3px;">
        {{ $invoice->encf }}
    </div>
    @if($invoice->track_id)
    <div class="small" style="margin-top:3px;word-break:break-all;">
        Track ID: {{ $invoice->track_id }}
    </div>
    @endif
   @if($invoice->qr_link)
<img src="${qrImgUrl}" alt="QR DGII" class="qr-img">
<div class="small">Escanee el código QR</div>
@endif
</div>
@endif

<table>
  <tr>
    <td class="lbl">Factura</td>
    <td class="td-r bold">{{ $invoice->invoice_number }}</td>
  </tr>
  <tr>
    <td class="lbl">Fecha</td>
    <td class="td-r">{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
  </tr>
  <tr>
    <td class="lbl">Estado</td>
    <td class="td-r"><span class="badge-status {{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span></td>
  </tr>
  @if(!$invoice->encf && $invoice->ncf)
  <tr>
    <td class="lbl">NCF</td>
    <td class="td-r">{{ $invoice->ncf }}</td>
  </tr>
  @endif
</table>

<div class="sep"></div>

<div class="lbl">PACIENTE</div>
<div class="bold" style="font-size:15px;margin-top:2px;">
  {{ strtoupper($invoice->patient->first_name . ' ' . $invoice->patient->last_name) }}
</div>
<div class="small">Cédula: {{ $invoice->patient->cedula ?? '—' }}</div>
@if($invoice->patient->phone)
<div class="small">Tel: {{ $invoice->patient->phone }}</div>
@endif

<div class="sep"></div>

<div class="lbl">MÉDICO</div>
<div class="bold" style="font-size:15px;margin-top:2px;">
    {{ $invoice->doctor->name ?? 'N/A' }}
</div>
<div class="small">{{ $invoice->doctor->branch->name ?? '' }}</div>

@if($invoice->insurance)
<div class="sep"></div>
<div class="lbl">SEGURO</div>
<div class="bold" style="font-size:15px;margin-top:2px;">
    {{ $invoice->insurance->name }}
</div>
<div class="small">Cobertura: {{ $invoice->insurance->coverage_percentage }}%</div>
@if($invoice->authorization_number)
<div class="small">Autorización: {{ $invoice->authorization_number }}</div>
@endif
@endif

@if($invoice->customer_business_name)
<div class="sep"></div>
<div class="lbl">CLIENTE</div>
<div class="bold" style="font-size:14px;margin-top:2px;">
    {{ strtoupper($invoice->customer_business_name) }}
</div>
@if($invoice->customer_rnc)
<div class="small">RNC: {{ $invoice->customer_rnc }}</div>
@endif
@endif

<div class="sep2"></div>

<div class="lbl" style="margin-bottom:4px;">SERVICIOS</div>

@foreach($invoice->items as $item)
<table style="margin-bottom:6px;">
  <tr>
    <td class="bold">{{ strtoupper($item->service->name) }}</td>
    <td class="td-r bold">{{ $company['currency'] }} {{ number_format($item->subtotal, 2) }}</td>
  </tr>
  <tr>
    <td class="small">
      {{ $item->quantity }} x {{ $company['currency'] }} {{ number_format($item->price, 2) }}
      @if($item->insurance_amount > 0)
      &nbsp; Seg: {{ $company['currency'] }} {{ number_format($item->insurance_amount, 2) }}
      @endif
    </td>
  </tr>
</table>
@endforeach

<div class="sep"></div>

<table>
  <tr>
    <td class="lbl">Subtotal</td>
    <td class="td-r">{{ $company['currency'] }} {{ number_format($invoice->subtotal, 2) }}</td>
  </tr>
  @if($invoice->tax_amount > 0)
  <tr>
    <td class="lbl">Impuestos</td>
    <td class="td-r">{{ $company['currency'] }} {{ number_format($invoice->tax_amount, 2) }}</td>
  </tr>
  @endif
  @if($invoice->insurance_discount > 0)
  <tr>
    <td class="lbl">Desc. seguro</td>
    <td class="td-r">- {{ $company['currency'] }} {{ number_format($invoice->insurance_discount, 2) }}</td>
  </tr>
  @endif
</table>

<div class="sep"></div>

<div class="sep2"></div>

<table>
  <tr class="total-row">
    <td>TOTAL</td>
    <td class="td-r">{{ $company['currency'] }} {{ number_format($invoice->total, 2) }}</td>
  </tr>
</table>

<div class="sep2"></div>

<div class="center small" style="margin-top:6px;">
    {{ $company['footer_text'] }}
</div>
<div class="center small" style="margin-top:2px;font-size:10px;">
    {{ $company['business_name'] }} · RNC: {{ $company['rnc'] }}
</div>
@if($invoice->customer_business_name)
<div class="center small" style="margin-top:2px;font-size:10px;">
    Cliente: {{ $invoice->customer_business_name }} (RNC: {{ $invoice->customer_rnc }})
</div>
@endif

<div style="margin-top:20px;"></div>

</body>
</html>`);
    doc.close();

    setTimeout(() => {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    }, 300);
}
</script>
@endpush

</x-app-layout>