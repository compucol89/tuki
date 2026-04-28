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

    <div class="row justify-content-between">
      <div class="col-lg-5 col-sm-6">
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
                  @endphp
                  @if ($socialUrl !== '')
                    <a href="{{ $socialUrl === '#' ? 'javascript:void(0)' : $socialUrl }}" target="_blank" rel="noopener noreferrer" title="{{ $socialUrl }}">
                      <i class="{{ $socialMediaInfo->icon }}"></i>
                      <span class="sr-only">{{ $socialMediaInfo->title ?? 'Red social' }}</span>
                    </a>
                  @endif
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="footer-widget link-widget ml-sm-auto">
          <h5 class="footer-title">{{ __('Accesos rápidos') }}</h5>
          <ul>
            @foreach ($footerQuickLinks as $quickLink)
              <li><a href="{{ $quickLink['url'] }}">{{ $quickLink['title'] }}</a></li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="col-lg-2 col-sm-6">
        <div class="footer-widget link-widget">
          <h5 class="footer-title">{{ __('Legal') }}</h5>
          <ul>
            <li><a href="{{ url('/terminos-y-condiciones') }}">{{ __('Términos y condiciones') }}</a></li>
            <li><a href="{{ url('/politica-de-privacidad') }}">{{ __('Política de privacidad') }}</a></li>
            <li><a href="{{ url('/eliminacion-de-datos') }}">{{ __('Eliminación de datos') }}</a></li>
            <li><a href="{{ url('/politica-de-reembolsos') }}">{{ __('Política de reembolsos') }}</a></li>
            <li><a href="{{ url('/politica-de-cookies') }}">{{ __('Política de cookies') }}</a></li>
            <li><a href="{{ url('/defensa-al-consumidor') }}">{{ __('Defensa al consumidor') }}</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="footer-widget about-widget footer-contact ml-sm-auto">
          <h5 class="footer-title">{{ __('Contacto') }}</h5>
          @if (!is_null($bex) && (!empty($addresses) || !empty($mails) || !empty($phones)))
            <ul class="footer-contact-list">
              @if (!empty($addresses))
                <li class="footer-contact-item">
                  <span class="footer-contact-item__icon" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></span>
                  <div class="footer-contact-item__body">
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
                    @foreach ($phones as $phone)
                      <a href="tel:{{ $phone }}">{{ $phone }}</a>
                    @endforeach
                  </div>
                </li>
              @endif
            </ul>
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
      <div class="footer-section__legal">{!! !empty($footerInfo->copyright_text) ? $footer_text : '' !!}</div>
      <p class="footer-version">v{{ trim(file_get_contents(base_path('VERSION'))) }}</p>
      <button type="button" class="scroll-top scroll-to-target" data-target="html" aria-label="{{ __('Volver arriba') }}"><span class="fa fa-angle-up"></span></button>
    </div>
  </div>
</footer>
