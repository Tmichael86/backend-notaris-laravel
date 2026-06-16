<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AtributPekerjaanNotaris extends Model
{
    protected $table = 'pekerjaan_notaris_atributs';

    protected $fillable = [
        'pekerjaan_notaris_id',
        'proses_pekerjaan_notaris_id',
        'atribut',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'status',
    ];

    protected $hidden = [];

    public $timestamps = false;

    public function transaksi(): BelongsToMany
    {
        return $this->belongsToMany(Transaksi::class, 'transaksi_detail_atribut_notaris', foreignPivotKey: 'pekerjaan_atribut_id', relatedPivotKey: 'transaksi_id')->withPivot('pekerjaan_notaris_id', 'pekerjaan_proses_id', 'created_by', 'updated_by', 'status')->withTimestamps();
    }
}
