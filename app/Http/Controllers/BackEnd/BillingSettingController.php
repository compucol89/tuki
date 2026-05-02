<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\BillingSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BillingSettingController extends Controller
{
  public function index()
  {
    $settings = BillingSetting::current();

    return view('backend.billing-settings.index', compact('settings'));
  }

  public function update(Request $request)
  {
    $validated = $request->validate([
      'enabled' => ['nullable', 'boolean'],
      'environment' => ['required', Rule::in(['testing', 'production'])],
      'issuer_cuit' => ['nullable', 'string', 'max:20'],
      'issuer_iva_condition' => ['nullable', 'string', 'max:100'],
      'point_of_sale' => ['nullable', 'integer', 'min:1'],
      'service_fee_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
      'service_fee_tax_mode' => ['required', Rule::in(['no_vat_added', 'vat_added', 'vat_included'])],
      'vat_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
      'default_invoice_type' => ['nullable', 'integer', 'min:1'],
    ]);

    $settings = BillingSetting::current();
    $validated['enabled'] = $request->boolean('enabled');
    $settings->update($validated);

    $request->session()->flash('success', 'Updated Successfully');

    return redirect()->back();
  }
}
