<?php

namespace Tests\Unit\Jobs;

use App\Jobs\AnalyzeEventFlyerJob;
use App\Models\Event\EventAiAssistantRun;
use App\Services\EventAi\EventFactsBuilder;
use App\Services\OpenAI\EventAiAssistantService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnalyzeEventFlyerJobTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('organizer_id')->nullable();
                $table->string('thumbnail')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('event_ai_assistant_runs')) {
            Schema::create('event_ai_assistant_runs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('organizer_id');
                $table->string('type', 30)->default('analysis');
                $table->string('status', 30)->default('pending');
                $table->timestamps();
            });
        }
    }

    public function test_handle_is_noop_when_run_was_deleted(): void
    {
        $this->mock(EventAiAssistantService::class);

        $job = new AnalyzeEventFlyerJob(999999);

        $job->handle(app(EventAiAssistantService::class), app(EventFactsBuilder::class));

        $this->assertSame(0, EventAiAssistantRun::count());
        $this->addToAssertionCount(1);
    }

    public function test_handle_is_noop_when_event_is_missing(): void
    {
        $this->mock(EventAiAssistantService::class);

        $run = EventAiAssistantRun::forceCreate([
            'event_id' => 999999,
            'organizer_id' => 1,
            'type' => 'analysis',
            'status' => 'pending',
        ]);

        $job = new AnalyzeEventFlyerJob($run->id);

        $job->handle(app(EventAiAssistantService::class), app(EventFactsBuilder::class));

        $this->assertSame('pending', $run->fresh()->status);
        $this->addToAssertionCount(1);
    }
}
