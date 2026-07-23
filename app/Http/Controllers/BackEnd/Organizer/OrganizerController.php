<?php

namespace App\Http\Controllers\BackEnd\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\Organizer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Language;
use App\Models\OrganizerInfo;
use App\Models\Transaction;
use App\Rules\MatchOldPasswordRule;
use App\Services\EventSettlementService;
use DateTime;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizerController extends Controller
{
  private $admin_user_name;
  public function __construct()
  {
    $admin = Admin::select('username')->first();
    $this->admin_user_name = $admin->username;
  }

  //index
  public function index(EventSettlementService $eventSettlementService)
  {
    $organizer = Auth::guard('organizer')->user();
    $organizerId = $organizer->id;
    $information['total_events'] = Event::where('organizer_id', $organizerId)->get()->count();
    $information['total_event_bookings'] = Booking::where('organizer_id', $organizerId)->get()->count();
    $information['transcation_count'] = Transaction::where('organizer_id', $organizerId)->get()->count();
    $information['settlementSummary'] = $eventSettlementService->dashboardSummaryForOrganizer($organizerId);
    $publishedEvents = Event::where('organizer_id', $organizerId)->where('status', 1);
    $profileEventStats = [
      'total' => (clone $publishedEvents)->count(),
      'upcoming' => (clone $publishedEvents)->where('end_date_time', '>=', now())->count(),
      'past' => (clone $publishedEvents)->where('end_date_time', '<', now())->count(),
    ];
    $information['profileDashboard'] = $this->profileDashboard($organizer, $profileEventStats);

    //income of event bookings 
    $eventBookingTotalIncomes = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(price) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->where('organizer_id', $organizerId)
      ->get();

    $TotalEventBookings = DB::table('bookings')
      ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total'))
      ->where('paymentStatus', '=', 'completed')
      ->groupBy('month')
      ->whereYear('created_at', '=', date('Y'))
      ->where('organizer_id', $organizerId)
      ->get();



    $eventMonths = [];

    $eventIncomes = [];
    $totalBookings = [];

    //event icome calculation
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $dateObj = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('M');
      array_push($eventMonths, $monthName);

      // get all 12 months's income
      $incomeFound = false;

      foreach ($eventBookingTotalIncomes as $eventIncomeInfo) {
        if ($eventIncomeInfo->month == $i) {
          $incomeFound = true;
          array_push($eventIncomes, $eventIncomeInfo->total);
          break;
        }
      }

      if ($incomeFound == false) {
        array_push($eventIncomes, 0);
      }


      // get all 12 months's c
      $bookingFound = false;

      foreach ($TotalEventBookings as $eventInfo) {
        if ($eventInfo->month == $i) {
          $bookingFound = true;
          array_push($totalBookings, $eventInfo->total);
          break;
        }
      }

      if ($bookingFound == false) {
        array_push($totalBookings, 0);
      }
    }

    $information['eventIncomes'] = $eventIncomes;
    $information['eventMonths'] = $eventMonths;
    $information['totalBookings'] = $totalBookings;

    $information['admin_setting'] = DB::table('basic_settings')->where('uniqid', 12345)->select('organizer_admin_approval', 'admin_approval_notice')->first();


    return view('organizer.index', $information);
  }

  private function profileDashboard(Organizer $organizer, array $profileEventStats): array
  {
    $defaultLanguage = Language::where('is_default', 1)->first() ?: Language::first();
    $defaultInfo = $defaultLanguage
      ? OrganizerInfo::where('organizer_id', $organizer->id)->where('language_id', $defaultLanguage->id)->first()
      : null;
    $profileName = trim((string) ($defaultInfo->name ?? $organizer->username ?? __('Organizador')));
    $profileBio = trim(strip_tags((string) ($defaultInfo->details ?? '')));
    $profileLocation = trim(implode(', ', array_filter([
      $defaultInfo->city ?? null,
      $defaultInfo->country ?? null,
    ])));
    $metaPixelValid = preg_match('/^\d{6,32}$/', trim((string) $organizer->meta_pixel_id));
    $editProfileUrl = route('organizer.edit.profile');
    $profileSlug = Str::slug($profileName);
    $publicProfileUrl = route('frontend.organizer.details', [
      $organizer->id,
      $profileSlug !== '' ? $profileSlug : Str::slug($organizer->username ?: 'organizador'),
    ], true);
    $createEventUrl = route('choose-event-type');
    $publishedTotal = $profileEventStats['total'] ?? 0;
    $checks = [
      [
        'label' => $publishedTotal > 0 ? __('Publicá una nueva fecha') : __('Publicá tu primer evento'),
        'complete' => ($profileEventStats['upcoming'] ?? 0) > 0,
        'hint' => __('La agenda activa es lo primero que mira quien entra al perfil.'),
        'href' => $createEventUrl,
        'icon' => 'fas fa-calendar-plus',
      ],
      [
        'label' => __('Subí portada'),
        'complete' => !empty($organizer->cover_photo),
        'hint' => __('Dale contexto visual a tu marca y a tus próximos eventos.'),
        'href' => $editProfileUrl . '#opb-media-title',
        'icon' => 'fas fa-image',
      ],
      [
        'label' => __('Agregá Instagram'),
        'complete' => trim((string) $organizer->instagram) !== '',
        'hint' => __('Es la red que más valida eventos, comunidad y convocatorias.'),
        'href' => $editProfileUrl . '#opb-social-title',
        'icon' => 'fab fa-instagram',
      ],
      [
        'label' => __('Subí foto de perfil'),
        'complete' => !empty($organizer->photo),
        'hint' => __('Usá logo o imagen reconocible para que te identifiquen rápido.'),
        'href' => $editProfileUrl . '#opb-media-title',
        'icon' => 'fas fa-user-circle',
      ],
      [
        'label' => __('Escribí una bio clara'),
        'complete' => mb_strlen($profileBio) >= 80,
        'hint' => __('Explicá qué hacés, dónde y por qué seguir tu agenda.'),
        'href' => $editProfileUrl . '#opb-content-title',
        'icon' => 'fas fa-align-left',
      ],
      [
        'label' => __('Completá ciudad y país'),
        'complete' => $profileLocation !== '',
        'hint' => __('Ayuda a que te encuentren por ciudad o país.'),
        'href' => $editProfileUrl . '#opb-content-title',
        'icon' => 'fas fa-map-marker-alt',
      ],
      [
        'label' => __('Agregá Meta Pixel'),
        'complete' => $metaPixelValid,
        'hint' => __('Medí visitas, interés y contactos del perfil.'),
        'href' => $editProfileUrl . '#opb-measurement-title',
        'icon' => 'fas fa-chart-line',
      ],
    ];
    $done = collect($checks)->where('complete', true)->count();
    $percent = (int) round(($done / max(count($checks), 1)) * 100);

    return [
      'percent' => $percent,
      'done' => $done,
      'total' => count($checks),
      'label' => $percent >= 84 ? __('Listo para compartir') : ($percent >= 50 ? __('Buen avance') : __('Perfil por completar')),
      'copy' => $percent >= 84
        ? __('Tu perfil ya tiene las señales principales para presentarse y medir resultados.')
        : __('Completá estos puntos para que tu perfil público venda mejor tu agenda.'),
      'next_actions' => collect($checks)->filter(fn ($check) => !$check['complete'])->take(3)->values(),
      'public_url' => $publicProfileUrl,
      'edit_url' => $editProfileUrl,
      'upcoming' => $profileEventStats['upcoming'] ?? 0,
    ];
  }
  //login
  public function login()
  {
    return view('frontend.organizer.login');
  }
  //signup
  public function signup()
  {
    return view('frontend.organizer.signup');
  }
  //create
  public function create(Request $request)
  {
    $rules = [
      'name' => 'required',
      'username' => [
        'required',
        'alpha_dash',
        'unique:organizers',
        "not_in:$this->admin_user_name"
      ],
      'email' => 'required|email|unique:organizers',
      'password' => 'required|confirmed|min:10',
    ];

    $info = Basic::select('google_recaptcha_status')->first();
    if ($info->google_recaptcha_status == 1) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $messages = [];

    if ($info->google_recaptcha_status == 1) {
      $messages['g-recaptcha-response.required'] = __('organizer.captcha.required');
      $messages['g-recaptcha-response.captcha'] = __('organizer.captcha.error');
    }

    $validator = Validator::make($request->all(), $rules, $messages);


    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }



    $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('organizer_email_verification', 'organizer_admin_approval')->first();

    if ($setting->organizer_admin_approval == 0 && $setting->organizer_email_verification == 0) {
      $defaultStatus = 1;
    } else {
      $defaultStatus = 0;
    }

    $verificationToken = null;
    $organizerData = [
      'name' => $request->name,
      'username' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ];

    if ($setting->organizer_email_verification == 1) {
      $verificationToken = Str::random(64);
      $organizerData['email_verification_token'] = Hash::make($verificationToken);
      $organizerData['email_verification_sent_at'] = now();
    }

    $organizer = Organizer::create($organizerData);
    $organizer->status = $defaultStatus;
    $organizer->email_verified_at = null;
    $organizer->amount = 0;
    $organizer->save();

    $language = $this->getLanguage();
    OrganizerInfo::create([
      'organizer_id' => $organizer->id,
      'language_id' => $language->id,
      'name' => $request->name,
    ]);

    if ($setting->organizer_email_verification == 1) {
      // first, get the mail template information from db
      $mailTemplate = MailTemplate::where('mail_type', 'verify_email')->first();

      if (!$mailTemplate) {
        Log::error('Organizer signup: verify_email MailTemplate not found');
        OrganizerInfo::where('organizer_id', $organizer->id)->delete();
        $organizer->delete();
        Session::flash('error', __('Error de configuración. Contactá al administrador.'));
        return redirect()->back();
      }

      $mailSubject = $mailTemplate->mail_subject;
      $mailBody = $mailTemplate->mail_body;

      // second, send a password reset link to user via email
      $info = DB::table('basic_settings')
        ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
        ->first();

      $name = $request->username;
      $link = url("organizers/email/verify?token=" . urlencode($verificationToken));

      $mailBody = str_replace('{username}', $name, $mailBody);
      $mailBody = str_replace('{verification_link}', $link, $mailBody);
      $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

      // initialize a new mail
      $mail = new PHPMailer(true);
      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';

      // if smtp status == 1, then set some value for PHPMailer
      if ($info->smtp_status == 1) {

        $mail->isSMTP();
        $mail->Host       = $info->smtp_host;

        if (!empty($info->smtp_username)) {
          $mail->SMTPAuth   = true;
          $mail->Username   = $info->smtp_username;
          $mail->Password   = $info->smtp_password;
        }

        if ($info->encryption == 'TLS') {
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->Port       = $info->smtp_port;
      }

      // finally add other informations and send the mail
      try {
        $mail->setFrom($info->from_mail, $info->from_name);
        $mail->addAddress($request->email);

        $mail->isHTML(true);
        $mail->Subject = $mailSubject;
        $mail->Body = $mailBody;

        $mail->send();

        Session::flash('success', __('organizer.flash.verification_mail_sent'));
      } catch (\Exception $e) {
        Log::error('Organizer verification mail failed: ' . $e->getMessage());
        OrganizerInfo::where('organizer_id', $organizer->id)->delete();
        $organizer->delete();
        Session::flash('error', __('organizer.flash.mail_not_sent'));
        return redirect()->back();
      }
    } else {
      Session::flash('success', __('organizer.flash.signup_success'));
    }

    return redirect()->route('organizer.login');
  }
  //authenticate
  public function authentication(Request $request)
  {
    $rules = [
      'username' => 'required',
      'password' => 'required'
    ];

    $info = Basic::select('google_recaptcha_status')->first();
    if ($info->google_recaptcha_status == 1) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $messages = [];

    if ($info->google_recaptcha_status == 1) {
      $messages['g-recaptcha-response.required'] = __('organizer.captcha.required');
      $messages['g-recaptcha-response.captcha'] = __('organizer.captcha.error');
    }

    $validator = Validator::make($request->all(), $rules, $messages);


    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    if (
      Auth::guard('organizer')->attempt([
        'username' => $request->username,
        'password' => $request->password
      ])
    ) {
      $authAdmin = Auth::guard('organizer')->user();
      $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('organizer_email_verification', 'organizer_admin_approval')->first();

      // check whether the admin's account is active or not
      if ($setting->organizer_email_verification == 1 && $authAdmin->email_verified_at == NULL && $authAdmin->status == 0) {
        Session::flash('alert', __('organizer.flash.verify_email_alert'));

        // logout auth admin as condition not satisfied
        Auth::guard('organizer')->logout();

        return redirect()->back();
      } elseif ($setting->organizer_email_verification == 0 && $setting->organizer_admin_approval == 1) {
        Session::put('secret_login', 0);
        return redirect()->route('organizer.dashboard');
      } else {
        Session::put('secret_login', 0);
        return redirect()->route('organizer.dashboard');
      }
    } else {
      return redirect()->back()->with('alert', __('organizer.flash.login_error'));
    }
  }
  //forget_passord
  public function forget_passord()
  {
    return view('frontend.organizer.forget-password');
  }
  //forget_mail
  public function forget_mail(Request $request)
  {
    $rules = [
      'email' => [
        'required',
        'email:rfc,dns',
      ]
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $genericMessage = 'Si tu email está registrado, te enviamos un link de recuperación.';
    $user = Organizer::where('email', $request->email)->first();

    if (!$user) {
      usleep(random_int(100000, 500000));
      Session::flash('success', $genericMessage);
      return redirect()->back();
    }

    $mailTemplate = MailTemplate::where('mail_type', 'reset_password')->first();
    if (!$mailTemplate) {
      Log::error('Organizer password reset: reset_password MailTemplate not found');
      Session::flash('error', __('organizer.flash.mail_not_sent'));
      return redirect()->back();
    }

    $mailSubject = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    $name = $user->username;
    $token =  Str::random(64);
    DB::table('password_resets')->where('email', $user->email)->delete();
    DB::table('password_resets')->insert([
      'email' => $user->email,
      'token' => Hash::make('organizer|' . $token),
      'created_at' => now(),
    ]);

    $link = url("organizer/reset-password?token=" . $token);

    $mailBody = str_replace('{customer_name}', $name, $mailBody);
    $mailBody = str_replace('{password_reset_link}', $link, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

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
      $mail->addAddress($request->email);

      $mail->isHTML(true);
      $mail->Subject = $mailSubject;
      $mail->Body = $mailBody;

      $mail->send();

      Session::flash('success', $genericMessage);
    } catch (\Exception $e) {
      Log::error('Organizer password reset mail failed: ' . $e->getMessage());
      DB::table('password_resets')->where('email', $user->email)->delete();
      Session::flash('error', __('organizer.flash.mail_not_sent'));
    }

    $request->session()->put('userEmail', $user->email);

    return redirect()->back();
  }
  //reset_password
  public function reset_password()
  {
    return view('frontend.organizer.reset-password');
  }
  //update_password
  public function update_password(Request $request)
  {
    $request->validate([
      'password' => 'required|confirmed|min:10',
      'token' => 'required',
    ]);

    $reset = null;
    foreach (DB::table('password_resets')->get() as $candidate) {
      try {
        if (Hash::check('organizer|' . $request->token, $candidate->token)) {
          $reset = $candidate;
          break;
        }
      } catch (\Exception $e) {
        continue;
      }
    }

    if (!$reset) {
      Session::flash('alert', 'El link de recuperación es inválido o ya fue utilizado.');
      return redirect()->route('organizer.login');
    }

    if ($reset->created_at && \Carbon\Carbon::parse($reset->created_at)->diffInMinutes(now()) > 60) {
      DB::table('password_resets')->where('email', $reset->email)->delete();
      Session::flash('alert', 'El link de recuperación expiró. Pedí uno nuevo.');
      return redirect()->route('organizer.forget.password');
    }

    $organizer = Organizer::where('email',  $reset->email)->first();
    if (!$organizer) {
      DB::table('password_resets')->where('email', $reset->email)->delete();
      Session::flash('alert', 'Cuenta no encontrada.');
      return redirect()->route('organizer.login');
    }

    $organizer->password = Hash::make($request->password);
    $organizer->save();
    DB::table('password_resets')->where('email', $reset->email)->delete();
    Session::flash('success', __('organizer.flash.password_reset'));

    return redirect()->route('organizer.login');
  }
  public function logout(Request $request)
  {
    Auth::guard('organizer')->logout();
    Session::forget('secret_login');

    return redirect()->route('organizer.login');
  }
  //change_password
  public function change_password()
  {
    return view('organizer.change-password');
  }
  //update_password
  public function updated_password(Request $request)
  {
    $rules = [
      'current_password' => [
        'required',
        new MatchOldPasswordRule('organizer')

      ],
      'new_password' => 'required|confirmed|min:10',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => __('organizer.validation.password_confirmation'),
      'new_password_confirmation.required' => __('organizer.validation.confirm_new_password_required')
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $organizer = Auth::guard('organizer')->user();

    $organizer->update([
      'password' => Hash::make($request->new_password)
    ]);

    Session::flash('success', __('organizer.flash.password_updated'));

    return response()->json(['status' => 'success'], 200);
  }
  //edit_profile
  public function edit_profile()
  {
    $languages = Language::get();
    $organizer = Auth::guard('organizer')->user();
    $publishedEvents = Event::where('organizer_id', $organizer->id)->where('status', 1);
    $profileEventStats = [
      'total' => (clone $publishedEvents)->count(),
      'upcoming' => (clone $publishedEvents)->where('end_date_time', '>=', now())->count(),
      'past' => (clone $publishedEvents)->where('end_date_time', '<', now())->count(),
    ];

    return view('organizer.edit-profile', compact('languages', 'profileEventStats'));
  }
  //update_profile
  public function update_profile(Request $request)
  {


    $rules = [
      'email' => [
        'required',
        'email',
        Rule::unique('organizers', 'email')->ignore(Auth::guard('organizer')->user()->id)
      ],
      'username' => [
        'required',
        'alpha_dash',
        "not_in:$this->admin_user_name",
        Rule::unique('organizers', 'username')->ignore(Auth::guard('organizer')->user()->id)
      ],
      'phone' => 'nullable|string|max:50',
      'facebook' => 'nullable|url|max:255',
      'twitter' => 'nullable|url|max:255',
      'linkedin' => 'nullable|url|max:255',
      'website' => 'nullable|url|max:255',
      'instagram' => 'nullable|url|max:255',
      'tiktok' => 'nullable|url|max:255',
      'meta_pixel_id' => ['nullable', 'regex:/^\d{6,32}$/'],
    ];

    $languages = Language::get();

    $messages = [];
    $messages['meta_pixel_id.regex'] = __('El Meta Pixel ID debe tener sólo números, entre 6 y 32 dígitos.');

    foreach ($languages as $language) {
      $rules[$language->code . '_name'] = 'required';
      $rules[$language->code . '_designation'] = 'nullable|string|max:255';
      $rules[$language->code . '_country'] = 'nullable|string|max:255';
      $rules[$language->code . '_city'] = 'nullable|string|max:255';
      $rules[$language->code . '_state'] = 'nullable|string|max:255';
      $rules[$language->code . '_zip_code'] = 'nullable|string|max:50';
      $rules[$language->code . '_address'] = 'nullable|string';
      $rules[$language->code . '_details'] = 'nullable|string';
      $messages[$language->code . '_name'] = __('organizer.validation.name_required_for_language', ['language' => $language->name]);
    }

    if ($request->hasFile('photo')) {
      $rules['photo'] = 'image|mimes:jpg,jpeg,png|max:2048|dimensions:width=300,height=300';
    }
    if ($request->hasFile('cover_photo')) {
      $rules['cover_photo'] = 'image|mimes:jpg,jpeg,png,webp|max:4096';
    }

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return Response::json(
        [
          'errors' => $validator->getMessageBag()
        ],
        400
      );
    }

    $validated = $validator->validated();
    $in = array_intersect_key($validated, array_flip([
      'email',
      'phone',
      'username',
      'facebook',
      'twitter',
      'linkedin',
      'website',
      'instagram',
      'tiktok',
      'meta_pixel_id',
    ]));
    $organizer = Organizer::find(Auth::guard('organizer')->user()->id);
    $file = $request->file('photo');
    if ($file) {
      $directory = public_path('assets/admin/img/organizer-photo/');
      $fileName = $file->hashName();
      @mkdir($directory, 0775, true);
      $file->move($directory, $fileName);

      if (!empty($organizer->photo)) {
        @unlink(public_path('assets/admin/img/organizer-photo/') . $organizer->photo);
      }
      $in['photo'] = $fileName;
    }
    $coverFile = $request->file('cover_photo');
    if ($coverFile) {
      $directory = public_path('assets/admin/img/organizer-cover-photo/');
      $fileName = $coverFile->hashName();
      @mkdir($directory, 0775, true);
      $coverFile->move($directory, $fileName);

      if (!empty($organizer->cover_photo)) {
        @unlink(public_path('assets/admin/img/organizer-cover-photo/') . $organizer->cover_photo);
      }
      $in['cover_photo'] = $fileName;
    }
    $organizer->update($in);

    $languages = Language::get();
    foreach ($languages as $language) {
      $organizer_info = OrganizerInfo::where('organizer_id', $organizer->id)->where('language_id', $language->id)->first();
      if (!$organizer_info) {
        $organizer_info = new OrganizerInfo();
        $organizer_info->language_id = $language->id;
        $organizer_info->organizer_id = $organizer->id;
      }
      $organizer_info->name = $validated[$language->code . '_name'];
      $organizer_info->designation = $validated[$language->code . '_designation'] ?? null;
      $organizer_info->country = $validated[$language->code . '_country'] ?? null;
      $organizer_info->city = $validated[$language->code . '_city'] ?? null;
      $organizer_info->state = $validated[$language->code . '_state'] ?? null;
      $organizer_info->zip_code = $validated[$language->code . '_zip_code'] ?? null;
      $organizer_info->address = $validated[$language->code . '_address'] ?? null;
      $organizer_info->details = $validated[$language->code . '_details'] ?? null;
      $organizer_info->save();
    }

    Session::flash('success', __('organizer.flash.profile_updated'));

    return Response::json(['status' => 'success'], 200);
  }
  //verify_email
  public function verify_email()
  {
    return view('organizer.verify');
  }
  //send_link
  public function send_link(Request $request)
  {

    $user = Organizer::where('email', Auth::guard('organizer')->user()->email)->first();


    // first, get the mail template information from db
    $mailTemplate = MailTemplate::where('mail_type', 'verify_email')->first();

    $mailSubject = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // second, send a password reset link to user via email
    $info = DB::table('basic_settings')
      ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    $name = $user->name;
    $token = Str::random(64);
    $user->email_verification_token = Hash::make($token);
    $user->email_verification_sent_at = now();
    $user->save();

    $link = url("organizers/email/verify?token=" . urlencode($token));

    $mailBody = str_replace('{username}', $user->name, $mailBody);
    $mailBody = str_replace('{verification_link}', $link, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

    // initialize a new mail
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // if smtp status == 1, then set some value for PHPMailer
    if ($info->smtp_status == 1) {

      $mail->isSMTP();
      $mail->Host       = $info->smtp_host;

      if (!empty($info->smtp_username)) {
        $mail->SMTPAuth   = true;
        $mail->Username   = $info->smtp_username;
        $mail->Password   = $info->smtp_password;
      }

      if ($info->encryption == 'TLS') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      }

      $mail->Port       = $info->smtp_port;
    }

    // finally add other informations and send the mail
    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($user->email);

      $mail->isHTML(true);
      $mail->Subject = $mailSubject;
      $mail->Body = $mailBody;

      $mail = $mail->send();

      Session::flash('success', __('organizer.flash.verification_mail_sent'));
      return response()->json(['status' => 'success'], 200);
    } catch (\Exception $e) {
      Log::error('Organizer verification mail resend failed: ' . $e->getMessage());
      Session::flash('error', __('organizer.flash.mail_not_sent'));
      return redirect()->back();
    }
  }
  //confirm_email'
  public function confirm_email()
  {
    $token = request()->input('token');

    if (empty($token)) {
      Session::flash('error', __('Link de verificación inválido.'));
      return redirect()->route('organizer.login');
    }

    $candidates = Organizer::whereNotNull('email_verification_token')
      ->whereNull('email_verified_at')
      ->get();

    $user = null;
    foreach ($candidates as $candidate) {
      if (Hash::check($token, $candidate->email_verification_token)) {
        $user = $candidate;
        break;
      }
    }

    if (!$user) {
      Session::flash('error', __('Link de verificación inválido o expirado.'));
      return redirect()->route('organizer.login');
    }

    if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInMinutes(now()) > 1440) {
      $user->email_verification_token = null;
      $user->email_verification_sent_at = null;
      $user->save();
      Session::flash('error', __('El link de verificación expiró. Pedí uno nuevo desde tu panel.'));
      return redirect()->route('organizer.login');
    }

    $user->email_verified_at = now();
    $user->email_verification_token = null;
    $user->email_verification_sent_at = null;
    $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('organizer_admin_approval')->first();
    if ($setting->organizer_admin_approval != 1) {
      $user->status = 1;
    }
    $user->save();

    Session::put('secret_login', 0);
    Session::flash('success', __('Email verificado. Ya podés iniciar sesión.'));
    return redirect()->route('organizer.login');
  }
  //pwa
  public function pwa()
  {
    if (Auth::guard('organizer')->check()) {
      return view('organizer.pwa.index');
    } else {
      return redirect()->route('organizer.login');
    }
  }

  //check_qrcode
  public function check_qrcode(Request $request)
  {
    if (str_contains($request->booking_id, '__')) {
      $ids = explode('__', $request->booking_id);
      $booking_id = $ids[0];
      $unique_id = $ids[1];
      $organizer_id = Auth::guard('organizer')->user()->id;
      $check = Booking::where([['booking_id', $booking_id]])->first();
      if ($check) {
        if ($check->organizer_id == $organizer_id) {
          // check payment status completed or not 
          if ($check->paymentStatus == 'completed' || $check->paymentStatus == 'free') {
            if (!$check->hasIssuedTicketUniqueId($unique_id)) {
              return response()->json(['alert_type' => 'error', 'message' => __('organizer.qrcode.unverified'), 'booking_id' => $request->booking_id]);
            }

            $scannedTicketArr = $check->scannedTicketIds();
            if (!in_array($unique_id, $scannedTicketArr, true)) {
              $scannedTicketArr[] = $unique_id;
              $check->scanned_tickets = json_encode($scannedTicketArr);
              $check->save();
              return response()->json(['alert_type' => 'success', 'message' => __('organizer.qrcode.verified'), 'booking_id' => $request->booking_id]);
            } else {
              return response()->json(['alert_type' => 'error', 'message' => __('organizer.qrcode.already_scanned'), 'booking_id' => $request->booking_id]);
            }
          } elseif ($check->paymentStatus == 'pending') {
            return response()->json(['alert_type' => 'error', 'message' => __('organizer.qrcode.payment_incomplete'), 'booking_id' => $request->booking_id]);
          } elseif ($check->paymentStatus == 'rejected') {
            return response()->json(['alert_type' => 'error', 'message' => __('organizer.qrcode.payment_rejected'), 'booking_id' => $request->booking_id]);
          }
        } else {
          return response()->json(['alert_type' => 'error', 'message' => __('organizer.qrcode.no_permission')]);
        }
      } else {
        return response()->json(['alert_type' => 'error', 'message' => __('organizer.qrcode.unverified')]);
      }
    } else {
      return response()->json(['alert_type' => 'error', 'message' => __('organizer.qrcode.unverified')]);
    }
  }

  public function changeTheme(Request $request)
  {
    $organizerInfo = Organizer::where('id', Auth::guard('organizer')->user()->id)->first();
    $organizerInfo->theme_version = $request->theme_version;
    $organizerInfo->save();
    return redirect()->back();
  }

  //transaction
  public function transaction(Request $request)
  {
    $transcation_id = null;
    if ($request->filled('transcation_id')) {
      $transcation_id = $request->transcation_id;
    }
    $transcations = Transaction::where('organizer_id', Auth::guard('organizer')->user()->id)
      ->when($transcation_id, function ($query) use ($transcation_id) {
        return $query->where('transcation_id', 'like', '%' . $transcation_id . '%');
      })
      ->orderBy('id', 'desc')->paginate(10);
    return view('organizer.transaction', compact('transcations'));
  }

  //monthly  income
  public function monthly_income(Request $request)
  {
    if ($request->filled('year')) {
      $date = $request->input('year');
    } else {
      $date = date('Y');
    }

    $monthWiseTotalIncomes = DB::table('transactions')->where('organizer_id', Auth::guard('organizer')->user()->id)
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
      ->where(function ($query) {
        return $query->where('transcation_type', 1)
          ->orWhere('transcation_type', 4);
      })
      ->where('payment_status', 1)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();


    $monthWiseTotalReject = DB::table('transactions')->where('organizer_id', Auth::guard('organizer')->user()->id)
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
      ->where('transcation_type', 3)
      ->where('payment_status', 2)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();


    $monthWiseTotalCommission = DB::table('transactions')->where('organizer_id', Auth::guard('organizer')->user()->id)
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(commission) as total'))
      ->where(function ($query) {
        return $query->where('transcation_type', 1)
          ->orWhere('transcation_type', 3);
      })
      ->where('payment_status', 1)
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();

    $monthWiseTotalExpenses = DB::table('transactions')->where('organizer_id', Auth::guard('organizer')->user()->id)
      ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
      ->where(function ($query) {
        return $query->where('transcation_type', 3)
          ->orWhere('transcation_type', 5);
      })
      ->groupBy('month')
      ->whereYear('created_at', '=', $date)
      ->get();

    $months = [];
    $incomes = [];
    $rejects = [];
    $commissions = [];
    $expenses = [];
    for ($i = 1; $i <= 12; $i++) {
      // get all 12 months name
      $monthNum = $i;
      $dateObj = DateTime::createFromFormat('!m', $monthNum);
      $monthName = $dateObj->format('M');
      array_push($months, $monthName);

      // get all 12 months's income of booking
      $incomeFound = false;
      foreach ($monthWiseTotalIncomes as $incomeInfo) {
        if ($incomeInfo->month == $i) {
          $incomeFound = true;
          array_push($incomes, $incomeInfo->total);
          break;
        }
      }
      if ($incomeFound == false) {
        array_push($incomes, 0);
      }

      // get all 12 months's total reject
      $rejectFound = false;
      foreach ($monthWiseTotalReject as $Reject) {
        if ($Reject->month == $i) {
          $rejectFound = true;
          array_push($rejects, $Reject->total);
          break;
        }
      }
      if ($rejectFound == false) {
        array_push($rejects, 0);
      }

      // get all 12 months's commission of event booking
      $commissionFound = false;
      foreach ($monthWiseTotalCommission as $commissionInfo) {
        if ($commissionInfo->month == $i) {
          $commissionFound = true;
          array_push($commissions, $commissionInfo->total);
          break;
        }
      }
      if ($commissionFound == false) {
        array_push($commissions, 0);
      }

      // get all 12 months's expenses of equipment booking
      $expensesFound = false;
      foreach ($monthWiseTotalExpenses as $expensesInfo) {
        if ($expensesInfo->month == $i) {
          $expensesFound = true;
          array_push($expenses, $expensesInfo->total);
          break;
        }
      }
      if ($expensesFound == false) {
        array_push($expenses, 0);
      }
    }
    $information['months'] = $months;
    $information['incomes'] = $incomes;
    $information['rejects'] = $rejects;
    $information['commissions'] = $commissions;
    $information['expenses'] = $expenses;

    return view('organizer.income', $information);
  }
}
