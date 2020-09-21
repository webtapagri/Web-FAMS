<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_LOG_MSTR_ASSET_FILE extends Model
{
    protected $table = 'TR_LOG_MSTR_ASSET_FILE';
    public $timestamps = false;

    protected $fillable = [
        "ID_LOG",
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

    protected $primaryKey = 'ID_LOG';

}
