<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_REG_ASSET_DETAIL_PO extends Model
{
    protected $table = 'TR_REG_ASSET_DETAIL_PO';
    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany('App\TR_REG_ASSET');
    }

    protected $fillable = [
        "ASSET_REG_ID",
        "NO_REG",
        "NO_PO",
        "ITEM_PO",
        "KODE_MATERIAL",
        "NAMA_MATERIAL",
        "QUANTITY_PO",
        "QUANTITY_SUBMIT",
        "CREATED_BY",
        "CREATED_AT",
        "UPDATED_BY",
        "UPDATED_AT"
    ]; 
}
