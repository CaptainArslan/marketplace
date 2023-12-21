<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Subscription;
use App\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    //
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }
    public function getplans()
    {     $plans=Subscription::all();
        $alreadybuy=UserSubscription::where('user_id',auth()->user()->id)->where('status',1)->first();
        $page_title = 'Upgrade Plan';
        return view($this->activeTemplate . 'user.subscription.index', get_defined_vars());
    }
    public function cancelPlan(Request $req)
    {  if($req->id){

    }
        $plans = UserSubscription::Where('sub_id',$req->id)->where('user_id',auth()->user()->id)->first();
        if($plans){
            $plans->status = 0;
            $plans->save();
        }
        $page_title = 'Upgrade Plan';
        return back();
    }

}
