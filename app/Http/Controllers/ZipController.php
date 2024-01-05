<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
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
        $data['page_title'] = "Profile Setting";
        $data['user'] = auth()->user() ?? auth('user')->user();
        $data['user']->base_url = url('/assets/images/user/profile/');
        if ($request->is('api/*')) {
            return $this->respondWithSuccess($data['user'], 'User Profile');
        }
        return view($this->activeTemplate . 'user.profile-setting', $data);
        
        $page_title = 'Upload and Extract Zip Files';
        return view('templates.basic.user.build.index', get_defined_vars());
    }

    public function uploadAndExtract(Request $request)
    {
        $request->validate([
            'zipFile' => 'required|mimes:zip|max:10240', // Adjust max file size as needed
            'destinationFolder' => 'required|string',
        ]);

        $zipFile = $request->file('zipFile');
        $destinationFolder = $request->input('destinationFolder');

        // Extract the contents of the zip file into the selected folder
        $extractPath = storage_path("app/public/uploads/{$destinationFolder}");
        $zip = new ZipArchive;

        if ($zip->open($zipFile->path()) === true) {
            $zip->extractTo($extractPath);
            $zip->close();

            return redirect()->route('upload.form')->with('success', 'Zip file uploaded and extracted successfully.');
        }

        return redirect()->route('upload.form')->with('error', 'Failed to extract the zip file.');
    }
}
