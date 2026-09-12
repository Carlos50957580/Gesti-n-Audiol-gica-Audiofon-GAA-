<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movement_id')->constrained('stock_movements')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->integer('previous_stock')->default(0); // Stock antes del movimiento
            $table->integer('new_stock')->default(0);      // Stock después del movimiento
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movement_items');
    }
};