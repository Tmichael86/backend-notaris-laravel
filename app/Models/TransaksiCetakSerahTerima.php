<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiCetakSerahTerima extends Model
{
    protected $table = "transaksi_cetak_serah_terima";
    protected $fillable = [
        "no_transaksi",
        "list_keperluan",
        "uraian",
        "keperluan",
        "created_by",
        "updated_by",
        "status"
    ];

    protected $hidden = [];
    public $timestamps = false;
}
