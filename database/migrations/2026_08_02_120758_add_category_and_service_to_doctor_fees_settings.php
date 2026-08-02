<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audiologist_fees_settings', function (Blueprint $table) {
            // ✅ Agregar campo para categoría (NULL = aplica a todas)
            $table->foreignId('category_id')
                ->nullable()
                ->after('doctor_id')
                ->constrained('service_categories')
                ->nullOnDelete();
            
            // ✅ Agregar campo para servicio específico (NULL = aplica a todos)
            $table->foreignId('service_id')
                ->nullable()
                ->after('category_id')
                ->constrained('services')
                ->nullOnDelete();
            
            // ✅ Índice para búsquedas rápidas
            $table->index(['doctor_id', 'category_id', 'service_id']);
        });
    }

    public function down()
    {
        Schema::table('audiologist_fees_settings', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn(['category_id', 'service_id']);
        });
    }
};