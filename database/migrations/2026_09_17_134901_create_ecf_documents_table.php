<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecf_documents', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica: puede ser product_invoice o invoice
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            
            // Datos del e-CF
            $table->string('tipo_ecf', 2);
            $table->string('encf', 20)->nullable()->comment('e-NCF generado por EF2');
            $table->string('track_id')->nullable()->comment('TrackID de DGII');
            $table->string('estado')->nullable()->comment('aceptado, rechazado, en_proceso, etc.');
            
            // URLs de respuesta
            $table->string('qr_link')->nullable();
            $table->string('pdf_cloud_url')->nullable();
            
            // Payload y respuesta completa
            $table->json('payload_enviado')->nullable();
            $table->json('respuesta_completa')->nullable();
            
            // Códigos de error
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            
            // Control
            $table->foreignId('ecf_sequence_id')->nullable()->constrained('ecf_sequences')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Intentos
            $table->integer('intentos')->default(1);
            $table->timestamp('enviado_at')->nullable();
            $table->timestamp('respondido_at')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('encf');
            $table->index('track_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecf_documents');
    }
};