<?php

namespace Tests\Feature\Controllers\Organizer;

use App\Jobs\GenerateAiImageJob;
use App\Models\Event;
use App\Models\Event\EventAiGeneration;
use App\Models\Organizer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventAiImageControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['features.ai_images_enabled' => true]);
        config(['openai.api_key' => 'sk-test-fake']);

        $this->setUpSchema();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('event_ai_generations');
        Schema::dropIfExists('events');
        Schema::dropIfExists('organizers');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('basic_settings');

        parent::tearDown();
    }

    private function setUpSchema(): void
    {
        if (!Schema::hasTable('organizers')) {
            Schema::create('organizers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('photo')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('linkedin')->nullable();
                $table->string('remember_token')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('email_verification_token')->nullable();
                $table->timestamp('email_verification_sent_at')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('organizer_id')->nullable();
                $table->string('thumbnail')->nullable();
                $table->string('og_image')->nullable();
                $table->text('ai_metadata')->nullable();
                $table->string('status')->nullable();
                $table->string('countdown_status')->nullable();
                $table->string('date_type')->nullable();
                $table->date('start_date')->nullable();
                $table->string('start_time')->nullable();
                $table->string('duration')->nullable();
                $table->date('end_date')->nullable();
                $table->string('end_time')->nullable();
                $table->string('end_date_time')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->string('event_type')->nullable();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->string('ticket_image')->nullable();
                $table->text('instructions')->nullable();
                $table->string('meeting_url')->nullable();
                $table->string('ticket_logo')->nullable();
                $table->string('meta_pixel_id')->nullable();
                $table->string('google_analytics_id')->nullable();
                $table->string('tiktok_pixel_id')->nullable();
                $table->string('spotify_url')->nullable();
                $table->string('youtube_url')->nullable();
                $table->string('manual_badge')->nullable();
                $table->boolean('event_addons_enabled')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('event_ai_generations')) {
            Schema::create('event_ai_generations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('organizer_id');
                $table->string('format');
                $table->string('status')->default('pending');
                $table->string('model')->default('gpt-image-2');
                $table->text('prompt')->nullable();
                $table->unsignedInteger('duration_ms')->nullable();
                $table->decimal('cost_estimate', 8, 4)->nullable();
                $table->text('error_message')->nullable();
                $table->string('output_path')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code')->nullable();
                $table->string('name')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('basic_settings')) {
            Schema::create('basic_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('uniqid')->default(12345);
                $table->string('website_title')->nullable();
                $table->string('organizer_email_verification')->default(0);
                $table->string('customer_email_verification')->default(0);
                $table->string('base_color')->nullable();
                $table->timestamps();
            });

            \DB::table('basic_settings')->insert([
                'uniqid' => 12345,
                'organizer_email_verification' => 0,
                'customer_email_verification' => 0,
            ]);
        }
    }

    public function test_generate_dispatches_batch_with_3_jobs(): void
    {
        Bus::fake();
        $organizer = $this->makeOrganizer('org1@test.com', 'org1');
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'cover.png',
        ]);

        $response = $this->actingAs($organizer, 'organizer')
            ->postJson("/organizer/events/{$event->id}/ai-images/generate");

        $response->assertOk()
            ->assertJsonStructure(['status', 'batch_id', 'formats']);

        Bus::assertBatched(function ($batch) {
            return count($batch->jobs) === 3;
        });
    }

    public function test_generate_requires_feature_flag(): void
    {
        config(['features.ai_images_enabled' => false]);
        $organizer = $this->makeOrganizer('org2@test.com', 'org2');
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'cover.png',
        ]);

        $this->actingAs($organizer, 'organizer')
            ->postJson("/organizer/events/{$event->id}/ai-images/generate")
            ->assertStatus(503);
    }

    public function test_generate_fails_without_thumbnail(): void
    {
        $organizer = $this->makeOrganizer('org3@test.com', 'org3');
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => null,
        ]);

        $this->actingAs($organizer, 'organizer')
            ->postJson("/organizer/events/{$event->id}/ai-images/generate")
            ->assertStatus(422);
    }

    public function test_generate_fails_if_3_already_generated(): void
    {
        $organizer = $this->makeOrganizer('org4@test.com', 'org4');
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'cover.png',
        ]);

        foreach (['square', 'gallery', 'og'] as $format) {
            EventAiGeneration::create([
                'event_id' => $event->id,
                'organizer_id' => $organizer->id,
                'format' => $format,
                'status' => 'completed',
            ]);
        }

        $this->actingAs($organizer, 'organizer')
            ->postJson("/organizer/events/{$event->id}/ai-images/generate")
            ->assertStatus(422);
    }

    public function test_status_returns_correct_payload(): void
    {
        $organizer = $this->makeOrganizer('org5@test.com', 'org5');
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'cover.png',
        ]);

        EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'square',
            'status' => 'completed',
            'output_path' => 'assets/admin/img/event-ai/1/square.png',
        ]);

        $response = $this->actingAs($organizer, 'organizer')
            ->getJson("/organizer/events/{$event->id}/ai-images/status")
            ->assertOk()
            ->assertJsonStructure([
                'total',
                'completed',
                'failed',
                'formats' => ['square', 'gallery', 'og'],
            ]);

        $this->assertEquals(3, $response->json('total'));
        $this->assertEquals(1, $response->json('completed'));
    }

    public function test_other_organizer_cannot_access(): void
    {
        $organizer1 = $this->makeOrganizer('o1@test.com', 'o1');
        $organizer2 = $this->makeOrganizer('o2@test.com', 'o2');
        $event = Event::create([
            'organizer_id' => $organizer1->id,
            'thumbnail' => 'cover.png',
        ]);

        $this->actingAs($organizer2, 'organizer')
            ->getJson("/organizer/events/{$event->id}/ai-images/status")
            ->assertStatus(403);
    }

    private function makeOrganizer(string $email, string $username): Organizer
    {
        $org = new Organizer();
        $org->forceFill([
            'email' => $email,
            'username' => $username,
            'password' => bcrypt('x'),
            'status' => 1,
        ]);
        $org->save();
        return $org;
    }
}
