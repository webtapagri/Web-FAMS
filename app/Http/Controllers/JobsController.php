<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;
use App\Workflow;
use App\jobs;
use App\failed_jobs;
use Debugbar;

class JobsController extends Controller
{

    public function index()
    {
        //echo "Module Workflow"; die();
        if (empty(Session::get('authenticated')))
            return redirect('/login');

        if (AccessRight::granted() === false) {
            $data['page_title'] = 'Oops! Unauthorized.';
            return response(view('errors.403')->with(compact('data')), 403);
        }
        
        $access = AccessRight::access();    
        $data['page_title'] = 'Jobs';
        $data['ctree_mod'] = 'Setting';
        $data['ctree'] = 'setting/jobs';
        $data["access"] = (object)$access;
        return view('monitoring.jobs')->with(compact('data'));
    }

    public function dataGrid(Request $request)
    {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";

        //$selectedColumn = ['workflow_code','workflow_name', 'menu_code', 'workflow_code'];
        $selectedColumn = ['id','queue','payload', 'attempts', 'reserved_at','available_at','created_at'];

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
                FROM JOBS
                WHERE 1=1
        ';
        Debugbar::info($sql);

        if ($request->idate)
        $sql .= " AND queue like'%" . $request->id . "%'";

        if ($request->queue)
        $sql .= " AND queue like'%" . $request->queue . "%'";

        if ($request->payload)
        $sql .= " AND payload like'%" . $request->payload . "%'";

        if ($request->attempts)
        $sql .= " AND attempts like'%" . $request->attempts . "%'";

        if ($request->payload)
        $sql .= " AND reserved_at like'%" . $request->reserved_at . "%'";

        if ($request->available_at)
        $sql .= " AND available_at like'%" . $request->available_at . "%'";

        if ($request->created_at)
        $sql .= " AND created_at like'%" . $request->created_at . "%'";

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

    

    // public function dataGridDetailJob(Request $request)
    // {
    //     //echo "<pre>"; print_r($request->id); die();
    //     $req_id = $request->id;
    //     $orderColumn = $request->order[0]["column"];
    //     $dirColumn = $request->order[0]["dir"];
    //     $sortColumn = "";
    //     $selectedColumn[] = "";

    //     $selectedColumn = ['a.workflow_job_code','b.workflow_group_name', 'c.name', 'a.seq', 'a.operation', 'a.lintas', 'd.name as next_approve', 'a.limit_approve'];

    //     if ($orderColumn) {
    //         $order = explode("as", $selectedColumn[$orderColumn]);
    //         if (count($order) > 1) {
    //             $orderBy = $order[0];
    //         } else {
    //             $orderBy = $selectedColumn[$orderColumn];
    //         }
    //     }

    //     $sql = '
    //         SELECT ' . implode(", ", $selectedColumn) . '
    //             FROM TR_WORKFLOW_JOB a 
    //                 LEFT JOIN TR_WORKFLOW_DETAIL b ON a.workflow_detail_code = b.workflow_detail_code
    //                 LEFT JOIN TBM_ROLE c ON a.id_role = c.id
    //                 LEFT JOIN TBM_ROLE d ON a.next_approve = d.id
    //             WHERE a.workflow_detail_code = '.$req_id.'
    //     ';

    //     if ($request->name)
    //     $sql .= " AND c.name like'%" . $request->name . "%'";


    //     if ($request->operation)
    //     $sql .= " AND a.operation like'%" . $request->operation . "%'";

    //     if ($orderColumn != "") {
    //         $sql .= " ORDER BY " . $orderBy . " " . $dirColumn;
    //     }

    //     $data = DB::select(DB::raw($sql));

    //     $iTotalRecords = count($data);
    //     $iDisplayLength = intval($request->length);
    //     $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
    //     $iDisplayStart = intval($request->start);
    //     $sEcho = intval($request->draw);
    //     $records = array();
    //     $records["data"] = array();

    //     $end = $iDisplayStart + $iDisplayLength;
    //     $end = $end > $iTotalRecords ? $iTotalRecords : $end;

    //     for ($i = $iDisplayStart; $i < $end; $i++) {
    //         $records["data"][] =  $data[$i];
    //     }

    //     if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    //         $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    //         $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
    //     }

    //     $records["draw"] = $sEcho;
    //     $records["recordsTotal"] = $iTotalRecords;
    //     $records["recordsFiltered"] = $iTotalRecords;
    //     return response()->json($records); # = 1
    // }

    
}
