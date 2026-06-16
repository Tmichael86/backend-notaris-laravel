<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiMaterai extends Model
{
    protected $table = 'transaksi_materai';

    protected $fillable = [
        'materai_id',
        'transaksi_id',
        'status',
    ];

    protected $hidden = [];

    public $timestamps = false;
}
