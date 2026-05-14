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
    $billingSettings = BillingSetting::current();

    return view('backend.billing-settings.index', compact('billingSettings'));
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
      'invoice_item_description' => ['nullable', 'string', 'max:255'],
      'invoice_item_include_event' => ['nullable', 'boolean'],
      'invoice_item_include_booking' => ['nullable', 'boolean'],
      'issuer_name' => ['nullable', 'string', 'max:255'],
      'issuer_address' => ['nullable', 'string', 'max:255'],
      'issuer_iva_condition_text' => ['nullable', 'string', 'max:255'],
      'pdf_logo' => ['nullable', 'image', 'max:2048'],
      'send_arca_invoice_email' => ['nullable', 'boolean'],
    ]);

    $billingSettings = BillingSetting::current();
    $validated['enabled'] = $request->boolean('enabled');
    $validated['invoice_item_include_event'] = $request->boolean('invoice_item_include_event');
    $validated['invoice_item_include_booking'] = $request->boolean('invoice_item_include_booking');
    $validated['send_arca_invoice_email'] = $request->boolean('send_arca_invoice_email');

    if ($request->hasFile('pdf_logo')) {
      $path = $request->file('pdf_logo')->store('billing', 'public');
      $validated['pdf_logo_path'] = $path;
    }

    $billingSettings->update($validated);

    $request->session()->flash('success', __('admin.flash.updated_successfully'));

    return redirect()->back();
  }
}
