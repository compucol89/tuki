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

    public function test_extend_background_sends_mask_and_omits_input_fidelity(): void
    {
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'data' => [['b64_json' => base64_encode($this->pngBytes([0, 0, 0], 1024, 1024))]],
            ], 200),
        ]);

        $source = $this->writePng([230, 80, 20], 600, 600);

        try {
            $this->service->extendBackground($source, '1024x1024');

            Http::assertSent(function ($request) {
                $body = $request->body();
                return str_contains($body, 'name="mask"')
                    && !str_contains($body, 'name="input_fidelity"')
                    && str_contains($body, 'name="model"');
            });
        } finally {
            @unlink($source);
        }
    }

    public function test_build_alpha_mask_has_transparent_outside_original(): void
    {
        $mask = $this->service->buildAlphaMask(100, 80, [
            'x' => 10,
            'y' => 20,
            'width' => 30,
            'height' => 40,
        ]);

        try {
            $this->assertGreaterThan(0, $this->pixelAlphaFromImage($mask, 0, 0));
            $this->assertSame(127, $this->pixelAlphaFromImage($mask, 0, 0));
        } finally {
            imagedestroy($mask);
        }
    }

    public function test_build_alpha_mask_has_opaque_over_original(): void
    {
        $mask = $this->service->buildAlphaMask(100, 80, [
            'x' => 10,
            'y' => 20,
            'width' => 30,
            'height' => 40,
        ]);

        try {
            $this->assertSame(0, $this->pixelAlphaFromImage($mask, 15, 25));
        } finally {
            imagedestroy($mask);
        }
    }

    public function test_build_alpha_mask_png_preserves_alpha_channel(): void
    {
        $mask = $this->service->buildAlphaMask(100, 80, [
            'x' => 10,
            'y' => 20,
            'width' => 30,
            'height' => 40,
        ]);
        $path = tempnam(sys_get_temp_dir(), 'mask_alpha_') . '.png';

        try {
            imagepng($mask, $path);
            $reopened = imagecreatefrompng($path);

            $this->assertSame(127, $this->pixelAlphaFromImage($reopened, 0, 0));
            $this->assertSame(0, $this->pixelAlphaFromImage($reopened, 15, 25));

            imagedestroy($reopened);
        } finally {
            imagedestroy($mask);
            @unlink($path);
        }
    }

    public function test_extend_background_uses_alpha_mask_when_enabled(): void
    {
        config(['openai.use_alpha_mask' => true]);
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'data' => [['b64_json' => base64_encode($this->pngBytes([0, 0, 0], 1536, 1024))]],
            ], 200),
        ]);

        $source = $this->writePng([230, 80, 20], 600, 600);

        try {
            $this->service->extendBackground($source, '1536x1024');

            Http::assertSent(function ($request) {
                $maskBytes = $this->extractMultipartFile($request->body(), 'mask');
                $mask = imagecreatefromstring($maskBytes);

                try {
                    return imagesx($mask) === 1536
                        && imagesy($mask) === 1024
                        && $this->pixelAlphaFromImage($mask, 0, 0) === 127
                        && $this->pixelAlphaFromImage($mask, 768, 512) === 0;
                } finally {
                    imagedestroy($mask);
                }
            });
        } finally {
            @unlink($source);
        }
    }

    public function test_extend_background_uses_opaque_mask_when_alpha_mask_disabled(): void
    {
        config(['openai.use_alpha_mask' => false]);
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'data' => [['b64_json' => base64_encode($this->pngBytes([0, 0, 0], 1536, 1024))]],
            ], 200),
        ]);

        $source = $this->writePng([230, 80, 20], 600, 600);

        try {
            $this->service->extendBackground($source, '1536x1024');

            Http::assertSent(function ($request) {
                $maskBytes = $this->extractMultipartFile($request->body(), 'mask');
                $mask = imagecreatefromstring($maskBytes);

                try {
                    return imagesx($mask) === 1536
                        && imagesy($mask) === 1024
                        && $this->pixelAlphaFromImage($mask, 0, 0) === 0
                        && $this->pixelAlphaFromImage($mask, 768, 512) === 0
                        && $this->pixelRgbFromImage($mask, 0, 0) === [255, 255, 255]
                        && $this->pixelRgbFromImage($mask, 768, 512) === [0, 0, 0];
                } finally {
                    imagedestroy($mask);
                }
            });
        } finally {
            @unlink($source);
        }
    }

    public function test_extend_background_composites_original_on_top_of_ai_output(): void
    {
        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'data' => [['b64_json' => base64_encode($this->pngBytes([0, 0, 0], 1536, 1024))]],
            ], 200),
        ]);

        $source = $this->writePng([230, 80, 20], 600, 600);
        $output = tempnam(sys_get_temp_dir(), 'extended_') . '.png';

        try {
            file_put_contents($output, $this->service->extendBackground($source, '1536x1024'));

            $this->assertSame([230, 80, 20], $this->pixelRgb($output, 768, 512));
        } finally {
            @unlink($source);
            @unlink($output);
        }
    }

    private function writePng(array $rgb, int $width, int $height): string
    {
        $path = tempnam(sys_get_temp_dir(), 'openai_ref_') . '.png';
        file_put_contents($path, $this->pngBytes($rgb, $width, $height));

        return $path;
    }

    private function pngBytes(array $rgb, int $width, int $height): string
    {
        $img = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($img, 0, 0, $color);

        ob_start();
        imagepng($img);
        $bytes = ob_get_clean();
        imagedestroy($img);

        return $bytes;
    }

    private function pixelRgb(string $path, int $x, int $y): array
    {
        $image = imagecreatefrompng($path);
        $color = imagecolorat($image, $x, $y);
        imagedestroy($image);

        return [
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF,
        ];
    }

    private function pixelAlphaFromImage(\GdImage $image, int $x, int $y): int
    {
        $color = imagecolorat($image, $x, $y);

        return ($color >> 24) & 0x7F;
    }

    private function pixelRgbFromImage(\GdImage $image, int $x, int $y): array
    {
        $color = imagecolorat($image, $x, $y);

        return [
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF,
        ];
    }

    private function extractMultipartFile(string $body, string $fieldName): string
    {
        $needle = 'name="' . $fieldName . '";';
        $start = strpos($body, $needle);
        $this->assertNotFalse($start, "Multipart field {$fieldName} not found");

        $contentStart = strpos($body, "\r\n\r\n", $start);
        $this->assertNotFalse($contentStart, "Multipart content for {$fieldName} not found");
        $contentStart += 4;

        $contentEnd = strpos($body, "\r\n--", $contentStart);
        $this->assertNotFalse($contentEnd, "Multipart boundary for {$fieldName} not found");

        return substr($body, $contentStart, $contentEnd - $contentStart);
    }
}
