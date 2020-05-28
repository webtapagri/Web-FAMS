<?php

namespace App\Exports;

// use App\TM_MSTR_ASSET;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
	
	use Exportable;
	
	public function __construct(string $sql)
	{
		$this->sql = $sql;
	}
	
    public function view():View
    {
        $dt = DB::select($this->sql);
        if(!empty($dt))
        {
            foreach( $dt as $k => $v )
            {
                $result[] = array(
                    'KODE_ASSET_AMS' => $v->KODE_ASSET_AMS,
                    'KODE_ASSET_SAP' => $v->KODE_ASSET_SAP,
                    'KODE_ASSET_CONTROLLER' => $v->KODE_ASSET_CONTROLLER,
                    'BA_PEMILIK_ASSET' => $v->BA_PEMILIK_ASSET,
                    'NAMA_PT_PEMILIK' => $v->NAMA_PT_PEMILIK,
                    'LOKASI_BA_CODE' => $v->LOKASI_BA_CODE,
                    'LOKASI_BA_DESCRIPTION' => $v->LOKASI_BA_DESCRIPTION,
                    'NAMA_ASSET' => $v->NAMA_ASSET,
                    'MERK' => $v->MERK,
                    'SPESIFIKASI_OR_WARNA' => $v->SPESIFIKASI_OR_WARNA,
                    'NO_RANGKA_OR_NO_SERI' => $v->NO_RANGKA_OR_NO_SERI,
                    'NO_MESIN_OR_IMEI' => $v->NO_MESIN_OR_IMEI,
                    'NO_POLISI' => $v->NO_POLISI,
                    'NAMA_PENANGGUNG_JAWAB_ASSET' => $v->NAMA_PENANGGUNG_JAWAB_ASSET,
                    'JABATAN_PENANGGUNG_JAWAB_ASSET' => $v->JABATAN_PENANGGUNG_JAWAB_ASSET,
                    'NO_PO' => $v->NO_PO,
                    'NAMA_VENDOR' => $v->NAMA_VENDOR,
                    'INFORMASI' => $v->INFORMASI,
                    'FOTO_ASET' => $v->FOTO_ASET,
                    'FOTO_SERI' => $v->FOTO_SERI,
                    'FOTO_MESIN' => $v->FOTO_MESIN,
                    'ASSET_CLASS' => $v->ASSET_CLASS,
                    'TAHUN_ASSET' => $v->TAHUN_ASSET,
                    'BOOK_DEPREC_01' => $v->BOOK_DEPREC_01,
                    'COST_CENTER' => $v->COST_CENTER,
                    'QUANTITY_ASSET_SAP' => $v->QUANTITY_ASSET_SAP,
                    'UOM_ASSET_SAP' => $v->UOM_ASSET_SAP,
                    'JENIS_ASSET' => $v->JENIS_ASSET_NAME,
                    'GROUP' => $v->GROUP_NAME,
                    'SUB_GROUP' => $v->SUB_GROUP_NAME,
                    'KONDISI_ASSET' => $v->KONDISI_ASSET,
                    'STATUS_DOCUMENT' => $v->STATUS_DOCUMENT
                ); 
            }
        }
        $data['report'] = (object)$result;
        // dd($data['report']);
        return view('report.list_asset',$data);
        // return view('report.list_asset')->with(compact('data'));
    }
}
