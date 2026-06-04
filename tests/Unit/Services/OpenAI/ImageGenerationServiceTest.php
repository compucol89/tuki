<?php

namespace Tests\Unit\Services\OpenAI;

use App\Exceptions\OpenAiNonRetryableException;
use App\Services\OpenAI\ImageGenerationService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class ImageGenerationServiceTest extends TestCase
{
    private ImageGenerationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config(['openai.api_key' => 'sk-test-fake']);
        $this->service = new ImageGenerationService();
    }

    public function test_generate_edit_returns_base64_on_success(): void
    {
        $fakeBase64 = base64_encode('fake-png-bytes');
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'data' => [['b64_json' => $fakeBase64]],
            ], 200),
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'ref_') . '.png';
        file_put_contents($tmpFile, 'fake');

        try {
            $result = $this->service->generateEdit(
                $tmpFile,
                'test prompt',
                '1024x1024'
            );

            $this->assertEquals($fakeBase64, $result);
        } finally {
            @unlink($tmpFile);
        }
    }

    public function test_generate_edit_sends_correct_payload(): void
    {
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'data' => [['b64_json' => 'abc']],
            ], 200),
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'ref_') . '.png';
        file_put_contents($tmpFile, 'fake');

        try {
            $this->service->generateEdit($tmpFile, 'mi prompt', '1536x1024');

            Http::assertSent(function ($request) {
                if ($request->url() !== 'https://api.openai.com/v1/images/edits') {
                    return false;
                }
                $body = $request->body();
                return str_contains($body, 'name="model"')
                    && str_contains($body, 'gpt-image-2')
                    && str_contains($body, 'name="prompt"')
                    && str_contains($body, 'mi prompt')
                    && str_contains($body, 'name="size"')
                    && str_contains($body, '1536x1024')
                    && str_contains($body, 'name="output_format"')
                    && str_contains($body, 'png');
            });
        } finally {
            @unlink($tmpFile);
        }
    }

    public function test_generate_edit_throws_on_4xx(): void
    {
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'error' => ['message' => 'Invalid API key', 'type' => 'invalid_request_error'],
            ], 401),
        ]);

        $this->expectException(OpenAiNonRetryableException::class);
        $this->expectExceptionMessage('OpenAI API error');

        $tmpFile = tempnam(sys_get_temp_dir(), 'ref_') . '.png';
        file_put_contents($tmpFile, 'fake');
        try {
            $this->service->generateEdit($tmpFile, 'prompt', '1024x1024');
        } finally {
            @unlink($tmpFile);
        }
    }

    public function test_generate_edit_throws_on_5xx(): void
    {
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response('Server Error', 500),
        ]);

        $this->expectException(RuntimeException::class);

        $tmpFile = tempnam(sys_get_temp_dir(), 'ref_') . '.png';
        file_put_contents($tmpFile, 'fake');
        try {
            $this->service->generateEdit($tmpFile, 'prompt', '1024x1024');
        } finally {
            @unlink($tmpFile);
        }
    }

    public function test_generate_edit_throws_when_api_key_missing(): void
    {
        config(['openai.api_key' => null]);

        $this->expectException(OpenAiNonRetryableException::class);
        $this->expectExceptionMessage('OPENAI_API_KEY');

        $this->service->generateEdit('/tmp/x.png', 'p', '1024x1024');
    }
}
