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

class PaystackController extends Controller
{
  private $api_key;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('paystack')->first();
    $paystackData = json_decode($data->information, true);

    $this->api_key = $paystackData['key'];
  }

  public function enrolmentProcess(Request $request)
  {
    $currencyInfo = $this->getCurrencyInfo();

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

    // checking whether the currency is set to 'NGN' or not
    if ($currencyInfo->base_currency_text !== 'NGN') {
      return redirect()->back()->with('error', 'Invalid currency for paystack payment.')->withInput();
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

      'method' => 'Paystack',
      'gateway_type' => 'online',
      'payment_status' => 'completed',
      'order_status' => 'pending',
      'tnxid' => '',
    );

    $notifyURL = route('product_order.paystack.notify');

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL            => 'https://api.paystack.co/transaction/initialize',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST  => 'POST',
      CURLOPT_POSTFIELDS     => json_encode([
        'amount'       => intval($grand_total) * 100,
        'email'        => $request->email,
        'callback_url' => $notifyURL
      ]),
      CURLOPT_HTTPHEADER     => [
        'authorization: Bearer ' . $this->api_key,
        'content-type: application/json',
        'cache-control: no-cache'
      ]
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $transaction = json_decode($response, true);

    // put some data in session before redirect to paystack url
    $request->session()->put('arrData', $arrData);

    if ($transaction['status'] == true) {
      return redirect($transaction['data']['authorization_url']);
    } else {
      return redirect()->back()->with('error', 'Error: ' . $transaction['message'])->withInput();
    }
  }

  public function notify(Request $request)
  {
    $arrData = $request->session()->get('arrData');

    $reference = $request->get('reference') ?? $request->get('trxref') ?? '';

    // Abortar si no hay referencia o datos de sesión
    if (empty($reference) || empty($arrData)) {
      $request->session()->forget('arrData');
      return redirect()->route('shop.checkout');
    }

    // Verificar el pago contra la API de Paystack (server-side)
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL            => 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference),
      CURLOPT_CUSTOMREQUEST  => 'GET',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 30,
      CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $this->api_key,
        'Content-Type: application/json',
      ],
    ]);
    $verification = json_decode(curl_exec($curl), true);
    curl_close($curl);

    // El status debe ser 'success' según la API
    if (($verification['status'] ?? false) !== true || ($verification['data']['status'] ?? '') !== 'success') {
      $request->session()->forget('arrData');
      return redirect()->route('shop.checkout');
    }

    // Verificar monto: Paystack devuelve el monto en centavos
    $expectedAmount = round((float)($arrData['grand_total'] ?? 0), 2);
    $paidAmount = (float)($verification['data']['amount'] ?? 0) / 100;
    if ($paidAmount < $expectedAmount) {
      $request->session()->forget('arrData');
      return redirect()->route('shop.checkout');
    }

    // Pago verificado — proceder con la orden
    $enrol = new OrderController();

    $orderInfo = $enrol->storeData($arrData);
    $orderItems = $enrol->storeOders($orderInfo);

    $invoice = $enrol->generateInvoice($orderInfo);
    $orderInfo->update(['invoice_number' => $invoice]);
    $enrol->sendMail($orderInfo);

    $request->session()->forget('arrData');
    return redirect()->route('product_order.complete');
  }
}
