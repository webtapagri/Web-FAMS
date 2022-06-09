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
	
	public function __construct(string $sql, $harga, $nilai_buku)
	{
        $this->sql = $sql;
        $this->harga = $harga;
        $this->nilai_buku = $nilai_buku;
	}
	
    public function view():View
    {
        $dt = DB::select($this->sql);
        $data['report'] = $dt;
        $data['harga']= $this->harga;
        $data['nilai_buku']= $this->nilai_buku;
        return view('report.list_asset_export',$data);
    }
}
