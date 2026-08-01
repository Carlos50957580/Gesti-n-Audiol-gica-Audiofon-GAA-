<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre del impuesto, ej: ITBIS');
            $table->string('code', 50)->unique()->comment('Código del impuesto, ej: ITBIS');
            $table->decimal('rate', 5, 2)->comment('Porcentaje del impuesto, ej: 18.00');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false)->comment('Si es el impuesto por defecto');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taxes');
    }
};