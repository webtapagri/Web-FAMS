<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\TR_REG_ASSET_DETAIL;
use API;
use App\Jobs\SendEmail;
use GuzzleHttp\Client;
use App\Http\Controllers\ApprovalController;
use Redirect;
use Illuminate\Support\Facades\Log;

class RestuqueController extends Controller
{
    public function __construct()
    {
		if(env('APP_ENV') == 'production'){
			$this->restuque = 'http://apis.tap-agri.com/rtq-msa-approval/';
		}else{
			$this->restuque = 'http://apisqa.tap-agri.com/rtq-msa-qa-approval/';
		}
    }

    //
    
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

    public function hitRestuque($noreg)
	{
		Log::info('hit restuque');
		$data = $this->getApi($noreg);	
		Log::info($data);
		// dd(json_encode($data));	
		$token = $this->getToken();
		$var = "rtq/v1.0/documents";
		$url = $this->restuque . $var;
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => array(
				"accept: */*",
				"content-type: application/json",
				"x-access-token: ".$token
			),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$statusCode = json_decode($response)->statusCode;
		
		$dt = json_decode($data);

		$doc_type = $dt->doc_type;
		$api_header = json_encode($dt->header);
		$api_detail = json_encode($dt->detail);
		$api_footer = json_encode($dt->footer);
		$api_history = json_encode($dt->history);
		$api_attach = json_encode($dt->lampiran);
		$doc_number = $noreg;

		$retry = 0;
		DB::beginTransaction();
		while($statusCode != 200 && $statusCode != 201 && $retry < 3){
			curl_exec($curl);
			$retry++;  
			if($retry == 2) {
			$err = $statusCode;
				$query_log = "insert into TR_RESTUQUE_LOG (API_URL,STATUS_CODE,LOG_TIME,DOC_TYPE,DOC_NUMBER,API_HEADER,API_DETAIL,API_FOOTER,API_HISTORY,API_ATTACHMENT)
								VALUES ('".$url."','".$err."',NOW(),'".$doc_type."','".$doc_number."','".$api_header."','".$api_detail."','".$api_footer."','".$api_history."','".$api_attach."')";
				DB::statement( $query_log );
				DB::commit();
			}
		}  

		$query_log = "insert into TR_RESTUQUE_LOG (API_URL,STATUS_CODE,LOG_TIME,DOC_TYPE,DOC_NUMBER,API_HEADER,API_DETAIL,API_FOOTER,API_HISTORY,API_ATTACHMENT)
						VALUES ('".$url."','".$statusCode."',NOW(),'".$doc_type."','".$doc_number."','".$api_header."','".$api_detail."','".$api_footer."','".$api_history."','".$api_attach."')";

		DB::statement( $query_log );
		DB::commit();
					  
		curl_close($curl);
	}

	public function completeRestuque($noreg)
	{
		$data = array('doc_type'=>'ams',
						'document_number' => $noreg,
						'status' =>'COMPLETED');
		$token = $this->getToken();
		$var = "rtq/v1.0/documents";
		$url = $this->restuque . $var;
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				"accept: */*",
				"content-type: application/json",
				"x-access-token: ".$token
			),
		));

		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$statusCode = json_decode($response)->statusCode;

		$dt = json_decode($data);
		$doc_type = $dt->doc_type;;
		$doc_number = $noreg;

		$retry = 0;
		DB::beginTransaction();
		while($statusCode != 200 && $statusCode != 201 && $retry < 3){
			curl_exec($curl);
			$retry++;  
			if($retry == 2) {
				$err = $statusCode;
				$query_log = "insert into TR_RESTUQUE_LOG (API_URL,STATUS_CODE,LOG_TIME,DOC_TYPE,DOC_NUMBER,API_HEADER)
								VALUES ('".$url."','".$err."',NOW(),'".$doc_type."','".$doc_number."','COMPLETED')";
								
				DB::statement( $query_log );
				DB::commit();
				
			}
		}  

		$query_log = "insert into TR_RESTUQUE_LOG (API_URL,STATUS_CODE,LOG_TIME,DOC_TYPE,DOC_NUMBER,API_HEADER)
						VALUES ('".$url."','".$statusCode."',NOW(),'".$doc_type."','".$doc_number."','COMPLETED')";

		DB::statement( $query_log );
		DB::commit();
							
					  
		curl_close($curl);
	}

	public function getToken()
	{
       	$data = array( 'username'=> 'uat4',
						'password'=>'superSecret',
						'device_token'=> 'device_token' );
		$var = "rtq/v1.0/auth/login";
		$url = $this->restuque . $var;
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30000,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				"accept: */*",
				"content-type: application/json"
			),
		));

		
		$response = curl_exec($curl);
		$res =json_decode(json_encode($response));
		// dd(json_decode($res)->data->token);
		return json_decode($res)->data->token;
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

}
