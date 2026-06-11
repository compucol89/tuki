<?php

namespace App\Http\Controllers\FrontEnd\Shop\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Shop\OrderController;
use App\Models\BasicSettings\Basic;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\PendingBooking;
use App\Models\ShopManagement\ProductContent;
use App\Models\ShopManagement\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MercadoPagoController extends Controller
{
  private $token, $sandbox_status;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('mercadopago')->first();
    $mercadopagoData = json_decode($data->information, true);

    $this->token = $mercadopagoData['token'];
    $this->sandbox_status = $mercadopagoData['sandbox_status'];
  }

  public function enrolmentProcess(Request $request)
  {
    $allowedCurrencies = array('ARS', 'BOB', 'BRL', 'CLF', 'CLP', 'COP', 'CRC', 'CUC', 'CUP', 'DOP', 'EUR', 'GTQ', 'HNL', 'MXN', 'NIO', 'PAB', 'PEN', 'PYG', 'USD', 'UYU', 'VEF', 'VES');
    $cart_items = Session::get('cart');

    $total = 0;
    $quantity = 0;
    foreach ($cart_items as $p) {
      $total += $p['price'] * $p['qty'];
      $quantity += $p['price'] * $p['qty'];
    }
    if ($request->shipping_method) {
      $shipping_cost = ShippingCharge::where('id', $request->shipping_method)->first();
      $shipping_charge = $shipping_cost->charge;
      $shipping_method = $shipping_cost->title;
    } else {
      $shipping_charge = 0;
      $shipping_method = NULL;
    }

    $discount = Session::get('Shop_discount');
    $tax = Basic::select('shop_tax')->first();
    $tax_percentage = $tax->shop_tax;
    $total_tax_amount = ($tax_percentage / 100) * ($total - $discount);
    $grand_total = ($shipping_charge + $total + $total_tax_amount) - $discount;
    $currencyInfo = $this->getCurrencyInfo();

    // checking whether the base currency is allowed or not
    if (!in_array($currencyInfo->base_currency_text, $allowedCurrencies)) {
      return redirect()->back()->with('error', 'Invalid currency for mercadopago payment.');
    }

    if (Auth::guard('customer')->user()) {
      $user_id = Auth::guard('customer')->user()->id;
    } else {
      $user_id = 0;
    }
    $arrData = array(
      'user_id' => $user_id,
      'fname' => $request->fname,
      'lname' => $request->lname,
      'email' => $request->email,
      'phone' => $request->phone,
      'country' => $request->country,
      'state' => $request->state,
      'city' => $request->city,
      'zip_code' => $request->zip_code,
      'address' => $request->address,

      's_fname' => $request->sameas_shipping == NULL ? $request->s_fname : $request->fname,
      's_lname' => $request->sameas_shipping == NULL ? $request->s_lname : $request->lname,
      's_email' => $request->sameas_shipping == NULL ? $request->s_email : $request->email,
      's_phone' => $request->sameas_shipping == NULL ? $request->s_phone : $request->phone,
      's_country' => $request->sameas_shipping == NULL ? $request->s_country : $request->country,
      's_state' => $request->sameas_shipping == NULL ? $request->s_state : $request->state,
      's_city' => $request->sameas_shipping == NULL ? $request->s_city : $request->city,
      's_zip_code' => $request->sameas_shipping == NULL ? $request->s_zip_code : $request->zip_code,
      's_address' => $request->sameas_shipping == NULL ? $request->s_address : $request->address,

      'cart_total' => $total,
      'discount' => $discount,
      'tax_percentage' => $tax_percentage,
      'tax' => $total_tax_amount,
      'grand_total' => $grand_total,
      'currency_code' => '',

      'shipping_charge' => $shipping_charge,
      'shipping_method' => $shipping_method,
      'order_number' => uniqid(),
      'charge_id' => $request->shipping_method,

      'method' => 'MercadoPago',
      'gateway_type' => 'online',
      'payment_status' => 'completed',
      'order_status' => 'pending',
      'tnxid' => '',
    );
    $title = 'Product Order';
    $notifyURL = route('product_order.mercadopago.notify');
    $cancelURL = route('shop.checkout');
    $chargeTotal = round((float)$grand_total, 2);

    // Token único por sesión de pago para verificar external_reference
    $paymentToken = bin2hex(random_bytes(16));

    $isLocalhost = str_contains($notifyURL, 'localhost') || str_contains($notifyURL, '127.0.0.1');

    $curl = curl_init();
    $preferenceData = [
      'items' => [
        [
          'id' => uniqid(),
          'title' => $title,
          'description' => 'Product Order via MercadoPago',
          'quantity' => 1,
          'currency_id' => $currencyInfo->base_currency_text,
          'unit_price' => $chargeTotal
        ]
      ],
      'payer' => [
        'email' => $request->email
      ],
      'back_urls' => [
        'success' => $notifyURL,
        'pending' => $cancelURL,
        'failure' => $cancelURL
      ],
      'external_reference' => $paymentToken,
      'notification_url' => route('product_order.mercadopago.webhook'),
    ];

    if (!$isLocalhost) {
      $preferenceData['auto_return'] = 'approved';
    }

    $httpHeader = [
      'Authorization: Bearer ' . $this->token,
      'Content-Type: application/json',
    ];

    $url = 'https://api.mercadopago.com/checkout/preferences';

    $curlOPT = [
      CURLOPT_URL             => $url,
      CURLOPT_CUSTOMREQUEST   => 'POST',
      CURLOPT_POSTFIELDS      => json_encode($preferenceData, true),
      CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 30,
      CURLOPT_HTTPHEADER      => $httpHeader
    ];

    curl_setopt_array($curl, $curlOPT);

    $response = curl_exec($curl);
    $responseInfo = json_decode($response, true);
    if (array_key_exists('error', $responseInfo)) {
      Session::flash('error', $responseInfo['message']);
      return redirect()->route('shop.checkout');
    }

    curl_close($curl);

    // Guardar datos en sesión antes de redirigir a MercadoPago
    $request->session()->put('arrData', $arrData);
    $request->session()->put('mp_payment_token', $paymentToken);
    $request->session()->put('mp_expected_amount', $chargeTotal);

    // Persistir en DB como fallback si la sesión expira
    PendingBooking::create([
      'token' => $paymentToken,
      'event_id' => 0,
      'data' => $arrData,
      'amount' => $chargeTotal,
      'status' => 'pending',
      'expires_at' => now()->addHours(2),
    ]);

    Log::info('MercadoPago shop enrolmentProcess: preferencia creada', [
      'order_number' => $arrData['order_number'] ?? 'N/A',
      'amount' => $chargeTotal,
    ]);

    if ($this->sandbox_status == 1) {
      return redirect($responseInfo['sandbox_init_point']);
    } else {
      return redirect($responseInfo['init_point']);
    }
  }

  public function notify(Request $request)
  {
    try {
      $arrData = $request->session()->get('arrData');
      $paymentToken = $request->session()->get('mp_payment_token');
      $expectedAmount = $request->session()->get('mp_expected_amount');

      // El payment_id lo envía MercadoPago como parámetro GET en el redirect
      $paymentId = $request->get('payment_id') ?? $request->get('collection_id');

      Log::info('MercadoPago shop notify: inicio', [
        'payment_id' => $paymentId,
        'has_arrData' => !empty($arrData),
        'has_session_token' => !empty($paymentToken),
      ]);

      // Abortar si no hay payment_id (sin esto no podemos verificar nada)
      if (empty($paymentId)) {
        Log::warning('MercadoPago shop notify: sin payment_id');
        $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
        return redirect()->route('shop.checkout');
      }

    // Verificar el pago contra la API de MercadoPago (server-side)
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL            => 'https://api.mercadopago.com/v1/payments/' . intval($paymentId),
      CURLOPT_CUSTOMREQUEST  => 'GET',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 30,
      CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $this->token,
        'Content-Type: application/json',
      ],
    ]);
    $apiResponse = json_decode(curl_exec($curl), true);
    curl_close($curl);

    // Fallback a DB si la sesión expiró — recuperar datos desde PendingBooking
    $mpExternalRef = $apiResponse['external_reference'] ?? $paymentToken;
    if (empty($arrData)) {
      $pending = PendingBooking::pending()->where('token', $mpExternalRef)->first();
      if ($pending) {
        Log::info('MercadoPago shop notify: recuperado desde DB (sesión perdida)', [
          'payment_id' => $paymentId,
          'token' => $mpExternalRef,
        ]);
        $arrData = $pending->data;
        $expectedAmount = (float)$pending->amount;
      } else {
        Log::warning('MercadoPago shop notify: sin sesión ni DB fallback', [
          'payment_id' => $paymentId,
        ]);
        $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
        return redirect()->route('shop.checkout');
      }
    }

    // Validar status desde la API (nunca desde los parámetros URL)
    if (($apiResponse['status'] ?? '') !== 'approved') {
      Log::warning('MercadoPago shop notify: pago no aprobado', [
        'payment_id' => $paymentId,
        'mp_status' => $apiResponse['status'] ?? 'unknown',
      ]);
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      return redirect()->route('shop.checkout');
    }

    // Validar external_reference para evitar reutilización de pagos ajenos
    if (!empty($paymentToken) && ($apiResponse['external_reference'] ?? '') !== $paymentToken) {
      Log::warning('MercadoPago shop notify: external_reference no coincide', [
        'payment_id' => $paymentId,
        'expected' => $paymentToken,
        'received' => $apiResponse['external_reference'] ?? 'null',
      ]);
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      return redirect()->route('shop.checkout');
    }

    // Validar monto cobrado ≥ monto esperado
    $paidAmount = (float)($apiResponse['transaction_amount'] ?? 0);
    if (!empty($expectedAmount) && $paidAmount < (float)$expectedAmount) {
      Log::warning('MercadoPago shop notify: monto insuficiente', [
        'payment_id' => $paymentId,
        'expected' => $expectedAmount,
        'paid' => $paidAmount,
      ]);
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      return redirect()->route('shop.checkout');
    }

    Log::info('MercadoPago shop notify: pago verificado', [
      'payment_id' => $paymentId,
      'amount' => $paidAmount,
    ]);

    // Atomic check-and-set — evita doble orden si notify() y webhook() llegan juntos
    $token = $mpExternalRef ?: $paymentToken;
    $claimed = PendingBooking::where('token', $token)
      ->where('status', 'pending')
      ->update(['status' => 'processing']);
    if ($claimed === 0) {
      Log::info('MercadoPago shop notify: pago ya siendo procesado (atómico)', [
        'payment_id' => $paymentId,
        'token' => $token,
      ]);
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      return redirect()->route('product_order.complete');
    }

    // Pago verificado — proceder con la orden
    $enrol = new OrderController();

    // Fix #3 (P1 #2): pre-validación de stock con lockForUpdate.
    // Si algún producto del carrito no tiene stock, abortamos ANTES de
    // crear Order/Earning/Transaction para evitar registros huérfanos.
    $cart = Session::get('cart');
    $prevalidationError = null;
    if (is_array($cart)) {
      $language = $this->getLanguage();
      DB::transaction(function () use ($cart, $language, &$prevalidationError) {
        foreach ($cart as $productId => $item) {
          $product = ProductContent::join('products', 'products.id', 'product_contents.product_id')
            ->where('product_contents.language_id', $language->id)
            ->where('products.id', $productId)
            ->select('products.*', 'product_contents.title')
            ->lockForUpdate()
            ->first();

          if (!$product) {
            $prevalidationError = "Producto {$productId} no encontrado.";
            return;
          }

          $requestedQty = (int) ($item['qty'] ?? 0);
          $currentStock = (int) $product->stock;

          if ($product->type !== 'digital' && $currentStock < $requestedQty) {
            $prevalidationError = "Stock insuficiente para {$product->title}. Disponible: {$currentStock}, solicitado: {$requestedQty}.";
            return;
          }
        }
      });
    }

    if ($prevalidationError !== null) {
      PendingBooking::where('token', $token)
        ->where('status', 'processing')
        ->update(['status' => 'failed']);
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      Session::flash('error', $prevalidationError);
      return redirect()->route('shop.checkout');
    }

    // Fix #3 (P1 #2): transacción envolvente sobre la orquestación completa.
    // storeData() crea Order, Earning, Transaction. storeOders() crea OrderItems
    // y descuenta stock. Si storeOders() lanza (por carrera o validación),
    // toda la orden se rollback, sin registros huérfanos.
    $orderInfo = null;
    try {
      DB::transaction(function () use ($enrol, $arrData, &$orderInfo) {
        $orderInfo = $enrol->storeData($arrData);
        $enrol->storeOders($orderInfo);
      });
    } catch (\Exception $e) {
      PendingBooking::where('token', $token)
        ->where('status', 'processing')
        ->update(['status' => 'failed']);
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      Session::flash('error', 'No se pudo confirmar la orden: ' . $e->getMessage());
      return redirect()->route('shop.checkout');
    }

    $invoice = $enrol->generateInvoice($orderInfo);
    $orderInfo->update(['invoice_number' => $invoice]);
    $enrol->sendMail($orderInfo);

    // Marcar PendingBooking como completado
    if (!empty($mpExternalRef)) {
      PendingBooking::where('token', $mpExternalRef)
        ->where('status', 'processing')
        ->update(['status' => 'completed']);
    }

    Log::info('MercadoPago shop notify: orden completada', [
      'order_id' => $orderInfo->id,
      'payment_id' => $paymentId,
    ]);

    $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
    return redirect()->route('product_order.complete');
    } catch (\Exception $e) {
      Log::error('MercadoPago shop notify: excepción', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'payment_id' => $request->get('payment_id') ?? $request->get('collection_id'),
      ]);
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      Session::flash('error', 'No pudimos procesar el pago. Contactá a soporte.');
      return redirect()->route('shop.checkout');
    }
  }

  public function curlCalls($url)
  {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $curlData = curl_exec($curl);

    curl_close($curl);

    return $curlData;
  }

  /**
   * Webhook de MercadoPago (IPN/notification_url) para Shop.
   * Recibe notificaciones server-to-server, independiente del navegador.
   */
  public function webhook(Request $request)
  {
    try {
      $topic = $request->input('topic') ?? $request->input('type');
      $paymentId = $request->input('data.id') ?? $request->input('id');

      Log::info('MercadoPago shop webhook: recibido', [
        'topic' => $topic,
        'payment_id' => $paymentId,
      ]);

      if ($topic !== 'payment' || empty($paymentId)) {
        return response('OK', 200);
      }

      // Consultar el pago en la API de MP
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_URL            => 'https://api.mercadopago.com/v1/payments/' . intval($paymentId),
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
          'Authorization: Bearer ' . $this->token,
          'Content-Type: application/json',
        ],
      ]);
      $apiResponse = json_decode(curl_exec($curl), true);
      curl_close($curl);

      $mpStatus = $apiResponse['status'] ?? 'unknown';
      $mpExternalRef = $apiResponse['external_reference'] ?? null;

      Log::info('MercadoPago shop webhook: respuesta API', [
        'payment_id' => $paymentId,
        'status' => $mpStatus,
        'external_reference' => $mpExternalRef,
      ]);

      if ($mpStatus !== 'approved') {
        return response('OK', 200);
      }

      // Atomic check-and-set
      $claimed = PendingBooking::where('token', $mpExternalRef)
        ->where('status', 'pending')
        ->update(['status' => 'processing']);
      if ($claimed === 0) {
        Log::info('MercadoPago shop webhook: ya procesado (atómico)');
        return response('OK', 200);
      }

      $pending = PendingBooking::where('token', $mpExternalRef)->first();
      if (!$pending) {
        return response('OK', 200);
      }

      // Validar monto
      $paidAmount = (float)($apiResponse['transaction_amount'] ?? 0);
      if ($paidAmount < (float)$pending->amount) {
        Log::warning('MercadoPago shop webhook: monto insuficiente');
        $pending->update(['status' => 'failed']);
        return response('OK', 200);
      }

      // Crear orden
      $arrData = $pending->data;
      $enrol = new OrderController();
      $orderInfo = null;

      DB::transaction(function () use ($enrol, $arrData, &$orderInfo) {
        $orderInfo = $enrol->storeData($arrData);
        $enrol->storeOders($orderInfo);
      });

      $invoice = $enrol->generateInvoice($orderInfo);
      $orderInfo->update(['invoice_number' => $invoice]);
      $enrol->sendMail($orderInfo);

      $pending->update(['status' => 'completed']);

      Log::info('MercadoPago shop webhook: orden completada', [
        'order_id' => $orderInfo->id,
        'payment_id' => $paymentId,
      ]);

      return response('OK', 200);
    } catch (\Exception $e) {
      Log::error('MercadoPago shop webhook: excepción', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response('OK', 200);
    }
  }
}
