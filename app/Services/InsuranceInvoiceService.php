<?php

namespace App\Services;

use App\Models\InsuranceInvoice;
use App\Models\NcfType;
use App\Models\NcfSequence;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InsuranceInvoiceService
{
    protected EF2Service $ef2Service;

    public function __construct(EF2Service $ef2Service)
    {
        $this->ef2Service = $ef2Service;
    }

    public function createInvoice(array $data): InsuranceInvoice
    {
        return DB::transaction(function () use ($data) {

            // NCF / tipo de comprobante
            $ncfCode = null;
            $ncfSequenceId = null;

            $tipoNcf = $data['ncf_type'] ?? 'credito_fiscal';

            $typeCodeMap = [
                'credito_fiscal'   => 'B01',
                'consumidor_final' => 'B02',
                'gubernamental'    => 'B15',
                'regimen_especial' => 'B14',
            ];

            $typeCode = $typeCodeMap[$tipoNcf] ?? 'B01';

            $ncfType = NcfType::where('code', $typeCode)->first();

            if ($ncfType) {
                $sequence = NcfSequence::active()
                    ->where('ncf_type_id', $ncfType->id)
                    ->where(function ($q) use ($data) {
                        $q->where('branch_id', $data['branch_id'])
                          ->orWhereNull('branch_id');
                    })
                    ->where('valid_until', '>=', now()->toDateString())
                    ->where('valid_from', '<=', now()->toDateString())
                    ->whereRaw('CAST(current_number AS UNSIGNED) <= CAST(end_number AS UNSIGNED)')
                    ->orderByRaw('CASE WHEN branch_id = ? THEN 0 ELSE 1 END', [$data['branch_id']])
                    ->orderBy('valid_until')
                    ->first();

                if ($sequence && $sequence->canIssue()) {
                    $ncfCode = $sequence->issueNext();
                    $ncfSequenceId = $sequence->id;
                }
            }

            // Cálculos
            $subtotal = (float) $data['amount'];
            $withTax = !empty($data['with_tax']);
            $taxRate = (float) (Setting::get('company_tax_rate', 18) ?? 18);
            $taxAmount = $withTax ? round($subtotal * ($taxRate / 100), 2) : 0;
            $total = $subtotal + $taxAmount;

            // Crear factura
            $invoice = InsuranceInvoice::create([
                'number'                 => InsuranceInvoice::generateNumber(),
                'invoice_date'           => $data['invoice_date'],
                'insurance_id'           => $data['insurance_id'],
                'branch_id'              => $data['branch_id'],
                'user_id'                => auth()->id(),
                'invoice_id'             => $data['invoice_id'] ?? null,
                'subtotal'               => $subtotal,
                'tax_amount'             => $taxAmount,
                'total'                  => $total,
                'with_tax'               => $withTax,
                'concept'                => $data['concept'] ?? null,
                'status'                 => 'pendiente',
                'paid_amount'            => 0,
                'balance'                => $total,
                'with_ncf'               => true,
                'ncf'                    => $ncfCode,
                'ncf_type'               => $tipoNcf,
                'ncf_sequence_id'        => $ncfSequenceId,
                'customer_rnc'           => $data['customer_rnc'] ?? null,
                'customer_business_name' => $data['customer_business_name'] ?? null,
                'notes'                  => $data['notes'] ?? null,
            ]);

            // Enviar a EF2
            $this->trySendToEf2($invoice->fresh(['insurance', 'branch']));

            return $invoice->fresh(['ecfDocuments']);
        });
    }

    protected function trySendToEf2(InsuranceInvoice $invoice): void
    {
        Log::info('InsuranceInvoiceService::trySendToEf2 - INICIO', [
            'invoice_id' => $invoice->id,
        ]);

        if (!$this->ef2Service->estaConfigurado()) {
            Log::warning('InsuranceInvoiceService::trySendToEf2 - EF2 no configurado');
            return;
        }

        try {
            $result = $this->ef2Service->enviarFacturaSeguro($invoice);
            $this->guardarRespuestaEf2($invoice, $result);
        } catch (\Exception $e) {
            Log::error('InsuranceInvoiceService::trySendToEf2 - EXCEPCIÓN', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function guardarRespuestaEf2(InsuranceInvoice $invoice, array $result): void
    {
        $exitoso = !empty($result['success']);

        // ✅ ID de secuencia devuelto por EF2Service
        $ecfSequenceId = $result['ecf_sequence_id'] ?? $invoice->ecf_sequence_id;

        $invoice->ecfDocuments()->create([
            'tipo_ecf'           => $this->mapearTipoEcf($invoice),
            'encf'               => $result['ncf'] ?? null,
            'track_id'           => $result['track_id'] ?? null,
            'estado'             => $result['estado'] ?? ($exitoso ? 'enviado' : 'error'),
            'qr_link'            => $result['qr_link'] ?? null,
            'pdf_cloud_url'      => $result['pdf_cloud_url'] ?? null,
            'payload_enviado'    => $result['payload'] ?? null,
            'respuesta_completa' => $result,
            'error_code'         => $result['code'] ?? null,
            'error_message'      => $exitoso ? null : ($result['message'] ?? 'Error desconocido'),
            'ecf_sequence_id'    => $ecfSequenceId,
            'user_id'            => auth()->id(),
            'intentos'           => 1,
            'enviado_at'         => now(),
            'respondido_at'      => now(),
        ]);

        if ($exitoso) {
            $invoice->update([
                'encf'             => $result['ncf'] ?? null,
                'track_id'         => $result['track_id'] ?? null,
                'estado_dgii'      => $result['estado'] ?? 'enviado',
                'qr_link'          => $result['qr_link'] ?? null,
                'pdf_cloud_url'    => $result['pdf_cloud_url'] ?? null,
                'ecf_sequence_id'  => $ecfSequenceId,  // ✅ AHORA SÍ SE GUARDA
                'enviada_dgii'     => true,
                'enviada_dgii_at'  => now(),
            ]);
        }
    }

    protected function mapearTipoEcf(InsuranceInvoice $invoice): string
    {
        return match ($invoice->ncf_type) {
            'credito_fiscal'   => '31',
            'consumidor_final' => '32',
            'gubernamental'    => '45',
            'regimen_especial' => '44',
            default            => '31',
        };
    }

    public function cancelarFactura(InsuranceInvoice $invoice, ?string $motivo = null): array
{
    $motivo = trim((string) $motivo) ?: 'Anulación de comprobante emitido por error';

    // 1) Nunca se envió a la DGII: nada que anular allá
    if (!$invoice->enviada_dgii || empty($invoice->encf)) {
        $invoice->update(['status' => 'cancelada']);

        return ['ok' => true, 'message' => 'Factura cancelada. No había comprobante enviado a la DGII.'];
    }

    $estado = strtolower((string) $invoice->estado_dgii);

    // 2) Rechazado: la DGII ya lo descarta, no requiere nota de crédito
    if (str_contains($estado, 'rechaz')) {
        $invoice->update(['status' => 'cancelada']);

        return ['ok' => true, 'message' => 'Factura cancelada. El comprobante fue rechazado por la DGII, no requiere nota de crédito.'];
    }

    // 3) Solo se puede anular un comprobante ya aceptado
    if (!str_contains($estado, 'acept')) {
        return [
            'ok'      => false,
            'message' => 'El comprobante aún no figura como aceptado por la DGII (estado: '
                       . ($invoice->estado_dgii ?: 'desconocido')
                       . '). Espera a que sea aceptado o rechazado antes de anularlo.',
        ];
    }

    // 4) ¿Ya se emitió una nota de crédito válida antes? (evita duplicados)
    $ncPrevia = $invoice->ecfDocuments()
        ->where('tipo_ecf', '34')
        ->whereNotNull('encf')
        ->whereNull('error_message')
        ->latest()
        ->first();

    if ($ncPrevia) {
        $invoice->update(['status' => 'cancelada']);

        return ['ok' => true, 'message' => 'Factura cancelada. Ya existía la nota de crédito ' . $ncPrevia->encf . '.'];
    }

    // 5) Emitir la nota de crédito a la DGII
    $result = $this->ef2Service->enviarNotaCreditoSeguro($invoice, $motivo);
    $this->guardarRespuestaNotaCredito($invoice, $result);

    if (empty($result['success'])) {
        return [
            'ok'      => false,
            'message' => 'No se pudo emitir la nota de crédito: ' . ($result['message'] ?? 'Error desconocido')
                       . '. La factura NO fue cancelada.',
        ];
    }

    $invoice->update(['status' => 'cancelada']);

    return [
        'ok'      => true,
        'message' => 'Factura cancelada. Nota de crédito enviada a la DGII. e-NCF: ' . ($result['ncf'] ?? ''),
    ];
}

public function guardarRespuestaNotaCredito(InsuranceInvoice $invoice, array $result): void
{
    $exitoso = !empty($result['success']);

    // OJO: no toca encf/track_id/estado de la factura, que siguen
    // siendo los del comprobante original.
    $invoice->ecfDocuments()->create([
        'tipo_ecf'           => '34',
        'encf'               => $result['ncf'] ?? null,
        'track_id'           => $result['track_id'] ?? null,
        'estado'             => $result['estado'] ?? ($exitoso ? 'enviado' : 'error'),
        'qr_link'            => $result['qr_link'] ?? null,
        'pdf_cloud_url'      => $result['pdf_cloud_url'] ?? null,
        'payload_enviado'    => $result['payload'] ?? null,
        'respuesta_completa' => $result,
        'error_code'         => $result['code'] ?? null,
        'error_message'      => $exitoso ? null : ($result['message'] ?? 'Error desconocido'),
        'ecf_sequence_id'    => $result['ecf_sequence_id'] ?? null,
        'user_id'            => auth()->id(),
        'intentos'           => 1,
        'enviado_at'         => now(),
        'respondido_at'      => now(),
    ]);
}
}