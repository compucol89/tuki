<?php

namespace Tests\Unit\Arca;

use App\Services\Arca\WsfeClient;
use Exception;
use ReflectionMethod;
use Tests\TestCase;

class WsfeClientErrorHandlingTest extends TestCase
{
    public function test_informational_events_do_not_throw_errors(): void
    {
        $result = (object) [
            'FEParamGetTiposCbteResult' => (object) [
                'Events' => (object) [
                    'Evt' => [
                        (object) ['Code' => 43, 'Msg' => 'IMPORTANTE: aviso informativo'],
                    ],
                ],
            ],
        ];

        $wsfe = $this->checkErrors($result);

        $this->assertSame([
            [
                'code' => 43,
                'message' => 'IMPORTANTE: aviso informativo',
            ],
        ], $wsfe->pullInformationalEvents());
    }

    public function test_real_errors_throw_exception(): void
    {
        $result = (object) [
            'Errors' => (object) [
                'Err' => [
                    (object) ['Code' => 600, 'Msg' => 'ValidacionDeToken'],
                ],
            ],
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[600] ValidacionDeToken');

        $this->checkErrors($result);
    }

    private function checkErrors(object $result): WsfeClient
    {
        $wsfe = new WsfeClient();
        $method = new ReflectionMethod(WsfeClient::class, 'checkErrors');
        $method->setAccessible(true);
        $method->invoke($wsfe, $result);

        return $wsfe;
    }
}
