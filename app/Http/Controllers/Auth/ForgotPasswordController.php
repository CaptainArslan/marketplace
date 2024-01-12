<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Carbon\Carbon;
use App\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\ForgetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }


    public function showLinkRequestForm()
    {
        $page_title = "Forgot Password";
        return view(activeTemplate() . 'user.auth.passwords.email', compact('page_title'));
    }

    public function sendResetLinkEmail(Request $request)
    {
        if ($request->is('api/*')) {
            $validator = $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors()->first());
            }
        } else {
            $this->validateEmail($request);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            if ($request->is('api/*')) {
                return $this->respondWithError("User not found!");
            }
            $notify[] = ['error', 'User not found.'];
            return back()->withNotify($notify);
        }

        PasswordReset::where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->expire_at = Carbon::now()->addMinutes(5);
        $password->created_at = Carbon::now();
        $password->save();

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();
        try {
            Mail::to($user->email)->send(new ForgetPassword($code, $user, $userBrowserInfo, $userIpInfo));
        } catch (\Throwable $th) {
            Log::error('Error occured while sending forget password email! ' . $th->getMessage());
            if ($request->is('api/*')) {
                return $this->respondWithError('Error Occured while sending email!');
            }
            $notify[] = ['error', 'Error Occured while sending email!'];
            return redirect()->route('user.password.code_verify')->withNotify($notify);
        }

        if ($request->is('api/*')) {
            return $this->respondWithSuccess($user->only('email'), 'Please check your email for OTP!');
        }

        $page_title = 'Account Recovery';
        $email = $user->email;
        session()->put('pass_res_mail', $email);
        $notify[] = ['success', 'Password reset email sent successfully'];
        return redirect()->route('user.password.code_verify')->withNotify($notify);
    }

    public function codeVerify(){
        $page_title = 'Account Recovery';
        $email = session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error','Opps! session expired'];
            return redirect()->route('user.password.request')->withNotify($notify);
        }
        return view(activeTemplate().'user.auth.passwords.code_verify',compact('page_title','email'));
    }

    public function verifyCode(Request $request)
    {
        if ($request->is('api/*')) {
            $validator = $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'code' => 'required|min:6',
                'password' => 'required|min:6|confirmed'
            ]);
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors()->first());
            }
        } else {
            $request->validate(['code.*' => 'required', 'email' => 'required']);
            $code =  str_replace(',', '', implode(',', $request->code));
        }

        if ($request->is('api/*')) {
            $code = $request->code;
        }

        $otp = PasswordReset::where('token', $code)->where('email', $request->email)->first();
        if (!$otp) {
            if ($request->is('api/*')) {
                return $this->respondWithError("Invalid OTP");
            }

            $notify[] = ['error', 'Invalid token'];
            return redirect()->route('user.password.request')->withNotify($notify);
        }

        if ($request->is('api/*')) {
            if (Carbon::now() > $otp->expire_at) {
                return $this->respondWithError("OTP has been Expired");
            }

            if ($otp->status) {
                return $this->respondWithError("OTP has already been used!");
            }

            PasswordReset::where('email', $request->email)->update(['status' => 1]);
            // User::where('email', $request->email)->update(['password' => Hash::make(base64_decode($request->password))]);
            User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
            return $this->respondWithSuccess(null, "Password has been updated!");
        }

        $notify[] = ['success', 'You can change your password.'];
        session()->flash('fpass_email', $request->email);
        return redirect()->route('user.password.reset', $code)->withNotify($notify);
    }

    public function sendEmail(Request $request)
    {
        $validator = $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors()->first());
        }
        $user = User::where('email', $request->email)->first();
        
        if($user){     
            $userIpInfo = getIpInfo();
            $userBrowserInfo = osBrowser();
            $code = rand(100000, 9999999);
            
            
            try{
                send_email($user, 'PASS_RESET_CODE', [
                    'code' => $code,
                    'operating_system' => @$userBrowserInfo['os_platform'],
                    'browser' => @$userBrowserInfo['browser'],
                    'ip' => @$userIpInfo['ip'],
                    'time' => @$userIpInfo['time']
                ]);
                    
                return $this->respondWithSuccess($email, 'Please check your email to get OTP! ');
            }catch(\Exception $e){
                return $this->respondWithError('Error Occured while sending Email! ' . $e->getMessage());
            }
    
            
        }else{
            return $this->respondWithError('User Not Found!');
        }
    }
    
    public function changePassword(Request $request)
    {
        dd($request->all());
        $validator = $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return $this->respondWithError($validator->errors()->first());
        }
        
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 401);
        }

        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

}
