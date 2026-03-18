<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SummernoteController extends Controller
{
  public function upload(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $imageName = UploadFile::store(public_path('assets/admin/img/summernotes/'), $request->file('image'));

    return url('/') . '/assets/admin/img/summernotes/' . $imageName;
  }

  public function remove(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'image' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $imageName = basename($request->image);
    $imagePath = public_path('assets/admin/img/summernotes/' . $imageName);

    if (is_file($imagePath)) {
      @unlink($imagePath);
    }

    return response()->json(['data' => 'Image removed successfully!'], 200);
  }
}
