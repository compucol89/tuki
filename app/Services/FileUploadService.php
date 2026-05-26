<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class FileUploadService
{
  public function store(string $directory, UploadedFile $file)
  {
    $extension = strtolower($file->getClientOriginalExtension());
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

    if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
      $this->optimizeImage($directory . $fileName, $extension);
    }

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

    $webpOld = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $oldFile);
    if ($webpOld !== $oldFile) {
      @unlink($directory . $webpOld);
    }

    return $this->store($directory, $newFile);
  }

  private function optimizeImage(string $path, string $extension): void
  {
    $src = match ($extension) {
      'png' => @imagecreatefrompng($path),
      'jpg', 'jpeg' => @imagecreatefromjpeg($path),
      'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
      default => null,
    };

    if (!$src) {
      Log::warning('FileUploadService: could not read image for optimization.', ['path' => $path, 'extension' => $extension]);
      return;
    }

    $width = imagesx($src);
    $height = imagesy($src);
    $maxWidth = 1200;

    if ($width > $maxWidth) {
      $newWidth = $maxWidth;
      $newHeight = (int) round($height * ($maxWidth / $width));
      $dst = imagecreatetruecolor($newWidth, $newHeight);
      if ($extension === 'png') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
      }
      imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
      imagedestroy($src);
      $src = $dst;
    }

    if ($extension === 'png') {
      imagepng($src, $path, 6);
    } elseif ($extension === 'webp' && function_exists('imagewebp')) {
      imagewebp($src, $path, 82);
    } else {
      imagejpeg($src, $path, 85);
    }

    if (function_exists('imagewebp') && $extension !== 'webp') {
      $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $path);
      imagewebp($src, $webpPath, 80);
    }

    imagedestroy($src);
  }

  public function convertToWebp(string $path, string $extension): void
  {
    $this->optimizeImage($path, $extension);
  }

  public static function imageUrl(string $relativeDir, string $filename): string
  {
    $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filename);
    if ($webp !== $filename && file_exists(public_path($relativeDir . $webp))) {
      return asset($relativeDir . $webp);
    }
    return asset($relativeDir . $filename);
  }
}
