<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiIndexFilesTest extends TestCase
{
  public function test_robots_allows_ai_search_bots_and_keeps_private_paths_blocked(): void
  {
    $response = $this->get('/robots.txt');

    $response->assertOk();

    $robots = $response->getContent();

    $this->assertStringContainsString('User-agent: OAI-SearchBot', $robots);
    $this->assertStringContainsString("User-agent: GPTBot\nDisallow: /", $robots);
    $this->assertStringContainsString("User-agent: ClaudeBot\nDisallow: /", $robots);
    $this->assertStringContainsString("User-agent: Google-Extended\nDisallow: /", $robots);
    $this->assertStringContainsString('User-agent: Claude-SearchBot', $robots);
    $this->assertStringContainsString('User-agent: PerplexityBot', $robots);
    $this->assertStringContainsString('Allow: /organizer/details/', $robots);
    $this->assertStringContainsString('Disallow: /admin/', $robots);
    $this->assertStringContainsString('Sitemap: https://www.tukipass.com/sitemap.xml', $robots);
  }

  public function test_llms_txt_exposes_machine_readable_site_map(): void
  {
    $response = $this->get('/llms.txt');

    $response->assertOk();

    $content = $response->getContent();

    $this->assertStringStartsWith('# Tukipass', $content);
    $this->assertStringContainsString('## Páginas principales', $content);
    $this->assertStringContainsString('https://www.tukipass.com/eventos', $content);
    $this->assertStringContainsString('https://www.tukipass.com/sitemap.xml', $content);
    $this->assertStringContainsString('https://www.tukipass.com/llms-full.txt', $content);
  }

  public function test_llms_full_txt_exposes_public_url_inventory(): void
  {
    $response = $this->get('/llms-full.txt');

    $response->assertOk();

    $content = $response->getContent();

    $this->assertStringStartsWith('# Tukipass', $content);
    $this->assertStringContainsString('## URLs públicas indexables', $content);
    $this->assertStringContainsString('https://www.tukipass.com/', $content);
    $this->assertStringContainsString('https://www.tukipass.com/organizadores', $content);
  }

  public function test_image_sitemap_is_exposed_for_google_search_console(): void
  {
    $response = $this->get('/sitemap-images.xml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $content = $response->getContent();

    $this->assertStringContainsString('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', $content);
    $this->assertStringContainsString('<urlset', $content);

    $robots = $this->get('/robots.txt')->getContent();

    $this->assertStringContainsString('Sitemap: https://www.tukipass.com/sitemap-images.xml', $robots);
  }
}
