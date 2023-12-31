<?php

namespace App\Http\Controllers\Admin;

use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Referral;
use Illuminate\Http\Request;

class ManageReferralController extends Controller
{
    public function index()
    {
        $page_title = 'Manage Referral';
        $depositLevels     = Referral::where('commission_type','deposit_commission')->get();

        return view('admin.referral.index',compact('page_title', 'depositLevels'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'level*' => 'required|integer|min:1',
            'percent*' => 'required|numeric',
            'commission_type' => 'required',
        ]);
        Referral::where('commission_type',$request->commission_type)->delete();
        for ($a = 0; $a < count($request->level); $a++){
            Referral::create([
                'level' => $request->level[$a],
                'percent' => $request->percent[$a],
                'commission_type' => $request->commission_type,
                'status' => 1,
            ]);
        }

        $notify[] = ['success', 'Create Successfully'];
        return back()->withNotify($notify);
    }

    public function referralStatusUpdate($type)
    {
        $general_setting = GeneralSetting::first();
        if (@$general_setting->$type == 1) {
            @$general_setting->$type = 0;
        $general_setting->save();
        }elseif(@$general_setting->$type == 0){
            @$general_setting->$type = 1;
            $general_setting->save();
        }else{
            $notify[] = ['error', 'Something Wrong'];
            return back()->withNotify($notify);
        }
        $notify[] = ['success', 'Updated Successfully'];
        return back()->withNotify($notify);
    }
}
