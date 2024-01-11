<?php

namespace App\Http\Controllers;

use App\{
    Category,
    Comment,
    GeneralSetting,
    Product,
    Reply,
    SubCategory,
    OtherCategory,
    OtherCategoryProduct,
    ProductCustomField,
    CustomField,
    TempProduct,
    ProductBump,
    Subscription,
    UserSubscription
};
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use File;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public $activeTemplate;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function allProduct(Request $request)
    {

        $page_title = 'All Products';
        $empty_message = 'No data found';
        $allowedplan = UserSubscription::where('user_id', auth()->user()->id)->where('status', 1)->with('subscriptions')->first();

        $productscount = Product::where('status', '!=', 4)->where('user_id', auth()->user()->id)->with(['category', 'subcategory'])->count();
        if (!is_null($allowedplan)) {
            if ($allowedplan->subscriptions->allowed_product <= $productscount) {
                $warning = 1;
            } else {
                $warning = 0;
            }
            $limit = $allowedplan->subscriptions->allowed_product;
        } else {
            $allowedplan = Subscription::where("id", 1)->first();
            if ($allowedplan->allowed_product <= $productscount) {
                $warning = 1;
            } else {
                $warning = 0;
            }
            $limit = $allowedplan->allowed_product;
        }

        $token = '';
        $api = false;
        if ($request->is('api/*')) {
            $token = $request->token;
            $api = true;
            $partial = false;
        }

        if ($request->ajax()) {
            $data = Product::with(['category', 'subcategory'])->whereUserId(auth()->id())->whereRaw("status !=  4")->latest()->take($limit)->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('encrupted_id', function ($row) {
                    return Crypt::encrypt($row->id);
                })
                ->editColumn('category_id', function ($row) {
                    return $row->category->name;
                })
                ->editColumn('sub_category_id', function ($row) {
                    return $row->subcategory->name;
                })
                ->editColumn('status', function ($row) {
                    $rowdata = '';
                    if ($row->status == 0) {
                        $rowdata = 'pending';
                    } elseif ($row->status == 1) {
                        $rowdata = '<span class="badge badge--success">Approved</span>';
                    } elseif ($row->status == 2) {
                        $rowdata = ' <span class="badge badge--warning">Soft Reject</span> <a
                                                        href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#softMessageModal"
                                                        data-id=$row->id><span><i
                                                                class="fas fa-info-circle"></i></span></a>';
                    } elseif ($row->status == 3) {
                        $rowdata = '<span class="badge badge--danger">Hard Reject</span> <a
                                                        href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#hardMessageModal$row->id"
                                                        data-id=$row->id><span><i
                                                                class="fas fa-info-circle"></i></span></a>';
                    } elseif ($row->status == 5) {
                        $rowdata = '<span class="badge badge--warning">Resubmitted</span>';
                    }
                    return $rowdata;
                })
                ->editColumn('update_status', function ($row) {
                    if ($row->status == 1 && $row->update_status == 1) {
                        return '<span class="badge badge--warning">Pending</span>';
                    } elseif ($row->status == 1 && $row->update_status == 2) {
                        return '<span class="badge badge--success">Approved</span>';
                    } elseif ($row->status == 1 && $row->update_status == 3) {
                        return ' <span class="badge badge--warning">Soft Reject</span> <a
                                                        href="javascript:void(0)" data-bs-toggle="modal"
                                                        data-bs-target="#softMessageModal"
                                                        data-id=$row->id><span><i
                                                                class="fas fa-info-circle"></i></span></a>';
                    } else {
                        return '<b>N/A</b>';
                    }
                })

                ->addColumn('action', function ($row) use ($request) {
                    $btn = '';
                    if ($row->status == 1 && ($row->update_status == 0 || $row->update_status == 2 || $row->update_status == 3)) {
                        $url = ($request->api == true) ? route('iframe.api.product.edit', Crypt::encrypt($row->id)).'?token='.$request->token : route('user.product.edit', Crypt::encrypt($row->id));

                        $btn = '<a href="' . $url . '"class="icon-btn bg--primary"><i class="las la-edit" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Update"></i></a>
                                            <a href="javascript:void(0)" class="icon-btn bg--danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"><i
                                                    class="lar la-trash-alt" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Delete"></i></a>';
                    }

                    if ($row->status == 2) {
                        $btn = '<a href="' . route('user.product.resubmit', Crypt::encrypt($row->id)) . '"
                                                class="bg--primary text-white p-1 rounded">Resubmit</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status', 'update_status'])
                ->make(true);
        }


        // $products = ->paginate(getPaginate());
        return view($this->activeTemplate . 'user.product.index', get_defined_vars());
    }

    public function getShortcode($id)
    {
        if ($id) {
            $product = Product::where('status', '!=', 4)
                ->where('user_id', auth()->user()->id)
                ->where('sub_category_id', $id)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($product) {
                return response()->json(['status' => 'success', 'code' => $product->product_code]);
            } else {
                return response()->json(['status' => 'success', 'code' => 'NoProduct']);
            }
        } else {
            return "";
        }
    }

    public function newProduct(Request $request)
    {
        $page_title = 'New Product';
        // $allowedproduct = UserSubscription::where('user_id', auth()->user()->id)->where('status', 1)->with('subscriptions')->first();
        // $productscount = Product::where('status', '!=', 4)->where('user_id', auth()->user()->id)->count();
        // if (!is_null($allowedproduct)) {
        //     if ($allowedproduct->subscriptions->allowed_product <= $productscount) {
        //         $notify[] = ['error', 'Please Upgrade Your Plans'];
        //         return back()->withNotify($notify);
        //     }
        // } else {
        //     $freeplan = Subscription::where("id", 1)->first();
        //     if ($freeplan->allowed_product <= $productscount) {
        //         $notify[] = ['error', 'Please Upgrade Your Plans'];
        //         return back()->withNotify($notify);
        //     }
        // }
        // if (!is_null($allowedproduct)) {
        //     $customfield_status = $allowedproduct->subscriptions->cf_status;
        // } else {
        //     $customfield_status = 0;
        // }
        $token = '';

        if ($request->is('api/*')) {
            $token = $request->token;
            $api = true;
            $partial = false;
        }
        $categories = Category::where('status', '1')->with(['subcategories' => function ($q) {
            $q->where('status', 1)->get();
        }, 'categoryDetails'])->latest()->get();
        $customfield_status = 0;
        $customfields = CustomField::where('status', 1)->where('user_id', auth()->user()->id)->get();
        return view($this->activeTemplate . 'user.product.new', get_defined_vars());
    }

    public function storeProduct(Request $request)
    {
        $validation_rule = [
            'category_id' => 'required|numeric|gt:0',
            'sub_category_id' => 'required|numeric|gt:0',
            'regular_price' => 'required|numeric|gt:0',
            'extended_price' => 'required|numeric|gt:0',
            'support' => 'required|integer|max:1',
            'support_discount' => 'sometimes|required|numeric|max:100',
            'support_charge' => 'sometimes|required|numeric|max:100',
            'name' => 'required|max:191',
            'image' => ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            // 'file' => ['required', 'mimes:zip', new FileTypeValidate(['zip'])],
            'screenshot' => 'required|array|min:1',
            'screenshot.*' => ['required', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'demo_link' => 'required|url|max:255',
            'message' => 'nullable|max:255',
            'tag.*' => 'required|max:255',
        ];
        $category = Category::where('status', 1)->findOrFail($request->category_id);
        $originalcategory = Category::where('name', 'like', "others")->first();

        $subcategory = SubCategory::where('status', 1)->findOrFail($request->sub_category_id);
        $subcategoryId = SubCategory::where('category_id', $request->category_id)->where('status', 1)->pluck('id')->toArray();




        if (!in_array($subcategory->id, $subcategoryId)) {
            $notify[] = ['error', 'Something goes wrong'];
            return back()->withNotify($notify);
        }

        $categoryDetails        = $category->categoryDetails;
        $categoryDetailsInput   = $request['c_details'] ?? [];

        $minPrice = $category->buyer_fee + (($category->buyer_fee * auth()->user()->levell->product_charge) / 100);

        if (($request->regular_price < $minPrice) || ($request->extended_price < $minPrice)) {
            $notify[] = ['error', 'Minimum price is ' . $minPrice];
            return back()->withNotify($notify);
        }

        if (count($categoryDetailsInput) != count($categoryDetails)) {
            $notify[] = ['error', 'Something goes wrong.'];
            return back()->withNotify($notify);
        }

        foreach ($categoryDetails->pluck('name') as $item) {
            $validation_rule['c_details.' . str_replace(' ', '_', strtolower($item))] = 'required';
        }

        $request->validate($validation_rule, [
            'tag.*.required' => 'Add at least one tag',
            'tag.*.max' => 'Total options should not be more than 191 characters'
        ]);


        $pImage = '';
        if ($request->hasFile('image')) {
            try {
                $location = imagePath()['p_image']['path'];
                $size = imagePath()['p_image']['size'];
                $thumb = imagePath()['p_image']['thumb'];
                $pImage = uploadImage($request->image, $location, $size, '', $thumb);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload the image'];
                return back()->withNotify($notify);
            }
        }

        $general = GeneralSetting::first();

        $pFile = '';

        if ($request->hasFile('file')) {

            $disk = $general->server;
            $date = date('Y') . '/' . date('m') . '/' . date('d');

            if ($disk == 'current') {
                try {
                    $location = imagePath()['p_file']['path'];
                    $pFile = str_replace(' ', '_', strtolower($request->name)) . '_' . uniqid() . time() . '.zip';
                    $request->file->move($location, $pFile);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload the file'];
                    return back()->withNotify($notify);
                }
                $server = 0;
            } else {
                try {
                    $fileExtension  = $request->file('file')->getClientOriginalExtension();
                    $file           = File::get($request->file);
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

        $pScreenshot = [];

        if ($request->hasFile('screenshot')) {
            foreach ($request->screenshot as $item) {
                try {
                    $location = imagePath()['p_screenshot']['path'];
                    $pScreenshot[] = uploadImage($item, $location);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload the image'];
                    return back()->withNotify($notify);
                }
            }
        }
        $product = new Product();
        $product->user_id           = auth()->user()->id;
        $product->category_id       = $request->category_id;
        $product->sub_category_id   = $request->sub_category_id;
        $product->regular_price     = $request->regular_price + $category->buyer_fee;
        $product->extended_price    = $request->extended_price + $category->buyer_fee;
        $product->support           = $request->support;
        $product->support_charge    = $request->support_charge ?? 0;
        $product->support_discount  = $request->support_discount ?? 0;
        $product->name              = $request->name;
        $product->server            = $server ?? 0;
        $product->image             = $pImage;
        if (empty($pFile) && is_null($request->sourcelink)) {
            $product->file = null;
        } elseif (empty($pFile) && !is_null($request->sourcelink)) {
            $product->file = $request->sourcelink;
        } else {
            $product->file = $pFile;
        }
        $product->screenshot = array_values($pScreenshot);
        $product->demo_link = $request->demo_link;
        $product->description = $request->description;

        $product->tag = array_values($request->tag);
        $product->message = $request->message;
        $product->category_details = $categoryDetailsInput;
        if (auth()->user()->is_approve == 1) {
            $product->status = 1;
        }
        $product->code = get_productcode($request->category_id, $request->sub_category_id, $request->name);
        $product->save();
        if ($request->varient_title) {
            $price = $request->varient_price;
            $quantity = $request->is_quantity;
            if ($request->min_quantity) {
                $minquantity = $request->min_quantity;
            }
            foreach ($request->varient_title as $key => $names) {
                $bumps = new ProductBump;
                $bumps->name = $names;
                $bumps->price = $price[$key];
                $bumps->is_quantity = $quantity[$key];
                if ($quantity[$key] == 0) {
                    $bumps->min_quantity = 0;
                } elseif ($quantity[$key] == 1) {
                    $bumps->min_quantity = $minquantity[$key];
                }
                $bumps->product_id = $product->id;
                $bumps->save();
            }
        }

        if ($request->category_id == $originalcategory->id) {

            $othercategory = OtherCategory::where('category_name', 'like', $request->othercategory)->first();
            if (is_null($othercategory)) {
                $othercategory = new OtherCategory;
                $othercategory->category_name = $request->othercategory;
                $othercategory->subcategory_name = $request->othersubcategory;
                $othercategory->save();
                $admincategory = new Category;
                $admincategory->name = $request->othercategory;
                $admincategory->status = 0;
                $admincategory->save();
                $adminsubcategory = new SubCategory;
                $adminsubcategory->name = $request->othersubcategory;
                $adminsubcategory->category_id = $admincategory->id;
                $adminsubcategory->status = 0;
                $adminsubcategory->save();
            }
            $othercategory->category_name = $request->othercategory;
            $othercategory->subcategory_name = $request->othersubcategory;
            $othercategory->save();
            $othercp = new OtherCategoryProduct;
            $othercp->product_id = $product->id;
            $othercp->othercategory_id = $othercategory->id;
            $othercp->save();
        }
        if ($request->field_name) {
            foreach ($request->field_name as $names) {
                $productcf = new ProductCustomField;
                $productcf->product_id = $product->id;
                $productcf->customfield_id = $names;
                $productcf->save();
            }
        }

        $notify[] = ['success', 'Product successfully submitted'];
        return redirect()->route('user.product.all')->withNotify($notify);
    }

    public function editProduct(Request $request, $id)
    {
        $page_title = 'Edit Product';
        
        $user = auth()->user();

        $product = Product::where('id', Crypt::decrypt($id))->where('user_id', $user->id)->with(['category', 'subcategory', 'bumps', 'othercategoriesproduct', 'productcustomfields'])->first();
        $productcustomfields = ProductCustomField::all();
        $customfield = CustomField::where('status', 1)->where('user_id', $user->id)->get();
        if ($product->user_id != $user->id) {
            $notify[] = ['error', 'Yor are not authorized to edit this product'];
            return back()->withNotify($notify);
        }

        $action = route('user.product.update', Crypt::encrypt($product->id));

        $api = false;
        $token = '';
        if ($request->is('api/*')) {
            $token = $request->token;
            $api = true;
            $partial = false;
            $action = route('iframe.api.product.update', Crypt::encrypt($product->id)) . "?token=" . $request->token;
        }

        return view($this->activeTemplate . 'user.product.edit', get_defined_vars());
    }

    public function updateProduct(Request $request, $id)
    {
        // $bumps=ProductBump::where('product_id',Crypt::decrypt($id))->get();
        // dd($bumps);
        $validation_rule = [
            'regular_price' => 'required|numeric|gt:0',
            'extended_price' => 'required|numeric|gt:0',
            'support' => 'required|integer|max:1',
            'support_discount' => 'sometimes|required|numeric|max:100',
            'support_charge' => 'sometimes|required|numeric|max:100',
            'name' => 'required|max:191',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            // 'file' => ['nullable', 'mimes:zip', new FileTypeValidate(['zip'])],
            'screenshot' => 'nullable|array|min:3',
            'screenshot.*' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'demo_link' => 'required|url|max:255',
            'message' => 'nullable|max:255',
            'tag.*' => 'required|max:255',
        ];

        $product = Product::findOrFail(Crypt::decrypt($id));

        if ($product->status != 1) {
            $notify[] = ['error', 'This product is not upgradable'];
            return back()->withNotify($notify);
        }

        if ($product->user_id != auth()->user()->id) {
            $notify[] = ['error', 'You are not authorized to edit this product'];
            return back()->withNotify($notify);
        }

        $checkProduct = TempProduct::where('user_id', auth()->user()->id)->where('product_id', $id)->where('type', 2)->first();

        if ($checkProduct) {
            $notify[] = ['error', 'Previous update of this product is pending'];
            return back()->withNotify($notify);
        }

        if ($checkProduct == null) {

            $category = Category::findOrFail($product->category_id);
            $categoryDetails = $category->categoryDetails;
            $categoryDetailsInput = $request['c_details'] ?? [];

            $minPrice = $category->buyer_fee + (($category->buyer_fee * auth()->user()->levell->product_charge) / 100);

            if (($request->regular_price < $minPrice) || ($request->extended_price < $minPrice)) {
                $notify[] = ['error', 'Minimum price is ' . $minPrice];
                return back()->withNotify($notify);
            }

            if (count($categoryDetailsInput) != count($categoryDetails)) {
                $notify[] = ['error', ' There is Something goes wrong.'];
                return back()->withNotify($notify);
            }

            foreach ($categoryDetails->pluck('name') as $item) {
                $validation_rule['c_details.' . str_replace(' ', '_', strtolower($item))] = 'required';
            }

            $request->validate($validation_rule, [
                'tag.*.required' => 'Add at least one tag',
                'tag.*.max' => 'Total options should not be more than 191 characters'
            ]);

            $pImage = '';
            if ($request->hasFile('image')) {
                try {
                    $location = imagePath()['temp_p_image']['path'];
                    $size = imagePath()['temp_p_image']['size'];
                    $thumb = imagePath()['temp_p_image']['thumb'];
                    $pImage = uploadImage($request->image, $location, $size, '', $thumb);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload the image'];
                    return back()->withNotify($notify);
                }
            }

            $pFile      = '';
            $general    = GeneralSetting::first();
            $server     = $product->server;

            if ($request->hasFile('file')) {
                $disk = $general->server;

                $date = date('Y') . '/' . date('m') . '/' . date('d');

                if ($disk == 'current') {
                    try {
                        $location = imagePath()['temp_p_file']['path'];
                        $pFile = str_replace(' ', '_', strtolower($request->name)) . '_' . uniqid() . time() . '.zip';
                        $request->file->move($location, $pFile);
                    } catch (\Exception $exp) {
                        $notify[] = ['error', 'Could not upload the file'];
                        return back()->withNotify($notify);
                    }
                    $server = 0;
                } else {
                    try {
                        $fileExtension  = $request->file('file')->getClientOriginalExtension();
                        $file           = File::get($request->file);
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

            $pScreenshot = [];
            if ($request->hasFile('screenshot')) {
                foreach ($request->screenshot as $item) {
                    try {
                        $location = imagePath()['temp_p_screenshot']['path'];
                        $pScreenshot[] = uploadImage($item, $location);
                    } catch (\Exception $exp) {
                        $notify[] = ['error', 'Could not upload the image'];
                        return back()->withNotify($notify);
                    }
                }
            }

            $product->update_status = 1;
            $product->save();

            $tempProduct                    = new TempProduct();
            $tempProduct->user_id           = userid();
            $tempProduct->product_id        = $product->id;
            $tempProduct->category_id       = $product->category_id;
            $tempProduct->sub_category_id   = $product->sub_category_id;
            $tempProduct->regular_price     = $request->regular_price + $product->category->buyer_fee;
            $tempProduct->extended_price    = $request->extended_price + $product->category->buyer_fee;
            $tempProduct->support           = $request->support;
            $tempProduct->support_charge    = $request->support_charge ?? 0;
            $tempProduct->support_discount  = $request->support_discount ?? 0;
            $tempProduct->name              = $request->name;
            $tempProduct->image             = $pImage;
            $tempProduct->server           = $server;
            $file = '';
            if (empty($pFile) && !is_null($request->sourcelink)) {
                $file = $request->sourcelink;
            } else {
                $file = $pFile;
            }
            $tempProduct->file = $file;
            $tempProduct->screenshot        = $pScreenshot;
            $tempProduct->demo_link         = $request->demo_link;
            $tempProduct->description       = $request->description;
            $tempProduct->tag               = array_values($request->tag);
            $tempProduct->message           = $request->message;
            $tempProduct->category_details  = $categoryDetailsInput;
            $tempProduct->type              = 2;
            $tempProduct->save();
            if ($request->varient_title) {
                $price = $request->varient_price;
                $quantity = $request->is_quantity;
                if ($request->min_quantity) {
                    $minquantity = $request->min_quantity;
                }
                foreach ($request->varient_title as $key => $names) {
                    $bumps = ProductBump::where('product_id', Crypt::decrypt($id))->where('name', 'like', $names)->first();
                    if (is_null($bumps)) {
                        $bumps = new ProductBump;
                    }
                    $bumps->name = $names;
                    $bumps->price = $price[$key];
                    $bumps->is_quantity = $quantity[$key];
                    if ($quantity[$key] == 0) {
                        $bumps->min_quantity = 0;
                    } elseif ($quantity[$key] == 1) {
                        $bumps->min_quantity = $minquantity[$key];
                    }
                    $bumps->product_id = Crypt::decrypt($id);
                    $bumps->save();
                }
            }
            if ($request->field_name) {
                foreach ($request->field_name as $names) {

                    $productcf = ProductCustomField::where('product_id', Crypt::decrypt($id))->where('customfield_id', $names)->first();
                    if (is_null($productcf)) {
                        $productcf = new ProductCustomField;
                        $productcf->product_id = Crypt::decrypt($id);
                        $productcf->customfield_id = $names;
                        $productcf->save();
                    } else {
                        $productcf->product_id = Crypt::decrypt($id);
                        $productcf->customfield_id = $names;
                        $productcf->save();
                    }
                }
            }

            $notify[] = ['success', 'Your action is on process. Wait for the approval'];
            return redirect()->route('user.product.all')->withNotify($notify);
        }
    }

    public function resubmitProduct($id)
    {
        $page_title = 'Resubmit Product';
        $product = Product::where('status', 2)->findOrFail(Crypt::decrypt($id));
        $productcustomfields = ProductCustomField::all();
        $customfield = CustomField::all();

        if ($product->user_id != auth()->user()->id) {
            $notify[] = ['error', 'Yor are not authorized to resubmit this product'];
            return back()->withNotify($notify);
        }
        $action = route('user.product.resubmit.store', Crypt::encrypt($product->id));
        return view($this->activeTemplate . 'user.product.edit', get_defined_vars());
    }

    public function resubmitProductStore(Request $request, $id)
    {
        $validation_rule = [
            'regular_price' => 'required|numeric|gt:0',
            'extended_price' => 'required|numeric|gt:0',
            'support' => 'required|integer|max:1',
            'support_discount' => 'sometimes|required|numeric|max:100',
            'support_charge' => 'sometimes|required|numeric|max:100',
            'name' => 'required|max:191',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            // 'file' => ['nullable', 'mimes:zip', new FileTypeValidate(['zip'])],
            'screenshot' => 'nullable|array|min:3',
            'screenshot.*' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'demo_link' => 'required|url|max:255',
            'message' => 'nullable|max:255',
            'tag.*' => 'required|max:255',
        ];

        $product = Product::findOrFail(Crypt::decrypt($id));

        if ($product->status != 2) {
            $notify[] = ['error', 'This product is not re-submittable'];
            return back()->withNotify($notify);
        }

        if ($product->user_id != userid()) {
            $notify[] = ['error', 'You are not authorized to resubmit this product'];
            return back()->withNotify($notify);
        }

        $checkProduct = TempProduct::where('user_id', auth()->user()->id)->where('product_id', $id)->where('type', 1)->first();

        if ($checkProduct) {
            $notify[] = ['error', 'Previous resubmission of this product is pending'];
            return back()->withNotify($notify);
        }

        if ($checkProduct == null) {

            $category = Category::findOrFail($product->category_id);
            $categoryDetails = $category->categoryDetails;
            $categoryDetailsInput = $request['c_details'] ?? [];

            $minPrice = $category->buyer_fee + (($category->buyer_fee * auth()->user()->levell->product_charge) / 100);

            if (($request->regular_price < $minPrice) || ($request->extended_price < $minPrice)) {
                $notify[] = ['error', 'Minimum price is ' . $minPrice];
                return back()->withNotify($notify);
            }

            if (count($categoryDetailsInput) != count($categoryDetails)) {
                $notify[] = ['error', 'Something goes wrong.'];
                return back()->withNotify($notify);
            }

            foreach ($categoryDetails->pluck('name') as $item) {
                $validation_rule['c_details.' . str_replace(' ', '_', strtolower($item))] = 'required';
            }

            $request->validate($validation_rule, [
                'tag.*.required' => 'Add at least one tag',
                'tag.*.max' => 'Total options should not be more than 191 characters'
            ]);


            $pImage = '';
            if ($request->hasFile('image')) {
                try {
                    $location = imagePath()['temp_p_image']['path'];
                    $size = imagePath()['temp_p_image']['size'];
                    $thumb = imagePath()['temp_p_image']['thumb'];
                    $pImage = uploadImage($request->image, $location, $size, '', $thumb);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload the image'];
                    return back()->withNotify($notify);
                }
            }

            $pFile = '';
            if ($request->hasFile('file')) {
                try {
                    $location = imagePath()['temp_p_file']['path'];
                    $pFile = str_replace(' ', '_', strtolower($request->name)) . '_' . uniqid() . time() . '.zip';
                    $request->file->move($location, $pFile);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload the file'];
                    return back()->withNotify($notify);
                }
            }

            $pScreenshot = [];
            if ($request->hasFile('screenshot')) {
                foreach ($request->screenshot as $item) {
                    try {
                        $location = imagePath()['temp_p_screenshot']['path'];
                        $pScreenshot[] = uploadImage($item, $location);
                    } catch (\Exception $exp) {
                        $notify[] = ['error', 'Could not upload the image'];
                        return back()->withNotify($notify);
                    }
                }
            }

            $product->status = 5;
            $product->save();

            $tempProduct = new TempProduct();
            $tempProduct->user_id = auth()->user()->id;
            $tempProduct->product_id = $product->id;
            $tempProduct->category_id = $product->category_id;
            $tempProduct->sub_category_id = $product->sub_category_id;
            $tempProduct->regular_price = $request->regular_price + $product->category->buyer_fee;
            $tempProduct->extended_price = $request->extended_price + $product->category->buyer_fee;
            $tempProduct->support = $request->support;
            $tempProduct->support_charge = $request->support_charge ?? 0;
            $tempProduct->support_discount = $request->support_discount ?? 0;
            $tempProduct->name = $request->name;
            $tempProduct->image = $pImage;
            $file = '';
            if (!empty($request->sourcelink)) {
                $file = $request->sourcelink;
            } else {
                $file = $pFile;
            }
            $tempProduct->file = $file;
            $tempProduct->screenshot = $pScreenshot;
            $tempProduct->demo_link = $request->demo_link;
            $tempProduct->description = $request->description;

            $tempProduct->tag = array_values($request->tag);

            $tempProduct->message = $request->message;
            $tempProduct->category_details = $categoryDetailsInput;
            $tempProduct->type = 1;
            $tempProduct->save();
            if ($request->varient_title) {
                $price = $request->varient_price;
                $quantity = $request->is_quantity;
                if ($request->min_quantity) {
                    $minquantity = $request->min_quantity;
                }
                foreach ($request->varient_title as $key => $names) {
                    $bumps = ProductBump::where('product_id', Crypt::decrypt($id))->where('name', 'like', $names)->first();
                    if (is_null($bumps)) {
                        $bumps = new ProductBump;
                    }
                    $bumps->name = $names;
                    $bumps->price = $price[$key];
                    $bumps->is_quantity = $quantity[$key];
                    if ($quantity[$key] == 0) {
                        $bumps->min_quantity = 0;
                    } elseif ($quantity[$key] == 1) {
                        $bumps->min_quantity = $minquantity[$key];
                    }
                    $bumps->product_id = Crypt::decrypt($id);
                    $bumps->save();
                }
            }
            if ($request->field_name) {
                foreach ($request->field_name as $names) {

                    $productcf = ProductCustomField::where('product_id', Crypt::decrypt($id))->where('customfield_id', $names)->first();
                    if (is_null($productcf)) {
                        $productcf = new ProductCustomField;
                        $productcf->product_id = Crypt::decrypt($id);
                        $productcf->customfield_id = $names;
                        $productcf->save();
                    } else {
                        $productcf->product_id = Crypt::decrypt($id);
                        $productcf->customfield_id = $names;
                        $productcf->save();
                    }
                }
            }

            $notify[] = ['success', 'You action is on process. Wait for the approval'];
            return redirect()->route('user.product.all')->withNotify($notify);
        }
    }
    public function deleteProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        $product = Product::findOrFail(Crypt::decrypt($request->product_id));

        $product->status = 4;
        $product->save();

        $notify[] = ['success', 'Product successfully deleted'];
        return back()->withNotify($notify);
    }

    public function commentStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'comment' => 'required'
        ]);

        $product = Product::where('status', 1)->findOrFail(Crypt::decrypt($request->product_id));

        $comment = new Comment();
        $comment->product_id = $product->id;
        $comment->user_id = auth()->user()->id;
        $comment->comment = $request->comment;
        $comment->save();

        $notify[] = ['success', 'Your comment added successfully'];
        return back()->withNotify($notify);
    }

    public function replyStore(Request $request)
    {
        $request->validate([
            'comment_id' => 'required',
            'reply' => 'required'
        ]);

        $comment = Comment::findOrFail(Crypt::decrypt($request->comment_id));

        $reply = new Reply();
        $reply->comment_id = $comment->id;
        $reply->user_id = auth()->user()->id;
        $reply->reply = $request->reply;
        $reply->save();

        $notify[] = ['success', 'Your reply added successfully'];
        return back()->withNotify($notify);
    }
    public function customfield()
    {
        $customfield = CustomField::where('user_id', auth()->user()->id)->get();
        if ($customfield->count() <= 0) {
            return response()->json(['status' => 'error', 'customfield' => null]);
        }
        return response()->json(['status' => 'success', 'customfield' => $customfield]);
    }
}
