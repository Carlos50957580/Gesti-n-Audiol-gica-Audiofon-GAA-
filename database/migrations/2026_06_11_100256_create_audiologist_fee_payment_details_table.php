<?php
// database/migrations/2024_01_01_000004_create_audiologist_fee_payment_details_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audiologist_fee_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('audiologist_fee_payments')->onDelete('cascade');
            $table->foreignId('fee_id')->constrained('audiologist_fees')->onDelete('cascade');
            $table->decimal('amount_applied', 12, 2);
            $table->timestamps();
            
            $table->unique(['payment_id', 'fee_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audiologist_fee_payment_details');
    }
};