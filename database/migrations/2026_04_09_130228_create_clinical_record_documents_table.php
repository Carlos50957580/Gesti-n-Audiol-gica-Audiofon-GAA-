<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // php artisan make:migration create_clinical_record_documents_table
public function up(): void
{
    Schema::create('clinical_record_documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('clinical_record_id')->constrained()->onDelete('cascade');
        $table->foreignId('patient_id')->constrained()->onDelete('cascade');
        $table->string('name');           // nombre descriptivo del documento
        $table->string('file_path');      // ruta en storage
        $table->string('file_name');      // nombre original del archivo
        $table->string('file_type');      // pdf, docx, etc.
        $table->unsignedBigInteger('file_size'); // en bytes
        $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('clinical_record_documents');
}

};
