<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use App\Followers;
use App\UserDetail;
use App\PostComment;
use App\PostLike;
use App\Post;
use App\Message;
use App\PostDetail;
use App\FollowHashTag;
use App\SharePost;
use App\ShopProduct;
use App\ShopProductDetail;
use App\ActivityLog;
use App\ActivityTime;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use DB;
use App\Status;
use App\StatusView;
use App\Invitation;
use App\ShopProductMeta;
use Illuminate\Support\Facades\Crypt;
use File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;

class RegisterController extends BaseController {

    private $limit = '5';
    private $limit_for_discover = '8';
    private $limit_for_search = '10';
    /*********************REGISTER***********************/

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
                    'username' => 'required',
                    'bdate' => 'required',
                    'email' => 'required|unique:users|email',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }


        $checkname = User::where('tagname', $request->tagname)->first();
        // print_r($checkname);
        // die;
        if($checkname)
        {
            return response()->json(["code" => 100, "message" => "The username has already been taken"]);
            // return $this->sendResponse($code, 'The username has already been taken.');
        }
        else 
        {
            $saUsers = new User();
            $saUsers->name = $request->username;
            $saUsers->full_name = $request->full_name;
            $saUsers->email = $request->email;
            $saUsers->app_token = $request->app_token;
            $saUsers->bdate = $request->bdate;
            $saUsers->tagname = $request->tagname;
            $saUsers->password = Hash::make($request->password);
            $saUsers->city=$request->city;
            $saUsers->country=$request->country;
            $saUsers->save();

            $success['token'] = $saUsers->createToken('MyApp')->accessToken;
            $success['name'] = $saUsers->name;
            $success['email'] = $saUsers->email;
            $success['email_verified_at'] = $saUsers->email_verified_at;
            $success['created_at'] = $saUsers->created_at;
            $success['updated_at'] = $saUsers->updated_at;
            $success['tagname'] = $saUsers->tagname;
            $success['bdate'] = $saUsers->bdate;
            $success['user_id'] = $saUsers->id;
            
            for($i=1;$i<4;$i++)
            {
                $follow_statuses[] = array('follewers_id' => $saUsers->id,
                                        'user_id' => $saUsers->id,
                                        'type' => $i
                                        );
                                        
            }
            $save_follow_statuses = DB::table('follow_statuses')->insert($follow_statuses);
            
            
            $data['name'] = $saUsers->name;
            $ssUsrl = url('email-veryfied?id=' . base64_encode($success['user_id']));
            $to_email = $request->email;
            $subject = 'ARTfora email verification';
			$message['to_email'] = $to_email;
			$message['ssUsrl'] = $ssUsrl;
			$message['view'] = "register";
            			
            $this->custom_email($to_email, $from=NULL, $subject, $message);


            //        Mail::send('Mail.gmailview', $success, function($message) use($success) {
            //            $message->to($success['email'], $success['name'])
            //                    ->subject('Artfora mail Conformation');
            //            $message->from('spyveb1234@gmail.com', ' SpyVeb');
            //        });
            return $this->sendResponse($success, 'User register successfully.');
        }
    }

    public function user_register(Request $request) {
        $validator = Validator::make($request->all(), [
                    'username' => 'required',
                    'email' => 'required|unique:users|email',
                    'password' => ['required', 'min:8', 'regex:/[A-Z]/','regex:/[0-9]/'],
                    'c_password' => 'required|same:password',
        ],[
            'password.min' => 'Minimum: 8 characters, 1 number and 1 capital letter.',
            'password.regex' => 'Minimum: 8 characters, 1 number and 1 capital letter.',
            'c_password.required' => 'The confirm password field is required.',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();

            return response()->json(["code" => 100, "message" => $error->messages()]);
        }

        
        $username = $request->username;
        $email = $request->email;
        $password = Hash::make($request->password);
        $radio_filter = $request->radio_filter;

        $inserted = DB::table('users')->insertGetId([
                                    'name'=>$username, 
                                    'email'=>$email, 
                                    'password'=>$password
                                ]);

        $user_id = $inserted;

        DB::table('user_detail')->insert([
            'user_id' => $user_id,
            'filter' => $radio_filter
        ]);

        $ssUsrl = url('email-veryfied?id=' . base64_encode($user_id).'&ver=1');
        $to_email = $email;
        $subject = 'ARTfora email verification';
        $message['to_email'] = $to_email;
        $message['ssUsrl'] = $ssUsrl;
        $message['view'] = "register";
                    
        $this->custom_email($to_email, $from=NULL, $subject, $message);

    }

    public function reSendEmail(Request $request) {

        $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();

            return $this->sendError($error->first(), array());
        }

        $obFindUser = User::where('email', $request->email)->first();
        if (empty($obFindUser)) {
            return $this->sendError('Email not found', array());
        } else {
            $success['token'] = $obFindUser->createToken('MyApp')->accessToken;
            $success['name'] = $obFindUser->name;
            $success['email'] = $obFindUser->email;
            $success['email_verified_at'] = $obFindUser->email_verified_at;
            $success['created_at'] = $obFindUser->created_at;
            $success['updated_at'] = $obFindUser->updated_at;
            $success['bdate'] = $obFindUser->bdate;
            $success['id'] = $obFindUser->id;

//            Mail::send('Mail.gmailview', $success, function($message) use($success) {
//                $message->to($success['email'], $success['name'])
//                        ->subject('Artfora mail Conformation');
//                $message->from('spyveb1234@gmail.com', ' SpyVeb');
//            });
            $ssUsrl = url('email-veryfied?id=' . base64_encode($success['id']));
            $to_email = $success['email'];
            $subject = 'ARTfora email verification';
            
			$message['to_email'] = $to_email;
			$message['ssUsrl'] = $ssUsrl;
			$message['view'] = "register";			
            $this->custom_email($to_email, $from=NULL, $subject, $message);
            
            $data['email'] = $obFindUser->email;
            return $this->sendResponse($data, 'Email sent');
        }
    }

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email' =>'required|email',
        ]);
        if($validator->fails()){
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        else
        {
            $user_id = User::where('email', $request->email)->pluck('id')->first();
            if(!empty($user_id))
            {
                $to = $request->email;
                $request_id = DB::table('forget_pass_request')->insertGetId(['email' => $to, 'date_time' => Carbon::now()]);
                $encrypted = Crypt::encryptString($request_id);
                $url = url('/')."/new_pass?id=".$encrypted;
                $subject = "Password Reset";
			
				$message['ssUsrl'] = $url;
				$message['view'] = "forgot";
			
                $this->custom_email($to, $from=NULL, $subject, $message);
                
                return $this->sendResponse(array(), 'Forgot Password email is sent.');
            }
            else
            {
                return $this->sendError('Email does not exists.', array());
            }
        }
    }
    
    public function checkRegisterEmail(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required|unique:users|email',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();

            return $this->sendError($error->first(), array());
        }
        return $this->sendResponse($request->email, 'Email not registred');
    }

    public function emailVeryfied(Request $request) 
	{
		$snId = $request->get('id');
        $snDecodeId = base64_decode($snId);
        $snCheckUser = User::findorFail($snDecodeId);
        if (!empty($snCheckUser)) {
            $snCheckUser->email_verified_at = date('Y-m-d H:i:s');
            $snCheckUser->save();
            // return view('verifyEmail');
            return view('verified_reg');   
        } else {
            echo '<center>User Not Found</center>';
            die;
        }
    }
	
	public function email_verify_api(Request $request) 
	{
		$snId = $request->get('id');
        $snDecodeId = base64_decode($snId);
        $snCheckUser = User::findorFail($snDecodeId);
        if (!empty($snCheckUser)) {
            $snCheckUser->email_verified_at = date('Y-m-d H:i:s');
            $snCheckUser->save();
            // return view('verifyEmail');
            $response = [
				'success' => true,
				'message' => 'Email verified success'
			  ];
			  return response()->json($response, 200);
		}
		else
		{
			$response = [
			'success' => true,
			'message' => 'Email Verified success'
		  ];
		  return response()->json($response, 200);            
		}
    }
	
	
	public function deleteaccountVeryfied($snId) {

        $snDecodeId = base64_decode($snId);
		$userdata = explode(":",$snDecodeId);
		
		$snCheckUser = User::where('id',$userdata[0])->where('email',$userdata[1])->first();
		if(isset($snCheckUser) && $snCheckUser->delete_email_verified_at == null){
			$snCheckUser->delete_email_verified_at = date('Y-m-d H:i:s', strtotime("+30 days"));
			$snCheckUser->status = 3;
            $snCheckUser->save();
            return view('verified_delete_acccount');
		}
        else if(isset($snCheckUser->delete_email_verified_at) && !empty($snCheckUser->delete_email_verified_at)){
            //$snCheckUser->delete_email_verified_at = date('Y-m-d H:i:s');
            //$snCheckUser->save();
            return view('already_verified_delete_acccount');   
        } else {
            echo '<center>User Not Found</center>';
            die;
        }
    }
	
	public function reactiveaccountVeryfied($snId) 
	{
        $snDecodeId = base64_decode($snId);
		$userdata = explode(":",$snDecodeId);
		
        $snCheckUser = User::where('id',$userdata[0])->where('email',$userdata[1])->first();
		if(isset($snCheckUser) && !empty($snCheckUser->delete_email_verified_at)){
			$snCheckUser->delete_email_verified_at = null;
			$snCheckUser->status = 1;
            $snCheckUser->save();
            return view('verified_reactive_acccount');
		}
        else if(empty($snCheckUser->delete_email_verified_at)){
            //$snCheckUser->delete_email_verified_at = date('Y-m-d H:i:s');
            //$snCheckUser->save();
            return view('already_verified_reactive_acccount');   
        } else {
            echo '<center>User Not Found</center>';
            die;
        }
    }
	
	
	public function reactiveAccount(Request $request) 
	{
		$validator = Validator::make($request->all(),[
            'user_id' =>'required',
            'email' =>'required|email',
        ]);
        if($validator->fails()){
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        else
        {	
			$snCheckUser = User::where('id',$request->user_id)->where('email',$request->email)->first();
			if(isset($snCheckUser) && !empty($snCheckUser->delete_email_verified_at)){
				$snCheckUser->delete_email_verified_at = null;
				$snCheckUser->status = 1;
				$snCheckUser->save();
			}
			return $this->sendResponse([], 'Account reactived');
		}
    }	
	
	public function updateemailVeryfied(Request $request) {

		$snId = $request->get('id');
        $snDecodeId = base64_decode($snId);
        $snCheckUser = User::find($snDecodeId);
        if (!empty($snCheckUser)) {
			if (isset($snCheckUser->verify_email) && !empty($snCheckUser->verify_email)) {
				$email = $snCheckUser->verify_email;
				$snCheckUser->email = $email;
				$snCheckUser->verify_email = "";
				$snCheckUser->verify_email_flag = 1;
				$snCheckUser->save();
				// return view('verifyEmail');
				return view('verified_reg');   
			} else{
				return view('verified_reg');
			}
        } else {
            echo '<center>User Not Found</center>';
            die;
        }
    }
	
	public function update_email_api_Veryfied(Request $request) {

		$snId = $request->get('id');
        $snDecodeId = base64_decode($snId);
        $snCheckUser = User::find($snDecodeId);
        if (!empty($snCheckUser)) {
			if (isset($snCheckUser->verify_email) && !empty($snCheckUser->verify_email)) {
				$email = $snCheckUser->verify_email;
				$snCheckUser->email = $email;
				$snCheckUser->verify_email = "";
				$snCheckUser->verify_email_flag = 1;
				$snCheckUser->save();
				// return view('verifyEmail');
				return $this->sendResponse([], 'Email updated');  
			} else{
				return $this->sendResponse([], 'Email updated');
			}
        } else {
            echo '<center>User Not Found</center>';
            die;
        }
    }

    public function checkVerifyEmail(Request $request) {
        $validator = Validator::make($request->all(), [
                    'id' => 'required|',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();

            return $this->sendError($error->first(), array());
        }
        $snCheckUser = User::whereNotNull('email_verified_at')->where('id', $request->id)->first();
        if (!empty($snCheckUser)) {
            $saData['id'] = $request->id;
            $saData['screen_code'] = 222;
            return $this->sendResponse($saData, 'Email Verifed');
        } else {
            $saData['id'] = $request->id;
            $saData['screen_code'] = 111;
            return $this->sendError('Email not Verifed', $saData);
        }
    }

    public function registerStepTwo(Request $request) {
        
        $urls = $request->urls;
        $user_id = $request->user_id;
        
        // ADD USER URLS
        if($request->has('urls'))
        {
            array_multisort(array_column($urls, 'priority'), SORT_ASC, $urls);
            $urls = collect($urls)->map(function ($item, $key) use ($user_id) {
                $item['user_id'] = $user_id;
                return $item;
            });
            
            $urls = $urls->map(function ($item) {
                return array_only($item, ['url', 'type', 'user_id']);
            });
            
            $urls = $urls->toArray();

            DB::table('user_urls')->insert($urls);    
        }
        // ADD USER URLS

        $check_user_detail = UserDetail::where('user_id', $user_id)->first();
        if(!$check_user_detail)
        {
            if (!empty($request['profile_image'])) {
                if ($request->hasFile('profile_image')) {
                    $ssProfileImage = $request->file('profile_image');
                    $name1 = time() . '1' . $ssProfileImage->getClientOriginalName();
                    $destinationPath1 = public_path('/images/profile_image/');
                    $ssProfileImage->move($destinationPath1, $name1);
                }
            } else {
                $name1 = null;
            }
    
            if (!empty($request['privete_profile'])) {
                if ($request->hasFile('privete_profile')) {
                    $ssProfileImage = $request->file('privete_profile');
                    $name2 = time() . '2' . $ssProfileImage->getClientOriginalName();
                    $destinationPath1 = public_path('/images/profile_image/');
                    $ssProfileImage->move($destinationPath1, $name2);
                }
            } else {
                $name2 = null;
            }
            $saUser = User::where('id', $user_id)->first();
            $saUserDetail = new UserDetail();
            $saUserDetail->user_id = $user_id;
            $saUserDetail->filter = $request['filter'];
            // $saUserDetail->birth_date = date('Y-m-d', strtotime($request['birth_date']));
            $saUserDetail->category = $request['category'];
            $saUserDetail->private_description = $request['private_description'];
            $saUserDetail->privete_profile_url = 'https://artfora.net/images/profile_image/' . $name2;
            $saUserDetail->profile_image_url = 'https://artfora.net/images/profile_image/' . $name1;
            $saUserDetail->description = $request['description'];
            $saUserDetail->save();
            $saUserDetail->name = $saUser->name;
            $saUserDetail->email = $saUser->email;
            $saUserDetail->email_verified_at = $saUser->email_verified_at;
            if (!empty($request['interest_group'])) {
                $saExpload = explode('.', $request['interest_group']);
                foreach ($saExpload AS $saInterest) {
                    DB::table('interest_group')->insert(
                            ['user_id' => $user_id, 'interest_name' => trim($saInterest)]
                    );
                }
            }
        }
        
        $notification_data = array('tag_notification' => $request->tag_notification,
                    'message_notification' => $request->message_notification,
                    'follow_notification' => $request->follow_notification,
                    'unfollow_notification' => $request->unfollow_notification,
                    'like_post_notification' => $request->like_post_notification,
                    'comment_post_notification' => $request->comment_post_notification,
                    'private_profile' => $request->private_profile
                    );
                    
        $update_notification_user = User::where('id', $user_id)->update($notification_data);
        
        if(!$check_user_detail)
        {
            return $this->sendResponse($saUserDetail, 'User register successfully.');
        }
        else
        {
            return $this->sendError('User detail already added.', array());
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
                    'username' => 'required',
                    'password' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $ssUrl = url('images/profile_image');
            $snUserCheck = DB::table('users')->select('users.id', 'users.status', 'users.tagname', 'users.bdate', 'users.name', 'users.email', 'users.password', 'users.country', 'users.email_verified_at', 'profile_image_url', 'user_detail.user_id as user_detail_id','user_detail.filter','users.role')
                    ->where('users.email', trim($request->username))
                    ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                    ->first();


            if (!empty($snUserCheck)) {
				
				$saData['is_blocked'] = false;
                if($snUserCheck->status == 2)
                {
					$saData['is_blocked'] = true;
                   //return $this->sendError('User is blocked by admin.', array()); 
                }
                if (Hash::check($request->password, $snUserCheck->password)) {
                    $saData['user_id'] = $snUserCheck->id;
                    $saData['name'] = $snUserCheck->name;
                    $saData['tagname'] = $snUserCheck->tagname;
                    $saData['email'] = $snUserCheck->email;
                    $saData['bdate'] = $snUserCheck->bdate;
                    $saData['country'] = $snUserCheck->country;
                    $saData['profile_image_url'] = $snUserCheck->profile_image_url;
                    $saData['email_verified_at'] = $snUserCheck->email_verified_at;
                    $saData['role'] = $snUserCheck->role;
                    $saData['token'] = '';
										
					/* $user_id = 1;
					$ShopProduct = ShopProduct::with(['shop_product_files'])
					->where([['user_id', $user_id], ['status', 1]])
					->orderBy('shop_products.id','DESC')
					->first(); */
					
					$product_info = ShopProduct::with('shop_product_files')->where('filter_id', 1);
					$product_info->where('status', 1);
					$product_info->where('shop_products.deleted_at','=', NULL);
					
					$product_info->orderBy('id', 'DESC');
					$product_info = $product_info->first();
					$saData['pages_tab_image'] = "";
					
					if(isset($product_info->shop_product_files[0]) && !empty($product_info->shop_product_files[0]->thumb)){
						$saData['pages_tab_image'] = "https://artfora.net/images/post/new_images/".$product_info->shop_product_files[0]->thumb;
					}
					
					$get_post_filter = DB::table('post')
                                ->select('post_files.*','post.*')
                                ->join('post_files', 'post_files.post_id', '=', 'post.id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('follow_statuses.user_id',$snUserCheck->id)
								->where('post.filter_id',1)
                                ->orderBy('post.id','DESC')
                                ->first(); 
					$saData['search_tab_image'] = "";
					if(isset($get_post_filter->file_name) && !empty($get_post_filter->file_name) && $get_post_filter->file_type == 1){
						$saData['search_tab_image'] = "https://artfora.net/images/post/new_images/".$get_post_filter->file_name;
					}
					if(isset($get_post_filter->thumb) && !empty($get_post_filter->thumb) && $get_post_filter->file_type == 2){
						$saData['search_tab_image'] = "https://artfora.net/images/post/new_images/thumb/".$get_post_filter->thumb;
					}
                    if(isset($get_post_filter->video_thumbnail) && !empty($get_post_filter->video_thumbnail) && $get_post_filter->file_type == 3){
                                            $saData['search_tab_image'] = $get_post_filter->video_thumbnail;
                                        }
								
					$get_post = DB::table('post')
                                ->select('post_files.*','post.*')
                                ->join('post_files', 'post_files.post_id', '=', 'post.id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('follow_statuses.user_id',$snUserCheck->id)
                                ->orderBy('post.id','DESC')
                                ->first(); 			
					
                    $saData['feed_tab_image'] = "";
					if(isset($get_post->file_name) && !empty($get_post->file_name) && $get_post->file_type == 1){	
						$saData['feed_tab_image'] = "https://artfora.net/images/post/new_images/".$get_post->file_name??"";
					}
					
					if(isset($get_post->thumb) && !empty($get_post->thumb) && $get_post->file_type == 2){	
						$saData['feed_tab_image'] = "https://artfora.net/images/post/new_images/thumb/".$get_post->thumb??"";
					}
                    if(isset($get_post->video_thumbnail) && !empty($get_post->video_thumbnail) && $get_post->file_type == 3){
                                            $saData['feed_tab_image'] = $get_post->video_thumbnail??"";
                  }
					$profilepic = Message::leftjoin('user_detail','user_detail.user_id','messages.receiver_id')->select('messages.id','user_detail.profile_image_url')->where('sender_id', $snUserCheck->id)->where('delete_status','!=',$snUserCheck->id)->orderBy('id','DESC')->first();
					
                    $saData['message_tab_image'] = $profilepic->profile_image_url??"";
					
                    $saData['filter'] = $snUserCheck->filter;
                    if ($snUserCheck->email_verified_at == NULL) {
                        $saData['screen_code'] = '111';
                    } elseif (empty($snUserCheck->user_detail_id)) {
                        $saData['screen_code'] = '222';
                    } else {
                        $saData['screen_code'] = '000';
                    }
					$saData['is_reactive'] = false;
					if($snUserCheck->status == 3)
					{
					   $saData['is_reactive'] = true;
					}
                    DB::table('users')->where('email',$request->username)->update(['app_token'=>$request->app_token]);
                    return $this->sendResponse($saData, 'User login successfully.');
                } 
                else {
                    return $this->sendError('Invalid Credentials', array());
                }
            } else {
                return $this->sendError('User not Found', array());
            }
        }
    }

	public function send_tag_people(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'user_tags' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			if (!empty($request['user_tags'])) {
                $saExploadTags = explode(',',$request['user_tags']);
				//$saExploadTags = json_decode($request['user_tags']);				
                $get_tag_user = User::whereIn('tagname', $saExploadTags)->where('tag_notification',1)->get();
                $tokens = [];
                foreach($get_tag_user As $get_user) 
				{
                    if(isset($get_user->app_token) && !empty($get_user->app_token))
                    {
                        $tokens[] = $get_user->app_token;
                    }
                }
				if(count($tokens) > 0){
					$title='New Notification';
					$body='You have been tagged';
					$this->send_push($title, $body, $tokens);
				}
            }
			return $this->sendResponse([], 'push send successfully.');
		}
	}
	
	
/*********************CREATE POST***************************/

    public function createPost(Request $request) {
    
        $interest_group = $request->interest_group;
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                //    'image' => 'required|mimes:jpeg,jpg,png,gif',
                //  'video' => 'mimes:mp4,mov,ogg',
                //  'add_detail' => 'required',
                    'filter' => 'required',
        ]);


            /*******************************************************************************************/
       
   //          $files = $request->file('attachment');   
   //          $array=0;
   //          $file_names=array();
   //          foreach($files as $file){
            //  $filename = $file->getClientOriginalName();
            //      $ssProfileImage = $file;
            //      $ssImage=$file_names[] = time() . $array . $ssProfileImage->getClientOriginalName();
            //      $destinationPath1 = public_path('/images/post/new_images');
   //               $ssProfileImage->move($destinationPath1, $ssImage);              
   //               $array=$array+1;             
            // }
            // print_r($file_names);
            // die;
            /*******************************************************************************************/           

			if ($validator->fails()) {
				$error = $validator->messages();
				return $this->sendError($error->first(), array());
			} else {

            // if (!empty($request['image'])) {
            //     if ($request->hasFile('image')) {
            //         $ssProfileImage = $request->file('image');
            //         $ssImage = time() . '1' . $ssProfileImage->getClientOriginalName();
            //         $destinationPath1 = public_path('/images/post/Image');
            //         $ssProfileImage->move($destinationPath1, $ssImage);
            //     }
            // } else {
            //     $ssImage = null;
            // }

            // if (!empty($request['video'])) {
            //     if ($request->hasFile('video')) {
            //         $ssProfileVideo = $request->file('video');
            //         $ssVideo = time() . '1' . $ssProfileVideo->getClientOriginalName();
            //         $destinationPath1 = public_path('/images/post/Video');
            //         $ssProfileVideo->move($destinationPath1, $ssVideo);
            //     }
            // } else {
            //     $ssVideo = null;
            // }

			$videos = isset($request['videos']) ? $request['videos'] : array();
            $images = isset($request['images']) ? $request['images'] : array();
			
			if(!empty($images)){
				for($i=0; $i<count($images); $i++)
				{
					$image = $images[$i][0];
					if($image->getSize() > 134217728){
						$error = "Maximum image size to upload is 128MB";
						return $this->sendError($error, array());
					}
				}
			}
			if(!empty($videos)){
				for($i=0; $i<count($videos); $i++)
				{
					$image = $videos[$i][0];
					if($image->getSize() > 134217728){
						$error = "Maximum thumbnail size to upload is 128MB";
						return $this->sendError($error, array());
					}
					$video = $videos[$i][1];
					if($video->getSize() > 134217728){
						$error = "Maximum video size to upload is 128MB";
						return $this->sendError($error, array());
					}
				}
			}
			
            $obNewPost = new \App\Post();
            $obNewPost->user_id = $request['user_id'];
            $obNewPost->video_thumbnail = $request['video_thumbnail'];
			// $obNewPost->image = $ssImage;
			//  $obNewPost->video = $ssVideo;
            $obNewPost->owner_name = $request['owner_name'];
            $obNewPost->decription = $request['decription'];
            $obNewPost->tag = $request['tag'];
            $obNewPost->filter_id = $request['filter'];
            $obNewPost->type = $request['type'];
            // $obNewPost->is_flipped = $request['is_flipped'];
            // $obNewPost->is_rotated = $request['is_rotated'];
            $obNewPost->save();
            $post_id=$obNewPost->id;
            //      $post_id='25851';
            /******************************************************************************************************/ 
            $counter=0;
            if($request['images']){
                         
                $array=$request['images'];  
                for($i=0;$i<count($array);$i++){
                    if(!empty($array[$i][0])){
                        $type=1;
                        /* $ssImage = time() . $counter . $array[$i][0]->getClientOriginalName();
                        $destinationPath1 = public_path('/images/post/new_images');
                        $array[$i][0]->move($destinationPath1, $ssImage);
                        $destinationPath1 = public_path('/images/post/new_images/thumb');
                        $array[$i][1]->move($destinationPath1, $ssImage); */                    
						
						$image = $array[$i][0];
						$randomname = time().rand().$counter;
						$ssImage = $randomname .'.'.$image->getClientOriginalExtension();
					 
						$destinationPath = public_path('/images/post/new_images/thumb');
						$img = Image::make($image->getRealPath());
						$img->resize(640, 640, function ($constraint) {
							$constraint->aspectRatio();
						})->save($destinationPath.'/'.$ssImage);
						
						$destinationPath = public_path('/images/post/new_images/thumbtab');
						$img2 = Image::make($image->getRealPath());
						$img2->resize(900, 900, function ($constraint) {
							$constraint->aspectRatio();
						})->save($destinationPath.'/'.$ssImage);
				   
						$destinationPath = public_path('/images/post/new_images');
						$image->move($destinationPath, $ssImage);
						
                    }
                    if(!empty($array[$i][11])){
                        $type=1;
                        /* $ssImage = time() . $counter . $array[$i][0]->getClientOriginalName();
                        $destinationPath1 = public_path('/images/post/new_images');
                        $array[$i][0]->move($destinationPath1, $ssImage);
                        $destinationPath1 = public_path('/images/post/new_images/thumb');
                        $array[$i][1]->move($destinationPath1, $ssImage); */
                        
                        $imageRes = $array[$i][11];
                        $randomnameRes = time().rand().$counter;
                        $ssImageRes = $randomnameRes .'.'.$imageRes->getClientOriginalExtension();
                     
                        $destinationPathRes = public_path('/images/post/new_images/thumb');
                        $imgRes = Image::make($imageRes->getRealPath());
                        $imgRes->resize(640, 640, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($destinationPathRes.'/'.$ssImageRes);
                   
                        $destinationPathRes = public_path('/images/post/new_images');
                        $imageRes->move($destinationPathRes, $ssImageRes);
                        
                    }
                    $flipped=$array[$i][2];
                    $is_rotated=$array[$i][3];
                    $priority=(int)$array[$i][4];

                    $width=$array[$i][5];
                    $height=$array[$i][6];
                    $x=$array[$i][7];
                    $y=$array[$i][8];
                    $tag_users=isset($array[$i][9])?$array[$i][9]:'';
                    $user_ids=isset($array[$i][10])?$array[$i][10]:'';

                    $post_files_data=array(
                        "file_type"=>'1',
                        "file_name"=>$ssImage,
                        "resized_file_name"=>$ssImageRes,
						"video_url"=>"",
                        "post_id"=>$post_id,
                        "is_flipped"=>$flipped,
                        "is_rotated"=>$is_rotated,
                        "thumb"=>$ssImage,
                        "priority"=>$priority,
                        "file_width"=>$width,
                        "file_height"=>$height,
                        "x"=>$x,
                        "y"=>$y,
                        "tag_users"=>$tag_users,
                        "user_ids"=>$user_ids
                    );
                  DB::table('post_files')->insert($post_files_data); 
                  $counter=$counter+1;              
                }
            }

            if($request['videos']){
                 $counter=$counter;            
                $array=$request['videos'];  
                for($i=0;$i<count($array);$i++){
                    if(!empty($array[$i][0])){
                        $type=2;
                        $ssImage1 = time().rand().$counter.$array[$i][0]->getClientOriginalName();
                        $destinationPath1 = public_path('/images/post/new_images');
                        $array[$i][0]->move($destinationPath1, $ssImage1);
                        $ssImage = time().rand().$counter.$array[$i][1]->getClientOriginalName();
                        $destinationPath1 = public_path('/images/post/new_images/thumb');
                        $array[$i][1]->move($destinationPath1, $ssImage); 		
                    }
                    if(!empty($array[$i][11])){
                        $type=2;
                        $ssImage1Res = time().rand().$counter.$array[$i][11]->getClientOriginalName();
                        $destinationPath1Res = public_path('/images/post/new_images');
                        $array[$i][11]->move($destinationPath1Res, $ssImage1Res);
                    }
                    $flipped=$array[$i][2];
                    $is_rotated=$array[$i][3];
                    $priority=(int)$array[$i][4];
                    $width=$array[$i][5];
                    $height=$array[$i][6];
					$x=isset($array[$i][7])?$array[$i][7]:json_encode([]);
                    $y=isset($array[$i][8])?$array[$i][8]:json_encode([]);
					$tag_users=isset($array[$i][9])?$array[$i][9]:json_encode([]);
                    $user_ids=isset($array[$i][10])?$array[$i][10]:json_encode([]);

                    $post_files_data=array(
                        "file_type"=>'2',
                        "file_name"=>$ssImage1,
                        "resized_file_name"=>$ssImage1Res,
						"video_url"=>"",
                        "post_id"=>$post_id,
                        "is_flipped"=>$flipped,
                        "is_rotated"=>$is_rotated,
                        "thumb"=>$ssImage,
                        "priority"=>$priority,
                        "file_width"=>$width,
                        "file_height"=>$height,
						"x"=>$x,
                        "y"=>$y,
						"tag_users"=>$tag_users,
                        "user_ids"=>$user_ids
                    );
                  DB::table('post_files')->insert($post_files_data); 
                  $counter=$counter+1;              
                }
            }
			
			if(isset($request['videourl']) && !empty($request['videourl'])){
                
				$post_files_data=array(
					"file_type"=>'3',
					"file_name"=>"",
                    "resized_file_name"=>"",
					"video_url"=>$request['videourl'],
					"post_id"=>$post_id,
					"is_flipped"=>0,
					"is_rotated"=>0,
					"thumb"=>"",
					"priority"=>0,
					"file_width"=>0,
					"file_height"=>0,
					"x"=>json_encode([]),
					"y"=>json_encode([]),
					"tag_users"=>json_encode([]),
					"user_ids"=>json_encode([])
				);
                DB::table('post_files')->insert($post_files_data); 
            }

          /******************************************************************************************************/


            //If hash taged 
            if (!empty($request['tag'])) {
                $hashExploadTags = explode(',', $request['tag']);
                foreach ($hashExploadTags As $hashTags) {
                    if($hashTags!=""){
                        $hashStrg = preg_replace('/\s+/', '', $hashTags);                   
                        if($hashStrg[0] == "#"){                        
                            $hashWord = ltrim ($hashStrg,'#');                        
                            $findhashWord = DB::table('hash_tags')->where('hash_tag_name', $hashWord)->first();       
                            if(!$findhashWord){
                                 DB::table('hash_tags')->insert(['hash_tag_name' => $hashWord, 'status' => 1, 'created_at' => Carbon::now()]);
                            }
                        }
                     }   
                }
            }
            
			/***********************************************************************/

				if($request['tag_list']){
					$tag_list=$request['tag_list'];
					for($i=0;$i<count($tag_list);$i++){
						$data_array=array(
							'post_id'=>$obNewPost->id,
							'user_id'=>$tag_list[$i]
							);
						DB::table('post_tags')->insertGetId($data_array);
					}
				}

			/***********************************************************************/
            //If taged user
            if (!empty($request['user_tags'])) {
                $saExploadTags = explode(',', $request['user_tags']);
                // $get_user = User::find($request['user_id']);
                // $title='New Post';
                // $body=$get_user->name.' has tagged you in a post.';
                    
                foreach ($saExploadTags As $snTags) {
					$tag_user = User::where('id', $request['user_id'])->first();
					if($request['user_id'] != $snTags){
						DB::table('user_tags')->insert(['post_id' => $obNewPost->id, 'user_id' => $request['user_id'], 'tag_user' => $snTags]);
						// $tokens = User::where('id', $snTags)->pluck('app_token');
						// $this->send_push($title, $body, $tokens, $request['user_id'], $obNewPost->id);
						$get_user = User::where('id', $snTags)->where('tag_notification',1)->first();
						//$title = $request['owner_name'];
						$title = $tag_user->tagname;
						$body ='has tagged you in a post.';
						if($get_user)
						{
							$tokens[] = $get_user->app_token;
							$this->send_push($title, $body, $tokens);
						}
						unset($tokens);
					}
                }
            }
            
            $saData['post_id'] = $obNewPost->id;
            if (!empty($request['add_detail'])) {
                
                foreach (json_decode($request['add_detail']) AS $obDetail) {
                    
                    $obPostDetail = new \App\PostDetail();
                    $obPostDetail->post_id = $obNewPost->id;
                    $obPostDetail->title = $obDetail->title;
                    $obPostDetail->description = $obDetail->description;
                    $obPostDetail->save();
                }
            }


            if (!empty($interest_group)) 
            {
            
                $interest_group_array = explode('.', $interest_group);
                
                // $new_interest = array();
                
                for($m=0; $m < count($interest_group_array); $m++)
                {
                    $interest_post[] = array(
                                            'user_id' => $request['user_id'],
                                            'interest_name' => $interest_group_array[$m],
                                            'post_id' => $obNewPost->id,
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now(),
                                        );
                }
                
                $post_interest = DB::table('interest_group_posts')->insert($interest_post);
            }
            
            
            // CHECK IF ANY USER FOLLOW CURRENT USER THEN SEND NOTIFICATION TO THEM
            $check_active_notification_users = DB::table('notification_statuses as ns')
                                                    ->join('users', 'users.id', '=', 'ns.user_id')
                                                    ->where([['ns.type', 2], ['ns.follewers_id', $request['user_id']]])
                                                    ->select('users.id', 'users.app_token')->get();
            if(count($check_active_notification_users) > 0)
            {
                $tokens = array();
                $get_user = DB::table('users')
                            ->join('user_detail', 'user_detail.user_id', '=', 'users.id')
                            ->where('users.id', $request['user_id'])
                            ->select('users.name', 'user_detail.profile_image_url')
                            ->first();
                $title = 'New Post';
                $body = $get_user->name.' has created a new post.';
                
                foreach($check_active_notification_users as $active_user)
                {
                    $tokens[] = $active_user->app_token;
                }
                
                $this->send_push_with_data($title, $body, $tokens, $request['user_id'], $obNewPost->id, $get_user->name, $get_user->profile_image_url);
            }
            // CHECK IF ANY USER FOLLOW CURRENT USER THEN SEND NOTIFICATION TO THEM
            return $this->sendResponse($saData, 'Post Created successfully.');
        }
    }
	
	
	public function rePost(Request $request) 
	{
			$interest_group = $request->interest_group;
			$validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'feed_id' => 'required',
                'feed_type' => 'required',
			]);

			if ($validator->fails()) {
				$error = $validator->messages();
				return $this->sendError($error->first(), array());
			} else {
			
			if($request->feed_type == '1'){
				$clonepost = Post::where('id',$request['feed_id'])->first();
				if($clonepost)
				{
					$obNewPost = new \App\SharePost();
					$obNewPost->user_id = $request['user_id'];
					$obNewPost->feed_type = $request->feed_type;
					$obNewPost->feed_id = $clonepost->id;
					$obNewPost->save();
					
					
					$post_id = $request['feed_id'];
					
					$saData['post_id'] = $request['feed_id'];
					
					// CHECK IF ANY USER FOLLOW CURRENT USER THEN SEND NOTIFICATION TO THEM
					$check_active_notification_users = DB::table('notification_statuses as ns')
															->join('users', 'users.id', '=', 'ns.user_id')
															->where([['ns.type', 2], ['ns.follewers_id', $request['user_id']]])
															->select('users.id', 'users.app_token')->get();
					if(count($check_active_notification_users) > 0)
					{
						$tokens = array();
						$get_user = DB::table('users')
									->join('user_detail', 'user_detail.user_id', '=', 'users.id')
									->where('users.id', $request['user_id'])
									->select('users.name', 'user_detail.profile_image_url')
									->first();
						$title = 'New Post';
						$body = $get_user->name.' has created a new post.';
						
						foreach($check_active_notification_users as $active_user)
						{
							$tokens[] = $active_user->app_token;
						}
						
						$this->send_push_with_data($title, $body, $tokens, $request['user_id'], $request['feed_id'], $get_user->name, $get_user->profile_image_url);
					}
				}
			} else if($request->feed_type == '0'){ 
				$clonepost = ShopProduct::where('id',$request['feed_id'])->first();
				if($clonepost)
				{
					$obNewPost = new \App\SharePost();
					$obNewPost->user_id = $request['user_id'];
					$obNewPost->feed_type = $request->feed_type;
					$obNewPost->feed_id = $clonepost->id;
					$obNewPost->save();
					$saData['post_id'] = $request['feed_id'];
					
					// CHECK IF ANY USER FOLLOW CURRENT USER THEN SEND NOTIFICATION TO THEM
					$check_active_notification_users = DB::table('notification_statuses as ns')
															->join('users', 'users.id', '=', 'ns.user_id')
															->where([['ns.type', 2], ['ns.follewers_id', $request['user_id']]])
															->select('users.id', 'users.app_token')->get();
					if(count($check_active_notification_users) > 0)
					{
						$tokens = array();
						$get_user = DB::table('users')
									->join('user_detail', 'user_detail.user_id', '=', 'users.id')
									->where('users.id', $request['user_id'])
									->select('users.name', 'user_detail.profile_image_url')
									->first();
						$title = 'New Post';
						$body = $get_user->name.' has created a new post.';
						
						foreach($check_active_notification_users as $active_user)
						{
							$tokens[] = $active_user->app_token;
						}
						
						$this->send_push_with_data($title, $body, $tokens, $request['user_id'], $request['feed_id'], $get_user->name, $get_user->profile_image_url);
					}
				}
			
			} else {
				return $this->sendError('No Post Exist', array());
			}
            // CHECK IF ANY USER FOLLOW CURRENT USER THEN SEND NOTIFICATION TO THEM
            return $this->sendResponse($saData, 'Repost successfully.');
        }
    }
	
    public function getAllPost(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $ssUrl = 'https://artfora.net/images/post/Image';

            $saGetAllPost = DB::table('post')->select('users.name', 'post.id', 'user_detail.profile_image_url', 'post.image', 'post.video', 'post.owner_name', 'post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                    ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                    ->join('users', 'post.user_id', '=', 'users.id')
                    ->where([['post.user_id', $request->user_id], ['post.deleted_at', NULL]])
                    ->get()
                    ->toArray();
        }
        
        $saData = array();
        
        for ($i = 0; count($saGetAllPost) > $i; $i++) {
            $saData[$i]['id'] = $saGetAllPost[$i]->id;
            $saData[$i]['user_name'] = $saGetAllPost[$i]->name;
            $saData[$i]['user_profile_image'] = url('/').'/public/images/profile_image/' . $saGetAllPost[$i]->profile_image_url;
            $saData[$i]['image'] = $ssUrl . '/' . $saGetAllPost[$i]->image;

            list($snWidth, $snHeight) = getimagesize($saData[$i]['image']);
            $ssExt = pathinfo($saData[$i]['image'], PATHINFO_EXTENSION);
            $saData[$i]['image_ext'] = $ssExt;
            $saData[$i]['image_width'] = $snWidth;
            $saData[$i]['image_height'] = $snHeight;

            $saData[$i]['video'] = url('images/post/Video/') . '/' . $saGetAllPost[$i]->video;
            $saData[$i]['owner_name'] = $saGetAllPost[$i]->owner_name;
            $saData[$i]['decription'] = $saGetAllPost[$i]->decription;
            $saData[$i]['tag'] = $saGetAllPost[$i]->tag;
            $saData[$i]['filter'] = $saGetAllPost[$i]->filter;

            $saGetDetail = \App\PostDetail::select('title', 'description')->where('post_id', $saGetAllPost[$i]->id)->get()->toArray();
            $saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $saGetAllPost[$i]->id], ['type', 'like']])->count();
            $saData[$i]['is_like'] = with(new \App\PostLike())->get_user_is_like($request->user_id, $saGetAllPost[$i]->id);
            $saData[$i]['detail'] = $saGetDetail;
            $saData[$i]['likes'] = $saGeLikes;
            $saData[$i]['created_at'] = $saGetAllPost[$i]->created_at;
        }
        
        return $this->sendResponse($saData, 'Post Listed successfully.');
    }

    
    public function postComment(Request $request) {
        $validator = Validator::make($request->all(), [
                    'post_id' => 'required',
                    'user_id' => 'required',
                    'is_repost' => 'required',
                    'comment' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $obPostComment = new \App\PostComment();
            $obPostComment->user_id = $request->user_id;
            $obPostComment->comment_text = $request->comment;
            $obPostComment->post_id = $request->post_id;
            $obPostComment->user_tags = $request->user_tags;
            $obPostComment->save();
            
            $user_tags = explode(',', $request->user_tags);
            $current_user = User::where('id', $request->user_id)->pluck('name')->first();
            $tokens = array();
            foreach($user_tags as $user)
            {
                $get_user = User::where('tagname', $user)->where('comment_post_notification',1)->first();
                $title='Comment';
                $body=$current_user.' has tagged you in a comment.';
                if($get_user)
                {
                    $tokens[] = $get_user->app_token;
                    $this->send_push($title, $body, $tokens, $request->user_id, $request->post_id);
                }
                unset($tokens);
            }
			
			$posts = Post::where('id', $request->post_id)->first();
			$message = $current_user. ' commented on your post.';
			$this->saveActivity($request->user_id,$posts->user_id,$request->post_id,'comment',$message,$request->is_repost);
			
			$user_id = $request->user_id;
			
			$get_post = DB::table('post')
                                ->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.private_name', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
                                ->where([['post.id', $request->post_id], ['post.deleted_at', NULL]])
                                ->first(); 
			if($get_post)
			{
				if($request->is_repost){
					$share_post = SharePost::where('feed_id',$request->post_id)->where('feed_type',1)->get();
					foreach($share_post as $value){
						//$message = 'Commented from repost from '.$get_post->name;	
						$message = $current_user. ' commented on your repost from '.$get_post->name;
						$this->saveActivity($request->user_id,$value->user_id,$request->post_id,'comment',$message,$request->is_repost);
					}
				}
			
				$get_post->user_name = $get_post->name;
				$get_post->user_profile_image = $get_post->profile_image_url;
				
                $saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
                $saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
                $saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
                $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
                ->join('users', 'users.id', '=', 'user_tags.tag_user')
                ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
                $is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
                if($is_saved)
                {
                    $is_saved = 'yes';
                }
                else
                {
                    $is_saved = 'no';
                }
                $getStatus = DB::table('post_tags')
                            ->where('post_id', $get_post->id)
                            ->join('users', 'users.id', '=', 'post_tags.user_id')
                            ->join('user_detail', 'user_detail.user_id', '=', 'users.id')
                             ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
                            ->get();
                $get_post->tag_list = $getStatus;



                $get_post->is_like = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
                $get_post->is_saved = $is_saved;
                $get_post->detail = $saGetDetail;
                $get_post->likes = $saGeLikes;
                $get_post->comments = $saGeComments;
                $get_post->post_interest = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
                $get_post->tags = $saGetTag;

                $get_post->files = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
                $get_post->object_type = 1;
				$get_post->is_repost =  (int)$request->is_repost;
				$obPostComment->post_data = $get_post;
			} else {
				$obPostComment->post_data = (object)[];
			}
            return $this->sendResponse($obPostComment, 'Post Commented successfully.');
        }
    }

    public function deleteComment(Request $request) {
        $validator = Validator::make($request->all(), [
                    'comment_id' => 'required',
                    'user_id' => 'required',
                    'is_shop' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $data = array();
            $commentId = $request['comment_id'];
            $userId    = $request['user_id'];
            $isShop    = $request['is_shop'];
            $result = 0;
            if ($isShop == 0) {
                $result = PostComment::where('id',$commentId)->where('user_id',$userId)->delete();
            }
            else {
                $result = DB::table('shop_product_metas')->where('id',$commentId)->where('user_id',$userId)->delete();
            }
            if($result > 0){
                $data["message"] = "success";
                return $this->sendResponse($data, 'Comment is deleted');
            } else{
                return $this->sendResponse([], 'Comment doesn\'t exist');
            }
        }
    }
    
    public function editComment(Request $request) {
        $validator = Validator::make($request->all(), [
                    'comment_id' => 'required',
                    'user_id' => 'required',
                    'is_shop' => 'required',
                    'comment' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $data = array();
            $commentId = $request['comment_id'];
            $userId    = $request['user_id'];
            $comment   = $request['comment'];
            $isShop    = $request['is_shop'];
            $result = 0;
            if ($isShop == 0) {
                $result = PostComment::where(['id'=>$commentId,'user_id'=>$userId])->update(['comment_text' => $comment]);
            }
            else {
                $result = DB::table('shop_product_metas')->where(['id'=>$commentId,'user_id'=>$userId])->update(['comment'=>$comment]);
            }
            if($result > 0){
                $data["message"] = "success";
                return $this->sendResponse($data, 'Comment is updated');
            } else{
                return $this->sendResponse([], 'Comment doesn\'t exist');
            }
        }
    }
    
    public function postLike(Request $request) 
	{
        $validator = Validator::make($request->all(), [
                    'post_id' => 'required',
                    'user_id' => 'required',
                    'is_repost' => 'required',
                    'like_status' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
				$user_id = $request->user_id;
				$get_post = DB::table('post')
								->select('post.id', 'post.type', 'users.id as user_id', 'users.full_name', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'user_detail.category', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
								->distinct('id')
								->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
								->join('users', 'post.user_id', '=', 'users.id')
								->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('post.id', $request->post_id)
								->first(); 

				$saData['id'] = $get_post->id;
				$saData['user_id'] = $get_post->user_id;
				$saData['user_name'] = $get_post->name;
				if($request->is_repost){
					$share_post = SharePost::where('feed_id',$request->post_id)->where('feed_type',1)->first();
					$get_user = User::where('id',$share_post->user_id)->first();
					$saData['user_name'] = $get_user->name??"";
				}
				$saData['tagname'] = $get_post->tagname;
				$saData['email'] = $get_post->email;
				$saData['posted_by'] = $get_post->full_name;
				$saData['user_profile_image'] = $get_post->profile_image_url;
				$saData['category'] = $get_post->category;

				if (!empty($get_post->video)) {
					$saData['video'] = 'https://artfora.net/images/post/Video/' . $get_post->video;
					$ssExt = pathinfo($saData['video'], PATHINFO_EXTENSION);
					$saData['image_ext'] = $ssExt;
				} else {
					$saData['video'] = null;
				}
				$saData['is_flipped'] = $get_post->is_flipped;
				$saData['is_rotated'] = $get_post->is_rotated;
				$saData['owner_name'] = $get_post->owner_name;
				$saData['decription'] = $get_post->decription;
				$saData['tag'] = $get_post->tag;
				$saData['filter'] = $get_post->filter;
				$saData['type'] = $get_post->type;

				$saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
				
				$saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
				 $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
				 ->join('users', 'users.id', '=', 'user_tags.tag_user')
				 ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
				$is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
				if($is_saved)
				{
					$is_saved = 'yes';
				}
				else
				{
					$is_saved = 'no';
				}
				
				$stData2 = array();
				$getStatus = DB::table('post_tags')
							->where('post_id', $get_post->id)
							->join('users', 'users.id', '=', 'post_tags.user_id')
							->join('user_detail', 'user_detail.user_id', '=', 'users.id')
							 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
							->get();
				
				$saData['tag_list']=$getStatus;

				$saData['is_saved'] = $is_saved;
				$saData['detail'] = $saGetDetail;
				$saData['comments'] = $saGeComments;
				$saData['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
				$saData['tags'] = $saGetTag;
				$saData['created_at'] = $get_post->created_at;

				$saData['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
				$saData['object_type'] = 1;
				$saData['is_repost'] = (int)$request->is_repost;
				
            if ($request->like_status == 1) {
                $newPostLike = new \App\PostLike();
                $newPostLike->user_id = $request->user_id;
                $newPostLike->post_id = $request->post_id;
                $newPostLike->type = 'like';
                $newPostLike->save();
                
                $user_token = DB::table('users')->where('id',$request->notification_user_id)->where('like_post_notification',1)->first();
                $app_token=array();
                $title='New Notification';
				
				if($request->is_repost){
					$body = $request->u_name. ' liked your repost from '.$saData['user_name'];
					$share_post = SharePost::where('feed_id',$request->post_id)->where('feed_type',1)->get();
					foreach($share_post as $value){
						$message = $request->u_name. ' liked your repost from '.$saData['user_name'];
						$this->saveActivity($request->user_id,$value->user_id,$request->post_id,'liked',$message,$request->is_repost);
					}
				} else{
					$body = $request->u_name.' liked your post.';
				}
                if($user_token)
                {
                    $app_token[]=$user_token->app_token;
                    $this->send_push($title, $body, $app_token);
                }
								
				$message = $request->u_name. ' liked your post.';				
				$this->saveActivity($request->user_id,$get_post->user_id,$request->post_id,'liked',$message,$request->is_repost);
				
				$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
				$saData['likes'] = $saGeLikes;
				$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
				
                return $this->sendResponse($saData, 'Liked');
            } elseif ($request->like_status == 2) {
                \App\PostLike::where('post_id', $request->post_id)->where([['user_id', $request->user_id], ['type', 'like']])->delete();
				
				$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
				$saData['likes'] = $saGeLikes;
				$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
				
                return $this->sendResponse($saData, 'UnLike');
            } elseif ($request->like_status == 3) {
                $get_view = PostLike::where([['user_id', $request->user_id], ['post_id', $request->post_id], ['type', 'view']])->first();
                if($get_view)
                {
                    return $this->sendError('Already Viewed', array());
                }
                else
                {
                    PostLike::create(['user_id' => $request->user_id, 'post_id' => $request->post_id, 'type' => 'view']);
					$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
					$saData['likes'] = $saGeLikes;
					$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
                    return $this->sendResponse($saData, 'Viewed');
                }
                
            } else {
                return $this->sendError('Please Select Valid Status', array());
            }
        }
    }

    public function getPostComments(Request $request) {
        $validator = Validator::make($request->all(), [
                    'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $ssImageUrl = 'https://artfora.net/images/profile_image';
            $saPostComment = DB::table('post_comment')
                            ->select('users.name', 'user_detail.profile_image_url as image', 'user_detail.category', 'post_comment.user_id','post_comment.id as comment_id', 'post_comment.comment_text', 'post_comment.created_at as date_time')
                            ->join('users', 'post_comment.user_id', '=', 'users.id')
                            ->join('user_detail', 'post_comment.user_id', '=', 'user_detail.user_id')
                            ->whereNull('post_comment.deleted_at')
                            ->where('post_id', $request->post_id)->get()->toArray();
            foreach($saPostComment as $postComment) {
                $postComment->time_ago = $this->time_diff($postComment->date_time);
            }
            return $this->sendResponse($saPostComment, 'Post Comment Listed Successfully');
        }
    }
	
	public function getActivity(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			
			$limit = (($request->per_page??10));
			
			$nextpage = (($request->page??1)+1);
			
			if(!empty($request->type)){
				$activtyloglist = ActivityLog::with('users.userdetail');
				$activtyloglist->where('profile_id', $request->user_id);
				if($request->type == 'follow'){
					$activtyloglist->whereIn('type', ['follow','unfollow']);
				} else{
					$activtyloglist->where('type', $request->type);
				}
				$activtyloglist->whereNull('activity_log.deleted_at');
				$activtyloglist->limit($limit)->offset((($request->page??1)-1)*$limit);
				$activtylog = $activtyloglist->orderBy('id', 'DESC')->get();				
			} else{			
				$activtylog = ActivityLog::with('users.userdetail')
				->where('profile_id', $request->user_id)
				->whereNull('activity_log.deleted_at')
				->limit($limit)->offset((($request->page??1)-1)*$limit)
				->orderBy('id', 'DESC')->get();				
			}
			
			$saData = array();
            for($i = 0; count($activtylog) > $i; $i++) {
				
                $saData[$i]['id'] = $activtylog[$i]->id;
                $saData[$i]['user_id'] = $activtylog[$i]->user_id;
                $saData[$i]['profile_id'] = $activtylog[$i]->profile_id;
                $saData[$i]['object_id'] = $activtylog[$i]->object_id;
                $saData[$i]['type'] = $activtylog[$i]->type;
                $saData[$i]['message'] = $activtylog[$i]->message;
                $saData[$i]['is_repost'] = $activtylog[$i]->is_repost;
                $saData[$i]['name'] = isset($activtylog[$i]->users->name)?$activtylog[$i]->users->name:'';
                $saData[$i]['tagname'] = isset($activtylog[$i]->users->tagname)?$activtylog[$i]->users->tagname:'';
                $saData[$i]['user_profile_image'] = isset($activtylog[$i]->users->userdetail->profile_image_url)?$activtylog[$i]->users->userdetail->profile_image_url:'';
				
				if($activtylog[$i]->type == 'follow'){
					$followers = \App\Followers::where('follewers_id', $activtylog[$i]->profile_id)->where('user_id', $activtylog[$i]->user_id)->first();
					$saData[$i]['followers'] = $followers;
				}  
				else {
					$saData[$i]['followers'] = (object)[];
				}
				
				$saData[$i]['post_thumb'] = "";
				if($activtylog[$i]->type != 'follow' || $activtylog[$i]->type != 'unfollow'){
					$postimage = DB::table('post_files')->select('thumb')->where('post_id',$activtylog[$i]->object_id)->orderBy('priority', 'ASC')->get()->toArray();
					//dd($postimage[0]->thumb);
					if(isset($postimage[0]->thumb)){
						$saData[$i]['post_thumb'] = "https://artfora.net/images/post/new_images/thumb/".$postimage[0]->thumb;
					}
				}
                $saData[$i]['created_at'] = $activtylog[$i]->created_at;
				$saData[$i]['time_ago'] = $this->time_diff($activtylog[$i]->created_at);
            }	
			
			$response = [
				'success' => true,
				'message' => 'Activity Log Successfully',
				'nextpage' => $nextpage,
				'data' => $saData
			];
			return response()->json($response, 200);
        }
    }
	
	public function time_diff($time1) {
    
	   $timestamp = strtotime($time1);	
	   
	   $strTime = array("s", "m", "h", "d", "m", "y");
	   $length = array("60","60","24","30","12","10");

	   $currentTime = time();
	   if($currentTime >= $timestamp) {
			$diff     = time()- $timestamp;
			for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
				$diff = $diff / $length[$i];
			}

			$diff = round($diff);
			if($strTime[$i] == 'm' || $strTime[$i] == 'y'){
				return date('d.m.Y',$timestamp);
			}else{
				if($strTime[$i] == 's'){
					return "just now";
				}
				else if($strTime[$i] == 'm'){
					return $diff . " minute ago";
				}
				else if($strTime[$i] == 'h'){
					return $diff . " hour ago";
				}
				else{
					$day = " day";
					if($diff > 1){
						$day = " days";
					}
					return $diff . $day ." ago";
				}
			}	
	   }
	}	
		
	public function saveActivity($user_id,$profile_id=0,$object_id,$type,$message="",$is_repost=0) 
	{		
        $activity_status = array('user_id'=>$user_id,'profile_id' =>$profile_id,'object_id'=>$object_id,'type'=>$type,'message'=>$message,'is_repost'=>$is_repost);
		$invite = ActivityLog::create($activity_status);
    }
	
	public function set_Activity_Time(Request $request) 
	{	
		$validator = Validator::make($request->all(), [
           'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			$countdata = [];
			$countdata['comment'] = 0;
			$countdata['follow'] = 0;
			$countdata['unfollow'] = 0;
			$countdata['liked'] = 0;
			$countdata['saved'] = 0;
			$actime = ActivityTime::where('user_id',$request->user_id)->first();
			if($actime){
				
				if(!empty($actime->end_time)){
					$start_time = date('Y-m-d H:i:s');
					//$start_time = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($date_raw)));
					$end_time = null;
					$activity_status = array('start_time' =>$start_time,'end_time' =>$end_time);
					$invite = $actime->update($activity_status);
				} 
				else if(!empty($actime->start_time)){
					$end_time = date('Y-m-d H:i:s');
					$activity_status = array('end_time' =>$end_time);
					$invite = $actime->update($activity_status);
					
					$activtylog = ActivityLog::select(\DB::raw('count(id) as type_count, type'))->where('profile_id', $request->user_id)->whereNull('activity_log.deleted_at')
					->where('created_at', '>=', $actime->start_time)
					->where('created_at', '<=', $end_time)->groupBy('type')->get();
					
					foreach($activtylog as $key=>$value){
						$countdata[$value->type] = $value->type_count;
					}
					$start_time = date('Y-m-d H:i:s');
					$activity_status = array('start_time' =>$start_time);
					$actime->update($activity_status);		
				} else{
					
				}
			} else {
				$start_time = date('Y-m-d H:i:s');
				//$start_time = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($date_raw)));
				$activity_status = array('user_id'=>$request->user_id,'start_time' =>$start_time);
				$invite = ActivityTime::create($activity_status);
			}		
			return $this->sendResponse($countdata, 'Activity Time Successfully');
		}
    }
	
    public function follow(Request $request) {

        $validator = Validator::make($request->all(), [
                    'follow_by_id' => 'required',
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			$feed_id = $request->feed_id;
			$user_id = $request->user_id;
			if(!empty($request->feed_id) && !empty($request->type) && $request->type == 'post')
			{
				$get_post = DB::table('post')
								->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
								->distinct('id')
								->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
								->join('users', 'post.user_id', '=', 'users.id')
								->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('post.id', $feed_id)
								->first(); 

				$saData['id'] = $get_post->id;
				$saData['user_id'] = $get_post->user_id;
				$saData['user_name'] = $get_post->name;
				$saData['tagname'] = $get_post->tagname;
				$saData['email'] = $get_post->email;
				$saData['user_profile_image'] = $get_post->profile_image_url;

				if (!empty($get_post->video)) {
					$saData['video'] = 'https://artfora.net/images/post/Video/' . $get_post->video;
					$ssExt = pathinfo($saData['video'], PATHINFO_EXTENSION);
					$saData['image_ext'] = $ssExt;
				} else {
					$saData['video'] = null;
				}
				$saData['is_flipped'] = $get_post->is_flipped;
				$saData['is_rotated'] = $get_post->is_rotated;
				$saData['owner_name'] = $get_post->owner_name;
				$saData['decription'] = $get_post->decription;
				$saData['tag'] = $get_post->tag;
				$saData['filter'] = $get_post->filter;
				$saData['type'] = $get_post->type;

				$saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
				$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
				$saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
				 $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
				 ->join('users', 'users.id', '=', 'user_tags.tag_user')
				 ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
				$is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
				if($is_saved)
				{
					$is_saved = 'yes';
				}
				else
				{
					$is_saved = 'no';
				}
				
				 /***************************************************************/
				$stData2 = array();
				$getStatus = DB::table('post_tags')
							->where('post_id', $get_post->id)
							->join('users', 'users.id', '=', 'post_tags.user_id')
							->join('user_detail', 'user_detail.user_id', '=', 'users.id')
							 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
							->get();
				
				$saData['tag_list']=$getStatus;

				$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
				$saData['is_saved'] = $is_saved;
				$saData['detail'] = $saGetDetail;
				$saData['likes'] = $saGeLikes;
				$saData['comments'] = $saGeComments;
				$saData['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
				$saData['tags'] = $saGetTag;
				$saData['created_at'] = $get_post->created_at;

				$saData['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
				$saData['object_type'] = 1;
			}
			else if(!empty($request->feed_id) && !empty($request->type) && $request->type == 'shop')
			{
				$saData = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
						$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
						->leftJoin('user_detail', 'user_detail.user_id', '=', 'shop_profiles.user_id')
						->select('shop_profiles.id', 'shop_profiles.name as shop_name', 'shop_profiles.company_name', 'user_detail.profile_image_url as shop_image_url', 'users.name as user_name', 'users.tagname as user_tagname');
				}])
				->withCount([
					'shop_product_meta as likes' => function($query){
						$query->where('type', 'like');
					},
					'shop_product_meta as comments' => function($query){
						$query->where('type', 'comment');
					},
					'shop_product_meta as is_liked' => function($query) use ($user_id){
						$query->where([['type', 'like'], ['user_id', $user_id]]);
					},
					'shop_product_meta as is_saved' => function($query) use ($user_id){
						$query->where([['type', 'save'], ['user_id', $user_id]]);
					}
				])
				->where([['id', $feed_id]])
				->first();

				$get_profile = DB::table('shop_profiles')->where('user_id',  $user_id)->select('name', 'rating')->first();
				$saData['posted_by'] = User::where('id', $user_id)->pluck('full_name')->first();
				$saData['seller_rating'] = $get_profile->rating;
				$saData['object_type'] = 0;
			} 
			else
			{
				$saData = [];
			}
				
            if ($request->follow_unfollow == 1) {
                
                $check_follow = Followers::where([['follewers_id', $request->follow_by_id], ['user_id', $request->user_id]])->first();
                if($check_follow){
                    return $this->sendError('You have already follow this user.', array());
                }
                else
				{	
                    $check_unfollow_already = Followers::onlyTrashed()->where([['follewers_id', $request->follow_by_id], ['user_id', $request->user_id]])->first();
                    if(!$check_unfollow_already){
                        $obFollowers = new \App\Followers();
                        $obFollowers->follewers_id = $request->follow_by_id;
                        $obFollowers->user_id = $request->user_id;
                        $obFollowers->save();
                        for($i=1;$i<4;$i++)
                        {
                            $follow_statuses[] = array('follewers_id' => $request->follow_by_id,
                                                    'user_id' => $request->user_id,
                                                    'type' => $i
                                                    );
                                                    
                            // $notification_statuses[] = array('follewers_id' => $request->follow_by_id,
                            //                             'user_id' => $request->user_id,
                            //                             'type' => $i
                            //                             );
                        }
                        $save_follow_statuses = DB::table('follow_statuses')->insert($follow_statuses);
						
						$message = $request->u_name. ' started following you.';
						$this->saveActivity($request->user_id,$request->follow_by_id,$obFollowers->id,'follow',$message);	
						
                        // $save_notification_statuses = DB::table('notification_statuses')->insert($notification_statuses);
                    }
                    else
					{
                        $check_unfollow_already->deleted_at = NULL;
                        $check_unfollow_already->save();
                        for($i=1;$i<4;$i++)
                        {
                            $follow_statuses[] = array('follewers_id' => $request->follow_by_id,
                                                    'user_id' => $request->user_id,
                                                    'type' => $i
                                                    );
                                                    
                            // $notification_statuses[] = array('follewers_id' => $request->follow_by_id,
                            //                             'user_id' => $request->user_id,
                            //                             'type' => $i
                            //                             );
                        }
                        $save_follow_statuses = DB::table('follow_statuses')->insert($follow_statuses);
						
                        // $save_notification_statuses = DB::table('notification_statuses')->insert($notification_statuses);
                    }
                    
                    $user_token=DB::table('users')->where('id',$request->follow_by_id)->where('follow_notification',1)->first();
                    $app_token=array();
                    $title= $request->u_name;
                    $body = 'Started following you.';
                    if($user_token)
                    {
                        $app_token[]=$user_token->app_token;
                        $this->send_push($title, $body, $app_token);    
                    }
                    else
                    {
                        
                    }
					
					$followers = \App\Followers::where('follewers_id', $request->follow_by_id)->where('user_id', $user_id)->first();
					$saData['followers'] = $followers;
                    return $this->sendResponse($saData, 'Follow Successfully');
                }
            } elseif ($request->follow_unfollow == 2) {
                
				$obFollowers = \App\Followers::where('follewers_id', $request->follow_by_id)->where('user_id', $request->user_id)->first();
				
				$saData['followers'] = null;
				$message = $request->u_name. ' has unfollowed you.';
				if(isset($obFollowers->id)){
					$this->saveActivity($request->user_id,$request->follow_by_id,$obFollowers->id,'unfollow',$message);	
				
				
					\App\Followers::where('follewers_id', $request->follow_by_id)->where('user_id', $request->user_id)->forceDelete();
					
					$save_follow_statuses = DB::table('follow_statuses')->where('follewers_id', $request->follow_by_id)->where('user_id', $request->user_id)->delete();
					$save_notification_statuses = DB::table('notification_statuses')->where('follewers_id', $request->follow_by_id)->where('user_id', $request->user_id)->delete();
					
					$user_token=DB::table('users')->where('id',$request->follow_by_id)->where('unfollow_notification',1)->first();
							
					$app_token=array();
					$title='New Notification';
					$body=$request->u_name.' has unfollowed you.';
					if($user_token)
					{
						$app_token[]=$user_token->app_token;
						$this->send_push($title, $body, $app_token);    
					}
					else
					{
						
					}
				}
                return $this->sendResponse($saData, 'Unfollow Successfully');
            } else {
                return $this->sendError('Please Select Valid Follow Status', array());
            }
        }
    }
	
	public function sendInvitation(Request $request) {

        $validator = Validator::make($request->all(), [
                    'invited_id' => 'required',
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			
			$check_invitat = Invitation::where([['invited_id', $request->invited_id], ['user_id', $request->user_id]])->where('deleted_at',Null)->first();
			if($check_invitat){
				return $this->sendError('You have already invited this user.', array());
			}
			else
			{
				$invited_statuses = array('invited_id' => $request->invited_id,'user_id' => $request->user_id,'status' => 0);
				$invite = Invitation::create($invited_statuses);
				return $this->sendResponse($invite, 'Invited Successfully');
			}	
		} 
    }
	
	public function getInvitationCount(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			
			$invitatcount = Invitation::where('invited_id', $request->user_id)->where('status', 0)->where('deleted_at',Null)->count();
			
			return $this->sendResponse(array("count"=>$invitatcount), 'Get Invited Count Successfully');
		} 
    }
	
	public function getInvitation(Request $request) {

        $validator = Validator::make($request->all(), [
                'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			
			$invitatcount = Invitation::where('invited_id', $request->user_id)->where('status', 0)->where('deleted_at',Null)->count();
			
			$invitatlist = Invitation::join('users', 'invitations.user_id', '=', 'users.id')->leftJoin('user_detail', 'invitations.user_id', '=', 'user_detail.user_id')->where('invitations.invited_id', $request->user_id)->where('invitations.status', 0)->where('invitations.deleted_at',Null)->select('invitations.id','invitations.status','invitations.invited_id','invitations.user_id','users.name','users.tagname','user_detail.profile_image_url','user_detail.privete_profile_url')->get();
			
			return $this->sendResponse(array("list"=>$invitatlist,"count"=>$invitatcount), 'Get Invited Successfully');
		} 
    }
	
	public function deleteInvitation(Request $request) {

        $validator = Validator::make($request->all(), [
                'id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			
			$invitatcount = Invitation::where('id', $request->id)->where('deleted_at',Null)->first();
			if($invitatcount){
				Invitation::where('id', $request->id)->delete();
				return $this->sendResponse(array(), 'Invitation delete Successfully');
			} else{
				return $this->sendResponse(array(), 'Invitation alredy deleted');
			}
		} 
    }
	
	public function acceptInvitation(Request $request) {

        $validator = Validator::make($request->all(), [
                'id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			
			$invitatcount = Invitation::where('id', $request->id)->where('status', 0)->where('deleted_at',Null)->first();
			if($invitatcount){
				Invitation::where('id', $request->id)->update(['status' => 1]);
				
				$get_user = User::where('id', $invitatcount->user_id)->first();
                $tokens = [];
				if(isset($get_user->app_token) && !empty($get_user->app_token))
				{
					$tokens[] = $get_user->app_token;
				}
				if(count($tokens) > 0){
					$title='New Notification';
					$body='Your invitation accepted';
					$this->send_push($title, $body, $tokens);
				}
				return $this->sendResponse(array(), 'Invitation accepted Successfully');
			} else {
				return $this->sendResponse(array(), 'Invitation alredy accepted');
			}
		} 
    }

    public function searchUser(Request $request) {
        $validator = Validator::make($request->all(), [
                    'username' => 'required',
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            // $ssImageUrl = 'https://artfora.net/images/profile_image';
            $ssImageUrl = '';
            $saSearchUser = DB::table('users')->select('users.id', 'users.name', 'users.tagname', 'users.email', 'users.deleted_at', 'users.email_verified_at', 'user_detail.filter', DB::raw("CONCAT('" . $ssImageUrl . "',user_detail.profile_image_url) as image"), 'category', 'private_description', 'privete_profile_url', 'description')
                            ->where([['users.name', 'like', $request->username . '%'], ['users.id', '!=', $request->user_id], ['users.status', 1]]);
                            if($request->is_admin != 1){
                                $saSearchUser->where('users.deleted_at', NULL);
                            }
            $saSearchUser = $saSearchUser->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')->orderBy('users.name', 'ASC')
                            ->get()->toArray();
            for ($i = 0; count($saSearchUser) > $i; $i++) {
                $saInterestGroup = DB::table('interest_group')->select('interest_name')->where('user_id', $saSearchUser[$i]->id)->get()->toArray();
                $saSearchUser[$i]->interest_group = $saInterestGroup;
                $saSearchUser[$i]->urls = DB::table('user_urls')->where('user_id', $saSearchUser[$i]->id)->get();
                $snIsFollow = Followers::where('follewers_id', $saSearchUser[$i]->id)->where('user_id', $request->user_id)->get()->first();
                if (empty($snIsFollow)) {
                    //Not Follow
                    $saSearchUser[$i]->is_follow = FALSE;
                } else {
                    //Follow
                    $saSearchUser[$i]->is_follow = TRUE;
                }
            }


            return $this->sendResponse($saSearchUser, 'User Listed Successfully');
        }
    }


   /*********************FEED***************************/

    public function feed(Request $request) {
    
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $user_id=$request->user_id;
            $self_user_id=$request->self_user_id;
            $filter_id=$request->filter_id;
            $snUserId = $request->user_id;
            $get_posts = $request->get_posts;
            $tab = $request->tab;
            $post_type = array('public');
        
            $Block_user=DB::table('blocked_users')->where('user_id',$user_id)->pluck('blocked_userid')->toArray();
            $Block_me_user=DB::table('blocked_users')->where('blocked_userid',$user_id)->pluck('user_id')->toArray();
            for ($i = 0; count($Block_me_user) > $i; $i++) {
                array_push($Block_user, $Block_me_user[$i]);
            }
            if($request->page){
                $start_from=($request->page*$this->limit);
            }
            else{
               $start_from=0;
            }
            $all_share_post_ids = [];
            $all_share_product_ids = [];
            if($tab == 'public')
            {
                if($get_posts == 'others')
                {
                    $saGetFollowingId = DB::table('follow_statuses')
                                        ->where('user_id', $user_id)
                                        ->whereColumn('user_id', '!=', 'follewers_id')
                                        ->where(function($query){
                                            $query->where('type', 1)
                                            ->orWhere('type', 2);
                                        })
                                        ->get();    

                    $saFollowingId =  $saGetFollowingId->where('type', 2)->pluck('follewers_id')->toArray();
                    array_push($saFollowingId, $snUserId);
                    
                    // GET SHOP FOLLOWERS
                    $shopFollowersId = $saGetFollowingId->where('type', 1)->where('status', 1)->pluck('follewers_id')->toArray();
                    array_push($shopFollowersId, $snUserId);
                    // GET SHOP FOLLOWERS
                    
                }
                else
                {
                    $saFollowingId = $shopFollowersId = array();
                    array_push($saFollowingId, $snUserId);
                    array_push($shopFollowersId, $snUserId);
                }
                $saFollowingId = array_diff($saFollowingId, $Block_user);
                $shopFollowersId = array_diff($shopFollowersId, $Block_user);
                
                $ssUrl = url('images/post/Image/');

                // GET HASH TAGS FOLLOWED
                $follow_hash_tags_array = DB::table('follow_hash_tags')
                    ->join('hash_tags', 'hash_tags.id', '=', 'follow_hash_tags.hash_tag_id')
                    ->where('user_id', $user_id)->pluck('hash_tags.hash_tag_name')->toArray();
                $follow_hash_tags_names = join(' ', $follow_hash_tags_array);
                // GET HASH TAGS FOLLOWED

                if($filter_id == 1)
                {
                    $filter_array = array(1);
                }
                elseif($filter_id == 2)
                {
                    $filter_array = array(1,2);
                }
                elseif($filter_id == 3)
                {
                    $filter_array = array(1,2,3);
                }
                else
                {
                    $filter_array = array(1,2,3,4);
                }
                
				if($get_posts == 'others')
                {
                    
					$all_share_post_ids = SharePost::whereIn('user_id',$saFollowingId)->where('feed_type',1)->pluck('feed_id')->toArray();
					$all_share_product_ids = SharePost::whereIn('user_id',$saFollowingId)->where('feed_type',0)->pluck('feed_id')->toArray();
					$all_post_ids = DB::table('post')
									->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url','user_detail.category','post.video_thumbnail','post.video_thumb_title','post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
									->distinct('id')
									->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
									->join('users', 'post.user_id', '=', 'users.id')
									->whereIn('post.user_id', $saFollowingId)
//									->whereNotIn('post.user_id',$Block_user)
									->where('post.deleted_at', NULL)
									->whereIn('post.filter_id',$filter_array)
									->whereIn('post.type', $post_type)
									->orWhere('post.user_id', $user_id)
									->pluck('post.id')->toArray();
                  
					if (($key = array_search($user_id, $shopFollowersId)) !== false) {
						unset($shopFollowersId[$key]);
					}
					// SHOP PRODUCTS
					$all_shop_product_ids = ShopProduct::join('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
									->whereIn('shop_products.user_id', $shopFollowersId)
//									->whereNotIn('shop_products.user_id',$Block_user)
									->whereIn('shop_products.filter_id',$filter_array)
									->where('shop_profiles.deleted_at', NULL)
									->orWhere('shop_profiles.user_id', $user_id)
									->pluck('shop_products.id')->toArray();
					// SHOP PRODUCTS
                
                    // IF THERE ARE HASHED TAGS
                    if(count($follow_hash_tags_array) > 0)
                    {
                        $posts_having_hash_tags = DB::table('post')
                                    ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title','post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                    ->distinct('id')
                                    ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                    ->join('users', 'post.user_id', '=', 'users.id')
                                    ->whereNotIn('post.user_id',$Block_user)
                                    ->whereIn('post.type', $post_type)
                                    ->where('post.deleted_at', NULL)
                                    ->whereRaw('MATCH (tag) AGAINST (?)' , $follow_hash_tags_names)
									->orWhere('post.user_id', $user_id)
                                    ->orderBy('post.id', 'DESC')
                                    ->pluck('post.id')->toArray();
                        $all_post_ids = array_unique(array_merge($all_post_ids, $posts_having_hash_tags));

                        
                    }
                    
                    // IF THERE ARE HASHED TAGS 
                    
                    // ADD PRIVATE POSTS
                    $private_posts = Post::where([['user_id', $user_id], ['type', 'private']])->pluck('id')->toArray();
                    $all_post_ids = array_unique(array_merge($all_post_ids, $private_posts));
					
					
					$user_ids = Invitation::where('user_id', $user_id)->where('status', 1)->pluck('invited_id')->toArray();
                    $differenceArray = array_intersect($user_ids, $saFollowingId);

                    $posts_invites = DB::table('post')
                                ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title','post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'post.user_id', '=', 'users.id')
								->whereIn('post.user_id',$differenceArray)
//                                ->whereNotIn('post.user_id',$Block_user)
								->where('post.type', 'private')
								->where('post.deleted_at', NULL)
                                ->pluck('post.id')->toArray();
					$all_post_ids = array_unique(array_merge($all_post_ids, $posts_invites));
                    // ADD PRIVATE POSTS
                }
				
				if($get_posts == 'self')
				{
					$all_share_post_ids = SharePost::where('user_id',$user_id)->where('feed_type',1)->pluck('feed_id')->toArray();
					$all_share_product_ids = SharePost::where('user_id',$user_id)->where('feed_type',0)->pluck('feed_id')->toArray();
					
					if($self_user_id != $user_id)
					{
						$all_post_ids = DB::table('post')
                                ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title','post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->where('post.deleted_at', NULL)
                                ->whereIn('post.type', $post_type)
                                ->where('post.user_id', $user_id)
								->whereIn('post.filter_id',$filter_array)
                                ->pluck('post.id')->toArray();
							if (($key = array_search($user_id, $shopFollowersId)) !== false) {
								unset($shopFollowersId[$key]);
							}
					} else{
						$all_post_ids = DB::table('post')
                                ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title','post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->where('post.deleted_at', NULL)
                                ->whereIn('post.type', $post_type)
                                ->where('post.user_id', $user_id)
                                ->pluck('post.id')->toArray();
							if (($key = array_search($user_id, $shopFollowersId)) !== false) {
								unset($shopFollowersId[$key]);
							}
					}
					
				}
               
                rsort($all_post_ids);
                //rsort($all_shop_product_ids);          
            }
            elseif($tab == 'private')
            {
                $all_post_ids = DB::table('post')
                                ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title','post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'post.user_id', '=', 'users.id')
//                                ->where([['post.type', 'private'], ['post.deleted_at', NULL]])
//                                ->whereNotIn('post.user_id',$Block_user)
                                ->where([['post.type', 'private'], ['post.deleted_at', NULL], ['post.user_id', $user_id]])
                                ->pluck('post.id')->toArray();
            }
			elseif($tab == 'shop')
            {
				
				$ssUrl = url('images/post/Image/');

                // GET HASH TAGS FOLLOWED
                $follow_hash_tags_array = DB::table('follow_hash_tags')
                    ->join('hash_tags', 'hash_tags.id', '=', 'follow_hash_tags.hash_tag_id')
                    ->where('user_id', $user_id)->pluck('hash_tags.hash_tag_name')->toArray();
                $follow_hash_tags_names = join(' ', $follow_hash_tags_array);
                // GET HASH TAGS FOLLOWED
				
				if($filter_id == 1)
                {
                    $filter_array = array(1);
                }
                elseif($filter_id == 2)
                {
                    $filter_array = array(1,2);
                }
                elseif($filter_id == 3)
                {
                    $filter_array = array(1,2,3);
                }
                else
                {
                    $filter_array = array(1,2,3,4);
                }
                // SHOP PRODUCTS
				$limit = 10;
                $all_shop_product_ids = ShopProduct::join('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
                                ->where('shop_products.user_id', $user_id)
                                //->whereNotIn('shop_products.user_id',$Block_user)
                                //->whereIn('shop_products.filter_id',$filter_array)
                                ->where('shop_profiles.deleted_at', NULL)
                                ->where('shop_products.status', 1)
                                ->orderBy('shop_products.created_at', 'DESC')
								->limit($limit)->offset((($request->page??1)-1)*$limit)
                                ->pluck('shop_products.id')->toArray();
                // SHOP PRODUCTS
				
				// SHOP PRODUCTS
				/* $shop_products_having_hash_tags = ShopProduct::join('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
								//->whereNotIn('shop_products.user_id',$Block_user)
								//->whereIn('shop_products.filter_id',$filter_array)
								->where('shop_profiles.deleted_at', NULL)
								->where('shop_products.status', 1)
								->whereRaw('MATCH (tags) AGAINST (?)' , $follow_hash_tags_names)
								->pluck('shop_products.id')->toArray();
				$all_post_ids = array_unique(array_merge($all_shop_product_ids, $shop_products_having_hash_tags)); */
				
				$saData = array();
				for ($i = 0; count($all_shop_product_ids) > $i; $i++)
				{ 
					$saData[$i] = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
							$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
							->select('shop_profiles.id', 'shop_profiles.shop_name', 'shop_profiles.company_name', 'users.email', 'shop_profiles.shop_image_url', 'users.name as user_name', 'users.tagname as user_tagname');
					}])
					->withCount([
						'shop_product_meta as likes' => function($query){
							$query->where('type', 'like');
						},
						'shop_product_meta as comments' => function($query){
							$query->where('type', 'comment');
						},
						'shop_product_meta as is_liked' => function($query) use ($user_id){
							$query->where([['type', 'like'], ['user_id', $user_id]]);
						},
						'shop_product_meta as is_saved' => function($query) use ($user_id){
							$query->where([['type', 'save'], ['user_id', $user_id]]);
						}
					])
					->where([['id', $all_shop_product_ids[$i]], ['status', 1]])->first();

					$get_profile = DB::table('shop_profiles')->where('user_id',  $user_id)->select('name', 'rating')->first();
					$user_detail = DB::table('user_detail')->where('user_id',  $user_id)->select('category')->first();
					$saData[$i]['posted_by'] = User::where('id', $user_id)->pluck('name')->first();
					$saData[$i]['seller_rating'] = $get_profile->rating;
					$saData[$i]['category'] = $user_detail->category;
					$saData[$i]['object_type'] = 0;
				}
				if(count($saData) > 0)
				{
					return $this->sendResponse($saData, 'User Listed Successfully'); 
				}
				else
				{
					return $this->sendResponse(array(), 'User Listed Successfully');
				}   
				// SHOP PRODUCTS
                //rsort($all_post_ids); 
            }
            else
            {
                $all_post_ids = DB::table('post')
                                ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title','post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->join('saved_posts', 'post.id', '=', 'saved_posts.post_id')
                                ->whereNotIn('post.user_id',$Block_user)
                                ->where('post.deleted_at', NULL)
                                ->where('saved_posts.user_id', $user_id)
                                ->pluck('post.id')->toArray();
            }

            // print_r($all_post_ids);
            // die;
            // $all_shop_product_ids = $all_post_ids = [];
            $ids_ordered = (count($all_post_ids) > 0) ? implode(',', $all_post_ids) : 'NULL';
            $product_ids_ordered = (isset($all_shop_product_ids) && count($all_shop_product_ids) > 0) ? implode(',', $all_shop_product_ids) : 'NULL';

            $get_posts_and_product_ids = DB::select('select * from 
            (select id, 0 as type, user_id, created_at,0 as is_repost,0 as share_user_id  from `shop_products` 
            where `id` in ('.$product_ids_ordered.') and `status` = 1

            UNION ALL

            select post.id, 1 as type, post.user_id, post.created_at,0 as is_repost,0 as share_user_id from `post` 
            where `post`.`id` in ('.$ids_ordered.')
            ) as uni order by created_at DESC LIMIT '.$this->limit.' OFFSET '.$start_from.'');
			
			
			$ids_share_ordered = (count($all_share_post_ids) > 0) ? implode(',', $all_share_post_ids) : 'NULL';
            $share_product_ids_ordered = (isset($all_share_product_ids) && count($all_share_product_ids) > 0) ? implode(',', $all_share_product_ids) : 'NULL';
			$get_share_posts_and_product_ids = [];
			if($get_posts == 'self')
			{
				$get_share_posts_and_product_ids = DB::select('select * from 
				(select shop_products.id, 0 as type, shop_products.user_id, sp.created_at,sp.id as is_repost,sp.user_id as share_user_id  from `shop_products` join share_posts as sp ON sp.feed_id = shop_products.id
				where shop_products.id in ('.$share_product_ids_ordered.') and shop_products.status = 1 and sp.user_id = '.$user_id.'

				UNION ALL

				select post.id, 1 as type, post.user_id, sp1.created_at,sp1.id as is_repost,sp1.user_id as share_user_id from `post` join share_posts as sp1 ON sp1.feed_id = post.id 
				where `post`.`id` in ('.$ids_share_ordered.') and sp1.user_id = '.$user_id.' 
				) as uni order by created_at DESC LIMIT '.$this->limit.' OFFSET '.$start_from.'');
				
			} 
			else
			{
				$get_share_posts_and_product_ids = DB::select('select * from 
				(select shop_products.id, 0 as type, shop_products.user_id, sp.created_at,sp.id as is_repost,sp.user_id as share_user_id  from `shop_products` join share_posts as sp ON sp.feed_id = shop_products.id
				where shop_products.id in ('.$share_product_ids_ordered.') and shop_products.status = 1

				UNION ALL

				select post.id, 1 as type, post.user_id, sp1.created_at,sp1.id as is_repost,sp1.user_id as share_user_id from `post` join share_posts as sp1 ON sp1.feed_id = post.id 
				where `post`.`id` in ('.$ids_share_ordered.') 
				) as uni order by created_at DESC LIMIT '.$this->limit.' OFFSET '.$start_from.'');
			}
			
			$get_posts_and_product_ids = array_merge($get_share_posts_and_product_ids,$get_posts_and_product_ids);
			if(count($get_posts_and_product_ids) > 0){			
				foreach ($get_posts_and_product_ids as $key => $row)
				{
					$vc_array_name[$key] = $row->created_at;
				}
				array_multisort($vc_array_name, SORT_DESC, $get_posts_and_product_ids);
			}
			$saData = $this->feed_posts_products($get_posts_and_product_ids, $user_id);
			
            if(count($saData) > 0)
            {
                return $this->sendResponse($saData, 'User Listed Successfully'); 
            }
            else
            {
                return $this->sendResponse(array(), 'User Listed Successfully');
            }            
        }
    }

    public function feed_posts_products($ids, $user_id)
    {
        $saData = array();
        for ($i = 0; count($ids) > $i; $i++)
        {   
            // IF TYPE == POST
            if($ids[$i]->type == 1)
            {
				if($ids[$i]->share_user_id){
					$get_post = DB::table('post')
                                ->select('post.id', 'post.type', 'post.user_id as original_poster_id', 'users.id as user_id', 'users.name', 'ouser.name as poster_name', 'users.tagname', 'users.email', 'post.image', 'user_detail.private_name', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('share_posts', 'share_posts.feed_id', '=', 'post.id')
                                ->join('user_detail', 'share_posts.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'share_posts.user_id', '=', 'users.id')
                                ->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
                                ->leftJoin('users as ouser', 'post.user_id', '=', 'ouser.id')
                                ->where('post.id', $ids[$i]->id)
                                ->first(); 
				} else{
					$get_post = DB::table('post')
                                ->select('post.id', 'post.type', 'post.user_id as original_poster_id', 'users.id as user_id', 'users.name', 'ouser.name as poster_name', 'users.tagname', 'users.email', 'post.image', 'user_detail.private_name', 'user_detail.profile_image_url','user_detail.category', 'post.video','post.video_thumbnail','post.video_thumb_title', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                                ->distinct('id')
                                ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
                                ->leftJoin('users as ouser', 'post.user_id', '=', 'ouser.id')
                                ->where('post.id', $ids[$i]->id)
                                ->first(); 
				}
					
                
                $saData[$i]['id'] = $get_post->id;
                                                            
                $saData[$i]['user_id'] = $get_post->user_id;
				if($get_post->type == "private"){
					$saData[$i]['user_name'] = $get_post->private_name;
                                                            
				}   else{
					$saData[$i]['user_name'] = $get_post->name;
				}
                                                             
                $saData[$i]['tagname'] = $get_post->tagname;
                $saData[$i]['email'] = $get_post->email;
                $saData[$i]['user_profile_image'] = $get_post->profile_image_url;
                $saData[$i]['category'] = $get_post->category;

                if (!empty($get_post->video)) {
                    $saData[$i]['video'] = 'https://artfora.net/images/post/Video/' . $get_post->video;
                    $ssExt = pathinfo($saData[$i]['video'], PATHINFO_EXTENSION);
                    $saData[$i]['image_ext'] = $ssExt;
                } else {
                    $saData[$i]['video'] = null;
                }
				
                $saData[$i]['is_flipped'] = $get_post->is_flipped;
                $saData[$i]['is_rotated'] = $get_post->is_rotated;
                $saData[$i]['owner_name'] = $get_post->owner_name;
                $saData[$i]['video_thumbnail'] = $get_post->video_thumbnail;
                $saData[$i]['video_thumb_title'] = $get_post->video_thumb_title;
                $saData[$i]['decription'] = $get_post->decription;
                $saData[$i]['tag'] = $get_post->tag;
                $saData[$i]['filter'] = $get_post->filter;
                $saData[$i]['type'] = $get_post->type;

                $saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
                $saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
                $saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
                $saGetRepostsCount = \App\SharePost::select('user_id')->where('feed_id',$get_post->id)->count();

                 $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
                 ->join('users', 'users.id', '=', 'user_tags.tag_user')
                 ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
                $is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
                if($is_saved)
                {
                    $is_saved = 'yes';
                }
                else
                {
                    $is_saved = 'no';
                }
                
                 /***************************************************************/
                $stData2 = array();
                $getStatus = DB::table('post_tags')
                            ->where('post_id', $get_post->id)
                            ->join('users', 'users.id', '=', 'post_tags.user_id')
                            ->join('user_detail', 'user_detail.user_id', '=', 'users.id')
                             ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
                            ->get();
                   // if(!$getStatus->isEmpty()){
                        
                   //      for ($i = 0; count($getStatus) > $i; $i++) {
                   //          $stData2[$i]['tag_list'] = DB::table('post_tags')
                   //                  ->join('users', 'users.id', '=', 'post_tags.user_id')
                   //                  ->join('user_detail', 'user_detail.user_id', '=', 'users.id')
                   //                  ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
                   //                  ->WhereRaw("post_tags.post_id=".$getStatus[$i]->id)
                   //                  ->get();
                   //      }
                   //  }        

                /**************************************************************/
                   $saData[$i]['tag_list']=$getStatus;



                $saData[$i]['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
                $saData[$i]['is_saved'] = $is_saved;
                $saData[$i]['detail'] = $saGetDetail;
                $saData[$i]['likes'] = $saGeLikes;
                $saData[$i]['comments'] = $saGeComments;
                $saData[$i]['reposts'] = $saGetRepostsCount;
                $saData[$i]['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
                $saData[$i]['tags'] = $saGetTag;
                $saData[$i]['created_at'] = $get_post->created_at;

                $saData[$i]['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
                $saData[$i]['object_type'] = 1;
                $saData[$i]['is_repost'] = $ids[$i]->is_repost;
                $saData[$i]['poster_name'] = $get_post->poster_name;
				$saData[$i]['original_poster_id'] = $get_post->original_poster_id;
            }
            else
            {
				if($ids[$i]->share_user_id){					
					$poster_name = User::where('id', $ids[$i]->share_user_id)->pluck('name')->first();
				} else {
					$poster_name = "";
				}

				$saData[$i] = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
							$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
							->leftJoin('user_detail', 'user_detail.user_id', '=', 'shop_profiles.user_id')
							->select('shop_profiles.id', 'shop_profiles.name as shop_name', 'shop_profiles.company_name', 'user_detail.profile_image_url as shop_image_url', 'shop_profiles.shop_name as user_name', 'users.tagname as user_tagname','user_detail.category');
					}])
					->withCount([
						'shop_product_meta as likes' => function($query){
							$query->where('type', 'like');
						},
						'shop_product_meta as comments' => function($query){
							$query->where('type', 'comment');
						},
						'shop_product_meta as is_liked' => function($query) use ($user_id){
							$query->where([['type', 'like'], ['user_id', $user_id]]);
						},
						'shop_product_meta as is_saved' => function($query) use ($user_id){
							$query->where([['type', 'save'], ['user_id', $user_id]]);
						}
					])
					->where([['id', $ids[$i]->id], ['status', 1]])->first();
				
                $get_profile = DB::table('shop_profiles')->where('user_id',  $ids[$i]->user_id)->select('name', 'rating')->first();
                $saData[$i]['posted_by'] = User::where('id', $ids[$i]->user_id)->pluck('name')->first();
                $saData[$i]['seller_rating'] = $get_profile->rating;
				$saData[$i]['is_repost'] = $ids[$i]->is_repost;
                $saData[$i]['poster_name'] = $saData[$i]['name'];
                $saData[$i]['name'] = $poster_name;
                $saData[$i]['original_poster_id'] = $ids[$i]->user_id;
                $saData[$i]['object_type'] = 0;
            }
        }
        return $saData;
    }

	public function userDeleteAccount(Request $request){
		
		$validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
		if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
		
		$saUsers = User::where('id', $request->user_id)->first();
		$data['name'] = $saUsers->name;
		$ssUsrl = url('delete-account-veryfied/' . base64_encode($saUsers->id.':'.$saUsers->email));
		$to_email = $saUsers->email;
		$subject = 'ARTfora Delete Account email verification';
		$message['to_email'] = $to_email;
		$message['ssUsrl'] = $ssUsrl;
		$message['view'] = "delete_account";
					
		$this->custom_email($to_email, $from=NULL, $subject, $message);
		return $this->sendResponse(array(), 'Confirm Email for Delete Account!!');
	}
	
	public function userReactiveAccountMail(Request $request)
	{		
		$Users = User::where('status',3)->get();
		$message = [];
		foreach($Users as $saUsers){
			$data['name'] = $saUsers->name;
			$ssUsrl = url('reactive-account-veryfied/' . base64_encode($saUsers->id.':'.$saUsers->email));
			$to_email = $saUsers->email;
			$subject = 'ARTfora Reactive Account';
			$message['to_email'] = $to_email;
			$message['ssUsrl'] = $ssUsrl;
			$hourdiff = round((strtotime($saUsers->delete_email_verified_at) - strtotime(date('Y-m-d H:i:s')))/3600, 2);
			if($hourdiff == 24){
				$message['view'] = "reactive_account";						
				$this->custom_email($to_email, $from=NULL, $subject, $message);
			} else if($hourdiff == 168){
				$message['view'] = "reactive_week_account";						
				$this->custom_email($to_email, $from=NULL, $subject, $message);
			} else {
				
			}
		}
		return $this->sendResponse($message, 'Confirm Email for Reactive Account!!');
	}
	
    public function userDetailById(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        $ssImageUrl = 'https://artfora.net/images/profile_image';
        $saGetUserDetail = DB::table('users')->select('users.id', 'users.status', 'users.tagname', 'users.name', 'users.email', 'users.email_verified_at', 'users.bdate', 'users.commission', 'ud.filter', 'ud.category', 'ud.private_description', 'ud.description', 'ud.profile_image_url', 'ud.privete_profile_url', 'ud.private_name', 'sp.shop_name as shop_profile_name')
                ->leftJoin('user_detail as ud', 'users.id', '=', 'ud.user_id')
                ->leftJoin('shop_profiles as sp', 'users.id', '=', 'sp.user_id');
		if(is_numeric($request->user_id))
		{
		   $saGetUserDetail->where('users.id', $request->user_id); 
		}
		else
		{
			$saGetUserDetail->where('users.tagname', $request->user_id); 
		}
        
        $saGetUserDetail = $saGetUserDetail->first();
        
        if($saGetUserDetail->status != 2)
        {
            $snIsFollow = Followers::where('follewers_id', $request->user_id)->where('user_id', $request->my_id)->get()->first();
            if (empty($snIsFollow)) {
                //Not Follow
                $saGetUserDetail->is_follow = FALSE;
            } else {
                //Follow
                $saGetUserDetail->is_follow = TRUE;
            }
                                                              
          $isBlockedByMe = DB::table('blocked_users')->where('user_id', $request->my_id)->where('blocked_userid', $request->user_id)->get()->first();
          if (empty($isBlockedByMe)) {
              //Not blocked by me
              $saGetUserDetail->is_blocked_by_me = FALSE;
          } else {
              //blocked by me
              $saGetUserDetail->is_blocked_by_me = TRUE;
          }
                                                              
          $isBlockedMe = DB::table('blocked_users')->where('user_id', $request->user_id)->where('blocked_userid', $request->my_id)->get()->first();
          if (empty($isBlockedMe)) {
              //Not blocked me
              $saGetUserDetail->is_blocked_me = FALSE;
          } else {
              //blocked me
              $saGetUserDetail->is_blocked_me = TRUE;
          }
            
            $saGetUserDetail->urls = DB::table('user_urls')->where('user_id', $request->user_id)->get();
        }
        else
        {
            return $this->sendError('User is blocked by admin.', array());
        }
        $saGetUserDetail->shop_description = DB::table('shop_profiles')->select('shop_description','shop_image_url')->where('user_id', $request->user_id)->first();
		
		$check_invitat = Invitation::where([['invited_id', $request->user_id], ['user_id', $request->my_id]])->where('status',1)->where('deleted_at',Null)->count();
		$saGetUserDetail->invite_status = false;
		if($check_invitat > 0)
		$saGetUserDetail->invite_status = true;
        return $this->sendResponse($saGetUserDetail, 'User Detail');
    }
    
    public function report(Request $request){
        $report=DB::table('reports')->insert([
            'user_id'  => $request->user_id,
            'story_id' => $request->story_id,
            'report_type' => $request->report_type,
            'reported_bit' => $request->reported_bit,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            ]);
        return $this->sendResponse($report, 'Reported Successfully');   
        
    }
    
	public function sharePostStatus(Request $request) { 
		
		$validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
		else 
		{
			$snUserId = $request->user_id;
            $user_id = $request->user_id;
            $feed_id = $request->feed_id;
            $file_type = $request->file_type;
			$i =0;
			if($file_type == 'post')
			{
				$get_post = DB::table('post')
								->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
								->distinct('id')
								->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
								->join('users', 'post.user_id', '=', 'users.id')
								->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('post.id', $feed_id)
								->first(); 

				$saData['id'] = $get_post->id;
				$saData['user_id'] = $get_post->user_id;
				$saData['user_name'] = $get_post->name;
				$saData['tagname'] = $get_post->tagname;
				$saData['email'] = $get_post->email;
				$saData['user_profile_image'] = $get_post->profile_image_url;

				if (!empty($get_post->video)) {
					$saData['video'] = 'https://artfora.net/images/post/Video/' . $get_post->video;
					$ssExt = pathinfo($saData['video'], PATHINFO_EXTENSION);
					$saData['image_ext'] = $ssExt;
				} else {
					$saData['video'] = null;
				}
				$saData['is_flipped'] = $get_post->is_flipped;
				$saData['is_rotated'] = $get_post->is_rotated;
				$saData['owner_name'] = $get_post->owner_name;
				$saData['decription'] = $get_post->decription;
				$saData['tag'] = $get_post->tag;
				$saData['filter'] = $get_post->filter;
				$saData['type'] = $get_post->type;

				$saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
				$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
				$saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
				 $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
				 ->join('users', 'users.id', '=', 'user_tags.tag_user')
				 ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
				$is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
				if($is_saved)
				{
					$is_saved = 'yes';
				}
				else
				{
					$is_saved = 'no';
				}
				
				 /***************************************************************/
				$stData2 = array();
				$getStatus = DB::table('post_tags')
							->where('post_id', $get_post->id)
							->join('users', 'users.id', '=', 'post_tags.user_id')
							->join('user_detail', 'user_detail.user_id', '=', 'users.id')
							 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
							->get();
				
				$saData['tag_list']=$getStatus;

				$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
				$saData['is_saved'] = $is_saved;
				$saData['detail'] = $saGetDetail;
				$saData['likes'] = $saGeLikes;
				$saData['comments'] = $saGeComments;
				$saData['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
				$saData['tags'] = $saGetTag;
				$saData['created_at'] = $get_post->created_at;

				$saData['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
				
				foreach ($saData['files'] AS $mykey=>$sFile) {
					if($mykey == 0){
						$saStatus = new Status();
						$saStatus->user_id = $user_id;
						$saStatus->type = $sFile->file_type;
						if($sFile->file_type == 1){
							$saStatus->file = $sFile->file_name;
						} else{
							$saStatus->file = $sFile->thumb;
						}
                        $saStatus->resized_file_name = $sFile->resized_file_name;
						$saStatus->vedio_image = $sFile->thumb;
						$saStatus->swipeUpUrl = 'nil';
						$saStatus->x = $sFile->x;
						$saStatus->y = $sFile->y;
						$saStatus->tag_users = $sFile->tag_users;
						$saStatus->user_ids = $sFile->user_ids;
						$saStatus->share_type = 'post';
						$saStatus->feed_id = $feed_id;
						$saStatus->save();	
					
						if(count(json_decode($sFile->user_ids)) > 0){
							$tag_list = json_decode($sFile->user_ids);
							for($i=0;$i<count($tag_list);$i++){
								$data_array=array(
									'story_id'=>$saStatus->id,
									'user_id'=>$tag_list[$i]
									);
								DB::table('tag_users')->insertGetId($data_array);
							}
						}	
					}					
				}
				$saData['object_type'] = 1;
			} 			
			if($file_type == 'shop')
			{
				$saData = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
						$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
						->leftJoin('user_detail', 'user_detail.user_id', '=', 'shop_profiles.user_id')
						->select('shop_profiles.id', 'shop_profiles.name as shop_name', 'shop_profiles.company_name', 'user_detail.profile_image_url as shop_image_url', 'users.name as user_name', 'users.tagname as user_tagname');
				}])
				->withCount([
					'shop_product_meta as likes' => function($query){
						$query->where('type', 'like');
					},
					'shop_product_meta as comments' => function($query){
						$query->where('type', 'comment');
					},
					'shop_product_meta as is_liked' => function($query) use ($user_id){
						$query->where([['type', 'like'], ['user_id', $user_id]]);
					},
					'shop_product_meta as is_saved' => function($query) use ($user_id){
						$query->where([['type', 'save'], ['user_id', $user_id]]);
					}
				])
				->where([['id', $feed_id]])
				->first();
				//, ['status', 1]

				$get_profile = DB::table('shop_profiles')->where('user_id',  $saData->user_id)->select('name', 'rating')->first();
				$saData['posted_by'] = User::where('id', $user_id)->pluck('name')->first();
				$saData['seller_rating'] = $get_profile->rating??0.0;
				$saData['object_type'] = 0;
				
				foreach ($saData['shop_product_files'] AS $mykey=>$sFile) {
					if($mykey == 0){
						$saStatus = new Status();
						$saStatus->user_id = $user_id;
						$saStatus->type = $sFile['file_type'];
						if($sFile['file_type'] == 2){
							$saStatus->file = $sFile['thumb'];
						} else{
							$saStatus->file = $sFile['file_name'];
						}
                        $saStatus->resized_file_name = $sFile['resized_file_name'];
						$saStatus->vedio_image = $sFile['thumb'];
						$saStatus->swipeUpUrl = 'nil';
						$saStatus->x = json_encode([]);
						$saStatus->y = json_encode([]);
						$saStatus->tag_users = json_encode([]);
						$saStatus->user_ids = json_encode([]);
						$saStatus->share_type = 'shop';
						$saStatus->feed_id = $feed_id;
						$saStatus->save();		
					}						
				}
			}
		}
			// CHECK IF ANY USER FOLLOW CURRENT USER THEN SEND NOTIFICATION TO THEM
		$check_active_notification_users = DB::table('notification_statuses as ns')
												->join('users', 'users.id', '=', 'ns.user_id')
												->where([['ns.type', 3], ['ns.follewers_id', $request['user_id']]])
												->select('users.id', 'users.app_token')->get();
		if(count($check_active_notification_users) > 0)
		{
			
			$tokens = array();
			$get_user = DB::table('users')
						->join('user_detail', 'user_detail.user_id', '=', 'users.id')
						->where('users.id', $request['user_id'])
						->select('users.name', 'user_detail.profile_image_url')
						->first();
						
			$title = 'New Post';
			$body = $get_user->name.' has created a new post.';
			
			foreach($check_active_notification_users as $active_user)
			{
				$tokens[] = $active_user->app_token;
			}
			$this->send_push($title, $body, $tokens, $request['user_id'], $saStatus->id, $get_user->name, $get_user->profile_image_url);
		
		}
        return $this->sendResponse($saData, 'Share status Successfully');
	}
    
    /*********************FEED***************************/

    public function single_feed(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $snUserId = $request->user_id;
            $user_id = $request->user_id;
            $feed_id = $request->feed_id;
            $file_type = $request->file_type;
			$i =0;
			if($file_type == 'post')
			{
				$get_post = DB::table('post')
								->select('post.id','post.parent_id', 'post.type', 'users.id as user_id', 'users.name', 'ouser.name as poster_name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
								->distinct('id')
								->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
								->join('users', 'post.user_id', '=', 'users.id')
								->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->leftJoin('users as ouser', 'post.parent_user_id', '=', 'ouser.id')
								->where('post.id', $feed_id)
								->first(); 

				$saData['id'] = $get_post->id;
				$saData['user_id'] = $get_post->user_id;
				$saData['user_name'] = $get_post->name;
				$saData['tagname'] = $get_post->tagname;
				$saData['email'] = $get_post->email;
				$saData['user_profile_image'] = $get_post->profile_image_url;

				if (!empty($get_post->video)) {
					$saData['video'] = 'https://artfora.net/images/post/Video/' . $get_post->video;
					$ssExt = pathinfo($saData['video'], PATHINFO_EXTENSION);
					$saData['image_ext'] = $ssExt;
				} else {
					$saData['video'] = null;
				}
				$saData['is_flipped'] = $get_post->is_flipped;
				$saData['is_rotated'] = $get_post->is_rotated;
				$saData['owner_name'] = $get_post->owner_name;
				$saData['decription'] = $get_post->decription;
				$saData['tag'] = $get_post->tag;
				$saData['filter'] = $get_post->filter;
				$saData['type'] = $get_post->type;

				$saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
				$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
				$saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
				 $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
				 ->join('users', 'users.id', '=', 'user_tags.tag_user')
				 ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
				$is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
				if($is_saved)
				{
					$is_saved = 'yes';
				}
				else
				{
					$is_saved = 'no';
				}
				
				 /***************************************************************/
				$stData2 = array();
				$getStatus = DB::table('post_tags')
							->where('post_id', $get_post->id)
							->join('users', 'users.id', '=', 'post_tags.user_id')
							->join('user_detail', 'user_detail.user_id', '=', 'users.id')
							 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
							->get();
				
				$saData['tag_list']=$getStatus;

				$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
				$saData['is_saved'] = $is_saved;
				$saData['detail'] = $saGetDetail;
				$saData['likes'] = $saGeLikes;
				$saData['comments'] = $saGeComments;
				$saData['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
				$saData['tags'] = $saGetTag;
				$saData['created_at'] = $get_post->created_at;

				$saData['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
				$saData['object_type'] = 1;
				$saData['is_repost'] = 0;
				$saData['poster_name'] = "";
				if(!empty($get_post->parent_id)){
					$saData['is_repost'] = 1;
					$saData['poster_name'] = $get_post->poster_name;
				} 
			}
			else if($file_type == 'shop')
			{
				$saData = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
						$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
						->leftJoin('user_detail', 'user_detail.user_id', '=', 'shop_profiles.user_id')
						->select('shop_profiles.id', 'shop_profiles.name as shop_name', 'shop_profiles.company_name', 'user_detail.profile_image_url as shop_image_url', 'users.name as user_name', 'users.tagname as user_tagname');
				}])
				->withCount([
					'shop_product_meta as likes' => function($query){
						$query->where('type', 'like');
					},
					'shop_product_meta as comments' => function($query){
						$query->where('type', 'comment');
					},
					'shop_product_meta as is_liked' => function($query) use ($user_id){
						$query->where([['type', 'like'], ['user_id', $user_id]]);
					},
					'shop_product_meta as is_saved' => function($query) use ($user_id){
						$query->where([['type', 'save'], ['user_id', $user_id]]);
					}
				])
				->where([['id', $feed_id]])
				->first();
				//, ['status', 1]

				$get_profile = DB::table('shop_profiles')->where('user_id',  $user_id)->select('name', 'rating')->first();
				$saData['posted_by'] = User::where('id', $user_id)->pluck('full_name')->first();
				$saData['seller_rating'] = $get_profile->rating;
				$saData['object_type'] = 0;
			} else{
				$get_post = DB::table('post')
								->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
								->distinct('id')
								->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
								->join('users', 'post.user_id', '=', 'users.id')
								->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('post.id', $feed_id)
								->first(); 

				$saData['id'] = $get_post->id;
				$saData['user_id'] = $get_post->user_id;
				$saData['user_name'] = $get_post->name;
				$saData['tagname'] = $get_post->tagname;
				$saData['email'] = $get_post->email;
				$saData['user_profile_image'] = $get_post->profile_image_url;

				if (!empty($get_post->video)) {
					$saData['video'] = 'https://artfora.net/images/post/Video/' . $get_post->video;
					$ssExt = pathinfo($saData['video'], PATHINFO_EXTENSION);
					$saData['image_ext'] = $ssExt;
				} else {
					$saData['video'] = null;
				}
				$saData['is_flipped'] = $get_post->is_flipped;
				$saData['is_rotated'] = $get_post->is_rotated;
				$saData['owner_name'] = $get_post->owner_name;
				$saData['decription'] = $get_post->decription;
				$saData['tag'] = $get_post->tag;
				$saData['filter'] = $get_post->filter;
				$saData['type'] = $get_post->type;

				$saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
				$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
				$saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
				 $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
				 ->join('users', 'users.id', '=', 'user_tags.tag_user')
				 ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
				$is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
				if($is_saved)
				{
					$is_saved = 'yes';
				}
				else
				{
					$is_saved = 'no';
				}
				
				 /***************************************************************/
				$stData2 = array();
				$getStatus = DB::table('post_tags')
							->where('post_id', $get_post->id)
							->join('users', 'users.id', '=', 'post_tags.user_id')
							->join('user_detail', 'user_detail.user_id', '=', 'users.id')
							 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
							->get();
				
				$saData['tag_list']=$getStatus;

				$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
				$saData['is_saved'] = $is_saved;
				$saData['detail'] = $saGetDetail;
				$saData['likes'] = $saGeLikes;
				$saData['comments'] = $saGeComments;
				$saData['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
				$saData['tags'] = $saGetTag;
				$saData['created_at'] = $get_post->created_at;

				$saData['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
				$saData['object_type'] = 1;
			}
		
            /* $saGetFollowingId = \App\Followers::select('user_id')->where('follewers_id', $snUserId)->get()->toArray();

            $saFollowingId = array_column($saGetFollowingId, 'user_id');
            array_push($saFollowingId, $snUserId);


            $ssUrl = url('images/post/Image/');
            $saGetAllPost = DB::table('post')
                            ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                            ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                            ->join('users', 'post.user_id', '=', 'users.id')
                            ->whereIn('post.user_id', $saFollowingId)
                            ->get()->toArray();



            $saData = array();
            for ($i = 0; count($saGetAllPost) > $i; $i++) {
                $saData[$i]['id'] = $saGetAllPost[$i]->id;
                $saData[$i]['user_id'] = $saGetAllPost[$i]->user_id;
                $saData[$i]['user_name'] = $saGetAllPost[$i]->name;
                $saData[$i]['user_profile_image'] = $saGetAllPost[$i]->profile_image_url;

				if(file_exists(public_path("images/post/Image/" . $saGetAllPost[$i]->image)) && !empty($saGetAllPost[$i]->image)){
					$saData[$i]['image'] = url('/').'/public/images/post/Image/' . $saGetAllPost[$i]->image;
					$ssExt = pathinfo($saData[$i]['image'], PATHINFO_EXTENSION);
					$saData[$i]['image_ext'] = $ssExt;
					list($width, $height) = getimagesize($saData[$i]['image']);
					$saData[$i]['image_width'] = $width;
					$saData[$i]['image_height'] = $height;
				} else {
					$saData[$i]['image'] = "";
					$saData[$i]['image_ext'] = "";
					$saData[$i]['image_width'] = "";
					$saData[$i]['image_height'] = "";
				}
				if (!empty($saGetAllPost[$i]->video)) {
					$saData[$i]['video'] = url('/').'/public/images/post/Video/' . $saGetAllPost[$i]->video;
					$ssExt = pathinfo($saData[$i]['video'], PATHINFO_EXTENSION);
					$saData[$i]['image_ext'] = $ssExt;
				} else {
					$saData[$i]['video'] = null;
				}
                $saData[$i]['is_flipped'] = $saGetAllPost[$i]->is_flipped;
                $saData[$i]['is_rotated'] = $saGetAllPost[$i]->is_rotated;
                $saData[$i]['owner_name'] = $saGetAllPost[$i]->owner_name;
                $saData[$i]['decription'] = $saGetAllPost[$i]->decription;
                $saData[$i]['tag'] = $saGetAllPost[$i]->tag;
                $saData[$i]['filter'] = $saGetAllPost[$i]->filter;

                $saGetDetail = \App\PostDetail::select('title', 'description')->where('post_id', $saGetAllPost[$i]->id)->get()->toArray();
                $saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $saGetAllPost[$i]->id], ['type', 'like']])->count();
                $saGetTag = DB::table('user_tags')->select('tag_user')->where('post_id', $saGetAllPost[$i]->id)->groupBy('tag_user')->get()->toArray();
                $saAllTags = array_column($saGetTag, 'tag_user');
                $saData[$i]['is_like'] = with(new \App\PostLike())->get_user_is_like($request->user_id, $saGetAllPost[$i]->id);
                $saData[$i]['detail'] = $saGetDetail;
                $saData[$i]['likes'] = $saGeLikes;
                $saData[$i]['tags'] = $saAllTags;
                $saData[$i]['created_at'] = $saGetAllPost[$i]->created_at;
            }
            $desired_object = collect($saData)->filter(function($item) use($feed_id){
            return $item['id'] == $feed_id;
            })->first(); */
			
            return $this->sendResponse($saData, 'Get Feed Successfully');
        }
    }
    
    
    
    public function user_values_count(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            $user_id = $request->user_id;
            $blocked_users = DB::table('blocked_users')->where('user_id',$user_id)->pluck('blocked_userid')->toArray();  
            
            $following_count = DB::table('followers')->join('users', 'users.id', '=', 'followers.follewers_id')->where([['users.status', 1], ['user_id', $user_id], ['followers.deleted_at', NULL]])->count();
            $follower_count = DB::table('followers')->join('users', 'users.id', '=', 'followers.user_id')->where([['users.status', 1], ['follewers_id', $user_id], ['followers.deleted_at', NULL]])->count();
            $unfollow_count = DB::table('followers')->join('users', 'users.id', '=', 'followers.user_id')->where([['users.status', 1], ['user_id', $user_id], ['followers.deleted_at', '!=', NULL]])->count();
            $block_user_counter = count($blocked_users);
            
			//$saved_posts_count = DB::table('saved_posts')->where('user_id', $user_id)->count();
			$saved_posts_count = DB::table('shop_products')->where('user_id', $user_id)->where('status', 1)->count();
			
            $get_all_posts = DB::table('post')
                                ->select('post.id', 'post.user_id', 'post.deleted_at', 'post.type')
                                ->distinct('id')
                                ->join('users', 'post.user_id', '=', 'users.id')
                                ->whereIn('post.user_id', array($user_id))
                                ->whereNotIn('post.user_id',$blocked_users)
                                ->where('post.deleted_at', NULL)
                                ->get();
								
			$get_all_share_posts = DB::table('post')
                                ->select('post.id', 'post.user_id', 'post.deleted_at', 'post.type')
                                ->join('share_posts', 'post.id', '=', 'share_posts.feed_id')
                                ->where('share_posts.user_id',$user_id)
                                ->whereNotIn('share_posts.user_id',$blocked_users)
                                ->where('post.deleted_at', NULL)
                                ->where('share_posts.feed_type', 1)
                                ->get();
			
			$get_all_share_products = DB::table('shop_products')
                                ->select('shop_products.id', 'shop_products.user_id', 'shop_products.deleted_at')
                                ->join('share_posts', 'shop_products.id', '=', 'share_posts.feed_id')
                                ->where('share_posts.user_id',$user_id)
                                ->where('shop_products.deleted_at', NULL)
                                ->where('share_posts.feed_type', 0)
                                ->get();						
								
            $public_posts_count = ($get_all_posts->where('type', 'public')->count() +  $get_all_share_posts->where('type', 'public')->count() + $get_all_share_products->count());
            $private_posts_count = ($get_all_posts->where('type', 'private')->count() +  $get_all_share_posts->where('type', 'private')->count());
            
            return response()->json(['code' => 200, 'message' => 'Counts', 'following_count' => $following_count, 'follower_count' => $follower_count, 'unfollow_count' => $unfollow_count,'block_user_counter' => $block_user_counter, 'public_posts_count' => $public_posts_count, 'private_posts_count' => $private_posts_count, 'saved_posts_count' => $saved_posts_count]);
        }
    }
    
    
    public function get_public_private_user_detail(Request $request)
	{
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            $user_id = $request->user_id;
            $status = $request->status;
            $get_user = User::where('id', $user_id)->first();

            if($status == 'private')
            {
                $get_user_detail = UserDetail::where('user_id', $user_id)
                                ->select('private_description', 'privete_profile_url', 'private_email as contact_email', 'private_name')
                                ->first();
                $get_user_detail->urls = DB::table('user_urls')->where([['user_id', $user_id], ['type', 'private']])->get();
				$get_user->private_name = isset($get_user_detail->private_name)?$get_user_detail->private_name:'';
            }
            else if($status == 'public'){
                $get_user_detail = UserDetail::where('user_id', $user_id)
                                ->select('description', 'profile_image_url', 'email as contact_email', 'public_name')
                                ->first();
                $get_user_detail->urls = DB::table('user_urls')->where([['user_id', $user_id], ['type', 'public']])->get();
				$get_user->public_name = isset($get_user_detail->public_name)?$get_user_detail->public_name:'';
            }
            else if($status == 'shop')
            {
                $get_user_detail = DB::table('shop_profiles')->where('user_id', $user_id)->first();
                if($get_user_detail)
                {
                    $get_user_detail->urls = DB::table('user_urls')->where([['user_id', $user_id], ['type', 'shop']])->get();
                }
                else
                {
                    $get_user_detail = array();
                }
				$get_user->shop_name = isset($get_user_detail->shop_name)?$get_user_detail->shop_name:'';
            }
            else
            {
                $get_user_detail = UserDetail::where('user_id', $user_id)
                                ->select('filter', 'category')
                                ->first();
                $interest = DB::table('interest_group')->select('interest_name')->where([['user_id', $user_id], ['deleted_at', NULL]])->get();
                return response()->json(['code' => 200, 'message' => 'User Detail', 'data' => $get_user_detail, 'interest' => $interest, 'user_info' => $get_user]);
            }        
            return response()->json(['code' => 200, 'message' => 'User Detail', 'data' => $get_user_detail, 'user_info' => $get_user]);
        }
    }
    
    
    public function get_follow_unfollow_followers_list(Request $request){
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'status' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            $user_id = $request->user_id;
            $status = $request->status;
            $sort_by = $request->sort_by;
            $search = $request->search;
            $ssImageUrl = '';
            
            if($status == 'following'){
                $user_list = DB::table('followers')->join('users', 'users.id', '=', 'followers.follewers_id')->where([['users.status', 1], ['user_id', $user_id], ['followers.deleted_at', NULL]])->select('follewers_id AS user_id','followers.created_at as created_at');
                if($sort_by == 1) {
                    $user_list = $user_list->orderBy('followers.created_at', 'DESC')->get();
                }
				else if($sort_by == 0) {
                    $user_list = $user_list->orderBy('users.name', 'DESC')->get();
                }
                else if($sort_by == 3) {
                    $user_list = $user_list->orderBy('users.name', 'ASC')->get();
                }
				else {
                    $user_list = $user_list->orderBy('followers.created_at', 'ASC')->get();
                }
                $bool = TRUE;
            }
            else if($status == 'unfollow'){
                $user_list = DB::table('followers')->join('users', 'users.id', '=', 'followers.user_id')->where([['users.status', 1], ['user_id', $user_id], ['followers.deleted_at', '!=', NULL]])->select('follewers_id AS user_id','followers.created_at as created_at');
                                                              
                if($sort_by == 1) {
                   $user_list = $user_list->orderBy('followers.created_at', 'DESC')->get();
                }
				else if($sort_by == 0) {
                    $user_list = $user_list->orderBy('users.name', 'DESC')->get();
                }
				else if($sort_by == 3) {
                    $user_list = $user_list->orderBy('followers.created_at', 'ASC')->get();
                }
                else {
                    $user_list = $user_list->orderBy('users.name', 'ASC')->get();
                }
                $bool = FALSE;
            }
            else{
                $user_list = DB::table('followers')->join('users', 'users.id', '=', 'followers.user_id')->where([['users.status', 1], ['follewers_id', $user_id], ['followers.deleted_at', NULL]])->select('user_id','followers.created_at as created_at');
                if($sort_by == 1)
                {
                    $user_list = $user_list->orderBy('followers.created_at', 'DESC')->get();
                }
				else if($sort_by == 0)
                {
                    $user_list = $user_list->orderBy('users.name', 'DESC')->get();
                }
				else if($sort_by == 3)
                {
                    $user_list = $user_list->orderBy('followers.created_at', 'ASC')->get();
                }
                else
                {
                    $user_list = $user_list->orderBy('users.name', 'ASC')->get();
                }
                $bool = FALSE;
            }
            
            $get_user_array = array();
            $user_info = array();
            foreach($user_list as $userTest) {
                $user_info[$userTest->user_id] = $userTest->created_at;
            }
            $user_list = $user_list->pluck('user_id')->toArray();
            $ids_ordered = implode(',', $user_list);
            
            
            if(count($user_list) > 0)
            {
                $get_user_array = DB::table('users')
                                ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                                ->select('users.id', 'users.name', 'users.tagname', 'users.email', 'users.email_verified_at', 'user_detail.filter',
                                    DB::raw("CONCAT('" . $ssImageUrl . "',user_detail.profile_image_url) as image"), 
                                    'category', 'private_description', 'privete_profile_url', 'description')
                                ->whereIn('users.id', $user_list);
                                    
                                if(!empty($search))
                                {
                                    $get_user_array->where('users.tagname', 'LIKE', '%'.$search.'%');
                                }
                                
                $get_user_array = $get_user_array->orderByRaw("FIELD(users.id, $ids_ordered)")->get();
                
                $get_users = collect($get_user_array)->map(function ($item, $key) use ($bool) {
                    $item->is_follow = $bool;
                });
                
                foreach($get_user_array as $user)
                {
                    $user->created_at = $user_info[$user->id];
                    $user->time_ago = $this->time_diff($user->created_at);
                    $user->urls = DB::table('user_urls')->where('user_id', $user->id)->get();
                }
            }
            
            return response()->json(['code' => 200, 'message' => 'User Detail', 'data' => $get_user_array]);
            
        }
    }
    
    
    public function update_public_private_user_detail(Request $request) {
        
        $status = $request->status;
        $user_id = $request->user_id;
        $description = $request->description;
        $urls = $request->urls;
        $email = $request->email;
        
        
        $get_user_detail = UserDetail::where('user_id', $user_id)->first();
        
        if (!empty($request['profile_image'])) {
            if ($request->hasFile('profile_image')) {
                $ssProfileImage = $request->file('profile_image');
                $profile_image = 'https://artfora.net/images/profile_image/'.time().str_replace(' ', '', $ssProfileImage->getClientOriginalName());
                $destinationPath1 = public_path('/images/profile_image/');
                $ssProfileImage->move($destinationPath1, $profile_image);
            }
        }
        
        if($status == 'private')
        {   
			$private_name = $request->private_name;
            if(!isset($profile_image)){
                $profile_image = $get_user_detail->privete_profile_url;
            }
            
            $get_user_detail->private_description = $description;
            $get_user_detail->privete_profile_url = $profile_image;
            $get_user_detail->private_email = $email;
            $get_user_detail->private_name = $private_name;
            DB::table('user_urls')->where([['user_id', $user_id], ['type', 'private']])->delete();
			//DB::table('users')->where('id', $user_id)->update(['name'=>$private_name,'is_private'=>1]);
        }
        else
        {
			$public_name = $request->public_name;
			DB::table('users')->where('id', $user_id)->update(['name'=>$public_name]);
			
            if(!isset($profile_image)){
                $profile_image = $get_user_detail->profile_image_url;
            }
            
            $get_user_detail->profile_image_url = $profile_image;
            $get_user_detail->description = $description;
            $get_user_detail->email = $email;
            $get_user_detail->public_name = $public_name;
            DB::table('user_urls')->where([['user_id', $user_id], ['type', 'public']])->delete();
        }
        
        // ADD USER URLS
        if($request->has('urls'))
        {
            array_multisort(array_column($urls, 'priority'), SORT_ASC, $urls);
            $urls = collect($urls)->map(function ($item, $key) use ($user_id) {
                $item['user_id'] = $user_id;
                return $item;
            });
            $urls = $urls->map(function ($item) {
                return array_only($item, ['url', 'type', 'user_id']);
            });
            $urls = $urls->toArray();
            DB::table('user_urls')->insert($urls);
        }
        // ADD USER URLS
		
        $get_user_detail->save();
        $get_user_detail->urls = DB::table('user_urls')->where('user_id', $user_id)->get();

        return response()->json(["code" => 200, "message" => "Updated User Detail", "data" => $get_user_detail]);
    }
    
    
    public function delete_post(Request $request) {
        $validator = Validator::make($request->all(), [
                    'post_id' => 'required',
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $post_id = $request->post_id;
            $user_id = $request->user_id;
            
            $data = array(
                'deleted_at' => Carbon::now()    
            );


            $result=DB::table('post_files')->where('post_id', $post_id)->get();
                for($i=0;$i<count($result);$i++){
                    $file_name=$result[$i]->file_name;
                    $thumb=$result[$i]->thumb;
                    if($file_name != ""){
                      unlink(public_path("images/post/new_images/".$file_name));
                    }
                    if($thumb != ""){
                        unlink(public_path("images/post/new_images/thumb/".$thumb));
                    }
                    $file_nameRes=$result[$i]->resized_file_name;
                    if($file_nameRes != ""){
                        unlink(public_path("images/post/new_images/".$file_nameRes));
                    }
                    $where = array("id" => $result[$i]->id);        
                    DB::table('post_files')->where($where)->delete();                    
                }
              
            // $delete_comments = PostComment::where('post_id', $post_id)->update($data);
            // $delete_likes = PostLike::where('post_id', $post_id)->update($data);
            // $delete_detail = PostDetail::where('post_id', $post_id)->update($data);
            // $delete_post = Post::where('id', $post_id)->update($data);

           // $get_image = Post::where('id', $post_id)->first();
            // print_r($get_image->image);
         //   $img_url = "images/post/Image/".$get_image->image;
            // echo $img_url;
// die;
            // $image_path = "https://artfora.net/images/post/Image/15784275481image-1578427546.335672.jpeg";  // Value is not URL but directory file path
       //     unlink(public_path($img_url));

            // File::delete($image_path);

// die;

            $delete_comments = PostComment::where('post_id', $post_id)->forceDelete();
            $delete_likes = PostLike::where('post_id', $post_id)->forceDelete();
            $delete_detail = PostDetail::where('post_id', $post_id)->forceDelete();
            $delete_post = Post::where('id', $post_id)->forceDelete();

            return $this->sendResponse(array(), 'Post Deleted successfully.');
        }
    }

    public function delete_repost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'repost_id' => 'required',
          
        ]);

        if ($validator->fails()) 
        {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
        else 
        {
            $repost_id = $request->repost_id;
            $delete_post = SharePost::where('id', $repost_id)->forceDelete();

            return $this->sendResponse(array(), 'Repost Deleted successfully.');
        }
    }

	public function delete_approved_product(Request $request) {
        $validator = Validator::make($request->all(), [
                    'product_id' => 'required',
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $product_id = $request->product_id;
            $user_id = $request->user_id;
            
            $data = array(
                'deleted_at' => Carbon::now()    
            );

            $result=DB::table('shop_product_files')->where('shop_product_id', $product_id)->where('user_id', $user_id)->get();
			for($i=0;$i<count($result);$i++){
				$file_name=$result[$i]->file_name;
				$thumb=$result[$i]->thumb;
				if(file_exists(public_path("images/shop_image/".$file_name))){
					unlink(public_path("images/shop_image/".$file_name));
				}
				if(file_exists(public_path("images/shop_image/".$thumb))){
					unlink(public_path("images/shop_image/".$thumb));
				}
                  $file_nameRes=$result[$i]->resized_file_name??"";
                  if ($file_nameRes != "") {
                      if(file_exists(public_path("images/shop_image/".$file_nameRes))){
                          unlink(public_path("images/shop_image/".$file_nameRes));
                      }
                  }
				$where = array("id" => $result[$i]->id);        
				DB::table('shop_product_files')->where($where)->delete();                    
			}
            $delete_detail = ShopProductDetail::where('shop_product_id', $product_id)->where('user_id', $user_id)->forceDelete();
            $delete_post = ShopProduct::where('id', $product_id)->where('user_id', $user_id)->forceDelete();

            return $this->sendResponse(array(), 'Product Deleted successfully.');
        }
    }

    public function list_of_all_users(Request $request)
    {
        $start = $request->start;
        $limit = $request->limit;

        $all_users = DB::select("SELECT *,`user_detail`.`profile_image_url` as image FROM `users` INNER JOIN `user_detail` ON (`users`.`id` = `user_detail`.`user_id`) LIMIT $limit OFFSET $start ");

        $no_of_users = DB::select("SELECT count(*) as total_users FROM `users` ");

        if($all_users)
        {
            return response()->json(["code" => 200, "message" => "Successfully", "data" => $all_users, "total_users" => $no_of_users[0]->total_users]);
        }
        else
        {
            return response()->json(["code" => 100, "message" => "No found"]);
        }
    }



    // public function admin_block_user(Request $request)
    // {
    //     $user_id = $request->user_id;
    //     $checkuser = User::where('id', $user_id)->first();
    //     // print_r($checkname);
    //     // die;
    //     if($checkname)
    //     {
    //         return response()->json(["code" => 100, "message" => "The username has already been taken"]);
    //         // return $this->sendResponse($code, 'The username has already been taken.');
    //     }
    //     else 
    //     {

    //     }

    // }
    
    
    
    
    // *********************GET ALL HASHTAG ***************

    public function getALLHashTags(Request $request){
        
        
        $validator = Validator::make($request->all(), [
            
            'hashtag_name' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else { 
            
            $hashtag_name = $request->hashtag_name;
            
         // $allhashWord = DB::table('hash_tags')->get();
            $allhashWord = DB::table('hash_tags')->where('hash_tag_name', 'like', $hashtag_name . '%')->orderBy('hash_tag_name', 'ASC')->get();
        
        
            $saData = array();
            
            for ($i = 0; count($allhashWord) > $i; $i++) {
    
                $saData[$i]['hash_tag_name'] = $allhashWord[$i]->hash_tag_name;
                $saData[$i]['created_at'] = $allhashWord[$i]->created_at;
            }
            
            return $this->sendResponse($saData, 'List Of HashTag.');
                
            }
        
    }
    
    
    
    //********************* HIDE USER  *****************************
    
    public function hideUser(Request $request){
        
        
        $validator = Validator::make($request->all(), [
            
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {

            $user_id = $request->user_id;
            $status = $request->status;
            
            if($status == 0){
                User::where('id', $user_id)->Delete();
                Post::where('user_id', $user_id)->Delete();
                PostComment::where('user_id', $user_id)->Delete();
                PostLike::where('user_id', $user_id)->Delete();
                Status::where('user_id', $user_id)->Delete();
                StatusView::where('user_id', $user_id)->Delete();
                Followers::where('user_id', $user_id)->orWhere('follewers_id', $user_id)->delete();
                return $this->sendResponse(array(), 'User data is hidden successfully.');
            }
            else{
                User::withTrashed()->where('id', $user_id)->restore();
                Post::withTrashed()->where('user_id', $user_id)->restore();
                PostComment::withTrashed()->where('user_id', $user_id)->restore();
                PostLike::withTrashed()->where('user_id', $user_id)->restore();
                Status::withTrashed()->where('user_id', $user_id)->restore();
                StatusView::withTrashed()->where('user_id', $user_id)->restore();
                Followers::withTrashed()->where('user_id', $user_id)->orWhere('follewers_id', $user_id)->restore();
                return $this->sendResponse(array(), 'User data is shown successfully.');
            }
        }
    
    }
    
    
    //********************* CHANGE PASSWORD  *****************************//
    public function change_password(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'old_password' => 'required',
                    'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $user_id = $request->user_id;
            $old_password = $request->old_password;
            $new_password = $request->new_password;
            
            $user = User::find($user_id);
            if($user)
            {
                if(Hash::check($old_password, $user->password))
                {
                    $user->verify_password = Hash::make($new_password);
                    //$user->app_token = NULL;
                    $user->password_bit = 1;
                    $user->save();
                    
                    $encrypted = Crypt::encryptString($user_id);
                    $url = url('/')."/verify_forgotpassword?id=".$encrypted;
                    $to = $user->email;
                    $subject = "Password Reset"; 
					
					$message['ssUsrl'] = $url;
					$message['view'] = "resetpass";	
                    $this->custom_email($to, $from=NULL, $subject, $message);
            
                    return response()->json(["code" => 200, "message" => "A verification link has been sent on your email please verify that to to complete your change password request."]);
                }
                else
                {
                    return response()->json(["code" => 100, "message" => "Incorrect old password."]);
                }
            }
            else
            {
                return response()->json(["code" => 100, "message" => "Not any user found"]);
            }
            
        }
    }
    
    
    // ******************* Verify Forgot Password****************//
    
    public function verifyForgotPassword(Request $request){
        
		$encrypted = $request->get('id');
        $decrypted = Crypt::decryptString($encrypted);
        $user = User::find($decrypted);
		if($user)
		{
			if($user->verify_password){
				
				  $new_password = $user->verify_password;
				  $user->password = $new_password ;
				  $user->password_bit = 0;
				  $user->verify_password = NULL;
				  $user->save();
				  return view('verified');
			}else{
				// echo '<center>Your Have Already Password Change</center>';
				return view('verified');            
			}
		  
		} else {
			echo '<center>User Not Found</center>';
			die;
		}
    }
	
	public function UpdateForgotPassword(Request $request){
        
		$encrypted = $request->get('id');
        $decrypted = Crypt::decryptString($encrypted);
        $user = User::find($decrypted);
		if($user)
		{
			if($user->verify_password)
			{
				  $new_password = $user->verify_password;
				  $user->password = $new_password ;
				  $user->password_bit = 0;
				  $user->verify_password = NULL;
				  $user->save();
				  $response = [
					'success' => true,
					'message' => 'Verified success'
				  ];
				  return response()->json($response, 200);
			}
			else
			{
				$response = [
				'success' => true,
				'message' => 'Verified success'
			  ];
			  return response()->json($response, 200);            
			}
		  
		} else {
			$response = [
				'success' => flase,
				'message' => 'User Not Found'
			];
			return response()->json($response, 200);
		}
    }
    
    //********************* GET POST LIKES COMMENTS USERS *****************************//
    public function get_post_likes_comments_users(Request $request) {
        $validator = Validator::make($request->all(), [
                    'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            // $ssImageUrl = 'https://artfora.net/images/profile_image';
            $ssImageUrl = '';
            $post_id = $request->post_id;
            $get_list = $request->get_list;
            
            if($get_list == "comments")
            {
                $user_list = PostComment::where('post_id', $post_id)->orderBy('id', 'DESC')->get();
            }
            else
            {
                $user_list = PostLike::where([['post_id', $post_id], ['type', 'like']])->orderBy('id', 'DESC')->get();
            }
            $user_info = array();
            foreach($user_list as $userTest) {
                $user_info[$userTest->user_id] = $userTest->created_at;
            }
            $user_list = $user_list->pluck('user_id')->toArray();
            $saSearchUser = array();
            if(count($user_list) > 0)
            {
                $joined_user_list = join(',', $user_list);
               
                $saSearchUser = DB::table('users')->select('users.id', 'users.name', 'users.email', 'users.deleted_at', 'users.email_verified_at', 'user_detail.filter', DB::raw("CONCAT('" . $ssImageUrl . "',user_detail.profile_image_url) as image"), 'category', 'private_description', 'privete_profile_url', 'description')
                                ->whereIn('users.id', $user_list);
                                if($request->is_admin != 1){
                                    $saSearchUser->where('users.deleted_at', NULL);
                                }
                $saSearchUser = $saSearchUser->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')->orderByRaw(DB::raw("FIELD(users.id, $joined_user_list)"))
                                ->get()->toArray();
                for ($i = 0; count($saSearchUser) > $i; $i++) {
                    $saInterestGroup = DB::table('interest_group')->select('interest_name')->where('user_id', $saSearchUser[$i]->id)->get()->toArray();
                    $saSearchUser[$i]->interest_group = $saInterestGroup;
                    $saSearchUser[$i]->urls = DB::table('user_urls')->where('user_id', $saSearchUser[$i]->id)->get();
                    $snIsFollow = Followers::where('follewers_id', $saSearchUser[$i]->id)->where('user_id', $request->user_id)->get()->first();
                    if (empty($snIsFollow)) {
                        //Not Follow
                        $saSearchUser[$i]->is_follow = FALSE;
                    } else {
                        //Follow
                        $saSearchUser[$i]->is_follow = TRUE;
                    }
                    $created_at = $user_info[$saSearchUser[$i]->id];
                    $saSearchUser[$i]->time_ago = $this->time_diff($created_at);
                }
            }

            return $this->sendResponse($saSearchUser, 'User Listed Successfully');
        }
    }
    
    
    
    /********************* SEARCH POSTS WITH HASHTAGS ***************************/

    public function search_posts_with_hashtags(Request $request) {
      
        $validator = Validator::make($request->all(), [
                    'search' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $search = $request->search;
            
            $ssUrl = url('images/post/Image/');
            $saGetAllPostttt = DB::table('post')
                            ->select('post.id', 'users.id as user_id', 'users.name', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                            ->distinct('id')
                            ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                            ->join('users', 'post.user_id', '=', 'users.id')
                            // ->where([['post.tag', 'LIKE', '%'.$search], ['post.deleted_at', NULL]])
                            ->where('post.deleted_at', NULL)
                            ->whereRaw('MATCH (tag) AGAINST (?)' , $search)
                            ->get();

            $saGetAllPost = $saGetAllPostttt->unique('id');

            $saData = array();
            for ($i = 0; count($saGetAllPost) > $i; $i++) {
                $saData[$i]['id'] = $saGetAllPost[$i]->id;
                $saData[$i]['user_id'] = $saGetAllPost[$i]->user_id;
                $saData[$i]['user_name'] = $saGetAllPost[$i]->name;
                $saData[$i]['user_profile_image'] = $saGetAllPost[$i]->profile_image_url;

                $saData[$i]['image'] = 'https://artfora.net/images/post/Image/' . $saGetAllPost[$i]->image;
                $ssExt = pathinfo($saData[$i]['image'], PATHINFO_EXTENSION);
                $saData[$i]['image_ext'] = $ssExt;
                list($width, $height) = getimagesize($saData[$i]['image']);
                $saData[$i]['image_width'] = $width;
                $saData[$i]['image_height'] = $height;

                if (!empty($saGetAllPost[$i]->video)) {
                    $saData[$i]['video'] = 'https://artfora.net/images/post/Video/' . $saGetAllPost[$i]->video;
                    $ssExt = pathinfo($saData[$i]['video'], PATHINFO_EXTENSION);
                    $saData[$i]['image_ext'] = $ssExt;
                } else {
                    $saData[$i]['video'] = null;
                }
                $saData[$i]['is_flipped'] = $saGetAllPost[$i]->is_flipped;
                $saData[$i]['is_rotated'] = $saGetAllPost[$i]->is_rotated;
                $saData[$i]['owner_name'] = $saGetAllPost[$i]->owner_name;
                $saData[$i]['decription'] = $saGetAllPost[$i]->decription;
                $saData[$i]['tag'] = $saGetAllPost[$i]->tag;
                $saData[$i]['filter'] = $saGetAllPost[$i]->filter;

                $saGetDetail = \App\PostDetail::select('title', 'description')->where('post_id', $saGetAllPost[$i]->id)->get()->toArray();
                $saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $saGetAllPost[$i]->id], ['type', 'like']])->count();
                $saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $saGetAllPost[$i]->id)->count();
                $saGetTag = DB::table('user_tags')->select('tag_user')->where('post_id', $saGetAllPost[$i]->id)->groupBy('tag_user')->get()->toArray();
                $saAllTags = array_column($saGetTag, 'tag_user');
                $saData[$i]['is_like'] = with(new \App\PostLike())->get_user_is_like($request->user_id, $saGetAllPost[$i]->id);
                $saData[$i]['detail'] = $saGetDetail;
                $saData[$i]['likes'] = $saGeLikes;
                $saData[$i]['comments'] = $saGeComments;
                $saData[$i]['tags'] = $saAllTags;
                $saData[$i]['created_at'] = $saGetAllPost[$i]->created_at;
            }
            return $this->sendResponse($saData, 'Posts Listed Successfully');
        }
    }
    
    
    
    /********************* DISCOVER ***************************/

    public function discover(Request $request) {

        $validator = Validator::make($request->all(), [
            'interest' => 'required',
        ]);
         
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $user_id=$request->user_id;
            $filter_id=$request->filter_id;
            $interest = $request->interest;
            $get_post_ids = DB::table('interest_group_posts')->where('interest_name', $interest)->pluck('post_id');
            $tag = $request->tag;
            $sort_by = $request->sort_by;
            $post_type = array('public');
            
            $result=DB::table('blocked_users')->select('blocked_userid')->where('user_id',$user_id)->get()->toArray();
            $result2=DB::table('blocked_users')->select('user_id')->where('blocked_userid',$user_id)->get()->toArray();
    
            $Block_user = array();
            for($i=0;$i<count($result);$i++){
                array_push($Block_user, $result[$i]->blocked_userid);
            }
            for($i=0;$i<count($result2);$i++){
                array_push($Block_user, $result2[$i]->user_id);
            }
            
    
            if($request->page){
                $start_from=($request->page*$this->limit_for_discover);
            }
            else{
               $start_from=0;
            }
            
            if($filter_id == 1)
            {
                $filter_array = array(1);
            }
            elseif($filter_id == 2)
            {
                $filter_array = array(1,2);
            }
            elseif($filter_id == 3)
            {
                $filter_array = array(1,2,3);
            }
            else
            {
                $filter_array = array(1,2,3,4);
            }
            
            if($tag)
            {
                $ssUrl = url('images/post/Image/');
                $saGetAllPostttt = DB::table('post')
                             ->select('post.id', 'users.id as user_id', 'users.name', 'users.tagname' ,'post.image', 'user_detail.profile_image_url', 'user_detail.category', 'post.video', 'post.owner_name','post.video_thumbnail','post.video_thumb_title','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                             ->distinct('id')
                             ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                             ->join('users', 'post.user_id', '=', 'users.id')
                              ->whereNotIn('post.user_id',$Block_user)
                             ->where('post.deleted_at', NULL)                            
                             ->whereIn('post.filter_id', $filter_array)
                             ->whereIn('post.type', $post_type);
				if($interest != 'All')
                {
					$saGetAllPostttt->orWhere('post.user_id',$user_id);
                }
                
                if($sort_by == 1)
                {
                    $saGetAllPostttt = $saGetAllPostttt->where('tag', 'LIKE', '%'.$tag.','.'%')->orWhere('tag', 'LIKE', $tag)->orWhere('tag', 'LIKE', '#'.$tag)
                                ->orderBy('post.id', 'DESC')
                                  ->offset($start_from)
                                 ->limit($this->limit_for_discover)
                                 ->get();
                }
                else
                {
                    $saGetAllPostttt = $saGetAllPostttt->where('tag', 'LIKE', '%'.$tag.','.'%')->orWhere('tag', 'LIKE', $tag)->orWhere('tag', 'LIKE', '#'.$tag)
                                ->inRandomOrder()
                                ->offset($start_from)
                                ->limit($this->limit_for_discover)
                                ->get();
                }
                
                $saGetAllPost = $saGetAllPostttt->unique('id');
            }
            else
            {
                $get_post_ids = DB::table('interest_group_posts')->where('interest_name', $interest)->pluck('post_id');   
                $ssUrl = url('images/post/Image/');
                $saGetAllPostttt = DB::table('post')
                             ->select('post.id', 'users.id as user_id', 'users.name','users.tagname', 'post.image', 'user_detail.profile_image_url', 'user_detail.category', 'post.video', 'post.owner_name','post.video_thumbnail','post.video_thumb_title','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
                             ->distinct('id')
                             ->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
                             ->join('users', 'post.user_id', '=', 'users.id')
                             ->whereIn('post.type', $post_type)
                             ->whereIn('post.filter_id', $filter_array);
                
                if($interest != 'All')
                {
                    $saGetAllPostttt->whereIn('post.id', $get_post_ids);
                } else {
//					$saGetAllPostttt->orWhere('post.user_id',$user_id);
				}
                
                if($sort_by == 1)
                {
                    $saGetAllPostttt = $saGetAllPostttt->where('post.deleted_at', NULL) ->whereNotIn('post.user_id',$Block_user)->orderBy('post.id', 'DESC')->offset($start_from)->limit($this->limit_for_discover)->get();
                    $saGetAllPost = $saGetAllPostttt->unique('id');
                }
                else
                {
                    $saGetAllPostttt = $saGetAllPostttt->where('post.deleted_at', NULL) ->whereNotIn('post.user_id',$Block_user)->offset($start_from)->limit($this->limit_for_discover)->get();
                                                             // $saGetAllPostttt = $saGetAllPostttt->inRandomOrder();
                    $postsArray = array();
                    $count = count($saGetAllPostttt);
                    
                    for ($i = 0; $count > $i; $i++) {
                        $randomInputIndex = rand(0, count($saGetAllPostttt)-1);
                        $randomInputValue = $saGetAllPostttt[$randomInputIndex];
                        array_push($postsArray, $randomInputValue);
                        unset($saGetAllPostttt[$randomInputIndex]);
                        $saGetAllPostttt = $saGetAllPostttt->values();
                    }
                    $saGetAllPostttt = $postsArray;
                    $saGetAllPost = $saGetAllPostttt;
                }

                // $saGetAllPostttt = $saGetAllPostttt->where('post.deleted_at', NULL)->orderBy('post.id', 'DESC')->get();
             }

            $saData = array();
            for ($i = 0; count($saGetAllPost) > $i; $i++) {
                $saData[$i]['id'] = $saGetAllPost[$i]->id;
                $saData[$i]['user_id'] = $saGetAllPost[$i]->user_id;
                $saData[$i]['user_name'] = $saGetAllPost[$i]->name;
                $saData[$i]['tagname'] = $saGetAllPost[$i]->tagname;
                $saData[$i]['user_profile_image'] = $saGetAllPost[$i]->profile_image_url;
                $saData[$i]['category'] = $saGetAllPost[$i]->category;
                // $saData[$i]['image'] = 'https://artfora.net/images/post/Image/' . $saGetAllPost[$i]->image;
                // $ssExt = pathinfo($saData[$i]['image'], PATHINFO_EXTENSION);
                // $saData[$i]['image_ext'] = $ssExt;
                // list($width, $height) = getimagesize($saData[$i]['image']);
                // $saData[$i]['image_width'] = $width;
                // $saData[$i]['image_height'] = $height;

                if (!empty($saGetAllPost[$i]->video)) {
                    $saData[$i]['video'] = 'https://artfora.net/images/post/Video/' . $saGetAllPost[$i]->video;
                    $ssExt = pathinfo($saData[$i]['video'], PATHINFO_EXTENSION);
                    $saData[$i]['image_ext'] = $ssExt;
                } else {
                    $saData[$i]['video'] = null;
                }
                $saData[$i]['is_flipped'] = $saGetAllPost[$i]->is_flipped;
                $saData[$i]['is_rotated'] = $saGetAllPost[$i]->is_rotated;
                $saData[$i]['owner_name'] = $saGetAllPost[$i]->owner_name;
                $saData[$i]['video_thumbnail'] = $saGetAllPost[$i]->video_thumbnail;
                $saData[$i]['video_thumb_title'] = $saGetAllPost[$i]->video_thumb_title;
                $saData[$i]['decription'] = $saGetAllPost[$i]->decription;
                $saData[$i]['tag'] = $saGetAllPost[$i]->tag;
                $saData[$i]['filter'] = $saGetAllPost[$i]->filter;
                $is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $saGetAllPost[$i]->id]])->first();
                if($is_saved)
                {
                    $is_saved = 'yes';
                }
                else
                {
                    $is_saved = 'no';
                }
                // $followers = \App\Followers::where('follewers_id', $request->follow_by_id)->where('user_id', $saData[$i]['user_id'])->first();

                $followers = \App\Followers::where('follewers_id', $saData[$i]['user_id'])->where('user_id', $request->follow_by_id)->first();

                $saGetDetail = \App\PostDetail::select('title', 'description')->where('post_id', $saGetAllPost[$i]->id)->get()->toArray();
                $saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $saGetAllPost[$i]->id], ['type', 'like']])->count();
                $saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $saGetAllPost[$i]->id)->count();
                
                // $saGetTag = DB::table('user_tags')->select('tag_user')->where('post_id', $saGetAllPost[$i]->id)->groupBy('tag_user')->get()->toArray();
                // $saAllTags = array_column($saGetTag, 'tag_user');
               
                $saAllTags = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
                 ->join('users', 'users.id', '=', 'user_tags.tag_user')
                 ->where('user_tags.post_id', $saGetAllPost[$i]->id)->groupBy('user_tags.tag_user')->get()->toArray();

                $saData[$i]['is_like'] = with(new \App\PostLike())->get_user_is_like($request->user_id, $saGetAllPost[$i]->id);
                $saData[$i]['is_saved'] = $is_saved;
                $saData[$i]['detail'] = $saGetDetail;
                $saData[$i]['likes'] = $saGeLikes;
                $saData[$i]['comments'] = $saGeComments;
                $saData[$i]['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $saGetAllPost[$i]->id], ['deleted_at', NULL]])->get()->toArray();

                $saData[$i]['tags'] = $saAllTags;
                $saData[$i]['created_at'] = $saGetAllPost[$i]->created_at;
                $saData[$i]['followers'] = $followers;
              //  dd($saData[$i]);
                $saData[$i]['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height')->where('post_id',$saGetAllPost[$i]->id)->orderBy('priority', 'ASC')->get()->toArray();
               
            }
            return $this->sendResponse($saData, 'Posts Listed Successfully');
        }
    }
    
    
    
    /********************* EDIT POST ***************************/
    
    public function edit_post(Request $request) {
    
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'post_id' => 'required',
                    'filter' => 'required',
        ]);


        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            $post_id = $request->post_id;
            $interest_group = $request->interest_group;
    
            $get_post = Post::find($post_id);
            $get_post->user_id = $request['user_id'];
            $get_post->image = $get_post->image;
            $get_post->image_thumb = $get_post->image_thumb;
            $get_post->video = $get_post->video;
            $get_post->owner_name = $request['owner_name'];
            $get_post->decription = $request['description'];
            $get_post->tag = $request['tag'];
            $get_post->filter_id = $request['filter'];
            $get_post->is_flipped = $request['is_flipped'];
            $get_post->is_rotated = $request['is_rotated'];
            $get_post->save();
            
            //If hash taged 
            if (!empty($request['tag'])) {
                $hashExploadTags = explode(',', $request['tag']);
                foreach ($hashExploadTags As $hashTags) {
                    if($hashTags!=""){
                        $hashStrg = preg_replace('/\s+/', '', $hashTags);                        
                        if($hashStrg[0] == "#"){
                            $hashWord = ltrim ($hashStrg,'#');                        
                            $findhashWord = DB::table('hash_tags')->where('hash_tag_name', $hashWord)->first();           
                            if(!$findhashWord){
                                 DB::table('hash_tags')->insert(['hash_tag_name' => $hashWord, 'status' => 1, 'created_at' => Carbon::now()]);
                            }
                        }
                    }
                }
            }
            
            
            //If taged user
            if (!empty($request['user_tags'])) {
                DB::table('user_tags')->where('post_id', $post_id)->delete();
                
                $saExploadTags = explode(',', $request['user_tags']);
                foreach ($saExploadTags As $snTags) {
                    DB::table('user_tags')->insert(['post_id' => $post_id, 'user_id' => $request['user_id'], 'tag_user' => $snTags]);
                }
            }
            
            if (!empty($request['add_detail'])) {
                DB::table('post_detail')->where('post_id', $post_id)->update(['deleted_at' => Carbon::now()]);
                
                foreach (json_decode($request['add_detail']) AS $obDetail) {
                    $obPostDetail = new \App\PostDetail();
                    $obPostDetail->post_id = $post_id;
                    $obPostDetail->title = $obDetail->title;
                    $obPostDetail->description = $obDetail->description;
                    $obPostDetail->save();
                }
            }


            if (!empty($interest_group)) 
            {
                DB::table('interest_group_posts')->where('post_id', $post_id)->update(['deleted_at' => Carbon::now()]);
                $interest_group_array = explode('.', $interest_group);
    
                for($m=0; $m < count($interest_group_array); $m++)
                {
                    $interest_post[] = array(
                                            'user_id' => $request['user_id'],
                                            'interest_name' => $interest_group_array[$m],
                                            'post_id' => $post_id,
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now(),
                                        );
                }
                
                $post_interest = DB::table('interest_group_posts')->insert($interest_post);
            }


            return $this->sendResponse($post_id, 'Post Updated successfully.');
        }
    }
    
    
    
    /********************* COMMON REPORT ***************************/
    
    public function common_report(Request $request){
        
        if($request->status == 'report_user')
        {
            $check_report_user = DB::table('report_commons')->where([['user_id', $request->user_id], ['common_id', $request->common_id], ['type', 'user'], ['status', 'report']])->first();
            
            if(!$check_report_user)
            {
                $insert=DB::table('report_commons')->insert([
                'user_id'  => $request->user_id,
                'common_id' => $request->common_id,
                'type' => 'user',
                'status' => 'report',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                ]);
                
                $subject = "Report User";
                $to = "reportuser@artfora.net";
            }
            else
            {
                return $this->sendError('You have already report this user.', array()); 
            }
            
        }
        else if($request->status == 'report_post')
        {   
            $check_report_post = DB::table('report_commons')->where([['user_id', $request->user_id], ['common_id', $request->common_id], ['type', 'post'], ['status', 'report']])->first();
            
            if(!$check_report_post)
            {
                $insert=DB::table('report_commons')->insert([
                'user_id'  => $request->user_id,
                'common_id' => $request->common_id,
                'type' => 'post',
                'status' => 'report',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                ]);
                
                $subject = "Report Post";
                $to = "reportpost@artfora.net";
            }
            else
            {
                return $this->sendError('You have already report this user.', array()); 
            }
        }
        else if($request->status == 'hide_post')
        {   
            $remove_post = DB::table('report_commons')->where([['user_id', $request->user_id], ['common_id', $request->common_id], ['type', 'post'], ['status', 'remove']])->first();
            
            if(!$remove_post)
            {
                $delete_comments = PostComment::where('post_id', $request->common_id)->Delete();
                $delete_likes = PostLike::where('post_id', $request->common_id)->Delete();
                $delete_detail = PostDetail::where('post_id', $request->common_id)->Delete();
                $delete_post = Post::where('id', $request->common_id)->Delete();
            
                $insert=DB::table('report_commons')->insert([
                'user_id'  => $request->user_id,
                'common_id' => $request->common_id,
                'type' => 'post',
                'status' => 'remove',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                ]);
                
                
                $subject = "Remove Immediately Post";
                $to = "removeimmediately@artfora.net, umer.farooq@appcrates.com";
            }
            else
            {
                return $this->sendError('You have already hide this post.', array()); 
            }
        }
        
        $message = "
        <html>
        <head>
        <title>HTML email</title>
        </head>
        <body>
        <p>".$request->message."</p>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <no-reply>' . "\r\n";
        // $headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);

        return $this->sendResponse($insert, 'Reported Successfully');   
        
    }

    public function contact_email(Request $request)
    {
        $form = $request->form;
        $subject = $request->subject;
        $body = $request->body;

        $username = $request->username;
        $user_tag = $request->user_tag;
        $to = $request->to;
        $email_type = $request->email_type;
        
        if($email_type == 0)
        {
            $email_type = 'Page Suggestion';
        }
        else
        {
            $email_type = 'Mail By Admin';
        }
        $to = $to;
        
		$message['body'] = $body;
		$message['username'] = $username;
		$message['user_tag'] = $user_tag;
		$message['email_type'] = $email_type;
		$message['view'] = "contact";
			
		$data = array('email' => $to,'replyTo' => $form,'username' => $username, 'subject' => $subject, 'message' => $message);
		\Mail::to($to)->send(new SendMail($data));
		
        return response()->json(["code" => 200, "message" => "Successfully send email."]);
    }
	
    public function send_email(Request $request)
    {
        $form = $request->form;
        $subject = $request->subject;
        $body = $request->body;

        $username = $request->username;
        $tagName = $request->tagName??"";
        $userId = $request->userId??"";
        $date_time = date('Y-m-d H:i:s');
        $postLink = $request->postLink??"";
        $to = $request->to;
        
		$message['body'] = $body;
		$message['view'] = "mail";
		if(!empty($tagName)){
			$message['extra'] = ['tagName'=>$tagName,'userId'=>$userId,'date_time'=>$date_time,'postLink'=>$postLink];
		}
			
		$data = array('email' => $to,'replyTo' => $form,'username' => $username, 'subject' => $subject, 'message' => $message);
		\Mail::to($to)->send(new SendMail($data));
        return response()->json(["code" => 200, "message" => "Successfully send email."]);
    }
    
    public function get_filters()
    {
        $filters = DB::table('filters')->where('status', 1)->get();
        return response()->json(["code" => 200, "message" => "Success", "data" => $filters]);
    }
    
    public function check_username(Request $request)
    {
        $username = $request->username;
        $check_username = User::where('tagname', $username)->first();
        if($check_username)
        {
            return response()->json(["code" => 100, "message" => "Username exists"]);
        }
        else
        {
            return response()->json(["code" => 200, "message" => "Username does not exist"]);
        }
    }
    
    
    /*=================  SEND PUSH NOTIFICATION  =================*/
    function send_push($title, $body, $tokens) {
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Custom data to be sent with the push

        $data = array
            (
            'message' => 'here is a message. message',
            'title' => $title,
            'body' => $body,
            'smallIcon' => 'small_icon',
            'some data' => 'Some Data',
            'Another Data' => 'Another Data',
            'click_action' => 'OPEN_ACTIVITY',
            'sound' => 'default',
        );


        // $data2=array(
        //     'user_id' => $user_id,
        //     'post_id' => $post_id,
        // );

        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array(
            'registration_ids' => $tokens,
            'notification' => $data,
            'priority' => 'high',
        );

        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend);
        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        $headers[] = 'Authorization: key= AIzaSyCrWenpawLiljuUw9PRb8VTNMOeMRCsryQ';

        //Setup curl, add headers and post parameters.

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //Send the request
        $response = curl_exec($ch);
        // return $response;
        //Close request
        curl_close($ch);
        return $response;
    }


function forget_pass(Request $request){
     $email=$request->email;
     $to_email = $email;

      
       $result=DB::table('forget_pass_request')->insert(['email' => $email, 'date_time' => 'NOW()']);
       $id = DB::getPdo()->lastInsertId();

       $ssUsrl = url('new_pass?id=' . base64_encode($id));


            $subject = 'Artfora mail Forgetpassword';
            $message = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Email template</title><link rel="stylesheet" href="css/screen.css"></head><body height="100%"><table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0"> <tbody><tr><td><p style="text-align: center; padding-bottom:40px;"><img src="https://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" /></p> </td></tr><tr><td><p style="text-align: center; color:#666;"><span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">WELCOME TO ARTfora</span></p></td></tr><tr style="text-align: center; color:#666;"><td><span style="font-size: 14px; font-family: AvenirNextCondensed, sans-serif; letter-spacing: 2px;">Please verify your email address by clicking the link below.</span></td></tr><tr style="text-align: center;"><td style="padding-top:50px;"><p><span><a style="font-size: 14px; font-family: AvenirNextCondensed, sans-serif; letter-spacing: 2px; color: #fff; background-color:#666; padding:20px; border-radius:10px; text-decoration: none;" href="' . $ssUsrl . '">' . $ssUsrl . '</a></span></p> </td></tr></tbody></table></body></html>';

             //        $message = '<p>Click the below button to activated your account.</p><div class="container" style="background-color:white"><a href="' . $ssUsrl . '"><button type="button">Activate</button></a></div>';
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
            mail($to_email, $subject, $message, $headers);


    return response()->json(['code' => '200', 'success' => 'Message is send successfully.']);
}

function new_pass(Request $request) {
    //$id = Crypt::decryptString($id);
	$eid = $request->id;
	$id = Crypt::decryptString($request->id);
    $forgot_password_request = DB::table('forget_pass_request')->where('id', $id)->first();
    if($forgot_password_request)
    {
        return view('new_password',compact('eid')); 
    }
    else
    {
        abort(403, 'Unauthorized action.');
    }
}

function submit_pass(Request $request){

    $id = Crypt::decryptString($request->id);

    $forgot_password_request = DB::table('forget_pass_request')->where('id', $id)->first();
    if($forgot_password_request)
    {
        $user = User::where('email', $forgot_password_request->email)->first();
        $user->password = Hash::make($request->pass);
        $user->save();

        $forgot_password_request = DB::table('forget_pass_request')->where('id', $id)->delete();
		return response()->json(["code" => 200, "message" => "Password is updated Successfully."]);
    }
    else
    {
		return response()->json(["code" => 403, "message" => "Unauthorized action."]);
    }
}


function get_taglist(Request $request){
    $tag = $request->tag;
    $tag_type = $request->tag_type;
    $user_id = $request->user_id;    
    $result="";
   // echo $request->page;

     if($request->page){
		$start_from=($request->page*$this->limit_for_search);
	}
	else{
	   $start_from=0;
	}

	$blocked_users = DB::table('blocked_users')->where('user_id',$user_id)->pluck('blocked_userid')->toArray();
    $wherein = "";  
    $saData=array();
    if($tag_type=='@'){
		   if(count($blocked_users) > 0){
			    $blocked_users_in = implode(",",$blocked_users);
				$wherein = " AND users.id NOT IN($blocked_users_in) ";
		   }
           $result= DB::select('select users.id,users.name,users.tagname,user_detail.profile_image_url,user_detail.profile_image_url,1 as Value,users.created_at,user_detail.category from users,user_detail where users.id=user_detail.user_id AND users.status=1 AND users.role!="admin" '.$wherein.' AND (tagname like "%'.$tag.'%" OR name like "%'.$tag.'%") LIMIT '.$this->limit_for_search.' OFFSET '.$start_from.'');
                            
           for($i=0;$i<count($result);$i++){
                    $snIsFollow = Followers::where('follewers_id', $result[$i]->id)->where('user_id', $request->user_id)->get()->first();
                        if (empty($snIsFollow)) {
                            //Not Follow
                            $follow = FALSE;
                        } else {
                            //Follow
                            $follow = TRUE;
                        }

                        $saData[]=array(
                            "user_info"=>$result[$i],
                            "follow"=>$follow
                        );
                }
               


          
    }   
    elseif($tag_type=='#'){
        $result=DB::select('SELECT id,hash_tag_name as name,0 as tagname,status as profile_image_url,2 as Value,created_at FROM `hash_tags` WHERE hash_tag_name like "%'.$tag.'%" LIMIT '.$this->limit_for_search.' OFFSET '.$start_from.'');
        $followed_hash_tags = DB::table('follow_hash_tags')->where('user_id', $request->user_id)->pluck('hash_tag_id')->toArray();
        for($i=0;$i<count($result);$i++){ 
            
            in_array($result[$i]->id, $followed_hash_tags) ? $follow = TRUE : $follow = FALSE ; 
                                
            $saData[]=array(
                "user_info"=>$result[$i],
                "follow"=>$follow
            );
        }    
    } 
    else
	{
		
		if(count($blocked_users) > 0){
			$blocked_users_in = implode(",",$blocked_users);
			$wherein = " AND users.id NOT IN($blocked_users_in) ";
	   }
	   
        $result=DB::select('select * from (select users.id,users.name,users.tagname,user_detail.profile_image_url,1 as Value,users.created_at,user_detail.category from users,user_detail where users.id=user_detail.user_id AND users.status=1 AND users.role!="admin" '.$wherein.' AND (tagname like "%'.$tag.'%" OR name like "%'.$tag.'%") UNION ALL   SELECT id,hash_tag_name as name,0 as tagname,status as profile_image_url,2 as Value,created_at,"" as category FROM `hash_tags` WHERE hash_tag_name like "%'.$tag.'%") as uni order by created_at LIMIT '.$this->limit_for_search.' OFFSET '.$start_from.'');
        $followed_hash_tags = DB::table('follow_hash_tags')->where('user_id', $request->user_id)->pluck('hash_tag_id')->toArray();
        for($i=0;$i<count($result);$i++){ 
            if($result[$i]->Value=='1'){
                 $snIsFollow = Followers::where('follewers_id', $result[$i]->id)->where('user_id', $request->user_id)->get()->first();
                        if (empty($snIsFollow)) {
                            //Not Follow
                            $follow = FALSE;
							} else {
                            //Follow
                            $follow = TRUE;
                            }
							
                        $saData[]=array(
                            "user_info"=>$result[$i],
                            "follow"=>$follow
                        );
            }
            else{
				//$user_detail = DB::table('user_detail')->select('category')->where('user_id',$result[$i]->id)->first();
                in_array($result[$i]->id, $followed_hash_tags) ? $follow = TRUE : $follow = FALSE;                     
				//$result[$i]->category = $user_detail->category??"";
                $saData[]=array(
                    "user_info"=>$result[$i],
                    "follow"=>$follow
                );
            }     
          }
    }
	return response()->json(['code' => '200', 'success' => $saData]);die;
}
 

public function change_email_name(Request $request){
    $email=$request->email;
    $name=$request->name;
    $data=array(
        "name"=>$name,
        "email"=>$email,
        "post_date"=>NOW()
    );
    $result_id=DB::table('change_email_request')->insertGetId($data); 
        
    /************************************************************************************************************/  
         $ssUsrl = url('email_verification/' . base64_encode($result_id));
            $to_email = $email;
            $subject = 'Artfora Username/Email Change Confirmation';
            $message = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Email template</title><link rel="stylesheet" href="css/screen.css"></head><body height="100%"><table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0"> <tbody><tr><td><p style="text-align: center; padding-bottom:40px;"><img src="https://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" /></p> </td></tr><tr><td><p style="text-align: center; color:#666;"><span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">WELCOME TO ARTfora</span></p></td></tr><tr style="text-align: center; color:#666;"><td><span style="font-size: 14px; font-family: AvenirNextCondensed, sans-serif; letter-spacing: 2px;">Please verify your username/email address change by clicking the link below.</span></td></tr><tr style="text-align: center;"><td style="padding-top:50px;"><p><span><a style="font-size: 14px; font-family: AvenirNextCondensed, sans-serif; letter-spacing: 2px; color: #fff; background-color:#666; padding:20px; border-radius:10px; text-decoration: none;" href="' . $ssUsrl . '">' . $ssUsrl . '</a></span></p> </td></tr></tbody></table></body></html>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
            mail($to_email, $subject, $message, $headers);  
            return response()->json(['code' => '200', 'success' => "Email have been send please verify your email"]);die;
    /***********************************************************************************************************/
}


    public function email_verification($id){
        $id=base64_decode($id); 
        $result=DB::table('change_email_request')->select('name','email','post_date')->where('id',$id)->where('status',1)->first(); 
        if($result)
        {
            $data=array(
                "name"=>$result->name,
                "email"=>$result->email,
                "tagname"=>strtolower( str_replace(' ', '', $result->name))
            );
            DB::table('users')->where('email',$result->email)->update($data);
            // echo "Information updated";die;
            return view('verified_change_name'); 
        }
        echo "Wrong infomation";die;
        
    }

    public function block_user(Request $request){
        $user_id=$request->user_id;
        $blocked_userid=$request->blocked_userid;
        $data=array("user_id"=>$user_id,"blocked_userid"=>$blocked_userid,"post_date"=>NOW());
        $result_id=DB::table('blocked_users')->insertGetId($data);

        $where1 = array("user_id" => $user_id,"follewers_id"=>$blocked_userid);
        DB::table('followers')->where($where1)->delete();
        $where2 = array("user_id" => $blocked_userid,"follewers_id"=>$user_id);
        DB::table('followers')->where($where2)->delete();
        DB::table('follow_statuses')->where($where1)->delete();
        DB::table('notification_statuses')->where($where1)->delete();

        return response()->json(['code' => '200', 'success' => "User has been blocked"]);die;
    }
    public function unblock_user(Request $request){
        $user_id=$request->user_id;
        $blocked_userid=$request->blocked_userid;
        $where = array("user_id" => $user_id,"blocked_userid"=>$blocked_userid);        
        DB::table('blocked_users')->where($where)->delete();
        $where = array("user_id" => $user_id,"follewers_id"=>$blocked_userid);      
        DB::table('followers')->where($where)->delete();
        return response()->json(['code' => '200', 'success' => "Blocked user has been revoked"]);die;
    }

    public function list_of_block_users_against_id(Request $request)
    {
        $user_id=$request->user_id;
        $search = $request->search;
        if(!empty($search))
        {
            $search = ' AND tagname LIKE "%'.$search.'%"';
        }
        
        $all_block_users = DB::select("SELECT
                                        `users`.`name`,
                                        `users`.`email`,
                                        `user_detail`.`profile_image_url`,
                                        `blocked_users`.`blocked_userid`
                                    FROM
                                        `users`
                                        INNER JOIN `blocked_users` 
                                            ON (`users`.`id` = `blocked_users`.`blocked_userid`)
                                        INNER JOIN `user_detail` 
                                            ON (`users`.`id` = `user_detail`.`user_id`)
                                        WHERE `blocked_users`.`user_id` = $user_id $search");
        return response()->json(['code' => '200', 'success' => "Successfully", 'data' => $all_block_users ]);
    }


    public function app_setting(Request $request)
    {
        $user_id = $request->user_id;
        $profile_layout = $request->profile_layout;
        $discover_layout = $request->discover_layout;
        $shop_layout = $request->shop_layout;
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'profile_layout' => 'required',
                    'discover_layout' => 'required',
                    'shop_layout' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }

        $layout_data = array('profile_layout' => $profile_layout,
                                'discover_layout' => $discover_layout,
                                'shop_view_toggle' => $shop_layout
                                );
                    
        $update_user = User::where('id', $user_id)->update($layout_data);
        return $this->sendResponse(array(), 'User Successfully.');


    }

    public function get_app_setting(Request $request)
    {
        
        $user_id=$request->user_id;
        $get_user = DB::table('users')->where('id', $user_id)->select('profile_layout', 'discover_layout', 'role', 'private_profile', 'shop_profile', 'shop_view_toggle as shop_layout')->first();
        $shop_profile_id = DB::table('shop_profiles')->where('user_id', $user_id)->select('id')->first();
        $get_user_private = DB::table('users')->where('id', $user_id)->select('is_private')->first();
        $app_setting = DB::table('page_texts')->where('id',7)->first();
        if($get_user->role == 'admin')
        {
            $get_users = DB::table('users')->where([['id', '!=', $user_id], ['deleted_at', '=', NULL]])->get();
            $total_users = $get_users->whereNotIn('status', [2,3])->count();
            $blocked_users = $get_users->where('status', 2)->count();
			//'user_id' => $request->user_id , 
            $admin_shops = ShopProduct::where(['status' => 1,'deleted_at' => NULL])->count();
        }
        else
        {
            $total_users = NULL;
            $blocked_users = NULL;
            $admin_shops = NULL;
        }
		// 25-11-2020 view shops user condition removed
        //'user_id' => $request->user_id ,
		$filter_array = array(1,2);
//        $view_shops = ShopProduct::leftJoin('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')->whereIn('shop_products.filter_id', $filter_array)->where('shop_profiles.deleted_at', NULL)->where(['shop_products.status' => 1,'shop_products.deleted_at'=> NULL])->count();
                                                                      
        $view_shops = ShopProduct::where(['shop_products.status' => 1,'shop_products.deleted_at'=> NULL])->count();
			
		if($get_user->role == 'admin')
        {
			$my_shop = ShopProduct::where('user_id',$request->user_id)->where('deleted_at','=',NULL)->count();
		} else{
			//->where('status',0)
			$my_shop = ShopProduct::where('user_id',$request->user_id)->where('deleted_at','=',NULL)->count();
		}
        $my_orders = DB::table('orders')->where('user_id',$request->user_id)->count();
        $saved_post = DB::table('saved_posts')->where('user_id',$request->user_id)->count();
        
        $get_latest_post_image = DB::table('post')
                                ->join('post_files', 'post_files.post_id', '=', 'post.id')
                                ->where([['post.filter_id', 1], ['post.deleted_at', NULL], ['post.type', 'public']])
                                ->orderBy('post.id', 'DESC')
                                ->pluck('post_files.thumb')
                                ->first();

        $data = array();
        $data_values = (object)array(
            'profile_layout' => $get_user->profile_layout,
            'discover_layout' => $get_user->discover_layout,
            'shop_layout' => $get_user->shop_layout,
            'total_users' => $total_users,
            'blocked_users' => $blocked_users,
            'latest_post_image' => $get_latest_post_image,
            'private_profile' => $get_user->private_profile,
            'shop_profile' => $get_user->shop_profile,
            'shop_profile_id' => (empty($shop_profile_id)) ? null : $shop_profile_id->id,
            'is_private' => (empty($get_user_private)) ? 0 : $get_user_private->is_private,
            'admin_shops' => $admin_shops,
            'view_shops' => $view_shops,
            'my_shop' => $my_shop,
            'my_orders' => $my_orders,
            'saved_posts' => $saved_post,
            'embed_header' => $app_setting->text??"Paste in the url of the video you want to embed."
        );
        $data[] = $data_values;
        
        return response()->json(['code' => '200', 'success' => "Successfully", 'data' => $data]); 
    }
    
    public function get_statuses(Request $request)
    {
        $user_id = $request->user_id;
        $sort_by = $request->sort_by;
        $tag_type = $request->tag_type;
        $search = $request->search;
        $ssImageUrl = '';
        $final_array = array();
        
        if($tag_type == '@')
        {
            if($sort_by == 1)
            {
                $user_list = DB::table('followers')->join('users', 'users.id', '=', 'followers.follewers_id')->where([['user_id', $user_id], ['followers.deleted_at', NULL]])->select('follewers_id AS user_id','followers.created_at AS created_at')->orderBy('followers.created_at', 'DESC')->get();
            }
			else if($sort_by == 0)
            {
                $user_list = DB::table('followers')->join('users', 'users.id', '=', 'followers.follewers_id')->where([['user_id', $user_id], ['followers.deleted_at', NULL]])->select('follewers_id AS user_id','followers.created_at AS created_at')->orderBy('users.name', 'DESC')->get();
            }
            else if($sort_by == 3)
            {
                $user_list = DB::table('followers')->join('users', 'users.id', '=', 'followers.follewers_id')->where([['user_id', $user_id], ['followers.deleted_at', NULL]])->select('follewers_id AS user_id', 'followers.created_at AS created_at')->orderBy('users.created_at', 'ASC')->get();
            }
			else
            {
                $user_list = DB::table('followers')->join('users', 'users.id', '=', 'followers.follewers_id')->where([['user_id', $user_id], ['followers.deleted_at', NULL]])->select('follewers_id AS user_id','followers.created_at AS created_at')->orderBy('users.name', 'ASC')->get();
            }
            
            if(count($user_list) > 0)
            {
                $user_info = array();
                foreach($user_list as $userTest) {
                    $user_info[$userTest->user_id] = $userTest->created_at;
                }
                $user_list = $user_list->pluck('user_id')->toArray();
                $ids_ordered = implode(',', $user_list);
    
                $get_user_array = DB::table('users')
                                ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                                ->select('users.id', 'users.name', 'users.email', 'users.email_verified_at', 'user_detail.filter','users.tagname',
                                    DB::raw("CONCAT('" . $ssImageUrl . "',user_detail.profile_image_url) as image"), 
                                    'category', 'private_description', 'privete_profile_url', 'description');
                                if(!empty($search))
                                {
                                    $get_user_array->where('users.tagname', 'LIKE', '%'.$search.'%');
									$get_user_array = $get_user_array->orderBy("users.tagname", "asc")->get();
                                } else {
									$get_user_array->whereIn('users.id', $user_list);
									$get_user_array = $get_user_array->orderByRaw("FIELD(users.id, $ids_ordered)")->get();
								}
                                
                
                $final_array = array();
                for($j=0;$j<count($get_user_array);$j++)
                {
                    $created_at = $user_info[$get_user_array[$j]->id];
                    $time_ago = $this->time_diff($created_at);
                    $final_array[] = array('user_id' => $get_user_array[$j]->id,
                                           'time_ago' => $time_ago,
                                        'name' => $get_user_array[$j]->name,
                                        'email' => $get_user_array[$j]->email,
                                        'filter' => $get_user_array[$j]->filter,
                                        'image' => $get_user_array[$j]->image,
                                        'category' => $get_user_array[$j]->category,
                                        'privete_profile_url' => $get_user_array[$j]->privete_profile_url,
                                        'tagname' => $get_user_array[$j]->tagname,
                                        'private_description' => $get_user_array[$j]->private_description,
                                        'privete_profile_url' => $get_user_array[$j]->privete_profile_url,
                                        'description' => $get_user_array[$j]->description,
                                        'follow_statuses' => $this->get_follow_statuses($user_id,$get_user_array[$j]->id),
                                        'notification_statuses' => $this->get_notification_statuses($user_id,$get_user_array[$j]->id),
                                        'urls' => DB::table('user_urls')->where('user_id', $get_user_array[$j]->id)->get()
                                        );
                }
            }
        }
        else
        {
            if($sort_by == 1)
            {
                $hash_tags_list = DB::table('follow_hash_tags')->where([['user_id', $user_id], ['deleted_at', NULL]])->select('hash_tag_id')->orderBy('id', 'DESC')->get();   
            }
            else
            {
                $hash_tags_list = DB::table('follow_hash_tags as fht')
                                    ->join('hash_tags as ht', 'ht.id', '=', 'fht.hash_tag_id')
                                    ->where([['fht.user_id', $user_id], ['fht.deleted_at', NULL]])
                                    ->select('fht.hash_tag_id')
                                    ->orderBy('ht.hash_tag_name', 'ASC')->get();
            }
            
            if(count($hash_tags_list) > 0)
            {
                $hash_tags_list = $hash_tags_list->pluck('hash_tag_id')->toArray();
                $ids_ordered = implode(',', $hash_tags_list);
    
                $final_array = DB::table('hash_tags as ht')
                                        ->join('follow_hash_tags as fht', 'ht.id', '=', 'fht.hash_tag_id')
                                        ->select('ht.hash_tag_name', 'ht.id');
                                if(!empty($search))
                                {
                                    $final_array->where('ht.hash_tag_name', 'LIKE', '%'.$search.'%');
                                }
                                
                $final_array = $final_array->orderByRaw("FIELD(ht.id, $ids_ordered)")
                                ->get();
            }
        }
        
        return response()->json(['code' => 200, 'message' => 'User Detail', 'data' => $final_array]);
    }
    
    public function get_follow_statuses($user_id,$following_id)
    {
        $follow_statuses = DB::table('follow_statuses')->where([['user_id', $user_id], ['follewers_id', $following_id]])->select('status AS is_following','type AS status','id AS follow_status_id')->get();
        // return $follow_statuses;
        if($follow_statuses)
        {
			/* $i=0;
			foreach($follow_statuses as $value){
				$follow_statuses_result[$i]['is_following'] = $value->status;
				$follow_statuses_result[$i]['status'] = $value->type;
				$follow_statuses_result[$i]['follow_status_id'] = $value->follow_status_id;
				$i++;
			} */
            return $follow_statuses;
        }
        else
        {
            return array();
        }
    }
    
    public function get_notification_statuses($user_id,$following_id)
    {
		$notification_statuses = DB::table('notification_statuses')->where([['user_id', $user_id], ['follewers_id', $following_id]])->select('status AS is_following','type AS status','id AS notification_status_id')->groupBy('type')->get();
        // return $follow_statuses;
        if($notification_statuses)
        {
            return $notification_statuses;
        }
        else
        {
            return array();
        }
    }
    
    
    public function follow_statuses(Request $request)
    {
        $user_id = $request->user_id;
        $following_id = $request->following_id;
        $type = $request->status;
        $api_type = $request->api_type;
        
        $status_array = array('follewers_id' => $following_id,
                                'user_id' => $user_id,
                                'type' => $type
                                );
        if($api_type == 1)
        {
			$fstatus = DB::table('follow_statuses')->where($status_array)->get();
			if($fstatus->count() == 0){
				$status_array['status'] = 1;
				$save_follow_statuses = DB::table('follow_statuses')->insert($status_array);    
			} else{
				if($fstatus->count() > 1){
					$save_follow_statuses = DB::table('follow_statuses')->where('id', $fstatus[0]->id)->delete();  
				}		
				$save_follow_statuses = DB::table('follow_statuses')->where($status_array)->update(['status'=>1]);    
			}
        }
        else
        {
			DB::table('notification_statuses')->where($status_array)->delete();
			$status_array['status'] = 1;
			$status_array['created_at'] = date('Y-m-d H:i:s');
			$status_array['updated_at'] = date('Y-m-d H:i:s');
            $save_notification_statuses = DB::table('notification_statuses')->insert($status_array);    
        }
        return $this->sendResponse(array(), 'Successfully.');
    }
    
    public function unfollow_statuses(Request $request)
    {
        $id = $request->id;
        $noti_status_id = $request->noti_status_id;
        $api_type = $request->api_type;
        $action_type = $request->action_type;
        
        
        if($api_type == 1)
        {
            if($action_type == 1)
            {
                \App\Followers::where('follewers_id', $request->follow_by_id)->where('user_id', $request->user_id)->delete();
                $save_follow_statuses = DB::table('follow_statuses')->where('id', $id)->delete();  
                $save_notification_statuses = DB::table('notification_statuses')->where('id', $noti_status_id)->delete();
            }
            else
            {
                //$save_follow_statuses = DB::table('follow_statuses')->where('id', $id)->delete();  
				
				$save_follow_statuses = DB::table('follow_statuses')->where('id', $id)->update(['status'=>0,'updated_at'=>date('Y-m-d H:i:s')]);
                $save_notification_statuses = DB::table('notification_statuses')->where('id', $noti_status_id)->delete();
            }
             
        }
        else
        {
			$save_follow_statuses = DB::table('notification_statuses')->where('id', $noti_status_id)->update(['status'=>0,'updated_at'=>date('Y-m-d H:i:s')]);
            //$save_notification_statuses = DB::table('notification_statuses')->where('id', $noti_status_id)->delete();
        }
        return $this->sendResponse(array(), 'Successfully.');
    }
    
    
    public function save_or_unsave_post(Request $request)
    {
        $user_id  = $request->user_id;
        $post_id = $request->post_id;
        $is_repost = $request->is_repost;
        $action = $request->action;
        $object_type = $request->object_type??1;
        
        if($action == 'save')
        {   
            $data = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
				'object_type'=> $object_type,
                'created_at' => Carbon::now()
            );
			
			if(!DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $post_id], ['object_type', $object_type]])->first()){
				DB::table('saved_posts')->insert($data);
				
				$current_user = User::where('id', $request->user_id)->pluck('name')->first();
				if(!empty($object_type)){
					$posts = Post::where('id', $request->post_id)->first();
				}
				if(empty($object_type)){
					$posts = ShopProduct::where('id', $request->post_id)->first();
				}
				
				$is_repost = $request->is_repost??0;
				if($request->is_repost){
					if($object_type){
						$get_post = DB::table('post')
									->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
									->distinct('id')
									->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
									->join('users', 'post.user_id', '=', 'users.id')
									->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
									->where('post.id', $request->post_id)
									->first(); 
						$share_post = SharePost::where('feed_id',$request->post_id)->where('feed_type',1)->get();
						foreach($share_post as $value){
							$message = $current_user. ' saved your repost from '.$get_post->name??"";
							$this->saveActivity($request->user_id,$value->user_id,$request->post_id,'saved',$message,$request->is_repost);
						}			
					} else{
						$get_post = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
							$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
                        ->leftJoin('user_detail', 'user_detail.user_id', '=', 'shop_profiles.user_id')
                        ->select('shop_profiles.id', 'shop_profiles.name as shop_name', 'shop_profiles.company_name', 'user_detail.profile_image_url as shop_image_url', 'shop_profiles.shop_name as user_name', 'users.tagname as user_tagname');
						}])
						->where([['id', $request->post_id], ['status', 1]])->first();
						
						$share_post = SharePost::where('feed_id',$request->post_id)->where('feed_type',0)->get();
						foreach($share_post as $value){
							echo $message = $current_user. ' saved your repost from '.$get_post->name??"";
							$this->saveActivity($request->user_id,$value->user_id,$request->post_id,'saved',$message,$is_repost);
						}
					}
					
				} else{
					$message = $current_user. ' saved on your post.';
					$this->saveActivity($request->user_id,$posts->user_id,$request->post_id,'saved',$message,$request->is_repost);
				}
			}
            return $this->sendResponse(array(), 'Saved Successfully.');
        }
        else
        {
            DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $post_id], ['object_type', $object_type]])->delete();
			
			$current_user = User::where('id', $request->user_id)->pluck('name')->first();
			if(!empty($object_type)){
				$posts = Post::where('id', $request->post_id)->first();
			}
			if(empty($object_type)){
				$posts = ShopProduct::where('id', $request->post_id)->first();
			}
			$is_repost = $request->is_repost??0;
			if($request->is_repost){
				if($object_type){
					$get_post = DB::table('post')
								->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'post.video', 'post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
								->distinct('id')
								->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
								->join('users', 'post.user_id', '=', 'users.id')
								->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('post.id', $request->post_id)
								->first(); 
					$share_post = SharePost::where('feed_id',$request->post_id)->where('feed_type',1)->get();
					foreach($share_post as $value){
						$message = $current_user. ' unsaved your repost from '.$get_post->name??"";
						$this->saveActivity($request->user_id,$value->user_id,$request->post_id,'unsaved',$message,$is_repost);
					}			
				} else{
					
					$get_post = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
						$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
					->leftJoin('user_detail', 'user_detail.user_id', '=', 'shop_profiles.user_id')
					->select('shop_profiles.id', 'shop_profiles.name as shop_name', 'shop_profiles.company_name', 'user_detail.profile_image_url as shop_image_url', 'shop_profiles.shop_name as user_name', 'users.tagname as user_tagname');
					}])
					->where([['id', $request->post_id], ['status', 1]])->first();
					
					$share_post = SharePost::where('feed_id',$request->post_id)->where('feed_type',0)->get();
					foreach($share_post as $value){
						echo $message = $current_user. ' unsaved your repost from '.$get_post->name??"";
						$this->saveActivity($request->user_id,$value->user_id,$request->post_id,'unsaved',$message,$is_repost);
					}
				}
				
			} else {
				$message = $current_user. ' unsaved on your post.';
				$this->saveActivity($request->user_id,$posts->user_id,$request->post_id,'unsaved',$message,$is_repost);
			}
				
            return $this->sendResponse(array(), 'Unsaved Successfully.');
        }
    }
	
	public function get_save_post(Request $request)
    {
        $user_id  = $request->user_id;
        $limit = 10;
        $data = DB::table('saved_posts')->select('post_id','object_type')->where('user_id',$user_id)->limit($limit)->offset((($request->page??1)-1)*$limit)->orderBy('id', 'DESC')->get();
		$i = 0;
		$saData = [];
		foreach($data as $value)
        {   
			if($value->object_type == 1){
				$get_post = DB::table('post')
								->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url', 'post.video','post.video_thumbnail','post.video_thumb_title','post.owner_name','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
								->distinct('id')
								->join('user_detail', 'post.user_id', '=', 'user_detail.user_id')
								->join('users', 'post.user_id', '=', 'users.id')
								->join('follow_statuses', 'users.id', '=', 'follow_statuses.follewers_id')
								->where('post.id', $value->post_id)
								->first(); 
				if($get_post){
					$saData[$i]['id'] = $get_post->id;
					$saData[$i]['user_id'] = $get_post->user_id;
					$saData[$i]['user_name'] = $get_post->name;
					$saData[$i]['tagname'] = $get_post->tagname;
					$saData[$i]['email'] = $get_post->email;
					$saData[$i]['user_profile_image'] = $get_post->profile_image_url;

					if (!empty($get_post->video)) {
						$saData[$i]['video'] = 'https://artfora.net/images/post/Video/' . $get_post->video;
						$ssExt = pathinfo($saData[$i]['video'], PATHINFO_EXTENSION);
						$saData[$i]['image_ext'] = $ssExt;
					} else {
						$saData[$i]['video'] = null;
					}
					$saData[$i]['is_flipped'] = $get_post->is_flipped;
					$saData[$i]['is_rotated'] = $get_post->is_rotated;
					$saData[$i]['owner_name'] = $get_post->owner_name;
                    $saData[$i]['video_thumbnail'] = $get_post->video_thumbnail;
                    $saData[$i]['video_thumb_title']= $get_post->video_thumb_title;
					$saData[$i]['decription'] = $get_post->decription;
					$saData[$i]['tag'] = $get_post->tag;
					$saData[$i]['filter'] = $get_post->filter;
					$saData[$i]['type'] = $get_post->type;

					$saGetDetail = \App\PostDetail::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->get()->toArray();
					$saGeLikes = \App\PostLike::select('title', 'description')->whereNull('deleted_at')->where([['post_id', $get_post->id], ['type', 'like']])->count();
					$saGeComments = \App\PostComment::select('title', 'description')->whereNull('deleted_at')->where('post_id', $get_post->id)->count();
					 $saGetTag = DB::table('user_tags')->select('user_tags.tag_user','users.tagname')
					 ->join('users', 'users.id', '=', 'user_tags.tag_user')
					 ->where('user_tags.post_id', $get_post->id)->groupBy('user_tags.tag_user')->get()->toArray();
					$is_saved = DB::table('saved_posts')->where([['user_id', $user_id], ['post_id', $get_post->id]])->first();
					if($is_saved)
					{
						$is_saved = 'yes';
					}
					else
					{
						$is_saved = 'no';
					}
					
					 /***************************************************************/
					$stData2 = array();
					$getStatus = DB::table('post_tags')
								->where('post_id', $get_post->id)
								->join('users', 'users.id', '=', 'post_tags.user_id')
								->join('user_detail', 'user_detail.user_id', '=', 'users.id')
								 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
								->get();
					   
					   $saData[$i]['tag_list']=$getStatus;



					$saData[$i]['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
					$saData[$i]['is_saved'] = $is_saved;
					$saData[$i]['detail'] = $saGetDetail;
					$saData[$i]['likes'] = $saGeLikes;
					$saData[$i]['comments'] = $saGeComments;
					$saData[$i]['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
					$saData[$i]['tags'] = $saGetTag;
					$saData[$i]['created_at'] = $get_post->created_at;

					$saData[$i]['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','video_url','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
					$saData[$i]['object_type'] = 1;
					$i++;
				} 
			} 
			else 
			{
				$saData[$i] = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
                        $query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
                        ->leftJoin('user_detail', 'user_detail.user_id', '=', 'shop_profiles.user_id')
                        ->select('shop_profiles.id', 'shop_profiles.name as shop_name', 'shop_profiles.company_name', 'user_detail.profile_image_url as shop_image_url', 'shop_profiles.shop_name as user_name', 'users.tagname as user_tagname');
                }])
                ->withCount([
                    'shop_product_meta as likes' => function($query){
                        $query->where('type', 'like');
                    },
                    'shop_product_meta as comments' => function($query){
                        $query->where('type', 'comment');
                    },
                    'shop_product_meta as is_liked' => function($query) use ($user_id){
                        $query->where([['type', 'like'], ['user_id', $user_id]]);
                    },
                    'shop_product_meta as is_saved' => function($query) use ($user_id){
                        $query->where([['type', 'save'], ['user_id', $user_id]]);
                    }
                ])
                ->where([['id', $value->post_id], ['status', 1]])->first();
				if($saData[$i]){
					$get_profile = DB::table('shop_profiles')->where('user_id',  $user_id)->select('name', 'rating')->first();
					$saData[$i]['posted_by'] = User::where('id', $user_id)->pluck('full_name')->first();
					$saData[$i]['seller_rating'] = $get_profile->rating;
					$saData[$i]['object_type'] = 0;
					$i++;
				}
			}
        }	
        return $this->sendResponse($saData, 'Get Saved Successfully.');
    }
    
    
    public function block_unblock_delete_user_by_admin(Request $request)
    {   
        $user_id = $request->user_id;
        $action = $request->action;
        
        if($action == 'delete')
        {
            DB::table('blocked_users')->where('user_id', $user_id)->delete();
            DB::table('blocked_users')->where('blocked_userid', $user_id)->delete();
            DB::table('followers')->where('user_id', $user_id)->delete();
            DB::table('followers')->where('follewers_id', $user_id)->delete();
            DB::table('follow_statuses')->where('user_id', $user_id)->delete();
            DB::table('follow_statuses')->where('follewers_id', $user_id)->delete();
            DB::table('interest_group')->where('user_id', $user_id)->delete();
            DB::table('interest_group_posts')->where('user_id', $user_id)->delete();
            DB::table('notification_statuses')->where('user_id', $user_id)->orWhere('follewers_id', $user_id)->delete();
            DB::table('saved_posts')->where('user_id', $user_id)->delete();
            DB::table('status_views')->where('user_id', $user_id)->delete();
            DB::table('tag_users')->where('user_id', $user_id)->delete();
            DB::table('activity_log')->where('user_id', $user_id)->delete();
            DB::table('activity_time')->where('user_id', $user_id)->delete();
            DB::table('users')->where('id', $user_id)->delete();
            DB::table('user_detail')->where('user_id', $user_id)->delete();
            DB::table('user_tags')->where('user_id', $user_id)->delete();
            DB::table('user_tags')->where('tag_user', $user_id)->delete();
            
            // DELETE CONVERSATIONS AND FILES ///////////////////////////////////////
            $conversations = DB::table('conversations')->where('sender_id', $user_id)->orWhere('receiver_id', $user_id)->pluck('id')->toArray();
            $coversation_files = DB::table('messages')->whereIn('conversation_id', $conversations)->where('file', '!=', NULL)->get();
            
            foreach($coversation_files as $c_file)
            {
                $c_file_name = explode("/", $c_file->file_url, 4);
                $c_file_thumb_name = explode("/", $c_file->thumbnail, 5);

                if(file_exists(public_path("images/chat_files/".$c_file_name[3])))
                {
                    unlink(public_path("images/chat_files/".$c_file_name[3]));
                }
                
                if(file_exists(public_path("images/chat_files/thumbnail/".$c_file_thumb_name[4])))
                {
                    unlink(public_path("images/chat_files/thumbnail/".$c_file_thumb_name[4]));
                }
            }
            
            DB::table('messages')->whereIn('conversation_id', $conversations)->delete();
            DB::table('conversations')->whereIn('id', $conversations)->delete();
            // DELETE CONVERSATIONS AND FILES ///////////////////////////////////////
            
            
            // DELETE STATUSES AND FILES ///////////////////////////////////////
            $statuses = DB::table('statuses')->where('user_id', $user_id)->get();
            foreach($statuses as $s_file)
            {
                if(file_exists(public_path("images/status/Video/".$s_file->file)))
                {
                    unlink(public_path("images/status/Video/".$s_file->file));
                }
                
                if(file_exists(public_path("images/status/VideoImage/".$s_file->vedio_image)))
                {
                    unlink(public_path("images/status/VideoImage/".$s_file->vedio_image));
                }
            }
            
            $statuses_id = $statuses->pluck('id')->toArray();
            DB::table('status_views')->whereIn('status', $statuses_id)->delete();
            $statuses = DB::table('statuses')->where('user_id', $user_id)->delete();
            // DELETE STATUSES AND FILES ///////////////////////////////////////
            
            
            // DELETE POSTS AND FILES ///////////////////////////////////////
            $posts = DB::table('post')->where('user_id', $user_id)->get();
            $posts_id = $posts->pluck('id')->toArray();
            
            foreach($posts as $post)
            {
                $post_files = DB::table('post_files')->where('post_id', $post->id)->get();
                foreach($post_files as $p_file)
                {
                    if(file_exists(public_path("images/post/new_images/".$p_file->file_name)))
                    {
                        unlink(public_path("images/post/new_images/".$p_file->file_name));
                    }
                    
                    if(file_exists(public_path("images/post/new_images/thumb/".$p_file->thumb)))
                    {
                        unlink(public_path("images/post/new_images/thumb/".$p_file->thumb));
                    }
                                                              
                      if(file_exists(public_path("images/post/new_images/".$p_file->resized_file_name)))
                      {
                          unlink(public_path("images/post/new_images/".$p_file->resized_file_name));
                      }
                }
            }
            
            DB::table('post')->where('user_id', $user_id)->delete();
            DB::table('post_comment')->whereIn('post_id', $posts_id)->delete();
            DB::table('post_detail')->whereIn('post_id', $posts_id)->delete();
            DB::table('post_likes')->whereIn('post_id', $posts_id)->delete();
            DB::table('user_tags')->whereIn('post_id', $posts_id)->delete();
            // DELETE POSTS AND FILES ///////////////////////////////////////
            
            // DELETE SHOP PRODUCTS ///////////////////////////////////////
            DB::table('shop_profiles')->where('user_id', $user_id)->delete();
            DB::table('shop_products')->where('user_id', $user_id)->delete();
            DB::table('shop_product_details')->where('user_id', $user_id)->delete();
            $shop_products = DB::table('shop_product_files')->where('user_id', $user_id)->get();
            foreach($shop_products as $s_file)
            {
                if(file_exists(public_path("images/post/new_images/".$s_file->file_name)))
                {
                    unlink(public_path("images/post/new_images/".$s_file->file_name));
                }
                
                if(file_exists(public_path("images/post/new_images/thumb/".$s_file->thumb)))
                {
                    unlink(public_path("images/post/new_images/thumb/".$s_file->thumb));
                }
                                                              
                  if(file_exists(public_path("images/post/new_images/".$s_file->resized_file_name)))
                  {
                      unlink(public_path("images/post/new_images/".$s_file->resized_file_name));
                  }
            }
            
            $shop_product_files = $shop_products->pluck('id')->toArray();
            $shop_product_ids = $shop_products->pluck('shop_product_id')->toArray();
            DB::table('shop_product_files')->whereIn('id', $shop_product_files)->delete();
            DB::table('shop_product_metas')->whereIn('shop_product_id', $shop_product_ids)->delete();
            DB::table('product_ratings')->where('seller_id', $user_id)->delete();
            DB::table('seller_ratings')->where('seller_id', $user_id)->delete();
            DB::table('shop_shipping_costs')->where('user_id', $user_id)->delete();
            // DELETE SHOP PRODUCTS ///////////////////////////////////////

            return $this->sendResponse(array(), 'User is Deleted Successfully.');
            
        }
        else
        {
            if($action == 'block')
            {
                $status_user = 2;
                $deleted_at = Carbon::now();
            }
            else
            {
                $status_user = 1;
                $deleted_at = NULL;
            }
            
            DB::table('users')->where('id', $user_id)->update(['status' => $status_user, 'blocked_at' => $deleted_at]);
            DB::table('post')->where('user_id', $user_id)->update(['deleted_at' => $deleted_at]);
            DB::table('post_comment')->where('user_id', $user_id)->update(['deleted_at' => $deleted_at]);
            DB::table('post_likes')->where('user_id', $user_id)->update(['deleted_at' => $deleted_at]);
            DB::table('statuses')->where('user_id', $user_id)->update(['deleted_at' => $deleted_at]);
            DB::table('status_views')->where('user_id', $user_id)->update(['deleted_at' => $deleted_at]);
            DB::table('shop_profiles')->where('user_id', $user_id)->update(['deleted_at' => $deleted_at]);
            
            $action = ($action == 'block') ? 'Blocked' : 'Unblocked';
            return $this->sendResponse(array(), 'User is '.$action.' Successfully.');
        }
    }
    
    public function delete_user_by_cron(Request $request)
    {   
		$Users = User::where('status',3)->where('delete_email_verified_at','<=',date('Y-m-d H:i:s'))->get();
		if($Users->count() > 0)
		{
			foreach($Users as $saUsers)
			{ 
				$user_id = $saUsers->id;
			
				DB::table('blocked_users')->where('user_id', $user_id)->delete();
				DB::table('blocked_users')->orWhere('blocked_userid', $user_id)->delete();
				DB::table('followers')->where('user_id', $user_id)->delete();
				DB::table('followers')->orWhere('follewers_id', $user_id)->delete();
				DB::table('follow_statuses')->where('user_id', $user_id)->delete();
				DB::table('follow_statuses')->orWhere('follewers_id', $user_id)->delete();
				DB::table('interest_group')->where('user_id', $user_id)->delete();
				DB::table('interest_group_posts')->where('user_id', $user_id)->delete();
				DB::table('notification_statuses')->where('user_id', $user_id)->orWhere('follewers_id', $user_id)->delete();
				DB::table('saved_posts')->where('user_id', $user_id)->delete();
				DB::table('status_views')->where('user_id', $user_id)->delete();
				DB::table('tag_users')->where('user_id', $user_id)->delete();
				DB::table('activity_log')->where('user_id', $user_id)->delete();
				DB::table('activity_time')->where('user_id', $user_id)->delete();
				DB::table('users')->where('id', $user_id)->delete();
				DB::table('user_detail')->where('user_id', $user_id)->delete();
				DB::table('user_tags')->where('user_id', $user_id)->delete();
				DB::table('user_tags')->where('tag_user', $user_id)->delete();
				
				// DELETE CONVERSATIONS AND FILES ///////////////////////////////////////
				$conversations = DB::table('conversations')->where('sender_id', $user_id)->orWhere('receiver_id', $user_id)->pluck('id')->toArray();
				$coversation_files = DB::table('messages')->whereIn('conversation_id', $conversations)->where('file', '!=', NULL)->get();
				
				foreach($coversation_files as $c_file)
				{
					$c_file_name = explode("/", $c_file->file_url, 4);
					$c_file_thumb_name = explode("/", $c_file->thumbnail, 5);

					if(file_exists(public_path("images/chat_files/".$c_file_name[3])))
					{
						unlink(public_path("images/chat_files/".$c_file_name[3]));
					}
					if(file_exists(public_path("images/chat_files/thumbnail/".$c_file_thumb_name[4])))
					{
						unlink(public_path("images/chat_files/thumbnail/".$c_file_thumb_name[4]));
					}
				}
				
				DB::table('messages')->whereIn('conversation_id', $conversations)->delete();
				DB::table('conversations')->whereIn('id', $conversations)->delete();
				// DELETE CONVERSATIONS AND FILES ///////////////////////////////////////
				
				
				// DELETE STATUSES AND FILES ///////////////////////////////////////
				$statuses = DB::table('statuses')->where('user_id', $user_id)->get();
				foreach($statuses as $s_file)
				{
					if(file_exists(public_path("images/status/Video/".$s_file->file)))
					{
						unlink(public_path("images/status/Video/".$s_file->file));
					}
					if(file_exists(public_path("images/status/VideoImage/".$s_file->vedio_image)))
					{
						unlink(public_path("images/status/VideoImage/".$s_file->vedio_image));
						
					}
					if(file_exists(public_path("images/status/VideoImage/thumbtab/".$s_file->vedio_image)))
					{
						unlink(public_path("images/status/VideoImage/thumbtab/".$s_file->vedio_image));
					}
				}
				
				$statuses_id = $statuses->pluck('id')->toArray();
				DB::table('status_views')->whereIn('status', $statuses_id)->delete();
				$statuses = DB::table('statuses')->where('user_id', $user_id)->delete();
				// DELETE STATUSES AND FILES ///////////////////////////////////////
				
				
				// DELETE POSTS AND FILES ///////////////////////////////////////
				$posts = DB::table('post')->where('user_id', $user_id)->get();
				$posts_id = $posts->pluck('id')->toArray();
				
				foreach($posts as $post)
				{
					$post_files = DB::table('post_files')->where('post_id', $post->id)->get();
					foreach($post_files as $p_file)
					{
						if(file_exists(public_path("images/post/new_images/".$p_file->file_name)))
						{
							unlink(public_path("images/post/new_images/".$p_file->file_name));
						}
                          if(file_exists(public_path("images/post/new_images/".$p_file->resized_file_name)))
                          {
                              unlink(public_path("images/post/new_images/".$p_file->resized_file_name));
                          }
						if(file_exists(public_path("images/post/new_images/".$p_file->thumb)))
						{
							unlink(public_path("images/post/new_images/".$p_file->thumb));
						}
						if(file_exists(public_path("images/post/new_images/thumb/".$p_file->thumb)))
						{
							unlink(public_path("images/post/new_images/thumb/".$p_file->thumb));
						}
						if(file_exists(public_path("images/post/new_images/thumbtab/".$p_file->thumb)))
						{
							unlink(public_path("images/post/new_images/thumbtab/".$p_file->thumb));
						}
					}
				}
				
				DB::table('post')->where('user_id', $user_id)->delete();
				DB::table('post_comment')->whereIn('post_id', $posts_id)->delete();
				DB::table('post_detail')->whereIn('post_id', $posts_id)->delete();
				DB::table('post_likes')->whereIn('post_id', $posts_id)->delete();
				DB::table('user_tags')->whereIn('post_id', $posts_id)->delete();
				// DELETE POSTS AND FILES ///////////////////////////////////////
				
				// DELETE SHOP PRODUCTS ///////////////////////////////////////
				DB::table('shop_profiles')->where('user_id', $user_id)->delete();
				DB::table('shop_products')->where('user_id', $user_id)->delete();
				DB::table('shop_product_details')->where('user_id', $user_id)->delete();
				$shop_products = DB::table('shop_product_files')->where('user_id', $user_id)->get();
				foreach($shop_products as $s_file)
				{
					if(file_exists(public_path("images/post/new_images/".$s_file->file_name)))
					{
						unlink(public_path("images/post/new_images/".$s_file->file_name));
					}
                      if(file_exists(public_path("images/post/new_images/".$s_file->resized_file_name)))
                      {
                          unlink(public_path("images/post/new_images/".$s_file->resized_file_name));
                      }
					if(file_exists(public_path("images/post/new_images/".$s_file->thumb)))
					{
						unlink(public_path("images/post/new_images/".$s_file->thumb));
					}
					if(file_exists(public_path("images/post/new_images/thumb/".$s_file->thumb)))
					{
						unlink(public_path("images/post/new_images/thumb/".$s_file->thumb));
					}
					if(file_exists(public_path("images/post/new_images/thumb/thumbtab/".$s_file->thumb)))
					{
						unlink(public_path("images/post/new_images/thumb/thumbtab/".$s_file->thumb));
					}
				}
				
				$shop_product_files = $shop_products->pluck('id')->toArray();
				$shop_product_ids = $shop_products->pluck('shop_product_id')->toArray();
				DB::table('shop_product_files')->whereIn('id', $shop_product_files)->delete();
				DB::table('shop_product_metas')->whereIn('shop_product_id', $shop_product_ids)->delete();
				DB::table('product_ratings')->where('seller_id', $user_id)->delete();
				DB::table('seller_ratings')->where('seller_id', $user_id)->delete();
				DB::table('shop_shipping_costs')->where('user_id', $user_id)->delete();
				// DELETE SHOP PRODUCTS ///////////////////////////////////////   
			}
		}
    }
	
    public function blocked_user_list_by_admin()
    {
        $blocked_users = DB::table('users')
                    ->select('users.id', 'users.email', 'users.tagname', 'users.name', 'user_detail.profile_image_url', 'users.blocked_at')
                    ->leftJoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                    ->where('users.status', 2)
                    ->get();  
        
        $blocked_users = $blocked_users->map(function ($item, $key) {
            $item->blocked_at = Carbon::createFromTimeStamp(strtotime($item->blocked_at))->diffForHumans();
            return $item;
        });
        return $this->sendResponse($blocked_users, 'Blocked Users');
    }
    
    
    function send_push_with_data($title, $body, $tokens, $user_id, $post_id, $user_name, $user_img) {
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Custom data to be sent with the push
        
        // $testing_array = array(
        //     'mutable-content' => '1',
        // );
        
        $data = array
            (
            'message' => 'here is a message. message',
            'title' => $title,
            'body' => $body,
            // 'mutable-content' => $testing_array,
            'smallIcon' => 'small_icon',
            'some data' => 'Some Data',
            'click_action' => 'CustomSamplePush',
            'sound' => 'default',
        );


        $data2=array(
            'user_id'=>$user_id,
            'post_id' => $post_id,
            'user_name' => $user_name,
            'user_image' => $user_img,
            // 'mutable-content' => '1',
        );

        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array(
            'registration_ids' => $tokens,
            'notification' => array(
                          "title" => $title,
                          "body" => $body,
                          "mutable_content" => true,
                          "sound" => "Tri-tone",
                          "click_action" => "CustomSamplePush"
                          ),
            'data' => $data2,
            'priority' => 'high',
        );

        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend);
        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        $headers[] = 'Authorization: key= AIzaSyCrWenpawLiljuUw9PRb8VTNMOeMRCsryQ';

        //Setup curl, add headers and post parameters.

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //Send the request
        $response = curl_exec($ch);
        
        //Close request
        curl_close($ch);
        return $response;
    }
    
    
    public function follow_unfollow_hash_tag(Request $request)
    {
        $user_id = $request->user_id;
        $hash_tag_id = $request->hash_tag_id;
        $action = $request->action;
        
        if($action == 'follow')
        {
            FollowHashTag::create(['hash_tag_id' => $hash_tag_id, 'user_id' => $user_id]);
            return $this->sendResponse(array(), 'Hash Tag Followed Successfully.');
        }
        else if($action == 'unfollow')
        {
            FollowHashTag::where([['hash_tag_id', $hash_tag_id], ['user_id', $user_id]])->forceDelete();
            return $this->sendResponse(array(), 'Hash Tag Unfollowed Successfully.');
        }
        else
        {
            $followed_hash_tags = FollowHashTag::where('user_id', $user_id)->with('hashTags')->get();
            return $this->sendResponse($followed_hash_tags, 'Followed Hash Tags');
        }
    }
    
    
    public function admin_search_user(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            if($request->page){
                $start_from=($request->page*$this->limit_for_search);
            }
            else{
               $start_from=0;
            }
            
            $ssImageUrl = '';
            $saSearchUser = DB::table('users')->select('users.id', 'users.name', 'users.tagname', 'users.email', 'users.deleted_at', 'users.email_verified_at', 'user_detail.filter', DB::raw("CONCAT('" . $ssImageUrl . "',user_detail.profile_image_url) as image"), 'category', 'private_description', 'privete_profile_url', 'description')
                            ->where([['users.id', '!=', $request->user_id], ['users.status', 1], ['users.role', '!=', 'admin']]);
                            
                        if(!empty($request->username))
                        {
                            $saSearchUser = $saSearchUser->where('users.name', 'like', $request->username . '%');
                        }
                        
            $saSearchUser = $saSearchUser->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')->orderBy('users.id', 'DESC')
                            ->offset($start_from)
                            ->limit($this->limit_for_search)
                            ->get();
                            
            for ($i = 0; count($saSearchUser) > $i; $i++) {
                $saInterestGroup = DB::table('interest_group')->select('interest_name')->where('user_id', $saSearchUser[$i]->id)->get()->toArray();
                $saSearchUser[$i]->interest_group = $saInterestGroup;
                $saSearchUser[$i]->urls = DB::table('user_urls')->where('user_id', $saSearchUser[$i]->id)->get();
                $snIsFollow = Followers::where('follewers_id', $saSearchUser[$i]->id)->where('user_id', $request->user_id)->get()->first();
                if (empty($snIsFollow)) {
                    //Not Follow
                    $saSearchUser[$i]->is_follow = FALSE;
                } else {
                    //Follow
                    $saSearchUser[$i]->is_follow = TRUE;
                }
            }

            return $this->sendResponse($saSearchUser, 'User Listed Successfully');
        }
    }
    
    
    public function post_views_comments_likes_count_by_country(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'is_product' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            $post_id = $request->post_id;
            $isProduct = $request->is_product;
            $likes = DB::table('users as u')
            ->join('post_likes as pl', 'pl.user_id', '=', 'u.id')
            ->select('u.country', DB::raw('count(pl.id) as likes_count'))
            ->where([['pl.post_id', $post_id], ['pl.type', 'like'], ['pl.deleted_at', NULL]])
            ->groupBy('u.country')
            ->orderBy('likes_count', 'DESC')
            ->get();
            
            $views = DB::table('users as u')
            ->join('post_likes as pl', 'pl.user_id', '=', 'u.id')
            ->select('u.country', DB::raw('count(pl.id) as views_count'))
            ->where([['pl.post_id', $post_id], ['pl.type', 'view'], ['pl.deleted_at', NULL]])
            ->groupBy('u.country')
            ->orderBy('views_count', 'DESC')
            ->get();

            if ($isProduct) {
                $clicks = DB::table('users as u')
                ->join('shop_product_metas as pl', 'pl.user_id', '=', 'u.id')
                ->select('u.country', DB::raw('count(pl.id) as clicks_count'))
                ->where([['pl.shop_product_id', $post_id], ['pl.type', 'click'], ['pl.deleted_at', NULL]])
                ->groupBy('u.country')
                ->orderBy('clicks_count', 'DESC')
                ->get();
            }
            else {
                $clicks = DB::table('users as u')
                ->join('post_likes as pl', 'pl.user_id', '=', 'u.id')
                ->select('u.country', DB::raw('count(pl.id) as clicks_count'))
                ->where([['pl.post_id', $post_id], ['pl.type', 'click'], ['pl.deleted_at', NULL]])
                ->groupBy('u.country')
                ->orderBy('clicks_count', 'DESC')
                ->get();
            }
            
            $comments = DB::table('users as u')
            ->join('post_comment as pc', 'pc.user_id', '=', 'u.id')
            ->select('u.country', DB::raw('count(pc.id) as comments_count'))
            ->where([['pc.post_id', $post_id], ['pc.deleted_at', NULL]])
            ->groupBy('u.country')
            ->orderBy('comments_count', 'DESC')
            ->get();
            
            $saved = DB::table('users as u')
            ->join('saved_posts as sp', 'sp.user_id', '=', 'u.id')
            ->select('u.country', DB::raw('count(sp.id) as saved_count'))
            ->where('sp.post_id', $post_id)
            ->groupBy('u.country')
            ->orderBy('saved_count', 'DESC')
            ->get();

 
            $visitor_data = DB::table('country_wise_visitor_data')
            ->select ( "id","post_id","post_url", DB::raw("COUNT('post_id') as country_click") )
            ->where('post_id',$post_id)
            ->groupby('post_id')->first();
           
           
            $data = array(
                'likes_count' => $likes->sum('likes_count'),
                'views_count' => $views->sum('views_count'),
                'comments_count' => $comments->sum('comments_count'),
                'saved_count' => $saved->sum('saved_count'),
                'clicks_count' => $clicks->sum('clicks_count'),
                'likes_by_country' => $likes, 
                'views_by_country' => $views, 
                'comments_by_country' => $comments,
                'saved_by_country' => $saved,
                'clicks_by_country' => $clicks,
                'read_count' => $visitor_data->country_click,
            );
            return $this->sendResponse($data, 'Post meta counts');
        }
        
    }



    /*==============================================
                    POST CLICK
    ==============================================*/

    public function post_click(Request $request) {
        $validator = Validator::make($request->all(), [
                    'post_id' => 'required',
                    'user_id' => 'required',
                    'is_product' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            if ($request->is_product) {
                ShopProductMeta::create(['user_id' => $request->user_id, 'shop_product_id' => $request->post_id, 'type' => 'click']);
                return $this->sendResponse(array(), 'Product is clicked.');
            }
            else {
                PostLike::create(['user_id' => $request->user_id, 'post_id' => $request->post_id, 'type' => 'click']);
                return $this->sendResponse(array(), 'Post is clicked.');
            }
        }
    }
    
    
    
    /*==============================================
                UPDATE APP TOKEN
    ==============================================*/

    public function update_app_token(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'app_token' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            User::where('id', $request->user_id)->update(['app_token' => $request->app_token]);
            return $this->sendResponse(array(), 'Token is updated.');
        }
    }



    /*==============================================
                GET PAGES TEXT
    ==============================================*/

    public function get_pages_text(Request $request) {
        $page_texts =  DB::table('page_texts')->get();
        return $this->sendResponse($page_texts, 'Page Texts.');
    }
    
    
    
    /*==============================================
                CHECK EMAIL EXISTS
    ==============================================*/
    
    public function check_email_exists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails())
        {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        else
        {
            $email = $request->email;
            $check_email_exists = User::where('email', 'LIKE', $email.'%')->first();
            if($check_email_exists)
            {
                return $this->sendError('Email already exists.', array());
            }
            else
            {
                return $this->sendResponse(array(), 'Email is new.'); 
            }
        }
    }
    
    
    
    /*==============================================
                GET APP BACKGROUND
    ==============================================*/
    
    public function get_app_background()
    {
        $background = DB::table('app_backgrounds')->limit(1)->pluck('file')->first();
        $array['path'] = 'images/background_images/';
        $array['background'] = !empty($background) ? $background : NULL;
        return $this->sendResponse($array, 'Background');
    }
    
    

    /*==============================================
                TERMS CONDITIONS
    ==============================================*/

    public function terms_conditions()
    {
        $array = array(
            'policies' => DB::table('terms')->where('txt_group', 'policies')->orderBy('txt_order', 'ASC')->get(),
            'terms' => DB::table('terms')->where('txt_group', 'terms')->orderBy('txt_order', 'ASC')->get()
        );
        return $this->sendResponse($array, 'Terms and Policies');
    }
    
    /*==============================================
            CREATE OR UPDATE USER SHOP PROFILE
    ==============================================*/
    
    public function create_or_update_user_shop_profile(Request $request)
    {
        $urls = $request->urls;
        $user_id = $request->user_id;
        $shipping_cost = $request->shipping_cost;
        $shop_name = $request->shop_name;
        $address_1 = $request->address_1;
        $city = $request->city;
        $country = $request->country;
        $requestData = $request->except(['urls', 'shipping_cost']);
        
        $get_shop_detail = DB::table('shop_profiles')->where('user_id', $user_id)->first();

        if (!empty($request['shop_image_url'])) {
            if ($request->hasFile('shop_image_url')) {
                $ssProfileImage = $request->file('shop_image_url');
                $shop_image = 'https://artfora.net/images/shop_image/'.time().str_replace(' ', '', $ssProfileImage->getClientOriginalName());
                $destinationPath1 = public_path('/images/shop_image/');
                $ssProfileImage->move($destinationPath1, $shop_image);
                $requestData['shop_image_url'] = $shop_image;
            }
        }
        
        $requestData['updated_at'] = Carbon::now();
        
        if(!empty($get_shop_detail))
        {
            DB::table('shop_profiles')->where('user_id', $user_id)->update($requestData);
        }
        else
        {
            $requestData['created_at'] = Carbon::now();
            DB::table('shop_profiles')->insert($requestData);
        }
    
        
        // ADD USER URLS
        if($request->has('urls'))
        {
            DB::table('user_urls')->where([['user_id', $user_id], ['type', 'shop']])->delete();
            array_multisort(array_column($urls, 'priority'), SORT_ASC, $urls);
            $urls = collect($urls)->map(function ($item, $key) use ($user_id) {
                $item['user_id'] = $user_id;
                return $item;
            });
            $urls = $urls->map(function ($item) {
                return array_only($item, ['url', 'type', 'user_id']);
            });
            $urls = $urls->toArray();
            DB::table('user_urls')->insert($urls);
        }
        // ADD USER URLS
        
        
        // ADD USER SHOP SHIPPING COSTS
        if($request->has('shipping_cost'))
        {
            $variables = array('user_id' => $user_id, 'type' => $requestData['shipping_type']);
            
            DB::table('shop_shipping_costs')->where('user_id', $user_id)->delete();
            if($requestData['shipping_type'] == 0)
            {
                $shipping_cost = array_map(function($shipping_cost) use ($variables) {
                    return array(
                        'user_id' => $variables['user_id'],
                        'title' => $shipping_cost['title'],
                        'cost' => $shipping_cost['cost'],
                        'type' => $variables['type'],
                        'items' => $shipping_cost['unit'],
                    );
                }, $shipping_cost);
            }
            else
            {
                $shipping_cost = array_map(function($shipping_cost) use ($variables) {
                    $weights = explode('-', $shipping_cost['unit']);
                    return array(
                        'user_id' => $variables['user_id'],
                        'title' => $shipping_cost['title'],
                        'cost' => $shipping_cost['cost'],
                        'type' => $variables['type'],
                        'weight_start' => $weights[0],
                        'weight_end' => isset($weights[1]) ? $weights[1] : NULL,
                    );
                }, $shipping_cost);
            }
            
            DB::table('shop_shipping_costs')->insert($shipping_cost);
        }
        // ADD USER SHOP SHIPPING COSTS
        
        
        $shop_details = DB::table('shop_profiles')->where('user_id', $user_id)->first();
        $shop_details->urls = DB::table('user_urls')->where([['user_id', $user_id], ['type', 'shop']])->get(); 
        return response()->json(["code" => 200, "message" => "User Shop Profile", "data" => $shop_details]);
    }

    /*==============================================
            UPDATE GENERAL PROFILE
    ==============================================*/

    public function update_general_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
        else 
        {
            $user_id = $request->user_id;
            $filter = $request->filter;
            $category = $request->category;
            $verify_email = $request->verify_email;
			
			if(!empty($verify_email)){
				DB::table('users')->where('id', $user_id)->update([
					'verify_email' => $verify_email,
					'verify_email_flag' => 0
				]);
				
				$ssUsrl = url('update-email-veryfied?id=' . base64_encode($user_id));
				$to_email = $verify_email;
				$subject = 'ARTfora email verification';
				$message['to_email'] = $to_email;
				$message['ssUsrl'] = $ssUsrl;
				$message['view'] = "verify_mail";
							
				$this->custom_email($to_email, $from=NULL, $subject, $message);
			}

            DB::table('user_detail')->where('user_id', $user_id)->update([
                'filter' => $filter,
                'category' => $category
            ]);
			
			
            DB::table('interest_group')->where('user_id', $user_id)->delete();
            if (!empty($request['interest_group'])) {
                $saExpload = explode('.', $request['interest_group']);
                foreach ($saExpload AS $saInterest) {
                    DB::table('interest_group')->insert(
                            ['user_id' => $user_id, 'interest_name' => trim($saInterest)]
                    );
                }
            }
        
            $notification_data = array('tag_notification' => $request->tag_notification,
                    'message_notification' => $request->message_notification,
                    'follow_notification' => $request->follow_notification,
                    'unfollow_notification' => $request->unfollow_notification,
                    'like_post_notification' => $request->like_post_notification,
                    'comment_post_notification' => $request->comment_post_notification,
                    'private_profile' => $request->private_profile,
                    'commission' => $request->commission,
                    'city' => $request->city,
                    'country' => $request->country
            );
                    
            $update_notification_user = User::where('id', $user_id)->update($notification_data);
            return $this->sendResponse(array(), 'Settings are updated.');
        }
    }
    
    
    
    /*==============================================
            CUSTOM EMAIL
    ==============================================*/

    public function custom_email($to, $from, $subject, $message)
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
        //mail($to, $subject, $html, $headers);   
		$data = array('email' => $to, 'subject' => $subject, 'message' => $message);
		\Mail::to($to)->send(new SendMail($data));
    }


    
}
