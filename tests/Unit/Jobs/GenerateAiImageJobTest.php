<?php

namespace Tests\Unit\Jobs;

use App\Exceptions\OpenAiNonRetryableException;
use App\Jobs\GenerateAiImageJob;
use App\Models\Event;
use App\Models\Event\EventAiGeneration;
use App\Models\Event\EventImage;
use App\Models\Organizer;
use App\Services\OpenAI\ImageGenerationService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GenerateAiImageJobTest extends TestCase
{
    private array $cleanupFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('event_ai_generations');
        Schema::dropIfExists('event_images');
        Schema::dropIfExists('event_contents');
        Schema::dropIfExists('events');
        Schema::dropIfExists('organizers');
        Schema::dropIfExists('languages');

        Schema::create('languages', function ($table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('organizers', function ($table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function ($table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id');
            $table->string('thumbnail')->nullable();
            $table->string('og_image')->nullable();
            $table->text('ai_metadata')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamps();
        });

        Schema::create('event_images', function ($table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('image');
            $table->string('format')->nullable();
            $table->timestamps();
        });

        Schema::create('event_contents', function ($table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('title')->nullable();
            $table->string('city')->nullable();
            $table->unsignedBigInteger('event_category_id')->nullable();
            $table->timestamps();
        });

        Schema::create('event_ai_generations', function ($table) {
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

    protected function tearDown(): void
    {
        foreach ($this->cleanupFiles as $file) {
            @unlink($file);
        }
        foreach (File::glob(public_path('assets/admin/img/event-gallery/ai_*')) ?: [] as $file) {
            @unlink($file);
        }
        $this->cleanupFiles = [];
        parent::tearDown();
    }

    private function makeRefPng(string $filename, int $size = 600): string
    {
        $path = public_path('assets/admin/img/event/thumbnail/' . $filename);
        File::ensureDirectoryExists(dirname($path));

        $img = imagecreatetruecolor($size, $size);
        $color = imagecolorallocate($img, 200, 100, 50);
        imagefill($img, 0, 0, $color);
        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        file_put_contents($path, $data);
        $this->cleanupFiles[] = $path;
        return $path;
    }

    public function test_job_saves_preview_file_and_does_not_apply_or_overwrite_organizer_thumbnail(): void
    {
        $organizer = Organizer::create(['email' => 'org1@test.com', 'username' => 'org1']);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'test-ref.png',
        ]);
        $originalThumbnail = $event->thumbnail;

        $refPath = $this->makeRefPng('test-ref.png');

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'square',
            'status' => 'pending',
            'prompt' => 'test',
        ]);

        $job = new GenerateAiImageJob($generation->id);
        $job->handle(app(ImageGenerationService::class));

        $generation->refresh();
        $this->cleanupFiles[] = public_path($generation->output_path);
        $this->assertEquals('completed', $generation->status);
        $this->assertNotNull($generation->output_path);
        $this->assertFileExists(public_path($generation->output_path));
        $this->assertSame([1024, 1024], array_slice(getimagesize(public_path($generation->output_path)), 0, 2));
        $this->assertEquals(0.0, (float) $generation->cost_estimate);

        $event->refresh();
        $this->assertEquals($originalThumbnail, $event->thumbnail, 'Organizer thumbnail must be preserved');

        $this->assertSame(0, EventImage::where('event_id', $event->id)->where('format', 'square')->count());
    }

    public function test_job_creates_preview_only_for_gallery_format(): void
    {
        $organizer = Organizer::create(['email' => 'org2@test.com', 'username' => 'org2']);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'test-ref.png',
        ]);

        $refPath = $this->makeRefPng('test-ref.png');

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'gallery',
            'status' => 'pending',
        ]);

        $job = new GenerateAiImageJob($generation->id);
        $job->handle(app(ImageGenerationService::class));

        $generation->refresh();
        $this->cleanupFiles[] = public_path($generation->output_path);
        $this->assertEquals('completed', $generation->status);
        $this->assertSame([1536, 1024], array_slice(getimagesize(public_path($generation->output_path)), 0, 2));

        $this->assertSame(0, EventImage::where('event_id', $event->id)->where('format', 'gallery')->count());
    }

    public function test_job_creates_preview_only_for_og_format(): void
    {
        $organizer = Organizer::create(['email' => 'org3@test.com', 'username' => 'org3']);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'test-ref.png',
        ]);

        $refPath = $this->makeRefPng('test-ref.png');

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'og',
            'status' => 'pending',
        ]);

        $job = new GenerateAiImageJob($generation->id);
        $job->handle(app(ImageGenerationService::class));

        $generation->refresh();
        $this->cleanupFiles[] = public_path($generation->output_path);
        $this->assertSame([1536, 1024], array_slice(getimagesize(public_path($generation->output_path)), 0, 2));

        $event->refresh();
        $this->assertNull($event->og_image);
    }

    public function test_job_marks_failed_when_variant_size_is_invalid(): void
    {
        config(['openai.formats.square.size' => 'bad-size']);

        $organizer = Organizer::create(['email' => 'org4@test.com', 'username' => 'org4']);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'test-ref.png',
        ]);

        $refPath = $this->makeRefPng('test-ref.png');

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'square',
            'status' => 'pending',
        ]);

        $thrown = null;
        try {
            $job = new GenerateAiImageJob($generation->id);
            $job->handle(app(ImageGenerationService::class));
        } catch (\RuntimeException $e) {
            $thrown = $e;
        }

        $this->assertNull($thrown, 'Invalid local variant config should mark failed without bubbling.');

        $generation->refresh();
        $this->assertEquals('failed', $generation->status);
        $this->assertStringContainsString('Invalid image size', $generation->error_message);
    }

    public function test_job_marks_failed_without_retry_on_invalid_image(): void
    {
        $organizer = Organizer::create(['email' => 'org6@test.com', 'username' => 'org6']);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'invalid-ref.png',
        ]);

        $refPath = public_path('assets/admin/img/event/thumbnail/invalid-ref.png');
        File::ensureDirectoryExists(dirname($refPath));
        File::put($refPath, 'this is not a valid image');
        $this->cleanupFiles[] = $refPath;

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'square',
            'status' => 'pending',
        ]);

        $thrown = null;
        try {
            $job = new GenerateAiImageJob($generation->id);
            $job->handle(app(ImageGenerationService::class));
        } catch (OpenAiNonRetryableException $e) {
            $thrown = $e;
        } catch (\Throwable $e) {
            $thrown = $e;
        }

        $this->assertNull($thrown, 'Validation should not throw — job should mark as failed and stop. Got: ' . ($thrown ? get_class($thrown) : 'none'));

        $generation->refresh();
        $this->assertEquals('failed', $generation->status);
        $this->assertStringContainsString('not a valid image', $generation->error_message);
    }

    public function test_job_does_not_call_openai_api_for_preserved_variant(): void
    {
        $organizer = Organizer::create(['email' => 'org7@test.com', 'username' => 'org7']);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => 'test-ref.png',
        ]);

        $refPath = $this->makeRefPng('test-ref.png');

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'square',
            'status' => 'pending',
        ]);

        $this->mock(ImageGenerationService::class, function ($mock) {
            $mock->shouldNotReceive('generateEdit');
        });

        $thrown = null;
        try {
            $job = new GenerateAiImageJob($generation->id);
            $job->handle(app(ImageGenerationService::class));
        } catch (\Throwable $e) {
            $thrown = $e;
        }

        $this->assertNull($thrown, 'Local preserved variant should not call OpenAI.');

        $generation->refresh();
        $this->assertEquals('completed', $generation->status);
        $this->cleanupFiles[] = public_path($generation->output_path);
    }

    public function test_job_throws_if_thumbnail_missing(): void
    {
        $organizer = Organizer::create(['email' => 'org5@test.com', 'username' => 'org5']);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'thumbnail' => null,
        ]);

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $organizer->id,
            'format' => 'square',
            'status' => 'pending',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('thumbnail is empty');

        $job = new GenerateAiImageJob($generation->id);
        $job->handle(app(ImageGenerationService::class));
    }
}
