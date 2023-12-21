<?php

namespace App\Http\Controllers\Admin;

use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Product;
use App\Sell;
use Illuminate\Http\Request;
use App\TempProduct;
use App\CustomField;
use App\OtherCategory;
use App\ProductCustomField;
use Illuminate\Support\Facades\Crypt;
use File;
use Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function pending()
    {
        $page_title = 'Pending Products';
        $empty_message = 'No data found';

        $products = Product::where('status',0)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->latest()->with(['category','subcategory'])->paginate(getPaginate());

        return view('admin.product.index',compact('page_title','products','empty_message'));
    }

    public function approved()
    {
        $page_title = 'Approved Products';
        $empty_message = 'No data found';

        $products = Product::where('status',1)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->latest()->with(['category','subcategory','user'])->paginate(getPaginate());

        return view('admin.product.index',compact('page_title','products','empty_message'));
    }

    public function softRejected()
    {
        $page_title = 'Soft Rejected Products';
        $empty_message = 'No data found';

        $products = Product::where('status',2)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->latest()->with(['category','subcategory'])->paginate(getPaginate());

        return view('admin.product.index',compact('page_title','products','empty_message'));
    }

    public function hardRejected()
    {
        $page_title = 'Hard Rejected Products';
        $empty_message = 'No data found';

        $products = Product::where('status',3)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->latest()->with(['category','subcategory'])->paginate(getPaginate());

        return view('admin.product.index',compact('page_title','products','empty_message'));
    }

    public function download($id)
    {
        $product = Product::findOrFail(Crypt::decrypt($id));
 $general = GeneralSetting::first();
        $file = $product->file;
       if(is_null($file) || empty($file)){
            $notify[] = ['error', 'Author Did not Provide the File yet'];
            return back()->withNotify($notify);
        }else{
        if(Str::contains($file, 'http')){
            session()->put('copytext',$file);
             $notify[] = ['success', 'Source Code of the Product is successfully Copied'];
            return back()->withNotify($notify);
        }else{
        if ($product->server == 0) {
            $full_path = 'assets/product/' . $file;
        }else{
            $full_path = $general->ftp->domain.'/'.Storage::disk('custom-ftp')->url($product->file);
        }
        $title = str_replace(' ','_',strtolower($product->name));
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $ext);
        return readfile($full_path);
    }
}
    }

    public function view($id)
    {
        $product = Product::whereHas('user', function ($query) {
            $query->where('status',1);
        })->with(['category', 'subcategory','bumps','othercategoriesproduct','productcustomfields'])->findOrFail(Crypt::decrypt($id));
 
        $customfield=CustomField::where('user_id',$product->user_id)->get();
        $othercategories=OtherCategory::all();
        $page_title = 'Product : '.$product->name;

        return view('admin.product.view',get_defined_vars());
    }

    public function approveProduct(Request $request){
        $request->validate([
            'id' => 'required|gt:0',
        ]);

        $product = Product::whereHas('user', function ($query) {
            $query->where('status',1);
        })->findOrFail($request->id);
        $product->status = 1;
        $product->save();

        // notify($product->user, 'PRODUCT_APPROVED', [
        //     'product_name' => $product->name
        // ]);

        $notify[] = ['success', 'Product has been approved successfully'];
        return back()->withNotify($notify);
    }

    public function softRejectProduct(Request $request){
        $request->validate([
            'id' => 'required|gt:0',
            'message' => 'required',
        ]);

        $product = Product::whereHas('user', function ($query) {
            $query->where('status',1);
        })->findOrFail($request->id);
        $product->status = 2;
        $product->soft_reject = $request->message;
        $product->save();

        notify($product->user, 'PRODUCT_SOFT_REJECT', [
            'product_name' => $product->name,
            'rejection_message' => $product->soft_reject,
        ]);

        $notify[] = ['success', 'Product has been soft rejected successfully'];
        return back()->withNotify($notify);
    }

    public function hardRejectProduct(Request $request){
        $request->validate([
            'id' => 'required|gt:0',
            'message' => 'required',
        ]);

        $product = Product::whereHas('user', function ($query) {
            $query->where('status',1);
        })->findOrFail($request->id);
        $product->status = 3;
        $product->hard_reject = $request->message;
        $product->save();

        notify($product->user, 'PRODUCT_HARD_REJECT', [
            'product_name' => $product->name
        ]);

        $notify[] = ['success', 'Product has been hard rejected successfully'];
        return back()->withNotify($notify);
    }

    public function featuredProduct(Request $request){
        $request->validate([
            'id' => 'required|gt:0'
        ]);

        $product = Product::whereHas('user', function ($query) {
            $query->where('status',1);
        })->findOrFail($request->id);

        $product->featured = 1;
        $product->save();

        $notify[] = ['success', 'Product has been featured successfully'];
        return back()->withNotify($notify);
    }

    public function unFeaturedProduct(Request $request){
        $request->validate([
            'id' => 'required|gt:0'
        ]);

        $product = Product::whereHas('user', function ($query) {
            $query->where('status',1);
        })->findOrFail($request->id);

        $product->featured = 0;
        $product->save();

        $notify[] = ['success', 'Product has been unfeatured successfully'];
        return back()->withNotify($notify);
    }

    public function updatePending()
    {
        $page_title = 'Update Pending Products';
        $empty_message = 'No data found';

        $products = TempProduct::where('type',2)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->latest()->with(['category','subcategory'])->paginate(getPaginate());

        return view('admin.product.index',compact('page_title','products','empty_message'));
    }

    public function updatePendingView($id)
    {
        $tempproduct = TempProduct::where('type',2)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->with(['product','product.category', 'product.subcategory','product.bumps','product.othercategoriesproduct','product.productcustomfields'])->findOrFail(Crypt::decrypt($id));
        $customfield=CustomField::all();
        $othercategories=OtherCategory::all();
        $page_title = 'Product : '.$tempproduct->name;

        return view('admin.product.view',get_defined_vars());
    }

    public function updatePendingDownload($id)
    {
        $product = TempProduct::where('type',2)->findOrFail(Crypt::decrypt($id));
        $general = GeneralSetting::first();
            $file = $product->file;
            if(is_null($file) || empty($file)){
            $notify[] = ['error', 'Author Did not Provide the File yet'];
            return back()->withNotify($notify);
        }elseif(Str::contains($file, 'http')){
            session()->put('copytext',$file);
             $notify[] = ['success', 'Source Code of the Product is successfully Copied'];
            return back()->withNotify($notify);
        }elseif(Str::contains($file, '.zip')){
            if ($product->server == 0) {
                $full_path = 'assets/temp_product/' . $file;
            }else{
                $full_path = $general->ftp->domain.'/'.Storage::disk('custom-ftp')->url($product->file);
            }
            $title = str_replace(' ','_',strtolower($product->name));
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
            header("Content-Type: " . $ext);
            return readfile($full_path);

        }else{
            $notify[] = ['error', 'This product has no file update'];
            return back()->withNotify($notify);
        }
        }


    public function updatePendingApprove(Request $request)
    {



        $request->validate([
            'id' => 'required',
        ]);



        $tempProduct = TempProduct::where('type',2)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->where('product_id',$request->id)->first();

        $mainProduct = Product::where('id',$tempProduct->product_id)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->first();




        if($tempProduct->image){
            $tempProductLocation = imagePath()['temp_p_image']['path'];
            $mainProductLocation = imagePath()['p_image']['path'];

            removeFile($mainProductLocation . '/' . $mainProduct->image);
            removeFile($mainProductLocation . '/thumb_' . $mainProduct->image);
            rename($tempProductLocation . '/' . $tempProduct->image, $mainProductLocation . '/' . $tempProduct->image);
            rename($tempProductLocation . '/thumb_' . $tempProduct->image, $mainProductLocation . '/thumb_' . $tempProduct->image);

            $mainProduct->image = $tempProduct->image;
        }

        if(Str::contains($tempProduct->file, 'http')){
            $mainProduct->file = $tempProduct->file;
        }elseif($tempProduct->file == null){
            $mainProduct->file='';
        }else{

            if($mainProduct->server==1){
                $gnl    = GeneralSetting::first();
                $disk = $gnl->server;
                removeRemoteFile($mainProduct->file, $disk);
            }else{
                $tempProductLocation = imagePath()['temp_p_file']['path'];
                $mainProductLocation = imagePath()['p_file']['path'];
                removeFile($mainProductLocation . '/' . $mainProduct->file);
                rename($tempProductLocation . '/' . $tempProduct->file, $mainProductLocation . '/' . $tempProduct->file);
            }

            $mainProduct->file = $tempProduct->file;
        }

        if($tempProduct->screenshot){
            $tempProductLocation = imagePath()['temp_p_screenshot']['path'];
            $mainProductLocation = imagePath()['p_screenshot']['path'];

            foreach ($mainProduct->screenshot as $item) {
                removeFile($mainProductLocation . '/' . $item);
            }

            foreach ($tempProduct->screenshot as $item) {
                rename($tempProductLocation . '/' . $item, $mainProductLocation . '/' . $item);
            }

            $pScreenshot = $tempProduct->screenshot;
        }else{
            $pScreenshot = $mainProduct->screenshot;
        }

        $mainProduct->update_status = 2;
        $mainProduct->regular_price = $tempProduct->regular_price;
        $mainProduct->extended_price = $tempProduct->extended_price;
        $mainProduct->support = $tempProduct->support;
        $mainProduct->support_charge = $tempProduct->support_charge;
        $mainProduct->support_discount = $tempProduct->support_discount;
        $mainProduct->name = $tempProduct->name;
        $mainProduct->screenshot = $pScreenshot;
        $mainProduct->demo_link = $tempProduct->demo_link;
        $mainProduct->description = $tempProduct->description;
        $mainProduct->tag = $tempProduct->tag;
        $mainProduct->message = $tempProduct->message;
        $mainProduct->category_details = $tempProduct->category_details;
        $mainProduct->save();

        $tempProduct->delete();



        notify($mainProduct->user, 'PRODUCT_UPDATE_APPROVED', [
            'product_name' => $mainProduct->name
        ]);

        $notify[] = ['success', 'Update of this product has been approved successfully'];
        return back()->withNotify($notify);
    }

    public function updatePendingReject(Request $request)
    {
        $request->validate([
            'id' => 'required|gt:0',
            'message' => 'required'
        ]);

        $tempProduct = TempProduct::where('type',2)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->where('product_id',$request->id)->first();

        $mainProduct = Product::where('id',$tempProduct->product_id)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->first();

        $mainProduct->update_status = 3;
        $mainProduct->update_reject = $request->message;
        $mainProduct->save();

        if($tempProduct->image){

            $tempProductLocation = imagePath()['temp_p_image']['path'];
            removeFile($tempProductLocation . '/' . $tempProduct->image);
            removeFile($tempProductLocation . '/thumb_' . $tempProduct->image);
        }

        if($tempProduct->file != null){

            $tempProductLocation = imagePath()['temp_p_file']['path'];
            removeFile($tempProductLocation . '/' . $tempProduct->file);
        }

        if($tempProduct->screenshot){

            $tempProductLocation = imagePath()['temp_p_screenshot']['path'];

            foreach ($tempProduct->screenshot as $item) {
                removeFile($tempProductLocation . '/' . $item);
            }
        }

        $tempProduct->delete();

        notify($mainProduct->user, 'PRODUCT_UPDATE_REJECTED', [
            'product_name' => $mainProduct->name
        ]);

        $notify[] = ['success', 'Update of this product has been rejected successfully'];
        return redirect()->route('admin.dashboard')->withNotify($notify);
    }

    public function resubmit()
    {
        $page_title = 'Resubmitted Products';
        $empty_message = 'No data found';

        $products = TempProduct::where('type',1)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->latest()->with(['category','subcategory'])->paginate(getPaginate());

        return view('admin.product.index',compact('page_title','products','empty_message'));
    }

    public function resubmitView($id)
    {
        $tempproduct = TempProduct::where('type',1)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->with(['product','product.category', 'product.subcategory','product.bumps','product.othercategoriesproduct','product.productcustomfields','product.customfieldresponses'])->findOrFail(Crypt::decrypt($id));
        $customfield=CustomField::all();
        $othercategories=OtherCategory::all();
        $page_title = 'Product : '.$tempproduct->name;

        return view('admin.product.view',get_defined_vars());
    }

    public function resubmitDownload($id)
    {
        $product = TempProduct::where('type',1)->findOrFail(Crypt::decrypt($id));

        if ($product->file) {
            $file = $product->file;
            if(is_null($file) || empty($file)){
            $notify[] = ['error', 'Author Did not Provide the File yet'];
            return back()->withNotify($notify);
        }elseif(Str::contains($file, 'http')){
            session()->put('copytext',$file);
             $notify[] = ['success', 'Source Code of the Product is successfully Copied'];
            return back()->withNotify($notify);
        }elseif(Str::contains($file, '.zip')){
            $full_path = 'assets/temp_product/' . $file;
            $title = str_replace(' ','_',strtolower($product->name));
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $mimetype = mime_content_type($full_path);
            header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
            header("Content-Type: " . $mimetype);
            return readfile($full_path);
        }else{
            $notify[] = ['error', 'This product has no file update'];
            return back()->withNotify($notify);
        }
    }
}

    public function resubmitApprove(Request $request)
    {
        $request->validate([
            'id' => 'required|gt:0',
        ]);

        $tempProduct = TempProduct::where('type',1)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->where('product_id',$request->id)->first();

        $mainProduct = Product::where('id',$tempProduct->product_id)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->first();


        if($tempProduct->image){
            $tempProductLocation = imagePath()['temp_p_image']['path'];
            $mainProductLocation = imagePath()['p_image']['path'];

            removeFile($mainProductLocation . '/' . $mainProduct->image);
            removeFile($mainProductLocation . '/thumb_' . $mainProduct->image);
            rename($tempProductLocation . '/' . $tempProduct->image, $mainProductLocation . '/' . $tempProduct->image);
            rename($tempProductLocation . '/thumb_' . $tempProduct->image, $mainProductLocation . '/thumb_' . $tempProduct->image);

            $mainProduct->image = $tempProduct->image;
        }

        if(Str::contains($tempProduct->file, 'http')){
            $mainProduct->file = $tempProduct->file;
        }elseif($tempProduct->file == null){
            $mainProduct->file='';
        }else{
            if($mainProduct->server==1){
                $gnl    = GeneralSetting::first();
                $disk = $gnl->server;
                removeRemoteFile($mainProduct->file, $disk);
            }else{
                $tempProductLocation = imagePath()['temp_p_file']['path'];
                $mainProductLocation = imagePath()['p_file']['path'];

                removeFile($mainProductLocation . '/' . $mainProduct->file);
                rename($tempProductLocation . '/' . $tempProduct->file, $mainProductLocation . '/' . $tempProduct->file);
            }


            $mainProduct->file = $tempProduct->file;
        }

        if($tempProduct->screenshot){
            $tempProductLocation = imagePath()['temp_p_screenshot']['path'];
            $mainProductLocation = imagePath()['p_screenshot']['path'];

            foreach ($mainProduct->screenshot as $item) {
                removeFile($mainProductLocation . '/' . $item);
            }

            foreach ($tempProduct->screenshot as $item) {
                rename($tempProductLocation . '/' . $item, $mainProductLocation . '/' . $item);
            }

            $pScreenshot = $tempProduct->screenshot;
        }else{
            $pScreenshot = $mainProduct->screenshot;
        }

        $mainProduct->status = 1;
        $mainProduct->regular_price = $tempProduct->regular_price;
        $mainProduct->extended_price = $tempProduct->extended_price;
        $mainProduct->support = $tempProduct->support;
        $mainProduct->support_charge = $tempProduct->support_charge;
        $mainProduct->support_discount = $tempProduct->support_discount;
        $mainProduct->name = $tempProduct->name;
        $mainProduct->screenshot = $pScreenshot;
        $mainProduct->demo_link = $tempProduct->demo_link;
        $mainProduct->description = $tempProduct->description;
        $mainProduct->tag = $tempProduct->tag;
        $mainProduct->message = $tempProduct->message;
        $mainProduct->category_details = $tempProduct->category_details;
        $mainProduct->save();

        $tempProduct->delete();

        notify($mainProduct->user, 'PRODUCT_APPROVED', [
            'product_name' => $mainProduct->name
        ]);

        $notify[] = ['success', 'Resubmisssion of this product has been approved successfully'];
        return redirect()->route('reviewer.dashboard')->withNotify($notify);
    }

    public function resubmitSoftReject(Request $request)
    {
        $request->validate([
            'id' => 'required|gt:0',
            'message' => 'required'
        ]);

        $tempProduct = TempProduct::where('type',1)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->where('product_id',$request->id)->first();

        $mainProduct = Product::where('id',$tempProduct->product_id)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->first();

        $mainProduct->status = 2;
        $mainProduct->soft_reject = $request->message;
        $mainProduct->save();

        if($tempProduct->image){

            $tempProductLocation = imagePath()['temp_p_image']['path'];
            removeFile($tempProductLocation . '/' . $tempProduct->image);
            removeFile($tempProductLocation . '/thumb_' . $tempProduct->image);
        }

        if($tempProduct->file != null){

            $tempProductLocation = imagePath()['temp_p_file']['path'];
            removeFile($tempProductLocation . '/' . $tempProduct->file);
        }

        if($tempProduct->screenshot){

            $tempProductLocation = imagePath()['temp_p_screenshot']['path'];

            foreach ($tempProduct->screenshot as $item) {
                removeFile($tempProductLocation . '/' . $item);
            }
        }

        $tempProduct->delete();

        notify($mainProduct->user, 'PRODUCT_SOFT_REJECT', [
            'product_name' => $mainProduct->name,
            'rejection_message' => $mainProduct->soft_reject,
        ]);

        $notify[] = ['success', 'Resubmission of this product has been softrejected successfully'];
        return redirect()->route('reviewer.dashboard')->withNotify($notify);
    }

    public function resubmitHardReject(Request $request)
    {
        $request->validate([
            'id' => 'required|gt:0',
            'message' => 'required'
        ]);

        $tempProduct = TempProduct::where('type',2)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->where('product_id',$request->id)->first();

        $mainProduct = Product::where('id',$tempProduct->product_id)->whereHas('user', function ($query) {
            $query->where('status',1);
        })->first();

        $mainProduct->status = 3;
        $mainProduct->hard_reject = $request->message;
        $mainProduct->save();

        if($tempProduct->image){

            $tempProductLocation = imagePath()['temp_p_image']['path'];
            removeFile($tempProductLocation . '/' . $tempProduct->image);
            removeFile($tempProductLocation . '/thumb_' . $tempProduct->image);
        }

        if($tempProduct->file != null){

            $tempProductLocation = imagePath()['temp_p_file']['path'];
            removeFile($tempProductLocation . '/' . $tempProduct->file);
        }

        if($tempProduct->screenshot){

            $tempProductLocation = imagePath()['temp_p_screenshot']['path'];

            foreach ($tempProduct->screenshot as $item) {
                removeFile($tempProductLocation . '/' . $item);
            }
        }

        $tempProduct->delete();

        notify($mainProduct->user, 'PRODUCT_HARD_REJECT', [
            'product_name' => $mainProduct->name
        ]);

        $notify[] = ['success', 'Resubmission of this product has been rejected successfully'];
        return redirect()->route('reviewer.dashboard')->withNotify($notify);
    }

    public function sellLog()
    {
        $page_title = 'Sell Logs';
        $empty_message = 'No data found';

        $sells = Sell::where('status',1)->with(['user','product','author'])->orderBy('id','desc')->paginate(getPaginate());

        return view('admin.sell_log',compact('page_title','sells','empty_message'));
    }

    public function sellSearch(Request $request)
    {
        $search = $request->search;
        $page_title = 'Sell Search - ' . $search;
        $empty_message = 'No data found';
        $sells = Sell::where('code',$search)->where('status',1)->paginate(getPaginate());

        return view('admin.sell_log', compact('page_title', 'sells', 'empty_message'));
    }
}
