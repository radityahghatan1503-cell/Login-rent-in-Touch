<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tb_tenant';

    protected $primaryKey = 'id_tenant';

    public const UPDATED_AT = null;

    protected $fillable = [
        'nama_rental',
        'subdomain',
        'alamat',
        'telepon',
        'email',
        'logo',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_tenant', 'id_tenant');
    }

    public function mobil()
    {
        return $this->hasMany(Mobil::class, 'id_tenant', 'id_tenant');
    }

    public function kategori()
    {
        return $this->hasMany(KategoriMobil::class, 'id_tenant', 'id_tenant');
    }

    public function supir()
    {
        return $this->hasMany(Supir::class, 'id_tenant', 'id_tenant');
    }

    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'id_tenant', 'id_tenant');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_tenant', 'id_tenant');
    }

    public function langganan()
    {
        return $this->hasMany(Langganan::class, 'id_tenant', 'id_tenant');
    }
}
