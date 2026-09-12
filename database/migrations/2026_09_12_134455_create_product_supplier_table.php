<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('supplier_code')->nullable(); // Código del producto en el proveedor
            $table->decimal('last_cost', 10, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['product_id', 'supplier_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_supplier');
    }
};