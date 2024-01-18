<?php

namespace App\Http\Controllers\Gateway;

use App\Sell;
use App\User;
use stdClass;
use App\Level;
use App\Order;
use App\Deposit;
use App\Transaction;
use App\BumpResponse;
use App\Notification;
use App\Subscription;
use App\GeneralSetting;
use App\GatewayCurrency;
use App\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public $activeTemplate;

    public function __construct()
    {
        return $this->activeTemplate = activeTemplate();
    }

    public function deposit()
    {

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $page_title = 'Deposit Methods';

        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'page_title'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();
        $gate = GatewayCurrency::where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid Gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please Follow Deposit Limit'];
            return back()->withNotify($notify);
        }

        $charge = getAmount($gate->fixed_charge + ($request->amount * $gate->percent_charge / 100));
        $payable = getAmount($request->amount + $charge);
        $final_amo = getAmount($payable * $gate->rate);

        $data = new Deposit();
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = getAmount($final_amo);
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();
        session()->put('Track', $data['trx']);
        return redirect()->route('user.deposit.preview');
    }

    public function depositPreview()
    {

        $track = session()->get('Track');
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->firstOrFail();

        if (is_null($data)) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
        if ($data->status != 0) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }

        $page_title = 'Deposit Preview';
        return view($this->activeTemplate . 'user.payment.preview', compact('data', 'page_title'));
    }

    public function subscriptionpayment($sub_id)
    {
        if (is_numeric($sub_id)) {
            $subscription = Subscription::where('id', $sub_id)->first();
            $page_title = 'Deposit Methods';
            $gatewayCurrency = GatewayCurrency::where('min_amount', '<', $subscription->price)->where('max_amount', '>', $subscription->price)->whereHas('method', function ($gate) {
                $gate->where('status', 1);
            })->with('method')->orderby('method_code')->get();
            return view($this->activeTemplate . 'user.payment.payment', compact('gatewayCurrency', 'page_title', 'subscription'));
        } else {
            dd("Sajjad");
        }
    }
    
    public function payment()
    {
        $orders = Order::where('order_number', auth()->user()->id)->get();

        if (count($orders) > 0) {
            $totalPrice = $orders->sum('total_price');
        } else {
            $notify[] = ['error', 'No products in your cart.'];
            return back()->withNotify($notify);
        }

        $page_title = 'Deposit Methods';

        $gatewayCurrency = GatewayCurrency::where('min_amount', '<', $totalPrice)->where('max_amount', '>', $totalPrice)->whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();

        return view($this->activeTemplate . 'user.payment.payment', compact('gatewayCurrency', 'page_title', 'totalPrice'));
    }


    // public function paymentInsert(Request $request)
    // {
    //     if ($request->is('api/*')) {
    //         $validator = Validator::make($request->all(), [
    //             'amount' => 'required|numeric|gt:0',
    //             'method_code' => 'required|in:101,103',
    //             'currency' => 'required',
    //             'order_number' => 'required'
    //         ]);
    //         if ($validator->fails()) {
    //             return $this->respondWithError($validator->errors()->first());
    //         }
    //     } else {
    //         $request->validate([
    //             'amount' => 'required|numeric|gt:0',
    //             'method_code' => 'required|in:101,103',
    //             'currency' => 'required',
    //         ]);
    //     }
        
    //     $user = auth()->user() ?? auth('user')->user();
    //     $newchargeprice = $request->amount;

    //     $subid = $request->subid ?? 0;
    //     if ($subid == 0) {
    //         if ($request->has('order_number')) {
    //             $orderNumber = $request->order_number;
    //         } else {
    //             $orderNumber = $user->id;
    //         }

    //         $orders = Order::where('order_number', $orderNumber)->get();
    //         $totalPrice = $orders->sum('total_price');

    //         if ($totalPrice != (float) $newchargeprice) {
    //             if ($request->is('api/*')) {
    //                 return $this->respondWithError('Something goes wrong!');
    //             }
    //             $notify[] = ['error', 'Something goes wrong.'];
    //             return redirect()->route('home')->withNotify($notify);
    //         }
    //     }

    //     $gate = GatewayCurrency::where('method_code', $request->method_code ?? 103)->where('currency', $request->currency ?? 'USD')->first();

    //     if (!$gate) {
    //         if ($request->is('api/*')) {
    //             return $this->respondWithError('Invalid Gateway');
    //         }
    //         $notify[] = ['error', 'Invalid Gateway'];
    //         return back()->withNotify($notify);
    //     }
        
    //     $data = new Deposit();
    //     if ($subid == 0) {
    //         $data->order_number = $orders[0]->order_number;
    //         $data->sub_id = null; //means this deposit is for the product order
    //     } else {
    //         $data->order_number = null;
    //         $data->sub_id = $subid; // means this deposit is for Subscription buy
    //         $sub = Subscription::where('id', $subid)->first();
    //         $subuser = UserSubscription::where('user_id', auth()->user()->id)->with('subscriptions')->first();

    //         if (!is_null($subuser)) {
    //             if ($subuser->subscriptions->plan_type == 1) {
    //                 $totaldays = Carbon::parse($subuser->expire_on)->diffInDays(Carbon::parse($subuser->start_on));
    //                 $remaindays = Carbon::parse($subuser->expire_on)->diffInDays(Carbon::now());
    //                 $perday = $subuser->subscriptions->price / $totaldays;
    //                 $currentprice = $remaindays * $perday;
    //                 $newchargeprice = $sub->price - $currentprice;
    //                 // dd($subuser->subscriptions->price, $totaldays, $remaindays, $perday, $currentprice, $newcharge);
    //             }
    //         }
    //     }

    //     $charge = getAmount($gate->fixed_charge + ($newchargeprice * $gate->percent_charge / 100));
    //     $payable = getAmount($newchargeprice + $charge);
    //     $final_amo = getAmount($payable * $gate->rate);
    //     $data->user_id = $user->id;
    //     $data->method_code = $gate->method_code;
    //     $data->method_currency = strtoupper($gate->currency);
    //     $data->amount = $newchargeprice;
    //     $data->charge = $charge;
    //     $data->rate = $gate->rate;
    //     $data->final_amo = getAmount($final_amo);
    //     $data->btc_amo = 0;
    //     $data->btc_wallet = "";
    //     $data->trx = getTrx();
    //     $data->try = 0;
    //     $data->status = 0;
    //     $data->save();
        
    //     if ($request->is('api/*')) {
    //         $deposit = Deposit::where('trx', $data->trx)->orderBy('id', 'DESC')->with('gateway')->first();
    //         if (is_null($deposit)) {
    //             if ($request->is('api/*')) {
    //                 return $this->respondWithError('Invalid Deposit Request');
    //             }
    //             $notify[] = ['error', 'Invalid Deposit Request'];
    //             return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    //         }
    //         if ($deposit->status != 0) {
    //             if ($request->is('api/*')) {
    //                 return $this->respondWithError('Invalid Deposit Request');
    //             }
    //             $notify[] = ['error', 'Invalid Deposit Request'];
    //             return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    //         }

    //         if ($deposit->method_code >= 1000) {
    //             $this->userDataUpdate($deposit);
    //             if ($request->is('api/*')) {
    //                 return $this->respondWithError('Your deposit request is queued for approval.');
    //             }
    //             $notify[] = ['success', 'Your deposit request is queued for approval.'];
    //             return back()->withNotify($notify);
    //         }

    //         $dirName = $deposit->gateway->alias;
    //         $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

    //         $response = $new::process($deposit);
    //         $response = json_decode($response);

    //         if (isset($response->error)) {
    //             if ($request->is('api/*')) {
    //                 return $this->respondWithError($response->message);
    //             }
    //             $notify[] = ['error', $data->message];
    //             return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    //         }
    //     }

    //     if($request->is('api/*')){            
    //         return $this->respondWithSuccess($data, 'Successfull');
    //     }

    //     session()->put('Track', $data->trx);
    //     session()->put('sub_id', $data->sub_id);

    //     return redirect()->route('user.payment.preview');
    // }
    
    public function paymentInsert(Request $request)
    {
        if ($request->is('api/*')) {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|gt:0',
                'method_code' => 'required|in:101,103',
                'currency' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors()->first());
            }
        } else {
            $request->validate([
                'amount' => 'required|numeric|gt:0',
                'method_code' => 'required|in:101,103',
                'currency' => 'required',
            ]);
        }

        $user = auth()->user() ?? auth('user')->user();
        $newchargeprice = $request->amount;

        $subid = $request->subid ?? 0;
        if ($subid == 0) {
            if ($request->has('order_number')) {
                $orderNumber = $request->order_number;
            } else {
                $orderNumber = $user->id;
            }

            $orders = Order::where('order_number', $orderNumber)->get();
            $totalPrice = $orders->sum('total_price');

            if ($totalPrice != (float) $newchargeprice) {
                if ($request->is('api/*')) {
                    Log::error('total pice or new price does not matched or order number is wrong');
                    return $this->respondWithError('Something went wrong!');
                }
                $notify[] = ['error', 'Something went wrong.'];
                return redirect()->route('home')->withNotify($notify);
            }
        }

        $gate = GatewayCurrency::where('method_code', $request->method_code ?? 103)->where('currency', $request->currency ?? 'USD')->first();
        $gateway =json_decode($gate->gateway_parameter, true);

        if (!$gate) {
            if ($request->is('api/*')) {
                return $this->respondWithError('Invalid Gateway!');
            }
            $notify[] = ['error', 'Invalid Gateway'];
            return back()->withNotify($notify);
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
        
        if ($request->is('api/*')) { 
            $deposit = Deposit::where('trx', $data->trx)->orderBy('id', 'DESC')->with('gateway')->first();
            if (is_null($deposit) || $deposit->status != 0) {
                if ($request->is('api/*')) {
                    return $this->respondWithError('Invalid Deposit Request!');
                }
                $notify[] = ['error', 'Invalid Deposit Request'];
                return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
            }

            if ($deposit->method_code >= 1000) {
                $this->userDataUpdate($deposit);
                if ($request->is('api/*')) {
                    return $this->respondWithError('Your deposit request is queued for approval.');
                }
                $notify[] = ['success', 'Your deposit request is queued for approval.'];
                return back()->withNotify($notify);
            }

            $dirName = $deposit->gateway->alias;
            $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

            $response = $new::process($deposit);
            $response = json_decode($response);

            if (isset($response->error)) {
                if ($request->is('api/*')) {
                    return $this->respondWithError($response->message);
                }
                $notify[] = ['error', $data->message];
                return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
            }
        }

        if($request->is('api/*')){
            $res = [
                'order' => $data,
                'publishable_key' => $gateway['publishable_key'] ?? null,
            ];
            return $this->respondWithSuccess($res, 'Successfull');
        }

        session()->put('Track', $data->trx);
        session()->put('sub_id', $data->sub_id);

        return redirect()->route('user.payment.preview');
    }



    public function paymentPreview()
    {
        $track = session()->get('Track');
        $subid = session()->get('sub_id');
        if ($subid == 0) {
            $data = Deposit::where('order_number', auth()->user()->id)->where('trx', $track)->orderBy('id', 'DESC')->firstOrFail();
        } else {
            $data = Deposit::where('sub_id', $subid)->where('trx', $track)->orderBy('id', 'DESC')->firstOrFail();
        }
        if (is_null($data)) {
            $notify[] = ['error', 'Invalid Payment Request'];
            return redirect()->route('home')->withNotify($notify);
        }
        if ($data->status != 0) {
            $notify[] = ['error', 'Invalid Payment Request'];
            return redirect()->route('home')->withNotify($notify);
        }
        $page_title = 'Payment Preview';
        return view($this->activeTemplate . 'user.payment.payment-preview', compact('data', 'page_title'));
    }

    public function depositConfirm()
    {

        $track = Session::get('Track');
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->with('gateway')->first();
        if (is_null($deposit)) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
        if ($deposit->status != 0) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }

        if ($deposit->method_code >= 1000) {
            $this->userDataUpdate($deposit);
            $notify[] = ['success', 'Your deposit request is queued for approval.'];
            return back()->withNotify($notify);
        }

        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];

            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }

        if (isset($data->redirect)) {

            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $page_title = 'Payment Confirm';

        return view($this->activeTemplate . $data->view, compact('data', 'page_title', 'deposit'));
    }

    public static function userDataUpdate($trx, $api = false)
    {
        $general = GeneralSetting::first();
        $data = Deposit::where('trx', $trx)->first();
        $user = User::find($data->user_id);

        if (is_null($data->order_number) && is_null($data->sub_id)) {

            Log::info('User update First if -> Order number empty' . $data->order_number . 'and sub id is null' . $data->sub_id);

            if ($data->status == 0) {
                $data->status = 1;
                $data->save();

                $user = User::find($data->user_id);
                $user->balance += $data->amount;
                $user->save();

                $transaction = new Transaction();
                $transaction->user_id = $data->user_id;
                $transaction->amount = $data->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = $data->charge;
                $transaction->trx_type = '+';
                $transaction->details = 'Deposit Via ' . $data->gateway_currency()->name;
                $transaction->trx = $data->trx;
                $transaction->save();

                if ($general->referral_system) {
                    $commissionType = 'deposit_commission';
                    levelCommission($user->id, $data->amount, $commissionType);
                }

                // notify($user, 'DEPOSIT_COMPLETE', [
                //     'method_name' => $data->gateway_currency()->name,
                //     'method_currency' => $data->method_currency,
                //     'method_amount' => getAmount($data->final_amo),
                //     'amount' => getAmount($data->amount),
                //     'charge' => getAmount($data->charge),
                //     'currency' => $general->cur_text,
                //     'rate' => getAmount($data->rate),
                //     'trx' => $data->trx,
                //     'post_balance' => getAmount($user->balance),
                // ]);

                if ($api == true) {
                    return response()->json(['success' => true, 'message' => 'Product is Successfully Purchased!']);
                }
            }
        }
        if (is_null($data->order_number) && !is_null($data->sub_id)) {
            Log::info('User update Second if -> Order number empty' . $data->order_number . 'and sub id is not null' . $data->sub_id);
            $gnl = GeneralSetting::first();
            $subuser = UserSubscription::where('user_id', auth()->user()->id)->with('subscriptions')->first();
            $subuser->status = 0;
            $subuser->save();
            $usersub = new UserSubscription();
            $usersub->sub_id = $data->sub_id;
            $sub = Subscription::where('id', $data->sub_id)->first();
            if ($sub->plan_type == 1) {
                $detail = "Monthly";
                $usersub->expire_on = Carbon::now()->addMonths(1);
            } else {
                $detail = "OneTime";
                $usersub->expire_on = null;
            }
            $usersub->user_id = $user->id;
            $usersub->status = 1;
            $usersub->save();
            $data->status = 1;
            $data->save();
            //Add desposit transection

            $user->balance += $data->amount;
            $transaction = new Transaction();
            $transaction->user_id = $data->user_id;
            $transaction->amount = $data->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $data->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Payment Via ' . $data->gateway_currency()->name . " For the Subscriptions";
            $transaction->trx = $data->trx;
            $transaction->save();
            $user->balance -= $data->amount;
            $user->save();
            //Subtract Purchase atrnasection Transections of Subscription
            $transaction = new Transaction();
            $transaction->user_id = $data->user_id;
            $transaction->amount = $data->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $data->charge;
            $transaction->trx_type = '-';
            $transaction->details = getAmount($data->amount) . ' ' . $gnl->cur_text . ' Subtracted From Your Own Wallet for the' . $detail . 'subscription Packg you buy.';
            $transaction->trx = getTrx();
            $transaction->save();
            $notification = new Notification();
            $notification->user_id = $data->user_id;
            $notification->cf_status = 0;
            $notification->meeting_status = 0;
            $notification->sell_id = 0;
            $notification->product_id = 0;
            $notification->subs_status = $data->sub_id;
            $notification->save();

            // notify($user, 'SUBSCRIPTION_PURCHASED', [
            //     'pack_name' => $sub->name,
            //     'plan_type' => $detail,
            //     'currency' => $gnl->cur_text,
            //     'pack_amount' => getAmount($sub->price),
            //     'product_allowed' => $sub->allowed_product,
            //     'trx' => $transaction->trx,
            // ]);
            if ($api == true) {
                return response()->json(['success' => true, 'message' => 'Product is Successfully Purchased!']);
            }
        }
        if (!is_null($data->order_number) && is_null($data->sub_id)) {

            Log::info('User update Third if -> Order number is not empty' . $data->order_number . 'and sub id is null' . $data->sub_id);

            $user = auth()->user() ?? auth('user')->user();
            $order_number = $data->order_number ?? $user->id;
            // Log::info($user);
            
            // $orders = Order::where('order_number', $user->id)->get();
            $orders = Order::where('order_number', $order_number)->get();
            // Log::info($orders);

            if (count($orders) > 0) {
                Log::info('Order Found!');
                $user = User::find($data->user_id);
                $gnl = GeneralSetting::first();

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
                    $sell->bump_fee = $item->bump_fee;
                    $sell->product_price = $item->product_price;
                    $sell->total_price = $item->total_price;
                    $sell->status = 1;
                    $sell->save();
                    Log::info("Sell Created! -> " . $sell->id);
                    if ($sell->bump_fee != 0) {
                        $bump = BumpResponse::Where('order_id', $item->id);
                        $bump->update([
                            'sell_id' => $sell->id,
                        ]);
                    }
                    $sell->product->total_sell += 1;
                    $sell->product->save();

                    $notification = new Notification();
                    $notification->user_id = $user->id;
                    $notification->cf_status = 0;
                    $notification->sell_id = $sell->id;
                    $notification->product_id = $sell->product_id;
                    $notification->meeting_status = 0;
                    $notification->save();

                    Log::info("Notification Created! -> " . $notification->id);

                    // $notification = new Notification;
                    // $notification->user_id = $item->author_id;
                    // $notification->cf_status = 0;
                    // $notification->sell_id = $sell->id;
                    // $notification->product_id = $sell->product_id;
                    // $notification->meeting_status = 0;
                    // $notification->save();

                    $levels = Level::get();
                    $author = $item->author;
                    $author->earning = $author->earning + ($sell->total_price - ($sell->product->category->buyer_fee + (($sell->total_price * $author->levell->product_charge) / 100)));
                    $author->balance += $sell->total_price;

                    $authorTransaction = new Transaction();
                    $authorTransaction->user_id = $author->id;
                    $authorTransaction->amount = $sell->total_price;
                    $authorTransaction->post_balance = $author->balance;
                    $authorTransaction->charge = 0;
                    $authorTransaction->trx_type = '+';
                    $authorTransaction->details = getAmount($authorTransaction->amount) . ' ' . $gnl->cur_text . ' Added with Balance For selling a product named ' . $item->product->name;
                    $authorTransaction->trx = getTrx();
                    $authorTransaction->save();

                    Log::info("First Author Transaction Created! -> " . $authorTransaction->id);

                    $plan = UserSubscription::where('user_id', $author->id)->with('subscriptions')->first();
                    if (is_null($plan)) {
                        $plan = Subscription::where('id', 1)->first();
                        $commission = $author->balance = $author->balance - $plan->commission;
                    } else {
                        if ($plan->subscriptions->commission_type == 1) //1 means percentage base Commision
                        {
                            $commission = (($sell->total_price * $plan->subscriptions->commission) / 100);
                        } else {
                            $commission = $sell->total_price + $plan->subscriptions->commission;
                        }
                        $author->balance = $author->balance - $commission;
                    }
                    $author->save();

                    if (($author->earning >= $author->levell->earning) && ($author->earning < $levels->max('earning'))) {
                        updateAuthorLevel($author);
                    }

                    $authorTransaction = new Transaction();
                    $authorTransaction->user_id = $author->id;
                    $authorTransaction->amount = $commission;
                    $authorTransaction->post_balance = $author->balance;
                    $authorTransaction->charge = 0;
                    $authorTransaction->trx_type = '-';
                    $authorTransaction->details = $commission . ' ' . $gnl->cur_text . ' Charged For selling a product named ' . $item->product->name;
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
                    //     'post_balance' => getAmount($author->balance),
                    //     'buyer_fee' => $author->levell->product_charge,
                    //     'amount' => $sell->total_price - $commission,
                    // ]);
                }

                $data->status = 1;
                $data->save();

                $user->balance += $data->amount;

                $transaction = new Transaction();
                $transaction->user_id = $data->user_id;
                $transaction->amount = $data->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = $data->charge;
                $transaction->trx_type = '+';
                $transaction->details = 'Payment Via ' . $data->gateway_currency()->name;
                $transaction->trx = $data->trx;
                $transaction->save();

                Log::info("Second Transaction Created! -> " . $transaction->id);

                $user->balance -= $data->amount;
                $user->save();

                Log::info("User balance updated! -> " . $user->balance);

                $transaction = new Transaction();
                $transaction->user_id = $data->user_id;
                $transaction->amount = $data->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = $data->charge;
                $transaction->trx_type = '-';
                $transaction->details = getAmount($data->amount) . ' ' . $gnl->cur_text . ' Subtracted From Your Balance For Purchasing Products.';
                $transaction->trx = $data->trx;
                $transaction->save();

                Log::info("Third Transaction Created! -> " . $transaction->id);

                $productarray = [];
                $productList = '<ol>';
                foreach ($orders as $item) {

                    if (!is_null(findcustomemail($item->product->id))) {
                        $products = new stdClass;
                        $products->itemname = $item->product->name;
                        $products->itemprice = $item->product_price;
                        $products->bumpfee = $item->bump_fee;
                        if ($item->support == 1) {
                            $products->support = "Yes";
                        } else {
                            $products->support = "No";
                        }
                        $products->supportfee = $item->support_fee;
                        $products->supporttime = $item->support_time;
                        $products->totalprice = $item->total_price;
                        $productarray[] = $products;
                        // notify($user, 'PRODUCT_PURCHASED', [
                        //     'method_name' => $data->gateway_currency()->name,
                        //     'currency' => $gnl->cur_text,
                        //     'total_amount' => getAmount($data->amount),
                        //     'post_balance' => $user->balance,
                        //     'product_list' => $productarray,
                        // ], $item->product->id);
                    } else {
                        $productList = '<ol>';
                        $productList .= '<ul><li>' . $item->product->name . '</li>';
                        $productList .= '<li>' . $item->product_price . '</li>';
                        $productList .= '<li> Payment method:' . $data->gateway_currency()->name . '</li>';
                        $productList .= '<li> Total Ammount:' . getAmount($data->amount) . '</li>';
                        if ($item->bump_fee != 0) {
                            $productList .= '<li>' . $item->bump_fee . '</li>';
                        }
                        if ($item->support == 1) {
                            $productList .= '<li>' . $item->support_fee . '</li>';
                            $productList .= '<li>' . $item->support_time . '</li>';
                        }
                        $productList .= '<li>' . $item->total_price . '</li></ul>';
                        $productList .= '</ol>';
                    }
                    // notify($user, 'PRODUCT_PURCHASED', [
                    //     'method_name' => $data->gateway_currency()->name,
                    //     'currency' => $gnl->cur_text,
                    //     'total_amount' => getAmount($data->amount),
                    //     'post_balance' => $user->balance,
                    //     'product_list' => $productList,
                    // ]);
                }

                foreach ($orders as $item) {
                    Log::info("Delete orders items from user cart ! -> " . $item->id);
                    $item->delete();
                }

                if ($api == true) {
                    return response()->json(['success' => true, 'message' => 'Product is Successfully Purchased!']);
                }

                session()->forget('order_number');
                session()->forget('cartCount');
                $notify[] = ['Success', 'Product is Successfully Purchased.'];

                return redirect()->route('user.purchased.product')->withNotify($notify);
            } else {
                Log::info('No products in your cart.');
                if ($api == true) {
                    return response()->json(['success' => false, 'message' => 'No products in your cart!'], 400);
                }
                $notify[] = ['error', 'No products in your cart.'];
                return back()->withNotify($notify);
            }
        }
        abort(404);
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }
        if ($data->status != 0) {
            return redirect()->route(gatewayRedirectUrl());

            $notify[] = ['success', 'You have purchased successfully.'];
        }
        if ($data->method_code > 999) {

            $page_title = 'Deposit Confirm';
            $method = $data->gateway_currency();
            return view($this->activeTemplate . 'user.manual_payment.manual_confirm', compact('data', 'page_title', 'method'));
        }
        abort(404);
    }

    public function manualPaymentConfirm()
    {
        $track = session()->get('Track');

        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->where('order_number', auth()->user()->id)->first();

        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }
        if ($data->status != 0) {
            return redirect()->route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {

            $page_title = 'Payment Confirm';
            $method = $data->gateway_currency();
            return view($this->activeTemplate . 'user.manual_payment.manual_payment_confirm', compact('data', 'page_title', 'method'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }
        if ($data->status != 0) {
            return redirect()->route(gatewayRedirectUrl());
        }

        $params = json_decode($data->gateway_currency()->gateway_parameter);

        $rules = [];
        $inputField = [];
        $verifyImages = [];

        if ($params != null) {
            foreach ($params as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');

                    array_push($verifyImages, $key);
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $this->validate($request, $rules);

        $directory = date("Y") . "/" . date("m") . "/" . date("d");
        $path = imagePath()['verify']['deposit']['path'] . '/' . $directory;
        $collection = collect($request);
        $reqField = [];
        if ($params != null) {
            foreach ($collection as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory . '/' . uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $inKey];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $data->detail = $reqField;
        } else {
            $data->detail = null;
        }

        $data->status = 2; // pending
        $data->save();

        $gnl = GeneralSetting::first();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gateway_currency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => getAmount($data->final_amo),
            'amount' => getAmount($data->amount),
            'charge' => getAmount($data->charge),
            'currency' => $gnl->cur_text,
            'rate' => getAmount($data->rate),
            'trx' => $data->trx,
        ]);

        $notify[] = ['success', 'You have deposit request has been taken.'];
        return redirect()->route('user.deposit.history')->withNotify($notify);
    }

    public function manualPaymentUpdate(Request $request)
    {
        $track = session()->get('Track');

        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->where('order_number', auth()->user()->id)->first();

        if (!$data) {
            return redirect()->route(gatewayRedirectUrl());
        }
        if ($data->status != 0) {
            return redirect()->route(gatewayRedirectUrl());
        }

        $params = json_decode($data->gateway_currency()->gateway_parameter);

        $rules = [];
        $inputField = [];
        $verifyImages = [];

        if ($params != null) {
            foreach ($params as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');

                    array_push($verifyImages, $key);
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $this->validate($request, $rules);

        $directory = date("Y") . "/" . date("m") . "/" . date("d");
        $path = imagePath()['verify']['deposit']['path'] . '/' . $directory;
        $collection = collect($request);
        $reqField = [];
        if ($params != null) {
            foreach ($collection as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory . '/' . uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $inKey];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $data->detail = $reqField;
        } else {
            $data->detail = null;
        }

        $data->status = 2; // pending
        $data->save();

        $gnl = GeneralSetting::first();

        $orders = Order::where('order_number', auth()->user()->id)->get();

        if (count($orders) > 0) {

            $user = auth()->user();

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
                $sell->status = 0;
                $sell->save();
            }

            foreach ($orders as $item) {
                $item->delete();
            }

            notify($data->user, 'PAYMENT_REQUEST', [
                'method_name' => $data->gateway_currency()->name,
                'method_currency' => $data->method_currency,
                'method_amount' => getAmount($data->final_amo),
                'amount' => getAmount($data->amount),
                'charge' => getAmount($data->charge),
                'currency' => $gnl->cur_text,
                'rate' => getAmount($data->rate),
                'trx' => $data->trx,
            ], $sell->product_id);

            session()->forget('order_number');
            session()->forget('cartCount');

            $notify[] = ['success', 'Your payment request has been taken. Wait for the approval'];
            return redirect()->route('user.purchased.product')->withNotify($notify);
        } else {
            $notify[] = ['error', 'No products in your cart.'];
            return back()->withNotify($notify);
        }
    }
}
