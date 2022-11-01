<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;

class ContactUsController extends Controller
{

    public function Commingpages(){
        return view('welcomelogo');
    }

    public function Logout(){
        Auth::logout();
        return Redirect::to('/index');
    }
    public function AddProductPage(){
       return view('productBlank.add-product');
    }

    public  function EmailThankYouPage(){
        return view('EmailThankYou');
    }
    public function ContactUsEmail(Request $request){

//        dd($request->all());
        $email=$request->email;
        $name=$request->name;

//        $message="Email Send SuccessFully";

//        $data = array('text' => $message, 'email' => $email);

        try {


            Mail::send('ContactUsEmail', ['name' => $name, 'email' => $email], function ($message) {

                $message->to('mina@ikennolim.com')->subject('Your Product Add SuccessFully..!');
            });


            return redirect('thank-you')->with('message', 'You have successfully sent an email to the admin!');


        }catch(Exception $error) {
            return $error->getMessage();
        }
    }
}
