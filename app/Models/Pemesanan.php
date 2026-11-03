<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $table = 'tb_pemesanan';

    protected $primaryKey = 'id_pemesanan';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'id_pelanggan',
        'id_mobil',
        'id_supir',
        'id_staff',
        'kode_pemesanan',
        'tanggal_pesan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi_jemput',
        'lokasi_kembali',
        'lama_sewa_hari',
        'total_biaya',
        'status',
    ];

    protected $casts = [
        'tanggal_pesan' => 'datetime',
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'total_biaya' => 'float',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'id_mobil', 'id_mobil');
    }

    public function supir()
    {
        return $this->belongsTo(Supir::class, 'id_supir', 'id_supir');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'id_staff', 'id_user');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_pemesanan', 'id_pemesanan');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'id_pemesanan', 'id_pemesanan');
    }
}
