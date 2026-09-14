<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ncf_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();    // B01, B02, B14, B15, etc.
            $table->string('name');                  // Nombre corto
            $table->text('description')->nullable(); // Descripción larga
            $table->enum('category', [               // Categoría del comprobante
                'ingresos',      // B01, B02, B03, B04
                'gastos',        // B11, B13, B15, B17
                'gubernamental', // B12, B16
                'regimen',       // B14
                'especial'       // B19, B20 (comprobantes de compras)
            ]);
            $table->boolean('is_electronic')->default(false); // ¿Es e-CF?
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ncf_types');
    }
};