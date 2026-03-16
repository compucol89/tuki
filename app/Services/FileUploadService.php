<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

class FileUploadService
{
  public function store(string $directory, UploadedFile $file)
  {
    $extension = $file->getClientOriginalExtension();
    $originalName = null;

    if (
      Route::is('admin.course_management.lesson.upload_video') ||
      Route::is('admin.course_management.lesson.upload_file')
    ) {
      $originalName = $file->getClientOriginalName();
    }

    $fileName = uniqid() . '.' . $extension;
    @mkdir($directory, 0775, true);
    $file->move($directory, $fileName);

    if (Route::is('admin.course_management.lesson.upload_video')) {
      $getID3 = new \getID3();
      $fileInfo = $getID3->analyze($directory . $fileName);
      $duration = date('H:i:s', $fileInfo['playtime_seconds']);

      return [
        'originalName' => $originalName,
        'uniqueName' => $fileName,
        'duration' => $duration,
      ];
    }

    if (Route::is('admin.course_management.lesson.upload_file')) {
      return [
        'originalName' => $originalName,
        'uniqueName' => $fileName,
      ];
    }

    return $fileName;
  }

  public function update(string $directory, UploadedFile $newFile, string $oldFile)
  {
    @unlink($directory . $oldFile);

    return $this->store($directory, $newFile);
  }
}
