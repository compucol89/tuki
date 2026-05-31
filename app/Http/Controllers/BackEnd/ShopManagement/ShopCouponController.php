<?php

namespace App\Http\Controllers\BackEnd\ShopManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopManagement\ShopCouponRequest;
use App\Http\Requests\ShopManagement\UpdateShopCounponRequest;
use App\Models\ShopManagement\ShopCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class ShopCouponController extends Controller
{
  public function index()
  {
    App::setLocale('admin');
    $data['collection'] = ShopCoupon::orderBy('id', 'DESC')->get();
    return view('backend.product.coupon.index', $data);
  }
  //store
  public function store(ShopCouponRequest $request)
  {
    App::setLocale('admin');
    ShopCoupon::create($request->all());
    Session::flash('success', __('admin.flash.added_successfully'));
    return response()->json(['status' => 'success'], 200);
  }
  //update
  public function update(UpdateShopCounponRequest $request)
  {
    App::setLocale('admin');
    $in = $request->all();
    $update = ShopCoupon::where('id', $request->id)->first();
    $update->update($in);
    Session::flash('success', __('admin.flash.updated_successfully'));
    return response()->json(['status' => 'success'], 200);
  }
  //destroy
  public function destroy(Request $request)
  {
    $delete = ShopCoupon::where('id', $request->id)->first();
    $delete->delete();
    Session::flash('warning', __('admin.flash.updated_successfully'));
    return back();
  }
  //bulk_destroy
  public function bulk_destroy(Request $request)
  {
    $ids = $request->ids;
    foreach ($ids as $id) {
      $delete = ShopCoupon::where('id', $id)->first();
      $delete->delete();
    }
    Session::flash('warning', __('admin.flash.deleted_successfully'));
    return response()->json(['status' => 'success'], 200);
  }
}
