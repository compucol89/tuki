<?php

namespace App\Http\Controllers\FrontEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Event\BookingController;
use App\Jobs\BookingInvoiceJob;
use App\Models\BasicSettings\Basic;
use App\Models\Earning;
use App\Models\PaymentGateway\OnlineGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class XenditController extends Controller
{
    public function makePayment(Request $request, $event_id)
    {
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Booking Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $currencyInfo = $this->getCurrencyInfo();
        $allowed_currency = array('IDR', 'PHP', 'USD', 'SGD', 'MYR');
        if (!in_array($currencyInfo->base_currency_text, $allowed_currency)) {
            return back()->with(['alert-type' => 'error', 'message' => 'Invalid Currency.']);
        }

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

        $total = Session::get('sub_total');
        $quantity = Session::get('quantity');
        $discount = Session::get('discount');

        //tax and commission end
        $basicSetting = Basic::select('commission')->first();

        $tax_amount = Session::get('tax');
        $commission_amount = ($total * $basicSetting->commission) / 100;

        $total_early_bird_dicount = Session::get('total_early_bird_dicount');
        // changing the currency before redirect to PayPal


        $arrData = array(
            'event_id' => $event_id,
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
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'paymentMethod' => 'Xendit',
            'gatewayType' => 'online',
            'paymentStatus' => 'completed',
        );

        $payable_amount = round($total + $tax_amount, 2);
        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Booking End ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

        /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~ Payment Gateway Info ~~~~~~~~~~~~~~
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        $external_id = Str::random(10);
        $secret_key = 'Basic ' . config('xendit.key_auth');

        $data_request = Http::withHeaders([
            'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
            'external_id' => $external_id,
            'amount' => $payable_amount,
            'currency' => $currencyInfo->base_currency_text,
            'success_redirect_url' => route('event_booking.xindit.notify')
        ]);
        $response = $data_request->object();
        $response = json_decode(json_encode($response), true);
        if (!empty($response['success_redirect_url'])) {
            $request->session()->put('event_id', $event_id);
            $request->session()->put('arrData', $arrData);
            $request->session()->put('xendit_id', $response['id']);
            $request->session()->put('secret_key', config('xendit.key_auth'));
            $request->session()->put('xendit_payment_type', 'event');

            return redirect($response['invoice_url']);
        } else {
            return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => $response['message']]);
        }
    }

    public function callback($request)
    {
        $data = $request->all();
        if ($data['status'] == 'PAID') {
            // get the information from session
            $event_id = Session::get('event_id');
            $arrData = Session::get('arrData');
            $booking = new BookingController();

            // store the course enrolment information in database
            $bookingInfo = $booking->storeData($arrData);

            $ticket = DB::table('basic_settings')->select('how_ticket_will_be_send')->first();

            if ($ticket->how_ticket_will_be_send == 'instant') {
                // generate an invoice in pdf format
                $invoice = $booking->generateInvoice($bookingInfo, $bookingInfo->event_id);

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
                $booking->sendMail($bookingInfo);
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
            Session::forget('event_id');
            Session::forget('selTickets');
            Session::forget('arrData');
            Session::forget('paymentId');
            Session::forget('discount');
            Session::forget('xendit_id');
            Session::forget('secret_key');
            Session::forget('xendit_payment_type');
            return redirect()->route('event_booking.complete', ['id' => $event_id, 'booking_id' => $bookingInfo->id]);
        } else {
            return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => 'Payment failed']);
        }
    }

    // return to success page
    public function notify(Request $request)
    {
        $xendit_id = Session::get('xendit_id');
        $secret_key = Session::get('secret_key');
        if (!is_null($xendit_id) && $secret_key == config('xendit.key_auth')) {
            // get the information from session
            $event_id = Session::get('event_id');
            $arrData = Session::get('arrData');
            $booking = new BookingController();

            // store the course enrolment information in database
            $bookingInfo = $booking->storeData($arrData);

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
            Session::forget('event_id');
            Session::forget('selTickets');
            Session::forget('arrData');
            Session::forget('paymentId');
            Session::forget('discount');
            Session::forget('xendit_id');
            Session::forget('secret_key');
            Session::forget('xendit_payment_type');
            return redirect()->route('event_booking.complete', ['id' => $event_id, 'booking_id' => $bookingInfo->id]);
        } else {
            return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => 'Payment failed']);
        }
    }
}
