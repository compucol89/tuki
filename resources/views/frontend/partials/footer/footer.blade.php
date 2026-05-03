<footer class="footer-section footer-section--premium">
  <div class="footer-section__ambient" aria-hidden="true"></div>
  <div class="footer-section__grain" aria-hidden="true"></div>
  <div class="container footer-section__container">
    @php
      $addresses = !is_null($bex) ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $bex->contact_addresses ?? '')))) : [];
      $mails = !is_null($bex) ? array_values(array_filter(array_map('trim', explode(',', $bex->contact_mails ?? '')))) : [];
      $phones = !is_null($bex) ? array_values(array_filter(array_map('trim', explode(',', $bex->contact_numbers ?? '')))) : [];

      $resolveQuickLinkUrl = function ($quickLinkInfo) {
          $url = trim((string) ($quickLinkInfo->url ?? ''));
          $title = strtolower(trim((string) ($quickLinkInfo->title ?? '')));

          if ($url !== '' && $url !== '#') {
              return $url;
          }

          return match (true) {
              str_contains($title, 'evento') => route('events'),
              str_contains($title, 'blog') => route('blogs'),
              str_contains($title, 'contact') => route('contact'),
              str_contains($title, 'nosotro'), str_contains($title, 'about') => route('about'),
              str_contains($title, 'organiza') => route('frontend.all.organizer'),
              str_contains($title, 'faq'), str_contains($title, 'pregunta') => route('faqs'),
              default => null,
          };
      };

      $resolvedQuickLinks = collect($quickLinkInfos ?? [])
          ->map(function ($quickLinkInfo) use ($resolveQuickLinkUrl) {
              $url = $resolveQuickLinkUrl($quickLinkInfo);
              if (empty($url)) {
                  return null;
              }

              return [
                  'title' => $quickLinkInfo->title,
                  'url' => $url,
              ];
          })
          ->filter()
          ->values();

      $fallbackQuickLinks = collect([
          ['title' => 'Eventos', 'url' => route('events')],
          ['title' => 'Blog', 'url' => route('blogs')],
          ['title' => 'Preguntas frecuentes', 'url' => route('faqs')],
          ['title' => 'Ayuda y contacto', 'url' => route('contact')],
          ['title' => 'Nosotros', 'url' => route('about')],
          ['title' => 'Organizadores', 'url' => route('frontend.all.organizer')],
      ]);

      $footerQuickLinks = $resolvedQuickLinks
          ->concat($fallbackQuickLinks)
          ->unique(function ($item) {
              return strtolower(trim((string) ($item['url'] ?? '')));
          })
          ->values();
    @endphp

    <div class="row justify-content-between footer-main-grid">
      <div class="col-lg-4 col-sm-6 footer-col footer-col--brand">
        <div class="footer-widget about-widget footer-brand">
          <div class="footer-logo mb-30">
            <a href="{{ route('index') }}"><img
                src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ config('app.name', 'Tukipass') }}"></a>
          </div>
          <div class="footer-copy">{!! $footerInfo ? $footerInfo->about_company : '' !!}</div>
          <div class="footer-social">
            <p class="footer-social__label">{{ __('Seguinos') }}</p>
            <div class="social-style-one mt-30">
              @if ($socialMediaInfos->isNotEmpty())
                @foreach ($socialMediaInfos as $socialMediaInfo)
                  @php
                    $socialUrl = trim((string) ($socialMediaInfo->url ?? ''));
                    $socialLabel = $socialMediaInfo->title ?? match (true) {
                        str_contains($socialMediaInfo->icon ?? '', 'facebook') || str_contains($socialUrl, 'facebook') => 'Facebook',
                        str_contains($socialMediaInfo->icon ?? '', 'linkedin') || str_contains($socialUrl, 'linkedin') => 'LinkedIn',
                        str_contains($socialMediaInfo->icon ?? '', 'twitter') || str_contains($socialUrl, 'twitter') => 'Twitter',
                        str_contains($socialMediaInfo->icon ?? '', 'instagram') || str_contains($socialUrl, 'instagram') => 'Instagram',
                        default => 'Red social',
                    };
                  @endphp
                  @if ($socialUrl !== '')
                    <a href="{{ $socialUrl === '#' ? 'javascript:void(0)' : $socialUrl }}" target="_blank" rel="noopener noreferrer" title="{{ $socialLabel }}" aria-label="{{ $socialLabel }}">
                      <i class="{{ $socialMediaInfo->icon }}" aria-hidden="true"></i>
                      <span class="sr-only">{{ $socialLabel }}</span>
                    </a>
                  @endif
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 footer-col footer-col--quick">
        <nav class="footer-widget link-widget ml-sm-auto" aria-labelledby="footer-quick-links-title">
          <h5 class="footer-title" id="footer-quick-links-title">{{ __('Accesos rápidos') }}</h5>
          <ul>
            @foreach ($footerQuickLinks as $quickLink)
              <li><a href="{{ $quickLink['url'] }}">{{ $quickLink['title'] }}</a></li>
            @endforeach
          </ul>
        </nav>
      </div>
      <div class="col-lg-2 col-sm-6 footer-col footer-col--legal">
        <nav class="footer-widget link-widget" aria-labelledby="footer-legal-title">
          <h5 class="footer-title" id="footer-legal-title">{{ __('Legal') }}</h5>
          <ul>
            <li><a href="{{ url('/terminos-y-condiciones') }}">{{ __('Términos y condiciones') }}</a></li>
            <li><a href="{{ url('/politica-de-privacidad') }}">{{ __('Política de privacidad') }}</a></li>
            <li><a href="{{ url('/eliminacion-de-datos') }}">{{ __('Eliminación de datos') }}</a></li>
            <li><a href="{{ url('/politica-de-reembolsos') }}">{{ __('Política de reembolsos') }}</a></li>
            <li><a href="{{ url('/politica-de-cookies') }}">{{ __('Política de cookies') }}</a></li>
            <li><a href="{{ url('/defensa-al-consumidor') }}">{{ __('Defensa al consumidor') }}</a></li>
          </ul>
        </nav>
      </div>
      <div class="col-lg-3 col-sm-6 footer-col footer-col--contact">
        <div class="footer-widget about-widget footer-contact ml-sm-auto" aria-labelledby="footer-contact-title">
          <h5 class="footer-title" id="footer-contact-title">{{ __('Contacto') }}</h5>
          @if (!is_null($bex) && (!empty($addresses) || !empty($mails) || !empty($phones)))
            <ul class="footer-contact-list">
              @if (!empty($addresses))
                <li class="footer-contact-item">
                  <span class="footer-contact-item__icon" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></span>
                  <div class="footer-contact-item__body">
                    <span class="sr-only">{{ __('Dirección') }}</span>
                    @foreach ($addresses as $address)
                      <span>{{ $address }}</span>
                    @endforeach
                  </div>
                </li>
              @endif

              @if (!empty($mails))
                <li class="footer-contact-item">
                  <span class="footer-contact-item__icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                  <div class="footer-contact-item__body">
                    <span class="sr-only">{{ __('Email') }}</span>
                    @foreach ($mails as $mail)
                      <a href="mailto:{{ $mail }}" class="text-transform-normal">{{ $mail }}</a>
                    @endforeach
                  </div>
                </li>
              @endif

              @if (!empty($phones))
                <li class="footer-contact-item">
                  <span class="footer-contact-item__icon" aria-hidden="true"><i class="fas fa-mobile-alt"></i></span>
                  <div class="footer-contact-item__body">
                    <span class="sr-only">{{ __('Teléfono') }}</span>
                    @foreach ($phones as $phone)
                      <a href="tel:{{ $phone }}">{{ $phone }}</a>
                    @endforeach
                  </div>
                </li>
              @endif
            </ul>
          @else
            <p class="footer-contact-fallback">
              <a href="{{ route('contact') }}">{{ __('Escribinos o consultá por tu compra') }}</a>
            </p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="footer-section__bottom">
    <div class="container">
      @php
        $date = Date('Y');
        if (!empty($footerInfo->copyright_text)) {
            $footer_text = str_replace('{year}', $date, $footerInfo->copyright_text);
        }
      @endphp
      <div class="footer-bottom__group footer-bottom__group--legal">
        <div class="footer-section__legal">{!! !empty($footerInfo->copyright_text) ? $footer_text : '' !!}</div>
      </div>
      <div class="footer-bottom__group footer-bottom__group--trust" aria-label="{{ __('Métodos de pago aceptados') }}">
        <span class="footer-trust__label">
          <i class="fas fa-lock" aria-hidden="true"></i> {{ __('Pagos seguros') }}
        </span>
        <div class="footer-trust__icons co-trust-logos footer-trust-logos--mono" aria-hidden="true">
          <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30" focusable="false" class="footer-trust-logo-svg">
            <rect width="56" height="36" rx="5" fill="none" stroke="rgba(255,255,255,0.42)" stroke-width="1"/>
            <text x="28" y="24" font-family="Arial,sans-serif" font-size="16" font-weight="900" font-style="italic" fill="rgba(255,255,255,0.9)" text-anchor="middle" letter-spacing="1">VISA</text>
          </svg>
          <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30" focusable="false" class="footer-trust-logo-svg">
            <rect width="56" height="36" rx="5" fill="none" stroke="rgba(255,255,255,0.42)" stroke-width="1"/>
            <circle cx="22" cy="18" r="8" fill="none" stroke="rgba(255,255,255,0.78)" stroke-width="1.2"/>
            <circle cx="34" cy="18" r="8" fill="none" stroke="rgba(255,255,255,0.78)" stroke-width="1.2"/>
          </svg>
          <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30" focusable="false" class="footer-trust-logo-svg">
            <rect width="56" height="36" rx="5" fill="none" stroke="rgba(255,255,255,0.42)" stroke-width="1"/>
            <text x="28" y="23" font-family="Arial,sans-serif" font-size="11" font-weight="700" fill="rgba(255,255,255,0.9)" text-anchor="middle" letter-spacing="1">AMEX</text>
          </svg>
          <img class="footer-trust-logo-mp" src="{{ asset('assets/front/images/mercadopago_logo.svg') }}" alt="" width="46" height="30" loading="lazy" decoding="async">
          <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30" focusable="false" class="footer-trust-logo-svg">
            <rect width="56" height="36" rx="5" fill="none" stroke="rgba(255,255,255,0.42)" stroke-width="1"/>
            <text x="28" y="23" font-family="Arial,sans-serif" font-size="11" font-weight="700" fill="rgba(255,255,255,0.9)" text-anchor="middle">Cabal</text>
          </svg>
          <svg viewBox="0 0 56 36" xmlns="http://www.w3.org/2000/svg" width="46" height="30" focusable="false" class="footer-trust-logo-svg">
            <rect width="56" height="36" rx="5" fill="none" stroke="rgba(255,255,255,0.42)" stroke-width="1"/>
            <text x="28" y="23" font-family="Arial,sans-serif" font-size="9" font-weight="700" fill="rgba(255,255,255,0.9)" text-anchor="middle">Naranja</text>
          </svg>
        </div>
      </div>
      <div class="footer-bottom__group footer-bottom__group--meta">
        <p class="footer-version">v{{ trim(file_get_contents(base_path('VERSION'))) }}</p>
        <button type="button" class="scroll-top scroll-to-target" data-target="html" aria-label="{{ __('Volver arriba') }}"><span class="fa fa-angle-up"></span></button>
      </div>
    </div>
  </div>
</footer>
