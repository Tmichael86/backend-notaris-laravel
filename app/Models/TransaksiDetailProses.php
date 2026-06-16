<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetailProses extends Model
{
    protected $table = 'transaksi_detail_proses';

    protected $fillable = [
        'transaksi_id',
        'prosesId',
        'jenis_pekerjaan_id',
        'pekerjaanNama',
        'kategoriNama',
        'prosesNama',
        'atribut',
        'catatan',
        'isValidate',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $hidden = [];

    public $timestamps = true;
}
