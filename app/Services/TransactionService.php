<?php

namespace App\Services;

use App\Models\Organizer;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
  public function storeBookingTransaction($booking): Transaction
  {
    $organizer = Organizer::query()->find($booking->organizer_id);
    $preBalance = $organizer?->amount ?? 0;
    $afterBalance = $organizer ? $organizer->amount + ($booking->price - $booking->commission) : null;

    return Transaction::create([
      'transcation_id' => time(),
      'booking_id' => $booking->id,
      'transcation_type' => $booking->transcation_type,
      'customer_id' => $booking->customer_id,
      'organizer_id' => $booking->organizer_id,
      'payment_status' => $booking->paymentStatus,
      'payment_method' => $booking->paymentMethod,
      'grand_total' => $booking->price,
      'tax' => $booking->tax,
      'commission' => $booking->commission,
      'pre_balance' => $preBalance,
      'after_balance' => $afterBalance,
      'gateway_type' => $booking->gatewayType,
      'currency_symbol' => $booking->currencySymbol,
      'currency_symbol_position' => $booking->currencySymbolPosition,
    ]);
  }

  public function storeProductTransaction($orderInfo): Transaction
  {
    return Transaction::create([
      'transcation_id' => time(),
      'booking_id' => $orderInfo->id,
      'transcation_type' => 2,
      'customer_id' => Auth::guard('customer')->id(),
      'organizer_id' => null,
      'payment_status' => $orderInfo->payment_status,
      'payment_method' => $orderInfo->method,
      'grand_total' => $orderInfo->total,
      'tax' => $orderInfo->tax,
      'commission' => null,
      'pre_balance' => null,
      'after_balance' => null,
      'gateway_type' => $orderInfo->gateway_type,
      'currency_symbol' => $orderInfo->currency_symbol,
      'currency_symbol_position' => $orderInfo->currency_symbol_position,
    ]);
  }
}
