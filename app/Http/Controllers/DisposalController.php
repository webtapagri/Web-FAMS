<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Cookie;
use App\TrUser;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;
use App\TM_MSTR_ASSET;
use App\Http\Controllers\FamsEmailController;

class DisposalController extends Controller
{
	protected $FamsEmailController;
    public function __construct(FamsEmailController $FamsEmailController)
    {
        $this->FamsEmailController = $FamsEmailController;
    }
	
	public function index()
    {	
		if(empty(Session::get('authenticated')))
        return redirect('/login');

		$access = AccessRight::access();
        $data['page_title'] = "Disposal";
        $data['ctree_mod'] = 'Disposal';
        $data['ctree'] = 'disposal-penjualan';

        $data['autocomplete'] = $this->get_autocomplete();
        $data['data'] = $this->get_data_cart(1);
		$data['list_kategori_upload'] = $this->list_kategori_upload(1);
        $row = $this->get_data_cart(1);					 
		$data['nilai_buku'] = $this->get_nilai_buku($row);
		
        $data['list_skip_harga_perolehan'] = $this->get_list_skip_harga_perolehan();

        return view('disposal.index')->with(compact('data'));
	}
	
	function get_nilai_buku($row)
    {
        $nilai = array();

        for($i=0;$i<count($row);$i++){
            $BUKRS = substr($row[$i]->BA_PEMILIK_ASSET,0,2);

            $YEAR = date('Y');

            $ANLN1 = $this->get_anln1($row[$i]->KODE_ASSET_SAP);
            
            $ANLN2 = '0000';
           
            

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
    
	
    function get_data_cart($jenis_pengajuan)
	{
		//echo "1<pre>"; print_r(session()->all()); die();

		$user_id = Session::get('user_id');
		// $area_code = explode(',',Session::get('area_code'));
		$area_code = Session::get('area_code');
		$role = Session::get('role');
		// dd(Session::get('role'));
		//$created_by = $u['username'];
		
		if($role == 'PGA'){$where = '1=1'; }else { $where = '1=0'; }

		$where .= " AND a.JENIS_PENGAJUAN = $jenis_pengajuan AND a.CHECKLIST = 0 ";

		if($area_code != 'All')
		{
			$where .= " AND a.LOKASI_BA_CODE in (".$area_code.") ";
			// $where .= "";
		}

		$sql = " SELECT a.* FROM TR_DISPOSAL_TEMP a  WHERE $where"; //echo $sql; die();
		
		$dt = DB::SELECT($sql);
		//echo "<pre>"; print_r($dt); die();
		return $dt;
	}

	function get_autocomplete()
    {   
    	
    	$area_code = Session::get('area_code'); 
    	$where = "";
    	
    	if( $area_code != 'All' )
    	{
    		$where .= " AND LOKASI_BA_CODE in ($area_code) ";
    		// $where .= " AND BA_PEMILIK_ASSET in ($area_code) ";
    	}

    	$sql = " SELECT a.kode_asset_ams AS kode_asset_ams, a.kode_material AS kode_material, a.nama_material AS nama_material, a.nama_asset_1 AS nama_asset_1, a.kode_asset_sap AS kode_asset_sap, a.lokasi_ba_description, a.ba_pemilik_asset 
    				FROM TM_MSTR_ASSET a 
    					WHERE (a.kode_asset_ams IS NOT NULL OR a.kode_asset_ams != '') and (a.nama_material IS NOT NULL OR a.nama_material != '' ) AND (a.DISPOSAL_FLAG IS NULL OR a.DISPOSAL_FLAG = '' ) $where
    				ORDER BY a.nama_material ASC ";//echo $sql;
 		$data = DB::SELECT($sql); 

 		if($data)
 		{
 			$datax = '';
 			foreach( $data as $k => $v )
 			{
 				$kode_asset_ams = base64_encode($v->kode_asset_ams);
 				$datax .= "{id : '{$kode_asset_ams}',
 								name : '{$v->nama_material}  ',
 								asset : '{$v->nama_asset_1} ',
 								kode_asset_ams : '{$v->kode_asset_ams}',
 								lokasi_ba_description : '{$v->lokasi_ba_description}',
 								ba_pemilik_asset : '{$v->ba_pemilik_asset}'
 							},";
 			}
 		}
 		return rtrim($datax,',');
    }

    function add($id,$jenis_pengajuan)
    {
    	if(empty(Session::get('authenticated')))
        return redirect('/login');

    	//echo $jenis_pengajuan; die();

    	$user_id = Session::get('user_id');
		$kode_asset_ams = base64_decode($id);
		$row = TM_MSTR_ASSET::find($kode_asset_ams);
		$validasi_asset = $this->check_asset($kode_asset_ams,1);
		if( $validasi_asset > 0 )
		{
			$sql = "SELECT  COUNT(*) as TOTAL FROM TR_DISPOSAL_ASSET_DETAIL WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ";
				$dt = DB::SELECT($sql);
				if($dt[0]->TOTAL > 0 ){
						Session::flash('alert', 'Data sedang dalam proses approval Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.' , DOCUMENT CODE : '.$row->NO_REG.' ) ');
						return Redirect::to('/disposal-penjualan');
						exit;
				}
				else{
					Session::flash('alert', 'Data sudah ditambahkan dalam daftar Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.') ');
					return Redirect::to('/disposal-penjualan');
					exit;
				}
		}
		
		if( $row->count() > 0) 
		{
			//$data = array( 'NO_REG' => $row->NO_REG,'KODE_ASSET_SAP' => $row->kode_asset_sap);
			//$NO_REG = $row->NO_REG;

			DB::beginTransaction();

			try 
			{
				$HARGA_PEROLEHAN = $this->get_harga_perolehan($row);

				$sql = "INSERT INTO TR_DISPOSAL_TEMP(KODE_ASSET_AMS,KODE_ASSET_SAP,NAMA_MATERIAL,BA_PEMILIK_ASSET,LOKASI_BA_CODE,LOKASI_BA_DESCRIPTION,NAMA_ASSET_1,CREATED_BY,JENIS_PENGAJUAN,CHECKLIST,HARGA_PEROLEHAN)
							VALUES('{$row->KODE_ASSET_AMS}','{$row->KODE_ASSET_SAP}','{$row->NAMA_MATERIAL}','{$row->BA_PEMILIK_ASSET}','{$row->LOKASI_BA_CODE}','{$row->LOKASI_BA_DESCRIPTION}','{$row->NAMA_ASSET_1}','{$user_id}','{$jenis_pengajuan}',0,'{$HARGA_PEROLEHAN}')";
				//	echo $sql; die();
				DB::insert($sql);
				DB::commit();

				Session::flash('message', 'Success add data to Latest disposal! (KODE AMS : '.$row->KODE_ASSET_AMS.') ');
				return Redirect::to('/disposal-penjualan');
			} 
			catch (\Exception $e) 
			{
				DB::rollback();
				Session::flash('message', $e->getMessage()); 
				return Redirect::to('/disposal-penjualan');
			}
		} 
		else 
		{
			Session::flash('alert-class', 'alert-danger'); 
            return Redirect::to('/disposal-penjualan');
        }
	}

	function remove($kode_asset_ams)
    {	

		DB::beginTransaction();

		try 
		{

			DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");
			DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");

			DB::commit();

			Session::flash('message', 'Success delete data disposal! (KODE AMS : '.$kode_asset_ams.') ');
			return Redirect::to('/disposal-penjualan');
		} 
		catch (\Exception $e) 
		{
			DB::rollback();
			Session::flash('message', $e->getMessage()); 
			return Redirect::to('/disposal-penjualan');
		}

    }

    public function index_hilang()
    {
		if(empty(Session::get('authenticated')))
        return redirect('/login');

		$access = AccessRight::access();
        $data['page_title'] = "Disposal";
        $data['ctree_mod'] = 'Disposal';
        $data['ctree'] = 'disposal-hilang';

        $data['autocomplete'] = $this->get_autocomplete();
		$data['data'] = $this->get_data_cart(2);
        $row = $this->get_data_cart(2);					 
		$data['nilai_buku'] = $this->get_nilai_buku($row);
		// dd( $data);
        $data['list_kategori_upload'] = $this->list_kategori_upload(2);
		$data['list_skip_harga_perolehan'] = $this->get_list_skip_harga_perolehan(); //$this->get_totalcartnotif();
        return view('disposal.index_hilang')->with(compact('data'));
    }

    function add_hilang($id,$jenis_pengajuan)
    {
    	if(empty(Session::get('authenticated')))
        return redirect('/login');

    	//echo $jenis_pengajuan; die();

    	$user_id = Session::get('user_id');
		$kode_asset_ams = base64_decode($id);

		$row = TM_MSTR_ASSET::find($kode_asset_ams);
		///
		$validasi_asset = $this->check_asset($kode_asset_ams,2);
		if( $validasi_asset > 0 )
		{
			$sql = "SELECT  COUNT(*) as TOTAL FROM TR_DISPOSAL_ASSET_DETAIL WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ";
				$dt = DB::SELECT($sql);
				if($dt[0]->TOTAL > 0 ){
						Session::flash('alert', 'Data sedang dalam proses approval Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.' , DOCUMENT CODE : '.$row->NO_REG.' ) ');
						return Redirect::to('/disposal-hilang');
						exit;
				}
				else{
						Session::flash('alert', 'Data sudah ditambahkan dalam daftar Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.') ');
						return Redirect::to('/disposal-hilang');
						exit;
				}
		}
		// else{
		// 		$sql = "SELECT  count(NO_REG) as TOTAL FROM v_disposal_status WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ";
		// 		$dt = DB::SELECT($sql);
		// 		if($dt[0]->TOTAL > 0 ){
		// 			Session::flash('alert', 'Data sedang dalam proses approval Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.' , DOCUMENT CODE : '.$row->NO_REG.' ) ');
		// 			return Redirect::to('/disposal-hilang');
		// 			exit;
		// 		}
		// }

		if( $row->count() > 0) 
		{
			//$data = array( 'NO_REG' => $row->NO_REG,'KODE_ASSET_SAP' => $row->kode_asset_sap);
			//$NO_REG = $row->NO_REG;

			DB::beginTransaction();

			try 
			{

				$HARGA_PEROLEHAN = $this->get_harga_perolehan($row);
				
				// dd($HARGA_PEROLEHAN);

				$sql = "INSERT INTO TR_DISPOSAL_TEMP(KODE_ASSET_AMS,KODE_ASSET_SAP,NAMA_MATERIAL,BA_PEMILIK_ASSET,LOKASI_BA_CODE,LOKASI_BA_DESCRIPTION,NAMA_ASSET_1,CREATED_BY,JENIS_PENGAJUAN,CHECKLIST,HARGA_PEROLEHAN)
							VALUES('{$row->KODE_ASSET_AMS}','{$row->KODE_ASSET_SAP}','{$row->NAMA_MATERIAL}','{$row->BA_PEMILIK_ASSET}','{$row->LOKASI_BA_CODE}','{$row->LOKASI_BA_DESCRIPTION}','{$row->NAMA_ASSET_1}','{$user_id}','{$jenis_pengajuan}',0,'{$HARGA_PEROLEHAN}')";
				//	echo $sql; die();
				DB::insert($sql);
				DB::commit();

				Session::flash('message', 'Success add data to Latest disposal! (KODE AMS : '.$row->KODE_ASSET_AMS.') ');
				return Redirect::to('/disposal-hilang');
			} 
			catch (\Exception $e) 
			{
				DB::rollback();
				Session::flash('message', $e->getMessage()); 
				return Redirect::to('/disposal-hilang');
			}
		} 
		else 
		{
			Session::flash('alert-class', 'alert-danger'); 
            return Redirect::to('/disposal-hilang');
        }
	}

	function remove_hilang($kode_asset_ams)
    {	

		DB::beginTransaction();

		try 
		{

			DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");
			DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");

			DB::commit();

			Session::flash('message', 'Success delete data disposal! (KODE AMS : '.$kode_asset_ams.') ');
			return Redirect::to('/disposal-hilang');
		} 
		catch (\Exception $e) 
		{
			DB::rollback();
			Session::flash('message', $e->getMessage()); 
			return Redirect::to('/disposal-hilang');
		}

    }

    function check_asset($kode_asset_ams,$jenis_pengajuan)
    {
    	$total = 0;

    	$data = DB::SELECT(" SELECT COUNT(*) AS TOTAL FROM v_asset_submitted WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");
    	//$total = $data[0]->TOTAL;

    	if( $data[0]->TOTAL == 0)
    	{
    		// #2 JIKA ASET DETAIL NULL VALIDASI DI DISPOSAL ASET TEMP
    		$sql2 = "SELECT COUNT(*) AS TOTAL FROM TR_DISPOSAL_TEMP WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ";
    		$dt = DB::SELECT($sql2);

			$total = $dt[0]->TOTAL; 
    	}
    	else
    	{
			$total = $data[0]->TOTAL;//1
		}
    	return $total;
    }

    public function index_rusak()
    {
		if(empty(Session::get('authenticated')))
        return redirect('/login');

		$access = AccessRight::access();
        $data['page_title'] = "Disposal";
        $data['ctree_mod'] = 'Disposal';
        $data['ctree'] = 'disposal-rusak';

        $data['autocomplete'] = $this->get_autocomplete();
        $data['data'] = $this->get_data_cart(3);
        $row = $this->get_data_cart(3);					 
		$data['nilai_buku'] = $this->get_nilai_buku($row);
        $data['list_kategori_upload'] = $this->list_kategori_upload(3);
		$data['list_skip_harga_perolehan'] = $this->get_list_skip_harga_perolehan();
		//$data['list_berkas'] = $this->get_list_berkas(3);

        return view('disposal.index_rusak')->with(compact('data'));
    }

	
	function add_rusak($id,$jenis_pengajuan)
    {
    	if(empty(Session::get('authenticated')))
        return redirect('/login');

    	//echo $jenis_pengajuan; die();

    	$user_id = Session::get('user_id');
		$kode_asset_ams = base64_decode($id);

		$row = TM_MSTR_ASSET::find($kode_asset_ams);

		$validasi_asset = $this->check_asset($kode_asset_ams,3);
		if( $validasi_asset > 0 )
		{
			// 1= sudah dalam list
			// 0= cek apakah sudah proses disposal
			$sql = "SELECT  COUNT(*) as TOTAL FROM TR_DISPOSAL_ASSET_DETAIL WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ";
				$dt = DB::SELECT($sql);
				if($dt[0]->TOTAL > 0 ){
						Session::flash('alert', 'Data sedang dalam proses approval Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.' , DOCUMENT CODE : '.$row->NO_REG.' ) ');
						return Redirect::to('/disposal-rusak');
						exit;
				}
				else{
					Session::flash('alert', 'Data sudah ditambahkan dalam daftar Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.') ');
					return Redirect::to('/disposal-rusak');
					exit;
				}
		}
		// if( $validasi_asset == "1" )
		// {
		// 	Session::flash('alert', 'Data sudah ditambahkan dalam daftar Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.') ');
		// 	return Redirect::to('/disposal-rusak');
		// 	exit;
		// }
		// else if( $validasi_asset == "2" )
		// {
		// 	Session::flash('alert', 'Data sedang dalam proses approval Disposal (KODE AMS : '.$row->KODE_ASSET_AMS.' , DOCUMENT CODE : '.$row->DISPOSAL_FLAG.' ) ');
		// 	return Redirect::to('/disposal-rusak');
		// 	exit;
		// }

		if( $row->count() > 0) 
		{
			DB::beginTransaction();

			try 
			{
				$HARGA_PEROLEHAN = $this->get_harga_perolehan($row);

				$sql = "INSERT INTO TR_DISPOSAL_TEMP(KODE_ASSET_AMS,KODE_ASSET_SAP,NAMA_MATERIAL,BA_PEMILIK_ASSET,LOKASI_BA_CODE,LOKASI_BA_DESCRIPTION,NAMA_ASSET_1,CREATED_BY,JENIS_PENGAJUAN,CHECKLIST,HARGA_PEROLEHAN)
							VALUES('{$row->KODE_ASSET_AMS}','{$row->KODE_ASSET_SAP}','{$row->NAMA_MATERIAL}','{$row->BA_PEMILIK_ASSET}','{$row->LOKASI_BA_CODE}','{$row->LOKASI_BA_DESCRIPTION}','{$row->NAMA_ASSET_1}','{$user_id}','{$jenis_pengajuan}',0,'{$HARGA_PEROLEHAN}')";
				
				DB::insert($sql);
				DB::commit();

				Session::flash('message', 'Success add data to Latest disposal! (KODE AMS : '.$row->KODE_ASSET_AMS.') ');
				return Redirect::to('/disposal-rusak');
			} 
			catch (\Exception $e) 
			{
				DB::rollback();
				Session::flash('message', $e->getMessage()); 
				return Redirect::to('/disposal-rusak');
			}
		} 
		else 
		{
			Session::flash('alert-class', 'alert-danger'); 
            return Redirect::to('/disposal-rusak');
        }
	}

	function proses(Request $request,$jenis)
	{
		if( $jenis == 1 ){ $jp = 'penjualan'; $menu_code = 'D3'; }
		elseif($jenis==2){ $jp = 'hilang'; $menu_code = 'D1'; }
		else{ $jp = 'rusak'; $menu_code = 'D2'; }

		$req = $request->all();
		$user_id = Session::get('user_id');
		$reg_no = $this->get_reg_no();

		$sql = " SELECT * FROM TR_DISPOSAL_TEMP WHERE JENIS_PENGAJUAN = $jenis AND CREATED_BY = $user_id AND CHECKLIST = 0 ";
		$data = DB::SELECT($sql);
		if(!empty($data))
		{
			DB::beginTransaction();
			try 
       		{
       			$vhp_plus = array();

       			if($data[0]->HARGA_PEROLEHAN >= 50000000){$tipe = 'plus';}
       			else{$tipe = 'minus';}

       			$lokasi_ba_code = $data[0]->LOKASI_BA_CODE;

       			$ac_awal = $this->get_ac_awal($data[0]->KODE_ASSET_AMS);
       			if( $ac_awal == "not found" )
       			{ 	
	           	 	Session::flash('alert', 'Asset Controller not found (KODE ASSET AMS : '.$data[0]->KODE_ASSET_AMS.' )');
					return Redirect::to('/disposal-'.$jp.''); 
				}
				

				foreach($data as $k => $v)
				{
					// #1 VALIDASI HARGA PEROLEHAN
					$vhp = $this->validasi_harga_perolehan($v,$tipe);
					if( $vhp['result'] != 1 )
					{
						DB::rollback(); 
						DB::commit();
	           	 		Session::flash('alert',$vhp['message']);
						return Redirect::to('/disposal-'.$jp.'');
						exit;
					}

					// #2 VALIDASI SATU LOKASI BA CODE
					$vlb = $this->validasi_lokasi_bacode($v,$lokasi_ba_code);
					if( $vlb['result'] != 1 )
					{
						DB::rollback();
						DB::commit();
	           	 		
	           	 		Session::flash('alert',$vlb['message']);
						return Redirect::to('/disposal-'.$jp.'');
						exit;
					}

					// #3 VALIDASI SAMA ASSET CONTROLLER
					$vac = $this->validasi_asset_controller($v->KODE_ASSET_AMS, $ac_awal); 
					if( $vac['result'] != 1 )
					{
						DB::rollback();DB::commit();
	           	 		Session::flash('alert',$vac['message']);
						return Redirect::to('/disposal-'.$jp.'');
						exit;
					}

					// #4 VALIDASI BERKAS MASING2 ASET
					$vbks = $this->validasi_berkas_peraset($v->KODE_ASSET_AMS);
					if( $vbks['result'] != 1 )
					{
						DB::rollback();DB::commit();
	           	 		Session::flash('alert',$vbks['message']);
						return Redirect::to('/disposal-'.$jp.'');
						exit;
					}
				}

				foreach($data as $k => $v)
				{
					//#1 INSERT FILE UPLOAD DARI TABLE TR_DISPOSAL_TEMP_FILE
                    $proses_upload_file = $this->proses_upload_file($v->KODE_ASSET_AMS,$reg_no,$jenis);
                    if(!$proses_upload_file['status'])
                    {
                    	Session::flash('alert', $proses_upload_file['message']);
						return Redirect::to('/disposal-'.$jp.'');
                    }
                    
                    //#2 INSERT ASSET DETAIL
                    DB::table('TR_DISPOSAL_ASSET_DETAIL')->insertGetId([
                        "NO_REG" => $reg_no,
                        "KODE_ASSET_AMS" => $v->KODE_ASSET_AMS,
                        "KODE_ASSET_SAP" => $v->KODE_ASSET_SAP,
                        "NAMA_MATERIAL" => $v->NAMA_MATERIAL,
                        "BA_PEMILIK_ASSET" => $v->BA_PEMILIK_ASSET,
                        "LOKASI_BA_CODE" => $v->LOKASI_BA_CODE,
                        "LOKASI_BA_DESCRIPTION" => $v->LOKASI_BA_DESCRIPTION,
                        "NAMA_ASSET_1" => $v->NAMA_ASSET_1,
                        "HARGA_PEROLEHAN" => $v->HARGA_PEROLEHAN,
                        "JENIS_PENGAJUAN" => $v->JENIS_PENGAJUAN,
                        "CREATED_BY" => Session::get('user_id'),
                    ]); 
				}

				DB::STATEMENT('call create_approval("'.$menu_code.'", "'.$data[0]->LOKASI_BA_CODE.'","","'.$reg_no.'","'.$user_id.'","'.$ac_awal.'","'.$data[0]->HARGA_PEROLEHAN.'")');

				$asset_id = DB::table('TR_DISPOSAL_ASSET')->insertGetId([
	                "CREATED_BY" => Session::get('user_id'),
	                "NO_REG" => $reg_no,
	                "TYPE_TRANSAKSI" => $jp,
	                "BUSINESS_AREA" => $data[0]->LOKASI_BA_CODE,
	                "TANGGAL_REG" => date("Y-m-d")
	            ]);

	            DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP WHERE JENIS_PENGAJUAN = $jenis AND CREATED_BY = $user_id AND CHECKLIST = 0 ");

				DB::commit();

				Session::flash('message', 'Proses sukses (NO REG : '.$reg_no.' ) ');


				$request = new \Illuminate\Http\Request();
				$request->replace(['noreg' => $reg_no]);
				$this->FamsEmailController->index($request);
				
				return Redirect::to('/disposal-'.$jp.'');
			}
			catch (\Exception $e) 
			{
	            DB::rollback();
	            Session::flash('alert', $e->getMessage());
	            return Redirect::to('/disposal-'.$jp.'');
	       }
		}
		// else
		// {
		// 	Session::flash('alert', 'Proses failed!');
		// 	return Redirect::to('/disposal-'.$jp.'');
		// }
	}

	public function get_reg_no()
    {
        $sql = "SELECT count(*) AS total FROM TR_DISPOSAL_ASSET WHERE YEAR(tanggal_reg) = YEAR(CURDATE()) AND MONTH(tanggal_reg) = MONTH(curdate())";
        $data = DB::select($sql);
        $maxno = $data[0]->total;
        $year= date('y');
        $month = date('m');
        $year=$year.'.';
        $n=$maxno;
        $n = str_pad($n + 1, 5, 0, STR_PAD_LEFT);
        $number=$year.$month.'/AMS/DSPA/'.$n;
        return $number;
    }

    public function update_harga_perolehan(Request $request)
    {
		DB::beginTransaction();
		try 
   		{
			DB::UPDATE(' UPDATE TR_DISPOSAL_TEMP SET HARGA_PEROLEHAN = '.$request->harga_perolehan.' WHERE KODE_ASSET_AMS = '.$request->kode_asset_ams.' ');

			$result = 1;
		}
		catch (\Exception $e) 
		{
            DB::rollback();
            $result = 0;
       	}

		if( $result == 1 )
		{
			Session::flash('message', 'Updated success ('.$request->kode_asset_ams.' - '.$request->nama_asset.') ');
			
			if( $request->tipe == 1 )
			{
				return Redirect::to('/disposal-penjualan');
			}
			elseif( $request->tipe == 2 )
			{
				return Redirect::to('/disposal-hilang');
			}
			else
			{
				return Redirect::to('/disposal-rusak');
			}
			
		}
		else
		{
			Session::flash('alert', 'Updated failed!');
			
			if( $request->tipe == 1 )
			{
				return Redirect::to('/disposal-penjualan');
			}
			elseif( $request->tipe == 2 )
			{
				return Redirect::to('/disposal-hilang');
			}
			else
			{
				return Redirect::to('/disposal-rusak');
			}

		}
	
    }

    function validasi_harga_perolehan($nilai,$jenis)
    {
    	$hp_default = 50000000;
    	$nilai_temp_plus = array();
    	$nilai_temp_minus = array();
		// dd($nilai);
    	if($nilai->HARGA_PEROLEHAN == 0)
    	{
    		$result = array('result'=> 0, 'message'=> 'Gagal Proses, Harga Perolehan tidak boleh kosong (0) (Rp. '.number_format($nilai->HARGA_PEROLEHAN,0,',','.').' / KODE ASSET AMS : '.$nilai->KODE_ASSET_AMS.' - '.$nilai->NAMA_ASSET_1.' ) ');
	    		return $result;
    	}

    	if($jenis == 'plus')
    	{
    		if($nilai->HARGA_PEROLEHAN >= $hp_default)
	    	{
	    		//array_push($nilai_temp_plus, $nilai);
	    		$nilai_temp_plus[] = $nilai->HARGA_PEROLEHAN;
	    		$result = array('result'=> 1, 'message'=> $nilai_temp_plus);
	    		return $result;
	    	}
	    	else
	    	{
	    		$result = array('result'=> 0, 'message'=> 'Gagal Proses, Harga Perolehan dibawah Rp. 50 juta (Rp. '.number_format($nilai->HARGA_PEROLEHAN,0,',','.').' / KODE ASSET AMS : '.$nilai->KODE_ASSET_AMS.' - '.$nilai->NAMA_ASSET_1.' ) ');
	    		return $result;
	    	}
    	}
    	else
    	{
    		if($nilai->HARGA_PEROLEHAN < $hp_default)
	    	{
	    		//array_push($nilai_temp_plus, $nilai);
	    		$nilai_temp_minus[] = $nilai->HARGA_PEROLEHAN;
	    		$result = array('result'=> 1, 'message'=> $nilai_temp_minus);
	    		return $result;
	    	}
	    	else
	    	{
	    		$result = array('result'=> 0, 'message'=> 'Gagal Proses, Harga Perolehan diatas Rp. 50 juta (Rp. '.number_format($nilai->HARGA_PEROLEHAN,0,',','.').' / KODE ASSET AMS : '.$nilai->KODE_ASSET_AMS.' - '.$nilai->NAMA_ASSET_1.' ) ');
	    		return $result;
	    	}
    	}
    }

    function validasi_lokasi_bacode($nilai,$lokasi_awal)
    {
    	//echo $lokasi_awal; die();

    	if( $lokasi_awal == $nilai->LOKASI_BA_CODE )
    	{
    		$result = array('result'=> 1, 'message'=> "success validasi_lokasi_bacode");
	    	return $result;
    	}
    	else
    	{
    		$result = array('result'=> 0, 'message'=> 'Gagal Proses, Lokasi BA Code tidak sama (KODE ASSET AMS : '.$nilai->KODE_ASSET_AMS.' - '.$nilai->NAMA_ASSET_1.' ) ');
	    		return $result;
    	}
    }

    function remove_rusak($kode_asset_ams)
    {	
    	DB::beginTransaction();

		try 
		{

			DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");
			DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");

			DB::commit();

			Session::flash('message', 'Success delete data disposal! (KODE AMS : '.$kode_asset_ams.') ');
			return Redirect::to('/disposal-rusak');
		} 
		catch (\Exception $e) 
		{
			DB::rollback();
			Session::flash('message', $e->getMessage()); 
			return Redirect::to('/disposal-rusak');
		}
    }

    function get_ac_awal($kode_asset_ams)
    {
    	$sql = " SELECT a.ASSET_CONTROLLER FROM TM_MSTR_ASSET a WHERE a.KODE_ASSET_AMS = '".$kode_asset_ams."' ";
    	$data = DB::SELECT($sql);
    	//echo "<pre>"; print_r($data); die();
    	if(!empty($data))
    	{
    		$ac = $data[0]->ASSET_CONTROLLER;
    	}
    	else
    	{
    		$ac = "not found";
    	}

    	//echo "2<br/>".$ac; die();

    	return $ac;
    }

    function validasi_asset_controller($kode_asset_ams,$ac_awal)
    {
    	$ac = $this->get_ac_awal($kode_asset_ams);

    	if( $ac == 'not found' )
    	{
    		$result = array('result'=> 0, 'message'=> 'Asset Controller not found (KODE ASSET AMS : '.$kode_asset_ams.' )');
	    	return $result;
    	}
    	else if( $ac == $ac_awal )
    	{
    		$result = array('result'=> 1, 'message'=> "success validasi_asset_controller");
	    	return $result;
    	}
    	else
    	{
    		$result = array('result'=> 0, 'message'=> 'Gagal Proses, AC Controller tidak sama (KODE ASSET AMS : '.$kode_asset_ams.' ) ');
	    	return $result;
    	}
    }

    function get_harga_perolehan($row)
    {
    	
    	$BUKRS = substr($row->BA_PEMILIK_ASSET,0,2);
		$YEAR = date('Y');

    	$ANLN1 = $this->get_anln1($row->KODE_ASSET_SAP);
    	
    	if( $row->KODE_ASSET_SUBNO_SAP == '') 
    	{
    		$ANLN2 = '0000';
    	}
    	else
    	{
    		$ANLN2 = $row->KODE_ASSET_SUBNO_SAP;
    	}
		
		

    	$service = API::exec(array(
            'request' => 'GET',
			'host' => 'ldap',
            'method' => "assets_price?BUKRS={$BUKRS}&ANLN1={$ANLN1}&ANLN2=$ANLN2&AFABE=15&GJAHR={$YEAR}", 
            //'method' => "assets_price?BUKRS=41&ANLN1=000060100612&ANLN2=0000&AFABE=1&GJAHR=2019", 
            //http://tap-ldapdev.tap-agri.com/data-sap/assets_price?BUKRS=41&ANLN1=000060100612&ANLN2=0000&AFABE=1&GJAHR=2019
		));
        
		$data = $service;

        if(!empty($data))
        {
        	$nilai = $data;
        }
        else
        {
        	$nilai = 0;
        }

		// dd($service,$BUKRS,$ANLN1,$ANLN2,$row->KODE_ASSET_SAP);
        return $nilai*100;
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

    function get_list_skip_harga_perolehan()
    {
    	$result = array();
    	$sql = " SELECT DESCRIPTION_CODE FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_synch_sap' AND STATUS = 't' ";
    	$data = DB::SELECT($sql);
    	//echo "1<pre>"; print_r($data); die();
    	if(!empty($data))
    	{
    		foreach( $data as $k => $v )
    		{
				$result[] =  $v->DESCRIPTION_CODE;
    		}
    	}

    	return $result;
    }

    function validasi_berkas_peraset($kode_asset_ams)
    {

    	$sql = " SELECT COUNT(*) AS TOTAL FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '".$kode_asset_ams."' ";
    	$data = DB::SELECT($sql); 

    	if($data[0]->TOTAL == 0)
    	{
    		$result = array('result'=> 0, 'message'=> 'Gagal Proses, berkas belum di upload (KODE ASSET AMS : '.$kode_asset_ams.' ) ');
    	}
    	else
    	{
    		/*
    		//Validasi Berkas Serah Terima
    		$sql2 = " SELECT COUNT(*) AS TOTAL FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '".$kode_asset_ams."' AND FILE_CATEGORY = 'serah_terima' ";
    		$data2 = DB::SELECT($sql2); 

    		if( $data2[0]->TOTAL == 0 )
    		{
    			$result = array('result'=> 0, 'message'=> 'Gagal Proses, file berkas terima belum di upload (KODE ASSET AMS : '.$kode_asset_ams.' ) ');
    		}
    		else
    		{
    			$result = array('result'=> 1, 'message'=> "success validasi_berkas_peraset");
    		}
    		*/
    		$result = array('result'=> 1, 'message'=> "success validasi_berkas_peraset");
    	}

		return $result;
    }

    function upload_berkas_hilang(Request $request)
    {
    	$req = $request->all();

		//MULTIPLE UPLOAD
		$sql = " SELECT DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'hilang' ";
		$data = DB::SELECT($sql);

		if( !empty($data) )
		{
			foreach($data as $k => $v)
			{
				$dc = explode("-",$v->DESCRIPTION);
				$desc_code = str_replace(" ", "_", $dc[0]);
				//echo $desc_code;
				//$desc_code = str_replace(" ", "_", $v->DESCRIPTION);
				//echo "1<pre>"; print_r($v);
				$this->upload_multiple_berkas($req, $desc_code);
			}
			//die();
		}
		//END MULTIPLE UPLOAD

		if( !empty($_FILES['serah_terima']['name']) )
		{
			$file_name = str_replace(" ", "_", $_FILES['serah_terima']['name']);
			$user_id = Session::get('user_id');
			$file_category = 'serah_terima';
			$file_category_label = strtoupper(str_replace("_", " ", $file_category));

			// #1 VALIDASI SIZE DOC MAX 1 MB
			$max_docsize = 1000000;
			if( $_FILES['serah_terima']['size'] != 0 )
			{
				if( $_FILES['serah_terima']['size'] > $max_docsize )
				{
					Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file maksimal 1MB'); 
					return Redirect::to('/disposal-hilang');
				}
			}
			else
			{
				Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file 0 MB'); 
					return Redirect::to('/disposal-hilang');
			}

			$file_upload = base64_encode(file_get_contents(addslashes($_FILES['serah_terima']['tmp_name'])));

			// #2 VALIDASI FILE UPLOAD EXIST
			$validasi_file_exist = $this->validasi_file_exist($request->kode_asset_ams,$file_category);
			if( $validasi_file_exist == 0 )
			{
				$sql = "INSERT INTO TR_DISPOSAL_TEMP_FILE(
							KODE_ASSET_AMS,
							FILE_CATEGORY,
							JENIS_FILE,
							FILE_NAME,
							DOC_SIZE,
							JENIS_PENGAJUAN,
							FILE_UPLOAD,
							NOTES,
							CREATED_BY)
								VALUES('{$request->kode_asset_ams}',
							'serah_terima',
							'".$_FILES['serah_terima']['type']."',
							'{$file_name}',
							'".$_FILES['serah_terima']['size']."',
							'{$request->tipe}',
							'{$file_upload}',
							'{$request->notes_asset}',
							'{$user_id}')";
			}
			else
			{
				$sql = "UPDATE TR_DISPOSAL_TEMP_FILE SET JENIS_FILE = '".$_FILES['serah_terima']['type']."', FILE_NAME = '{$file_name}', DOC_SIZE = '".$_FILES['serah_terima']['size']."', FILE_UPLOAD = '{$file_upload}', NOTES = '{$request->notes_asset}', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE KODE_ASSET_AMS = {$request->kode_asset_ams} AND FILE_CATEGORY = '{$file_category}' ";
			}

			DB::beginTransaction();

			try 
			{
				DB::insert($sql);
				DB::commit();

				Session::flash('message', 'Success upload data! (KODE AMS : '.$request->kode_asset_ams.') ');
				return Redirect::to('/disposal-hilang');
			} 
			catch (\Exception $e) 
			{
				DB::rollback();
				Session::flash('message', $e->getMessage()); 
				return Redirect::to('/disposal-hilang');
			}
		}
		else
		{
			Session::flash('message', 'Success upload data! (KODE AMS : '.$request->kode_asset_ams.') ');
			return Redirect::to('/disposal-hilang');
		}
    }

    function validasi_file_exist($kode_asset_ams,$file_category)
    {
    	$sql = " SELECT COUNT(*) AS TOTAL FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' AND FILE_CATEGORY = '{$file_category}' ";
    	$data = DB::SELECT($sql); 

    	if($data[0]->TOTAL == 0)
    	{
    		return 0;
    	}
    	else
    	{
    		return 1;
    	}
    }

    function list_kategori_upload($jenis)
    {
    	$result = array();

    	// IF DISPOSAL HILANG
    	if( $jenis == 2 )
    	{
    		$sql = " SELECT ID, DESCRIPTION_CODE, DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'hilang' AND STATUS = 't' ";
    	}
    	elseif( $jenis == 3 )
    	{
    		//IF DISPOSAL RUSAK
    		$sql = " SELECT ID, DESCRIPTION_CODE, DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'rusak' AND STATUS = 't' ";
    	}
    	else
    	{
    		//IF DISPOSAL PENJUALAN
    		$sql = " SELECT ID, DESCRIPTION_CODE, DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'jual' AND STATUS = 't' ";
    	}

    	$data = DB::SELECT($sql);
    	
    	if(!empty($data))
    	{
    		foreach($data as $k => $v)
    		{
    			$result[] = array(
    				"ID" => $v->ID,
    				"DESCRIPTION_CODE" => $v->DESCRIPTION_CODE,
    				"DESCRIPTION" => $v->DESCRIPTION,
    				"DETAIL" => $this->get_detail_berkas($v->DESCRIPTION,$jenis)
    			);
    		}
    	}
    	return $result;
    }

    function upload_multiple_berkas($req, $desc_code)
    {
    	//echo "2<pre>"; print_r($_FILES); die();
    	
    	if( @$req['tipe'] == 2 )
    	{
    		$tipe = 'hilang';
    	}
    	else if( @$req['tipe'] == 3 )
    	{
    		$tipe = 'rusak';
    	}else
    	{
    		$tipe = 'penjualan';
    	}
    	
    	if( @$_FILES[''.$desc_code.'']['name'] != '')
    	{
			$file_name = str_replace(" ", "_", $_FILES[''.$desc_code.'']['name']);
			$user_id = Session::get('user_id');
			$file_category = $desc_code;
			$file_category_label = strtoupper(str_replace("_", " ", $desc_code));

			// #3 VALIDASI TYPEFILE JPG/PNG/PDF
            if( $_FILES[''.$desc_code.'']['type'] != 'image/jpeg' && $_FILES[''.$desc_code.'']['type'] != 'image/png' && $_FILES[''.$desc_code.'']['type'] != 'application/pdf' )
            {
                Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), file type hanya PNG/JPG/PDF'); 
                return Redirect::to('/disposal-'.$tipe.'');
                die();
            }

			// #1 VALIDASI SIZE DOC MAX 1 MB
			$max_docsize = 1000000;
			if( $_FILES[''.$desc_code.'']['size'] != 0 )
			{
				if( $_FILES[''.$desc_code.'']['size'] > $max_docsize )
				{
					Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file maksimal 1MB'); 
					return Redirect::to('/disposal-'.$tipe.'');
				}
			}
			else
			{
				Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file 0 MB'); 
					return Redirect::to('/disposal-'.$tipe.'');
			}

			$file_upload = base64_encode(file_get_contents(addslashes($_FILES[''.$desc_code.'']['tmp_name'])));

			// #2 VALIDASI FILE UPLOAD EXIST
			$validasi_file_exist = $this->validasi_file_exist($req['kode_asset_ams'],$file_category);
			if( $validasi_file_exist == 0 )
			{
				$sql = "INSERT INTO TR_DISPOSAL_TEMP_FILE(
							KODE_ASSET_AMS,
							FILE_CATEGORY,
							JENIS_FILE,
							FILE_NAME,
							DOC_SIZE,
							JENIS_PENGAJUAN,
							FILE_UPLOAD,
							NOTES,
							CREATED_BY)
								VALUES('".$req['kode_asset_ams']."',
							'{$desc_code}',
							'".$_FILES[''.$desc_code.'']['type']."',
							'{$file_name}',
							'".$_FILES[''.$desc_code.'']['size']."',
							'".$req['tipe']."',
							'{$file_upload}',
							'".$req['notes_asset']."',
							'{$user_id}')";
			}
			else
			{
				$sql = "UPDATE TR_DISPOSAL_TEMP_FILE SET JENIS_FILE = '".$_FILES[''.$desc_code.'']['type']."', FILE_NAME = '{$file_name}', DOC_SIZE = '".$_FILES[''.$desc_code.'']['size']."', FILE_UPLOAD = '{$file_upload}', NOTES = '".$req['notes_asset']."', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE KODE_ASSET_AMS = '".$req['kode_asset_ams']."' AND FILE_CATEGORY = '{$file_category}' ";
			}

			//echo $sql; die();

			DB::beginTransaction();

			try 
			{
				DB::insert($sql);
				DB::commit();
			} 
			catch (\Exception $e) 
			{
				DB::rollback();
			}
    	}

    	return true;
    }

    function upload_berkas_rusak(Request $request)
    {
    	$req = $request->all();

		//MULTIPLE UPLOAD
		$sql = " SELECT DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'rusak' ";
		$data = DB::SELECT($sql);

		if( !empty($data) )
		{
			foreach($data as $k => $v)
			{
				$dc = explode("-",$v->DESCRIPTION);
				$desc_code = str_replace(" ", "_", $dc[0]);
				$this->upload_multiple_berkas($req, $desc_code);
			}
		}
		//END MULTIPLE UPLOAD

		if( !empty($_FILES['serah_terima']['name']) )
		{
			
			$file_name = str_replace(" ", "_", $_FILES['serah_terima']['name']);
			$user_id = Session::get('user_id');
			$file_category = 'serah_terima';
			$file_category_label = strtoupper(str_replace("_", " ", $file_category));

			// #1 VALIDASI SIZE DOC MAX 1 MB
			$max_docsize = 1000000;
			if( $_FILES['serah_terima']['size'] != 0 )
			{
				if( $_FILES['serah_terima']['size'] > $max_docsize )
				{
					Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file maksimal 1MB'); 
					return Redirect::to('/disposal-rusak');
				}
			}
			else
			{
				Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file 0 MB'); 
					return Redirect::to('/disposal-rusak');
			}

			$file_upload = base64_encode(file_get_contents(addslashes($_FILES['serah_terima']['tmp_name'])));

			// #2 VALIDASI FILE UPLOAD EXIST
			$validasi_file_exist = $this->validasi_file_exist($request->kode_asset_ams,$file_category);
			if( $validasi_file_exist == 0 )
			{
				$sql = "INSERT INTO TR_DISPOSAL_TEMP_FILE(
							KODE_ASSET_AMS,
							FILE_CATEGORY,
							JENIS_FILE,
							FILE_NAME,
							DOC_SIZE,
							JENIS_PENGAJUAN,
							FILE_UPLOAD,
							NOTES,
							CREATED_BY)
								VALUES('{$request->kode_asset_ams}',
							'serah_terima',
							'".$_FILES['serah_terima']['type']."',
							'{$file_name}',
							'".$_FILES['serah_terima']['size']."',
							'{$request->tipe}',
							'{$file_upload}',
							'{$request->notes_asset}',
							'{$user_id}')";
			}
			else
			{
				$sql = "UPDATE TR_DISPOSAL_TEMP_FILE SET JENIS_FILE = '".$_FILES['serah_terima']['type']."', FILE_NAME = '{$file_name}', DOC_SIZE = '".$_FILES['serah_terima']['size']."', FILE_UPLOAD = '{$file_upload}', NOTES = '{$request->notes_asset}', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE KODE_ASSET_AMS = {$request->kode_asset_ams} AND FILE_CATEGORY = '{$file_category}' ";
			}

			DB::beginTransaction();

			try 
			{
				DB::insert($sql);
				DB::commit();

				Session::flash('message', 'Success upload data! (KODE AMS : '.$request->kode_asset_ams.') ');
				return Redirect::to('/disposal-rusak');
			} 
			catch (\Exception $e) 
			{
				DB::rollback();
				Session::flash('message', $e->getMessage()); 
				return Redirect::to('/disposal-rusak');
			}
		}
		else
		{
			Session::flash('message', 'Success upload data! (KODE AMS : '.$request->kode_asset_ams.') ');
			return Redirect::to('/disposal-rusak');
		}
    }

    function upload_berkas(Request $request)
    {
    	$req = $request->all();

		//MULTIPLE UPLOAD
		$sql = " SELECT DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'jual' ";
		$data = DB::SELECT($sql);

		if( !empty($data) )
		{
			foreach($data as $k => $v)
			{
				$dc = explode("-",$v->DESCRIPTION);
				$desc_code = str_replace(" ", "_", $dc[0]);
				//$desc_code = str_replace(" ", "_", $v->DESCRIPTION);
				$this->upload_multiple_berkas($req, $desc_code);
			}
		}
		//END MULTIPLE UPLOAD

		
		//echo "3<pre>"; print_r($_FILES); die();
		if( !empty($_FILES['serah_terima']['name']) )
		{
			$file_name = str_replace(" ", "_", $_FILES['serah_terima']['name']);
			$file_category = 'serah_terima';
			$file_category_label = strtoupper(str_replace("_", " ", $file_category));
			$user_id = Session::get('user_id');

			// #1 VALIDASI SIZE DOC MAX 1 MB
			$max_docsize = 1000000;
			if( $_FILES['serah_terima']['size'] != 0 )
			{
				if( $_FILES['serah_terima']['size'] > $max_docsize )
				{
					Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file maksimal 1MB'); 
					return Redirect::to('/disposal-penjualan');
				}
			}
			else
			{
				Session::flash('alert', 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file 0 MB'); 
					return Redirect::to('/disposal-penjualan');
			}

			$file_upload = base64_encode(file_get_contents(addslashes($_FILES['serah_terima']['tmp_name'])));

			// #2 VALIDASI FILE UPLOAD EXIST
			$validasi_file_exist = $this->validasi_file_exist($request->kode_asset_ams,$file_category);
			if( $validasi_file_exist == 0 )
			{
				$sql = "INSERT INTO TR_DISPOSAL_TEMP_FILE(
							KODE_ASSET_AMS,
							FILE_CATEGORY,
							JENIS_FILE,
							FILE_NAME,
							DOC_SIZE,
							JENIS_PENGAJUAN,
							FILE_UPLOAD,
							NOTES,
							CREATED_BY)
								VALUES('{$request->kode_asset_ams}',
							'serah_terima',
							'".$_FILES['serah_terima']['type']."',
							'{$file_name}',
							'".$_FILES['serah_terima']['size']."',
							'{$request->tipe}',
							'{$file_upload}',
							'{$request->notes_asset}',
							'{$user_id}')";
			}
			else
			{
				$sql = "UPDATE TR_DISPOSAL_TEMP_FILE SET JENIS_FILE = '".$_FILES['serah_terima']['type']."', FILE_NAME = '{$file_name}', DOC_SIZE = '".$_FILES['serah_terima']['size']."', FILE_UPLOAD = '{$file_upload}', NOTES = '{$request->notes_asset}', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE KODE_ASSET_AMS = {$request->kode_asset_ams} AND FILE_CATEGORY = '{$file_category}' ";
			}

			DB::beginTransaction();

			try 
			{
				DB::insert($sql);
				DB::commit();

				Session::flash('message', 'Success upload data! (KODE AMS : '.$request->kode_asset_ams.') ');
				return Redirect::to('/disposal-penjualan');
			} 
			catch (\Exception $e) 
			{
				DB::rollback();
				Session::flash('message', $e->getMessage()); 
				return Redirect::to('/disposal-penjualan');
			}
		}
		else
		{
			Session::flash('message', 'Success upload data! (KODE AMS : '.$request->kode_asset_ams.') ');
			return Redirect::to('/disposal-penjualan');
		}

    }

    function berkas_disposal($kode_asset_ams)
    {
        $sql = " SELECT b.DOC_SIZE, b.FILE_NAME, b.FILE_CATEGORY, b.FILE_UPLOAD, b.JENIS_FILE
					FROM TR_DISPOSAL_TEMP_FILE b
					WHERE b.KODE_ASSET_AMS = '".$kode_asset_ams."' "; 
        $data = DB::SELECT($sql);
        
        $l = "";
        if(!empty($data))
        {
            $l .= '<center>';
            $l .= '<h1>'.$kode_asset_ams.'</h1><br/>';

            foreach($data as $k => $v)
            {
                $file_category = str_replace("_", " ", $v->FILE_CATEGORY);

                if( $v->JENIS_FILE == 'image/jpeg' || $v->JENIS_FILE == 'image/png' )
                {
                    $l .= '<div class="caption"><h3><u>'.strtoupper($file_category).'</u><br/><br/><img src="data:image/jpeg;base64,'.$v->FILE_UPLOAD.'"/><br/>'. $v->FILE_NAME. '</h3></div>';
                }
                else if($v->JENIS_FILE == 'application/pdf')
                {
                    $l .= ''.strtoupper($file_category).'<br/> <object data="data:application/pdf;base64,'.$v->FILE_UPLOAD.'" type="'.$v->JENIS_FILE.'" style="height:100%;width:100%"></object><br/>'. $v->FILE_NAME. '';
                }
                else
                {
                	$l .= '<div class="caption"><h3><u>'.strtoupper($file_category).'</u><br/><br/><a href="'.url("/disposal/view-berkas-by-type/".$kode_asset_ams."/".$v->FILE_CATEGORY."").'">'. $v->FILE_NAME. '</a></h3></div>';
                }
            }
        }
        else
        {
            $l .= "<center><h1>FILE NOT FOUND</h1></center>";
        }

        $l .= '</center>';
        echo $l; 
    }

    function file_download($kode_asset_ams,$file_category)  
    {
    	$data = DB::SELECT(" SELECT * FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = {$kode_asset_ams} AND FILE_CATEGORY = '{$file_category}' ");
    	//echo "4<pre>"; print_r($data); die();
    	if(!empty($data))
    	{
    		//if( $data[0]->JENIS_FILE == 'application/vnd.openxmlformats-officedocument.spre' || $data[0]->JENIS_FILE == 'application/vnd.ms-excel' ){
    			header('Content-type: '.$data[0]->JENIS_FILE.';base64');
	    		header('Content-Disposition: attachment; filename="'.$data[0]->FILE_NAME.'"');
    		//}
	    		print $data[0]->FILE_UPLOAD;
    	}
    	//header('Content-type: '.$data[0]->JENIS_FILE.'');
	    //header('Content-Disposition: attachment; filename="'.$data[0]->FILE_NAME.'"');
	    die();
	}

    function proses_upload_file($kode_asset_ams,$no_reg,$jenis_pengajuan)
    {
    	$data = DB::SELECT(" SELECT * FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '{$kode_asset_ams}' ");

    	if(!empty($data))
    	{
    		foreach ($data as $k => $v) 
    		{
    			DB::beginTransaction();

			   try 
			   {
			        DB::INSERT(" INSERT INTO TR_DISPOSAL_ASSET_FILE(NO_REG,KODE_ASSET_AMS,FILE_CATEGORY,JENIS_FILE,FILE_NAME,FILE_UPLOAD,DOC_SIZE,JENIS_PENGAJUAN,NOTES,CREATED_BY)VALUES('".$no_reg."','".$v->KODE_ASSET_AMS."','".$v->FILE_CATEGORY."','".$v->JENIS_FILE."','".$v->FILE_NAME."','".$v->FILE_UPLOAD."','".$v->DOC_SIZE."','".$v->JENIS_PENGAJUAN."','".$v->NOTES."','".Session::get('user_id')."') ");

			        DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = {$v->KODE_ASSET_AMS} ");

			        DB::commit();
			   } 
			   catch (\Exception $e) 
			   {
			        DB::rollback();
			        return array('status'=>false,'message'=> $e->getMessage());
			   }
    		}

    	}

    	return array('status'=>true,'message'=> 'Success insert file');
    }

    function get_list_berkas($jenis_pengajuan)
    {
    	$data = array();
    	$data = DB::SELECT("SELECT * FROM TR_DISPOSAL_TEMP_FILE WHERE JENIS_PENGAJUAN = $jenis_pengajuan");
    	return $data;
    }

    function get_detail_berkas($description,$jenis_pengajuan)
    {
    	$file_category = str_replace(" ", "_", $description);
    	$data = array();
    	$data = DB::SELECT("SELECT * FROM TR_DISPOSAL_TEMP_FILE WHERE JENIS_PENGAJUAN = $jenis_pengajuan AND FILE_CATEGORY = '".$file_category."' ");
    	return $data;
    }

    function berkas_serah_terima($kode_asset_ams)
    {
        $sql = " SELECT b.DOC_SIZE, b.FILE_NAME, b.FILE_CATEGORY, b.FILE_UPLOAD, b.JENIS_FILE
					FROM TR_DISPOSAL_TEMP_FILE b
					WHERE b.KODE_ASSET_AMS = '".$kode_asset_ams."' AND b.FILE_CATEGORY = 'serah_terima' "; 
        $data = DB::SELECT($sql);
        
        $l = "";
        if(!empty($data))
        {
            foreach($data as $k => $v)
            {
                $file_category = str_replace("_", " ", $v->FILE_CATEGORY);

                if( $v->JENIS_FILE == 'image/jpeg' || $v->JENIS_FILE == 'image/png' )
                {
                    $l .= '<a href="'.url('disposal/view-berkas-detail/'.$kode_asset_ams.'/'.$v->FILE_CATEGORY.'').'" target="_blank">'.$v->FILE_NAME.'</a>';
                }
                else if($v->JENIS_FILE == 'application/pdf')
                {
                    $l .= $v->FILE_NAME;
                }
                else
                {
                	$l .= $v->FILE_NAME;

                	/*
                    $data_excel = explode(",",$v->FILE_UPLOAD);
                    header('Content-type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment; filename="'.$v->FILE_NAME.'"');
                    print $data_excel[1];
                    die();
                    */
                }
            }
        }

        echo $l; 
    }

    function berkas_disposal_detail($kode_asset_ams,$file_category)
    {
        $sql = " SELECT b.DOC_SIZE, b.FILE_NAME, b.FILE_CATEGORY, b.FILE_UPLOAD, b.JENIS_FILE
					FROM TR_DISPOSAL_TEMP_FILE b
					WHERE b.KODE_ASSET_AMS = '".$kode_asset_ams."' AND b.FILE_CATEGORY = '".$file_category."' "; 
        $data = DB::SELECT($sql);
        
        $l = "";
        if(!empty($data))
        {
            $l .= '<center>';
            $l .= '<h1>'.$kode_asset_ams.'</h1><br/>';

            foreach($data as $k => $v)
            {
                $file_category = str_replace("_", " ", $v->FILE_CATEGORY);

                if( $v->JENIS_FILE == 'image/jpeg' || $v->JENIS_FILE == 'image/png' )
                {
                    $l .= '<div class="caption"><h3>'.strtoupper($file_category).'<br/><img src="data:image/jpeg;base64,'.$v->FILE_UPLOAD.'"/><br/>'. $v->FILE_NAME. '</h3></div>';
                }
                else if($v->JENIS_FILE == 'application/pdf')
                {
                    $l .= ''.strtoupper($file_category).'<br/> <object data="data:application/pdf;base64,'.$v->FILE_UPLOAD.'" type="'.$v->JENIS_FILE.'" style="height:100%;width:100%"></object><br/>'. $v->FILE_NAME. '';
                }
                else
                {
                    $data_excel = explode(",",$v->FILE_UPLOAD);
                    header('Content-type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment; filename="'.$v->FILE_NAME.'"');
                    print $data_excel[1];
                    die();
                }
            }
        }
        else
        {
            $l .= "<center><h1>FILE NOT FOUND</h1></center>";
        }

        $l .= '</center>';
        echo $l; 
    }

    function list_file_category($kode_asset_ams,$jenis_pengajuan)
    {
    	$result = array();
    	$l = "";

    	// IF DISPOSAL HILANG
    	if( $jenis_pengajuan == 2 )
    	{
    		$sql = " SELECT ID, DESCRIPTION_CODE, DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'hilang' AND STATUS = 't' ";
    	}
    	elseif( $jenis_pengajuan == 3 )
    	{
    		//IF DISPOSAL RUSAK
    		$sql = " SELECT ID, DESCRIPTION_CODE, DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'rusak' AND STATUS = 't' ";
    	}
    	else
    	{
    		//IF DISPOSAL PENJUALAN
    		$sql = " SELECT ID, DESCRIPTION_CODE, DESCRIPTION FROM TM_GENERAL_DATA WHERE GENERAL_CODE = 'ba_disposal_upload' AND DESCRIPTION_CODE = 'jual' AND STATUS = 't' ";
    	}

    	$data = DB::SELECT($sql);
    	
    	if(!empty($data))
    	{
    		$mandatory = "";
			$mandatory_label = "";
    		foreach($data as $k => $v)
    		{
    			$dc = explode("-",$v->DESCRIPTION);

    			$DESCRIPTION_CODE = str_replace(" ", "_", $dc[0]);

    			$detail = DB::SELECT("SELECT * FROM TR_DISPOSAL_TEMP_FILE WHERE FILE_CATEGORY = '".$DESCRIPTION_CODE."' AND KODE_ASSET_AMS = '".$kode_asset_ams."' ");
				$total_detail = count($detail); 

				if( !empty($dc[1]) )
				{
					if( $total_detail == 0 )
    				{	
    					$mandatory = 'required';
    					$mandatory_label = '<span style="color:red">*</span>';	
    				}
    				else
    				{
    					$mandatory = '';
    					$mandatory_label = '<span style="color:red">*</span>';
    				}
				}
				else
				{
					$mandatory = '';
    				$mandatory_label = '';
				}

        		$l .= '<div class="form-group">
			                <label class="control-label col-xs-4" >'.strtoupper(trim($dc[0])).' '.$mandatory_label.'</label>
			                <div class="col-xs-8">
			                    <input type="file" class="form-control" id="'.$DESCRIPTION_CODE.'" name="'.$DESCRIPTION_CODE.'" value="" placeholder="Upload '.$DESCRIPTION_CODE.'" '.$mandatory.'/>';

			    		if( !empty($detail) )
						{
							foreach( $detail as $kk => $vv )
							{
								$l .= '<span id="file-berkas-'.$vv->KODE_ASSET_AMS.'"><a href="'.url('disposal/view-berkas-detail/'.$vv->KODE_ASSET_AMS.'/'.$DESCRIPTION_CODE.'').'" target="_blank">'.$vv->FILE_NAME.'</a> <a href="#"><i class="fa fa-trash del-berkas" onClick="delete_berkas('.$vv->KODE_ASSET_AMS.',\''.$vv->FILE_CATEGORY.'\')"></i></a></span> ';	
							}
						}

			    $l .= '</div>
			            </div>';
    		}
    	}

    	echo $l; 
    }

    function berkas_notes($kode_asset_ams)
    {
        $records = array();

        $sql = " SELECT DISTINCT(NOTES) AS CATATAN FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = '".$kode_asset_ams."' ";
        $data = DB::SELECT($sql);
        
        if($data)
        {
            foreach ($data as $k => $v) 
            {
                $records[] = array(
                    'notes' => trim($v->CATATAN),
                );

            }
        }
        else
        {
            $records[0] = array();
        }
        echo json_encode($records[0]);
    }

    function delete_berkas_temp(Request $request)
    {
    	$req = $request->all();

        DB::beginTransaction();

        try 
        {
            $user_id = Session::get('user_id');

            DB::DELETE(" DELETE FROM TR_DISPOSAL_TEMP_FILE WHERE KODE_ASSET_AMS = {$request->kode_asset_ams} AND FILE_CATEGORY = '{$request->file_category}' ");    

            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully updated']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }
}

?>