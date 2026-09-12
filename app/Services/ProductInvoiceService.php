<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductInvoice;
use App\Models\ProductInvoiceItem;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Illuminate\Support\Facades\DB;

class ProductInvoiceService
{
    /**
     * Crear factura de productos
     */
    public function createInvoice(array $data): ProductInvoice
    {
        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            $totalTax = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty     = (int) $item['quantity'];
                $price   = (float) $item['price'];
                
                $itemSubtotal = $qty * $price;
                $taxAmount    = 0;

                // Aplicar ITBIS si el producto lo requiere
                if ($product->has_tax) {
                    $taxRate   = (float) (\App\Models\Setting::get('company_tax_rate', 18) ?? 18);
                    $taxAmount = $itemSubtotal * ($taxRate / 100);
                }

                $items[] = [
                    'product_id'      => $product->id,
                    'quantity'        => $qty,
                    'price'           => $price,
                    'subtotal'        => $itemSubtotal,
                    'tax_amount'      => $taxAmount,
                    'total_with_tax'  => $itemSubtotal + $taxAmount,
                ];

                $subtotal += $itemSubtotal;
                $totalTax += $taxAmount;
            }

            $totalWithTax = $subtotal + $totalTax;
            $discount     = $data['discount'] ?? 0;
            $total        = $totalWithTax - $discount;

            // Crear factura
            $invoice = ProductInvoice::create([
                'number'               => ProductInvoice::generateNumber(),
                'patient_id'           => $data['patient_id'],
                'user_id'              => auth()->id(),
                'branch_id'            => $data['branch_id'],
                'subtotal'             => $subtotal,
                'tax_amount'           => $totalTax,
                'total_with_tax'       => $totalWithTax,
                'discount'             => $discount,
                'total'                => $total,
                'status'               => 'pendiente',
                'with_ncf'             => $data['with_ncf'] ?? false,
                'ncf'                  => $data['ncf'] ?? null,
                'ncf_type'             => $data['ncf_type'] ?? null,
                'customer_rnc'         => $data['customer_rnc'] ?? null,
                'customer_business_name' => $data['customer_business_name'] ?? null,
                'notes'                => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            // Registrar salida de stock automáticamente
            $this->registerStockExit($invoice);

            return $invoice;
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
            'status'        => 'confirmado', // Se confirma automáticamente
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

            // Descontar del stock
            $stock->quantity = $previousStock - $item->quantity;
            $stock->save();
        }
    }

    /**
     * Registrar pago de factura
     */
    public function registerPayment(ProductInvoice $invoice, array $data): \App\Models\ProductReceipt
    {
        return DB::transaction(function () use ($invoice, $data) {
            $cash     = (float) ($data['cash_amount'] ?? 0);
            $card     = (float) ($data['card_amount'] ?? 0);
            $transfer = (float) ($data['transfer_amount'] ?? 0);
            $total    = round($cash + $card + $transfer, 2);

            if ($total < (float) $invoice->total - 0.01) {
                throw new \Exception('El monto pagado es menor al total de la factura.');
            }

            $receipt = \App\Models\ProductReceipt::create([
                'number'             => \App\Models\ProductReceipt::generateNumber(),
                'product_invoice_id' => $invoice->id,
                'user_id'            => auth()->id(),
                'branch_id'          => $invoice->branch_id,
                'cash_amount'        => $cash > 0 ? $cash : null,
                'card_amount'        => $card > 0 ? $card : null,
                'transfer_amount'    => $transfer > 0 ? $transfer : null,
                'total_paid'         => $invoice->total,
                'card_reference'     => $data['card_reference'] ?? null,
                'transfer_reference' => $data['transfer_reference'] ?? null,
                'notes'              => $data['notes'] ?? null,
            ]);

            $invoice->update(['status' => 'pagada']);

            return $receipt;
        });
    }
}