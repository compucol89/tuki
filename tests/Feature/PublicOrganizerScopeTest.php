<?php

namespace Tests\Feature;

use App\Models\Organizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * F-005 — política pública de organizadores (listable relajado):
 * perfil completo + email verificado, SIN exigir evento publicado ya
 * realizado. Cuentas de prueba (test@/example) quedan fuera.
 */
class PublicOrganizerScopeTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->buildMinimalSchema();

    $pdo = DB::connection()->getPdo();
    $pdo->sqliteCreateFunction('CHAR_LENGTH', fn ($value) => mb_strlen((string) $value), 1);
  }

  public function test_complete_profile_is_listable_without_past_event(): void
  {
    $this->insertOrganizer(1, 'organizador-real', 'real@dominio.com', true);
    $this->insertInfo(1, 'Organizador Real', 'Argentina', 'CABA');

    $ids = Organizer::listable()->pluck('id')->all();

    $this->assertSame([1], $ids);
  }

  public function test_profile_without_verified_email_is_not_listable(): void
  {
    $this->insertOrganizer(1, 'sin-email', 'x@dominio.com', false);
    $this->insertInfo(1, 'Sin Email', 'Argentina', 'CABA');

    $this->assertEmpty(Organizer::listable()->pluck('id')->all());
  }

  public function test_profile_without_social_network_is_not_listable(): void
  {
    $this->insertOrganizer(1, 'sin-redes', 'real@dominio.com', true, ['website' => null]);
    $this->insertInfo(1, 'Sin Redes', 'Argentina', 'CABA');

    $this->assertEmpty(Organizer::listable()->pluck('id')->all());
  }

  public function test_incomplete_info_is_not_listable(): void
  {
    $this->insertOrganizer(1, 'info-corta', 'real@dominio.com', true);
    DB::table('organizer_infos')->insert([
      'organizer_id' => 1, 'language_id' => 8, 'name' => 'Info Corta',
      'country' => 'Argentina', 'details' => 'muy corto',
    ]);

    $this->assertEmpty(Organizer::listable()->pluck('id')->all());
  }

  public function test_test_accounts_are_not_listable(): void
  {
    $this->insertOrganizer(1, 'qa-account', 'qa@test.com', true);
    $this->insertInfo(1, 'QA Account', 'Argentina', 'CABA');
    $this->insertOrganizer(2, 'example-account', 'user@example.com', true);
    $this->insertInfo(2, 'Example Account', 'Argentina', 'CABA');

    $this->assertEmpty(Organizer::listable()->pluck('id')->all());
  }

  private function insertOrganizer(int $id, string $username, string $email, bool $verified, array $overrides = []): void
  {
    DB::table('organizers')->insert(array_merge([
      'id' => $id,
      'username' => $username,
      'email' => $email,
      'password' => 'x',
      'photo' => 'foto.jpg',
      'cover_photo' => 'portada.jpg',
      'email_verified_at' => $verified ? now() : null,
      'website' => 'https://dominio.com',
    ], $overrides));
  }

  private function insertInfo(int $organizerId, string $name, string $country, string $city): void
  {
    DB::table('organizer_infos')->insert([
      'organizer_id' => $organizerId,
      'language_id' => 8,
      'name' => $name,
      'country' => $country,
      'city' => $city,
      'details' => str_repeat('Detalle del perfil del organizador con contenido suficiente. ', 5),
    ]);
  }

  private function buildMinimalSchema(): void
  {
    foreach (['organizers', 'organizer_infos'] as $table) {
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
  }
}
