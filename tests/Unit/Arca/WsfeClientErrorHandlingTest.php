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

    public function test_array_informational_events_do_not_throw_errors(): void
    {
        $result = [
            'FEParamGetTiposCbteResult' => [
                'Events' => [
                    'Evt' => [
                        'Code' => 39,
                        'Msg' => 'IMPORTANTE: aviso informativo',
                    ],
                ],
            ],
        ];

        $wsfe = $this->checkErrors($result);

        $this->assertSame([
            [
                'code' => 39,
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

    public function test_single_error_object_throws_exception(): void
    {
        $result = (object) [
            'Errors' => (object) [
                'Err' => (object) ['Code' => 600, 'Msg' => 'ValidacionDeToken'],
            ],
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[600] ValidacionDeToken');

        $this->checkErrors($result);
    }

    public function test_soap_items_normalize_object_array_and_scalars(): void
    {
        $items = $this->soapItems([
            (object) ['Id' => 11, 'Desc' => 'Factura C'],
            ['Id' => 6, 'Desc' => 'Factura B'],
            11,
        ]);

        $this->assertCount(2, $items);
        $this->assertSame(11, $this->soapField($items[0], 'Id'));
        $this->assertSame('Factura B', $this->soapField($items[1], 'Desc'));
    }

    public function test_single_soap_item_is_normalized_as_list(): void
    {
        $items = $this->soapItems((object) ['Id' => 11, 'Desc' => 'Factura C']);

        $this->assertCount(1, $items);
        $this->assertSame(11, $this->soapField($items[0], 'Id'));
        $this->assertSame('Factura C', $this->soapField($items[0], 'Desc'));
    }

    private function checkErrors($result): WsfeClient
    {
        $wsfe = new WsfeClient();
        $method = new ReflectionMethod(WsfeClient::class, 'checkErrors');
        $method->setAccessible(true);
        $method->invoke($wsfe, $result);

        return $wsfe;
    }

    private function soapItems($value): array
    {
        $method = new ReflectionMethod(WsfeClient::class, 'soapItems');
        $method->setAccessible(true);

        return $method->invoke(new WsfeClient(), $value);
    }

    private function soapField($item, string $field)
    {
        $method = new ReflectionMethod(WsfeClient::class, 'soapField');
        $method->setAccessible(true);

        return $method->invoke(new WsfeClient(), $item, $field);
    }
}
