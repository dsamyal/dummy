<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;
use DB;

class JobController extends BaseController {

    public function createJob(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'job_title' => 'required',
                    'i_am' => 'required',
                    'looking_for' => 'required',
                    'location' => 'required',
                    'from_date_time' => 'required',
                    'end_date_time' => 'required',
                    'image' => 'required',
                    'description' => 'required',
                    'payment' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        if (!empty($request['image'])) {
            if ($request->hasFile('image')) {
                $ssJobImage = $request->file('image');
                $ssImgName = time() . $ssJobImage->getClientOriginalName();
                $destinationPath1 = public_path('/images/Job/');
                $ssJobImage->move($destinationPath1, $ssImgName);
            }
        } else {
            $ssImgName = null;
        }
        $snJobId = DB::table('jobs')->insertGetId(
                ['user_id' => $request->user_id, 'job_title' => trim($request->job_title), 'i_am' => trim($request->i_am), 'looking_for' => trim($request->looking_for), 'location' => trim($request->location), 'from_date_time' => $request->from_date_time, 'end_date_time' => $request->end_date_time, 'image' => $ssImgName, 'description' => trim($request->description), 'payment' => trim($request->payment)]
        );
        if (!empty($request->style)) {
            $saExploadeStyle = explode(',', $request->style);
            foreach ($saExploadeStyle As $saStyle) {
                DB::table('jobs_style')->insertGetId(
                        ['job_id' => $snJobId, 'style' => trim($saStyle)]
                );
            }
        }
        if (!empty($request->haircolor)) {
            $saExploadeHair = explode(',', $request->haircolor);
            foreach ($saExploadeHair As $saHairColor) {
                DB::table('job_hair_color')->insertGetId(
                        ['job_id' => $snJobId, 'hair_color' => trim($saHairColor)]
                );
            }
        }
        if (!empty($request->skincolor)) {
            $saExplodeSkin = explode(',', $request->skincolor);
            foreach ($saExplodeSkin As $saSkinColor) {
                DB::table('job_skin_color')->insertGetId(
                        ['job_id' => $snJobId, 'skin_color' => trim($saSkinColor)]
                );
            }
        }
        $saData['id'] = $snJobId;
        return $this->sendResponse($saData, 'Job created successfully.');
    }

    public function updateJob(Request $request) {
        $validator = Validator::make($request->all(), [
                    'job_id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        $saCheckJob = \App\Jobs::where('id', $request->job_id)->first();
        if (!empty($saCheckJob)) {
            if (!empty($request->job_title)) {
                $saCheckJob->job_title = $request->job_title;
            }
            if (!empty($request->i_am)) {
                $saCheckJob->i_am = $request->i_am;
            }
            if (!empty($request->looking_for)) {
                $saCheckJob->looking_for = $request->looking_for;
            }
            if (!empty($request->location)) {
                $saCheckJob->location = $request->location;
            }
            if (!empty($request->from_date_time)) {
                $saCheckJob->from_date_time = $request->from_date_time;
            }
            if (!empty($request->end_date_time)) {
                $saCheckJob->end_date_time = $request->end_date_time;
            }
            if (!empty($request->description)) {
                $saCheckJob->description = $request->description;
            }
            if (!empty($request->payment)) {
                $saCheckJob->payment = $request->payment;
            }
            if (!empty($request['image'])) {
                if ($request->hasFile('image')) {
                    $ssJobImage = $request->file('image');
                    $ssImgName = time() . $ssJobImage->getClientOriginalName();
                    $destinationPath1 = public_path('/images/Job/');
                    $ssJobImage->move($destinationPath1, $ssImgName);

                    $saCheckJob->image = $ssImgName;
                }
            }
            $saCheckJob->save();
            if (!empty($request->style)) {
                DB::table('jobs_style')->where('job_id', $request->job_id)->delete();
                $saExploadeStyle = explode(',', $request->style);
                foreach ($saExploadeStyle As $saStyle) {
                    DB::table('jobs_style')->insertGetId(
                            ['job_id' => $request->job_id, 'style' => trim($saStyle)]
                    );
                }
            }
            if (!empty($request->haircolor)) {
                DB::table('job_hair_color')->where('job_id', $request->job_id)->delete();
                $saExploadeHair = explode(',', $request->haircolor);
                foreach ($saExploadeHair As $saHairColor) {
                    DB::table('job_hair_color')->insertGetId(
                            ['job_id' => $request->job_id, 'hair_color' => trim($saHairColor)]
                    );
                }
            }
            if (!empty($request->skincolor)) {
                DB::table('job_skin_color')->where('job_id', $request->job_id)->delete();
                $saExplodeSkin = explode(',', $request->skincolor);
                foreach ($saExplodeSkin As $saSkinColor) {
                    DB::table('job_skin_color')->insertGetId(
                            ['job_id' => $request->job_id, 'skin_color' => trim($saSkinColor)]
                    );
                }
            }
            return $this->sendResponse(array(), 'Job Updated successfully.');
        } else {
            return $this->sendError('Job not found', array());
        }
    }

    public function jobSearch(Request $request) {
		
		$errors_array = array();
		if ($request->input('filter') == 1){
			$validator = Validator::make($request->all(), [
                   // 'user_id' => 'required',              
                    'looking_for' => 'required',
        ]);
		}else if( $request->input('filter') == "" || !$request->has('filter')){
			$validator = Validator::make($request->all(), [
                   // 'user_id' => 'required',
                   // 'i_am' => 'required',
                    'looking_for' => 'required',
        ]);
		}
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        // $saFindJobLike = DB::table('jobs')
                       // ->select('id')
                        // ->where('user_id', $request->user_id)
                        // ->groupBy('id')
                        // ->get()->toArray();
						//print_r($saFindJobLike);
        //$saUnlikesLikes = array_column($saFindJobLike, 'id');
		//print_r($saUnlikesLikes);
		// if ($request->input('filter') == 1){
			// $saJobs = DB::table('jobs')
                // ->select('id')				
                // ->where('looking_for', $request->looking_for);
               //->where('user_id', $request->user_id);
                //->whereNotIn('id', $saUnlikesLikes);
		 // }
		  //else if( $request->input('filter') == "" || !$request->has('filter')){
			 // $saJobs = DB::table('jobs')
                // ->select('id')
				// ->where('i_am', $request->i_am)
                // ->where('looking_for', $request->looking_for)
                // ->where('user_id', '!=', $request->user_id);
                //->whereNotIn('id', $saUnlikesLikes);
		 // }
      // print_r($saJobs);
				$saJobs = DB::table('jobs');                       
				
        if (!empty($request->i_am)) {
            $saJobs->where('i_am', $request->i_am);
        }  
		if (!empty($request->user_id)) {
            $saJobs->where('user_id', $request->user_id);
        } 
		if (!empty($request->location)) {
            $saJobs->where('location', $request->location);
        }
        if (!empty($request->from_date_time)) {
            $saJobs->where('from_date_time', '>=', $request->from_date_time);
        }
        if (!empty($request->end_date_time)) {
            $saJobs->where('from_date_time', '<=', $request->end_date_time);
        }
        $saresult = $saJobs->get()->toArray();
		//print_r($saresult );
        $saGetJobId = array_column($saresult, 'id');
        //Check Style
        if (!empty($request->style)) {
            $saExploadeStyle = explode(',', $request->style);
            $saStyle = DB::table('jobs_style')->select('job_id')->whereIn('job_id', $saGetJobId);
            $trim = array();
            foreach ($saExploadeStyle As $saStyles) {
                $trim[] = trim($saStyles);
            }
            $saStyle->whereIn('style', $trim);
            $saStyle->groupBy('job_id');
            $saStyleResult = $saStyle->get()->toArray();

            $saStyleJob = array_column($saStyleResult, 'job_id');
        } else {
            $saStyleJob = $saGetJobId;
        }
        //Check Hair

        if (!empty($request->haircolor)) {
            $saExploadeHair = explode(',', $request->haircolor);
            $saHair = DB::table('job_hair_color')->select('job_id')->whereIn('job_id', $saStyleJob);

            $trimHair = array();
            foreach ($saExploadeHair As $saHairs) {
                $trimHair[] = trim($saHairs);
            }

            $saHair->whereIn('hair_color', $trimHair);
            $saHair->groupBy('job_id');
            $saHairResult = $saHair->get()->toArray();

            $saHairJob = array_column($saHairResult, 'job_id');
        } else {
            $saHairJob = $saStyleJob;
        }

        //Check Skin
        if (!empty($request->skincolor)) {
            $saExploadeSkin = explode(',', $request->skincolor);
            $saSkin = DB::table('job_skin_color')->select('job_id')->whereIn('job_id', $saHairJob);

            $trimSkins = array();
            foreach ($saExploadeSkin As $saSkins) {
                $trimSkins[] = trim($saSkins);
            }

            $saSkin->whereIn('skin_color', $trimSkins);
            $saSkin->groupBy('job_id');
            $saSkinResult = $saSkin->get()->toArray();

            $saSkinJob = array_column($saSkinResult, 'job_id');
        } else {
            $saSkinJob = $saHairJob;
        }
        $ssUrl = url('/');
        $saJobs = DB::table('jobs')
                        ->select('jobs.id', 'jobs.job_title', 'users.id as user_id', 'users.name', 'user_detail.profile_image_url', 'user_detail.profile_image_url as profile_image', 'jobs.i_am', 'jobs.looking_for', 'jobs.location', 'jobs.from_date_time', 'jobs.end_date_time', DB::raw("CONCAT('https://artfora.net/images/Job','/',jobs.image) as image"), 'jobs.description', 'jobs.payment')
                        ->join('users', 'jobs.user_id', '=', 'users.id')
                        ->join('user_detail', 'jobs.user_id', '=', 'user_detail.user_id')
                        ->whereIn('jobs.id', $saSkinJob)
                        ->get()->toArray();

        for ($i = 0; count($saJobs) > $i; $i++) {
            //Style
            $saJobStyle = DB::table('jobs_style')
                            ->select('style')
                            ->where('job_id', $saJobs[$i]->id)
                            ->get()->toArray();
            $saStyleArray = array_column($saJobStyle, 'style');
            $saJobs[$i]->style = $saStyleArray;
            //Hair
            $saJobHair = DB::table('job_hair_color')
                            ->select('hair_color')
                            ->where('job_id', $saJobs[$i]->id)
                            ->get()->toArray();
            $saHairArray = array_column($saJobHair, 'hair_color');
            $saJobs[$i]->haircolor = $saHairArray;
            //Skin
            $saJobSkin = DB::table('job_skin_color')
                            ->select('skin_color')
                            ->where('job_id', $saJobs[$i]->id)
                            ->get()->toArray();
            $saSkinArray = array_column($saJobSkin, 'skin_color');
            $saJobs[$i]->skincolor = $saSkinArray;
        }
        return $this->sendResponse($saJobs, 'Job Listed successfully.');
    }

    public function myJob(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        $ssUrl = url('/');
        $saJobs = DB::table('jobs')
                        ->select('id', 'job_title', 'i_am', 'looking_for', 'location', 'from_date_time', 'end_date_time', DB::raw("CONCAT('https://artfora.net/images/Job','/',image) as image"), 'description', 'payment')
                        ->where('user_id', $request->user_id)
                        ->get()->toArray();

        for ($i = 0; count($saJobs) > $i; $i++) {
            //Style
            $saJobStyle = DB::table('jobs_style')
                            ->select('style')
                            ->where('job_id', $saJobs[$i]->id)
                            ->get()->toArray();
            $saStyleArray = array_column($saJobStyle, 'style');
            $saJobs[$i]->style = $saStyleArray;
            //Hair
            $saJobHair = DB::table('job_hair_color')
                            ->select('hair_color')
                            ->where('job_id', $saJobs[$i]->id)
                            ->get()->toArray();
            $saHairArray = array_column($saJobHair, 'hair_color');
            $saJobs[$i]->haircolor = $saHairArray;
            //Skin
            $saJobSkin = DB::table('job_skin_color')
                            ->select('skin_color')
                            ->where('job_id', $saJobs[$i]->id)
                            ->get()->toArray();
            $saSkinArray = array_column($saJobSkin, 'skin_color');
            $saJobs[$i]->skincolor = $saSkinArray;
        }
        return $this->sendResponse($saJobs, 'Job Listed successfully.');
    }

    public function jobLike(Request $request) {
        $validator = Validator::make($request->all(), [
                    'job_id' => 'required',
                    'user_id' => 'required',
                    'status' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }

        $saInsert = DB::table('job_likes')->insert(['job_id' => $request['job_id'], 'user_id' => $request['user_id'], 'status' => $request['status']]);

        $saData['likes'] = DB::table('job_likes')->where('user_id', $request['user_id'])->where('status', '1')->count();
        $saData['un-likes'] = DB::table('job_likes')->where('user_id', $request['user_id'])->where('status', '2')->count();
        $saData['ignore'] = DB::table('job_likes')->where('user_id', $request['user_id'])->where('status', '3')->count();
        if ($request['status'] == '1') {
            $ssMsg = 'Job liked';
        } elseif ($request['status'] == '2') {
            $ssMsg = 'Job un-liked';
        } else {
            $ssMsg = 'Job ignore';
        }
        return $this->sendResponse($saData, $ssMsg);
    }

    public function jobLikesList(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'status' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->sendError($error->first(), array());
        }
        if ($request['status'] == '1') {
            $ssMsg = 'Liked';
        } else {
            $ssMsg = 'Un-liked';
        }
        $saData = DB::table('job_likes')
                ->select('jobs.*', 'users.name', 'user_detail.profile_image_url')
                ->join('jobs', 'job_likes.job_id', '=', 'jobs.id')
                ->join('users', 'jobs.user_id', '=', 'users.id')
                ->join('user_detail', 'jobs.user_id', '=', 'user_detail.user_id')
                ->where('job_likes.user_id', $request['user_id'])
                ->where('job_likes.status', $request['status'])
                ->get()
                ->toArray();

        for ($i = 0; count($saData) > $i; $i++) {

            $saData[$i]->profile_image_url = $saData[$i]->profile_image_url;
            $saData[$i]->image = 'https://artfora.net/images/Job/' . $saData[$i]->image;
            $saStyleArray = DB::table('jobs_style')
                            ->select('style')
                            ->where('job_id', $saData[$i]->id)
                            ->get()->toArray();

            $saData[$i]->style = array_column($saStyleArray, 'style');

            $saHairArray = DB::table('job_hair_color')
                            ->select('hair_color')
                            ->where('job_id', $saData[$i]->id)
                            ->get()->toArray();

            $saData[$i]->haircolor = array_column($saHairArray, 'hair_color');

            $saSkinArray = DB::table('job_skin_color')
                            ->select('skin_color')
                            ->where('job_id', $saData[$i]->id)
                            ->get()->toArray();

            $saData[$i]->skincolor = array_column($saSkinArray, 'skin_color');
        }
//        die;

        return $this->sendResponse($saData, $ssMsg . ' Job Listed successfully.');
    }
	public function alljobs(){
		$userjobs = array();
		$countjobs = array();
		//if (!$request->has('categorysearch') || $request->input('categorysearch') == ""){
			$saSkinArray = DB::table('jobs')
		 // ->select('jobs.*','users.name','users.email','users.id','user_detail.profile_image_url','user_detail.category')
		 // ->join('users','users.id','=','jobs.user_id')
		 // ->join('user_detail','user_detail.user_id','=','jobs.user_id')
		 ->get();
	
		 
		 foreach($saSkinArray as $jobs){
			 $userjobs['id'] = $jobs->id;
			 $userjobs['job_title'] = $jobs->job_title;
			 $userjobs['i_am'] = $jobs->i_am;
			 $userjobs['looking_for'] = $jobs->looking_for;
			 $userjobs['location'] = $jobs->location;
			 $userjobs['from_date_time'] = $jobs->from_date_time;
			 $userjobs['end_date_time'] = $jobs->end_date_time;
			 $userjobs['image'] = 'https://artfora.net/images/Job/'.$jobs->image;
			 $userjobs['description'] = $jobs->description;
			 $userjobs['payment'] = $jobs->payment;
				$user = DB::table('users')
							->select('name','email','id')
							->where('id',$jobs->user_id)
							->get();													
				$user_detail = DB::table('user_detail')
							->select('profile_image_url','category')
							->where('user_id',$jobs->user_id)
							->get();
			$userjobs['user_detail'] = array(
								'name' => $user[0]->name,
								'email' => $user[0]->email,
								'user_id' => $user[0]->id,
								'profile_image_url'=>$user_detail[0]->profile_image_url,
								'category'=>$user_detail[0]->category,
							);
			$countjobs[] = $userjobs;
				
		 }
		 if($saSkinArray != null){
			return Response::json(array('error' => false, 'message' => 'All Jobs', 'request' => $countjobs), 202);
		 }else{            
			return Response::json(array('error' => true, 'errors' => 'Data not found'), 200);
		}	
    }
    



	public function alljobs1(Request $request){


      

		$userjobs = array();
		$countjobs = array();
		$categoryarray = array(
					'3D artist' => '3D artist',
					'Acting' => 'Acting',
					'Concept art' => 'Concept art',
					'Dance' => 'Dance',
					'Drawing' => 'Drawing',
					'Film/video' => 'Film/video',
					'Modelling' => 'Model',
					'Music/sound' => 'Music/sound',
					'Painting/aquarelle' => 'Painting/aquarelle',
					'Photography' => 'Photographer',
					'Sculpture' => 'Sculpture',
					'Writing' => 'Writing',
					'Styling (hair, make-up, etc)' => 'Style',
					
					);
		foreach($categoryarray as $key=>$velue){
			if($request->input('categorysearch') == $key){
				$category = $velue;
			}
		}

		
		if (!$request->has('categorysearch') || $request->input('categorysearch') == "" || $category == 'Style'){
			$saSkinArray = DB::table('jobs')
		 // ->select('jobs.*','users.name','users.email','users.id','user_detail.profile_image_url','user_detail.category')
		 // ->join('users','users.id','=','jobs.user_id')
		 // ->join('user_detail','user_detail.user_id','=','jobs.user_id')
		 ->get();
		}else if($category != 'Style'){
			$saSkinArray = DB::table('jobs')
			->where('looking_for', $category)
			->get();
		}
		 
		 foreach($saSkinArray as $jobs){
			 $userjobs['id'] = $jobs->id;
			 $userjobs['job_title'] = $jobs->job_title;
			 $userjobs['i_am'] = $jobs->i_am;
			 $userjobs['looking_for'] = $jobs->looking_for;
			 $userjobs['location'] = $jobs->location;
			 $userjobs['from_date_time'] = $jobs->from_date_time;
			 $userjobs['end_date_time'] = $jobs->end_date_time;
			 $userjobs['image'] = 'https://artfora.net/images/Job/'.$jobs->image;
			 $userjobs['description'] = $jobs->description;
			 $userjobs['payment'] = $jobs->payment;
				$user = DB::table('users')
							->select('name','email','id')
							->where('id',$jobs->user_id)
							->get();													
				$user_detail = DB::table('user_detail')
							->select('profile_image_url','category')
							->where('user_id',$jobs->user_id)
							->get();
			$userjobs['user_detail'] = array(
								'name' => $user[0]->name,
								'email' => $user[0]->email,
								'user_id' => $user[0]->id,
								'profile_image_url' => $user_detail[0]->profile_image_url,
								'category' => $user_detail[0]->category,
							);
			$countjobs[] = $userjobs;
				
		 }
		 if($saSkinArray != null){
			return Response::json(array('error' => false, 'message' => 'All Jobs', 'request' => $countjobs), 202);
		 }else{            
			return Response::json(array('error' => true, 'errors' => 'Data not found'), 200);
		}	
	}
	







public function alljobs2(Request $request){



		$userjobs = array();
		$countjobs = array();
	
    
     
        $category= $request->input('categorysearch');

     if($category == "All" || $category == "" || $category == "all")
   {
  return $data = DB::table('jobs')->get();
   }   
   else{
    $categoryarray = array(
        '3D artist' => '3D artist',
        'Acting' => 'Acting',
        'Concept art' => 'Concept art',
        'Dance' => 'Dance',
        'Drawing' => 'Drawing',
        'Film/video' => 'Film/video',
        'Modelling' => 'Model',
        'Music/sound' => 'Music/sound',
        'Painting/aquarelle' => 'Painting/aquarelle',
        'Photography' => 'Photographer',
        'Sculpture' => 'Sculpture',
        'Writing' => 'Writing',
        'Styling (hair, make-up, etc)' => 'Style',
        
        );
foreach($categoryarray as $key=>$velue){
if($request->input('categorysearch') == $key){
    $category = $velue;
}
}
    $data = DB::table('jobs')->where('looking_for', $category)->get();
   } 


													

     
    foreach($data as $jobs)
{
    $userjobs['id'] = $jobs->id;
    $userjobs['job_title'] = $jobs->job_title;
    $userjobs['i_am'] = $jobs->i_am;
    $userjobs['looking_for'] = $jobs->looking_for;
    $userjobs['location'] = $jobs->location;
    $userjobs['from_date_time'] = $jobs->from_date_time;
    $userjobs['end_date_time'] = $jobs->end_date_time;
    $userjobs['image'] = 'https://artfora.net/images/Job/'.$jobs->image;
    $userjobs['description'] = $jobs->description;
    $userjobs['payment'] = $jobs->payment;
  
    $user = DB::table('users')
    ->select('name','email','id')
    ->where('id',$jobs->id)
    ->get();													
$user_detail = DB::table('user_detail')
    ->select('profile_image_url','category')
    ->where('user_id',$jobs->id)
    ->get();
$userjobs['user_detail'] = array(
        'name' => $user[0]->name,
        'email' => $user[0]->email,
        'user_id' => $user[0]->id,
        'profile_image_url' => $user_detail[0]->profile_image_url,
        'category' => $user_detail[0]->category,
    );
  
    $countjobs[] = $userjobs;
}

if($data != null){
    return Response::json(array('error' => false, 'message' => 'All Jobs', 'request' => $countjobs), 202);
 }else{            
    return Response::json(array('error' => true, 'errors' => 'Data not found'), 200);
}	


}






}