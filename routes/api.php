<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// public routes
Route::get('/{home?}', 'SiteController@index')->name('homepage');
Route::get('all-products/{fetch?}', 'SiteController@allProducts')->name('fetch.products');
Route::get('product/search/{query?}', 'SiteController@productSearch')->name('search.product');
Route::get('product/filtered/{query?}', 'SiteController@productFilter')->name('product.filtered');
Route::get('product-details/{slug}/{id}/{fetch}/{ordernumber?}', 'SiteController@productDetails')->name('product.details');
Route::post('product/add-to-cart', 'SellController@addToCart')->name('addtocart');
Route::get('product/cart/{ordernumber?}', 'SellController@carts')->name('carts');
Route::get('product/remove-cart/{id}', 'SellController@removeCart')->name('remove.cart');
Route::get('product/empty-cart/{order_number}', 'SellController@emptyCart')->name('empty.cart');

// Authentication
Route::post('auth/sign-up', 'Auth\RegisterController@register');
Route::post('auth/sign-in', 'Auth\LoginController@login');
Route::post('auth/password/reset', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('auth/password/update', 'Auth\ForgotPasswordController@verifyCode');

Route::post('test/email', 'Auth\ForgotPasswordController@sendEmail');

// token verified routes
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
        Route::get('meetings/all', 'MeetingController@allMeeting')->name('meeting.all');

        
        Route::get('ticket', 'TicketController@supportTicket')->name('ticket');
        // Route::get('tickets/view/{id}', 'SupportTicketController@ticketReply')->name('ticket.view');
        Route::get('/new/ticket/{id?}', 'TicketController@openSupportTicket')->name('ticket.open');
        Route::post('ticket/store', 'TicketController@storeSupportTicket')->name('ticket.store');
        Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.show');
        Route::post('/reply/{ticket}', 'TicketController@replyTicket')->name('ticket.reply');
        Route::post('rating', 'UserController@rating')->name('rating');
        
        //Product
        Route::get('product/all', 'ProductController@allProduct')->name('product.all');
        Route::get('product-details/{slug}/{id}', 'SiteController@productDetails')->name('product.details');
        Route::get('product/new', 'ProductController@newProduct')->name('product.new');
        Route::post('product/store', 'ProductController@storeProduct')->name('product.store');
        Route::get('product/edit/{id}', 'ProductController@editProduct')->name('product.edit');
        Route::post('product/update/{id}', 'ProductController@updateProduct')->name('product.update');
        Route::post('product/delete', 'ProductController@deleteProduct')->name('product.delete');

        // sell
        Route::get('sells-log', 'UserController@sellLog')->name('sell.log');
        Route::get('track-sell', 'UserController@trackSell')->name('track.sell');
        Route::post('track-sell-search', 'UserController@trackSellSearch')->name('track.sell.search');
        Route::get('customfield/all', 'UserController@allCustomfield')->name('allCustomfield');
        Route::get('emailtemplate/all', 'UserController@allEmailTemplate')->name('emailtemplate');

        // subscription
        Route::get('plans', 'SubscriptionController@getplans')->name('getplans');

        // withdraw
        Route::get('download/{id}', 'UserController@download')->name('download');
        Route::get('invoice/{id}', 'UserController@invoice')->name('invoice');
    });
});
