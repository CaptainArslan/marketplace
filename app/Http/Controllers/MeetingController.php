<?php

namespace App\Http\Controllers;

use App\BuyerSellerMeeting;
use Illuminate\Support\Carbon;
use App\Product;
use App\Http\Controllers\Controller;
use App\Notification;
use App\GeneralSetting;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class MeetingController extends Controller
{
    public $activeTemplate;
    //
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function allMeeting(Request $request)
    {
        $page_title = 'All Meetings';
        $empty_message = 'No data found';

        $user = auth()->user() ?? auth('user')->user();
        
        $token = '';
        $api = false;
        if ($request->is('api/*')) {
            $token = $request->token;
            $api = true;
            $partial = false;
        }

        if ($request->ajax()) {
            if ($user->seller == 1) {
                $data = BuyerSellerMeeting::where('author_id', $user->id);
            } else {
                $data = BuyerSellerMeeting::where('buyer_id', $user->id);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $rowdata = '';
                    if ($row->status == 0) {
                        $rowdata = '<span class="badge bg-warning">Pending</span>';
                    } elseif ($row->status == 1) {
                        $rowdata = '<span class="badge  bg-success">Approved</span>';
                    } elseif ($row->status == 2) {
                        $rowdata = ' <span class="badge  bg-danger">Meeting Over</span>';
                    } elseif ($row->status == 3) {
                        $rowdata = ' <span class="badge  bg-danger">Rejected</span>';
                    }
                    return $rowdata;
                })
                ->addColumn('action', function ($row) use ($user) {
                    $general = GeneralSetting::first();
                    if ($user->seller == 0) {
                        $btn = '<td data-label="Action">
                    <a href="' . route('user.meeting.edit', $row->id) . '"
                                                        class="icon-btn bg--primary"><i class="las la-edit"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Update"></i></a>';
                        //<a href="javascript:void(0)" class="icon-btn bg--danger"
                        //     data-bs-toggle="modal" data-bs-target="#deleteModal'.$row->id. '"><i
                        //         class="lar la-trash-alt" data-bs-toggle="tooltip"
                        //         data-bs-placement="top" title="Delete"></i></a>
                        $btn .= '<a href="javascript:void(0)" class="icon-btn bg--primary approveBtn' . $row->id . '"
                                                        data-bs-toggle="modal" data-bs-target="#approveModal' . $row->id . '"
                                                        ><i
                                                            class="las la-desktop" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Details"></i></a>
                                                </td>';
                    } else {
                        $btn = '<td data-label="Action">
                                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#m_' . $row->id . '" class="icon-btn bg--danger"><i
                                                            class="editfieldresponse las la-edit" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title=" Please Fill the Meeting details"></i></a>
                                                    <a href="javascript:void(0)" class="icon-btn bg--primary approveBtn' . $row->id . '"
                                                        data-bs-toggle="modal" data-bs-target="#approveModal' . $row->id . '"><i
                                                            class="las la-desktop" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Details"></i></a>
                                                </td>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view($this->activeTemplate . 'user.zoommeeting.index', get_defined_vars());
    }


    public function newMeeting($id)
    {
        $page_title = 'New Meeting';
        $product_id = $id;
        return view($this->activeTemplate . 'user.zoommeeting.new', get_defined_vars());
    }
    public function deleteMeeting(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required',
        ]);
        $meeting = BuyerSellerMeeting::findOrFail($request->meeting_id);
        $meeting->delete();

        $notify[] = ['success', 'Meeting successfully deleted'];
        return back()->withNotify($notify);
    }
    public function storeMeeting(Request $request)
    {
        $validation_rule = [
            'agenda' => 'required|max:191',
            'time' => 'required',
            'date' => 'required',
        ];
        $meeting = BuyerSellerMeeting::where('buyer_id', auth()->user()->id)->where('status', '!=', 3)->first();
        if ($meeting) {
            $notify[] = ['error', 'Your first meeting is Undr Process.'];
            return back()->withNotify($notify);
        } else {
            $createmeeting = new BuyerSellerMeeting;
            $createmeeting->agenda = $request->agenda;
            $createmeeting->meeting_date = $request->meetingdate;
            $createmeeting->meeting_time = $request->meetingtime;
            $createmeeting->status = 0;
            $createmeeting->buyer_id = auth()->user()->id;
            $product = Product::where('id', $request->product_id)->first();
            $createmeeting->product_id = $request->product_id;
            $createmeeting->author_id = $product->user_id;
            $createmeeting->save();

            $notification = new Notification;
            $notification->user_id = $product->user_id;
            $notification->cf_status = 0;
            $notification->product_id = $request->product_id;
            $notification->meeting_status = 1;  //1 means  send request to the seller
            $notification->save();
            $msg = "Your Request has been sent";
        }
        $notify[] = ['success', 'Meeting request successfully submitted'];
        return redirect()->route('user.meeting.all')->withNotify($notify);
    }
    public function authorResponseMeeting(Request $req)
    {
        $findmeeting = BuyerSellerMeeting::where('id', $req->meeting_id)->first();
        $notification = new Notification;
        $notification->user_id = $findmeeting->buyer_id;
        $notification->cf_status = 0;
        $notification->product_id = $findmeeting->product_id;
        if ($req->meetinglink != null) {
            $findmeeting->update([
                'meeting_link' => $req->meetinglink,
                'status' => 1,

            ]);
            $notification->meeting_status = 2;   //2 sent the  confirm approval request to Buyer
            $notification->save();
            $notify[] = ['success', 'Meeting Approval is Sent'];
            return back()->withNotify($notify);
        } else {
            $findmeeting->update([
                'status' => 3,
            ]);
            $notification->meeting_status = 3;   //3 means Rejection sent to buyer
            $notification->save();
            $notify[] = ['success', 'Meeting Rejection Sent'];
            return back()->withNotify($notify);
        }
    }
    public function editMeeting($id)
    {

        $meetings = BuyerSellerMeeting::where('id', $id)->where('buyer_id', auth()->user()->id)->first();
        $page_title = 'Edit  Meeting';
        return view($this->activeTemplate . 'user.zoommeeting.edit', get_defined_vars());
    }
    public function updateMeeting(Request $req, $id)
    {;
        $createmeeting = BuyerSellerMeeting::where('id', $id)->where('buyer_id', auth()->user()->id)->first();
        $createmeeting->agenda = $req->agenda;
        $createmeeting->meeting_date = $req->meetingdate;
        $createmeeting->meeting_time = date("g:i a", strtotime($req->meetingtime));
        $createmeeting->status = 0;
        $createmeeting->buyer_id = auth()->user()->id;
        $createmeeting->product_id = $req->productid;
        $createmeeting->save();
        $notify[] = ['success', $createmeeting->agenda . ' has been Updated'];
        return back()->withNotify($notify);
    }
}
