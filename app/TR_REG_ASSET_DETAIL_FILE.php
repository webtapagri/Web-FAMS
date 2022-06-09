<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_REG_ASSET_DETAIL_FILE extends Model
{
    protected $table = 'TR_REG_ASSET_DETAIL_FILE';
    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany('App\TR_REG_ASSET_DETAIL');
    }

    protected $fillable = [
        "ASSET_PO_DETAIL_ID",
        "NO_REG_ITEM_FILE",
        "NO_REG",
        "JENIS_FOTO",
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
