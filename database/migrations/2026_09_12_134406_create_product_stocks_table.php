<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // Reservado (para pedidos pendientes)
            $table->timestamps();
            
            $table->unique(['product_id', 'branch_id']);
            $table->index(['branch_id', 'quantity']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_stocks');
    }
};