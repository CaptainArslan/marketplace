<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;

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

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Access-Control headers are received during OPTIONS requests
// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//     if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
//         header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
//     if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
//         header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
//     exit(0);
// }

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/{home?}', 'SiteController@index')->name('homepage');
Route::get('all-products/{fetch?}', 'SiteController@allProducts')->name('fetch.products');
Route::get('product/search/{query?}', 'SiteController@productSearch')->name('search.product');
Route::get('product/filtered/{query?}', 'SiteController@productFilter')->name('product.filtered');
Route::get('product-details/{slug}/{id}/{fetch}', 'SiteController@productDetails')->name('product.details');
Route::post('product/add-to-cart', 'SellController@addToCart')->name('addtocart');
Route::get('product/cart/{ordernumber?}', 'SellController@carts')->name('carts');
Route::get('product/remove-cart/{id}', 'SellController@removeCart')->name('remove.cart');

Route::post('auth/sign-up', 'Auth\RegisterController@register');
Route::post('auth/sign-in', 'Auth\LoginController@login');
Route::post('auth/password/reset', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('auth/password/update', 'Auth\ForgotPasswordController@verifyCode');

Route::post('test/email', 'Auth\ForgotPasswordController@sendEmail');

Route::middleware('jwt.verify')->group(function () {
    Route::post('auth/logout', 'Auth\LoginController@logout');
    Route::post('password/change', 'UserController@submitPassword');

    Route::post('checkout', 'SellController@checkoutPayment');
    Route::post('checkout/process', 'Gateway\PaymentController@paymentInsert');
    Route::post('payment/process', 'Gateway\stripe\ProcessController@ipnApi');

    Route::get('profile/setting', 'UserController@profile');
    Route::post('profile-setting', 'UserController@submitProfile');

    Route::get('user/puchased-list', 'UserController@purchasedProductApi');

    // prefix: user
    Route::name('iframe.api.')->prefix('iframe')->group(function () {
        Route::get('user/dashboard', 'UserController@home')->name('user.dashboard');
        Route::get('deposit/history', 'UserController@depositHistory')->name('deposit.history');
        Route::get('transaction', 'UserController@transaction')->name('user.transaction');
        Route::get('purchased-product/list', 'UserController@purchasedProduct')->name('purchased.product');
        Route::get('ticket', 'TicketController@supportTicket')->name('ticket');
        Route::get('meetings/all', 'MeetingController@allMeeting')->name('meeting.all');
        Route::get('/new/ticket/{id?}', 'TicketController@openSupportTicket')->name('ticket.open');
    });
});
