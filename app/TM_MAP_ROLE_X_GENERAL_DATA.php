<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_MAP_ROLE_X_GENERAL_DATA extends Model
{
    protected $table = 'TM_MAP_ROLE_X_GENERAL_DATA';
    public $timestamps = false;

    protected $fillable = [
    	"id",
        "code_role_x_general_data",
        "id_role",
        "role_name",
        "description_code",
        "description",
        "id_user",
        "username",
    ]; 

    protected $primaryKey = 'id';

}
