<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
          Schema::create('tb_tenant', function (Blueprint $table) {
        $table->id('id_tenant');
        $table->string('nama_rental');
        $table->string('subdomain')->unique();
        $table->text('alamat')->nullable();
        $table->string('telepon', 20)->nullable();
        $table->string('email')->nullable();
        $table->string('logo')->nullable();
        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tenant');
    }
};
