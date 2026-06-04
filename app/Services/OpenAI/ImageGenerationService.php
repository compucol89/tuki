<?php

namespace App\Services\OpenAI;

use App\Exceptions\OpenAiNonRetryableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ImageGenerationService
{
    public function generateEdit(string $referenceImagePath, string $prompt, string $size): string
    {
        $apiKey = config('openai.api_key');
        if (empty($apiKey)) {
            throw new OpenAiNonRetryableException('OPENAI_API_KEY is not configured');
        }

        $imageStream = fopen($referenceImagePath, 'r');
        if ($imageStream === false) {
            throw new OpenAiNonRetryableException('Reference image is not readable');
        }

        $response = Http::withToken($apiKey)
            ->timeout(config('openai.timeout', 60))
            ->attach(
                'image[]',
                $imageStream,
                basename($referenceImagePath)
            )
            ->post(config('openai.base_url') . '/images/edits', [
                'model' => config('openai.model'),
                'prompt' => $prompt,
                'size' => $size,
                'n' => 1,
                'output_format' => 'png',
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $error = $response->json('error.message') ?? $response->body();
            Log::error('OpenAI image generation failed', [
                'status' => $status,
                'error' => $error,
            ]);

            if ($status >= 500 || $status === 429) {
                throw new RuntimeException("OpenAI API error ({$status}): {$error}");
            }

            throw new OpenAiNonRetryableException("OpenAI API error ({$status}): {$error}");
        }

        $b64 = $response->json('data.0.b64_json');
        if (empty($b64)) {
            throw new OpenAiNonRetryableException('OpenAI returned empty response');
        }

        return $b64;
    }
}
