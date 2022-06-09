<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use function GuzzleHttp\json_encode;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Session;
use AccessRight;
use App\RoleAccess;
use API;


class AccessRightController extends Controller
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
        $data["page_title"] = "Access Right";
        $data['ctree_mod'] = 'Setting';
        $data['ctree'] = 'accessright';
        $data["access"] = (object)$access;
        return view('usersetting.accessright')->with(compact('data'));
    }

    public function dataGrid(Request $request)
    {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";
        $field = array(
            array("index" => "0", "field" => "role.name", "alias" => "role_name"),
            array("index" => "1", "field" => "module.module_name", "alias" => ""),
            array("index" => "2", "field" => "module.menu_name", "alias" => ""),
            array("index" => "3", "field" => "role_access.create", "alias" => ""),
            array("index" => "4", "field" => "role_access.read", "alias" => ""),
            array("index" => "5", "field" => "role_access.update", "alias" => ""),
            array("index" => "6", "field" => "role_access.delete", "alias" => ""),
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
                role.id as role_id,
                module.module_id,
                module.menu_id, 
                role_access.id as access_id
                '.implode(", ",$selectedColumn).'
                FROM  
                    TBM_ROLE as role
                JOIN(
                    SELECT 
                        module.id as module_id,
                        module.name as module_name,
                        menu.id as menu_id,
                        menu.name as menu_name
                    FROM 
                        TBM_MODULE as module
                    INNER JOIN TBM_MENU as menu ON (module.id=menu.module_id)
                    WHERE module.deleted=0
                ) as module
                LEFT OUTER JOIN(
                    select id, role_id, menu_id, module_id, `create`, `read`, `update`, `delete`
                    from TBM_ROLE_ACCESS 
                )  as role_access ON (role_access.role_id = role.id AND role_access.module_id=module.module_id  AND role_access.menu_id=module.menu_id)
                WHERE role.deleted=0 
        ';

        if($request->role)
            $sql .= ' AND role.id = ' . $request->role;
        
        if($request->module)
            $sql .= ' AND module.module_id = ' . $request->module;
       
        if($request->menu)
            $sql .= ' AND module.menu_id = ' . $request->menu;
        
        
        if($request->create)
        $sql .= ' AND role_access.create = ' . $request->create;
        
        if($request->read)
        $sql .= ' AND role_access.read = ' . $request->read;
        
        if($request->update)
        $sql .= ' AND role_access.update = ' . $request->update;
        
        if($request->delete)
        $sql .= ' AND role_access.delete = ' . $request->delete;

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

    public function show()
    {
        $param = $_REQUEST;
        $data = explode('-', $param["id"]);
        $service = API::exec(array(
            'request' => 'GET',
            'method' => "tr_role_accessright/" . $data[0] .'/'. $data[1] .'/'. $data[2]
        ));
        $data = $service->data;

        return response()->json(array('data' => $data));

    }

    public function store(Request $request)
    {
        try {
            foreach($request->param as $row) {
                if($row["access_id"]) {
                    $data = RoleAccess::find( $row["access_id"]);
                    $data->updated_by = Session::get('user_id');
                } else {
                    $data = new RoleAccess();
                    $data->created_by = Session::get('user_id');
                }

                $data->role_id = $row["role_id"];
                $data->module_id = $row["module_id"];
                $data->menu_id = $row["menu_id"];
                $data->create = $row["create"];
                $data->read = $row["read"];
                $data->update = $row["update"];
                $data->delete = $row["remove"];
                $data->save();
            }

            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->edit_id ? 'updated' : 'added')]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }
}
