<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Ej: Consultas, Radiología, Laboratorio');
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->comment('Icono para UI');
            $table->string('color')->nullable()->comment('Color para UI');
            $table->boolean('requires_clinical_record')->default(false)->comment('¿Requiere historia clínica?');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_categories');
    }
};