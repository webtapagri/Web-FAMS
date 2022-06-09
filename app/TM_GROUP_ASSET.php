<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_GROUP_ASSET extends Model
{
    protected $table = 'TM_GROUP_ASSET';
    public $timestamps = false;

    protected $fillable = [
    	"id",
        "group_code",
        "group_description",
        "jenis_asset_code"
    ]; 

    protected $primaryKey = 'id';

}
