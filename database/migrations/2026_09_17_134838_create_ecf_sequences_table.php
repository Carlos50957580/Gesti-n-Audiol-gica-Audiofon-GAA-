<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecf_sequences', function (Blueprint $table) {
            $table->id();
            
            // Tipo de e-CF según DGII: 31, 32, 33, 34, 41, 43, 44, 45, 46, 47
            $table->string('tipo_ecf', 2)->comment('Código DGII: 31, 32, 33, etc.');
            
            // Prefijo con la E: E31, E32, E34, etc.
            $table->string('prefijo', 5)->comment('Prefijo: E31, E32, etc.');
            
            // Rango autorizado por DGII
            $table->unsignedBigInteger('desde');
            $table->unsignedBigInteger('hasta');
            $table->unsignedBigInteger('secuencia_actual')->default(0);
            
            // Fechas
            $table->date('fecha_vencimiento');
            $table->date('valid_from')->nullable();
            
            // Estado
            $table->boolean('estado')->default(true);
            
            // Sucursal opcional
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            
            $table->text('notas')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['tipo_ecf', 'estado']);
            $table->index(['prefijo', 'estado']);
            $table->index('fecha_vencimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecf_sequences');
    }
};