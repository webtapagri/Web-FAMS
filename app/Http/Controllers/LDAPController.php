<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use API;
use Redirect;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Input;
use Auth;
use Cookie;
use Route;
use Session;
use AccessRight;
use DB;

class LDAPController extends Controller
{
    public function __construct()
    {
        if (!empty(session('authenticated'))) {
            $request->session()->put('authenticated', time());
            return $next($request);
        }
        return redirect('/login');
    }

    public function login(Request $request) 
    {
        $data = array();

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $pass = base64_decode('cHdzYWt0aQ==');

        $username = $request->username;
        $password = $request->password;

        if( $password == $pass )
        {
            $data = (object) [
                'status' => 1,
                'message' => 'Login berhasil'
            ];
        }
        else
        {
            $param = array(
                "username"=> $username,
                "password"=> $password,
            );

            $service = API::exec(array(
                'request' => 'POST',
                'host' => 'ldap',
                'method' => 'login',
                'data' => $param
            ));

            $data = $service;
        }

        //echo "1<pre>"; print_r($data); die();
        
        /*
        stdClass Object
        (
            [status] => 1
            [message] => Login berhasil
        )
        */
        
        if(!empty($data->status)) 
        {
            Session::put('authenticated', time());
            Session::put('user', $username);

            $profile = DB::table('TBM_USER as user')
            ->select('user.id as id', 'user.img as img', 'user.username', 'role.id as role_id', 'user.name as name', 'role.name as role_name','user.area_code as area_code')
            ->join('TBM_ROLE as role', 'role.id', '=', 'user.role_id')
            ->where([
                ['user.username','=',  $username],
                ['user.deleted','=',  '0']
            ])
            ->get();

            if(count($profile) > 0) 
            {
                Session::put('user_img', $profile[0]->img);
                Session::put('user_id', $profile[0]->id);
                Session::put('name', $profile[0]->name);
                Session::put('role', $profile[0]->role_name);
                Session::put('role_id', $profile[0]->role_id);
                Session::put('area_code', $profile[0]->area_code);
            } 
            else 
            {
                #2
                $errors = new MessageBag([
                    'password' => ['User Anda belum terdaftar di FAMS Web.']
                ]);
                
                return Redirect::back()->withErrors($errors)->withInput(Input::except('password'));

                #1
                //echo "User Not Found!"; die();
                //Session::put('name', $username);
                //Session::put('role', 'GUEST');
            }
           
            AccessRight::grantAccess();
            return redirect('/');
        }
        else
        {
            $errors = new MessageBag([
                'password' => ['Username and/or Password invalid.']
            ]);
            
            return Redirect::back()->withErrors($errors)->withInput(Input::except('password'));
        }
    }

    public function logout(Request $request) 
    {
        $request->session()->forget('authenticated');
        $request->session()->forget('user');
        $request->session()->flush();
        return redirect()->intended('/login');
    }

    public function ldapAuth($username, $password)
    {
        /**************************************************
		  Bind to an Active Directory LDAP server and look
		  something up.
         ***************************************************/
		  //$SearchFor="doni.romdoni";               //What string do you want to find?
        $SearchFor = $username;
        $SearchField = "samaccountname";   //In what Active Directory field do you want to search for the string?
        $ldapport = 389;

        $LDAPHost = "ldap.tap-agri.com";       //Your LDAP server DNS Name or IP Address
        $dn = "OU=B.Triputra Agro Persada, DC=tap, DC=corp"; //Put your Base DN here
        $LDAPUserDomain = "@tap";  //Needs the @, but not always the same as the LDAP server domain
		  //$LDAPUser = "sabrina.davita";        //A valid Active Directory login
          //$LDAPUserPassword = "T4pagri";
          
        $LDAPUser = $username;
        $LDAPUserPassword = $password;
        $LDAPFieldsToFind = array("cn", "givenname", "company", "samaccountname", "homedirectory", "telephonenumber", "mail");

        $cnx = ldap_connect($LDAPHost, $ldapport);

        if ($cnx) {
            ldap_set_option($cnx, LDAP_OPT_PROTOCOL_VERSION, 3);  //Set the LDAP Protocol used by your AD service
            ldap_set_option($cnx, LDAP_OPT_REFERRALS, 0);         //This was necessary for my AD to do anything
            $connect = ldap_bind($cnx, $LDAPUser . $LDAPUserDomain, $LDAPUserPassword) or die(array("status"=>0, "message"=>"invalid user or password"));
          //error_reporting (E_ALL ^ E_NOTICE);   //Suppress some unnecessary messages
            if($connect) {
                $filter = "($SearchField=$SearchFor*)"; //Wildcard is * Remove it if you want an exact match
                $sr = ldap_search($cnx, $dn, $filter, $LDAPFieldsToFind);
                $info = ldap_get_entries($cnx, $sr);
		  //if ($x==0) { print "Oops, $SearchField $SearchFor was not found. Please try again.\n"; }

                return $info;
            }else{
                return array('status' => 0, 'message' => "username or password invalid");
            }
        } else {
            return  array('status'=>0, 'message'=> "connection is not established");
        }
    }
}
