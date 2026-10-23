<?php

namespace App\Services;

use App\Support\EventGalleryImageValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class FileUploadService
{
  public function store(string $directory, UploadedFile $file)
  {
    $extension = EventGalleryImageValidator::resolveStorageExtension($file);
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
    $src = EventGalleryImageValidator::loadImageResource($path, $extension);
    if ($src === false) {
      $src = null;
    }

    if (!$src) {
      Log::warning('FileUploadService: could not read image for optimization.', ['path' => $path, 'extension' => $extension]);
      return;
    }

    // Convert palette-based images (e.g. 8-bit PNG) to truecolor for WebP compatibility
    if (function_exists('imageistruecolor') && !imageistruecolor($src)) {
      imagepalettetotruecolor($src);
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

  public function createResponsiveWebp(string $path, string $extension, int $maxWidth = 1920, int $quality = 80): bool
  {
    if (!function_exists('imagewebp')) {
      Log::warning('FileUploadService: WebP support is not available.', ['path' => $path]);

      return false;
    }

    $src = EventGalleryImageValidator::loadImageResource($path, $extension);
    if ($src === false) {
      Log::warning('FileUploadService: could not read image for WebP conversion.', ['path' => $path, 'extension' => $extension]);

      return false;
    }

    if (function_exists('imageistruecolor') && !imageistruecolor($src)) {
      imagepalettetotruecolor($src);
    }

    $width = imagesx($src);
    $height = imagesy($src);

    if ($width <= 0 || $height <= 0) {
      imagedestroy($src);

      return false;
    }

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

    $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $path);
    $converted = is_string($webpPath) && imagewebp($src, $webpPath, $quality);

    imagedestroy($src);

    return $converted;
  }

  public static function imageUrl(string $relativeDir, string $filename): string
  {
    $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filename);
    if ($webp !== $filename && file_exists(public_path($relativeDir . $webp))) {
      return asset($relativeDir . $webp);
    }
    return asset($relativeDir . $filename);
  }

  public static function imagePath(string $relativeDir, string $filename): string
  {
    $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filename);
    $path = ($webp !== $filename && file_exists(public_path($relativeDir . $webp)))
      ? $relativeDir . $webp
      : $relativeDir . $filename;

    return '/' . ltrim($path, '/');
  }

  public static function imageExists(string $relativeDir, string $filename): bool
  {
    if ($filename === '') {
      return false;
    }
    $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filename);
    if ($webp !== $filename && file_exists(public_path($relativeDir . $webp))) {
      return true;
    }

    return file_exists(public_path($relativeDir . $filename));
  }

  /**
   * Devuelve una variante WebP redimensionada, generándola una sola vez si falta.
   * Si GD/WebP no está disponible o el archivo no existe, devuelve la imagen original.
   */
  public static function responsiveImageUrl(string $relativeDir, string $filename, int $width, int $quality = 80): string
  {
    $filename = basename(trim($filename));
    if ($filename === '' || $filename === 'noimage.jpg') {
      return asset('assets/admin/img/noimage.jpg');
    }

    $relativeDir = rtrim($relativeDir, '/') . '/';
    $webpName = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filename);
    $sourceName = ($webpName !== $filename && is_file(public_path($relativeDir . $webpName))) ? $webpName : $filename;
    $sourcePath = public_path($relativeDir . $sourceName);

    if (!is_file($sourcePath)) {
      return asset($relativeDir . $filename);
    }

    $sourceInfo = @getimagesize($sourcePath);
    if (!$sourceInfo || (int) $sourceInfo[0] <= $width) {
      return self::imageUrl($relativeDir, $filename);
    }

    $variantName = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $filename) . '-' . $width . '.webp';
    $variantPath = public_path($relativeDir . $variantName);

    if (!is_file($variantPath)) {
      self::generateResponsiveWebp($sourcePath, $variantPath, $width, $quality);
    }

    return is_file($variantPath) ? asset($relativeDir . $variantName) : self::imageUrl($relativeDir, $filename);
  }

  private static function generateResponsiveWebp(string $sourcePath, string $targetPath, int $width, int $quality): bool
  {
    if (!function_exists('imagewebp')) {
      return false;
    }

    $info = @getimagesize($sourcePath);
    if (!$info) {
      return false;
    }

    $source = match ($info['mime'] ?? '') {
      'image/jpeg' => @imagecreatefromjpeg($sourcePath),
      'image/png' => @imagecreatefrompng($sourcePath),
      'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
      default => false,
    };

    if (!$source) {
      return false;
    }

    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);
    $targetWidth = min($width, $sourceWidth);
    $targetHeight = (int) round($sourceHeight * ($targetWidth / $sourceWidth));
    $target = imagecreatetruecolor($targetWidth, $targetHeight);

    if (($info['mime'] ?? '') !== 'image/jpeg') {
      imagealphablending($target, false);
      imagesavealpha($target, true);
    }

    imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

    @mkdir(dirname($targetPath), 0775, true);
    $temporaryPath = $targetPath . '.tmp.' . getmypid() . '.' . uniqid();
    $saved = imagewebp($target, $temporaryPath, $quality);

    imagedestroy($source);
    imagedestroy($target);

    if (!$saved) {
      @unlink($temporaryPath);

      return false;
    }

    if (!@rename($temporaryPath, $targetPath)) {
      @unlink($temporaryPath);

      return false;
    }

    return true;
  }

  public static function eventCardUrl(?string $thumbnail, int $width = 400): string
  {
    $thumbnail = trim((string) $thumbnail);
    if ($thumbnail === '' || !self::imageExists('assets/admin/img/event/thumbnail/', $thumbnail)) {
      return asset('assets/admin/img/noimage.jpg');
    }

    return self::responsiveImageUrl('assets/admin/img/event/thumbnail/', $thumbnail, $width);
  }

  /**
   * @param iterable<object{image?: string|null}>|null $galleryImages
   * @return array{dir: string, name: string}
   */
  public static function eventVisualData(?iterable $galleryImages, ?string $thumbnail): array
  {
    if ($galleryImages) {
      foreach ($galleryImages as $row) {
        $name = $row->image ?? '';
        if (self::imageExists('assets/admin/img/event-gallery/', $name)) {
          return ['dir' => 'assets/admin/img/event-gallery/', 'name' => $name];
        }
      }
    }

    if ($thumbnail && self::imageExists('assets/admin/img/event/thumbnail/', $thumbnail)) {
      return ['dir' => 'assets/admin/img/event/thumbnail/', 'name' => $thumbnail];
    }

    return ['dir' => 'assets/admin/img/', 'name' => 'noimage.jpg'];
  }

  /**
   * @param iterable<object{image?: string|null}>|null $galleryImages
   */
  public static function eventVisualUrl(?iterable $galleryImages, ?string $thumbnail): string
  {
    $visual = self::eventVisualData($galleryImages, $thumbnail);

    return self::imageUrl($visual['dir'], $visual['name']);
  }

  /**
   * @param iterable<object{image?: string|null}>|null $galleryImages
   */
  public static function eventVisualResponsiveUrl(?iterable $galleryImages, ?string $thumbnail, int $width, int $quality = 80): string
  {
    $visual = self::eventVisualData($galleryImages, $thumbnail);

    return self::responsiveImageUrl($visual['dir'], $visual['name'], $width, $quality);
  }
}
