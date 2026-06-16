<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiUmum extends Model
{
    protected $table = 'konfigurasi_umum';

    protected $fillable = [
        'alamat',
        'telp_rumah',
        'telp_pertama',
        'telp_kedua',
        'email',
        'notaris_bersangkutan',
        'ppat_bersangkutan',
        'besaran_nilai_tidak_kena_pajak',
        'pengecekan',
        'surat_kuasa_membebankan_hak_tanggungan',
        'ploting_validasi',
        'harga_beli_materai',
        'harga_jual_materai',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
    ];

    protected $hidden = [];

    public $timestamps = false;
}
