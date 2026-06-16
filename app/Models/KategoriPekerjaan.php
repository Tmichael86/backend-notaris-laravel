<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPekerjaan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'pekerjaan_kategori';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
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
