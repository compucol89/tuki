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

    $this->assertStringEndsWith('/assets/admin/img/event/thumbnail/test-social-thumb.png', $socialImage['url']);
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

    $this->assertStringEndsWith('/assets/admin/img/event-ai/121/test-social-og.jpg', $socialImage['url']);
    $this->assertSame(1200, $socialImage['width']);
    $this->assertSame(630, $socialImage['height']);
    $this->assertSame('image/jpeg', $socialImage['type']);
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

  private function ensureDirectory(string $path): void
  {
    if (!is_dir(dirname($path))) {
      mkdir(dirname($path), 0775, true);
    }
  }
}
