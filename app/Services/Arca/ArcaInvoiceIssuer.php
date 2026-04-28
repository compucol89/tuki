<?php

declare(strict_types=1);

namespace App\Services\Arca;

use RuntimeException;

class ArcaInvoiceIssuer
{
    public function __construct(private readonly WsfeClient $wsfe)
    {
    }

    public function previewOnly(array $payload): array
    {
        return $payload;
    }

    public function issue(array $payload): array
    {
        if (!config('arca.enable_issuing')) {
            throw new RuntimeException('ARCA issuing is disabled. Set ARCA_ENABLE_ISSUING=true to enable.');
        }

        return $this->wsfe->autorizarComprobante($payload);
    }
}
