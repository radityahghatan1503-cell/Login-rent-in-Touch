<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    protected $table = 'tb_mobil';

    protected $primaryKey = 'id_mobil';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'id_kategori',
        'plat_nomor',
        'merek',
        'model',
        'tahun',
        'warna',
        'transmisi',
        'bahan_bakar',
        'kapasitas_penumpang',
        'harga_per_hari',
        'foto',
        'status',
    ];

    protected $casts = [
        'harga_per_hari' => 'float',
        'kapasitas_penumpang' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriMobil::class, 'id_kategori', 'id_kategori');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_mobil', 'id_mobil');
    }
}
