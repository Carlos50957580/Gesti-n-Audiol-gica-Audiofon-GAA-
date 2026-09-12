<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Generar referencia única para movimiento
     */
    public function generateReference(string $type): string
    {
        $prefix = match($type) {
            'entrada'       => 'ENT',
            'salida'        => 'SAL',
            'ajuste'        => 'AJU',
            'transferencia' => 'TRF',
            default         => 'MOV',
        };

        $date = now()->format('Ymd');
        $last = StockMovement::where('reference', 'like', "{$prefix}-{$date}-%")
                    ->orderByDesc('id')->first();

        $number = $last ? ((int) substr($last->reference, -4)) + 1 : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $number);
    }

    /**
     * Obtener o crear stock para producto/sucursal
     */
    public function getOrCreateStock(int $productId, int $branchId): ProductStock
    {
        return ProductStock::firstOrCreate(
            ['product_id' => $productId, 'branch_id' => $branchId],
            ['quantity' => 0, 'reserved_quantity' => 0]
        );
    }

    /**
     * Registrar una entrada de productos
     */
    public function registerEntry(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create([
                'reference'     => $this->generateReference('entrada'),
                'type'          => 'entrada',
                'branch_id'     => $data['branch_id'],
                'supplier_id'   => $data['supplier_id'] ?? null,
                'user_id'       => auth()->id(),
                'movement_date' => $data['movement_date'],
                'notes'         => $data['notes'] ?? null,
                'total'         => $data['total'] ?? 0,
                'status'        => 'borrador',
            ]);

            foreach ($data['items'] as $item) {
                $stock = $this->getOrCreateStock($item['product_id'], $data['branch_id']);
                
                StockMovementItem::create([
                    'movement_id'    => $movement->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'subtotal'       => $item['quantity'] * $item['unit_price'],
                    'previous_stock' => $stock->quantity,
                    'new_stock'      => $stock->quantity + $item['quantity'],
                ]);
            }

            return $movement;
        });
    }

    /**
     * Registrar una salida de productos
     */
    public function registerExit(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            // Validar stock antes de proceder
            foreach ($data['items'] as $item) {
                $stock = $this->getOrCreateStock($item['product_id'], $data['branch_id']);
                if ($stock->quantity < $item['quantity']) {
                    throw new \Exception(
                        "Stock insuficiente para el producto ID {$item['product_id']}. " .
                        "Disponible: {$stock->quantity}, Solicitado: {$item['quantity']}"
                    );
                }
            }

            $movement = StockMovement::create([
                'reference'     => $this->generateReference('salida'),
                'type'          => 'salida',
                'branch_id'     => $data['branch_id'],
                'user_id'       => auth()->id(),
                'movement_date' => $data['movement_date'],
                'notes'         => $data['notes'] ?? null,
                'total'         => 0,
                'status'        => 'borrador',
            ]);

            foreach ($data['items'] as $item) {
                $stock = $this->getOrCreateStock($item['product_id'], $data['branch_id']);

                StockMovementItem::create([
                    'movement_id'    => $movement->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => 0,
                    'subtotal'       => 0,
                    'previous_stock' => $stock->quantity,
                    'new_stock'      => $stock->quantity - $item['quantity'],
                ]);
            }

            return $movement;
        });
    }

    /**
     * Confirmar movimiento y aplicar cambios al stock
     */
    public function confirmMovement(StockMovement $movement): bool
    {
        if ($movement->status !== 'borrador') {
            throw new \Exception('Solo se pueden confirmar movimientos en borrador.');
        }

        return DB::transaction(function () use ($movement) {
            foreach ($movement->items as $item) {
                $stock = $this->getOrCreateStock($item->product_id, $movement->branch_id);

                if ($movement->type === 'entrada') {
                    $stock->quantity += $item->quantity;
                } elseif ($movement->type === 'salida') {
                    if ($stock->quantity < $item->quantity) {
                        throw new \Exception("Stock insuficiente para producto ID {$item->product_id}");
                    }
                    $stock->quantity -= $item->quantity;
                } elseif ($movement->type === 'ajuste') {
                    $stock->quantity = $item->new_stock;
                } elseif ($movement->type === 'transferencia') {
                    // Restar de origen
                    if ($stock->quantity < $item->quantity) {
                        throw new \Exception("Stock insuficiente para transferir producto ID {$item->product_id}");
                    }
                    $stock->quantity -= $item->quantity;
                    $stock->save();

                    // Sumar a destino
                    $destStock = $this->getOrCreateStock($item->product_id, $movement->destination_branch_id);
                    $destStock->quantity += $item->quantity;
                    $destStock->save();
                    continue;
                }

                $stock->save();
            }

            $movement->update(['status' => 'confirmado']);
            return true;
        });
    }

    /**
     * Cancelar un movimiento
     */
    public function cancelMovement(StockMovement $movement): bool
    {
        if ($movement->status === 'cancelado') {
            throw new \Exception('El movimiento ya está cancelado.');
        }

        // Si estaba confirmado, revertir los cambios
        if ($movement->status === 'confirmado') {
            foreach ($movement->items as $item) {
                $stock = $this->getOrCreateStock($item->product_id, $movement->branch_id);
                
                if ($movement->type === 'entrada') {
                    $stock->quantity -= $item->quantity;
                } elseif ($movement->type === 'salida') {
                    $stock->quantity += $item->quantity;
                }
                $stock->save();
            }
        }

        $movement->update(['status' => 'cancelado']);
        return true;
    }

    /**
     * Ajustar stock manualmente
     */
    public function adjustStock(int $productId, int $branchId, int $newQuantity, string $reason = null): StockMovement
    {
        return DB::transaction(function () use ($productId, $branchId, $newQuantity, $reason) {
            $stock = $this->getOrCreateStock($productId, $branchId);
            $previousQty = $stock->quantity;

            $movement = StockMovement::create([
                'reference'     => $this->generateReference('ajuste'),
                'type'          => 'ajuste',
                'branch_id'     => $branchId,
                'user_id'       => auth()->id(),
                'movement_date' => now()->toDateString(),
                'notes'         => $reason ?? 'Ajuste manual',
                'total'         => 0,
                'status'        => 'confirmado',
            ]);

            StockMovementItem::create([
                'movement_id'    => $movement->id,
                'product_id'     => $productId,
                'quantity'       => abs($newQuantity - $previousQty),
                'unit_price'     => 0,
                'subtotal'       => 0,
                'previous_stock' => $previousQty,
                'new_stock'      => $newQuantity,
            ]);

            $stock->quantity = $newQuantity;
            $stock->save();

            return $movement;
        });
    }

    /**
     * Transferir productos entre sucursales
     */
    public function transfer(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            // Validar stock en origen
            foreach ($data['items'] as $item) {
                $stock = $this->getOrCreateStock($item['product_id'], $data['branch_id']);
                if ($stock->quantity < $item['quantity']) {
                    throw new \Exception(
                        "Stock insuficiente para transferir producto ID {$item['product_id']}"
                    );
                }
            }

            $movement = StockMovement::create([
                'reference'             => $this->generateReference('transferencia'),
                'type'                  => 'transferencia',
                'branch_id'             => $data['branch_id'],
                'destination_branch_id' => $data['destination_branch_id'],
                'user_id'               => auth()->id(),
                'movement_date'         => $data['movement_date'],
                'notes'                 => $data['notes'] ?? null,
                'total'                 => 0,
                'status'                => 'borrador',
            ]);

            foreach ($data['items'] as $item) {
                $stock = $this->getOrCreateStock($item['product_id'], $data['branch_id']);

                StockMovementItem::create([
                    'movement_id'    => $movement->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => 0,
                    'subtotal'       => 0,
                    'previous_stock' => $stock->quantity,
                    'new_stock'      => $stock->quantity - $item['quantity'],
                ]);
            }

            return $movement;
        });
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStockProducts(?int $branchId = null)
    {
        return Product::with(['stocks' => function ($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
        }])
        ->whereHas('stocks', function ($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
            $q->whereColumn('quantity', '<=', 'products.min_stock');
        })
        ->get();
    }
}