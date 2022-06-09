<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_ASSET_CONTROLLER_MAP extends Model
{
    protected $table = 'TM_ASSET_CONTROLLER_MAP';
    public $timestamps = false;

    protected $fillable = [
    	"id",
        "map_code",
        "jenis_asset_code",
        "group_code",
        "subgroup_code",
        "asset_ctrl_code",
        "asset_ctrl_description",
    ]; 

    protected $primaryKey = 'id';

}
