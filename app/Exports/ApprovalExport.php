<?php

namespace App\Exports;

// use App\TM_MSTR_ASSET;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ApprovalExport implements FromView
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
        $data['report'] = $dt;
        return view('report.list_history_approval_export',$data);
    }
}
