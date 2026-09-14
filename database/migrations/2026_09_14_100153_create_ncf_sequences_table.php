<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ncf_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                 // "Factura Crédito Fiscal 2026", etc.
            $table->foreignId('ncf_type_id')->constrained('ncf_types')->onDelete('restrict');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->string('prefix', 10)->default('B');             // B, E (para e-CF)
            $table->string('serie', 3)->default('01');              // 01, 02, etc. (nomenclatura interna DGII)
            $table->string('start_number', 20);                     // Número inicial (00000001)
            $table->string('end_number', 20);                       // Número final (00010000)
            $table->string('current_number', 20);                   // Número actual (00000001)
            $table->integer('alert_threshold')->default(50);        // Alerta cuando queden N números
            $table->date('valid_from');                             // Válido desde
            $table->date('valid_until');                            // Vence el
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['ncf_type_id', 'is_active']);
            $table->index(['branch_id', 'is_active']);
            $table->index('valid_until');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ncf_sequences');
    }
};