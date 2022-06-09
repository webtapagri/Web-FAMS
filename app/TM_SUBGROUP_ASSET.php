<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_SUBGROUP_ASSET extends Model
{
    protected $table = 'TM_SUBGROUP_ASSET';
    public $timestamps = false;

    protected $fillable = [
    	"id",
        "subgroup_code",
        "subgroup_description",
        "group_code"
    ]; 

    protected $primaryKey = 'id';

}
