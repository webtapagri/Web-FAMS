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
	
	public function __construct(string $sql, $harga)
	{
        $this->sql = $sql;
        $this->harga = $harga;
	}
	
    public function view():View
    {
        $dt = DB::select($this->sql);
        $data['report'] = $dt;
        $data['harga']= $this->harga;
        return view('report.list_asset_export',$data);
    }
}
