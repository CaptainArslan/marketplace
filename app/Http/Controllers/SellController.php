<?php

namespace App\Http\Controllers;

use App\Sell;
use App\Level;
use App\Order;
use App\Deposit;
use App\Product;
use Carbon\Carbon;
use App\ProductBump;
use App\Transaction;
use App\BumpResponse;
use App\Subscription;
use App\GeneralSetting;
use App\GatewayCurrency;
use App\WishlistProduct;
use App\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

class SellController extends Controller
{
    public $activeTemplate;
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function addToCart(Request $request)
    {
        $apidata = [];
        $request->validate([
            'license' => 'required|numeric|in:1,2',
            'product_id' => 'required',
            'order_number' => 'nullable',
        ]);

        try {
            if ($request->has('order_number')) {
                $order = Order::where('order_number', $request->order_number)->where('product_id', $request->product_id)->first();
                if ($order) {
                    Log::info('Order Number: ' . $request->order_number . ' Product ID: ' . $request->product_id . ' Already Exists in Cart and deleted');
                    $order->bumpresponses()->delete();
                    $order->delete();
                }
            }

            $product = Product::where('status', 1)->whereHas('user', function ($query) {
                $query->where('status', 1);
            })->findOrFail(Crypt::decrypt($request->product_id));

            $user = auth()->user() ?? auth('user')->user();
            if ($user) {
                if ($product->user->id == $user->id) {
                    $apidata['status'] = "Error";
                    $apidata['data'] = " ";
                    $apidata['message'] = "It is your own product. You are not allowed to purchase this";
                    if ($request->is('api/*')) {
                        return response()->json($apidata);
                    }
                    $notify[] = ['error', 'It is your own product. You are not allowed to purchase this'];
                    return back()->withNotify($notify);
                }
            }
            // for to create order number
            if ($user) {
                $orderNumber = $user->id;
                Log::info('user loggend in and the order number is = ' . $orderNumber);
            } else {
                if ($request->is('api/*')) {
                    Log::info('add to cart api');
                    if ($request->has('order_number') && !empty($request->order_number)) {
                        $orderNumber = $request->order_number;
                        Log::info('if request has order number' . $orderNumber);
                    } else {
                        $orderNumber = getTrx(8);
                        $apidata['order_number'] = $orderNumber;
                        Log::info('if request does not have order number' . $orderNumber);
                    }
                } else {
                    if (session()->has('order_number')) {
                        $orderNumber = session()->get('order_number');
                    }
                    if (!session()->has('order_number')) {
                        $orderNumber = getTrx(8);
                        session()->put('order_number', $orderNumber);
                    }
                }
            }

            $orderdetail = managebumps($request->license, $product);
            $totalPrice = $orderdetail[0];
            $supportFee = $orderdetail[1];

            $general = GeneralSetting::first();

            if ($product->support == 1 && $request->extented_support) {

                $support_time = Carbon::now()->addMonths($general->extended)->format('Y-m-d');
            }
            if (($product->support == 0) || ($product->support == 1 && !$request->extented_support)) {

                $supportFee = 0;

                if ($request->license == 1) {
                    $totalPrice = $product->regular_price;
                }
                if ($request->license == 2) {
                    $totalPrice = $product->extended_price;
                }
            }

            if ($product->support == 1 && !$request->extented_support) {
                $support_time = Carbon::now()->addMonths($general->regular)->format('Y-m-d');
            }

            if ($product->support == 0) {
                $support_time = null;
            }

            $totalPrice = $totalPrice + $request->bump_fee;
            $order = new Order();
            $order->order_number = $orderNumber;
            $order->code = getTrx(20);
            $order->author_id = $product->user->id;
            $order->product_id = $product->id;
            $order->license = $request->license;
            $order->support = $product->support;
            $order->support_time = $support_time;
            $order->support_fee = $supportFee;
            $order->bump_fee = $request->bump_fee;
            $order->product_price = $request->license == 1 ? $product->regular_price : ($request->license == 2 ? $product->extended_price : '');
            $order->total_price = $totalPrice;
            $order->save();

            if ($request->bump_fee != 0) {
                if (!is_array($request->bump)) {
                    $bumpids =  json_decode($request->bump, true);
                } else {
                    $bumpids =  json_decode($request->bump, true);
                }
                if (!is_array($request->pages)) {
                    $pages = json_decode($request->pages, true);
                } else {
                    $pages = json_decode($request->pages, true);
                }

                foreach ($bumpids as $key => $value) {
                    $bump = ProductBump::findorFail($key);
                    $newbump = new BumpResponse;
                    $newbump->order_id = $order->id;
                    $newbump->bump_id = $bump->id;
                    $newbump->pages = $pages[$key];
                    $newbump->sell_id = null;
                    if ($newbump->pages > 0) {
                        $newbump->price = $newbump->pages * $value;
                    } else {
                        $newbump->price = $value;
                    }
                    $newbump->save();
                }
            }

            $notify[] = ['success', 'Product added to cart successfully'];

            if (empty($order)) {
                $apidata['status'] = "Error";
                $apidata['data'] = " ";
                $apidata['message'] = "Product Not added Due to Some Error While Saving";
            } else {
                $apidata['status'] = "Success";
                $apidata['data'] = $order;
                $apidata['message'] = "Product added to cart successfully";
            }
            if ($request->is('api/*')) {
                return response()->json($apidata);
            }
            return back()->withNotify($notify);
        } catch (DecryptException $e) {
            if ($request->is('api/*')) {
                $apidata['status'] = "Error";
                $apidata['data'] = '';
                $apidata['message'] = $e->getMessage();
                return response()->json($apidata);
            }
            return back()->withNotify($e->getMessage());
        }
    }
    
    public function addtowishlist($id)
    {

        $product = Product::where('status', 1)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->findOrFail(Crypt::decrypt($id));

        if (auth()->user()) {

            if ($product->user->id == auth()->user()->id) {
                $notify[] = ['error', 'It is your own product. You are not allowed to purchase this'];
                return back()->withNotify($notify);
            }
        }
        if (auth()->user()) {
            $itemnumber = auth()->user()->id;
        } else {
            if (session()->has('itemnnumber')) {
                $itemnumber = session()->get('item_number');
            }
            if (!session()->has('itemn_umber')) {
                $itemnumber = getTrx(8);
                session()->put('item_number', $itemnumber);
            }
        }
        $item = new WishlistProduct();
        $item->product_id = $product->id;
        $item->user_id = auth()->user()->id;
        $item->save();
        $notify[] = ['success', 'Product added to wishlist successfully'];
        return back()->withNotify($notify);
    }



    public function carts(Request $request, $ordernumber = null)
    {
        $page_title = 'Cart';

        if (auth()->user()) {
            $user = auth()->user();
            Order::where('author_id', $user->id)->delete();
            $orders = Order::with('product', 'bumpresponses')->where('order_number', $user->id)->get();
        } else {
            if ($request->is('api/*')) {
                if (empty($ordernumber) || is_null($ordernumber)) {
                    $apidata['status'] = "Success";
                    $apidata['data'] = " ";
                    $apidata['message'] = "No Product Added";
                } else {
                    // $order = Order::where('order_number', $ordernumber)->get();
                    // // $orderNumber = $user->id;
                    // if($order->count() > 0){
                    //     $orderNumber = $ordernumber;
                    // }
                    $apidata['status'] = "Success";
                    $orders = Order::with('product', 'bumpresponses')->where('order_number', $ordernumber)->get()->map(function ($order) {
                        $order->encrypted_id = Crypt::encrypt($order->id);
                        $order->encrypted_order_number = Crypt::encrypt($order->order_number);
                        return $order;
                    });
                    // $balance = auth()->user()->balance ?? 0;
                    // $apidata['user_balance'] = getAmount($balance);
                    $apidata['data'] = $orders;
                    $apidata['message'] = "Product Retrived Successfully";
                }
                return response()->json($apidata);
            } else {
                $orders = Order::with('product', 'bumpresponses')->where('order_number', session()->get('order_number'))->get();
            }
        }
        return view($this->activeTemplate . 'cart', compact('page_title', 'orders'));
    }


    public function wishlists()
    {
        $page_title = 'Wishlist';

        if (auth()->user()) {
            $user = auth()->user();
            $items = WishlistProduct::with('product', 'product.user')->where('user_id', $user->id)->get();
        } else {
            $items = WishlistProduct::where('user_id', session()->get('item_number'))->get();
        }
        return view($this->activeTemplate . 'wishlist', get_defined_vars());
    }
    public function removeCart(Request $request, $id)
    {
        $order = Order::findOrFail(Crypt::decrypt($id));
        $apidata = [];
        if ($request->is('api/*') && empty($order)) {
            $apidata['status'] = "Error";
            $apidata['data'] = "";
            $apidata['message'] = "No Product Added";
            return $apidata;
        }
        $bump = BumpResponse::Where('order_id', $order->id);
        $order->delete();
        $bump->delete();

        if ($request->is('api/*')) {
            $apidata['status'] = "Success";
            $apidata['data'] = "";
            $apidata['message'] = "Product has been remove from cart successfully";
            return $apidata;
        }
        $notify[] = ['success', 'Product has been remove from cart successfully'];
        return back()->withNotify($notify);
    }

    public function emptyCart(Request $request, $order_number)
    {
        $order = Order::where('order_number', Crypt::decrypt($order_number))->get();

        if ($request->is('api/*') && $order->count() == 0) {
            $apidata['status'] = "Error";
            $apidata['data'] = "";
            $apidata['message'] = "No Product Added";
            return $apidata;
        }

        foreach ($order as $item) {
            $bump = BumpResponse::Where('order_id', $item->id);
            if ($bump->count() > 0) {
                $bump->delete();
            }
            $item->delete();
        }

        if ($request->is('api/*')) {
            $apidata['status'] = "Success";
            $apidata['data'] = "";
            $apidata['message'] = "Product has been remove from your cart successfully";
            return $apidata;
        }
        $notify[] = ['success', 'Product has been remove from your cart successfully'];
        return back()->withNotify($notify);
    }

    public function removewishlist($id)
    {
        $item = WishlistProduct::findOrFail(Crypt::decrypt($id));
        $item->delete();

        $notify[] = ['success', 'Product has been remove from Wishlist successfully'];
        return back()->withNotify($notify);
    }

    public function checkoutPayment(Request $request)
    {
        if ($request->is('api/*')) {
            $validator = Validator::make($request->all(), [
                'wallet_type' => 'required|in:own,online',
                'subscription' => 'required',
                'order_number' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors()->first());
            }
        } else {
            $request->validate([
                'wallet_type' => 'required|in:own,online',
            ]);
        }

        $user = auth()->user() ?? auth('user')->user();

        if ($request->wallet_type == 'own' && $request->subscription == 0) {
            if ($request->order_number) {
                $orders = Order::where('order_number', $request->order_number)->get();
            } else {
                $orders = Order::where('order_number', $user->id)->get();
            }

            if (count($orders) > 0) {
                // $user = auth()->user();
                $totalPrice = $orders->sum('total_price');
                $gnl = GeneralSetting::first();

                if ($totalPrice > (float) $user->balance) {
                    if ($request->is('api/*')) {
                        return $this->respondWithError("You do not have enough balance!");
                    }
                    $notify[] = ['error', 'You do not have enough balance.'];
                    return back()->withNotify($notify);
                }

                if ($totalPrice <= $user->balance) {

                    try {
                        DB::beginTransaction();
                        foreach ($orders as $item) {
                            $sell = new Sell();
                            $sell->code = $item->code;
                            $sell->author_id = $item->author_id;
                            $sell->user_id = $user->id;
                            $sell->product_id = $item->product_id;
                            $sell->license = $item->license;
                            $sell->support = $item->support;
                            $sell->support_time = $item->support_time;
                            $sell->support_fee = $item->support_fee;
                            $sell->product_price = $item->product_price;
                            $sell->total_price = $item->total_price;
                            $sell->bump_fee = $item->bump_fee;
                            $sell->status = 1;
                            $sell->save();

                            if ($sell->bump_fee != 0) {
                                $bump = BumpResponse::Where('orderr_id', $item->id);
                                $bump->update([
                                    'sell_id' => $sell->id,
                                ]);
                            }

                            $sell->product->total_sell += 1;
                            $sell->product->save();

                            $levels = Level::get();
                            $author = $item->author;

                            $author->earning = $author->earning + ($sell->total_price - ($sell->product->category->buyer_fee + (($sell->total_price * $author->levell->product_charge) / 100)));
                            $author->balance = $author->balance + $sell->total_price;

                            $authorTransaction = new Transaction();
                            $authorTransaction->user_id = $author->id;
                            $authorTransaction->amount = $sell->total_price;
                            $authorTransaction->post_balance = $author->balance;
                            $authorTransaction->charge = 0;
                            $authorTransaction->trx_type = '+';
                            $authorTransaction->details = getAmount($authorTransaction->amount) . ' ' . $gnl->cur_text . ' Added with Balance For selling a product named ' . $item->product->name;
                            $authorTransaction->trx = getTrx();
                            $authorTransaction->save();

                            $author->balance = $author->balance - ($sell->product->category->buyer_fee + (($sell->total_price * $author->levell->product_charge) / 100));
                            $author->save();


                            if (($author->earning >= $author->levell->earning) && ($author->earning < $levels->max('earning'))) {
                                updateAuthorLevel($author);
                            }

                            $authorTransaction = new Transaction();
                            $authorTransaction->user_id = $author->id;
                            $authorTransaction->amount = $sell->product->category->buyer_fee + (($sell->total_price * $author->levell->product_charge) / 100);
                            $authorTransaction->post_balance = $author->balance;
                            $authorTransaction->charge = 0;
                            $authorTransaction->trx_type = '-';
                            $authorTransaction->details = $sell->product->category->buyer_fee + (($sell->total_price * $author->levell->product_charge) / 100) . ' ' . $gnl->cur_text . ' Charged For selling a product named ' . $item->product->name;
                            $authorTransaction->trx = getTrx();
                            $authorTransaction->save();

                            if ($item->license == 1) {
                                $licenseType = 'Regular';
                            }
                            if ($item->license == 2) {
                                $licenseType = 'Extended';
                            }

                            // notify($author, 'PRODUCT_SOLD', [
                            //     'product_name' => $item->product->name,
                            //     'license' => $licenseType,
                            //     'currency' => $gnl->cur_text,
                            //     'product_amount' => getAmount($sell->product_price),
                            //     'support_fee' => getAmount($sell->support_fee),
                            //     'bump_fee' => getAmount($sell->bump_fee),
                            //     'support_time' => $sell->support_time ? $sell->support_time : 'No support',
                            //     'trx' => $authorTransaction->trx,
                            //     'purchase_code' => $sell->code,
                            //     'post_balance' => $author->balance,
                            //     'buyer_fee' => $author->levell->product_charge,
                            //     'amount' => $sell->total_price - ($sell->product->category->buyer_fee + (($sell->total_price * $author->levell->product_charge) / 100)),
                            // ]);

                        }

                        $user->balance = $user->balance - $totalPrice;
                        $user->save();

                        $transaction = new Transaction();
                        $transaction->user_id = $user->id;
                        $transaction->amount = $totalPrice;
                        $transaction->post_balance = $user->balance;
                        $transaction->charge = 0;
                        $transaction->trx_type = '-';
                        $transaction->details = getAmount($totalPrice) . ' ' . $gnl->cur_text . ' Subtracted From Your Own Wallet For Purchasing Products.';
                        $transaction->trx = getTrx();
                        $transaction->save();

                        $productList = '';

                        foreach ($orders as $item) {
                            $productList .= '# ' . $item->product->name . '<br>';
                        }

                        // notify($user, 'PRODUCT_PURCHASED', [
                        //     'method_name' => 'Own Wallet',
                        //     'currency' => $gnl->cur_text,
                        //     'total_amount' => getAmount($totalPrice),
                        //     'post_balance' => $user->balance,
                        //     'product_list' => $productList,
                        // ]);

                        foreach ($orders as $item) {
                            $item->delete();
                        }
                        session()->forget('order_number');

                        DB::commit();
                        if ($request->is('api/*')) {
                            return $this->respondWithSuccess($user, 'Your Order has been placed!');
                        }

                        return redirect()->route('user.purchased.product');
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        if ($request->is('api/*')) {
                            return $this->respondWithError('Error Occured while placing order!');
                        }
                    }
                }
            } else {

                if ($request->is('api/*')) {
                    return $this->respondWithError('No products in your cart!');
                }

                $notify[] = ['error', 'No products in your cart.'];
                return back()->withNotify($notify);
            }
        }
        if ($request->wallet_type == 'online' && $request->subscription == 0) {
            $orderNumber = null;
            if ($request->order_number) {
                $orderNumber = $request->order_number;
            } else {
                $orderNumber = $user->id;
            }

            $orders = Order::where('order_number', $orderNumber)->get();

            if (count($orders) > 0) {
                if ($request->is('api/*')) {
                    $publishable_keys = []; // Initialize as an array
                    $totalPrice = $orders->sum('total_price');
                    $gatewayCurrency = GatewayCurrency::where('min_amount', '<', $totalPrice)
                        // ->select('id', 'name', 'method')
                        ->where('max_amount', '>', $totalPrice)
                        ->whereHas('method', function ($gate) {
                            $gate->where('status', 1);
                        })
                        // ->with('method')
                        ->orderBy('method_code')
                        ->get();

                    foreach ($gatewayCurrency as $gate) {
                        $parameter = json_decode($gate->gateway_parameter, true);
                        $publishable_keys[$gate->gateway_alias] = $parameter['publishable_key'] ?? null;
                    }

                    $subid = $request->subid ?? 0;

                    $payment = $this->paymentInsert($request, $subid, $totalPrice, $user);

                    $data = [
                        'order' => $payment,
                        'publishable_keys' => $publishable_keys, // Use the collected array
                        // 'total_price' => $totalPrice,
                        // 'gateway_currency' => $gatewayCurrency,
                    ];

                    return $this->respondWithSuccess($data, 'Checkout now!');
                }
                return redirect()->route('user.payment');
            } else {
                if ($request->is('api/*')) {
                    return $this->respondWithError('No products in your cart!');
                }
                // user .payment function
                $notify[] = ['error', 'No products in your cart.'];
                return back()->withNotify($notify);
            }
        }
        if ($request->wallet_type == 'own' && $request->subscription == 1) {
            if ($request->is('api/*')) {
                $validator = Validator::make($request->all(), [
                    'subscription_id' => 'required|exists:subscriptions,id',
                ]);
                if ($validator->fails()) {
                    return $this->respondWithError($validator->errors()->first());
                }
            }
            $gnl = GeneralSetting::first();
            $usersub = new UserSubscription();
            $subscription_id = $request->subscriptionid;
            if ($request->is('api/*')) {
                $subscription_id = $request->subscription_id;
            }

            $usersub->sub_id = $subscription_id;
            $sub = Subscription::where('id', $subscription_id)->first();
            $newchargeprice = $sub->price;
            $subuser = UserSubscription::where('user_id', $user->id)->where('status', 1)->with('subscriptions')->first();
            if (!is_null($subuser)) {
                if ($subuser->subscriptions->plan_type == 1) {
                    $totaldays = Carbon::parse($subuser->expire_on)->diffInDays(Carbon::parse($subuser->start_on));
                    $remaindays = Carbon::parse($subuser->expire_on)->diffInDays(Carbon::now());
                    $perday = $subuser->subscriptions->price / $totaldays;
                    $currentprice = $remaindays * $perday;
                    $newchargeprice = $sub->price - $currentprice;
                    // dd($subuser->subscriptions->price, $totaldays, $remaindays, $perday, $currentprice, $newcharge);
                }
                $subuser->status = 0;
                $subuser->save();
            }

            if ($sub->plan_type == 1) {
                $detail = "Monthly";
                $usersub->start_on = Carbon::now();
                $usersub->expire_on = Carbon::now()->addMonths(1);
            } else {
                $detail = "oneTime";
                $usersub->expire_on = null;
                $usersub->start_on = Carbon::now();
            }
            $usersub->user_id = $user->id;
            $usersub->status = 1;
            $usersub->save();

            $user->balance = $user->balance - $newchargeprice;
            $user->save();
            //save Transections of Subscription
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $newchargeprice;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = getAmount($newchargeprice) . ' ' . $gnl->cur_text . ' Subtracted From Your Own Wallet for the' . $detail . 'subscription Packg you buy.';
            $transaction->trx = getTrx();
            $transaction->save();
            if ($request->is('api/*')) {
                return $this->respondWithSuccess(null, 'Order purchased successfully!', [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ], 200);
            }

            return redirect()->route('user.purchased.product');
        }
        if ($request->wallet_type == 'online' && $request->subscription == 1) {

            if ($request->is('api/*')) {
                $validator = Validator::make($request->all(), [
                    'subscription_id' => 'required|exists:subscriptions,id',
                ]);
                if ($validator->fails()) {
                    return $this->respondWithError($validator->errors()->first());
                }
            }
            $subscription_id = $request->subscriptionid;
            if ($request->is('api/*')) {
                $subscription_id = $request->subscription_id;
            }

            $sub = Subscription::where('id', $request->subscription_id)->first();

            if ($request->is('api/*')) {
                $subscription = Subscription::where('id', $sub->id)->first();
                $gatewayCurrency = GatewayCurrency::where('min_amount', '<', $subscription->price)
                    ->where('max_amount', '>', $subscription->price)
                    ->whereHas('method', function ($gate) {
                        $gate->where('status', 1);
                    })
                    ->with('method')
                    ->orderby('method_code')
                    ->get();
                return $this->respondwithSuccess($gatewayCurrency, 'Checkout now!');
            }

            return redirect()->route('user.subscriptionpayment', $sub->id);
        }
    }

    public function paymentInsert($request, $subid = 0, $newchargeprice, $user)
    {
        if ($request->has('order_number')) {
            $orderNumber = $request->order_number;
        } else {
            $orderNumber = $user->id;
        }

        $orders = Order::where('order_number', $orderNumber)->get();
        $totalPrice = $orders->sum('total_price');

        if ($totalPrice != (float) $newchargeprice) {
            return $this->respondWithError('Something went wrong!');
        }
        $gate = GatewayCurrency::where('method_code', $request->method_code ?? 103)->where('currency', $request->currency ?? 'USD')->first();

        if (!$gate) {
            return $this->respondWithError('Invalid Gateway!');
        }

        $data = new Deposit();
        if ($subid == 0) {
            $data->order_number = $orders[0]->order_number;
            $data->sub_id = null; //means this deposit is for the produt order
        } else {
            $data->order_number = null;
            $data->sub_id = $subid; // means this deposit is for Subscription buy
            $sub = Subscription::where('id', $subid)->first();
            $subuser = UserSubscription::where('user_id', auth()->user()->id)->with('subscriptions')->first();

            if (!is_null($subuser)) {
                if ($subuser->subscriptions->plan_type == 1) {
                    $totaldays = Carbon::parse($subuser->expire_on)->diffInDays(Carbon::parse($subuser->start_on));
                    $remaindays = Carbon::parse($subuser->expire_on)->diffInDays(Carbon::now());
                    $perday = $subuser->subscriptions->price / $totaldays;
                    $currentprice = $remaindays * $perday;
                    $newchargeprice = $sub->price - $currentprice;
                    // dd($subuser->subscriptions->price, $totaldays, $remaindays, $perday, $currentprice, $newcharge);
                }
            }
        }

        $charge = getAmount($gate->fixed_charge + ($newchargeprice * $gate->percent_charge / 100));
        $payable = getAmount($newchargeprice + $charge);
        $final_amo = getAmount($payable * $gate->rate);
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $newchargeprice;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = getAmount($final_amo);
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();

        $deposit = Deposit::where('trx', $data->trx)->orderBy('id', 'DESC')->with('gateway')->first();
        if (is_null($deposit) || $deposit->status != 0) {
            return $this->respondWithError('Invalid Deposit Request!');
        }

        if ($deposit->method_code >= 1000) {
            $this->userDataUpdate($deposit);
            return $this->respondWithError('Your deposit request is queued for approval.');
        }

        return $data;
    }
}
