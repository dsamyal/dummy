<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('email-veryfied', 'RegisterController@emailVeryfied');
Route::get('email-verify', 'RegisterController@emailVeryfied');

Route::get('update-email-veryfied', 'RegisterController@updateemailVeryfied');

Route::get('delete-account-veryfied/{id}', 'RegisterController@deleteaccountVeryfied');
Route::get('reactive-account-veryfied/{id}', 'RegisterController@reactiveaccountVeryfied');
//cron
Route::get('reactive-user-account', 'RegisterController@userReactiveAccountMail');
Route::get('delete_user_by_cron', 'RegisterController@delete_user_by_cron');

Route::get('new_pass', 'RegisterController@new_pass');

Route::get('/verify_forgotpassword', 'RegisterController@verifyForgotPassword');
Route::get('/verify-forgot-password', 'RegisterController@verifyForgotPassword');

Route::get('check_email', 'RegisterController@check_email');
Route::post('submit_pass', 'RegisterController@submit_pass');

Route::get('email_verification/{id}', 'RegisterController@email_verification');
//Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/clickviews', [App\Http\Controllers\ClickviewsController::class, 'clickviews'])->name('clickviews');
Route::get('/user_profile_views', [App\Http\Controllers\UserprofileviewController::class, 'user_profile_views'])->name('user_profile_views');
Auth::routes(['verify' => true]);
//Route::get('/', function () {
//    return view('welcomelogo');
//});

Route::get('/logout','ContactUsController@Logout');
Route::get('/', 'SiteController@index')->name('site.index');
//Route::get('/','ContactUsController@Commingpages');
//Auth::routes();
//
//Route::get('/welcome', 'WelcomeController@index')->name('welcome');

Route::get('site/check-post/{prev}', 'SiteController@checkPost')->name('site.check-post');


/*Route::get('/login', function () {
   return Redirect::to('/');
});*/

//Route::get('/logout', function () {
//    Auth::logout();
//    return Redirect::to('/');
//});


Route::get('/contact', 'SiteController@contact')->name('site.contact');
Route::post('/post-contact', 'SiteController@postContact')->name('submit-contact-form');

/* captcha */
/* Route::get('captcha_code', 'SiteController@Captcha'); */
Route::post('contact_mail', 'SiteController@contact_modal');
Route::get('refresh_captcha', 'SiteController@refreshCaptcha')->name('refresh_captcha');

Route::post('join_gallery', 'SiteController@join_gallery');
Route::post('email_modal', 'SiteController@email_modal');



Route::get('{user_name}/{id}', 'SiteController@product_details');

Route::get('/{tagname}', 'SiteController@author_filter');
# Route::post('/author_filter/', 'SiteController@author_filter');

Route::post('/toggle_options/', 'SiteController@toggle_options');

Route::get('/search/', 'SiteController@index');
/*Route::post('search', ['as' => 'search', function(){
    $file = Request\Request::input('main-search-filter');
    return Redirect::route('search', [$file]);
}] );
Route::get('search/{$file}', ['as' => 'search', 'uses' => 'SiteController@index']);*/

Route::post('/product_details/send-email', 'SiteController@product_send_email')->name('send-product-email');
Route::post('/product_details/set-click', 'SiteController@product_set_click')->name('set-product-click');
Route::get('/test', 'SiteController@test')->name('site.test');

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('home', 'HomeController');
    Route::resource('categories', 'CategoryController');
    Route::resource('clickviews', 'ClickviewsController');
    Route::resource('user_profile_views', 'UserprofileviewController');
    Route::post('ajaxSubcategory', 'CategoryController@ajaxSubcategory');
});

Route::get('/add_product','ContactUsController@AddProductPage');
Route::post('/contactus_email','ContactUsController@ContactUsEmail');
Route::get('/thank-you','ContactUsController@EmailThankYouPage');

Route::get('/my-tutorial','SiteController@myTutorial');
//Route::get('public', function () {
//    return view('layouts.front');
//});
//Route::get('/my-tutorial','SiteController@myTutorial');


// Route::post('/index', 'SiteController@contact_modal');
Route::post('/', 'SiteController@commission_modal');

// Route::get('/artfora_stats_1', 'CategoryController@my_stats')->name('artfora.stats');
// Route::get('/artfora_stats_1', [App\Http\Controllers\HomeController::class, 'my_stats'])->name('artfora.stats');

/* like routes */
Route::post('like_ajax_action', 'SiteController@like_ajax_action');

Route::post('user_register', 'RegisterController@user_register')->name('user_register');