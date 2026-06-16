<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPajak extends Model
{
    protected $table = 'jenis_pajak';

    protected $fillable = [
        'nama_pajak',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $hidden = [];

    public $timestamps = false;
}
