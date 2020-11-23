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

class RequestBakController1 extends Controller
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

    public function create(Request $request) {
        $data['page_title'] = 'Request '.($request->type == "amp" ? 'Melalui PO AMP':'Melalui PO Sendiri');
        $data['type'] = ($request->type == "amp" ? 'Melalui PO AMP':'Melalui PO Sendiri');
        $access = AccessRight::access();
        $data["access"] = (object)$access;
        $data["ba_user"] = '';
        $profile = AccessRight::profile();
        
        // dd($profile);
        
        if($profile[0]->area_code)
        {
            if(($profile[0]->area_code)=='All'){
                $ba_user = "'".$profile[0]->area_code."'";
            }else{
                $areacode = explode(',',$profile[0]->area_code);
                if($areacode)
                {
                    $ba_user = '';
                    foreach( $areacode as $k => $v )
                    {
                        $ba_user .= '"'.$v.'",' ;
                    }
                
                }
            }            
            $data["ba_user"] .= ''.$ba_user.'';
            // dd($data["ba_user"]);
        }
        
        if($request->type == "amp") {
            return view('request.amp')->with(compact('data'));
        }else {
            return view('request.sap')->with(compact('data'));
        }
        
    }

    public function getPO(Request $request) {
        $param = $_REQUEST;
        $service = API::exec(array(
            'request' => 'GET',
            'host' => 'ldap',
            'method' => "select_po/" . $param["no_po"]
        ));
        $data = $service;
       if(isset( $data->EBELN)) {
            return response()->json(array('data' => $data));
       } else {
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
        //DB::beginTransaction();
       try {
           
            $reg_no = rand(0, 1000000);

           if ($request->asset_id) {
                $reg_asset = TR_REG_ASSET::find($request->edit_id);
                $reg_asset->UPDATED_BY = Session::get('user_id');
            } else {
                $reg_asset = new TR_REG_ASSET();
                $reg_asset->CREATED_BY = Session::get('user_id');
                $reg_asset->CREATED_AT = date('Y-m-d H:i:s');
                $reg_asset->NO_REG = $reg_no;
            }

            $reg_asset->BUSINESS_AREA = $request->business_area;
            $reg_asset->TYPE_TRANSAKSI = $request->transaction_type;
            $reg_asset->TANGGAL_REG = date_format(date_create($request->request_date), "Y-m-d");
            $reg_asset->NO_PO = $request->po_no;
            $reg_asset->TANGGAL_PO = date_format(date_create($request->po_date), "Y-m-d");
            $reg_asset->KODE_VENDOR = $request->vendor_code;
            $reg_asset->NAMA_VENDOR = $request->vendor_name;
            $reg_asset->save();
            $asset_id = $reg_asset->id;

            $no = 1;
            if( $request->docs) {
                foreach ($request->docs as $row) {
                    $reg_asset_file = new TR_REG_ASSET_FILE();
                    $reg_asset_file->ASSET_REG_ID = $asset_id;
                    $reg_asset_file->NO_FILE = $no;
                    $reg_asset_file->NO_REG = $reg_no;
                    $reg_asset_file->FILENAME = $row['name'];
                    $reg_asset_file->DOC_SIZE = $row["size"];
                    $reg_asset_file->FILE_CATEGORY = $row['type'];
                    $reg_asset_file->FILE_UPLOAD = $row['file'];
                    $reg_asset_file->CREATED_BY = Session::get('user_id');
                    $reg_asset_file->CREATED_AT = date('Y-m-d H:i:s');
                    $reg_asset_file->save();
                    $no++;
                }
            }

            
            $no = 0;
            if( $request->asset) {
                foreach ($request->asset as $row) {
                    if (number_format($row["item_po"])) {
                        $reg_asset_detail_po = new TR_REG_ASSET_DETAIL_PO();
                        $reg_asset_detail_po->ASSET_REG_ID = $asset_id;
                        $reg_asset_detail_po->NO_REG = $reg_no;
                        $reg_asset_detail_po->NO_PO = $request->po_no;
                        $reg_asset_detail_po->ITEM_PO = number_format($row["item_po"]);
                        $reg_asset_detail_po->KODE_MATERIAL = $row["code"];
                        $reg_asset_detail_po->NAMA_MATERIAL = $row["name"];
                        $reg_asset_detail_po->QUANTITY_PO = $row["qty"];
                        $reg_asset_detail_po->QUANTITY_SUBMIT = $row["request_qty"];
                        $reg_asset_detail_po->CREATED_BY = Session::get('user_id');
                        $reg_asset_detail_po->save();
                        $reg_asset_po_id = $reg_asset_detail_po->id;
                        $detail = $row["detail"];

                        for ($i = 0; $i < count($detail); $i++) {
                            $reg_asset_detail = new TR_REG_ASSET_DETAIL();
                            $reg_asset_detail->ASSET_PO_ID =  $reg_asset_po_id;
                            $reg_asset_detail->NO_REG_ITEM = $i+1;
                            $reg_asset_detail->NO_REG = $reg_no;
                            $reg_asset_detail->ITEM_PO = number_format($row["item_po"]);
                            $reg_asset_detail->KODE_MATERIAL = $row["code"];
                            $reg_asset_detail->NAMA_MATERIAL = $row["name"];
                            $reg_asset_detail->NO_PO = $request->po_no;
                            $reg_asset_detail->KODE_JENIS_ASSET = '';
                            $reg_asset_detail->JENIS_ASSET = $detail[$i]["asset_type"];
                            $reg_asset_detail->GROUP = $detail[$i]["asset_group"];
                            $reg_asset_detail->SUB_GROUP = $detail[$i]["asset_sub_group"];
                            $reg_asset_detail->ASSET_CLASS = '';
                            $reg_asset_detail->NAMA_ASSET = $detail[$i]["asset_name"];
                            $reg_asset_detail->MERK = $detail[$i]["asset_brand"];
                            $reg_asset_detail->SPESIFIKASI_OR_WARNA = $detail[$i]["asset_specification"];
                            $reg_asset_detail->NO_RANGKA_OR_NO_SERI = $detail[$i]["asset_serie_no"];
                            $reg_asset_detail->NO_MESIN_OR_IMEI = $detail[$i]["asset_imei"];
                            $reg_asset_detail->NO_POLISI = $detail[$i]["asset_police_no"];
                            $reg_asset_detail->LOKASI_BA_CODE = $detail[$i]["asset_location"];
                            $reg_asset_detail->LOKASI_BA_DESCRIPTION = '';
                            $reg_asset_detail->TAHUN_ASSET = $detail[$i]["asset_year"];
                            $reg_asset_detail->KONDISI_ASSET = '';
                            $reg_asset_detail->INFORMASI = $detail[$i]["asset_info"];
                            $reg_asset_detail->NAMA_PENANGGUNG_JAWAB_ASSET = $detail[$i]["asset_pic_name"];
                            $reg_asset_detail->JABATAN_PENANGGUNG_JAWAB_ASSET = $detail[$i]["asset_pic_level"];
                            $reg_asset_detail->CREATED_BY = Session::get('user_id');
                            $reg_asset_detail->save();
                            $reg_asset_detail_id = $reg_asset_detail->id;
                            $item_file_id = ($no + 1) . ($i + 1);
           
                            if ($detail[$i]["foto_asset"]["name"]) {
                                var_dump('01');
                                var_dump($reg_asset_detail_id);
                                $reg_asset_detail_file_asset = new TR_REG_ASSET_DETAIL_FILE();
                                $reg_asset_detail_file_asset->ASSET_PO_DETAIL_ID =  $reg_asset_detail_id;
                                $reg_asset_detail_file_asset->NO_REG_ITEM_FILE = $item_file_id;
                                $reg_asset_detail_file_asset->NO_REG = $reg_no;
                                $reg_asset_detail_file_asset->JENIS_FOTO = 'foto asset';
                                $reg_asset_detail_file_asset->FILENAME = $detail[$i]["foto_asset"]["name"];
                                $reg_asset_detail_file_asset->DOC_SIZE = $detail[$i]["foto_asset"]["size"];
                                $reg_asset_detail_file_asset->FILE_CATEGORY = $detail[$i]["foto_asset"]["type"];
                                $reg_asset_detail_file_asset->FILE_UPLOAD = $detail[$i]["foto_asset"]["file"];
                                $reg_asset_detail_file_asset->save();
                            }

                            if ($detail[$i]["foto_asset_seri"]["name"]) {
                                var_dump('02');
                                var_dump($reg_asset_detail_id);
                                $reg_asset_detail_file_seri = new TR_REG_ASSET_DETAIL_FILE();
                                $reg_asset_detail_file_seri->ASSET_PO_DETAIL_ID =  $reg_asset_detail_id;
                                $reg_asset_detail_file_seri->NO_REG_ITEM_FILE = $item_file_id;
                                $reg_asset_detail_file_seri->NO_REG = $reg_no;
                                $reg_asset_detail_file_seri->JENIS_FOTO = 'Foto no. seri / no rangka';
                                $reg_asset_detail_file_seri->FILENAME = $detail[$i]["foto_asset_seri"]["name"];
                                $reg_asset_detail_file_seri->DOC_SIZE = $detail[$i]["foto_asset_seri"]["size"];
                                $reg_asset_detail_file_seri->FILE_CATEGORY = $detail[$i]["foto_asset_seri"]["type"];
                                $reg_asset_detail_file_seri->FILE_UPLOAD = $detail[$i]["foto_asset_seri"]["file"];
                                $reg_asset_detail_file_seri->save();
                            }

                            if ($detail[$i]["foto_asset_mesin"]["name"]) {
                                var_dump('03');
                                var_dump( $reg_asset_detail_id);
                                $reg_asset_detail_file_mesin = new TR_REG_ASSET_DETAIL_FILE();
                                $reg_asset_detail_file_mesin->ASSET_PO_DETAIL_ID =  $reg_asset_detail_id;
                                $reg_asset_detail_file_mesin->NO_REG_ITEM_FILE = $item_file_id;
                                $reg_asset_detail_file_mesin->NO_REG = $reg_no;
                                $reg_asset_detail_file_mesin->JENIS_FOTO = 'Foto No msin / IMEI';
                                $reg_asset_detail_file_mesin->FILENAME = $detail[$i]["foto_asset_mesin"]["name"];
                                $reg_asset_detail_file_mesin->DOC_SIZE = $detail[$i]["foto_asset_mesin"]["size"];
                                $reg_asset_detail_file_mesin->FILE_CATEGORY = $detail[$i]["foto_asset_mesin"]["type"];
                                $reg_asset_detail_file_mesin->FILE_UPLOAD = $detail[$i]["foto_asset_mesin"]["file"];
                                $reg_asset_detail_file_mesin->save();
                            }
                        }
                        $no++;
                    }
                }
            }
            //DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->edit_id ? 'updated' : 'added')]);
       } catch (\Exception $e) {
            //DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
       }
    }

    public function validateUsername($username) {
        $service = API::exec(array(
            'request' => 'GET',
            'method' => "tr_user_profile/" . $username
        ));
        $profile = $service->data;    
        if($profile) {
            return false;
        } else {
            return true;
        }

    }

    public function show()
    {
        $param = $_REQUEST;
        $service = API::exec(array(
            'request' => 'GET',
            'method' => "tr_user/" . $param["id"]
        ));
        $data = $service;
        return response()->json(array('data' => $data->data));
    }

    public function inactive(Request $request) {
        try {
            $param["updated_by"] = Session::get('user');
            $data = API::exec(array(
                'request' => 'ACTIVE',
                'method' => 'tr_user/' . $request->id . '/0',
                'data' => $param
            ));

            $res = $data;

            if ($res->code == '201') {
                return response()->json(['status' => true, "message" => 'Data is successfully inactived']);;
            } else {
                return response()->json(['status' => false, "message" => $res->message]);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }
   
    public function active(Request $request) {
        try {
            $param["updated_by"] = Session::get('user');
            $data = API::exec(array(
                'request' => 'ACTIVE',
                'method' => 'tr_user/' . $request->id . '/1',
                'data' => $param
            ));

            $res = $data;

            if ($res->code == '201') {
                return response()->json(['status' => true, "message" => 'Data is successfully inactived']);;
            } else {
                return response()->json(['status' => false, "message" => $res->message]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
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
}
