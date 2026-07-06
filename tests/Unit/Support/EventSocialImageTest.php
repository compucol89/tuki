<?php

namespace Tests\Unit\Support;

use App\Support\EventSocialImage;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EventSocialImageTest extends TestCase
{
  private array $files = [];

  protected function tearDown(): void
  {
    foreach ($this->files as $file) {
      @unlink($file);
    }

    foreach (glob(public_path('assets/admin/img/event-social/122/*')) ?: [] as $file) {
      @unlink($file);
    }
    @rmdir(public_path('assets/admin/img/event-social/122'));

    parent::tearDown();
  }

  public function test_prefers_thumbnail_before_gallery_and_reports_real_metadata(): void
  {
    $thumbnail = public_path('assets/admin/img/event/thumbnail/test-social-thumb.png');
    $gallery = public_path('assets/admin/img/event-gallery/test-social-gallery.jpg');
    $this->makePng($thumbnail, 320, 640);
    $this->makeJpeg($gallery, 1200, 630);

    $event = (object) [
      'id' => 121,
      'og_image' => null,
      'thumbnail' => basename($thumbnail),
    ];
    $images = new Collection([(object) ['image' => basename($gallery)]]);

    $socialImage = EventSocialImage::from($event, $images);

    $this->assertStringContainsString('/assets/admin/img/event/thumbnail/test-social-thumb.png?v=', $socialImage['url']);
    $this->assertSame(320, $socialImage['width']);
    $this->assertSame(640, $socialImage['height']);
    $this->assertSame('image/png', $socialImage['type']);
  }

  public function test_prefers_generated_og_image_before_thumbnail(): void
  {
    $og = public_path('assets/admin/img/event-ai/121/test-social-og.jpg');
    $thumbnail = public_path('assets/admin/img/event/thumbnail/test-social-thumb.png');
    $this->makeJpeg($og, 1200, 630);
    $this->makePng($thumbnail, 320, 640);

    $event = (object) [
      'id' => 121,
      'og_image' => basename($og),
      'thumbnail' => basename($thumbnail),
    ];

    $socialImage = EventSocialImage::from($event, new Collection());

    $this->assertStringContainsString('/assets/admin/img/event-ai/121/test-social-og.jpg?v=', $socialImage['url']);
    $this->assertSame(1200, $socialImage['width']);
    $this->assertSame(630, $socialImage['height']);
    $this->assertSame('image/jpeg', $socialImage['type']);
  }

  public function test_large_png_thumbnail_uses_optimized_social_jpeg(): void
  {
    $thumbnail = public_path('assets/admin/img/event/thumbnail/test-social-heavy.png');
    $this->makeNoisyPng($thumbnail, 900, 900);

    $event = (object) [
      'id' => 122,
      'og_image' => null,
      'thumbnail' => basename($thumbnail),
    ];

    $socialImage = EventSocialImage::from($event, new Collection());
    $path = public_path(parse_url($socialImage['url'], PHP_URL_PATH));

    $this->assertStringContainsString('/assets/admin/img/event-social/122/', $socialImage['url']);
    $this->assertStringEndsWith('.jpg?v=' . filemtime($path), $socialImage['url']);
    $this->assertSame('image/jpeg', $socialImage['type']);
    $this->assertLessThanOrEqual(300000, filesize($path));
  }

  private function makePng(string $path, int $width, int $height): void
  {
    $this->ensureDirectory($path);
    $image = imagecreatetruecolor($width, $height);
    imagepng($image, $path);
    imagedestroy($image);
    $this->files[] = $path;
  }

  private function makeJpeg(string $path, int $width, int $height): void
  {
    $this->ensureDirectory($path);
    $image = imagecreatetruecolor($width, $height);
    imagejpeg($image, $path);
    imagedestroy($image);
    $this->files[] = $path;
  }

  private function makeNoisyPng(string $path, int $width, int $height): void
  {
    $this->ensureDirectory($path);
    $image = imagecreatetruecolor($width, $height);

    for ($x = 0; $x < $width; $x++) {
      for ($y = 0; $y < $height; $y++) {
        $color = imagecolorallocate($image, ($x * 17 + $y * 7) % 256, ($x * 5 + $y * 13) % 256, ($x * 11 + $y * 3) % 256);
        imagesetpixel($image, $x, $y, $color);
      }
    }

    imagepng($image, $path, 0);
    imagedestroy($image);
    $this->files[] = $path;
  }

  private function ensureDirectory(string $path): void
  {
    if (!is_dir(dirname($path))) {
      mkdir(dirname($path), 0775, true);
    }
  }
}
