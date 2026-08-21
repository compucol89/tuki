<?php

namespace Tests\Feature;

use App\Http\Controllers\FrontEnd\BlogController;
use App\Models\Journal\BlogCategory;
use App\Models\Journal\BlogInformation;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * F-004 — los contadores de categoría del blog deben usar la misma política
 * que el listado (mismo idioma), para que "contador visible == resultados".
 */
class BlogCategoryCountsTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->buildMinimalSchema();
  }

  public function test_category_counts_are_scoped_to_active_language(): void
  {
    DB::table('languages')->insert(['id' => 1, 'is_default' => 1]);
    DB::table('languages')->insert(['id' => 2, 'is_default' => 0]);

    DB::table('blog_categories')->insert([
      ['id' => 1, 'language_id' => 1, 'name' => 'Business', 'slug' => 'business', 'status' => 1, 'serial_number' => 1],
      ['id' => 2, 'language_id' => 1, 'name' => 'Wedding', 'slug' => 'wedding', 'status' => 1, 'serial_number' => 2],
    ]);

    DB::table('blogs')->insert([
      ['id' => 1, 'image' => 'a.jpg', 'serial_number' => 1],
      ['id' => 2, 'image' => 'b.jpg', 'serial_number' => 2],
      ['id' => 3, 'image' => 'c.jpg', 'serial_number' => 3],
    ]);

    DB::table('blog_informations')->insert([
      ['id' => 1, 'blog_id' => 1, 'language_id' => 1, 'blog_category_id' => 1, 'title' => 'Post A', 'slug' => 'post-a', 'content' => 'x'],
      ['id' => 2, 'blog_id' => 2, 'language_id' => 1, 'blog_category_id' => 1, 'title' => 'Post B', 'slug' => 'post-b', 'content' => 'x'],
      ['id' => 3, 'blog_id' => 3, 'language_id' => 1, 'blog_category_id' => 2, 'title' => 'Post C', 'slug' => 'post-c', 'content' => 'x'],
      ['id' => 4, 'blog_id' => 3, 'language_id' => 2, 'blog_category_id' => 2, 'title' => 'Post C EN', 'slug' => 'post-c-en', 'content' => 'x'],
    ]);

    $language = Language::find(1);
    $categories = app(BlogController::class)->getCategories($language);

    $counts = $categories->mapWithKeys(fn ($c) => [$c->slug => $c->blogCount])->all();

    $this->assertSame(2, $counts['business']);
    $this->assertSame(1, $counts['wedding']);
  }

  private function buildMinimalSchema(): void
  {
    foreach (['languages', 'blog_categories', 'blogs', 'blog_informations'] as $table) {
      Schema::dropIfExists($table);
    }

    Schema::create('languages', function ($t) {
      $t->id();
      $t->boolean('is_default')->default(false);
      $t->string('code')->nullable();
    });
    Schema::create('blog_categories', function ($t) {
      $t->id();
      $t->integer('language_id')->nullable();
      $t->string('name')->nullable();
      $t->string('slug')->nullable();
      $t->integer('status')->default(1);
      $t->integer('serial_number')->nullable();
      $t->timestamps();
    });
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
