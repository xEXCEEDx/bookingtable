<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function showUploadForm()
    {
        return view('imagepage');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete previous images
        Image::truncate();

        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(public_path('images'), $imageName);

        // Save image path to database
        $image = new Image();
        $image->imgbooking = $imageName;
        $image->save();

        return back()
            ->with('success', 'Image uploaded successfully.')
            ->with('image', $imageName);
    }
}
