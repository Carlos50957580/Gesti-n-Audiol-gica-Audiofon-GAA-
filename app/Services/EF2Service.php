<?php

namespace App\Services;

use App\Models\EcfSequence;
use App\Models\ProductInvoice;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EF2Service
{
    protected string $baseUrl;
    protected string $token;
    protected string $rncEmisor;

    public function __construct()
    {
        $this->baseUrl = 'https://master.ef2.do/api2';
        $this->token = Setting::get('ef2_token', '');
        $this->rncEmisor = Setting::get('ef2_rnc_empresa', '');
    }

    public function estaConfigurado(): bool
    {
        $activo = Setting::get('ef2_activo', '0') === '1';
        $tieneToken = !empty($this->token);
        $tieneRnc = !empty($this->rncEmisor);

        Log::info('EF2::estaConfigurado - CHECK', [
            'ef2_activo_raw' => Setting::get('ef2_activo'),
            'activo' => $activo,
            'tiene_token' => $tieneToken,
            'tiene_rnc' => $tieneRnc,
            'rnc' => $this->rncEmisor,
            'resultado_final' => $activo && $tieneToken && $tieneRnc,
        ]);

        return $activo && $tieneToken && $tieneRnc;
    }

    public function enviarFacturaProductos(ProductInvoice $invoice): array
    {
        Log::info('═══════════════════════════════════════════');
        Log::info('EF2::enviarFacturaProductos - INICIO', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
        ]);

        if (!$this->estaConfigurado()) {
            return [
                'success' => false,
                'message' => 'La facturación electrónica no está configurada o activa.',
            ];
        }

        $secuencia = $this->seleccionarSecuencia($invoice);

        if (!$secuencia) {
            Log::warning('EF2::enviarFacturaProductos - Sin secuencia disponible', [
                'tipo_ecf_buscado' => $this->mapearTipoNcfAEcf($invoice->ncf_type),
            ]);
            return [
                'success' => false,
                'message' => 'No hay secuencias e-CF disponibles para este tipo de comprobante.',
            ];
        }

        $payload = $this->construirPayload($invoice, $secuencia);

        Log::info('EF2::enviarFacturaProductos - Payload construido', ['payload' => $payload]);

        try {
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post("{$this->baseUrl}/procesar_factura.php", $payload);

            Log::info('EF2::enviarFacturaProductos - Respuesta HTTP', [
                'status_code' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500),
            ]);

            $data = $response->json();

            if (!$data) {
                return [
                    'success' => false,
                    'message' => 'Respuesta inválida de EF2',
                    'raw' => $response->body(),
                ];
            }

            if (!empty($data['success'])) {
                $secuencia->avanzar();
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('EF2::enviarFacturaProductos - EXCEPCIÓN', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con EF2: ' . $e->getMessage(),
            ];
        }
    }

    protected function seleccionarSecuencia(ProductInvoice $invoice): ?EcfSequence
    {
        $tipoEcf = $this->mapearTipoNcfAEcf($invoice->ncf_type);

        $secuencia = EcfSequence::where('tipo_ecf', $tipoEcf)
            ->where('estado', true)
            ->whereColumn('secuencia_actual', '<', 'hasta')
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')
                  ->orWhere('fecha_vencimiento', '>=', now()->toDateString());
            })
            ->orderBy('id')
            ->first();

        return $secuencia;
    }

    protected function mapearTipoNcfAEcf(?string $ncfType): string
    {
        $map = [
            'credito_fiscal'   => '31',
            'consumidor_final' => '32',
            'gubernamental'    => '45',
            'regimen_especial' => '44',
        ];

        return $map[$ncfType] ?? '32';
    }

    protected function construirPayload(ProductInvoice $invoice, EcfSequence $secuencia): array
    {
        $tipoEcf = $this->mapearTipoNcfAEcf($invoice->ncf_type);

        $idDoc = [
            'TipoeCF' => $tipoEcf,
            'eNCF' => $secuencia->siguiente_encf,
            'IndicadorMontoGravado' => '0',
            'TipoIngresos' => '01',
            'TipoPago' => '1',
        ];

        if (!in_array($tipoEcf, ['32', '34'])) {
            $idDoc['FechaVencimientoSecuencia'] = $this->fechaVencimiento($tipoEcf, $secuencia);
        }

        return [
            'ECF' => [
                'Encabezado' => [
                    'Version' => '1.0',
                    'IdDoc' => $idDoc,
                    'Emisor' => $this->construirEmisor(),
                    'Comprador' => $this->construirComprador($invoice),
                    'Totales' => $this->construirTotales($invoice),
                ],
                'DetallesItems' => [
                    'Item' => $this->construirItems($invoice),
                ],
            ],
        ];
    }

    protected function fechaVencimiento(string $tipoEcf, EcfSequence $secuencia): string
    {
        return $secuencia->fecha_vencimiento
            ? $secuencia->fecha_vencimiento->format('d-m-Y')
            : now()->addYear()->format('d-m-Y');
    }

    protected function construirEmisor(): array
    {
        $direccion = trim(Setting::get('company_address') ?? '') ?: 'Santo Domingo, República Dominicana';
        $correo = trim(Setting::get('company_email') ?? '');
        if (empty($correo)) {
            $nombreLimpio = preg_replace('/[^a-z0-9]/', '', strtolower(Setting::get('company_name', 'empresa')));
            $correo = 'info@' . $nombreLimpio . '.com';
        }

        return [
            'RNCEmisor' => str_replace('-', '', Setting::get('ef2_rnc_empresa', '')),
            'RazonSocialEmisor' => Setting::get('company_business_name', ''),
            'NombreComercial' => Setting::get('company_name', ''),
            'DireccionEmisor' => $direccion,
            'Municipio' => '010100',
            'Provincia' => '010000',
            'CorreoEmisor' => $correo,
            'FechaEmision' => now()->format('d-m-Y'),
        ];
    }

    /**
     * ✅ MODIFICADO: Usa la cédula del paciente si no hay RNC del cliente
     */
    protected function construirComprador(ProductInvoice $invoice): array
    {
        // Prioridad 1: RNC del cliente (ingresado manualmente)
        $rnc = $invoice->customer_rnc
            ? preg_replace('/[^0-9]/', '', $invoice->customer_rnc)
            : '';

        // Prioridad 2: Cédula del paciente (automático)
        if (empty($rnc) && $invoice->patient && $invoice->patient->cedula) {
            $rnc = preg_replace('/[^0-9]/', '', $invoice->patient->cedula);
        }

        // Nombre: razón social del cliente o nombre completo del paciente
        $nombre = $invoice->customer_business_name
            ?: trim(($invoice->patient->first_name ?? '') . ' ' . ($invoice->patient->last_name ?? ''));

        if (empty($nombre)) {
            $nombre = 'Consumidor Final';
        }

        $data = [
            'RazonSocialComprador' => $nombre,
        ];

        // Solo enviar RNC si es válido (9 dígitos = RNC, 11 = cédula)
        if (preg_match('/^\d{9}$|^\d{11}$/', $rnc)) {
            $data['RNCComprador'] = $rnc;
        }

        Log::info('EF2::construirComprador', $data);

        return $data;
    }

    protected function construirTotales(ProductInvoice $invoice): array
    {
        return [
            'MontoGravadoTotal' => number_format($invoice->subtotal, 2, '.', ''),
            'MontoGravadoI1' => number_format($invoice->subtotal, 2, '.', ''),
            'ITBIS1' => '18',
            'TotalITBIS' => number_format($invoice->tax_amount, 2, '.', ''),
            'TotalITBIS1' => number_format($invoice->tax_amount, 2, '.', ''),
            'MontoTotal' => number_format($invoice->total, 2, '.', ''),
        ];
    }

    protected function construirItems(ProductInvoice $invoice): array
    {
        $items = [];
        $linea = 1;

        foreach ($invoice->items as $item) {
            $items[] = [
                'NumeroLinea' => (string) $linea,
                'IndicadorFacturacion' => $item->tax_amount > 0 ? '1' : '4',
                'NombreItem' => $item->product->name ?? 'Producto',
                'IndicadorBienoServicio' => '1',
                'CantidadItem' => (string) $item->quantity,
                'UnidadMedida' => '43',
                'PrecioUnitarioItem' => number_format($item->price, 2, '.', ''),
                'MontoItem' => number_format($item->subtotal, 2, '.', ''),
            ];
            $linea++;
        }

        return $items;
    }


        // ═══════════════════════════════════════════════════════════
    // FACTURAS DE SERVICIOS (médicos/clínicos)
    // ═══════════════════════════════════════════════════════════

    public function enviarFacturaServicios(\App\Models\Invoice $invoice): array
    {
        Log::info('═══════════════════════════════════════════');
        Log::info('EF2::enviarFacturaServicios - INICIO', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number ?? $invoice->id,
        ]);

        if (!$this->estaConfigurado()) {
            return [
                'success' => false,
                'message' => 'La facturación electrónica no está configurada o activa.',
            ];
        }

        $secuencia = $this->seleccionarSecuenciaServicio($invoice);

        if (!$secuencia) {
            Log::warning('EF2::enviarFacturaServicios - Sin secuencia disponible', [
                'tipo_ecf_buscado' => $this->mapearTipoNcfAEcf($invoice->ncf_type),
            ]);
            return [
                'success' => false,
                'message' => 'No hay secuencias e-CF disponibles para este tipo de comprobante.',
            ];
        }

        $payload = $this->construirPayloadServicio($invoice, $secuencia);

        Log::info('EF2::enviarFacturaServicios - Payload construido', ['payload' => $payload]);

        try {
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post("{$this->baseUrl}/procesar_factura.php", $payload);

            Log::info('EF2::enviarFacturaServicios - Respuesta HTTP', [
                'status_code' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500),
            ]);

            $data = $response->json();

            if (!$data) {
                return [
                    'success' => false,
                    'message' => 'Respuesta inválida de EF2',
                    'raw' => $response->body(),
                ];
            }

            if (!empty($data['success'])) {
                $secuencia->avanzar();
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('EF2::enviarFacturaServicios - EXCEPCIÓN', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con EF2: ' . $e->getMessage(),
            ];
        }
    }

    protected function seleccionarSecuenciaServicio(\App\Models\Invoice $invoice): ?EcfSequence
    {
        $tipoEcf = $this->mapearTipoNcfAEcf($invoice->ncf_type);

        // ✅ Priorizar secuencia de la misma sucursal, luego global
        $secuencia = EcfSequence::where('tipo_ecf', $tipoEcf)
            ->where('estado', true)
            ->whereColumn('secuencia_actual', '<', 'hasta')
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')
                  ->orWhere('fecha_vencimiento', '>=', now()->toDateString());
            })
            ->where(function ($q) use ($invoice) {
                $q->where('branch_id', $invoice->branch_id)
                  ->orWhereNull('branch_id');
            })
            ->orderByRaw('CASE WHEN branch_id = ? THEN 0 ELSE 1 END', [$invoice->branch_id])
            ->orderBy('id')
            ->first();

        return $secuencia;
    }

    protected function construirPayloadServicio(\App\Models\Invoice $invoice, EcfSequence $secuencia): array
    {
        $tipoEcf = $this->mapearTipoNcfAEcf($invoice->ncf_type);

        $idDoc = [
            'TipoeCF' => $tipoEcf,
            'eNCF' => $secuencia->siguiente_encf,
            'IndicadorMontoGravado' => '0',
            'TipoIngresos' => '01',
            'TipoPago' => '1',
        ];

        if (!in_array($tipoEcf, ['32', '34'])) {
            $idDoc['FechaVencimientoSecuencia'] = $this->fechaVencimiento($tipoEcf, $secuencia);
        }

        return [
            'ECF' => [
                'Encabezado' => [
                    'Version' => '1.0',
                    'IdDoc' => $idDoc,
                    'Emisor' => $this->construirEmisor(),
                    'Comprador' => $this->construirCompradorServicio($invoice),
                    'Totales' => $this->construirTotalesServicio($invoice),
                ],
                'DetallesItems' => [
                    'Item' => $this->construirItemsServicio($invoice),
                ],
            ],
        ];
    }

    protected function construirCompradorServicio(\App\Models\Invoice $invoice): array
    {
        $rnc = $invoice->customer_rnc
            ? preg_replace('/[^0-9]/', '', $invoice->customer_rnc)
            : '';

        if (empty($rnc) && $invoice->patient && $invoice->patient->cedula) {
            $rnc = preg_replace('/[^0-9]/', '', $invoice->patient->cedula);
        }

        $nombre = $invoice->customer_business_name
            ?: trim(($invoice->patient->first_name ?? '') . ' ' . ($invoice->patient->last_name ?? ''));

        if (empty($nombre)) {
            $nombre = 'Consumidor Final';
        }

        $data = ['RazonSocialComprador' => $nombre];

        if (preg_match('/^\d{9}$|^\d{11}$/', $rnc)) {
            $data['RNCComprador'] = $rnc;
        }

        Log::info('EF2::construirCompradorServicio', $data);

        return $data;
    }

        protected function construirTotalesServicio(\App\Models\Invoice $invoice): array
    {
        // ✅ e-CF al paciente por el monto que PAGA EL PACIENTE (sin seguro)
        $montoTotalPaciente = $invoice->total;

        // Base gravable = subtotal del paciente (subtotal - descuento seguro proporcional)
        // Como el descuento del seguro no genera ITBIS adicional, calculamos:
        // - Base gravable: subtotal del paciente
        // - ITBIS: solo sobre la parte del paciente
        $subtotalPaciente = $invoice->subtotal - $invoice->insurance_discount;
        
        // ✅ ITBIS proporcional al paciente (si hay impuestos)
        // Si subtotal total era 500 y seguro cubre 200 (40%), el ITBIS del paciente es 60% del ITBIS total
        $factorPaciente = $invoice->subtotal > 0 
            ? ($subtotalPaciente / $invoice->subtotal) 
            : 1;
        $itbisPaciente = $invoice->tax_amount * $factorPaciente;

        // Protección: evitar negativos
        if ($subtotalPaciente < 0) $subtotalPaciente = 0;
        if ($itbisPaciente < 0) $itbisPaciente = 0;
        if ($montoTotalPaciente < 0) $montoTotalPaciente = 0;

        return [
            'MontoGravadoTotal' => number_format($subtotalPaciente, 2, '.', ''),
            'MontoGravadoI1' => number_format($subtotalPaciente, 2, '.', ''),
            'ITBIS1' => '18',
            'TotalITBIS' => number_format($itbisPaciente, 2, '.', ''),
            'TotalITBIS1' => number_format($itbisPaciente, 2, '.', ''),
            'MontoTotal' => number_format($montoTotalPaciente, 2, '.', ''),
        ];
    }

    protected function construirItemsServicio(\App\Models\Invoice $invoice): array
    {
        $items = [];
        $linea = 1;

        foreach ($invoice->items as $item) {
            // ✅ Precio unitario = lo que paga el paciente por unidad
            // Si el item tiene seguro, el precio efectivo es patient_amount / quantity
            $precioPaciente = $item->quantity > 0
                ? ($item->patient_amount / $item->quantity)
                : $item->price;
            
            // ✅ Monto del item = lo que paga el paciente (sin seguro)
            $montoPaciente = $item->patient_amount;

            // IndicadorFacturacion: 1 = con ITBIS, 4 = exento
            // Solo ponemos 1 si el item genera ITBIS Y el paciente paga ITBIS
            $factorPaciente = $item->subtotal > 0
                ? ($item->patient_amount / $item->subtotal)
                : 1;
            $itbisItemPaciente = $item->tax_amount * $factorPaciente;
            $indicador = $itbisItemPaciente > 0 ? '1' : '4';

            $items[] = [
                'NumeroLinea' => (string) $linea,
                'IndicadorFacturacion' => $indicador,
                'NombreItem' => $item->service->name ?? 'Servicio',
                'IndicadorBienoServicio' => '2', // ✅ 2 = Servicio
                'CantidadItem' => (string) $item->quantity,
                'UnidadMedida' => '43',
                'PrecioUnitarioItem' => number_format($precioPaciente, 2, '.', ''),
                'MontoItem' => number_format($montoPaciente, 2, '.', ''),
            ];
            $linea++;
        }

        return $items;
    }
}