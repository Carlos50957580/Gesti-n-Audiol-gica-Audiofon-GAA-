<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Service;
use App\Models\Insurance;
use App\Models\EcfSequence;
use App\Models\DoctorFee;
use App\Models\DoctorFeeSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    protected EF2Service $ef2Service;

    public function __construct(EF2Service $ef2Service)
    {
        $this->ef2Service = $ef2Service;
    }

    /**
     * Crear factura de servicios
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // ── FORZAR CONSUMIDOR FINAL SI NO SE MARCA CRÉDITO FISCAL ──
            if (empty($data['with_ncf'])) {
                $data['with_ncf'] = true;
                $data['ncf_type'] = 'consumidor_final';
                Log::info('InvoiceService::createInvoice - Forzando consumidor_final', [
                    'branch_id' => $data['branch_id'] ?? null,
                ]);
            }

            // ── CONSUMIR SECUENCIA e-CF ─────────────────────────────
            $ncfCode = null;      // Se mantiene por compatibilidad (B02/B01)
            $ncfSequenceId = null;
            $ecfSequenceId = null;

            if (!empty($data['ncf_type'])) {
                $tipoEcf = $this->mapearTipoNcfAEcf($data['ncf_type']);

                $ecfSequence = EcfSequence::where('tipo_ecf', $tipoEcf)
                    ->where('estado', true)
                    ->whereColumn('secuencia_actual', '<', 'hasta')
                    ->where(function ($q) {
                        $q->whereNull('fecha_vencimiento')
                          ->orWhere('fecha_vencimiento', '>=', now()->toDateString());
                    })
                    ->where(function ($q) use ($data) {
                        $q->where('branch_id', $data['branch_id'])
                          ->orWhereNull('branch_id');
                    })
                    ->orderByRaw('CASE WHEN branch_id = ? THEN 0 ELSE 1 END', [$data['branch_id']])
                    ->orderBy('id')
                    ->first();

                if (!$ecfSequence) {
                    throw new \Exception('No hay secuencias e-CF disponibles para este tipo de comprobante.');
                }

                $ecfSequenceId = $ecfSequence->id;
            }

            // ── PROCESAR ITEMS ──────────────────────────────────────
            $subtotal = 0;
            $totalTax = 0;
            $insuranceDiscount = 0;
            $items = [];

            $insurance = !empty($data['insurance_id'])
                ? Insurance::find($data['insurance_id'])
                : null;

            foreach ($data['services'] as $serviceData) {
                $service = Service::with(['taxes', 'insuranceCoverage'])->find($serviceData['id']);
                $quantity = (int) $serviceData['quantity'];
                $price = (float) ($serviceData['custom_price'] ?? $service->price);
                $subtotalItem = $price * $quantity;

                $taxCalculation = $service->calculateTaxes($subtotalItem);
                $taxAmount = $taxCalculation['total_tax'];
                $totalTax += $taxAmount;

                $coveragePercentage = 0;
                $insuranceAmount = 0;
                $patientAmount = $subtotalItem;

                if ($insurance) {
                    $specificCoverage = $service->getCoverageForInsurance($insurance);

                    if ($specificCoverage) {
                        $calculation = $specificCoverage->calculateCoverage($subtotalItem);
                        $coveragePercentage = $calculation['percentage'];
                        $insuranceAmount = $calculation['insurance_amount'];
                        $patientAmount = $subtotalItem - $insuranceAmount;
                    } else {
                        $globalCoverage = $insurance->coverage_percentage;
                        if ($globalCoverage > 0) {
                            $coveragePercentage = $globalCoverage;
                            $insuranceAmount = $subtotalItem * ($globalCoverage / 100);
                            $patientAmount = $subtotalItem - $insuranceAmount;
                        }
                    }

                    $insuranceDiscount += $insuranceAmount;
                }

                $items[] = [
                    'service_id' => $service->id,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotalItem,
                    'coverage_percentage' => $coveragePercentage,
                    'insurance_amount' => $insuranceAmount,
                    'patient_amount' => $patientAmount,
                    'tax_amount' => $taxAmount,
                    'tax_details' => $taxCalculation['taxes'],
                    'total_with_tax' => $patientAmount + $taxAmount,
                ];

                $subtotal += $subtotalItem;
            }

            $totalWithTax = $subtotal + $totalTax;
            $total = $totalWithTax - $insuranceDiscount;

            // ── CREAR FACTURA ──────────────────────────────────────
            $invoice = Invoice::create([
                'patient_id' => $data['patient_id'],
                'user_id' => auth()->id(),
                'doctor_id' => $data['doctor_id'],
                'branch_id' => $data['branch_id'],
                'insurance_id' => $data['insurance_id'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'total_with_tax' => $totalWithTax,
                'insurance_discount' => $insuranceDiscount,
                'total' => $total,
                'status' => 'pendiente',
                'authorization_number' => $data['authorization_number'] ?? null,
                'with_ncf' => true,
                'ncf' => $ncfCode,
                'ncf_type' => $data['ncf_type'],
                'ncf_sequence_id' => $ncfSequenceId,
                'ecf_sequence_id' => $ecfSequenceId,
                'customer_rnc' => $data['customer_rnc'] ?? null,
                'customer_business_name' => $data['customer_business_name'] ?? null,
                'tax_details' => [
                    'total_tax' => $totalTax,
                    'items' => collect($items)->map(fn($item) => $item['tax_details'])->flatten(1)->toArray(),
                ],
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            // ✅ NUEVO: Crear honorarios del médico automáticamente
            $this->createDoctorFee($invoice->fresh(['items.service']));

            // ✅ NUEVO: Enviar a EF2 (no rompe la venta si falla)
            $this->trySendToEf2($invoice->fresh(['items.service', 'patient', 'branch']));

            return $invoice->fresh(['items', 'ecfDocuments', 'doctorFees']);
        });
    }

    /**
     * ✅ NUEVO: Crear DoctorFee automático
     */
    protected function createDoctorFee(Invoice $invoice): void
    {
        if (!$invoice->doctor_id) {
            Log::info('InvoiceService::createDoctorFee - Sin doctor_id, se omite');
            return;
        }

        // Evitar duplicados
        if (DoctorFee::where('invoice_id', $invoice->id)->exists()) {
            Log::info('InvoiceService::createDoctorFee - Ya existe honorario para esta factura');
            return;
        }

        // ✅ Estrategia: buscar config para cada item y sumar honorarios
        // Si un item no tiene config, se omite (no aporta al fee)
        // La base de cálculo es el TOTAL COMPLETO de la factura (con seguro)
        $totalFee = 0;
        $calculationType = null;
        $calculationValue = null;
        $settingsUsados = [];

        foreach ($invoice->items as $item) {
            $setting = DoctorFeeSetting::getForDoctorAndService(
                $invoice->doctor_id,
                $item->service_id
            );

            if (!$setting) {
                continue;
            }

            // Calcular honorario sobre el subtotal del item (sin impuestos)
            // ⚠️ Si prefieres sobre total completo, cambia $item->subtotal por $item->subtotal + $item->tax_amount
            $itemBase = $item->subtotal;
            $itemFee = $setting->calculateFee($itemBase);
            $totalFee += $itemFee;

            // Guardar el primer tipo/valor para el registro (representativo)
            if ($calculationType === null) {
                $calculationType = $setting->calculation_type;
                $calculationValue = $setting->value;
            }

            $settingsUsados[] = [
                'setting_id' => $setting->id,
                'service_id' => $item->service_id,
                'base' => $itemBase,
                'fee' => round($itemFee, 2),
            ];
        }

        if ($totalFee <= 0) {
            Log::info('InvoiceService::createDoctorFee - Sin config activa para este médico', [
                'doctor_id' => $invoice->doctor_id,
            ]);
            return;
        }

        DoctorFee::create([
            'doctor_id' => $invoice->doctor_id,
            'invoice_id' => $invoice->id,
            'invoice_total' => $invoice->total_with_tax,
            'calculation_type' => $calculationType ?? 'percentage',
            'calculation_value' => $calculationValue ?? 0,
            'fee_amount' => round($totalFee, 2),
            'status' => 'pending',
            'notes' => json_encode([
                'config_usadas' => $settingsUsados,
                'base_calculo' => 'subtotal_por_item',
            ]),
        ]);

        Log::info('InvoiceService::createDoctorFee - Honorario creado', [
            'invoice_id' => $invoice->id,
            'doctor_id' => $invoice->doctor_id,
            'fee_amount' => round($totalFee, 2),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // FACTURACIÓN ELECTRÓNICA (EF2)
    // ═══════════════════════════════════════════════════════════

    protected function trySendToEf2(Invoice $invoice): void
    {
        Log::info('═══════════════════════════════════════════');
        Log::info('InvoiceService::trySendToEf2 - INICIO', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
        ]);

        if (!$this->ef2Service->estaConfigurado()) {
            Log::warning('InvoiceService::trySendToEf2 - ABORT: EF2 no configurado');
            return;
        }

        if (!$invoice->ncf_type) {
            Log::warning('InvoiceService::trySendToEf2 - ABORT: Sin ncf_type');
            return;
        }

        try {
            $result = $this->ef2Service->enviarFacturaServicios($invoice);
            $this->guardarRespuestaEf2($invoice, $result);
        } catch (\Exception $e) {
            Log::error('InvoiceService::trySendToEf2 - EXCEPCIÓN', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            try {
                $invoice->ecfDocuments()->create([
                    'tipo_ecf' => $this->mapearTipoEcf($invoice),
                    'estado' => 'error',
                    'error_message' => $e->getMessage(),
                    'user_id' => auth()->id(),
                    'intentos' => 1,
                    'enviado_at' => now(),
                ]);
            } catch (\Exception $inner) {
                Log::error('InvoiceService::trySendToEf2 - Error guardando ecf_document', [
                    'error' => $inner->getMessage(),
                ]);
            }
        }
    }

    public function guardarRespuestaEf2(Invoice $invoice, array $result): void
    {
        $exitoso = !empty($result['success']);

        $invoice->ecfDocuments()->create([
            'tipo_ecf' => $this->mapearTipoEcf($invoice),
            'encf' => $result['ncf'] ?? null,
            'track_id' => $result['track_id'] ?? null,
            'estado' => $result['estado'] ?? ($exitoso ? 'enviado' : 'error'),
            'qr_link' => $result['qr_link'] ?? null,
            'pdf_cloud_url' => $result['pdf_cloud_url'] ?? null,
            'payload_enviado' => $result['payload'] ?? null,
            'respuesta_completa' => $result,
            'error_code' => $result['code'] ?? null,
            'error_message' => $exitoso ? null : ($result['message'] ?? 'Error desconocido'),
            'ecf_sequence_id' => $invoice->ecf_sequence_id,
            'user_id' => auth()->id(),
            'intentos' => 1,
            'enviado_at' => now(),
            'respondido_at' => now(),
        ]);

        if ($exitoso) {
            $invoice->update([
                'encf' => $result['ncf'] ?? null,
                'track_id' => $result['track_id'] ?? null,
                'estado_dgii' => $result['estado'] ?? 'enviado',
                'qr_link' => $result['qr_link'] ?? null,
                'pdf_cloud_url' => $result['pdf_cloud_url'] ?? null,
                'enviada_dgii' => true,
                'enviada_dgii_at' => now(),
            ]);
        }
    }

    protected function mapearTipoEcf(Invoice $invoice): string
    {
        return match ($invoice->ncf_type) {
            'credito_fiscal' => '31',
            'consumidor_final' => '32',
            'gubernamental' => '45',
            'regimen_especial' => '44',
            default => '32',
        };
    }

    protected function mapearTipoNcfAEcf(?string $ncfType): string
    {
        $map = [
            'credito_fiscal' => '31',
            'consumidor_final' => '32',
            'gubernamental' => '45',
            'regimen_especial' => '44',
        ];
        return $map[$ncfType] ?? '32';
    }

        // Añadir al final de InvoiceService
    public function enviarFacturaServiciosPublic(Invoice $invoice): array
    {
        $result = $this->ef2Service->enviarFacturaServicios($invoice);
        $this->guardarRespuestaEf2($invoice, $result);
        return $result;
    }
}