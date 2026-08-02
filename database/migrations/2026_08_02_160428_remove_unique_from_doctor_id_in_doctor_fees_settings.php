<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('doctor_fees_settings', function (Blueprint $table) {
            // Eliminar la restricción UNIQUE en doctor_id
            $table->dropUnique('audiologist_fees_settings_audiologist_id_unique');
            
            // Si el nombre es diferente, usa este comando para verlo:
            // php artisan doctrine:schema:list --table=doctor_fees_settings
        });
    }

    public function down()
    {
        Schema::table('doctor_fees_settings', function (Blueprint $table) {
            // Recrear la restricción UNIQUE
            $table->unique('doctor_id', 'audiologist_fees_settings_audiologist_id_unique');
        });
    }
};