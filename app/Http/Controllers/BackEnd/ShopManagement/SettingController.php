<?php

namespace App\Http\Controllers\BackEnd\ShopManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopManagement\ShippingChargeRequest;
use App\Models\Language;
use App\Models\ShopManagement\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class SettingController extends Controller
{
  public function index(Request $request)
  {
    App::setLocale('admin');
    $lang = Language::where('code', $request->language)->firstOrFail();
    $lang_id = $lang->id;
    $data['collection'] = ShippingCharge::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();
    $data['lang_id'] = $lang_id;
    $data['langs'] = Language::get();
    return view('backend.product.shop_setting.index', $data);
  }
  //store
  public function store(ShippingChargeRequest $request)
  {
    App::setLocale('admin');
    $in = $request->all();
    $store = ShippingCharge::create($in);
    Session::flash('success', __('admin.flash.added_successfully'));
    return response()->json(['status' => 'success'], 200);
  }
  //delete
  public function delete(Request $request)
  {
    $delete = ShippingCharge::where('id', $request->id)->first();
    $delete->delete();
    Session::flash('warning', __('admin.flash.deleted_successfully'));
    return back();
  }
  //bulkdelete
  public function bulkdelete(Request $request)
  {
    foreach ($request->ids as $id) {
      $delete = ShippingCharge::where('id', $id)->first();
      $delete->delete();
    }
    Session::flash('warning', __('admin.flash.deleted_successfully'));
    return response()->json(['status' => 'success'], 200);
  }
  //update
  public function update(Request $request)
  {
    App::setLocale('admin');
    $in = $request->all();
    $update = ShippingCharge::where('id', $request->id)->first();
    $update->update($in);
    Session::flash('success', __('admin.flash.updated_successfully'));
    return response()->json(['status' => 'success'], 200);
  }
}
