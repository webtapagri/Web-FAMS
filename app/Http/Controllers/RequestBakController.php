<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\TrUser;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;
use Spipu\Html2Pdf\Html2Pdf;
use App\TR_REG_ASSET;
use App\TR_REG_ASSET_FILE;
use App\TR_REG_ASSET_DETAIL;
use App\TR_REG_ASSET_DETAIL_FILE;
use App\TR_REG_ASSET_DETAIL_DETAIL;
use App\TR_REG_ASSET_DETAIL_PO;

use App\Mail\FamsEmail;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    public function index()
    {
        if (empty(Session::get('authenticated')))
            return redirect('/login');


        if (AccessRight::granted() === false) {
            $data['page_title'] = 'Oops! Unauthorized.';
            return response(view('errors.403')->with(compact('data')), 403);
        }

        $access = AccessRight::access();
        $data["page_title"] = "Request";
        $data["access"] = (object)$access;
        return view('request.index')->with(compact('data'));
    }

    public function create(Request $request) 
    {
        if (empty(Session::get('authenticated')))
            return redirect('/login');

        $data['ctree_mod'] = 'Pendaftaran';
        
        $data['page_title'] = 'Request '.($request->type == "amp" ? 'Melalui PO AMP':'Melalui PO Sendiri');
        $data['type'] = ($request->type == "amp" ? 'Melalui PO AMP':'Melalui PO Sendiri');
        $access = AccessRight::access();
        $data["access"] = (object)$access;
        
        //$data["ba_user"] = '"1211","2141","5121","3433"';
        $data["ba_user"] = '';
        $profile = AccessRight::profile();
        
        if($profile[0]->area_code)
        {
            $areacode = explode(',',$profile[0]->area_code); 
            if($areacode)
            {
                $ba_user = '';
                foreach( $areacode as $k => $v )
                {
                    $ba_user .= '"'.$v.'",' ;
                }
                $data["ba_user"] .= ''.$ba_user.'';
            }
        }

        //echo "<pre>"; print_r($data['ba_user']); die();

        if($request->type == "amp") {
            $data['ctree'] = 'request/create/amp';
            return view('request.amp')->with(compact('data'));
        }else {
            $data['ctree'] = 'request/create/po';
            return view('request.sap')->with(compact('data'));
        }
        
    }
    
    public function getPO(Request $request) 
    {
        $param = $_REQUEST;
        $service = API::exec(array(
            'request' => 'GET',
            'host' => 'ldap',
            'method' => "select_po/" . $param["no_po"]
        ));
        
        $data = $service;

        //echo "<pre>"; print_r($data); die();
        /*
            stdClass Object
            (
                [EBELN] => 2013009721
                [AEDAT] => 2018-12-17
                [LIFNR] => 2300000058
                [NAME1] => CITRA KENCANA
                [DETAIL_ITEM] => Array
                    (
                        [0] => stdClass Object
                            (
                                [EBELP] => 00001
                                [MATNR] => 000000000405010019
                                [MAKTX] => LEMARI ARSIP 86CM X 40CM X 176CM
                                [MENGE] => 1
                                [MEINS] => UN
                                [NETPR] => 2600000.00
                                [WERKS] => 2121
                            )

                        [1] => stdClass Object
                            (
                                [EBELP] => 00002
                                [MATNR] => 000000000405010020
                                [MAKTX] => MEJA MAKAN + 6 KURSI (SET)
                                [MENGE] => 4
                                [MEINS] => UN
                                [NETPR] => 1400000.00
                                [WERKS] => 2121
                            )
                    )
            )
        */

        $datax = array();
        
        if(isset( $data->EBELN)) 
        {
            /*foreach($data as $k => $v)
            {
                echo "<pre>"; print_r($v);
            }
            die();*/
            return response()->json(array('data' => $data));
        } 
        else 
        {
            return response()->json(array('data' => array())); 
        }
    }

    public function dataGrid() {
        $data = array(
            array(
                "id" => '1',
                "request_no" => '572120171023001',
                "request_date" => '2019-01-02',
                "controller_asset_code" => '',
                "fams_asset_code" => '',
                "verification" => '0',
                "satatus" => '0'
            ),
            array(
                "id" => '2',
                "request_no" => '572120171023002',
                "request_date" => '2019-01-02',
                "controller_asset_code" => '',
                "fams_asset_code" => '',
                "verification" => '0',
                "satatus" => '0'
            ),
            array(
                "id" => '3',
                "request_no" => '572120171023003',
                "request_date" => '2019-02-04',
                "controller_asset_code" => '',
                "fams_asset_code" => '',
                "verification" => '0',
                "satatus" => '0'
            ),
            array(
                "id" => '3',
                "request_no" => '572120171023004',
                "request_date" => '2019-01-03',
                "controller_asset_code" => '',
                "fams_asset_code" => '',
                "verification" => '0',
                "satatus" => '0'
            ),
            array(
                "id" => '4',
                "request_no" => '572120171023005',
                "request_date" => '2019-01-03',
                "controller_asset_code" => '',
                "fams_asset_code" => '',
                "verification" => '0',
                "satatus" => '0'
            ),
        );

        $iTotalRecords = count($data);
        //$iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = intval(10);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        //$iDisplayStart = intval($_REQUEST['start']);
        $iDisplayStart = intval(0);
        //$sEcho = intval($_REQUEST['draw']);
        $sEcho = intval(2);
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        for($i = $iDisplayStart; $i < $end; $i++) {
            $records["data"][] =  $data[$i];
        }

        if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        return response()->json($records);
    }

    public function store(Request $request)
    {
        $req = $request->all();
        $asset_type = "";
        //echo "4<pre>"; print_r($req); die();
        //return response()->json(["status"=>true, "message"=>"Document Created!", "new_noreg"=>"ini noreg"]);
        
        DB::beginTransaction();

       try 
       {
            //1 VALIDASI JENIS ASSET, GROUP, SUBGROUP HARUS SERAGAM IT@250719
            $validasi_asset_controller = $this->validasi_asset_controller($req);
            //echo "4<pre>"; print_r($validasi_asset_controller); die();

            if( $validasi_asset_controller['status'] == false )
            {
                return response()->json(['status' => false, "message" => "Create document gagal, Asset Controller tidak sama"]);
            }
            else
            {
                $asset_type = $validasi_asset_controller['message'];
            }

            // INSERT DATABASE
            $reg_no = $this->get_reg_no();
            $user_id = Session::get('user_id');
            $po_type = $request->po_type;
            if($po_type == 0){ $menu_code = 'P1'; }else{ $menu_code = 'P2'; }
            
            // INSERT TO PROCEDURE
            DB::STATEMENT('call create_approval("'.$menu_code.'", "'.$request->business_area.'","","'.$reg_no.'","'.$user_id.'","'.$asset_type.'","")');

            $asset_id = DB::table('TR_REG_ASSET')->insertGetId([
                "CREATED_BY" => Session::get('user_id'),
                "NO_REG" => $reg_no,
                "BUSINESS_AREA" => $request->business_area,
                "TYPE_TRANSAKSI" => 1,
                "PO_TYPE" => $request->po_type,
                "TANGGAL_REG" => date_format(date_create($request->request_date), "Y-m-d"),
                "NO_PO" => $request->po_no,
                "TANGGAL_PO" => date_format(date_create($request->po_date), "Y-m-d"),
                "KODE_VENDOR" => $request->vendor_code,
                "NAMA_VENDOR" => $request->vendor_name,
            ]);

            $no = 1;
            if( $request->docs) {
                foreach ($request->docs as $row) {
                    DB::table('TR_REG_ASSET_FILE')->insert([
                        "ASSET_REG_ID" => $asset_id,
                        "NO_FILE" => $no,
                        "NO_REG" => $reg_no,
                        "FILENAME" => $row['name'],
                        "DOC_SIZE" => $row["size"],
                        "FILE_CATEGORY" => $row['type'],
                        "FILE_UPLOAD" => $row['file'],
                        "CREATED_BY" => Session::get('user_id'),
                    ]);
                    $no++;
                }
            }

            $no = 0;
            $item_po = 1;
            if( $request->asset) 
            {
                foreach ($request->asset as $row) 
                {

                    if( !empty($row["item_po"]) )
                    {
                        $item_po_data = $row["item_po"];  
                    }
                    else
                    {
                        $item_po_data = $item_po;
                    }

                    //if ($row["item_po"]) {
                    if ($row["name"]) {
                        $reg_asset_po_id = DB::table( 'TR_REG_ASSET_DETAIL_PO')-> insertGetId([
                            "ASSET_REG_ID" =>  $asset_id,
                            "NO_REG" =>  $reg_no,
                            "NO_PO" =>  $request->po_no,
                            //"ITEM_PO" =>  $row["item_po"],
                            "ITEM_PO" =>  $item_po_data,
                            "KODE_MATERIAL" =>  $row["code"],
                            "NAMA_MATERIAL" =>  $row["name"],
                            "QUANTITY_PO" =>  $row["qty"],
                            "QUANTITY_SUBMIT" =>  $row["request_qty"],
                            "CREATED_BY" =>  Session::get('user_id'),
                        ]);
                        $detail = $row["detail"];

                        for ($i = 0; $i < count($detail); $i++) 
                        {

                            $reg_asset_detail_id = DB::table( 'TR_REG_ASSET_DETAIL')->insertGetId([
                                "ASSET_PO_ID" =>   $reg_asset_po_id,
                                "NO_REG_ITEM" =>  $i + 1,
                                "NO_REG" =>  $reg_no,
                                //"ITEM_PO" =>  $row["item_po"],
                                "ITEM_PO" =>  $item_po_data,
                                "KODE_MATERIAL" =>  $row["code"],
                                "NAMA_MATERIAL" =>  $row["name"],
                                "NO_PO" =>  $request->po_no,
                                "BA_PEMILIK_ASSET" =>  $request->business_area,
                                "JENIS_ASSET" =>  $detail[$i]["asset_type"],
                                "GROUP" =>  $detail[$i]["asset_group"],
                                "SUB_GROUP" =>  $detail[$i]["asset_sub_group"],
                                "ASSET_CLASS" =>  '',
                                "NAMA_ASSET" =>  $detail[$i]["asset_name"],
                                "MERK" =>  $detail[$i]["asset_brand"],
                                "SPESIFIKASI_OR_WARNA" =>  $detail[$i]["asset_specification"],
                                "NO_RANGKA_OR_NO_SERI" =>  $detail[$i]["asset_serie_no"],
                                "NO_MESIN_OR_IMEI" =>  $detail[$i]["asset_imei"],
                                "NO_POLISI" =>  $detail[$i]["asset_police_no"],
                                "LOKASI_BA_CODE" =>  $detail[$i]["asset_location"],
                                "LOKASI_BA_DESCRIPTION" => $detail[$i]["asset_location_desc"],
                                "KONDISI_ASSET" =>  $detail[$i]["asset_condition"],
                                "TAHUN_ASSET" =>  $detail[$i]["asset_year"],
                                "INFORMASI" =>  $detail[$i]["asset_info"],
                                "NAMA_PENANGGUNG_JAWAB_ASSET" =>  $detail[$i]["asset_pic_name"],
                                "JABATAN_PENANGGUNG_JAWAB_ASSET" =>  $detail[$i]["asset_pic_level"],
                                "CREATED_BY" =>  Session::get('user_id'),
                            ]); 

                            $item_file_id = ($no + 1) . ($i + 1);
           
                            if ($detail[$i]["foto_asset"]["name"]) 
                            {
                                DB::table( 'TR_REG_ASSET_DETAIL_FILE')->insert([
                                    "ASSET_PO_DETAIL_ID" =>  $reg_asset_detail_id,
                                    "NO_REG_ITEM_FILE" => $item_file_id,
                                    "NO_REG" => $reg_no,
                                    "JENIS_FOTO" => 'foto asset',
                                    "FILENAME" => $detail[$i]["foto_asset"]["name"],
                                    "DOC_SIZE" => $detail[$i]["foto_asset"]["size"],
                                    "FILE_CATEGORY" => 'asset',
                                    "FILE_UPLOAD" => $detail[$i]["foto_asset"]["file"],

                                ]);
                            }

                            if ($detail[$i]["foto_asset_seri"]["name"]) {
                                DB::table('TR_REG_ASSET_DETAIL_FILE')->insert([
                                    "ASSET_PO_DETAIL_ID" =>  $reg_asset_detail_id,
                                    "NO_REG_ITEM_FILE" => $item_file_id,
                                    "NO_REG" => $reg_no,
                                    "JENIS_FOTO" => 'Foto no. seri / no rangka',
                                    "FILENAME" => $detail[$i]["foto_asset_seri"]["name"],
                                    "DOC_SIZE" => $detail[$i]["foto_asset_seri"]["size"],
                                    "FILE_CATEGORY" => 'no seri',
                                    "FILE_UPLOAD" => $detail[$i]["foto_asset_seri"]["file"],
                                ]);
                            }

                            if ($detail[$i]["foto_asset_mesin"]["name"]) {
                                DB::table('TR_REG_ASSET_DETAIL_FILE')->insert([
                                    "ASSET_PO_DETAIL_ID" =>  $reg_asset_detail_id,
                                    "NO_REG_ITEM_FILE" => $item_file_id,
                                    "NO_REG" => $reg_no,
                                    "JENIS_FOTO" => 'Foto No msin / IMEI',
                                    "FILENAME" => $detail[$i]["foto_asset_mesin"]["name"],
                                    "DOC_SIZE" => $detail[$i]["foto_asset_mesin"]["size"],
                                    "FILE_CATEGORY" => 'imei',
                                    "FILE_UPLOAD" => $detail[$i]["foto_asset_mesin"]["file"],
                                ]);
                            }
                        }
                        
                        $no++;
                        $item_po++;
                    }
                }
            }
            DB::commit();

            //SEND EMAIL NOTIF CREATE PO 
            //$famsemail = new FamsEmail();
            //Mail::to("irvan27@gmail.com")->send($famsemail);

            //return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->edit_id ? 'updated' : 'added')]);
            return response()->json(["status"=>true, "message"=>"Document Created ({$reg_no})", "new_noreg"=>$reg_no ]);

       } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
       }
    }

    function validasi_asset_controller($req)
    {
        //echo "1<pre>"; print_r($req); die();

        $ac = array();
        $vv = "";
        
        if( $req['asset'] ) 
        {
            foreach ( $req['asset'] as $row ) 
            {
                //echo "2"; count($row["detail"]); die();
                if ($row["name"]) 
                {
                    $detail = $row["detail"];

                    for ($i = 0; $i < count($detail); $i++) 
                    {
                        $sql = " SELECT ASSET_CTRL_CODE FROM TM_ASSET_CONTROLLER_MAP WHERE JENIS_ASSET_CODE = '".$detail[$i]["asset_type"]."' AND GROUP_CODE = '".$detail[$i]["asset_group"]."' AND SUBGROUP_CODE = '".$detail[$i]["asset_sub_group"]."' "; //echo $sql; die();
                        $data = DB::SELECT($sql); 
                        //echo "1<pre>"; print_r($data); die();
                        if(!empty($data))
                        {
                            foreach($data as $k => $v)
                            {
                                //echo "1<pre>"; print_r($v);
                                $vv = $v->ASSET_CTRL_CODE.","; 
                            }
                            array_push($ac,rtrim($vv,","));
                            //die();
                        }
                    }
                }
            }
        }

        //echo "4<pre>"; print_r($ac);die();
        /*
        Array
        (
            [0] => IF
            [1] => IT
        )
        */

        if (count(array_unique($ac)) === 1) 
        {
            $result = array("status"=>true, "message"=> $ac[0]);
        }
        else
        {
            if(!empty( $ac ))
            {
                $result = array("status"=>false, "message"=> "Aset Controller tidak sama / belum disetting");
            }
            else
            {
                $result = array("status"=>true, "message"=> "");
            }
        }

        return $result;
    }

    public function businessarea() 
    {
        $user_id = Session::get('user_id');
        /*
        $sql = "
            SELECT DESCRIPTION_CODE as id, DESCRIPTION as text
            FROM TM_GENERAL_DATA
            WHERE GENERAL_CODE = 'plant'
            AND FIND_IN_SET(DESCRIPTION_CODE, (select  area_code from TBM_USER where id = ".$user_id."))
        ";
        */    

        $sql = "
            SELECT DESCRIPTION_CODE as id, DESCRIPTION as text
            FROM TM_GENERAL_DATA
            WHERE GENERAL_CODE = 'plant'
            AND DESCRIPTION_CODE IN (select area_code from v_user where id = {$user_id} AND area_code != 'All' )
        ";

        $data = DB::select(DB::raw($sql));
        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id . '-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }

    public function pdfDoc()
    {
        $html2pdf = new Html2Pdf('P', 'A4', 'en');
        $html2pdf->writeHTML(view('request.pdf', [
            'name' => 'dadang kurniawan',
        ]));

        $pdf = $html2pdf->output("", "S");
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Length', strlen($pdf))
            ->header('Content-Disposition', 'inline; filename="request.pdf"');
    }

    public function get_reg_no()
    {
        $sql = "SELECT count(*) AS total FROM TR_REG_ASSET WHERE YEAR(tanggal_reg) = YEAR(CURDATE()) AND MONTH(tanggal_reg) = MONTH(curdate())";
        $data = DB::select($sql);
        $maxno = $data[0]->total+1;
        //echo "<pre>"; print_r($maxno); die();
        
        $year= date('y');
        $month = date('m');
        $year=$year.'.';
        $n=$maxno;
        $n = str_pad($n + 1, 5, 0, STR_PAD_LEFT);
        $number=$year.$month.'/AMS/PDFA/'.$n;
        //echo $number; die();
        return $number;
    }

    public function qty_po(Request $request) 
    {
        $param = $_REQUEST;
        
        $sql = " 
            SELECT qty_po_submit FROM v_qty_po_submit WHERE NO_PO = ".$param['po_no']." 
                AND ITEM_PO = '".$param['item_po']."' 
                AND KODE_MATERIAL = '".$param['kode_material']."'
        ";
        
        $datax = DB::SELECT($sql);
        
        if(!empty($datax))
        {
            $data = array("nilai" => $datax[0]->qty_po_submit);    
        }
        else
        {
            $data = array("nilai" => 0);
        }

        //echo "<pre>"; print_r($data); die();

        //echo "<pre>"; print_r(response()->json(array('data' => $data))); die();
        
        return response()->json(array('data' => $data));
        
    }
}
