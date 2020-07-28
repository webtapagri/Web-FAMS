<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\FamsEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\TR_REG_ASSET_DETAIL;
use API;
use App\Jobs\SendEmail;

class FamsEmailController extends Controller
{

	public function index(Request $request)
	{
		$req = $request->all();
		$no_registrasi = $req['noreg'];
		$document_code = str_replace("-", "/", $no_registrasi); 
		$jenis_document = "";
		$kolom_mutasi = "";
		$join_mutasi = "";
		
		if (strpos($document_code, 'PDFA') !== false) 
		{
			$jenis_document = 'PENDAFTARAN';
		}
		else if (strpos($document_code, 'DSPA') !== false) 
		{ 
			$jenis_document = "DISPOSAL";}
		else
		{
			$jenis_document = "MUTASI";
			$kolom_mutasi = ", d.TUJUAN, e.DESCRIPTION as LOKASI_TUJUAN_DESC, d.KODE_ASSET_AMS_TUJUAN ";
			$join_mutasi = "LEFT JOIN TR_MUTASI_ASSET_DETAIL d ON a.document_code = d.NO_REG LEFT JOIN TM_GENERAL_DATA e ON d.TUJUAN = e.DESCRIPTION_CODE AND e.GENERAL_CODE = 'PLANT'";
		}
	
		// 1. DATA ASSET
		$sql = " SELECT distinct(a.document_code) as document_code, a.KODE_MATERIAL, a.NAMA_MATERIAL, a.LOKASI_BA_CODE, a.PO_TYPE, a.NO_PO, a.BA_PEMILIK_ASSET, b.DESCRIPTION as LOKASI_BA_CODE_DESC, c.DESCRIPTION as BA_PEMILIK_ASSET_DESC, a.TAHUN_ASSET as TAHUN_PEROLEHAN, a.KODE_ASSET_AMS as KODE_ASSET_AMS "
				.$kolom_mutasi."  
				FROM v_email_approval a 
				LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
				LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'"
				.$join_mutasi ."
				WHERE a.document_code = '{$document_code}'
				order by a.nama_material ";
		$dt = DB::SELECT($sql);

		// 2. HISTORY APPROVAL 
		$sql2 = " SELECT a.*, a.date AS date_create FROM v_history a WHERE a.document_code = '{$document_code}' ORDER BY date_create ";
		$dt_history_approval = DB::SELECT($sql2);

		$row = DB::table('v_email_approval')
                     ->where('document_code','LIKE','%'.$document_code.'%')
                     ->get();
		
        $HARGA_PEROLEHAN = $this->get_harga_perolehan($row);
		$NILAI_BUKU = $this->get_nilai_buku($row);
		

		// 3. EMAIL TO
		$data = new \stdClass();
        $data->noreg = array($document_code,1,2);
        $data->jenis_pemberitahuan = $jenis_document;
        $data->sender = 'TAP Agri';
		$data->datax = $dt;
		$data->harga_perolehan = $HARGA_PEROLEHAN;
		$data->nilai_buku = $NILAI_BUKU;
		$data->history_approval = $dt_history_approval;
		$data->no_reg = $req['noreg'];

		// $sql3 = " SELECT b.name, b.email FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID WHERE a.document_code = '{$document_code}' AND status_approval = 'menunggu' "; //echo $sql3; die();
		$sql3 = " SELECT b.name, b.email, b.id as user_id, b.role_id, c.name as role_name FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID LEFT JOIN TBM_ROLE c ON b.role_id = c.id WHERE a.document_code = '{$document_code}' AND status_approval = 'menunggu' "; //echo $sql3; die();
		$dt_email_to = DB::SELECT($sql3);
		
		#1 IT@220719 
		if(!empty($dt_email_to))
		{
			foreach($dt_email_to as $k => $v)
			{
				$data->nama_lengkap = $v->name;
				$data->role_name = $v->role_name;
				$data->role_id = $v->role_id;
				$data->user_id = $v->user_id;
				
				dispatch((new SendEmail($v->email, $data))->onQueue('high'));	
				
				// Mail::to($v->email)
					// ->bcc('system.administrator@tap-agri.com')
					// ->send(new FamsEmail($data));
			}
		}
	}

	public function kirim_email()
	{
		$no_registrasi = $_GET['doc_code'];
		$document_code = str_replace("-", "/", $no_registrasi); 
		$jenis_document = "";
		
		if (strpos($document_code, 'PDFA') !== false) 
		{
			$jenis_document = 'PENDAFTARAN';
		}
		else if (strpos($document_code, 'DSPA') !== false) 
		{ 
			$jenis_document = "DISPOSAL";}
		else
		{
			$jenis_document = "MUTASI";
		}
	
		// 1. DATA ASSET
		$sql = " SELECT distinct(a.document_code) as document_code, a.KODE_MATERIAL, a.NAMA_MATERIAL, a.LOKASI_BA_CODE, a.PO_TYPE, a.NO_PO, a.BA_PEMILIK_ASSET, b.DESCRIPTION as LOKASI_BA_CODE_DESC, c.DESCRIPTION as BA_PEMILIK_ASSET_DESC, a.TAHUN_ASSET as TAHUN_PEROLEHAN, a.KODE_ASSET_AMS as KODE_ASSET_AMS    
					FROM v_email_approval a 
					LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
					LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'
					WHERE a.document_code = '{$document_code}'
					order by a.nama_material ";
		$dt = DB::SELECT($sql);

		// 2. HISTORY APPROVAL 
		$sql2 = " SELECT a.*, a.date AS date_create FROM v_history a WHERE a.document_code = '{$document_code}' ORDER BY date_create ";
		$dt_history_approval = DB::SELECT($sql2);

		$row = DB::table('v_email_approval')
                     ->where('document_code','LIKE','%'.$document_code.'%')
                     ->get();
		
        $HARGA_PEROLEHAN = $this->get_harga_perolehan($row);
		$NILAI_BUKU = $this->get_nilai_buku($row);
		

		// 3. EMAIL TO
		$data = new \stdClass();
        $data->noreg = array($document_code,1,2);
        $data->jenis_pemberitahuan = $jenis_document;
        $data->sender = 'TAP Agri';
		$data->datax = $dt;
		$data->harga_perolehan = $HARGA_PEROLEHAN;
		$data->nilai_buku = $NILAI_BUKU;
        $data->history_approval = $dt_history_approval;

		$sql3 = " SELECT b.name, b.email FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID WHERE a.document_code = '{$document_code}' AND status_approval = 'menunggu' "; //echo $sql3; die();
		$dt_email_to = DB::SELECT($sql3);
		
		#1 IT@220719 
		if(!empty($dt_email_to))
		{
			foreach($dt_email_to as $k => $v)
			{
				$data->nama_lengkap = $v->name;
				
				dispatch((new SendEmail($v->email, $data))->onQueue('high'));	
				
				// Mail::to($v->email)
					// ->bcc('system.administrator@tap-agri.com')
					// ->send(new FamsEmail($data));
			}
		}
	}

	public function showToken()
	{
      echo csrf_token(); 
	}
	
	function get_harga_perolehan($row)
    {
        $nilai = array();

        for($i=0;$i<count($row);$i++){
            $BUKRS = substr($row[$i]->BA_PEMILIK_ASSET,0,2);

            $YEAR = date('Y');

            $ANLN1 = $this->get_anln1($row[$i]->KODE_ASSET_SAP);
            
            if( $row[$i]->KODE_ASSET_SUBNO_SAP == '') 
            {
                $ANLN2 = '0000';
            }
            else
            {
                $ANLN2 = $row[$i]->KODE_ASSET_SUBNO_SAP;
            }
            
            

            $service = API::exec(array(
                'request' => 'GET',
                'host' => 'ldap',
                'method' => "assets_price?BUKRS={$BUKRS}&ANLN1={$ANLN1}&ANLN2=$ANLN2&AFABE=1&GJAHR={$YEAR}", 
                //'method' => "assets_price?BUKRS=41&ANLN1=000060100612&ANLN2=0000&AFABE=1&GJAHR=2019", 
                //http://tap-ldapdev.tap-agri.com/data-sap/assets_price?BUKRS=41&ANLN1=000060100612&ANLN2=0000&AFABE=1&GJAHR=2019
            ));
            
            $data = $service;

            if(!empty($data))
            {
                $nilai[] = $data*100;
            }
            else
            {
                $nilai[] = 0;
            }

		// dd($service,$BUKRS,$ANLN1,$ANLN2,$row->KODE_ASSET_SAP);
        }

        // dd($nilai);
        return $nilai;

    	
    }

    function get_nilai_buku($row)
    {
        $nilai = array();

        for($i=0;$i<count($row);$i++){
            $BUKRS = substr($row[$i]->BA_PEMILIK_ASSET,0,2);

            $YEAR = date('Y');

            $ANLN1 = $this->get_anln1($row[$i]->KODE_ASSET_SAP);
            
            if( $row[$i]->KODE_ASSET_SUBNO_SAP == '') 
            {
                $ANLN2 = '0000';
            }
            else
            {
                $ANLN2 = $row[$i]->KODE_ASSET_SUBNO_SAP;
            }
            
            

            $service = API::exec(array(
                'request' => 'GET',
                'host' => 'ldap',
                'method' => "assets_bookvalue?BUKRS={$BUKRS}&ANLN1={$ANLN1}&ANLN2=$ANLN2&AFABE=1&GJAHR={$YEAR}", 
            ));
            
            $data = $service;

            if(!empty($data))
            {
                $nilai[] = $data*100;
            }
            else
            {
                $nilai[] = 0;
            }
        }

        return $nilai;

    	
    }

    function get_anln1($kode)
    {
    	$total = strlen($kode); //12 DIGIT

    	if( $total == 8 )
    	{
    		$ksap = '0000'.$kode.'';
    	}
    	elseif( $total == 7 )
    	{
    		$ksap = '00000'.$kode.'';
    	}
    	else
    	{
    		$ksap = '0000'.$kode.'';
    	}
    	return $ksap;
    }

}