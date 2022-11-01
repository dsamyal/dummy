<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
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
    public function index()
    {
		/*$userrole = \Auth::user()->role;
		if($userrole != 'admin'){
            return redirect('/index');
		} else {
			\Auth::logout();
			return redirect(route('login'));
        }
        return view('home');*/

        $userrole = \Auth::user()->role;
        if($userrole != 'admin'){
            return redirect('/index');
        } else if($userrole == 'admin'){
            $visitor_data = DB::table('country_wise_visitor_data')
                        ->select('*')
                        ->get();

            return view('home', ['visitor_data' => $visitor_data]);

            // return view('home');
        } else {
            \Auth::logout();
            return redirect(route('login'));
        }
        return view('home');
    }


    public function index2()
    {
        /*$userrole = \Auth::user()->role;
        if($userrole != 'admin'){
            return redirect('/index');
        } else {
            \Auth::logout();
            return redirect(route('login'));
        }
        return view('home');*/

        $userrole = \Auth::user()->role;
        if($userrole != 'admin'){
            return redirect('/index');
        } else if($userrole == 'admin'){
            $visitor_data = DB::table('country_wise_visitor_data')
                        ->select('*')
                        ->get();

            return view('home', ['visitor_data' => $visitor_data]);

            // return view('home');
        } else {
            \Auth::logout();
            return redirect(route('login'));
        }
        return view('home');
    }
      
}
