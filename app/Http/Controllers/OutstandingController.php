<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\TrUser;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;
use App\User;

class OutstandingController extends Controller
{
    public function index()
    {
        if (empty(Session::get('authenticated')))
            return redirect('/login');

       /*  if (AccessRight::granted() == false)
            return response(view('errors.403'), 403); */

       /*  $access = AccessRight::access(); */
       $data["page_title"] = "User";
        return view('usersetting.users')->with(compact('data'));
    }

    public function dataGrid(Request $request)
    {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";
        $field = array(
            array("index" => "0", "field" => "asset.NO_REG", "alias" => "no_reg"),
            array("index" => "1", "field" => "asset.TYPE_TRANSAKSI ", "alias" => "type"),
            array("index" => "2", "field" => "asset.PO_TYPE", "alias" => "po_type"),
            array("index" => "3", "field" => "asset.NO_PO", "alias" => "no_po"),
            array("index" => "4", "field" => "DATE_FORMAT(asset.TANGGAL_REG, '%d %b %Y')", "alias" => "request_date"),
            array("index" => "5", "field" => "requestor.name", "alias" => "requestor"),
            array("index" => "6", "field" => "DATE_FORMAT(asset.TANGGAL_PO, '%d %b %Y')", "alias" => "po_date"),
            array("index" => "7", "field" => "asset.KODE_VENDOR", "alias" => "vendor_code"),
            array("index" => "8", "field" => "asset.NAMA_VENDOR", "alias" => "vendor_name"),
        );

        foreach ($field as $row) {
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
            SELECT asset.ID as id ' . implode(", ", $selectedColumn) . '
            FROM TR_REG_ASSET as asset
            INNER JOIN TBM_USER as requestor ON (requestor.id=asset.CREATED_BY)
            WHERE asset.NO_REG > 0
        ';

        $total_data = DB::select(DB::raw($sql));

        if ($request->no_po)
            $sql .= " AND asset.NO_PO  like '%" . $request->no_po . "%'";
       
            if ($request->no_reg)
            $sql .= " AND asset.NO_REG  like '%" . $request->no_reg . "%'";

        if ($request->requestor)
            $sql .= " AND requestor.name  like '%" . $request->requestor . "%'";

        if ($request->vendor_code)
            $sql .= " AND asset.KODE_VENDOR  like '%" . $request->vendor_code . "%'";

        if ($request->vendor_name)
            $sql .= " AND asset.NAMA_VENDOR  like '%" . $request->vendor_name . "%'";

        if ($request->transaction_type)
            $sql .= " AND asset.TYPE_TRANSAKSI  = " . $request->transaction_type;
     
        if($request->po_type !='')
            $sql .= " AND asset.PO_TYPE  = " . $request->po_type;

        if ($request->request_date)
            $sql .= " AND DATE_FORMAT(asset.TANGGAL_REG, '%Y-%m-%d') = '" . DATE_FORMAT(date_create($request->request_date), 'Y-m-d'). "'";


        if ($request->po_date)
            $sql .= " AND DATE_FORMAT(asset.TANGGAL_PO, '%Y-%m-%d') = '" . DATE_FORMAT(date_create($request->po_date), 'Y-m-d') ."'";

        if ($orderColumn != "") {
            $sql .= " ORDER BY " . $field[$orderColumn]['field'] . " " . $dirColumn;
        }
        else
        {
            $sql .= " ORDER BY asset.ID DESC ";
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

    function requestDetail()
    {
        $id = $_REQUEST['id'];
        $data = DB::table('TR_REG_ASSET')
            ->select("ID  as id", "NO_REG as no_reg", "BUSINESS_AREA as business_area" ,"TYPE_TRANSAKSI as transaction_type", "TANGGAL_REG as request_date","NO_PO as no_po", "TANGGAL_PO as po_date","KODE_VENDOR as vendor_code","NAMA_VENDOR as vendor_name")
            ->where("ID", "=", $id)
            ->get();

        return response()->json(array('data' => $data));
    }

    function requestDetailFiles()
    {
        $id = $_REQUEST['id'];
        $data = DB::table('TR_REG_ASSET_FILE')
            ->select(
            "ID as id",
            "NO_FILE as no",
            "FILE_CATEGORY as category",
            "FILENAME as file_name",
            "DOC_SIZE as size",
            "FILE_UPLOAD as file")
            ->where([
                [ "ASSET_REG_ID", "=", $id]
            ])
            ->get();

        return response()->json(array('data' => $data));
    }
   
    function requestDetailItem()
    {
        $id = $_REQUEST['id'];
        $data = DB::table('TR_REG_ASSET_DETAIL_PO')
            ->select("ID as id", "ITEM_PO as item_id", "KODE_MATERIAL as material_code", "NAMA_MATERIAL as material_name","QUANTITY_PO as qty", "QUANTITY_SUBMIT as qty_request")
            ->where( "ASSET_REG_ID", "=", $id)
            ->get();

        return response()->json(array('data' => $data));
    }
    
    function requestDetailItemPO()
    {
        $id = $_REQUEST['id'];
        $data = DB::table('TR_REG_ASSET_DETAIL')
            ->select(
            "ID as id",
            "ITEM_PO as item_po",
            "KODE_MATERIAL as code",
            "NAMA_MATERIAL as name",
            "NO_PO as po_no",
            "KODE_JENIS_ASSET  as asset_type",
            "JENIS_ASSET as asset_type",
            "GROUP as asset_group",
            "SUB_GROUP as asset_sub_group",
            "NAMA_ASSET as asset_name",
            "MERK as asset_brand",
            "SPESIFIKASI_OR_WARNA as asset_specification",
            "NO_RANGKA_OR_NO_SERI as asset_serie_no",
            "NO_MESIN_OR_IMEI as asset_imei",
            "NO_POLISI as asset_police_no",
            "LOKASI_BA_CODE as asset_ba_code",
            "LOKASI_BA_DESCRIPTION  as  asset_location",
            "TAHUN_ASSET as asset_year",
            "KONDISI_ASSET  as asset_condition",
            "INFORMASI as asset_info",
            "NAMA_PENANGGUNG_JAWAB_ASSET as asset_pic_name",
            "JABATAN_PENANGGUNG_JAWAB_ASSET as asset_pic_level")
            ->where([
                [ "ASSET_PO_ID", "=", $id]
            ])
            ->get();

        return response()->json(array('data' => $data));
    }

    function requestDetailItemFile()
    {
        $id = $_REQUEST['id'];
        $data = DB::table('TR_REG_ASSET_DETAIL_FILE')
            ->select(
            "ID as id",
            "JENIS_FOTO as type",
            "FILE_CATEGORY as category",
            "FILENAME as file_name",
            "DOC_SIZE as size",
            "FILE_UPLOAD as file")
            ->where([
                [ "ASSET_PO_DETAIL_ID", "=", $id]
            ])
            ->get();

        return response()->json(array('data' => $data));
    }

}
