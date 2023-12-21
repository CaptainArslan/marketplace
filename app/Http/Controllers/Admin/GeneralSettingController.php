<?php

namespace App\Http\Controllers\Admin;

use App\Frontend;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Image;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $general = GeneralSetting::first();
        $page_title = 'General Settings';
        $timezones = json_decode(file_get_contents(resource_path('views/admin/partials/timezone.json')));
        return view('admin.setting.general_setting', compact('page_title', 'general', 'timezones'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'base_color' => 'nullable', 'regex:/^[a-f0-9]{6}$/i',
            'secondary_color' => 'nullable', 'regex:/^[a-f0-9]{6}$/i',
            'regular' => ['required', 'integer', 'min:1', 'max:12'],
            'extended' => ['required', 'integer', 'min:1', 'max:12'],
            'timezone' => 'required',
        ]);

        $general                    = GeneralSetting::first();
        $general->sitename          = $request->sitename;
        $general->cur_text          = $request->cur_text;
        $general->cur_sym           = $request->cur_sym;
        $general->regular           = $request->regular;
        $general->extended          = $request->extended;
        $general->base_color        = $request->base_color;
        $general->secondary_color   = $request->secondary_color;
        $general->secure_password   = $request->secure_password ? 1 : 0;
        $general->agree             = $request->agree ? 1 : 0;
        $general->registration      = $request->registration ? 1 : 0;
        $general->referral_system   = $request->referral_system ? 1 : 0;
        $general->ev                = $request->ev ? 1 : 0;
        $general->en                = $request->en ? 1 : 0;
        $general->sv                = $request->sv ? 1 : 0;
        $general->sn                = $request->sn ? 1 : 0;
        $general->suggestion_box                = $request->suggestion_box;
        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content = '<?php $timezone = ' . $request->timezone . ' ?>';
        file_put_contents($timezoneFile, $content);

        $notify[] = ['success', 'General setting has been updated.'];
        return back()->withNotify($notify);
    }


    public function ftp()
    {
        $setting = GeneralSetting::first();
        $page_title = "FTP Setting";
        return view('admin.setting.ftp', compact('page_title', 'setting'));
    }

    public function ftpSet(Request $request)
    {
        $request->validate([
            'server_name'   => 'required',
            'ftp.host'      => 'required',
            'ftp.username'  => 'required',
            'ftp.password'  => 'required',
            'ftp.port'      => 'required',
            'ftp.root'      => 'required',
            'ftp.domain'    => 'required',
        ]);
        $setting = GeneralSetting::first();
        $setting->update([
            'server'    => $request->server_name,
            'ftp'       => $request->ftp
        ]);
        $notify[] = ['success', 'FTP Setting Updated'];
        return back()->withNotify($notify);
    }


    public function logoIcon()
    {
        $page_title = 'Logo & Icon';
        return view('admin.setting.logo_icon', compact('page_title'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo' => 'image|mimes:jpg,jpeg,png',
            'favicon' => 'image|mimes:png',
        ]);
        if ($request->hasFile('logo')) {
            try {
                $path = imagePath()['logoIcon']['path'];
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make($request->logo)->save($path . '/logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Logo could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                $path = imagePath()['logoIcon']['path'];
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size = explode('x', imagePath()['favicon']['size']);
                Image::make($request->favicon)->resize($size[0], $size[1])->save($path . '/favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Favicon could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Logo Icons has been updated.'];
        return back()->withNotify($notify);
    }

    public function customCss()
    {
        $page_title = 'Custom CSS';
        $file = activeTemplate(true) . 'css/custom.css';
        $file_content = @file_get_contents($file);
        return view('admin.setting.custom_css', compact('page_title', 'file_content'));
    }


    public function customCssSubmit(Request $request)
    {
        $file = activeTemplate(true) . 'css/custom.css';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file, $request->css);
        $notify[] = ['success', 'CSS updated successfully'];
        return back()->withNotify($notify);
    }

    public function optimize()
    {
        Artisan::call('optimize:clear');
        $notify[] = ['success', 'Cache cleared successfully'];
        return back()->withNotify($notify);
    }


    public function cookie()
    {
        $page_title = 'GDPR Cookie';
        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        return view('admin.setting.cookie', compact('page_title', 'cookie'));
    }

    public function cookieSubmit(Request $request)
    {
        $request->validate([
            'link' => 'required',
            'description' => 'required',
        ]);
        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        $cookie->data_values = [
            'link' => $request->link,
            'description' => $request->description,
            'status' => $request->status ? 1 : 0,
        ];
        $cookie->save();
        $notify[] = ['success', 'Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }
}
