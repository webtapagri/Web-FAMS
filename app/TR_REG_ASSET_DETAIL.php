<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_REG_ASSET_DETAIL extends Model
{
    protected $table = 'TR_REG_ASSET_DETAIL';
    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany('App\TR_REG_ASSET_DETAIL_PO');
    }

    protected $fillable = [
        "ASSET_PO_ID",
        "NO_REG_ITEM",
        "NO_REG",
        "ITEM_PO",
        "KODE_MATERIAL" ,
        "NAMA_MATERIAL",
        "NO_PO",
        "KODE_JENIS_ASSET",
        "JENIS_ASSET",
        "GROUP",
        "SUB_GROUP",
        "ASSET_CLASS",
        "NAMA_ASSET",
        "MERK",
        "SPESIFIKASI_OR_WARNA",
        "NO_RANGKA_OR_NO_SERI",
        "NO_MESIN_OR_IMEI",
        "NO_POLISI",
        "LOKASI_BA_CODE",
        "LOKASI_BA_DESCRIPTION",
        "TAHUN_ASSET",
        "KONDISI_ASSET",
        "INFORMASI",
        "NAMA_PENANGGUNG_JAWAB_ASSET",
        "JABATAN_PENANGGUNG_JAWAB_ASSET",
        "CREATED_BY",
        "CREATED_AT",
        "UPDATED_BY",
        "UPDATED_AT"
    ]; 

}
