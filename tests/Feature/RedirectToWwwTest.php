<?php

namespace Tests\Feature;

use App\Http\Middleware\RedirectToWww;
use Illuminate\Http\Request;
use Tests\TestCase;

class RedirectToWwwTest extends TestCase
{
  private function runMiddleware(string $method, string $url)
  {
    $request = Request::create($url, $method);
    $middleware = new RedirectToWww();

    return $middleware->handle($request, fn () => response('ok'));
  }

  public function test_get_request_from_non_www_redirects_with_308(): void
  {
    $response = $this->runMiddleware('GET', 'https://tukipass.com/csrf-token');

    $this->assertEquals(308, $response->getStatusCode());
    $this->assertSame('https://www.tukipass.com/csrf-token', $response->headers->get('Location'));
  }

  public function test_post_request_from_non_www_keeps_post_with_308(): void
  {
    $response = $this->runMiddleware('POST', 'https://tukipass.com/check-out2');

    $this->assertEquals(308, $response->getStatusCode());
    $this->assertSame('https://www.tukipass.com/check-out2', $response->headers->get('Location'));
  }

  public function test_www_host_is_not_redirected(): void
  {
    $response = $this->runMiddleware('GET', 'https://www.tukipass.com/csrf-token');

    $this->assertEquals(200, $response->getStatusCode());
  }
}
