<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cookie;
use Session;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(empty(Session::get('authenticated')))
            return redirect('/login');

        $data['page_title'] = "Dashboard";
        if(Session::get('role_id')) {
            return view('dashboard')->with(compact('data'));
        } else {
            return view('home')->with(compact('data'));
        }    
    }
}