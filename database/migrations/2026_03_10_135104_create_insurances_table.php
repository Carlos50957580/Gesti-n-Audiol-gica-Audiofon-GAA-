<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insurances', function (Blueprint $table) {

            $table->id();

            // Nombre de la aseguradora
            $table->string('name');

            // Dirección
            $table->string('address')->nullable();

            // Email
            $table->string('email')->nullable();

            // Teléfono principal
            $table->string('phone')->nullable();

            // Teléfono para autorizaciones
            $table->string('authorization_phone')->nullable();

            // Porcentaje de cobertura del seguro
            $table->decimal('coverage_percentage',5,2)->default(0);

            // Estado del seguro
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};