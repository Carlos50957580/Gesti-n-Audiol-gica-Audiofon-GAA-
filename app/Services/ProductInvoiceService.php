<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductInvoice;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\NcfType;
use App\Models\NcfSequence;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductInvoiceService
{
    protected EF2Service $ef2Service;

    public function __construct(EF2Service $ef2Service)
    {
        $this->ef2Service = $ef2Service;
    }

    /**
     * Crear factura de productos
     */
    public function createInvoice(array $data): ProductInvoice
    {
        return DB::transaction(function () use ($data) {
            // ── FORZAR CONSUMIDOR FINAL SI NO SE MARCA CRÉDITO FISCAL ──
            // Si el usuario no marcó "with_ncf", se emite automáticamente como Consumidor Final (B02/e-CF 32)
            if (empty($data['with_ncf'])) {
                $data['with_ncf'] = true;
                $data['ncf_type'] = 'consumidor_final';
                Log::info('ProductInvoiceService::createInvoice - Forzando consumidor_final', [
                    'branch_id' => $data['branch_id'] ?? null,
                ]);
            }

            // ── CONSUMIR NCF AUTOMÁTICAMENTE ────────────────────────
            $ncfCode = null;
            $ncfSequenceId = null;

            if (!empty($data['with_ncf']) && !empty($data['ncf_type'])) {
                $typeCodeMap = [
                    'consumidor_final' => 'B02',
                    'credito_fiscal'   => 'B01',
                    'gubernamental'    => 'B15',
                    'regimen_especial' => 'B14',
                ];

                $typeCode = $typeCodeMap[$data['ncf_type']] ?? null;
                if (!$typeCode) {
                    throw new \Exception('Tipo de NCF inválido.');
                }

                $ncfType = NcfType::where('code', $typeCode)->first();
                if (!$ncfType) {
                    throw new \Exception('Tipo de NCF no configurado en el sistema (código ' . $typeCode . ').');
                }

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

                if (!$sequence) {
                    throw new \Exception('No hay una secuencia NCF activa para este tipo de comprobante (' . $typeCode . ').');
                }

                if (!$sequence->canIssue()) {
                    throw new \Exception('La secuencia NCF no puede emitir más comprobantes (' . $sequence->status_label . ').');
                }

                $ncfCode = $sequence->issueNext();
                $ncfSequenceId = $sequence->id;
            }

            // ── PROCESAR ITEMS ──────────────────────────────────────
            $subtotal = 0;
            $totalTax = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty     = (int) $item['quantity'];
                $price   = (float) $item['price'];

                $itemSubtotal = $qty * $price;
                $taxAmount    = 0;

                if ($product->has_tax) {
                    $taxRate   = (float) (Setting::get('company_tax_rate', 18) ?? 18);
                    $taxAmount = $itemSubtotal * ($taxRate / 100);
                }

                $items[] = [
                    'product_id'     => $product->id,
                    'quantity'       => $qty,
                    'price'          => $price,
                    'subtotal'       => $itemSubtotal,
                    'tax_amount'     => $taxAmount,
                    'total_with_tax' => $itemSubtotal + $taxAmount,
                ];

                $subtotal += $itemSubtotal;
                $totalTax += $taxAmount;
            }

            $totalWithTax = $subtotal + $totalTax;
            $discount     = $data['discount'] ?? 0;
            $total        = $totalWithTax - $discount;

            // ── CREAR FACTURA ──────────────────────────────────────
            $invoice = ProductInvoice::create([
                'number'                 => ProductInvoice::generateNumber(),
                'patient_id'             => $data['patient_id'],
                'user_id'                => auth()->id(),
                'branch_id'              => $data['branch_id'],
                'subtotal'               => $subtotal,
                'tax_amount'             => $totalTax,
                'total_with_tax'         => $totalWithTax,
                'discount'               => $discount,
                'total'                  => $total,
                'paid_amount'            => 0,
                'balance'                => $total,
                'status'                 => 'pendiente',
                'with_ncf'               => true,
                'ncf'                    => $ncfCode,
                'ncf_type'               => $data['ncf_type'],
                'ncf_sequence_id'        => $ncfSequenceId,
                'customer_rnc'           => $data['customer_rnc'] ?? null,
                'customer_business_name' => $data['customer_business_name'] ?? null,
                'notes'                  => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            $this->registerStockExit($invoice);

            // ✅ Enviar a EF2 (no rompe la venta si falla)
            $this->trySendToEf2($invoice->fresh(['items.product', 'patient', 'branch']));

            return $invoice->fresh(['items', 'ecfDocuments']);
        });
    }

    /**
     * Registrar salida de stock por la venta
     */
    protected function registerStockExit(ProductInvoice $invoice): void
    {
        $movement = StockMovement::create([
            'reference'     => 'VENTA-' . $invoice->number,
            'type'          => 'salida',
            'branch_id'     => $invoice->branch_id,
            'user_id'       => $invoice->user_id,
            'movement_date' => now()->toDateString(),
            'notes'         => 'Venta de productos - Factura ' . $invoice->number,
            'total'         => 0,
            'status'        => 'confirmado',
        ]);

        foreach ($invoice->items as $item) {
            $stock = ProductStock::firstOrCreate(
                ['product_id' => $item->product_id, 'branch_id' => $invoice->branch_id],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );

            $previousStock = $stock->quantity;

            StockMovementItem::create([
                'movement_id'    => $movement->id,
                'product_id'     => $item->product_id,
                'quantity'       => $item->quantity,
                'unit_price'     => $item->price,
                'subtotal'       => $item->subtotal,
                'previous_stock' => $previousStock,
                'new_stock'      => $previousStock - $item->quantity,
            ]);

            $stock->quantity = $previousStock - $item->quantity;
            $stock->save();
        }
    }

    /**
     * Registrar pago de factura (permite pagos parciales)
     */
    public function registerPayment(ProductInvoice $invoice, array $data): \App\Models\ProductReceipt
    {
        return DB::transaction(function () use ($invoice, $data) {
            if ($invoice->status === 'pagada') {
                throw new \Exception('Esta factura ya está completamente pagada.');
            }
            if ($invoice->status === 'cancelada') {
                throw new \Exception('No se puede pagar una factura cancelada.');
            }

            $cash     = (float) ($data['cash_amount'] ?? 0);
            $card     = (float) ($data['card_amount'] ?? 0);
            $transfer = (float) ($data['transfer_amount'] ?? 0);
            $total    = round($cash + $card + $transfer, 2);

            if ($total <= 0) {
                throw new \Exception('Debes ingresar al menos un monto de pago.');
            }

            $receipt = \App\Models\ProductReceipt::create([
                'number'             => \App\Models\ProductReceipt::generateNumber(),
                'product_invoice_id' => $invoice->id,
                'user_id'            => auth()->id(),
                'branch_id'          => $invoice->branch_id,
                'cash_amount'        => $cash > 0 ? $cash : null,
                'card_amount'        => $card > 0 ? $card : null,
                'transfer_amount'    => $transfer > 0 ? $transfer : null,
                'total_paid'         => $total,
                'card_reference'     => $data['card_reference'] ?? null,
                'transfer_reference' => $data['transfer_reference'] ?? null,
                'notes'              => $data['notes'] ?? null,
            ]);

            $invoice->recalculateBalance();

            return $receipt;
        });
    }

    // ═══════════════════════════════════════════════════════════
    // FACTURACIÓN ELECTRÓNICA (EF2)
    // ═══════════════════════════════════════════════════════════

    protected function trySendToEf2(ProductInvoice $invoice): void
    {
        Log::info('═══════════════════════════════════════════');
        Log::info('ProductInvoiceService::trySendToEf2 - INICIO', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number,
        ]);

        if (!$this->ef2Service->estaConfigurado()) {
            Log::warning('ProductInvoiceService::trySendToEf2 - ABORT: EF2 no configurado');
            return;
        }

        if (!$invoice->ncf_type) {
            Log::warning('ProductInvoiceService::trySendToEf2 - ABORT: Sin ncf_type');
            return;
        }

        try {
            $result = $this->ef2Service->enviarFacturaProductos($invoice);
            $this->guardarRespuestaEf2($invoice, $result);
        } catch (\Exception $e) {
            Log::error('ProductInvoiceService::trySendToEf2 - EXCEPCIÓN', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            try {
                $invoice->ecfDocuments()->create([
                    'tipo_ecf'          => $this->mapearTipoEcf($invoice),
                    'estado'            => 'error',
                    'error_message'     => $e->getMessage(),
                    'user_id'           => auth()->id(),
                    'intentos'          => 1,
                    'enviado_at'        => now(),
                ]);
            } catch (\Exception $inner) {
                Log::error('ProductInvoiceService::trySendToEf2 - Error guardando ecf_document', [
                    'error' => $inner->getMessage(),
                ]);
            }
        }
    }

    public function guardarRespuestaEf2(ProductInvoice $invoice, array $result): void
    {
        $exitoso = !empty($result['success']);

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
            'ecf_sequence_id'    => $invoice->ecf_sequence_id,
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
                'enviada_dgii'     => true,
                'enviada_dgii_at'  => now(),
            ]);
        }
    }

    protected function mapearTipoEcf(ProductInvoice $invoice): string
    {
        return match ($invoice->ncf_type) {
            'credito_fiscal'   => '31',
            'consumidor_final' => '32',
            'gubernamental'    => '45',
            'regimen_especial' => '44',
            default            => '32',
        };
    }
}