<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'tb_pelanggan';

    protected $primaryKey = 'id_pelanggan';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'id_user',
        'nik',
        'nama_lengkap',
        'telepon',
        'alamat',
        'no_sim',
        'foto_ktp',
        'foto_sim',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
