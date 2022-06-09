<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_MSTR_ASSET_FILE extends Model
{
    protected $table = 'TM_MSTR_ASSET_FILE';
    public $timestamps = false;

    protected $fillable = [
        "ID",
        "KODE_ASSET",
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
        "UPDATED_AT",
    ]; 

    protected $primaryKey = 'ID';

}
