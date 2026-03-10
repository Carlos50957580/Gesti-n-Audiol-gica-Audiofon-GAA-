<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('invoice_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('price',10,2);

            $table->integer('quantity')->default(1);

            $table->decimal('subtotal',10,2);

            $table->decimal('coverage_percentage',5,2)->nullable();

            $table->decimal('insurance_amount',10,2)->nullable();

            $table->decimal('patient_amount',10,2)->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};