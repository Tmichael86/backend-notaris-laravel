<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'pengeluaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_faktur',
        'jenis_pengeluaran_id',
        'jumlah',
        'tanggal_pengeluaran',
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

    protected $casts = [
        'tanggal_pengeluaran' => 'datetime:Y-m-d H:i:s',
    ];

    public $timestamps = false;
}
