<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'TBM_MODULE';
    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "sort",
        "icon",
        "created_by",
        "updated_by",
        "deleted"
    ]; 

}
