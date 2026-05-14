<?php

namespace Tests\Unit\Arca;

use App\Services\Arca\WsfeClient;
use Exception;
use Tests\TestCase;

class WsfeClientRecuperarTest extends TestCase
{
    private function makeWsfe(): WsfeClient
    {
        return $this->getMockBuilder(WsfeClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['verificarComprobante'])
            ->getMock();
    }

    public function test_returns_formatted_response_when_arca_has_cae(): void
    {
        $wsfe = $this->makeWsfe();
        $wsfe->method('verificarComprobante')
            ->with(6, 42)
            ->willReturn([
                'cae'             => 'CAE12345678901234',
                'cae_vencimiento' => '20260520',
                'resultado'       => 'A',
                'fecha'           => '20260513',
                'imp_total'       => 1210.0,
            ]);

        $result = $wsfe->recuperarSiYaEmitido(6, 42);

        $this->assertNotNull($result);
        $this->assertSame('CAE12345678901234', $result['cae']);
        $this->assertSame('20260520', $result['cae_vencimiento']);
        $this->assertSame(42, $result['cbte_nro']);
        $this->assertSame(42, $result['cbte_desde']);
        $this->assertSame(42, $result['cbte_hasta']);
        $this->assertSame(6, $result['cbte_tipo']);
        $this->assertTrue($result['recovered']);
        $this->assertSame([], $result['observaciones']);
    }

    public function test_returns_null_when_verificar_throws(): void
    {
        $wsfe = $this->makeWsfe();
        $wsfe->method('verificarComprobante')
            ->willThrowException(new Exception('ARCA: comprobante no encontrado'));

        $this->assertNull($wsfe->recuperarSiYaEmitido(6, 99));
    }

    public function test_returns_null_when_cae_is_empty(): void
    {
        $wsfe = $this->makeWsfe();
        $wsfe->method('verificarComprobante')
            ->willReturn([
                'cae'             => '',
                'cae_vencimiento' => '',
                'resultado'       => 'R',
                'fecha'           => '20260513',
                'imp_total'       => 0.0,
            ]);

        $this->assertNull($wsfe->recuperarSiYaEmitido(6, 42));
    }
}
