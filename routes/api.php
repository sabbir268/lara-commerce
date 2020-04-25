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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Login And Resister with JSON Web Token(JWT)
Route::post('register', 'API\UserApiController@register');
Route::post('login', 'API\UserApiController@login');
Route::post('logout', 'API\UserApiController@logout');
Route::get('email/resend', 'API\VerificationController@resend')->name('verification.resend');
Route::post('email/verify/{id}/{hash}', 'API\VerificationController@verify')->name('verification.verify');
// Route::post('password/email', 'API\ForgotPasswordController@sendResetLinkEmail');
// Route::post('password/reset', 'API\ResetPasswordController@reset');

//User
Route::group(['middleware' => ['jwt.auth', 'mail.verified']], function(){
    Route::get('user', 'API\UserApiController@user');
    Route::post('user', 'API\UserApiController@user_update');
    Route::post('shipping_info', 'API\UserApiController@shipping_info');
    Route::get('wishlist', 'API\UserApiController@wishlist');
    Route::post('wishlist', 'API\UserApiController@add_wishlist');
    Route::delete('wishlist', 'API\UserApiController@remove_wishlist');
    Route::get('cart', 'API\UserApiController@cart');
    Route::post('cart', 'API\UserApiController@add_cart');
    Route::delete('cart', 'API\UserApiController@remove_cart');
});

Route::get('banner', 'API\GeneralApiController@banner');
Route::get('flash_sales', 'API\GeneralApiController@flash_sales');
Route::get('brands', 'API\GeneralApiController@brands');
Route::get('categories', 'API\GeneralApiController@categories');
Route::get('sub_categories/{category_id}', 'API\GeneralApiController@sub_categories');
Route::get('category/products/{category_id}', 'API\GeneralApiController@category_product');
Route::get('sub_category/products/{sub_category_id}', 'API\GeneralApiController@sub_category_product');
