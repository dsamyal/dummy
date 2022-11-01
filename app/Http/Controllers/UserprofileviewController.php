<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UserprofileviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    public function index() { 
        $userrole = \Auth::user()->role;
        if($userrole != 'admin'){
            return redirect('/index');
        } else if($userrole == 'admin'){
            $visitor_data = DB::table('user_profile_view_data')->select('*')->get();
            return view('user_profile_views', ['visitor_data' => $visitor_data]);
        } else {
            \Auth::logout();
            return redirect(route('login'));
        }
        return view('user_profile_views');
    }

      
}
