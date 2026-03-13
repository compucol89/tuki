@extends('frontend.layout')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->event_page_title ?? __('Events') }}
  @else
    {{ __('Events') }}
  @endif
@endsection

@php
  $metaKeywords = !empty($seo->meta_keyword_event) ? $seo->meta_keyword_event : '';
  $metaDescription = !empty($seo->meta_description_event) ? $seo->meta_description_event : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">
          @if (!empty($pageHeading))
            {{ $pageHeading->event_page_title ?? __('Events') }}
          @else
            {{ __('Events') }}
          @endif
        </h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">
              @if (!empty($pageHeading))
                {{ $pageHeading->event_page_title ?? __('Events') }}
              @else
                {{ __('Events') }}
              @endif
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection
@section('content')
  <!-- Event Page Start -->
  <section class="event-page-section py-120 rpy-100">
    <div class="container container-custom">
      <div class="row">
        <div class="col-lg-3">
          <div class="sidebar rmb-75">
            <div class="widget widget-search">
              <form action="{{ route('events') }}">

                <input type="text" name="search-input"
                  value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}"
                  placeholder="{{ __('Search') }}.....">
                @if (request()->filled('category'))
                  <input type="hidden" id="category-id" name="category"
                    value="{{ !empty(request()->input('category')) ? request()->input('category') : '' }}">
                @endif
                @if (request()->filled('event'))
                  <input type="hidden" name="event"
                    value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">
                @endif
                @if (request()->filled('min'))
                  <input type="hidden" name="min"
                    value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">
                @endif

                @if (request()->filled('max'))
                  <input type="hidden" name="max"
                    value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">
                @endif

                @if (request()->filled('location'))
                  <input type="hidden" name="location"
                    value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}">
                @endif

                @if (request()->filled('dated'))
                  <input type="hidden" name="dates"
                    value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">
                @endif
                <button type="submit" class="fa fa-search event-search-button"></button>
              </form>
            </div>
            {{-- date filter input --}}
            <div class="widget widget-dropdown">
              <div class="form-group">
                <label for="">{{ __('Filter by Date') }}</label>
                <input type="text" placeholder="{{ __('Start/End Date') }}"
                  @if (request()->input('dates') && request()->input('dates')) value="{{ request()->input('dates') }}" @endif name="daterange" />
              </div>
            </div>
            {{-- location input --}}
            <div class="widget widget-search">
              <form action="{{ route('events') }}">

                @if (request()->filled('search-input'))
                  <input type="hidden" name="search-input"
                    value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}">
                @endif

                @if (request()->filled('category'))
                  <input type="hidden" id="category-id" name="category"
                    value="{{ !empty(request()->input('category')) ? request()->input('category') : '' }}">
                @endif

                @if (request()->filled('event'))
                  <input type="hidden" name="event"
                    value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">
                @endif

                <input type="text" name="location"
                  value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}"
                  placeholder="{{ __('Enter Location') }}">

                @if (request()->filled('dates'))
                  <input type="hidden" name="dates"
                    value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">
                @endif

                @if (request()->filled('min'))
                  <input type="hidden" name="min"
                    value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">
                @endif

                @if (request()->filled('max'))
                  <input type="hidden" name="max"
                    value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">
                @endif
                <button type="submit" class="fa fa-search  event-search-button"></button>
              </form>
            </div>
            <div class="widget widget-cagegory">
              <h5 class="widget-title">{{ __('Category') }}</h5>
              <form action="{{ route('events') }}" id="catForm">
                @if (request()->filled('search-input'))
                  <input type="hidden" name="search-input"
                    value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}">
                @endif

                <select id="category" name="category" class="widget-select">
                  <option disabled>{{ __('Select a Category') }}</option>
                  <option value="">{{ __('All') }}</option>
                  @foreach ($information['categories'] as $item)
                    <option {{ request()->input('category') == $item->slug ? 'selected' : '' }}
                      value="{{ $item->slug }}">{{ $item->name }}</option>
                  @endforeach
                </select>
                {{-- form hidden input --}}

                @if (request()->filled('location'))
                  <input type="hidden" name="location"
                    value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}">
                @endif

                @if (request()->filled('event'))
                  <input type="hidden" name="event"
                    value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">
                @endif

                @if (request()->filled('min'))
                  <input type="hidden" name="min"
                    value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">
                @endif

                @if (request()->filled('max'))
                  <input type="hidden" name="max"
                    value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">
                @endif

                @if (request()->filled('dates'))
                  <input type="hidden" name="dates"
                    value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">
                @endif
              </form>
            </div>
            <div class="widget widget-location">
              <h5 class="widget-title">{{ __('Events') }}</h5>
              <div class="widget-radio">
                <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input"
                    {{ request()->input('event') == 'online' ? 'checked' : '' }} value="online" name="event"
                    id="radio1">
                  <label class="custom-control-label" for="radio1">{{ __('Online Events') }}</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input" value="venue"
                    {{ request()->input('event') == 'venue' ? 'checked' : '' }} name="event" id="radio2">
                  <label class="custom-control-label" for="radio2">{{ __('Venue Events') }}</label>
                </div>
              </div>
            </div>


            <div class="widget price-filter-widget">
              <h5 class="widget-title">{{ __('Price Filter') }}</h5>
              <div class="price-slider-range" id="range-slider"></div>
              <div class="price-btn">
                <input type="text" dir="ltr" id="price" value="{{ request()->input('min') }}" readonly>
                <button class="theme-btn" id="slider_submit">{{ __('Price Filter') }}</button>
              </div>
            </div>
            @if (!empty(showAd(2)))
              <div class="text-center mt-4">
                {!! showAd(2) !!}
              </div>
            @endif
          </div>
        </div>
        <div class="col-lg-9">
          <div class="event-page-content">
            <div class="price-filter-bar mb-25">
              @php
                $currentPricing = request()->input('pricing', '');
                $baseParams = request()->except('pricing');
              @endphp
              <a href="{{ route('events', array_merge($baseParams, ['pricing' => ''])) }}"
                class="price-filter-btn {{ $currentPricing === '' ? 'active' : '' }}">{{ __('Todos') }}</a>
              <a href="{{ route('events', array_merge($baseParams, ['pricing' => 'free'])) }}"
                class="price-filter-btn {{ $currentPricing === 'free' ? 'active' : '' }}">{{ __('Gratis') }}</a>
              <a href="{{ route('events', array_merge($baseParams, ['pricing' => 'paid'])) }}"
                class="price-filter-btn {{ $currentPricing === 'paid' ? 'active' : '' }}">{{ __('De pago') }}</a>
            </div>
            <div class="row">
              @if (count($information['events']) > 0)
                @foreach ($information['events'] as $event)
                  <div class="col-sm-6 col-xl-4 ev-card-col">
                    @include('frontend.partials.event-card')
                  </div>
                @endforeach
              @else
                <div class="col-lg-12">
                  <h3 class="text-center">{{ __('No Event Found') }}</h3>
                </div>
              @endif
            </div>
            <ul class="pagination flex-wrap pt-10">
              {{ $information['events']->links() }}
            </ul>
            @if (!empty(showAd(3)))
              <div class="text-center mt-4">
                {!! showAd(3) !!}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Event Page End -->

  <form id="filtersForm" class="d-none" action="{{ route('events') }}" method="GET">
    <input type="hidden" id="category-id" name="category"
      value="{{ !empty(request()->input('category')) ? request()->input('category') : '' }}">

    <input type="hidden" id="event" name="event"
      value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">

    <input type="hidden" id="min-id" name="min"
      value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">

    <input type="hidden" id="max-id" name="max"
      value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">

    <input type="hidden" name="search-input"
      value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}">
    <input type="hidden" name="location"
      value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}">

    <input type="hidden" id="dates-id" name="dates"
      value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">

    <input type="hidden" name="pricing"
      value="{{ !empty(request()->input('pricing')) ? request()->input('pricing') : '' }}">

    <button type="submit" id="submitBtn"></button>
  </form>
@endsection

@section('custom-script')
  <script type="text/javascript" src="{{ asset('assets/front/js/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/front/js/daterangepicker.min.js') }}"></script>

  <script>
    let min_price = {!! htmlspecialchars($information['min']) !!};
    let max_price = {!! htmlspecialchars($information['max']) !!};
    let symbol = "{!! htmlspecialchars($basicInfo->base_currency_symbol) !!}";
    let position = "{!! htmlspecialchars($basicInfo->base_currency_symbol_position) !!}";
    let curr_min = {!! !empty(request()->input('min')) ? htmlspecialchars(request()->input('min')) : 5 !!};
    let curr_max = {!! !empty(request()->input('max')) ? htmlspecialchars(request()->input('max')) : 800 !!};
  </script>

  <script src="{{ asset('assets/front/js/custom_script.js') }}"></script>
@endsection
