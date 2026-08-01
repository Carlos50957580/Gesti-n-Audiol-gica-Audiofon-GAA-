<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->default(0)->after('patient_amount');
            $table->json('tax_details')->nullable()->after('tax_amount');
            $table->decimal('total_with_tax', 10, 2)->default(0)->after('tax_details');
        });
    }

    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'tax_details', 'total_with_tax']);
        });
    }
};