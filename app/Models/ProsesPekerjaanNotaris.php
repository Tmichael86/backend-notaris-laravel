<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProsesPekerjaanNotaris extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'pekerjaan_notaris_proses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'pekerjaan_notaris_id',
        'detail',
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
