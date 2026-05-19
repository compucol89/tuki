<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Event\Booking;

class SystemHealthCheck extends Command
{
    protected $signature = 'tukipass:health-check';
    protected $description = 'Diagnóstico completo de TukiPass para validación de producción';

    private $issues = [];
    private $warnings = [];
    private $ok = [];

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  TUKIPASS — DIAGNÓSTICO COMPLETO DE SISTEMA');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->checkBasicConfig();
        $this->checkDatabase();
        $this->checkMercadoPago();
        $this->checkPostmark();
        $this->checkArca();
        $this->checkRoutes();
        $this->checkStorage();
        $this->checkQueue();
        $this->checkSSL();

        $this->newLine();
        $this->printSummary();

        return count($this->issues) > 0 ? 1 : 0;
    }

    private function checkBasicConfig()
    {
        $this->info('─── CONFIGURACIÓN BÁSICA ───');

        $env = env('APP_ENV');
        $debug = env('APP_DEBUG');
        $url = env('APP_URL');

        if ($env === 'production') {
            $this->ok[] = "APP_ENV=production";
            $this->line("  ✅ APP_ENV: production");
        } else {
            $this->issues[] = "APP_ENV no es production (actual: {$env})";
            $this->error("  ❌ APP_ENV: {$env} (debe ser 'production')");
        }

        if ($debug === false || $debug === 'false' || $debug === 0) {
            $this->ok[] = "APP_DEBUG=false";
            $this->line("  ✅ APP_DEBUG: false");
        } else {
            $this->issues[] = "APP_DEBUG está activado ({$debug}) — riesgo de seguridad";
            $this->error("  ❌ APP_DEBUG: {$debug} (debe ser 'false')");
        }

        if (str_starts_with($url, 'https://')) {
            $this->ok[] = "APP_URL usa HTTPS";
            $this->line("  ✅ APP_URL: {$url}");
        } else {
            $this->issues[] = "APP_URL no usa HTTPS ({$url})";
            $this->error("  ❌ APP_URL: {$url} (debe usar https://)");
        }

        $this->line("  ℹ️  APP_KEY: " . substr(env('APP_KEY'), 0, 20) . '...');
        $this->line("  ℹ️  TIMEZONE: " . env('APP_TIMEZONE'));
        $this->newLine();
    }

    private function checkDatabase()
    {
        $this->info('─── BASE DE DATOS ───');

        try {
            DB::connection()->getPdo();
            $this->ok[] = "Conexión DB OK";
            $this->line("  ✅ Conexión MySQL: OK");
            $this->line("  ℹ️  Host: " . env('DB_HOST'));
            $this->line("  ℹ️  Database: " . env('DB_DATABASE'));
        } catch (\Exception $e) {
            $this->issues[] = "Error de conexión a DB: " . $e->getMessage();
            $this->error("  ❌ Conexión MySQL: FALLÓ");
        }

        try {
            $migrations = DB::table('migrations')->count();
            $this->line("  ℹ️  Migraciones ejecutadas: {$migrations}");
        } catch (\Exception $e) {
            $this->warnings[] = "No se pudo contar migraciones";
            $this->warn("  ⚠️  No se pudo verificar migraciones");
        }

        $this->newLine();
    }

    private function checkMercadoPago()
    {
        $this->info('─── MERCADOPAGO ───');

        $gateway = OnlineGateway::where('keyword', 'mercadopago')->first();

        if (!$gateway) {
            $this->issues[] = "No existe configuración de MercadoPago en online_gateways";
            $this->error("  ❌ Configuración no encontrada");
            $this->newLine();
            return;
        }

        $info = json_decode($gateway->information, true);
        $token = $info['token'] ?? null;
        $sandbox = $info['sandbox_status'] ?? null;
        $status = $gateway->status;

        // Status
        if ($status == 1) {
            $this->ok[] = "MercadoPago activo";
            $this->line("  ✅ Estado: Activo");
        } else {
            $this->issues[] = "MercadoPago desactivado (status={$status})";
            $this->error("  ❌ Estado: Inactivo");
        }

        // Sandbox
        if ($sandbox == 1 || $sandbox === '1') {
            $this->warnings[] = "MercadoPago está en modo sandbox";
            $this->warn("  ⚠️  Modo: SANDBOX (cambiar a producción)");
        } else {
            $this->ok[] = "MercadoPago en producción";
            $this->line("  ✅ Modo: Producción");
        }

        // Token
        if ($token) {
            $prefix = substr($token, 0, 6);
            if ($prefix === 'TEST-') {
                $this->warnings[] = "Token de MercadoPago es de prueba (TEST-)";
                $this->warn("  ⚠️  Token: TEST-... (cambiar a APP_USR-...)");
            } elseif ($prefix === 'APP_US') {
                $this->ok[] = "Token de MercadoPago es de producción";
                $this->line("  ✅ Token: APP_USR-... (producción)");
            } else {
                $this->warnings[] = "Token de MercadoPago tiene formato desconocido";
                $this->warn("  ⚠️  Token: formato desconocido");
            }
            $this->line("  ℹ️  Token enmascarado: " . substr($token, 0, 8) . '****' . substr($token, -4));
        } else {
            $this->issues[] = "Token de MercadoPago vacío";
            $this->error("  ❌ Token: Vacío");
        }

        // Health check API
        if ($token) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])->timeout(10)->get('https://api.mercadopago.com/users/me');

                if ($response->successful()) {
                    $data = $response->json();
                    $this->ok[] = "API MercadoPago responde";
                    $this->line("  ✅ API: Conectado (User ID: {$data['id']})");
                } else {
                    $this->issues[] = "API MercadoPago responde con error: " . $response->status();
                    $this->error("  ❌ API: Error {$response->status()} — token inválido o expirado");
                }
            } catch (\Exception $e) {
                $this->issues[] = "No se pudo conectar a API MercadoPago: " . $e->getMessage();
                $this->error("  ❌ API: No responde — " . $e->getMessage());
            }
        }

        // Último booking
        try {
            $lastBooking = Booking::latest()->first();
            if ($lastBooking) {
                $this->line("  ℹ️  Último booking: #{$lastBooking->id} — {$lastBooking->created_at}");
                $this->line("  ℹ️  Estado pago: " . ($lastBooking->paymentStatus ?? 'N/A'));
            } else {
                $this->line("  ℹ️  No hay bookings registrados");
            }
        } catch (\Exception $e) {
            $this->line("  ℹ️  No se pudo consultar bookings");
        }

        $this->newLine();
    }

    private function checkPostmark()
    {
        $this->info('─── POSTMARK (EMAIL) ───');

        $token = env('POSTMARK_TOKEN');
        $mailer = env('MAIL_MAILER');
        $from = env('MAIL_FROM_ADDRESS');

        if ($mailer === 'smtp' || $mailer === 'postmark') {
            $this->ok[] = "MAIL_MAILER configurado";
            $this->line("  ✅ Mailer: {$mailer}");
        } else {
            $this->warnings[] = "MAIL_MAILER no es smtp/postmark ({$mailer})";
            $this->warn("  ⚠️  Mailer: {$mailer}");
        }

        if ($from === 'info@tukipass.com') {
            $this->ok[] = "MAIL_FROM_ADDRESS correcto";
            $this->line("  ✅ From: {$from}");
        } else {
            $this->warnings[] = "MAIL_FROM_ADDRESS no es info@tukipass.com";
            $this->warn("  ⚠️  From: {$from}");
        }

        if ($token && strlen($token) > 30) {
            $this->line("  ℹ️  Token: " . substr($token, 0, 8) . '****' . substr($token, -4));

            try {
                $response = Http::withHeaders([
                    'X-Postmark-Server-Token' => $token,
                ])->timeout(10)->get('https://api.postmarkapp.com/server');

                if ($response->successful()) {
                    $data = $response->json();
                    $this->ok[] = "Postmark API conectada";
                    $this->line("  ✅ API: Conectado (Server: {$data['Name']})");
                    $this->line("  ℹ️  Estado: {$data['Status']}");
                } else {
                    $this->issues[] = "Postmark responde con error: " . $response->status();
                    $this->error("  ❌ API: Error {$response->status()}");
                }
            } catch (\Exception $e) {
                $this->issues[] = "No se pudo conectar a Postmark: " . $e->getMessage();
                $this->error("  ❌ API: No responde — " . $e->getMessage());
            }
        } else {
            $this->issues[] = "POSTMARK_TOKEN vacío o inválido";
            $this->error("  ❌ Token: Vacío");
        }

        $this->newLine();
    }

    private function checkArca()
    {
        $this->info('─── ARCA / AFIP ───');

        $issuing = config('arca.enable_issuing');
        $preview = config('arca.enable_preview');
        $env = config('arca.environment');
        $cuit = config('arca.cuit');
        $cert = config('arca.certificate');
        $key = config('arca.private_key');

        if ($issuing) {
            $this->warnings[] = "ARCA_ENABLE_ISSUING=true — emisión real activada";
            $this->warn("  ⚠️  Emisión real: ACTIVADA");
        } else {
            $this->ok[] = "ARCA emisión real desactivada";
            $this->line("  ✅ Emisión real: Desactivada (seguro)");
        }

        if ($preview) {
            $this->ok[] = "ARCA preview activado";
            $this->line("  ✅ Preview: Activado");
        }

        $this->line("  ℹ️  Entorno: {$env}");
        $this->line("  ℹ️  CUIT: {$cuit}");

        if ($cert && $key) {
            $this->ok[] = "Certificados ARCA configurados";
            $this->line("  ✅ Certificados: Configurados");
        } else {
            $this->issues[] = "Certificados ARCA faltantes";
            $this->error("  ❌ Certificados: Faltantes");
        }

        // Verificar punto de venta
        $ptoVenta = config('arca.punto_venta');
        $this->line("  ℹ️  Punto de venta: {$ptoVenta}");

        $this->newLine();
    }

    private function checkRoutes()
    {
        $this->info('─── RUTAS CRÍTICAS ───');

        $routes = [
            'event_booking.mercadopago.notify',
            'event_booking.mercadopago.webhook',
            'product_order.mercadopago.notify',
        ];

        foreach ($routes as $route) {
            try {
                $url = route($route);
                if (str_starts_with($url, 'https://')) {
                    $this->ok[] = "Ruta {$route} usa HTTPS";
                    $this->line("  ✅ {$route}");
                    $this->line("     → {$url}");
                } else {
                    $this->issues[] = "Ruta {$route} no usa HTTPS";
                    $this->error("  ❌ {$route}");
                    $this->error("     → {$url}");
                }
            } catch (\Exception $e) {
                $this->issues[] = "Ruta {$route} no definida";
                $this->error("  ❌ {$route}: No definida");
            }
        }

        $this->newLine();
    }

    private function checkStorage()
    {
        $this->info('─── ALMACENAMIENTO ───');

        // Storage link
        if (file_exists(public_path('storage'))) {
            $this->ok[] = "Storage link existe";
            $this->line("  ✅ public/storage: Enlazado");
        } else {
            $this->warnings[] = "Storage link no existe — ejecutar php artisan storage:link";
            $this->warn("  ⚠️  public/storage: No enlazado");
        }

        // Directorios críticos
        $dirs = [
            'storage/app' => storage_path('app'),
            'storage/logs' => storage_path('logs'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        foreach ($dirs as $name => $path) {
            if (is_writable($path)) {
                $this->line("  ✅ {$name}: Escritura OK");
            } else {
                $this->issues[] = "{$name} no tiene permisos de escritura";
                $this->error("  ❌ {$name}: Sin permisos");
            }
        }

        // ARCA cert storage
        if (Storage::disk('local')->exists('arca/cert.crt')) {
            $this->ok[] = "Certificado ARCA en storage";
            $this->line("  ✅ storage/app/arca/cert.crt: Existe");
        } else {
            $this->warnings[] = "Certificado ARCA no encontrado en storage/app/arca/";
            $this->warn("  ⚠️  storage/app/arca/cert.crt: No existe");
        }

        $this->newLine();
    }

    private function checkQueue()
    {
        $this->info('─── COLAS / QUEUE ───');

        $driver = config('queue.default');
        $this->line("  ℹ️  Driver: {$driver}");

        if ($driver === 'database') {
            try {
                $pending = DB::table('jobs')->count();
                $failed = DB::table('failed_jobs')->count();
                $this->line("  ℹ️  Jobs pendientes: {$pending}");
                $this->line("  ℹ️  Jobs fallidos: {$failed}");

                if ($failed > 0) {
                    $this->warnings[] = "Hay {$failed} jobs fallidos";
                    $this->warn("  ⚠️  Hay jobs fallidos en failed_jobs");
                }
            } catch (\Exception $e) {
                $this->line("  ℹ️  No se pudo verificar jobs");
            }
        }

        $this->newLine();
    }

    private function checkSSL()
    {
        $this->info('─── SSL / HTTPS ───');

        $url = env('APP_URL');

        if (!str_starts_with($url, 'https://')) {
            $this->issues[] = "APP_URL no usa HTTPS";
            $this->error("  ❌ APP_URL sin HTTPS");
            return;
        }

        try {
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $this->ok[] = "HTTPS responde correctamente";
                $this->line("  ✅ {$url}: Responde OK ({$response->status()})");
            } else {
                $this->warnings[] = "HTTPS responde con status {$response->status()}";
                $this->warn("  ⚠️  {$url}: Status {$response->status()}");
            }
        } catch (\Exception $e) {
            $this->warnings[] = "No se pudo verificar HTTPS: " . $e->getMessage();
            $this->warn("  ⚠️  No se pudo verificar SSL: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function printSummary()
    {
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  RESUMEN');
        $this->info('═══════════════════════════════════════════════════════════════');

        $totalOk = count($this->ok);
        $totalWarnings = count($this->warnings);
        $totalIssues = count($this->issues);

        $this->line("  ✅ OK: {$totalOk}");
        $this->line("  ⚠️  Advertencias: {$totalWarnings}");
        $this->line("  ❌ Errores: {$totalIssues}");

        $this->newLine();

        if ($totalIssues === 0 && $totalWarnings === 0) {
            $this->info('  ✅ SISTEMA LISTO PARA PRODUCCIÓN');
        } elseif ($totalIssues === 0) {
            $this->warn('  ⚠️  SISTEMA FUNCIONAL CON ADVERTENCIAS');
        } else {
            $this->error('  ❌ SISTEMA NO LISTO — REVISAR ERRORES');
        }

        $this->newLine();

        if (count($this->issues) > 0) {
            $this->error('Errores críticos:');
            foreach ($this->issues as $i => $issue) {
                $this->error("  " . ($i + 1) . ". {$issue}");
            }
            $this->newLine();
        }

        if (count($this->warnings) > 0) {
            $this->warn('Advertencias:');
            foreach ($this->warnings as $i => $warning) {
                $this->warn("  " . ($i + 1) . ". {$warning}");
            }
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════════');
    }
}
