<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Aman dijalankan berulang.
     */
    public function run(): void
    {
        // 1. Data contoh di SQL menyimpan password sebagai teks biasa (mis. "andi123").
        //    Laravel butuh hash, jadi ubah semua yang belum berupa hash bcrypt.
        //    Kata sandi lama tetap sama, hanya disimpan dalam bentuk hash.
        DB::table('tb_users')
            ->where('password', 'not like', '$2y$%')
            ->get(['id_user', 'password'])
            ->each(function ($u) {
                DB::table('tb_users')
                    ->where('id_user', $u->id_user)
                    ->update(['password' => Hash::make($u->password)]);
            });

        // 2. Data contoh belum punya super admin untuk tab "Admin".
        //    Hanya dibuat jika belum ada (kata sandi tidak ditimpa).
        if (! User::where('email', 'superadmin@example.com')->exists()) {
            User::create([
                'id_tenant' => null,
                'nama' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => 'superadmin123',
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
            ]);
        }
    }
}
