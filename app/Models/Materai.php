<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materai extends Model
{
    protected $table = 'materais';

    protected $fillable = [
        'date',
        'materai_masuk',
        'materai_keluar',
        'stok_materai',
        'keterangan',
        'is_transaksi',
        'is_add_materai',
        'created_by',
        'updated_by',
        'status',
        'petugas_id',
        'jenis_pekerjaan_id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [];

    public $timestamps = false;

    public function transaction()
    {
        return $this->hasOne(Transaksi::class, 'materai_id', 'id');
    }
}
