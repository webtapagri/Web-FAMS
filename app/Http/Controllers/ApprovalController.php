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
use DateTime;
use Debugbar;
use App\TM_MSTR_ASSET;
use Redirect;
/* use NahidulHasan\Html2pdf\Facades\Pdf; */

class ApprovalController extends Controller
{
    public function index()
    {
        
        if (empty(Session::get('authenticated')))
            return redirect('/login');

        /*if (AccessRight::granted() == false)
            return response(view('errors.403'), 403);*/
        if( !empty($_GET) )
        {
            $role_id = Session::get('role_id');
            $noreg = base64_decode($_GET['noreg']);
            $noreg = str_replace("-", "/", $noreg);
            $data['outstanding'] = $this->validasi_outstanding($noreg,$role_id);
    
        }
        else
        {
            $data['outstanding'] = 1;   
        }
        $access = AccessRight::access();
        $data['page_title'] = "Approval";
        $data['ctree_mod'] = 'Approval';
        $data['ctree'] = 'approval';
        return view('approval.index')->with(compact('data'));
    }

    function get_master_asset_by_id($id)
    {
        //echo $id; die();

        $result = array();

        //$sql = " SELECT * FROM TM_MSTR_ASSET WHERE KODE_ASSET_AMS = $id ";
        
        $sql = " SELECT a.*, b.DESCRIPTION AS BA_PEMILIK_ASSET_DESCRIPTION, c.DESCRIPTION AS LOKASI_BA_DESCRIPTION 
                    FROM TM_MSTR_ASSET a 
                        LEFT JOIN TM_GENERAL_DATA b ON a.BA_PEMILIK_ASSET = b.DESCRIPTION_CODE AND b.GENERAL_CODE = 'plant' 
                        LEFT JOIN TM_GENERAL_DATA c ON a.LOKASI_BA_CODE = c.DESCRIPTION_CODE AND c.GENERAL_CODE = 'plant' 
                    WHERE a.KODE_ASSET_AMS = ".$id." ";

        $data = DB::SELECT($sql);

        if(!empty($data))
        {
            foreach($data as $k => $v)
            {
                //echo "1<pre>"; print_r($v);
                $result = $v;
            }
        }

        return $result;

    }

    public function dataGrid(Request $request)
    {
        //echo "<pre>"; print_r($request->all());
        $role_id = Session::get('role_id');
        $user_id = Session::get('user_id');

        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";
        $addwhere = "";
               
        $field = array
        (
            array("index" => "0", "field" => "APPROVAL.DOCUMENT_CODE", "alias" => "NO_REG"),
            array("index" => "1", "field" => "ASSET.TYPE_TRANSAKSI ", "alias" => "TYPE"),
            array("index" => "2", "field" => "ASSET.PO_TYPE", "alias" => "PO_TYPE"),
            array("index" => "3", "field" => "ASSET.NO_PO", "alias" => "NO_PO"),
            array("index" => "4", "field" => " CASE WHEN LOCATE('DSPA',APPROVAL.DOCUMENT_CODE) THEN TD.TANGGAL_REG 
            WHEN LOCATE('MTSA',APPROVAL.DOCUMENT_CODE) THEN TM.CREATED_AT
            ELSE ASSET.TANGGAL_REG END", "alias" => "REQUEST_DATE"),
            // array("index" => "4", "field" => "DATE_FORMAT(ASSET.TANGGAL_REG, '%d %b %Y')", "alias" => "REQUEST_DATE"),
            array("index" => "5", "field" => "CASE WHEN LOCATE('DSPA',APPROVAL.DOCUMENT_CODE) THEN (SELECT U.NAME FROM TBM_USER U WHERE U.ID = TD.CREATED_BY)
            WHEN LOCATE('MTSA',APPROVAL.DOCUMENT_CODE) THEN (SELECT U.NAME FROM TBM_USER U WHERE U.ID = TM.CREATED_BY)
            ELSE REQUESTOR.NAME END", "alias" => "REQUESTOR"),
            // array("index" => "5", "field" => "REQUESTOR.NAME", "alias" => "REQUESTOR"),
            array("index" => "6", "field" => "ASSET.TANGGAL_PO", "alias" => "PO_DATE"),
            array("index" => "7", "field" => "ASSET.KODE_VENDOR", "alias" => "VENDOR_CODE"),
            array("index" => "8", "field" => "ASSET.NAMA_VENDOR", "alias" => "VENDOR_NAME"),
        );

        foreach ($field as $row) 
        {
            if ($row["alias"]) {
                $selectedColumn[] = $row["field"] . " as " . $row["alias"];
            } else {
                $selectedColumn[] = $row["field"];
            }

            if ($row["index"] == $orderColumn) {
                $orderColumnName = $row["field"];
            }
        }

        // it@140619 JOIN W v_outstanding
        $sql = ' SELECT DISTINCT(ASSET.ID) AS ID '.implode(", ", $selectedColumn).'
            FROM v_outstanding AS APPROVAL 
                LEFT JOIN TR_REG_ASSET AS ASSET ON ( APPROVAL.DOCUMENT_CODE = ASSET.NO_REG )
                LEFT JOIN TR_DISPOSAL_ASSET AS TD ON ( APPROVAL.DOCUMENT_CODE = TD.NO_REG )
                LEFT JOIN TR_MUTASI_ASSET_DETAIL AS TM ON ( APPROVAL.DOCUMENT_CODE = TM.NO_REG )
                LEFT JOIN TBM_USER AS REQUESTOR ON (REQUESTOR.ID=ASSET.CREATED_BY)
            WHERE 1=1 ';

        if($role_id != 4)
            $sql .= " AND APPROVAL.USER_ID = '{$user_id}' "; 

        if ($request->NO_PO)
            $sql .= " AND ASSET.NO_PO like '%" . $request->NO_PO . "%'";
       
        if ($request->NO_REG)
            $sql .= " AND APPROVAL.DOCUMENT_CODE  like '%" . $request->NO_REG . "%'";

        if ($request->REQUESTOR)
            $sql .= " AND REQUESTOR.NAME  like '%" . $request->REQUESTOR . "%'";

        if ($request->VENDOR_CODE)
            $sql .= " AND ASSET.KODE_VENDOR  like '%" . $request->VENDOR_CODE . "%'";

        if ($request->VENDOR_NAME)
            $sql .= " AND ASSET.NAMA_VENDOR  like '%" . $request->VENDOR_NAME . "%'";

        if ($request->TYPE)
            $sql .= " AND ASSET.TYPE_TRANSAKSI  = " . $request->TYPE;
     
        if($request->PO_TYPE !='')
            $sql .= " AND ASSET.PO_TYPE  = " . $request->PO_TYPE;

        if ($request->REQUEST_DATE)
            $sql .= " AND DATE_FORMAT(ASSET.TANGGAL_REG, '%Y-%m-%d') = '" . DATE_FORMAT(date_create($request->REQUEST_DATE), 'Y-m-d'). "'";

        if ($request->PO_DATE)
            $sql .= " AND DATE_FORMAT(ASSET.TANGGAL_PO, '%Y-%m-%d') = '" . DATE_FORMAT(date_create($request->PO_DATE), 'Y-m-d') ."'";

        if ($orderColumn != "") {
            $sql .= " ORDER BY " . $field[$orderColumn]['field'] . " " . $dirColumn;
        }
        else
        {
            $sql .= " ORDER BY APPROVAL.APPROVAL_DETAIL_CODE DESC ";
            //$sql .= " ORDER BY ASSET.ID DESC ";
        }

        //echo $sql; die();

        $data = DB::select(DB::raw($sql));

        $iTotalRecords = count($data);
        $iDisplayLength = intval($request->length);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($request->start);
        $sEcho = intval($request->draw);
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        for ($i = $iDisplayStart; $i < $end; $i++) {
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

    public function create(Request $request) {
        $type = ($request->type == "amp" ? 'Melalui PO AMP':'Melalui PO Sendiri');
        return view('assets.add')->with(compact('type'));
    }

    public function convertToPdf() {
        $html2pdf = new Html2Pdf('L', 'A4', 'en');
        $html2pdf->writeHTML(view('assets.pdf'));
        $html2pdf->output("test.pdf", "D");    
        
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

    function view($id)
    {
        $noreg = str_replace("-", "/", $id);

        $records = array();

        $sql = " SELECT a.*, date_format(a.tanggal_reg,'%d-%m-%Y') AS TANGGAL_REG, b.description_code AS CODE_AREA, b.description AS NAME_AREA, c.name AS REQUESTOR 
                    FROM TR_REG_ASSET a 
                        LEFT JOIN TM_GENERAL_DATA b ON a.business_area = b.description_code AND b.general_code = 'plant'
                        LEFT JOIN TBM_USER c ON a.created_by = c.id 
                    WHERE a.no_reg = '$noreg' ";
        $data = DB::SELECT($sql);
        
        if($data)
        {
            $type_transaksi = array(
                1 => 'Barang',
                2 => 'Jasa',
                3 => 'Lain-lain',
            );

            $po_type = array(
                0 => 'SAP',
                1 => 'AMP',
                2 => 'Asset Lainnya'
            );

            foreach ($data as $k => $v) 
            {
                $records[] = array(
                    'no_reg' => trim($v->NO_REG),
                    'type_transaksi' => trim($type_transaksi[$v->TYPE_TRANSAKSI]),
                    'po_type' => trim($po_type[$v->PO_TYPE]),
                    'business_area' => trim($v->CODE_AREA).' - '.trim($v->NAME_AREA),
                    'requestor' => trim($v->REQUESTOR),
                    'tanggal_reg' => trim($v->TANGGAL_REG),
                    'item_detail' => $this->get_item_detail($noreg),
                    'sync_sap' => $this->get_sinkronisasi_sap($noreg),
                    'sync_amp' => $this->get_sinkronisasi_amp($noreg),
                    'sync_lain' => $this->get_sinkronisasi_lain($noreg),
                    'cek_reject' => $this->get_cek_reject($noreg),
                    'vendor' => trim($v->KODE_VENDOR).' - '.trim($v->NAMA_VENDOR),
                    'kode_vendor' =>trim($v->KODE_VENDOR),
                    'nama_vendor' =>trim($v->NAMA_VENDOR),
                );

            }
        }
        else
        {
            $records[0] = array();
        }
        echo json_encode($records[0]);
    }

    function get_item_detail($noreg)
    {
        $request = array();
        
        $sql = " SELECT a.* FROM TR_REG_ASSET_DETAIL_PO a WHERE a.no_reg = '{$noreg}' ";
        $data = DB::SELECT($sql);
        //echo "<pre>"; print_r($data); die();

        if($data)
        {
            foreach( $data as $k => $v )
            {
                $request[] = array
                (
                    'id' => trim($v->ID),
                    'no_po' => trim($v->NO_PO),
                    'item' => trim($v->ITEM_PO),
                    'qty' => trim($v->QUANTITY_SUBMIT),
                    //'qty' => $this->get_total_qty($noreg),
                    'kode' => trim($v->KODE_MATERIAL),
                    'nama' => trim($v->NAMA_MATERIAL)
                );
            }
        }

        return $request;
    }

    function get_total_qty($noreg)
    {
        $sql = " SELECT COUNT(*) AS JML FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND DELETED != 'X' ";
        $data = DB::SELECT($sql);
        //echo "<pre>"; print_r($data);die();
        return trim($data[0]->JML);
    }

    function get_asset_detail($noreg,$id)
    {
        //echo $noreg.'/'.$id; die();

        $kondisi = array(
            'B' => 'Baik',
            'BP' => 'Butuh Perbaikan',
            'TB' => 'Tidak Baik'
        );

        $noreg = str_replace("-", "/", $noreg);

        $records = array();
        
        // SKIP DULU IT@200819 ~ ASSET YANG DIDELETE DITAMPILKAN IT@200819
        $sql = " SELECT a.*, b.jenis_asset_description AS JENIS_ASSET_NAME, c.group_description AS GROUP_NAME, d.subgroup_description AS SUB_GROUP_NAME, e.KODE_VENDOR, e.NAMA_VENDOR, e.BUSINESS_AREA AS BUSINESS_AREA, e.PO_TYPE AS PO_TYPE
                    FROM TR_REG_ASSET_DETAIL a 
                        LEFT JOIN TM_JENIS_ASSET b ON a.jenis_asset = b.jenis_asset_code 
                        LEFT JOIN TM_GROUP_ASSET c ON a.group = c.group_code AND a.jenis_asset = c.jenis_asset_code
                        LEFT JOIN TM_SUBGROUP_ASSET d ON a.sub_group = d.subgroup_code AND a.group = d.group_code AND a.jenis_asset = d.jenis_asset_code
                        LEFT JOIN TR_REG_ASSET e ON a.NO_REG = e.NO_REG
                    WHERE a.no_reg = '{$noreg}' AND a.asset_po_id = '{$id}' AND (a.DELETED is null OR a.DELETED = '')
                        ORDER BY a.no_reg_item ";

        /*$sql = " SELECT a.*, b.jenis_asset_description AS JENIS_ASSET_NAME, c.group_description AS GROUP_NAME, d.subgroup_description AS SUB_GROUP_NAME, e.KODE_VENDOR, e.NAMA_VENDOR, e.BUSINESS_AREA AS BUSINESS_AREA, e.PO_TYPE AS PO_TYPE
                    FROM TR_REG_ASSET_DETAIL a 
                        LEFT JOIN TM_JENIS_ASSET b ON a.jenis_asset = b.jenis_asset_code 
                        LEFT JOIN TM_GROUP_ASSET c ON a.group = c.group_code AND a.jenis_asset = c.jenis_asset_code
                        LEFT JOIN TM_SUBGROUP_ASSET d ON a.sub_group = d.subgroup_code AND a.group = d.group_code
                        LEFT JOIN TR_REG_ASSET e ON a.NO_REG = e.NO_REG
                    WHERE a.no_reg = '{$noreg}' AND a.asset_po_id = '{$id}' AND (a.DELETED is null OR a.DELETED = '')
                        ORDER BY a.no_reg_item ";*/

        $data = DB::SELECT($sql);

        if($data)
        {
            foreach( $data as $k => $v )
            {
                $rolename = Session::get('role');
                if( $rolename == 'AMS' )
                {
                    $kondisi_asset = trim($v->JENIS_ASSET);
                    $group = trim($v->GROUP);
                    $subgroup = trim($v->SUB_GROUP);
                }
                else
                {
                    $kondisi_asset = trim($v->JENIS_ASSET).'-'.trim($v->JENIS_ASSET_NAME);
                    $group = trim($v->GROUP).'-'.trim($v->GROUP_NAME);
                    $subgroup = trim($v->SUB_GROUP).'-'.trim($v->SUB_GROUP_NAME);
                }

                $records[] = array
                (
                    'id' => trim($v->ID),
                    'no_po' => trim($v->NO_PO),
                    'asset_po_id' => trim($v->ASSET_PO_ID),
                    'tgl_po' => trim($v->CREATED_AT),
                    'kondisi_asset' => trim(@$kondisi[$v->KONDISI_ASSET]),
                    'jenis_asset' => trim($v->JENIS_ASSET).'-'.trim($v->JENIS_ASSET_NAME),
                    //'jenis_asset' => $kondisi_asset,
                    'group' => trim($v->GROUP).'-'.trim($v->GROUP_NAME),
                    //'group' => $group,
                    'sub_group' => trim($v->SUB_GROUP).'-'.trim($v->SUB_GROUP_NAME),
                    //'sub_group' => $subgroup,
                    'nama_asset' => trim($v->NAMA_ASSET),
                    'merk' => trim($v->MERK),
                    'spesifikasi_or_warna' => trim($v->SPESIFIKASI_OR_WARNA),
                    'no_rangka_or_no_seri' => trim($v->NO_RANGKA_OR_NO_SERI),
                    'no_mesin_or_imei' => trim($v->NO_MESIN_OR_IMEI),
                    'no_polisi'=>trim($v->NO_POLISI),
                    'lokasi' => trim($v->LOKASI_BA_DESCRIPTION),
                    'tahun' => trim($v->TAHUN_ASSET),
                    'nama_penanggung_jawab_asset' => trim($v->NAMA_PENANGGUNG_JAWAB_ASSET),
                    'jabatan_penanggung_jawab_asset' => trim($v->JABATAN_PENANGGUNG_JAWAB_ASSET),
                    'info' => trim($v->INFORMASI),
                    'file' => $this->get_asset_file($v->ID,$noreg),
                    'nama_asset_1' => trim($v->NAMA_ASSET_1),
                    'nama_asset_2' => trim($v->NAMA_ASSET_2),
                    'nama_asset_3' => trim($v->NAMA_ASSET_3),
                    'quantity_asset_sap' => trim($v->QUANTITY_ASSET_SAP),
                    'uom_asset_sap' => trim($v->UOM_ASSET_SAP),
                    'capitalized_on' => trim($v->CAPITALIZED_ON),
                    'deactivation_on' => trim($v->DEACTIVATION_ON),
                    'cost_center' => trim($v->COST_CENTER),
                    'book_deprec_01' => trim($v->BOOK_DEPREC_01),
                    'fiscal_deprec_15' => trim($v->FISCAL_DEPREC_15),
                    'group_deprec_30' => trim($v->GROUP_DEPREC_30),
                    'no_reg_item' => trim($v->NO_REG_ITEM),
                    'vendor' => trim($v->KODE_VENDOR).'-'.trim($v->NAMA_VENDOR),
                    'business_area' => trim($v->BUSINESS_AREA),
                    'kode_asset_sap' => trim($v->KODE_ASSET_SAP),
                    'kode_asset_controller' => trim($v->KODE_ASSET_CONTROLLER),
                    'kode_asset_ams' => trim($v->KODE_ASSET_AMS),
                    'po_type' => trim($v->PO_TYPE),
                    'gi_number' => trim($v->GI_NUMBER),
                    'gi_year' => trim($v->GI_YEAR),
                    'total_asset' => $this->get_validasi_delete_asset($noreg),
                    'nama_material' => trim($v->NAMA_MATERIAL),
                    'deleted' => trim($v->DELETED)
                );
            }
        }

        echo json_encode($records);
    }

    function get_asset_file($id,$noreg)
    {
        $records = array();
        $sql = " SELECT a.* FROM TR_REG_ASSET_DETAIL_FILE a WHERE a.no_reg = '{$noreg}' AND a.asset_po_detail_id = '{$id}' ";
        $data = DB::SELECT($sql);//echo $sql; die();
        //echo "<pre>"; print_r($data); die();

        if($data)
        {
            foreach( $data as $k => $v )
            {
                $records[] = array
                (
                    'id' => $v->ID,
                    'file_category' => $v->FILE_CATEGORY,
                    'filename' => $v->FILENAME,
                    'jenis_foto' => $v->JENIS_FOTO,
                    'file_thumb' => $v->FILE_UPLOAD
                );
            }
        }

        return $records;
    }

    function delete_asset(Request $request, $id)
    {

        //$total_asset_now = 100; //$this->getInfo;

        DB::beginTransaction();

        try 
        {
            $user_id = Session::get('user_id');

            $sql = " UPDATE TR_REG_ASSET_DETAIL SET DELETED = 'X', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE ID = $id ";
            DB::UPDATE($sql);    

            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($id ? 'updated' : 'update')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    function save_asset_sap(Request $request, $id)
    {
        $user_id = Session::get('user_id');

        // IF UOM NULL or OBJECT VAL
        $uom_asset_sap = $request->uom; //echo $uom_asset_sap; die();
        $request_uom = '';
        if (strpos($uom_asset_sap, '[object Object]') !== false) 
        {
            
            // VALIDASI UOM ASSET SAP
            // $sql = " SELECT UOM_ASSET_SAP FROM TR_REG_ASSET_DETAIL a WHERE a.ID = $id AND a.NO_REG = '{$request->getnoreg}' AND a.NO_REG_ITEM = {$request->no_reg_item} ";
            // $uom_now_data = DB::SELECT($sql);

            $uom_now_data = DB::table('TR_REG_ASSET_DETAIL AS a')
							->selectRaw("UOM_ASSET_SAP")
							->whereRaw ("a.ID = $id AND a.NO_REG = '$request->getnoreg' AND a.NO_REG_ITEM = '$request->no_reg_item' ")
							->get();
                         
            // $uom_now = @$uom_now_data[0]->UOM_ASSET_SAP;
            $uom_now = $uom_now_data[0]->UOM_ASSET_SAP;
            //echo "1<pre>"; print_r($uom_now);die();

            if( $uom_now != $uom_asset_sap )
            {
                if($uom_now == '')
                {
                    $request_uom = $request->uom;
                    return response()->json(['status' => false, 'message' => 'UOM ASSET SAP error / belum diisi, silahkan utk diupdate kembali!' ]);
                    die();    
                }
                else
                {
                    $request_uom = $uom_now;
                }           
            }
        }else{ $request_uom = $uom_asset_sap; }

        DB::beginTransaction();

        try 
        {
            $deactivation_on = $request->deactivation_on;
            if($deactivation_on == '')
            { $do = "a.deactivation_on = NULL,"; }else
            { $do = "a.deactivation_on = '{$request->deactivation_on}',"; }

            $capitalized_on = $request->capitalized_on;
            if($capitalized_on == '')
            { $co = "a.capitalized_on = NULL,"; }else
            { $co = "a.capitalized_on = '{$request->capitalized_on}',"; }

            $sql = " UPDATE TR_REG_ASSET_DETAIL a
                        SET 
                            a.nama_asset_1 = '{$request->nama_asset_1}',
                            a.nama_asset_2 = '{$request->nama_asset_2}',
                            a.nama_asset_3 = '{$request->nama_asset_3}',
                            a.quantity_asset_sap = '{$request->quantity}',
                            a.uom_asset_sap = '{$request_uom}',
                            {$co}
                            {$do}
                            a.cost_center = '{$request->cost_center}',
                            a.book_deprec_01 = '{$request->book_deprec_01}',
                            a.fiscal_deprec_15 = '{$request->fiscal_deprec_15}',
                            a.group_deprec_30 = '{$request->fiscal_deprec_15}',
                            a.updated_by = '{$user_id}',
                            a.updated_at = current_timestamp()
                    WHERE a.ID = $id AND a.NO_REG = '{$request->getnoreg}' AND a.NO_REG_ITEM = '{$request->no_reg_item}' ";
            DB::UPDATE($sql);    

            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($id ? 'updated' : 'update')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    function save_item_detail(Request $request, $id)
    {
        $user_id = Session::get('user_id');

        DB::beginTransaction();

        try 
        {
            $deactivation_on = $request->deactivation_on;
            if($deactivation_on == '')
            { $do = "a.deactivation_on = NULL,"; }else
            { $do = "a.deactivation_on = '{$request->deactivation_on}',"; }

            $capitalized_on = $request->capitalized_on;
            if($capitalized_on == '')
            { $co = "a.capitalized_on = NULL,"; }else
            { $co = "a.capitalized_on = '{$request->capitalized_on}',"; }

            $sql = " UPDATE TR_REG_ASSET_DETAIL a
                        SET 
                            a.jenis_asset = '{$request->jenis_asset}',
                            a.group = '{$request->group}',
                            a.sub_group = '{$request->subgroup}',
                            a.updated_by = '{$user_id}',
                            a.updated_at = current_timestamp()
                    WHERE a.ID = $id AND a.NO_REG = '{$request->getnoreg}' AND a.NO_REG_ITEM = {$request->no_reg_item} ";
            DB::UPDATE($sql);    

            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($id ? 'updated' : 'update')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }


    
    function update_costcenter($costcenter,$noreg, $kode)
    {
        $kode_ams = str_replace(",", "','", $kode);
        $no_registrasi = str_replace("-", "/", $noreg);
        DB::beginTransaction();

        try 
        {   
            $updated_at = date("Y-m-d H:i:s");
            $updated_by = Session::get('user_id');
            $sql1 = " INSERT INTO TR_LOG_MUTASI                   
                        SELECT NULL AS ID,b.NO_REG,b.CREATED_AT,a.BA_PEMILIK_ASSET, b.CREATED_BY, a.COST_CENTER, CURDATE() AS UPDATED_AT, b.CREATED_BY 
                        FROM TM_MSTR_ASSET a LEFT JOIN TR_MUTASI_ASSET_DETAIL b ON a.KODE_ASSET_AMS = b.KODE_ASSET_AMS
                        where b.KODE_ASSET_AMS IN ('{$kode_ams}');";

            DB::INSERT($sql1);
            
            $sql3 = " UPDATE TR_MUTASI_ASSET_DETAIL a
                        SET 
                            a.cost_center = '{$costcenter}',
                            a.updated_at = '{$updated_at}',
                            a.updated_by = '{$updated_by}'
                    WHERE a.no_reg = '{$no_registrasi}' ";
            DB::UPDATE($sql3);  
            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($kode ? 'updated' : 'update')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }
    
    function update_pic(Request $req)
    {
        $kode_asset_ams = explode(',', $req->kode_asset_ams); 
        $kode_ams = str_replace(",", "','", $req->kode_asset_ams);
        $no_registrasi = str_replace("-", "/", $req->no_reg);   
        $penanggung_jawab = explode(',', $req->penanggung_jawab);
        $jabatan = explode(',', $req->jabatan);
       
        DB::beginTransaction();

        try 
        {   
            $updated_at = date("Y-m-d H:i:s");  
            
        $case_penanggung_jawab = "";        
        $case_jabatan = "";   
        $sql ="";      
            for($i=0;$i<count($kode_asset_ams);$i++){
                $case_penanggung_jawab .= " when kode_asset_ams = '$kode_asset_ams[$i]' then '$penanggung_jawab[$i]' ";
                $case_jabatan .= " when kode_asset_ams = '$kode_asset_ams[$i]' then '$jabatan[$i]' ";
            }
            $sql .= " UPDATE TR_MUTASI_ASSET_DETAIL SET penanggung_jawab = (case $case_penanggung_jawab end),
                        jabatan = (case $case_jabatan end) WHERE kode_asset_ams in ('$kode_ams') AND no_reg = '$no_registrasi' ";

            DB::UPDATE($sql);
            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($penanggung_jawab ? 'updated' : 'update')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    function update_jenis_asset(Request $req)
    {
        $kode_asset_ams = explode(',', $req->kode_asset_ams); 
        $kode_ams = str_replace(",", "','", $req->kode_asset_ams);
        $no_registrasi = str_replace("-", "/", $req->no_reg);   
        $jenis_asset = explode(',', $req->jenis_asset);
        $group = explode(',', $req->group);
        $subgroup = explode(',', $req->subgroup);
       
        DB::beginTransaction();

        try 
        {   
            $updated_at = date("Y-m-d H:i:s");  
            
        $case_jenis_asset = "";  
        $sql ="";      
            for($i=0;$i<count($kode_asset_ams);$i++){
                $case_jenis_asset .= " when kode_asset_ams = '$kode_asset_ams[$i]' then '$jenis_asset[$i]' ";
                $case_asset_group .= " when kode_asset_ams = '$kode_asset_ams[$i]' then '$group[$i]' ";
                $case_asset_subgroup .= " when kode_asset_ams = '$kode_asset_ams[$i]' then '$subgroup[$i]' ";
            }
            $sql .= " UPDATE TR_MUTASI_ASSET_DETAIL 
                        SET jenis_asset_tujuan = (case $case_jenis_asset end) ,
                        SET group = (case $case_asset_group end) ,
                        SET sub_group = (case $case_asset_subgroup end)
                        WHERE kode_asset_ams in ('$kode_ams') AND no_reg = '$no_registrasi' ";

            DB::UPDATE($sql);
            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($jenis_asset ? 'updated' : 'update')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }


    public function dataGridHistory(Request $request)
    {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";
        $addwhere = "";
        $role_id = Session::get('role_id');
        $user_id = Session::get('user_id');
        
        $field = array
        (
            array("index" => "0", "field" => "document_code ", "alias" => "document_code"),
            array("index" => "1", "field" => "area_code", "alias" => "area_code"),
            array("index" => "2", "field" => "name", "alias" => "name"),
            array("index" => "3", "field" => "status_dokumen", "alias" => "status_dokumen"),
            //array("index" => "4", "field" => "status_approval", "alias" => "status_approval"),
            //array("index" => "5", "field" => "notes", "alias" => "po_notes"),
            array("index" => "5", "field" => "date", "alias" => "po_date"),
            // array("index" => "5", "field" => "DATE_FORMAT(date, '%d %b %Y')", "alias" => "po_date"),
            array("index" => "6", "field" => "po_type", "alias" => "po_type"),
        );

        foreach ($field as $row) 
        {
            if ($row["alias"]) {
                $selectedColumn[] = $row["field"] . " as " . $row["alias"];
            } else {
                $selectedColumn[] = $row["field"];
            }

            if ($row["index"] == $orderColumn) {
                $orderColumnName = $row["field"];
            }
        }

        $sql = '
            SELECT b.po_type AS po_type, a.user_id AS user_id '.implode(", ", $selectedColumn).'
                FROM v_history a LEFT JOIN TR_REG_ASSET b ON a.document_code = b.no_reg
            WHERE 1=1 
        ';

        $total_data = DB::select(DB::raw($sql));

        // IF ROLE = SUPER ADMINISTRATOR, SHOW ALL DATA IT@111019
        if( $role_id != 4 )
            $sql .= " AND a.user_id = '{$user_id}' ";

        if ($request->document_code)
            $sql .= " AND a.document_code like '%".$request->document_code."%'";

        if ($request->area_code)
            $sql .= " AND a.area_code  like '%" . $request->area_code . "%'";
       
        if ($request->name)
            $sql .= " AND a.name  like '%" . $request->name . "%'";

        if ($request->status_dokumen)
            $sql .= " AND a.status_dokumen  like '%" . $request->status_dokumen . "%'";

        if ($request->status_approval)
            $sql .= " AND a.status_approval  like '%" . $request->status_approval . "%'";

        if ($request->date_history)
            $sql .= " AND DATE_FORMAT(a.date, '%d/%m/%Y') = '".$request->date_history."' ";
    
        if ($orderColumn != "") {
            $sql .= " ORDER BY " . $field[$orderColumn]['field'] . " " . $dirColumn;
        }
        else
        {
            $sql .= " ORDER BY a.DOCUMENT_CODE DESC ";
        }

        $data = DB::select(DB::raw($sql));

        $iTotalRecords = count($data);
        $iDisplayLength = intval($request->length);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($request->start);
        $sEcho = intval($request->draw);
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        for ($i = $iDisplayStart; $i < $end; $i++) {
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

    function validasi_input_all_io(Request $request, $status, $noreg)
    {
        $req = $request->all();
        //echo "<pre>"; print_r($req); die();

        $request_ka = json_decode($req['request_ka']);
        $no_registrasi = str_replace("-", "/", $noreg);
        $list_kode_asset = "";

        //$sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$no_registrasi}' AND (KODE_ASSET_CONTROLLER is null OR KODE_ASSET_CONTROLLER = '' ) AND (DELETED is null OR DELETED = '') AND JENIS_ASSET IN ('E4030','4030', '4010') ";

        $sql = " SELECT * FROM TR_REG_ASSET_DETAIL a 
LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.GROUP = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE
WHERE a.NO_REG = '{$no_registrasi}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '') AND (b.MANDATORY_KODE_ASSET_CONTROLLER is not null AND b.MANDATORY_KODE_ASSET_CONTROLLER != '') ";

        $dt = DB::SELECT($sql); 
        //echo "2<pre>"; print_r($dt);die();

        if(!empty($dt))
        {
            foreach($dt as $k => $v)
            {
                $list_kode_asset .= $v->KODE_ASSET_SAP.",";
            }
            $result = array('status'=>false,'message'=> 'Kode Aset Controller (KODE ASET SAP : '.rtrim($list_kode_asset,',').') belum diisi');
        }
        else
        {
            $result = array('status'=>true,'message'=> '');
        }
        
        return $result; 
    }

    function get_validasi_input_create_asset_sap(Request $request)
    {
        $req = $request->all();
        $noreg = $req['no-reg'];

        $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND (COST_CENTER is null OR COST_CENTER = '') AND (DELETED IS NULL OR DELETED = '') ";

        $dt = DB::SELECT($sql);
       
        if(!empty($dt))
        {
            $message = '';
            foreach($dt as $k => $v)
            {
                $message .= $v->KODE_MATERIAL.'('.$v->NAMA_MATERIAL.'),';
            }
            $result = array('status'=>false,'message'=> 'Detail Aset SAP belum diisi! ( Material : '.rtrim($message,',').' )');
                return $result;
        }
        else
        {
            $result = array('status'=>true,'message'=> 'Success');
            return $result;  
        }
    }

    function update_status(Request $request, $status, $noreg)
    {
        $req = $request->all();
        $jenis_dokumen = $req['po-type'];
        //echo $jenis_dokumen; die(); //Asset Lainnya

        $rolename = Session::get('role');
        $asset_type = "";

        //validasi email next approval
        $user_id = Session::get('user_id');
        // $last_email_approve = $this->get_last_email_approve($no_registrasi);
        // //echo "2<pre>"; print_r($validasi_last_approve); die();

        // if($last_email_approve <> 1){

        //     $cek_email = $this->get_email_next_approval($no_registrasi,$user_id);

        //     if($cek_email['email'] == ""){
        //         return response()->json(['status' => false, "message" => "Email User ". $cek_email['next_approve'] ."tidak tersedia"]);
        //     }
        // }


        // VALIDASI ASSET CONTROLLER 
        if($status != 'R')
        {
            $validasi_asset_controller = $this->validasi_asset_controller($noreg);
            if( $validasi_asset_controller['status'] == false )
            {
                return response()->json(['status' => false, "message" =>  $validasi_asset_controller['message'] ]);
            }
            else
            {
                $asset_type = $validasi_asset_controller['message'];
            }
        }

        // VALIDASI PROSES CHANGE STATUS ASSET LAIN IT@090819
        if( $jenis_dokumen == 'Asset Lainnya' )
        {
            $cek_sap = $this->get_sinkronisasi_sap($noreg);
            
            if($cek_sap != "")
            { 
                $jenis_dokumen = 'SAP'; 
            }
            else
            {
                $jenis_dokumen = 'AMP';
            }
        }

        //echo "4-".$jenis_dokumen."=====1-".$req['po-type'];die();
        
        if( $jenis_dokumen == 'AMP' )
        {
            if($status != 'R')
            {
                if($rolename == 'AC')
                {
                    $validasi_io = $this->get_validasi_io_amp($request, $status, $noreg);
                } 
                else
                {
                    $validasi_io['status'] = true;
                }
            }
            else
            {
                $validasi_io['status'] = true;            
            }

            if( $validasi_io['status'] == false )
            {
                return response()->json(['status' => false, "message" => $validasi_io['message']]);
            }
            else
            {
                /* AMP PROCESS */

                if( $status != 'R' )
                {
                    if($rolename == 'AC' )
                    {
                        // DISKIP KARENA SUDAH DI MAPPING DI TABLE TM_ASSET_CONTROLLER_MAP X IT@150719 
                        /*
                        $validasi_input_all_io = $this->validasi_input_all_io($request, $status, $noreg);
                
                        if(!$validasi_input_all_io['status'])
                        {
                            return response()->json(['status' => false, "message" => $validasi_input_all_io['message']] );
                            die();
                        }
                        */
                    }
                }

                //echo "masuk ke validasi last approve"; die();            

                $no_registrasi = str_replace("-", "/", $noreg);
                $user_id = Session::get('user_id');
                $note = $request->parNote;
                $role_id = Session::get('role_id');
                $role_name = Session::get('role'); //get role id user
                $asset_controller = ''; //get asset controller 
                //echo $note;die();

                $validasi_last_approve = $this->get_validasi_last_approve($no_registrasi);

                if( $validasi_last_approve == 0 )
                {
                    DB::beginTransaction();
                    
                    try 
                    {
                        DB::STATEMENT('CALL update_approval("'.$no_registrasi.'", "'.$user_id.'","'.$status.'", "'.$note.'", "'.$role_name.'", "'.$asset_type.'")');
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
                    //$validasi_check_gi_amp = $this->get_validasi_check_gi_amp($request,$no_registrasi); //true;
                    //echo "1<pre>"; print_r($validasi_check_gi_amp); die();
                    $validasi_check_gi_amp['status'] = 'success';

                    if($validasi_check_gi_amp['status'] == 'success')
                    {
                        DB::beginTransaction();
                        try 
                        {
                            DB::STATEMENT('CALL complete_document("'.$no_registrasi.'", "'.$user_id.'")');
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
                        return response()->json(['status' => false, "message" => "Error Validasi GI"]);
                    }
                   
                }
            }
        }
        else
        {
            /* SAP PROCESS */ 

            if($status != 'R')
            {
                if($rolename == 'AC')
                {
                    $validasi_io = $this->get_validasi_io($request);
                } 
                else if($rolename == 'AMS')
                {
                    $validasi_io = $this->get_validasi_input_create_asset_sap($request);
                }
                else
                {
                    $validasi_io['status'] = true;
                }
            }
            else
            {
                $validasi_io['status'] = true;            
            }
            //echo "1<pre>"; print_r($validasi_io); die();

            if( $validasi_io['status'] == false )
            {
                return response()->json(['status' => false, "message" => $validasi_io['message']]);
            }
            else
            {
                // IF SYNCHRONIZE SAP SUCCESS

                if( $status != 'R' )
                {
                    if($rolename == 'AC' )
                    {
                        // CEK SEKALI LAGI UNTUK ALL INPUT IO (KODE ASET CONTROLLER) IT@250719  ~ DISKIP KARENA SUDAH DI MAPPING DI TABLE TM_ASSET_CONTROLLER_MAP X IT@150719 
                        $validasi_input_all_io = $this->validasi_input_all_io($request, $status, $noreg);
                
                        if(!$validasi_input_all_io['status'])
                        {
                            return response()->json(['status' => false, "message" => $validasi_input_all_io['message']] );
                            die();
                        }
                        
                    }
                }

                //echo "masuk ke validasi last approve"; die();            

                $no_registrasi = str_replace("-", "/", $noreg);
                $user_id = Session::get('user_id');
                $note = $request->parNote;
                $role_id = Session::get('role_id'); //get role id user
                $role_name = Session::get('role');
                $asset_controller = ''; //get asset controller 
                //echo $note;die();

                $validasi_last_approve = $this->get_validasi_last_approve($no_registrasi);
                if( $validasi_last_approve == 0 )
                {
                    //echo "1".$asset_type; die();

                    DB::beginTransaction();
                    
                    try 
                    {
                        DB::STATEMENT('CALL update_approval("'.$no_registrasi.'", "'.$user_id.'","'.$status.'", "'.$note.'", "'.$role_name.'", "'.$asset_type.'")');
                        DB::commit();
                        return response()->json([ 'status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'update'), "new_noreg"=>$no_registrasi ]);
                    } 
                    catch (\Exception $e) 
                    {
                        DB::rollback();
                        return response()->json(['status' => false, "message" => $e->getMessage()]);
                    }
                }    
                else
                {
                    //echo "3<pre>"; print_r($request->all()); die();

                    if( $req['po-type'] == 'Asset Lainnya' )
                    {
                        $validasi_check_gi['status'] = 'success';
                    }
                    else
                    {
                        // $validasi_check_gi = $this->get_validasi_check_gi($request,$no_registrasi);
                        $validasi_check_gi = $this->get_validasi_check_gi($req,$no_registrasi);
                    }
                    
                    //echo "1<pre>"; print_r($validasi_check_gi); die();

                    if($validasi_check_gi['status']=='success')
                    {
                        DB::beginTransaction();
                        try 
                        {
                            DB::STATEMENT('CALL complete_document("'.$no_registrasi.'", "'.$user_id.'")');
                            DB::commit();
                            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'completed'), "new_noreg"=>$no_registrasi ]);
                        } 
                        catch (\Exception $e) 
                        {
                            DB::rollback();
                            return response()->json(['status' => false, "message" => $e->getMessage()]);
                        }
                    }
                    else
                    {
                        return response()->json(['status' => false, "message" => $validasi_check_gi['message'] ]);
                    }
                   
                }
            }
        }           
    }

    function validasi_asset_controller($noreg)
    {
        $no_registrasi = str_replace("-", "/", $noreg);
        //echo $noreg; die();

        $sql = " SELECT a.JENIS_ASSET,a.GROUP,a.SUB_GROUP FROM TR_REG_ASSET_DETAIL a WHERE a.NO_REG = '{$no_registrasi}' AND (a.DELETED is null OR a.DELETED = '') ";
        $data = DB::SELECT($sql); 
        //echo "1<pre>"; print_r($data); die();
        if( !empty($data) )
        {
            //$result = array("status"=>true, "message"=> 'success' );

            $ac = array();
            $ast = "";

            foreach( $data as $k => $v )
            {
                //echo "1<pre>"; print_r($v);
                /*
                stdClass Object
                (
                    [JENIS_ASSET] => 4030
                    [GROUP] => G20
                    [SUB_GROUP] => SG161
                )
                */
                
                $sql = " SELECT ASSET_CTRL_CODE FROM TM_ASSET_CONTROLLER_MAP WHERE JENIS_ASSET_CODE = '".$v->JENIS_ASSET."' AND GROUP_CODE = '".$v->GROUP."' AND SUBGROUP_CODE = '".$v->SUB_GROUP."' "; //echo $sql; die();
                $datax = DB::SELECT($sql); 
                //echo "1<pre>"; print_r($data); die();
                if(!empty($datax))
                {
                    foreach($datax as $kk => $vv)
                    {
                        //echo "1<pre>"; print_r($v);
                        $ast = $vv->ASSET_CTRL_CODE.","; 
                    }
                    array_push($ac,rtrim($ast,","));
                    //die();
                }
            }
            //die();
            //echo "1<pre>"; print_r($ac);die();

            if (count(array_unique($ac)) === 1) 
            {
                $result = array("status"=>true, "message"=> $ac[0]);
            }
            else
            {
                //echo "1<pre>"; print_r($ac); die();
                if(!empty( $ac ))
                {
                    $result = array("status"=>false, "message"=> "Aset Controller tidak sama / belum disetting");
                }
                else
                {
                    $result = array("status"=>true, "message"=> "");
                }
            }

        }
        else
        {
            $result = array("status"=>false, "message"=> "Data not found");
        }
        return $result;
    }

    function get_validasi_check_gi_amp(Request $request, $noreg)
    {
        $req = $request->all();
        
        $request_gi = json_decode($req['request_gi']);
        //echo "3<pre>"; print_r($request_gi); die();

        if(!empty($request_gi))
        {
            foreach( $request_gi as $k => $v )
            {
                //echo "1<pre>"; print_r($v);
                
                $proses = $this->proses_validasi_check_gi_amp($noreg,$v);

                if($proses['status']=='error')
                {
                    $result = array('status'=>'error','message'=> $proses['message']);
                    return $result;
                    die();
                }
                
            }
            //die();

            //Cek sekali lagi utk penginputan GI Number dan GI Year
            $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '".$noreg."' AND ((GI_NUMBER is null OR GI_NUMBER = '') OR (GI_YEAR is null OR GI_YEAR = '')) AND (DELETED is null OR DELETED = '') ";
            $data = DB::SELECT($sql);

            //echo "4<pre>"; print_r($data); die();
            if(!empty($data))
            {
                $message = '';
                foreach($data as $a => $b)
                {
                    //echo "2<pre>"; print_r($b);
                    $message .= "".$b->KODE_ASSET_AMS.",";
                }
                //die();

                $result = array('status'=>'error','message'=> 'Kode GI Number & Year belum diisi (Kode Asset AMS : '.rtrim($message,',').' ) ' );
                return $result;
            }
            else
            {
                $result = array('status'=>'success','message'=> 'Check GI Success');
                return $result;
            }

            //$result = array('status'=>'success','message'=> 'SUCCESS');
            //return $result;
        }
        else
        {
            //Cek sekali lagi utk penginputan GI Number dan GI Year
            $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '".$noreg."' AND ((GI_NUMBER is null OR GI_NUMBER = '') OR (GI_YEAR is null OR GI_YEAR = '')) AND (DELETED is null OR DELETED = '') ";
            $data = DB::SELECT($sql);
            //echo "4<pre>"; print_r($data); die();
            if(!empty($data))
            {
                $message = '';
                foreach($data as $a => $b)
                {
                    //echo "2<pre>"; print_r($b);
                    $message .= "".$b->KODE_ASSET_AMS.",";
                }
                //die();

                $result = array('status'=>'error','message'=> 'Kode GI Number & Year belum diisi (Kode Asset AMS : '.rtrim($message,',').' ) ' );
                return $result;
            }
            else
            {
                $result = array('status'=>'success','message'=> 'Check GI Success');
                return $result;
            }
            //$result = array('status'=>'error','message'=> 'Kode GI Number & Year belum diisi');
            //return $result;
        }      
    }

    // function get_validasi_check_gi(Request $request, $noreg)
    function get_validasi_check_gi($request, $noreg)
    {
        // $req = $request->all();
        $req = $request;
        
        $request_gi = json_decode($req['request_gi']);
        //echo "3<pre>"; print_r($request_gi); die();

        if(!empty($request_gi))
        {
            foreach( $request_gi as $k => $v )
            {
                //echo "1<pre>"; print_r($v);
                
                $proses = $this->proses_validasi_check_gi($noreg,$v);

                if($proses['status']=='error')
                {
                    $result = array('status'=>'error','message'=> $proses['message']);
                    return $result;
                    die();
                }
                
            }
            //die();

            // MELAKUKAN CEK SEKALI LAGI JIKA INPUTAN GI MASIH ADA YG BLM DIINPUT IT@160719
            // $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '".$noreg."' AND ((GI_NUMBER is null OR GI_NUMBER = '') OR (GI_YEAR is null OR GI_YEAR = '')) AND (DELETED is null OR DELETED = '') ";
            // $data = DB::SELECT($sql);

            
            $data = DB::table('TR_REG_ASSET_DETAIL')
            ->selectRaw("*")
            ->whereRaw ("NO_REG = '$noreg' AND ((GI_NUMBER is null OR GI_NUMBER = '') OR (GI_YEAR is null OR GI_YEAR = '')) AND (DELETED is null OR DELETED = '')")
            ->get();
            // dd($data);

            //echo "4<pre>"; print_r($data); die();
            if(!empty($data))
            {
                $message = '';
                foreach($data as $a => $b)
                {
                    //echo "2<pre>"; print_r($b);
                    $message .= "".$b->KODE_ASSET_SAP.",";
                }
                //die();

                $result = array('status'=>'error','message'=> 'Kode GI Number & Year belum diisi (Kode Asset SAP : '.rtrim($message,',').' ) ' );
                return $result;
            }
            else
            {
                $result = array('status'=>'success','message'=> 'Check GI Success');
                return $result;
            }

            //$result = array('status'=>'success','message'=> 'SUCCESS');
            //return $result;
        }
        else
        {
            $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '".$noreg."' AND ((GI_NUMBER is null OR GI_NUMBER = '') OR (GI_YEAR is null OR GI_YEAR = '')) AND (DELETED is null OR DELETED = '') ";
            $data = DB::SELECT($sql);
            //echo "4<pre>"; print_r($data); die();
            if(!empty($data))
            {
                $message = '';
                foreach($data as $a => $b)
                {
                    //echo "2<pre>"; print_r($b);
                    $message .= "".$b->KODE_ASSET_SAP.",";
                }
                //die();

                $result = array('status'=>'error','message'=> 'Kode GI Number & Year belum diisi (Kode Asset SAP : '.rtrim($message,',').' ) ' );
                return $result;
            }
            else
            {
                $result = array('status'=>'success','message'=> 'Check GI Success');
                return $result;
            }
            //$result = array('status'=>'error','message'=> 'Kode GI Number & Year belum diisi');
            //return $result;
        }      
    }

    function proses_validasi_check_gi_amp($noreg, $data)
    {
        //echo "1<pre>"; print_r($data); die();
        /*
        stdClass Object
        (
            [gi_number] => 1
            [gi_year] => 2
            [no_registrasi] => 19.07/AMS/PDFA/00042
        )
        */

        if( strlen($data->gi_year) != 4 )
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }

        if( $data->gi_number == '' )
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }

        if( $data->gi_year == '' )
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }

        //VALIDASI IF GI NUMBER & GI YEAR NULL THEN LAKUKAN VALIDASI IT@170619
        $sql = " SELECT COUNT(*) AS TOTAL FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$data->no_registrasi}' AND KODE_ASSET_AMS = {$data->kode_sap} AND (GI_NUMBER IS NOT NULL OR GI_NUMBER != '') AND (GI_YEAR IS NOT NULL OR GI_YEAR != '') ";
        $jml = DB::SELECT($sql);
        //echo "2<pre>"; print_r($jml);die();

        if($jml[0]->TOTAL == 0)
        {
            $gi_number = $data->gi_number;
            $gi_year = $data->gi_year;
            $ka_sap = $data->kode_sap;

            $user_id = Session::get('user_id');
            //echo "1".$nore.'====='.$ka_sap.'===='.$ka_con;
                
            DB::beginTransaction();
            try 
            {   
                $sql = " UPDATE TR_REG_ASSET_DETAIL SET GI_NUMBER = '{$gi_number}', GI_YEAR = '{$gi_year}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_AMS = '{$ka_sap}' ";
                //echo $sql; die();
                DB::UPDATE($sql);
                DB::commit();

                $result = array('status'=>'success','message'=> "Validation Success");
            }
            catch (\Exception $e) 
            {
                DB::rollback();
                $result = array('status'=>'error','message'=>$e->getMessage());
            }
            
            return $result;
        }
        else
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }
    }

    function proses_validasi_check_gi($noreg, $data)
    {
        //echo "3<pre>"; print_r($data); die();
        /*
            [gi_number] => 21212
            [gi_year] => 2
            [kode_sap] => 40100248
            [no_registrasi] => 19.07/AMS/PDFA/00038
        */

        //echo "6".strlen($data->gi_year);die();
        if( strlen($data->gi_year) != 4 )
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }

        if( $data->gi_number == '' )
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }

        if( $data->gi_year == '' )
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }

        //VALIDASI IF GI NUMBER & GI YEAR NULL THEN LAKUKAN VALIDASI IT@170619
        $sql = " SELECT COUNT(*) AS TOTAL FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$data->no_registrasi}' AND KODE_ASSET_SAP = {$data->kode_sap} AND (GI_NUMBER IS NOT NULL OR GI_NUMBER != '') AND (GI_YEAR IS NOT NULL OR GI_YEAR != '') ";
        $jml = DB::SELECT($sql);
        //echo "2<pre>"; print_r($jml);die();

        if($jml[0]->TOTAL == 0)
        {
            $gi_number = $data->gi_number;
            $gi_year = $data->gi_year;
            $ka_sap = $data->kode_sap;

            $user_id = Session::get('user_id');
            //echo "1".$nore.'====='.$ka_sap.'===='.$ka_con;
            
            $service = API::exec(array(
                'request' => 'GET',
                'host' => 'ldap',
                'method' => "check_gi?MBLNR=".$gi_number."&MJAHR=".$gi_year."&ANLN1=".$ka_sap."&ANLN2=0", 
            ));
            
            $data = $service;
            //$data = 1;

            if( $data->TYPE == 'S' )
            //if($data==1)
            {
                
                DB::beginTransaction();
                try 
                {   
                    $sql = " UPDATE TR_REG_ASSET_DETAIL SET GI_NUMBER = '{$gi_number}', GI_YEAR = '{$gi_year}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_SAP = '{$ka_sap}' ";
                    //echo $sql; die();
                    DB::UPDATE($sql);
                    DB::commit();

                    $result = array('status'=>'success','message'=> "Validation Success");
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>'error','message'=>$e->getMessage());
                }
                
                //$result = array('status'=>'success','message'=> "Validation Success");
            }
            else
            {
                $result = array('status'=>'error','message'=> $data->MESSAGE.' (GI Number:'.$gi_number.' & Year : '.$gi_year.' )');
            }
            return $result;
        }
        else
        {
            $result = array('status'=>'success','message'=> "Skip Validation");
            return $result;
        }
    }

    public function get_validasi_last_approve($noreg)
    {
        // $sql = " SELECT COUNT(*) AS jml FROM v_history WHERE status_dokumen = 'Disetujui' AND document_code = '{$noreg}' ";
        // $data = DB::SELECT($sql);
        
        // $noreg = str_replace("-", "/", $noreg);
        $data = DB::table('v_history')
							->selectRaw(" COUNT(*) AS jml")
							->whereRaw ("status_dokumen = 'Disetujui' AND document_code = '$noreg'")
                            ->get();
                            

        if($data)
        { 
            $dt = $data[0]->jml; 
        }
        else
        { 
            $dt = '0'; 
        }
        
        return $dt;
    }

    public function get_last_email_approve($noreg)
    {
        // $sql = " SELECT COUNT(*) AS jml FROM v_history WHERE status_dokumen = 'Disetujui' AND document_code = '{$noreg}' ";
        // $data = DB::SELECT($sql);
        
        // $noreg = str_replace("-", "/", $noreg);
        $data = DB::table('v_history_approval')
							->selectRaw(" COUNT(*) AS jml")
							->whereRaw ("status_approval = 'Menunggu' AND document_code = '$noreg'")
                            ->get();
                            

        if($data)
        { 
            $dt = $data[0]->jml; 
        }
        else
        { 
            $dt = '0'; 
        }
        
        return $dt;
    }

    function get_validasi_io(Request $request)
    {
        $req = $request->all();
        $noreg = $req['no-reg'];
        $jenis_dokumen = $req['po-type'];

        //VALIDASI CEK IO JIKA MASIH ADA YANG KOSONG DI MASING2 ASET IT@140819
        $sql = " SELECT KODE_ASSET_AMS,KODE_ASSET_SAP, KODE_ASSET_CONTROLLER FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND (KODE_ASSET_CONTROLLER is null OR KODE_ASSET_CONTROLLER = '' ) ";
        $dt = DB::SELECT($sql);

        if(!empty($dt))
        {
            //#1 VALIDASI MAPPING INPUT KODE ASSET / IO
            $sql = " SELECT a.KODE_ASSET_SAP AS KODE_ASSET_SAP, b.mandatory_kode_asset_controller FROM TR_REG_ASSET_DETAIL a 
    LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.GROUP = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE
    WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '') AND (b.MANDATORY_KODE_ASSET_CONTROLLER is not null AND b.MANDATORY_KODE_ASSET_CONTROLLER != '') ";
            $data = DB::SELECT($sql); 
            //echo "2<pre>"; print_r($data);die();
            if(!empty($data))
            {
                $request_ka = json_decode($req['request_ka']);
                if(!empty($request_ka))
                {
                    foreach( $request_ka as $k => $v )
                    {
                        $proses = $this->validasi_io_proses_v2($noreg,$v,$jenis_dokumen);

                        if($proses['status']=='error')
                        {
                            $result = array('status'=>false,'message'=> $proses['message']);
                            return $result;
                            die();
                        }
                    }
                    //die();
                    //$result = array('status'=>true,'message'=> 'SUCCESS');
                    //return $result;

                    //VALIDASI CEK IO JIKA MASIH ADA YANG KOSONG DI MASING2 ASET IT@140819
                    $sql = " SELECT KODE_ASSET_AMS,KODE_ASSET_SAP, KODE_ASSET_CONTROLLER FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND (KODE_ASSET_CONTROLLER is null OR KODE_ASSET_CONTROLLER = '' ) ";
                    $dt = DB::SELECT($sql);

                    if(!empty($dt))
                    {
                        $message = '';
                        foreach($dt as $k => $v)
                        {
                            $message .= $v->KODE_ASSET_SAP.",";
                        }
                        
                        $result = array('status'=>false,'message'=> 'Kode IO Asset Controller belum diisi! ( Kode Asset SAP : '.rtrim($message,',').' )');
                        return $result;
                    }
                    else
                    {
                        $result = array('status'=>true,'message'=> 'Validasi IO PO SENDIRI Success');
                        return $result;   
                    }
                    //END VALIDASI CEK IO JIKA MASIH ADA YANG KOSONG DI MASING2 ASET
                }
                else
                {
                    $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND (KODE_ASSET_CONTROLLER is null OR KODE_ASSET_CONTROLLER = '' ) ";

                    /*hide it@071519
                    //Cek Data Jenis Asset harus kendaraan
                    $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '".$noreg."' AND JENIS_ASSET IN ('E4030','4030', '4010') AND (KODE_ASSET_SAP != '' OR KODE_ASSET_SAP IS NOT NULL) ";
                    */

                    $dt = DB::SELECT($sql); 
                    //echo "4<pre>"; print_r($dt);die();

                    if(!empty($dt))
                    {
                        $message = '';
                        foreach($dt as $k => $v)
                        {
                            //echo "5<pre>"; print_r($v);
                            $message .= $v->KODE_ASSET_SAP.",";
                        }
                        //die();
                        $result = array('status'=>false,'message'=> 'Kode IO Asset Controller belum diisi! ( Kode Asset SAP : '.rtrim($message,',').' )');
                        return $result;
                    }
                    else
                    {
                        $result = array('status'=>true,'message'=> 'Success');
                        return $result;   
                    }
                }   
            }
            else
            {
                $result = array('status'=>true,'message'=> 'Validasi Kode Asset Controller / IO sudah berhasil');
                return $result;   
            }
            //#1 END VALIDASI MAPPING INPUT KODE ASSET / IO
        }
        else
        {
            $result = array('status'=>true,'message'=> 'Validasi Kode Asset Controller / IO sudah berhasil');
            return $result;   
        }
        //END VALIDASI CEK IO JIKA MASIH ADA YANG KOSONG DI MASING2 ASET
        
    }

    function get_validasi_io_amp(Request $request , $status, $no_reg)
    {
        $req = $request->all();
        $noreg = $req['no-reg'];
        $po_type = $req['po-type'];

        //#1 VALIDASI MAPPING INPUT KODE ASSET / IO
        $sql = " SELECT a.KODE_ASSET_SAP AS KODE_ASSET_SAP, b.mandatory_kode_asset_controller FROM TR_REG_ASSET_DETAIL a 
LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.GROUP = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE
WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '')  AND (b.MANDATORY_KODE_ASSET_CONTROLLER is not null AND b.MANDATORY_KODE_ASSET_CONTROLLER != '')  ";    
        $data = DB::SELECT($sql); 
        
        if(!empty($data))
        {
            $request_ka = json_decode($req['request_ka']);
            
            if(!empty($request_ka))
            {
                foreach( $request_ka as $k => $v )
                {
                    $proses = $this->validasi_io_proses_amp($noreg, $v, $po_type);
                
                    if($proses['status']=='error')
                    {
                        $result = array('status'=>false,'message'=> $proses['message']);
                        return $result;
                        die();
                    }
                }

                //VALIDASI CEK IO JIKA MASIH ADA YANG KOSONG DI MASING2 ASET
                $sql = " SELECT KODE_ASSET_AMS,KODE_ASSET_SAP, KODE_ASSET_CONTROLLER FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND (KODE_ASSET_CONTROLLER is null OR KODE_ASSET_CONTROLLER = '' ) ";
                $dt = DB::SELECT($sql);

                if(!empty($dt))
                {
                    $message = '';
                    foreach($dt as $k => $v)
                    {
                        $message .= $v->KODE_ASSET_AMS.",";
                    }
                    
                    $result = array('status'=>false,'message'=> 'Kode IO Asset Controller belum diisi! ( Kode Asset AMS : '.rtrim($message,',').' )');
                    return $result;
                }
                else
                {
                    $result = array('status'=>true,'message'=> 'Validasi IO AMP Success');
                    return $result;   
                }
                //END VALIDASI CEK IO JIKA MASIH ADA YANG KOSONG DI MASING2 ASET
            }
            else
            {
                $list_kac_required = $this->list_kac_required($noreg);
                $result = array('status'=>false,'message'=> 'Kode Aset Controller belum diisi ('.$list_kac_required.')');
                return $result;
            }
        }
        else
        {
            $result = array('status'=>true,'message'=> 'Tidak menginput kode asset');
            return $result;
        }
        
    }

    function validasi_io_proses_amp($noreg, $data, $po_type)
    {
        $ka_con = $data->kode_aset_controller;
        $ka_sap = $data->kode_aset_sap;
        $user_id = Session::get('user_id');
        
        /*DB::beginTransaction();
        try 
        {   
            $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_AMS = '{$ka_sap}' ";
            DB::UPDATE($sql);
            DB::commit();

            $result = array('status'=>'success','message'=> "SUKSES UPDATE KODE ASET");
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            $result = array('status'=>'error','message'=>$e->getMessage());
        }
        return $result; exit;*/
        
        // VALIDASI MANDATORY CHECK IO SAP IT@130819
        $sql = " SELECT a.KODE_ASSET_SAP AS KODE_ASSET_SAP, b.mandatory_kode_asset_controller FROM TR_REG_ASSET_DETAIL a 
LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.GROUP = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE
WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '') AND (b.MANDATORY_CHECK_IO_SAP is not null AND b.MANDATORY_CHECK_IO_SAP != '') ";
        $data = DB::SELECT($sql); 

        if(!empty($data))
        {
            $service = API::exec(array(
                'request' => 'GET',
                'host' => 'ldap',
                'method' => "check_io?AUFNR=$ka_con&AUFUSER3=$ka_sap", 
            ));
            
            $data = $service;
            //$data = 1;
            //echo "<pre>"; print_r($data); die();

            if( $data->TYPE == 'S' )
            //if($data==1)
            {
                DB::beginTransaction();
                try 
                {   
                    $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_AMS = '{$ka_sap}' ";
                    DB::UPDATE($sql);
                    DB::commit();

                    $result = array('status'=>'success','message'=> "SUKSES UPDATE KODE ASET");
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>'error','message'=>$e->getMessage());
                }
            }
            else
            {    
                $result = array('status'=>'error','message'=> $data->MESSAGE.'');
                //$result = array('status'=>'error','message'=> $data->MESSAGE.' (Kode Aset Controller:'.$ka_con.')');
            }

            return $result;
        }
        else
        {
            // JIKA ASET LAIN MAKA SKIP CHECK IO SAP IT@130819
            DB::beginTransaction();
            try 
            {   
                $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_AMS = '{$ka_sap}' ";
                DB::UPDATE($sql);
                DB::commit();

                $result = array('status'=>'success','message'=> "SUKSES UPDATE KODE ASET");
            }
            catch (\Exception $e) 
            {
                DB::rollback();
                $result = array('status'=>'error','message'=>$e->getMessage());
            }
            return $result;
        }
    }

    function validasi_io_proses_v2($noreg, $data, $po_type)
    {
        //echo "<pre>"; print_r($data); die();
        /*
        stdClass Object
            (
                [kode_aset_controller] => 1
                [kode_aset_sap] => 40100246
                [no_registrasi] => 19.07/AMS/PDFA/00038
            )
        */

        $ka_con = $data->kode_aset_controller;
        $ka_sap = $data->kode_aset_sap;

        $user_id = Session::get('user_id');
        //echo "1".$nore.'====='.$ka_sap.'===='.$ka_con;

        // VALIDASI MANDATORY_CHECK_IO_SAP IT@130819
        $sql = " SELECT a.KODE_ASSET_SAP AS KODE_ASSET_SAP, b.mandatory_kode_asset_controller FROM TR_REG_ASSET_DETAIL a 
LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.GROUP = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE
WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '')  AND (b.MANDATORY_CHECK_IO_SAP is not null AND b.MANDATORY_CHECK_IO_SAP != '') ";
        $data = DB::SELECT($sql); 

        if(!empty($data))
        {
            $service = API::exec(array(
                'request' => 'GET',
                'host' => 'ldap',
                'method' => "check_io?AUFNR=$ka_con&AUFUSER3=$ka_sap", 
            ));
            
            $data = $service;
            //$data = 1;
            
            //echo "<pre>"; print_r($data); die();

            if( $data->TYPE == 'S' )
            //if($data==1)
            {
                DB::beginTransaction();
                try 
                {   
                    $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_AMS = '{$ka_sap}' ";
                    //echo $sql; die();
                    DB::UPDATE($sql);
                    DB::commit();

                    $result = array('status'=>'success','message'=> "SUKSES UPDATE KODE ASET");
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>'error','message'=>$e->getMessage());
                }

            }
            else
            {                
                //$result = array('status'=>'error','message'=> $data->MESSAGE.' (Kode Aset Controller:'.$ka_con.')');
                $result = array('status'=>'error','message'=> $data->MESSAGE.'');
            }
            return $result;
        }
        else
        {
            // SKIP VALIDASI CHECK IO SAP JIKA ASSET LAINNYA
            DB::beginTransaction();
            try 
            {   
                $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_AMS = '{$ka_sap}' ";
                //echo $sql; die();
                DB::UPDATE($sql);
                DB::commit();

                $result = array('status'=>'success','message'=> "SUKSES UPDATE KODE ASET");
            }
            catch (\Exception $e) 
            {
                DB::rollback();
                $result = array('status'=>'error','message'=>$e->getMessage());
            }
            return $result;   
        }
    }

    function log_history($id)
    {
        $noreg = str_replace("-", "/", $id);

        $records = array();

        $sql = "SELECT document_code,user_id,name,area_code,status_approval,notes,date FROM v_history_approval WHERE document_code = '{$noreg}' ORDER BY -date ASC, date ASC ";

        /*$sql = "SELECT a.document_code,a.user_id,a.name AS name_role,a.area_code,a.status_approval,a.notes,a.date,b.name AS nama_lengkap FROM v_history_approval a LEFT JOIN TBM_USER b ON a.user_id = b.id WHERE a.document_code = '{$noreg}' ORDER BY -a.date ASC, -a.date ASC ";*/

        /*$sql = "SELECT a.*, date_format(a.date,'%d-%m-%Y %h:%i:%s') AS date2 FROM v_history a WHERE a.document_code = '{$noreg}' ORDER BY a.date";*/

        $data = DB::SELECT($sql);
        //echo "3<pre>"; print_r($data); die();
        
        if($data)
        {

            foreach ($data as $k => $v) 
            {
                //echo "<pre>"; print_r($v->NO_REG);
                # code...

                $notes = $v->notes == '' ? '' : $v->notes;

                $records[] = array(
                    'document_code' => $v->document_code,
                    'area_code' => $v->area_code,
                    'user_id' => $v->user_id,
                    'name' => $v->name,
                    //'status_dokumen' => $v->status_dokumen,
                    'status_approval' => $v->status_approval,
                    'notes' => $notes,
                    'date' => $v->date,
                    //'item_detail' => $this->get_item_detail($noreg)
                );

            }
        }

        echo json_encode($records);
    }

    function get_sinkronisasi_sap($noreg)
    {
        $noreg = str_replace("-", "/", $noreg);
        $request = array();
        $datax = '';
        $sql = " SELECT a.KODE_MATERIAL, a.NAMA_MATERIAL FROM v_kode_asset_sap a WHERE a.NO_REG = '{$noreg}' ";
        $data = DB::SELECT($sql);

        if(!empty($data))
        {
            $material = '';//;

            if( $data[0]->KODE_MATERIAL != '' )
            {
                $material = $data[0]->KODE_MATERIAL;
            }else
            {
                $material = $data[0]->NAMA_MATERIAL;
            }


            $datax .= $material;
        }
        
        return $datax;
    }

    function get_sinkronisasi_amp($noreg)
    {
        $request = array();
        $datax = '';
        $sql = " SELECT COUNT(*) AS TOTAL FROM TR_REG_ASSET_DETAIL a WHERE a.no_reg = '{$noreg}' AND (a.KODE_ASSET_AMS IS NULL OR a.KODE_ASSET_AMS = '') AND (a.DELETED is null OR a.DELETED = '') ";
        $data = DB::SELECT($sql);
        //echo "<pre>"; print_r($data); die();

        if($data)
        {
            $datax .= $data[0]->TOTAL;
            foreach( $data as $k => $v )
            {
                $request[] = array
                (
                    'SYNC_AMP' => trim($v->TOTAL),
                );
            }
        }

        return $datax;
    }

    function synchronize_sap(Request $request)
    {
        $no_reg = @$request->noreg;

        $sql = " SELECT a.*, date_format(a.CAPITALIZED_ON,'%d.%m.%Y') AS CAPITALIZED_ON, date_format(a.DEACTIVATION_ON,'%d.%m.%Y') AS DEACTIVATION_ON FROM TR_REG_ASSET_DETAIL a WHERE a.NO_REG = '{$no_reg}' AND (a.KODE_ASSET_SAP = '' OR a.KODE_ASSET_SAP is null) AND (a.DELETED is null OR a.DELETED = '') ";

        $data = DB::SELECT($sql); 

        $params = array();

        if($data)
        {
            foreach( $data as $k => $v )
            {
                // IF UOM NULL or OBJECT VAL
                $uom_asset_sap = $v->UOM_ASSET_SAP;
                if (strpos($uom_asset_sap, '[object Object]') !== false) 
                {
                    return response()->json(['status' => false, 'message' => 'UOM ASSET SAP error, silahkan utk diupdate kembali' ]);
                    die();
                }   

                $proses = $this->synchronize_sap_process($v);             
                
                if($proses['status']=='error')
                {
                               
                    dd($proses);
                    return response()->json(['status' => false, "message" => $proses['message']]);
                    die();
                }
            }

            return response()->json(['status' => true, "message" => "Synchronize success"]);
        }
        else
        { 
            $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_SAP = '' WHERE NO_REG = '{$no_reg}' "; 
                DB::UPDATE($sql);
            return response()->json(['status' => false, "message" => "Synchronize failed, data not found"]);
        }
    }

    function synchronize_amp(Request $request)
    {
        $no_reg = @$request->noreg;
        //echo $no_reg; 

        #CREATE KODE ASSET FAMS
        $sql = " SELECT * FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$no_reg}' ";
        $data = DB::SELECT($sql);

        if( !empty($data) )
        {
            foreach($data as $k => $v)
            {
                if( !$this->execute_amp_create_kode_asset_ams($no_reg, $v) )
                {
                    //return response()->json(['status' => false, "message" => "Synchronize AMP failed"]);
                    return response()->json(['status' => false, "message" => "Synchronize failed"]);
                    die();
                }
            }

            return response()->json(['status' => true, "message" => "Synchronize berhasil"]);
            //return response()->json(['status' => true, "message" => "Synchronize AMP berhasil"]);
            //die();
        }
        else
        {
            return response()->json(['status' => false, "message" => "Tidak ada data yang di Synchronize"]);
        }
    }

    public function synchronize_sap_process($dt) 
    {
        $ANLA_BUKRS = substr($dt->BA_PEMILIK_ASSET,0,2);
        $ANLA_LIFNR = $this->get_kode_vendor($dt->NO_REG);
        $ANLA_SERNR = substr($dt->NO_RANGKA_OR_NO_SERI,0,18);
        $ANLA_INVNR = substr($dt->NO_MESIN_OR_IMEI,0,25);

        $service = API::exec(array(
            'request' => 'GET',
            'host' => 'ldap',
            'method' => "create_asset?ANLA_ANLKL={$dt->JENIS_ASSET}&ANLA_BUKRS={$ANLA_BUKRS}&RA02S_NASSETS=1&ANLA_TXT50={$dt->NAMA_ASSET_1}&ANLA_TXA50={$dt->NAMA_ASSET_2}&ANLH_ANLHTXT={$dt->NAMA_ASSET_3}&ANLA_SERNR={$ANLA_SERNR}&ANLA_INVNR={$ANLA_INVNR}&ANLA_MENGE={$dt->QUANTITY_ASSET_SAP}&ANLA_MEINS={$dt->UOM_ASSET_SAP}&ANLA_AKTIV={$dt->CAPITALIZED_ON}&ANLA_DEAKT={$dt->DEACTIVATION_ON}&ANLZ_GSBER={$dt->BA_PEMILIK_ASSET}&ANLZ_KOSTL={$dt->COST_CENTER}&ANLZ_WERKS=$dt->BA_PEMILIK_ASSET&ANLA_LIFNR={$ANLA_LIFNR}&ANLB_NDJAR_01={$dt->BOOK_DEPREC_01}&ANLB_NDJAR_02={$dt->FISCAL_DEPREC_15}", 
        ));
        
        $data = $service;
        if( !empty($data->item->TYPE) )
        {
            #2
            if( $data->item->TYPE == 'S' )
            {
                $user_id = Session::get('user_id');
                $asset_controller = $this->get_asset_controller($user_id,$dt->LOKASI_BA_CODE);

                DB::beginTransaction();
                try 
                {   
                    //1. ADD KODE_ASSET_SAP & ASSET_CONTROLLER TR_REG_ASSET 
                    $sql_1 = " UPDATE TR_REG_ASSET_DETAIL SET ASSET_CONTROLLER = '{$asset_controller}', KODE_ASSET_SAP = '".$data->item->MESSAGE_V1."', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE NO_REG = '{$dt->NO_REG}' AND ASSET_PO_ID = '{$dt->ASSET_PO_ID}' AND NO_REG_ITEM = '{$dt->NO_REG_ITEM}' ";
                    DB::UPDATE($sql_1);

                    //2. INSERT LOG
                    $create_date = date("Y-m-d H:i:s");
                    $sql_2 = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG}','{$dt->ASSET_PO_ID}','{$dt->NO_REG_ITEM}','".$data->item->TYPE."','".$data->item->ID."','".$data->item->NUMBER."','".$data->item->MESSAGE."','".$data->item->MESSAGE_V1."','".$data->item->MESSAGE_V2."','".$data->item->MESSAGE_V3."','".$data->item->MESSAGE_V4."','".$create_date."') ";
                    DB::INSERT($sql_2);

                    DB::commit();

                    //3. CREATE CODE ASSET AMS
                    $sql_3 = " CALL create_kode_asset_ams('".$dt->NO_REG."', '".$ANLA_BUKRS."', '".$dt->JENIS_ASSET."', '".$data->item->MESSAGE_V1."') ";
                    //echo $sql_3; die();
                    DB::STATEMENT($sql_3);

                    return true;
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    return false;
                    //die();
                }
            }
            else 
            {
                DB::beginTransaction();

                try 
                {   
                    $create_date = date("Y-m-d H:i:s");
                    $sql = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG}','{$dt->ASSET_PO_ID}','{$dt->NO_REG_ITEM}','".$data->item->TYPE."','".$data->item->ID."','".$data->item->NUMBER."','".$data->item->MESSAGE."','".$data->item->MESSAGE_V1."','".$data->item->MESSAGE_V2."','".$data->item->MESSAGE_V3."','".$data->item->MESSAGE_V4."','".$create_date."') ";
                    
                    DB::INSERT($sql); 
                    DB::commit();
                    
                    $result = array('status'=>'error','message'=> ''.$data->item->MESSAGE.' (No Reg Item: '.$dt->NO_REG_ITEM.')');
                    return $result;                 
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>'error','message'=>$e->getMessage());
                    return $result;
                }
            }         
        }
        
        if( !empty($data->item[0]->TYPE) ) 
        {
            //RETURN ARRAY LEBIH DARI 1 ROW
            $result = array();
            $message = '';

            //echo "20<pre>"; count($data); die();

            foreach($data->item as $k => $v)
            {
                //echo "20<pre>"; print_r($v);
                
                if( $v->TYPE == 'S' && $v->ID == 'AA' && $v->NUMBER == 228 )
                {
                    $message .= $v->MESSAGE.',';
                    $result = array(
                        'TYPE' => 'S',
                        'ID' => $v->ID,
                        'NUMBER' => $v->NUMBER,
                        'MESSAGE' => $message,
                        'LOG_NO' => $v->LOG_NO,
                        'LOG_MSG_NO' => $v->LOG_MSG_NO,
                        'MESSAGE_V1' => $v->MESSAGE_V1,
                        'MESSAGE_V2' => $v->MESSAGE_V2,
                        'MESSAGE_V3' => $v->MESSAGE_V3,
                        'MESSAGE_V4' => $v->MESSAGE_V4
                    );
                }
                else
                {
                    $message .= $v->MESSAGE.',';
                    $result = array(
                        'TYPE' => 'E',
                        'ID' => $v->ID,
                        'NUMBER' => $v->NUMBER,
                        'MESSAGE' => $message,
                        'LOG_NO' => $v->LOG_NO,
                        'LOG_MSG_NO' => $v->LOG_MSG_NO,
                        'MESSAGE_V1' => $v->MESSAGE_V1,
                        'MESSAGE_V2' => $v->MESSAGE_V2,
                        'MESSAGE_V3' => $v->MESSAGE_V3,
                        'MESSAGE_V4' => $v->MESSAGE_V4
                    );
                }
                
            }
            //die();
            

            if( $result['TYPE'] == 'S' )
            {
                $user_id = Session::get('user_id');
                $asset_controller = $this->get_asset_controller($user_id,$dt->LOKASI_BA_CODE);

                DB::beginTransaction();
                try 
                {   
                    //1. ADD KODE_ASSET_SAP & ASSET_CONTROLLER TR_REG_ASSET 
                    $sql_1 = " UPDATE TR_REG_ASSET_DETAIL SET ASSET_CONTROLLER = '{$asset_controller}', KODE_ASSET_SAP = '".$result['MESSAGE_V1']."', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE NO_REG = '{$dt->NO_REG}' AND ASSET_PO_ID = '{$dt->ASSET_PO_ID}' AND NO_REG_ITEM = '{$dt->NO_REG_ITEM}' ";
                    DB::UPDATE($sql_1);

                    //2. INSERT LOG
                    $create_date = date("Y-m-d H:i:s");
                    $sql_2 = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG}','{$dt->ASSET_PO_ID}','{$dt->NO_REG_ITEM}','".$result['TYPE']."','".$result['ID']."','".$result['NUMBER']."','".$result['MESSAGE']."','".$result['MESSAGE_V1']."','".$result['MESSAGE_V2']."','".$result['MESSAGE_V3']."','".$result['MESSAGE_V4']."','".$create_date."') ";
                    DB::INSERT($sql_2);

                    DB::commit();

                    //3. CREATE CODE ASSET AMS
                    $sql_3 = " CALL create_kode_asset_ams('".$dt->NO_REG."', '".$ANLA_BUKRS."', '".$dt->JENIS_ASSET."', '".$data->item->MESSAGE_V1."') ";
                    //echo $sql_3; die();
                    DB::STATEMENT($sql_3);

                    return true;
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    return false;
                    //die();
                }
            }
            else 
            {
                DB::beginTransaction();

                try 
                {    
                    $create_date = date("Y-m-d H:i:s");
                    $sql = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG}','{$dt->ASSET_PO_ID}','{$dt->NO_REG_ITEM}','".$result['TYPE']."','".$result['ID']."','".$result['NUMBER']."','".$result['MESSAGE']."','".$result['MESSAGE_V1']."','".$result['MESSAGE_V2']."','".$result['MESSAGE_V3']."','".$result['MESSAGE_V4']."','".$create_date."') ";
                    
                    DB::INSERT($sql); 
                    DB::commit();
                    
                    $result = array('status'=>'error','message'=> ''.$result['MESSAGE'].' (No Reg Item: '.$dt->NO_REG_ITEM.')');
                    return $result;                 
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>'error','message'=>$e->getMessage());
                    return $result;
                }
            }              
        }
    }

    public function get_asset_controller($user_id, $area_code)
    {
        $sql = "SELECT description FROM v_user WHERE id = '{$user_id}' AND area_code = '{$area_code}' ";
        $data = DB::SELECT($sql);
        if(!empty($data)){ $dt = $data[0]->description; }else{ $dt = ''; }
        return $dt;
    }

    public function get_kode_vendor($noreg)
    {
        $sql = "SELECT KODE_VENDOR FROM TR_REG_ASSET WHERE NO_REG = '{$noreg}' ";
        $data = DB::SELECT($sql);
        if($data){ $dt = $data[0]->KODE_VENDOR; }else{ $dt = '0'; }
        return $dt;
    }

    /*
    function update_ka_con_temp(Request $request)
    {
        $req = $request->all();
        echo "<pre>"; print_r($req); die();
    }
    */

    public function execute_create_kode_asset_ams($dt) 
    { 
        $ANLA_BUKRS = substr($dt->BA_PEMILIK_ASSET,0,2);
        $user_id = Session::get('user_id');

        DB::beginTransaction();
        try 
        {   
            //3. CREATE KODE ASSET AMS PROCEDURE
            $sql_3 = 'CALL create_kode_asset_ams("'.$noreg.'", "'.$ANLA_BUKRS.'", "'.$dt->JENIS_ASSET.'", "'.$dt->KODE_ASSET_SAP.'")';
            //echo $sql_3; die();
            DB::STATEMENT($sql_3);

            DB::commit();

            return true;
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            return false;
            //die();
        } 
    }

    public function execute_amp_create_kode_asset_ams($noreg,$dt) 
    { 
        //return true;
        $ANLA_BUKRS = substr($dt->BA_PEMILIK_ASSET,0,2);
        $user_id = Session::get('user_id');

        DB::beginTransaction();
        try 
        {   
            $sql = " CALL create_kode_asset_ams('".$noreg."', '".$ANLA_BUKRS."', '".$dt->JENIS_ASSET."', '-".$dt->ID."') ";
            //echo $sql; die();
            DB::STATEMENT($sql);
            DB::commit();

            return true;
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            return false;
            //die();
        } 
    }

    function validasi_outstanding($noreg,$role_id)
    {
        $sql = " SELECT COUNT(*) AS JML FROM v_outstanding WHERE document_code = '{$noreg}' AND role_id = $role_id ";
        //echo $sql; die();
        $data = DB::SELECT($sql); 
        //echo "4<pre>"; print_r($data);die(); 
        return $data[0]->JML;
    }

    function get_new_asset($noreg)
    {
        $sql = "  SELECT COUNT(*) AS JML FROM TM_MSTR_ASSET a left join TR_MUTASI_ASSET_DETAIL b ON a.KODE_ASSET_AMS = b.KODE_ASSET_AMS_TUJUAN WHERE b.NO_REG = '{$noreg}' ";
        $data = DB::SELECT($sql); 
        return $data[0]->JML;
    }

    function berkas_amp($noreg)
    {
        $noreg = base64_decode($noreg);

        $sql = " SELECT DOC_SIZE,FILENAME,FILE_CATEGORY,FILE_UPLOAD FROM TR_REG_ASSET_FILE a WHERE a.no_reg = '{$noreg}' ";
        $data = DB::SELECT($sql);
        
        $l = "";
        if(!empty($data))
        {
            $l .= '<center>';

            if( $data[0]->FILE_CATEGORY == 'image/jpeg' || $data[0]->FILE_CATEGORY == 'image/png' )
            {
                $l .= '<h1>'.$noreg.'</h1><br/>';
                $l .= '<div class="caption"><h3><img src="'.$data[0]->FILE_UPLOAD.'"/><br/>'. $data[0]->FILENAME. '</h3></div>';
            }
            else if($data[0]->FILE_CATEGORY == 'application/pdf')
            {
                $l .= '<object data="'.$data[0]->FILE_UPLOAD.'" type="'.$data[0]->FILE_CATEGORY.'" style="height:100%;width:100%"></object>';
            }
            else
            {
                $data_excel = explode(",",$data[0]->FILE_UPLOAD);
                header('Content-type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="'.$data[0]->FILENAME.'"');
                print $data_excel[1];
                die();
            }
        }
        else
        {
            $l .= "FILE NOT FOUND";
        }

        $l .= '</center>';
        echo $l; 
    }

    function get_sinkronisasi_lain($noreg)
    {
        #1 CEK SYNC SAP / TIDAK 
        $cek_sap = $this->get_sinkronisasi_sap($noreg);
        //echo "1<pre>"; print_r($cek_sap);die();
        if($cek_sap != "")
        { 
            $datax = '';
            $sql = " SELECT COUNT(*) AS TOTAL FROM TR_REG_ASSET_DETAIL a WHERE a.no_reg = '{$noreg}' AND (a.KODE_ASSET_SAP IS NULL OR a.KODE_ASSET_SAP = '') AND (a.DELETED is null OR a.DELETED = '') "; //echo $sql."<br/>";
            $data = DB::SELECT($sql);
            //echo "2<pre>"; print_r($data); die();

            if( $data )
            {
                $datax .= $data[0]->TOTAL;
            }
            else
            {
                $datax .= 0;
            }

            if( $datax > 0 )
            {
                return "SAP";
            }
            else
            {
                return "ASET SAP SUDAH DI SYNC";    
            }
        }
        else
        {
            $cek_amp = $this->get_sinkronisasi_amp($noreg);
            //echo $cek_amp; die();
            if($cek_amp!=0)
            {
                return "AMP";
            }  
            else
            {
                return "ASET LAIN SUDAH DI SYNC";
            }
        }
    }

    function get_validasi_delete_asset($noreg)
    {
        //echo "<pre>"; print_r($noreg); die();
        $sql = " SELECT COUNT(*) AS TOTAL FROM TR_REG_ASSET_DETAIL a WHERE a.NO_REG = '{$noreg}' AND (a.DELETED is null OR a.DELETED = '') ";
        $data = DB::SELECT($sql);
        //echo "1<pre>"; print_r($data);die();
        return $data[0]->TOTAL;
    }

    function update_kode_vendor_aset_lain(Request $request)
    {
        $user_id = Session::get('user_id');

        DB::beginTransaction();

        try 
        {

            $sql = " UPDATE TR_REG_ASSET a
                        SET 
                            a.kode_vendor = '{$request->new_kode_vendor}',
                            a.updated_by = '{$user_id}',
                            a.updated_at = current_timestamp()
                    WHERE a.NO_REG = '{$request->getnoreg}' AND a.NO_PO = '{$request->no_po}' ";
            DB::UPDATE($sql);    

            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully updated']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    function get_cek_reject($noreg)
    {
        //echo "1~".$noreg;

        $sql = " SELECT COUNT(*) AS total FROM v_history WHERE document_code = '{$noreg}' AND status_approval = 'Tolak' ";
        $data = DB::SELECT($sql);
        //echo "2<pre>"; print_r($data); die();

        return $data[0]->total;
    }

    function update_kode_asset_controller(Request $request)
    {
        $po_type = $request->po_type;
        $noreg = $request->getnoreg;
        $ka_con = $request->kode_asset_controller;
        $ka_sap = $request->kode_asset_nilai;
        $user_id = Session::get('user_id');

        //#1 VALIDASI MAPPING INPUT KODE ASSET / IO
        $sql = " SELECT a.KODE_ASSET_SAP AS KODE_ASSET_SAP, b.mandatory_kode_asset_controller FROM TR_REG_ASSET_DETAIL a 
LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.GROUP = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE
WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '') AND (b.MANDATORY_KODE_ASSET_CONTROLLER is not null AND b.MANDATORY_KODE_ASSET_CONTROLLER != '') ";
        $data = DB::SELECT($sql); //echo "2<pre>"; print_r($data); die(); 
        if(!empty($data))
        {
            // #2 VALIDASI MANDATORY_CHECK_IO_SAP IT@140819
            $sql2 = " SELECT a.KODE_ASSET_SAP AS KODE_ASSET_SAP, b.MANDATORY_KODE_ASSET_CONTROLLER, b.MANDATORY_CHECK_IO_SAP  FROM TR_REG_ASSET_DETAIL a 
    LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.GROUP = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE
    WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '')  AND (b.MANDATORY_CHECK_IO_SAP is not null AND b.MANDATORY_CHECK_IO_SAP != '') ";
            $data2 = DB::SELECT($sql2); //echo "2<pre>"; print_r($data2); die(); 

            if( $po_type == 1 || $po_type == 2 )
            {   
                // AMP & LAIN
                $kode_asset = "KODE_ASSET_AMS";
                
                if( $po_type == 1 )
                {
                    $kode_asset_label = "KODE ASSET FAMS";
                }
                else
                {
                    $kode_asset_label = "KODE ASSET FAMS / SAP";    
                }
                   
            }
            else
            {
                // SAP
                $kode_asset = "KODE_ASSET_SAP";
                $kode_asset_label = "KODE ASSET SAP"; 
            }

            if(!empty($data2))
            {
                if( $ka_sap == '' )
                {
                    $result = array('status'=>false,'message'=> ''.$kode_asset_label.' kosong! ');
                    return $result;  
                }

                $service = API::exec(array(
                    'request' => 'GET',
                    'host' => 'ldap',
                    'method' => "check_io?AUFNR=$ka_con&AUFUSER3=$ka_sap", 
                ));
                
                $data = $service;
                //$data = 1;
                
                //echo "<pre>"; print_r($data); die();

                if( $data->TYPE == 'S' )
                //if($data==1)
                {

                    DB::beginTransaction();
                    try 
                    {   
                        $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND $kode_asset = '{$ka_sap}' ";
                        //echo $sql; die();
                        DB::UPDATE($sql);
                        DB::commit();

                        $result = array('status'=>true,'message'=> "SUKSES UPDATE KODE ASET");
                    }
                    catch (\Exception $e) 
                    {
                        DB::rollback();
                        $result = array('status'=>false,'message'=>$e->getMessage());
                    }
                }
                else
                {
                    
                    $result = array('status'=>false,'message'=> $data->MESSAGE.' (Kode Aset Controller:'.$ka_con.')');
                }
                return $result;
            }
            else
            {
                if( $ka_sap == '' )
                {
                    $result = array('status'=>false,'message'=> ''.$kode_asset_label.' required! ');
                    return $result;  
                }

                // SKIP VALIDASI CHECK IO SAP JIKA ASSET LAINNYA
                DB::beginTransaction();
                try 
                {   
                    $sql = " UPDATE TR_REG_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND $kode_asset = '{$ka_sap}' ";
                    //echo $sql; die();
                    DB::UPDATE($sql);
                    DB::commit();

                    $result = array('status'=>true,'message'=> "Updated Success");
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>false,'message'=>$e->getMessage());
                }
                return $result;   
            }
            // #2 END VALIDASI MANDATORY_CHECK_IO_SAP IT@140819
        }
        else
        {
            $result = array('status'=>false,'message'=> 'Tidak perlu menginput Kode Asset Controller / IO');
            return $result;   
        }
        //#1 END VALIDASI MAPPING INPUT KODE ASSET / IO
    }

    function update_kode_asset_controller_mutasi(Request $request)
    {
        $po_type = $request->po_type;
        $noreg = $request->getnoreg;
        $ka_con = $request->kode_asset_controller;
        $ka_sap = $request->kode_asset_nilai;
        $user_id = Session::get('user_id');

        //#1 VALIDASI MAPPING INPUT KODE ASSET / IO
        $sql = " SELECT a.KODE_SAP_TUJUAN AS KODE_ASSET_SAP, b.mandatory_kode_asset_controller FROM TR_MUTASI_ASSET_DETAIL a 
        LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET_TUJUAN = b.JENIS_ASSET_CODE 
        LEFT JOIN TM_MSTR_ASSET c ON c.KODE_ASSET_AMS = a.KODE_ASSET_AMS AND c.GROUP = b.GROUP_CODE AND c.SUB_GROUP = b.SUBGROUP_CODE
        WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '') AND (b.MANDATORY_KODE_ASSET_CONTROLLER is not null AND b.MANDATORY_KODE_ASSET_CONTROLLER != '') ";
        $data = DB::SELECT($sql); //echo "2<pre>"; print_r($data); die(); 
        if(!empty($data))
        {
            // #2 VALIDASI MANDATORY_CHECK_IO_SAP IT@140819
            $sql2 = " SELECT a.KODE_SAP_TUJUAN AS KODE_ASSET_SAP, b.MANDATORY_KODE_ASSET_CONTROLLER, b.MANDATORY_CHECK_IO_SAP  FROM TR_MUTASI_ASSET_DETAIL a 
            LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET_TUJUAN = b.JENIS_ASSET_CODE 
                LEFT JOIN TM_MSTR_ASSET c ON c.KODE_ASSET_AMS = a.KODE_ASSET_AMS AND c.GROUP = b.GROUP_CODE AND c.SUB_GROUP = b.SUBGROUP_CODE
            WHERE a.NO_REG = '{$noreg}' AND (a.KODE_ASSET_CONTROLLER is null OR a.KODE_ASSET_CONTROLLER = '' ) AND (a.DELETED is null OR a.DELETED = '')  AND (b.MANDATORY_CHECK_IO_SAP is not null AND b.MANDATORY_CHECK_IO_SAP != '') ";
            $data2 = DB::SELECT($sql2); //echo "2<pre>"; print_r($data2); die(); 

            if( $po_type == 1 || $po_type == 2 )
            {   
                // AMP & LAIN
                $kode_asset = "KODE_ASSET_AMS";
                
                if( $po_type == 1 )
                {
                    $kode_asset_label = "KODE ASSET FAMS";
                }
                else
                {
                    $kode_asset_label = "KODE ASSET FAMS / SAP";    
                }
                   
            }
            else
            {
                // SAP
                $kode_asset = "KODE_ASSET_SAP";
                $kode_asset_label = "KODE ASSET SAP"; 
            }

            if(!empty($data2))
            {
                if( $ka_sap == '' )
                {
                    $result = array('status'=>false,'message'=> ''.$kode_asset_label.' kosong! ');
                    return $result;  
                }

                $service = API::exec(array(
                    'request' => 'GET',
                    'host' => 'ldap',
                    'method' => "check_io?AUFNR=$ka_con&AUFUSER3=$ka_sap", 
                ));
                
                $data = $service;
                //$data = 1;
                
                //echo "<pre>"; print_r($data); die();

                if( $data->TYPE == 'S' )
                //if($data==1)
                {

                    DB::beginTransaction();
                    try 
                    {   
                        $sql = " UPDATE TR_MUTASI_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND $kode_asset = '{$ka_sap}' ";
                        //echo $sql; die();
                        DB::UPDATE($sql);
                        DB::commit();

                        $result = array('status'=>true,'message'=> "SUKSES UPDATE KODE ASET");
                    }
                    catch (\Exception $e) 
                    {
                        DB::rollback();
                        $result = array('status'=>false,'message'=>$e->getMessage());
                    }
                }
                else
                {
                    
                    $result = array('status'=>false,'message'=> $data->MESSAGE.' (Kode Aset Controller:'.$ka_con.')');
                }
                return $result;
            }
            else
            {
                if( $ka_sap == '' )
                {
                    $result = array('status'=>false,'message'=> ''.$kode_asset_label.' required! ');
                    return $result;  
                }

                // SKIP VALIDASI CHECK IO SAP JIKA ASSET LAINNYA
                DB::beginTransaction();
                try 
                {   
                    $sql = " UPDATE TR_MUTASI_ASSET_DETAIL SET KODE_ASSET_CONTROLLER = '{$ka_con}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND $kode_asset = '{$ka_sap}' ";
                    //echo $sql; die();
                    DB::UPDATE($sql);
                    DB::commit();

                    $result = array('status'=>true,'message'=> "Updated Success");
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>false,'message'=>$e->getMessage());
                }
                return $result;   
            }
            // #2 END VALIDASI MANDATORY_CHECK_IO_SAP IT@140819
        }
        else
        {
            $result = array('status'=>false,'message'=> 'Tidak perlu menginput Kode Asset Controller / IO');
            return $result;   
        }
        //#1 END VALIDASI MAPPING INPUT KODE ASSET / IO
    }

    function save_gi_number_year(Request $request)
    {
        $po_type = $request->po_type;
        $noreg = $request->getnoreg;
        $gi_number = $request->md_number;
        $gi_year = $request->md_year;
        $ka_sap = $request->ka_sap;
        $user_id = Session::get('user_id');

        $sql = " SELECT COUNT(*) AS TOTAL FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND KODE_ASSET_SAP = {$ka_sap} AND (GI_NUMBER IS NOT NULL OR GI_NUMBER != '') AND (GI_YEAR IS NOT NULL OR GI_YEAR != '') ";
        $jml = DB::SELECT($sql);
        //echo "2<pre>"; print_r($jml);die();
        if($jml[0]->TOTAL == 0)
        {
            
            $service = API::exec(array(
                'request' => 'GET',
                'host' => 'ldap',
                'method' => "check_gi?MBLNR=".$gi_number."&MJAHR=".$gi_year."&ANLN1=".$ka_sap."&ANLN2=0", 
            ));
            
            $data = $service;

            //echo "1<pre>"; print_r($data); die();
            //$data = 1;

            if( $data->TYPE == 'S' )
            //if($data==1)
            {
                
                DB::beginTransaction();
                try 
                {   
                    $sql = " UPDATE TR_REG_ASSET_DETAIL SET GI_NUMBER = '{$gi_number}', GI_YEAR = '{$gi_year}', UPDATED_AT = current_timestamp(), UPDATED_BY = '{$user_id}' WHERE NO_REG = '{$noreg}' AND KODE_ASSET_SAP = '{$ka_sap}' ";
                    //echo $sql; die();
                    DB::UPDATE($sql);
                    DB::commit();

                    $result = array('status'=>true,'message'=> "Data GI is successfully updated");
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>false,'message'=>$e->getMessage());
                }
                
                //$result = array('status'=>'success','message'=> "Validation Success");
            }
            else
            {
                $result = array('status'=>false,'message'=> $data->MESSAGE.' (GI Number:'.$gi_number.' & Year : '.$gi_year.' )');
            }
            return $result;
        }
        else
        {
            $result = array('status'=>true,'message'=> "Data GI sudah di validasi");
            return $result;
        }
    }

    public function print_io($noreg,$asset_po_id,$jenis_kendaraan,$no_reg_item)
    {
        //$data = $this->get_data_print_io($noreg,$asset_po_id); 
        //echo "3<pre>"; print_r($data[0]['nama_asset']); die();

        $no_document = $noreg;
        $no_document = str_replace("-", "/", $no_document);
        $namafile = str_replace(".", "_", $noreg);

        $html2pdf = new Html2Pdf('P', 'A4', 'en');
        $html2pdf->writeHTML(view('approval.print_io', [
            'no_document' => $no_document,
            'data' => $this->get_data_print_io($noreg,$asset_po_id,$no_reg_item),
            'name' => 'Triputra Agro Persada',
            'jenis_kendaraan' => $jenis_kendaraan
        ]));

        $pdf = $html2pdf->output("", "S");
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Length', strlen($pdf))
            ->header('Content-Disposition', 'inline; filename="Pengajuan_Print_IO_'.$namafile.'.pdf"');
    }

    function get_data_print_io($noreg,$id,$no_reg_item)
    {
        $kondisi = array(
            'B' => 'Baik',
            'BP' => 'Butuh Perbaikan',
            'TB' => 'Tidak Baik'
        );

        $noreg = str_replace("-", "/", $noreg);

        $records = array();
        $sql = " SELECT a.*, b.jenis_asset_description AS JENIS_ASSET_NAME, c.group_description AS GROUP_NAME, d.subgroup_description AS SUB_GROUP_NAME, e.KODE_VENDOR, e.NAMA_VENDOR, e.BUSINESS_AREA AS BUSINESS_AREA, e.PO_TYPE AS PO_TYPE
                    FROM TR_REG_ASSET_DETAIL a 
                        LEFT JOIN TM_JENIS_ASSET b ON a.jenis_asset = b.jenis_asset_code 
                        LEFT JOIN TM_GROUP_ASSET c ON a.group = c.group_code AND a.jenis_asset = c.jenis_asset_code
                        LEFT JOIN TM_SUBGROUP_ASSET d ON a.sub_group = d.subgroup_code AND a.group = d.group_code
                        LEFT JOIN TR_REG_ASSET e ON a.NO_REG = e.NO_REG
                    WHERE a.no_reg = '{$noreg}' AND a.no_reg_item = '{$no_reg_item}' AND a.asset_po_id = '{$id}' AND (a.DELETED is null OR a.DELETED = '')
                        ORDER BY a.no_reg_item ";
        
        $data = DB::SELECT($sql);

        if($data)
        {
            foreach( $data as $k => $v )
            {
                $rolename = Session::get('role');
                if( $rolename == 'AMS' )
                {
                    $kondisi_asset = trim($v->JENIS_ASSET);
                    $group = trim($v->GROUP);
                    $subgroup = trim($v->SUB_GROUP);
                }
                else
                {
                    $kondisi_asset = trim($v->JENIS_ASSET).'-'.trim($v->JENIS_ASSET_NAME);
                    $group = trim($v->GROUP).'-'.trim($v->GROUP_NAME);
                    $subgroup = trim($v->SUB_GROUP).'-'.trim($v->SUB_GROUP_NAME);
                }

                $records[] = array
                (
                    'id' => trim($v->ID),
                    'no_po' => trim($v->NO_PO),
                    'asset_po_id' => trim($v->ASSET_PO_ID),
                    'tgl_po' => trim($v->CREATED_AT),
                    'kondisi_asset' => trim(@$kondisi[$v->KONDISI_ASSET]),
                    'jenis_asset' => trim($v->JENIS_ASSET).'-'.trim($v->JENIS_ASSET_NAME),
                    //'jenis_asset' => $kondisi_asset,
                    'group' => trim($v->GROUP).'-'.trim($v->GROUP_NAME),
                    //'group' => $group,
                    'sub_group' => trim($v->SUB_GROUP).'-'.trim($v->SUB_GROUP_NAME),
                    //'sub_group' => $subgroup,
                    'nama_asset' => trim($v->NAMA_ASSET),
                    'merk' => trim($v->MERK),
                    'spesifikasi_or_warna' => trim($v->SPESIFIKASI_OR_WARNA),
                    'no_rangka_or_no_seri' => trim($v->NO_RANGKA_OR_NO_SERI),
                    'no_mesin_or_imei' => trim($v->NO_MESIN_OR_IMEI),
                    'no_polisi' => trim($v->NO_POLISI),
                    'lokasi_ba_code' => trim($v->LOKASI_BA_CODE),
                    'lokasi' => trim($v->LOKASI_BA_DESCRIPTION),
                    'tahun' => trim($v->TAHUN_ASSET),
                    'nama_penanggung_jawab_asset' => trim($v->NAMA_PENANGGUNG_JAWAB_ASSET),
                    'jabatan_penanggung_jawab_asset' => trim($v->JABATAN_PENANGGUNG_JAWAB_ASSET),
                    'info' => trim($v->INFORMASI),
                    'file' => $this->get_asset_file($v->ID,$noreg),
                    'nama_asset_1' => trim($v->NAMA_ASSET_1),
                    'nama_asset_2' => trim($v->NAMA_ASSET_2),
                    'nama_asset_3' => trim($v->NAMA_ASSET_3),
                    'quantity_asset_sap' => trim($v->QUANTITY_ASSET_SAP),
                    'uom_asset_sap' => trim($v->UOM_ASSET_SAP),
                    'capitalized_on' => trim($v->CAPITALIZED_ON),
                    'deactivation_on' => trim($v->DEACTIVATION_ON),
                    'cost_center' => trim($v->COST_CENTER),
                    'book_deprec_01' => trim($v->BOOK_DEPREC_01),
                    'fiscal_deprec_15' => trim($v->FISCAL_DEPREC_15),
                    'group_deprec_30' => trim($v->GROUP_DEPREC_30),
                    'no_reg_item' => trim($v->NO_REG_ITEM),
                    'vendor' => trim($v->KODE_VENDOR).'-'.trim($v->NAMA_VENDOR),
                    'business_area' => trim($v->BUSINESS_AREA),
                    'kode_asset_sap' => trim($v->KODE_ASSET_SAP),
                    'kode_asset_controller' => trim($v->KODE_ASSET_CONTROLLER),
                    'kode_asset_ams' => trim($v->KODE_ASSET_AMS),
                    'po_type' => trim($v->PO_TYPE),
                    'gi_number' => trim($v->GI_NUMBER),
                    'gi_year' => trim($v->GI_YEAR),
                    'total_asset' => $this->get_validasi_delete_asset($noreg),
                    'nama_material' => trim($v->NAMA_MATERIAL)
                );
            }
        }

        return $records;
    }

    function list_kac_required($noreg)
    {
        $dt = "";

        $sql = " SELECT a.NO_REG_ITEM, a.NAMA_MATERIAL, c.SUBGROUP_DESCRIPTION 
FROM TR_REG_ASSET_DETAIL a 
LEFT JOIN TM_ASSET_CONTROLLER_MAP b ON a.JENIS_ASSET = b.JENIS_ASSET_CODE AND a.`GROUP` = b.GROUP_CODE AND a.SUB_GROUP = b.SUBGROUP_CODE  
LEFT JOIN TM_SUBGROUP_ASSET c ON a.JENIS_ASSET = c.JENIS_ASSET_CODE AND a.`GROUP` = c.GROUP_CODE AND a.SUB_GROUP = c.SUBGROUP_CODE 
WHERE a.no_reg = '".$noreg."' AND b.MANDATORY_KODE_ASSET_CONTROLLER = 'X' ORDER BY a.NO_REG_ITEM ";
        
        $data = DB::SELECT($sql);
        //echo "4<pre>"; print_r($data); die(); 

        if( !empty($data) )
        {
            $no = 1;
            foreach($data as $k => $v)
            {
                $dt .= $v->NO_REG_ITEM.'. '.$v->SUBGROUP_DESCRIPTION.' ('.$v->NAMA_MATERIAL.')<br/>';  
                $no++;
            }
        }
        else
        {
            $dt = "";
        }

        return $dt;
    }

    function update_status_disposal(Request $request, $status, $noreg)
    {
        $req = $request->all();

        $no_registrasi = str_replace("-", "/", $noreg);
        $note = $request->parNote;
        $role_id = Session::get('role_id');
        $role_name = Session::get('role'); //get role id user
        $asset_controller = $this->get_ac($no_registrasi); //get asset controller 
    
        $validasi_last_approve = $this->get_validasi_last_approve($no_registrasi);
        // $last_email_approve = $this->get_last_email_approve($no_registrasi);
        $user_id = Session::get('user_id');
        //echo "2<pre>"; print_r($validasi_last_approve); die();

        // if($last_email_approve <> 1){

        //     $cek_email = $this->get_email_next_approval($no_registrasi,$user_id);

        //     if($cek_email['email'] == ""){
        //         return response()->json(['status' => false, "message" => "Email User ". $cek_email['next_approve'] ."tidak tersedia"]);
        //     }
        // }

        if( $validasi_last_approve == 0 )
        {
            DB::beginTransaction();
            
            try 
            {
                if($status=='R')
                {
                    // SEMENTARA DI DELETE DULU JIKA DI REJECT IT@081019 
                    //DB::DELETE(" DELETE FROM TR_DISPOSAL_ASSET_DETAIL WHERE NO_REG = '".$no_registrasi."' ");
                    DB::UPDATE(" UPDATE TR_DISPOSAL_ASSET_DETAIL SET DELETED = 'R' WHERE NO_REG = '".$no_registrasi."' "); 
                }

                DB::STATEMENT('CALL update_approval("'.$no_registrasi.'", "'.$user_id.'","'.$status.'", "'.$note.'", "'.$role_name.'", "'.$asset_controller.'")');
                
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
            //$validasi_check_gi_amp = $this->get_validasi_check_gi_amp($request,$no_registrasi); //true;
            //echo "1<pre>"; print_r($validasi_check_gi_amp); die();
            $validasi_check_gi_amp['status'] = 'success';

            if($validasi_check_gi_amp['status'] == 'success')
            {
                DB::beginTransaction();
                try 
                {
                    DB::STATEMENT('CALL complete_document_disposal("'.$no_registrasi.'", "'.$user_id.'")');
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
                return response()->json(['status' => false, "message" => "Error Validasi GI"]);
            }
           
        }
    }
    function update_status_disposal_email()
    {
        $request = new \Illuminate\Http\Request();

		$request->replace(['id' => $_GET['id']]);
        $req = unserialize(urldecode($_GET['id']));

        
        $no_registrasi = str_replace("-", "/", $req['noreg']);
        $status = $req['status'];
        $note = '';
        $asset_controller = $this->get_ac($no_registrasi); //get asset controller 
    
        $validasi_last_approve = $this->get_validasi_last_approve($no_registrasi);
        //echo "2<pre>"; print_r($validasi_last_approve); die();

        if( $validasi_last_approve == 0 )
        {
            DB::beginTransaction();
            
            try 
            {
                if($status=='R')
                {
                    // SEMENTARA DI DELETE DULU JIKA DI REJECT IT@081019 
                    //DB::DELETE(" DELETE FROM TR_DISPOSAL_ASSET_DETAIL WHERE NO_REG = '".$no_registrasi."' ");
                    DB::UPDATE(" UPDATE TR_DISPOSAL_ASSET_DETAIL SET DELETED = 'R' WHERE NO_REG = '".$no_registrasi."' "); 
                }

                DB::STATEMENT('CALL update_approval("'.$no_registrasi.'", "'.$req['user_id'].'","'.$status.'", "'.$note.'", "'.$req['role_name'].'", "'.$asset_controller.'")');
                
                DB::commit();

                // return response()->json(['status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'update'), "new_noreg"=>$no_registrasi]);
                $data['message'] =  'Data is successfully updated' ;
                $data = serialize($data);
                return route('mail_response', [$data]);
            } 
            catch (\Exception $e) 
            {
                DB::rollback();
                // return response()->json(['status' => false, "message" => $e->getMessage()]);
                
                $data = array('message' => $e->getMessage());
                $data = serialize($data);
                return route('mail_response', [$data]);
            }
        }    
        else
        {
            //$validasi_check_gi_amp = $this->get_validasi_check_gi_amp($request,$no_registrasi); //true;
            //echo "1<pre>"; print_r($validasi_check_gi_amp); die();
            $validasi_check_gi_amp['status'] = 'success';

            if($validasi_check_gi_amp['status'] == 'success')
            {
                DB::beginTransaction();
                try 
                {
                    DB::STATEMENT('CALL complete_document_disposal("'.$no_registrasi.'", "'.$req['user_id'].'")');
                    DB::commit();
                    // return response()->json(['status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'update'), "new_noreg"=>$no_registrasi]);
                    $data['message'] =   'Data is successfully updated' ;                    
                    $data = serialize($data);
                    return route('mail_response', [$data]);
                } 
                catch (\Exception $e) 
                {
                    DB::rollback();
                    // return response()->json(['status' => false, "message" => $e->getMessage()]);
                    
                    $data = array('message' => $e->getMessage());
                    $data = serialize($data);
                    return route('mail_response', [$data]);
                }
            }
            else
            {
                // return response()->json(['status' => false, "message" => "Error Validasi GI"]);
                $data['message'] =   'Error Validasi GI' ;                
                $data = serialize($data);
                    return route('mail_response', [$data]);
            }
           
        }
    }

    function update_disposal_email()
    {
        $request = new \Illuminate\Http\Request();

		$request->replace(['id' => $_GET['id']]);
        $req = json_decode($_GET['id']);
        $status = $req->status;
        $note = $req->note;
        $no_registrasi = $req->noreg;
        $asset_controller = $this->get_ac($no_registrasi); //get asset controller 
    
        $validasi_last_approve = $this->get_validasi_last_approve($no_registrasi);
        //echo "2<pre>"; print_r($validasi_last_approve); die();

        if( $validasi_last_approve == 0 )
        {
            DB::beginTransaction();
            
            try 
            {
                if($status=='R')
                {
                    // SEMENTARA DI DELETE DULU JIKA DI REJECT IT@081019 
                    //DB::DELETE(" DELETE FROM TR_DISPOSAL_ASSET_DETAIL WHERE NO_REG = '".$no_registrasi."' ");
                    DB::UPDATE(" UPDATE TR_DISPOSAL_ASSET_DETAIL SET DELETED = 'R' WHERE NO_REG = '".$no_registrasi."' "); 
                }

                DB::STATEMENT('CALL update_approval("'.$no_registrasi.'", "'.$req->user_id.'","'.$status.'", "'.$note.'", "'.$req->role_name.'", "'.$asset_controller.'")');
                
                DB::commit();

                // return response()->json(['status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'update'), "new_noreg"=>$no_registrasi]);
                $data['message'] =  'Data is successfully updated' ;
                
                $data = serialize($data);
                    return route('mail_response', [$data]);
            } 
            catch (\Exception $e) 
            {
                DB::rollback();
                // return response()->json(['status' => false, "message" => $e->getMessage()]);
                
                $data = array('message' => $e->getMessage());
                $data = serialize($data);
                    return route('mail_response', [$data]);
            }
        }    
        else
        {
            //$validasi_check_gi_amp = $this->get_validasi_check_gi_amp($request,$no_registrasi); //true;
            //echo "1<pre>"; print_r($validasi_check_gi_amp); die();
            $validasi_check_gi_amp['status'] = 'success';

            if($validasi_check_gi_amp['status'] == 'success')
            {
                DB::beginTransaction();
                try 
                {
                    DB::STATEMENT('CALL complete_document_disposal("'.$no_registrasi.'", "'.$req->user_id.'")');
                    DB::commit();
                    // return response()->json(['status' => true, "message" => 'Data is successfully ' . ($no_registrasi ? 'updated' : 'update'), "new_noreg"=>$no_registrasi]);
                    $data['message'] =   'Data is successfully updated' ;
                    
                    $data = serialize($data);
                    return route('mail_response', [$data]);
                } 
                catch (\Exception $e) 
                {
                    DB::rollback();
                    // return response()->json(['status' => false, "message" => $e->getMessage()]);
                    
                    $data = array('message' => $e->getMessage());
                    $data = serialize($data);
                    return route('mail_response', [$data]);
                }
            }
            else
            {
                // return response()->json(['status' => false, "message" => "Error Validasi GI"]);
                $data['message'] =   'Error Validasi GI' ;
                
                $data = serialize($data);
                    return route('mail_response', [$data]);
            }
           
        }
    }

    function view_disposal($id)
    {
        //echo "<pre>"; print_r($id);die();

        $noreg = str_replace("-", "/", $id);

        $records = array();

        $sql = " SELECT a.*, date_format(a.tanggal_reg,'%d-%m-%Y') AS TANGGAL_REG, b.description_code AS CODE_AREA, b.description AS NAME_AREA, c.name AS REQUESTOR 
                    FROM TR_DISPOSAL_ASSET a 
                        LEFT JOIN TM_GENERAL_DATA b ON a.business_area = b.description_code AND b.general_code = 'plant'
                        LEFT JOIN TBM_USER c ON a.created_by = c.id 
                    WHERE a.no_reg = '$noreg' ";
        $data = DB::SELECT($sql);
        
        if($data)
        {
            $type_transaksi = array(
                1 => 'Barang',
                2 => 'Jasa',
                3 => 'Lain-lain',
            );

            $po_type = array(
                0 => 'SAP',
                1 => 'AMP',
                2 => 'Asset Lainnya'
            );

            foreach ($data as $k => $v) 
            {
                $records[] = array(
                    'no_reg' => trim($v->NO_REG),
                    'type_transaksi' => '',//trim($type_transaksi[$v->TYPE_TRANSAKSI]),
                    'po_type' => '', //trim($po_type[$v->PO_TYPE]),
                    'business_area' => trim($v->CODE_AREA).' - '.trim($v->NAME_AREA),
                    'requestor' => trim($v->REQUESTOR),
                    'tanggal_reg' => trim($v->TANGGAL_REG),
                    'item_detail' => $this->get_item_detail_disposal($noreg),
                    'sync_sap' => '',//$this->get_sinkronisasi_sap($noreg),
                    'sync_amp' => '',//$this->get_sinkronisasi_amp($noreg),
                    'sync_lain' => '',//$this->get_sinkronisasi_lain($noreg),
                    'cek_reject' => $this->get_cek_reject($noreg),
                    'vendor' => '', //trim($v->KODE_VENDOR).' - '.trim($v->NAMA_VENDOR),
                    'kode_vendor' => '', //trim($v->KODE_VENDOR),
                    'nama_vendor' => '', //trim($v->NAMA_VENDOR),
                );

            }
        }
        else
        {
            $records[0] = array();
        }

        echo json_encode($records[0]);
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

    function get_item_detail_disposal($noreg)
    {
        $request = array();

        // $row = TM_MSTR_ASSET::where('NO_REG','LIKE','%'.$noreg.'%')->get()->all();
        $row = DB::table('TR_DISPOSAL_ASSET_DETAIL')
                     ->where('NO_REG','LIKE','%'.$noreg.'%')
                     ->get();
        $NILAI_BUKU = $this->get_nilai_buku($row);
        Debugbar::info($row);
        Debugbar::info($NILAI_BUKU);
        
        $sql = " SELECT b.ASSET_PO_ID as ASSET_PO_ID,b.NO_REG as DOCUMENT_CODE, a.* FROM TR_DISPOSAL_ASSET_DETAIL a LEFT JOIN TR_REG_ASSET_DETAIL b ON a.kode_asset_ams = b.KODE_ASSET_AMS WHERE a.no_reg = '{$noreg}' ";

        /*$sql1 = " SELECT b.ASSET_PO_ID as ASSET_PO_ID,b.NO_REG as DOCUMENT_CODE, a.* FROM TR_DISPOSAL_ASSET_DETAIL a LEFT JOIN TR_REG_ASSET_DETAIL b ON a.kode_asset_ams = b.KODE_ASSET_AMS WHERE a.no_reg = '{$noreg}' AND (a.DELETED is null OR a.DELETED = '') ";*/

        $data = DB::SELECT($sql);

        if($data)
        {
            foreach( $data as $k => $v )
            {
                $request[] = array
                (
                    'asset_po_id' => trim($v->ASSET_PO_ID),
                    'document_code' => trim($v->DOCUMENT_CODE),
                    'id' => trim($v->ID),
                    'no_reg' => trim($v->NO_REG),
                    'kode_asset_ams' => trim($v->KODE_ASSET_AMS),
                    'kode_asset_sap' => trim($v->KODE_ASSET_SAP),
                    'nama_material' => trim($v->NAMA_MATERIAL),
                    'ba_pemilik_asset' => trim($v->BA_PEMILIK_ASSET),
                    'lokasi_ba_code' => trim($v->LOKASI_BA_CODE),
                    'lokasi_ba_description' => trim($v->LOKASI_BA_DESCRIPTION),
                    'nama_asset_1' => trim($v->NAMA_ASSET_1),
                    'harga_perolehan' => number_format(trim($v->HARGA_PEROLEHAN),0,',','.'),
                    'nilai_buku' => number_format(trim($NILAI_BUKU[$k]),0,',','.'),
                    'jenis_pengajuan' => trim($v->JENIS_PENGAJUAN),
                    'created_by' => trim($v->CREATED_BY),
                    'created_at' => trim($v->CREATED_AT)
                );
                Debugbar::info($NILAI_BUKU[$k]);
            }
        }

        return $request;
    }

    function delete_asset_disposal(Request $request)
    {
        $no_reg = str_replace("-", "/", $request->getnoreg);

        DB::beginTransaction();

        try 
        {
            $user_id = Session::get('user_id');

            DB::DELETE(" DELETE FROM TR_DISPOSAL_ASSET_DETAIL WHERE NO_REG = '".$no_reg."' AND KODE_ASSET_AMS = ".$request->kode_asset_ams." "); 

            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->kode_asset_ams ? 'deleted' : 'delete')]);
        } 
        catch (\Exception $e) 
        {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    function get_ac($noreg)
    {
        $sql = "SELECT b.ASSET_CONTROLLER
                    FROM TR_DISPOSAL_ASSET_DETAIL a 
                    LEFT JOIN TM_MSTR_ASSET b on a.KODE_ASSET_AMS = b.KODE_ASSET_AMS
                    WHERE a.NO_REG = '".$noreg."' LIMIT 1";

        $data = DB::SELECT($sql);

        if(!empty($data))
        {   
            $ac = $data[0]->ASSET_CONTROLLER;
        }
        else
        {
            $ac = "";
        }

        return $ac;
    }
    function get_email_next_approval($noreg,$user_id)
    {
        $sql = " SELECT * FROM (
            SELECT @prev_col_a as prev_name, @user_id as prev_user_id,b.name as next_approve, b.email, b.role_id,
               @prev_col_a := a.name AS col_a,
                 @user_id := user_id AS user_id
            FROM v_history_approval a LEFT JOIN TBM_USER b ON a.USER_ID = b.ID LEFT JOIN TBM_ROLE c ON b.role_id = c.id,
            (SELECT @prev_col_a := NULL, @user_id := 0) prv
            WHERE a.document_code = '".$noreg."' 
            ORDER BY -a.date ASC, a.date ASC) approver
            WHERE approver.prev_user_id = '".$user_id."' ";

        $data = DB::SELECT($sql);

        if(!empty($data))
        {   
            $dt = array( 'email' => $data[0]->email,'next_approve' => $data[0]->next_approve);
        }
        else
        {
            $dt = array( 'email' => "" ,'next_approve' => "");
        }

        return $dt;
    }

    function cek_kas_ac($noreg)
    {
        $sql = " SELECT NO_REG_ITEM, KODE_MATERIAL, NAMA_MATERIAL, NO_PO, NAMA_ASSET, INFORMASI FROM TR_REG_ASSET_DETAIL WHERE NO_REG = '{$noreg}' AND (KODE_ASSET_SAP IS NULL OR KODE_ASSET_SAP = '') ";

        $data = DB::SELECT($sql);
        $total = count($data);
        //echo "1<br/>".$total; die();

        $msg = "";

        if(!empty($data))
        {
            foreach($data as $k => $v)
            {
                $msg .= " KODE MATERIAL : {$v->KODE_MATERIAL} / NO REG ITEM : {$v->NO_REG_ITEM} / NAMA ASSET : {$v->NAMA_ASSET} <br/> ";
            }
            
            $result = array('status'=>false,'message'=>$msg);
        }
        else
        {
            $result = array('status'=>true,'message'=>"all synchronize sap success");
        }

        return $result;
    }

    function berkas_disposal($noreg)
    {
        $noreg = base64_decode($noreg); 

        $sql = " SELECT b.KODE_ASSET_AMS, b.DOC_SIZE, b.FILE_NAME, b.FILE_CATEGORY, b.FILE_UPLOAD, b.JENIS_FILE FROM TR_DISPOSAL_ASSET_FILE b WHERE b.NO_REG = '".$noreg."' "; 
        $data = DB::SELECT($sql);
        
        $l = "";
        if(!empty($data))
        {
            $l .= '<center>';
            $l .= '<h1>'.$noreg.'</h1>';

            foreach($data as $k => $v)
            {
                $file_category = str_replace("_", " ", $v->FILE_CATEGORY);

                if( $v->JENIS_FILE == 'image/jpeg' || $v->JENIS_FILE == 'image/png' )
                {
                    $l .= '<div class="caption"><h1><u>'.$v->KODE_ASSET_AMS.'</u></h1><h3>'.strtoupper($file_category).'<br/><img src="data:image/jpeg;base64,'.$v->FILE_UPLOAD.'"/><br/>'. $v->FILE_NAME. '</h3></div>';
                }
                else if($v->JENIS_FILE == 'application/pdf')
                {
                    $l .= '<h1><u>'.$v->KODE_ASSET_AMS.'</u></h1>'.strtoupper($file_category).'<br/><object data="data:application/pdf;base64,'.$v->FILE_UPLOAD.'" type="'.$v->JENIS_FILE.'" style="height:100%;width:100%"></object><br/>'. $v->FILE_NAME. '';
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
            $l .= "FILE NOT FOUND";
        }

        $l .= '</center>';
        echo $l; 
    }

    function view_mutasi($id)
    {
        //echo "<pre>"; print_r($id);die();
        $noreg = str_replace("-", "/", $id);

        $records = array();
        $sql = " SELECT a.ID,a.NO_REG,a.TYPE_TRANSAKSI,b.JENIS_PENGAJUAN,a.CREATED_BY,a.CREATED_AT,a.UPDATED_BY,a.UPDATED_AT,d.DESCRIPTION AS COST_CENTER,group_concat(b.KODE_ASSET_AMS) as KODE_ASSET_AMS, date_format(a.created_at,'%d-%m-%Y') AS TANGGAL_REG, c.name AS REQUESTOR, b.TUJUAN AS BA_TUJUAN,
        (SELECT BA_PEMILIK_ASSET FROM TM_MSTR_ASSET WHERE KODE_ASSET_AMS = (
       SELECT KODE_ASSET_AMS FROM TR_MUTASI_ASSET_DETAIL a WHERE NO_REG = '$noreg' LIMIT 1)) AS BA_PEMILIK_ASSET 
                           FROM TR_MUTASI_ASSET a   
                                         LEFT JOIN TR_MUTASI_ASSET_DETAIL b ON a.NO_REG = b.NO_REG  
                                         LEFT JOIN TM_GENERAL_DATA d ON d.DESCRIPTION_CODE = b.TUJUAN  and d.GENERAL_CODE = 'ba_mutasi_tujuan_costcenter' 
                               LEFT JOIN TBM_USER c ON a.created_by = c.id 
                           WHERE a.no_reg = '$noreg' group by a.NO_REG";
        $data = DB::SELECT($sql);
        
        // $data_sap = $this->get_master_asset_by_id($noreg);
        
        if($data)
        {
            $type_transaksi = array(
                1 => 'AMP',
                2 => 'NON AMP',
            );

            $po_type = array(
                0 => 'SAP',
                1 => 'AMP',
                2 => 'Asset Lainnya'
            );

            foreach ($data as $k => $v) 
            {
                $records[] = array(
                    'no_reg' => trim($v->NO_REG),
                    'type_transaksi' => trim($type_transaksi[$v->TYPE_TRANSAKSI]),
                    //'po_type' => '', //trim($po_type[$v->PO_TYPE]),
                    'po_type' => trim($v->JENIS_PENGAJUAN),
                    'ba_pemilik_asset' => trim($v->BA_PEMILIK_ASSET),
                    'requestor' => trim($v->REQUESTOR),
                    'tanggal_reg' => trim($v->TANGGAL_REG),
                    'item_detail' => $this->get_item_detail_mutasi($noreg),
                    'sync_sap' => $this->get_sinkronisasi_sap_mutasi($noreg),
                    'sync_amp' => $this->get_sinkronisasi_amp($noreg),
                    'sync_lain' => '',//$this->get_sinkronisasi_lain($noreg),
                    'cek_reject' => $this->get_cek_reject($noreg),
                    'vendor' => '', //trim($v->KODE_VENDOR).' - '.trim($v->NAMA_VENDOR),
                    'kode_vendor' => '', //trim($v->KODE_VENDOR),
                    'nama_vendor' => '', //trim($v->NAMA_VENDOR),
                    'cost_center' => $v->COST_CENTER, 
                    'kode_asset_ams' => $v->KODE_ASSET_AMS, 
                    'ba_tujuan' => $v->BA_TUJUAN, 
                    'new_asset' => $this->get_new_asset($noreg),
                );

            }
        }
        else
        {
            $records[0] = array();
        }

        echo json_encode($records[0]);
    }

    function get_item_detail_mutasi($noreg)
    {
        $request = array();
        
        $sql = " SELECT b.*, b.NO_REG as DOCUMENT_CODE, a.* , c.DESCRIPTION as KODE_ASSET_CLASS FROM TR_MUTASI_ASSET_DETAIL a LEFT JOIN TM_MSTR_ASSET b ON a.kode_asset_ams = b.KODE_ASSET_AMS LEFT JOIN TM_GENERAL_DATA c ON c.GENERAL_CODE= 'kodefikasi_asset_class' AND c.DESCRIPTION_CODE = SUBSTR(a.TUJUAN, 3, 1) WHERE a.no_reg = '{$noreg}' ";

        $data = DB::SELECT($sql);

        if($data)
        {
            foreach( $data as $k => $v )
            {
                $request[] = array
                (
                    'asset_po_id' => trim($v->ITEM_PO),
                    'document_code' => trim($v->DOCUMENT_CODE),
                    'id' => trim($v->ID),
                    'no_reg' => trim($v->NO_REG),
                    'kode_asset_ams' => trim($v->KODE_ASSET_AMS),
                    'kode_asset_sap' => trim($v->KODE_ASSET_SAP),
                    'nama_material' => trim($v->NAMA_MATERIAL),
                    'ba_pemilik_asset' => trim($v->BA_PEMILIK_ASSET),
                    'lokasi_ba_code' => trim($v->LOKASI_BA_CODE),
                    'lokasi_ba_description' => trim($v->LOKASI_BA_DESCRIPTION),
                    'nama_asset_1' => trim($v->NAMA_ASSET_1),
                    'harga_perolehan' => '',//number_format(trim($v->HARGA_PEROLEHAN),0,',','.'),
                    'jenis_pengajuan' => trim($v->JENIS_PENGAJUAN),
                    'tujuan' => trim($v->TUJUAN),
                    'created_by' => trim($v->CREATED_BY),
                    'created_at' => trim($v->CREATED_AT),
                    'penanggung_jawab' => trim($v->PENANGGUNG_JAWAB),
                    'jabatan' => trim($v->JABATAN),
                    'kode_asset_ams_tujuan' => trim($v->KODE_ASSET_AMS_TUJUAN),
                    'kode_sap_tujuan' => trim($v->KODE_SAP_TUJUAN),
                    'jenis_asset' => trim($v->JENIS_ASSET),
                    'jenis_asset_tujuan' => trim($v->JENIS_ASSET_TUJUAN),
                    'kode_asset_class' => trim($v->KODE_ASSET_CLASS),
                    'group' => trim($v->GROUP),
                    'sub_group' => trim($v->SUB_GROUP),
                    'asset_controller' => trim($v->ASSET_CONTROLLER),
                    'kode_asset_controller' => trim($v->KODE_ASSET_CONTROLLER),
                    'no_reg_item' => trim($v->NO_REG_ITEM),
                );
            }
        }

        return $request;
    }

    function update_status_mutasi(Request $request, $status, $noreg)
    {
        $req = $request->all();
        
        $no_registrasi = str_replace("-", "/", $noreg);
        $note = $request->parNote;
        $role_id = Session::get('role_id');
        $role_name = Session::get('role'); //get role id user
        $asset_controller = $this->get_ac_mutasi($no_registrasi); //get asset controller 

        
        $user_id = Session::get('user_id');
        // $last_email_approve = $this->get_last_email_approve($no_registrasi);
        // //echo "2<pre>"; print_r($validasi_last_approve); die();

        // if($last_email_approve <> 1){

        //     $cek_email = $this->get_email_next_approval($no_registrasi,$user_id);

        //     if($cek_email['email'] == ""){
        //         return response()->json(['status' => false, "message" => "Email User ". $cek_email['next_approve'] ."tidak tersedia"]);
        //     }
        // }

    
        $validasi_last_approve = $this->get_validasi_last_approve($no_registrasi);

        if( $validasi_last_approve == 0 )
        {
            DB::beginTransaction();
            
            try 
            {
                if($status=='R')
                {
                    DB::UPDATE(" UPDATE TR_MUTASI_ASSET_DETAIL SET DELETED = 'R' WHERE NO_REG = '".$no_registrasi."' "); 
                }

                DB::STATEMENT('CALL update_approval("'.$no_registrasi.'", "'.$user_id.'","'.$status.'", "'.$note.'", "'.$role_name.'", "'.$asset_controller.'")');
                
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
            //$validasi_check_gi_amp = $this->get_validasi_check_gi_amp($request,$no_registrasi); //true;
            //echo "1<pre>"; print_r($validasi_check_gi_amp); die();
            $validasi_check_gi_amp['status'] = 'success';

            if($validasi_check_gi_amp['status'] == 'success')
            {
                DB::beginTransaction();
                try 
                {
                    // DB::STATEMENT('CALL complete_document_disposal("'.$no_registrasi.'", "'.$user_id.'")');
                    DB::STATEMENT('CALL complete_document_mutasi("'.$no_registrasi.'", "'.$user_id.'")');
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
                return response()->json(['status' => false, "message" => "Error Validasi GI"]);
            }
           
        }
    }

    function get_ac_mutasi($noreg)
    {
        $sql = "SELECT b.ASSET_CONTROLLER
                    FROM TR_MUTASI_ASSET_DETAIL a 
                    LEFT JOIN TM_MSTR_ASSET b on a.KODE_ASSET_AMS = b.KODE_ASSET_AMS
                    WHERE a.NO_REG = '".$noreg."' LIMIT 1";

        $data = DB::SELECT($sql);

        if(!empty($data))
        {   
            $ac = $data[0]->ASSET_CONTROLLER;
        }
        else
        {
            $ac = "";
        }

        return $ac;
    }

    function delete_asset_mutasi(Request $request)
    {
        $no_reg = str_replace("-", "/", $request->getnoreg);

        DB::beginTransaction();

        try 
        {
            $user_id = Session::get('user_id');

            DB::DELETE(" DELETE FROM TR_MUTASI_ASSET_DETAIL WHERE NO_REG = '".$no_reg."' AND KODE_ASSET_AMS = ".$request->kode_asset_ams." ");
            DB::DELETE(" DELETE FROM TR_MUTASI_ASSET_FILE WHERE NO_REG = '".$no_reg."' AND KODE_ASSET_AMS = ".$request->kode_asset_ams." "); 

            DB::commit();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->kode_asset_ams ? 'deleted' : 'delete')]);
        } 
        catch (\Exception $e) 
        {
            DB::rollback();
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    function berkas_mutasi($noreg)
    {
        $noreg = base64_decode($noreg); 

        $sql = " SELECT b.KODE_ASSET_AMS, b.DOC_SIZE, b.FILE_NAME, b.FILE_CATEGORY, b.FILE_UPLOAD, b.JENIS_FILE FROM TR_MUTASI_ASSET_FILE b WHERE b.NO_REG = '".$noreg."' "; 
        $data = DB::SELECT($sql);
        
        $l = "";
        if(!empty($data))
        {
            $l .= '<center>';
            $l .= '<h1>'.$noreg.'</h1>';

            foreach($data as $k => $v)
            {
                $file_category = str_replace("_", " ", $v->FILE_CATEGORY);

                if( $v->JENIS_FILE == 'image/jpeg' || $v->JENIS_FILE == 'image/png' )
                {
                    $l .= '<div class="caption"><h1><u>'.$v->KODE_ASSET_AMS.'</u></h1><h3>'.strtoupper($file_category).'<br/><img src="data:image/jpeg;base64,'.$v->FILE_UPLOAD.'"/><br/>'. $v->FILE_NAME. '</h3><hr style="border:1px"/></div>';
                }
                else if($v->JENIS_FILE == 'application/pdf')
                {
                    $l .= '<h1><u>'.$v->KODE_ASSET_AMS.'</u></h1>'.strtoupper($file_category).'<br/><object data="data:application/pdf;base64,'.$v->FILE_UPLOAD.'" type="'.$v->JENIS_FILE.'" style="height:100%;width:100%"></object><br/>'. $v->FILE_NAME. '<hr style="border:1px"/>';
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
            $l .= "FILE NOT FOUND";
        }

        $l .= '</center>';
        echo $l; 
    }

    function get_sinkronisasi_sap_mutasi($noreg)
    {
        $noreg = str_replace("-", "/", $noreg);
        $request = array();
        $datax = '';
        $sql = " SELECT a.KODE_ASSET_AMS_ASAL FROM v_kode_asset_sap_mutasi a WHERE a.NO_REG = '{$noreg}' ";
        $data = DB::SELECT($sql);

        if(!empty($data))
        {
            $material = '';//;

            if( $data[0]->KODE_ASSET_AMS_ASAL != '' )
            {
                $material = $data[0]->KODE_ASSET_AMS_ASAL;
            }

            $datax .= $material;
        }
        
        return $datax;
    }

    function synchronize_sap_mutasi(Request $request)
    {
        $no_reg = @$request->noreg;
        $kode_ams = @$request->kode_asset_ams;
        // dd($no_reg);

        $sql = " SELECT d.DESCRIPTION AS COST_CENTER_BARU,a.*, date_format(a.CREATED_AT,'%d.%m.%Y') AS CREATED_AT, date_format(a.UPDATED_AT,'%d.%m.%Y') AS UPDATED_AT, b.*, a.NO_REG AS NO_REG_MUTASI FROM TR_MUTASI_ASSET_DETAIL a LEFT JOIN TM_GENERAL_DATA d ON d.DESCRIPTION_CODE = a.TUJUAN and d.GENERAL_CODE = 'ba_mutasi_tujuan_costcenter' LEFT JOIN TM_MSTR_ASSET b ON a.KODE_ASSET_AMS = b.KODE_ASSET_AMS WHERE a.NO_REG = '{$no_reg}' AND (a.KODE_SAP_TUJUAN = '' OR a.KODE_SAP_TUJUAN is null) AND (a.DELETED is null OR a.DELETED = '') "; //echo $sql; die();

        $data = DB::SELECT($sql); 

        $params = array();

        if(!empty($data))
        {
            foreach( $data as $k => $v )
            {   
                $costcenter = $v->COST_CENTER_BARU;
                $this->update_costcenter($costcenter,$no_reg,$kode_ams);
                $proses = $this->synchronize_sap_process_mutasi($v);   
                
                if($proses['status']=='error')
                {
                    return response()->json(['status' => false, "message" => $proses['message']]);
                    die();
                }
            }

            return response()->json(['status' => true, "message" => "Synchronize Mutasi success"]);
        }
        else
        {
            $sql = " UPDATE TR_MUTASI_ASSET_DETAIL SET KODE_SAP_TUJUAN = '' WHERE NO_REG = '{$no_reg}' "; 
                DB::UPDATE($sql);
            return response()->json(['status' => false, "message" => "Synchronize Mutasi failed, data not found"]);
        }
    }

    public function synchronize_sap_process_mutasi($dt) 
    {

        $ANLA_BUKRS = substr($dt->TUJUAN,0,2);
        $ANLA_LIFNR = $this->get_kode_vendor($dt->NO_REG);

        $service = API::exec(array(
            'request' => 'GET',
            'host' => 'ldap',
            'method' => "create_asset?ANLA_ANLKL={$dt->JENIS_ASSET_TUJUAN}&ANLA_BUKRS={$ANLA_BUKRS}&RA02S_NASSETS=1&ANLA_TXT50={$dt->NAMA_ASSET_1}&ANLA_TXA50={$dt->NAMA_ASSET_2}&ANLH_ANLHTXT={$dt->NAMA_ASSET_3}&ANLA_SERNR={$dt->NO_RANGKA_OR_NO_SERI}&ANLA_INVNR={$dt->NO_MESIN_OR_IMEI}&ANLA_MENGE={$dt->QUANTITY_ASSET_SAP}&ANLA_MEINS={$dt->UOM_ASSET_SAP}&ANLA_AKTIV={$dt->CAPITALIZED_ON}&ANLA_DEAKT={$dt->DEACTIVATION_ON}&ANLZ_GSBER={$dt->TUJUAN}&ANLZ_KOSTL={$dt->COST_CENTER_BARU}&ANLZ_WERKS=$dt->TUJUAN&ANLA_LIFNR=&ANLB_NDJAR_01={$dt->BOOK_DEPREC_01}&ANLB_NDJAR_02={$dt->FISCAL_DEPREC_15}", 
            // 'method' => "create_asset?ANLA_ANLKL={$dt->JENIS_ASSET}&ANLA_BUKRS={$ANLA_BUKRS}&RA02S_NASSETS=1&ANLA_TXT50={$dt->NAMA_ASSET_1}&ANLA_TXA50={$dt->NAMA_ASSET_2}&ANLH_ANLHTXT={$dt->NAMA_ASSET_3}&ANLA_SERNR={$dt->NO_RANGKA_OR_NO_SERI}&ANLA_INVNR={$dt->NO_MESIN_OR_IMEI}&ANLA_MENGE={$dt->QUANTITY_ASSET_SAP}&ANLA_MEINS={$dt->UOM_ASSET_SAP}&ANLA_AKTIV={$dt->CAPITALIZED_ON}&ANLA_DEAKT={$dt->DEACTIVATION_ON}&ANLZ_GSBER={$dt->TUJUAN}&ANLZ_KOSTL={$dt->COST_CENTER_BARU}&ANLZ_WERKS=$dt->TUJUAN&ANLA_LIFNR=&ANLB_NDJAR_01={$dt->BOOK_DEPREC_01}&ANLB_NDJAR_02={$dt->FISCAL_DEPREC_15}", 
        ));
        
        $data = $service;
        // dd($data);
        // $data['dt'] = $dt;
        // $data['service'] = $service;
        // $result = array('status'=>'error','message'=> ''.$data);
        // return $data;      
        // die;
        //echo "1<pre>"; print_r($data); die();
        
        if( !empty($data->item->TYPE) )
        {
            #2
            if( $data->item->TYPE == 'S' )
            {
                $user_id = Session::get('user_id');

                DB::beginTransaction();
                try 
                {   
                    //1. ADD KODE_SAP_TUJUAN  TR_REG_ASSET 
                    $sql_1 = " UPDATE TR_MUTASI_ASSET_DETAIL SET KODE_SAP_TUJUAN = '".$data->item->MESSAGE_V1."', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE NO_REG = '{$dt->NO_REG_MUTASI}' AND KODE_ASSET_AMS = '{$dt->KODE_ASSET_AMS}'; ";
                    DB::UPDATE($sql_1);
                    //2. INSERT LOG
                    $create_date = date("Y-m-d H:i:s");
                    $sql_2 = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG_MUTASI}',0,'{$dt->NO_REG_ITEM}','".$data->item->TYPE."','".$data->item->ID."','".$data->item->NUMBER."','".$data->item->MESSAGE."','".$data->item->MESSAGE_V1."','".$data->item->MESSAGE_V2."','".$data->item->MESSAGE_V3."','".$data->item->MESSAGE_V4."','".$create_date."') ";
                    DB::INSERT($sql_2);

                    DB::commit();
                    //3. CREATE CODE ASSET AMS MUTASI
                    if( $dt->JENIS_PENGAJUAN == 1 )
                    {
                        $sql_3 = " CALL create_kode_asset_ams_mutasi('".$dt->NO_REG_MUTASI."', '".$ANLA_BUKRS."', '".$dt->JENIS_ASSET."', '".$data->item->MESSAGE_V1."') ";//echo $sql_3; die();
                    }
                    else
                    {
                        $sql_3 = " CALL create_kode_asset_ams_mutasi('".$dt->NO_REG_MUTASI."', '".$ANLA_BUKRS."', '".$dt->JENIS_ASSET."', '-".$data->item->MESSAGE_V1."') ";//echo $sql_3; die();
                    }
                    
                    DB::STATEMENT($sql_3);

                    return true;
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    return false;
                }
            }
            else 
            {
                DB::beginTransaction();

                try 
                {    
                    $create_date = date("Y-m-d H:i:s");
                    $sql = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG_MUTASI}','','{$dt->NO_REG_ITEM}','".$data->item->TYPE."','".$data->item->ID."','".$data->item->NUMBER."','".$data->item->MESSAGE."','".$data->item->MESSAGE_V1."','".$data->item->MESSAGE_V2."','".$data->item->MESSAGE_V3."','".$data->item->MESSAGE_V4."','".$create_date."') ";
                    
                    DB::INSERT($sql); 
                    DB::commit();
                    
                    $result = array('status'=>'error','message'=> ''.$data->item->MESSAGE.' (No Reg Item: '.$dt->NO_REG_ITEM.')');
                    return $result;                 
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>'error','message'=>$e->getMessage());
                    return $result;
                }
            }         
        }
        
        if( !empty($data->item[0]->TYPE) ) 
        {
            //RETURN ARRAY LEBIH DARI 1 ROW
            $result = array();
            $message = '';

            //echo "20<pre>"; count($data); die();

            foreach($data->item as $k => $v)
            {
                //echo "20<pre>"; print_r($v);
                
                if( $v->TYPE == 'S' && $v->ID == 'AA' && $v->NUMBER == 228 )
                {
                    $message .= $v->MESSAGE.',';
                    $result = array(
                        'TYPE' => 'S',
                        'ID' => $v->ID,
                        'NUMBER' => $v->NUMBER,
                        'MESSAGE' => $message,
                        'LOG_NO' => $v->LOG_NO,
                        'LOG_MSG_NO' => $v->LOG_MSG_NO,
                        'MESSAGE_V1' => $v->MESSAGE_V1,
                        'MESSAGE_V2' => $v->MESSAGE_V2,
                        'MESSAGE_V3' => $v->MESSAGE_V3,
                        'MESSAGE_V4' => $v->MESSAGE_V4
                    );
                }
                else
                {
                    $message .= $v->MESSAGE.',';
                    $result = array(
                        'TYPE' => 'E',
                        'ID' => $v->ID,
                        'NUMBER' => $v->NUMBER,
                        'MESSAGE' => $message,
                        'LOG_NO' => $v->LOG_NO,
                        'LOG_MSG_NO' => $v->LOG_MSG_NO,
                        'MESSAGE_V1' => $v->MESSAGE_V1,
                        'MESSAGE_V2' => $v->MESSAGE_V2,
                        'MESSAGE_V3' => $v->MESSAGE_V3,
                        'MESSAGE_V4' => $v->MESSAGE_V4
                    );
                }
                
            }
            //die();
            

            if( $result['TYPE'] == 'S' )
            {
                $user_id = Session::get('user_id');

                DB::beginTransaction();
                try 
                {   
                    //1. ADD KODE_ASSET_SAP & ASSET_CONTROLLER TR_REG_ASSET 
                    $sql_1 = " UPDATE TR_MUTASI_ASSET_DETAIL SET KODE_SAP_TUJUAN = '".$result['MESSAGE_V1']."', UPDATED_BY = '{$user_id}', UPDATED_AT = current_timestamp() WHERE NO_REG = '{$dt->NO_REG_MUTASI}' AND KODE_ASSET_AMS = '{$dt->KODE_ASSET_AMS}' ";
                    DB::UPDATE($sql_1);

                    //2. INSERT LOG
                    $create_date = date('Y-m-d H:i:s');
                    $sql_2 = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG_MUTASI}','','{$dt->NO_REG_ITEM}','".$result['TYPE']."','".$result['ID']."','".$result['NUMBER']."','".$result['MESSAGE']."','".$result['MESSAGE_V1']."','".$result['MESSAGE_V2']."','".$result['MESSAGE_V3']."','".$result['MESSAGE_V4']."','".$create_date."') ";
                    DB::INSERT($sql_2);

                    //3. CREATE CODE ASSET AMS MUTASI
                    if( $dt->JENIS_PENGAJUAN == 1 )
                    {
                        $sql_3 = " CALL create_kode_asset_ams_mutasi('".$dt->NO_REG_MUTASI."', '".$ANLA_BUKRS."', '".$dt->JENIS_ASSET."', '".$data->item->MESSAGE_V1."') ";
                    }
                    else
                    {
                        $sql_3 = " CALL create_kode_asset_ams_mutasi('".$dt->NO_REG_MUTASI."', '".$ANLA_BUKRS."', '".$dt->JENIS_ASSET."', '-".$data->item->MESSAGE_V1."') ";
                    }
                    
                    DB::STATEMENT($sql_3);
                    DB::commit();

                    return true;
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    return false;
                    //die();
                }
            }
            else 
            {
                DB::beginTransaction();

                try 
                {    
                    $create_date = date("Y-m-d H:i:s");
                    $sql = " INSERT INTO TR_LOG_SYNC_SAP(no_reg,asset_po_id,no_reg_item,msgtyp,msgid,msgnr,message,msgv1,msgv2,msgv3,msgv4,create_date)VALUES('{$dt->NO_REG_MUTASI}','','{$dt->NO_REG_ITEM}','".$result['TYPE']."','".$result['ID']."','".$result['NUMBER']."','".$result['MESSAGE']."','".$result['MESSAGE_V1']."','".$result['MESSAGE_V2']."','".$result['MESSAGE_V3']."','".$result['MESSAGE_V4']."','".$create_date."') ";
                    // Debugbar::info($create_date);
                    DB::INSERT($sql); 
                    DB::commit();
                    
                    $result = array('status'=>'error','message'=> ''.$result['MESSAGE'].' (No Reg Item: '.$dt->NO_REG_ITEM.')');
                    return $result;                 
                }
                catch (\Exception $e) 
                {
                    DB::rollback();
                    $result = array('status'=>'error','message'=>$e->getMessage());
                    return $result;
                }
            }              
        }
    }
}
