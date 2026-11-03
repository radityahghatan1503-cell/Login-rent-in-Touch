<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriMobil extends Model
{
    protected $table = 'tb_kategori_mobil';

    protected $primaryKey = 'id_kategori';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'nama_kategori',
        'deskripsi',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function mobil()
    {
        return $this->hasMany(Mobil::class, 'id_kategori', 'id_kategori');
    }
}
