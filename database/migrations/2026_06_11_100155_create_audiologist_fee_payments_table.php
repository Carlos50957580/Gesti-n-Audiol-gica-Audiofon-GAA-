<?php
// database/migrations/2024_01_01_000003_create_audiologist_fee_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audiologist_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audiologist_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('reference_number')->nullable();
            $table->enum('payment_method', ['bank_transfer', 'cash', 'check', 'other'])->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Tabla pivote para los fees pagados en este pago
            $table->index('payment_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audiologist_fee_payments');
    }
};