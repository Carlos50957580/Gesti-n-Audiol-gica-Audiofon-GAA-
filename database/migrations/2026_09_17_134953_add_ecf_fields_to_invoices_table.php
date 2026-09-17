<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('encf', 20)->nullable()->after('ncf');
            $table->string('track_id')->nullable()->after('encf');
            $table->string('estado_dgii')->nullable()->after('track_id');
            $table->string('qr_link')->nullable()->after('estado_dgii');
            $table->string('pdf_cloud_url')->nullable()->after('qr_link');
            
            $table->foreignId('ecf_sequence_id')->nullable()->after('pdf_cloud_url')
                  ->constrained('ecf_sequences')->onDelete('set null');
            
            $table->boolean('enviada_dgii')->default(false)->after('ecf_sequence_id');
            $table->timestamp('enviada_dgii_at')->nullable()->after('enviada_dgii');
            
            $table->index('encf');
            $table->index('track_id');
            $table->index('estado_dgii');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['ecf_sequence_id']);
            $table->dropIndex(['encf']);
            $table->dropIndex(['track_id']);
            $table->dropIndex(['estado_dgii']);
            
            $table->dropColumn([
                'encf',
                'track_id',
                'estado_dgii',
                'qr_link',
                'pdf_cloud_url',
                'ecf_sequence_id',
                'enviada_dgii',
                'enviada_dgii_at',
            ]);
        });
    }
};