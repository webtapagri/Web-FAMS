<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_REG_ASSET extends Model
{
    protected $table = 'TR_REG_ASSET';
    public $timestamps = false;

    protected $fillable = [
        "ID",
        "NO_REG",
        "TYPE_TRANSAKSI",
        "BUSINESS_AREA",
        "TANGGAL_REG",
        "NO_PO",
        "TANGGAL_PO",
        "KODE_VENDOR",
        "NAMA_VENDOR",
        "CREATED_BY",
        "CREATED_AT",
        "UPDATED_BY",
        "UPDATED_AT"
    ]; 

}
