<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('register', 'RegisterController@register');
Route::post('check-verify-email', 'RegisterController@checkVerifyEmail');
Route::post('check-register-email', 'RegisterController@checkRegisterEmail');
Route::post('register-step-two', 'RegisterController@registerStepTwo');
Route::get('verify-forgot-password', 'RegisterController@UpdateForgotPassword');
Route::get('email-verify', 'RegisterController@email_verify_api');
Route::get('update-email-veryfied', 'RegisterController@update_email_api_Veryfied');
Route::post('login', 'RegisterController@login');
Route::post('create-post', 'RegisterController@createPost');
Route::post('re-post', 'RegisterController@rePost');
Route::post('delete_repost', 'RegisterController@delete_repost');
Route::post('share-post-status', 'RegisterController@sharePostStatus');
Route::post('get-all-post', 'RegisterController@getAllPost');
Route::post('edit-comment', 'RegisterController@editComment');
Route::post('post-comment', 'RegisterController@postComment');
Route::post('delete-comment', 'RegisterController@deleteComment');
Route::post('post-like', 'RegisterController@postLike'); 
Route::post('get-post-comments', 'RegisterController@getPostComments'); 
Route::post('follow', 'RegisterController@follow'); 
Route::post('search-user', 'RegisterController@searchUser'); 
Route::post('feed', 'RegisterController@feed'); 
Route::post('single_feed', 'RegisterController@single_feed'); 
Route::post('re-send-email', 'RegisterController@reSendEmail'); 
Route::post('descover', 'RegisterController@descover');
Route::post('create-job', 'JobController@createJob');
Route::post('my-job', 'JobController@myJob');
Route::post('search-job', 'JobController@jobSearch');
Route::post('job-like', 'JobController@jobLike');
Route::post('job-likes-list', 'JobController@jobLikesList');
Route::get('alljobs', 'JobController@alljobs');
Route::post('alljobs1', 'JobController@alljobs1');
Route::post('alljobs2', 'JobController@alljobs2');
Route::post('jobFilter', 'JobController@jobFilter');
Route::post('jobFilter1', 'JobController@jobFilter1');
Route::post('user-detail-by-id', 'RegisterController@userDetailById');
Route::post('delete-user-account', 'RegisterController@userDeleteAccount');
Route::post('reactive_account', 'RegisterController@reactiveAccount');
Route::post('forgot-password','RegisterController@forgotPassword');
Route::post('forgot-submit-password','RegisterController@submit_pass');
Route::post('report','RegisterController@report');

Route::post('add-story','StatusController@addStory');
Route::post('view-own-story','StatusController@viewOwnStory');
Route::post('share-story','StatusController@shareStory');
Route::post('user-show-story','StatusController@userShowStory');
Route::post('user-story-seen','StatusController@userStorySeen');
Route::post('view-story-seen','StatusController@viewStorySeen');
Route::get('expire-story','StatusController@expireStory');
Route::post('tag-search-user', 'StatusController@tagSearchUser');
Route::post('delete-story', 'StatusController@deleteStory');
Route::post('get-hashtag', 'RegisterController@getALLHashTags');
Route::post('hide-user', 'RegisterController@hideUser');


Route::post('send_message','MessageController@send_message');
Route::post('delete_message','MessageController@deleteMessage');
Route::post('edit_message','MessageController@editMessage');
Route::post('get_conversation_id','MessageController@get_conversation_id');
Route::post('send_message_media','MessageController@send_message_media');
Route::post('get_conversation_user_list','MessageController@get_conversation_user_list');
Route::post('get_user_messages','MessageController@get_user_messages');
Route::post('get_feed_shop','MessageController@get_feed_shop');
Route::post('read_bit_messages','MessageController@read_bit_messages');
Route::post('search_conversation_user','MessageController@search_conversation_user');
Route::post('get_user_profile','MessageController@get_user_profile');

Route::post('user_values_count','RegisterController@user_values_count');
Route::post('get_public_private_user_detail','RegisterController@get_public_private_user_detail');
Route::post('update_public_private_user_detail','RegisterController@update_public_private_user_detail');
Route::post('get_follow_unfollow_followers_list','RegisterController@get_follow_unfollow_followers_list');
Route::post('delete_post','RegisterController@delete_post');
Route::post('delete_approved_product','RegisterController@delete_approved_product');
Route::post('change_password','RegisterController@change_password');
Route::post('get_post_likes_comments_users','RegisterController@get_post_likes_comments_users');
Route::post('search_posts_with_hashtags','RegisterController@search_posts_with_hashtags');
Route::post('discover','RegisterController@discover');
Route::post('edit_post','RegisterController@edit_post');
Route::post('common_report','RegisterController@common_report');
Route::post('check_username','RegisterController@check_username');
Route::get('get_filters','RegisterController@get_filters');



Route::post('add_contact','MessageController@add_contact');
Route::post('delete_contact','MessageController@delete_contact');


Route::post('delete_conversation','MessageController@delete_conversation');
Route::post('list_of_all_users','RegisterController@list_of_all_users');
// Route::post('admin_block_user','RegisterController@admin_block_user');

Route::post('get_taglist','RegisterController@get_taglist');



Route::post('get_counter_msg_user','MessageController@get_counter_msg_user');
Route::post('contact_email','RegisterController@contact_email');
Route::post('send_email','RegisterController@send_email');
Route::middleware('auth:api')->group( function () {
	Route::resource('products', 'API\ProductController');
});



/****************** 26-2-2020 ******************************************************/

Route::post('change_email_name','RegisterController@change_email_name');
Route::post('block_user','RegisterController@block_user');
Route::post('unblock_user','RegisterController@unblock_user');
Route::post('list_of_block_users_against_id','RegisterController@list_of_block_users_against_id');


/****************** 11-3-2020 ****************************************************/

Route::post('app_setting','RegisterController@app_setting');
Route::post('get_app_setting','RegisterController@get_app_setting');




/****************** 13-3-2020 ***************************************************/

Route::post('get_statuses','RegisterController@get_statuses');
Route::post('follow_statuses','RegisterController@follow_statuses');
Route::post('unfollow_statuses','RegisterController@unfollow_statuses');

/****************** 24-3-2020 ***************************************************/

Route::post('save_or_unsave_post','RegisterController@save_or_unsave_post');
Route::post('get_save_post','RegisterController@get_save_post');

/****************** 25-3-2020 ***************************************************/

Route::post('block_unblock_delete_user_by_admin','RegisterController@block_unblock_delete_user_by_admin');
Route::get('blocked_user_list_by_admin','RegisterController@blocked_user_list_by_admin');

/****************** 31-3-2020 ***************************************************/

Route::post('follow_unfollow_hash_tag','RegisterController@follow_unfollow_hash_tag');

/****************** 6-4-2020 ***************************************************/

Route::post('admin_search_user','RegisterController@admin_search_user');
Route::post('post_views_comments_likes_count_by_country','RegisterController@post_views_comments_likes_count_by_country');
Route::post('private_post_access_subscription','RegisterController@private_post_access_subscription');
Route::post('post_click','RegisterController@post_click');
Route::post('update_app_token','RegisterController@update_app_token');
Route::get('get_pages_text','RegisterController@get_pages_text');
Route::post('check_email_exists', 'RegisterController@check_email_exists');
Route::get('get_app_background', 'RegisterController@get_app_background');
Route::post('create_or_update_user_shop_profile', 'RegisterController@create_or_update_user_shop_profile');
Route::get('terms_conditions', 'RegisterController@terms_conditions');
Route::post('update_general_profile', 'RegisterController@update_general_profile');

/****************** 6-22-2020 ***************************************************/
/****************** shop products ***************************************************/
Route::post('new_product', 'ShopController@shop_product');
Route::post('all_products', 'ShopController@all_products');
Route::post('add_to_cart', 'ShopController@add_to_cart');
Route::post('product_quantity', 'ShopController@productQuantity');
Route::post('cart_products', 'ShopController@get_cart_product');
Route::post('shop_view', 'ShopController@shop_view_toggle');
Route::post('placeOrder', 'ShopController@place_order');
Route::post('total_products', 'ShopController@total_cart_products');
Route::post('emailSeller', 'ShopController@email_seller');
Route::post('emailInvoice', 'ShopController@email_invoice');
Route::post('get_orders', 'ShopController@getOrders');
Route::post('product_rating', 'ShopController@product_rating');
Route::post('viewOrder', 'ShopController@view_order');
Route::post('shop_products', 'ShopController@get_shop_product');
Route::post('shop_product_meta', 'ShopController@shop_product_meta');
Route::post('shop_product_comment', 'ShopController@shop_product_comment');
Route::post('get_shop_product_comments', 'ShopController@get_shop_product_comments');
Route::post('product_approve', 'ShopController@product_approve');
Route::post('product_delete', 'ShopController@product_delete');

/****************** product count API ***************************************************/
Route::post('get_product_count', 'ShopController@get_product_count');



/****************** 23-11-2020 ***************************************************/
Route::get('category','CategoryController@getcategory');
Route::get('user_category','CategoryController@getusercategory');
Route::put('update_product/{id}', 'ShopController@update_product');
Route::post('invite', 'RegisterController@sendInvitation'); 
Route::post('accept_invite', 'RegisterController@acceptInvitation'); 
Route::post('invite_count', 'RegisterController@getInvitationCount'); 
Route::post('get_invite', 'RegisterController@getInvitation'); 
Route::post('delete_invite', 'RegisterController@deleteInvitation'); 


Route::post('get-activity', 'RegisterController@getActivity'); 
Route::post('set_activity_time', 'RegisterController@set_Activity_Time'); 
Route::post('push_tag_people', 'RegisterController@send_tag_people'); 
