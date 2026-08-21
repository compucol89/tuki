<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * F-007/F-008 SEO — contratos mínimos de metadata por página pública:
 * canonical autocanónico en el layout, un único h1 por vista clave.
 */
class PublicSeoMetadataTest extends TestCase
{
  public function test_layout_emits_self_referencing_canonical(): void
  {
    $layout = (string) File::get(base_path('resources/views/frontend/layout.blade.php'));

    $this->assertStringContainsString("yieldContent('canonical')", $layout);
    $this->assertStringContainsString('<link rel="canonical" href="{{ $canonicalUrl }}"', $layout);
    $this->assertStringContainsString('url()->current()', $layout);
  }

  public function test_key_public_pages_have_exactly_one_h1(): void
  {
    $views = [
      'home/index-v1.blade.php',
      'organizer/index.blade.php',
      'about.blade.php',
      'event/event.blade.php',
      'journal/blogs.blade.php',
      'contact.blade.php',
      'faqs.blade.php',
    ];

    foreach ($views as $view) {
      $path = base_path('resources/views/frontend/' . $view);
      $this->assertTrue(File::exists($path), "Falta la vista {$view}");

      $content = (string) File::get($path);
      $this->assertSame(
        1,
        preg_match_all('/<h1[\s>]/', $content),
        "La vista {$view} debe tener exactamente un <h1>"
      );
    }
  }

  public function test_blog_json_ld_matches_visible_posts(): void
  {
    $blogs = (string) File::get(base_path('resources/views/frontend/journal/blogs.blade.php'));

    $this->assertStringContainsString("'@type' => 'BlogPosting'", $blogs);
    $this->assertStringContainsString('$visibleBlogs', $blogs);
    $this->assertStringNotContainsString('$demoBlogSlugs', $blogs);
  }
}
