<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;

use Illuminate\Http\Request;
use Validator;
use App\Conversation;
use App\Message;
use App\Contact;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\User;
use App\ShopProduct;
use App\UserDetail;
use Carbon\Carbon;

date_default_timezone_set("Asia/Karachi");


class MessageController extends BaseController
{   
    
    function send_push($title, $body, $tokens, $user_id, $con_id, $total,$u_name,$u_img) {
    
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


        $data2=array(
            'user_id'=>$user_id,
            'con_id' => $con_id,
            'total' => $total,
            'name' => $u_name,
            'image' => $u_img
        );

        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array(
            'registration_ids' => $tokens,
            'notification' => $data,
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
        // return $response;
        //Close request
        curl_close($ch);
//      return $response;

        // echo $response;

    }
    /*==============================================
              GET CONVERSATION USER LIST
    ==============================================*/
    
    public function get_conversation_user_list(Request $request)
    {
        $user = $request->user_id;
        $timezone = $request->local_timezone??"UTC";

        $list_of_conversations_ids = DB::select("SELECT conversation_id FROM `messages` where receiver_id = $user AND read_bit = 1 GROUP BY conversation_id ");
        
        $contacts = DB::table('conversations')
        ->join('users', 'conversations.sender_id', '=', 'users.id')
        ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
        ->join('users as users_1', 'conversations.receiver_id', '=', 'users_1.id')
        ->leftjoin('user_detail as user_detail1', 'users_1.id', '=', 'user_detail1.user_id')
        ->select('conversations.id as cnv_id',
        'users.name as s_name','users.id as s_id', 'users.email as s_email', 'users.tagname as s_tagname',
        'users_1.name as r_name','users_1.id as r_id', 'users_1.email as r_email','users_1.tagname as r_tagname',
        'conversations.id as conversation_id', 'conversations.last_message', 'conversations.updated_at', 'user_detail.profile_image_url as s_image', 'user_detail1.profile_image_url as r_image')
        ->whereRaw(' (conversations.sender_id='.$user.' OR conversations.receiver_id='.$user.') AND (conversations.status != '.$user.' AND conversations.status != 2) ')
        ->orderBy('conversations.updated_at', 'desc')
        ->get();

		$contactids = [];
		$contact_ids = DB::select("SELECT contact_id FROM `contacts` where user_id = $user ");
		foreach($contact_ids as $value){
			$contactids[] = $value->contact_id;
		}
		
        $final = array();
        for($i=0; $i<count($contacts); $i++ ){
			
            //$date = Carbon::createFromTimeStamp(strtotime($date->setTimezone($timezone)))->diffForHumans();
			$date = Carbon::createFromFormat('Y-m-d H:i:s', $contacts[$i]->updated_at, 'UTC');
			$localdate = $date->setTimezone($timezone)->diffForHumans();

            $total_messages = Message::where('conversation_id', $contacts[$i]->conversation_id)->where('delete_status','!=',$user)->count();
            
            if($contacts[$i]->s_id == $user){
                $final[$i]['id'] = $contacts[$i]->r_id;
                $final[$i]['name'] = $contacts[$i]->r_name;
                $final[$i]['email'] = $contacts[$i]->r_email;
                $final[$i]['tagname'] = $contacts[$i]->r_tagname;
                $final[$i]['last_message'] = $contacts[$i]->last_message;
                $final[$i]['conversation_id'] = $contacts[$i]->conversation_id;
                $final[$i]['updated_at'] = $contacts[$i]->updated_at;
                $final[$i]['conversation_updated_at'] = $localdate;
                $final[$i]['profile_image_url'] = $contacts[$i]->r_image;
                $final[$i]['total_messages'] = $total_messages;
                $final[$i]['is_contact'] = false;
				if(in_array($contacts[$i]->r_id,$contactids)){
					$final[$i]['is_contact'] = true;	
				}
            }
            else if($contacts[$i]->r_id == $user){
                $final[$i]['id'] = $contacts[$i]->s_id;
                $final[$i]['name'] = $contacts[$i]->s_name;
                $final[$i]['email'] = $contacts[$i]->s_email;
                $final[$i]['tagname'] = $contacts[$i]->s_tagname;
                $final[$i]['last_message'] = $contacts[$i]->last_message;
                $final[$i]['conversation_id'] = $contacts[$i]->conversation_id;
                $final[$i]['updated_at'] = $contacts[$i]->updated_at;
                $final[$i]['conversation_updated_at'] = $localdate;
                $final[$i]['profile_image_url'] = $contacts[$i]->s_image;
                $final[$i]['total_messages'] = $total_messages;
				$final[$i]['is_contact'] = false;
				if(in_array($contacts[$i]->s_id,$contactids)){
					$final[$i]['is_contact'] = true;	
				}
            }
            else{

            }
        }
        return response()->json(['code' => 200, 'message' => 'Conversation List', 'data' => $final,'list_of_conversations_ids' => $list_of_conversations_ids]);
    }
	
	public function get_conversation_user_search(Request $request)
    {
        $user = $request->user_id;
        $search_name = $request->search_name;
        $timezone = $request->local_timezone??"UTC";

		$followers = [];
		if(!empty($search_name)){
			$followers = DB::table('users')
			->select('users.name as s_name','users.id as s_id', 'users.email as s_email', 'users.tagname as s_tagname','f1.id as fid','f1.updated_at','user_detail.profile_image_url as s_image')
			->leftjoin('followers as f1', 'users.id', '=', 'f1.follewers_id')
			->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
			->where('users.name', 'like', '%' . $search_name . '%')
			->groupBy('users.id')
			->orderBy('f1.id', 'DESC')
			->get();
		}
		
        $list_of_conversations_ids = DB::select("SELECT conversation_id FROM `messages` where receiver_id = $user AND read_bit = 1 GROUP BY conversation_id ");
        
		if(!empty($search_name)){
			$contacts = DB::table('conversations')
			->join('users', 'conversations.sender_id', '=', 'users.id')
			->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
			->join('users as users_1', 'conversations.receiver_id', '=', 'users_1.id')
			->leftjoin('user_detail as user_detail1', 'users_1.id', '=', 'user_detail1.user_id')
			->select('conversations.id as cnv_id',
			'users.name as s_name','users.id as s_id', 'users.email as s_email', 'users.tagname as s_tagname',
			'users_1.name as r_name','users_1.id as r_id', 'users_1.email as r_email','users_1.tagname as r_tagname',
			'conversations.id as conversation_id', 'conversations.last_message', 'conversations.updated_at', 'user_detail.profile_image_url as s_image', 'user_detail1.profile_image_url as r_image')
			->whereRaw(' (conversations.sender_id='.$user.' OR conversations.receiver_id='.$user.') AND (conversations.status != '.$user.' AND conversations.status != 2) ')
			->where('users.name', 'like', '%' . $search_name . '%')
			->orderBy('conversations.updated_at', 'desc')
			->get();
		} else{
			$contacts = DB::table('conversations')
			->join('users', 'conversations.sender_id', '=', 'users.id')
			->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
			->join('users as users_1', 'conversations.receiver_id', '=', 'users_1.id')
			->leftjoin('user_detail as user_detail1', 'users_1.id', '=', 'user_detail1.user_id')
			->select('conversations.id as cnv_id',
			'users.name as s_name','users.id as s_id', 'users.email as s_email', 'users.tagname as s_tagname',
			'users_1.name as r_name','users_1.id as r_id', 'users_1.email as r_email','users_1.tagname as r_tagname',
			'conversations.id as conversation_id', 'conversations.last_message', 'conversations.updated_at', 'user_detail.profile_image_url as s_image', 'user_detail1.profile_image_url as r_image')
			->whereRaw(' (conversations.sender_id='.$user.' OR conversations.receiver_id='.$user.') AND (conversations.status != '.$user.' AND conversations.status != 2) ')
			->orderBy('conversations.updated_at', 'desc')
			->get();
		}
		
		$contactids = [];
		$contact_ids = DB::select("SELECT contact_id FROM `contacts` where user_id = $user ");
		foreach($contact_ids as $value){
			$contactids[] = $value->contact_id;
		}
		
        $final = $ffinal = array();
		$existids = [];
        for($i=0; $i<count($contacts); $i++){
			
            //$date = Carbon::createFromTimeStamp(strtotime($date->setTimezone($timezone)))->diffForHumans();
			$date = Carbon::createFromFormat('Y-m-d H:i:s', $contacts[$i]->updated_at, 'UTC');
			$localdate = $date->setTimezone($timezone)->diffForHumans();

            $total_messages = Message::where('conversation_id', $contacts[$i]->conversation_id)->where('delete_status','!=',$user)->count();
            
            if($contacts[$i]->s_id == $user){
				$existids[] = $contacts[$i]->r_id;
                $final[$i]['id'] = $contacts[$i]->r_id;
                $final[$i]['name'] = $contacts[$i]->r_name;
                $final[$i]['email'] = $contacts[$i]->r_email;
                $final[$i]['tagname'] = $contacts[$i]->r_tagname;
                $final[$i]['last_message'] = $contacts[$i]->last_message??"";
                $final[$i]['conversation_id'] = $contacts[$i]->conversation_id??0;
                $final[$i]['updated_at'] = $contacts[$i]->updated_at;
                $final[$i]['conversation_updated_at'] = $localdate;
                $final[$i]['profile_image_url'] = $contacts[$i]->r_image??"";
                $final[$i]['total_messages'] = $total_messages;
                $final[$i]['is_contact'] = false;
				if(in_array($contacts[$i]->r_id,$contactids)){
					$final[$i]['is_contact'] = true;	
				}
            }
            else if($contacts[$i]->r_id == $user){
				$existids[] = $contacts[$i]->s_id;
                $final[$i]['id'] = $contacts[$i]->s_id;
                $final[$i]['name'] = $contacts[$i]->s_name;
                $final[$i]['email'] = $contacts[$i]->s_email;
                $final[$i]['tagname'] = $contacts[$i]->s_tagname??"";
                $final[$i]['last_message'] = $contacts[$i]->last_message??"";
                $final[$i]['conversation_id'] = $contacts[$i]->conversation_id??0;
                $final[$i]['updated_at'] = $contacts[$i]->updated_at;
                $final[$i]['conversation_updated_at'] = $localdate;
                $final[$i]['profile_image_url'] = $contacts[$i]->s_image??"";
                $final[$i]['total_messages'] = $total_messages;
				$final[$i]['is_contact'] = false;
				if(in_array($contacts[$i]->s_id,$contactids)){
					$final[$i]['is_contact'] = true;	
				}
            }
            else{

            }
			
			
        }
		
		for($i=0; $i<count($followers); $i++){
				if(!in_array($followers[$i]->s_id,$existids)){
					$localdate = "";
					if(!empty($contacts[$i]->updated_at)){
						$date = Carbon::createFromFormat('Y-m-d H:i:s', $contacts[$i]->updated_at??date('Y-m-d H:i:s'), 'UTC');
						$localdate = $date->setTimezone($timezone)->diffForHumans();
					}
					$total_messages = 0;
					$ffinal[$i]['id'] = $followers[$i]->s_id;
					$ffinal[$i]['name'] = $followers[$i]->s_name;
					$ffinal[$i]['email'] = $followers[$i]->s_email;
					$ffinal[$i]['tagname'] = $followers[$i]->s_tagname;
					$ffinal[$i]['last_message'] = "";
					$ffinal[$i]['conversation_id'] = 0;
					$ffinal[$i]['updated_at'] = $followers[$i]->updated_at;
					$ffinal[$i]['conversation_updated_at'] = $localdate;
					$ffinal[$i]['profile_image_url'] = $followers[$i]->s_image??"";
					$ffinal[$i]['total_messages'] = $total_messages;
					$ffinal[$i]['is_contact'] = false;                
					if(in_array($followers[$i]->s_id,$contactids)){
						$ffinal[$i]['is_contact'] = true;	
					}
				}				
        }
		
		$conv = array_merge($final,$ffinal);
		
        return response()->json(['code' => 200, 'message' => 'Search Conversation List', 'data' => $conv,'list_of_conversations_ids' => $list_of_conversations_ids]);
    }
    
    /*==============================================
              EDIT/DELETE USER MESSAGE
    ==============================================*/
    
    public function deleteMessage(Request $request) {
        $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $data = array();
            $messageId = $request['id'];
            $userId    = $request['user_id'];
//            $result = DB::table('messages')->where('id',$messageId)->where('sender_id',$userId)->update(['isRemoved'=>1]);
            
            $result = DB::table('messages')->where('id',$messageId)->where('sender_id',$userId)->delete();
            if($result > 0){
                $data["message"] = "success";
                return $this->sendResponse($data, 'Message removed');
            } else{
                return $this->sendResponse([], 'Message doesn\'t exist');
            }
        }
    }
    
    public function editMessage(Request $request) {
        $validator = Validator::make($request->all(), [
                                     'id' => 'required',
                                     'user_id' => 'required',
                                     'message' => 'required',
                                     ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            $data = array();
            $messageId = $request['id'];
            $userId    = $request['user_id'];
            $message   = $request['message'];
            $result = DB::table('messages')->where( ['id' => $messageId , 'sender_id' => $userId] )->update(['message' => $message]);
            if($result > 0){
                $data["message"] = "success";
                return $this->sendResponse($data, 'Message updated');
            } else{
                return $this->sendResponse([], 'Message doesn\'t exist');
            }
        }
    }
    
	public function add_contact(Request $request)
    {
		$user_id = $request->user_id;
        $contact_id = $request->contact_id;
		$where['user_id'] = $user_id;
		$where['contact_id'] = $contact_id;
		if(!$contact = Contact::where($where)->first()){
			$contact = Contact::create(['user_id' => $user_id, 'contact_id' => $contact_id]);
			$message = 'Contact added';
		} else{
			$message = 'Contact already added';
		}
		return response()->json(['code' => 200, 'message' => $message, 'data' => $contact]);
	}
    
	
	public function delete_contact(Request $request)
    {
		$user_id = $request->user_id;
        $contact_id = $request->contact_id;
		
		$where['user_id'] = $user_id;
		$where['contact_id'] = $contact_id;
		$contact = Contact::where($where)->delete();
		return response()->json(['code' => 200, 'message' => 'Contact Removed', 'data' => $contact]);
	}
    
    
	public function get_user_profile(Request $request)
    {
        $user_id = $request->user_id;
        $profile_image_url = "";
        $check_conversation = Conversation::whereRaw('(sender_id='.$user_id.' ) OR (receiver_id='.$user_id.')')->orderBy('id','DESC')->first();

        if($check_conversation)
		{
            $con_id = $check_conversation->id;  
			if($check_conversation->sender_id == $user_id){
				$profileid  = $check_conversation->receiver_id;
			}
			else if($check_conversation->receiver_id == $user_id){
				$profileid  = $check_conversation->sender_id;
			} else{
				
			}
			$userdetail = UserDetail::where('user_id',$profileid)->select('profile_image_url')->first();
			$profile_image_url = $userdetail->profile_image_url;
        }
        return response()->json(['code' => 200, 'message' => 'get profile success', 'profile_image_url' => $profile_image_url]);
    }
	
    /*==============================================
                    SEND MESSAGE
    ==============================================*/
    
	
	
	public function get_conversation_id(Request $request)
    {
        $sender_id = $request->sender_id;
        $receiver_id = $request->receiver_id;
		
        $check_conversation = Conversation::whereRaw('(sender_id='.$sender_id.' AND receiver_id='.$receiver_id.') OR (sender_id='.$receiver_id.' AND receiver_id='.$sender_id.')')->first();

        if($check_conversation)
		{
            $con_id = $check_conversation->id;   
        }
        else
		{            
            $get_latest_coversation = Conversation::create(['sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'last_message' => "No Message"]);
            $con_id = $get_latest_coversation->id;
        }
        
        return response()->json(['code' => 200, 'message' => 'Conversation Created', 'conversation_id' => $con_id]);

    }
	
    public function send_message(Request $request)
    {
        $sender_id = $request->sender_id;
        $receiver_id = $request->receiver_id;
        $story_user_id = $request->story_user_id??0;
        $story_id = $request->story_id??0;
        $text = $request->message;
        $not_approved = $request->not_approved??0;
        $isPriceRequest = $request->isPriceRequest??0;        
		if($request->isRemoved){
			$receiver_id = 4;
		}
		
		
        // $check_conversation = Conversation::whereRaw([['sender_id', $sender_id], ['receiver_id', $receiver_id]])->orWhere([['sender_id', $receiver_id], ['receiver_id', $sender_id]])->first();

        $check_conversation = Conversation::whereRaw('(sender_id='.$sender_id.' AND receiver_id='.$receiver_id.') OR (sender_id='.$receiver_id.' AND receiver_id='.$sender_id.')')->first();

        if($check_conversation){

            $con_id = $check_conversation->id;
 
            if($request->file_type=='story' || $request->file_type=='post' || $request->file_type=='shop_product' || $request->file_type=='shop')
            {
				
                $message=Message::create([
                    'sender_id'         => $sender_id,
                    'thumbnail'         => $request->thumbnail,
                    'isRemoved'         => $request->isRemoved??0,
                    'file_type'         => $request->file_type,
                    'receiver_id'       => $receiver_id,
                    'conversation_id'   => $check_conversation->id,
                    'story_user_id'     => $story_user_id,
                    'story_id'          => $story_id,
                    'not_approved'      => $not_approved,
                    'isPriceRequest'      => $isPriceRequest,
                ]);

                $check_conversation->last_message = $request->file_type;
                $check_conversation->status = 0;
                $check_conversation->save();
            }
            else
            {
                if(request()->has('file')){

                    $file = $request->file('file');
                    if(request()->has('thumbnail'))
                    {
                        $thumbnail = $request->file('thumbnail');
                        $thumbnail_original_name = $thumbnail->getClientOriginalName();
                        $thumbnailname = time(). '.' .$thumbnail->getClientOriginalName();
                        $thumbnailname = str_replace(' ', '', $thumbnailname);
                        $location = app()->basePath('public/images/chat_files/thumbnail/');
                        $thumbnail->move($location, $thumbnailname);    
                        $thumbnailname = 'public/images/chat_files/thumbnail/'.$thumbnailname;
                    }
                    else
                    {
                        $thumbnailname = "";
                    }
                    
                    $file_original_name = $file->getClientOriginalName();
                    $filename = time(). '.' .$file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);
                    $location = app()->basePath('public/images/chat_files/');
                    $file->move($location, $filename);
                            
                    $file_type = $request->file_type;
                    $file_size = $request->file_size;
                            
                    $message=Message::create([
                        'sender_id'         => $sender_id,
                        'file'              => $file_original_name,
                        'file_url'          => 'public/images/chat_files/'.$filename,
                        'thumbnail'         => $thumbnailname,
                        'file_type'         => $file_type,
                        'file_size'         => $file_size,
                        'receiver_id'       => $receiver_id,
                        'conversation_id'   => $check_conversation->id,
						'isRemoved'         => $request->isRemoved??0,
						'not_approved'      => $not_approved,
						'isPriceRequest'      => $isPriceRequest,
                    ]);
                    $check_conversation->last_message = "File";
                    $check_conversation->status = 0;
                    $check_conversation->save();
                }
                else{
                    //echo $con_id;die;

                    $message=Message::create(['message' => $text, 'sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'conversation_id' => $con_id,'isRemoved'=> $request->isRemoved??0,'not_approved' => $not_approved]);
                    $check_conversation->last_message = $text;
                    $check_conversation->status = 0;
                    $check_conversation->save();
                }
            }
        }
        else
		{
            if(request()->has('file')){
                $text = 'File';
            }
            if($request->file_type=='story'){
                $text='story';
            }
            if($request->file_type=='post'){
                $text='post';
            }
			if($request->file_type=='shop'){
                $text='shop';
            }
            if($request->file_type=='shop_product'){
                $text='shop_product';
            }
            Conversation::create(['sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'last_message' => $text]);
            $get_latest_coversation = Conversation::orderBy('id', 'DESC')->first();

            if($request->file_type=='story' || $request->file_type=='post' || $request->file_type=='shop_product' || $request->file_type=='shop'){
                $message = Message::create([
                    'sender_id'         => $sender_id,
                    'thumbnail'         => $request->thumbnail,
                    'file_type'         => $request->file_type,
                    'receiver_id'       => $receiver_id,
                    'conversation_id'   => $get_latest_coversation->id,
                    'story_user_id'     => $story_user_id,
                    'story_id'          => $story_id,
					'isRemoved'         => $request->isRemoved??0,
					'not_approved'      => $not_approved,
					'isPriceRequest'      => $isPriceRequest,
                ]);
            }
            else{
                if(request()->has('file')){
                    $file = $request->file('file');
                    $file_original_name = $file->getClientOriginalName();
                    $filename = time(). '.' .$file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);
                    $location = app()->basePath('public/images/chat_files/');
                    $file->move($location, $filename);
                    
                    if(request()->has('thumbnail'))
                    {
                        $thumbnail = $request->file('thumbnail');
                        $thumbnail_original_name = $thumbnail->getClientOriginalName();
                        $thumbnailname = time(). '.' .$thumbnail->getClientOriginalName();
                        $thumbnailname = str_replace(' ', '', $thumbnailname);
                        $location = app()->basePath('public/images/chat_files/thumbnail/');
                        $thumbnail->move($location, $thumbnailname);    
                        $thumbnailname = 'public/images/chat_files/thumbnail/'.$thumbnailname;
                    }
                    else
                    {
                        $thumbnailname = "";
                    }

                    $file_type = $request->file_type;
                    $file_size = $request->file_size;
                    
                    $message=Message::create([
                        'sender_id' => $sender_id,
                        'file' => $file_original_name,
                        'file_url' => 'public/images/chat_files/'.$filename,
                        'thumbnail' => $thumbnailname,
                        'file_type' => $file_type,
                        'file_size' => $file_size,
                        'receiver_id' => $receiver_id,
                        'conversation_id' => $get_latest_coversation->id,
                        'isRemoved' => $request->isRemoved??0,
						'not_approved' => $not_approved,
						'isPriceRequest'      => $isPriceRequest,
                    ]);
                }
                else{
                    $message=Message::create(['message' => $text, 'sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'conversation_id' => $get_latest_coversation->id,'isRemoved'=> $request->isRemoved??0,'not_approved' => $not_approved]);
                }
            }

            $con_id = $get_latest_coversation->id;
        }
        
        $count = DB::select("SELECT count(*) AS count FROM `messages` where conversation_id = $con_id AND (delete_status != 2) "); 
        if($count)
        {
            $total = $count[0]->count;
        }
        else
        {
            $total = 0;
        }

        $user_token=DB::table('users')->where('id',$receiver_id)->first();

        $u_d=DB::table('users')->where('id',$sender_id)->first();
        $u_d_img=DB::table('user_detail')->where('user_id',$sender_id)->first();
        // echo $u_d_img->profile_image_url;
        // die;
        
        // print_r($u_d);
        // die;
        if($user_token->message_notification == 1)
        {
            // echo "yes";
            $app_token=array();
            $title='New Message';
            $body='You have New Message';
            $app_token[]=$user_token->app_token;
            echo  $this->send_push($title, $body, $app_token, $sender_id, $con_id, $total,$u_d->name,$u_d_img->profile_image_url);
        }
        else
        {
            // echo "not";
        }
        
         $user = $receiver_id;
         $contacts = DB::table('conversations')
        ->join('users', 'conversations.sender_id', '=', 'users.id')
        ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
        ->join('users as users_1', 'conversations.receiver_id', '=', 'users_1.id')
        ->leftjoin('user_detail as user_detail1', 'users_1.id', '=', 'user_detail1.user_id')
        ->select('conversations.id as cnv_id',
        'users.name as s_name','users.id as s_id', 'users.email as s_email',
        'users_1.name as r_name','users_1.id as r_id', 'users_1.email as r_email',
        'conversations.id as conversation_id', 'conversations.last_message', 'conversations.updated_at', 'user_detail.profile_image_url as s_image', 'user_detail1.profile_image_url as r_image')
        ->whereRaw(' (conversations.sender_id='.$user.' OR conversations.receiver_id='.$user.') AND (conversations.status != '.$user.' AND conversations.status != 2) ')
        ->orderBy('conversations.updated_at', 'desc')
        ->first();
        $final = array();

        // print_r($contacts->updated_at);
        // die;
       
       if($contacts)
       {
            $dt = $contacts->updated_at;
       }
       else
       {
            $dt = date('Y-m-d H:i:s');
       }
            
            $date = Carbon::createFromTimeStamp(strtotime($dt))->diffForHumans();
            // die;
            
            $total_messages = Message::where('conversation_id', $contacts->conversation_id)->where('delete_status','!=',$sender_id)->count();
            
            if($contacts->s_id == $user){
                $final['id'] = $contacts->r_id;
                $final['name'] = $contacts->r_name;
                $final['email'] = $contacts->r_email;
                $final['last_message'] = $contacts->last_message;
                $final['conversation_id'] = $contacts->conversation_id;
                $final['conversation_updated_at'] = $date;
                $final['profile_image_url'] = $contacts->r_image;
                $final['total_messages'] = $total_messages;
            }
            else if($contacts->r_id == $user){
                $final['id'] = $contacts->s_id;
                $final['name'] = $contacts->s_name;
                $final['email'] = $contacts->s_email;
                $final['last_message'] = $contacts->last_message;
                $final['conversation_id'] = $contacts->conversation_id;
                $final['conversation_updated_at'] = $date;
                $final['profile_image_url'] = $contacts->s_image;
                $final['total_messages'] = $total_messages;
            }
            // else{

            // }
       
        return response()->json(['code' => 200, 'message' => 'Conversation List', 'data' => $final]);
        
        // return response()->json(['status' => 200, 'message' => 'Message Send Successfully.', 'data' => $message]);

    }
	
	public function send_message_media(Request $request)
    {
		$message = [];
        if(request()->has('file')){
			$file = $request->file('file');
			if(request()->has('thumbnail'))
			{
				$thumbnail = $request->file('thumbnail');
				$thumbnail_original_name = $thumbnail->getClientOriginalName();
				$thumbnailname = time(). '.' .$thumbnail->getClientOriginalName();
				$thumbnailname = str_replace(' ', '', $thumbnailname);
				$location = app()->basePath('public/images/chat_files/thumbnail/');
				$thumbnail->move($location, $thumbnailname);    
				$thumbnailname = 'public/images/chat_files/thumbnail/'.$thumbnailname;
			}
			else
			{
				$thumbnailname = "";
			}
			
			$file_original_name = $file->getClientOriginalName();
			$filename = time(). '.' .$file->getClientOriginalName();
			$filename = str_replace(' ', '', $filename);
			$location = app()->basePath('public/images/chat_files/');
			$file->move($location, $filename);
					
			$file_type = $request->file_type;
			$file_size = $request->file_size;
					
			$message= [				
				'file'=> $file_original_name,
				'file_url'=> 'public/images/chat_files/'.$filename,
				'thumbnail'=> $thumbnailname,
				'file_type'=> $file_type,
				'file_size'=> $file_size,
				'last_message'=> 'File',
				'status'=> 0,
			];
		}
        return response()->json(['code' => 200, 'message' => 'Media Uploaded', 'data' => $message]);
    }


    /*==============================================
                   GET USER MESSAGES
    ==============================================*/
    
    public function get_user_messages(Request $request)
    {
        
        $user_id = $request->user_id;
        // return $user_id;
        $conversation_id = $request->conversation_id;
        $start = $request->start;
        $limit = $request->limit;
        
        // $get_user_messages = Message::where('conversation_id', $conversation_id)->offset($start)->limit($limit)->get();
        // $count = Message::where('conversation_id', $conversation_id)->count();
        
        $get_user_messages = DB::select("SELECT * FROM `messages` where conversation_id = $conversation_id AND (delete_status != 2) AND delete_status != $user_id LIMIT $limit OFFSET $start ");
        // print_r($get_user_messages);
        // die;
        $count = DB::select("SELECT count(*) AS count FROM `messages` where conversation_id = $conversation_id AND (delete_status != 2)  AND delete_status != $user_id ");    
        // $get_user_messages = Message::where([['conversation_id', $conversation_id], ['delete_status', '!=', 2], ['delete_status', '!=', $user_id] ])->offset($start)->limit($limit)->tosql();
        // $count = Message::where('conversation_id', $conversation_id)->count();
        
        // TO GET STATUS IF OTHER USER IS BLOCKED BY ADMIN OR BY THE CURRENT USER BLOCKED OTHER USER OR OTHER USER BLOCKED CURRENT USER
        $get_conversation = Conversation::find($conversation_id);
        $other_user_id = ($get_conversation->sender_id == $user_id) ? $get_conversation->receiver_id : $get_conversation->sender_id;
        $other_user = User::find($other_user_id);
        
        // Other user is blocked by admin
        if($other_user->status == 2)
        {
            $status = 1;
        }
        else
        {
            // You have blocked the other user
            $blocked_user_1 = DB::table('blocked_users')->where([['user_id', $user_id], ['blocked_userid', $other_user_id]])->first();
            if($blocked_user_1)
            {
                $status = 2;
            }
            else
            {   
                // Other user has blocked you
                $blocked_user_2 = DB::table('blocked_users')->where([['user_id', $other_user_id], ['blocked_userid', $user_id]])->first();
                if($blocked_user_2)
                {
                    $status = 3;
                }
                else
                {
                    $status = 0;
                }
            }
        }
        
        return response()->json(['status' => 200, 'message' => 'User Messages List.', 'data' => $get_user_messages, 'count' => $count[0]->count, 'status' => $status,'conversation_id' => $conversation_id]);
        
    }
    
    
    /*==============================================
                   READ BIT UPDATE
    ==============================================*/
    
    public function read_bit_messages(Request $request)
    {
        
        $receiver_id = $request->receiver_id;
        $conversation_id = $request->conversation_id;
        
        $get_user_messages = Message::where([['conversation_id', $conversation_id], ['receiver_id', $receiver_id]])->update(['read_bit' => 0]);
        
        return response()->json(['status' => 200, 'message' => 'Messages have read.']);
        
    }
    
    
    
    /*==============================================
                SEARCH CONVERSATION USER
    ==============================================*/
    
    public function search_conversation_user(Request $request)
    {
        
        $search = $request->search;
        $user_id = $request->user_id;
        
        $get_users = DB::table('users')
                        ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                        ->where([['users.name', 'LIKE', '%'.$search.'%']])
                        // ->orWhere('users.email', 'LIKE', '%'.$search.'%')
                        ->where('users.deleted_at', NULL)
                        ->where('users.id', '!=' ,$user_id)
                        ->select('users.tagname','users.id', 'users.name', 'users.email', 'user_detail.profile_image_url')->get();
        
        $get_users_ids = $get_users->pluck('id')->toArray();
        
        $get_conversation_users = DB::table('messages')
                                            ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
                                            ->join('users', 'users.id', '=', 'messages.receiver_id')
                                            ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                                            ->whereIN('messages.receiver_id', $get_users_ids)
                                            ->where('messages.sender_id', $user_id)->where('delete_status','!=',$user_id)
                                            ->select('users.tagname','users.id', 'users.name', 'users.email', 'conversations.last_message', 'conversations.id as conversation_id', 'conversations.updated_at as conversation_updated_at', 'user_detail.profile_image_url')
                                            ->groupBy('messages.conversation_id')
                                            ->orderBy('conversations.updated_at', 'DESC')
                                            ->get();
        
        // $total_messages = Message::where('conversation_id', $contacts[$i]->conversation_id)->count();
        
        $get_conversation_sender_users = DB::table('messages')
                                        ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
                                        ->join('users', 'users.id', '=', 'messages.sender_id')
                                        ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                                        ->whereIN('messages.sender_id', $get_users_ids)
                                        ->where('messages.receiver_id', $user_id)->where('delete_status','!=',$user_id)
                                        ->select('users.tagname','users.id', 'users.name', 'users.email', 'conversations.last_message', 'conversations.id as conversation_id', 'conversations.updated_at as conversation_updated_at', 'user_detail.profile_image_url')
                                        ->groupBy('messages.conversation_id')
                                        ->orderBy('conversations.updated_at', 'DESC')
                                        ->get();
        
        
        $get_conversation = $get_conversation_users->merge($get_conversation_sender_users);
        
        $get_conversation = $get_conversation->sortByDesc(function($post){
                                                                  return $post->conversation_updated_at;
                                                                });
        
        $get_conversation = collect($get_conversation)->map(function ($item, $key) {
            $item->conversation_updated_at = Carbon::createFromTimeStamp(strtotime($item->conversation_updated_at))->diffForHumans();
            
            return $item;
        })->unique('conversation_id')->values();
        // echo count($get_conversation);
        // die;
        for($con=0; $con < count($get_conversation); $con++)
        {
            $conversation_id = $get_conversation[$con]->conversation_id;
            $total_messages = Message::where('conversation_id', $conversation_id)->where('delete_status','!=',$user_id)->count();
            $get_conversation[$con]->total_messages = $total_messages;
        }
        
        // return $total_messages;
        // die;
    //   $new = collect();
    //     $get_users = $get_users->map(function($contact) use ($get_conversation_users, $new) {
    //         $single_agent = $get_conversation_users->where('id', $contact->id)->first();
    //         if($single_agent){
    //             return collect($new)->merge($single_agent);
    //         }
    //     });
        
    //     return $get_conversation_users->merge($new);
        
         $get_users = collect($get_users)->map(function ($item, $key) {
            $item->last_message = NULL;
            $item->conversation_id = NULL;
            $item->updated_at = NULL;
            // $item->profile_image_url = NULL;
            $item->total_messages = NULL;
            
            return $item;
        });
        
         $get_conversation_data = $get_conversation->merge($get_users);
        
        
        // $data = array_values($get_conversation_data);
         $get_unique_conversation = $get_conversation_data->unique('id')->values();
        // $spg = array_slice($get_conversation->values()->all(), 0, 5, true);
        // return $spg;
        // die;
        
        return response()->json(['status' => 200, 'message' => 'Messages have read.', 'data' => $get_unique_conversation]);   
    }
    
    public function delete_conversation(Request $request)
    {
        $user_id = $request->user_id;
        $conversation_id = $request->conversation_id;
        
        $get_user_messages = Conversation::where([ ['id', $conversation_id] ])->first();
        // return $get_user_messages;
        // die;
        
        if($get_user_messages)
        {
            $delete_status = $get_user_messages->status;
            if($delete_status == $user_id)
            {
                return response()->json(['status' => 100, 'message' => 'This conversation id already delete .']);
            }
            else
            {
                if($delete_status == 1)
                {
                    $delete_conversation = Conversation::where([['id', $conversation_id]])->update(['status' => $user_id]);
                    $delete_messages = Message::where([['conversation_id', $conversation_id]])->update(['delete_status' => $user_id]);
                }
                elseif($delete_status != 1)
                {
                    $delete_conversation = Conversation::where([['id', $conversation_id]])->update(['status' => 2]);
                    $delete_messages = Message::where([['conversation_id', $conversation_id]])->update(['delete_status' => 2]);
                }
                else
                {
                    $delete_conversation = Conversation::where([['id', $conversation_id]])->update(['status' => $user_id]);
                    $delete_messages = Message::where([['conversation_id', $conversation_id]])->update(['delete_status' => $user_id]);
                }   
                
                return response()->json(['status' => 200, 'message' => 'Messages removed successfully.']);
            }
        }
        else
        {
            return response()->json(['status' => 100, 'message' => 'This conversation id does not save in DB .']);
        }
        
        // $get_user_messages = Message::where([['conversation_id', $conversation_id], ['receiver_id', $receiver_id]])->update(['read_bit' => 0]);   
    }


    public function get_counter_msg_user(Request $request)
    {
        $user_id = $request->user_id;
        $count = DB::select("SELECT count(*) AS count FROM `messages` join conversations on conversations.id=messages.conversation_id where messages.receiver_id = $user_id AND (messages.delete_status != 2)  AND messages.delete_status != $user_id AND messages.read_bit = 1 ");        
        return response()->json(['status' => 200, 'message' => 'Successfully', 'count' => $count[0]->count]);
    }
    
	public function get_feed_shop(Request $request)
	{
		
		$user_id = $request->user_id;
		$stroy_id = $request->stroy_id;
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
							->where('post.id', $stroy_id)
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
			   // if(!$getStatus->isEmpty()){
					
			   //      for ($i = 0; count($getStatus) > $i; $i++) {
			   //          $stData2['tag_list'] = DB::table('post_tags')
			   //                  ->join('users', 'users.id', '=', 'post_tags.user_id')
			   //                  ->join('user_detail', 'user_detail.user_id', '=', 'users.id')
			   //                  ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
			   //                  ->WhereRaw("post_tags.post_id=".$getStatus->id)
			   //                  ->get();
			   //      }
			   //  }        

			/**************************************************************/
			   $saData['tag_list']=$getStatus;



			$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
			$saData['is_saved'] = $is_saved;
			$saData['detail'] = $saGetDetail;
			$saData['likes'] = $saGeLikes;
			$saData['comments'] = $saGeComments;
			$saData['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
			$saData['tags'] = $saGetTag;
			$saData['created_at'] = $get_post->created_at;

			$saData['files'] = DB::table('post_files')->select('file_type','file_name','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();				
			$saData['object_type'] = 1;
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
			->where([['id', $stroy_id]])
			->first();
			//, ['status', 1]

			$get_profile = DB::table('shop_profiles')->where('user_id',  $user_id)->select('name', 'rating')->first();
			$saData['posted_by'] = User::where('id', $user_id)->pluck('full_name')->first();
			$saData['seller_rating'] = $get_profile->rating;
			$saData['object_type'] = 0;
		} else{
			$saData = [];
		}
		return response()->json(['status' => 200, 'message' => 'Get '.$file_type.' Successfully', 'data' => $saData]);
	}
}
