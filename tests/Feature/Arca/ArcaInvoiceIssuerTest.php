<?php

namespace Tests\Feature\Arca;

use App\Services\Arca\ArcaInvoiceIssuer;
use App\Services\Arca\WsfeClient;
use RuntimeException;
use Tests\TestCase;

class ArcaInvoiceIssuerTest extends TestCase
{
    public function test_issuer_throws_when_arca_issuing_is_disabled(): void
    {
        config(['arca.enable_issuing' => false]);

        $issuer = new ArcaInvoiceIssuer($this->createMock(WsfeClient::class));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ARCA issuing is disabled');

        $issuer->issue([]);
    }

    public function test_preview_path_does_not_call_authorize_comprobante(): void
    {
        config(['arca.enable_issuing' => false]);

        $wsfe = $this->createMock(WsfeClient::class);
        $wsfe->expects($this->never())->method('autorizarComprobante');

        $issuer = new ArcaInvoiceIssuer($wsfe);

        $preview = $issuer->previewOnly(['invoice_total' => 10000.0]);

        $this->assertSame(['invoice_total' => 10000.0], $preview);
    }
}
