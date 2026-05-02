<?php

namespace App\Http\Controllers\FrontEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Event\BookingController;
use App\Jobs\ArcaInvoiceIssuingJob;
use App\Jobs\BookingInvoiceJob;
use App\Models\BillingSetting;
use App\Models\BasicSettings\Basic;
use App\Models\Earning;
use App\Models\Event;
use App\Models\PaymentGateway\OnlineGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

  public function bookingProcess(Request $request, $eventId)
  {
    $rules = [
      'fname' => 'required',
      'lname' => 'required',
      'email' => 'required',
      'phone' => 'required',
      'country' => 'required',
      'address' => 'required',
      'gateway' => 'required',
    ];

    $message = [];

    $message['fname.required'] = 'The first name feild is required';
    $message['lname.required'] = 'The last name feild is required';
    $message['gateway.required'] = 'The payment gateway feild is required';
    $request->validate($rules, $message);

    $booking = new BookingController();


    $allowedCurrencies = array('ARS', 'BOB', 'BRL', 'CLF', 'CLP', 'COP', 'CRC', 'CUC', 'CUP', 'DOP', 'EUR', 'GTQ', 'HNL', 'MXN', 'NIO', 'PAB', 'PEN', 'PYG', 'USD', 'UYU', 'VEF', 'VES');
    $total = Session::get('grand_total');
    $quantity = Session::get('quantity');
    $discount = Session::get('discount');
    $total_early_bird_dicount = Session::get('total_early_bird_dicount');

    //tax and commission end
    $basicSetting = Basic::select('commission')->first();

    $tax_amount = Session::get('tax');
    $commission_amount = ($total * $basicSetting->commission) / 100;

    $currencyInfo = $this->getCurrencyInfo();

    // checking whether the base currency is allowed or not
    if (!in_array($currencyInfo->base_currency_text, $allowedCurrencies)) {
      return redirect()->back()->with('currency_error', 'Invalid currency for mercadopago payment.');
    }

    $arrData = array(
      'event_id' => $eventId,
      'price' => $total,
      'tax' => $tax_amount,
      'commission' => $commission_amount,
      'quantity' => $quantity,
      'discount' => $discount,
      'total_early_bird_dicount' => $total_early_bird_dicount,
      'currencyText' => $currencyInfo->base_currency_text,
      'currencyTextPosition' => $currencyInfo->base_currency_text_position,
      'currencySymbol' => $currencyInfo->base_currency_symbol,
      'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
      'fname' => $request->fname,
      'lname' => $request->lname,
      'email' => $request->email,
      'phone' => $request->phone,
      'dni' => $request->input('dni'),
      'country' => $request->country,
      'state' => $request->state,
      'city' => $request->city,
      'zip_code' => $request->city,
      'address' => $request->address,
      'paymentMethod' => 'Mercadopago',
      'gatewayType' => 'online',
      'paymentStatus' => 'completed',
    );

    $sessionEvent = Session::get('event');
    $eventTitle = $sessionEvent ? $sessionEvent->title : 'Event Booking';
    $completeURL = route('event_booking.mercadopago.notify');
    $cancelURL = route('event_booking.cancel', ['id' => $eventId]);
    $chargeTotal = round((float)$total + $tax_amount, 2);

    // Token único por sesión de pago para verificar external_reference
    $paymentToken = bin2hex(random_bytes(16));

    $curl = curl_init();
    $isLocalhost = str_contains($completeURL, 'localhost') || str_contains($completeURL, '127.0.0.1');
    $preferenceData = [
      'items' => [
        [
          'id' => uniqid(),
          'title' => $eventTitle . ' — ' . $quantity . ' entrada(s)',
          'description' => $eventTitle . ' (' . $quantity . ' entrada(s))',
          'quantity' => 1,
          'currency_id' => $currencyInfo->base_currency_text,
          'unit_price' => $chargeTotal
        ]
      ],
      'payer' => [
        'email' => $request->email
      ],
      'back_urls' => [
        'success' => $completeURL,
        'pending' => $cancelURL,
        'failure' => $cancelURL
      ],
      'external_reference' => $paymentToken,
    ];
    if (!$isLocalhost) {
      $preferenceData['auto_return'] = 'approved';
    }

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
      return redirect()->route('check-out');
    }

    curl_close($curl);

    // Guardar datos en sesión antes de redirigir a MercadoPago
    $request->session()->put('eventId', $eventId);
    $request->session()->put('arrData', $arrData);
    $request->session()->put('mp_payment_token', $paymentToken);
    $request->session()->put('mp_expected_amount', $chargeTotal);

    if ($this->sandbox_status == 1) {
      return redirect($responseInfo['sandbox_init_point']);
    } else {
      return redirect($responseInfo['init_point']);
    }
  }

  public function notify(Request $request)
  {
    // get the information from session
    $eventId = $request->session()->get('eventId');
    $arrData = $request->session()->get('arrData');
    $paymentToken = $request->session()->get('mp_payment_token');
    $expectedAmount = $request->session()->get('mp_expected_amount');

    // El payment_id lo envía MercadoPago como parámetro GET en el redirect
    $paymentId = $request->get('payment_id') ?? $request->get('collection_id');

    // Abortar si faltan datos de sesión o payment_id
    if (empty($paymentId) || empty($arrData) || empty($eventId)) {
      $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
      return redirect()->route('event_booking.cancel', ['id' => $eventId ?? 0]);
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
      $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
      return redirect()->route('event_booking.cancel', ['id' => $eventId]);
    }

    // Validar external_reference para evitar reutilización de pagos ajenos
    if (!empty($paymentToken) && ($apiResponse['external_reference'] ?? '') !== $paymentToken) {
      $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
      return redirect()->route('event_booking.cancel', ['id' => $eventId]);
    }

    // Validar monto cobrado ≥ monto esperado (evita manipulación de precio)
    $paidAmount = (float)($apiResponse['transaction_amount'] ?? 0);
    if (!empty($expectedAmount) && $paidAmount < (float)$expectedAmount) {
      $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
      return redirect()->route('event_booking.cancel', ['id' => $eventId]);
    }

    // Pago verificado — proceder con el booking
    $enrol = new BookingController();

    $bookingInfo['transcation_type'] = 1;

    // store the course enrolment information in database
    $bookingInfo = $enrol->storeData($arrData);

    if (BillingSetting::current()->enabled) {
      ArcaInvoiceIssuingJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(30));
    }

    $ticket = DB::table('basic_settings')->select('how_ticket_will_be_send')->first();

    if ($ticket->how_ticket_will_be_send == 'instant') {
      // generate an invoice in pdf format
      $invoice = $enrol->generateInvoice($bookingInfo, $bookingInfo->event_id);

      //unlink qr code
      if ($bookingInfo->variation != null) {
        $variations = json_decode($bookingInfo->variation, true);
        foreach ($variations as $variation) {
          @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
        }
      } else {
        for ($i = 1; $i <= $bookingInfo->quantity; $i++) {
          @unlink(public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '__' . $i . '.svg');
        }
      }

      // then, update the invoice field info in database
      $bookingInfo->invoice = $invoice;
      $bookingInfo->save();

      // send a mail to the customer with the invoice
      $enrol->sendMail($bookingInfo);
    } else {
      BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(10));
    }

    //add blance to admin revinue
    $earning = Earning::first();
    $earning->total_revenue = $earning->total_revenue + $arrData['price'] + $bookingInfo->tax;
    if ($bookingInfo['organizer_id'] != null) {
      $earning->total_earning = $earning->total_earning + ($bookingInfo->tax + $bookingInfo->commission);
    } else {
      $earning->total_earning = $earning->total_earning + $arrData['price'] + $bookingInfo->tax;
    }
    $earning->save();

    //storeTransaction
    $bookingInfo['paymentStatus'] = 1;
    $bookingInfo['transcation_type'] = 1;

    storeTranscation($bookingInfo);

    //store amount to organizer
    $organizerData['organizer_id'] = $bookingInfo['organizer_id'];
    $organizerData['price'] = $arrData['price'];
    $organizerData['tax'] = $bookingInfo->tax;
    $organizerData['commission'] = $bookingInfo->commission;
    storeOrganizer($organizerData);

    // remove all session data
    $request->session()->forget(['event_id', 'eventId', 'selTickets', 'arrData', 'paymentId', 'discount', 'mp_payment_token', 'mp_expected_amount']);
    return redirect()->route('event_booking.complete', ['id' => $eventId, 'booking_id' => $bookingInfo->id]);
  }
}
