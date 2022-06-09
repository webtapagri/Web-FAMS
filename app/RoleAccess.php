<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleAccess extends Model
{
    protected $table = "TBM_ROLE_ACCESS";
    public $timestamps = false;

    protected $fillable = [
        "id",
        "role_id",
        "module_id",
        "menu_id",
        "create",
        "read",
        "update",
        "delete",
        "deleted",
        "created_by",
        "updated_by"
    ];
}
