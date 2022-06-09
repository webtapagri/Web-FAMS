<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_REG_ASSET_FILE extends Model
{
    protected $table = 'TR_REG_ASSET_FILE';
    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany('App\TR_REG_ASSET');
    }

    protected $fillable = [
        "ID",
        "ASSET_REG_ID",
        "NO_REG",
        "NO_FILE",
        "FILENAME",
        "DOC_SIZE",
        "FILE_CATEGORY",
        "FILE_UPLOAD",
        "CREATED_BY",
        "CREATED_AT",
        "UPDATED_BY",
        "UPDATED_AT"
    ]; 

}
