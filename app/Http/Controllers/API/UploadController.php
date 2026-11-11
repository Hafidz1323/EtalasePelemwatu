<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['status' => 'error', 'message' => 'No file uploaded'], 400);
        }

        $file = $request->file('image');
        $filename = 'image-' . time() . '-' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded',
            'path' => '/uploads/' . $filename,
        ]);
    }
}

