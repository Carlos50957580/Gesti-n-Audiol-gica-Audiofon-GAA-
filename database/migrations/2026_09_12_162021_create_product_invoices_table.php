<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();           // Número interno (FAC-PROD-000001)
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quien factura
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            
            // Totales
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_with_tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            
            // Estado
            $table->enum('status', ['pendiente', 'pagada', 'cancelada'])->default('pendiente');
            
            // NCF (opcional, si usas facturación electrónica)
            $table->boolean('with_ncf')->default(false);
            $table->string('ncf')->nullable();
            $table->enum('ncf_type', ['consumidor_final', 'credito_fiscal', 'gubernamental', 'regimen_especial'])->nullable();
            $table->string('customer_rnc')->nullable();
            $table->string('customer_business_name')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['branch_id', 'status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_invoices');
    }
};