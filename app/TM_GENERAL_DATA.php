<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_GENERAL_DATA extends Model
{
    protected $table = 'TM_GENERAL_DATA';
    public $timestamps = false;

    protected $fillable = [
    	"id",
        "general_code",
        "description_code",
        "description",
        "status"
    ]; 

    protected $primaryKey = 'id';

}
