<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendapatan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = "pendapatan";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "tanggal",
        "penghasilan",
        "pengeluaran",
        "pendapatan",
        "saldo",
        "jenis_pembayaran_id",
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
    ];

    public $timestamps = false;

    public function JenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class, 'jenis_pembayaran_id');
    }
}
