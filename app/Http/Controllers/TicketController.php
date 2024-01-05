<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\SupportAttachment;
use App\SupportMessage;
use App\SupportTicket;
use App\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TicketController extends Controller
{

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }
    // Support Ticket
    public function supportTicket(Request $request)
    {
        $user = auth()->user() ?? auth('user')->user();

        if (!isset($user) || $user->id == null) {
            abort(404);
        }
        $api = false;
        $token = '';
        if (($request->is('api/*') || $request->is('iframe/*')) && $request->token) {
            $partial = false;
            $api = true;
            $token = $request->token;
        }
        $page_title = "Support Tickets";
        if ($request->ajax()) {
            $data = SupportTicket::where('user_id', $user->id)->orwhere('seller_id', $user->id)->with('product', 'seller')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('subject', function ($row) {
                    $btn = '<a href="' . route('ticket.view', $row->ticket) . '" class="text--base">
                                                    [Ticket ' . $row->ticket . ']';
                    if (!is_null($row->product_id)) {
                        $btn .= '<a href="' . route('product.details', [str_slug(__($row->product->name)), $row->product_id]) . '">[' . $row->product->name . ']</a>';
                    }
                    $btn .= '__' . $row->subject;
                    return $btn;
                })
                ->editColumn('last_reply', function ($row) {
                    return Carbon::parse($row->last_reply)->diffForHumans();
                })
                ->editColumn('seller_id', function ($row) {

                    if ($row->seller_id != 0) {
                        $btn = 'Seller( ' . $row->seller->username . ' )';
                    } else {
                        $btn = 'Admin';
                    }
                    return  $btn;
                })
                ->editColumn('status', function ($row) {
                    $rowdata = '';
                    if ($row->status == 0) {
                        $rowdata = '<span class="badge badge--success">Open</span>';
                    } elseif ($row->status == 1) {
                        $rowdata = '<span class="badge badge--primary">Answered</span>';
                    } elseif ($row->status == 2) {
                        $rowdata = '<span class="badge badge--warning">Customer Reply</span>';
                    } elseif ($row->status == 3) {
                        $rowdata = '<span class="badge badge--danger">Closed</span>';
                    }
                    return $rowdata;
                })
                ->addColumn('action', function ($row) use ($request) {
                    $url  = route('ticket.view', $row->ticket);
                    if ($request->api && $request->token) {
                        $url = route('iframe.api.ticket.show', $row->ticket . "?token=" . $request->token);
                    }
                    return '<a href="' . $url . '"class="icon-btn bg--primary"><i class="las la-desktop"></i></a>';
                })
                ->rawColumns(['action', 'subject', 'status'])
                ->make(true);
        }
        
        if(($request->is('api/*') || $request->is('iframe/*')) && $request->token) {
            $partial = false;
        }

        return view($this->activeTemplate . 'user.support.index', get_defined_vars());
    }
    public function openSupportTicket(Request $request, $id = null)
    {
        $user = auth()->user() ?? auth('user')->user();
        if (!$user) {
            abort(404);
        }
        $page_title = "Support Tickets";
        if (!is_null($id)) {
            $product = Product::where('id', $id)->first();
        }
        $sellers = User::where('seller', 1)->where('status', 1)->get();

        if($request->is('api/*')){
            $partial = false;
        }

        return view($this->activeTemplate . 'user.support.create', get_defined_vars());
    }

    public function storeSupportTicket(Request $request)
    {
        $ticket = new SupportTicket();
        $message = new SupportMessage();

        $files = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx');


        $this->validate($request, [
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) use ($files, $allowedExts) {
                    foreach ($files as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > 2) {
                            return $fail("Images MAX  2MB ALLOW!");
                        }
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg, pdf, doc, docx files are allowed");
                        }
                    }
                    if (count($files) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'name' => 'required|max:191',
            'email' => 'required|email|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);


        $ticket->user_id = Auth::id();
        $random = rand(100000, 999999);
        $ticket->ticket = $random;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->subject = $request->subject;
        if ($request->product) {
            $ticket->ticket_for = 1;
            $product = Product::Where('id', $request->product)->first();
            $ticket->seller_id = $product->user_id;
            $ticket->product_id = $request->product;
        }
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();


        $path = imagePath()['ticket']['path'];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as  $file) {
                try {
                    $attachment = new SupportAttachment();
                    $attachment->support_message_id = $message->id;
                    $attachment->attachment = uploadFile($file, $path);
                    $attachment->save();
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload your ' . $file];
                    return back()->withNotify($notify)->withInput();
                }
            }
        }
        $notify[] = ['success', 'ticket created successfully!'];
        if($request->is('api/*')){
            return  to_route('iframe.api.ticket', ['token' => $request->token])->withNotify($notify);
        }
        return redirect()->route('ticket')->withNotify($notify);
    }

    public function viewTicket($ticket)
    {
        $page_title = "Support Tickets";
        $my_ticket = SupportTicket::where('ticket', $ticket)->latest()->first();
        $messages = SupportMessage::where('supportticket_id', $my_ticket->id)->latest()->get();
        $user = auth()->user();
        if ($my_ticket->seller_id != 0) {
            $seller = User::where('id', $my_ticket->seller_id)->first();
        }
        if($request->is('api/*')){
            $partial = false;
        }
        return view($this->activeTemplate . 'user.support.view', get_defined_vars());
    }
    public function backticket()
    {

        return $this->supportTicket();
    }

    public function replyTicket(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $message = new SupportMessage();
        if ($request->replayTicket == 1) {
            $imgs = $request->file('attachments');
            $allowedExts = array('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx');

            $this->validate($request, [
                'attachments' => [
                    'max:4096',
                    function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                        foreach ($imgs as $img) {
                            $ext = strtolower($img->getClientOriginalExtension());
                            if (($img->getSize() / 1000000) > 2) {
                                return $fail("Images MAX  2MB ALLOW!");
                            }
                            if (!in_array($ext, $allowedExts)) {
                                return $fail("Only png, jpg, jpeg, pdf doc docx files are allowed");
                            }
                        }
                        if (count($imgs) > 5) {
                            return $fail("Maximum 5 files can be uploaded");
                        }
                    },
                ],
                'message' => 'required',
            ]);

            $ticket->status = 2;
            $ticket->last_reply = Carbon::now();
            $ticket->save();

            $message->supportticket_id = $ticket->id;
            if (auth()->user()->id == $ticket->seller_id) {
                $message->seller_id = auth()->user()->id;
            }
            $message->message = $request->message;
            $message->save();

            $path = imagePath()['ticket']['path'];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    try {
                        $attachment = new SupportAttachment();
                        $attachment->support_message_id = $message->id;
                        $attachment->attachment = uploadFile($file, $path);
                        $attachment->save();
                    } catch (\Exception $exp) {
                        $notify[] = ['error', 'Could not upload your ' . $file];
                        return back()->withNotify($notify)->withInput();
                    }
                }
            }

            $notify[] = ['success', 'Support ticket replied successfully!'];
        } elseif ($request->replayTicket == 2) {
            $ticket->status = 3;
            $ticket->last_reply = Carbon::now();
            $ticket->save();
            $notify[] = ['success', 'Support ticket closed successfully!'];
        }
        return back()->withNotify($notify);
    }
    public function ticketDownload($ticket_id)
    {
        $attachment = SupportAttachment::findOrFail(decrypt($ticket_id));
        $file = $attachment->attachment;

        $path = imagePath()['ticket']['path'];
        $full_path = $path . '/' . $file;

        $title = str_slug($attachment->supportMessage->ticket->subject);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($full_path);


        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($full_path);
    }
}
