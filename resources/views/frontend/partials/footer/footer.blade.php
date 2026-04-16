<footer class="footer-section bg-lighter pt-100">
  <div class="container">
    @php
      $addresses = !is_null($bex) ? array_values(array_filter(array_map('trim', explode(PHP_EOL, $bex->contact_addresses ?? '')))) : [];
      $mails = !is_null($bex) ? array_values(array_filter(array_map('trim', explode(',', $bex->contact_mails ?? '')))) : [];
      $phones = !is_null($bex) ? array_values(array_filter(array_map('trim', explode(',', $bex->contact_numbers ?? '')))) : [];
    @endphp

    <div class="row justify-content-between">
      <div class="col-lg-5 col-sm-6">
        <div class="footer-widget about-widget footer-brand">
          <div class="footer-logo mb-30">
            @if (!is_null($footerInfo))
              <a href="{{ route('index') }}"><img
                  src="{{ asset('assets/admin/img/footer_logo/' . $footerInfo->footer_logo) }}" alt="{{ config('app.name', 'Tukipass') }}"></a>
            @endif
          </div>
          <div class="footer-copy">{!! $footerInfo ? $footerInfo->about_company : '' !!}</div>
          <div class="footer-social">
            <p class="footer-social__label">{{ __('Seguinos') }}</p>
            <div class="social-style-one mt-30">
              @if ($socialMediaInfos->isNotEmpty())
                @foreach ($socialMediaInfos as $socialMediaInfo)
                  <a href="{{ $socialMediaInfo->url }}" target="_blank" rel="noopener noreferrer">
                    <i class="{{ $socialMediaInfo->icon }}"></i>
                    <span class="sr-only">{{ $socialMediaInfo->title ?? 'Social media' }}</span>
                  </a>
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="footer-widget link-widget ml-sm-auto">
          <h5 class="footer-title">{{ __('Quick Links') }}</h5>
          <ul>
            @foreach ($quickLinkInfos as $quickLinkInfo)
              <li><a href="{{ $quickLinkInfo->url }}">{{ $quickLinkInfo->title }}</a></li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="col-lg-4 col-sm-6">
        <div class="footer-widget about-widget footer-contact ml-sm-auto">
          <h5 class="footer-title">{{ __('Contact Us') }}</h5>
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

    <div class="copyright-area">
      @php
        $date = Date('Y');
        if (!empty($footerInfo->copyright_text)) {
            $footer_text = str_replace('{year}', $date, $footerInfo->copyright_text);
        }
      @endphp
      <p>{!! !empty($footerInfo->copyright_text) ? $footer_text : '' !!}</p>
      <p class="footer-version">v{{ trim(file_get_contents(base_path('VERSION'))) }}</p>
      <!-- Scroll Top Button -->
      <button class="scroll-top scroll-to-target" data-target="html" aria-label="{{ __('Scroll to top') }}"><span class="fa fa-angle-up"></span></button>
    </div>
  </div>
</footer>
