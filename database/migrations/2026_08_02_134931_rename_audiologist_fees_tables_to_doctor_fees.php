<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Renombrar tabla audiologist_fees → doctor_fees
        Schema::rename('audiologist_fees', 'doctor_fees');

        // 2. Renombrar tabla audiologist_fee_payments → doctor_fee_payments
        Schema::rename('audiologist_fee_payments', 'doctor_fee_payments');

        // 3. Renombrar tabla audiologist_fee_payment_details → doctor_fee_payment_details
        Schema::rename('audiologist_fee_payment_details', 'doctor_fee_payment_details');

        // 4. Renombrar tabla audiologist_fees_settings → doctor_fees_settings
        Schema::rename('audiologist_fees_settings', 'doctor_fees_settings');

        // 5. Renombrar columnas en doctor_fees (audiologist_id → doctor_id)
        Schema::table('doctor_fees', function (Blueprint $table) {
            $table->renameColumn('audiologist_id', 'doctor_id');
        });

        // 6. Renombrar columnas en doctor_fee_payments (audiologist_id → doctor_id)
        Schema::table('doctor_fee_payments', function (Blueprint $table) {
            $table->renameColumn('audiologist_id', 'doctor_id');
        });

        // 7. Renombrar columnas en doctor_fees_settings (audiologist_id → doctor_id)
        Schema::table('doctor_fees_settings', function (Blueprint $table) {
            $table->renameColumn('audiologist_id', 'doctor_id');
        });

        // 8. Renombrar columnas en doctor_fee_payment_details (fee_id → doctor_fee_id, payment_id → doctor_fee_payment_id)
        // Nota: estas columnas son foreign keys, las renombramos para mantener consistencia
        Schema::table('doctor_fee_payment_details', function (Blueprint $table) {
            $table->renameColumn('fee_id', 'doctor_fee_id');
            $table->renameColumn('payment_id', 'doctor_fee_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revertir renombres de columnas en doctor_fee_payment_details
        Schema::table('doctor_fee_payment_details', function (Blueprint $table) {
            $table->renameColumn('doctor_fee_id', 'fee_id');
            $table->renameColumn('doctor_fee_payment_id', 'payment_id');
        });

        // Revertir renombres de columnas
        Schema::table('doctor_fees_settings', function (Blueprint $table) {
            $table->renameColumn('doctor_id', 'audiologist_id');
        });

        Schema::table('doctor_fee_payments', function (Blueprint $table) {
            $table->renameColumn('doctor_id', 'audiologist_id');
        });

        Schema::table('doctor_fees', function (Blueprint $table) {
            $table->renameColumn('doctor_id', 'audiologist_id');
        });

        // Revertir renombres de tablas
        Schema::rename('doctor_fees_settings', 'audiologist_fees_settings');
        Schema::rename('doctor_fee_payment_details', 'audiologist_fee_payment_details');
        Schema::rename('doctor_fee_payments', 'audiologist_fee_payments');
        Schema::rename('doctor_fees', 'audiologist_fees');
    }
};