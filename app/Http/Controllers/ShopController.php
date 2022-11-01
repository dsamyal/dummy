<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ShopProduct;
use App\ShopProfile;
use App\ShopProductFile;
use App\ShopProductDetail;
use App\Cart;
use App\CartProduct;
use App\SharePost;
use App\User;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Mail\ShopProductMail;
use Response;
use App\ShopProductMeta;
use App\ActivityLog;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;

// import the storage facade
use Illuminate\Support\Facades\Storage;


class ShopController extends BaseController {

    public function shop_product(Request $request) {

        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'shop_profile_id' => 'required',
        ]);

        	 $contactInfo = Storage::disk('local')->exists('data.json') ? json_decode(Storage::disk('local')->get('data.json')) : [];
        
            $inputData = $request->all();
           
            $inputData['datetime_submitted'] = date('Y-m-d H:i:s');
 
            array_push($contactInfo,$inputData);
    
            Storage::disk('local')->put('data.json', json_encode($contactInfo));

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {

            $shop_product = $request->except('videos', 'images');
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
            $shop_product_id = ShopProduct::create($shop_product);

            //If hash taged 
            if (!empty($request['tags'])) {
                $hashExploadTags = explode(',', $request['tags']);
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
            //If hash taged

            $p = 0;
            while($p < count($images))
            {
                $images[$p][7] = 0;
                $p++;
            }

            $p = 0;
            while($p < count($videos))
            {
                $videos[$p][7] = 1;
                $p++;
            }

            // MERGE TWO ARRAYS AND SORT BY PRIORITY
            $shop_files = array_merge($images, $videos);
            array_multisort(array_column($shop_files, 4), SORT_ASC, $shop_files);
            // $sorted_array_by_priority = array_values(array_sort($shop_files, function ($value) {
            //     return $value[4];
            // }));
            // MERGE TWO ARRAYS AND SORT BY PRIORITY

            for($i=0; $i<count($shop_files); $i++)
            {
                //$ssImage1 = time() . $shop_files[$i][1]->getClientOriginalName();
                /* $destinationPath1 = public_path('/images/post/new_images');
                $shop_files[$i][0]->move($destinationPath1, $ssImage1);
                $ssImage = time() . $shop_files[$i][1]->getClientOriginalName();
                $destinationPath1 = public_path('/images/post/new_images/thumb');
                $shop_files[$i][1]->move($destinationPath1, $ssImage);  */
				
				$image = $shop_files[$i][0];
				$randomname = time().rand();
				$ssImage = $randomname .'.'.$image->getClientOriginalExtension();
                
                $imageRes = $shop_files[$i][8];
                $randomnameRes = time().rand();
                $ssImageRes = $randomnameRes .'.'.$imageRes->getClientOriginalExtension();
//                $PROD_URL = "https://artfora.net";                                      //Bhavesh - Changes
				//video upload start
				if($shop_files[$i][7] == 1){
					$videofile = $shop_files[$i][1];
					$ssImage1 = "";
					if(!empty($videofile)){
						$shop_files[$i][7] = 2;
						$randomname1 = time().rand();
						$ssImage1 = $randomname1 .'.'.$videofile->getClientOriginalExtension();
						$destinationPath = public_path('/images/post/new_images');
//                        $destinationPath = $PROD_URL . '/images/post/new_images' ;       //Bhavesh - Changes
						$videofile->move($destinationPath, $ssImage1);
					}
                    
                    //For resized image
                    $videofileRes = $shop_files[$i][8];
                    $ssImage1Res = "";
                    if(!empty($videofileRes)){
                        $randomname1Res = time().rand();
                        $ssImage1Res = $randomname1Res .'.'.$videofileRes->getClientOriginalExtension();
                        $destinationPathRes = public_path('/images/post/new_images');
//                        $destinationPathRes = $PROD_URL . '/images/post/new_images';       //Bhavesh - Changes
                        $videofileRes->move($destinationPathRes, $ssImage1Res);
                    }
				} else {
					$ssImage1 = $ssImage;
                    $ssImage1Res = $ssImageRes;
                    
                    //For ResizedImage
                    $destinationPathRes = public_path('/images/post/new_images/thumb');
//                    $destinationPathRes = $PROD_URL . '/images/post/new_images/thumb' ;      //Bhavesh - Changes
                    $imgRes = Image::make($imageRes->getRealPath());
                    $imgRes->resize(640, 640, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPathRes.'/'.$ssImageRes);

                    $destinationPathRes = public_path('/images/post/new_images');          //Bhavesh - Changes
//                    $destinationPathRes = $PROD_URL . '/images/post/new_images' ;
                    $imageRes->move($destinationPathRes, $ssImageRes);
                    //For ResizedImage End
				}
				//video upload end
                $destinationPath = public_path('/images/post/new_images/thumb');
//				$destinationPath = $PROD_URL . '/images/post/new_images/thumb' ;            //Bhavesh - Changes
				$img = Image::make($image->getRealPath());
				$img->resize(640, 640, function ($constraint) {
					$constraint->aspectRatio();
				})->save($destinationPath.'/'.$ssImage);
				
                $destinationPath = public_path('/images/post/new_images/thumbtab');
//				$destinationPath = $PROD_URL . '/images/post/new_images/thumbtab';          //Bhavesh - Changes
				$img2 = Image::make($image->getRealPath());
				$img2->resize(900, 900, function ($constraint) {
					$constraint->aspectRatio();
				})->save($destinationPath.'/'.$ssImage);
                $destinationPath = public_path('/images/post/new_images');
//				$destinationPath = $PROD_URL . '/images/post/new_images';                  //Bhavesh - Changes
				$image->move($destinationPath, $ssImage);
                
                $shop_files_data[] = array(
                    "user_id" => $request->user_id,
                    "shop_profile_id" => $request->shop_profile_id,
                    "shop_product_id" => $shop_product_id->id,
                    "file_name"=>$ssImage1,
                    "resized_file_name"=>$ssImage1Res,
                    "file_type"=>$shop_files[$i][7],
                    "thumb"=>$ssImage,
                    "is_flipped"=>$shop_files[$i][2],
                    "is_rotated"=>$shop_files[$i][3],
                    "file_width"=>$shop_files[$i][5],
                    "file_height"=>$shop_files[$i][6],
                    "file_status" => 0
                );                
            }

            DB::table('shop_product_files')->insert($shop_files_data);
      
            if (!empty($request['product_detail'])) {
                $shop_details = array();
                foreach (json_decode($request['product_detail']) AS $product_detail) {

                    $shop_details[] = array(
                        'user_id' => $request->user_id,
                        'shop_profile_id' => $request->shop_profile_id,
                        'shop_product_id' => $shop_product_id->id,
                        'title' => $product_detail->title,
                        'value' => $product_detail->value,
                    );
                }

                if(count($shop_details) > 0)
                {
                    DB::table('shop_product_details')->insert($shop_details);
                }
            }
            
                $shopper_email = ShopProfile::find($request->shop_profile_id);
                
                $to = $shopper_email->email;
                $body = 'Hi '.$shopper_email->name.', <br>Your product '.$request->name.' has been sent for approval.';
                $subject = "Product Approval";
               
				$message['to_email'] = $to;
				$message['view'] = "mail";
				$message['body'] = $body;
				
				// $this->custom_email($to, $from=NULL, $subject, $message);
				
                // email admin for approval
                // $to = "shop_pending@artfora.net";
				
                $body = 'Hi Admin,<br> '.$shopper_email->name.' added a new product '.$request->name.' which needs your approval.<br> 
                        <a href="https://artfora.net/product_details/'.$shop_product_id->id.'"> Click to review<a>';
                
				
				$to_email = "shop_pending@artfora.net";
				$subject = "Product Approval";
				$message['to_email'] = $to_email;
				$message['view'] = "mail";
				$message['body'] = $body;

				$userData = User::where('id', $request->user_id)->first();
				$profile = DB::table('user_detail')->where('user_id',  $request->user_id)->select('profile_image_url','category')->first();
				$filter_text = DB::table('filters')->where('id',  $request->filter_id)->select('filter_text')->first();

				$data = array(
					'email' => $to_email,
					'subject' => $subject,
					'message' => $body,
					'shop_product' => $shop_product,
					'user_name' => $userData->name,
					'user_image' => $profile->profile_image_url,
					'user_tagname' => $profile->category,
					'shop_product' => $shop_product,
					'shop_files_data' => $shop_files_data,
					'filter_text' => $filter_text->filter_text,
					'datetime_submitted' => date('d.m.Y'),
				);
				
				try {

					\Mail::to($to_email)->send(new ShopProductMail($data));

				} catch (\Exception $e) {

				    Storage::disk('local')->put('error.txt', $e->getMessage());
				}
							
				// $this->custom_email($to_email, $from=NULL, $subject, $message);
                
                return $this->sendResponse($shop_product_id, 'Product added successfully.');
		}
    }
	
	public function update_product(Request $request,$id) {
		
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'shop_profile_id' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
			
			$shop_product = $request->except('videos', 'images');
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
			
			$product = ShopProduct::where([['id', $id], ['user_id', $request->user_id]])->firstOrFail();
			$product->update($shop_product);
            //$shop_product_id = ShopProduct::create($shop_product);
			$shop_product_id = ShopProduct::where('id',$id)->first();

            //If hash taged 
            if (!empty($request['tags'])) {
                $hashExploadTags = explode(',', $request['tags']);
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
			
			$all_shop_product_ids = DB::table('shop_product_files')->where('shop_product_id', $id)->pluck('id')->toArray();
			
            //If hash taged
			if(!empty($images) || !empty($videos)){
				// upload file 
				$p = 0;
				while($p < count($images))
				{
					$images[$p][7] = 0;
					$p++;
				}

				$p = 0;
				while($p < count($videos))
				{
					$videos[$p][7] = 1;
					$p++;
				}

				// MERGE TWO ARRAYS AND SORT BY PRIORITY
				$shop_files = array_merge($images, $videos);
				array_multisort(array_column($shop_files, 4), SORT_ASC, $shop_files);            
				// MERGE TWO ARRAYS AND SORT BY PRIORITY

				for($i=0; $i<count($shop_files); $i++)
				{
					$image = $shop_files[$i][0];
					$randomname = time().rand();
					$ssImage = $randomname .'.'.$image->getClientOriginalExtension();
                    
                    $imageRes = $shop_files[$i][8];
                    $randomnameRes = time().rand();
                    $ssImageRes = $randomnameRes .'.'.$imageRes->getClientOriginalExtension();
					
					//video upload start
					if($shop_files[$i][7] == 1){
						$videofile = $shop_files[$i][1];
						$ssImage1 = "";
						if(!empty($videofile)){
							$shop_files[$i][7] = 2;
							$randomname1 = time().rand();
							$ssImage1 = $randomname1 .'.'.$videofile->getClientOriginalExtension();
							$destinationPath = public_path('/images/post/new_images');
							$videofile->move($destinationPath, $ssImage1);
						}
                        
                        //For resized image
                        $videofileRes = $shop_files[$i][8];
                        $ssImage1Res = "";
                        if(!empty($videofileRes)){
                            $randomname1Res = time().rand();
                            $ssImage1Res = $randomname1Res .'.'.$videofileRes->getClientOriginalExtension();
                            $destinationPathRes = public_path('/images/post/new_images');
                            $videofileRes->move($destinationPathRes, $ssImage1Res);
                        }
					} else {
                        $ssImage1 = $ssImage;
                        $ssImage1Res = $ssImageRes;
                        
                        //For ResizedImage
                        $destinationPathRes = public_path('/images/post/new_images/thumb');
                        $imgRes = Image::make($imageRes->getRealPath());
                        $imgRes->resize(640, 640, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($destinationPathRes.'/'.$ssImageRes);

                        $destinationPathRes = public_path('/images/post/new_images');
                        $imageRes->move($destinationPathRes, $ssImageRes);
                        //For ResizedImage End
					}
					//video upload end
					
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

					$shop_files_data[] = array(
						"user_id" => $request->user_id,
						"shop_profile_id" => $request->shop_profile_id,
						"shop_product_id" => $id,
						"file_name"=>$ssImage1,
                        "resized_file_name"=>$ssImageRes,
						"file_type"=>$shop_files[$i][7],
						"thumb"=>$ssImage,
						"is_flipped"=>$shop_files[$i][2],
						"is_rotated"=>$shop_files[$i][3],
						"file_width"=>$shop_files[$i][5],
						"file_height"=>$shop_files[$i][6],
						"file_status" => 0
					);                
				}
			
				DB::table('shop_product_files')->insert($shop_files_data);
			}
			
			// upload file end
			
			if(!empty($request['shop_files_id'])) {
                $shop_files_id = explode(",",$request['shop_files_id']);
				
				$diff = array_diff($all_shop_product_ids,$shop_files_id);
				
				if(count($diff) > 0){
					DB::table('shop_product_files')->whereIn('id',$diff)->delete();
				}
				
				for($i=1; $i<=count($shop_files_id); $i++){
                
						$update_shop_file = array(
							'file_index' => $i
						);
						DB::table('shop_product_files')->where('id', $shop_files_id[$i-1])->update($update_shop_file);
				}
			} else {
				DB::table('shop_product_files')->whereIn('id',$all_shop_product_ids)->delete();
			}
			
			DB::table('shop_product_details')->where('shop_product_id', $shop_product_id->id)->delete();
            if (!empty($request['product_detail'])) {
                $shop_details = array();
                foreach (json_decode($request['product_detail']) AS $product_detail) {
					$shop_details[] = array(
							'user_id' => $request->user_id,
							'shop_profile_id' => $request->shop_profile_id,
							'shop_product_id' => $shop_product_id->id,
							'title' => $product_detail->title,
							'value' => $product_detail->value,
						);
                }
                if(count($shop_details) > 0)
                {
                    DB::table('shop_product_details')->insert($shop_details);
                }
            }
            
                $shopper_email = ShopProfile::find($request->shop_profile_id);
                /* 
                $to = $shopper_email->email;
                $body = 'Hi '.$shopper_email->name.', <br>Your product '.$request->name.' has been sent for approval.<br>Thanks!';
                $subject = "Product Approval";
                $message = '<!doctype html>
                <html>
                <head>
                    <title>Email template</title>
                    <link rel="stylesheet" href="css/screen.css">
                </head>
                <body height="100%">
                    <table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0"> 
                        <tbody>
                            <tr>
                                <td>
                                    <p style="text-align: center; padding-bottom:40px;">
                                        <img src="http://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" />
                                    </p> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="text-align: center; color:#666;">
                                        <span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">WELCOME TO ARTfora</span>
                                    </p>
                                </td>
                            </tr>
                             <tr>
                                <td>
                                    <p style="text-align: center; color:#666;">
                                        <span style="font-size: 13px; font-family: Prozak, sans-serif;">'.$body.'</span>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                 </body>
                </html>';
            
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
                
                mail($to, $subject, $message, $headers); 
				
				*/
                
                // email admin for approval
                // $to = "shop_pending@artfora.net";
                
//                $to = "shop_pending@artfora.net";
//                $body = 'Hi Admin,<br> '.$shopper_email->name.' updated a product '.$request->name.' which needs your approval.<br>
//                        <a href="http://artfora.appcrates.co/product_details/'.$shop_product_id->id.'"> Click to review<a> <br> Thanks!';
//
//                $subject = "Product Approval";
//                $message = '<!doctype html>
//                <html>
//                <head>
//                    <title>Email template</title>
//                    <link rel="stylesheet" href="css/screen.css">
//                </head>
//                <body height="100%">
//                    <table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0">
//                        <tbody>
//                            <tr>
//                                <td>
//                                    <p style="text-align: center; padding-bottom:40px;">
//                                        <img src="http://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" />
//                                    </p>
//                                </td>
//                            </tr>
//                            <tr>
//                                <td>
//                                    <p style="text-align: center; color:#666;">
//                                        <span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">WELCOME TO ARTfora</span>
//                                    </p>
//                                </td>
//                            </tr>
//                             <tr>
//                                <td>
//                                    <p style="text-align: center; color:#666;">
//                                        <span style="font-size: 13px; font-family: Prozak, sans-serif;">'.$body.'</span>
//                                    </p>
//                                </td>
//                            </tr>
//                        </tbody>
//                    </table>
//                 </body>
//                </html>';
//
//                $headers = "MIME-Version: 1.0" . "\r\n";
//                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//                $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
//
//                mail($to, $subject, $message, $headers);
                
            $body = 'Hi Admin,<br> '.$shopper_email->name.' added a new product '.$request->name.' which needs your approval.<br>
                <a href="https://artfora.net/product_details/'.$shop_product_id->id.'"> Click to review<a>';
            
            
            $to_email = "shop_pending@artfora.net";
            $subject = "Product Approval";
            $message['to_email'] = $to_email;
            $message['view'] = "mail";
            $message['body'] = $body;

            $userData = User::where('id', $request->user_id)->first();
            $profile = DB::table('user_detail')->where('user_id',  $request->user_id)->select('profile_image_url','category')->first();
            $filter_text = DB::table('filters')->where('id',  $request->filter_id)->select('filter_text')->first();
//            if (empty($shop_files_data)) {
                $shop_files_data_query = DB::table('shop_product_files')->whereIn('id',$shop_files_id)->get()->toArray();
//            }
            
            foreach($shop_files_data_query as $file) {
                $shop_files_data[] = array(
                    "user_id" => $file->user_id,
                    "shop_profile_id" => $file->shop_profile_id,
                    "shop_product_id" => $file->shop_product_id,
                    "file_name"=>$file->file_name,
                    "resized_file_name"=>$file->resized_file_name,
                    "file_type"=>$file->file_type,
                    "thumb"=>$file->thumb,
                    "is_flipped"=>$file->is_flipped,
                    "is_rotated"=>$file->is_rotated,
                    "file_width"=>$file->file_width,
                    "file_height"=>$file->file_height,
                    "file_status" => $file->file_status
                );
            }
            
            $data = array(
                'email' => $to_email,
                'subject' => $subject,
                'message' => $body,
                'shop_product' => $shop_product,
                'user_name' => $userData->name,
                'user_image' => $profile->profile_image_url,
                'user_tagname' => $profile->category,
                'shop_product' => $shop_product,
                'shop_files_data' => $shop_files_data ?? [],
                'filter_text' => $filter_text->filter_text,
                'datetime_submitted' => date('d.m.Y'),
            );
            
            try {

                \Mail::to($to_email)->send(new ShopProductMail($data));

            } catch (\Exception $e) {

                Storage::disk('local')->put('error.txt', $e->getMessage());
            }
            
            return $this->sendResponse($shop_product_id, 'Product added successfully.');
        }
    }

    public function all_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails())
        {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
        else 
        {
            $user_id = $request->user_id;
            $filter_id = $request->filter_id;
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
            
            $Block_user=DB::table('blocked_users')->where('user_id',$user_id)->pluck('blocked_userid')->toArray();
            $Block_me_user=DB::table('blocked_users')->where('blocked_userid',$user_id)->pluck('user_id')->toArray();
            for ($i = 0; count($Block_me_user) > $i; $i++) {
                array_push($Block_user, $Block_me_user[$i]);
            }

            $product_info = ShopProduct::with('shop_product_details', 'shop_product_files')
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
            ->leftJoin('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
            ->whereIn('filter_id', $filter_array)->where('shop_profiles.deleted_at', NULL);
            
            $product_info->where('status', 1);
            if($request->type == 'all')
            {
//                $product_info->where('status', 1);
            }
            else
            {
                $type_arr = explode(',', $request->type);
                $product_info->whereIn('type',$type_arr);
            }
            
            if($request->for_sale == '1') //Data for sale only
            {
                $product_info->where('sale', 'yes');
            }
            
			$product_info->where('shop_products.deleted_at','=', NULL)->whereNotIn('shop_products.user_id',$Block_user);
            
			if($request->sort_by){
				$product_info->inRandomOrder();
			} else{
				$product_info->orderBy('id', 'DESC');
			}
			
            $product_info->offset($request->offset)->limit($request->limit);
            $product_info = $product_info->get();
            //$product_info = $product_info->inRandomOrder()->get();
            
            for($i=0;$i<count($product_info);$i++)
            {
                $profile = DB::table('user_detail')->where('user_id', $product_info[$i]->user_id)->select('category')->first();
				$followers = \App\Followers::where('follewers_id', $product_info[$i]['user_id'])->where('user_id', $request->user_id)->first();
                $get_profile = DB::table('shop_profiles')->where('user_id',  $product_info[$i]->user_id)->select('name', 'rating', 'shop_image_url')->first();
                $saGetRepostsCount = \App\SharePost::select('user_id')->where('feed_id',$product_info[$i]->id)->count();
                
                $product_info[$i]['posted_by'] = User::where('id', $product_info[$i]->user_id)->pluck('full_name')->first();
                $product_info[$i]['seller_rating'] = $get_profile->rating;
                $product_info[$i]['profile_image_url'] = $get_profile->shop_image_url;
				$shop_name = ShopProfile::where('id',$product_info[$i]->shop_profile_id)->first();
                $product_info[$i]['shop_name'] = $shop_name->shop_name??"";
                $product_info[$i]['shop_image_url'] = $shop_name->shop_image_url??"";
                               $product_info[$i]['shop_category'] = $profile->category??"";
				$product_info[$i]['followers'] = $followers;
                $product_info[$i]['reposts'] = $saGetRepostsCount;
            }
            return $this->sendResponse($product_info, 'All Products');
        }
    }
    
    public function add_to_cart(Request $request){
        
         $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {

            
            $user_cart = Cart::where('user_id',$request->user_id)->first();
            
            if ($user_cart){
                
                $cart_products = CartProduct::where(array('cart_id'=>$user_cart->id,'shop_product_id'=>$request->shop_product_id,'seller_id'=>$request->seller_id))->first();
                
                if($cart_products){
                    
                    $cart_product = CartProduct::find($cart_products->id);
                    $cart_product->qty = $cart_products->qty + $request->qty;
                    $cart_product->weight = $cart_products->weight + $request->weight;
                    $cart_product->price = $cart_products->price + $request->price;
                    
                    $get_shop_detail = DB::table('shop_profiles')->where('id', $request->seller_id)->first();
                
                $get_shipping_detail = DB::table('shop_shipping_costs')
                ->where('user_id','=',$get_shop_detail->user_id)
                ->where('type' ,'=',$get_shop_detail->shipping_type)
                ->get();
    
             if($get_shop_detail->shipping_type == 1)
             {
                 foreach($get_shipping_detail as $shipping_detail)
                 {
                  
                 if( $cart_product->weight >= $shipping_detail->weight_start &&  $cart_product->weight <= $shipping_detail->weight_end  ){
                 
                     $cart_product->shipping =  $shipping_detail->cost;
                    
                
                } else if($cart_product->weight >= $shipping_detail->weight_start && $shipping_detail->weight_end == ""){
               
                $cart_product->shipping =  $shipping_detail->cost;
              
                    }
            
                }
            } else {
               
                foreach($get_shipping_detail as $shipping_detail)
                {
                    
                    if( $cart_product->qty == $shipping_detail->items ){
                    
                    $cart_product->shipping =  $shipping_detail->cost;
              
                
                    } else if( $cart_product->qty >= 3 && $shipping_detail->items >= 3 ){
                
                    $cart_product->shipping = $shipping_detail->cost;
                 
                        }
                }
            }
                    $cart_product->save();
                    
                    $total_shipping = 0;
                    $subtotal = 0;
                    $final_total = 0;
                    $cart_data = Cart::where('user_id',$request->user_id)->first();
                    $all_products_total = CartProduct::where(array('cart_id'=>$cart_data->id))->get()->toArray();
                    
                    for($z=0;$z<count($all_products_total);$z++)
                    {    
                         $total_shipping = $total_shipping + $all_products_total[$z]['shipping'];
                         
                         $subtotal = $subtotal + $all_products_total[$z]['qty'] * $all_products_total[$z]['price'];
                    }
                
                    $final_total = $subtotal + $total_shipping;
                    
                    $cart_data->sub_total = $subtotal;
                    $cart_data->shipping_total = $total_shipping;
                    $cart_data->total = $final_total;
                    $cart_data->save(); 
                    
                    return $this->sendResponse($cart_product,'Product updated in cart successfully.');
                    
                } else {
                    
                    $cart_product = new CartProduct();
                    $cart_product->cart_id = $user_cart->id;
                    $cart_product->seller_id = $request->seller_id;
                    $cart_product->shop_product_id = $request->shop_product_id;
                    $cart_product->qty = $request->qty;
                    // $cart_product->type = $request->type;
                    $cart_product->size = $request->size;
                    $cart_product->weight = $request->weight;
                    $cart_product->price = $request->price;
                    
                    $get_shop_detail = DB::table('shop_profiles')->where('id', $request->seller_id)->first();
                
                    $get_shipping_detail = DB::table('shop_shipping_costs')
                    ->where('user_id','=',$get_shop_detail->user_id)
                    ->where('type' ,'=',$get_shop_detail->shipping_type)
                    ->get();
    
             if($get_shop_detail->shipping_type == 1)
             {
                 
                 foreach($get_shipping_detail as $shipping_detail)
                 {
                  
                 if($request->weight >= $shipping_detail->weight_start && $request->weight <= $shipping_detail->weight_end  )
                 {
                        $cart_product->shipping = $shipping_detail->cost;
                    
                
                 } else if($cart_product->weight >= $shipping_detail->weight_start && $shipping_detail->weight_end == ""){
               
                        $cart_product->shipping = $shipping_detail->cost; 
                    }
                }
                
                } else if($get_shop_detail->shipping_type == 0) {
               
                foreach($get_shipping_detail as $shipping_detail)
                {
                   
                if($request->qty == $shipping_detail->items )
                {      
                    $cart_product->shipping = $shipping_detail->cost;
              
                } else if( $request->qty >= 3 && $shipping_detail->items >= 3){
              
                    $cart_product->shipping = $shipping_detail->cost;
                    }
            
                }
                
            }
                    $cart_product->save();
                    
                    $total_shipping = 0;
                    $subtotal = 0;
                    $final_total = 0;
                    $cart_data = Cart::where('user_id',$request->user_id)->first();
                    $all_products_total = CartProduct::where(array('cart_id'=>$cart_data->id))->get()->toArray();
                    
                    for($z=0;$z<count($all_products_total);$z++)
                    {    
                         $total_shipping = $total_shipping + $all_products_total[$z]['shipping'];
                         
                         $subtotal = $subtotal + $all_products_total[$z]['qty'] * $all_products_total[$z]['price'];
                    }
                
                    $final_total = $subtotal + $total_shipping;
                    
                    $cart_data->sub_total = $subtotal;
                    $cart_data->shipping_total = $total_shipping;
                    $cart_data->total = $final_total;
                    $cart_data->save(); 
                    
                    return $this->sendResponse($cart_product ,'Product added to cart successfully.');
                    
                }
              
            
            } else {
                
            $cart = new Cart();
            
            $cart->user_id = $request->user_id;
            // $cart->sub_total = $request->sub_total;
            // $cart->shipping_total = $request->shipping_total;
            // $cart->total = $request->total;
            $cart->save();
            
            $cart_product = new CartProduct();
            $cart_product->cart_id = $cart->id;
            $cart_product->seller_id = $request->seller_id;
            $cart_product->shop_product_id = $request->shop_product_id;
            $cart_product->qty = $request->qty;
            // $cart_product->type = $request->type;
            $cart_product->size = $request->size;
            $cart_product->weight = $request->weight;
            $cart_product->price = $request->price;
            
            $get_shop_detail = DB::table('shop_profiles')->where('id', $request->seller_id)->first();
                
                $get_shipping_detail = DB::table('shop_shipping_costs')
                ->where('user_id','=',$get_shop_detail->user_id)
                ->where('type' ,'=',$get_shop_detail->shipping_type)
                ->get();
    
             if($get_shop_detail->shipping_type == 1)
             {
              
             
                 foreach($get_shipping_detail as $shipping_detail)
                 {
                  
                 if($request->weight >= $shipping_detail->weight_start && $request->weight <= $shipping_detail->weight_end  )
                 {
                     $cart_product->shipping = $shipping_detail->cost;
                     
                 } else if($request->weight >= $shipping_detail->weight_start && $shipping_detail->weight_end == ""){
               
                $cart_product->shipping = $shipping_detail->cost;
              
                 }
            
                }
            } else {
               
                foreach($get_shipping_detail as $shipping_detail)
                {
                   
                if($request->qty == $shipping_detail->items )
                {
                    
              $cart_product->shipping = $shipping_detail->cost;
                    
                } else if( $request->qty >= 3 && $shipping_detail->items >= 3 ){
                
               $cart_product->shipping = $shipping_detail->cost;
                 
                    }
            
                }
                
            }
    
            $cart_product->save();
            
            $total_shipping = 0;
                    $subtotal = 0;
                    $final_total = 0;
                    $cart_data = Cart::where('user_id',$request->user_id)->first();
                    $all_products_total = CartProduct::where(array('cart_id'=>$cart_data->id))->get()->toArray();
                    
                    for($z=0;$z<count($all_products_total);$z++)
                    {    
                         $total_shipping = $total_shipping + $all_products_total[$z]['shipping'];
                         
                         $subtotal = $subtotal + $all_products_total[$z]['qty'] * $all_products_total[$z]['price'];
                    }
                
                    $final_total = $subtotal + $total_shipping;
                    
                    $cart_data->sub_total = $subtotal;
                    $cart_data->shipping_total = $total_shipping;
                    $cart_data->total = $final_total;
                    $cart_data->save(); 
                    
            }
            
            return $this->sendResponse($cart ,'New Product added to cart successfully.');
            }        
        }

    
    public function productQuantity(Request $request){
        
          $validator = Validator::make($request->all(), [
                'cart_id' => 'required',
                'seller_id' => 'required',
                'shop_product_id' => 'required',
                
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
             
            $cart_product_quantity = CartProduct::where(array('cart_id'=>$request->cart_id,'shop_product_id'=>$request->shop_product_id,'seller_id'=>$request->seller_id))->first();
            $get_product_detail = DB::table('shop_products')->where(array('shop_profile_id' => $request->seller_id, 'id' => $request->shop_product_id))->first();
            // var_dump($get_product_detail);
            // die;
            
            if($cart_product_quantity){
                    
                    if($request->type == '+'){
                    
                    $cart_product_quantity->qty = $cart_product_quantity->qty + 1;
                    $cart_product_quantity->weight = $cart_product_quantity->weight + $get_product_detail->package_weight;
                    $cart_product_quantity->price = $cart_product_quantity->price + $get_product_detail->price;
                    
                    $get_shop_detail = DB::table('shop_profiles')->where('id', $request->seller_id)->first();
                
                $get_shipping_detail = DB::table('shop_shipping_costs')
                ->where('user_id','=',$get_shop_detail->user_id)
                ->where('type' ,'=',$get_shop_detail->shipping_type)
                ->get();
    
             if($get_shop_detail->shipping_type == 1){
              
             
                 foreach($get_shipping_detail as $shipping_detail){
                  
                //   echo $cart_product_quantity->weight;
                //   die;
                  
                 if($cart_product_quantity->weight >= $shipping_detail->weight_start && $cart_product_quantity->weight <= $shipping_detail->weight_end  ){
                 
                     $cart_product_quantity->shipping = $shipping_detail->cost;
                
                
            } else if($cart_product_quantity->weight >= $shipping_detail->weight_start && $shipping_detail->weight_end == "") {
               
                $cart_product_quantity->shipping = $shipping_detail->cost;
              
            }
            
                }
            } else {
               
                foreach($get_shipping_detail as $shipping_detail){
                    
                if($cart_product_quantity->qty == $shipping_detail->items ){
                   
              $cart_product_quantity->shipping = $shipping_detail->cost;
            
                
            } else if($cart_product_quantity->qty >= 3 && $shipping_detail->items >= 3){
                
               $cart_product_quantity->shipping = $shipping_detail->cost;
            
                 
            }
            
                }
                
            }
        //   end of shipping
        
                    $cart_product_quantity->save();
                    
                    return response()->json([
                        "response" => "Quantity Updated Successfully"
                        ]);
                    
                    } else if($request->type == '-'){
                        
                    $cart_product_quantity->qty = $cart_product_quantity->qty - 1;
                    $cart_product_quantity->weight = $cart_product_quantity->weight - $get_product_detail->package_weight;
                    $cart_product_quantity->price = $cart_product_quantity->price - $get_product_detail->price;
                    
                    $get_shop_detail = DB::table('shop_profiles')->where('id', $request->seller_id)->first();
                
                $get_shipping_detail = DB::table('shop_shipping_costs')
                ->where('user_id','=',$get_shop_detail->user_id)
                ->where('type' ,'=',$get_shop_detail->shipping_type)
                ->get();
    
             if($get_shop_detail->shipping_type == 1){
              
             
                 foreach($get_shipping_detail as $shipping_detail){
                  
                 if($cart_product_quantity->weight >= $shipping_detail->weight_start && $cart_product_quantity->weight <= $shipping_detail->weight_end  ){
                 
                     $cart_product_quantity->shipping = $shipping_detail->cost;
                
                
            } else if($cart_product_quantity->weight >= $shipping_detail->weight_start && $shipping_detail->weight_end == "") {
               
                $cart_product_quantity->shipping = $shipping_detail->cost;
              
            }
            
                }
            } else {
               
                foreach($get_shipping_detail as $shipping_detail){
                 
                if($cart_product_quantity->qty == $shipping_detail->items ){
                    
              $cart_product_quantity->shipping = $shipping_detail->cost;
                
            } else if( $cart_product_quantity->qty >= 3 && $shipping_detail->items >= 3){
                
               $cart_product_quantity->shipping = $shipping_detail->cost;
              
                 
            }
            
                }
                
            }
                    $cart_product_quantity->save();
                    
                    return response()->json([
                        "response" => "Quantity Updated Successfully"
                        ]);
                        
                    }
                    
                } else {
                    
                    return response()->json([
                        "response" => "Item does not exist"
                        ],404); 
        }
    }
}

    public function get_cart_product(Request $request){
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
                
        ]);
        
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            $user_id = $request->user_id;
            $cart_id = DB::table('cart')->where('user_id', $user_id)->pluck('id')->first();
            $cart_products = array();
            $totals = array(
                'subtotal' => 0,
                'shipping_total' => 0,
                'total' => 0
            );
                
            if($cart_id)
            {
                $shoppers = DB::table('cart')
                ->join('cart_products', 'cart.id', '=', 'cart_products.cart_id')
                ->join('shop_profiles', 'cart_products.seller_id', '=', 'shop_profiles.id')
                ->select('shop_profiles.id', 'shop_profiles.name', 'shop_profiles.shop_image_url as shop_image_url')
                ->distinct('cart_products.seller_id')
                ->where("cart.user_id", "=", $user_id)
                ->get();
                
                if(count($shoppers) > 0)
                {   
                    foreach($shoppers as $shopper)
                    {
                        $shopper_cart_products = DB::table('cart_products')
                        ->leftJoin('shop_products', 'shop_products.id', '=', 'cart_products.shop_product_id')
                        ->leftJoin('shop_product_files', 'shop_product_files.shop_product_id', '=', 'shop_products.id')
                        ->select('shop_products.id', 'shop_products.name', 'shop_products.artist_name', 'shop_products.shop_profile_id as shopper_id', 'shop_product_files.thumb', 'cart_products.qty', 'cart_products.size', 'cart_products.weight', 'cart_products.price', 'cart_products.shipping')
                        ->where([['seller_id', $shopper->id], ['cart_id', $cart_id]])
                        ->groupby('cart_products.shop_product_id')
                        ->get();
                        
                        $subtotal = $shopper_cart_products->sum('price');
                        $shipping = $shopper_cart_products->sum('shipping');
                        $total = $shopper_cart_products->sum('price')+$shopper_cart_products->sum('shipping');
                        
                        $array = array(
                            'shopper' => $shopper,
                            'products' => $shopper_cart_products,
                            'subtotal' => $subtotal,
                            'shipping' => $shipping,
                            'total' => $total,
                        );
                        
                        $cart_products[] = $array;
                        $totals['subtotal'] = $totals['subtotal']+$subtotal;
                        $totals['shipping_total'] = $totals['shipping_total']+$shipping;
                        $totals['total'] = $totals['total']+$total;
                    }
                }
            }

            return response()->json(['success' => 'true', 'message' => 'Cart Products', 'cart_id' => $cart_id, 'data' => $cart_products, 'order_totals' => $totals]);
        }
    }

    public function shop_view_toggle(Request $request)
    {
         $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
              $shop_view = User::find($request->user_id);
            
               if($shop_view->shop_view_toggle == 0)
               {
            
              $shop_view->shop_view_toggle = 1;
               
               } else {
                   
                $shop_view->shop_view_toggle = 0;
                   
               }
               $shop_view->save();
        }
        
        return response()->json([
                        "response" => "Shop view toggle updated"
                        ]);
    }
    
    public function place_order(Request $request){
        
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'transaction_fee' => 'required',
                
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
            
        } else {
            
            $user_cart = Cart::where('user_id',$request->user_id)->first();
            
            $cart_data = array(
                
                'user_id' => $user_cart->user_id,
                'support_fee' => 100,
                'order_subtotal' => $user_cart->sub_total,
                'shipping_total' => $user_cart->shipping_total,
                'total' => $user_cart->total,
                'transaction_id' => 32472,
                'transaction_fee' => $request->transaction_fee,
                'status' => 0 ,
                'created_at' => date('Y-m-d H:i:s'),
                'deleted_at' => NULL
                );
                
           $order_id = DB::table('orders')->insertGetId($cart_data);
            
           $cart_products = CartProduct::where(array('cart_id'=>$user_cart->id))->get()->toArray();
           
           foreach($cart_products as $order_details)
           {
               
              $data[] = array(
                   
                  'order_id' => $order_id,
                  'shopper_id' => $order_details['seller_id'],
                  'product_id' => $order_details['shop_product_id'],
                  'size' => $order_details['size'],
                  'weight' => $order_details['weight'],
                  'quantity' => $order_details['qty'],
                  'price' => $order_details['price'],
                  'shipping' => $order_details['shipping']
                  );         
           }
           
           DB::table('order_details')->insert($data);
           
           $cart = Cart::where('user_id', $request->user_id)->delete();
           $cart_products = CartProduct::where(array('cart_id'=>$user_cart->id))->delete();
           
          return response()->json([
                        "response" => "Order Placed Successfully"
                        ]);
        }
    }
    
    public function total_cart_products(Request $request){
        
         $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
            
        } else {
            
            $cart_id = DB::table('cart')->where('user_id', $request->user_id)->select('id')->first();
            $cart_products = DB::table('cart_products')->where('cart_id', $cart_id->id)->select('qty')->get();
            
            $total_products = 0;
            foreach($cart_products as $user_total_products)
            {
                $total_products += $user_total_products->qty;
            }
            return $this->sendResponse($total_products, 'User total products added in cart.');
        }
    }
    
    public function email_seller(Request $request){
        
        $validator = Validator::make($request->all(), [
                'email' => 'required',
                
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
            
        } else {
        
                $to = $request->email;
                $body = $request->body;
                $subject = "Inquiry";
                $message = '<!doctype html>
                <html>
                <head>
                    <title>Email template</title>
                    <link rel="stylesheet" href="css/screen.css">
                </head>
                <body height="100%">
                    <table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0"> 
                        <tbody>
                            <tr>
                                <td>
                                    <p style="text-align: center; padding-bottom:40px;">
                                        <img src="http://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" />
                                    </p> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="text-align: center; color:#666;">
                                        <span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">WELCOME TO ARTfora</span>
                                    </p>
                                </td>
                            </tr>
                             <tr>
                                <td>
                                    <p style="text-align: center; color:#666;">
                                        <span style="font-size: 13px; font-family: Prozak, sans-serif; letter-spacing: 5px;">'.$body.'</span>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                 </body>
                </html>';
            
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
                
                mail($to, $subject, $message, $headers);
                
                     return response()->json([
                        "response" => "Email sent Successfully"
                        ]);
        }
    }
    
    public function email_invoice(Request $request){
        
        $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'user_email' => 'required'
                
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
            
        } else {
        
                $to = $request->user_email;
                $order_id = $request->order_id; 
                $shopper = DB::table('order_details')->where('order_id', $order_id)->groupBy('shopper_id')->pluck('shopper_id')->toArray();
                $order_created = DB::table('orders')->where('id', $order_id)->select('created_at')->first();
                
                $subject = "Invoice";
                $message = '<!doctype html>
                <html>
                <head>
                    <title>Email template</title>
                    <link rel="stylesheet" href="css/screen.css">
                </head>
                <body height="100%">
                    <table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0"> 
                        <tbody>
                            <tr>
                                <td>
                                    <p style="text-align: center; padding-bottom:40px;">
                                        <img src="http://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" />
                                    </p> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="text-align: center; color:#666;">
                                        <span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">Order Invoice</span>
                                    </p>
                                </td>
                            </tr>
                             <span style="padding-left:48px;">
                                 <b>Order Id:</b> '.$order_id.'
                                 </span>
                                 <span style="float:right; padding-right:40px;">
                                 <b>Created at:</b> '.$order_created->created_at.'
                                 </span>
                             <table style="width: 500px; height: 80%; margin-left: auto; margin-right: auto; ">
                             <tbody>';
                             
                              $final_subtotal = 0;
                               $final_shipping = 0;
                               $final_total = 0;
                             for($i =0 ; $i<count($shopper) ; $i++){
                               $price = 0;
                               $shipping = 0;
                               $total = 0;
                              
                            $invoice_products = DB::table('order_details')->where('shopper_id', $shopper[$i])->get();
                            $shopper_name = DB::table('shop_profiles')->where('id', $shopper[$i])->first();
                          
                              $message .=    
                
                                 '
                                 <hr style="width:710%;text-align:left;margin-left:0"> 
                                 <tr>
                                 <td colspan="7" ><b>Shopper Name: </b>'.$shopper_name->name.'</td></tr>
                                <tr>
                                <td  style="width:70px;"><b>Item ID</b></td>
                                <td><b>Item Name</b></td>
                                <td><b>Item Size</b></td>
                                <td><b>Item Weight</b></td>
                                <td><b>Shipping</b></td>
                                <td><b>Qty</b></td>
                                <td><b>Price</b></td>
                                </tr>';
                                
                                foreach($invoice_products as $invoice){
                                    
                                $product_name = ShopProduct::where(array('shop_profile_id' => $shopper[$i], 'id' => $invoice->product_id))->first();

                                $message .= '<tr>
                                <td >'.$invoice->product_id.'</td>
                                <td >'.$product_name->name.'</td>
                                <td >'.$invoice->size.'</td>
                                <td >'.$invoice->weight.'</td>
                                <td >'.$invoice->shipping.'</td>
                                <td >'.$invoice->quantity.'</td>
                                <td >'.$invoice->price.'</td>
                                </tr>
                                
                                ';
                                $price = $price + $invoice->price * $invoice->quantity ;
                                $shipping = $shipping + $invoice->shipping;
                                
                                }
                                // die;
                                $total = $price + $shipping;
                                
                                $message .=  '
                                <tr style= "text-align: left;" >
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Subtotal</b></td>
                                <td>'.$price.'</td>
                                </tr>
                                <tr style= "text-align: left;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Shipping</b></td>
                                <td> '.$shipping.'</td>
                                </tr>
                                <tr style= "text-align: left;">
                                 
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Total from seller</b></td>
                                <td> '.$total.' </td>
                                </tr>
                               
                                ';
                                
                                $final_subtotal += $price;
                                $final_shipping += $shipping;
                                $final_total += $total;
                                }
        
                               $message .=  ' 
                               <hr style="width:710%;" >
                                <tr style= "text-align: left;" >
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th >Order Subtotal    <td> '.$final_subtotal.'</td></th>
                                </tr>
                                <tr style= "text-align: left;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th>Shipping Total    <td> '.$final_shipping.'</td></th>
                                </tr>
                                <tr style= "text-align: left;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th>Order Total    <td> '.$final_total.'</td></th>
                                </tr>
                                
                                </tbody>
                                                </table>
                            
                        </tbody>
                    </table>
                 </body>
                </html>';
            
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
                
                mail($to, $subject, $message, $headers);
                
                     return response()->json([
                        "response" => "Email sent Successfully"
                        ]);
                
        }

    }
    
    public function getOrders(Request $request){
        
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
            
        } else {
            
            $order_detail = array();
            $get_orders = DB::table('orders')->where('user_id', $request->user_id)->select('id','created_at')->get();
            
            foreach($get_orders as $order)
            {   
                $shopper_array = array();
                $get_order_shoppers = DB::table('order_details')->where('order_id', $order->id)->groupBy('shopper_id')->pluck('shopper_id')->toArray();
                
                foreach($get_order_shoppers as $shopper)
                {
                    $order_products = DB::table('order_details')
                                        ->join('shop_products as sp', 'sp.id', '=', 'order_details.product_id')
                                        ->select('sp.id', 'sp.name', 'order_details.size', 'order_details.weight', 'order_details.order_id', 'order_details.quantity', 'order_details.shipping', 'order_details.price')
                                        ->where([['shopper_id', $shopper], ['order_id', $order->id]])->get();
                    $shopper_array[] = array(
                        'shopper_id' => $shopper,
                        'shopper_name' => DB::table('shop_profiles')->where('id', $shopper)->pluck('company_name')->first(),
                        'items' => $order_products,
                        'product_rating' => DB::table('product_ratings')->where('order_id', $order->id)->pluck('product_rating')->first(),
                        'delivery_rating' => DB::table('product_ratings')->where('order_id', $order->id)->pluck('delivery_rating')->first(),
                        'seller_rating' => DB::table('seller_ratings')->where('order_id', $order->id)->pluck('seller_rating')->first()
                    );
                }
                
                $orders_array[] = array(
                    'order_id' => $order->id,
                    'created_at' => $order->created_at,
                    'items' => $shopper_array
                );
            }
            return $this->sendResponse($orders_array, 'Total ordered products.');
        }
    }
    
    public function product_rating(Request $request){
        
         $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'order_id' => 'required',
        ]);
        
         if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
            
        } else {
            
            $all_ratings = isset($request['review_ratings']) ? $request['review_ratings'] : array();
            
            for( $i=0; $i<count($all_ratings);$i++)
            {
                
                $products = $all_ratings[$i]['product_ids'];
                $product_ids = explode(",",$products);
                
                for($j=0; $j< count($product_ids) ; $j++){
                    
                $product_ratings_data[] = array(
                    "user_id" => $request->user_id,
                    "order_id" => $request->order_id,
                    "seller_id" => $all_ratings[$i]['seller_id'],
                    "product_id" => $product_ids[$j],
                    "product_rating" => $all_ratings[$i]['product_rating'],
                    "product_review" => $all_ratings[$i]['product_review'],
                    "delivery_rating" => $all_ratings[$i]['delivery_rating'],
                    "delivery_review" => $all_ratings[$i]['delivery_review'],
                   
                );
                
                $product_info = ShopProduct::where(array('shop_profile_id' => $all_ratings[$i]['seller_id'], 'id' => $product_ids[$j]))->first();
                    
                $product_info->rating = ($product_info->rating + $all_ratings[$i]['product_rating'])/2;
                $product_info->delivery = ($product_info->delivery + $all_ratings[$i]['delivery_rating'])/2;
                $product_info->save();
                }
                
                $seller_ratings_data[] = array(
                    
                    "user_id" => $request->user_id,
                    "order_id" => $request->order_id,
                    "seller_id" => $all_ratings[$i]['seller_id'],
                    "seller_rating" => $all_ratings[$i]['seller_rating'],
                    "seller_review" => $all_ratings[$i]['seller_review'],
                    );
                    
                $shop_detail = DB::table('shop_profiles')->where('id', $all_ratings[$i]['seller_id'])->first();
                
                $shop_rating = DB::table('shop_profiles')
                               ->where('id', $all_ratings[$i]['seller_id'])
                               ->update(array('rating'=> ($shop_detail->rating + $all_ratings[$i]['seller_rating'])/2 ));
             
            }
            
            DB::table('product_ratings')->insert($product_ratings_data);
            DB::table('seller_ratings')->insert($seller_ratings_data);
            
             return response()->json([
                        "response" => "Review added successfully"
                        ]);
            // return $this->sendResponse($seller_ratings_data, 'Total ordered products.');
        }
    }
    
     public function view_order(Request $request){
        
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
                
        ]);
        
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
            
            // $cart_id = DB::table('cart')->where('user_id', $user_id)->pluck('id')->first();
            $order_id = $request->order_id;
            $order_products = array();
            $totals = array(
                'subtotal' => 0,
                'shipping_total' => 0,
                'total' => 0
            );
                
            if($order_id)
            {
                $shoppers = DB::table('orders')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->join('shop_profiles', 'order_details.shopper_id', '=', 'shop_profiles.id')
                ->select('shop_profiles.id', 'shop_profiles.name', 'shop_profiles.shop_image_url as shop_image_url')
                ->distinct('order_details.shopper_id')
                ->where("orders.id", "=", $order_id)
                ->get();
                // return $shoppers;
                if(count($shoppers) > 0)
                {   
                    foreach($shoppers as $shopper)
                    {
                        $shopper_order_products = DB::table('order_details')
                        ->leftJoin('shop_products', 'shop_products.id', '=', 'order_details.product_id')
                        ->leftJoin('shop_product_files', 'shop_product_files.shop_product_id', '=', 'shop_products.id')
                        ->select('shop_products.id', 'shop_products.name', 'shop_products.artist_name', 'shop_products.shop_profile_id as shopper_id', 'shop_product_files.thumb', 'order_details.quantity', 'order_details.size', 'order_details.weight', 'order_details.price', 'order_details.shipping')
                        ->where([['shopper_id', $shopper->id], ['order_id', $order_id]])
                        ->groupby('order_details.product_id')
                        ->get();
                        
                        $subtotal = $shopper_order_products->sum('price');
                        $shipping = $shopper_order_products->sum('shipping');
                        $total = $shopper_order_products->sum('price')+$shopper_order_products->sum('shipping');
                        
                        $array = array(
                            'shopper' => $shopper,
                            'products' => $shopper_order_products,
                            'subtotal' => $subtotal,
                            'shipping' => $shipping,
                            'total' => $total,
                        );
                        
                        $order_products[] = $array;
                        $totals['subtotal'] = $totals['subtotal']+$subtotal;
                        $totals['shipping_total'] = $totals['shipping_total']+$shipping;
                        $totals['total'] = $totals['total']+$total;
                    }
                }
            }

            return response()->json(['success' => 'true', 'message' => 'Ordered Products', 'order_id' => $order_id, 'data' => $order_products, 'order_totals' => $totals]);
        }
    }
    
    public function get_shop_product(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_profile_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {
        
        $user_id = $request->user_id;
        $my_product_info_data = ShopProduct::with('shop_product_details', 'shop_product_files')
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
            ]);
        $product_count = ShopProduct::where('shop_profile_id',$request->shop_profile_id)->count();
        $shop_name = ShopProfile::where('id',$request->shop_profile_id)->first();
        if($request->type != 'all')
        {
            $type_arr = explode(',', $request->type);
            $my_product_info_data->whereIn('type',$type_arr);
        }
            
        if($request->for_sale == '1') //Data for sale only
        {
            $my_product_info_data->where('sale', 'yes');
        }
		
		if($request->status == 'all'){
			$my_product_info_data->where('deleted_at',NULL);
		}
		else if($request->status == 'pending'){
			$my_product_info_data->where('status',0);
			$my_product_info_data->where('deleted_at',NULL);
		}
		else if($request->status == 'approved'){
			$my_product_info_data->where('status',1);
			$my_product_info_data->where('deleted_at',NULL);
		}
		else if($request->status == 'noapproved'){
			$my_product_info_data->where('deleted_at','!=',NULL);
		} else{
			
		}

        if($request->role=='admin'){
			//->where('status',0)
            $my_product_info_data->offset($request->offset)->limit($request->limit);
        }else{
			//->where('status',0)
			$my_product_info_data->where('shop_profile_id',$request->shop_profile_id)->offset($request->offset)->limit($request->limit);
        }
		if($request->sort_by){
			$my_product_info = $my_product_info_data->inRandomOrder()->get();
		} else{
			$my_product_info = $my_product_info_data->orderBy('updated_at', 'DESC')->get();
		}
        for($i=0;$i<count($my_product_info);$i++) 
        {
            $get_profile = DB::table('shop_profiles')->where('user_id',  $my_product_info[$i]->user_id)->select('name', 'rating', 'shop_image_url')->first();
            $user = DB::table('users')->where('id',  $my_product_info[$i]->user_id)->select('tagname')->first();
            $profile = DB::table('user_detail')->where('user_id',  $my_product_info[$i]->user_id)->select('profile_image_url','category')->first();
            $saGetRepostsCount = \App\SharePost::select('user_id')->where('feed_id',$my_product_info[$i]->id)->count();
            
            $my_product_info[$i]['posted_by'] = User::where('id', $my_product_info[$i]->user_id)->pluck('name')->first();
            $my_product_info[$i]['seller_rating'] = $get_profile->rating;
            $my_product_info[$i]['profile_image_url'] = $get_profile->shop_image_url;
            $my_product_info[$i]['tagname'] = isset($user->tagname)?$user->tagname:"";
            $my_product_info[$i]['profile_image_url'] = isset($profile->profile_image_url)?$profile->profile_image_url:"";
			if(empty($request->shop_profile_id)){
				$shop_name = ShopProfile::where('id',$my_product_info[$i]->shop_profile_id)->first();
			}
            $my_product_info[$i]['shop_name'] = $shop_name->shop_name??"";
            $my_product_info[$i]['shop_image_url'] = $shop_name->shop_image_url??"";
            $my_product_info[$i]['shop_category'] = $profile->category??"";
            $my_product_info[$i]['reposts'] = $saGetRepostsCount;
        }
        
        return response()->json(['success' => 'true', 'message' => 'My Products', 'Total Products' => $product_count, 'Data' => $my_product_info]);
        }
    }


    public function get_product_count(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } else {

        	$product_count_data = DB::table('shop_products')
			                        ->where('id', '=', $request->product_id)
			                        ->first();

			$data = [];
			$data['product_id'] = $request->product_id;
			$data['read_count'] = $product_count_data->read_count;
        
        	return response()->json(['success' => 'true', 'message' => 'Product Count', 'Data' => $data]);
        }
    }
    

    /*==============================================
                SHOP PRODUCT META
    ==============================================*/

    public function shop_product_meta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_product_id' => 'required',
            'user_id' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails())
        {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
        else 
        {
			$type = "";
            if (($request->type == 'like') ||  ($request->type == 'save'))
            {   
                ShopProductMeta::create($request->all());
                $type  = ($request->type == 'like') ? 'Liked' : 'Saved';
                //return $this->sendResponse($saData, $type);
				if($request->type == 'like'){
					$product_info = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
								$query->leftJoin('users', 'users.id', '=', 'shop_profiles.user_id')
								->select('shop_profiles.id', 'shop_profiles.shop_name', 'shop_profiles.company_name', 'users.email', 'shop_profiles.shop_image_url', 'users.name as user_name', 'users.tagname as user_tagname');
					}])				
					->leftJoin('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
					->where('shop_products.id', $request->shop_product_id)->where('shop_profiles.deleted_at', NULL);
					$get_info = $product_info->first();
					
					$is_repost = "";
					if($request->is_repost){
						$is_repost = 're';
					}
					$message = $request->u_name. ' liked your '.$is_repost.'post.';				
					$this->saveActivity($request->user_id,$get_info->user_id,$request->shop_product_id,'liked',$message);
				}
				$message = $type;
            }
            else if ($request->type == 'dislike')
            {
                ShopProductMeta::where([['user_id', $request->user_id], ['shop_product_id', $request->shop_product_id], ['type', 'like']])->delete();
                //return $this->sendResponse($saData, 'DisLiked');
				$message = 'DisLiked';
            }
            else if ($request->type == 'unsave')
            {
                ShopProductMeta::where([['user_id', $request->user_id], ['shop_product_id', $request->shop_product_id], ['type', 'save']])->forceDelete();
                //return $this->sendResponse($saData, 'Unsaved');
				$message = 'Unsaved';
            }
            else{
                return $this->sendError('Please Select Valid Status', array());
            }
			
			$user_id = $request->user_id;
			$feed_id = $request->shop_product_id;
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
			
			return $this->sendResponse($saData, $message);
        }
    }

	

    /*==============================================
                SHOP PRODUCT COMMENT
    ==============================================*/

    public function shop_product_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_product_id' => 'required',
            'user_id' => 'required',
            'is_repost' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails())
        {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
        else 
        {
            $request['type'] = 'comment';
			$user_id = $request->user_id??0;
            ShopProductMeta::create($request->all());
			$product_info = ShopProduct::with(['shop_product_details', 'shop_product_files', 'shop_profiles' => function($query){
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
            ->leftJoin('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
            ->where('shop_products.id', $request->shop_product_id)->where('shop_profiles.deleted_at', NULL);
            $product_info = $product_info->first();
			
			$get_profile = DB::table('shop_profiles')->where('user_id',  $product_info->user_id)->select('name', 'rating')->first();
			$product_info->posted_by = User::where('id', $product_info->user_id)->pluck('name')->first();
			$product_info->seller_rating = $get_profile->rating;
			$shop_name = ShopProfile::where('id',$product_info->shop_profile_id)->first();
			$product_info->shop_name = $shop_name->shop_name??"";
			$product_info->is_repost = (int)$shop_name->is_repost;
			
			if($request->is_repost){
				$share_post = SharePost::where('feed_id',$request->shop_product_id)->where('feed_type',0)->get();
				foreach($share_post as $value){
					$message = 'Commented from repost from '.$shop_name->shop_name;	
					$this->saveActivity($request->user_id,$value->user_id,$request->shop_product_id,'comment',$message,$request->is_repost);
				}
			}
				
			$message = 'Commented from post from '.$shop_name->shop_name;	
			$this->saveActivity($request->user_id,$product_info->user_id,$request->shop_product_id,'comment',$message,$request->is_repost);	
			
            return $this->sendResponse($product_info, 'Comment is added.');
        }
    }



    /*==============================================
                GET SHOP PRODUCT COMMENTS
    ==============================================*/

    public function get_shop_product_comments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_product_id' => 'required',
        ]);

        if ($validator->fails())
        {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
        else 
        {
            $comments = ShopProductMeta::select('users.name', 'user_detail.profile_image_url as image', 'user_detail.category', 'shop_product_metas.user_id', 'shop_product_metas.id as comment_id','shop_product_metas.comment', 'shop_product_metas.created_at as date_time')
                            ->leftJoin('users', 'shop_product_metas.user_id', '=', 'users.id')
                            ->leftJoin('user_detail', 'shop_product_metas.user_id', '=', 'user_detail.user_id')
                            ->where([['shop_product_metas.shop_product_id', $request->shop_product_id], ['type', 'comment']])->orderBy('shop_product_metas.id', 'DESC')->get();
            foreach($comments as $postComment) {
                $postComment->time_ago = $this->time_diff($postComment->date_time);
            }
            return $this->sendResponse($comments, 'Shop Product Comments Listed Successfully.');
        }
    }

	public function product_approve(Request $request)
	{
		$result=DB::table('shop_products')->where('id', $request->product_id)->update(array('status'=>1,'approval_date'=>date('Y-m-d H:i:s')));
		return response()->json(['success' => $result, 'message' => 'Product has been approved']);
	}
	
	public function product_delete(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails())
        {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        } 
		$result = DB::table('shop_products')->where('id', $request->product_id)->where('user_id', $request->user_id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
		if($result){
			return response()->json(['success' => $result, 'message' => 'Product has been deleted']);
		} else{
			return response()->json(['success' => $result, 'message' => 'Something went wrong']);
		}
	}
	
	public function saveActivity($user_id,$profile_id=0,$object_id,$type,$message="",$is_repost=0) 
	{		
        $activity_status = array('user_id'=>$user_id,'profile_id' =>$profile_id,'object_id'=>$object_id,'type'=>$type,'message'=>$message,'is_repost'=>$is_repost);
		$invite = ActivityLog::create($activity_status);
    }
	
	public function custom_email($to, $from, $subject, $message)
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@artfora.net\nX-Mailer: PHP/";
        //mail($to, $subject, $html, $headers);   
		$data = array('email' => $to, 'subject' => $subject, 'message' => $message);
		\Mail::to($to)->send(new SendMail($data));
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
    
}
