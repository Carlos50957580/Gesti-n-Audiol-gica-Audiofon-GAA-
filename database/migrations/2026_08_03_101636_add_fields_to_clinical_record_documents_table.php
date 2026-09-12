<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clinical_record_documents', function (Blueprint $table) {
            // Agregar campos adicionales para documentos
            $table->string('mime_type', 100)->nullable()->after('file_size');
            $table->text('description')->nullable()->after('uploaded_by');
            
            // Opcional: agregar índice para búsquedas
            $table->index(['clinical_record_id', 'patient_id']);
        });
    }

    public function down()
    {
        Schema::table('clinical_record_documents', function (Blueprint $table) {
            $table->dropColumn(['mime_type', 'description']);
            $table->dropIndex(['clinical_record_id', 'patient_id']);
        });
    }
};