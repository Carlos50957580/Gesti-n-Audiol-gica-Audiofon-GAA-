<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clinical_records', function (Blueprint $table) {
            // Renombrar audiologist_id a doctor_id si aún no está renombrado
            if (Schema::hasColumn('clinical_records', 'audiologist_id')) {
                $table->renameColumn('audiologist_id', 'doctor_id');
            } elseif (!Schema::hasColumn('clinical_records', 'doctor_id')) {
                $table->foreignId('doctor_id')->after('patient_id')->constrained('users')->onDelete('cascade');
            }

            // Agregar campos de historia clínica
            $table->text('anamnesis')->nullable()->after('notes');
            
            // Signos vitales y medidas (JSON)
            $table->json('vital_signs')->nullable()->after('anamnesis');
            
            // Exploración física
            $table->text('physical_exam')->nullable()->after('vital_signs');
            
            // Diagnóstico presuntivo
            $table->text('presumptive_diagnosis')->nullable()->after('physical_exam');
            
            // Tratamiento
            $table->text('treatment')->nullable()->after('presumptive_diagnosis');
            
            // Evolución
            $table->text('evolution')->nullable()->after('treatment');
            
            // Observaciones
            $table->text('observations')->nullable()->after('evolution');
            
            // Recomendaciones
            $table->text('recommendations')->nullable()->after('observations');
            
            // Fecha de la consulta
            $table->date('consultation_date')->nullable()->after('status');
            
            // Tipo de consulta
            $table->enum('consultation_type', ['primera_vez', 'seguimiento', 'urgencia', 'control'])->default('primera_vez')->after('consultation_date');
            
            // Motivo de la consulta (breve)
            $table->string('consultation_reason', 255)->nullable()->after('consultation_type');
        });
    }

    public function down()
    {
        Schema::table('clinical_records', function (Blueprint $table) {
            // Eliminar campos agregados
            $table->dropColumn([
                'anamnesis',
                'vital_signs',
                'physical_exam',
                'presumptive_diagnosis',
                'treatment',
                'evolution',
                'observations',
                'recommendations',
                'consultation_date',
                'consultation_type',
                'consultation_reason',
            ]);

            // Restaurar nombre de columna si se renombró
            if (Schema::hasColumn('clinical_records', 'doctor_id')) {
                $table->renameColumn('doctor_id', 'audiologist_id');
            }
        });
    }
};