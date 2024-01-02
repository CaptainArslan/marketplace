<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Order;
use App\UserLogin;
use App\GeneralSetting;
use App\Lib\StrongPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public $activeTemplate = '';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('regStatus')->except('registrationNotAllowed');

        $this->activeTemplate = activeTemplate();
    }

    public function showRegistrationForm()
    {
        $page_title = "Sign Up";
        $info = json_decode(json_encode(getIpInfo()), true);
        $mobile_code = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view($this->activeTemplate . 'user.auth.register', compact('page_title', 'mobile_code', 'countries'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = GeneralSetting::first();
        $password_validation = 'min:6';

        if ($general->secure_password) {
            $strongPassword = new StrongPassword(6, $data);
            $password_validation = $strongPassword->mixedCase()->letters()->numbers()->symbols();
        }

        $agree = 'nullable';

        if ($general->agree) {
            $agree = 'required';
        }


        $countryData    = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes   = implode(',', array_keys($countryData));
        $mobileCodes    = implode(',', array_column($countryData, 'dial_code'));
        $countries      = implode(',', array_column($countryData, 'country'));
        $validate       = Validator::make($data, [
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:90|unique:users',
            'mobile' => 'required|string|max:50|unique:users',
            'password' => ['required', 'confirmed', $password_validation],
            'username' => 'required|alpha_num|unique:users|min:6',
            'captcha' => 'sometimes|required',
            'mobile_code' => 'required|in:' . $mobileCodes,
            'country_code' => 'required|in:' . $countryCodes,
            'country' => 'nullable|in:' . $countries,
            'agree' => $agree
        ]);
        return $validate;
    }

    public function register(Request $request)
    {
        // $validator = $this->validator($request->all())->validate();
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            if ($request->is('api/*')) {
                return $this->respondWithError($validator->errors()->first());
            } else {
                $notify[] = ['error', $errors[0]];
                return back()->withNotify($notify)->withInput();
            }
        }

        $exist = User::where('mobile', $request->country_code . $request->mobile)->first();

        if ($exist) {
            $message = 'Mobile number already exists';

            if ($request->is('api/*')) {
                return $this->respondWithError($message);
            } else {
                $notify[] = ['error', $message];
                return back()->withNotify($notify)->withInput();
            }
        }

        if (isset($request->captcha)) {
            if ($request->is('api/*')) {
                return $this->respondWithError("Invalid Captcha");
            }
            if (!captchaVerify($request->captcha, $request->captcha_secret)) {
                $notify[] = ['error', "Invalid Captcha"];
                return back()->withNotify($notify)->withInput();
            }
        }

        if (!isset($request->country)) {
            $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
            $country = $countryData[$request->country_code];
        }

        if ($request->is('api/*')) {
            $request->merge([
                'country' => $country->country,
                'password' => base64_decode($request->password)
            ]);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($request->is('api/*')) {
            if ($request->order_number) {
                $order = Order::where('order_number', $request->order_number)->get();
                if ($order->count() > 0) {
                    foreach ($order as $o) {
                        $o->order_number = $user->id;
                        $o->save();
                    }
                } else {
                    return $this->respondWithError('Invalid Order Number!');
                }
            }
            return $this->respondWithSuccess(null, 'User signed up successfully!');
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $general = GeneralSetting::first();

        $referBy = session()->get('reference');
        $referBy = session()->get('reference');

        if ($referBy && $general->referral_system) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }
        //User Create
        $user               = new User();
        $user->firstname    = isset($data['firstname']) ? $data['firstname'] : null;
        $user->lastname     = isset($data['lastname']) ? $data['lastname'] : null;
        $user->email        = strtolower(trim($data['email']));
        $user->password     = Hash::make($data['password']);
        $user->username     = trim($data['username']);
        $user->ref_by       = $referUser ? $referUser->id : 0;
        $user->country_code = $data['country_code'];
        $user->mobile       = $data['mobile_code'] . $data['mobile'];
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => isset($data['country']) ? $data['country'] : null,
            'city' => ''
        ];
        $user->status = 1;
        $user->ev = $general->ev ? 0 : 1;
        $user->sv = $general->sv ? 0 : 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->level_id = 1;
        $user->seller = 0;
        $user->save();



        //Login Log Create
        $ip = $_SERVER["REMOTE_ADDR"];
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
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


        return $user;
    }

    public function registered()
    {
        if (session('last_location')) {
            return redirect(session('last_location'));
        } else {
            return redirect()->route('user.home');
        }
    }
}
