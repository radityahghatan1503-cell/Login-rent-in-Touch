<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Langganan extends Model
{
    protected $table = 'tb_langganan';

    protected $primaryKey = 'id_langganan';

    public $timestamps = false;

    protected $fillable = [
        'id_tenant',
        'id_paket',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'id_tenant', 'id_tenant');
    }

    public function paket()
    {
        return $this->belongsTo(PaketLangganan::class, 'id_paket', 'id_paket');
    }
}
