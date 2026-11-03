<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'tb_pengembalian';

    protected $primaryKey = 'id_pengembalian';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'id_pemesanan',
        'tanggal_kembali',
        'kondisi_mobil',
        'keterlambatan_jam',
        'denda',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kembali' => 'datetime',
        'denda' => 'float',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }
}
