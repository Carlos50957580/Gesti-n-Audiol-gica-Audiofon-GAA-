<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // php artisan make:migration add_profile_photo_to_users_table
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('profile_photo')->nullable()->after('name');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('profile_photo');
    });
}
};
