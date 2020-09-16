<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditField extends Model
{
    protected $table = "TBM_EDITFIELD";
    public $timestamps = false;

    protected $fillable = [
        "id",
        "role_id",
        "tablename",
        "fieldname",
        "editable",
        "deleted",
        "created_by",
        "updated_by"
    ];
}
