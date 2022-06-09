<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'TBM_ROLE';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'created_by',
        'created_on',
        'updated_on',
        'updated_by',
        'deleted'
    ];
}
