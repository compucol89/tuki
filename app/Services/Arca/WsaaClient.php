<?php

namespace App\Services\Arca;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WsaaClient
{
    protected string $endpoint;
    protected string $certificate;
    protected string $privateKey;
    protected string $passphrase;
    protected string $cuit;
    protected string $service;

    public function __construct(string $service = 'wsfe')
    {
        $config = config('arca');
        $env = $config['environment'] ?? 'homologation';

        $this->endpoint = $config['wsaa'][$env];
        $this->certificate = $config['certificate'];
        $this->privateKey = $config['private_key'];
        $this->passphrase = $config['passphrase'] ?? '';
        $this->cuit = $config['cuit'];
        $this->service = $service;
    }

    /**
     * Obtiene un Ticket de Acceso (TA) válido o renueva si expiró.
     * Los TA son válidos por 12 horas. Cacheamos con margen de seguridad.
     */
    public function getTicketAcceso(): array
    {
        $cacheKey = "arca.ta.{$this->service}";

        return Cache::remember($cacheKey, now()->addHours(10), function () {
            return $this->requestNewTA();
        });
    }

    /**
     * Solicita un nuevo Ticket de Acceso al WSAA.
     */
    public function requestNewTA(): array
    {
        $tra = $this->createTRA();
        $cms = $this->signTRA($tra);

        $client = new \SoapClient($this->endpoint, [
            'soap_version' => SOAP_1_2,
            'trace' => true,
            'exceptions' => true,
        ]);

        $response = $client->loginCms(['in0' => $cms]);

        if (isset($response->loginCmsReturn)) {
            $xml = simplexml_load_string($response->loginCmsReturn);

            if ($xml->header->estatus === 'OK') {
                return [
                    'token' => (string) $xml->credentials->token,
                    'sign' => (string) $xml->credentials->sign,
                    'expiration' => (string) $xml->header->expirationtime,
                ];
            }

            throw new Exception("WSAA error: {$xml->header->error}");
        }

        throw new Exception('WSAA returned empty response');
    }

    /**
     * Crea el Ticket de Request de Acceso (TRA) en XML.
     */
    protected function createTRA(): string
    {
        $tra = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0"></loginTicketRequest>'
        );

        $tra->addChild('header');
        $tra->header->addChild('uniqueId', date('U'));
        $tra->header->addChild('generation', date('c', date('U') - 60));
        $tra->header->addChild('expiration', date('c', date('U') + 600));

        $tra->addChild('service', $this->service);

        return $tra->asXML();
    }

    /**
     * Firma el TRA con el certificado y clave privada.
     * Retorna el mensaje CMS (PKCS#7) en base64.
     */
    protected function signTRA(string $tra): string
    {
        $cert = file_get_contents($this->certificate);
        $key = file_get_contents($this->privateKey);

        $tempTra = tempnam(sys_get_temp_dir(), 'tra_');
        $tempSigned = tempnam(sys_get_temp_dir(), 'signed_');

        try {
            file_put_contents($tempTra, $tra);

            $passphrase = $this->passphrase ?: null;

            $signed = openssl_pkcs7_sign(
                $tempTra,
                $tempSigned,
                $cert,
                [$key, $passphrase],
                [],
                PKCS7_BINARY | PKCS7_NOATTR
            );

            if (!$signed) {
                throw new Exception('Failed to sign TRA. Check certificate and private key.');
            }

            $signedContent = file_get_contents($tempSigned);

            // Extraer solo el body del CMS
            $parts = explode("\n\n", $signedContent, 2);

            return base64_encode($parts[1] ?? $signedContent);
        } finally {
            if (file_exists($tempTra)) {
                unlink($tempTra);
            }
            if (file_exists($tempSigned)) {
                unlink($tempSigned);
            }
        }
    }

    /**
     * Verifica que los certificados sean válidos y no estén expirados.
     */
    public function validateCertificate(): array
    {
        try {
            $certContent = file_get_contents($this->certificate);
            $info = openssl_x509_parse($certContent);

            if ($info === false) {
                return ['valid' => false, 'error' => 'Certificate is not readable'];
            }

            $now = time();
            $validFrom = $info['validFrom_time_t'];
            $validTo = $info['validTo_time_t'];

            if ($now < $validFrom) {
                return ['valid' => false, 'error' => 'Certificate not yet valid'];
            }

            if ($now > $validTo) {
                return ['valid' => false, 'error' => 'Certificate expired'];
            }

            $daysRemaining = (int) (($validTo - $now) / 86400);

            return [
                'valid' => true,
                'subject' => $info['subject']['CN'] ?? 'Unknown',
                'issuer' => $info['issuer']['O'] ?? 'Unknown',
                'expires' => date('Y-m-d', $validTo),
                'days_remaining' => $daysRemaining,
            ];
        } catch (Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }
}
