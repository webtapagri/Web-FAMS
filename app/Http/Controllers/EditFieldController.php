<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use function GuzzleHttp\json_encode;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Session;
use AccessRight;
use App\EditField;
use API;


class EditFieldController extends Controller
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
        $data["page_title"] = "Editable Field";
        $data['ctree_mod'] = 'Setting';
        $data['ctree'] = 'accessright';
        $data["access"] = (object)$access;
        return view('usersetting.editfield')->with(compact('data'));
    }

    public function dataGrid(Request $request)
    {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";
        $field = array(
            array("index" => "0", "field" => "role.name", "alias" => "role_name"),
            array("index" => "1", "field" => "tn.TABLE_NAME", "alias" => "tablename"),
            array("index" => "2", "field" => "tn.COLUMN_NAME", "alias" => "fieldname"),
            array("index" => "3", "field" => "te.id", "alias" => "field_id"),
            array("index" => "3", "field" => "te.editable", "alias" => ""),
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
                SELECT
                    role.id as role_id
                    '.implode(", ",$selectedColumn).'
                    FROM   TBM_ROLE as role
                    JOIN(
                    SELECT DISTINCT UPPER(TABLE_NAME) AS TABLE_NAME, UPPER(COLUMN_NAME) AS COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME LIKE "TM_%" AND COLUMN_NAME NOT LIKE "ID%"
                    ) as tn
                    LEFT OUTER JOIN(
                    select id, role_id, tablename, fieldname, editable
                    from TBM_EDITFIELD 
                    ) as te ON (te.role_id = role.id AND te.tablename = tn.TABLE_NAME  AND te.fieldname = tn.COLUMN_NAME)
                    WHERE role.deleted=0 AND tn.TABLE_NAME = "TM_MSTR_ASSET" ';

        if($request->role)
            $sql .= ' AND role.id = ' . $request->role;
        
        if($request->tablename)
            $sql .= " AND tn.TABLE_NAME =  '". $request->tablename ."'";
       
        if($request->fieldname)
            $sql .= " AND tn.COLUMN_NAME = '" . $request->fieldname."'";
        
        if($request->editable)
            $sql .= ' AND te.editable = ' . $request->editable;

        if ($orderColumn != "") {
            $sql .= " ORDER BY " . $orderColumnName . " " . $dirColumn;
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

    // public function show()
    // {
    //     $param = $_REQUEST;
    //     $data = explode('-', $param["id"]);
    //     $service = API::exec(array(
    //         'request' => 'GET',
    //         'method' => "tr_role_accessright/" . $data[0] .'/'. $data[1] .'/'. $data[2]
    //     ));
    //     $data = $service->data;

    //     return response()->json(array('data' => $data));

    // }

    public function store(Request $request)
    {
        try {
            foreach($request->param as $row) {
                if($row["field_id"]) {
                    $data = EditField::find( $row["field_id"]);
                    $data->updated_by = Session::get('user_id');
                } else {
                    $data = new EditField();
                    $data->created_by = Session::get('user_id');
                }

                $data->role_id = $row["role_id"];
                $data->tablename = $row["tablename"];
                $data->fieldname = $row["fieldname"];
                $data->editable = $row["editable"];
                $data->save();
            }

            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->edit_id ? 'updated' : 'added')]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    public function select_tablename()
    {
        // $sql = 'SELECT DISTINCT UPPER(TABLE_NAME) as id, UPPER(TABLE_NAME) as text FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE "TM_%" ';
        $sql = " SELECT DISTINCT UPPER(TABLE_NAME) as id, UPPER(TABLE_NAME) as text FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'TM_MSTR_ASSET' ";
        $data = DB::select(DB::raw($sql));
        return response()->json(array("data" => $data));
    }

    public function select_fieldname(Request $request)
    {
        // $sql = " SELECT DISTINCT UPPER(COLUMN_NAME) as id, UPPER(COLUMN_NAME) as text FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME NOT LIKE 'ID%' AND TABLE_NAME = '{$request->tbl}' ";
        $sql = " SELECT DISTINCT UPPER(COLUMN_NAME) as id, UPPER(COLUMN_NAME) as text FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_NAME NOT LIKE 'ID%' AND TABLE_NAME = 'TM_MSTR_ASSET' ";
        $data = DB::select(DB::raw($sql));

        return response()->json(array("data" => $data));
    }

    


}
