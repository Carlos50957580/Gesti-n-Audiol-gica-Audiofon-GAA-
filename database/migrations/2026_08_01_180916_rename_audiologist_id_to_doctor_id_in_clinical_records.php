<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('total_with_tax', 10, 2)->default(0)->after('tax_amount');
            $table->json('tax_details')->nullable()->after('total_with_tax');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'total_with_tax', 'tax_details']);
        });
    }
};