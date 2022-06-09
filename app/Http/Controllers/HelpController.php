<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cookie;
use Session;
use DB;

class HelpController extends Controller
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

        $data['page_title'] = "Help";
        return view('help')->with(compact('data'));
    }
}