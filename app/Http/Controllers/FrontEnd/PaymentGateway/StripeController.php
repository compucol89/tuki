<?php

namespace App\Http\Controllers\FrontEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Event\BookingController;
use App\Jobs\ArcaInvoiceIssuingJob;
use App\Jobs\BookingInvoiceJob;
use App\Models\BillingSetting;
use App\Models\BasicSettings\Basic;
use App\Models\Earning;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Exception\UnauthorizedException;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeController extends Controller
{

  public function bookingProcess(Request $request, $eventId)
  {
    $eventId = $eventId;
    // card validation start 
    $rules = [
      'fname' => 'required',
      'lname' => 'required',
      'email' => 'required',
      'phone' => 'required',
      'country' => 'required',
      'address' => 'required',
      'gateway' => 'required',
      'stripeToken' => 'required',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }
    // card validation end

    $enrol = new BookingController();


    $currencyInfo = $this->getCurrencyInfo();
    $total = Session::get('sub_total');
    $quantity = Session::get('quantity');
    $discount = Session::get('discount');
    $total_early_bird_dicount = Session::get('total_early_bird_dicount');
    //tax and commission end
    $basicSetting = Basic::select('commission')->first();

    $tax_amount = Session::get('tax');
    $commission_amount = ($total * $basicSetting->commission) / 100;

    // changing the currency before redirect to Stripe
    if ($currencyInfo->base_currency_text !== 'USD') {
      $rate = floatval($currencyInfo->base_currency_rate);
      $convertedTotal = round(((Session::get('grand_total') + $tax_amount) / $rate), 2);
    }

    $stripeTotal = $currencyInfo->base_currency_text === 'USD' ? ($total + $tax_amount) : $convertedTotal;

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
      'paymentMethod' => 'Stripe',
      'gatewayType' => 'online',
      'paymentStatus' => 'completed',
    );

    try {
      // initialize stripe
      $stripe = new Stripe();
      $stripe = Stripe::make(Config::get('services.stripe.secret'));

      try {
        // generate token
        try {
          // generate charge
          $charge = $stripe->charges()->create([
            // 'source' => $token['id'],
            'source' => $request->stripeToken,
            'currency' => 'USD',
            'amount'   => $stripeTotal
          ]);
        } catch (\Exception $th) {
          Session::flash('error', $th->getMessage());
          return redirect()->route('check-out');
        }

        if ($charge['status'] == 'succeeded') {

          $bookingInfo['transcation_type'] = 1;

          // store the course enrolment information in database
          try {
            $bookingInfo = $enrol->storeData($arrData);
          } catch (\RuntimeException $e) {
            \Illuminate\Support\Facades\Log::warning('Stripe booking validation failed', [
              'error' => $e->getMessage(),
            ]);
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
            // generate an invoice in pdf format
            $invoice = $enrol->generateInvoice($bookingInfo, $bookingInfo->event_id);

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
            $enrol->sendMail($bookingInfo);
          } else {
            BookingInvoiceJob::dispatch($bookingInfo->id)->delay(now()->addSeconds(10));
          }

          //add blance to admin revinue
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

          // remove all session data
          $request->session()->forget('event_id');
          $request->session()->forget('selTickets');
          $request->session()->forget('arrData');
          $request->session()->forget('paymentId');
          $request->session()->forget('discount');
          return redirect()->route('event_booking.complete', [
            'id' => $eventId,
            'booking_id' => $bookingInfo->id
          ]);
        } else {
          return redirect()->route('event_booking.cancel', ['id' => $eventId]);
        }
      } catch (CardErrorException $e) {
        Session::flash('error', $e->getMessage());

        return redirect()->route('event_booking.cancel', ['id' => $eventId]);
      }
    } catch (UnauthorizedException $e) {
      Session::flash('error', $e->getMessage());

      return redirect()->route('event_booking.cancel', ['id' => $eventId]);
    }
  }
}
