<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Status;
use App\ShopProduct;
use App\StatusView;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\User;
use Intervention\Image\Facades\Image;

class StatusController extends BaseController
{

    
    function send_push($title, $body, $tokens, $user_id, $status_id, $user_name, $user_img) {
    
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
            'status_id' => $status_id,
            'user_name' => $user_name,
            'user_image' => $user_img
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
        
        //Close request
        curl_close($ch);
        return $response;
    }
    
    
    // **********ADD STATUS**************
    public function addStory(Request $request) {

		$validator = Validator::make($request->all(), [
                'user_id' => 'required',
                // 'image' => 'required|mimes:jpeg,jpg,png,gif',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
		else
		{
			$saData = [];
			$saStatus = new Status();
			if(!empty($request['feed_id'])){
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

						$saData['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
						
						foreach ($saData['files'] AS $mykey=>$sFile) {
							if($mykey == 0){
								
								$saStatus->user_id = $user_id;
								$saStatus->type = $sFile->file_type;
								if($sFile->file_type == 1){
									$saStatus->file = $sFile->file_name;
								} else{
									$saStatus->file = $sFile->thumb;
								}
//                                $saStatus->resized_file_name = $sFile->resized_file_name;
								$saStatus->vedio_image = $sFile->thumb;
								$saStatus->swipeUpUrl = 'nil';
                                $saStatus->story_time = $request['story_time'];
								$saStatus->x = $sFile->x;
								$saStatus->y = $sFile->y;
								$saStatus->tag_users = $sFile->tag_users;
								$saStatus->user_ids = $sFile->user_ids;
								$saStatus->share_type = 'post';
								$saStatus->feed_id = $feed_id;
								//$saStatus->save();	
							
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
								
								$saStatus->user_id = $user_id;
								$saStatus->type = $sFile['file_type'];
								if($sFile['file_type'] == 2){
									$saStatus->file = $sFile['thumb'];
								} else{
									$saStatus->file = $sFile['file_name'];
								}
//                                $saStatus->resized_file_name = $sFile['resized_file_name'];
								$saStatus->vedio_image = $sFile['thumb'];
								$saStatus->swipeUpUrl = 'nil';
                                $saStatus->story_time = $request['story_time'];
								$saStatus->x = json_encode([]);
								$saStatus->y = json_encode([]);
								$saStatus->tag_users = json_encode([]);
								$saStatus->user_ids = json_encode([]);
								$saStatus->share_type = 'shop';
								$saStatus->feed_id = $feed_id;
								//$saStatus->save();		
							}						
						}
					}
					
			} 
			
			$user_id =   $request['user_id'];
			$swipeUpUrl = $request['swipeUpUrl'];
            $story_time = $request['story_time'];
			$files = $request['file'];
			$file_count = count($files);

			if (!empty($file_count)) {

			 foreach ($files AS $sFile) {

				if($sFile['type'] == 1){
					
					$vi_file = null;
					
					$image = $sFile['vedio_image'];
					$randomname = time().rand();
					$s_file = $randomname .'.'.$image->getClientOriginalExtension();
				 
					$destinationPath = public_path('/images/status/Image');
					$img = Image::make($image->getRealPath());
					$img->resize(1080, 1080, function ($constraint) {
						$constraint->aspectRatio();
					})->save($destinationPath.'/'.$s_file);
					
					$destinationPath = public_path('/images/status/Image/thumbtab');
					$img2 = Image::make($image->getRealPath());
					$img2->resize(1920, 1920, function ($constraint) {
						$constraint->aspectRatio();
					})->save($destinationPath.'/'.$s_file);
			   
					$x = isset($sFile['x'])?$sFile['x']:'';
					$y = isset($sFile['y'])?$sFile['y']:'';
					$names = isset($sFile['names'])?$sFile['names']:'';
					$ids = isset($sFile['ids'])?$sFile['ids']:'';

				}else{
					$vedioFile = $sFile['file'];
					$s_file = time() . '1' . $vedioFile->getClientOriginalName();
					$destinationPath1 = public_path('/images/status/Video');
					$vedioFile->move($destinationPath1, $s_file);

					$x = isset($sFile['x'])?$sFile['x']:'';
					$y = isset($sFile['y'])?$sFile['y']:'';
					$names = isset($sFile['names'])?$sFile['names']:'';
					$ids = isset($sFile['ids'])?$sFile['ids']:'';
					
					if(!empty($sFile['vedio_image'])){

						//$vedioImageFile =  $sFile['vedio_image'];
						//$vi_file = time() . '1' . $vedioImageFile->getClientOriginalName();
						//$destinationPath1 = public_path('/images/status/VideoImage');
						//$vedioImageFile->move($destinationPath1, $vi_file);
						
						$image = $sFile['vedio_image'];
						$randomname = time().rand();
						$vi_file = $randomname .'.'.$image->getClientOriginalExtension();
					 
						$destinationPath = public_path('/images/status/VideoImage');
						$img = Image::make($image->getRealPath());
						$img->resize(1080, 1080, function ($constraint) {
							$constraint->aspectRatio();
						})->save($destinationPath.'/'.$vi_file);
						
						$destinationPath = public_path('/images/status/VideoImage/thumbtab');
						$img2 = Image::make($image->getRealPath());
						$img2->resize(1920, 1920, function ($constraint) {
							$constraint->aspectRatio();
						})->save($destinationPath.'/'.$vi_file);						

					}else{
						$vi_file = null;
					}
				}

					//$saStatus = new Status();
					$saStatus->user_id = $user_id;
					$saStatus->type = $sFile['type'];
					$saStatus->file = $s_file;
					$saStatus->vedio_image = $vi_file;
					$saStatus->swipeUpUrl = $swipeUpUrl;
                    $saStatus->story_time = $story_time;
					$saStatus->x = $x;
					$saStatus->y = $y;
					$saStatus->tag_users = $names;
					$saStatus->user_ids = $ids;
					$saStatus->feed_id = $request->feed_id??0;
					$saStatus->share_type = $request->file_type??"status";
					
					$saStatus->save();
					$data['story_id'] = $saStatus->id;
					
					if($request['tag_list']){
						$tag_list=$request['tag_list'];
						for($i=0;$i<count($tag_list);$i++){
							$data_array=array(
								'story_id'=>$saStatus->id,
								'user_id'=>$tag_list[$i]
								);
							DB::table('tag_users')->insertGetId($data_array);
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
			// CHECK IF ANY USER FOLLOW CURRENT USER THEN SEND NOTIFICATION TO THEM
			
			 return $this->sendResponse($saData, 'Story Created successfully.');

			}else{
				return $this->sendError("File is empty", array());
			}
		}
    } 
	
	public function shareStory(Request $request) {

		$validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'story_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
		else
		{
			$user_id =   $request['user_id'];
			$story_id =   $request['story_id'];
			
			$status = Status::find($story_id);
			if($status){
				
				$saStatus = new Status();
				$saStatus->user_id = $user_id;
				$saStatus->type = $status->type;
				$saStatus->file = $status->file;
				$saStatus->vedio_image = $status->vedio_image;
				$saStatus->swipeUpUrl = $status->swipeUpUrl;
                $saStatus->story_time = $status->story_time;
				$saStatus->x = $status->x;
				$saStatus->y = $status->y;
				$saStatus->tag_users = $status->tag_users;
				$saStatus->user_ids = $status->user_ids;
				$saStatus->share_type = $status->share_type;
				$saStatus->feed_id = $status->feed_id;
				$saStatus->save();
				$data['story_id'] = $saStatus->id;
				return $this->sendResponse($data, 'Story Shared successfully.');
			} else {
				return $this->sendResponse($data, 'Story Expired.');
			}
		}
	}


    // **********VIEW OWN STORY**************
    // public function viewOwnStory (Request $request) {

    //  $validator = Validator::make($request->all(), [
    //         'user_id' => 'required'
    //     ]);


 //        if ($validator->fails()) {
 //            $error = $validator->messages();
 //            return $this->sendError($error->first(), array());
 //        }else{

 //         $userId = $request->user_id;

 //            $getStatus = Status::where('user_id', $userId)->get();

 //            if(!$getStatus->isEmpty()){

    //             $stData = array();
    //             for ($i = 0; count($getStatus) > $i; $i++) {

 //                    $single_storyTotalViews = StatusView::where('status_id', $getStatus[$i]->id)->count();

    //              $stData[$i]['id'] = $getStatus[$i]->id;
    //              $stData[$i]['user_id'] = $getStatus[$i]->user_id;
 //                    $stData[$i]['story_views'] = $single_storyTotalViews;
    //              $stData[$i]['type'] = $getStatus[$i]->type;

    //              if($getStatus[$i]->type == 1){
    //              $stData[$i]['file'] = 'https://artfora.net/images/status/Image/' .  $getStatus[$i]->file;
    //              }else{
    //              $stData[$i]['file'] = 'https://artfora.net/images/status/Video/' .  $getStatus[$i]->file;

 //                        if($getStatus[$i]->vedio_image == null){

 //                            $stData[$i]['vedio_image'] = null;
 //                        }else{

 //                            $stData[$i]['vedio_image'] = 'https://artfora.net/images/status/VideoImage/' . $getStatus[$i]->vedio_image;
 //                        }



    //              }

    //              $stData[$i]['created_at'] = $getStatus[$i]->created_at;
    //             }
    //             return $this->sendResponse($stData, 'Story List');

 //            }else{
 //             return $this->sendError("User Story Is not Exsit", array());
 //            }
 //        }

    // }


    public function viewOwnStory (Request $request) {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);


        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }else{

            $userId = $request->user_id;
            $userdata = User::join('user_detail','user_detail.user_id','=','users.id')->select('users.name','user_detail.profile_image_url')->where('users.id',$userId)->first();
			$removeStatus = Status::where('user_id', $userId)->where("created_at","<",\Carbon\Carbon::now()->subHours(24))->get();
			if(!empty($removeStatus)){
				foreach($removeStatus as $rvalue) {
					$rvalue->delete();
				}
			}
            $getStatus = Status::where('user_id', $userId)->where("created_at",">",\Carbon\Carbon::now()->subHours(24))->get();

            if(!$getStatus->isEmpty()){

                $stData = array();
                for ($i = 0; count($getStatus) > $i; $i++) {

                    $single_storyTotalViews = StatusView::where('status_id', $getStatus[$i]->id)->count();

                    $stData[$i]['id'] = $getStatus[$i]->id;
                    $stData[$i]['user_id'] = $getStatus[$i]->user_id;
                    $stData[$i]['story_views'] = $single_storyTotalViews;
                    $stData[$i]['type'] = $getStatus[$i]->type;
                    $stData[$i]['name'] = $userdata->name;
                    $stData[$i]['image'] =$userdata->profile_image_url;
                    $stData[$i]['tag_list'] = DB::table('tag_users')
                    ->join('users', 'users.id', '=', 'tag_users.user_id')
                    ->join('user_detail', 'user_detail.user_id', '=', 'users.id')
                    ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
                    ->WhereRaw("tag_users.story_id=".$getStatus[$i]->id)
                    ->get();
                    
                    for($j=0;$j<count($stData[$i]['tag_list']);$j++){
                       
                        $stData[$i]['intrest_group']=DB::table('interest_group')
                    ->WhereRaw("user_id=".$stData[$i]['tag_list'][$j]->user_id)
                    ->get();
                        
                    }    
                    $saData = [];    
                        
                        
                    if($getStatus[$i]->share_type == 'status'){    
						if($getStatus[$i]->type == 1){
							$stData[$i]['file'] = 'https://artfora.net/images/status/Image/' .  $getStatus[$i]->file;
							$stData[$i]['file_tab'] = 'https://artfora.net/images/status/Image/thumbtab/' .  $getStatus[$i]->file;
						}
						else
						{
							$stData[$i]['file'] = 'https://artfora.net/images/status/Video/' .  $getStatus[$i]->file;
							if($getStatus[$i]->vedio_image == null){
								$stData[$i]['vedio_image'] = null;
							}
							else
							{
								$stData[$i]['vedio_image'] = 'https://artfora.net/images/status/VideoImage/' . $getStatus[$i]->vedio_image;
								$stData[$i]['vedio_image_tab'] = 'https://artfora.net/images/status/VideoImage/thumbtab/' . $getStatus[$i]->vedio_image;
							}
						}
					} else {
						
						$user_id = $getStatus[$i]->user_id;
						$feed_id = $getStatus[$i]->feed_id;
						if($getStatus[$i]->share_type == 'post')
						{
							$get_post = DB::table('post')
											->select('post.id', 'post.type', 'users.id as user_id', 'users.name', 'users.tagname', 'users.email', 'post.image', 'user_detail.profile_image_url','user_detail.category','post.video', 'post.owner_name','post.video_thumbnail','post.video_thumb_title','post.is_flipped','post.is_rotated','post.decription', 'post.tag', 'post.filter_id as filter', 'post.created_at')
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
                            $saData['video_thumbnail'] = $get_post->video_thumbnail;
                            $saData['video_thumb_title'] = $get_post->video_thumb_title;
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
							/* $getStatus = DB::table('post_tags')
										 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
										->join('users', 'users.id', '=', 'post_tags.user_id')
										->join('user_detail', 'user_detail.user_id', '=', 'users.id')
										->where('post_id', $feed_id)
										->get(); */
							
							$saData['tag_list'] = [];

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
						else if($getStatus[$i]->share_type == 'shop')
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

							$get_profile = DB::table('shop_profiles')->where('user_id',  $saData->user_id)->select('name', 'rating')->first();
							$saData['posted_by'] = User::where('id', $saData->user_id)->pluck('name')->first();
							$saData['seller_rating'] = $get_profile->rating;
							$saData['object_type'] = 0;				
						}
						
						if($getStatus[$i]->type == 1){
							$stData[$i]['file'] = 'https://artfora.net/images/status/Image/' .  $getStatus[$i]->file;
							$stData[$i]['file_tab'] = 'https://artfora.net/images/status/Image/thumbtab/' .  $getStatus[$i]->file;
						}
						else
						{
							$stData[$i]['file'] = 'https://artfora.net/images/status/Video/' .  $getStatus[$i]->file;
							if($getStatus[$i]->vedio_image == null){
								$stData[$i]['vedio_image'] = null;
							}
							else
							{
								$stData[$i]['vedio_image'] = 'https://artfora.net/images/status/VideoImage/' . $getStatus[$i]->vedio_image;
								$stData[$i]['vedio_image_tab'] = 'https://artfora.net/images/status/VideoImage/thumbtab/' . $getStatus[$i]->vedio_image;
							}
						}
					}
                    $stData[$i]['swipeUpUrl'] = $getStatus[$i]->swipeUpUrl;
                    $stData[$i]['story_time'] = $getStatus[$i]->story_time;
                    $stData[$i]['share_type'] = $getStatus[$i]->share_type;
                    $stData[$i]['created_at'] = $getStatus[$i]->created_at;
                    $stData[$i]['data'] = $saData;
                }
                return $this->sendResponse($stData, 'Story List');

            }else{
                return $this->sendError("User Story Is not Exsit", array());
            }
        }
    }

    // **********USER SHOW STORY**************
    public function userShowStory (Request $request) {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);


        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
		else
		{
			$userId = $request->user_id;
			$removeStatus = Status::where('user_id', $userId)->where("created_at","<",\Carbon\Carbon::now()->subHours(24))->get();
			if(!empty($removeStatus)){
				foreach($removeStatus as $rvalue) {
					$rvalue->delete();
				}
			}
			
			$isStatus = Status::where('user_id', $userId)->get();
			$blocked_users = DB::table('blocked_users')->where('user_id',$userId)->pluck('blocked_userid')->toArray();

			$user_stories = DB::table('statuses as st')
			->leftjoin('users as us', 'us.id','=','st.user_id')
			->leftjoin('user_detail as ud', 'ud.user_id','=','us.id')
			->select('st.user_id',     DB::raw('count(*) as total_stories'), 'us.name', 
			'ud.profile_image_url', DB::raw('max(st.created_at) as last_story'))
			->groupBy('st.user_id')
			->where([['st.user_id', $userId], ['st.deleted_at', '=', NULL]])
			->orderBy('last_story', 'Desc')
			->get();
			
			
			$follow_stories = DB::table('statuses as st')
			->leftjoin('users as us', 'us.id','=','st.user_id')
			->leftjoin('user_detail as ud', 'ud.user_id','=','us.id')
			->leftjoin('followers as f', 'f.follewers_id','=','us.id')
			->select('st.user_id',     DB::raw('count(*) as total_stories'), 'us.name', 
			'ud.profile_image_url', DB::raw('max(st.created_at) as last_story'))
			->groupBy('st.user_id')
			->where([['f.user_id', $userId], ['st.deleted_at', '=', NULL]])
			->whereNotIn('st.user_id',$blocked_users)
			->orderBy('last_story', 'Desc')
			->get();

			// $other_user_stories = DB::table('statuses as st')
			// ->leftjoin('users as us', 'us.id','=','st.user_id')
			// ->leftjoin('user_detail as ud', 'ud.user_id','=','us.id')
			// ->select('st.user_id',     DB::raw('count(*) as total_stories'), 'us.name', 
			// 'ud.profile_image_url', DB::raw('max(st.created_at) as last_story'))
			// ->groupBy('st.user_id')
			// ->where('st.user_id', '!=' ,$userId)
			// ->orderBy('last_story', 'Desc')
			// ->get();


			$other_user_stories = DB::select("select `st`.`user_id`, count(*) as total_stories, `us`.`name`, `ud`.`profile_image_url`, max(st.created_at) as last_story from `followers` as `f`, `statuses` as `st` left join `users` as `us` on `us`.`id` = `st`.`user_id` left join `user_detail` as `ud` on `ud`.`user_id` = `us`.`id` WHERE (`st`.`user_id`=`f`.`follewers_id` AND `f`.`user_id`=$userId AND `st`.`deleted_at`=NULL) group by `st`.`user_id` order by `last_story` desc ");  




			if(!$isStatus->isEmpty()){

					$response = [
					'success' => true,
					'message' => 'Story List',
					'user_stories' => $user_stories[0],
					'follow_stories' => $follow_stories,
					'data' => $other_user_stories,
					];

					return response()->json($response, 200);

			}else{

					$response = [
					'success' => true,
					'message' => 'Story List',
					'user_stories' => $user_stories,
					'follow_stories' => $follow_stories,
					'data' => $other_user_stories,
					];

					return response()->json($response, 200);
			}
        }

		// $user_info =     Status::groupBy('user_id')
		// ->selectRaw('count(*) as total, user_id')
		// ->get();

		// $Data = array();
		// $Data['user_stories'] = $user_stories[0];
		// $Data['other_user_stories'] =  $other_user_stories;
		// return $this->sendResponse($Data, 'Story List');
    }


    // **********USER STORY SEEN **************
    public function userStorySeen(Request $request) {


        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'status_id' => 'required'
        ]);


        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }else{

            $loggedInUserId = $request['user_id'];
            $user_id =  $request['user_id'];
            $status_id  =  $request['status_id'];

//            $alreadyViews = StatusView::where([['user_id', $user_id],['status_id', $status_id]])->first();
			$status = Status::select('*')->selectRaw('IF(x!="","true","false") as is_tagged')->where('id', $status_id)->first();
			if(!$status){
				 return $this->sendResponse([], 'Story Removed');	
			}
			$saData = [];
			$user_id = $status->user_id;
			$feed_id = $status->feed_id;
			
			if($status->share_type == 'post' && !empty($feed_id))
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
				/* $getStatus = DB::table('post_tags')
							 ->select('users.id as userr_id','users.name as user_name','user_detail.*','users.tagname as tagname')
							->join('users', 'users.id', '=', 'post_tags.user_id')
							->join('user_detail', 'user_detail.user_id', '=', 'users.id')
							->where('post_id', $feed_id)
							->get(); */
				
				$saData['tag_list'] = [];

				$saData['is_like'] = with(new \App\PostLike())->get_user_is_like($user_id, $get_post->id);
				$saData['is_saved'] = $is_saved;
				$saData['detail'] = $saGetDetail;
				$saData['likes'] = $saGeLikes;
				$saData['comments'] = $saGeComments;
				$saData['post_interest'] = DB::table('interest_group_posts')->select('interest_name')->where([['post_id', $get_post->id], ['deleted_at', NULL]])->get()->toArray();
				$saData['tags'] = $saGetTag;
				$saData['created_at'] = $get_post->created_at;

				$saData['files'] = DB::table('post_files')->select('file_type','file_name','resized_file_name','is_flipped','is_rotated','thumb','file_width','file_height','x','y','user_ids','tag_users')->selectRaw('IF(x!="[]","true","false") as isTagged')->where('post_id',$get_post->id)->orderBy('priority', 'ASC')->get()->toArray();
				$saData['object_type'] = 1;
			}
			else if($status->share_type == 'shop' && !empty($feed_id))
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

				$get_profile = DB::table('shop_profiles')->where('user_id',  $saData->user_id)->select('name', 'rating')->first();
				$saData['posted_by'] = User::where('id', $user_id)->pluck('name')->first();
				$saData['seller_rating'] = $get_profile->rating;
				$saData['object_type'] = 0;				
			}
			
			if($status->type == 1){
				$saData['file'] = 'https://artfora.net/images/post/new_images/thumb/' .  $status->file;
				$saData['file_tab'] = 'https://artfora.net/images/post/new_images/thumbtab/' .  $status->file;
			}
			else
			{
				$saData['file'] = 'https://artfora.net/images/post/new_images/thumb/' .  $status->file;
				if($status->vedio_image == null){
					$saData['vedio_image'] = null;
				}
				else
				{
					$saData['vedio_image'] = 'https://artfora.net/images/post/new_images/thumb/' . $status->vedio_image;
					$saData['vedio_image_tab'] = 'https://artfora.net/images/post/new_images/thumbtab/' . $status->vedio_image;
				}
			}
			
			$status->post = $saData; 
						
            if ($user_id != $loggedInUserId) {
                $vStatus = new StatusView();
                $vStatus->user_id = $loggedInUserId;
                $vStatus->status_id = $status_id;
                $vStatus->save();
                return $this->sendResponse($status, 'View Story Updated');
            }else{
                return $this->sendResponse($status, 'Already View Story');
            }
        } 
    }

    // **********VIEW STORY SEEN**************
    public function viewStorySeen(Request $request) {

        $validator = Validator::make($request->all(), [

                'status_id' => 'required'
        ]);


        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }else{

            $status_id   =    $request['status_id'];

            $check_status = Status::where('id', $status_id)->first();

            if(!empty($check_status)){
            
            $totalViews = StatusView::where('status_id', $status_id)->count();
            
                $seen_user = DB::table('status_views as st_v')
                     ->leftjoin('users', 'users.id','=','st_v.user_id')
                     ->leftJoin('user_detail', 'user_detail.user_id', '=', 'users.id')
                     ->select( 'users.name', 'st_v.created_at as Seen_date_time', 'user_detail.profile_image_url as image')
                     ->where('status_id', $status_id)
                     ->get();



            $Data = array();

            $Data['totalViews'] = $totalViews;
            $Data['seen_user'] = $seen_user;

             return $this->sendResponse($Data, 'View User List');

            }else{

             return $this->sendError("Status Id is Not Exsit", array());
            }   
        }
    }


 
    // ****************** EXPIRE STORY ************
    public function expireStory(Request $request) {

      $date = new DateTime();
      $date->modify('-24 min');
      $formatted_date = $date->format('Y-m-d H:i:s');
      $storyResult = DB::table('statuses')->where('created_at', '<',$formatted_date)->delete();


      if($storyResult > 0){
        return $this->sendResponse([], 'Story is expire');
      }else{
         return $this->sendResponse([], 'Story not Exsit');
      }
    }
    

    // *******************************************
    public function tagSearchUser(Request $request) {


        $validator = Validator::make($request->all(), [

                    'username' => 'required',

        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            /*FOR ONLINE LINK*/  
            $ssImageUrl = 'https://artfora.net/images/profile_image';

            /*FOR APPCRATES LINK*/ 
          
            $saSearchUser = DB::table('users')
                            ->select('users.id', 'users.name','users.email','users.tagname',
                            DB::raw("CONCAT('','',user_detail.profile_image_url)as image"),'privete_profile_url')
                            ->where('users.name', 'like', '%' . $request->username . '%')
                            ->leftjoin('user_detail', 'users.id', '=', 'user_detail.user_id')
                            ->groupBy('users.id')
                            ->get()->toArray();


            return $this->sendResponse($saSearchUser, 'User Listed Successfully Search');
        }
    }

 // *************** DELETE STORY **************************
    public function deleteStory(Request $request) {



        $validator = Validator::make($request->all(), [

                    'status_id' => 'required',

        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {

            $data = array();

              $status_id   =    $request['status_id'];
              $storyResult = DB::table('statuses')->where('id',$status_id)->delete();

              $data["story_id"] = $status_id;

                if($storyResult > 0){
                    return $this->sendResponse($data, 'Story is Delete');
                  }else{
                     return $this->sendResponse([], 'Story not Exsit');
                  }

        }
    }


}
