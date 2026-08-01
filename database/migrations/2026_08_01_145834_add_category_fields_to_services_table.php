<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Agregar columna category_id después de id
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('service_categories')
                  ->nullOnDelete();
            
            // Agregar código interno
            $table->string('code', 50)->nullable()->after('category_id')->comment('Código interno del estudio');
            
            // Agregar duración estimada
            $table->integer('duration_minutes')->nullable()->after('description')->comment('Duración estimada en minutos');
            
            // ¿Requiere autorización?
            $table->boolean('requires_authorization')->default(false)->after('duration_minutes');
            
            // ¿Requiere historia clínica? (puede heredar de categoría o ser individual)
            $table->boolean('requires_clinical_record')->default(false)->after('requires_authorization');
            
            // Asegurar que price sea decimal con precisión
            // Si ya existe, no la modifiques, solo asegúrate
            if (!Schema::hasColumn('services', 'price')) {
                $table->decimal('price', 10, 2)->default(0);
            }
            
            // Si existe la columna 'active', renombrarla a 'is_active' para consistencia
            if (Schema::hasColumn('services', 'active')) {
                $table->renameColumn('active', 'is_active');
            } else if (!Schema::hasColumn('services', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            // Eliminar foreign key primero
            $table->dropForeign(['category_id']);
            
            // Eliminar columnas
            $table->dropColumn([
                'category_id',
                'code',
                'duration_minutes',
                'requires_authorization',
                'requires_clinical_record'
            ]);
            
            // Revertir renombre si existe
            if (Schema::hasColumn('services', 'is_active') && !Schema::hasColumn('services', 'active')) {
                $table->renameColumn('is_active', 'active');
            }
        });
    }
};