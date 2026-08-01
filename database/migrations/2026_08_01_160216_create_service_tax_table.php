<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_tax', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(true)->comment('Si el impuesto es obligatorio para este servicio');
            $table->timestamps();
            
            $table->unique(['service_id', 'tax_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_tax');
    }
};