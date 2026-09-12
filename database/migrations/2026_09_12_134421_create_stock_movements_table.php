<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();          // Número de referencia
            $table->enum('type', ['entrada', 'salida', 'ajuste', 'transferencia']);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Para transferencias
            $table->foreignId('destination_branch_id')->nullable()->constrained('branches')->onDelete('set null');
            
            $table->date('movement_date');
            $table->text('notes')->nullable();
            $table->decimal('total', 12, 2)->default(0);    // Total del movimiento (en compras)
            $table->enum('status', ['borrador', 'confirmado', 'cancelado'])->default('borrador');
            
            $table->timestamps();
            
            $table->index(['type', 'branch_id', 'movement_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};