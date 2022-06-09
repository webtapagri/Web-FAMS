<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\TmRole;
use function GuzzleHttp\json_encode;
use Session;
use API;
use AccessRight;

class ResumeController extends Controller
{

    public function index()
    {
        if (empty(Session::get('authenticated')))
            return redirect('/login');
            

        $data["page_title"] = "Resume";    
        $data["profile"] = AccessRight::access();
        $data['ctree_mod'] = 'Resume Process';
        $data['ctree'] = 'resume/document';
        //echo "1<pre>"; print_r($data);
        return view('resume.document')->with(compact('data'));
    }

    function document_submit(Request $request)
    {
    	$user_id = Session::get('user_id');
    	$no_document = $request->no_document;

    	DB::beginTransaction();
        try 
        {   
            DB::STATEMENT('CALL resume_approval("'.$no_document.'")');
            DB::commit();

            $result = array('status'=>true,'message'=> "Resume is successfully updated");
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            $result = array('status'=>false,'message'=>$e->getMessage());
        }
        return $result;
    }

    public function user()
    {
        if (empty(Session::get('authenticated')))
            return redirect('/login');
            

        $data["page_title"] = "Resume User";    
        $data["profile"] = AccessRight::access();
        $data['ctree_mod'] = 'Resume Process';
        $data['ctree'] = 'resume/user';
        //echo "1<pre>"; print_r($data);
        return view('resume.user')->with(compact('data'));
    }

    function user_submit(Request $request)
    {
    	$user_id = Session::get('user_id');
    	$user_id_old = $request->user_id_old;
    	$user_id_new = $request->user_id_new;

    	DB::beginTransaction();
        try 
        {   
            DB::STATEMENT(' CALL resume_user("'.$user_id_old.'","'.$user_id_new.'") ');

            DB::INSERT(' INSERT iNTO TR_LOG_RESUME_USER(user_id_old,user_id_new,created_by,created_on)VALUES('.$user_id_old.','.$user_id_new.','.$user_id.', current_timestamp() ) ');

            DB::commit();

            $result = array('status'=>true,'message'=> "Resume is successfully updated");
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            $result = array('status'=>false,'message'=>$e->getMessage());
        }
        return $result;
    }

}
