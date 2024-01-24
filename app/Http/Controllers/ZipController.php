<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

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


    function uploadZip(Request $request)
    {
        try {
            $server = 0;
            $pFile = '';
            $general = GeneralSetting::first();
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
                    } catch (\Exception $e) {
                        return response()->json(['errors' => 'Could not upload the Video']);
                    }
                    $server = 1;
                }

                return response()->json(['status' => true, 'message' => 'File Uploaded Successfully', 'file' => $pFile, 'server' => $server]);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error occurred while uploading file']);
        }
    }
}
