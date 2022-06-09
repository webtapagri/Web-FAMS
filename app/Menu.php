<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = "TBM_MENU";
    public $timestamps = false;

    protected $fillable = [
        "id",
        "module_id",
        "name",
        "url",
        "sort",
        "deleted",
        "created_by",
        "updated_by"
    ];
}
