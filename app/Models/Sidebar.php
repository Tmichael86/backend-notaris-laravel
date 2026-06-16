<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sidebar extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'sidebar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sidebar_parent_id',
        'sidebar_nama',
        'sidebar_route',
        'sidebar_kode',
        'sidebar_icon',
        'sidebar_index',
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
