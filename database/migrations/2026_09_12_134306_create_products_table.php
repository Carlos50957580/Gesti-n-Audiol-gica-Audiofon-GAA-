<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->string('code')->unique();              // Código interno
            $table->string('barcode')->nullable()->unique(); // Código de barras
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->default('unidad');     // unidad, caja, ml, mg, etc.
            
            // Precios
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            
            // Control de stock
            $table->integer('min_stock')->default(0);       // Stock mínimo (alerta)
            $table->integer('max_stock')->nullable();       // Stock máximo (opcional)
            $table->boolean('has_tax')->default(false);     // ¿Aplica ITBIS?
            $table->boolean('is_active')->default(true);
            
            // Imagen
            $table->string('image')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['name', 'code', 'barcode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};