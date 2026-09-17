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

    // ==========================================================
    // Validaciones previas
    // ==========================================================

    public function estaConfigurado(): bool
    {
        $activo = Setting::get('ef2_activo', '0') === '1';
        $tieneToken = !empty($this->token);
        $tieneRnc = !empty($this->rncEmisor);

        Log::info('EF2::estaConfigurado - CHECK', [
            'ef2_activo_raw' => Setting::get('ef2_activo'),
            'activo' => $activo,
            'tiene_token' => $tieneToken,
            'token_preview' => $tieneToken ? substr($this->token, 0, 15) . '...' : 'VACÍO',
            'tiene_rnc' => $tieneRnc,
            'rnc' => $this->rncEmisor,
            'resultado_final' => $activo && $tieneToken && $tieneRnc,
        ]);

        return $activo && $tieneToken && $tieneRnc;
    }

    // ==========================================================
    // Envío de facturas
    // ==========================================================

    public function enviarFacturaProductos(ProductInvoice $invoice): array
    {
        Log::info('═══════════════════════════════════════════');
        Log::info('EF2::enviarFacturaProductos - INICIO', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
        ]);

        // ── Validar configuración ──
        if (!$this->estaConfigurado()) {
            Log::warning('EF2::enviarFacturaProductos - FALLO: No está configurado');
            return [
                'success' => false,
                'message' => 'La facturación electrónica no está configurada o activa.',
            ];
        }

        // ── Validar secuencia ──
        Log::info('EF2::enviarFacturaProductos - Buscando secuencia', [
            'ncf_type_factura' => $invoice->ncf_type,
            'tipo_ecf_mapeado' => $this->mapearTipoNcfAEcf($invoice->ncf_type),
        ]);

        $secuencia = $this->seleccionarSecuencia($invoice);

        if (!$secuencia) {
            Log::warning('EF2::enviarFacturaProductos - FALLO: No hay secuencia disponible', [
                'tipo_ecf_buscado' => $this->mapearTipoNcfAEcf($invoice->ncf_type),
            ]);
            return [
                'success' => false,
                'message' => 'No hay secuencias e-CF disponibles para este tipo de comprobante.',
            ];
        }

        Log::info('EF2::enviarFacturaProductos - Secuencia encontrada', [
            'sequence_id' => $secuencia->id,
            'prefijo' => $secuencia->prefijo,
            'secuencia_actual' => $secuencia->secuencia_actual,
            'hasta' => $secuencia->hasta,
            'siguiente_encf' => $secuencia->siguiente_encf,
        ]);

        // ── Construir payload ──
        $payload = $this->construirPayload($invoice, $secuencia);

        Log::info('EF2::enviarFacturaProductos - Payload construido', [
            'payload' => $payload,
        ]);

        // ── Enviar a EF2 ──
        try {
            Log::info('EF2::enviarFacturaProductos - Enviando request HTTP', [
                'url' => "{$this->baseUrl}/procesar_factura.php",
                'token_preview' => substr($this->token, 0, 20) . '...',
            ]);

            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post("{$this->baseUrl}/procesar_factura.php", $payload);

            Log::info('EF2::enviarFacturaProductos - Respuesta HTTP recibida', [
                'status_code' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500),
            ]);

            $data = $response->json();

            if (!$data) {
                Log::error('EF2::enviarFacturaProductos - Respuesta no es JSON válido', [
                    'raw_body' => $response->body(),
                ]);
                return [
                    'success' => false,
                    'message' => 'Respuesta inválida de EF2',
                    'raw' => $response->body(),
                ];
            }

            // Avanzar secuencia si fue exitoso
            if (!empty($data['success'])) {
                Log::info('EF2::enviarFacturaProductos - ÉXITO, avanzando secuencia');
                $secuencia->avanzar();
            } else {
                Log::warning('EF2::enviarFacturaProductos - EF2 rechazó la factura', [
                    'response' => $data,
                ]);
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('EF2::enviarFacturaProductos - EXCEPCIÓN', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error de conexión con EF2: ' . $e->getMessage(),
            ];
        }
    }

    // ==========================================================
    // Selección de secuencia
    // ==========================================================

    protected function seleccionarSecuencia(ProductInvoice $invoice): ?EcfSequence
    {
        $tipoEcf = $this->mapearTipoNcfAEcf($invoice->ncf_type);

        // Log de todas las secuencias existentes
        $todasLasSecuencias = EcfSequence::all(['id', 'tipo_ecf', 'estado', 'secuencia_actual', 'hasta', 'fecha_vencimiento'])->toArray();

        Log::info('EF2::seleccionarSecuencia - Secuencias en DB', [
            'total' => count($todasLasSecuencias),
            'secuencias' => $todasLasSecuencias,
            'buscando_tipo' => $tipoEcf,
        ]);

        $secuencia = EcfSequence::where('tipo_ecf', $tipoEcf)
            ->where('estado', true)
            ->whereColumn('secuencia_actual', '<', 'hasta')
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')
                  ->orWhere('fecha_vencimiento', '>=', now()->toDateString());
            })
            ->orderBy('id')
            ->first();

        if (!$secuencia) {
            // Log específico de por qué no se encontró
            $porTipo = EcfSequence::where('tipo_ecf', $tipoEcf)->get()->toArray();
            Log::warning('EF2::seleccionarSecuencia - Sin resultados', [
                'tipo_ecf_buscado' => $tipoEcf,
                'secuencias_de_este_tipo' => $porTipo,
            ]);
        }

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

        $resultado = $map[$ncfType] ?? '32';

        Log::info('EF2::mapearTipoNcfAEcf', [
            'ncf_type' => $ncfType,
            'tipo_ecf' => $resultado,
        ]);

        return $resultado;
    }

    // ==========================================================
    // Construcción del payload
    // ==========================================================

    protected function construirPayload(ProductInvoice $invoice, EcfSequence $secuencia): array
    {
        $tipoEcf = $this->mapearTipoNcfAEcf($invoice->ncf_type);

        return [
            'ECF' => [
                'Encabezado' => [
                    'Version' => '1.0',
                    'IdDoc' => [
                        'TipoeCF' => $tipoEcf,
                        'eNCF' => $secuencia->siguiente_encf,
                        'FechaVencimientoSecuencia' => $this->fechaVencimiento($tipoEcf, $secuencia),
                        'IndicadorMontoGravado' => '0',
                        'TipoIngresos' => '01',
                        'TipoPago' => '1',
                    ],
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
        if ($tipoEcf === '32') {
            return now()->addYears(100)->format('d-m-Y');
        }

        return $secuencia->fecha_vencimiento
            ? $secuencia->fecha_vencimiento->format('d-m-Y')
            : now()->addYear()->format('d-m-Y');
    }

    protected function construirEmisor(): array
    {
        $emisor = [
            'RNCEmisor' => str_replace('-', '', Setting::get('ef2_rnc_empresa', '')),
            'RazonSocialEmisor' => Setting::get('company_business_name', ''),
            'NombreComercial' => Setting::get('company_name', ''),
            'DireccionEmisor' => Setting::get('company_address', ''),
            'Municipio' => '010100',
            'Provincia' => '010000',
            'CorreoEmisor' => Setting::get('company_email', ''),
            'FechaEmision' => now()->format('d-m-Y'),
        ];

        Log::info('EF2::construirEmisor', $emisor);

        return $emisor;
    }

    protected function construirComprador(ProductInvoice $invoice): array
    {
        $rnc = $invoice->customer_rnc
            ? preg_replace('/[^0-9]/', '', $invoice->customer_rnc)
            : '';

        $data = [
            'RazonSocialComprador' => $invoice->customer_business_name
                ?? ($invoice->patient->first_name . ' ' . $invoice->patient->last_name),
        ];

        if (preg_match('/^\d{9}$|^\d{11}$/', $rnc)) {
            $data['RNCComprador'] = $rnc;
        }

        Log::info('EF2::construirComprador', $data);

        return $data;
    }

    protected function construirTotales(ProductInvoice $invoice): array
    {
        $totales = [
            'MontoGravadoTotal' => number_format($invoice->subtotal, 2, '.', ''),
            'MontoGravadoI1' => number_format($invoice->subtotal, 2, '.', ''),
            'ITBIS1' => '18',
            'TotalITBIS' => number_format($invoice->tax_amount, 2, '.', ''),
            'TotalITBIS1' => number_format($invoice->tax_amount, 2, '.', ''),
            'MontoTotal' => number_format($invoice->total, 2, '.', ''),
        ];

        Log::info('EF2::construirTotales', $totales);

        return $totales;
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

        Log::info('EF2::construirItems', ['total_items' => count($items), 'items' => $items]);

        return $items;
    }
}