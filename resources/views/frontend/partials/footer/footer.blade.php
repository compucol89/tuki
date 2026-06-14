<footer class="footer-section footer-section--premium" aria-label="{{ __('Pie de página') }}">
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

    <div class="footer-layout">
      <section class="footer-layout__brand" aria-labelledby="footer-brand-title">
        <div class="footer-logo">
          <a href="{{ route('index') }}">
            <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ config('app.name', 'Tukipass') }}">
          </a>
        </div>
        <div class="footer-copy summernote-content">{!! $footerInfo ? $footerInfo->about_company : '' !!}</div>
        @if ($socialMediaInfos->isNotEmpty())
          <div class="footer-social">
            <p class="footer-social__label" id="footer-brand-title">{{ __('Seguinos') }}</p>
            <ul class="footer-social__list">
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
                  <li>
                    <a href="{{ $socialUrl === '#' ? 'javascript:void(0)' : $socialUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabel }}">
                      <i class="{{ $socialMediaInfo->icon }}" aria-hidden="true"></i>
                    </a>
                  </li>
                @endif
              @endforeach
            </ul>
          </div>
        @endif
      </section>

      <div class="footer-layout__nav">
        <nav class="footer-nav" aria-labelledby="footer-quick-links-title">
          <h2 class="footer-title" id="footer-quick-links-title">{{ __('Accesos rápidos') }}</h2>
          <ul class="footer-nav__list">
            @foreach ($footerQuickLinks as $quickLink)
              <li><a href="{{ $quickLink['url'] }}">{{ $quickLink['title'] }}</a></li>
            @endforeach
          </ul>
        </nav>

        <nav class="footer-nav" aria-labelledby="footer-legal-title">
          <h2 class="footer-title" id="footer-legal-title">{{ __('Legal') }}</h2>
          <ul class="footer-nav__list">
            <li><a href="{{ url('/terminos-y-condiciones') }}">{{ __('Términos y condiciones') }}</a></li>
            <li><a href="{{ url('/politica-de-privacidad') }}">{{ __('Política de privacidad') }}</a></li>
            <li><a href="{{ url('/eliminacion-de-datos') }}">{{ __('Eliminación de datos') }}</a></li>
            <li><a href="{{ url('/politica-de-reembolsos') }}">{{ __('Política de reembolsos') }}</a></li>
            <li><a href="{{ url('/politica-de-cookies') }}">{{ __('Política de cookies') }}</a></li>
            <li><a href="{{ url('/defensa-al-consumidor') }}">{{ __('Defensa al consumidor') }}</a></li>
          </ul>
        </nav>

        <section class="footer-nav footer-nav--contact" aria-labelledby="footer-contact-title">
          <h2 class="footer-title" id="footer-contact-title">{{ __('Contacto') }}</h2>
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
        </section>
      </div>
    </div>
  </div>

  <div class="footer-section__bottom">
    <div class="container footer-bar">
      @php
        $date = Date('Y');
        if (!empty($footerInfo->copyright_text)) {
            $footer_text = str_replace('{year}', $date, $footerInfo->copyright_text);
        }
      @endphp
      <div class="footer-bar__top">
        <div class="footer-section__legal">{!! !empty($footerInfo->copyright_text) ? $footer_text : '' !!}</div>
        <button type="button" class="scroll-top scroll-to-target" data-target="html" aria-label="{{ __('Volver arriba') }}">
          <span class="fa fa-angle-up" aria-hidden="true"></span>
        </button>
      </div>
      <div class="footer-bar__payments" aria-label="{{ __('Métodos de pago aceptados') }}">
        <p class="footer-bar__payments-label">
          <i class="fas fa-lock" aria-hidden="true"></i>
          <span>{{ __('Pagos seguros') }}</span>
        </p>
        <ul class="footer-payments">
          <li><span class="footer-pay footer-pay--visa">VISA</span></li>
          <li>
            <span class="footer-pay footer-pay--mc" role="img" aria-label="Mastercard">
              <span class="footer-pay__mc-dot footer-pay__mc-dot--l" aria-hidden="true"></span>
              <span class="footer-pay__mc-dot footer-pay__mc-dot--r" aria-hidden="true"></span>
            </span>
          </li>
          <li><span class="footer-pay footer-pay--amex">AMEX</span></li>
          <li>
            <span class="footer-pay footer-pay--mp">
              <img src="{{ asset('assets/front/images/mercadopago_logo.svg') }}" alt="Mercado Pago" width="56" height="34" loading="lazy" decoding="async">
            </span>
          </li>
          <li><span class="footer-pay footer-pay--cabal">Cabal</span></li>
          <li><span class="footer-pay footer-pay--naranja">Naranja</span></li>
        </ul>
      </div>
    </div>
  </div>
</footer>
