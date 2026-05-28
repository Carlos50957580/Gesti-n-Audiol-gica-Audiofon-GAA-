<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('invoices', function (Blueprint $table) {

        $table->boolean('with_ncf')->default(false);

        $table->string('ncf')->nullable();

        $table->enum('ncf_type', [
            'consumidor_final',
            'credito_fiscal',
            'gubernamental',
            'regimen_especial'
        ])->nullable();

        $table->string('customer_rnc')->nullable();

        $table->string('customer_business_name')->nullable();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
