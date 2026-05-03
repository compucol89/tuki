<?php

namespace App\Http\Controllers\FrontEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Event\BookingController;
use App\Jobs\ArcaInvoiceIssuingJob;
use App\Jobs\BookingInvoiceJob;
use App\Models\BasicSettings\Basic;
use App\Models\BillingSetting;
use App\Models\Earning;
use App\Models\PaymentGateway\OfflineGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class OfflineController extends Controller
{
  public function bookingProcess(Request $request, $eventId)
  {
    $request->validate([
      'fname' => 'required',
      'lname' => 'required',
      'email' => 'required',
      'phone' => 'required',
      'country' => 'required',
      'address' => 'required',
      'gateway' => 'required',
    ]);
    $offlineGateway = OfflineGateway::find($request->gateway);

    // check whether attachment is required or not
    if ($offlineGateway->has_attachment == 1) {
      $rules = [
        'attachment' => [
          'required',
          'mimes:jpg,jpeg,png'
        ]
      ];

      $validator = Validator::make($request->all(), $rules);

      Session::flash('gatewayId', $offlineGateway->id);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator->errors())->withInput();
      }
    }

    $booking = new BookingController();

    $currencyInfo = $this->getCurrencyInfo();
    $total = Session::get('grand_total');
    $quantity = Session::get('quantity');
    $discount = Session::get('discount');
    $total_early_bird_dicount = Session::get('total_early_bird_dicount');

    //tax and commission end


    $basicSetting = Basic::select('tax', 'commission')->first();

    $tax_amount = Session::get('tax');
    $commission_amount = ($total * $basicSetting->commission) / 100;

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
      'paymentMethod' => $offlineGateway->name,
      'gatewayType' => 'offline',
      'paymentStatus' => 'pending',
    );

    if ($request->hasFile('attachment')) {
      $filename = time() . '.' . $request->file('attachment')->getClientOriginalExtension();
      @mkdir(public_path('assets/admin/file/attachments/'), 0775, true);
      $request->file('attachment')->move(public_path('assets/admin/file/attachments/'), $filename);
      $arrData['attachmentFile'] = $filename;
    }
    // store the course enrolment information in database
    $bookingInfo = $booking->storeData($arrData);

    // Billing: Arca (si está habilitado)
    if (BillingSetting::current()->enabled) {
      ArcaInvoiceIssuingJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(30));
    }

    // Invoice PDF: intentar inline, fallback a job
    $ticket = DB::table('basic_settings')->select('how_ticket_will_be_send')->first();
    if ($ticket->how_ticket_will_be_send == 'instant') {
      try {
        $invoice = $booking->generateInvoice($bookingInfo, $bookingInfo->event_id);
        if ($invoice && substr($invoice, -4) === '.pdf') {
          // Unlink QR codes temporales
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
          // Guardar referencia de invoice en booking
          $bookingInfo->invoice = $invoice;
          $bookingInfo->save();
          // Enviar email con factura adjunta
          $booking->sendMail($bookingInfo);
        } else {
          Log::error('Offline: generateInvoice retornó valor inválido: ' . $invoice);
          BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(5));
        }
      } catch (\Exception $e) {
        Log::error('Offline: Error generando invoice: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(5));
      }
    } else {
      // No instant: generar en background
      BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(10));
    }

    // Add balance to admin revenue
    $earning = Earning::first();
    if ($earning) {
      $earning->total_revenue = $earning->total_revenue + $arrData['price'] + $bookingInfo->tax;
      $earning->save();
    }

    $request->session()->forget('event_id');
    $request->session()->forget('selTickets');
    $request->session()->forget('arrData');
    $request->session()->forget('discount');

    return redirect()->route('event_booking.complete', ['id' => $eventId, 'via' => 'offline', 'booking_id' => $bookingInfo->id]);
  }
}
