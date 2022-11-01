<?php
namespace App\Http\Controllers;

use App\Post;
use App\ShopProductButton;
use App\ShopProductWebsiteButtonClick;
use App\User;
use App\UserWebsiteButtonClick;
use App\WebsiteButton;
use App\WebsiteButtonClick;
use Illuminate\Http\Request;
use App\Helper\FileHelper;
use App\Mail\ContactMail;
use Auth;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use App\ShopProduct;
use App\Category;
use DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;
use Crypt;
use Captcha;

class SiteController extends Controller
{
    public function index(Request $request, $username = false) {
        $filter_type ='';
        if(\Session::has('filter_type')) {
            $filter_type = \Session::get('filter_type');
        }
        
        $filter_user_id = 0;
        $filter_user_name = '';
        if(\Session::has('filter_user')) {
            $filter_user_id = \Session::get('user_value');

            // $filterUser = User::where(['id' =>$filter_user_id])->first();
            
            $shop_profiles = DB::table('shop_profiles')
                        ->where('user_id', '=', $filter_user_id)
                        ->first();
            $filter_user_name = $shop_profiles->shop_name;
        }

        if($username) {
            $filterUser = User::where(['tagname' => $username])->first();

            if($filterUser) {
                $filter_user_name = $username;
                $filter_user_id = $filterUser->id;

                $shop_profiles = DB::table('shop_profiles')
                        ->where('user_id', '=', $filter_user_id)
                        ->first();
                $filter_user_name = $shop_profiles->shop_name;

                $filter_type = '';
            }
        }

        /*$products = ShopProduct::with(['shop_profiles' => function($query){
            $query->select('id', 'name');
        }, 'shop_product_files' => function($query){
            $query->select('shop_product_id', 'thumb')->pluck('thumb')->first();
        }])->get();
        return \Session::get('gallery_type');*/

        $mainSearchFilter = '';
        if (($request->input('main-search-filter')) && ($request->input('main-search-filter')!='')) {
            $mainSearchFilter = $request->input('main-search-filter');
            \Session::put('main-search-filter',$mainSearchFilter);
        } else {
            if(\Session::has('main-search-filter')) {
                \Session::forget('main-search-filter');
            }
        }

        if($mainSearchFilter != ''){
            if(\Session::has('filter_value')) {
                \Session::forget('filter_value');
            }
            if(\Session::has('filter_type')) {
                \Session::forget('filter_type');
            }
            if(\Session::has('post_type')) {
                \Session::forget('post_type');
            }
        }


        // CHECK SESSION FOR GALLERY TOGGLE
        $gallery_type = 'random';
        $post_type = 'all';
        if(\Session::has('gallery_type')) {
            $gallery_type = \Session::get('gallery_type');
        }

        if(\Session::has('post_type')) {
            $post_type = \Session::get('post_type');
        }

        $filter_value ='All';
        if(\Session::has('filter_value')) {
            $filter_value = \Session::get('filter_value');
        }


        $filter_subvalue = (\Session::has('filter_subvalue'))
                            ? \Session::get('filter_subvalue')
                            : null;

        if(!empty($filter_subvalue)){
            $filter_subvalue = array_values(array_filter($filter_subvalue));
        } else {
            $filter_subvalue = null;
        }

        $subcategories = false;

        if($filter_value !== 'All') {
            $category = Category::where([
                'title' => $filter_value,
                'parent_id' => 0
            ])->first();

            if($category) {
                // $subcategories = $category->childs;
            }
        }

        // CHECK SESSION FOR GALLERY TOGGLE
        $user = auth()->user();

        $userId_for_filter = Auth::id();
        // $userId_for_filter = 9;
        // echo '<pre>'; print_r($userId);exit;
        $get_user_filter = DB::table('user_detail')->where('user_id', '=', $userId_for_filter)->first();
        // echo '<pre>'; print_r($get_user_filter->filter);exit;

        $products = ShopProduct::byApproveStatus()
                    ->leftJoin('shop_product_files', 'shop_product_files.shop_product_id', '=', 'shop_products.id')
                    ->leftJoin('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
                    ->leftJoin('users', 'users.id', '=', 'shop_products.user_id')
                    ->leftJoin('shop_product_details', 'shop_products.id', '=', 'shop_product_details.shop_product_id')
                    ->select('users.tagname', 'shop_products.artist_name', 'shop_products.id', 'shop_products.user_id', 'shop_products.name', 'shop_products.description', 'shop_products.type', 'shop_product_files.thumb', 'shop_products.price', 'shop_products.filter_id','shop_products.category_type as title', 'shop_products.quantity', 'shop_products.comment', 'shop_products.sale', 'shop_profiles.company_name', 'shop_profiles.shop_name', 'shop_profiles.shop_image_url','users.name as user_name')
                    ->groupby('shop_products.id');
        // if($user->role){
            // $products = $products->where('shop_products.status', 1); //Moved into scope with approval_date check
        // }
        $types= clone $products;
        $types = $types ->select('type');
        if (($filter_type == '') && ($filter_user_id > 0)) {
            $types->where('shop_products.user_id', $filter_user_id);
        }
        
        /* if (($filter_type != '') && ($filter_user_id == 0)) {
            $types->where('shop_products.type', $filter_value);
        } */

        if (($filter_type != '') && ($filter_user_id > 0)) {
            $types->where('shop_products.type', $filter_value);
            $types->where('shop_products.user_id', $filter_user_id);
        }

        $types->distinct('type')->orderBy('type');

        if ($user == null) {
            $types = $types->where('filter_id', 1);
        } elseif ($user->role == 'admin') {

        } else {
            /*$userDetail = auth()->user()->userDetail;
            $filter = ($userDetail != null) ? $userDetail->filter : false;
            if ($filter === false) {
            } else {
                $types = $types->where('filter_id', $filter);
            }*/
        }
        $types = $types->get();
        $scat = clone $products;
        $scat->where('type', $filter_value);
        // \Session::get('gallery_subtype'); 
        if ($filter_type =="type") {
            if(!is_null($filter_subvalue) && \Session::get('catOrSubCat') != 'cat') {                
                // $products = $products->whereIn('category_type', $filter_subvalue);
                if(empty($filter_subvalue)){

                } else {
                    for ($i=0; $i < count($filter_subvalue); $i++) { 
                        if($i == 0){
                            $products = $products->where('category_type', 'LIKE', '%' . $filter_subvalue[$i] . '%');
                        } else {
                            $products = $products->orWhere('category_type', 'LIKE', '%' . $filter_subvalue[$i] . '%');
                        }
                    }
                }
            }
            $products = $products->where('type', $filter_value);
        }
        // echo"<pre>";print_r(DB::table('shop_products')->toSql());die;
        // echo"<pre>";print_r( ShopProduct::query()->where('in','type', $filter_value)->toSql());die;
        if ($filter_user_id != 0) {
            $products = $products->where('shop_product_files.user_id', $filter_user_id);
        }

        if(\Session::has('main-search-filter')) {
            $mainSearchFilter = '%'. \Session::get('main-search-filter') . '%';
            //$products = $products->where('shop_product_files.user_id', $filter_user_id);
            $products->where(function ($query) use ($mainSearchFilter) {
                $query->where('users.name', 'LIKE', $mainSearchFilter)
                        // ->orWhere('users.tagname', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.artist_name', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_product_details.value', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_profiles.shop_name', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.name', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.tags', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.type', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.category_type', 'LIKE', $mainSearchFilter);
            });
            $mainSearchFilter =  \Session::get('main-search-filter');
        }

        /*if(@$_GET['test'] == 1){
            echo $post_type." - ".$gallery_type;exit;
        }*/

        if($gallery_type == 'latest') {
            $products = $products->orderBy('shop_products.approval_date', 'DESC');
        } else {
            $products = $products->inRandomOrder();
        }


        if($post_type == "sale"){
            $products = $products->where('shop_products.sale','yes');
        }

        if ($user == null) {
            $products = $products->where('filter_id', 1);
        } elseif ($user->role == 'admin') {
            // All Products
        } else {
            /*$userDetail = auth()->user()->userDetail;
            $filter = ($userDetail != null) ? $userDetail->filter : false;
            if ($filter === false) {
                $count = $products->count();
            } else {
                $count = $products->where('filter_id', $filter)->count();
            }
            $products = $products->where('filter_id', $filter);*/

            if($get_user_filter){
                $filter = $get_user_filter->filter;
            } else {
                $filter = 1;
            }
            $products = $products->where('shop_products.filter_id', '<=', $filter);
        }

        $products = $products->get();

        /*if(\Session::has('main-search-filter')) {
            DB::enableQueryLog();
            // dd($products);exit;
            $query = DB::getQueryLog();
            dd($query);
        }*/

        $count = $products->count();
        // CHECK SESSION FOR TOGGLE VIEW
        $gallery_view = 'justified-without';
        if(\Session::has('gallery_view')) {
            $gallery_view = \Session::get('gallery_view');
            // echo '<pre>'; print_r($gallery_view);exit;
        }

        $scat->getQuery()->groups = [];
        $scat->groupby('category_type');
        $subcategories = $scat->get();

        /*$scat->Join('categories', 'shop_products.category_type', '=', 'categories.title');
        $subcategories = $scat->get();*/

        if($userId_for_filter){
            $check_liked_or_not = DB::table('shop_product_metas')
                                            ->select('shop_product_id')
                                            ->where('user_id', '=', $userId_for_filter)
                                            ->get();
            $liked_products = [];
            foreach ($check_liked_or_not as $key => $check_liked_or_not_1) {
                $liked_products[] = $check_liked_or_not_1->shop_product_id;
            }
        } else {
            $liked_products = [];
        }

        $get_filters_radio = DB::table('filters')
                                ->where('status', '=', 1)
                                ->get();

        // CHECK SESSION FOR TOGGLE VIEW
        return view('site.index', compact('products', 'count', 'gallery_view', 'gallery_type', 'post_type', 'types', 'filter_type', 'filter_value', 'filter_subvalue', 'subcategories', 'filter_user_id', 'filter_user_name', 'mainSearchFilter', 'get_user_filter','liked_products','get_filters_radio'));
    }

    public function checkPost($prev = 0){
        $data = [];
        $data['status'] = 'NOK';
        $user = auth()->user();
        $count = 0;

        if(\Session::has('filter_value')) {
                \Session::forget('filter_value');
            }
            if(\Session::has('filter_type')) {
                \Session::forget('filter_type');
            }
            if(\Session::has('post_type')) {
                \Session::forget('post_type');
            }

        // CHECK SESSION FOR GALLERY TOGGLE
        $gallery_type = 'latest';
        if(\Session::has('gallery_type'))
        {
            $gallery_type = \Session::get('gallery_type');
        }

        $post_type = 'all';
        if(\Session::has('post_type'))
        {
            $post_type = \Session::get('post_type');
        }


        // CHECK SESSION FOR GALLERY TOGGLE
        $filter_user_id = 0;
        $filter_user_name = '';
        if(\Session::has('filter_user'))
        {
            $filter_user_id = \Session::get('user_value');
            // $filterUser = User::where(['id' =>$filter_user_id])->first();

            $shop_profiles = DB::table('shop_profiles')
                        ->where('user_id', '=', $filter_user_id)
                        ->first();
            $filter_user_name = $shop_profiles->shop_name;

            // $filter_user_name = $filterUser->tagname;
        }

        $products = ShopProduct::byApproveStatus()
        ->leftJoin('shop_product_files', 'shop_product_files.shop_product_id', '=', 'shop_products.id')
        ->leftJoin('users', 'users.id', '=', 'shop_products.user_id')
        ->leftJoin('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
        ->leftJoin('shop_product_details', 'shop_products.id', '=', 'shop_product_details.shop_product_id')
       // ->select('users.tagname', 'shop_products.id', 'shop_products.user_id', 'shop_products.name', 'shop_products.description', 'shop_products.type', 'shop_product_files.thumb', 'shop_products.price', 'shop_products.filter_id', 'shop_products.quantity', 'shop_profiles.company_name', 'shop_profiles.shop_image_url')
       ->select('users.tagname', 'shop_products.artist_name', 'shop_products.id', 'shop_products.user_id', 'shop_products.name', 'shop_products.description', 'shop_products.type', 'shop_product_files.thumb', 'shop_products.price', 'shop_products.sale', 'shop_products.filter_id', 'shop_products.quantity', 'shop_products.comment', 'shop_profiles.company_name', 'shop_profiles.shop_name', 'shop_profiles.shop_image_url','users.name as user_name')

        ->groupby('shop_products.id');
        $products = $products->where('shop_products.status', 1);
        $filter_type = '';

        if(\Session::has('filter_type'))
        {
            $filter_type = \Session::get('filter_type');
        }
        $filter_value ='All';
        if(\Session::has('filter_value'))
        {
            $filter_value = \Session::get('filter_value');
        }
        if (($filter_type =="type") && ($filter_value != "All")){
            $products = $products->where('type', $filter_value);
        }
        $filter_subvalue = (\Session::has('filter_subvalue'))
                            ? \Session::get('filter_subvalue')
                            : null;

        if(!empty($filter_subvalue)){
            $filter_subvalue = array_values(array_filter($filter_subvalue));
        } else {
            $filter_subvalue = null;
        }

        if ($filter_type =="type") {
            if(!is_null($filter_subvalue) && \Session::get('catOrSubCat') != 'cat') {
                // $products = $products->whereIn('category_type', $filter_subvalue);
                if(empty($filter_subvalue)){

                } else {
                    for ($i=0; $i < count($filter_subvalue); $i++) { 
                        if($i == 0){
                            $products = $products->where('category_type', 'LIKE', '%' . $filter_subvalue[$i] . '%');
                        } else {
                            $products = $products->orWhere('category_type', 'LIKE', '%' . $filter_subvalue[$i] . '%');
                        }
                    }
                }
                // $products = $products->where('category_type', 'LIKE', '%' . $filter_subvalue[0] . '%');
            }            
            $products = $products->where('type', $filter_value);
        }
        if ($filter_user_id != 0) {
            $products = $products->where('shop_product_files.user_id', $filter_user_id);
        }
        if(\Session::has('main-search-filter'))
        {
            $mainSearchFilter = '%'. \Session::get('main-search-filter') . '%';
            //$products = $products->where('shop_product_files.user_id', $filter_user_id);
            $products->where(function ($query) use ($mainSearchFilter) {
                $query
                    ->where('users.name', 'LIKE', $mainSearchFilter)
                    // ->orWhere('users.tagname', 'LIKE', $mainSearchFilter)
                    ->orWhere('shop_products.artist_name', 'LIKE', $mainSearchFilter)
                    ->orWhere('shop_product_details.value', 'LIKE', $mainSearchFilter)
                    ->orWhere('shop_profiles.shop_name', 'LIKE', $mainSearchFilter)
                    ->orWhere('shop_products.name', 'LIKE', $mainSearchFilter)
                    ->orWhere('shop_products.tags', 'LIKE', $mainSearchFilter)
                    ->orWhere('shop_products.type', 'LIKE', $mainSearchFilter)
                    ->orWhere('shop_products.category_type', 'LIKE', $mainSearchFilter);
            });
        }

        if($post_type == "sale"){
            $products = $products->where('shop_products.sale','yes');
        }

        if($gallery_type == 'latest')
        {
            $products = $products->orderBy('shop_products.approval_date', 'DESC')->get();
        }
        else
        {
            $products = $products->inRandomOrder()->get();
        }


        if ($user == null) {
            $count = $products->where('filter_id', 1)->count();
        } elseif ($user->role == 'admin') {
            $count = $products->count();
        } else {
            $userDetail = auth()->user()->userDetail;

            $userDetail = DB::table('user_detail')
                ->where('user_id', '=', $userDetail->user_id)
                ->first();


            $filter = ($userDetail != null) ? $userDetail->filter : false;
            if ($filter === false) {
                $count = $products->count();
            } else {
                $count = $products->where('filter_id', $filter)->count();
            }

        }

        if ($prev != $count) {
            $data['status'] = 'OK';

            if ($user == null)
            {
                $products = $products->where('filter_id', 1);
            }
            elseif ($user->role == 'admin')
            {
                // All Products
            }
            else
            {
                $products = $products->where('filter_id', $filter);
            }

            // CHECK SESSION FOR TOGGLE VIEW
            $gallery_view = 'justified-without';
            if(\Session::has('gallery_view'))
            {
                $gallery_view = \Session::get('gallery_view');
            }
            // CHECK SESSION FOR TOGGLE VIEW

            $userId = Auth::id();
            if($userId){
                $check_liked_or_not = DB::table('shop_product_metas')
                                                ->select('shop_product_id')
                                                ->where('user_id', '=', $userId)
                                                ->get();
                $liked_products = [];
                foreach ($check_liked_or_not as $key => $check_liked_or_not_1) {
                    $liked_products[] = $check_liked_or_not_1->shop_product_id;
                }
            } else {
                $liked_products = [];
            }

            $html = view('site._post', [
                'count' => $count,
                'products' => $products,
                'gallery_view' => $gallery_view,
                'gallery_type' => $gallery_type,
                'post_type' => $post_type,
                'filter_type' => $filter_type,
                'filter_value' => $filter_value,
                'filter_user_id' => $filter_user_id,
                'filter_user_name' => $filter_user_name,
                'liked_products' => $liked_products
            ])->render();

            $data['html'] = $html;
            $data['count'] = $count;
        }

        return response($data);
    }

    public function contact(){
        $bannerImages = Post::inRandomOrder()->orderBy('id', 'DESC')->get();
        $filterBannerImages = [];
        $validImage = [
            'png',
            'jpg',
            'jpeg'
        ];
        foreach ($bannerImages as $i => $bannerImage) {
            $image = FileHelper::getFile($bannerImage->image);
            if (@file_get_contents($image) != null) {
                $endExt = explode('.', $bannerImage->image);
                $ext = strtolower(end($endExt));
                if (in_array($ext, $validImage)) {
                    $imageSize = getimagesize($image);
                    $width = $imageSize[0];
                    $height = $imageSize[1];
                    if ($width >= 1920 && ($width > $height)) {
                        $filterBannerImages[] = [
                            'image' => $image,
                            'username' => $bannerImage->user->name,
                            'tagname' => $bannerImage->user->tagname,
                            'description' => $bannerImage->decription
                        ];
                    }
                }
            }
        }
        if (! isset($filterBannerImages[0])) {

            $filterBannerImages[0] = [
                'image' => 'assets/img/misc/page-header-bg-9.jpg',
                'username' => 'default',
                'tagname' => 'default',
                'description' => 'default'
            ];
        }
        if (! isset($filterBannerImages[1])) {

            $filterBannerImages[1] = [
                'image' => 'assets/img/misc/page-header-bg-9.jpg',
                'username' => 'default',
                'tagname' => 'default',
                'description' => 'default'
            ];
        }
        return view('site.contact', compact('filterBannerImages'));
    }

    public function join_gallery(Request $request){
        
        $validated = request()->validate([
            'captcha1' => 'required|captcha'
        ],
        ['captcha1.captcha1'=>'Invalid captcha code join gallery.']);

        /*$validated = request()->validate([
            'captcha' => 'required|captcha'
        ],
        ['captcha.captcha'=>'Invalid captcha code join gallery.'])->validateWithBag('post');*/

        if($request->mail_subject){
            $subject = $request->mail_subject;
        }else{
            $subject = 'Contact ARTfora';
        }

        if($request->post_user_mail){ 
            $data = [
                'sender_copy' => 'You have got a new message from the ARTfora online gallery from:',
                'name' => $request->contact_name,
                'email' => $request->contact_email,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message),
                'subject' => $subject,
            ];
            Mail::to($request->post_user_mail)->send(new ContactMail($data));

            $data = [
                'sender_copy' => 'Thank you for your email. This is your copy of the email you have sent to ' . $request->post_user_mail,
                'name' => $request->contact_name,
                'email' => $request->contact_email,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message),
                'subject' => $subject,
            ];
            Mail::to($request->contact_email)->send(new ContactMail($data));

            $data = [
                'sender_copy' => 'New email has been sent to ' . $request->post_user_mail . ' from:',
                'name' => $request->contact_name,
                'email' => $request->contact_email,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message),
                'subject' => $subject,
            ];
            Mail::to('gallery@artfora.net')->send(new ContactMail($data));
        } else {
            $data = [
                'name' => $request->contact_name,
                'email' => $request->contact_email,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message),
                'subject' => $subject,
            ];
            Mail::to('contact@artfora.net')->send(new ContactMail($data));
            //Mail::to('info@icreatewebtech.com')->send(new ContactMail($data));
        }
        
        /* return redirect()->to( $request->contact_url ); */
        return redirect()->to('/index');
    }

    public function email_modal(Request $request){
        $captcha = \Session::get('captcha');
        // dd(Captcha::check($request->captcha4));exit;
        
        $request->validate([
            'captcha4' => 'required|captcha'
        ],
        ['captcha4.captcha4'=>'Invalid captcha code email modal.']);
        
        /*if($request->mail_subject){
            $subject = $request->mail_subject;
        }else{
            $subject = 'Contact ARTfora';
        }*/
        $subject = 'Contact ARTfora';

        if($request->post_user_mail){ 
            $data = [
                'sender_copy' => 'You have got a new message from the ARTfora online gallery from:',
                'name' => $request->contact_name_emailModal,
                'email' => $request->contact_email_emailModal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_emailModal),
                'subject' => $subject,
            ];
            Mail::to($request->post_user_mail)->send(new ContactMail($data));

            $data = [
                'sender_copy' => 'Thank you for your email. This is your copy of the email you have sent to ' . $request->post_user_mail,
                'name' => $request->contact_name_emailModal,
                'email' => $request->contact_email_emailModal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_emailModal),
                'subject' => $subject,
            ];
            Mail::to($request->contact_email_emailModal)->send(new ContactMail($data));

            $data = [
                'sender_copy' => 'New email has been sent to ' . $request->post_user_mail . ' from:',
                'name' => $request->contact_name_emailModal,
                'email' => $request->contact_email_emailModal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_emailModal),
                'subject' => $subject,
            ];
            Mail::to('gallery@artfora.net')->send(new ContactMail($data));
            

        } else {
            $data = [
                'name' => $request->contact_name_emailModal,
                'email' => $request->contact_email_emailModal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_emailModal),
                'subject' => $subject,
            ];
            Mail::to('contact@artfora.net')->send(new ContactMail($data));
            //Mail::to('info@icreatewebtech.com')->send(new ContactMail($data));
        }
        
        return redirect()->to( $request->contact_url );
        // return redirect()->to('/index');
    }

    public function contact_modal(Request $request){
        $validated = request()->validate([
            'captcha2' => 'required|captcha'
        ],
        ['captcha2.captcha2'=>'Invalid captcha code contact modal.']);

        if($request->mail_subject){
            $subject = $request->mail_subject;
        }else{
            $subject = 'Contact ARTfora';
        }

        if($request->post_user_mail){ 
            $data = [
                'sender_copy' => 'You have got a new message from the ARTfora online gallery from:',
                'name' => $request->contact_name_con_modal,
                'email' => $request->contact_email_con_modal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_con_modal),
                'subject' => $subject,
            ];
            Mail::to($request->post_user_mail)->send(new ContactMail($data));

            $data = [
                'sender_copy' => 'Thank you for your email. This is your copy of the email you have sent to ' . $request->post_user_mail,
                'name' => $request->contact_name_con_modal,
                'email' => $request->contact_email_con_modal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_con_modal),
                'subject' => $subject,
            ];
            Mail::to($request->contact_email)->send(new ContactMail($data));

            $data = [
                'sender_copy' => 'New email has been sent to ' . $request->post_user_mail . ' from:',
                'name' => $request->contact_name_con_modal,
                'email' => $request->contact_email_con_modal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_con_modal),
                'subject' => $subject,
            ];
            Mail::to('gallery@artfora.net')->send(new ContactMail($data));
        } else {
            $data = [
                'name' => $request->contact_name_con_modal,
                'email' => $request->contact_email_con_modal,
                'url' => $request->contact_url,
                'message' => nl2br($request->contact_message_con_modal),
                'subject' => $subject,
            ];
            Mail::to('contact@artfora.net')->send(new ContactMail($data));
            //Mail::to('info@icreatewebtech.com')->send(new ContactMail($data));
        }
        
        /* return redirect()->to( $request->contact_url ); */
        return redirect()->to('/index');
    }

    public function refreshCaptcha(){
        return response()->json(['captcha'=> captcha_img()]);
    }

    

    public function commission_modal(Request $request){
        $validated = request()->validate([
            'captcha3' => 'required|captcha'
        ],
        ['captcha3.captcha3'=>'Invalid captcha code commission modal.']);

        $data = [
            'sender_copy' => 'You have got a new message from the ARTfora online gallery from:',
            'name' => $request->contact_name,
            'email' => $request->contact_email,
            'url' => $request->contact_url,
            'message' => nl2br($request->contact_message),
            'subject' => 'Message regarding commission',
        ];
        Mail::to($request->post_user_mail)->send(new ContactMail($data));
        $data = [
            'sender_copy' => 'Thank you for your email. This is your copy of the email you have sent to ' . $request->post_user_mail,
            'name' => $request->contact_name,
            'email' => $request->contact_email,
            'url' => $request->contact_url,
            'message' => nl2br($request->contact_message),
            'subject' => 'Message regarding commission',
        ];
        Mail::to($request->contact_email)->send(new ContactMail($data));

        $data = [
            'sender_copy' => 'New email has been sent to ' . $request->post_user_mail . ' from:',
            'name' => $request->contact_name,
            'email' => $request->contact_email,
            'url' => $request->contact_url,
            'message' => nl2br($request->contact_message),
            'subject' => 'Message regarding commission',
        ];
        Mail::to('gallery@artfora.net')->send(new ContactMail($data));

        return redirect()->to( $request->contact_url );
        // return redirect()->to('/');
    }

    public function postContact(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'subject' => 'required'
        ]);

        $data = [
            'name' => $request->name,
            'message' => $request->message,
            'subject' => $request->subject,
            'email' => $request->email
        ];

        Mail::to('contact@artfora.net')->send(new ContactMail($data));
        return back()->with('success', 'Thanks for contacting us!');
    }

    public function product_details(Request $request,$user_name, $id, $approvalCode = null){
        $buttons = WebsiteButton::get();
        $product_details = ShopProduct::with('shop_product_details', 'shop_product_files','user')->where('id', $id)->first();

        if (!$product_details->is_approved && $approvalCode === null)
        {
            throw new NotFoundHttpException();
        }

        $users_details = DB::table('users')->where('id', '=', $product_details->user_id)->first();
        $product_details['users_details'] = $users_details;

        $shop_product_details = DB::table('shop_product_details')->where('shop_product_id', '=', $id)->get();
        $product_details['shop_product_details'] = $shop_product_details;

        $product_details['shop_profile'] = DB::table('shop_profiles')->where('user_id',  $product_details->user_id)->first();
        $product_details['product_rating'] = DB::table('product_ratings as p')
                                            ->leftJoin('users', 'users.id', '=', 'p.user_id')
                                            ->leftJoin('user_detail', 'user_detail.user_id', '=', 'users.id')
                                            ->select('users.name', 'users.tagname','user_detail.profile_image_url', 'p.product_rating', 'p.product_review', 'p.delivery_rating')
                                            ->where('p.product_id',  $id)->get();
        $product_details['seller_rating'] = DB::table('seller_ratings')->where('seller_id',  $product_details->shop_profile_id)->get();


        /*get_product_read_count*/
        $product_count_data = DB::table('shop_products')
                        ->where('id', '=', $id)
                        ->first();

        $current_count = $product_count_data->read_count;
        $updated_count = $current_count + 1;

        DB::table('shop_products')
            ->where('id', $id)
            ->update(['read_count' => $updated_count]);

        // $publicIP = file_get_contents("http://ipecho.net/plain");
        $publicIP = $_SERVER['REMOTE_ADDR'];
        $details = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$publicIP}"));
        $country = $details->geoplugin_countryName;
        $post_url = url()->current();

        DB::table('country_wise_visitor_data')->insert([
                                                        'ip' => $publicIP,
                                                        'country' => $country,
                                                        'post_id' => $id,
                                                        'post_url' => $post_url
                                                    ]);

        // echo '<pre>'; print_r($product_details->type);exit;
        return view('site.product_details', compact('product_details', 'buttons', 'approvalCode'));
    }

    public function toggle_options(Request $request)
    {
        // echo '<pre>'; print_r($tagname);exit;
        if(!isset($request->action) || ($request->action == '') || is_null($request->action)) {
            // if (isset($request->gallery_view) && !empty($request->gallery_view)) {
                \Session::put('gallery_view', $request->gallery_view);
            // } else {
                if ($request->type == "type") {
                    if ($request->gallery_type == 'All') {
                        \Session::forget('filter_value');
                        \Session::forget('filter_type');
                        \Session::forget('post_type');
                        \Session::forget('user_value');
                        \Session::forget('filter_user');
                        \Session::forget('main-search-filter');
                        \Session::forget('filter_subvalue');
                        //\Session::put('gallery_subtype', $request->gallery_subtype);
                    } else if($request->remove_session == 1){
                        \Session::forget('filter_value');
                        \Session::forget('filter_type');
                        \Session::forget('post_type');
                    } else {
                        $subtype = \Session::get('filter_subvalue');
                        if(empty($subtype)){
                            $subtype = [];
                        }
                        
                        if(!empty($subtype) && !is_array($subtype)){
                            $subtype = [$subtype];
                        }


                        if(in_array($request->gallery_subtype,$subtype)){
                            if (($key = array_search($request->gallery_subtype, $subtype)) !== false) {
                                unset($subtype[$key]);
                            }
                        } else {
                            $subtype[] = $request->gallery_subtype;    
                        }

                        \Session::put('catOrSubCat', $request->catOrSubCat);
                        \Session::put('filter_type', $request->type);
                        \Session::put('filter_value', $request->gallery_type);
                        \Session::put('post_type', $request->post_type);

                        /*\Session::put('filter_value', $request->gallery_type_sort_by);
                        \Session::put('post_type', $request->post_type_sort_by);*/
                        //\Session::put('gallery_subtype', $request->gallery_subtype);
                        

                        if($request->is_subcat_selected != 'empty') {
                            \Session::forget('filter_subvalue');
                        } else {
                            \Session::put('filter_subvalue', $subtype);
                        }
                    }
                } elseif ($request->type == "user") {
                    \Session::put('user_value', $request->user_id);
                    \Session::put('filter_user', $request->type);
                } else {
                    \Session::put('gallery_type', $request->gallery_type);
                    \Session::put('post_type', $request->post_type);

                }
            // }
        } elseif($request->action == "disable") {
            if ($request->type == "type") {
                \Session::forget('filter_value');
                \Session::forget('filter_type');
            } elseif ($request->type == "user") {
                \Session::forget('user_value');
                \Session::forget('filter_user');
            }
        } elseif($request->action == "user-selection") {
            if ($request->type == "user") {
                \Session::forget('filter_value');
                \Session::forget('filter_type');

                \Session::put('user_value', $request->user_id);
                \Session::put('filter_user', $request->type);
            }
        }

        $view = \Session::get('gallery_view');
        $gallery_type = \Session::get('gallery_type');
        $post_type = \Session::get('post_type');
        // echo 'gallery_view: '.$view.' gallery_type: '.$gallery_type.' post_type: '.$post_type;exit;

        // $gallery_view = 'justified_without';
        // if(\Session::has('gallery_view'))
        // {
        //     $gallery_view = \Session::get('gallery_view');
        // }
    }

    public function author_filter(Request $request, $tagname = null, $username = null){
        $user_url = url()->current();
        
        if(strpos($user_url, '.env') !== false){
           return redirect('/');
        }
        
        $filter_type ='';
        if(\Session::has('filter_type')) {
            $filter_type = \Session::get('filter_type');
        }

        $filter_user_id = 0;
        $filter_user_name = '';
        if(\Session::has('filter_user')) {
            $filter_user_id = \Session::get('user_value');

            // $filterUser = User::where(['id' =>$filter_user_id])->first();

            $shop_profiles = DB::table('shop_profiles')
                        ->where('user_id', '=', $filter_user_id)
                        ->first();
            $filter_user_name = $shop_profiles->shop_name;
        }
        $username = $tagname;
        if($username) {
            $filterUser = User::where(['tagname' => $username])->first();

            if($filterUser) {
                $filter_user_name = $username;
                $filter_user_id = $filterUser->id;

                $shop_profiles = DB::table('shop_profiles')
                        ->where('user_id', '=', $filter_user_id)
                        ->first();
                $filter_user_name = $shop_profiles->shop_name;

                $filter_type = '';
            }
        }

        /*$products = ShopProduct::with(['shop_profiles' => function($query){
            $query->select('id', 'name');
        }, 'shop_product_files' => function($query){
            $query->select('shop_product_id', 'thumb')->pluck('thumb')->first();
        }])->get();
        return \Session::get('gallery_type');*/

        // CHECK SESSION FOR GALLERY TOGGLE
        $mainSearchFilter = '';
        if (($request->input('main-search-filter')) && ($request->input('main-search-filter')!='')) {
            $mainSearchFilter = $request->input('main-search-filter');
            \Session::put('main-search-filter',$mainSearchFilter);
        } else {
            if(\Session::has('main-search-filter')) {
                \Session::forget('main-search-filter');
            }
        }

        $gallery_type = 'latest';
        $post_type = 'all';
        if(\Session::has('gallery_type')) {
            $gallery_type = \Session::get('gallery_type');
        }

        if(\Session::has('post_type')) {
            $post_type = \Session::get('post_type');
        }

        $filter_value ='All';
        if(\Session::has('filter_value')) {
            $filter_value = \Session::get('filter_value');
        }


        $filter_subvalue = (\Session::has('filter_subvalue'))
                            ? \Session::get('filter_subvalue')
                            : null;

        if(!empty($filter_subvalue)){
            $filter_subvalue = array_values(array_filter($filter_subvalue));
        } else {
            $filter_subvalue = null;
        }

        $subcategories = false;

        if($filter_value !== 'All') {
            $category = Category::where([
                'title' => $filter_value,
                'parent_id' => 0
            ])->first();

            if($category) {
                //$subcategories = $category->childs;
            }
        }

        // CHECK SESSION FOR GALLERY TOGGLE
        $user = auth()->user();

        $userId_for_filter = Auth::id();
        $get_user_filter = DB::table('user_detail')->where('user_id', '=', $userId_for_filter)->first();

        $products = ShopProduct::byApproveStatus()
                    ->leftJoin('shop_product_files', 'shop_product_files.shop_product_id', '=', 'shop_products.id')
                    ->leftJoin('shop_profiles', 'shop_profiles.id', '=', 'shop_products.shop_profile_id')
                    ->leftJoin('users', 'users.id', '=', 'shop_products.user_id')
                    ->leftJoin('shop_product_details', 'shop_products.id', '=', 'shop_product_details.shop_product_id')
                    ->select('users.tagname', 'shop_products.artist_name', 'shop_products.id', 'shop_products.user_id', 'shop_products.name', 'shop_products.description', 'shop_products.type', 'shop_product_files.thumb', 'shop_products.price', 'shop_products.filter_id','shop_products.category_type as title', 'shop_products.quantity', 'shop_products.comment', 'shop_products.sale', 'shop_profiles.company_name', 'shop_profiles.shop_name', 'shop_profiles.shop_image_url','users.name as user_name')
                    ->groupby('shop_products.id');
        // $products = $products->where('shop_products.type', $post_type);
        // $products = $products->where('shop_products.status', 1); //Moved into scope with approval_date check
        $types = clone $products;
        $types = $types ->select('type');
        if (($filter_type == '') && ($filter_user_id > 0)) {
            $types->where('shop_products.user_id', $filter_user_id);
        }
        
        /* if (($filter_type != '') && ($filter_user_id == 0)) {
            $types->where('shop_products.type', $filter_value);
        } */

        if (($filter_type != '') && ($filter_user_id > 0)) {
            $types->where('shop_products.type', $filter_value);
            $types->where('shop_products.user_id', $filter_user_id);
        }

        $types->distinct('type')->orderBy('type');

        if ($user == null) {
            $types = $types->where('filter_id', 1);
        } elseif ($user->role == 'admin') {

        } else {
            /*$userDetail = auth()->user()->userDetail;
            $filter = ($userDetail != null) ? $userDetail->filter : false;
            if ($filter === false) {
            } else {
                $types = $types->where('filter_id', $filter);
            }*/
        }
        $types = $types->get();


        $scat = clone $products;
        $scat->where('type', $filter_value);
        // \Session::get('gallery_subtype'); 
        if ($filter_type =="type") {
            if(!is_null($filter_subvalue) && \Session::get('catOrSubCat') != 'cat') {                
                // $products = $products->whereIn('category_type', $filter_subvalue);
                if(empty($filter_subvalue)){

                } else {
                    for ($i=0; $i < count($filter_subvalue); $i++) { 
                        if($i == 0){
                            $products = $products->where('category_type', 'LIKE', '%' . $filter_subvalue[$i] . '%');
                        } else {
                            $products = $products->orWhere('category_type', 'LIKE', '%' . $filter_subvalue[$i] . '%');
                        }
                    }
                }
            }
            $products = $products->where('type', $filter_value);
        }

        if ($filter_type = "type") {
            if($post_type != "all"){
                $products = $products->where('type', $post_type);
            }
        }
        // echo"<pre>";print_r(DB::table('shop_products')->toSql());die;
        // echo"<pre>";print_r( ShopProduct::query()->where('in','type', $filter_value)->toSql());die;
        if ($filter_user_id != 0) {
            $products = $products->where('shop_product_files.user_id', $filter_user_id);
        }

        if(\Session::has('main-search-filter')) {
            $mainSearchFilter = '%'. \Session::get('main-search-filter') . '%';
            //$products = $products->where('shop_product_files.user_id', $filter_user_id);
            $products->where(function ($query) use ($mainSearchFilter) {
                $query->where('users.name', 'LIKE', $mainSearchFilter)
                        // ->orWhere('users.tagname', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.artist_name', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_product_details.value', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_profiles.shop_name', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.name', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.tags', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.type', 'LIKE', $mainSearchFilter)
                        ->orWhere('shop_products.category_type', 'LIKE', $mainSearchFilter);
            });
            $mainSearchFilter =  \Session::get('main-search-filter');
        }

        if($gallery_type == 'latest') {
            $products = $products->orderBy('shop_products.approval_date', 'DESC');
        } else {
            $products = $products->inRandomOrder();
        }

        if($post_type == "sale"){
            $products = $products->where('shop_products.sale','yes');
        }

        if ($user == null) {
            $products = $products->where('filter_id', 1);
        } elseif ($user->role == 'admin') {
            // All Products
        } else {
            /*$userDetail = auth()->user()->userDetail;
            $filter = ($userDetail != null) ? $userDetail->filter : false;
            if ($filter === false) {
                $count = $products->count();
            } else {
                $count = $products->where('filter_id', $filter)->count();
            }
            $products = $products->where('filter_id', $filter);*/
            
            if($get_user_filter){
                $filter = $get_user_filter->filter;
            } else {
                $filter = 1;
            }
            
            $products = $products->where('shop_products.filter_id', '<=', $filter);
        }

        // DB::enableQueryLog();
        $products = $products->get();
        // echo '<pre>'; print_r($products);exit;
        /*if($_GET['test'] == 1){
            dd($products);exit;
        }*/
        // $query = DB::getQueryLog();
        // dd($query);

        $count = $products->count();
        // CHECK SESSION FOR TOGGLE VIEW
        $gallery_view = 'justified-without';
        if(\Session::has('gallery_view')) {
            $gallery_view = \Session::get('gallery_view');
            // echo '<pre>'; print_r($gallery_view);exit;
        }

        $scat->getQuery()->groups = [];
        $scat->groupby('category_type');
        $subcategories = $scat->get();
        // dd($subcategories);exit;


        $sub_temp = array();
        foreach ($products as $key => $value_1) {
            // echo '<pre>'; print_r($value_1->title);exit;
            $a = explode('.', $value_1->title);
            foreach ($a as $key => $value_2) { 
                $shop_profiles_1 = DB::table('shop_products')
                                ->where('type', '=', $post_type)
                                ->where('category_type', 'LIKE', '%' . $value_2 . '%')
                                ->first();
                if(@$shop_profiles_1->id){
                    $sub_temp[] = $value_2;
                }
            }
        }
        $subcategories = array_unique($sub_temp);

        $a = [];
        $b = '';
        $c = [];
        foreach ($subcategories as $key => $value_3) {
            $a['title'] = $value_3;
            $b = (object) $a;
            $c[] = $b;
        }
        $subcategories = $c;

        $publicIP = $_SERVER['REMOTE_ADDR'];
        $details = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$publicIP}"));
        $country = $details->geoplugin_countryName;
        
        DB::table('user_profile_view_data')->insert([
                                                        'ip' => $publicIP,
                                                        'country' => $country,
                                                        'user_id' => $filter_user_id,
                                                        'user_url' => $user_url
                                                    ]);

        /*$scat->Join('categories', 'shop_products.category_type', '=', 'categories.title');
        $subcategories = $scat->get();*/

        $current_user = auth()->user();
        $current_user_id = Auth::id();

        if($current_user_id){
            $check_liked_or_not = DB::table('shop_product_metas')
                                            ->select('shop_product_id')
                                            ->where('user_id', '=', $current_user_id)
                                            ->get();
            $liked_products = [];
            foreach ($check_liked_or_not as $key => $check_liked_or_not_1) {
                $liked_products[] = $check_liked_or_not_1->shop_product_id;
            }
        } else {
            $liked_products = [];
        } 

        $get_filters_radio = DB::table('filters')
                                ->where('status', '=', 1)
                                ->get();
        /*$fakevar_01;*/
        // CHECK SESSION FOR TOGGLE VIEW
        return view('site.index', compact('products', 'count', 'gallery_view', 'gallery_type', 'post_type', 'types', 'filter_type', 'filter_value', 'filter_subvalue', 'subcategories', 'filter_user_id', 'filter_user_name', 'mainSearchFilter','liked_products','get_filters_radio'));
    }

    public function buyFromArtistEmail(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'subject'                  => 'required|min:2|max:255',
            'email'                 => 'required|email',
            'message'               => 'required'
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'error' => implode(", ", $validator->errors()->all()),
                'data' =>[]
            ];
            return response()->json($response);
        }

        $product = ShopProduct::with('user')->where('id', $product_id)->first();

        Mail::send('email.buyFromArtist', ['data' => $validator->valid()], function ($m) use ($product) {
            $m->from('no-reply@artfora.com', config('app.name'));

            $m->to($product->user->email, $product->user->full_name)->subject('ARTfora: Somebody want buy your art!');
        });

        Mail::send('email.buyFromArtist', ['data' => $validator->valid()], function ($m) use ($product) {
            $m->from('no-reply@artfora.com', config('app.name'));

            $m->to('no-reply@artfora.com', $product->user->name)->subject('ARTfora: Somebody want buy your art!');
        });
    }

    public function product_send_email(Request $request) {

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|min:2|max:255',
            'email'                 => 'required|email',
            'message'               => 'required',
            'site_button_id'         =>'required|integer|exists:website_buttons,id',
            'shop_product_id'         =>'required|integer|exists:shop_products,id',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'error' => implode(", ", $validator->errors()->all()),
                'data' =>[]
            ];
            return response()->json($response);
        }
        $product = ShopProduct::with('user')->where('id', $request->shop_product_id)->first();
        $to_email = $product->user->email;
        //$to_email = "nnedogarko@gmail.com";
        if($request->site_button_id == 1) {
            $subject = 'ARTfora: Someone wants to contact you…';
        } else {
            $subject = 'ARTfora: Someone wants to hire you…';
        }
        $message = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Email template</title><link rel="stylesheet" href="css/screen.css"></head><body height="100%"><table style="width: 600px; height: 100%; margin-left: auto; margin-right: auto; border-radius:20px; border:2px solid #666; padding: 50px 0 100px 0"> <tbody><tr><td><p style="text-align: center; padding-bottom:40px;"><img src="http://www.artfora.net/images/logo_email_template.png" alt="ARTfora logo" width="100" height="100" /></p> </td></tr><tr><td><p style="text-align: center; color:#666;"><span style="font-size: 24px; font-family: Prozak, sans-serif; letter-spacing: 5px;">WELCOME TO ARTfora</span></p></td></tr></tbody></table></body></html>';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: ".$request->email."\nX-Mailer: PHP/";
        mail($to_email, $subject, $message, $headers);

        $response = [
            'success' => true,
            'error' => "",
            'data' =>[]
        ];
        return response()->json($response);
    }

    public function product_set_click(Request $request) {

        $validator = Validator::make($request->all(), [
            'site_button_id'         =>'required|integer|exists:website_buttons,id',
            'shop_product_id'         =>'required|integer|exists:shop_products,id',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'error' => implode(", ", $validator->errors()->all()),
                'data' =>[]
            ];
            return response()->json($response);
        }
        $product = ShopProduct::with('user')->where('id', $request->shop_product_id)->first();
        $wbc = new WebsiteButtonClick();
        $wbc->product_id = $product->id;
        $wbc->user_id = $product->user_id;
        $wbc->button_id = $request->site_button_id;
        $wbc->save();

        $uwbc = UserWebsiteButtonClick::where([
            'user_id' => $product->user_id,
            'website_button_id' => $request->site_button_id
        ])->first();

        if (is_null($uwbc)) {
            $uwbc = new UserWebsiteButtonClick();
            $uwbc->user_id = $product->user_id;
            $uwbc->website_button_id = $request->site_button_id;
            $uwbc->quantity = 1;
        } else {
            $uwbc->quantity = $uwbc->quantity + 1;
        }
        $uwbc->save();

        $spwbc = ShopProductWebsiteButtonClick::where([
            'shop_product_id' => $product->id,
            'website_button_id' => $request->site_button_id
        ])->first();

        if (is_null($spwbc)) {
            $spwbc = new ShopProductWebsiteButtonClick();
            $spwbc->shop_product_id = $product->id;
            $spwbc->website_button_id = $request->site_button_id;
            $spwbc->quantity = 1;
        } else {
            $spwbc->quantity = $uwbc->quantity + 1;
        }
        $spwbc->save();

        $response = [
            'success' => true,
            'error' => "",
            'data' =>[]
        ];
        return response()->json($response);
    }

    public function like_ajax_action(Request $request){
        $user = auth()->user();
        $userId = Auth::id();

        $get_user_profile_details = DB::table('shop_profiles')
                ->where('user_id', '=', $request->product_user_id)
                ->first();

        $shop_product_id = $request->shop_product_id;
        $shop_profile_id = $get_user_profile_details->id;

        if(Auth::check()){
            $check_liked_or_not = DB::table('shop_product_metas')
                                        ->where('user_id', '=', $userId)
                                        ->where('shop_product_id', '=', $shop_product_id)
                                        ->where('shop_profile_id', '=', $shop_profile_id)
                                        ->first();

            if($check_liked_or_not){
                $deleted = DB::table('shop_product_metas')->where('id', '=', $check_liked_or_not->id)->delete();
                
                $res = 2;
            } else {
                $insert = DB::table('shop_product_metas')->insert([
                    'user_id' => $userId,
                    'shop_product_id' => $shop_product_id,
                    'shop_profile_id' => $shop_profile_id,
                    'type' => 'like',
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $res = 1;
            }
        } else {
            $res = 3;
        }

        return response()->json($res);
    }
}
