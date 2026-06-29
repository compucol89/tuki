<?php

namespace Tests\Unit\Support;

use App\Support\YoutubeEmbedUrl;
use PHPUnit\Framework\TestCase;

class YoutubeEmbedUrlTest extends TestCase
{
  public function test_builds_embed_url_from_common_youtube_urls(): void
  {
    $expected = 'https://www.youtube.com/embed/dQw4w9WgXcQ';

    $this->assertSame($expected, YoutubeEmbedUrl::from('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
    $this->assertSame($expected, YoutubeEmbedUrl::from('https://www.youtube.com/watch?si=abc&v=dQw4w9WgXcQ'));
    $this->assertSame($expected, YoutubeEmbedUrl::from('https://youtu.be/dQw4w9WgXcQ?si=abc'));
    $this->assertSame($expected, YoutubeEmbedUrl::from('https://www.youtube.com/embed/dQw4w9WgXcQ'));
    $this->assertSame($expected, YoutubeEmbedUrl::from('https://www.youtube.com/shorts/dQw4w9WgXcQ'));
    $this->assertSame($expected, YoutubeEmbedUrl::from('https://www.youtube.com/live/dQw4w9WgXcQ?si=abc'));
  }

  public function test_returns_null_for_invalid_youtube_url(): void
  {
    $this->assertNull(YoutubeEmbedUrl::from('https://example.com/video'));
  }
}
