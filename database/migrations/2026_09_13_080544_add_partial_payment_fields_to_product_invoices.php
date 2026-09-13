<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_invoices', function (Blueprint $table) {
            // Monto total pagado (acumulado)
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total');
            // Balance pendiente = total - paid_amount
            $table->decimal('balance', 10, 2)->default(0)->after('paid_amount');
        });

        // Modificar el enum status para agregar 'pagada_parcial'
        DB::statement("ALTER TABLE product_invoices MODIFY COLUMN status ENUM('pendiente', 'pagada_parcial', 'pagada', 'cancelada') NOT NULL DEFAULT 'pendiente'");
    }

    public function down()
    {
        Schema::table('product_invoices', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'balance']);
        });

        DB::statement("ALTER TABLE product_invoices MODIFY COLUMN status ENUM('pendiente', 'pagada', 'cancelada') NOT NULL DEFAULT 'pendiente'");
    }
};