<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audiologist_fees_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audiologist_id')->constrained('users')->onDelete('cascade');
            $table->enum('calculation_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2); // porcentaje (ej: 30.00) o monto fijo
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('audiologist_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audiologist_fees_settings');
    }
};