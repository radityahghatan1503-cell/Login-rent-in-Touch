<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tb_pemesanan') && ! Schema::hasColumn('tb_pemesanan', 'alasan_penolakan')) {
            Schema::table('tb_pemesanan', function (Blueprint $table) {
                $table->text('alasan_penolakan')->nullable();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Tidak menghapus kolom maupun tabel agar riwayat booking/notifikasi
        // tidak hilang saat rollback kode aplikasi.
    }
};
