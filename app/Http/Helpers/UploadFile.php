<?php

namespace App\Http\Helpers;

use App\Services\FileUploadService;

class UploadFile
{
  public static function store($directory, $file)
  {
    return app(FileUploadService::class)->store($directory, $file);
  }

  public static function update($directory, $newFile, $oldFile)
  {
    return app(FileUploadService::class)->update($directory, $newFile, $oldFile);
  }
}
