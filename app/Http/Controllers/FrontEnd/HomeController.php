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
use App\Models\Event\EventDates;
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
use App\Services\HeroSlideUrlsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
    $cacheTTL = now()->addHours(6);

    $queryResult['seoInfo'] = Cache::remember('home_seo_' . $language->id, 86400, fn () =>
      $language->seoInfo()->select('meta_keyword_home', 'meta_description_home')->first()
    );

    // get the sections of selected home version
    $sectionInfo = Cache::remember('home_section', 86400, fn () => Section::first());
    $queryResult['secInfo'] = $sectionInfo;

    $queryResult['heroInfo'] = Cache::remember('home_hero_info_' . $language->id, 86400, fn () =>
      $language->heroSec()->first()
    );

    $queryResult['secTitleInfo'] = Cache::remember('home_sec_title_' . $language->id, 86400, fn () =>
      $language->sectionTitle()->first()
    );

    $categories = Cache::remember('home_categories_' . $language->id, 86400, fn () =>
      $language->event_category()->where('status', 1)->where('is_featured', '=', 'yes')
        ->orderBy('serial_number', 'asc')->get()
    );


    $queryResult['categories'] = $categories;

    $queryResult['currencyInfo'] = $this->getCurrencyInfo();

    if ($sectionInfo->features_section_status == 1) {
      $queryResult['featureData'] = Cache::remember('home_feature_data_' . $language->id, 86400, fn () =>
        Basic::select('features_section_image')->first()
      );

      $queryResult['features'] = Cache::remember('home_features_' . $language->id, 86400, fn () =>
        $language->feature()->orderBy('serial_number', 'asc')->get()
      );
    }


    if ($sectionInfo->about_us_section_status == 1) {
      $queryResult['aboutUsInfo'] = Cache::remember('home_about_us_info_' . $language->id, 86400, fn () =>
        $language->aboutUsSec()->first()
      );
    }
    $queryResult['heroSection'] = Cache::remember('home_hero_section_' . $language->id, 86400, fn () =>
      HeroSection::where('language_id', $language->id)->first()
    );
    $queryResult['eventCategories'] = Cache::remember('home_event_categories_' . $language->id, 86400, fn () =>
      EventCategory::where([['language_id', $language->id], ['status', 1], ['is_featured', 'yes']])
        ->orderBy('serial_number', 'asc')->get()
    );

    $queryResult['aboutUsSection'] = Cache::remember('home_about_us_' . $language->id, 86400, fn () =>
      AboutUsSection::where('language_id', $language->id)->first()
    );

    $queryResult['featureEventSection'] = Cache::remember('home_feature_section_' . $language->id, 86400, fn () =>
      EventFeatureSection::where('language_id', $language->id)->first()
    );
    $queryResult['featureEventItems'] = Cache::remember('home_feature_items_' . $language->id, 86400, fn () =>
      EventFeature::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get()
    );

    $queryResult['howWork'] = Cache::remember('how_work_' . $language->id, 3600, fn () => HowWork::where('language_id', $language->id)->first());
    $queryResult['howWorkItems'] = Cache::remember('home_how_work_items_' . $language->id, 86400, fn () =>
      HowWorkItem::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get()
    );

    if ($sectionInfo->testimonials_section_status == 1) {
      $queryResult['testimonialData'] = Cache::remember('home_testimonial_section_' . $language->id, 86400, fn () =>
        TestimonialSection::where('language_id', $language->id)->first()
      );

      $queryResult['testimonials'] = Cache::remember('home_testimonials_' . $language->id, 86400, fn () =>
        Testimonial::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get()
      );
    }

    $queryResult['partnerInfo'] = Cache::remember('home_partner_info_' . $language->id, 86400, fn () =>
      PartnerSection::where('language_id', $language->id)->first()
    );
    $queryResult['partners'] = Cache::remember('home_partners_' . $language->id, 86400, fn () =>
      Partner::orderBy('serial_number', 'asc')->get()
    );
    $queryResult['footerInfo'] = Cache::remember('home_footer_' . $language->id, 86400, fn () =>
      FooterContent::where('language_id', $language->id)->first()
    );
    $queryResult['quickLinkInfos'] = Cache::remember('home_quick_links_' . $language->id, 86400, fn () =>
      QuickLink::orderBy('serial_number', 'asc')->get()
    );

    // Event images for marquee slider
    $marqueeEvents = Cache::remember('home_marquee_events_' . $language->id, $cacheTTL, function () use ($language) {
      return DB::table('events')
        ->join('event_contents', function ($join) use ($language) {
          $join->on('events.id', '=', 'event_contents.event_id')
            ->where('event_contents.language_id', '=', $language->id);
        })
        ->leftJoin(DB::raw('(SELECT event_id, MIN(CAST(price AS DECIMAL(10,2))) as min_price, MIN(pricing_type) as pricing_type FROM tickets GROUP BY event_id) as t'), 't.event_id', '=', 'events.id')
        ->where('events.status', 1)
        ->where('events.end_date_time', '>=', $this->now_date_time)
        ->whereNotNull('events.thumbnail')
        ->select('events.id', 'events.thumbnail', 'event_contents.slug', 'event_contents.title', 'events.start_date', 'events.start_time', 'events.manual_badge', 'events.views_count', 'events.views_last_24h', 'events.created_at', 't.min_price', 't.pricing_type')
        ->orderBy('events.created_at', 'desc')
        ->limit(20)
        ->get();
    });
    $queryResult['marqueeEvents'] = $marqueeEvents;
    $featuredExcludeIds = $marqueeEvents->pluck('id')->toArray();

    // Gallery images para el marquee (agrupadas por event_id)
    $marqueeEventIds = $marqueeEvents->pluck('id')->toArray();
    $queryResult['marqueeGallery'] = Cache::remember('home_marquee_gallery_' . $language->id, $cacheTTL, function () use ($marqueeEventIds) {
      return \App\Models\Event\EventImage::whereIn('event_id', $marqueeEventIds)
        ->get()->groupBy('event_id');
    });

    // Hero: intercala imágenes de campaña (hero-campaign) con fotos reales de eventos
    $heroSlideUrls = Cache::remember('home_hero_slide_urls', 3600, fn () =>
      HeroSlideUrlsService::build(maxSlides: 3)
    );
    $queryResult['heroSlideUrls'] = $heroSlideUrls;
    $queryResult['firstHeroSlideUrl'] = $heroSlideUrls[0] ?? null;

    // ── Wishlist del customer autenticado ──
    $wishlistMap = [];
    if (Auth::guard('customer')->check()) {
      $wishlistMap = array_flip(
        DB::table('wishlists')
          ->where('customer_id', Auth::guard('customer')->user()->id)
          ->pluck('event_id')
          ->toArray()
      );
    }
    $queryResult['wishlistMap'] = $wishlistMap;

    // ── Subquery de tickets reutilizable ──
    $ticketSub = DB::raw("(SELECT event_id,
      COUNT(*) as ticket_count,
      MIN(CASE WHEN pricing_type != 'free' AND price > 0 THEN CAST(price AS DECIMAL(10,2)) END) as min_price,
      MAX(CASE WHEN pricing_type = 'free' THEN 1 ELSE 0 END) as has_free,
      MAX(CASE WHEN pricing_type = 'variation' OR (pricing_type != 'free' AND price > 0) THEN 1 ELSE 0 END) as has_paid
      FROM tickets GROUP BY event_id) as tk");

    // ── Eventos destacados "todos" ──
    $featuredEventsAll = Cache::remember('home_featured_events_all_' . $language->id, $cacheTTL, function () use ($language, $ticketSub, $featuredExcludeIds) {
      $query = DB::table('event_contents')
        ->join('events', 'events.id', '=', 'event_contents.event_id')
        ->leftJoin($ticketSub, 'tk.event_id', '=', 'events.id')
        ->leftJoin('organizers', 'organizers.id', '=', 'events.organizer_id')
        ->where([
          ['event_contents.language_id', '=', $language->id],
          ['events.status', 1],
          ['events.end_date_time', '>=', $this->now_date_time],
          ['events.is_featured', '=', 'yes'],
        ])
        ->select('event_contents.*', 'events.*',
          'tk.ticket_count', 'tk.min_price', 'tk.has_free', 'tk.has_paid',
          'organizers.id as org_id', 'organizers.username as org_username');
      $results = $query->clone()
        ->whereNotIn('events.id', $featuredExcludeIds)
        ->orderBy('events.created_at', 'desc')
        ->get();

      if ($results->isEmpty()) {
        $results = $query->clone()
          ->orderBy('events.created_at', 'desc')
          ->get();
      }

      return $results;
    });
    $queryResult['featuredEventsAll'] = $featuredEventsAll;

    // ── Eventos destacados por categoría ──
    $categoryIds = $categories->pluck('id')->toArray();
    $allFeatured = Cache::remember('home_featured_events_by_category_' . $language->id, $cacheTTL, function () use ($language, $categoryIds, $ticketSub, $featuredExcludeIds) {
      $query = DB::table('event_contents')
        ->join('events', 'events.id', '=', 'event_contents.event_id')
        ->leftJoin($ticketSub, 'tk.event_id', '=', 'events.id')
        ->leftJoin('organizers', 'organizers.id', '=', 'events.organizer_id')
        ->whereIn('event_contents.event_category_id', $categoryIds)
        ->where('event_contents.language_id', $language->id)
        ->where('events.status', 1)
        ->where('events.end_date_time', '>=', $this->now_date_time)
        ->where('events.is_featured', 'yes')
        ->select('event_contents.*', 'events.*',
          'tk.ticket_count', 'tk.min_price', 'tk.has_free', 'tk.has_paid',
          'organizers.id as org_id', 'organizers.username as org_username',
          'event_contents.event_category_id as cat_id');
      $results = $query->clone()
        ->whereNotIn('events.id', $featuredExcludeIds)
        ->orderBy('events.created_at', 'desc')
        ->get();

      if ($results->isEmpty()) {
        $results = $query->clone()
          ->orderBy('events.created_at', 'desc')
          ->get();
      }

      return $results;
    });
    $featuredEventsByCategory = $allFeatured->groupBy('cat_id');
    $queryResult['featuredEventsByCategory'] = $featuredEventsByCategory;

    // Pre-calcular fechas latest para eventos de tipo 'multiple' (1 query en vez de N)
    $multipleEventIds = collect();
    if (!empty($queryResult['featuredEventsAll'])) {
      $multipleEventIds = $multipleEventIds->merge(
        $queryResult['featuredEventsAll']->where('date_type', 'multiple')->pluck('id')
      );
    }
    if (!empty($queryResult['featuredEventsByCategory'])) {
      foreach ($queryResult['featuredEventsByCategory'] as $catEvents) {
        $multipleEventIds = $multipleEventIds->merge(
          $catEvents->where('date_type', 'multiple')->pluck('id')
        );
      }
    }
    $multipleEventIds = $multipleEventIds->unique()->values();
    if ($multipleEventIds->isNotEmpty()) {
      $latestDatesMap = EventDates::whereIn('event_id', $multipleEventIds)
        ->where('start_date_time', '>=', now())
        ->orderBy('start_date_time')
        ->get()
        ->keyBy('event_id');
    } else {
      $latestDatesMap = collect();
    }
    $queryResult['latestDatesMap'] = $latestDatesMap;

    // Pre-calcular badges para todos los eventos de la home (3 queries en vez de N×3)
    $allEventsForBadges = collect();
    if (!empty($queryResult['featuredEventsAll'])) {
        $allEventsForBadges = $allEventsForBadges->merge($queryResult['featuredEventsAll']);
    }
    if (!empty($queryResult['featuredEventsByCategory'])) {
        foreach ($queryResult['featuredEventsByCategory'] as $catEvents) {
            $allEventsForBadges = $allEventsForBadges->merge($catEvents);
        }
    }
    if (!empty($queryResult['marqueeEvents'])) {
        $allEventsForBadges = $allEventsForBadges->merge($queryResult['marqueeEvents']);
    }
    $queryResult['badgeMap'] = \App\Services\EventBadgeService::getBadgesForEvents($allEventsForBadges->unique('id')->values());

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
    $sectionInfo = Cache::remember('home_section', 3600, fn () => Section::first());
      $queryResult['secInfo'] = $sectionInfo;

      $queryResult['secTitleInfo'] = $language->sectionTitle()->first();

      $queryResult['currencyInfo'] = $this->getCurrencyInfo();


      if ($sectionInfo->about_us_section_status == 1) {
        $queryResult['aboutUsInfo'] = $language->aboutUsSec()->first();
      }
    $queryResult['heroSection'] = Cache::remember('hero_section_' . $language->id, 3600, fn () => HeroSection::where('language_id', $language->id)->first());

    $queryResult['aboutUsSection'] = Cache::remember('about_us_section_' . $language->id, 3600, fn () => AboutUsSection::where('language_id', $language->id)->first());
      $queryResult['aboutMetrics'] = config('about_metrics');

      if ($sectionInfo->testimonials_section_status == 1) {
      $queryResult['testimonialData'] = Cache::remember('testimonial_section_' . $language->id, 3600, fn () => TestimonialSection::where('language_id', $language->id)->first());

        $queryResult['testimonials'] = Testimonial::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();
      }

    $queryResult['featureEventSection'] = Cache::remember('feature_event_section_' . $language->id, 3600, fn () => EventFeatureSection::where('language_id', $language->id)->first());
      $queryResult['featureEventItems'] = EventFeature::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get();

    $queryResult['partnerInfo'] = Cache::remember('partner_section_' . $language->id, 3600, fn () => PartnerSection::where('language_id', $language->id)->first());
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
