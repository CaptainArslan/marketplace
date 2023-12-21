<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Order;
use App\Extension;
use App\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //    protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = 'user/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'logoutGet');
    }

    public function showLoginForm()
    {

        $page_title = "Sign In";
        return view(activeTemplate() . 'user.auth.login', compact('page_title'));
    }



    public function login(Request $request)
    {
        // Validation
        if ($request->is('api/*')) {
            $validator = $this->validateApiLogin($request);
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors()->first());
            }
        } else {
            $this->validateLogin($request);
        }


        // Captcha validation
        if (isset($request->captcha) && !captchaVerify($request->captcha, $request->captcha_secret)) {
            return $request->is('api/*')
                ? $this->respondWithError("Invalid Captcha")
                : back()->withNotify(['error', "Invalid Captcha"])->withInput();
        }

        // Throttling
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // Authentication attempt
        if ($request->is('api/*')) {
            if (!User::where('email', $request->email)->exists()) {
                return $this->respondWithError("User Not registered!");
            }

            if (!$token = auth('user')->attempt($request->all())) {
                return $this->respondWithError('Something went wrong!');
            }

            $user = auth('user')->user();
            Auth::loginUsingId($user->id);
            Log::info('API User login successfully');

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'data' => $user->only([
                    'id', 'firstname', 'lastname', 'username', 'email',
                    'country_code', 'country', 'mobile', 'image', 'cover_image', 'description',
                ]),
            ], Response::HTTP_OK);
        }
        // Web authentication
        if (!$this->attemptLogin($request)) {
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
        }
        return $this->sendLoginResponse($request);
    }

    public function username()
    {
        return 'username';
    }

    protected function validateLogin(Request $request)
    {
        $customRecaptcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        if ($customRecaptcha) {
            $validation_rule['captcha'] = 'required';
        }

        $request->validate($validation_rule);
    }

    protected function validateApiLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        return $validator;
    }


    // public function logout()
    // {
    //     $this->guard()->logout();

    //     request()->session()->invalidate();

    //     $notify[] = ['success', 'You have been logged out.'];
    //     return redirect()->route('user.login')->withNotify($notify);
    // }

    public function logout(Request $request)
    {
        if ($request->is('api/*')) {
            $user = auth('user')->user();
            Auth::loginUsingId($user->id);
            return $this->respondWithSuccess(null, 'Successfully logged out!');
        }

        $this->guard()->logout();

        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return redirect()->route('user.login')->withNotify($notify);
    }



    public function authenticated(Request $request, $user)
    {
        if ($user->status == 0) {
            $this->guard()->logout();
            return redirect()->route('user.login')->withErrors(['Your account has been deactivated.']);
        }


        $user = auth()->user();
        $user->tv = $user->ts == 1 ? 0 : 1;
        $user->save();
        $ip = $_SERVER["REMOTE_ADDR"];
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->location =  $exist->location;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->location =  @implode(',', $info['city']) . (" - " . @implode(',', $info['area']) . "- ") . @implode(',', $info['country']) . (" - " . @implode(',', $info['code']) . " ");
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        if (session()->has('order_number')) {
            $orderUpdate = Order::where('order_number', session()->get('order_number'))->update(['order_number' => $user->id]);
        }
        if (session('last_location')) {
            return redirect(session('last_location'));
        } else {
            return redirect()->route('user.home');
        }
    }
}
