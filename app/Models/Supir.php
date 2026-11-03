<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supir extends Model
{
    protected $table = 'tb_supir';

    protected $primaryKey = 'id_supir';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'nama_supir',
        'telepon',
        'no_sim',
        'tarif_per_hari',
        'status',
    ];

    protected $casts = [
        'tarif_per_hari' => 'float',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_supir', 'id_supir');
    }
}
