<?php
// database/migrations/2024_01_01_000002_create_audiologist_fees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audiologist_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audiologist_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('invoice_total', 12, 2);
            $table->enum('calculation_type', ['percentage', 'fixed']);
            $table->decimal('calculation_value', 10, 2);
            $table->decimal('fee_amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['audiologist_id', 'status']);
            $table->index('invoice_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audiologist_fees');
    }
};