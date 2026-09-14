<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_invoices', function (Blueprint $table) {
            $table->foreignId('ncf_sequence_id')
                  ->nullable()
                  ->after('ncf_type')
                  ->constrained('ncf_sequences')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('product_invoices', function (Blueprint $table) {
            $table->dropForeign(['ncf_sequence_id']);
            $table->dropColumn('ncf_sequence_id');
        });
    }
};