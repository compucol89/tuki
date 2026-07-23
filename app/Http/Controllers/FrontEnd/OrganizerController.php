<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Event\EventContent;
use App\Models\HomePage\EventFeature;
use App\Models\HomePage\EventFeatureSection;
use App\Models\HomePage\Partner;
use App\Models\HomePage\Section;
use App\Models\HomePage\Testimonial;
use App\Models\HomePage\TestimonialSection;
use App\Models\Organizer;
use App\Models\OrganizerInfo;
use App\Support\DemoEventExclusion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;

class OrganizerController extends Controller
{
  //show
  public function index(Request $request)
  {
    $language = $this->getLanguage();

    $organizer_name = $username = $location = null;

    $organizerIds = [];
    if ($request->filled('organizer')) {
      $organizer_name = $request->organizer;

      $organizer_infos = OrganizerInfo::where('name', 'like', '%' . $organizer_name . '%')
        ->where('language_id', $language->id)
        ->get();
      foreach ($organizer_infos as $info) {
        if (!in_array($info->organizer_id, $organizerIds)) {
          array_push($organizerIds, $info->organizer_id);
        }
      }
    }
    if ($request->filled('username')) {
      $username = $request->username;
    }
    $locationIds = [];
    if ($request->filled('location')) {
      $location = $request->location;
      $organizer_infos = OrganizerInfo::where('city', 'like', '%' . $location . '%')
        ->orWhere('state', 'like', '%' . $location . '%')
        ->orWhere('country', 'like', '%' . $location . '%')
        ->orWhere('address', 'like', '%' . $location . '%')
        ->where('language_id', $language->id)
        ->get();
      foreach ($organizer_infos as $info) {
        if (!in_array($info->organizer_id, $locationIds)) {
          array_push($locationIds, $info->organizer_id);
        }
      }
    }

    $collection = Organizer::with(['organizer_info' => function ($query) use ($language) {
      return $query->where('language_id', $language->id);
    }])->when($username, function ($query) use ($username) {
      return $query->where('username', 'like', '%' . $username . '%');
    })
      ->when($location, function ($query) use ($locationIds) {
        return $query->whereIn('id', $locationIds);
      })
      ->when($organizer_name, function ($query) use ($organizerIds) {
        return $query->whereIn('id', $organizerIds);
      })
      ->paginate(20);

    $secInfo = Cache::remember('home_section', 86400, fn () => Section::first());
    $featureEventSection = Cache::remember('home_feature_section_' . $language->id, 86400, fn () =>
      EventFeatureSection::where('language_id', $language->id)->first()
    );
    $featureEventItems = Cache::remember('home_feature_items_' . $language->id, 86400, fn () =>
      EventFeature::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get()
    );
    $testimonialData = null;
    $testimonials = collect();
    if ($secInfo && $secInfo->testimonials_section_status == 1) {
      $testimonialData = Cache::remember('home_testimonial_section_' . $language->id, 86400, fn () =>
        TestimonialSection::where('language_id', $language->id)->first()
      );
      $testimonials = Cache::remember('home_testimonials_' . $language->id, 86400, fn () =>
        Testimonial::where('language_id', $language->id)->orderBy('serial_number', 'asc')->get()
      );
    }
    $partners = Cache::remember('home_partners_' . $language->id, 86400, fn () =>
      Partner::orderBy('serial_number', 'asc')->get()
    );

    return view('frontend.organizer.index', compact(
      'collection',
      'secInfo',
      'featureEventSection',
      'featureEventItems',
      'testimonialData',
      'testimonials',
      'partners'
    ));
  }

  public function details(Request $request, $id, $name)
  {
    try {
      $language = $this->getLanguage();
      $information = [];
      $information['basicInfos'] = DB::table('basic_settings')
        ->select('google_recaptcha_status')
        ->first();

      if (filled($request->admin)) {
        $admin = Admin::first();
        $information['organizer'] = $admin;
        $information['admin'] = true;
        $information['organizer_info'] = null;
        $organizerEventColumn = null;
      } else {
        $organizer = Organizer::where('id', $id)->first();
        if (!$organizer) {
          return response()->view('errors.404', [], 404);
        }

        $information['organizer_info'] = OrganizerInfo::where('organizer_id', $id)->where('language_id', $language->id)->first();

        $information['organizer'] = $organizer;
        $information['admin'] = false;
        $organizerEventColumn = $organizer->id;
      }

      $ticketSub = DB::raw("(SELECT event_id,
        COUNT(*) as ticket_count,
        MIN(CASE WHEN pricing_type != 'free' AND price > 0 THEN CAST(price AS DECIMAL(10,2)) END) as min_price,
        MAX(CASE WHEN pricing_type = 'free' THEN 1 ELSE 0 END) as has_free,
        MAX(CASE WHEN pricing_type = 'variation' OR (pricing_type != 'free' AND price > 0) THEN 1 ELSE 0 END) as has_paid
        FROM tickets GROUP BY event_id) as tk");

      $baseEventsQuery = EventContent::join('events', 'events.id', '=', 'event_contents.event_id')
        ->leftJoin($ticketSub, 'tk.event_id', '=', 'events.id')
        ->where('event_contents.language_id', $language->id)
        ->where('events.status', 1)
        ->whereNotIn('events.id', DemoEventExclusion::EVENT_IDS)
        ->when($organizerEventColumn === null, function ($query) {
          return $query->whereNull('events.organizer_id');
        }, function ($query) use ($organizerEventColumn) {
          return $query->where('events.organizer_id', $organizerEventColumn);
        })
        ->select('event_contents.*', 'events.*', 'tk.ticket_count', 'tk.min_price', 'tk.has_free', 'tk.has_paid');

      $nowDateTime = Carbon::now();
      $upcomingEvents = (clone $baseEventsQuery)
        ->where('events.end_date_time', '>=', $nowDateTime)
        ->orderBy('events.start_date', 'asc')
        ->orderBy('events.start_time', 'asc')
        ->get();
      $pastEvents = (clone $baseEventsQuery)
        ->where('events.end_date_time', '<', $nowDateTime)
        ->orderBy('events.end_date_time', 'desc')
        ->limit(6)
        ->get();
      $information['upcomingEvents'] = $upcomingEvents;
      $information['pastEvents'] = $pastEvents;
      $information['events'] = $upcomingEvents->concat($pastEvents);
      $information['publicOrganizerName'] = trim((string) ($information['organizer_info']->name ?? $information['organizer']->username ?? config('app.name', 'Tukipass')));
      $information['publicOrganizerDescription'] = trim(strip_tags((string) ($information['organizer_info']->details ?? '')));
      $profileSlug = Str::slug($information['publicOrganizerName']);
      $routeParameters = [$id, $profileSlug !== '' ? $profileSlug : Str::slug($name)];
      if (filled($request->admin)) {
        $routeParameters['admin'] = 'true';
      }
      $information['publicOrganizerUrl'] = route('frontend.organizer.details', $routeParameters, true);

      return view('frontend.organizer.details', $information); //code...
    } catch (\Throwable $th) {
      return response()->view('errors.404', [], 404);
    }
  }


  public function sendMail(Request $request)
  {

    $info = DB::table('basic_settings')
      ->select('google_recaptcha_status', 'website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'email_address')
      ->first();

    $rules = [
      'name' => 'required',
      'email' => 'required',
      'subject' => 'required',
      'message' => 'required'
    ];
    if ($info->google_recaptcha_status == 1) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $msgs = [];

    if ($info->google_recaptcha_status == 1) {
      $msgs['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $msgs['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }
    $organizer = Organizer::where('id', $request->id)->first();


    $name = $request->name;
    $subject = $request->subject;

    $message = '<p>Message : ' . $request->message . '</p> <p><strong>Enquirer Name: </strong>' . $name . '<br/><strong>Enquirer Mail: </strong>' . $request->email . '</p>';

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    if ($info->smtp_status == 1) {

      $mail->isSMTP();
      $mail->Host       = $info->smtp_host;
      $mail->SMTPAuth   = true;
      $mail->Username   = $info->smtp_username;
      $mail->Password   = $info->smtp_password;

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port       = $info->smtp_port;
    }

    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($organizer->email);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $message;

      $mail->send();

      Session::flash('message', 'Your contact request send to organizer successfully.');
      Session::flash('alert-type', 'success');
    } catch (\Exception $e) {
      Session::flash('message', 'Something went wrong');
      Session::flash('alert-type', 'error');
    }

    return 'success';
  }
}
