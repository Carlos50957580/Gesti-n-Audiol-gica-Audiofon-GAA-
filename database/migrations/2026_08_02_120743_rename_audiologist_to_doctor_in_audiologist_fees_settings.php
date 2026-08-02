<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('audiologist_fees_settings', function (Blueprint $table) {
            $table->renameColumn('audiologist_id', 'doctor_id');
        });
    }

    public function down()
    {
        Schema::table('audiologist_fees_settings', function (Blueprint $table) {
            $table->renameColumn('doctor_id', 'audiologist_id');
        });
    }
};