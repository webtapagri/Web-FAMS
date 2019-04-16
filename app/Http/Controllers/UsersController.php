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
class UsersController extends Controller
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

    public function dataGrid(Request $request) {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";

        $selectedColumn = ['user.img', "user.username", "user.name","role.name as role_name", "user.email", "user.job_code", "user.NIK", "user.area_code", "user.deleted", "user.id"];
        if($orderColumn) {
            $order = explode("as", $selectedColumn[$orderColumn]);
            if(count($order)>1) {
                $orderBy = $order[0]; 
            } else {
                $orderBy = $selectedColumn[$orderColumn];
            }

        }

        $sql = '
            SELECT ' . implode(", ", $selectedColumn) . '
                FROM tbm_user as user
                INNER JOIN tbm_role as role ON (role.id=user.role_id)
        ';

        $total_data = DB::select(DB::raw($sql));
        //$sql .=  " limit " . $request->start . ', ' .$request->length;

        if ($request->username)
            $whereClause[] = [ "user.username", 'like',"%". $request->username ."%"];
        
            if ($request->role)
            $whereClause[] = [ "role.id", 'like',"%". $request->role ."%"];

       
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
                $data = User::find($request->edit_id);
                $data->updated_by = Session::get('user_id');
            } else {
                $data = new User();
                $data->created_by = Session::get('user_id');
            }

            $data->role_id = $request->role_id;
            $data->username = $request->username;
            $data->name = $request->name;
            $data->email = $request->email;
            $data->job_code = $request->job_code;
            $data->nik = $request->nik;
            $data->area_code = implode(',', $request->area_code);
          
            foreach ($_FILES as $row) {
                if ($row["name"]) {
                    $name = $row["name"];
                    $size = $row["size"];
                    $path = $row["tmp_name"];
                    $type = pathinfo($row["tmp_name"], PATHINFO_EXTENSION);
                    $img = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($img);
                   $data->img = $base64;
                }
            }

            $data->save();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->edit_id ? 'updated' : 'added')]);
            
       } catch (\Exception $e) {
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
        $data = User::find($param['id']);
        return response()->json(array('data' => $data));
    }

    public function inactive(Request $request) {
        try {
            $data = User::find($request->id);
            $data->updated_by = Session::get('user_id');
            $data->deleted = 1;

            $data->save();

            return response()->json(['status' => true, "message" => 'Data is successfully inactived']);

        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }
   
    public function active(Request $request) {
        try {
            $data = User::find($request->id);
            $data->updated_by = Session::get('user_id');
            $data->deleted = 0;

            $data->save();

            return response()->json(['status' => true, "message" => 'Data is successfully inactived']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }
}
