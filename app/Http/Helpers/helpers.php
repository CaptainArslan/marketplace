<?php

use App\Category;
use App\CustomCss;
use App\GeneralSetting;
use App\GhlAuth;
use App\Level;
use App\Product;
use App\SubCategory;
use App\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

function sidebarVariation()
{

    /// for sidebar
    $variation['sidebar'] = 'bg_img';

    //for selector
    $variation['selector'] = 'capsule--rounded';

    //for overlay
    $variation['overlay'] = 'overlay--indigo';

    //Opacity
    $variation['opacity'] = 'overlay--opacity-8'; // 1-10

    return $variation;
}

function systemDetails()
{
    $system['name'] = 'viserplace';
    $system['version'] = '1.2';
    return $system;
}

function getLatestVersion()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/version/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function shortDescription($string, $length = 120)
{
    return Illuminate\Support\Str::limit($string, $length);
}

function shortCodeReplacer($shortCode, $replace_with, $template_string)
{
    return str_replace($shortCode, $replace_with, $template_string);
}

function verificationCode($length)
{
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = 0;
    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

//moveable
function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
{
    $path = makeDirectory($location);
    if (!$path) {
        throw new Exception('File could not been created.');
    }

    if (!empty($old)) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();

    $image = Image::make($file);

    if (!empty($size)) {
        $size = explode('x', strtolower($size));
        $image->resize($size[0], $size[1]);
    }
    $image->save($location . '/' . $filename);

    if (!empty($thumb)) {

        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
    }

    return $filename;
}

function uploadFile($file, $location, $size = null, $old = null)
{
    $path = makeDirectory($location);
    if (!$path) {
        throw new Exception('File could not been created.');
    }

    if (!empty($old)) {
        removeFile($location . '/' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $file->move($location, $filename);
    return $filename;
}

function makeDirectory($path)
{
    if (file_exists($path)) {
        return true;
    }

    return mkdir($path, 0755, true);
}

function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}

function activeTemplate($asset = false)
{

    $gs = GeneralSetting::first(['active_template']);

    $template = $gs->active_template;

    $sess = session()->get('template');

    if (trim($sess) != null) {
        $template = $sess;
    }
    if ($asset) {
        return 'assets/templates/' . $template . '/';
    }

    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $gs = GeneralSetting::first(['active_template']);
    $template = $gs->active_template;
    $sess = session()->get('template');
    if (trim($sess) != null) {
        $template = $sess;
    }
    return $template;
}

function reCaptcha()
{
    $reCaptcha = \App\Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
    return $reCaptcha ? $reCaptcha->generateScript() : '';
}

function analytics()
{
    $analytics = \App\Extension::where('act', 'google-analytics')->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function tawkto()
{
    $tawkto = \App\Extension::where('act', 'tawk-chat')->where('status', 1)->first();
    return $tawkto ? $tawkto->generateScript() : '';
}

function fbcomment()
{
    $comment = \App\Extension::where('act', 'fb-comment')->where('status', 1)->first();
    return $comment ? $comment->generateScript() : '';
}

function getCustomCaptcha($height = 46, $width = '100%', $bgcolor = '#003', $textcolor = '#abc')
{
    $textcolor = '#' . App\GeneralSetting::first()->base_color;
    $captcha = \App\Extension::where('act', 'custom-captcha')->where('status', 1)->first();

    $code = rand(100000, 999999);
    $char = str_split($code);
    $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
    $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgcolor . '; color: ' . $textcolor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
    foreach ($char as $value) {
        $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
    }
    $ret .= '</div>';
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
    return $ret;
}

function captchaVerify($code, $secret)
{
    $captcha = \App\Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    if ($captchaSecret == $secret) {
        return true;
    }
    return false;
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function currency()
{
    $data['crypto'] = 8;
    $data['fiat'] = 2;
    return $data;
}

function getAmount($amount, $length = 0)
{
    if (0 < $length) {
        return number_format($amount + 0, $length);
    }
    $value = $amount + 0;
    return round($value, 2);
}
function currtext()
{
    $general = GeneralSetting::first();
    return $general->cur_text;
}
function currsym()
{
    $general = GeneralSetting::first();
    return $general->cur_sym;
}
function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}

function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet, $amount, $crypto = null)
{

    $varb = $wallet . "?amount=" . $amount;
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8";
}

//moveable
function curlContent($url)
{
    //open connection
    $ch = curl_init();
    //set the url, number of POST vars, POST data

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);

    return $result;
}

//moveable
function curlPostContent($url, $arr = null)
{
    if ($arr) {
        $params = http_build_query($arr);
    } else {
        $params = '';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function inputTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}

function str_slug($title = null)
{
    return \Illuminate\Support\Str::slug($title);
}

function str_limit($title = null, $length = 10)
{
    return \Illuminate\Support\Str::limit($title, $length);
}

//moveable
function getIpInfo()
{
    $ip = null;
    $deep_detect = true;

    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
    }

    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);

    $country = @$xml->geoplugin_countryName;
    $city = @$xml->geoplugin_city;
    $area = @$xml->geoplugin_areaCode;
    $code = @$xml->geoplugin_countryCode;
    $long = @$xml->geoplugin_longitude;
    $lat = @$xml->geoplugin_latitude;

    $data['country'] = $country;
    $data['city'] = $city;
    $data['area'] = $area;
    $data['code'] = $code;
    $data['long'] = $long;
    $data['lat'] = $lat;
    $data['ip'] = request()->ip();
    $data['time'] = date('d-m-Y h:i:s A');

    return $data;
}

//moveable
function osBrowser()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile',
    );
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    $browser = "Unknown Browser";
    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser',
    );
    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
        }
    }

    $data['os_platform'] = $os_platform;
    $data['browser'] = $browser;

    return $data;
}
function get_productcode($catid, $subcatid, $pname)
{
    $cat = Category::where('id', $catid)->first()->pluck('name');
    $subcat = SubCategory::where('id', $subcatid)->first()->pluck('name');

    return $cat . '_' . $subcat . '_' . $pname;
}
function site_name()
{
    $general = GeneralSetting::first();
    $sitname = str_word_count($general->sitename);
    $sitnameArr = explode(' ', $general->sitename);
    if ($sitname > 1) {
        $title = "<span>$sitnameArr[0] </span> " . str_replace($sitnameArr[0], '', $general->sitename);
    } else {
        $title = "<span>$general->sitename</span>";
    }

    return $title;
}

//moveable
function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}

function getPageSections($arr = false)
{

    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null)
{
    $clean = '';
    $size = $size ? $size : 'undefined';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    } else {
        return route('placeholderImage', $size);
    }
}
function getnotifycount(){
    $notifycount = App\Notification::where('user_id', auth()->user()->id)
        ->where('mark_read', 0)
        ->where(function ($query) {
            $query->where('cf_status', '!=', 1)->orWhere('meeting_status', '!=', 1);
        })
        ->count();
    session()->put('pcount', $notifycount);
        return $notifycount;

}
function findcustomemail($pid)
{
    $product = Product::Where('id', $pid)->first();
    $author = User::Where('id', $product->user_id)->first();
    $email_template = \App\EmailTemplateSetting::where('user_id', $author->id)->where('status', 1)->first();
    return $email_template;
}
function findcustomtemplate($uid)
{
    $email_template = \App\EmailTemplateSetting::where('user_id', $uid)->where('status', 1)->first();
    return $email_template;
}

function notify($user, $type, $shortCodes = null, $productid = null)
{
    if (!is_null($productid)) {
        $product = Product::Where('id', $productid)->first();
        $author = User::Where('id', $product->user_id)->first();
        $email_template = \App\EmailTemplateSetting::where('user_id', $author->id)->where('status', 1)->first();
        if (!is_null($email_template)) {
            send_customemail($user, $type, $shortCodes, $author);
        } else {
            send_email($user, $type, $shortCodes, $author);
        }
        send_sms($user, $type, $shortCodes);
    } else {
        send_email($user, $type, $shortCodes);
        send_sms($user, $type, $shortCodes);
    }
}

/*SMS EMIL moveable*/

function send_sms($user, $type, $shortCodes = [])
{
    $general = GeneralSetting::first(['sn', 'sms_api']);
    $sms_template = \App\SmsTemplate::where('act', $type)->where('sms_status', 1)->first();
    if ($general->sn == 1 && $sms_template) {

        $template = $sms_template->sms_body;

        foreach ($shortCodes as $code => $value) {
            $template = shortCodeReplacer('{{' . $code . '}}', $value, $template);
        }
        $template = urlencode($template);

        $message = shortCodeReplacer("{{number}}", $user->mobile, $general->sms_api);
        $message = shortCodeReplacer("{{message}}", $template, $message);
        $result = @file_get_contents($message);
    }
}

function send_customemail($user, $type = null, $shortCodes = [], $author)
{
    $general = GeneralSetting::first();
    $email_template1 = \App\EmailTemplate::where('act', $type)->where('email_status', 1)->first();

    $email_template = \App\EmailTemplateSetting::where('user_id', $author->id)->where('status', 1)->first();

    if ($general->en != 1 || !$email_template1) {
        return;
    }
    if (!is_null($author->company_logo) && ($email_template->act != "PASS_RESET_CODE" || "PASS_RESET_DONE" || "EVER_CODE" || "SEVER_CODE" || "2FA_ENABLE" || "2FA_DISABLE" || "ADMIN_SUPPORT_REPLY")) {
        $emaillogo = getImage(imagePath()['profile']['user']['path'] . '/' . $author->company_logo, imagePath()['profile']['user']['size']);
        $message = shortCodeReplacer("{{ emaillogo }}", $emaillogo, $email_template->email_template);
    }
    $message = shortCodeReplacer("{{ name }}", $user->username, $message);
    $messagebody = '';
    $emailbody = $email_template->email_body;

    foreach ($shortCodes['product_list'] as $value) {
        foreach ($value as $k => $v) {
            $emailbody = shortCodeReplacer('{{ ' . $k . ' }}', $v, $emailbody);
        }
        $messagebody .= $emailbody;
    }
    foreach ($shortCodes as $code => $value) {
        if ($code != 'product_list') {
            $message = shortCodeReplacer('{{ ' . $code . ' }}', $value, $message);
        }
    }

    $message = shortCodeReplacer("{{ product_list }}", $messagebody, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        send_php_mail($user->email, $user->username, $general->email_from, $email_template->subj, $message);
    } else if ($config->name == 'smtp') {
        send_smtp_mail($config, $user->email, $user->username, $general->email_from, $general->sitetitle, $email_template1->subj, $message);
    } else if ($config->name == 'sendgrid') {
        send_sendGrid_mail($config, $user->email, $user->username, $general->email_from, $general->sitetitle, $email_template1->subj, $message);
    } else if ($config->name == 'mailjet') {
        send_mailjet_mail($config, $user->email, $user->username, $general->email_from, $general->sitetitle, $email_template1->subj, $message);
    }
    if (!is_null($author)) {
        $ghluser = GhlAuth::where('user_id', $author->id)->first();
        if (!is_null($ghluser)) {
            $newcontact = addcontacttoghl($user, $author);
            $ghluserid = findauthid($author);
            send_notesto_buyer($ghluserid, $newcontact, $messagebody);
        }
    }
}
function send_email($user, $type = null, $shortCodes = [], $author = null)
{

    $general = GeneralSetting::first();

    $email_template = \App\EmailTemplate::where('act', $type)->where('email_status', 1)->first();

    if ($general->en != 1 || !$email_template) {
        return;
    }

    $message = shortCodeReplacer("{{name}}", $user->username, $general->email_template);
    $message = shortCodeReplacer("{{message}}", $email_template->email_body, $message);
    

    if (!is_null($author) && !is_null($author->company_logo) && ($email_template->act != "PASS_RESET_CODE" || "PASS_RESET_DONE" || "EVER_CODE" || "SEVER_CODE" || "2FA_ENABLE" || "2FA_DISABLE" || "ADMIN_SUPPORT_REPLY")) {
        $emaillogo = getImage(imagePath()['profile']['user']['path'] . '/' . $author->company_logo, imagePath()['profile']['user']['size']);
        $message = shortCodeReplacer("{{emaillogo}}", $emaillogo, $message);
    } else {
        $emaillogo = getImage(imagePath()['emaillogo']['user']['path'] . '/' . $general->email_logo, imagePath()['emaillogo']['user']['size']);

        $message = shortCodeReplacer("{{emaillogo}}", $emaillogo, $message);
    }

    if (empty($message)) {
        $message = $email_template->email_body;
    }

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
    }
    
    $config = $general->mail_config;

    if ($config->name == 'php') {
        send_php_mail($user->email, $user->username, $general->email_from, $email_template->subj, $message);
    } else if ($config->name == 'smtp') {
        send_smtp_mail($config, $user->email, $user->username, $general->email_from, $general->sitetitle, $email_template->subj, $message);
    } else if ($config->name == 'sendgrid') {
        send_sendGrid_mail($config, $user->email, $user->username, $general->email_from, $general->sitetitle, $email_template->subj, $message);
    } else if ($config->name == 'mailjet') {
        send_mailjet_mail($config, $user->email, $user->username, $general->email_from, $general->sitetitle, $email_template->subj, $message);
    }
    if (!is_null($author)) {
        $ghluser = GhlAuth::where('user_id', $author->id)->first();
        if (!is_null($ghluser)) {
            $newcontact = addcontacttoghl($user, $author);
            $ghluserid = findauthid($author);
            send_notesto_buyer($ghluserid, $newcontact, $message);
        }
    }
}
function addcontacttoghl($buyer, $seller)
{
    $find = GhlAuth::where('user_id', $seller->id)->first();
    if ($find) {
        $addnew = new stdClass;
        $addnew->firstName = $buyer->firstname;
        $addnew->lastName = $buyer->lastname;
        $addnew->email = $buyer->email;
        $addnew->locationId = $find->location_id;
        session()->put('ghl_api_token', $find->access_token);
        session()->put('location_id', $find->location_id);
        $addcontact = ghl_api_call('contacts/upsert', 'POST', json_encode($addnew), [], true, true);
        if ($addcontact && property_exists($addcontact, 'contact')) {
            return $addcontact->contact;
        }
        return false;
    }
    return false;
}

function findauthid($seller)
{
    $find = GhlAuth::where('user_id', $seller->id)->first();
    if (!is_null($find)) {
        session()->put('ghl_api_token', $find->access_token);
        session()->put('location_id', $find->location_id);
        $getuser = ghl_api_call('users/');
        $userid = $getuser->users;
        $ghlsellerid = null;
        foreach ($userid as $u) {
            $ghlsellerid = $u->id;
            return $ghlsellerid;
        }
    }
    return false;
}

function send_notesto_buyer($ghluserid, $newcontact, $message)
{
    $notes = new stdClass;
    $notes->userId = $ghluserid;
    $notes->body = $message;
    $sendingnotes = ghl_api_call('contacts/' . $newcontact->id . '/notes', 'POST', json_encode($notes), [], true, true);
    dd($sendingnotes);
}

function send_php_mail($receiver_email, $receiver_name, $sender_email, $subject, $message)
{
    $gnl = GeneralSetting::first();
    $headers = "From: $gnl->sitename <$sender_email> \r\n";
    $headers .= "Reply-To: $gnl->sitename <$sender_email> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    @mail($receiver_email, $subject, $message, $headers);
}

function send_smtp_mail($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
{
    $mail = new PHPMailer(true);
    $gnl = GeneralSetting::first();
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = $config->host;
        $mail->SMTPAuth = true;
        $mail->Username = $config->username;
        $mail->Password = $config->password;
        if ($config->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port = $config->port;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($sender_email, $sender_name);
        $mail->addAddress($receiver_email, $receiver_name);
        $mail->addReplyTo($sender_email, $gnl->sitename);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();
    } catch (Exception $e) {

        throw new Exception($e);
    }
}

function send_sendGrid_mail($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
{
    $sendgridMail = new \SendGrid\Mail\Mail();
    $sendgridMail->setFrom($sender_email, $sender_name);
    $sendgridMail->setSubject($subject);
    $sendgridMail->addTo($receiver_email, $receiver_name);
    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        // echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}

function send_mailjet_mail($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
{
    $mj = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $sender_email,
                    'Name' => $sender_name,
                ],
                'To' => [
                    [
                        'Email' => $receiver_email,
                        'Name' => $receiver_name,
                    ],
                ],
                'Subject' => $subject,
                'TextPart' => "",
                'HTMLPart' => $message,
            ],
        ],
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function menuActive($routeName, $type = null)
{
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}

function imagePath()
{
    $data['gateway'] = [
        'path' => 'assets/images/gateway',
        'size' => '800x800',
    ];
    $data['verify'] = [
        'withdraw' => [
            'path' => 'assets/images/verify/withdraw',
        ],
        'deposit' => [
            'path' => 'assets/images/verify/deposit',
        ],
    ];
    $data['image'] = [
        'default' => 'assets/images/default.png',
    ];
    $data['audio'] = [
        'path' => 'assets/images/notifyaudio',
    ];
    $data['withdraw'] = [
        'method' => [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ],
    ];
    $data['ticket'] = [
        'path' => 'assets/images/support',
    ];
    $data['language'] = [
        'path' => 'assets/images/lang',
        'size' => '64x64',
    ];
    $data['logoIcon'] = [
        'path' => 'assets/images/logoIcon',
    ];
    $data['favicon'] = [
        'size' => '128x128',
    ];
    $data['extensions'] = [
        'path' => 'assets/images/extensions',
    ];
    $data['seo'] = [
        'path' => 'assets/images/seo',
        'size' => '600x315',
    ];
    $data['emaillogo'] = [
        'user' => [
            'path' => 'assets/images/user/profile',
            'size' => '150x150',
        ],
    ];
    $data['profile'] = [
        'user' => [
            'path' => 'assets/images/user/profile',
            'size' => '512x512',
        ],
        'cover' => [
            'path' => 'assets/images/user/profile',
            'size' => '835x345',
        ],
        'admin' => [
            'path' => 'assets/admin/images/profile',
            'size' => '400x400',
        ],
        'reviewer' => [
            'path' => 'assets/reviewer/images/profile',
            'size' => '400x400',
        ],
    ];
    $data['category'] = [
        'path' => 'assets/images/category',
        'size' => '512x512',
    ];
    $data['level'] = [
        'path' => 'assets/images/level',
        'size' => '512x512',
    ];
    $data['p_image'] = [
        'path' => 'assets/images/product',
        'size' => '1180x600',
        'thumb' => '590x300',
    ];
    $data['p_file'] = [
        'path' => 'assets/product',
    ];
    $data['p_screenshot'] = [
        'path' => 'assets/images/screenshot',
    ];
    $data['temp_p_image'] = [
        'path' => 'assets/images/temp_product',
        'size' => '1920x1080',
        'thumb' => '590x300',
    ];
    $data['temp_p_file'] = [
        'path' => 'assets/temp_product',
    ];
    $data['temp_p_screenshot'] = [
        'path' => 'assets/images/temp_screenshot',
    ];
    return $data;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    \Carbon\Carbon::setlocale($lang);
    return \Carbon\Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'd M, Y h:i A')
{
    $lang = session()->get('lang');
    \Carbon\Carbon::setlocale($lang);
    return \Carbon\Carbon::parse($date)->translatedFormat($format);
}

//moveable
function send_general_email($email, $subject, $message, $receiver_name = '')
{

    $general = GeneralSetting::first();

    if ($general->en != 1 || !$general->email_from) {
        return;
    }

    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{name}}", $receiver_name, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        send_php_mail($email, $receiver_name, $general->email_from, $subject, $message);
    } else if ($config->name == 'smtp') {
        send_smtp_mail($config, $email, $receiver_name, $general->email_from, $general->sitename, $subject, $message);
    } else if ($config->name == 'sendgrid') {
        send_sendgrid_mail($config, $email, $receiver_name, $general->email_from, $general->sitename, $subject, $message);
    } else if ($config->name == 'mailjet') {
        send_mailjet_mail($config, $email, $receiver_name, $general->email_from, $general->sitename, $subject, $message);
    }
}

function getContent($data_keys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = \App\Frontend::where('data_keys', $data_keys)->latest()->first();
    } else {
        $article = \App\Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $data_keys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $data_keys)->latest()->get();
        }
    }
    return $content;
}

function gatewayRedirectUrl()
{
    return 'user.home';
}

function paginateLinks($data, $design = 'admin.partials.paginate')
{
    return $data->appends(request()->all())->links($design);
}

function paginateMacro()
{
    Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
        return new LengthAwarePaginator(
            $this->forPage($page, $perPage),
            $total ?: $this->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    });
}

function displayRating($avgReview)
{
    $prec = round($avgReview, 2) - intval($avgReview);
    $result = '';
    if ($prec > 0.25) {
        $avgReview = intval($avgReview) + 0.5;
    }

    if ($prec > 0.75) {
        $avgReview = intval($avgReview) + 1;
    }

    for ($i = 0; $i < intval($avgReview); $i++) {
        $result .= '<i class="la la-star"></i>';
    }

    if ($avgReview - intval($avgReview) == 0.5) {
        $i++;
        $result .= '<i class="las la-star-half-alt"></i>';
    }

    for ($k = 0; $k < 5 - $i; $k++) {
        $result .= '<i class="lar la-star"></i>';
    }
    return $result;
}

function levelCommission($id, $amount, $commissionType = '')
{
    $usr = $id;
    $user = \App\User::find($id);
    $i = 1;
    $gnl = GeneralSetting::first();
    $level = \App\Referral::where('commission_type', $commissionType)->count();

    while ($usr != "" || $usr != "0" || $i < $level) {
        $me = \App\User::find($usr);
        $refer = \App\User::find($me->ref_by);

        if ($refer == "") {
            break;
        }

        $commission = \App\Referral::where('commission_type', $commissionType)->where('level', $i)->first();

        if (!$commission) {
            break;
        }
        $com = ($amount * $commission->percent) / 100;

        $referWallet = $refer;
        $new_bal = getAmount($referWallet->balance + $com);
        $referWallet->balance = $new_bal;
        $referWallet->save();
        $trx = getTrx();

        $transaction = new \App\Transaction();
        $transaction->user_id = $refer->id;
        $transaction->amount = getAmount($com);
        $transaction->post_balance = $new_bal;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Level ' . $i . ' Referral Commission From ' . $user->username;
        $transaction->trx = $trx;
        $transaction->save();

        $commissionLog = new \App\CommissionLog();
        $commissionLog->to_id = $refer->id;
        $commissionLog->from_id = $id;
        $commissionLog->level = $i;
        $commissionLog->commission_amount = getAmount($com);
        $commissionLog->main_amo = $new_bal;
        $commissionLog->trx_amo = $amount;
        $commissionLog->title = 'Level ' . $i . ' Referral Commission From ' . $user->username;
        $commissionLog->type = $commissionType;
        $commissionLog->percent = $commission->percent;
        $commissionLog->trx = $trx;
        $commissionLog->save();

        notify($refer, 'REFERRAL_COMMISSION', [
            'amount' => getAmount($com),
            'main_balance' => $new_bal,
            'trx' => $trx,
            'level' => $i . ' level Referral Commission',
            'currency' => $gnl->cur_text,
        ]);

        $usr = $refer->id;
        $i++;
    }
    return 0;
}

function ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}

function uploadRemoteFile($file, $location, $fileExtension, $disk)
{
    diskConfigure();
    $disk = Storage::disk($disk);
    makeRemoteDirectory($location, $disk);
    $video = uniqid() . time() . '.' . $fileExtension;
    $disk->put($location . '/' . $video, $file);
    return ['success', $location . '/' . $video];
}

function makeRemoteDirectory($path, $disk)
{
    if ($disk->exists($path)) {
        return true;
    }
    $disk->makeDirectory($path);
}

function removeRemoteFile($video, $disk)
{
    diskConfigure();
    $path = 'assets/videos/' . $video;
    $storage = Storage::disk($disk);
    if (file_exists($path) && is_file($path)) {
        @unlink($path);
        return true;
    } elseif ($storage->exists($video)) {
        $storage->delete($video);
    } else {
        return false;
    }
}

function diskConfigure()
{
    $gnl = GeneralSetting::first();
    //ftp
    Config::set('filesystems.disks.custom-ftp.driver', 'ftp');
    Config::set('filesystems.disks.custom-ftp.host', $gnl->ftp->host);
    Config::set('filesystems.disks.custom-ftp.username', $gnl->ftp->username);
    Config::set('filesystems.disks.custom-ftp.password', $gnl->ftp->password);
    Config::set('filesystems.disks.custom-ftp.port', 21);
    Config::set('filesystems.disks.custom-ftp.root', $gnl->ftp->root);
}

function updateAuthorLevel($author)
{
    $authCurrentLevel = Level::where('id', $author->level_id)->first();
    if ($authCurrentLevel) {
        $authNextLevel = Level::where('id', '!=', $authCurrentLevel->id)->where('earning', '>=', $author->earning)->orderBy('earning', 'ASC')->first();
        $author->level_id = $authNextLevel->id;
        $author->save();
    }
}

function managebumps($license, $product)
{
    if ($license == 1) {

        if ($product->support_discount) {

            $tempCharge = ($product->regular_price * $product->support_charge) / 100;
            $lessCharge = ($tempCharge * $product->support_discount) / 100;
            $supportFee = $tempCharge - $lessCharge;
            $totalPrice = $product->regular_price + $supportFee;
        } else {
            $supportFee = ($product->regular_price * $product->support_charge) / 100;
            $totalPrice = $product->regular_price + $supportFee;
        }
    }

    if ($license == 2) {
        if ($product->support_discount) {

            $tempCharge = ($product->extended_price * $product->support_charge) / 100;
            $lessCharge = ($tempCharge * $product->support_discount) / 100;
            $supportFee = $tempCharge - $lessCharge;
            $totalPrice = $product->extended_price + $supportFee;
        } else {
            $supportFee = ($product->extended_price * $product->support_charge) / 100;
            $totalPrice = $product->extended_price + $supportFee;
        }
    }

    return [$totalPrice, $supportFee];
}
function getcolor1()
{
    $color1 = '#' . App\GeneralSetting::first()->base_color;
    return $color1;
}
function getcolor2()
{
    $color2 = '#' . App\GeneralSetting::first()->secondary_color;
    return $color2;
}
function userid()
{
    return auth()->user()->id;
}

function setting($j, $k)
{
    return $k;
}

// Ghl oAuthentication system

function ghl_api_call($url = '', $method = 'get', $data = '', $headers = [], $json = false, $is_v2 = true)
{
    $baseurl = 'https://rest.gohighlevel.com/v1/';
    $bearer = 'Bearer ';
    if (setting('oauth_ghl', 'api') != 'oauth' && 1 == 2) { //if the api is from v1
        //$token = company_user()->ghl_api_key;
    } else {
        $token = session('ghl_api_token');
        if (empty($token)) {
            abort(redirect()->intended(route('setting.index')));
        }
        $baseurl = 'https://services.leadconnectorhq.com/';
        $version = setting('oauth_ghl_version', '2021-04-15');
        $location = session('location_id');
        $headers['Version'] = $version;
        if ($method == 'get' || $method == 'GET') {
            $url .= (strpos($url, '?') !== false) ? '&' : '?';
            if (strpos($url, 'location_id=') === false) {
                $url .= 'locationId=' . $location;
            }
        }
        if (strpos($url, 'custom') !== false) {
            $url = 'locations/' . $location . '/' . $url;
        }
    }
    if ($token) {
        $headers['Authorization'] = $bearer . $token;
    }
    $headers['Content-Type'] = "application/json";

    $client = new \GuzzleHttp\Client(['http_errors' => false, 'headers' => $headers]);
    $options = [];
    if (!empty($data)) {
        $options['body'] = $data;
    }
    $url1 = $baseurl . $url;

    $response = $client->request($method, $url1, $options);
    $bd = $response->getBody()->getContents();

    $bd = json_decode($bd);
    if (isset($bd->error) && $bd->error == 'Unauthorized') {
        request()->code = setting('refresh_token', login_id());
        $tok = ghl_token(request(), '1');
        sleep(1);
        return ghl_api_call($url, $method, $data, $headers, $json, $is_v2);
        if (session('cronjob')) {
            return false;
        }
        abort(401, 'Unauthorized');
    }
    return $bd;
}

function ghl_oauth_call($code = '', $method = '')
{
    $url = 'https://api.msgsndr.com/oauth/token';
    $curl = curl_init();
    $data = [];
    $data['client_id'] = env('GHL_CLIENT');
    $data['client_secret'] = env('GHL_SECRET');
    $md = empty($method) ? 'code' : 'refresh_token';
    $data[$md] = $code; // (empty($code)?company_user()->ghl_api_key:$code);
    $data['grant_type'] = empty($method) ? 'authorization_code' : 'refresh_token';

    $postv = '';
    $x = 0;

    foreach ($data as $key => $value) {
        if ($x > 0) {
            $postv .= '&';
        }
        $postv .= $key . '=' . $value;
        $x++;
    }

    $curlfields = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postv,
    );
    //dd($url,$postv);
    curl_setopt_array($curl, $curlfields);

    $response = curl_exec($curl);
    $response = json_decode($response);
    curl_close($curl);
    return $response;
}

function login_id()
{
    if (session('ghl_api_token')) {
        $seller = GhlAuth::where('access_token', session('ghl_api_token'))->first();
        return $seller->refresh_token;
    } else {
        dd("lan milla haa");
    }
}

function get_user($loc)
{
    return User::where('id', $loc);
}
function connected()
{
    $connected = GhlAuth::where('user_id', auth()->user()->id)->first();
    return $connected;
}
function saveUser($code)
{
    $location_detail = ghl_api_call('locations/' . $code->locationId, 'get', '', [], true, true);
    $loc = $location_detail->location;
    $data = [
        'access_token' => $code->access_token,
        'refresh_token' => $code->refresh_token,
        'location_id' => $code->locationId,
        'location_name' => $loc->name,
        'user_id' => auth()->id(),
    ];

    GhlAuth::updateorCreate([
        'user_id' => auth()->id(),
    ], $data);
    $notify[] = ['success', 'Your are Newly Connection is established with the CRM'];

    return redirect()->route('user.home')->withNotify($notify);
}
function getCss()
{
    $find = CustomCss::where('user_id', auth()->user()->id)->first();
    if (!is_null($find)) {
        return $find->styletag;
    }
}

function ghl_token($request, $type = '')
{
    $code = $request->code;
    $code = ghl_oauth_call($code, $type);
    if ($code) {
        if (property_exists($code, 'access_token')) {
            session()->put('ghl_api_token', $code->access_token);
            $user = saveUser($code);
        } else {
            if (property_exists($code, 'error_description')) {
                if (empty($type)) {
                    abort(redirect()->route('user.register')->with('error', $code->error_description));
                }
            }
            abort(redirect()->route('user.register')->with('error', $code->error_description));
        }
    }
    if (empty($type)) {
        abort(redirect()->route('user.register')->with('error', 'Server error'));
    }
}

function getscript($gen)
{

    return $gen->suggestion_box;
}
function verifySOSignature($ssoUrl, $secret)
{
    // Parse the SSO URL
    list($base_url, $providedSignature) = explode('?signature=', $ssoUrl, 2);

    $testSignature = hash_hmac('sha256', $base_url, $secret);

    // Check if the provided signature matches the calculated signature
    return hash_equals($testSignature, $providedSignature);
}

function getLastPartWithoutDots($inputString)
{
    $parts = explode('.', $inputString);

    if (count($parts) === 2) {
        return $parts[1];
    }
    return null;
}

// app/helpers.php

if (!function_exists('extractBearerToken')) {
    function extractBearerToken($authorizationHeader)
    {
        // Check if the header starts with 'Bearer '
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            // Remove 'Bearer ' and any leading/trailing spaces
            return trim(substr($authorizationHeader, 7));
        }

        // If the header does not start with 'Bearer ', return null or handle accordingly
        return null;
    }
}

