<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // quien cobró
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');

            // Montos por método (null = no se usó ese método)
            $table->decimal('cash_amount',    10, 2)->nullable();
            $table->decimal('card_amount',    10, 2)->nullable();
            $table->decimal('transfer_amount',10, 2)->nullable();
            $table->decimal('total_paid',     10, 2);                           // suma de los tres

            // Referencias opcionales
            $table->string('card_reference',    100)->nullable();
            $table->string('transfer_reference',100)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};