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

        $this->endpoint  = $config['wsaa'][$env];
        $this->passphrase = $config['passphrase'] ?? '';
        $this->cuit      = $config['cuit'];
        $this->service   = $service;

        // Soporte dual: archivo en disco o base64 en variable de entorno (EasyPanel/Docker)
        $this->certificate = $this->resolveCredentialPath(
            $config['certificate'],
            env('ARCA_CERT_B64'),
            'arca_cert'
        );
        $this->privateKey = $this->resolveCredentialPath(
            $config['private_key'],
            env('ARCA_KEY_B64'),
            'arca_key'
        );
    }

    /**
     * Resuelve la ruta del archivo de credencial.
     * Si el archivo no existe pero hay base64 en env, lo decodifica a un archivo temporal.
     */
    protected function resolveCredentialPath(?string $filePath, ?string $base64, string $prefix): string
    {
        if ($filePath && file_exists($filePath)) {
            return $filePath;
        }

        if ($base64) {
            $tmpPath = sys_get_temp_dir() . '/' . $prefix . '_' . md5($base64) . '.pem';
            if (!file_exists($tmpPath)) {
                file_put_contents($tmpPath, base64_decode($base64));
                chmod($tmpPath, 0600);
            }
            return $tmpPath;
        }

        return (string) $filePath;
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

        if (!isset($response->loginCmsReturn)) {
            throw new Exception('WSAA returned empty response');
        }

        $xml = simplexml_load_string($response->loginCmsReturn);

        if ($xml === false || empty((string) $xml->credentials->token)) {
            throw new Exception('WSAA returned invalid or empty token');
        }

        return [
            'token' => (string) $xml->credentials->token,
            'sign'  => (string) $xml->credentials->sign,
            'expiration' => (string) $xml->header->expirationTime,
        ];
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

        $now = time();
        $tra->addChild('header');
        $tra->header->addChild('uniqueId', (string) $now);
        $tra->header->addChild('generationTime', gmdate('Y-m-d\TH:i:s.000\Z', $now - 60));
        $tra->header->addChild('expirationTime', gmdate('Y-m-d\TH:i:s.000\Z', $now + 600));

        $tra->addChild('service', $this->service);

        return $tra->asXML();
    }

    /**
     * Firma el TRA con el certificado y clave privada.
     * Retorna el mensaje CMS (PKCS#7) en base64.
     */
    protected function signTRA(string $tra): string
    {
        $tempTra = tempnam(sys_get_temp_dir(), 'tra_');
        $tempSigned = tempnam(sys_get_temp_dir(), 'signed_');

        try {
            file_put_contents($tempTra, $tra);

            $passphrase = $this->passphrase ?: null;

            // Sin PKCS7_BINARY: el body del SMIME queda en base64 directamente
            $signed = openssl_pkcs7_sign(
                $tempTra,
                $tempSigned,
                'file://' . $this->certificate,
                ['file://' . $this->privateKey, $passphrase],
                [],
                PKCS7_NOATTR
            );

            if (!$signed) {
                throw new Exception('Failed to sign TRA: ' . openssl_error_string());
            }

            $smime = file_get_contents($tempSigned);

            // El SMIME tiene headers + línea vacía + body base64
            // AFIP espera el body base64 (DER encoded) sin los headers MIME
            if (($pos = strpos($smime, "\r\n\r\n")) !== false) {
                $body = substr($smime, $pos + 4);
            } elseif (($pos = strpos($smime, "\n\n")) !== false) {
                $body = substr($smime, $pos + 2);
            } else {
                $body = $smime;
            }

            // Limpiar saltos de línea del base64
            return str_replace(["\r\n", "\r", "\n"], '', trim($body));

        } finally {
            if (file_exists($tempTra)) unlink($tempTra);
            if (file_exists($tempSigned)) unlink($tempSigned);
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
