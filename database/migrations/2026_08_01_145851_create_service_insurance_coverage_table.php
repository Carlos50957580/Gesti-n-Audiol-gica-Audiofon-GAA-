<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_insurance_coverage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('insurance_id')->constrained()->onDelete('cascade');
            $table->decimal('coverage_percentage', 5, 2)->default(0);
            $table->decimal('fixed_amount', 10, 2)->nullable()->comment('Monto fijo si aplica');
            $table->boolean('requires_authorization')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Evitar duplicados
            $table->unique(['service_id', 'insurance_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_insurance_coverage');
    }
};