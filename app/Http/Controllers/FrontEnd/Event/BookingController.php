<?php

namespace App\Http\Controllers\FrontEnd\Event;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use App\Http\Controllers\FrontEnd\PaymentGateway\OfflineController;
use App\Jobs\ArcaInvoiceIssuingJob;
use App\Jobs\BookingInvoiceJob;
use App\Models\BillingSetting;
use App\Models\BasicSettings\Basic;
use App\Mail\EventConfirmationMail;
use App\Models\BasicSettings\MailTemplate;
use App\Models\CustomerFiscalProfile;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Event\EventImage;
use App\Models\Event\Ticket;
use App\Models\Language;
use App\Services\EventAddonCartService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
  public function index(Request $request, $id)
  {
    $request->validate([
      'fname' => 'required|string|max:255',
      'lname' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'phone' => 'required|string|max:50',
      'dni'   => 'required|string|max:20',
    ], [
      'fname.required' => 'El nombre es obligatorio.',
      'lname.required' => 'El apellido es obligatorio.',
      'email.required' => 'El email es obligatorio.',
      'email.email'    => 'Ingresá un email válido.',
      'phone.required' => 'El teléfono es obligatorio.',
      'dni.required'   => 'El DNI es obligatorio.',
    ]);

    $basic = Basic::select('event_guest_checkout_status')->first();
    if ($basic->event_guest_checkout_status == 0 && $request->type != 'guest') {
      // check whether user is logged in or not
      if (Auth::guard('customer')->check() == false) {
        return redirect()->route('customer.login', ['redirectPath' => 'course_details']);
      }
    }

    $freePassLimit = checkSelectedFreePassLimits($id, Session::get('selTickets') ?: Session::get('freeTicketSelection'), $request->email, $request->phone, $request->input('dni'));
    if ($freePassLimit['status'] == 'true') {
      Session::flash('error', __('Alcanzaste el límite de :limit entradas gratis para este evento.', ['limit' => $freePassLimit['limit'] ?? 2]));
      return redirect()->back()->withInput();
    }

    // payment
    if ($request->total != 0 || Session::get('sub_total') != 0) {
      if (!$request->exists('gateway')) {
        Session::flash('error', 'Por favor seleccioná un método de pago.');

        return redirect()->back();
      } else if ($request['gateway'] === 'mercadopago') {
        $mercadopago = new MercadoPagoController();

        return $mercadopago->bookingProcess($request, $id);
      } else {
        if (!is_numeric((string) $request['gateway'])) {
          Session::flash('error', 'Por ahora solo Mercado Pago está habilitado como pago online.');

          return redirect()->back()->withInput();
        }

        $offline = new OfflineController();
        return $offline->bookingProcess($request, $id);
      }
    } else {
      try {
        $event = Session::get('event');
        $sessionEventId = is_object($event) ? ($event->id ?? null) : ($event['id'] ?? null);
        $event_id = (int) $id;
        if ($sessionEventId && (int) $sessionEventId !== $event_id) {
          Log::warning('Free event booking session event mismatch', [
            'route_event_id' => $event_id,
            'session_event_id' => $sessionEventId,
            'email' => $request->email,
          ]);
        }
        $eventModel = Event::find($event_id);
        if (!$eventModel || empty($eventModel->organizer_id)) {
          Log::warning('Free event booking blocked: invalid event context', [
            'route_event_id' => $id,
            'session_event_id' => $sessionEventId,
            'email' => $request->email,
          ]);

          return redirect()->route('event_booking.cancel', ['id' => $event_id ?: $id])
            ->with('error', 'No pudimos validar el evento de tu reserva. Por favor intentá de nuevo.');
        }
        $event = $event ? (array) $event : [];
        $arrData = array(
          'event_id' => $event_id,
          'price' => 0,
          'tax' => 0,
          'commission' => 0,
          'quantity' => $request->quantity,
          'discount' => 0,
          'total_early_bird_dicount' => 0,
          'currencyText' => null,
          'currencyTextPosition' => null,
          'currencySymbol' => null,
          'currencySymbolPosition' => null,
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
          'paymentMethod' => null,
          'gatewayType' => null,
          'paymentStatus' => 'free',
          'event_date' => Session::get('event_date')
        );

        $bookingInfo = $this->storeData($arrData);

        $ticket = DB::table('basic_settings')->select('how_ticket_will_be_send')->first();

        if ($ticket->how_ticket_will_be_send == 'instant') {
          // generate an invoice in pdf format
          $invoice = $this->generateInvoice($bookingInfo, $bookingInfo->event_id);

          //unlink qr code 
          if (
            $bookingInfo->variation != null
          ) {
            //generate qr code for without wise ticket
            $variations = json_decode($bookingInfo->variation, true);
            foreach ($variations as $variation) {

              @unlink(storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
            }
          } else {
            //generate qr code for without wise ticket
            for ($i = 1; $i <= $bookingInfo->quantity; $i++) {
              @unlink(storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $i .  '.svg');
            }
          }

          // then, update the invoice field info in database
          $bookingInfo->invoice = $invoice;
          $bookingInfo->save();

          // send a mail to the customer with the invoice
          $this->sendMail($bookingInfo);
        } else {
          if (BillingSetting::current()->enabled) {
            ArcaInvoiceIssuingJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(30));
          }

          BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(10));
        }

        $request->session()->forget('event_id');
        $request->session()->forget('selTickets');
        $request->session()->forget('freeTicketSelection');
        $request->session()->forget('arrData');
        $request->session()->forget('discount');

        return redirect()->route('event_booking.complete', ['id' => $event_id, 'booking_id' => $bookingInfo->id, 'via' => 'offline']);
      } catch (\RuntimeException $th) {
        Log::warning('Free event booking validation failed: ' . $th->getMessage());
        $errorMessage = str_contains($th->getMessage(), 'entradas seleccionadas')
          ? $th->getMessage()
          : 'Hubo un problema al procesar tu reserva. Por favor intentá de nuevo.';
        return redirect()->back()->with('error', $errorMessage)->withInput();
      } catch (\Throwable $th) {
        Log::error('Free event booking failed: ' . $th->getMessage() . ' | ' . $th->getFile() . ':' . $th->getLine());
        return redirect()->back()->with('error', 'Hubo un problema al procesar tu reserva. Por favor intentá de nuevo.');
      }
    }
  }

  public function storeData($info)
  {
    try {
      $event = Event::find($info['event_id']);
      if (!$event || empty($event->organizer_id)) {
        throw new \RuntimeException('Invalid booking event context.');
      }

      $organizer_id = $event->organizer_id;
      $variations = $info['selTickets'] ?? Session::get('selTickets');
      $freeLimitSelection = $variations ?: Session::get('freeTicketSelection');

      if ($freeLimitSelection) {
        $this->ensureSelectedTicketsBelongToEvent($freeLimitSelection, (int) $info['event_id']);
        $freePassLimit = checkSelectedFreePassLimits($info['event_id'], $freeLimitSelection, $info['email'] ?? null, $info['phone'] ?? null, $info['dni'] ?? null);
        if ($freePassLimit['status'] == 'true') {
          throw new \RuntimeException(__('Alcanzaste el límite de :limit entradas gratis para este evento.', ['limit' => $freePassLimit['limit'] ?? 2]));
        }
      }

      if ($variations) {
        $info['quantity'] = !empty($info['quantity'])
          ? $info['quantity']
          : collect($variations)->sum(fn ($variation) => (int) ($variation['qty'] ?? 0));

        foreach ($variations as $variation) {
          // Transacción por ticket — lockForUpdate evita overselling en pagos simultáneos
          DB::transaction(function () use ($variation) {
            $ticket = Ticket::where('id', $variation['ticket_id'])->lockForUpdate()->first();
            if (!$ticket) return;
            $requestedQty = (int) ($variation['qty'] ?? 0);
            if ($ticket->pricing_type == 'normal' && $ticket->ticket_available_type == 'limited') {
              if ((int) $ticket->ticket_available < $requestedQty) {
                throw new \RuntimeException('No hay stock disponible para la entrada seleccionada.');
              }
              $ticket->ticket_available = (int) $ticket->ticket_available - $requestedQty;
              $ticket->save();
            } elseif ($ticket->pricing_type == 'variation') {
              $ticket_variations = json_decode($ticket->variations, true);
              $update_variation = [];
              $matchedVariation = false;
              foreach ($ticket_variations as $ticket_variation) {
                if (Booking::ticketNameMatches($variation['ticket_id'], $ticket_variation['name'] ?? null, $variation['name'] ?? null)) {
                  $matchedVariation = true;
                  if ($ticket_variation['ticket_available_type'] == 'limited') {
                    if ((int) $ticket_variation['ticket_available'] < $requestedQty) {
                      throw new \RuntimeException('No hay stock disponible para la entrada seleccionada.');
                    }
                    $ticket_available = (int) $ticket_variation['ticket_available'] - $requestedQty;
                  } else {
                    $ticket_available = $ticket_variation['ticket_available'];
                  }
                  $update_variation[] = [
                    'name' => $ticket_variation['name'],
                    'price' => round($ticket_variation['price'], 2),
                    'ticket_available_type' => $ticket_variation['ticket_available_type'],
                    'ticket_available' => $ticket_available,
                    'max_ticket_buy_type' => $ticket_variation['max_ticket_buy_type'],
                    'v_max_ticket_buy' => $ticket_variation['v_max_ticket_buy'],
                  ];
                } else {
                  $update_variation[] = [
                    'name' => $ticket_variation['name'],
                    'price' => round($ticket_variation['price'], 2),
                    'ticket_available_type' => $ticket_variation['ticket_available_type'],
                    'ticket_available' => $ticket_variation['ticket_available'],
                    'max_ticket_buy_type' => $ticket_variation['max_ticket_buy_type'],
                    'v_max_ticket_buy' => $ticket_variation['v_max_ticket_buy'],
                  ];
                }
              }
              if (!$matchedVariation) {
                throw new \RuntimeException('No pudimos validar las entradas seleccionadas. Volvé a seleccionar tus entradas.');
              }
              $ticket->variations = json_encode($update_variation, true);
              $ticket->save();
            } elseif ($ticket->pricing_type == 'free' && $ticket->ticket_available_type == 'limited') {
              if ((int) $ticket->ticket_available < $requestedQty) {
                throw new \RuntimeException('No hay stock disponible para la entrada seleccionada.');
              }
              $ticket->ticket_available = (int) $ticket->ticket_available - $requestedQty;
              $ticket->save();
            }
          });
        }
        $c_variations = [];
        foreach ($variations as $variation) {
          for ($i = 1; $i <= $variation['qty']; $i++) {
            $c_variations[] = [
              'ticket_id' => $variation['ticket_id'],
              'early_bird_dicount' => $variation['early_bird_dicount'],
              'name' => Booking::displayTicketName($variation['ticket_id'], $variation['name'] ?? null),
              'qty' => 1,
              'price' => $variation['price'],
              'scan_status' => 0,
              'unique_id' => (string) Str::uuid(),
            ];
          }
        }
        $variations = json_encode($c_variations, true);
      } else {
        if ($event) {
          DB::transaction(function () use ($event, $info) {
            $ticket = $event->ticket()->lockForUpdate()->first();
            if ($ticket && $ticket->ticket_available_type == 'limited') {
              $ticket->ticket_available = max(0, $ticket->ticket_available - (int)$info['quantity']);
              $ticket->save();
            }
          });
        }
        if ($event->event_type == 'venue' && $event->tickets()->exists()) {
          throw new \RuntimeException('No pudimos validar las entradas seleccionadas. Volvé a seleccionar tus entradas.');
        }
      }

      $basic  = Basic::where('uniqid', 12345)->select('tax', 'commission')->first();

      $booking = Booking::create([
        'customer_id' => Auth::guard('customer')->user() ? Auth::guard('customer')->user()->id : null,
        'booking_id' => (string) Str::uuid(),
        'fname' => $info['fname'],
        'lname' => $info['lname'],
        'email' => $info['email'],
        'phone' => $info['phone'],
        'country' => $info['country'],
        'state' => $info['state'],
        'city' => $info['city'],
        'zip_code' => $info['zip_code'],
        'address' => $info['address'],
        'event_id' => $info['event_id'],
        'organizer_id' => $organizer_id,
        'variation' => $variations,
        'price' => round($info['price'], 2),
        'tax' => round($info['tax'], 2),
        'commission' => round($info['commission'], 2),
        'tax_percentage' => $basic->tax,
        'commission_percentage' => $basic->commission,
        'quantity' => $info['quantity'],
        'discount' => round($info['discount'], 2),
        'early_bird_discount' => round($info['total_early_bird_dicount'], 2),
        'currencyText' => $info['currencyText'],
        'currencyTextPosition' => $info['currencyTextPosition'],
        'currencySymbol' => $info['currencySymbol'],
        'currencySymbolPosition' => $info['currencySymbolPosition'],
        'paymentMethod' => $info['paymentMethod'],
        'gatewayType' => $info['gatewayType'],
        'paymentStatus' => $info['paymentStatus'],
        'invoice' => array_key_exists('attachmentFile', $info) ? $info['attachmentFile'] : null,
        'attachmentFile' => array_key_exists('attachmentFile', $info) ? $info['attachmentFile'] : null,
        'event_date' => $info['event_date'] ?? Session::get('event_date'),
        'conversation_id' => array_key_exists('conversation_id', $info) ? $info['conversation_id'] : null,
        'access_token' => Auth::guard('customer')->check() ? null : Str::random(40),
        'token_legacy_expires_at' => Auth::guard('customer')->check() ? null : now()->addDays(30),
        'fiscal_invoice_token' => Str::random(64),
      ]);

      if (!empty($info['dni'])) {
        try {
          $customerId = Auth::guard('customer')->id();
          if (
            $customerId !== null &&
            CustomerFiscalProfile::where('customer_id', $customerId)
              ->where('booking_id', '<>', $booking->id)
              ->exists()
          ) {
            $customerId = null;
          }

          CustomerFiscalProfile::updateOrCreate(
            ['booking_id' => $booking->id],
            [
              'customer_id' => $customerId,
              'full_name' => trim(($info['fname'] ?? '') . ' ' . ($info['lname'] ?? '')),
              'document_type' => 'DNI',
              'document_number' => $info['dni'],
              'iva_condition' => 'consumidor_final',
              'fiscal_address' => $info['address'] ?? null,
              'fiscal_email' => $info['email'] ?? null,
            ]
          );
        } catch (\Throwable $e) {
          Log::warning('Could not create customer fiscal profile for booking.', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage(),
          ]);
        }
      }

      app(EventAddonCartService::class)->attachToBooking($booking, $info['cart_addons'] ?? Session::get('cart_addons', []));

      return $booking;
    } catch (\Exception $th) {
      Log::error('storeData failed: ' . $th->getMessage());
      throw $th;
    }
  }

  private function ensureSelectedTicketsBelongToEvent($variations, int $eventId): void
  {
    if (!is_array($variations)) {
      Log::warning('Invalid selected tickets payload for booking.', [
        'event_id' => $eventId,
      ]);

      throw new \RuntimeException('No pudimos validar las entradas seleccionadas. Volvé a seleccionar tus entradas.');
    }

    $ticketIds = [];

    foreach ($variations as $variation) {
      if (!is_array($variation) || !isset($variation['ticket_id']) || !is_numeric($variation['ticket_id'])) {
        Log::warning('Invalid selected ticket item for booking.', [
          'event_id' => $eventId,
        ]);

        throw new \RuntimeException('No pudimos validar las entradas seleccionadas. Volvé a seleccionar tus entradas.');
      }

      $ticketIds[] = (int) $variation['ticket_id'];
    }

    $ticketIds = array_values(array_unique($ticketIds));

    if (empty($ticketIds)) {
      Log::warning('Empty selected tickets payload for booking.', [
        'event_id' => $eventId,
      ]);

      throw new \RuntimeException('No pudimos validar las entradas seleccionadas. Volvé a seleccionar tus entradas.');
    }

    $ticketEventIds = Ticket::whereIn('id', $ticketIds)->pluck('event_id', 'id');
    $invalidTicketIds = [];

    foreach ($ticketIds as $ticketId) {
      if (!$ticketEventIds->has($ticketId) || (int) $ticketEventIds->get($ticketId) !== $eventId) {
        $invalidTicketIds[] = $ticketId;
      }
    }

    if (!empty($invalidTicketIds)) {
      Log::warning('Selected tickets do not belong to booking event.', [
        'event_id' => $eventId,
        'ticket_ids' => $ticketIds,
        'invalid_ticket_ids' => $invalidTicketIds,
      ]);

      throw new \RuntimeException('Las entradas seleccionadas no corresponden a este evento. Volvé a seleccionar tus entradas.');
    }
  }


  public function complete(Request $request)
  {
    $language = $this->getLanguage();

    Session::forget('selTickets');
    Session::forget('total');
    Session::forget('quantity');
    Session::forget('total_early_bird_dicount');
    Session::forget('event');

    $id = $request->id;
    $booking_id = $request->booking_id;

    $booking = Booking::where('id', $booking_id)->firstOrFail();
    $eventId = (int) ($booking->event_id ?: $id);
    $fiscalProfile = CustomerFiscalProfile::where('booking_id', $booking_id)->first();
    app(EventAddonCartService::class)->forgetEvent($eventId);
    $information['booking'] = $booking;
    $event = Event::where('id', $eventId)->with([
      'information' => function ($query) use ($language) {
        return $query->where('language_id', $language->id)->first();
      },
      'organizer.organizer_info'
    ])->first();
    $information['event'] = $event;

    $ticketIds = $booking->variation
      ? collect(json_decode($booking->variation, true))->pluck('ticket_id')->unique()->toArray()
      : [];
    $ticketContents = [];
    if (!empty($ticketIds)) {
      $ticketContents = \App\Models\Event\TicketContent::whereIn('ticket_id', $ticketIds)
        ->where('language_id', $language->id)
        ->get()
        ->groupBy('ticket_id');
    }
    $information['ticketContents'] = $ticketContents;

    if ($event->date_type == 'multiple') {
      $start_date_time = strtotime($booking->event_date);
      $start_date_time = date('Y-m-d H:i:s', $start_date_time);

      $event_date = EventDates::where('start_date_time', $start_date_time)->where('event_id', $eventId)->first();

      $information['event_date'] = $event_date;
    }
    $information['fiscalProfile'] = $fiscalProfile;
    return view('frontend.payment.success', $information);
  }

  public function cancel($id, Request $request)
  {
    return redirect()->route('check-out');
  }

  public function sendMail($bookingInfo)
  {
    Mail::to($bookingInfo->email)->queue(new EventConfirmationMail($bookingInfo));
  }
  public function generateInvoice($bookingInfo, $eventId)
  {
    try {
      $fileName = $bookingInfo->booking_id . '.pdf';
      $directory = storage_path('app/invoices/');

      @mkdir($directory, 0775, true);

      $fileLocated = $directory . $fileName;

      //generate qr code
      @mkdir(storage_path('app/qrcodes/tmp/'), 0775, true);
      if ($bookingInfo->variation != null) {
        //generate qr code for without wise ticket
        $variations = json_decode($bookingInfo->variation, true);
        foreach ($variations as $variation) {
          QrCode::size(110)->generate($bookingInfo->booking_id . '__' . $variation['unique_id'], storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
        }
      } else {
        //generate qr code for without wise ticket
        for ($i = 1; $i <= $bookingInfo->quantity; $i++) {
          QrCode::size(110)->generate($bookingInfo->booking_id . '__' . $i, storage_path('app/qrcodes/tmp/') . $bookingInfo->booking_id . '__' . $i . '.svg');
        }
      }

      //generate qr code end

      // get course title
      $language = Language::where('is_default', 1)->first();
      $event = Event::find($bookingInfo->event_id);

      $eventInfo = EventContent::where('event_id', $bookingInfo->event_id)->where('language_id', $language->id)->first();
      if (!$eventInfo) {
        $eventInfo = EventContent::where('event_id', $bookingInfo->event_id)->first();
      }

      $width = "50%";
      $float = "right";
      $mb = "35px";
      $ml = "18px";

      $websiteInfo = Basic::first();

      PDF::loadView('frontend.event.invoice', compact('bookingInfo', 'event', 'eventInfo', 'width', 'float', 'mb', 'ml', 'language', 'websiteInfo'))->save($fileLocated);

      // Verificar que el archivo se creó correctamente
      if (!file_exists($fileLocated) || filesize($fileLocated) < 1000) {
        Log::error('generateInvoice: PDF no se creó correctamente o está vacío: ' . $fileLocated);
        return "z";
      }

      Log::info('generateInvoice: PDF generado exitosamente: ' . $fileName);
      return $fileName;
    } catch (\Exception $e) {
      Log::error('generateInvoice ERROR: ' . $e->getMessage());
      Log::error('generateInvoice TRACE: ' . $e->getTraceAsString());
      Session::flash('error', $e->getMessage());
      return "z";
    }
  }
}
