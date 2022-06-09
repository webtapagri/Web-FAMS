<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\TmRole;
use function GuzzleHttp\json_encode;
use Session;
use API;
use App\Menu;
use AccessRight;

class MenuController extends Controller
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
        $data['page_title'] = 'Module';
        $data['ctree_mod'] = 'Setting';
        $data['ctree'] = 'menu';
        $data["access"] = (object) $access;
        return view('usersetting.menu')->with(compact('data'));
    }

    public function dataGrid(Request $request)
    {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";

        $selectedColumn = ["menu.sort", "module.name as module_name", "menu.menu_code", "menu.name", "menu.url", "menu.deleted", "module.id as module_id", "menu.id"];

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
                FROM TBM_MENU as menu
                INNER JOIN TBM_MODULE as module ON (module.id=menu.module_id)
                WHERE menu.id > 0
        ';


        if ($request->module)
            $sql .= " AND module.id ='" . $request->module . "'";

        if ($request->menu_code)
            $sql .= " AND menu.menu_code like'%" . $request->menu_code . "%'";
       
        if ($request->name)
            $sql .= " AND menu.name like'%" . $request->name . "%'";


        if ($request->url)
            $sql .= " AND menu.url like'%" . $request->url . "%'";


        if ($request->status)
            $sql .= " AND menu.deleted = " . $request->status;

        if ($request->sort)
            $sql .= " AND menu.sort = " . $request->sort;

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
        try 
        {
            if ($request->edit_id) 
            {
                $data = Menu::find($request->edit_id);
                $data->updated_by = Session::get('user_id');
            } 
            else 
            {
                $data = new Menu();
                $data->created_by = Session::get('user_id');
            }

            $data->module_id = $request->module;
            $data->menu_code = $request->menu_code;
            $data->name = $request->name;
            $data->sort = $request->sorting;
            $data->url = $request->url;

            $data->save();

            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->edit_id ? 'updated' : 'added')]);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }

    function validateName($name) 
    {
        $data = DB::table('TBM_MENU')
            ->select('id', 'name as text')
            ->where('name', $name)
            ->get();

        if (count($data > 0)) {
            return false;
        } else {
            return true;
        }
    }

    public function show()
    {
        $param = $_REQUEST;
        $data = Menu::find($param["id"]);
        return response()->json(array('data' => $data));
        
    }

    public function inactive(Request $request)
    {
        try {
            $data = Menu::find($request->id);
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
            $data = Menu::find($request->id);
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
        $data = DB::table('TBM_MENU')
            ->select('menu_code as id', 'name as text')
            ->where('deleted', 0)
            ->where("menu_code", "!=", "")
            ->get();

        return response()->json(array("data" => $data));
    }

    /* HIDE IT@031019 REV MOD Workflow, edit / add menggunakan menu_code column
    public function select2()
    {
        $data = DB::table('TBM_MENU')
            ->select('id', 'name as text')
            ->where('deleted', 0)
            ->get();

        return response()->json(array("data" => $data));
    }
    */
}
