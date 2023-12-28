<?php

namespace App\Http\Controllers;

use Str;
use File;
use Image;
use App\Sell;
use App\User;
use stdClass;
use Exception;
use App\Rating;
use App\Product;
use App\CustomCss;
use App\Withdrawal;
use App\CustomField;
use App\Transaction;
use App\Notification;
use App\Subscription;
use App\CommissionLog;
use App\GeneralSetting;
use App\WithdrawMethod;
use App\CustomfieldItem;
use App\UserSubscription;
use App\Lib\StrongPassword;
use App\CustomFieldResponse;
use Illuminate\Http\Request;
use App\EmailTemplateSetting;
use App\Lib\GoogleAuthenticator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public $activeTemplate;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    // if ($request->is('api/*') && $request->signature) {
    //     $signature = $request->signature;
    //     $encId = getLastPartWithoutDots($signature);
    //     if ($encId == null) {
    //         return $this->respondWithError('Users invalid signature!');
    //     }

    //     $decryptedId = Crypt::decryptString($encId);
    //     $user = User::findOrFail($decryptedId);
    //     if(!$user) {
    //         return $this->respondWithError('unauthorized!');
    //     }
    //     auth()->login($user);

    //     $parts = explode('.', $signature);

    //     $signature = $parts[0];

    //     // dd($signature, $request->fullUrl(), $request->query('signature'));

    //     dd( $request->except('signature'));
    //     $parts = explode('.', $request->fullUrl());
    //     array_pop($parts);
    //     $newBaseUrl = implode('.', $parts);

    //     dd($newBaseUrl, $signature);

    //     $res = verifySOSignature($newBaseUrl, $signature);

    //     if (!$res) {
    //         return $this->respondWithError('not verfied!');
    //     } else {
    //         dd('verified');
    //     }
    // }
    public function home(Request $request, $signature = null)
    {
        $page_title = 'Dashboard';
        $user = auth()->user() ?? auth('user')->user();

        $uploadedProductCount = Product::where('user_id', $user->id)->where('status', 1)->count();
        $purchasedProductCount = Sell::where('user_id', $user->id)->where('status', 1)->count();
        $transactionCount = Transaction::where('user_id', $user->id)->count();
        $totalSell = Sell::where('author_id', $user->id)->where('status', 1)->count();

        $sell['month'] = collect([]);
        $sell['amount'] = collect([]);

        $sell_chart = Sell::whereYear('created_at', '=', date('Y'))->orderBy('created_at')->groupBy(DB::Raw("MONTH(created_at)"))->get();

        $sell_chart_data = $sell_chart->map(function ($query) use ($sell, $user) {
            $sell['month'] = $query->created_at->format('F');
            $sell['amount'] = $query->where('author_id', $user->id)->where('status', 1)->whereMonth('created_at', $query->created_at)->sum('product_price');
            return $sell;
        });

        $thisMonthRealeased = $user->products()->whereMonth('created_at', now())->where('status', 1)->count();
        $thisMonthPurchased = $user->buy()->whereMonth('created_at', now())->where('status', 1)->count();
        // if ($request->is('api/*') && !$request->token) {
        //     $data = [
        //         'redirect_url' => route('iframe.api.user.dashboard', ['token' => extractBearerToken($request->header('authorization'))]),
        //     ];
        //     return $this->respondWithSuccess($data, 'Dashboard page loaded!');
        // }

        if(($request->is('api/*') || $request->is('iframe/*')) && $request->token) {
            $partial = false;
        }

        return view($this->activeTemplate . 'user.dashboard', get_defined_vars());
    }

    public function profile()
    {
        $data['page_title'] = "Profile Setting";
        $data['user'] = Auth::user();
        return view($this->activeTemplate . 'user.profile-setting', $data);
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => "sometimes|required|max:80",
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => 'mimes:png,jpg,jpeg',
            'logoimage' => 'mimes:png',
            'cover_image' => 'mimes:png,jpg,jpeg',
        ], [
            'firstname.required' => 'First Name Field is required',
            'lastname.required' => 'Last Name Field is required',
        ]);

        $user = Auth::user();

        $in['firstname'] = $request->firstname;
        $user->firstname = $in['firstname'];
        $in['lastname'] = $request->lastname;
        $user->lastname = $in['lastname'];

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
            'country' => $user->address->country,
        ];
        $user->address = $in['address'];
        $in['description'] = $request->description;
        $user->description = $in['description'];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $in['image'] = uploadImage($image, $path, $size, $user->image);
            $user->image = $in['image'];
        }
        if ($request->hasFile('logoimage')) {
            $image = $request->file('logoimage');
            $path = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $in['logoimage'] = uploadImage($image, $path, $size, $user->company_logo);
            $user->company_logo = $in['logoimage'];
        }

        if ($request->hasFile('cover_image')) {
            try {
                $location = imagePath()['profile']['cover']['path'];
                $size = imagePath()['profile']['cover']['size'];
                $old = $user->cover_image;
                $coverImage = uploadImage($request->cover_image, $location, $size, $old);
                $in['cover_image'] = $coverImage;
                $user->cover_image = $in['cover_image'];
            } catch (\Exception $exp) {
                return back()->withNotify(['error', 'Could not upload the image.']);
            }
        };
        $user->save();
        $notify[] = ['success', 'Profile Updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $data['page_title'] = "CHANGE PASSWORD";
        return view($this->activeTemplate . 'user.password', $data);
    }

    public function submitPassword(Request $request)
    {

        $general = GeneralSetting::first();
        $password_validation = 'min:6';

        if ($general->secure_password) {
            $strongPassword = new StrongPassword(6, $request->all());
            $password_validation = $strongPassword->mixedCase()->letters()->numbers()->symbols();
        }

        if ($request->is('api/*')) {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->respondWithError($validator->errors()->first());
            }
        } else {
            $this->validate($request, [
                'current_password' => 'required',
                'password' => ['required', 'confirmed', $password_validation],
            ]);
        }

        try {
            $user = auth()->user();
            if ($request->is('api/*')) {
                $user = auth('user')->user();
            }
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();
                if ($request->is('api/*')) {
                    return $this->respondWithSuccess(null, 'Password has been updated successfully!');
                }
                $notify[] = ['success', 'Password Changes successfully.'];
                return back()->withNotify($notify);
            } else {
                if ($request->is('api/*')) {
                    return $this->respondWithError('Current password does not matched!');
                }
                $notify[] = ['error', 'Current password not match.'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /*
     * Deposit History
     */
    public function depositHistory(Request $request)
    {
        $page_title = 'Deposit History';
        $empty_message = 'No history found.';
        $user = auth()->user();
        if ($request->ajax()) {
            $data = $user->deposits()->where('order_number', null)->with(['gateway'])->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('method_code', function ($row) {
                    return $row->gateway->name;
                })
                ->editColumn('created_at', function ($row) {
                    return showDateTime($row->created_at);
                })
                ->editColumn('amount', function ($row) {
                    return getAmount($row->amount) . " " . currsym();
                })

                ->editColumn('status', function ($row) {
                    $rowdata = '';
                    if ($row->status == 1) {
                        $rowdata = '<span class="badge badge--success">Complete</span>';
                    } elseif ($row->status == 2) {
                        $rowdata = '<span class="badge badge--success">Pending</span>';
                    } elseif ($row->status == 3) {
                        $rowdata = ' <span class="badge badge--success">Cancelled</span>';
                    }
                    return $rowdata;
                })
                ->addColumn('action', function ($row) {
                    $general = GeneralSetting::first();
                    $btn = '<a href="javascript:void(0)" class="icon-btn bg--primary approveBtn"
                                                    data-bs-toggle="modal" data-bs-target="#approveModal"><i class="las la-desktop" data-bs-toggle="tooltip" data-bs-placement="top" title=Details></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'update_status'])
                ->make(true);
        }
        return view($this->activeTemplate . 'user.deposit_history', get_defined_vars());
    }

    /*
     * Withdraw Operation
     */

    public function withdrawMoney()
    {
        $data['withdrawMethod'] = WithdrawMethod::whereStatus(1)->get();
        $data['page_title'] = "Withdraw Money";
        return view(activeTemplate() . 'user.withdraw.methods', $data);
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric',
        ]);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $user = auth()->user();
        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your Requested Amount is Smaller Than Minimum Amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your Requested Amount is Larger Than Maximum Amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'Your do not have Sufficient Balance For Withdraw.'];
            return back()->withNotify($notify);
        }

        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = getAmount($afterCharge * $method->rate);

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id; // wallet method ID
        $withdraw->user_id = $user->id;
        $withdraw->amount = getAmount($request->amount);
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();
        session()->put('wtrx', $withdraw->trx);
        return redirect()->route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $data['withdraw'] = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', 0)->latest()->firstOrFail();
        $data['page_title'] = "Withdraw Preview";
        return view($this->activeTemplate . 'user.withdraw.preview', $data);
    }

    public function withdrawSubmit(Request $request)
    {
        $general = GeneralSetting::first();
        $withdraw = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', 0)->latest()->firstOrFail();

        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }
        $this->validate($request, $rules);
        $user = auth()->user();

        if (getAmount($withdraw->amount) > $user->balance) {
            $notify[] = ['error', 'Your Request Amount is Larger Then Your Current Balance.'];
            return back()->withNotify($notify);
        }

        $directory = date("Y") . "/" . date("m") . "/" . date("d");
        $path = imagePath()['verify']['withdraw']['path'] . '/' . $directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory . '/' . uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }

        $withdraw->status = 2;
        $withdraw->save();
        $user->balance -= $withdraw->amount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = getAmount($withdraw->amount);
        $transaction->post_balance = getAmount($user->balance);
        $transaction->charge = getAmount($withdraw->charge);
        $transaction->trx_type = '-';
        $transaction->details = getAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx = $withdraw->trx;
        $transaction->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => getAmount($withdraw->final_amount),
            'amount' => getAmount($withdraw->amount),
            'charge' => getAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => getAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => getAmount($user->balance),
            'delay' => $withdraw->method->delay,
        ]);

        $notify[] = ['success', 'Withdraw Request Successfully Send'];
        return redirect()->route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
    {
        $data['page_title'] = "Withdraw Log";
        $data['withdraws'] = Withdrawal::where('user_id', Auth::id())->where('status', '!=', 0)->with('method')->latest()->paginate(getPaginate());
        $data['empty_message'] = "No Data Found!";
        return view($this->activeTemplate . 'user.withdraw.log', $data);
    }

    public function show2faForm()
    {
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();

        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $secret);

        $prevcode = $user->tsc;

        $prevqr = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $prevcode);
        $page_title = 'Two Factor';
        return view($this->activeTemplate . 'user.twofactor', compact('page_title', 'secret', 'qrCodeUrl', 'prevcode', 'prevqr'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);

        if ($oneCode === $request->code) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->tv = 1;
            $user->save();

            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time'],
            ]);

            $notify[] = ['success', 'Google Authenticator Enabled Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {

            $user->tsc = null;
            $user->ts = 0;
            $user->tv = 1;
            $user->save();

            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time'],
            ]);

            $notify[] = ['success', 'Two Factor Authenticator Disable Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }
    }

    public function purchasedProduct(Request $request)
    {
        $page_title = "Purchased Products";
        // $data= Sell::where('user_id', auth()->user()->id)->with('product', 'productcustomfields', 'customfieldresponse', 'bumpresponses')->get();
        // dd($data);
        $empty_message = 'No data found';
        if ($request->ajax()) {
            if ($request->nid == '') {
                $data = Sell::where('user_id', auth()->user()->id)->with('product', 'productcustomfields', 'customfieldresponse', 'bumpresponses');
            } else {
                $data = Sell::where('id', $request->nid)->where('user_id', auth()->user()->id)->with('product', 'productcustomfields', 'customfieldresponse', 'bumpresponses');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return showDateTime($row->created_at);
                })
                ->editColumn('product_id', function ($row) {
                    return $row->product->name;
                })
                ->editColumn('support_time', function ($row) {
                    $rowdata = '';
                    if ($row->support_time) {
                        $rowdata = '<b>' . $row->support_time . '</b>';
                    } else {
                        $rowdata = '<b>NoSupport</b>';
                    }
                    return $rowdata;
                })
                ->editColumn('support', function ($row) {
                    if ($row->support == 1) {
                        return '<span class="badge badge--success">Yes</span>';
                    } elseif ($row->support == 0) {
                        return '<span class="badge badge--danger">No</span>';
                    }
                })

                ->editColumn('bump_fee', function ($row) {
                    $btn = getAmount($row->bump_fee) . ' ' . currsym() . '';
                    if ($row->bump_fee != 0) {
                        $btn .= '<span class="badge badge--danger viewdetails' . $row->id . '">Extras</span>';
                    }
                    return  $btn;
                })
                ->editColumn('status', function ($row) {
                    $rowdata = '';
                    if ($row->status == 1) {
                        $rowdata = '<span class="badge badge--success">Purchased</span>';
                    } elseif ($row->status == 2) {
                        $rowdata = '<span class="badge badge--danger">Rejected</span>';
                    } elseif ($row->status == 0) {
                        $rowdata = '<span class="badge badge--Warning">Pending</span>';
                    }
                    return $rowdata;
                })
                ->addColumn('additionalinfo', function ($row) {
                    //if (is_null($row->product->file) || $row->product->file='') condition to be changed
                    if ($row->productcustomfields->count() > 0) {
                        return '<span class="badge badge--danger">Yes</span>';
                    } else {
                        return '<span class="badge badge--success">No</span>';
                    }
                })
                ->addColumn('createticket', function ($row) {
                    $ticket = '<a href="' . route('ticket.open', $row->product_id) . '"><i
                                class="las la-headset fs-5 me-2"></i>Ticket Support</a>';
                    return $ticket;
                })

                ->addColumn('action', function ($row) {

                    if ($row->status == 1) {

                        $statusdata  = '<a href="' . route('user.download', Crypt::encrypt($row->product->id)) . '"
                                                        class="icon-btn bg--primary download-file"><i
                                                            class="las la-download" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Download"></i></a>
                                                    <a href="' . route('user.invoice', Crypt::encrypt($row->product->id)) . '"
                                                        class="icon-btn bg--primary"><i class="las la-receipt"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Invoice"></i></a>';

                        if (!auth()->user()->existedRating($row->product->id)) {
                            $statusdata .= ' <a href="javascript:void(0)" data-bs-toggle="modal"
                                                            data-bs-target="#reviewModal' . $row->id . '"
                                                            class="icon-btn bg--primary reviewBtn"><i
                                                                class="las la-star-of-david" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="Give Review"></i></a>';
                        }
                        if ($row->productcustomfields->count() > 0) {
                            $statusdata .= ' <a href="javascript:void(0)" data-bs-toggle="modal"
                                                            data-bs-target="#addcustomfieldmodal' . $row->id . '"
                                                            class="icon-btn bg--primary"><i
                                                                class="editfieldresponse' . $row->id . ' las la-edit"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Please Fill the CustomField"></i></a>';
                        }
                    } elseif ($row->status == 2) {
                        $statusdata = ' <a href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#messageModal' . $row->id . '"
                                                        class="icon-btn bg--primary"><i class="las la-info-circle"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Message"></i></a>';
                    }
                    if (!is_null($row->product->shareable_link)) {

                        $statusdata  .= ' <a href="' . route('user.copyslink', Crypt::encrypt($row->product->id)) . '"
                                                        class="icon-btn bg--primary download-file"><i
                                                            class="las la-copy" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="CopyLink"></i></a>';
                    }
                    return $statusdata;
                })
                ->rawColumns(['action', 'additionalinfo', 'bump_fee', 'support', 'support_time', 'status', 'createticket'])
                ->make(true);
        }
        return view($this->activeTemplate . 'user.product.purchased', get_defined_vars());
    }

    public function rating(Request $request)
    {

        $request->validate([
            'rating' => 'required|integer|gt:0|max:5',
            'product_id' => 'required|integer|gt:0',
            'review' => 'required',
        ]);

        $product = Sell::where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->where('status', 1)->first();
        $user = auth()->user();

        if ($product == null) {
            $notify[] = ['error', 'Something went wrong'];
            return back()->withNotify($notify);
        }

        $rating = new Rating();
        $rating->product_id = $request->product_id;
        $rating->user_id = $user->id;
        $rating->rating = $request->rating;
        $rating->review = $request->review;
        $rating->save();

        $totalRatingProduct = $product->product->total_rating + $request->rating;
        $totalResponseProduct = $product->product->total_response + 1;
        $avgRatingProduct = round($totalRatingProduct / $totalResponseProduct);

        $product->product->total_rating = $totalRatingProduct;
        $product->product->total_response = $totalResponseProduct;
        $product->product->avg_rating = $avgRatingProduct;
        $product->product->save();

        $totalRatingAuthor = $product->product->user->total_rating + $request->rating;
        $totalResponseAthor = $product->product->user->total_response + 1;
        $avgRatingAuthor = round($totalRatingAuthor / $totalResponseAthor);

        $product->product->user->total_rating = $totalRatingAuthor;
        $product->product->user->total_response = $totalResponseAthor;
        $product->product->user->avg_rating = $avgRatingAuthor;
        $product->product->user->save();

        $notify[] = ['success', 'Thanks for your review'];
        return back()->withNotify($notify);
    }
    public function copyShareableLink($id)
    {
        $product = Product::findOrFail(Crypt::decrypt($id));
        $productCheck = Sell::where('product_id', $product->id)->where('user_id', auth()->user()->id)->where('status', 1)->first();

        if ($productCheck == null) {
            $notify[] = ['error', 'You are not allowed to download this'];
            return back()->withNotify($notify);
        }

        $file = $product->shareable_link;
        if (!is_null($file)) {
            session()->put('copytext', $file);
            $notify[] = ['success', 'Shareable link of the Product is successfully Copied'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'No shear able link is available here'];
            return back()->withNotify($notify);
        }
    }

    public function download($id)
    {
        $product = Product::findOrFail(Crypt::decrypt($id));
        $productCheck = Sell::where('product_id', $product->id)->where('user_id', auth()->user()->id)->where('status', 1)->first();

        if ($productCheck == null) {
            $notify[] = ['error', 'You are not allowed to download this'];
            return back()->withNotify($notify);
        }

        $file = $product->file;

        if (is_null($file)) {
            $notify[] = ['error', 'Author Did not Provide the File yet'];
            return back()->withNotify($notify);
        } else {
            if (Str::contains($file, 'http')) {
                session()->put('copytext', $file);
                $notify[] = ['success', 'Source Code of the Product is successfully Copied'];
                return back()->withNotify($notify);
            } else {
                $full_path = 'assets/product/' . $file;
                $title = str_replace(' ', '_', strtolower($product->name));
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $mimetype = mime_content_type($full_path);
                header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
                header("Content-Type: " . $mimetype);
                return readfile($full_path);
            }
        }
    }

    public function invoice($id)
    {
        $page_title = 'Invoice';
        $product = Product::findOrFail(Crypt::decrypt($id));
        $productCheck = Sell::where('product_id', $product->id)->where('user_id', auth()->user()->id)->where('status', 1)->first();

        if ($productCheck == null) {
            $notify[] = ['error', 'You are not allowed to download invoice'];
            return back()->withNotify($notify);
        }

        $filename = strtolower(str_replace(' ', '_', $productCheck->product->name));
        return view($this->activeTemplate . 'user.product.invoice', compact('page_title', 'productCheck', 'filename'));
    }

    public function transaction(Request $request)
    {
        $page_title = 'Transaction Logs';
        // $transactions = Transaction::where('user_id', Auth::id())->orderBy('id', 'desc');
        $empty_message = 'No transactions.';
        if ($request->ajax()) {
            $data = Transaction::where('user_id', Auth::id())->orderBy('id', 'desc')->latest();
            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('created_at', function ($row) {
                    return showDateTime($row->created_at);
                })
                ->editColumn('amount', function ($row) {

                    if ($row->trx_type == '+') {
                        return  '<strong class="text--primary">+' . (getAmount($row->amount)) . ' ' . currtext() . '</strong>';
                    } else {
                        return
                            '<strong class="text--danger">-' . getAmount($row->amount) . ' ' . currtext() . '</strong>';
                    }
                })
                ->editColumn('charge', function ($row) {

                    return getAmount($row->amount) . "" . currsym();
                })
                ->editColumn('post_balance', function ($row) {

                    return getAmount($row->post_balance) . "" . currsym();
                })
                ->rawColumns(['amount'])
                ->make(true);
        }
        return view($this->activeTemplate . 'user.transaction', get_defined_vars());
    }

    public function sellLog(Request $request)
    {
        $page_title = 'Sell Logs';
        $empty_message = 'No data found.';
        if ($request->ajax()) {
            $data = Sell::where('author_id', Auth::id())->where('status', 1)->with('product', 'productcustomfields', 'customfieldresponse', 'bumpresponses');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return showDateTime($row->created_at);
                })
                ->editColumn('product_id', function ($row) {
                    return $row->product->name;
                })
                ->editColumn('license', function ($row) {
                    $rowdata = '';
                    if ($row->license == 1) {
                        $rowdata = '<b>Regular</b>';
                    } elseif ($row->license == 2) {
                        $rowdata = '<b>Extende</b>';
                    }
                    return $rowdata;
                })
                ->editColumn('support_time', function ($row) {
                    $rowdata = '';
                    if ($row->support_time) {
                        $rowdata = '<b>' . $row->support_time . '</b>';
                    } else {
                        $rowdata = '<b>NoSupport</b>';
                    }
                    return $rowdata;
                })
                ->editColumn('product_price', function ($row) {

                    return getAmount($row->product_price) . "" . currsym();
                })
                ->editColumn('support_fee', function ($row) {

                    return getAmount($row->support_fee) . "" . currsym();
                })
                ->editColumn('total_price', function ($row) {

                    return getAmount($row->total_price) . "" . currsym();
                })
                ->editColumn('bump_fee', function ($row) {
                    $btn = getAmount($row->bump_fee) . ' ' . currsym() . '';
                    if ($row->bump_fee != 0) {
                        $btn .= '<span class="badge badge--danger viewdetails' . $row . '">' . __('Extras') . '</span>';
                    }
                    return  $btn;
                })
                ->addColumn('source_required', function ($row) {
                    if ($row->productcustomfields->count() > 0) {
                        return '<span class="badge badge--danger">Yes</span>';
                    } else {
                        return '<span class="badge badge--success">No</span>';
                    }
                })

                ->addColumn('action', function ($row) {
                    if ($row->productcustomfields->count() > 0) {

                        return  '<a href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#addsourcemodal' . $row->id . '"
                                                        class="relative icon-btn bg--danger reviewBtn"
                                                        ><i
                                                            class=" editfieldresponse' . $row->id . ' las la-edit" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Please provide the Product Source Data"></i></a>';
                    }
                })
                ->rawColumns(['action', 'source_required', 'bump_fee', 'support_time', 'license'])
                ->make(true);
        }
        return view($this->activeTemplate . 'user.sell_log', get_defined_vars());
    }

    public function trackSell()
    {
        $page_title = 'Track Sells';
        $result = null;
        return view($this->activeTemplate . 'user.track_sell', compact('page_title', 'result'));
    }

    public function trackSellSearch(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $result = Sell::where('code', $request->code)->where('author_id', auth()->user()->id)->where('status', 1)->first();

        if ($result) {
            $page_title = 'Track Sells Seach';
            return view($this->activeTemplate . 'user.track_sell', compact('page_title', 'result'));
        } else {
            $notify[] = ['error', 'No result found with this code'];
            return redirect()->route('user.track.sell')->withNotify($notify);
        }
    }

    public function emailAuthor(Request $request)
    {
        $request->validate([
            'author' => 'required',
            'message' => 'required',
        ]);

        $author = User::where('status', 1)->where('username', $request->author)->firstOrFail();

        notify($author, 'MAIL_TO_ATHOR', [
            'reply_to' => auth()->user()->email,
            'message' => $request->message,
        ]);

        $notify[] = ['success', 'You have successfully sent your message.'];
        return back()->withNotify($notify);
    }

    public function referredUsers()
    {
        $page_title = "My referred Users";
        $empty_message = "No referee found";
        $referees = User::where('ref_by', auth()->id())->paginate(getPaginate());

        return view($this->activeTemplate . 'user.referral.index', compact('page_title', 'empty_message', 'referees'));
    }

    public function commissionLogs()
    {
        $page_title = "Referral Commissions";
        $logs = CommissionLog::where('type', 'deposit_commission')->where('to_id', Auth::id())->with('user', 'bywho')->latest()->paginate(getPaginate());
        $empty_message = "No commission yet";
        return view($this->activeTemplate . 'user.referral.logs', compact('page_title', 'logs', 'empty_message'));
    }
    public function newCustomfield()
    {
        $page_title = 'New CustomField';
        return view($this->activeTemplate . 'user.customfield.new', compact('page_title'));
    }
    public function deleteCustomfield(Request $request)
    {
        $request->validate([
            'customfield_id' => 'required',
        ]);
        $customfield = CustomField::findOrFail($request->customfield_id);
        $customfield->delete();

        $notify[] = ['success', 'CustomField successfully deleted'];
        return back()->withNotify($notify);
    }
    public function getacustomfield(Request $request)
    {

        $customfield = CustomField::where('user_id', auth()->user()->id)->with('customfielditem')->get();
        return response()->json(['success ' => 'CustomField successfully deleted', 'customfield ' => $customfield]);
    }
    public function allCustomfield(Request $request)
    {
        $page_title = 'All CustomField';
        $empty_message = 'No data found';
        // $plans = UserSubscription::where('user_id', auth()->user()->id)->with('subscriptions')->first();
        // if (is_null($plans)) {
        //     $plans = Subscription::where('id', 1)->first();
        //     if ($plans->cf_status == 0) {
        //         $warning = 1;
        //     }
        // } else {
        //     if ($plans->subscriptions->cf_status == 0) {
        //         $warning = 1;
        //     }
        // }
        if ($request->ajax()) {
            $data = CustomField::where('user_id', auth()->user()->id)->with('customfielditem');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return  strtoupper($row->type);
                })
                ->editColumn('fieldoption', function ($row) {
                    if ($row->fieldoption == null) {
                        return '<span class="badge badge--info">RegularField</span>';
                    }
                    if ($row->fieldoption == 0) {
                        return '<span class="badge badge--primary">Single</span>';
                    }
                    if ($row->fieldoption == 1) {
                        return '<span class="badge badge--primary">Multipleoption</span>';
                    }
                })
                ->editColumn('status', function ($row) {
                    $rowdata = '';
                    if ($row->status == 1) {
                        $rowdata = '<span class="badge badge--success">Active</span>';
                    } elseif ($row->status == 0) {
                        $rowdata = ' <span class="badge badge--danger">Disabled</span>';
                    }
                    return $rowdata;
                })

                ->addColumn('labels', function ($row) {
                    $rowdata = [];
                    if ($row->fieldoption != null) {
                        foreach ($row->customfielditem as $cfitem) {
                            $rowdata[] = $cfitem->label;
                        }
                    } else {
                        $rowdata[] = $row->name;
                    }
                    return $rowdata;
                })
                ->addColumn('action', function ($row) {
                    $general = GeneralSetting::first();
                    $btn = '<a href="' . route('user.customfield.edit', $row->id) . '"
                                                        class="icon-btn bg--primary"><i class="las la-edit"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Update"></i></a>
                                                    <a href="javascript:void(0)" class="icon-btn bg--danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '"><i
                                                            class="lar la-trash-alt" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Delete"></i></a>
                                                    <a href="javascript:void(0)" class="icon-btn bg--primary"
                                                        data-bs-toggle="modal" data-bs-target="#approveModal' . $row->id . '"><i
                                                            class="las la-desktop" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Details"></i></a>';

                    if ($row->status == 0) {
                        $btn .= '<a href="javascript:void(0)" class="icon-btn bg-danger activateBtn"
                                                            data-bs-toggle="modal" data-bs-target="#activateModal' . $row->id . '"
                                                            data-original-title="Enable"><i
                                                                class="la la-eye"></i></a>';
                    } elseif ($row->status == 1) {
                        $btn .= '<a href="javascript:void(0)"
                                                            class="icon-btn bg-success deactivateBtn" data-bs-toggle="modal"
                                                            data-bs-target="#deactivateModal' . $row->id . '"
                                                            data-original-title="Disable"><i
                                                                class="la la-eye"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'fieldoption', 'labels'])
                ->make(true);
        }
        return view($this->activeTemplate . 'user.customfield.index', get_defined_vars());
    }
    public function storeCustomfield(Request $request)
    {
        $validation_rule = [
            'name' => 'required|max:191',
            'type' => 'required',
        ];
        $customfield = CustomField::where('name', 'like', '%' . $request->name . '%')->where('user_id', auth()->user()->id)->first();
        if ($customfield) {
            $notify[] = ['error', 'Something goes wrong.'];
            return back()->withNotify($notify);
        } else {
            $createcf = new CustomField;
            $createcf->name = $request->name;
            $createcf->type = $request->type;
            $createcf->fieldoption = $request->fieldoptions;
            if (!is_null($request->placeholder)) {
                $createcf->placeholder = $request->placeholder;
            }
            $createcf->status = 1;
            $createcf->user_id = auth()->user()->id;
            $createcf->save();
            if ($request->fieldoptions != null) {
                foreach ($request->label as $labels) {
                    $new = new CustomfieldItem;
                    $new->label = $labels;
                    $new->customfield_id = $createcf->id;
                    $new->save();
                }
            }
        }
        $notify[] = ['success', 'Custom Field successfully submitted'];
        return redirect()->route('user.allCustomfield')->withNotify($notify);
    }

    public function activate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $customfield = CustomField::findOrFail($request->id);
        $customfield->status = 1;
        $customfield->save();
        $notify[] = ['success', $customfield->name . ' has been activated'];
        return back()->withNotify($notify);
    }

    public function deactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $customfield = CustomField::findOrFail($request->id);
        $customfield->status = 0;
        $customfield->save();

        $notify[] = ['success', $customfield->name . ' has been disabled'];
        return back()->withNotify($notify);
    }
    public function editCustomfield($id)
    {

        $customfields = CustomField::where('id', $id)->where('user_id', auth()->user()->id)->with('customfielditem')->first();
        $page_title = 'Edit  CustomField';
        return view($this->activeTemplate . 'user.customfield.edit', get_defined_vars());
    }
    public function updateCustomField(Request $req, $id)
    {
        $customfield = CustomField::where('id', $id)->where('user_id', auth()->user()->id)->first();
        $cfitems = CustomfieldItem::where('customfield_id', $id)->get();
        if (count($cfitems) > 0) {
            foreach ($cfitems as $cfitem) {
                $cfitem->delete();
            }
        }
        $customfield->name = $req->name;
        $customfield->type = $req->type;
        $customfield->fieldoption = $req->fieldoptions;
        if (!is_null($req->placeholder)) {
            $customfield->placeholder = $req->placeholder;
        }
        $customfield->user_id = auth()->user()->id;
        $customfield->save();
        if ($req->fieldoptions != null) {
            foreach ($req->label as $labels) {
                $new = new CustomfieldItem;
                $new->label = $labels;
                $new->customfield_id = $customfield->id;
                $new->save();
            }
        }
        $notify[] = ['success', $customfield->name . ' has been Updated'];
        return back()->withNotify($notify);
    }
    public function lateaddcustomfield(Request $request)
    {
        $colorobject = new stdClass;
        if ($request->field_label) {
            $fieldid = [];
            foreach ($request->field_label as $key => $value) {
                $cflabels = CustomFieldItem::where('id', $key)->first();
                $label = $cflabels->label;
                $colorobject->$label = $value;
                $fieldid[$cflabels->customfield_id] = $colorobject;
            }
        } else {
            $fieldid = $request->field_id;
        }
        foreach ($fieldid as $key => $value) {
            $customfield = CustomFieldResponse::where('sell_id', $request->sell_id)
                ->where('customfield_id', $key)
                ->first();
            if (is_null($customfield)) {
                $customfield = new CustomFieldResponse;
            }
            if ($request->field_label) {
                $customfield->field_value = @json_encode($colorobject);
            } else {
                $customfield->field_value = $value;
            }
            $customfield->sell_id = $request->sell_id;
            $customfield->customfield_id = $key;
            $customfield->save();
        }

        $sell = Sell::findorFail($request->sell_id);
        if ($sell) {
            $sell->update([
                'request_by' => 0,
                'approve_edit' => 0,
            ]);
        }

        $notify[] = ['success', 'Customfield has been Updated'];
        return back()->withNotify($notify);
    }

    public function addproductsource(Request $req)
    {
        $general = GeneralSetting::first();
        $pFile = '';

        if ($req->hasFile('file')) {

            $disk = $general->server;
            $date = date('Y') . '/' . date('m') . '/' . date('d');

            if ($disk == 'current') {
                try {
                    $location = imagePath()['p_file']['path'];
                    $pFile = str_replace(' ', '_', strtolower($req->name)) . '_' . uniqid() . time() . '.zip';
                    $req->file->move($location, $pFile);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload the file'];
                    return back()->withNotify($notify);
                }
                $server = 0;
            } else {
                try {
                    $fileExtension = $req->file('file')->getClientOriginalExtension();
                    $file = File::get($req->file);
                    $location = 'FILES/' . $date;

                    $responseValue = uploadRemoteFile($file, $location, $fileExtension, $disk);

                    if ($responseValue[0] == 'error') {
                        return response()->json(['errors' => $responseValue[1]]);
                    } else {
                        $pFile = $responseValue[1];
                    }
                } catch (Exception $e) {
                    return response()->json(['errors' => 'Could not upload the Video']);
                }
                $server = 1;
            }
        }
        $product = Product::findOrFail($req->product_id);
        if (empty($pFile)) {

            $product->update([
                'file' => $req->sourcelink,
            ]);
        }
        $notify[] = ['success', 'Product Source has been Updated'];
        return back()->withNotify($notify);
    }
    public function requesteditbybuyer($id)
    {
        $sell = Sell::where('id', $id)->first();
        if ($sell) {
            $sell->update([
                'request_by' => 1,
            ]);
        }
        $notification = new Notification;
        $notification->user_id = $sell->author_id;
        $notification->cf_status = 1;
        $notification->meeting_status = 0;
        $notification->sell_id = $sell->id;
        $notification->product_id = $sell->product_id;
        $notification->save();
        $msg = "Your Request has been sent";

        return response()->json($msg);
    }
    public function allowedit($id)
    {
        $sell = Sell::findOrFail($id);
        if ($sell) {
            $sell->update([
                'request_by' => 0,
                'approve_edit' => 1,
            ]);
            $notification = new Notification;
            $notification->user_id = $sell->user_id;
            $notification->cf_status = 2;
            $notification->meeting_status = 0;
            $notification->sell_id = $sell->id;
            $notification->product_id = $sell->product_id;
            $notification->save();
        }

        $msg = "Approval Sent";

        return response()->json($msg);
    }
    public function editrequestbyseller($id)
    {
        $sell = Sell::findOrFail($id);
        if ($sell) {
            $sell->update([
                'request_by' => 0,
                'approve_edit' => 1,
            ]);
            $notification = new Notification;
            $notification->user_id = $sell->user_id;
            $notification->cf_status = 3;
            $notification->meeting_status = 0;
            $notification->sell_id = $sell->id;
            $notification->product_id = $sell->product_id;
            $notification->save();
        }

        $msg = "Request for the changes to buyer has been Sent";

        return response()->json($msg);
    }
    public function allEmailTemplate(Request $request)
    {

        $page_title = 'All Email Templates';
        $empty_message = 'No data found';
        $plans = UserSubscription::where('user_id', auth()->user()->id)->with('subscriptions')->first();
        // if (is_null($plans)) {
        //     $plans = Subscription::where('id', 1)->first();
        //     if ($plans->cf_status == 0) {
        //         $warning = 1;
        //     }
        // } else {
        //     if ($plans->subscriptions->cf_status == 0) {
        //         $warning = 1;
        //     }
        // }
        if ($request->ajax()) {
            $data = EmailTemplateSetting::where('user_id', auth()->user()->id);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn(
                    'status',
                    function ($row) {
                        $rowdata = '';
                        if ($row->status == 1) {
                            $rowdata = '<span class="badge badge--success">Active</span>';
                        } elseif ($row->status == 0) {
                            $rowdata = ' <span class="badge badge--danger">Disabled</span>';
                        }
                        return $rowdata;
                    }
                )
                ->addColumn('action', function ($row) {
                    $general = GeneralSetting::first();
                    $btn = '<a href="' . route('user.emailtemplate.edit', $row->id) . '"
                                                        class="icon-btn bg--primary"><i class="las la-edit"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Update"></i></a>
                                                    <a href="javascript:void(0)" class="icon-btn bg--danger"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal' . $row->id . '"><i
                                                            class="lar la-trash-alt" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Delete"></i></a>';

                    if ($row->status == 0) {
                        $btn .= `<a href="javascript:void(0)" class="icon-btn bg-danger activateBtn"
                                                            data-bs-toggle="modal" data-bs-target="#activateModal'.$row->id.'"
                                                            data-original-title="Enable"><i
                                                                class="la la-eye"></i></a>`;
                    } else {
                        $btn .= '<a href="javascript:void(0)"
                                                            class="icon-btn bg-success deactivateBtn" data-bs-toggle="modal"
                                                            data-bs-target="#deactivateModal' . $row->id . '"
                                                            data-original-title="Disable"><i
                                                                class="la la-eye"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }


        return view($this->activeTemplate . 'user.emailtemplate.index', get_defined_vars());
    }
    public function newEmailTemplate()
    {
        $page_title = 'New Email Template';
        return view($this->activeTemplate . 'user.emailtemplate.new', compact('page_title'));
    }
    public function storeEmailtemplate(Request $request)
    {

        $user = Auth()->user();
        if ($request->hasFile('logoimage')) {
            $request->validate([
                'logoimage' => 'mimes:png',
            ]);
            $image = $request->file('logoimage');
            $path = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $in['logoimage'] = uploadImage($image, $path, $size, $user->company_logo);
            $user->company_logo = $in['logoimage'];
        }
        $shortcode = new stdClass;
        foreach ($request->codekey as $key => $value) {
            $shortcode->$value = $request->codedetail[$key];
        }
        $emailadd = new EmailTemplateSetting;
        $emailadd->name = $request->templatename;
        $emailadd->shortcodes = json_encode($shortcode);
        $emailadd->email_body = '<li><b>ItemName: </b>{{ itemname }}</li>
<ul>
<li><b>Item Price: </b>{{ itemprice }}</li>
<li><b>Bump Fee: </b>{{ bumpfee }}</li>
<li><b>Support: </b>{{ support }}</li>
<li><b>Support Fee: </b>{{ supportfee }}</li>
<li><b>Total Price: </b>{{ totalprice }}</li>
</ul>';
        $emailadd->email_template = $request->emailtemplate;
        $emailadd->status = 1;
        $emailadd->user_id = auth()->user()->id;
        $emailadd->save();

        $notify[] = ['success', 'Email Template  successfully submitted'];
        return redirect()->route('user.emailtemplate')->withNotify($notify);
    }
    public function editEmailTemplate($id)
    {

        $emailtemplate = EmailTemplateSetting::where('id', $id)->where('user_id', auth()->user()->id)->first();
        $page_title = 'Edit  Email Template';
        return view($this->activeTemplate . 'user.emailtemplate.edit', get_defined_vars());
    }
    public function updateEmailTemplate(Request $request, $id)
    {
        $user = Auth()->user();
        if ($request->hasFile('logoimage')) {
            $request->validate([
                'logoimage' => 'mimes:png',
            ]);
            $image = $request->file('logoimage');
            $path = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $in['logoimage'] = uploadImage($image, $path, $size, $user->company_logo);
            $user->company_logo = $in['logoimage'];
        }
        $shortcode = new stdClass;
        foreach ($request->codekey as $key => $value) {
            $shortcode->$value = $request->codedetail[$key];
        }
        $emailadd = EmailTemplateSetting::where('id', $id)->first();
        $emailadd->name = $request->templatename;
        $emailadd->shortcodes = json_encode($shortcode);
        $emailadd->email_body = '
<li><b>ItemName: </b>{{ itemname }}</li>
<ul>
<li><b>Item Price: </b>{{ itemprice }}</li>
<li><b>Bump Fee: </b>{{ bumpfee }}</li>
<li><b>Support: </b>{{ support }}</li>
<li><b>Support Fee: </b>{{ supportfee }}</li>
<li><b>Total Price: </b>{{ totalprice }}</li>
</ul>';
        $emailadd->email_template = $request->emailtemplate;
        $emailadd->status = 1;
        $emailadd->user_id = auth()->user()->id;
        $emailadd->save();

        $notify[] = ['success', $emailadd->name . ' has been Updated'];
        return redirect()->route('user.emailtemplate')->withNotify($notify);
    }
    public function deleteEmailTemplate(Request $request)
    {
        $emailtemplate = EmailTemplateSetting::findOrFail($request->emailtemplate_id);
        $emailtemplate->delete();

        $notify[] = ['success', 'Template is  successfully deleted'];
        return back()->withNotify($notify);
    }
    public function templateactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $emailtemplate = EmailTemplateSetting::findOrFail($request->id);
        $emailtemplate->status = 1;
        $emailtemplate->save();
        $notify[] = ['success', $emailtemplate->name . ' has been activated'];
        return back()->withNotify($notify);
    }

    public function templatedeactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $emailtemplate = EmailTemplateSetting::findOrFail($request->id);
        $emailtemplate->status = 0;
        $emailtemplate->save();

        $notify[] = ['success', $emailtemplate->name . ' has been disabled'];
        return back()->withNotify($notify);
    }
    public function getCustomCss()
    {
        $newcs = CustomCss::where('user_id', auth()->user()->id)->first();
        $page_title = 'Do Custom Css';
        $empty_message = 'No data found';

        return view($this->activeTemplate . 'user.customcss.new', get_defined_vars());
    }
    public function updateCustomCss(Request $req)
    {
        $newcs = CustomCss::where('user_id', auth()->user()->id)->first();
        if (is_null($newcs)) {
            $newcs = new CustomCss;
        }
        $newcs->styletag = $req->customcss;
        $newcs->user_id = auth()->user()->id;
        $newcs->save();
        $notify[] = ['success', 'Custom css has been updated'];
        return back()->withNotify($notify);
    }
}
