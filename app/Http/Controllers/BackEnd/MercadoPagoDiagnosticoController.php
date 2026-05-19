<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Event\Booking;
use App\Models\PaymentGateway\OnlineGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class MercadoPagoDiagnosticoController extends Controller
{
    private function getGateway(): ?OnlineGateway
    {
        return OnlineGateway::where('keyword', 'mercadopago')->first();
    }

    private function getToken(OnlineGateway $gateway): string
    {
        $info = json_decode($gateway->information, true);
        return $info['token'] ?? '';
    }

    private function maskToken(string $token): string
    {
        if (strlen($token) < 12) {
            return str_repeat('*', strlen($token));
        }
        return substr($token, 0, 8) . '****' . substr($token, -4);
    }

    private function detectMode(string $token): string
    {
        if (str_starts_with($token, 'TEST-')) {
            return 'Sandbox';
        }
        if (str_starts_with($token, 'APP_USR-')) {
            return 'Producción';
        }
        return 'Desconocido';
    }

    public function index(): View
    {
        $gateway = $this->getGateway();

        if (!$gateway) {
            return view('backend.mercadopago.diagnostico', [
                'configured' => false,
            ]);
        }

        $info = json_decode($gateway->information, true);
        $token = $info['token'] ?? '';
        $sandboxStatus = $info['sandbox_status'] ?? '0';

        $lastBooking = Booking::where('paymentMethod', 'Mercadopago')->latest()->first();

        return view('backend.mercadopago.diagnostico', [
            'configured'    => true,
            'active'        => (int) $gateway->status === 1,
            'sandbox'       => (string) $sandboxStatus === '1',
            'mode'          => $this->detectMode($token),
            'maskedToken'   => $this->maskToken($token),
            'lastBooking'   => $lastBooking,
        ]);
    }

    public function testConnection(): JsonResponse
    {
        $gateway = $this->getGateway();

        if (!$gateway) {
            return response()->json(['success' => false, 'message' => 'MercadoPago no está configurado.']);
        }

        $token = $this->getToken($gateway);

        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'Token no configurado.']);
        }

        $ch = curl_init('https://api.mercadopago.com/users/me');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::info('MP Diagnóstico: testConnection falló con error cURL.', ['error' => $curlError]);
            return response()->json(['success' => false, 'message' => 'Error de red: ' . $curlError]);
        }

        $data = json_decode($response, true);

        if ($httpCode === 200) {
            $userId = $data['id'] ?? null;
            $nickname = $data['nickname'] ?? null;
            Log::info('MP Diagnóstico: testConnection exitoso.', ['user_id' => $userId]);
            return response()->json([
                'success' => true,
                'message' => 'Conexión exitosa',
                'user_id' => $userId,
                'nickname' => $nickname,
            ]);
        }

        $mpMessage = $data['message'] ?? $data['error'] ?? 'Error desconocido';
        Log::info('MP Diagnóstico: testConnection falló.', ['http_code' => $httpCode, 'mp_message' => $mpMessage]);

        return response()->json([
            'success' => false,
            'message' => "Error {$httpCode}: {$mpMessage}",
        ]);
    }

    public function testPreference(): JsonResponse
    {
        $gateway = $this->getGateway();

        if (!$gateway) {
            return response()->json(['success' => false, 'message' => 'MercadoPago no está configurado.']);
        }

        $token = $this->getToken($gateway);

        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'Token no configurado.']);
        }

        $payload = json_encode([
            'items' => [[
                'title'       => 'Test TukiPass',
                'quantity'    => 1,
                'currency_id' => 'ARS',
                'unit_price'  => 1.00,
            ]],
        ]);

        $ch = curl_init('https://api.mercadopago.com/checkout/preferences');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::info('MP Diagnóstico: testPreference falló con error cURL.', ['error' => $curlError]);
            return response()->json(['success' => false, 'message' => 'Error de red: ' . $curlError]);
        }

        $data = json_decode($response, true);

        if ($httpCode === 201) {
            $preferenceId = $data['id'] ?? null;
            Log::info('MP Diagnóstico: testPreference exitoso.', ['preference_id' => $preferenceId]);
            return response()->json([
                'success'       => true,
                'message'       => 'Preferencia creada correctamente',
                'preference_id' => $preferenceId,
            ]);
        }

        $mpMessage = $data['message'] ?? $data['error'] ?? 'Error desconocido';
        Log::info('MP Diagnóstico: testPreference falló.', ['http_code' => $httpCode, 'mp_message' => $mpMessage]);

        return response()->json([
            'success' => false,
            'message' => "Error {$httpCode}: {$mpMessage}",
        ]);
    }
}
