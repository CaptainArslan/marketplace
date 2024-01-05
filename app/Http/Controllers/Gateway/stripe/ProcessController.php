<?php

namespace App\Http\Controllers\Gateway\stripe;

use App\Deposit;
use App\GeneralSetting;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;


class ProcessController extends Controller
{

    /*
     * Stripe Gateway
     */
    public static function process($deposit)
    {

        $alias = $deposit->gateway->alias;

        $send['track'] = $deposit->trx;
        $send['view'] = 'user.payment.'.$alias;
        $send['method'] = 'post';
        $send['url'] = route('ipn.'.$alias);

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = Session::get('Track');
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        
        if ($data->status == 1) {
            $notify[] = ['error', 'Invalid Request.'];
            return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
        }
        $this->validate($request, [
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC' => 'required',
        ]);

        $cc = $request->cardNumber;
        $exp = $request->cardExpiry;
        $cvc = $request->cardCVC;

        $exp = $pieces = explode("/", $_POST['cardExpiry']);
        $emo = trim($exp[0]);
        $eyr = trim($exp[1]);
        $cnts = round($data->final_amo, 2) * 100;

        $stripeAcc = json_decode($data->gateway_currency()->gateway_parameter);


        Stripe::setApiKey($stripeAcc->secret_key);

        Stripe::setApiVersion("2020-03-02");

        try {
            $token = \Stripe\Token::create(array(
                "card" => array(
                    "number" => "$cc",
                    "exp_month" => $emo,
                    "exp_year" => $eyr,
                    "cvc" => "$cvc"
                )
            ));
            Log::info("Token -> " . $token);
            try {
                $charge = Charge::create(array(
                    'card' => $token['id'],
                    'currency' => $data->method_currency,
                    'amount' => $cnts,
                    'description' => 'item',
                ));
                Log::info($charge);

                if ($charge['status'] == 'succeeded') {
                    PaymentController::userDataUpdate($data->trx);
                    $notify[] = ['success', 'Payment Success.'];
                }
            } catch (\Exception $e) {
                 Log::error("Charge Creation  => ".$e->getMessage());
                $notify[] = ['error', $e->getMessage()];
            }
        } catch (\Exception $e) {
            Log::error("Token Creation => ".$e->getMessage());
            $notify[] = ['error', $e->getMessage()];
        }


        return redirect()->route(gatewayRedirectUrl())->withNotify($notify);
    }
    
    public function ipnApi(Request $request)
    {
        if ($request->is('api/*')) {
            $validator = Validator::make($request->all(), [
                'track' => 'required',
                'stripe_token' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors()->first());
            }
        }

        $track = $request->track;
        Log:info("You placed an order against this trx = " . $track);
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if ($data->status == 1) {
            return $this->respondWithError('Invalid Request!');
        }

        $cnts = round($data->final_amo, 2) * 100;
        $stripeAcc = json_decode($data->gateway_currency()->gateway_parameter);
        Stripe::setApiKey($stripeAcc->secret_key);
        Stripe::setApiVersion("2020-03-02");
        try {
            $charge = Charge::create(array(
                'card' => $request->stripe_token,
                'currency' => $data->method_currency,
                'amount' => $cnts,
                'description' => 'item',
                // 'payment_method' => $paymentMethodId,
                // 'confirm' => true,
            ));

            Log::info($charge);

            if ($charge['status'] == 'succeeded') {
                PaymentController::userDataUpdate($data->trx, true);
                return $this->respondWithSuccess(null, 'Payment Success!');
            }
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
}
