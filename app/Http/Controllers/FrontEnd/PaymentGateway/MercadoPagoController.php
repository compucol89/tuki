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
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Language;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\PendingBooking;
use App\Services\EventAddonCartService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    $message['fname.required'] = 'El nombre es obligatorio';
    $message['lname.required'] = 'El apellido es obligatorio';
    $message['gateway.required'] = 'El medio de pago es obligatorio';
    $request->validate($rules, $message);

    $booking = new BookingController();


    $allowedCurrencies = array('ARS', 'BOB', 'BRL', 'CLF', 'CLP', 'COP', 'CRC', 'CUC', 'CUP', 'DOP', 'EUR', 'GTQ', 'HNL', 'MXN', 'NIO', 'PAB', 'PEN', 'PYG', 'USD', 'UYU', 'VEF', 'VES');
    $total = Session::get('sub_total');
    $quantity = Session::get('quantity');
    $discount = Session::get('discount');
    $total_early_bird_dicount = Session::get('total_early_bird_dicount');
    $event = Event::find($eventId);
    $selectedTickets = Session::get('selTickets');
    $eventDate = Session::get('event_date') ?: $this->resolveEventStartLabel($event);

    if (!$event) {
      Session::flash('error', 'No pudimos validar el evento de tu reserva. Por favor intentá de nuevo.');
      return redirect()->route('index');
    }

    if ($selectedTickets) {
      $quantity = collect($selectedTickets)->sum(fn ($ticket) => (int) ($ticket['qty'] ?? 0));
    }

    if ($event->event_type == 'venue' && empty($selectedTickets)) {
      Log::warning('MercadoPago bookingProcess blocked: missing selected tickets.', [
        'event_id' => $eventId,
        'email' => $request->email,
      ]);

      return redirect()->route('event.details', [
        'slug' => $this->resolveEventSlug((int) $eventId),
        'id' => $eventId,
      ])->with('error', 'No pudimos validar las entradas seleccionadas. Volvé a seleccionar tus entradas.');
    }

    if ((int) $quantity <= 0 || $eventDate === null || $eventDate === '') {
      Log::warning('MercadoPago bookingProcess blocked: incomplete booking context.', [
        'event_id' => $eventId,
        'quantity' => $quantity,
        'event_date' => $eventDate,
        'email' => $request->email,
      ]);

      return redirect()->route('event.details', [
        'slug' => $this->resolveEventSlug((int) $eventId),
        'id' => $eventId,
      ])->with('error', 'No pudimos validar la fecha o cantidad de tu reserva. Volvé a seleccionar tus entradas.');
    }

    //tax and commission end
    $basicSetting = Basic::select('commission')->first();

    $tax_amount = Session::get('tax');
    if ($total === null || $tax_amount === null) {
      Log::warning('MercadoPago bookingProcess blocked: missing checkout totals.', [
        'event_id' => $eventId,
        'total' => $total,
        'tax' => $tax_amount,
        'email' => $request->email,
      ]);

      return redirect()->route('event.details', [
        'slug' => $this->resolveEventSlug((int) $eventId),
        'id' => $eventId,
      ])->with('error', 'Tu sesión de compra expiró. Volvé a seleccionar tus entradas.');
    }

    $commission_amount = ($total * $basicSetting->commission) / 100;
    try {
      $addonSummary = app(EventAddonCartService::class)->putSummaryInSession((int) $request->event_id);
    } catch (\Exception $e) {
      Session::flash('error', $e->getMessage());
      return redirect()->route('check-out');
    }

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
      'zip_code' => $request->zip_code,
      'address' => $request->address,
      'paymentMethod' => 'Mercadopago',
      'gatewayType' => 'online',
      'paymentStatus' => 'completed',
      'event_date' => $eventDate,
      'cart_addons' => Session::get('cart_addons', []),
      'event_addons' => $addonSummary['items'],
      'event_addons_total' => $addonSummary['total'],
    );

    // Persistir selTickets para fallback DB/webhook (stock y variaciones)
    $arrData['selTickets'] = $selectedTickets;

    $completeURL = route('event_booking.mercadopago.notify');
    $cancelURL = route('event_booking.cancel', ['id' => $eventId]);
    $chargeTotal = round((float)$total + $tax_amount, 2);

    // Token único por sesión de pago para verificar external_reference
    $paymentToken = bin2hex(random_bytes(16));

    $curl = curl_init();
    $isLocalhost = str_contains($completeURL, 'localhost') || str_contains($completeURL, '127.0.0.1');
    $preferenceData = [
      'items' => [
        $this->buildEventBookingPreferenceItem((int) $eventId, (int) $quantity, $chargeTotal, $currencyInfo->base_currency_text)
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
      'notification_url' => route('event_booking.mercadopago.webhook'),
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
      return redirect()->route('check-out');
    }

    curl_close($curl);

    // Guardar datos en sesión y en DB antes de redirigir a MercadoPago
    // (DB actúa como fallback si la sesión se pierde durante el redirect cross-site)
    PendingBooking::create([
      'token' => $paymentToken,
      'event_id' => $eventId,
      'data' => $arrData,
      'amount' => $chargeTotal,
      'status' => 'pending',
      'expires_at' => now()->addHours(2),
    ]);

    $request->session()->put('eventId', $eventId);
    $request->session()->put('arrData', $arrData);
    $request->session()->put('mp_payment_token', $paymentToken);
    $request->session()->put('mp_expected_amount', $chargeTotal);

    Log::info('MercadoPago bookingProcess: preference creada', [
      'event_id' => $eventId,
      'payment_token' => $paymentToken,
      'amount' => $chargeTotal,
      'sandbox' => $this->sandbox_status,
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
      // get the information from session
      $eventId = $request->session()->get('eventId');
      $arrData = $request->session()->get('arrData');
      $paymentToken = $request->session()->get('mp_payment_token');
      $expectedAmount = $request->session()->get('mp_expected_amount');

      // El payment_id lo envía MercadoPago como parámetro GET en el redirect
      $paymentId = $request->get('payment_id') ?? $request->get('collection_id');

      Log::info('MercadoPago notify: inicio', [
        'payment_id' => $paymentId,
        'has_eventId' => !empty($eventId),
        'has_arrData' => !empty($arrData),
        'has_paymentToken' => !empty($paymentToken),
        'ip' => $request->ip(),
      ]);

      // Abortar si falta payment_id
      if (empty($paymentId)) {
        Log::warning('MercadoPago notify: payment_id vacío');
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

      $mpStatus = $apiResponse['status'] ?? 'unknown';
      $mpStatusDetail = $apiResponse['status_detail'] ?? 'unknown';
      $mpExternalRef = $apiResponse['external_reference'] ?? null;

      Log::info('MercadoPago notify: respuesta API', [
        'payment_id' => $paymentId,
        'status' => $mpStatus,
        'status_detail' => $mpStatusDetail,
        'external_reference' => $mpExternalRef,
      ]);

      // Validar status desde la API (nunca desde los parámetros URL)
      if ($mpStatus !== 'approved') {
        Log::warning('MercadoPago notify: pago no aprobado', [
          'payment_id' => $paymentId,
          'status' => $mpStatus,
          'status_detail' => $mpStatusDetail,
        ]);
        $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
        return redirect()->route('event_booking.cancel', ['id' => $eventId ?? 0]);
      }

      // Fallback a DB si la sesión se perdió durante el redirect cross-site
      if (empty($arrData) || empty($eventId)) {
        $pending = PendingBooking::pending()->where('token', $mpExternalRef)->first();
        if ($pending) {
          Log::info('MercadoPago notify: recuperado desde DB (session perdida)', [
            'payment_id' => $paymentId,
            'token' => $mpExternalRef,
          ]);
          $eventId = $pending->event_id;
          $arrData = $pending->data;
          $paymentToken = $pending->token;
          $expectedAmount = (float)$pending->amount;

          // Restaurar selTickets para storeData()
          if (!empty($arrData['selTickets'])) {
            $request->session()->put('selTickets', $arrData['selTickets']);
          }
          if (!empty($arrData['cart_addons']) && is_array($arrData['cart_addons'])) {
            $request->session()->put('cart_addons', $arrData['cart_addons']);
          }
        } else {
          Log::error('MercadoPago notify: sin sesión ni registro en DB', [
            'payment_id' => $paymentId,
            'external_reference' => $mpExternalRef,
          ]);
          $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
          return redirect()->route('event_booking.cancel', ['id' => 0]);
        }
      }

      // Validar external_reference para evitar reutilización de pagos ajenos
      if (!empty($paymentToken) && $mpExternalRef !== $paymentToken) {
        Log::warning('MercadoPago notify: external_reference no coincide', [
          'expected' => $paymentToken,
          'received' => $mpExternalRef,
        ]);
        $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
        return redirect()->route('event_booking.cancel', ['id' => $eventId]);
      }

      // Validar monto cobrado ≥ monto esperado (evita manipulación de precio)
      $paidAmount = (float)($apiResponse['transaction_amount'] ?? 0);
      if (!empty($expectedAmount) && $paidAmount < (float)$expectedAmount) {
        Log::warning('MercadoPago notify: monto insuficiente', [
          'expected' => $expectedAmount,
          'paid' => $paidAmount,
        ]);
        $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
        return redirect()->route('event_booking.cancel', ['id' => $eventId]);
      }

      // Atomic check-and-set — evita race condition con webhook()
      $token = $paymentToken ?: $mpExternalRef;
      $claimed = PendingBooking::where('token', $token)
        ->where('status', 'pending')
        ->update(['status' => 'processing']);
      if ($claimed === 0) {
        Log::info('MercadoPago notify: pago ya siendo procesado (atómico)', [
          'payment_id' => $paymentId,
          'token' => $token,
        ]);
        $existingBooking = Booking::where('paymentMethod', 'Mercadopago')
          ->where('event_id', $eventId)
          ->latest()
          ->first();
        $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
        return redirect()->route('event_booking.complete', [
          'id'         => $eventId,
          'booking_id' => $existingBooking?->id ?? 0,
        ]);
      }
      // Si llegamos aquí, somos el único proceso que crea el booking

      // Pago verificado — proceder con el booking
      $enrol = new BookingController();
      if (!empty($arrData['cart_addons']) && is_array($arrData['cart_addons'])) {
        $request->session()->put('cart_addons', $arrData['cart_addons']);
      }

      $bookingInfo['transcation_type'] = 1;

      // store the course enrolment information in database
      try {
        $bookingInfo = $enrol->storeData($arrData);
      } catch (\RuntimeException $e) {
        Log::warning('MercadoPago notify: booking validation failed', [
          'payment_id' => $paymentId,
          'error' => $e->getMessage(),
        ]);
        PendingBooking::where('token', $token)->update(['status' => 'failed']);
        $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
        $errorMessage = str_contains($e->getMessage(), 'entradas seleccionadas')
          ? $e->getMessage()
          : 'Hubo un problema al procesar tu reserva. Por favor intentá de nuevo.';
        return redirect()->route('event_booking.cancel', ['id' => $eventId])
          ->with('error', $errorMessage);
      }

      if (BillingSetting::current()->enabled) {
        ArcaInvoiceIssuingJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(30));
      }

      $ticket = DB::table('basic_settings')->select('how_ticket_will_be_send')->first();

      if ($ticket->how_ticket_will_be_send == 'instant') {
        try {
          // generate an invoice in pdf format
          $invoice = $enrol->generateInvoice($bookingInfo, $bookingInfo->event_id);

          // Verificar que se generó correctamente (debe terminar en .pdf)
          if ($invoice && substr($invoice, -4) === '.pdf') {
            //unlink qr code
            if ($bookingInfo->variation != null) {
              $variations = json_decode($bookingInfo->variation, true);
              foreach ($variations as $variation) {
                @unlink(storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
              }
            } else {
              for ($i = 1; $i <= $bookingInfo->quantity; $i++) {
                @unlink(storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $i . '.svg');
              }
            }

            // then, update the invoice field info in database
            $bookingInfo->invoice = $invoice;
            $bookingInfo->save();

            // send a mail to the customer with the invoice
            $enrol->sendMail($bookingInfo);
          } else {
            Log::error('MercadoPago: generateInvoice retornó valor inválido: ' . $invoice);
            // Generar invoice en background si falló el instantáneo
            BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(5));
          }
        } catch (\Exception $e) {
          Log::error('MercadoPago: Error generando invoice: ' . $e->getMessage());
          Log::error($e->getTraceAsString());
          // Intentar generar en background si falló
          BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(5));
        }
      } else {
        BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(10));
      }

      // Actualización atómica de earnings — evita condición de carrera en pagos simultáneos
      $revenueInc = round((float)($arrData['price'] + $bookingInfo->tax), 2);
      $earningInc = $bookingInfo['organizer_id'] != null
        ? round((float)($bookingInfo->tax + $bookingInfo->commission), 2)
        : round((float)($arrData['price'] + $bookingInfo->tax), 2);
      Earning::query()->limit(1)->update([
        'total_revenue' => DB::raw("total_revenue + {$revenueInc}"),
        'total_earning' => DB::raw("total_earning + {$earningInc}"),
      ]);

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

      // Marcar pending booking como completado
      if (!empty($mpExternalRef)) {
        PendingBooking::where('token', $mpExternalRef)
          ->where('status', 'processing')
          ->update(['status' => 'completed']);
      }

      // remove all session data
      $request->session()->forget(['event_id', 'eventId', 'selTickets', 'arrData', 'paymentId', 'discount', 'mp_payment_token', 'mp_expected_amount', 'event_addons_summary', 'event_addons_total']);

      Log::info('MercadoPago notify: booking completado', [
        'booking_id' => $bookingInfo->id,
        'payment_id' => $paymentId,
      ]);

      return redirect()->route('event_booking.complete', ['id' => $eventId, 'booking_id' => $bookingInfo->id]);
    } catch (\Exception $e) {
      Log::error('MercadoPago notify: excepción no controlada', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'payment_id' => $request->get('payment_id') ?? $request->get('collection_id'),
      ]);
      $request->session()->forget(['eventId', 'arrData', 'mp_payment_token', 'mp_expected_amount', 'discount']);
      return redirect()->route('event_booking.cancel', ['id' => $request->session()->get('eventId', 0)]);
    }
  }

  /**
   * Webhook de MercadoPago (IPN/notification_url).
   * Recibe notificaciones server-to-server, independiente del navegador del usuario.
   * Excluido de CSRF vía VerifyCsrfToken middleware.
   */
  public function webhook(Request $request)
  {
    try {
      $topic = $request->input('topic') ?? $request->input('type');
      $paymentId = $request->input('data.id') ?? $request->input('id');

      Log::info('MercadoPago webhook: recibido', [
        'topic' => $topic,
        'payment_id' => $paymentId,
        'ip' => $request->ip(),
      ]);

      if ($topic !== 'payment' || empty($paymentId)) {
        Log::warning('MercadoPago webhook: topic inválido o sin payment_id');
        return response('OK', 200);
      }

      if (!$this->hasValidWebhookSignature($request, (string) $paymentId)) {
        Log::warning('MercadoPago webhook: firma inválida', [
          'payment_id' => $paymentId,
          'ip' => $request->ip(),
        ]);
        return response('Invalid signature', 401);
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

      Log::info('MercadoPago webhook: respuesta API', [
        'payment_id' => $paymentId,
        'status' => $mpStatus,
        'external_reference' => $mpExternalRef,
      ]);

      if ($mpStatus !== 'approved') {
        Log::info('MercadoPago webhook: pago no aprobado, nada que hacer');
        return response('OK', 200);
      }

      // Atomic check-and-set — evita race condition con notify()
      $claimed = PendingBooking::where('token', $mpExternalRef)
        ->where('status', 'pending')
        ->update(['status' => 'processing']);
      if ($claimed === 0) {
        Log::info('MercadoPago webhook: pago ya siendo procesado (atómico)', [
          'payment_id' => $paymentId,
          'external_reference' => $mpExternalRef,
        ]);
        return response('OK', 200);
      }

      // Recuperar pending booking (ahora con status = processing)
      $pending = PendingBooking::where('token', $mpExternalRef)->first();
      if (!$pending) {
        Log::warning('MercadoPago webhook: no se encontró pending booking después de claim', [
          'payment_id' => $paymentId,
          'external_reference' => $mpExternalRef,
        ]);
        return response('OK', 200);
      }

      // Validar monto
      $paidAmount = (float)($apiResponse['transaction_amount'] ?? 0);
      if ($paidAmount < (float)$pending->amount) {
        Log::warning('MercadoPago webhook: monto insuficiente', [
          'expected' => $pending->amount,
          'paid' => $paidAmount,
        ]);
        $pending->update(['status' => 'failed']);
        return response('OK', 200);
      }

      // Restaurar selTickets para storeData()
      if (!empty($pending->data['selTickets'])) {
        session(['selTickets' => $pending->data['selTickets']]);
      }

      // Crear booking
      $enrol = new BookingController();
      $arrData = $pending->data;
      try {
        $bookingInfo = $enrol->storeData($arrData);
      } catch (\RuntimeException $e) {
        Log::warning('MercadoPago webhook: booking validation failed', [
          'payment_id' => $paymentId ?? 'unknown',
          'error' => $e->getMessage(),
        ]);
        if (isset($pending) && $pending) {
          $pending->update(['status' => 'failed']);
        }
        return response('OK', 200);
      }

      if (BillingSetting::current()->enabled) {
        ArcaInvoiceIssuingJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(30));
      }

      $ticket = DB::table('basic_settings')->select('how_ticket_will_be_send')->first();
      if ($ticket->how_ticket_will_be_send == 'instant') {
        try {
          $invoice = $enrol->generateInvoice($bookingInfo, $bookingInfo->event_id);
          if ($invoice && substr($invoice, -4) === '.pdf') {
            if ($bookingInfo->variation != null) {
              $variations = json_decode($bookingInfo->variation, true);
              foreach ($variations as $variation) {
                @unlink(storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
              }
            } else {
              for ($i = 1; $i <= $bookingInfo->quantity; $i++) {
                @unlink(storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $i . '.svg');
              }
            }
            $bookingInfo->invoice = $invoice;
            $bookingInfo->save();
            $enrol->sendMail($bookingInfo);
          } else {
            Log::error('MercadoPago webhook: generateInvoice inválido: ' . $invoice);
            BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(5));
          }
        } catch (\Exception $e) {
          Log::error('MercadoPago webhook: error generando invoice: ' . $e->getMessage());
          BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(5));
        }
      } else {
        BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(10));
      }

      // Actualización atómica de earnings — evita condición de carrera en pagos simultáneos
      $revenueInc = round((float)($arrData['price'] + $bookingInfo->tax), 2);
      $earningInc = $bookingInfo['organizer_id'] != null
        ? round((float)($bookingInfo->tax + $bookingInfo->commission), 2)
        : round((float)($arrData['price'] + $bookingInfo->tax), 2);
      Earning::query()->limit(1)->update([
        'total_revenue' => DB::raw("total_revenue + {$revenueInc}"),
        'total_earning' => DB::raw("total_earning + {$earningInc}"),
      ]);

      $bookingInfo['paymentStatus'] = 1;
      $bookingInfo['transcation_type'] = 1;
      storeTranscation($bookingInfo);

      $organizerData['organizer_id'] = $bookingInfo['organizer_id'];
      $organizerData['price'] = $arrData['price'];
      $organizerData['tax'] = $bookingInfo->tax;
      $organizerData['commission'] = $bookingInfo->commission;
      storeOrganizer($organizerData);

      $pending->update(['status' => 'completed']);
      // Nota: el update anterior ya fue condicionado via update atómico donde('status', 'processing')
      // al inicio del método webhook()

      Log::info('MercadoPago webhook: booking completado', [
        'booking_id' => $bookingInfo->id,
        'payment_id' => $paymentId,
      ]);

      return response('OK', 200);
    } catch (\Exception $e) {
      Log::error('MercadoPago webhook: excepción', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      return response('OK', 200);
    }
  }

  private function hasValidWebhookSignature(Request $request, string $paymentId): bool
  {
    $secret = config('services.mercadopago.webhook_secret') ?: env('MERCADOPAGO_WEBHOOK_SECRET');
    if (empty($secret)) {
      Log::debug('MercadoPago webhook: secret de firma no configurado');
      return true;
    }

    $xSignature = (string) $request->header('x-signature');
    $xRequestId = (string) $request->header('x-request-id');
    if ($xSignature === '' || $xRequestId === '') {
      return false;
    }

    $signatureParts = [];
    foreach (explode(',', $xSignature) as $part) {
      [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);
      if ($key !== null && $value !== null) {
        $signatureParts[$key] = $value;
      }
    }

    if (empty($signatureParts['ts']) || empty($signatureParts['v1'])) {
      return false;
    }

    $manifest = 'id:' . $paymentId . ';request-id:' . $xRequestId . ';ts:' . $signatureParts['ts'] . ';';
    $expected = hash_hmac('sha256', $manifest, $secret);

    return hash_equals($expected, $signatureParts['v1']);
  }

  private function buildEventBookingPreferenceItem(int $eventId, int $quantity, float $chargeTotal, string $currency): array
  {
    $eventTitle = $this->resolveEventTitle($eventId);
    $eventDate = $this->resolveEventStartLabel(Event::find($eventId));
    $dateSuffix = $eventDate ? ' — ' . $eventDate : '';

    return [
      'id' => 'event-' . $eventId,
      'title' => $eventTitle . $dateSuffix . ' — ' . $quantity . ' entrada(s)',
      'description' => $eventTitle . ($eventDate ? ' — Función: ' . $eventDate : '') . ' — ' . $quantity . ' entrada(s)',
      'quantity' => 1,
      'currency_id' => $currency,
      'unit_price' => $chargeTotal,
    ];
  }

  private function resolveEventTitle(int $eventId): string
  {
    $defaultLanguageId = optional(Language::where('is_default', 1)->first())->id;
    $query = EventContent::where('event_id', $eventId);

    if ($defaultLanguageId) {
      $defaultTitle = (clone $query)->where('language_id', $defaultLanguageId)->value('title');
      if (!empty($defaultTitle)) {
        return $defaultTitle;
      }
    }

    return $query->whereNotNull('title')->value('title') ?: 'Reserva de evento #' . $eventId;
  }

  private function resolveEventSlug(int $eventId): string
  {
    return EventContent::where('event_id', $eventId)->whereNotNull('slug')->value('slug') ?: (string) $eventId;
  }

  private function resolveEventStartLabel(?Event $event): ?string
  {
    if (!$event) {
      return null;
    }

    if ($event->date_type == 'single' && $event->start_date) {
      return Carbon::parse(trim($event->start_date . ' ' . $event->start_time))->format('d/m/Y H:i');
    }

    $date = EventDates::where('event_id', $event->id)->orderBy('start_date')->orderBy('start_time')->first();
    if (!$date) {
      return null;
    }

    return Carbon::parse(trim($date->start_date . ' ' . $date->start_time))->format('d/m/Y H:i');
  }
}
