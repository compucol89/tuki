<?php

namespace App\Http\Controllers\FrontEnd\Shop\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Shop\OrderController;
use App\Models\BasicSettings\Basic;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\ShopManagement\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
      's_zip_code' => $request->sameas_shipping == NULL ? $request->s_city : $request->city,
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
      'auto_return' => 'approved'
    ];

    $httpHeader = ['Content-Type: application/json'];

    $url = 'https://api.mercadopago.com/checkout/preferences?access_token=' . $this->token;

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

    if ($this->sandbox_status == 1) {
      // Forzar dominio sandbox si el token APP_USR no redirige correctamente
      $redirectUrl = $responseInfo['sandbox_init_point'] ?? $responseInfo['init_point'];
      $redirectUrl = str_replace('https://www.mercadopago.com.ar', 'https://sandbox.mercadopago.com.ar', $redirectUrl);
      $redirectUrl = str_replace('https://www.mercadopago.com', 'https://sandbox.mercadopago.com', $redirectUrl);
      return redirect($redirectUrl);
    } else {
      return redirect($responseInfo['init_point']);
    }
  }

  public function notify(Request $request)
  {
    $arrData = $request->session()->get('arrData');
    $paymentToken = $request->session()->get('mp_payment_token');
    $expectedAmount = $request->session()->get('mp_expected_amount');

    // El payment_id lo envía MercadoPago como parámetro GET en el redirect
    $paymentId = $request->get('payment_id') ?? $request->get('collection_id');

    // Abortar si faltan datos de sesión o payment_id
    if (empty($paymentId) || empty($arrData)) {
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

    // Validar status desde la API (nunca desde los parámetros URL)
    if (($apiResponse['status'] ?? '') !== 'approved') {
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      return redirect()->route('shop.checkout');
    }

    // Validar external_reference para evitar reutilización de pagos ajenos
    if (!empty($paymentToken) && ($apiResponse['external_reference'] ?? '') !== $paymentToken) {
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      return redirect()->route('shop.checkout');
    }

    // Validar monto cobrado ≥ monto esperado
    $paidAmount = (float)($apiResponse['transaction_amount'] ?? 0);
    if (!empty($expectedAmount) && $paidAmount < (float)$expectedAmount) {
      $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
      return redirect()->route('shop.checkout');
    }

    // Pago verificado — proceder con la orden
    $enrol = new OrderController();

    $orderInfo = $enrol->storeData($arrData);
    $orderItems = $enrol->storeOders($orderInfo);

    $invoice = $enrol->generateInvoice($orderInfo);
    $orderInfo->update(['invoice_number' => $invoice]);
    $enrol->sendMail($orderInfo);

    $request->session()->forget(['arrData', 'mp_payment_token', 'mp_expected_amount']);
    return redirect()->route('product_order.complete');
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
}
