<?php

namespace App\Http\Controllers\BackEnd\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Event;
use App\Models\Event\EventContent;
use App\Models\Event\EventImage;
use App\Models\Language;
use App\Models\Organizer;
use App\Models\OrganizerInfo;
use App\Models\Transaction;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;

class OrganizerManagementController extends Controller
{
  private $admin_user_name;
  public function __construct()
  {
    $admin = Admin::select('username')->first();
    $this->admin_user_name = $admin->username;
  }

  public function settings()
  {
    $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('organizer_email_verification', 'organizer_admin_approval', 'admin_approval_notice')->first();
    return view('backend.end-user.organizer.settings', compact('setting'));
  }
  //update_setting
  public function update_setting(Request $request)
  {
    if ($request->organizer_email_verification) {
      $organizer_email_verification = 1;
    } else {
      $organizer_email_verification = 0;
    }
    if ($request->organizer_admin_approval) {
      $organizer_admin_approval = 1;
    } else {
      $organizer_admin_approval = 0;
    }
    // finally, store the favicon into db
    DB::table('basic_settings')->updateOrInsert(
      ['uniqid' => 12345],
      [
        'organizer_email_verification' => $organizer_email_verification,
        'organizer_admin_approval' => $organizer_admin_approval,
        'admin_approval_notice' => $request->admin_approval_notice,
      ]
    );

    Session::flash('success', __('admin.flash.updated_successfully'));
    return back();
  }

  public function index(Request $request)
  {
    $searchKey = null;
    $profileFilter = $request->input('profile_filter');
    $profileOpportunityFilters = $this->profileOpportunityFilters();

    if ($request->filled('info')) {
      $searchKey = $request['info'];
    }

    $defaultLanguage = Language::where('is_default', 1)->first() ?: Language::first();

    $organizers = Organizer::with(['organizer_info' => function ($query) use ($defaultLanguage) {
      if ($defaultLanguage) {
        $query->where('language_id', $defaultLanguage->id);
      }
    }])
      ->when($searchKey, function ($query, $searchKey) {
        return $query->where(function ($query) use ($searchKey) {
          $query->where('username', 'like', '%' . $searchKey . '%')
            ->orWhere('email', 'like', '%' . $searchKey . '%');
        });
      })
      ->when(array_key_exists((string) $profileFilter, $profileOpportunityFilters), function ($query) use ($profileFilter, $defaultLanguage) {
        $this->applyOrganizerOpportunityFilter($query, (string) $profileFilter, $defaultLanguage);
      })
      ->orderBy('id', 'desc')
      ->paginate(10);

    $organizerIds = $organizers->getCollection()->pluck('id')->values()->all();
    $eventStatsByOrganizer = empty($organizerIds)
      ? collect()
      : Event::query()
        ->whereIn('organizer_id', $organizerIds)
        ->where('status', 1)
        ->select('organizer_id')
        ->selectRaw('COUNT(*) as total')
        ->selectRaw('SUM(CASE WHEN end_date_time >= ? THEN 1 ELSE 0 END) as upcoming', [now()->toDateTimeString()])
        ->selectRaw('SUM(CASE WHEN end_date_time < ? THEN 1 ELSE 0 END) as past', [now()->toDateTimeString()])
        ->groupBy('organizer_id')
        ->get()
        ->keyBy('organizer_id');

    $profileQualityByOrganizer = $organizers->getCollection()
      ->mapWithKeys(function ($organizer) use ($eventStatsByOrganizer) {
        return [
          $organizer->id => $this->organizerProfileQuality($organizer, $eventStatsByOrganizer->get($organizer->id)),
        ];
      });

    return view('backend.end-user.organizer.index', compact('organizers', 'profileQualityByOrganizer', 'profileOpportunityFilters', 'profileFilter'));
  }

  private function profileOpportunityFilters(): array
  {
    return [
      'low_profile' => __('Perfil flojo'),
      'without_cover' => __('Sin portada'),
      'without_description' => __('Sin descripción'),
      'without_social' => __('Sin redes'),
      'without_pixel' => __('Sin Meta Pixel'),
      'without_active_event' => __('Sin evento activo'),
    ];
  }

  private function applyOrganizerOpportunityFilter($query, string $profileFilter, $defaultLanguage): void
  {
    if ($profileFilter === 'low_profile') {
      $query->where(function ($query) use ($defaultLanguage) {
        $query->where(function ($query) {
          $this->whereMissingCover($query);
        })
          ->orWhere(function ($query) use ($defaultLanguage) {
            $this->whereMissingStrongDescription($query, $defaultLanguage);
          })
          ->orWhere(function ($query) {
            $this->whereMissingSocialLinks($query);
          })
          ->orWhere(function ($query) {
            $this->whereMissingActiveEvent($query);
          });
      });

      return;
    }

    if ($profileFilter === 'without_cover') {
      $this->whereMissingCover($query);
    }

    if ($profileFilter === 'without_description') {
      $this->whereMissingStrongDescription($query, $defaultLanguage);
    }

    if ($profileFilter === 'without_social') {
      $this->whereMissingSocialLinks($query);
    }

    if ($profileFilter === 'without_pixel') {
      $query->where(function ($query) {
        $query->whereNull('meta_pixel_id')
          ->orWhere('meta_pixel_id', '')
          ->orWhereRaw("meta_pixel_id NOT REGEXP '^[0-9]{6,32}$'");
      });
    }

    if ($profileFilter === 'without_active_event') {
      $this->whereMissingActiveEvent($query);
    }
  }

  private function whereMissingCover($query): void
  {
    $query->where(function ($query) {
      $query->whereNull('cover_photo')
        ->orWhere('cover_photo', '');
    });
  }

  private function whereMissingStrongDescription($query, $defaultLanguage): void
  {
    $query->whereDoesntHave('organizer_info', function ($query) use ($defaultLanguage) {
      if ($defaultLanguage) {
        $query->where('language_id', $defaultLanguage->id);
      }

      $query->whereRaw("CHAR_LENGTH(TRIM(COALESCE(details, ''))) >= 80");
    });
  }

  private function whereMissingSocialLinks($query): void
  {
    $query->where(function ($query) {
      foreach (['website', 'instagram', 'tiktok', 'facebook', 'twitter', 'linkedin'] as $field) {
        $query->where(function ($query) use ($field) {
          $query->whereNull($field)
            ->orWhere($field, '');
        });
      }
    });
  }

  private function whereMissingActiveEvent($query): void
  {
    $query->whereNotExists(function ($query) {
      $query->select(DB::raw(1))
        ->from('events')
        ->whereColumn('events.organizer_id', 'organizers.id')
        ->where('events.status', 1)
        ->where('events.end_date_time', '>=', now()->toDateTimeString());
    });
  }

  private function organizerProfileQuality(Organizer $organizer, $eventStats): array
  {
    $info = $organizer->organizer_info;
    $profileBio = trim(strip_tags((string) ($info->details ?? '')));
    $profileLocation = trim(implode(', ', array_filter([
      $info->city ?? null,
      $info->country ?? null,
    ])));
    $socialCount = collect([
      $organizer->website,
      $organizer->instagram,
      $organizer->tiktok,
      $organizer->facebook,
      $organizer->twitter,
      $organizer->linkedin,
    ])->filter(fn ($url) => trim((string) $url) !== '')->count();
    $metaPixelValid = preg_match('/^\d{6,32}$/', trim((string) $organizer->meta_pixel_id));
    $upcomingCount = (int) ($eventStats->upcoming ?? 0);
    $checks = [
      [
        'label' => __('Visual'),
        'complete' => !empty($organizer->photo) && !empty($organizer->cover_photo),
        'impact' => __('Impacto: mejora confianza al compartir el perfil y evita que se vea genérico.'),
      ],
      [
        'label' => __('Descripción'),
        'complete' => mb_strlen($profileBio) >= 80,
        'impact' => __('Impacto: ayuda a Google, IA y usuarios a entender qué hace el organizador.'),
      ],
      [
        'label' => __('Ubicación'),
        'complete' => $profileLocation !== '',
        'impact' => __('Impacto: mejora búsquedas por ciudad, país y contexto local.'),
      ],
      [
        'label' => __('Redes'),
        'complete' => $socialCount > 0,
        'impact' => __('Impacto: valida la entidad con enlaces oficiales y da canales de confianza.'),
      ],
      [
        'label' => __('Pixel'),
        'complete' => $metaPixelValid,
        'impact' => __('Impacto: permite medir visitas y contactos del perfil con Meta Pixel.'),
      ],
      [
        'label' => __('Evento activo'),
        'complete' => $upcomingCount > 0,
        'impact' => __('Impacto: convierte el perfil en una agenda reservable, no sólo una ficha.'),
      ],
    ];
    $done = collect($checks)->where('complete', true)->count();
    $percent = (int) round(($done / max(count($checks), 1)) * 100);
    $profileName = trim((string) ($info->name ?? $organizer->username ?? 'organizador'));
    $profileSlug = Str::slug($profileName);

    return [
      'percent' => $percent,
      'done' => $done,
      'total' => count($checks),
      'label' => $percent >= 84 ? __('Fuerte') : ($percent >= 50 ? __('En progreso') : __('Flojo')),
      'tone' => $percent >= 84 ? 'is-strong' : ($percent >= 50 ? 'is-mid' : 'is-low'),
      'signals' => collect($checks),
      'complete' => collect($checks)->where('complete', true)->values(),
      'missing' => collect($checks)->filter(fn ($check) => !$check['complete'])->take(3)->values(),
      'gaps' => collect($checks)->filter(fn ($check) => !$check['complete'])->values(),
      'upcoming' => $upcomingCount,
      'public_url' => route('frontend.organizer.details', [
        $organizer->id,
        $profileSlug !== '' ? $profileSlug : Str::slug($organizer->username ?: 'organizador'),
      ], true),
      'edit_url' => route('admin.edit_management.organizer_edit', ['id' => $organizer->id]),
    ];
  }

  //add
  public function add()
  {
    $languages = Language::get();
    return view('backend.end-user.organizer.create', compact('languages'));
  }
  public function create(Request $request)
  {
    $rules = [
      'email' => [
        'required',
        'email',
        Rule::unique('organizers', 'email')
      ],
      'username' => [
        'required',
        'alpha_dash',
        "not_in:$this->admin_user_name",
        Rule::unique('organizers', 'username')
      ],
      'password' => 'required|confirmed|min:10',
      'phone' => 'nullable|string|max:50',
      'facebook' => 'nullable|url|max:255',
      'twitter' => 'nullable|url|max:255',
      'linkedin' => 'nullable|url|max:255',
      'website' => 'nullable|url|max:255',
      'instagram' => 'nullable|url|max:255',
      'tiktok' => 'nullable|url|max:255',
      'meta_pixel_id' => ['nullable', 'regex:/^\d{6,32}$/'],
      'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048|dimensions:width=300,height=300',
      'cover_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ];

    $languages = Language::get();

    $messages = [];

    foreach ($languages as $language) {
      $rules[$language->code . '_name'] = 'required';
      $rules[$language->code . '_designation'] = 'nullable|string|max:255';
      $rules[$language->code . '_country'] = 'nullable|string|max:255';
      $rules[$language->code . '_city'] = 'nullable|string|max:255';
      $rules[$language->code . '_state'] = 'nullable|string|max:255';
      $rules[$language->code . '_zip_code'] = 'nullable|string|max:50';
      $rules[$language->code . '_address'] = 'nullable|string';
      $rules[$language->code . '_details'] = 'nullable|string';
      $messages[$language->code . '_name'] = 'The name field is required for ' . $language->name . ' language.';
    }

    $validated = $request->validate($rules, $messages);

    $organizerData = [
      'username' => $validated['username'],
      'email' => $validated['email'],
      'phone' => $validated['phone'] ?? null,
      'facebook' => $validated['facebook'] ?? null,
      'twitter' => $validated['twitter'] ?? null,
      'linkedin' => $validated['linkedin'] ?? null,
      'website' => $validated['website'] ?? null,
      'instagram' => $validated['instagram'] ?? null,
      'tiktok' => $validated['tiktok'] ?? null,
      'meta_pixel_id' => $validated['meta_pixel_id'] ?? null,
      'password' => Hash::make($validated['password']),
    ];

    $file = $request->file('photo');
    if ($file) {
      $directory = public_path('assets/admin/img/organizer-photo/');
      $fileName = $file->hashName();
      @mkdir($directory, 0775, true);
      $file->move($directory, $fileName);
      $organizerData['photo'] = $fileName;
    }
    $coverFile = $request->file('cover_photo');
    if ($coverFile) {
      $directory = public_path('assets/admin/img/organizer-cover-photo/');
      $fileName = $coverFile->hashName();
      @mkdir($directory, 0775, true);
      $coverFile->move($directory, $fileName);
      $organizerData['cover_photo'] = $fileName;
    }

    $organizer = Organizer::create($organizerData);
    $organizer->status = 1;
    $organizer->email_verified_at = now();
    $organizer->amount = 0;
    $organizer->save();

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
    Session::flash('success', __('organizer.flash.added_successfully'));
    return Response::json(['status' => 'success'], 200);
  }

  public function updateEmailStatus(Request $request, $id)
  {
    $organizer = Organizer::find($id);
    if ($request->email_status == 1) {
      $organizer->email_verified_at = now();
    } else {
      $organizer->email_verified_at = null;
    }
    $organizer->save();
    Session::flash('success', __('organizer.flash.email_verification_status_updated'));

    return redirect()->back();
  }

  public function show($id)
  {

    $information['langs'] = Language::all();

    $language = Language::where('code', request()->input('language'))->firstOrFail();
    $information['language'] = $language;

    $event_type = request()->input('event_type');


    $events = Event::join('event_contents', 'event_contents.event_id', '=', 'events.id')
      ->join('event_categories', 'event_categories.id', '=', 'event_contents.event_category_id')
      ->where('event_contents.language_id', '=', $language->id)
      ->where('events.organizer_id', '=', $id)
      ->when($event_type, function ($query, $event_type) {
        return $query->where('events.event_type', $event_type);
      })
      ->select('events.*', 'event_contents.id as eventInfoId', 'event_contents.title', 'event_categories.name as category', 'event_contents.slug')
      ->orderByDesc('events.id')
      ->get();

    $information['events'] = $events;

    $organizer = Organizer::with(['organizer_info' => function ($query) use ($language) {
      $query->where('language_id', $language->id);
    }])->findOrFail($id);
    $information['organizer'] = $organizer;
    $profileEventStats = Event::query()
      ->where('organizer_id', $organizer->id)
      ->where('status', 1)
      ->selectRaw('COUNT(*) as total')
      ->selectRaw('SUM(CASE WHEN end_date_time >= ? THEN 1 ELSE 0 END) as upcoming', [now()->toDateTimeString()])
      ->selectRaw('SUM(CASE WHEN end_date_time < ? THEN 1 ELSE 0 END) as past', [now()->toDateTimeString()])
      ->first();
    $information['profileQualityDetail'] = $this->organizerProfileQuality($organizer, $profileEventStats);

    return view('backend.end-user.organizer.details', $information);
  }
  public function updateAccountStatus(Request $request, $id)
  {

    $user = Organizer::find($id);
    if ($request->account_status == 1) {
      $user->status = 1;
    } else {
      $user->status = 0;
    }
    $user->save();
    Session::flash('success', __('organizer.flash.updated_successfully'));

    return redirect()->back();
  }
  public function changePassword($id)
  {
    $userInfo = Organizer::findOrFail($id);

    return view('backend.end-user.organizer.change-password', compact('userInfo'));
  }
  public function updatePassword(Request $request, $id)
  {
    $rules = [
      'new_password' => 'required|confirmed|min:10',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => 'Password confirmation does not match.',
      'new_password_confirmation.required' => 'The confirm new password field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $user = Organizer::find($id);

    $user->update([
      'password' => Hash::make($request->new_password)
    ]);

    Session::flash('success', __('organizer.flash.updated_successfully'));

    return Response::json(['status' => 'success'], 200);
  }

  public function edit($id)
  {
    $information = [];
    $languages = Language::get();
    $organizer = Organizer::findOrFail($id);
    $information['organizer'] = $organizer;
    $information['currencyInfo'] = $this->getCurrencyInfo();
    $information['languages'] = $languages;
    return view('backend.end-user.organizer.edit', $information);
  }

  //update
  public function update(Request $request, $id, Organizer $organizer)
  {
    try {
      $rules = [
        'email' => [
          'required',
          'email',
          Rule::unique('organizers', 'email')->ignore($id)
        ],
        'username' => [
          'required',
          'alpha_dash',
          "not_in:$this->admin_user_name",
          Rule::unique('organizers', 'username')->ignore($id)
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
      foreach ($languages as $language) {
        $rules[$language->code . '_name'] = 'required';
        $rules[$language->code . '_designation'] = 'nullable|string|max:255';
        $rules[$language->code . '_country'] = 'nullable|string|max:255';
        $rules[$language->code . '_city'] = 'nullable|string|max:255';
        $rules[$language->code . '_state'] = 'nullable|string|max:255';
        $rules[$language->code . '_zip_code'] = 'nullable|string|max:50';
        $rules[$language->code . '_address'] = 'nullable|string';
        $rules[$language->code . '_details'] = 'nullable|string';
        $messages[$language->code . '_name'] = 'The name field is required for ' . $language->name . ' language.';
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
      $organizer  = Organizer::where('id', $id)->first();
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
    } catch (\Exception $th) {
      Log::error('Organizer profile update failed.', [
        'organizer_id' => $id,
        'exception' => $th,
      ]);

      return Response::json(
        [
          'errors' => [
            'error' => [__('Something went wrong')]
          ]
        ],
        500
      );
    }

    Session::flash('success', __('organizer.flash.updated_successfully'));

    return Response::json(['status' => 'success'], 200);
  }
  //update_organizer_balance
  public function update_organizer_balance(Request $request, $id)
  {
    $organizer  = Organizer::where('id', $id)->first();
    $currency_info = Basic::select('base_currency_symbol_position', 'base_currency_symbol')
      ->first();
    //add or subtract organizer balance
    if ($request->amount_status && $request->amount_status == 1) {
      $amount = $organizer->amount + $request->amount;

      //store data to transcation table
      $transcation = Transaction::create([
        'transcation_id' => time(),
        'booking_id' => NULL,
        'transcation_type' => 4,
        'user_id' => NULL,
        'organizer_id' => $organizer->id,
        'payment_status' => 1,
        'payment_method' => NULL,
        'grand_total' => $request->amount,
        'pre_balance' => $organizer->amount,
        'after_balance' => $amount,
        'gateway_type' => NULL,
        'currency_symbol' => $currency_info->base_currency_symbol,
        'currency_symbol_position' => $currency_info->base_currency_symbol_position,
      ]);

      $organizer_new_amount = $amount;
    } else {
      $amount = $organizer->amount - $request->amount;
      //store data to transcation table
      $transcation = Transaction::create([
        'transcation_id' => time(),
        'booking_id' => NULL,
        'transcation_type' => 5,
        'user_id' => NULL,
        'organizer_id' => $organizer->id,
        'payment_status' => 1,
        'payment_method' => NULL,
        'grand_total' => $request->amount,
        'pre_balance' => $organizer->amount,
        'after_balance' => $amount,
        'gateway_type' => NULL,
        'currency_symbol' => $currency_info->base_currency_symbol,
        'currency_symbol_position' => $currency_info->base_currency_symbol_position,
      ]);

      $organizer_new_amount = $amount;
    }

    //send mail
    if ($request->amount_status == 1 || $request->amount_status == 0) {
      if ($request->amount_status == 1) {
        $template_type = 'balance_add';

        $organizer_alert_msg = "Balance added to organizer account succefully.!";
      } else {
        $template_type = 'balance_subtract';
        $organizer_alert_msg = "Balance Subtract from organizer account succefully.!";
      }
      //mail sending
      // get the website title & mail's smtp information from db
      $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'base_currency_symbol_position', 'base_currency_symbol')
        ->first();

      //preparing mail info
      // get the mail template info from db
      $mailTemplate = MailTemplate::query()->where('mail_type', '=', $template_type)->first();
      $mailData['subject'] = $mailTemplate->mail_subject;
      $mailBody = $mailTemplate->mail_body;

      // get the website title info from db
      $website_info = Basic::select('website_title')->first();

      // preparing dynamic data
      $organizerName = $organizer->username;
      $organizerEmail = $organizer->email;
      $organizer_amount = $amount;

      $websiteTitle = $website_info->website_title;

      // replacing with actual data
      $mailBody = str_replace('{transaction_id}', $transcation->transcation_id, $mailBody);
      $mailBody = str_replace('{username}', $organizerName, $mailBody);
      $mailBody = str_replace('{amount}', $info->base_currency_symbol . $request->amount, $mailBody);

      $mailBody = str_replace('{current_balance}', $info->base_currency_symbol . $organizer_amount, $mailBody);
      $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);

      $mailData['body'] = $mailBody;

      $mailData['recipient'] = $organizerEmail;
      //preparing mail info end

      // initialize a new mail
      $mail = new PHPMailer(true);
      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';

      // if smtp status == 1, then set some value for PHPMailer
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

      // add other informations and send the mail
      try {
        $mail->setFrom($info->from_mail, $info->from_name);
        $mail->addAddress($mailData['recipient']);

        $mail->isHTML(true);
        $mail->Subject = $mailData['subject'];
        $mail->Body = $mailData['body'];

        $mail->send();
        Session::flash('success', $organizer_alert_msg);
      } catch (Exception $e) {
        Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo);
      }
      //mail sending end
    }
    $organizer->amount = $organizer_new_amount;
    $organizer->save();
    return Response::json(['status' => 'success'], 200);
  }

  public function destroy($id)
  {
    $organizer = Organizer::find($id);

    $withdraws = $organizer->withdraws()->get();
    foreach ($withdraws as $withdraw) {
      $withdraw->delete();
    }

    $events = Event::where('organizer_id', $organizer->id)->get();
    foreach ($events as $event) {
      @unlink(public_path('assets/admin/img/event/thumbnail/') . $event->thumbnail);
      $event_contents = EventContent::where('event_id', $event->id)->get();
      foreach ($event_contents as $event_content) {
        $event_content->delete();
      }

      $event_images = EventImage::where('event_id', $event->id)->get();
      foreach ($event_images as $event_image) {
        @unlink(public_path('assets/admin/img/event-gallery/') . $event_image->image);
        $event_image->delete();
      }

      //bookings 
      $bookings = $event->booking()->get();
      foreach ($bookings as $booking) {
        // first, delete the attachment
        @unlink(public_path('assets/admin/file/attachments/') . $booking->attachment);

        // second, delete the invoice
        @unlink(storage_path('app/invoices/') . $booking->invoice);

        $booking->delete();
      }
      //tickets
      $tickets = $event->tickets()->get();
      foreach ($tickets as $ticket) {
        $ticket->delete();
      }

      // finally delete the event
      $event->delete();
    }

    $organizer->delete();

    return redirect()->back()->with('success', __('organizer.flash.deleted_successfully'));
  }

  public function bulkDestroy(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $organizer = Organizer::find($id);

      $withdraws = $organizer->withdraws()->get();
      foreach ($withdraws as $withdraw) {
        $withdraw->delete();
      }

      $events = Event::where('organizer_id', $organizer->id)->get();
      foreach ($events as $event) {
        @unlink(public_path('assets/admin/img/event/thumbnail/') . $event->thumbnail);
        $event_contents = EventContent::where('event_id', $event->id)->get();
        foreach ($event_contents as $event_content) {
          $event_content->delete();
        }

        $event_images = EventImage::where('event_id', $event->id)->get();
        foreach ($event_images as $event_image) {
          @unlink(public_path('assets/admin/img/event-gallery/') . $event_image->image);
          $event_image->delete();
        }

        //bookings 
        $bookings = $event->booking()->get();
        foreach ($bookings as $booking) {
          // first, delete the attachment
          @unlink(public_path('assets/admin/file/attachments/') . $booking->attachment);

          // second, delete the invoice
        @unlink(storage_path('app/invoices/') . $booking->invoice);

          $booking->delete();
        }
        //tickets
        $tickets = $event->tickets()->get();
        foreach ($tickets as $ticket) {
          $ticket->delete();
        }

        // finally delete the event
        $event->delete();
      }

      $organizer->delete();
    }

    Session::flash('success', __('organizer.flash.deleted_successfully'));

    return Response::json(['status' => 'success'], 200);
  }

  //secrtet login
  public function secret_login($id)
  {
    Session::put('secret_login', 1);
    $organizer = Organizer::where('id', $id)->first();
    Log::warning('Admin secret login as organizer', [
      'admin_id' => Auth::guard('admin')->id(),
      'organizer_id' => $id,
    ]);
    Auth::guard('organizer')->login($organizer);
    return redirect()->route('organizer.dashboard');
  }

  //update_organizer_balance
  public function send_mail_template()
  {
    //mail sending
    // get the website title & mail's smtp information from db
    $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'base_currency_symbol_position', 'base_currency_symbol')
      ->first();

    //preparing mail info


    // get the website title info from db
    $website_info = Basic::select('website_title')->first();


    $websiteTitle = $website_info->website_title;

    // replacing with actual data
    $view = View::make('backend.template-view.index');
    $mailData['subject'] = 'Test Mail Tempate Subject';
    $mailData['body'] = $view;

    $mailData['recipient'] = 'fahadahmadshemul@gmail.com';
    //preparing mail info end

    // initialize a new mail
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // if smtp status == 1, then set some value for PHPMailer
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

    // add other informations and send the mail
    try {
      $mail->setFrom($info->from_mail, $info->from_name);
      $mail->addAddress($mailData['recipient']);

      $mail->isHTML(true);
      $mail->Subject = $mailData['subject'];
      $mail->Body = $mailData['body'];

      $mail->send();
      return 'mail send';
    } catch (Exception $e) {
      return $e;
    }
    //mail sending end
  }
}
