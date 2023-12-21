<?php

namespace App\Http\Controllers\Admin;

use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Subscription;
use Illuminate\Http\Request;

class ManageSubscriptionController extends Controller
{
    //
    public function index()
    {
        $page_title = 'Pricing Plans';
        $general = GeneralSetting::first();
        $plans = Subscription::all();
        return view('admin.subscription.index', get_defined_vars());
    }
    public function addplan(Request $req)
    {
        if ($req->id) {

            $plan = Subscription::where('id', $req->id)->first();
            $notify[] = ['success', 'Plan Updated Successfully'];
        } else {

            $plan = new Subscription;
            $notify[] = ['success', 'Plan Added Successfully'];
        }
        $plan->name = $req->name;
        $plan->price = $req->price;
        $plan->plan_type = $req->plantype;
        $plan->cf_status = $req->customfield;
        $plan->allowed_product = $req->productallowed;
        $plan->discount = $req->discount;
        if ($req->commisiontype == '1') {

            $plan->commission = $req->percomm;
        } else {
            $plan->commission = $req->fixedcomm;
        }
        $plan->commission_type = $req->commisiontype;

        $plan->status = 1;
        $plan->save();
        return back()->withNotify($notify);
    }
    public function activatePlan(Request $req)
    {
        $plan = Subscription::where('id', $req->id)->first();
        $plan->status = 1;
        $plan->save();

        $notify[] = ['success', 'Plan Activated Successfully'];
        return back()->withNotify($notify);
    }
    public function deactivatePlan(Request $req)
    {
        $plan = Subscription::where('id', $req->id)->first();
        $plan->status = 0;
        $plan->save();

        $notify[] = ['success', 'Plan Deactivated Successfully'];
        return back()->withNotify($notify);
    }
}
