<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /** @test */
    public function response_includes_x_frame_options(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    /** @test */
    public function response_includes_x_content_type_options(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /** @test */
    public function response_includes_referrer_policy(): void
    {
        $response = $this->get('/');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /** @test */
    public function response_includes_permissions_policy(): void
    {
        $response = $this->get('/');
        $this->assertStringContainsString(
            'geolocation=()',
            (string) $response->headers->get('Permissions-Policy', '')
        );
    }

    /** @test */
    public function response_includes_content_security_policy(): void
    {
        $response = $this->get('/');
        $csp = (string) $response->headers->get('Content-Security-Policy', '');

        $this->assertNotEmpty($csp, 'CSP header missing');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString('frame-ancestors', $csp);
    }

    /** @test */
    public function htaccess_is_not_served_at_root(): void
    {
        $response = $this->get('/.htaccess');

        $this->assertNotEquals(
            200,
            $response->getStatusCode(),
            '.htaccess root file must not be served'
        );
    }
}
