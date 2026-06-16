<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtributPekerjaanPPAT extends Model
{
    protected $table = 'pekerjaan_ppat_atributs';

    protected $fillable = [
        'pekerjaan_ppat_id',
        'proses_pekerjaan_ppat_id',
        'atribut',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
    ];

    public $timestamps = false;

    // public function transaksi()
    // {

    //     return $this->belongsToMany(Transaksi::class, 'transaksi_detail_atribut_ppat', 'transaksi_id', 'atribut_id')->withPivot('status_atribut');

    // }
}
