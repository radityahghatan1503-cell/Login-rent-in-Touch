<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'tb_pembayaran';

    protected $primaryKey = 'id_pembayaran';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'id_pemesanan',
        'jenis',
        'metode',
        'jumlah',
        'tanggal_bayar',
        'bukti_bayar',
        'status',
    ];

    protected $casts = [
        'jumlah' => 'float',
        'tanggal_bayar' => 'datetime',
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
