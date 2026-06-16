<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaPekerjaanNotaris extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'pekerjaan_notaris_harga';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'harga',
        'kategori_pekerjaan_id',
        'pekerjaan_notaris_id',
        'estimasi_waktu',
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
