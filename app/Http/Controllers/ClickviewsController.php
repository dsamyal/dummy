<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ClickviewsController extends Controller
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
            
            
            $visitor_data = DB::table('country_wise_visitor_data')->select ( "id","post_id","post_url", DB::raw("COUNT('post_id') as count_click") )->groupby('post_id')->get();
            return view('clickviews', ['visitor_data' => $visitor_data]);
        } else {
            \Auth::logout();
            return redirect(route('login'));
        }
        return view('clickviews');
    }  
}
