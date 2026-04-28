<?php

namespace App\Console\Commands;

use App\Services\Arca\WsaaClient;
use App\Services\Arca\WsfeClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ArcaTestConnection extends Command
{
    protected $signature = 'arca:test-connection {--cert : Solo validar certificado} {--status : Solo verificar estado de servidores}';
    protected $description = 'Testea la conexión con ARCA/AFIP (WSAA + WSFEv1) en ambiente de homologación o producción';

    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║        ARCA / AFIP — Connection Test                     ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->line('');

        $env = config('arca.environment', 'homologation');
        $this->info("Environment: {$env}");
        $this->info("Issuing enabled: " . (config('arca.enable_issuing') ? 'YES' : 'NO'));
        $this->info("Preview enabled: " . (config('arca.enable_preview') ? 'YES' : 'NO'));
        $this->info("CUIT: " . $this->maskCuit(config('arca.cuit')));
        $this->line('');

        $exitCode = 0;

        // 1. Validar configuración
        $exitCode = $this->checkConfiguration() ?: $exitCode;
        $this->line('');

        // 2. Validar certificado
        if ($this->option('status')) {
            // Skip certificate check if only checking server status
        } else {
            $exitCode = $this->checkCertificate() ?: $exitCode;
            $this->line('');
        }

        // 3. Solo validar certificado
        if ($this->option('cert')) {
            return $exitCode;
        }

        // 4. Test WSAA (autenticación)
        $exitCode = $this->testWsaa() ?: $exitCode;
        $this->line('');

        // 5. Test WSFEv1 (estado de servidores)
        $exitCode = $this->testWsfeStatus() ?: $exitCode;
        $this->line('');

        // 6. Test WSFEv1 (parámetros)
        $exitCode = $this->testWsfeParams() ?: $exitCode;
        $this->line('');

        if ($exitCode === 0) {
            $this->info('✅ All tests passed. ARCA/AFIP connection is working.');
        } else {
            $this->error('❌ Some tests failed. Check the output above.');
        }

        return $exitCode;
    }

    protected function checkConfiguration(): int
    {
        $this->warn('── Configuration ──');

        $required = [
            'ARCA_CUIT' => config('arca.cuit'),
            'ARCA_CERT_PATH' => config('arca.certificate'),
            'ARCA_KEY_PATH' => config('arca.private_key'),
        ];

        $missing = [];
        foreach ($required as $key => $value) {
            if (empty($value)) {
                $missing[] = $key;
                $this->error("  ✗ {$key} is not set");
            } else {
                $this->info("  ✓ {$key} is set");
            }
        }

        if (!empty($missing)) {
            $this->error("  Missing: " . implode(', ', $missing));
            return 1;
        }

        // Verificar que los archivos existan
        $certPath = config('arca.certificate');
        $keyPath = config('arca.private_key');

        if (!File::exists($certPath)) {
            $this->error("  ✗ Certificate file not found: {$certPath}");
            return 1;
        }
        $this->info("  ✓ Certificate file exists: {$certPath}");

        if (!File::exists($keyPath)) {
            $this->error("  ✗ Private key file not found: {$keyPath}");
            return 1;
        }
        $this->info("  ✓ Private key file exists: {$keyPath}");

        return 0;
    }

    protected function checkCertificate(): int
    {
        $this->warn('── Certificate Validation ──');

        try {
            $wsaa = new WsaaClient();
            $result = $wsaa->validateCertificate();

            if ($result['valid']) {
                $this->info("  ✓ Certificate is valid");
                $this->info("  Subject: {$result['subject']}");
                $this->info("  Issuer: {$result['issuer']}");
                $this->info("  Expires: {$result['expires']} ({$result['days_remaining']} days remaining)");

                if ($result['days_remaining'] < 30) {
                    $this->warn("  ⚠ Certificate expires in less than 30 days!");
                }

                return 0;
            } else {
                $this->error("  ✗ Certificate is invalid: {$result['error']}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("  ✗ Certificate validation failed: {$e->getMessage()}");
            return 1;
        }
    }

    protected function testWsaa(): int
    {
        $this->warn('── WSAA Authentication ──');

        try {
            $wsaa = new WsaaClient();
            $ta = $wsaa->requestNewTA();

            $this->info("  ✓ Authentication successful");
            $this->info("  TA expires: {$ta['expiration']}");

            return 0;
        } catch (\Exception $e) {
            $this->error("  ✗ WSAA authentication failed: {$e->getMessage()}");
            return 1;
        }
    }

    protected function testWsfeStatus(): int
    {
        $this->warn('── WSFEv1 Server Status ──');

        try {
            $wsfe = new WsfeClient();
            $status = $wsfe->getServerStatus();

            $this->info("  AppServer: {$status['appserver']}");
            $this->info("  DbServer:  {$status['dbserver']}");
            $this->info("  AuthServer: {$status['authserver']}");

            if ($status['appserver'] === 'OK' && $status['dbserver'] === 'OK' && $status['authserver'] === 'OK') {
                $this->info("  ✓ All servers are operational");
                return 0;
            } else {
                $this->warn("  ⚠ Some servers are not responding normally");
                return 0;
            }
        } catch (\Exception $e) {
            $this->error("  ✗ WSFEv1 status check failed: {$e->getMessage()}");
            return 1;
        }
    }

    protected function testWsfeParams(): int
    {
        $this->warn('── WSFEv1 Parameters ──');

        try {
            $wsfe = new WsfeClient();

            // Test tipos de comprobante
            $tiposCbte = $wsfe->getTiposComprobante();
            $this->info("  ✓ Tipos de comprobante: " . count($tiposCbte) . " disponibles");

            // Verificar que Factura C (tipo 6) exista
            if (isset($tiposCbte[6])) {
                $this->info("  ✓ Factura C (tipo 6): {$tiposCbte[6]['descripcion']}");
            } else {
                $this->warn("  ⚠ Factura C (tipo 6) not found in available types");
            }

            // Test tipos de moneda
            $tiposMoneda = $wsfe->getTiposMoneda();
            if (isset($tiposMoneda['PES'])) {
                $this->info("  ✓ Moneda PES: {$tiposMoneda['PES']}");
            }

            // Test último comprobante
            $ultimo = $wsfe->getLastComprobante(6);
            $this->info("  ✓ Último comprobante (Factura C): {$ultimo}");

            return 0;
        } catch (\Exception $e) {
            $this->error("  ✗ WSFEv1 parameters check failed: {$e->getMessage()}");
            return 1;
        }
    }

    protected function maskCuit(?string $cuit): string
    {
        if (!$cuit) {
            return 'no configurado';
        }

        if (strlen($cuit) < 11) {
            return '***';
        }

        return substr_replace($cuit, '***', 4, 7);
    }
}
