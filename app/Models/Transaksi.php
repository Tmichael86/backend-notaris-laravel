<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'transaksi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_akta',
        'tanggal_daftar',
        'tanggal_selesai',
        'biaya_layanan',
        'biaya_lainnya',
        'keterangan',
        'sub_total',
        'potongan_biaya',
        'total',
        'jatuh_tempo',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',

        'locked_at',
        'locked_by',

        // => Pajak
        'jenis_pajak_id',
        'acuan_hitung_pajak',
        'nilai_pengurang',
        'besaran_tidak_kena_pajak',
        'besaran_pajak_pihak_pertama',
        'besaran_pajak_pihak_kedua',

        // => Form Tambahan Notari
        'judul',
        'nomor_akta',
        'tanggal_akta',

        // => Foreign Key
        'jenis_pembayaran_id',
        'pemohon_id',
        'jenis_pekerjaan_id',
        'pekerjaan_id',
        'kategori_pekerjaan_id',
        'status_id',
        'petugas_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    public $timestamps = false;

    public function materai()
    {
        return $this->belongsTo(Materai::class, 'materia_id', 'id');
    }
}
