<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Laravel butuh kolom remember_token agar fitur "Ingat saya" berfungsi.
 * Satu-satunya tambahan di luar ERD.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tb_users', 'remember_token')) {
            Schema::table('tb_users', function (Blueprint $table) {
                $table->string('remember_token', 100)->nullable()->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_users', 'remember_token')) {
            Schema::table('tb_users', function (Blueprint $table) {
                $table->dropColumn('remember_token');
            });
        }
    }
};
