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
    Schema::create('clinical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('invoice_id')->unique()->constrained()->onDelete('cascade');
        $table->foreignId('patient_id')->constrained()->onDelete('cascade');
        $table->foreignId('audiologist_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('branch_id')->constrained()->onDelete('cascade');

        // Sección 1 - Anamnesis
        $table->text('reason_for_consultation')->nullable();

        // Sección 2 - Diagnóstico
        $table->text('diagnosis')->nullable();
        $table->text('treatment_plan')->nullable();

        // Extra
        $table->text('notes')->nullable();
        $table->enum('status', ['pendiente', 'completada'])->default('pendiente');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_records');
    }
};
