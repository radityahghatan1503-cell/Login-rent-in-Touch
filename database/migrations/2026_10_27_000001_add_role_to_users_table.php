<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Tidak dipakai lagi. Login sekarang memakai tabel tb_users dari ERD
 * (kolom role sudah ada di sana). File ini dibiarkan kosong agar riwayat
 * migrasi tidak rusak.
 */
return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
