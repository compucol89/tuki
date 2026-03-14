<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\PaymentGateway\MyFatoorahController;
use App\Http\Controllers\FrontEnd\PaymentGateway\XenditController;
use App\Http\Controllers\FrontEnd\Shop\PaymentGateway\MyFatoorahController as ShopGatewayMyFatoorahController;
use App\Http\Controllers\FrontEnd\Shop\PaymentGateway\XenditController as ShopXenditController;
use App\Models\BasicSettings\Basic;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventCategory;
use App\Models\Event\EventContent;
use App\Models\Event\EventImage;
use App\Models\Footer\FooterContent;
use App\Models\Footer\QuickLink;
use App\Models\HomePage\AboutUsSection;
use App\Models\HomePage\EventFeature;
use App\Models\HomePage\EventFeatureSection;
use App\Models\HomePage\HeroSection;
use App\Models\HomePage\HowWork;
use App\Models\HomePage\HowWorkItem;
use App\Models\HomePage\Partner;
use App\Models\HomePage\PartnerSection;
use App\Models\HomePage\Section;
use App\Models\HomePage\Testimonial;
use App\Models\HomePage\TestimonialSection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
  private $now_date_time;
  public function __construct()
  {
    $this->now_date_time = Carbon::now();
  }
  public function index()
  {
    $language = $this->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_home', 'meta_description_home')->first();

    // get the sections of selected home version
    $sectionInfo = Section::first();
    $queryResult['secInfo'] = $sectionInfo;

    $queryResult['heroInfo'] = $language->heroSec()->first();

    $queryResult['secTitleInfo'] = $language->sectionTitle()->first();

    $categories = $language->event_category()->where('status', 1)->where('is_featured', '=', 'yes')->orderBy('serial_number', 'asc')
      ->get();


    $queryResult['categories'] = $categories;

    $queryResult['currencyInfo'] = $this->getCurrencyInfo();

    if ($sectionInfo->features_section_status == 1) {
      $queryResult['featureData'] = Basic::select('features_section_image')->first();

      $queryResult['features'] = $language->feature()->orderBy('serial_number', 'asc')->get();
    }


    if ($sectionInfo->about_us_section_status == 1) {
      $queryResult['aboutUsInfo'] = $language->aboutUsSec()->first();
    }
    $queryResult['heroSection'] = HeroSection::where('language_id', $language->id)->first();
    $queryResult['eventCategories'] = EventCategory::where([['language_id', $language->id], ['status', 1], ['is_featured', 'yes']])->orderBy('serial_number', 'asc')->get();

    $queryResult['aboutUsSection'] = AboutUsSection::where('language_id', $language->id)->first();

    $queryResult['featureEventSection'] = EventFeatureSection::where('language_id', $language->id)->first();
    $queryResult['featureEventItems'] = EventFeature::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();

    $queryResult['howWork'] = HowWork::where('language_id', $language->id)->first();
    $queryResult['howWorkItems'] = HowWorkItem::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();

    if ($sectionInfo->testimonials_section_status == 1) {
      $queryResult['testimonialData'] = TestimonialSection::where('language_id', $language->id)->first();

      $queryResult['testimonials'] = Testimonial::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();
    }

    $queryResult['partnerInfo'] = PartnerSection::where('language_id', $language->id)->first();
    $queryResult['partners'] = Partner::orderBy('serial_number', 'asc')->get();
    $queryResult['footerInfo'] = FooterContent::where('language_id', $language->id)->first();
    $queryResult['quickLinkInfos'] = QuickLink::orderBy('serial_number', 'asc')->get();

    // Event images for marquee slider
    $marqueeEvents = DB::table('events')
      ->join('event_contents', function ($join) use ($language) {
        $join->on('events.id', '=', 'event_contents.event_id')
          ->where('event_contents.language_id', '=', $language->id);
      })
      ->leftJoin(DB::raw('(SELECT event_id, MIN(CAST(price AS DECIMAL(10,2))) as min_price, MIN(pricing_type) as pricing_type FROM tickets GROUP BY event_id) as t'), 't.event_id', '=', 'events.id')
      ->where('events.status', 1)
      ->where('events.end_date_time', '>=', $this->now_date_time)
      ->whereNotNull('events.thumbnail')
      ->select('events.id', 'events.thumbnail', 'event_contents.slug', 'event_contents.title', 'events.start_date', 'events.start_time', 't.min_price', 't.pricing_type')
      ->orderBy('events.created_at', 'desc')
      ->limit(20)
      ->get();
    $queryResult['marqueeEvents'] = $marqueeEvents;

    // Gallery images for hero slideshow (solo archivos que existen en disco)
    $queryResult['heroGalleryImages'] = EventImage::inRandomOrder()->limit(20)->pluck('image')
        ->filter(fn($img) => file_exists(public_path('assets/admin/img/event-gallery/' . $img)))
        ->take(8)
        ->values();
    $queryResult['firstHeroImage'] = $queryResult['heroGalleryImages']->first();

    return view('frontend.home.index-v1', $queryResult);
  }
  //offline
  public function offline()
  {
    return view('frontend.offline');
  }

  public function about()
  {
    try {
      $language = $this->getLanguage();

      $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_home', 'meta_description_home')->first();

      // get the sections of selected home version
      $sectionInfo = Section::first();
      $queryResult['secInfo'] = $sectionInfo;

      $queryResult['secTitleInfo'] = $language->sectionTitle()->first();

      $queryResult['currencyInfo'] = $this->getCurrencyInfo();


      if ($sectionInfo->about_us_section_status == 1) {
        $queryResult['aboutUsInfo'] = $language->aboutUsSec()->first();
      }
      $queryResult['heroSection'] = HeroSection::where('language_id', $language->id)->first();

      $queryResult['aboutUsSection'] = AboutUsSection::where('language_id', $language->id)->first();

      if ($sectionInfo->testimonials_section_status == 1) {
        $queryResult['testimonialData'] = TestimonialSection::where('language_id', $language->id)->first();

        $queryResult['testimonials'] = Testimonial::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();
      }

      $queryResult['featureEventSection'] = EventFeatureSection::where('language_id', $language->id)->first();
      $queryResult['featureEventItems'] = EventFeature::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();

      $queryResult['partnerInfo'] = PartnerSection::where('language_id', $language->id)->first();
      $queryResult['partners'] = Partner::orderBy('serial_number', 'asc')->get();
      $queryResult['footerInfo'] = FooterContent::where('language_id', $language->id)->first();
      $queryResult['quickLinkInfos'] = QuickLink::orderBy('serial_number', 'asc')->get();
      return view('frontend.about', $queryResult); //code...
    } catch (\Exception $th) {
    }
  }

  public function midtrans_cancel()
  {
    Session::forget('event_id');
    Session::forget('selTickets');
    Session::forget('arrData');
    Session::forget('paymentId');
    Session::forget('discount');
    Session::forget('token');

    return redirect()->route('index')->with(['alert-type' => 'error', 'message' => 'Payment Canceled.']);
  }
  public function xendit_callback(Request $request)
  {
    return $request->all();
    if (Session::get('xendit_payment_type') == 'event') {
      $data = new XenditController();
      $data->callback($request);
    } elseif (Session::get('xendit_payment_type') == 'shop') {
      $data = new ShopXenditController();
      $data->callback($request);
    }
  }

  public function myfatoorah_callback(Request $request)
  {
    $type = Session::get('myfatoorah_payment_type');
    if ($type == 'event') {
      $data = new MyFatoorahController();
      $data = $data->successCallback($request);
      // return redirect($data);
      Session::forget('myfatoorah_payment_type');
      if ($data['status'] == 'success') {
        return redirect()->route('event_booking.complete', ['id' => $data['event_id'], 'booking_id' => $data['booking_id']]);
      } else {
        return redirect()->route('check-out')->with(['alert-type' => 'error', 'message' => 'Payment Cancel']);
      }
    } elseif ($type == 'shop') {
      $data = new ShopGatewayMyFatoorahController();
      $data = $data->successCallback($request);
      Session::forget('myfatoorah_payment_type');
      if ($data['status'] == 'success') {
        return redirect()->route('product_order.complete');
      } else {
        return redirect()->route('shop.checkout')->with(['alert-type' => 'error', 'message' => 'Payment failed']);
      }
    }
  }

  public function myfatoorah_cancel(Request $request)
  {
    return redirect()->route('index')->with(['alert-type' => 'error', 'message' => 'Payment failed']);
  }
}
