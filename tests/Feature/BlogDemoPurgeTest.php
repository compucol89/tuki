<?php

namespace Tests\Feature;

use App\Models\Journal\Blog;
use App\Models\Journal\BlogInformation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * F-004 — los posts demo del seed no pueden volver al listado público:
 * el comando blog:purge-demo los elimina con backup y permite restaurar.
 */
class BlogDemoPurgeTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    Storage::fake('local');
    $this->buildMinimalSchema();
  }

  public function test_purge_removes_demo_posts_and_keeps_real_ones(): void
  {
    Blog::create(['id' => 1, 'image' => 'demo.jpg', 'serial_number' => 1]);
    Blog::create(['id' => 2, 'image' => 'real.jpg', 'serial_number' => 2]);
    BlogInformation::create(['id' => 1, 'blog_id' => 1, 'language_id' => 8, 'blog_category_id' => 36, 'title' => 'Demo', 'slug' => 'vivamus-vestibulum-demo', 'content' => 'x']);
    BlogInformation::create(['id' => 2, 'blog_id' => 2, 'language_id' => 8, 'blog_category_id' => 36, 'title' => 'Real', 'slug' => 'evento-real', 'content' => 'x']);

    $exit = Artisan::call('blog:purge-demo');

    $this->assertSame(0, $exit);
    $this->assertDatabaseMissing('blog_informations', ['id' => 1]);
    $this->assertDatabaseMissing('blogs', ['id' => 1]);
    $this->assertDatabaseHas('blog_informations', ['id' => 2]);
    $this->assertDatabaseHas('blogs', ['id' => 2]);
    $this->assertNotEmpty(Storage::disk('local')->files('backups'), 'Debe existir el backup JSON');
  }

  public function test_dry_run_does_not_delete(): void
  {
    Blog::create(['id' => 1, 'image' => 'demo.jpg', 'serial_number' => 1]);
    BlogInformation::create(['id' => 1, 'blog_id' => 1, 'language_id' => 8, 'blog_category_id' => 36, 'title' => 'Demo', 'slug' => 'morbi-in-sem-demo', 'content' => 'x']);

    Artisan::call('blog:purge-demo', ['--dry-run' => true]);

    $this->assertDatabaseHas('blog_informations', ['id' => 1]);
  }

  public function test_restore_reinserts_purged_posts(): void
  {
    Blog::create(['id' => 1, 'image' => 'demo.jpg', 'serial_number' => 1]);
    BlogInformation::create(['id' => 1, 'blog_id' => 1, 'language_id' => 8, 'blog_category_id' => 36, 'title' => 'Demo', 'slug' => 'phasellus-ultrices-demo', 'content' => 'x']);

    Artisan::call('blog:purge-demo');
    $backup = head(Storage::disk('local')->files('backups'));

    Artisan::call('blog:purge-demo', ['--restore' => $backup]);

    $this->assertDatabaseHas('blogs', ['id' => 1]);
    $this->assertDatabaseHas('blog_informations', ['id' => 1]);
  }

  private function buildMinimalSchema(): void
  {
    foreach (['blogs', 'blog_informations'] as $table) {
      Schema::dropIfExists($table);
    }

    Schema::create('blogs', function ($t) {
      $t->id();
      $t->string('image')->nullable();
      $t->integer('serial_number')->nullable();
      $t->timestamps();
    });
    Schema::create('blog_informations', function ($t) {
      $t->id();
      $t->integer('blog_id')->nullable();
      $t->integer('language_id')->nullable();
      $t->integer('blog_category_id')->nullable();
      $t->string('title')->nullable();
      $t->string('slug')->nullable();
      $t->string('author')->nullable();
      $t->text('content')->nullable();
      $t->string('meta_keywords')->nullable();
      $t->string('meta_description')->nullable();
      $t->timestamps();
    });
  }
}
