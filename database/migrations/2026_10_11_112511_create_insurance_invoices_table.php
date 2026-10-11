<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_invoices', function (Blueprint $table) {
            $table->id();

            // Número de factura (ej: FAC-ARS-000001)
            $table->string('number')->unique();

            // Fecha de emisión (editable)
            $table->date('invoice_date');

            // Relaciones
            $table->foreignId('insurance_id')->constrained('insurances')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // quien la crea

            // Factura(s) de paciente que respaldan esta factura al seguro (opcional)
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');

            // Montos
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('with_tax')->default(false); // aplicar ITBIS o no

            // Concepto / descripción
            $table->text('concept')->nullable();

            // Estado del pago
            $table->enum('status', ['pendiente', 'pagada', 'pagada_parcial', 'cancelada'])->default('pendiente');
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            // NCF tradicional (compatibilidad)
            $table->boolean('with_ncf')->default(true);
            $table->string('ncf')->nullable();
            $table->string('ncf_type')->nullable(); // 'credito_fiscal', 'gubernamental', etc.
            $table->foreignId('ncf_sequence_id')->nullable()->constrained('ncf_sequences')->onDelete('set null');

            // Datos del cliente (RNC del seguro)
            $table->string('customer_rnc')->nullable();
            $table->string('customer_business_name')->nullable();

            // e-CF (facturación electrónica)
            $table->string('encf', 20)->nullable();
            $table->string('track_id')->nullable();
            $table->string('estado_dgii')->nullable();
            $table->string('qr_link')->nullable();
            $table->string('pdf_cloud_url')->nullable();
            $table->foreignId('ecf_sequence_id')->nullable()->constrained('ecf_sequences')->onDelete('set null');
            $table->boolean('enviada_dgii')->default(false);
            $table->timestamp('enviada_dgii_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['branch_id', 'status', 'invoice_date']);
            $table->index('encf');
            $table->index('estado_dgii');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_invoices');
    }
};