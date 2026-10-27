<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Akun demo untuk pengembangan. Ganti/hapus sebelum produksi.
     */
    public function run(): void
    {
        $akun = [
            ['name' => 'Admin Rent In-Touch', 'email' => 'admin@rentintouch.test', 'role' => User::ROLE_ADMIN],
            ['name' => 'Jaya Rent',           'email' => 'mitra@rentintouch.test', 'role' => User::ROLE_MITRA],
            ['name' => 'Budi Pelanggan',      'email' => 'pelanggan@rentintouch.test', 'role' => User::ROLE_PELANGGAN],
        ];

        foreach ($akun as $a) {
            User::updateOrCreate(
                ['email' => $a['email']],
                $a + ['password' => 'password123']
            );
        }
    }
}
