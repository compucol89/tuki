<?php

namespace Tests\Feature;

use App\Models\Organizer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Integridad de datos de producción (production-data-policy):
 * cuentas con apariencia de QA y contenido demo no pueden ser públicos.
 * Replica los casos observados en el audit (organizador "dknfglsxzy").
 */
class ProductionDataIntegrityTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->buildMinimalSchema();

    $pdo = DB::connection()->getPdo();
    $pdo->sqliteCreateFunction('CHAR_LENGTH', fn ($value) => mb_strlen((string) $value), 1);
  }

  public function test_organizer_with_random_username_and_test_email_is_not_listable_even_with_complete_profile(): void
  {
    DB::table('organizers')->insert([
      'id' => 1,
      'username' => 'dknfglsxzy',
      'email' => 'fkpklzoo@immenseignite.info',
      'password' => 'x',
      'photo' => 'foto.jpg',
      'cover_photo' => 'portada.jpg',
      'email_verified_at' => now(),
      'website' => 'https://x.com',
    ]);
    DB::table('organizer_infos')->insert([
      'organizer_id' => 1,
      'language_id' => 8,
      'name' => 'dknfglsxzy',
      'country' => 'Argentina',
      'details' => str_repeat('Detalle del perfil con contenido suficiente. ', 5),
    ]);

    $this->assertEmpty(Organizer::listable()->pluck('id')->all());
  }

  public function test_demo_blog_posts_are_purged_from_database(): void
  {
    DB::table('blogs')->insert(['id' => 1, 'image' => 'demo.jpg', 'serial_number' => 1]);
    DB::table('blog_informations')->insert([
      'id' => 1, 'blog_id' => 1, 'language_id' => 8, 'blog_category_id' => 36,
      'title' => 'Demo', 'slug' => 'vivamus-vestibulum-demo', 'content' => 'x',
    ]);

    Artisan::call('blog:purge-demo');

    $this->assertDatabaseMissing('blog_informations', ['id' => 1]);
    $this->assertDatabaseMissing('blogs', ['id' => 1]);
  }

  private function buildMinimalSchema(): void
  {
    foreach (['organizers', 'organizer_infos', 'blogs', 'blog_informations'] as $table) {
      Schema::dropIfExists($table);
    }

    Schema::create('organizers', function ($t) {
      $t->id();
      $t->string('username');
      $t->string('email');
      $t->string('password');
      $t->string('photo')->nullable();
      $t->string('cover_photo')->nullable();
      $t->timestamp('email_verified_at')->nullable();
      $t->string('website')->nullable();
      $t->string('instagram')->nullable();
      $t->string('tiktok')->nullable();
      $t->string('facebook')->nullable();
      $t->string('twitter')->nullable();
      $t->string('linkedin')->nullable();
      $t->timestamps();
    });
    Schema::create('organizer_infos', function ($t) {
      $t->id();
      $t->integer('organizer_id')->nullable();
      $t->integer('language_id')->nullable();
      $t->string('name')->nullable();
      $t->string('country')->nullable();
      $t->string('city')->nullable();
      $t->string('state')->nullable();
      $t->text('address')->nullable();
      $t->longText('details')->nullable();
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
      $t->text('content')->nullable();
      $t->timestamps();
    });
  }
}
