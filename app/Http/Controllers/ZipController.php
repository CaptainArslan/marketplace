<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
            'file' => 'required|mimes:zip|max:10240', // Adjust max file size as needed
        ]);

        $destinationFolder = $request->app_type;

        // Upload the file
        $uploadedFile = $request->file('file');
        $rand = Str::random(6);

        // Save the uploaded file
        $storagePath = 'uploads/' . $rand;
        $uploadedFilePath = $uploadedFile->storeAs($storagePath, $uploadedFile->getClientOriginalName(), 'public');
        Log::info($uploadedFilePath);

        // Check if the file has been saved successfully
        if (!$uploadedFilePath) {
            return back()->withNotify(['error', 'Failed to save the uploaded file.']);
        }

        // Now, read the file and extract it
        $zip = new ZipArchive;
        if ($zip->open(storage_path("app/public/{$uploadedFilePath}")) === true) {
            $extractPath = public_path("uploads/{$destinationFolder}");
            $extractPath = dirname(dirname(dirname($extractPath)));
            Log::info($extractPath);
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0777, true);
            }
            // Extract to the specified directory
            $zip->extractTo($extractPath);
            $zip->close();

            // Move all files from the extracted folder to the outer folder
            $extractedFiles = File::allFiles($extractPath);
            foreach ($extractedFiles as $file) {
                File::move($file->getPathname(), $extractPath . '/' . $file->getFilename());
            }

            // Delete the temporary uploaded zip file
            Storage::disk('public')->delete($uploadedFilePath);

            return back()->withNotify(['success', 'Zip file uploaded and extracted successfully.']);
        }

        // Delete the temporary uploaded zip file in case of extraction failure
        // Storage::disk('public')->delete($uploadedFilePath);

        return back()->withNotify(['error', 'Failed to extract the zip file.']);
    }
}
