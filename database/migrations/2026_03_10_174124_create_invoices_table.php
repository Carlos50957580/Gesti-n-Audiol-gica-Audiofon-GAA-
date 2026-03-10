<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('insurance_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('subtotal',10,2)->default(0);
            $table->decimal('insurance_discount',10,2)->default(0);
            $table->decimal('total',10,2)->default(0);

            $table->enum('status',['pendiente','pagada','cancelada'])
                ->default('pendiente');

            $table->string('authorization_number')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
