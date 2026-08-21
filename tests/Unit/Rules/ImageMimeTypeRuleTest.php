<?php

namespace Tests\Unit\Rules;

use App\Rules\ImageMimeTypeRule;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageMimeTypeRuleTest extends TestCase
{
    public function test_accepts_real_images(): void
    {
        $rule = new ImageMimeTypeRule();

        $this->assertTrue($rule->passes('photo', UploadedFile::fake()->image('foto.jpg')));
        $this->assertTrue($rule->passes('photo', UploadedFile::fake()->image('foto.png')));
        // WebP solo si la extensión GD del entorno lo soporta (imagen docker sin imagewebp)
        if (function_exists('imagewebp')) {
            $this->assertTrue($rule->passes('photo', UploadedFile::fake()->image('foto.webp')));
        }
    }

    public function test_rejects_php_file_renamed_as_image(): void
    {
        $rule = new ImageMimeTypeRule();

        $this->assertFalse($rule->passes('photo', UploadedFile::fake()->create('foto.php', 100, 'application/x-php')));
        $this->assertFalse($rule->passes('photo', UploadedFile::fake()->create('foto.jpg', 100, 'text/plain')));
    }

    public function test_rejects_non_file_values(): void
    {
        $rule = new ImageMimeTypeRule();

        $this->assertFalse($rule->passes('photo', 'not-a-file'));
        $this->assertFalse($rule->passes('photo', null));
    }
}
