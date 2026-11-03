<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Memetakan tabel tb_users (ERD).
 * Peran di database: super_admin, admin_rental, staff, pelanggan.
 * Di aplikasi dikelompokkan jadi 3 "grup" sesuai tab login: admin, mitra, pelanggan.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN_RENTAL = 'admin_rental';
    public const ROLE_STAFF = 'staff';
    public const ROLE_PELANGGAN = 'pelanggan';

    protected $table = 'tb_users';

    protected $primaryKey = 'id_user';

    // tb_users hanya punya created_at (tidak ada updated_at)
    public const UPDATED_AT = null;

    protected $fillable = [
        'id_tenant',
        'nama',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    /** Agar $user->name tetap bisa dipakai di view. */
    public function getNameAttribute(): string
    {
        return $this->nama;
    }

    /** Grup sesuai tab login: admin | mitra | pelanggan. */
    public function grup(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'admin',
            self::ROLE_ADMIN_RENTAL, self::ROLE_STAFF => 'mitra',
            default => 'pelanggan',
        };
    }

    public function grupLabel(): string
    {
        return ucfirst($this->grup());
    }

    /** Cek grup, mis. hasRole('admin') atau hasRole('mitra', 'admin'). */
    public function hasRole(string ...$grup): bool
    {
        return in_array($this->grup(), $grup, true);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN_RENTAL => 'Admin Rental',
            self::ROLE_STAFF => 'Staf',
            default => 'Pelanggan',
        };
    }
}
