<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use API;
use Session;

class Select2Controller extends Controller
{
    public function get(Request $request){
         $table = $request->get('table');
         $id = $request->get('id');
         $text = $request->get('text');
        $where = '';
        if( $request->get('wheres')) {
            $no = 1;
            foreach( $request->get('wheres') as $row) {
                $param = explode(',',$row);
                if($no>1) {
                    $where .= ' AND';
                }
                if($param[1] == 'equal') {
                    $where .= $param[0]."= '".$param[2]."'";
                }
                $no++;
            }
        }
        
         $data = DB::table("$table")
         ->select($id . ' as id', $text . ' as text')
         ->whereRaw($where)
         ->get();

        $arr = array();
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }

    public function generaldataplant(Request $request) {
        $data = DB::table('TM_GENERAL_DATA')
        ->select('DESCRIPTION_CODE as id', 'DESCRIPTION as text')
        ->where([
            [ 'GENERAL_CODE',"=" ,'plant'],
        ])
        ->get();

        $arr = array();
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }


    public function businessarea() 
    {
        $user_id = Session::get('user_id');
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


     public function generaldata_assetcontroller(Request $request) 
     {
        $data = DB::table('TM_GENERAL_DATA')
        ->select('DESCRIPTION_CODE as id', 'DESCRIPTION as text')
        ->where([
            [ 'GENERAL_CODE',"=" ,'asset_controller'],
        ])
        ->get();

        $arr = array();
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id.'__'.$row->text,
                // "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }
    
    public function jenisasset(Request $request) {
        $data = DB::table( 'TM_JENIS_ASSET')
        ->select( 'JENIS_ASSET_CODE as id', 'JENIS_ASSET_DESCRIPTION as text')
        ->orderby('id', 'asc')
        ->get();

        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) 
        {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }
        //echo "<pre>"; print_r($arr); die();
        return response()->json(array('data' => $arr));
    }

    public function jenis_asset(Request $request) {
        $sql = "select A.id,A.text from
                (SELECT A.JENIS_ASSET_CODE as id, A.JENIS_ASSET_DESCRIPTION as text FROM TM_JENIS_ASSET A
                    JOIN TM_GENERAL_DATA B ON B.GENERAL_CODE = 'kodefikasi_asset_class'
                WHERE B.DESCRIPTION_CODE = '$request->ba_code' AND B.DESCRIPTION = SUBSTR( A.JENIS_ASSET_CODE, 1, 1 )
                UNION
                SELECT A.JENIS_ASSET_CODE as id, A.JENIS_ASSET_DESCRIPTION as text FROM TM_JENIS_ASSET A
                WHERE SUBSTR( A.JENIS_ASSET_CODE, 1, 1 ) = 'U') A
                order by A.id asc";
        $data = DB::select(DB::raw($sql))
        ->orderby('id', 'asc')
        ->get();
        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) 
        {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }
        //echo "<pre>"; print_r($arr); die();
        return response()->json(array('data' => $arr));
    }
    
    public function assetgroup(Request $request) 
    {
        $data = DB::table( 'TM_GROUP_ASSET')
        ->select('GROUP_CODE as id', 'GROUP_DESCRIPTION as text')
        ->where( "JENIS_ASSET_CODE", $request->type)
        ->get();

        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }

    public function assetgroupcondition(Request $request) 
    {
        $data = DB::table( 'TM_GROUP_ASSET')
        ->select('GROUP_CODE as id',DB::raw('CONCAT(GROUP_CODE,"-",GROUP_DESCRIPTION) as text'))
        ->where( "JENIS_ASSET_CODE", $request->type)
        ->get();

        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }

    public function assetsubgroup(Request $request) 
    {
        $data = DB::table( 'TM_SUBGROUP_ASSET AS SUBGROUP')
        ->select('SUBGROUP.SUBGROUP_CODE AS id', 'SUBGROUP.SUBGROUP_DESCRIPTION AS text')
        //->join('TM_GROUP_ASSET AS GROUP', 'SUBGROUP.GROUP_CODE', '=', 'GROUP.ID')
        ->where( 
                array(
                    "SUBGROUP.GROUP_CODE" => $request->group,
                    "SUBGROUP.JENIS_ASSET_CODE" => $request->jenis_asset_code
                )
            )
        ->get();

        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id.'-'.$row->text
            );
        }
        //echo "<pre>"; print_r($arr); die();

        return response()->json(array('data' => $arr));
    }

    /*
    public function assetsubgroup_v1(Request $request) 
    {
        $data = DB::table( 'TM_SUBGROUP_ASSET AS SUBGROUP')
        ->select('SUBGROUP.SUBGROUP_CODE AS id', 'SUBGROUP.SUBGROUP_DESCRIPTION AS text')
        ->where( 
                array(
                    "SUBGROUP.GROUP_CODE" => $request->group
                )
            )
        ->get();

        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }
    */

    public function select_uom()
    {
        $data = DB::table('TM_GENERAL_DATA')
        ->select('description_code as id', 'description as text')
        ->where('general_code', 'uom')
        ->orderby('description', 'asc')
        ->get();
        return response()->json(array("data"=>$data));
    }

    public function select_role()
    {
        $data = DB::table('TBM_ROLE')
        ->select('id as id', 'name as text')
        ->where('deleted', 0)
        ->orderby('name', 'asc')
        ->get();

        return response()->json(array("data"=>$data));
    }

    public function select_user(Request $request) 
    {
        $data = DB::table( 'TBM_USER')
        ->select('ID as id', 'NAME as text')
        ->where( "ROLE_ID", $request->type)
        ->orderby('NAME', 'asc')
        ->get();

        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }

    public function select_jenis_kendaraan(Request $request)
    {
        $data = DB::table('TM_GENERAL_DATA')
        ->select(DB::raw('CONCAT(description_code," - ",description) as id'), DB::raw('CONCAT(description_code," - ",description) as text'))
        ->where('general_code', 'jenis_kendaraan')
        ->orderby('description', 'asc')
        ->get();
        return response()->json(array("data"=>$data));
    }

    public function select_jenis_asset(Request $request)
    {
        $data = DB::table('TM_JENIS_ASSET')
        ->select(DB::raw('JENIS_ASSET_CODE as id'), DB::raw('CONCAT(JENIS_ASSET_CODE," - ",JENIS_ASSET_DESCRIPTION) as text'))
        ->orderby('JENIS_ASSET_CODE', 'asc')
        ->get();
        return response()->json(array("data"=>$data));
    }

    public function tujuan_business_area(Request $request) 
    {
        $data = DB::table('TM_GENERAL_DATA')
        ->select('DESCRIPTION_CODE as id', 'DESCRIPTION as text')
        //->select('DESCRIPTION_CODE as id', DB::raw('CONCAT(DESCRIPTION_CODE,"-",DESCRIPTION) as text'))
        ->where( 
            array(
                "GENERAL_CODE" => "plant",
            )
        )
        ->where("DESCRIPTION_CODE", "like", "".$request->type."%")
        ->get();

        $arr = array();
        $arr[] = array("id"=>"","text"=>"");
        foreach ($data as $row) {
            $arr[] = array(
                "id" => $row->id,
                "text" => $row->id .'-' . $row->text
            );
        }

        return response()->json(array('data' => $arr));
    }
}
