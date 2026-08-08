@if (!empty($popupInfos) && count($popupInfos) > 0)
  @php
    $popupRgba = static function ($hex, $opacity) {
      $hex = ltrim((string) $hex, '#');
      if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
      }
      if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
        $hex = '1e2532';
      }
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
      $a = is_numeric($opacity) ? max(0, min(1, (float) $opacity)) : 0.85;

      return "rgba({$r}, {$g}, {$b}, {$a})";
    };
  @endphp

  @foreach ($popupInfos as $popupInfo)
    @php $type = (int) $popupInfo->type; @endphp

    @if ($type === 1)
      <div data-popup_delay="{{ $popupInfo->delay }}" data-popup_id="{{ $popupInfo->id }}" id="modal-popup-{{ $popupInfo->id }}" class="popup-wrapper mfp-hide" role="dialog" aria-modal="true" aria-label="{{ __('Anuncio') }}">
        <div>
          <img data-src="{{ asset('assets/admin/img/popups/' . $popupInfo->image) }}" class="lazy popup-image" alt="{{ $popupInfo->title ?: __('Anuncio') }}" width="960" height="540">
        </div>
      </div>
    @elseif ($type === 2)
      <div data-popup_delay="{{ $popupInfo->delay }}" data-popup_id="{{ $popupInfo->id }}" id="modal-popup-{{ $popupInfo->id }}" class="popup-wrapper mfp-hide" role="dialog" aria-modal="true" aria-labelledby="popup-title-{{ $popupInfo->id }}">
        <div class="popup-one bg_cover lazy" data-bg="{{ asset('assets/admin/img/popups/' . $popupInfo->image) }}">
          <div class="popup_main-content" style="background-color: {{ $popupRgba($popupInfo->background_color, $popupInfo->background_color_opacity) }};">
            <h2 id="popup-title-{{ $popupInfo->id }}">{{ $popupInfo->title }}</h2>
            <p>{{ $popupInfo->text }}</p>
            <a href="{{ $popupInfo->button_url }}" class="popup-main-btn" style="background-color: {{ '#' . ltrim((string) $popupInfo->button_color, '#') }};">{{ $popupInfo->button_text }}</a>
          </div>
        </div>
      </div>
    @elseif ($type === 3)
      <div data-popup_delay="{{ $popupInfo->delay }}" data-popup_id="{{ $popupInfo->id }}" id="modal-popup-{{ $popupInfo->id }}" class="popup-wrapper mfp-hide" role="dialog" aria-modal="true" aria-labelledby="popup-title-{{ $popupInfo->id }}">
        <div class="popup-two bg_cover lazy" data-bg="{{ asset('assets/admin/img/popups/' . $popupInfo->image) }}">
          <div class="popup_main-content" style="background-color: {{ $popupRgba($popupInfo->background_color, $popupInfo->background_color_opacity) }};">
            <h2 id="popup-title-{{ $popupInfo->id }}">{{ $popupInfo->title }}</h2>
            <p>{{ $popupInfo->text }}</p>

            <div class="subscribe-form">
              <form class="subscriptionForm" action="{{ route('store_subscriber') }}" method="POST">
                @csrf
                <div class="form_group">
                  <label class="sr-only" for="popup-email-{{ $popupInfo->id }}">{{ __('Correo electrónico') }}</label>
                  <input id="popup-email-{{ $popupInfo->id }}" type="email" class="form_control" placeholder="{{ __('Ingresá tu correo electrónico') }}" name="email_id" autocomplete="email" required>
                </div>

                <div class="form_group">
                  <button type="submit" class="popup-main-btn" style="background-color: {{ '#' . ltrim((string) $popupInfo->button_color, '#') }};">
                    {{ $popupInfo->button_text }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    @elseif ($type === 4)
      <div data-popup_delay="{{ $popupInfo->delay }}" data-popup_id="{{ $popupInfo->id }}" id="modal-popup-{{ $popupInfo->id }}" class="popup-wrapper mfp-hide" role="dialog" aria-modal="true" aria-labelledby="popup-title-{{ $popupInfo->id }}">
        <div class="popup-three">
          <div class="popup_main-content">
            <div class="left-bg bg_cover lazy" data-bg="{{ asset('assets/admin/img/popups/' . $popupInfo->image) }}" role="img" aria-label="{{ $popupInfo->title ?: __('Anuncio') }}"></div>
            <div class="right-content">
              <h2 id="popup-title-{{ $popupInfo->id }}">{{ $popupInfo->title }}</h2>
              <p>{{ $popupInfo->text }}</p>
              <a href="{{ $popupInfo->button_url }}" class="popup-main-btn" style="background-color: {{ '#' . ltrim((string) $popupInfo->button_color, '#') }};">{{ $popupInfo->button_text }}</a>
            </div>
          </div>
        </div>
      </div>
    @elseif ($type === 5)
      <div data-popup_delay="{{ $popupInfo->delay }}" data-popup_id="{{ $popupInfo->id }}" id="modal-popup-{{ $popupInfo->id }}" class="popup-wrapper mfp-hide" role="dialog" aria-modal="true" aria-labelledby="popup-title-{{ $popupInfo->id }}">
        <div class="popup-four">
          <div class="popup_main-content">
            <div class="left-bg bg_cover lazy" data-bg="{{ asset('assets/admin/img/popups/' . $popupInfo->image) }}" role="img" aria-label="{{ $popupInfo->title ?: __('Anuncio') }}"></div>
            <div class="right-content">
              <h2 id="popup-title-{{ $popupInfo->id }}">{{ $popupInfo->title }}</h2>
              <p>{{ $popupInfo->text }}</p>

              <div class="subscribe-form">
                <form class="subscriptionForm" action="{{ route('store_subscriber') }}" method="POST">
                  @csrf
                  <div class="form_group">
                    <label class="sr-only" for="popup-email-{{ $popupInfo->id }}">{{ __('Correo electrónico') }}</label>
                    <input id="popup-email-{{ $popupInfo->id }}" type="email" class="form_control" placeholder="{{ __('Ingresá tu correo electrónico') }}" name="email_id" autocomplete="email" required>
                  </div>

                  <div class="form_group">
                    <button type="submit" class="popup-main-btn" style="background-color: {{ '#' . ltrim((string) $popupInfo->button_color, '#') }};">
                      {{ $popupInfo->button_text }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    @elseif ($type === 6)
      <div data-popup_delay="{{ $popupInfo->delay }}" data-popup_id="{{ $popupInfo->id }}" id="modal-popup-{{ $popupInfo->id }}" class="popup-wrapper mfp-hide" role="dialog" aria-modal="true" aria-labelledby="popup-title-{{ $popupInfo->id }}">
        <div class="popup-five bg_cover lazy" data-bg="{{ asset('assets/admin/img/popups/' . $popupInfo->image) }}">
          <div class="popup_main-content">
            <h2 id="popup-title-{{ $popupInfo->id }}">{{ $popupInfo->title }}</h2>
            <h4>{{ $popupInfo->text }}</h4>

            <div class="offer-timer" data-end_date="{{ $popupInfo->end_date }}" data-end_time="{{ $popupInfo->end_time }}"></div>

            <a href="{{ $popupInfo->button_url }}" class="popup-main-btn" style="background-color: {{ '#' . ltrim((string) $popupInfo->button_color, '#') }};">{{ $popupInfo->button_text }}</a>
          </div>
        </div>
      </div>
    @else
      <div data-popup_delay="{{ $popupInfo->delay }}" data-popup_id="{{ $popupInfo->id }}" id="modal-popup-{{ $popupInfo->id }}" class="popup-wrapper mfp-hide" role="dialog" aria-modal="true" aria-labelledby="popup-title-{{ $popupInfo->id }}">
        <div class="popup-six">
          <div class="popup_main-content">
            <div class="left-bg bg_cover lazy" data-bg="{{ asset('assets/admin/img/popups/' . $popupInfo->image) }}" role="img" aria-label="{{ $popupInfo->title ?: __('Anuncio') }}"></div>

            <div class="right-content bg_cover" style="background-color: {{ '#' . ltrim((string) $popupInfo->background_color, '#') }}; background-image: url({{ asset('assets/admin/img/popups/right-bg.png') }});">
              <h2 id="popup-title-{{ $popupInfo->id }}">{{ $popupInfo->title }}</h2>
              <h4>{{ $popupInfo->text }}</h4>

              <div class="offer-timer" data-end_date="{{ $popupInfo->end_date }}" data-end_time="{{ $popupInfo->end_time }}"></div>

              <a href="{{ $popupInfo->button_url }}" class="popup-main-btn" style="background-color: {{ '#' . ltrim((string) $popupInfo->button_color, '#') }};">{{ $popupInfo->button_text }}</a>
            </div>
          </div>
        </div>
      </div>
    @endif
  @endforeach
@endif
