<?php

namespace Tests\Feature;

use Tests\TestCase;

class CsrfRecoveryTest extends TestCase
{
  public function test_csrf_token_refresh_endpoint_returns_token_without_cache(): void
  {
    $response = $this->get('/csrf-token');

    $response->assertOk();
    $response->assertJsonStructure(['token']);
    $this->assertNotEmpty($response->json('token'));
    $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
  }

  public function test_419_error_page_uses_spanish_checkout_message(): void
  {
    $html = file_get_contents(resource_path('views/errors/419.blade.php'));

    $this->assertStringContainsString('Tu reserva expiró', $html);
    $this->assertStringContainsString('Volver a intentar', $html);
  }

  public function test_checkout_page_keeps_csrf_token_fresh_while_open(): void
  {
    $html = file_get_contents(resource_path('views/frontend/check-out.blade.php'));

    $this->assertStringContainsString('csrfKeepAliveInterval', $html);
    $this->assertStringContainsString('visibilitychange', $html);
    $this->assertStringContainsString('pageshow', $html);
  }
}
