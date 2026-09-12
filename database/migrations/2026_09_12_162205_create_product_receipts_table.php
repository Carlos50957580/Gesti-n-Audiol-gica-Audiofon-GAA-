<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();           // REC-PROD-000001
            $table->foreignId('product_invoice_id')->constrained('product_invoices')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            
            // Métodos de pago
            $table->decimal('cash_amount', 10, 2)->nullable();
            $table->decimal('card_amount', 10, 2)->nullable();
            $table->decimal('transfer_amount', 10, 2)->nullable();
            $table->decimal('total_paid', 10, 2);
            
            $table->string('card_reference', 100)->nullable();
            $table->string('transfer_reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_receipts');
    }
};