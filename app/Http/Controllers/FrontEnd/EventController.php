<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\Event\Coupon;
use App\Models\Event\EventCategory;
use App\Models\Event\EventContent;
use App\Models\Event\EventDates;
use App\Models\Event\EventImage;
use App\Models\Event\Ticket;
use App\Models\HomePage\HeroSection;
use App\Models\Event\Wishlist;
use App\Models\Organizer;
use App\Services\HeroSlideUrlsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
  private $now_date_time;
  public function __construct()
  {
    $this->now_date_time = Carbon::now();
  }
  //index
  public function index(Request $request)
  {
    $language = $this->getLanguage();
    $information  = [];
    $categories = EventCategory::where([['language_id', $language->id], ['status', 1]])->orderBy('serial_number', 'asc')->get();
    $information['categories'] = $categories;
    $countries = Country::get();
    $information['countries'] = $countries;
    $information['heroSection'] = HeroSection::where('language_id', $language->id)->first();

    //for filter
    $category = $location =  $event_type = $min = $max = $keyword = $date1 = $date2 = null;

    if ($request->filled('category')) {
      $category = $request['category'];
      $category = EventCategory::where([['slug', $category], ['status', 1]])->first();
      $category = $category->id;
    }
    $eventSIds = [];
    if ($request->filled('location')) {
      $location = $request['location'];

      $event_contents = EventContent::where(function ($query) use ($location) {
        return $query->where('address', 'like', '%' . $location . '%')
          ->orWhere('city', 'like', '%' . $location . '%')
          ->orWhere('country', 'like', '%' . $location . '%')
          ->orWhere('state', 'like', '%' . $location . '%');
      })->where('language_id', $language->id)->get();

      foreach ($event_contents as $event_content) {
        if (!in_array($event_content->event_id, $eventSIds)) {
          array_push($eventSIds, $event_content->event_id);
        }
      }
    }

    if ($request->filled('event')) {
      $event_type = $request['event'];
    }
    $eventIds = [];

    if ($request->filled('min') && $request->filled('max')) {
      $min = $request['min'];
      $max = $request['max'];

      $tickets = Ticket::where('tickets.f_price', '>=', $min)->where('tickets.f_price', '<=', $max)->get();

      foreach ($tickets as $ticket) {
        if (!in_array($ticket->event_id, $eventIds)) {
          array_push($eventIds, $ticket->event_id);
        }
      }
    }

    if ($request->filled('search-input')) {
      $keyword = $request['search-input'];
    }

    $pricing = null;
    $pricingEventIds = [];
    if ($request->filled('pricing')) {
      $pricing = $request['pricing'];
      if ($pricing === 'free') {
        $tickets = Ticket::where('pricing_type', 'free')->get();
      } elseif ($pricing === 'paid') {
        $tickets = Ticket::where('pricing_type', '!=', 'free')->get();
      }
      if (isset($tickets)) {
        foreach ($tickets as $ticket) {
          if (!in_array($ticket->event_id, $pricingEventIds)) {
            array_push($pricingEventIds, $ticket->event_id);
          }
        }
      }
    }

    $eventIds2 = [];
    if ($request->filled('dates')) {

      $dates = $request['dates'];
      $dateArray = explode(' ', $dates);

      $date1 = $dateArray[0];
      $date2 = $dateArray[2];

      $q_events = EventDates::whereDate('start_date', '<=', $date1)->whereDate('end_date', '>=', $date2)->get();
      foreach ($q_events as $evnt) {
        if (!in_array($evnt->event_id, $eventIds2)) {
          array_push($eventIds2, $evnt->event_id);
        }
      }

      $events = Event::whereDate('start_date', '<=', $date1)->whereDate('end_date', '>=', $date2)->get();

      foreach ($events as $event) {
        if (!in_array($event->id, $eventIds2)) {
          array_push($eventIds2, $event->id);
        }
      }
    }


    $events = EventContent::join('events', 'events.id', 'event_contents.event_id')
      ->where('event_contents.language_id', $language->id)
      ->when($category, function ($query, $category) {
        return $query->where('event_contents.event_category_id', '=', $category);
      })
      ->when($event_type, function ($query, $event_type) {
        return $query->where('events.event_type', '=', $event_type);
      })
      ->when(($min && $max), function ($query) use ($eventIds) {
        return $query->whereIn('events.id', $eventIds);
      })
      ->when($location, function ($query) use ($eventSIds) {
        return $query->whereIn('events.id', $eventSIds);
      })
      ->when(($date1 && $date2), function ($query) use ($eventIds2) {
        return $query->whereIn('events.id', $eventIds2);
      })
      ->when($pricing, function ($query) use ($pricingEventIds) {
        return $query->whereIn('events.id', $pricingEventIds);
      })
      ->when($keyword, function ($query, $keyword) {
        return $query->where('event_contents.title', 'like', '%' . $keyword . '%');
      })
      ->where('events.status', 1)
      ->whereDate('events.end_date_time', '>=', $this->now_date_time)
      ->select('events.*', 'event_contents.title', 'event_contents.description', 'event_contents.city', 'event_contents.state', 'event_contents.country', 'event_contents.address', 'event_contents.zip_code', 'event_contents.slug')
      ->orderBy('events.id', 'desc')
      ->paginate(9);

    $max = Ticket::max('f_price');
    $min = Ticket::min('f_price');
    $information['max'] = $max;
    $information['min'] = $min;
    $information['events'] = $events;

    return view('frontend.event.event', compact('information') + [
      'heroSlideUrls' => HeroSlideUrlsService::build(),
      'heroSection'   => $information['heroSection'],
      'categories'    => $information['categories'],
    ]);
  }

  //details
  public function details($slug, $id)
  {
    try {
      $language = $this->getLanguage();
      $information = [];

      //remove all session data
      Session::forget('selTickets');
      Session::forget('total');
      Session::forget('quantity');
      Session::forget('total_early_bird_dicount');
      Session::forget('event');
      Session::forget('online_gateways');
      Session::forget('offline_gateways');

      \App\Models\Event::where('id', $id)->increment('views_count');
      \App\Models\Event::where('id', $id)->increment('views_last_24h');

      $tickets_count = Ticket::where('event_id', $id)->get()->count();
      $information['tickets_count'] = $tickets_count;
      if ($tickets_count < 1) {
        $content = EventContent::join('events', 'events.id', 'event_contents.event_id')
          ->join('event_images', 'event_images.event_id', '=', 'events.id')
          ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
          ->where('event_contents.language_id', $language->id)
          ->where('events.id', $id)
          ->select('events.*', 'event_contents.title', 'event_contents.slug as eventSlug', 'event_contents.description', 'meta_keywords', 'meta_description', 'event_contents.event_category_id', 'event_categories.name', 'event_categories.slug', 'event_contents.city', 'event_contents.state', 'event_contents.country', 'event_contents.address', 'event_contents.zip_code', 'event_contents.refund_policy')
          ->first();
        if (is_null($content)) {
          $content = EventContent::join('events', 'events.id', 'event_contents.event_id')
            ->join('event_images', 'event_images.event_id', '=', 'events.id')
            ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
            ->where('events.id', $id)
            ->select('events.*', 'event_contents.title', 'event_contents.slug as eventSlug', 'event_contents.description', 'meta_keywords', 'meta_description', 'event_contents.event_category_id', 'event_categories.name', 'event_categories.slug', 'event_contents.city', 'event_contents.state', 'event_contents.country', 'event_contents.address', 'event_contents.zip_code', 'event_contents.refund_policy')
            ->first();
        }
        if (is_null($content)) {
          return redirect()->route('index');
        }
      } else {
        $content = EventContent::join('events', 'events.id', 'event_contents.event_id')
          ->join('tickets', 'tickets.event_id', '=', 'events.id')
          ->join('event_images', 'event_images.event_id', '=', 'events.id')
          ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
          ->where('event_contents.language_id', $language->id)
          ->where('events.id', $id)
          ->select('events.*', 'event_contents.title', 'event_contents.slug as eventSlug', 'event_contents.description', 'meta_keywords', 'meta_description', 'event_contents.event_category_id', 'event_categories.name', 'event_categories.slug', 'tickets.price', 'tickets.variations', 'tickets.pricing_type', 'event_contents.city', 'event_contents.state', 'event_contents.country', 'event_contents.address', 'event_contents.zip_code', 'event_contents.refund_policy')
          ->first();
        if (is_null($content)) {
          $content = EventContent::join('events', 'events.id', 'event_contents.event_id')
            ->join('tickets', 'tickets.event_id', '=', 'events.id')
            ->join('event_images', 'event_images.event_id', '=', 'events.id')
            ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
            ->where('events.id', $id)
            ->select('events.*', 'event_contents.title', 'event_contents.slug as eventSlug', 'event_contents.description', 'meta_keywords', 'meta_description', 'event_contents.event_category_id', 'event_categories.name', 'event_categories.slug', 'tickets.price', 'tickets.variations', 'tickets.pricing_type', 'event_contents.city', 'event_contents.state', 'event_contents.country', 'event_contents.address', 'event_contents.zip_code', 'event_contents.refund_policy')
            ->first();
        }
        if (is_null($content)) {
          return redirect()->route('index');
        }
      }

      $information['content'] = $content;
      $images = EventImage::where('event_id', $id)->get();
      $information['images'] = $images;

      $information['organizer'] = '';
      if ($content) {
        if ($content->organizer_id != NULL) {
          $organizer = Organizer::where('id', $content->organizer_id)->first();
          $information['organizer'] = $organizer;
        }
      }

      $basicSettings = DB::table('basic_settings')
        ->select('timezone', 'website_title')
        ->first();
      $websiteTimezone = $basicSettings->timezone ?? config('app.timezone');
      $websiteTitle = $basicSettings->website_title ?? config('app.name');

      $statusMeta = $this->buildEventStatusMeta($content, $websiteTimezone);
      $ticketSummary = $this->buildTicketSummary($content);
      $signalMeta = $this->buildSignalMeta($content->id, $ticketSummary['limited_stock_total']);

      $information['websiteTimezone'] = $websiteTimezone;
      $information['websiteTitle'] = $websiteTitle;
      $information['heroDateTimestamp'] = $statusMeta['hero_date_timestamp'];
      $information['startDateTime'] = $statusMeta['start_date_time'];
      $information['endDateTime'] = $statusMeta['end_date_time'];
      $information['lastEndDate'] = $statusMeta['last_end_date'];
      $information['nowTime'] = $statusMeta['now_time'];
      $information['over'] = $statusMeta['over'];
      $information['heroStatusClass'] = $statusMeta['hero_status_class'];
      $information['heroStatusLabel'] = $statusMeta['hero_status_label'];
      $information['ticketSummary'] = $ticketSummary;
      $information['signalStock'] = $ticketSummary['limited_stock_total'];
      $information['ev_viewers'] = $signalMeta['viewers'];
      $information['ev_saved'] = $signalMeta['saved'];
      $information['ed_nudge_pool'] = $signalMeta['nudge_pool'];
      $information['spotifyEmbedUrl'] = $this->buildSpotifyEmbedUrl($content->spotify_url);
      $information['summaryLocation'] = $content->event_type == 'online'
        ? __('Online')
        : collect([$content->city, $content->state, $content->country])->filter()->implode(', ');
      $information['summaryOrganizer'] = !empty($information['organizer'])
        ? $information['organizer']->username
        : $websiteTitle;

      $category_id = $content->event_category_id;
      $event_id = $content->id;
      $related_events = EventContent::join('events', 'events.id', 'event_contents.event_id')
        ->where('event_contents.language_id', $language->id)
        ->where('event_contents.event_category_id', $category_id)
        ->where('events.id', '!=', $event_id)
        ->whereDate('events.end_date_time', '>=', $this->now_date_time)
        ->select('events.*', 'event_contents.title', 'event_contents.description', 'event_contents.slug', 'event_contents.city', 'event_contents.country')
        ->orderBy('events.id', 'desc')
        ->get();

      // Pre-cargar tickets y organizadores de eventos relacionados en 2 queries (evita N+1)
      $relatedIds = $related_events->pluck('id')->toArray();
      $relatedTickets = Ticket::whereIn('event_id', $relatedIds)
        ->select('event_id', 'price', 'event_type', 'early_bird_discount', 'early_bird_discount_type', 'early_bird_discount_amount', 'early_bird_discount_date', 'early_bird_discount_time')
        ->get()
        ->keyBy('event_id');
      $relatedOrganizerIds = $related_events->whereNotNull('organizer_id')->pluck('organizer_id')->unique()->toArray();
      $relatedOrganizers = Organizer::whereIn('id', $relatedOrganizerIds)
        ->select('id', 'username')
        ->get()
        ->keyBy('id');

      $information['related_events'] = $related_events;
      $information['relatedTickets'] = $relatedTickets;
      $information['relatedOrganizers'] = $relatedOrganizers;

      // SEO / Open Graph
      $rawDescription = trim(preg_replace('/\s+/u', ' ', strip_tags($content->description ?? '')));
      $seoDescription = trim($content->meta_description ?: Str::limit($rawDescription, 160, ''));
      if ($seoDescription === '') {
        $seoDescription = trim($content->title . ' | ' . __('Comprá entradas y descubrí toda la información del evento en Tukipass.'));
      }
      $ogImage = $images->isNotEmpty()
        ? asset('assets/admin/img/event-gallery/' . $images->first()->image)
        : asset('assets/admin/img/event/thumbnail/' . $content->thumbnail);

      $information['seo_title'] = $content->title;
      $information['og_title'] = $content->title . ' | ' . $websiteTitle;
      $information['og_description'] = $seoDescription;
      $information['og_image'] = $ogImage;
      $information['og_image_alt'] = $content->title . ' — ' . __('evento en Tukipass');
      $information['og_url'] = url()->current();
      $information['canonical'] = url()->current();

      return view('frontend.event.event-details', $information);
    } catch (\Exception $th) {
      return view('errors.404');
    }
  }

  private function buildEventStatusMeta($content, string $timezone): array
  {
    if ($content->date_type == 'multiple') {
      $eventDate = eventLatestDates($content->id);
      $heroDateTimestamp = strtotime(optional($eventDate)->start_date);
      $startDateTime = optional($eventDate)->start_date_time;
      $endDateTime = optional($eventDate)->end_date_time;
      $lastEndDate = optional(eventLastEndDates($content->id))->end_date_time;
    } else {
      $heroDateTimestamp = strtotime($content->start_date);
      $startDateTime = $content->start_date . ' ' . $content->start_time;
      $endDateTime = $content->end_date . ' ' . $content->end_time;
      $lastEndDate = $endDateTime;
    }

    $nowTime = Carbon::now()
      ->timezone($timezone)
      ->translatedFormat('Y-m-d H:i:s');

    $over = false;
    $heroStatusClass = null;
    $heroStatusLabel = null;

    if ($content->date_type == 'single' && $content->countdown_status == 1) {
      if ($startDateTime >= $nowTime) {
        $heroStatusClass = 'ed-hero__status-pill--upcoming';
        $heroStatusLabel = __('Próximamente');
      } elseif ($startDateTime <= $endDateTime && $endDateTime >= $nowTime) {
        $heroStatusClass = 'ed-hero__status-pill--running';
        $heroStatusLabel = __('En curso');
      } else {
        $over = true;
        $heroStatusClass = 'ed-hero__status-pill--over';
        $heroStatusLabel = __('Finalizado');
      }
    } elseif ($content->date_type == 'multiple') {
      if ($startDateTime >= $nowTime) {
        $heroStatusClass = 'ed-hero__status-pill--upcoming';
        $heroStatusLabel = __('Próximamente');
      } elseif ($startDateTime <= $lastEndDate && $lastEndDate >= $nowTime) {
        $heroStatusClass = 'ed-hero__status-pill--running';
        $heroStatusLabel = __('En curso');
      } else {
        $over = true;
        $heroStatusClass = 'ed-hero__status-pill--over';
        $heroStatusLabel = __('Finalizado');
      }
    }

    return [
      'hero_date_timestamp' => $heroDateTimestamp,
      'start_date_time' => $startDateTime,
      'end_date_time' => $endDateTime,
      'last_end_date' => $lastEndDate,
      'now_time' => $nowTime,
      'over' => $over,
      'hero_status_class' => $heroStatusClass,
      'hero_status_label' => $heroStatusLabel,
    ];
  }

  private function buildTicketSummary($content): array
  {
    $minTicketPrice = Ticket::where('event_id', $content->id)->min('price');
    $maxTicketPrice = Ticket::where('event_id', $content->id)->max('price');

    if (!is_numeric($minTicketPrice)) {
      $variationTickets = Ticket::where('event_id', $content->id)
        ->where('pricing_type', 'variation')
        ->pluck('variations');

      $variationPrices = [];

      foreach ($variationTickets as $variationJson) {
        $variations = json_decode($variationJson, true);

        if (!is_array($variations)) {
          continue;
        }

        foreach ($variations as $variation) {
          if (isset($variation['price']) && is_numeric($variation['price'])) {
            $variationPrices[] = (float) $variation['price'];
          }
        }
      }

      if (!empty($variationPrices)) {
        $minTicketPrice = min($variationPrices);
        $maxTicketPrice = max($variationPrices);
      }
    }

    $ticketStocks = Ticket::where('event_id', $content->id)
      ->get(['ticket_available_type', 'ticket_available']);

    $hasUnlimitedStock = $ticketStocks->contains('ticket_available_type', 'unlimited');
    $totalStock = $hasUnlimitedStock ? null : (int) $ticketStocks->sum('ticket_available');

    return [
      'min_ticket_price' => $minTicketPrice,
      'max_ticket_price' => $maxTicketPrice,
      'has_price_range' => is_numeric($minTicketPrice) && is_numeric($maxTicketPrice) && $minTicketPrice != $maxTicketPrice,
      'has_unlimited_stock' => $hasUnlimitedStock,
      'total_stock' => $totalStock,
      'limited_stock_total' => (int) Ticket::where('event_id', $content->id)
        ->where('ticket_available_type', 'limited')
        ->sum('ticket_available'),
    ];
  }

  private function buildSignalMeta(int $eventId, int $signalStock): array
  {
    $viewersKey = 'ev_viewers_' . $eventId;
    $viewers = Cache::get($viewersKey);

    if (!$viewers) {
      $viewers = rand(80, 160);
      Cache::put($viewersKey, $viewers, now()->addDays(90));
    } elseif (rand(1, 100) <= 40) {
      $viewers = min($viewers + rand(1, 2), 340);
      Cache::put($viewersKey, $viewers, now()->addDays(90));
    }

    $savedKey = 'ev_saved_' . $eventId;
    $saved = Cache::get($savedKey);

    if (!$saved) {
      $saved = rand(40, 120);
      Cache::put($savedKey, $saved, now()->addDays(90));
    } elseif (rand(1, 100) <= 25) {
      $saved = min($saved + rand(1, 2), 480);
      Cache::put($savedKey, $saved, now()->addDays(90));
    }

    $nudgePool = [
      ['icon' => 'fire', 'text' => __('Este evento se está agotando rápido')],
      ['icon' => 'zap', 'text' => __('Alta demanda') . ' — ' . __('no te quedes sin tu entrada')],
      ['icon' => 'trending', 'text' => __('Evento popular en tu zona')],
      ['icon' => 'heart', 'text' => '<strong>' . $saved . '</strong> ' . __('personas guardaron este evento')],
      ['icon' => 'clock', 'text' => __('No esperes al último momento') . ' — ' . __('asegurá tu lugar ahora')],
      ['icon' => 'star', 'text' => __('Uno de los eventos más buscados esta semana')],
      ['icon' => 'shield', 'text' => __('Compra protegida') . ' — ' . __('reembolso garantizado')],
      ['icon' => 'calendar', 'text' => __('La fecha se acerca') . ' — ' . __('comprá con anticipación')],
    ];

    if ($signalStock > 0 && $signalStock <= 20) {
      $nudgePool[] = [
        'icon' => 'alert',
        'text' => __('Quedan solo') . ' <strong>' . $signalStock . '</strong> ' . ($signalStock == 1 ? __('entrada') : __('entradas')),
      ];
    }

    shuffle($nudgePool);

    return [
      'viewers' => $viewers,
      'saved' => $saved,
      'nudge_pool' => $nudgePool,
    ];
  }

  private function buildSpotifyEmbedUrl(?string $spotifyUrl): ?string
  {
    if (empty($spotifyUrl)) {
      return null;
    }

    preg_match('/spotify\.com\/(?:intl-[a-z-]+\/)?artist\/([a-zA-Z0-9]+)/', $spotifyUrl, $matches);

    if (empty($matches[1])) {
      return null;
    }

    return 'https://open.spotify.com/embed/artist/' . $matches[1] . '?utm_source=generator&theme=0';
  }
  //applyCoupon
  public function applyCoupon(Request $request)
  {
    $coupon = Coupon::where('code', $request->coupon_code)->first();

    if (!$coupon) {
      Session::put('discount', NULL);
      return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
    } else {

      $start = Carbon::parse($coupon->start_date);
      $end = Carbon::parse($coupon->end_date);
      $today = Carbon::now();
      $event = Session::get('event');
      $event_id = $event->id;
      $events = json_decode($coupon->events, true);
      if (!empty($events)) {
        if (in_array($event_id, $events)) {

          // if coupon is active
          if ($today->greaterThanOrEqualTo($start) && $today->lessThan($end)) {
            $value = $coupon->value;
            $type = $coupon->type;
            $early_bird_dicount = Session::get('total_early_bird_dicount');
            if ($early_bird_dicount != '') {
              $cartTotal = Session::get('sub_total') - $early_bird_dicount;
            } else {
              $cartTotal = Session::get('sub_total') - $early_bird_dicount;
            }
            if ($type == 'fixed') {
              $couponAmount = $value;
            } else {
              $couponAmount = ($cartTotal * $value) / 100;
            }
            $cartTotal - $couponAmount;
            Session::put('discount', $couponAmount);
            return response()->json(['status' => 'success', 'message' => "Coupon applied successfully"]);
          } else {
            return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
          }
        } else {
          return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
        }
      } else {
        // if coupon is active
        if ($today->greaterThanOrEqualTo($start) && $today->lessThan($end)) {
          $value = $coupon->value;
          $type = $coupon->type;
          $early_bird_dicount = Session::get('total_early_bird_dicount');
          if ($early_bird_dicount != '') {
            $cartTotal = Session::get('sub_total') - $early_bird_dicount;
          } else {
            $cartTotal = Session::get('sub_total') - $early_bird_dicount;
          }
          if ($type == 'fixed') {
            $couponAmount = $value;
          } else {
            $couponAmount = ($cartTotal * $value) / 100;
          }
          $cartTotal - $couponAmount;
          Session::put('discount', $couponAmount);
          return response()->json(['status' => 'success', 'message' => "Coupon applied successfully"]);
        } else {
          return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
        }
      }
    }
  }

  //add_to_wishlist
  public function add_to_wishlist($id)
  {
    if (Auth::guard('customer')->check()) {
      $customer_id = Auth::guard('customer')->user()->id;
      $check = Wishlist::where('event_id', $id)->where('customer_id', $customer_id)->first();

      if (!empty($check)) {
        $notification = array('message' => 'You already added this event into your wishlist..!', 'alert-type' => 'error');
        return back()->with($notification);
      } else {
        $add = new Wishlist;
        $add->event_id = $id;
        $add->customer_id = $customer_id;
        $add->save();
        $notification = array('message' => 'Add to your wishlist successfully..!', 'alert-type' => 'success');
        return back()->with($notification);
      }
    } else {
      return redirect()->route('customer.login');
    }
  }
}
