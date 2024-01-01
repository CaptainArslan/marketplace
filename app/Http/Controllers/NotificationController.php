<?php

namespace App\Http\Controllers;

use App\BuyerSellerMeeting;
use App\Http\Controllers\Controller;
use App\Notification;
use App\Sell;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public $activeTemplate ;
    //
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }
    public function delnotify(Request $req)
    {
        $notifications = Notification::where('id', $req->id)->first();
        if($notifications){
        $notifications->delete();
        return response()->json('success');
        }else{
            return response()->json('error');
        }
    }
    public function notifyMarkasread($nid = null)
    {
        $user = auth()->user() ?? auth('user')->user();
        if ($nid == null) {
            $notifications = Notification::where('user_id', $user->id)->where('mark_read', 0)->get();

            foreach ($notifications as $n) {
                $n->mark_read = 1;
                $n->save();
            }
        } else {
            $notifications = Notification::where('id', $nid)->update([
                'mark_read' => 1
            ]);
        }
        return response()->json('success');
    }
    public function notifyDetail($pid)
    {
        if (auth()->user()->seller == 1) {
            $page_title = 'Sell Logs';
            $sells = Sell::where('author_id', auth()->user()->id)->where('id', $pid)->where('status', 1)->with('product', 'productcustomfields', 'customfieldresponse', 'bumpresponses')->paginate(getPaginate());
            $empty_message = 'No data found.';
            return view($this->activeTemplate . 'user.sell_log', get_defined_vars());
        } else {
            $page_title = "Purchased Products";
            $products = Sell::where('user_id', auth()->user()->id)->where('id', $pid)->with('product', 'productcustomfields', 'customfieldresponse', 'bumpresponses')->paginate(getPaginate());
            $empty_message = 'No data found';
            return view($this->activeTemplate . 'user.product.purchased', get_defined_vars());
        }
    }
    public function notifyCount()
    {
        $count = Notification::where('user_id', auth()->user()->id)->where('mark_read', 0)->orderBy('id', 'desc')->get();
        $data = new \stdClass;
        $data->count = count($count);
        $data->last_notification = [];
        if ($data->count > 0) {
            $data->last_notification = [$count[0]];
        }
        return response()->json($data);
    }
    public function notifyAll()
    {
        $notifications = Notification::where('user_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('cf_status', '!=', null)->orWhere('meeting_status', '!=', null);
            })
            ->with('products', 'selling', 'subscription')
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['notifications' => $notifications]);
    }
    public function metNotifyDetail($pid)
    { {
            $page_title = 'All Meetings';
            $empty_message = 'No data found';
            if (auth()->user()->seller == 1) {
                $meetings = BuyerSellerMeeting::where('author_id', auth()->user()->id)->where('product_id', $pid)->paginate(getPaginate());
            } else {
                $meetings = BuyerSellerMeeting::where('buyer_id', auth()->user()->id)->where('product_id', $pid)->paginate(getPaginate());
            }
            return view($this->activeTemplate . 'user.zoommeeting.index', get_defined_vars());
        }
    }
}
