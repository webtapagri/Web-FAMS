<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_JENIS_ASSET extends Model
{
    protected $table = 'TM_JENIS_ASSET';
    public $timestamps = false;

    protected $fillable = [
    	"id",
        "jenis_asset_code",
        "jenis_asset_description"
    ]; 

    protected $primaryKey = 'id';

}
