<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketLangganan extends Model
{
    protected $table = 'tb_paket_langganan';

    protected $primaryKey = 'id_paket';

    public $timestamps = false;

    protected $fillable = [
        'nama_paket',
        'harga_bulanan',
        'maks_mobil',
        'maks_pengguna',
        'deskripsi',
    ];

    protected $casts = [
        'harga_bulanan' => 'float',
    ];

    public function langganan()
    {
        return $this->hasMany(Langganan::class, 'id_paket', 'id_paket');
    }
}
