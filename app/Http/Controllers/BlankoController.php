<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;
use App\TM_BLANKO;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class BlankoController extends Controller
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
        $data["page_title"] = "Blanko";
        $data['ctree_mod'] = 'Setting';
        $data['ctree'] = 'blanko';
        $data["access"] = (object)$access;
        return view('usersetting.blanko')->with(compact('data'));
    }

    public function dataGrid(Request $request) {
        $orderColumn = $request->order[0]["column"];
        $dirColumn = $request->order[0]["dir"];
        $sortColumn = "";
        $selectedColumn[] = "";
        $where = "";

        $selectedColumn = ["ID","FILE_DESCRIPTION","FILE_NAME", "JENIS_FILE", "DOC_SIZE", "FILE_UPLOAD", "DELETED"];
        if($orderColumn) {
            $order = explode("as", $selectedColumn[$orderColumn]);
            if(count($order)>1) {
                $orderBy = $order[0]; 
            } else {
                $orderBy = $selectedColumn[$orderColumn];
            }

        }
        
        $user_role = Session::get('role');
        if($user_role == 'Super Administrator' || $user_role == 'Admin System'){
            $where .= " AND DELETED = '0' ";
        }

        $sql = '
            SELECT ' . implode(", ", $selectedColumn) . '
                FROM TM_BLANKO 
                WHERE ID > 0 '. $where;

        if ($request->file_description)
        $sql .= " AND FILE_DESCRIPTION like'%". $request->file_description ."%'";

        if ($request->file_name)
        $sql .= " AND FILE_NAME like'%". $request->file_name ."%'";
        if ($request->jenis_file)
        $sql .= " AND JENIS_FILE like'%". $request->jenis_file ."%'";
        if ($request->doc_size)
        $sql .= " AND DOC_SIZE like'%". $request->doc_size ."%'";
              
       
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

    public function upload_berkas(Request $request)
    {
       try {
            
            if( !empty($_FILES['file_upload']['name']) )
            {
                $file_name = str_replace(" ", "_", $_FILES['file_upload']['name']);
                $user_id = Session::get('user_id');
                $file_category = 'file_upload';
                $file_category_label = strtoupper(str_replace("_", " ", $file_category));

                // #1 VALIDASI SIZE DOC MAX 1 MB
                $max_docsize = 1000000;
                if( $_FILES['file_upload']['size'] != 0 )
                {
                    if( $_FILES['file_upload']['size'] > $max_docsize )
                    {
                        return response()->json(['status' => true, "message" => 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file maksimal 1MB']);
                    }
                }
                else
                {                        
                    return response()->json(['status' => true, "message" => 'Gagal upload '.$file_name.' ('.$file_category_label.'), ukuran file 0 MB']);
            
                }

                $file_upload = base64_encode(file_get_contents(addslashes($_FILES['file_upload']['tmp_name'])));

                // #2 VALIDASI FILE UPLOAD EXIST
                $validasi_file_exist = $this->validasi_file_exist($request->file_description);
                if( $validasi_file_exist == 0 )
                {
                    $sql = "INSERT INTO TM_BLANKO(
                                `FILE_NAME`,
                                FILE_DESCRIPTION,
                                JENIS_FILE,
                                DOC_SIZE,
                                FILE_UPLOAD)
                                    VALUES('{$file_name}',
                                    '{$request->file_description}',
                                '".$_FILES['file_upload']['type']."',
                                '".$_FILES['file_upload']['size']."',
                                '{$file_upload}')";
                }
                else
                {
                    $sql = "UPDATE TM_BLANKO SET JENIS_FILE = '".$_FILES['file_upload']['type']."', `FILE_NAME` = '{$file_name}', DOC_SIZE = '".$_FILES['file_upload']['size']."', FILE_UPLOAD = '{$file_upload}' WHERE `FILE_DESCRIPTION` = {$request->file_description}";
                }

                DB::beginTransaction();

                try 
                {
                    DB::insert($sql);
                    DB::commit();
                    return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->file_name ? 'updated' : 'added')]);

                } 
                catch (\Exception $e) 
                {
                    DB::rollback();
                    return response()->json(['status' => false, "message" => 'Failed to update']);
                }
            }
            else
            {
                Session::flash('message', 'Success upload data!');
                return Redirect::to('/blanko');
            }
            

            $data->save();
            return response()->json(['status' => true, "message" => 'Data is successfully ' . ($request->file_name ? 'updated' : 'added')]);
            
       } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
       }


    }

    
    
    public function show()
    {
        $param = $_REQUEST;
        $data = TM_BLANKO::find($param['id']);
        return response()->json(array('data' => $data));
    }


    function list_file_category($id)
    {
    	$result = array();
    	$l = "";

    	
    		
                $l .= '<div class="form-group">
                            <div class="col-xs-12">
                                <label class="control-label" for="name">File Upload</label>
                                <input type="file" class="form-control" id="file_upload" name="file_upload" value="" placeholder="Upload File" />
                            </div>
			            </div>';

    	echo $l; 
    }

    function validasi_file_exist($file_description)
    {
    	$sql = " SELECT COUNT(*) AS TOTAL FROM TM_BLANKO WHERE FILE_DESCRIPTION = '{$file_description}' ";
    	$data = DB::SELECT($sql); 

    	if($data[0]->TOTAL == 0)
    	{
    		return 0;
    	}
    	else
    	{
    		return 1;
    	}
    }


    function berkas($id)
    {
        $id = base64_decode($id); 

        $sql = " SELECT b.DOC_SIZE, b.FILE_NAME,b.FILE_DESCRIPTION, b.FILE_UPLOAD, b.JENIS_FILE FROM TM_BLANKO b WHERE b.ID = '".$id."' "; 
        $data = DB::SELECT($sql);
        
        $l = "";
        if(!empty($data))
        {
            $l .= '<center>';
            $l .= '<h1>'.$id.'</h1>';

            foreach($data as $k => $v)
            {
                

                if( $v->JENIS_FILE == 'image/jpeg' || $v->JENIS_FILE == 'image/png' )
                {
                    $l .= '<div class="caption"><h1><u>'.$v->FILE_NAME.'</u></h1><h3>'.strtoupper($file_category).'<br/><img src="data:image/jpeg;base64,'.$v->FILE_UPLOAD.'"/><br/>'. $v->FILE_NAME. '</h3></div>';
                }
                else if($v->JENIS_FILE == 'application/pdf')
                {
                    $l .= '<h1><u>'.$v->FILE_NAME.'</u></h1><br/><object data="data:application/pdf;base64,'.$v->FILE_UPLOAD.'" type="'.$v->JENIS_FILE.'" style="height:100%;width:100%"></object><br/>'. $v->FILE_NAME. '';
                }
                else
                {
                    
                    $data_excel = trim($v->FILE_UPLOAD); // explode(",",$v->FILE_UPLOAD);
                    // header('Content-type: application/vnd.ms-excel');
                    header('Content-type: '.$v->JENIS_FILE.'"');
                    header('Content-Disposition: attachment; filename="'.$v->FILE_NAME.'"');
                    print $data_excel;
                    // print $data_excel[1];
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


    public function inactive(Request $request)
    {
        try {

            $data = TM_BLANKO::find($request->id);
            // $data->updated_by = Session::get('user_id');
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
            $data = TM_BLANKO::find($request->id);
            // $data->updated_by = Session::get('user_id');
            $data->deleted = 0;

            $data->save();

            return response()->json(['status' => true, "message" => 'Data is successfully activated']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, "message" => $e->getMessage()]);
        }
    }
    
}
