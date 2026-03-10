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
        Schema::table('patients', function (Blueprint $table) {

            // Relación con aseguradora
            $table->foreignId('insurance_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('insurances')
                ->nullOnDelete();

            // Número de afiliado del paciente
            $table->string('insurance_number')
                ->nullable()
                ->after('insurance_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {

            $table->dropForeign(['insurance_id']);
            $table->dropColumn('insurance_id');
            $table->dropColumn('insurance_number');

        });
    }
};