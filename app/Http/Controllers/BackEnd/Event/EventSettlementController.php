<?php

namespace App\Http\Controllers\BackEnd\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventSettlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use RuntimeException;

class EventSettlementController extends Controller
{
  public function store(Request $request, $id, EventSettlementService $settlementService)
  {
    $data = $request->validate([
      'amount_option' => ['required', Rule::in([
        EventSettlementService::AMOUNT_ORGANIZER_NET,
        EventSettlementService::AMOUNT_CHARGED_TOTAL,
        EventSettlementService::AMOUNT_CUSTOM,
      ])],
      'custom_amount' => ['nullable', 'required_if:amount_option,' . EventSettlementService::AMOUNT_CUSTOM, 'numeric', 'min:0.01'],
      'paid_at' => ['nullable', 'date'],
      'reference' => ['nullable', 'string', 'max:160'],
      'note' => ['nullable', 'string', 'max:1000'],
    ]);

    try {
      $settlementService->settleEvent(Event::findOrFail($id), $data, Auth::guard('admin')->id());
      Session::flash('success', __('Evento marcado como liquidado.'));
    } catch (RuntimeException $exception) {
      Session::flash('warning', $exception->getMessage());
    }

    return redirect()->back();
  }
}
