<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\FamsEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\TR_REG_ASSET_DETAIL;
use API;
use App\Jobs\SendEmail;
use GuzzleHttp\Client;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\RestuqueController;
use Redirect;
use Illuminate\Support\Facades\Log;

class FamsEmailController extends Controller
{
	protected $ApprovalController;
    public function __construct(ApprovalController $ApprovalController)
    {
        $this->ApprovalController = $ApprovalController;
		
		if(env('APP_ENV') == 'production'){
			$this->restuque = 'http://apis.tap-agri.com/rtq-msa-approval/';
		}else{
			$this->restuque = 'http://apisqa.tap-agri.com/rtq-msa-qa-approval/';
		}
    }

	public function index(Request $request)
	{
		$req = $request->all();
		$no_registrasi = $req['noreg'];
		$document_code = str_replace("-", "/", $no_registrasi); 
		$jenis_document = "";
		$kolom_mutasi = "";
		$kolom_pendaftaran = "";
		$join_mutasi = "";
		$join_pendaftaran = "";
		
		if (strpos($document_code, 'PDFA') !== false) 
		{
			$jenis_document = 'PENDAFTARAN';
			$kolom_pendaftaran = ' ,d.QUANTITY_SUBMIT as QTY';
			$join_pendaftaran = ' LEFT JOIN  TR_REG_ASSET_DETAIL_PO d  ON d.NO_REG = a.document_code ';
		}
		else if (strpos($document_code, 'DSPA') !== false) 
		{ 
			$jenis_document = "DISPOSAL";}
		else
		{
			$jenis_document = "MUTASI";
			$kolom_mutasi = ", d.TUJUAN, e.DESCRIPTION as LOKASI_TUJUAN_DESC, d.KODE_ASSET_AMS_TUJUAN ";
			$join_mutasi = "LEFT JOIN TR_MUTASI_ASSET_DETAIL d ON a.document_code = d.NO_REG AND d.KODE_ASSET_AMS = a.KODE_ASSET_AMS LEFT JOIN TM_GENERAL_DATA e ON d.TUJUAN = e.DESCRIPTION_CODE AND e.GENERAL_CODE = 'PLANT'";
		}
	
		// 1. DATA ASSET
		// $sql = " SELECT distinct(a.document_code) as document_code, a.KODE_MATERIAL, a.NAMA_MATERIAL, a.LOKASI_BA_CODE, a.PO_TYPE, a.NO_PO, a.BA_PEMILIK_ASSET, b.DESCRIPTION as LOKASI_BA_CODE_DESC, c.DESCRIPTION as BA_PEMILIK_ASSET_DESC, a.TAHUN_ASSET as TAHUN_PEROLEHAN, a.KODE_ASSET_AMS as KODE_ASSET_AMS "
		$sql = " SELECT distinct(a.document_code) as document_code, a.KODE_MATERIAL, a.NAMA_MATERIAL, a.LOKASI_BA_CODE, a.PO_TYPE, a.NO_PO, a.BA_PEMILIK_ASSET, b.DESCRIPTION as LOKASI_BA_CODE_DESC, c.DESCRIPTION as BA_PEMILIK_ASSET_DESC, a.TAHUN_ASSET as TAHUN_PEROLEHAN "
				.$kolom_pendaftaran.$kolom_mutasi."  
				FROM v_email_data_approval a 
				LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
				LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'"
				.$join_pendaftaran 
				.$join_mutasi ."
				WHERE a.document_code = '{$document_code}'
				order by a.nama_material ";
		$dt = DB::SELECT($sql);
		// dd($sql);

		// 2. HISTORY APPROVAL 
		$sql2 = " SELECT a.*, a.date AS date_create FROM v_history a WHERE a.document_code = '{$document_code}' ORDER BY date_create ";
		$dt_history_approval = DB::SELECT($sql2);

		// $row = DB::table('v_email_approval')
		$row = DB::table('v_email_data_approval')
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
		$data->message = "";
		// dd($data->datax);
		$document_code_new = $data->datax[0]->document_code;
		if($document_code_new != "")
		{
			$dc = base64_encode($document_code_new);
		}
		else
		{
			$dc = "";
		}
		$data->detail_url = url('/?noreg='.$dc.'');
		
		// $sql3 = " SELECT b.name, b.email FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID WHERE a.document_code = '{$document_code}' AND status_approval = 'menunggu' "; //echo $sql3; die();
		$sql3 = " SELECT b.name, b.email, b.id as user_id, b.role_id, c.name as role_name FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID LEFT JOIN TBM_ROLE c ON b.role_id = c.id WHERE a.document_code = '{$document_code}' AND status_approval = 'menunggu' "; //echo $sql3; die();
		$dt_email_to = DB::SELECT($sql3);

		$check_last_approve = " SELECT distinct b.workflow_group_name from TR_APPROVAL a left join TR_WORKFLOW_DETAIL b ON a.workflow_detail_code = b.workflow_detail_code
								where a.document_code = '{$document_code}' and execution_status = '' ";
		$lasthit = DB::SELECT($check_last_approve);

		$role = array();

		
		#1 IT@220719 
		if(!empty($dt_email_to))
		{
			foreach($dt_email_to as $k => $v)
			{
				$data->nama_lengkap = $v->name;
				$data->role_name = $v->role_name;
				$data->role_id = $v->role_id;
				$data->user_id = $v->user_id;
				$role[] = $v->role_name;

				$param_approve = array(
					'noreg' => $data->no_reg,
					'status' => 'A',
					'user_id' => $data->user_id,
					'id' => $data->user_id,
					'role_name' => $data->role_name,
					'role_id' => $data->role_id,
					'note' =>''
				);

				$param_reject = array(
					'noreg' => $data->no_reg,
					'status' => 'R',
					'user_id' => $data->user_id,
					'id' => $data->user_id,
					'role_name' => $data->role_name,
					'role_id' => $data->role_id,
					'note' => ''
				);


				$ida = urlencode(serialize($param_approve));
				$idr = urlencode(serialize($param_reject));
				$data->approve_url = url('/email_approve/?id='.$ida);
				$data->reject_url = url('/email_reject/?id='.$idr);

				// //cek email jenjang approve berikutny sebelum approve via email 
				// $last_email_approve = $this->ApprovalController->get_last_email_approve($data->no_reg);
				// if($last_email_approve <> 1){

				// 	$cek_email = $this->ApprovalController->get_email_next_approval($data->no_reg, $data->user_id);
				// 		if($cek_email['email'] == "" && $cek_email['next_approve'] !=""){
				// 			$data->message = "Email user <b>". $cek_email['next_approve'] ."</b> tidak tersedia, sehingga user bersangkutan tidak dapat menerima email pemberitahauan.";
				// 		}	
				// }

				// dispatch((new SendEmail($v->email, $data, $document_code))->onQueue('high'));
				dispatch((new SendEmail($v->email, $data))->onQueue('high'));
				
				// Mail::to($v->email)
					// ->bcc('system.administrator@tap-agri.com')
					// ->send(new FamsEmail($data));
			}
			
			$list_approve_role = array("VP", "MPC", "KADIV", "CEOR", "MDU", "DM", "MDD", "CFO");
			if(count(array_intersect($role,$list_approve_role)) > 0){
					$restuque = new RestuqueController;
					$restuque->hitRestuque($document_code);	
			}

			if(count($lasthit) == 1){
				if(strpos($lasthit[0]->workflow_group_name, 'Complete') !== false){
					$restuque = new RestuqueController;
					$restuque->hitRestuque($document_code);	
				}
			}
		}
	}

	public function approve()
	{		
		$request = new \Illuminate\Http\Request();

		$request->replace(['id' => $_GET['id']]);

		$id =  $_GET['id'];
		return $this->ApprovalController->update_status_disposal_email($id);

	}

	public function reject()
	{		
		$request = new \Illuminate\Http\Request();
		$request->replace(['id' => $_GET['id']]);
		$message = unserialize(urldecode($_GET['id']));
		// return Redirect::route('mail_response', array('message' => $req));		
		// return view('email.respon')->with(compact('req'));
		return view('email.respon')->with('message', $message);

		// $request->replace(['id' => $_GET['id']]);

		// $id =  $_GET['id'];
		// $this->ApprovalController->update_status_disposal_email($id);
	}

	public function respon($message)
	{			
		if (@unserialize($message)<>""){
			$message = unserialize(urldecode($message));
		}
		
		return view('email.respon')->with('message', $message);
	}

	public function kirim_email()
	{
		$request = new \Illuminate\Http\Request();

		$request->replace(['noreg' => $_GET['doc_code']]);
		$this->index($request);
		
	}

	public function showToken()
	{
      echo csrf_token(); 
	}
	
	function get_harga_perolehan($row)
    {
		$nilai = array();
		// dd($row);

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
				// $ANLN2 = $row[$i]->KODE_ASSET_SUBNO_SAP;
				$ANLN2 = str_pad($row[$i]->KODE_ASSET_SUBNO_SAP, 4, '0', STR_PAD_LEFT);
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
				// $ANLN2 = $row[$i]->KODE_ASSET_SUBNO_SAP;
				$ANLN2 = str_pad($row[$i]->KODE_ASSET_SUBNO_SAP, 4, '0', STR_PAD_LEFT);
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



	public function getDataApi()
	{
		$request = new \Illuminate\Http\Request();
		$request->replace(['noreg' => $_GET['doc_code']]);
		$document_code = $request['noreg'];
		// dd($document_code);

		// $document_code = $noreg;

		$no_doc = base64_encode($document_code);

		$hp = array();
		$nbv = array();
		$approve_url = array();
		$reject_url = array();
		
		if (strpos($document_code, 'PDFA') !== false) 
		{
			// HEADER PENDAFTARAN
			$sql_header = "SELECT
								a.NO_REG as document_number,
								date_format(a.CREATED_AT,'%Y-%m-%d') as document_date,
								c.name as creator,
								d.DESCRIPTION as plant_description,
								d.DESCRIPTION as company_name,
								CASE WHEN a.PO_TYPE = '0' THEN 'PO SAP'
								WHEN a.PO_TYPE = '1' THEN 'PO AMP'
								ELSE 'PO ASET LAIN' END as transaction_type,
								b.description as header_note,
								a.NO_PO as purchase_number
							FROM TR_REG_ASSET a
							left join TR_APPROVAL b ON a.NO_REG = b.document_code
							join TBM_USER c on c.id = a.CREATED_BY
							left join TM_GENERAL_DATA d on d.DESCRIPTION_CODE = a.BUSINESS_AREA and d.GENERAL_CODE = 'plant'
							where b.seq is null
							and a.NO_REG = '{$document_code}' ";
			$sql_detail = "SELECT distinct
									a.KODE_MATERIAL,
											a.NAMA_MATERIAL,
											concat(a.LOKASI_BA_CODE,' - ', b.DESCRIPTION) as LOKASI,
											d.QUANTITY_SUBMIT as QTY
							FROM v_email_data_approval a
									LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
									LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'
								LEFT JOIN  TR_REG_ASSET_DETAIL_PO d  ON d.NO_REG = a.document_code
							WHERE a.DOCUMENT_CODE = '{$document_code}'
							order by a.NAMA_MATERIAL";
			$sql_lampiran = " SELECT b.FILENAME as title ,concat('"
							.url('/approval/berkas-amp/')
							."','/{$no_doc}') as link_file"
							." FROM TR_REG_ASSET_FILE b WHERE b.no_reg = '{$document_code}' ";
			
		}
		else if (strpos($document_code, 'DSPA') !== false) 
		{ 
			$sql_header = "SELECT
								a.NO_REG as document_number,
								date_format(a.CREATED_AT,'%Y-%m-%d') as document_date,
								c.name as creator,
								d.DESCRIPTION as plant_description,
								d.DESCRIPTION as company_name,
								CASE WHEN a.TYPE_TRANSAKSI = 'hilang' THEN 'DISPOSAL HILANG'
								WHEN a.TYPE_TRANSAKSI = 'rusak' THEN 'DISPOSAL RUSAK'
								ELSE 'DISPOSAL PENJUALAN' END as transaction_type,
								b.description as header_note
							FROM TR_DISPOSAL_ASSET a
							left join TR_APPROVAL b ON a.NO_REG = b.document_code
							join TBM_USER c on c.id = a.CREATED_BY
							left join TM_GENERAL_DATA d on d.DESCRIPTION_CODE = a.BUSINESS_AREA and d.GENERAL_CODE = 'plant'
							where b.seq is null
							and a.NO_REG = '{$document_code}' ";
			$sql_detail = "SELECT distinct
									a.KODE_MATERIAL AS KODE_FAMS,
									a.NAMA_MATERIAL AS NAMA_ASET,
									concat(a.LOKASI_BA_CODE,' - ', b.DESCRIPTION) AS LOKASI,
									a.TAHUN_ASSET as TAHUN_PEROLEHAN
							FROM v_email_data_approval a
									LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
									LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'
							WHERE a.DOCUMENT_CODE = '{$document_code}'
							order by a.NAMA_MATERIAL";

			$sql_lampiran = " SELECT b.FILE_NAME as title ,concat('"
							.url('/approval/berkas-disposal/')
							."','/{$no_doc}') as link_file"
							." FROM TR_DISPOSAL_ASSET_FILE b WHERE b.no_reg = '{$document_code}' ";
        
		}
		else
		{
			$sql_header = "SELECT
								a.NO_REG as document_number,
								date_format(a.CREATED_AT,'%Y-%m-%d') as document_date,
								c.name as creator,
								d.DESCRIPTION as plant_description,
								d.DESCRIPTION as company_name,
								CASE WHEN a.JENIS_PENGAJUAN = '1' THEN 'MUTASI - ANTAR BA DALAM 1 PT'
									ELSE 'MUTASI - SEWA AMP ANTAR BA' END as transaction_type,
								b.description as header_note
							FROM TR_MUTASI_ASSET_DETAIL a
									left join TM_MSTR_ASSET e on e.NO_REG = a.NO_REG and a.KODE_ASSET_AMS = e.KODE_ASSET_AMS
									left join TR_APPROVAL b ON a.NO_REG = b.document_code
									join TBM_USER c on c.id = a.CREATED_BY
									left join TM_GENERAL_DATA d on d.DESCRIPTION_CODE = e.BA_PEMILIK_ASSET  and d.GENERAL_CODE = 'plant'
							where b.seq is null
							and a.NO_REG = '{$document_code}' ";
			$sql_detail = "SELECT distinct
									a.KODE_MATERIAL AS KODE_FAMS,
									a.NAMA_MATERIAL AS NAMA_ASET,
									concat(a.BA_PEMILIK_ASSET, ' - ',c.DESCRIPTION) AS PEMILIK_ASSET,
									concat(a.LOKASI_BA_CODE,' - ', b.DESCRIPTION) AS LOKASI_AWAL_ASSET,
									concat(d.TUJUAN, ' - ', e.DESCRIPTION) AS LOKASI_TUJUAN_ASSET,
									d.KODE_ASSET_AMS_TUJUAN AS KODE_FAMS_BARU
							FROM v_email_data_approval a
									LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
									LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'
									LEFT JOIN TR_MUTASI_ASSET_DETAIL d ON a.document_code = d.NO_REG AND d.KODE_ASSET_AMS = a.KODE_ASSET_AMS LEFT JOIN TM_GENERAL_DATA e ON d.TUJUAN = e.DESCRIPTION_CODE AND e.GENERAL_CODE = 'PLANT'
							WHERE a.DOCUMENT_CODE = '{$document_code}'
							order by a.NAMA_MATERIAL";
							
			$sql_lampiran = " SELECT b.FILE_NAME as title ,concat('"
							.url('/approval/berkas-mutasi/')
							."','/{$no_doc}') as link_file"
							." FROM TR_MUTASI_ASSET_FILE b WHERE b.no_reg = '{$document_code}' ";
        
		}

		//API HEADER
		$dt_header = DB::SELECT($sql_header);
		$header = array();
			foreach($dt_header as $qdh){
				foreach($qdh as $key => $value ){
					$header[] = array ('key' => strtolower($key),
										'label' => ucwords(str_replace("_"," ",$key)),
										'value' => $value);
				}
			}

		//API APPROVER
		$row = DB::table('v_email_data_approval')
		->where('document_code','LIKE','%'.$document_code.'%')
		->get();

		$HARGA_PEROLEHAN = $this->get_harga_perolehan($row);
		$NILAI_BUKU = $this->get_nilai_buku($row);
		// dd($NILAI_BUKU);

		
		//API DETAIL
		$dt_detail = DB::SELECT($sql_detail);
		$detail = array();
			$component = array() ;
			$values = array();

			$i = 0;
			foreach($dt_detail as $qdc){
				$j = 0;
				foreach($qdc as $key => $value ){
					if(count($component) > 0 && $j >0){
						if(in_array(strtolower($key),$component)){
							$values[$j]['value'][] = $value;
						}
						else{
							$values[$j]['value'][] = $value;
							$component[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
						}
					}else{
						$values[$j]['value'][] = $value;
							$component[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
					}
					$detail[$j] = array_merge($component[$j], $values[$j]);
					$j++;
				}
				if($HARGA_PEROLEHAN == null){
					$hp[] = 0;
				}else{
					$hp[] = number_format($HARGA_PEROLEHAN[$i],0,',','.');
				}
				if($NILAI_BUKU == null){
					$nbv[] = 0;
				}else{
					$nbv[] = number_format($NILAI_BUKU[$i],0,',','.');
				}
				
				$i++;	
			}

		
		if (strpos($document_code, 'DSPA') !== false) {
			$hrgperolehan[] = array('key'=> 'harga_perolehan',
							'label'=>'Harga Perolehan',
							'value'=>$hp);
			$nbuku[] = array('key'=> 'nbv',
							'label'=>'NBV',
							'value'=>$hp);
			$detail = array_merge($detail,$hrgperolehan,$nbuku);		
		}
		// dd($detail);

		$sql2 = " SELECT b.name as full_name,
					case when a.status_approval = 'menunggu' then 'WAITING' else a.status_approval end as approval_status,
					b.username,
					b.email, b.id as user_id, b.role_id, c.name as role_name
					FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID
						LEFT JOIN TBM_ROLE c ON b.role_id = c.id
					WHERE a.document_code = '{$document_code}' AND status_approval = 'menunggu' "; 

		$dt_email_to = DB::SELECT($sql2);

		$approver = array();
			$dt_approve = array() ;
			$values = array();
			$x = 1;
			$number = array() ;
			$no = array();
			$i = 0;
			$doc = str_replace("/", "-", $document_code); 
			foreach($dt_email_to as $qda){
				$no[] = $x++;
				$param_approve = array(
					'noreg' => $doc,
					'status' => 'A',
					'user_id' => $qda->user_id,
					'id' => $qda->user_id,
					'role_name' => $qda->role_name,
					'role_id' => $qda->role_id,
					'note' =>''
				);

				$param_reject = array(
					'noreg' => $doc,
					'status' => 'R',
					'user_id' => $qda->user_id,
					'id' => $qda->user_id,
					'role_name' => $qda->role_name,
					'role_id' => $qda->role_id,
					'notes' => ''
				);
				$ida = urlencode(serialize($param_approve));
				$idr = urlencode(serialize($param_reject));
				$approve_url[] = url('/email_approve/?id='.$ida);
				$reject_url[] = url('/direct_reject/?id='.$idr);


				$j = 0;
				foreach($qda as $key => $value ){
					if(count($dt_approve) > 0 && $j >0){
						if(in_array(strtolower($key),$dt_approve)){
							$values[$j]['value'][] = $value;
						}
						else{
							$values[$j]['value'][] = $value;
							$dt_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
						}
					}else{
						$values[$j]['value'][] = $value;
							$dt_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
					}
					$approver[$j] = array_merge($dt_approve[$j], $values[$j]);
					$j++;
				}
				$i++;	
			}
			
			$number[] = array('key'=> 'no',
							'label'=>'No',
							'value'=>$no);
			$approver = array_merge($number,$approver);	

			$approvelink[] = array('key'=> 'approve_link',
							'label'=>'Approve Link',
							'value'=>$approve_url);
			$rejectlink[] = array('key'=> 'reject_link',
							'label'=>'Reject Link',
							'value'=>$reject_url);
			$approver = array_merge($approver,$approvelink,$rejectlink);

		// 3. HISTORY APPROVAL 
		$sql3 = " SELECT a.name as full_name, a.status_approval as approval_status, a.name as action_by, date_format(a.date,'%d-%b-%Y') as action_date
				  FROM v_history a WHERE a.document_code = '{$document_code}' ORDER BY a.date ";
		$dt_history_approval = DB::SELECT($sql3);

		$history = array();
			$list_approve = array() ;
			$values = array();
			$y = 1;
			$nomer = array() ;
			$num = array();
			$i = 0;
			foreach($dt_history_approval as $qha){
				$num[] = $y++;
				$j = 0;
				foreach($qha as $key => $value ){
					if(count($list_approve) > 0 && $j >0){
						if(in_array(strtolower($key),$list_approve)){
							$values[$j]['value'][] = $value;
						}
						else{
							$values[$j]['value'][] = $value;
							$list_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
						}
					}else{
						$values[$j]['value'][] = $value;
							$list_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
					}
					$history[$j] = array_merge($list_approve[$j], $values[$j]);
					$j++;
				}
				$i++;	
			}
			
			$nomer[] = array('key'=> 'no',
							'label'=>'No',
							'value'=>$num);
			$history = array_merge($nomer,$history);		

			$lampiran = DB::SELECT($sql_lampiran);
			
			$file_attach = array();
			$file = array() ;

			if(!empty($lampiran))
			{
				$values = array();
				foreach($lampiran as $atch){
					$j = 0;
					foreach($atch as $key => $value ){
						if(count($file_attach) > 0 && $i >0){
							if(in_array(strtolower($key),$file_attach)){
								$values[$j]['value'][] = $value;
							}
							else{
								$values[$j]['value'][] = $value;
								$file_attach[$j] = array( 'key' => strtolower($key),
															'label' => ucwords(str_replace("_"," ",$key)),
												);
							}
						}else{
							$values[$j]['value'][] = $value;
								$file_attach[$j] = array( 'key' => strtolower($key),
															'label' => ucwords(str_replace("_"," ",$key)),
												);
						}
						$file[$j] = array_merge($file_attach[$j], $values[$j]);
						$j++;
					}
					$i++;	
					
				}
			}
			else{
				$file = array(array('key' => 'title',
							'label' => 'Title',
							'value' => [null]),
						array('key' => 'link_file',
							'label' => 'Link File',
							'value' => [null]),
					);
			}

			$data = array (
				'doc_type' => 'ams',
				'header' => $header,
				'detail' => 
							array (
									array (
									'key' => 'custom_data',
									'value' => $detail,
									),
							),
				'footer' => 
							array (
								array (
									'key' => 'approver',
									'value' => $approver,
								),
							),
				'history' => 
							array (
								array (
									'key' => 'list_approval',
									'value' => $history,
								),
							),
				'lampiran' => 
							array (
								array (
									'key' => 'file',
									'value' => $file,
								),
							)
				);
			return json_encode($data);
		
	}

	public function getApi($noreg)
	{
		$document_code = $noreg;

		$no_doc = base64_encode($document_code);

		$hp = array();
		$nbv = array();
		$approve_url = array();
		$reject_url = array();
		
		if (strpos($document_code, 'PDFA') !== false) 
		{
			// HEADER PENDAFTARAN
			$sql_header = "SELECT
								a.NO_REG as document_number,
								date_format(a.CREATED_AT,'%Y-%m-%d') as document_date,
								c.name as creator,
								d.DESCRIPTION as plant_description,
								d.DESCRIPTION as company_name,
								CASE WHEN a.PO_TYPE = '0' THEN 'PO SAP'
								WHEN a.PO_TYPE = '1' THEN 'PO AMP'
								ELSE 'PO ASET LAIN' END as transaction_type,
								b.description as header_note,
								a.NO_PO as purchase_number
							FROM TR_REG_ASSET a
							left join TR_APPROVAL b ON a.NO_REG = b.document_code
							join TBM_USER c on c.id = a.CREATED_BY
							left join TM_GENERAL_DATA d on d.DESCRIPTION_CODE = a.BUSINESS_AREA and d.GENERAL_CODE = 'plant'
							where b.seq is null
							and a.NO_REG = '{$document_code}' ";
			$sql_detail = "SELECT distinct
									a.KODE_MATERIAL,
											a.NAMA_MATERIAL,
											concat(a.LOKASI_BA_CODE,' - ', b.DESCRIPTION) as LOKASI,
											d.QUANTITY_SUBMIT as QTY
							FROM v_email_data_approval a
									LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
									LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'
								LEFT JOIN  TR_REG_ASSET_DETAIL_PO d  ON d.NO_REG = a.document_code
							WHERE a.DOCUMENT_CODE = '{$document_code}'
							order by a.NAMA_MATERIAL";
			$sql_lampiran = " SELECT b.FILENAME as title ,concat('"
							.url('/approval/berkas-amp/')
							."','/{$no_doc}') as link_file"
							." FROM TR_REG_ASSET_FILE b WHERE b.no_reg = '{$document_code}' ";
			
		}
		else if (strpos($document_code, 'DSPA') !== false) 
		{ 
			$sql_header = "SELECT
								a.NO_REG as document_number,
								date_format(a.CREATED_AT,'%Y-%m-%d') as document_date,
								c.name as creator,
								d.DESCRIPTION as plant_description,
								d.DESCRIPTION as company_name,
								CASE WHEN a.TYPE_TRANSAKSI = 'hilang' THEN 'DISPOSAL HILANG'
								WHEN a.TYPE_TRANSAKSI = 'rusak' THEN 'DISPOSAL RUSAK'
								ELSE 'DISPOSAL PENJUALAN' END as transaction_type,
								b.description as header_note
							FROM TR_DISPOSAL_ASSET a
							left join TR_APPROVAL b ON a.NO_REG = b.document_code
							join TBM_USER c on c.id = a.CREATED_BY
							left join TM_GENERAL_DATA d on d.DESCRIPTION_CODE = a.BUSINESS_AREA and d.GENERAL_CODE = 'plant'
							where b.seq is null
							and a.NO_REG = '{$document_code}' ";
			$sql_detail = "SELECT distinct
									a.KODE_MATERIAL AS KODE_FAMS,
									a.NAMA_MATERIAL AS NAMA_ASET,
									concat(a.LOKASI_BA_CODE,' - ', b.DESCRIPTION) AS LOKASI,
									a.TAHUN_ASSET as TAHUN_PEROLEHAN
							FROM v_email_data_approval a
									LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
									LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'
							WHERE a.DOCUMENT_CODE = '{$document_code}'
							order by a.NAMA_MATERIAL";

			$sql_lampiran = " SELECT b.FILE_NAME as title ,concat('"
							.url('/approval/berkas-disposal/')
							."','/{$no_doc}') as link_file"
							." FROM TR_DISPOSAL_ASSET_FILE b WHERE b.no_reg = '{$document_code}' ";
        
		}
		else
		{
			$sql_header = "SELECT
								a.NO_REG as document_number,
								date_format(a.CREATED_AT,'%Y-%m-%d') as document_date,
								c.name as creator,
								d.DESCRIPTION as plant_description,
								d.DESCRIPTION as company_name,
								CASE WHEN a.JENIS_PENGAJUAN = '1' THEN 'MUTASI - ANTAR BA DALAM 1 PT'
									ELSE 'MUTASI - SEWA AMP ANTAR BA' END as transaction_type,
								b.description as header_note
							FROM TR_MUTASI_ASSET_DETAIL a
									left join TM_MSTR_ASSET e on e.NO_REG = a.NO_REG and a.KODE_ASSET_AMS = e.KODE_ASSET_AMS
									left join TR_APPROVAL b ON a.NO_REG = b.document_code
									join TBM_USER c on c.id = a.CREATED_BY
									left join TM_GENERAL_DATA d on d.DESCRIPTION_CODE = e.BA_PEMILIK_ASSET  and d.GENERAL_CODE = 'plant'
							where b.seq is null
							and a.NO_REG = '{$document_code}' ";
			$sql_detail = "SELECT distinct
									a.KODE_MATERIAL AS KODE_FAMS,
									a.NAMA_MATERIAL AS NAMA_ASET,
									concat(a.BA_PEMILIK_ASSET, ' - ',c.DESCRIPTION) AS PEMILIK_ASSET,
									concat(a.LOKASI_BA_CODE,' - ', b.DESCRIPTION) AS LOKASI_AWAL_ASSET,
									concat(d.TUJUAN, ' - ', e.DESCRIPTION) AS LOKASI_TUJUAN_ASSET,
									d.KODE_ASSET_AMS_TUJUAN AS KODE_FAMS_BARU
							FROM v_email_data_approval a
									LEFT JOIN TM_GENERAL_DATA b ON a.LOKASI_BA_CODE = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'PLANT'
									LEFT JOIN TM_GENERAL_DATA c ON a.BA_PEMILIK_ASSET = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'PLANT'
									LEFT JOIN TR_MUTASI_ASSET_DETAIL d ON a.document_code = d.NO_REG AND d.KODE_ASSET_AMS = a.KODE_ASSET_AMS LEFT JOIN TM_GENERAL_DATA e ON d.TUJUAN = e.DESCRIPTION_CODE AND e.GENERAL_CODE = 'PLANT'
							WHERE a.DOCUMENT_CODE = '{$document_code}'
							order by a.NAMA_MATERIAL";
							
			$sql_lampiran = " SELECT b.FILE_NAME as title ,concat('"
							.url('/approval/berkas-mutasi/')
							."','/{$no_doc}') as link_file"
							." FROM TR_MUTASI_ASSET_FILE b WHERE b.no_reg = '{$document_code}' ";
        
		}

		//API HEADER
		$dt_header = DB::SELECT($sql_header);
		$header = array();
			foreach($dt_header as $qdh){
				foreach($qdh as $key => $value ){
					$header[] = array ('key' => strtolower($key),
										'label' => ucwords(str_replace("_"," ",$key)),
										'value' => $value);
				}
			}

		//API APPROVER
		$row = DB::table('v_email_data_approval')
		->where('document_code','LIKE','%'.$document_code.'%')
		->get();

		$HARGA_PEROLEHAN = $this->get_harga_perolehan($row);
		$NILAI_BUKU = $this->get_nilai_buku($row);
		// dd($NILAI_BUKU);

		
		//API DETAIL
		$dt_detail = DB::SELECT($sql_detail);
		$detail = array();
			$component = array() ;
			$values = array();

			$i = 0;
			foreach($dt_detail as $qdc){
				$j = 0;
				foreach($qdc as $key => $value ){
					if(count($component) > 0 && $j >0){
						if(in_array(strtolower($key),$component)){
							$values[$j]['value'][] = $value;
						}
						else{
							$values[$j]['value'][] = $value;
							$component[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
						}
					}else{
						$values[$j]['value'][] = $value;
							$component[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
					}
					$detail[$j] = array_merge($component[$j], $values[$j]);
					$j++;
				}
				if($HARGA_PEROLEHAN == null){
					$hp[] = 0;
				}else{
					$hp[] = number_format($HARGA_PEROLEHAN[$i],0,',','.');
				}
				if($NILAI_BUKU == null){
					$nbv[] = 0;
				}else{
					$nbv[] = number_format($NILAI_BUKU[$i],0,',','.');
				}
				
				$i++;	
			}

		
		if (strpos($document_code, 'DSPA') !== false) {
			$hrgperolehan[] = array('key'=> 'harga_perolehan',
							'label'=>'Harga Perolehan',
							'value'=>$hp);
			$nbuku[] = array('key'=> 'nbv',
							'label'=>'NBV',
							'value'=>$hp);
			$detail = array_merge($detail,$hrgperolehan,$nbuku);		
		}
		// dd($detail);

		$sql2 = " SELECT b.name as full_name,
					case when a.status_approval = 'menunggu' then 'WAITING' else a.status_approval end as approval_status,
					b.username,
					b.email, b.id as user_id, b.role_id, c.name as role_name
					FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID
						LEFT JOIN TBM_ROLE c ON b.role_id = c.id
					WHERE a.document_code = '{$document_code}' AND status_approval = 'menunggu' "; 

		$dt_email_to = DB::SELECT($sql2);

		$approver = array();
			$dt_approve = array() ;
			$values = array();
			$x = 1;
			$number = array() ;
			$no = array();
			$i = 0;
			$doc = str_replace("/", "-", $document_code); 
			foreach($dt_email_to as $qda){
				$no[] = $x++;
				$param_approve = array(
					'noreg' => $doc,
					'status' => 'A',
					'user_id' => $qda->user_id,
					'id' => $qda->user_id,
					'role_name' => $qda->role_name,
					'role_id' => $qda->role_id,
					'note' =>''
				);

				$param_reject = array(
					'noreg' => $doc,
					'status' => 'R',
					'user_id' => $qda->user_id,
					'id' => $qda->user_id,
					'role_name' => $qda->role_name,
					'role_id' => $qda->role_id,
					'notes' => ''
				);
				$ida = urlencode(serialize($param_approve));
				$idr = urlencode(serialize($param_reject));
				$approve_url[] = url('/email_approve/?id='.$ida);
				$reject_url[] = url('/direct_reject/?id='.$idr);


				$j = 0;
				foreach($qda as $key => $value ){
					if(count($dt_approve) > 0 && $j >0){
						if(in_array(strtolower($key),$dt_approve)){
							$values[$j]['value'][] = $value;
						}
						else{
							$values[$j]['value'][] = $value;
							$dt_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
						}
					}else{
						$values[$j]['value'][] = $value;
							$dt_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
					}
					$approver[$j] = array_merge($dt_approve[$j], $values[$j]);
					$j++;
				}
				$i++;	
			}
			
			$number[] = array('key'=> 'no',
							'label'=>'No',
							'value'=>$no);
			$approver = array_merge($number,$approver);	

			$approvelink[] = array('key'=> 'approve_link',
							'label'=>'Approve Link',
							'value'=>$approve_url);
			$rejectlink[] = array('key'=> 'reject_link',
							'label'=>'Reject Link',
							'value'=>$reject_url);
			$approver = array_merge($approver,$approvelink,$rejectlink);

		// 3. HISTORY APPROVAL 
		$sql3 = " SELECT a.name as full_name, a.status_approval as approval_status, a.name as action_by, date_format(a.date,'%d-%b-%Y') as action_date
				  FROM v_history a WHERE a.document_code = '{$document_code}' ORDER BY a.date ";
		$dt_history_approval = DB::SELECT($sql3);

		$history = array();
			$list_approve = array() ;
			$values = array();
			$y = 1;
			$nomer = array() ;
			$num = array();
			$i = 0;
			foreach($dt_history_approval as $qha){
				$num[] = $y++;
				$j = 0;
				foreach($qha as $key => $value ){
					if(count($list_approve) > 0 && $j >0){
						if(in_array(strtolower($key),$list_approve)){
							$values[$j]['value'][] = $value;
						}
						else{
							$values[$j]['value'][] = $value;
							$list_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
						}
					}else{
						$values[$j]['value'][] = $value;
							$list_approve[$j] = array( 'key' => strtolower($key),
														'label' => ucwords(str_replace("_"," ",$key)),
											);
					}
					$history[$j] = array_merge($list_approve[$j], $values[$j]);
					$j++;
				}
				$i++;	
			}
			
			$nomer[] = array('key'=> 'no',
							'label'=>'No',
							'value'=>$num);
			$history = array_merge($nomer,$history);		

			$lampiran = DB::SELECT($sql_lampiran);
			
			$file_attach = array();
			$file = array() ;

			if(!empty($lampiran))
			{
				$values = array();
				foreach($lampiran as $atch){
					$j = 0;
					foreach($atch as $key => $value ){
						if(count($file_attach) > 0 && $i >0){
							if(in_array(strtolower($key),$file_attach)){
								$values[$j]['value'][] = $value;
							}
							else{
								$values[$j]['value'][] = $value;
								$file_attach[$j] = array( 'key' => strtolower($key),
															'label' => ucwords(str_replace("_"," ",$key)),
												);
							}
						}else{
							$values[$j]['value'][] = $value;
								$file_attach[$j] = array( 'key' => strtolower($key),
															'label' => ucwords(str_replace("_"," ",$key)),
												);
						}
						$file[$j] = array_merge($file_attach[$j], $values[$j]);
						$j++;
					}
					$i++;	
					
				}
			}
			else{
				$file = array(array('key' => 'title',
							'label' => 'Title',
							'value' => [null]),
						array('key' => 'link_file',
							'label' => 'Link File',
							'value' => [null]),
					);
			}

			$data = array (
				'doc_type' => 'ams',
				'header' => $header,
				'detail' => 
							array (
									array (
									'key' => 'custom_data',
									'value' => $detail,
									),
							),
				'footer' => 
							array (
								array (
									'key' => 'approver',
									'value' => $approver,
								),
							),
				'history' => 
							array (
								array (
									'key' => 'list_approval',
									'value' => $history,
								),
							),
				'lampiran' => 
							array (
								array (
									'key' => 'file',
									'value' => $file,
								),
							)
				);
			return json_encode($data);
		
	}


	public function direct_reject(Request $rq)
	{
		// $request = new \Illuminate\Http\Request();
		// $request->replace(['id' => $_GET['id']]);
        $req = unserialize(urldecode($_GET['id']));
		$notes = $rq->notes;

		$status = $req['status'];
        $note = $rq->notes;
		$no_registrasi = str_replace("-", "/", $req['noreg']); 

		$appcontrol = new ApprovalController;
        $asset_controller = $appcontrol->get_ac($no_registrasi); //get asset controller 
        $validasi_last_approve = $appcontrol->get_validasi_last_approve($no_registrasi);

        if( $validasi_last_approve == 0 )
        {
            DB::beginTransaction();
            
            try 
            {
                if($status=='R')
                {
                    DB::UPDATE(" UPDATE TR_DISPOSAL_ASSET_DETAIL SET DELETED = 'R' WHERE NO_REG = '".$no_registrasi."' "); 
                }

                DB::STATEMENT('CALL update_approval("'.$no_registrasi.'", "'.$req['user_id'].'","'.$status.'", "'.$note.'", "'.$req['role_name'].'", "'.$asset_controller.'")');
                
                DB::commit();

                return response()->json(['status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'update'), "new_noreg"=>$no_registrasi]);
                
            } 
            catch (\Exception $e) 
            {
                DB::rollback();
                return response()->json(['status' => false, "message" => $e->getMessage()]);
                
            }
        }    
        else
        {
            $validasi_check_gi_amp['status'] = 'success';

            if($validasi_check_gi_amp['status'] == 'success')
            {
                DB::beginTransaction();
                try 
                {
                    
                    DB::STATEMENT('CALL complete_document_disposal("'.$no_registrasi.'", "'.$req['user_id'].'")');
                    DB::commit();
                    return response()->json(['status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'update'), "new_noreg"=>$no_registrasi]);
                } 
                catch (\Exception $e) 
                {
                    DB::rollback();
                    return response()->json(['status' => false, "message" => $e->getMessage()]);
                }
            }
            else
            {
                $data['message'] =   'Error Validasi GI' ;
                
                $data = serialize($data);
                return view('email.respon')->with('message', $data);
            }
           
        }
	}


}