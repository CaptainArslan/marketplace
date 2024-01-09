<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ZipController extends Controller
{
    public $activeTemplate;

    public function construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function index(Request $request)
    {
        $page_title = "Profile Setting";
        return view('templates.basic.user.build.index', get_defined_vars());
    }

    public function uploadAndExtract(Request $request)
    {
        $request->validate([
            'app_type' => 'required|string|in:live,beta',
            'file' => 'required|mimes:zip', // Adjust max file size as needed
        ]);
        // Upload the file
        $uploadedFile = $request->file('file');
        // Save the uploaded file
        $storagePath = storage_path('app\public\uploads');
        // $uploadedFilePath = $uploadedFile->storeAs($storagePath, $uploadedFile->getClientOriginalName(), 'public');
        $uploadedFilePath = uploadFile($uploadedFile, $storagePath);
        // Check if the file has been saved successfully
        if (!$uploadedFilePath) {
            return back()->withNotify(['error', 'Failed to save the uploaded file.']);
        }
        $uploadedFilePath = storage_path("app/public/uploads/{$uploadedFilePath}");
        // Now, read the file and extract it
        $zip = new ZipArchive;
        if ($zip->open($uploadedFilePath) === true) {
            if (env('APP_ENV') == 'production') {
                $extractPath = ($request->app_type == 'live') ? '/home/marketplacejdfun/public_html' : '/home/marketplacejdfun/staging_app';
            } else {
                $extractPath = ($request->app_type == 'live') ? 'C:\xampp\htdocs\paths\live' : 'C:\xampp\htdocs\paths\beta';
            }

            Log::info($extractPath);
            $zip->extractTo($extractPath);
            $zip->close();

            // Delete the temporary uploaded zip file
            // Storage::disk('public')->delete($uploadedFilePath);
            $notify[] = ['success', 'Zip file uploaded and extracted successfully.'];

            return back()->withNotify($notify);
        }

        // Delete the temporary uploaded zip file in case of extraction failure
        // Storage::disk('public')->delete($uploadedFilePath);

        return back()->withNotify(['error', 'Failed to extract the zip file.']);
    }
}
