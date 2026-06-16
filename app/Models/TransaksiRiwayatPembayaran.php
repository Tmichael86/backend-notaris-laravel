<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiRiwayatPembayaran extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'transaksi_riwayat_pembayaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal_pembayaran',
        'no_transaksi',
        'jumlah_dibayar',
        'pembayaran_ke',
        'keterangan',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    public $timestamps = false;
}
