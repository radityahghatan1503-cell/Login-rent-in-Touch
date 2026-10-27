<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'tb_tenant';

    protected $primaryKey = 'id_tenant';

    protected $fillable = [
        'nama_rental',
        'subdomain',
        'alamat',
        'telepon',
        'email',
        'logo',
        'status',
    ];
}