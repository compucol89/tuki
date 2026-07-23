@extends('frontend.layout')

@php
  use Illuminate\Support\Str;

  $frontCssAsset = static function (string $path): string {
    $fullPath = public_path($path);

    return asset($path) . (is_file($fullPath) ? '?v=' . filemtime($fullPath) : '');
  };

  $eventsCssPath = app()->environment('production') ? 'assets/front/css/events.min.css' : 'assets/front/css/events.css';
  $homeCssPath = app()->environment('production') ? 'assets/front/css/home.min.css' : 'assets/front/css/home.css';
  $organizerCssPath = app()->environment('production') ? 'assets/front/css/organizer.min.css' : 'assets/front/css/organizer.css';
  $profileName = trim((string) ($publicOrganizerName ?? $organizer->username ?? config('app.name', 'Tukipass')));
  $profileHandle = trim((string) ($organizer->username ?? $profileName));
  $profileTagline = trim((string) ($organizer_info->designation ?? ''));
  $profileDescription = trim((string) ($publicOrganizerDescription ?? ''));
  $profileFallbackDescription = __('Conocé los eventos publicados por :name en Tukipass, sus próximas fechas, redes oficiales y datos de contacto.', ['name' => $profileName]);
  $profileDescriptionText = $profileDescription !== '' ? $profileDescription : $profileFallbackDescription;
  $profileUrl = $publicOrganizerUrl ?? url()->current();
  $profileCreatedAt = !empty($organizer->created_at) ? \Carbon\Carbon::parse($organizer->created_at)->toIso8601String() : null;
  $profileUpdatedAt = !empty($organizer->updated_at) ? \Carbon\Carbon::parse($organizer->updated_at)->toIso8601String() : $profileCreatedAt;
  $memberSince = !empty($organizer->created_at) ? \Carbon\Carbon::parse($organizer->created_at)->locale('es')->translatedFormat('F Y') : null;
  $upcomingCount = $upcomingEvents->count();
  $pastCount = $pastEvents->count();
  $totalCount = $upcomingCount + $pastCount;
  $locationParts = array_filter([
    trim((string) ($organizer_info->city ?? '')),
    trim((string) ($organizer_info->country ?? '')),
  ]);
  $profileLocation = implode(', ', $locationParts);
  $photoUrl = null;
  $schemaPhotoUrl = null;
  if ($admin == true && !empty($organizer->image)) {
    $photoUrl = asset('assets/admin/img/admins/' . $organizer->image);
    $schemaPhotoUrl = $photoUrl;
  } elseif ($admin != true && !empty($organizer->photo)) {
    $photoUrl = asset('assets/admin/img/organizer-photo/' . $organizer->photo);
    $schemaPhotoUrl = $photoUrl;
  } else {
    $photoUrl = asset('assets/front/images/user.png');
  }
  $coverUrl = !empty($organizer->cover_photo)
    ? asset('assets/admin/img/organizer-cover-photo/' . $organizer->cover_photo)
    : asset('assets/admin/img/' . $basicInfo->breadcrumb);
  $eventPlaceholderUrl = asset('assets/admin/img/noimage.jpg');
  $ogImage = asset('assets/front/img/og/tukipass-og.jpg');
  $profileMetaDescription = Str::limit($profileDescriptionText, 155, '');
  $rawSocialLinks = [
    ['key' => 'website', 'url' => $organizer->website ?? null, 'label' => __('Sitio web'), 'icon' => 'fas fa-globe'],
    ['key' => 'instagram', 'url' => $organizer->instagram ?? null, 'label' => 'Instagram', 'icon' => 'fab fa-instagram'],
    ['key' => 'tiktok', 'url' => $organizer->tiktok ?? null, 'label' => 'TikTok', 'icon' => 'fab fa-tiktok'],
    ['key' => 'facebook', 'url' => $organizer->facebook ?? null, 'label' => 'Facebook', 'icon' => 'fab fa-facebook-f'],
    ['key' => 'twitter', 'url' => $organizer->twitter ?? null, 'label' => 'X / Twitter', 'icon' => 'fab fa-twitter'],
    ['key' => 'linkedin', 'url' => $organizer->linkedin ?? null, 'label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in'],
  ];
  $socialLinks = collect($rawSocialLinks)
    ->map(function ($item) {
      $url = trim((string) ($item['url'] ?? ''));
      $item['url'] = filter_var($url, FILTER_VALIDATE_URL) ? $url : null;

      return $item;
    })
    ->filter(fn ($item) => !empty($item['url']))
    ->values();
  $sameAs = $socialLinks->pluck('url')->values()->all();
  $profilePixelId = trim((string) ($organizer->meta_pixel_id ?? ''));
  $profilePixelId = preg_match('/^\d{6,32}$/', $profilePixelId) ? $profilePixelId : '';
  $profilePixelPageViewEventId = $profilePixelId !== '' ? 'organizer-page-view-' . (int) $organizer->id . '-' . Str::uuid() : '';
  $profilePixelViewContentEventId = $profilePixelId !== '' ? 'organizer-view-content-' . (int) $organizer->id . '-' . Str::uuid() : '';
  $profilePixelPageViewUrl = $profilePixelId !== '' ? 'https://www.facebook.com/tr?' . http_build_query([
    'id' => $profilePixelId,
    'ev' => 'PageView',
    'noscript' => 1,
    'dl' => $profileUrl,
    'eid' => $profilePixelPageViewEventId,
  ]) : '';
  $addressSchema = !empty($organizer_info) ? array_filter([
    '@type' => 'PostalAddress',
    'streetAddress' => $organizer_info->address ?? null,
    'addressLocality' => $organizer_info->city ?? null,
    'addressRegion' => $organizer_info->state ?? null,
    'postalCode' => $organizer_info->zip_code ?? null,
    'addressCountry' => $organizer_info->country ?? null,
  ]) : [];
  $addressSchema = count($addressSchema) > 1 ? $addressSchema : null;
  $profileSchemaEntity = array_filter([
    '@type' => 'Organization',
    '@id' => $profileUrl . '#organizer',
    'name' => $profileName,
    'alternateName' => $profileHandle !== $profileName ? $profileHandle : null,
    'identifier' => (string) $organizer->id,
    'description' => $profileDescriptionText,
    'url' => $profileUrl,
    'image' => $schemaPhotoUrl,
    'sameAs' => $sameAs ?: null,
    'email' => filter_var($organizer->email ?? null, FILTER_VALIDATE_EMAIL) ? $organizer->email : null,
    'telephone' => !empty($organizer->phone) ? $organizer->phone : null,
    'address' => $addressSchema,
    'agentInteractionStatistic' => [
      '@type' => 'InteractionCounter',
      'interactionType' => 'https://schema.org/WriteAction',
      'userInteractionCount' => $totalCount,
    ],
  ], fn ($value) => $value !== null && $value !== '' && $value !== []);
  $profilePageSchema = array_filter([
    '@type' => 'ProfilePage',
    '@id' => $profileUrl . '#profile',
    'url' => $profileUrl,
    'name' => $profileName . ' | Organizador en Tukipass',
    'description' => $profileDescriptionText,
    'dateCreated' => $profileCreatedAt,
    'dateModified' => $profileUpdatedAt,
    'mainEntity' => [
      '@id' => $profileUrl . '#organizer',
    ],
  ], fn ($value) => $value !== null && $value !== '' && $value !== []);
  $eventListItems = collect($events ?? [])
    ->take(12)
    ->values()
    ->map(function ($event, $index) {
      return array_filter([
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $event->title ?? null,
        'url' => !empty($event->slug) && !empty($event->id) ? route('event.details', [$event->slug, $event->id], true) : null,
      ], fn ($value) => $value !== null && $value !== '');
    })
    ->filter(fn ($item) => !empty($item['name']) && !empty($item['url']))
    ->values()
    ->all();
  $eventItemListSchema = !empty($eventListItems) ? [
    '@type' => 'ItemList',
    '@id' => $profileUrl . '#events',
    'name' => __('Eventos de :name', ['name' => $profileName]),
    'itemListOrder' => 'https://schema.org/ItemListOrderAscending',
    'numberOfItems' => count($eventListItems),
    'itemListElement' => $eventListItems,
  ] : null;
  $breadcrumbSchema = [
    '@type' => 'BreadcrumbList',
    '@id' => $profileUrl . '#breadcrumb',
    'itemListElement' => [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => __('Inicio'),
        'item' => url('/'),
      ],
      [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => __('Organizadores'),
        'item' => route('frontend.all.organizer'),
      ],
      [
        '@type' => 'ListItem',
        'position' => 3,
        'name' => $profileName,
        'item' => $profileUrl,
      ],
    ],
  ];
  $profileGraphSchema = [
    '@context' => 'https://schema.org',
    '@graph' => array_values(array_filter([
      $profilePageSchema,
      $profileSchemaEntity,
      $eventItemListSchema,
      $breadcrumbSchema,
    ])),
  ];
@endphp

@section('body-class', 'organizer-details-page')
@section('pageHeading', $profileName . ' | Organizador en Tukipass')
@section('meta-keywords', $profileName . ', eventos, organizador, entradas, Tukipass')
@section('meta-description', $profileMetaDescription)
@section('og-title', $profileName . ' | Organizador en Tukipass')
@section('og-description', $profileMetaDescription)
@section('og-image', $ogImage)
@section('og-type', 'website')
@section('canonical', $profileUrl)
@section('og-url', $profileUrl)

@push('styles')
  <link rel="stylesheet" href="{{ $frontCssAsset($eventsCssPath) }}">
  <link rel="stylesheet" href="{{ $frontCssAsset($homeCssPath) }}">
  <link rel="stylesheet" href="{{ $frontCssAsset($organizerCssPath) }}">
@endpush

@push('schema')
<script type="application/ld+json">{!! json_encode($profileGraphSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@if($profilePixelId !== '')
  @push('head-scripts')
<!-- Meta Pixel Code: organizer profile -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ $profilePixelId }}');
fbq('trackSingle', '{{ $profilePixelId }}', 'PageView', {}, {eventID: {!! json_encode($profilePixelPageViewEventId, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}});
fbq('trackSingle', '{{ $profilePixelId }}', 'ViewContent', {
  content_ids: [{!! json_encode('organizer_' . (int) $organizer->id, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}],
  content_name: {!! json_encode($profileName, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!},
  content_category: 'organizer_profile'
}, {eventID: {!! json_encode($profilePixelViewContentEventId, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}});
</script>
<!-- End Meta Pixel Code -->
  @endpush
@endif

@if($profilePixelId !== '')
  @push('scripts')
<script>
(function () {
  if (typeof window.fbq !== 'function') return;

  document.querySelectorAll('[data-org-contact-pixel]').forEach(function (contactTrigger) {
    contactTrigger.addEventListener('click', function () {
      var eventId = 'organizer-contact-' + {{ (int) $organizer->id }} + '-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);
      window.fbq('trackSingle', '{{ $profilePixelId }}', 'Contact', {
        content_name: {!! json_encode($profileName, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!},
        content_category: 'organizer_profile'
      }, {eventID: eventId});
    });
  });
})();
</script>
  @endpush
@endif

@push('scripts')
<script>
(function () {
  function copyText(value) {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(value);
    }

    var input = document.createElement('textarea');
    input.value = value;
    input.setAttribute('readonly', 'readonly');
    input.style.position = 'fixed';
    input.style.left = '-9999px';
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);

    return Promise.resolve();
  }

  document.querySelectorAll('[data-org-share-profile]').forEach(function (shareButton) {
    shareButton.addEventListener('click', function () {
      var payload = {
        title: shareButton.getAttribute('data-share-title') || document.title,
        text: shareButton.getAttribute('data-share-text') || '',
        url: shareButton.getAttribute('data-share-url') || window.location.href
      };

      if (navigator.share) {
        navigator.share(payload).catch(function () {});
        return;
      }

      copyText(payload.url);
    });
  });

  document.querySelectorAll('[data-org-copy-profile]').forEach(function (copyButton) {
    copyButton.addEventListener('click', function () {
      var label = copyButton.querySelector('[data-copy-label]');
      var original = copyButton.getAttribute('data-copy-default') || copyButton.textContent;
      var done = copyButton.getAttribute('data-copy-done') || original;

      copyText(copyButton.getAttribute('data-copy-url') || window.location.href).then(function () {
        if (!label) return;
        label.textContent = done;
        window.setTimeout(function () {
          label.textContent = original;
        }, 1800);
      });
    });
  });
})();
</script>
@endpush

@section('content')
<main id="main-content" class="org-public-profile" tabindex="-1">
  @if($profilePixelPageViewUrl !== '')
    <noscript class="org-profile-pixel-noscript"><img height="1" width="1" alt="" style="display:none" src="{{ $profilePixelPageViewUrl }}"></noscript>
  @endif

  <section class="org-profile-head" aria-labelledby="organizer-profile-title">
    <div class="container">
      <article class="org-profile-panel">
        <div class="org-profile-panel__cover" style="--org-cover-image: url('{{ $coverUrl }}');"></div>

        <div class="org-profile-panel__body">
          <div class="org-profile-panel__main">
            <figure class="org-profile-panel__avatar">
              <img src="{{ $photoUrl }}" alt="{{ __('Foto de perfil de :name', ['name' => $profileName]) }}" width="144" height="144">
            </figure>

            <div class="org-profile-panel__identity">
              <p class="org-profile-panel__eyebrow">{{ __('Organizador en Tukipass') }}</p>
              <h1 id="organizer-profile-title">{{ $profileName }}</h1>
              @if($profileTagline !== '')
                <p class="org-profile-panel__tagline">{{ $profileTagline }}</p>
              @endif
              <p class="org-profile-panel__bio">{{ $profileDescriptionText }}</p>

              <div class="org-profile-panel__meta" aria-label="{{ __('Datos del organizador') }}">
                @if($memberSince)
                  <span>{{ __('Miembro desde') }} {{ $memberSince }}</span>
                @endif
                @if($profileLocation !== '')
                  <span>{{ $profileLocation }}</span>
                @endif
                <span>{{ trans_choice('{0} Agenda en preparación|{1} :count evento activo|[2,*] :count eventos activos', $upcomingCount, ['count' => $upcomingCount]) }}</span>
              </div>
            </div>
          </div>

          <aside class="org-profile-panel__side" aria-label="{{ __('Resumen del organizador') }}">
            <dl class="org-profile-stats">
              <div>
                <dt>{{ __('Próximos') }}</dt>
                <dd>{{ $upcomingCount }}</dd>
              </div>
              <div>
                <dt>{{ __('Realizados') }}</dt>
                <dd>{{ $pastCount }}</dd>
              </div>
              <div>
                <dt>{{ __('Total') }}</dt>
                <dd>{{ $totalCount }}</dd>
              </div>
            </dl>

            @if($socialLinks->isNotEmpty())
              <nav class="org-profile-social" aria-label="{{ __('Redes y sitio oficial') }}">
                @foreach($socialLinks as $link)
                  <a href="{{ $link['url'] }}" target="_blank" rel="me noopener noreferrer" aria-label="{{ $link['label'] }}">
                    <i class="{{ $link['icon'] }}" aria-hidden="true"></i>
                  </a>
                @endforeach
              </nav>
            @endif

            <div class="org-profile-actions" aria-label="{{ __('Acciones del perfil') }}">
              <button type="button" class="org-profile-action" data-org-share-profile
                data-share-title="{{ $profileName . ' | Organizador en Tukipass' }}"
                data-share-text="{{ __('Conocé el perfil y los eventos de :name en Tukipass.', ['name' => $profileName]) }}"
                data-share-url="{{ $profileUrl }}">
                <i class="fas fa-share-alt" aria-hidden="true"></i>
                <span>{{ __('Compartir') }}</span>
              </button>
              <button type="button" class="org-profile-action" data-org-copy-profile
                data-copy-url="{{ $profileUrl }}"
                data-copy-default="{{ __('Copiar link') }}"
                data-copy-done="{{ __('Link copiado') }}">
                <i class="fas fa-link" aria-hidden="true"></i>
                <span data-copy-label>{{ __('Copiar link') }}</span>
              </button>
            </div>

            <div class="org-profile-contact">
              @if(!empty($organizer->email))
                <a href="mailto:{{ $organizer->email }}"><i class="fas fa-envelope" aria-hidden="true"></i>{{ $organizer->email }}</a>
              @endif
              @if(!empty($organizer->phone))
                <a href="tel:{{ $organizer->phone }}"><i class="fas fa-phone-alt" aria-hidden="true"></i>{{ $organizer->phone }}</a>
              @endif
            </div>

            <button type="button" class="org-profile-contact__btn" data-toggle="modal" data-target="#contactModal" data-org-contact-pixel>
              {{ __('Contactar organizador') }}
            </button>
          </aside>
        </div>
      </article>
    </div>
  </section>

  <section class="org-timeline org-timeline--upcoming events-section {{ $upcomingCount === 1 ? 'org-timeline--single-upcoming' : '' }}" aria-labelledby="org-upcoming-title">
    <div class="container">
      <header class="org-timeline__header">
        <div>
          <p class="org-section-kicker">{{ __('Eventos publicados') }}</p>
          <h2 id="org-upcoming-title">{{ $upcomingCount === 1 ? __('Evento activo') : __('Próximos eventos') }}</h2>
        </div>
        <span>{{ trans_choice('{0} Agenda en preparación|{1} :count fecha activa|[2,*] :count fechas activas', $upcomingCount, ['count' => $upcomingCount]) }}</span>
      </header>

      @if($upcomingEvents->count() > 0)
        @if($upcomingCount === 1)
          <div class="org-active-agenda">
            <div class="org-active-agenda__event" aria-label="{{ __('Evento activo') }}">
              @foreach($upcomingEvents as $event)
                <div class="ev-card-col">
                  @include('frontend.partials.event-card', ['event' => $event, 'eventImageFallbackUrl' => $coverUrl])
                </div>
              @endforeach
            </div>

            <aside class="org-active-agenda__context" aria-label="{{ __('Resumen de agenda') }}">
              <p class="org-active-agenda__eyebrow">{{ __('Agenda actual') }}</p>
              <h3>{{ __('Una fecha activa disponible') }}</h3>
              <p>{{ __('Perfil oficial de :name: próximas fechas, historial y canales de contacto en Tukipass.', ['name' => $profileName]) }}</p>

              <dl class="org-active-agenda__facts">
                <div>
                  <dt>{{ __('Historial') }}</dt>
                  <dd>{{ trans_choice('{0} Sin eventos realizados|{1} :count evento realizado|[2,*] :count eventos realizados', $pastCount, ['count' => $pastCount]) }}</dd>
                </div>
                @if($profileLocation !== '')
                  <div>
                    <dt>{{ __('Base') }}</dt>
                    <dd>{{ $profileLocation }}</dd>
                  </div>
                @endif
              </dl>

              <div class="org-active-agenda__actions">
                <button type="button" class="org-active-agenda__primary" data-toggle="modal" data-target="#contactModal" data-org-contact-pixel>
                  {{ __('Contactar organizador') }}
                </button>
                <button type="button" class="org-active-agenda__secondary" data-org-share-profile
                  data-share-title="{{ $profileName . ' | Organizador en Tukipass' }}"
                  data-share-text="{{ __('Conocé el perfil y los eventos de :name en Tukipass.', ['name' => $profileName]) }}"
                  data-share-url="{{ $profileUrl }}">
                  <i class="fas fa-share-alt" aria-hidden="true"></i>
                  <span>{{ __('Compartir perfil') }}</span>
                </button>
              </div>
            </aside>
          </div>
        @else
          <div class="row org-timeline__grid">
            @foreach($upcomingEvents as $event)
              <div class="col-lg-4 col-md-6 ev-card-col">
                @include('frontend.partials.event-card', ['event' => $event, 'eventImageFallbackUrl' => $coverUrl])
              </div>
            @endforeach
          </div>
        @endif
      @else
        <div class="org-empty-state" role="status">
          <p>{{ __('Este organizador todavía no tiene eventos próximos publicados.') }}</p>
        </div>
      @endif
    </div>
  </section>

  <section class="org-timeline org-timeline--past" aria-labelledby="org-past-title">
    <div class="container">
      <header class="org-timeline__header">
        <div>
          <p class="org-section-kicker">{{ __('Historial') }}</p>
          <h2 id="org-past-title">{{ __('Eventos realizados') }}</h2>
        </div>
        <span>{{ trans_choice('{0} Sin historial visible|{1} :count evento pasado|[2,*] :count eventos pasados', $pastCount, ['count' => $pastCount]) }}</span>
      </header>

      @if($pastEvents->count() > 0)
        <div class="org-archive-grid">
          @foreach($pastEvents as $event)
            @php
              $pastEventDateSource = $event->start_date ?? substr((string) ($event->end_date_time ?? ''), 0, 10);
              $pastEventTimeSource = $event->start_time ?? '00:00:00';
              $pastEventDate = \Carbon\Carbon::parse($pastEventDateSource, $websiteInfo->timezone ?? config('app.timezone', 'UTC'))->locale('es');
              $pastEventTime = \Carbon\Carbon::parse(trim($pastEventDateSource . ' ' . $pastEventTimeSource), $websiteInfo->timezone ?? config('app.timezone', 'UTC'));
              $pastEventImage = \App\Services\FileUploadService::eventVisualUrl(null, $event->thumbnail ?? null);
              $pastEventImage = $pastEventImage === $eventPlaceholderUrl ? $coverUrl : $pastEventImage;
              $pastEventLocation = ($event->event_type ?? '') === 'venue'
                ? trim(($event->city ?? '') . (($event->city ?? null) && ($event->country ?? null) ? ', ' : '') . ($event->country ?? ''))
                : __('Online');
              $pastEventLocation = $pastEventLocation !== '' ? $pastEventLocation : __('Presencial');
            @endphp

            <article class="org-archive-card">
              <a class="org-archive-card__image" href="{{ route('event.details', [$event->slug, $event->id]) }}" aria-label="{{ __('Ver evento :title', ['title' => $event->title]) }}">
                <img class="lazy" data-src="{{ $pastEventImage }}"
                  src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                  alt="{{ $event->title }}" loading="lazy" width="96" height="96">
              </a>

              <div class="org-archive-card__body">
                <div class="org-archive-card__meta">
                  <span>{{ ucfirst($pastEventDate->translatedFormat('d M Y')) }}</span>
                  <span>{{ $pastEventTime->format('H:i') }} hrs</span>
                </div>
                <h3>
                  <a href="{{ route('event.details', [$event->slug, $event->id]) }}">{{ $event->title }}</a>
                </h3>
                <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i>{{ $pastEventLocation }}</p>
              </div>
            </article>
          @endforeach
        </div>
      @else
        <div class="org-empty-state" role="status">
          <p>{{ __('Cuando el organizador complete eventos, van a aparecer en este historial.') }}</p>
        </div>
      @endif
    </div>
  </section>

  @if (!empty(showAd(3)))
    <div class="container">
      <div class="text-center mt-4 org-events-showcase__ad">
        {!! showAd(3) !!}
      </div>
    </div>
  @endif

  <div class="contact-modal modal fade" id="contactModal" tabindex="-1" role="dialog"
    aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="contactModalLabel">{{ __('Contactar organizador') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Cerrar') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="contact-wrapper">
            <div class="contact-form m-0">
              <form action="{{ route('organizer.contact.send_mail') }}" method="POST" id="vendorContactForm">
                @csrf
                <input type="hidden" name="id" value="{{ $organizer->id }}">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form_group mb-20">
                      <input type="text" class="form_control" placeholder="{{ __('Tu nombre completo') }}" name="name">
                      <p class="text-danger em mt_1" id="Error_name"></p>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form_group mb-20">
                      <input type="email" class="form_control" placeholder="{{ __('Tu email') }}" name="email">
                      <p class="text-danger em mt_1" id="Error_email"></p>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form_group mb-20">
                      <input type="text" class="form_control" placeholder="{{ __('Asunto') }}" name="subject">
                      <p class="text-danger em mt_1" id="Error_subject"></p>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form_group mb-20">
                      <textarea name="message" class="form_control" placeholder="{{ __('Escribí tu mensaje') }}"></textarea>
                      <p class="text-danger em mt_1" id="Error_message"></p>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    @if ($basicInfos->google_recaptcha_status == 1)
                      <div class="form_group">
                        {!! NoCaptcha::renderJs() !!}
                        {!! NoCaptcha::display() !!}

                        <p class="text-danger em" id="Error_g-recaptcha-response"></p>
                      </div>
                    @endif
                  </div>
                  <div class="col-lg-12 text-center">
                    <button class="theme-btn" type="submit" title="{{ __('Enviar') }}">{{ __('Enviar') }}</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
