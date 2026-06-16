<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarAkses extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'sidebar_akses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sidebar_id',
        'group_id',
        'read',
        'create',
        'update',
        'delete',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    public $timestamps = false;
}
