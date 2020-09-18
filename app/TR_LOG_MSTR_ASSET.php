<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_LOG_MSTR_ASSET extends Model
{
    protected $table = 'TR_LOG_MSTR_ASSET';
    public $timestamps = false;

    protected $fillable = [
        "ID",
    	"KODE_ASSET_AMS",
        "NO_REG_ITEM",
        "NO_REG",
        "ITEM_PO",
        "KODE_MATERIAL",
        "NAMA_MATERIAL",
        "KODE_MATERIAL",
        "NO_PO",
        "BA_PEMILIK_ASSET",
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
        "ASSET_CONTROLLER",
        "KODE_ASSET_CONTROLLER",
        "NAMA_ASSET_1",
        "NAMA_ASSET_2",
        "NAMA_ASSET_3",
        "QUANTITY_ASSET_SAP",
        "UOM_ASSET_SAP",
        "CAPITALIZED_ON",
        "DEACTIVATION_ON",
        "COST_CENTER",
        "BOOK_DEPREC_01",
        "FISCAL_DEPREC_15",
        "GROUP_DEPREC_30",
        "DELETED",
        "CREATED_BY",
        "CREATED_AT",
        "UPDATED_BY",
        "UPDATED_AT",
        "KODE_ASSET_SAP",
        "KODE_ASSET_SUBNO_SAP",
        "GI_NUMBER",
        "GI_YEAR",
    ]; 

    protected $primaryKey = 'ID';

}
