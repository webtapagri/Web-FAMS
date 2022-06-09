<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;
use App\Role;

class RolesController extends Controller
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
        $data["page_title"] = "Role";
        $data['ctree_mod'] = 'Setting';
        $data['ctree'] = 'roles';
        $data["access"] = (object)$access;
        return view('usersetting.roles')->with(compact('data'));
    }

    public function dataGrid(Request $request)
    {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";

        $selectedColumn = ['name', 'description', 'deleted', "id"];

        if ($orderColumn) {
            $order = explode("as", $selectedColumn[$orderColumn]);
            if (count($order) > 1) {
                $orderBy = $order[0];
            } else {
                $orderBy = $selectedColumn[$orderColumn];
            }
        }

        $sql = '
            SELECT ' . implode(", ", $selectedColumn) . '
                FROM TBM_ROLE
                WHERE id > 0
        ';


        if ($request->name)
            $sql .= " AND name like'%" . $request->name . "%'";


        if ($request->desc)
            $sql .= " AND description like'%" . $request->desc . "%'";



        if ($request->status)
            $sql .= " AND deleted = " . $request->status;

        if ($orderColumn != "") {
            $sql .= " ORDER BY " . $orderBy . " " . $dirColumn;
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

    public function store(Request $request)
    {
        try {
            if ($request->edit_id) {
                $data = Role::find($request->edit_id);
                $data->updated_by = Session::get('user_id');
            } else {
                $data = new Role();
                $data->created_by = Session::get('user_id');
            }

            $data->name = $request->name;
            $data->description = $request->description;

            $data->save();

            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->edit_id ? 'updated' : 'added')]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    public function show()
    {
        $param = $_REQUEST;
        $data = Role::find($param["id"]);
        return response()->json(array('data' => $data));
        
    }

    public function inactive(Request $request)
    {
        try {

            $data = Role::find($request->id);
            $data->updated_by = Session::get('user_id');
            $data->deleted = 1;

            $data->save();

            return response()->json(['status' => true, "message" => 'Data is successfully inactived']);

        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    public function active(Request $request)
    {
        try {
            $data = Role::find($request->id);
            $data->updated_by = Session::get('user_id');
            $data->deleted = 0;

            $data->save();

            return response()->json(['status' => true, "message" => 'Data is successfully activated']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    public function select2()
    {
        $data = DB::table('TBM_ROLE')
            ->select('id', 'name as text')
            ->where('deleted', 0)
            ->get();

        return response()->json(array("data" => $data));
    }

    public function select_role(Request $request) 
    {
        $data = DB::table('TBM_ROLE')
        ->select('id as id', 'name as text')
        //->where([[ '1',"=" ,'1'],])
        ->get();

        $arr = array();
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id.'__'.$row->text,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }
}
