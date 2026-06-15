@extends('frontend.layout')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->organizer_page_title ?? __('Organizadores') }}
  @else
    {{ __('Organizadores') }}
  @endif
@endsection
@php
  $metaKeywords = !empty($seo->meta_keyword_organizer) ? $seo->meta_keyword_organizer : '';
  $metaDescription = !empty($seo->meta_description_organizer) ? $seo->meta_description_organizer : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")
@section('body-class', 'organizers-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/home.css') }}">
@endpush

@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">
          @if (!empty($pageHeading))
            {{ $pageHeading->organizer_page_title ?? __('Organizadores') }}
          @else
            {{ __('Organizadores') }}
          @endif
        </h2>
        <nav aria-label="{{ __('Ruta de navegación') }}">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">
              @if (!empty($pageHeading))
                {{ $pageHeading->organizer_page_title ?? __('Organizadores') }}
              @else
                {{ __('Organizadores') }}
              @endif
            </li>
          </ol>
        </nav>

        <div class="authors-search-filter mt-30">
          <form action="{{ route('frontend.all.organizer') }}" method="GET" role="search">
            <div class="search-filter-form">
              <div class="row no-gutters justify-content-center">
                <div class="search-item">
                  <label class="sr-only" for="organizer-search">{{ __('Nombre del organizador') }}</label>
                  <input type="text" id="organizer-search" class="form_control" name="organizer"
                    placeholder="{{ __('Nombre del organizador') }}" value="{{ request()->input('organizer') }}">
                </div>

                <div class="search-item">
                  <label class="sr-only" for="organizer-username">{{ __('Usuario') }}</label>
                  <input type="text" id="organizer-username" class="form_control" placeholder="{{ __('Usuario') }}" name="username"
                    value="{{ request()->input('username') }}">
                </div>

                <div class="search-item">
                  <label class="sr-only" for="organizer-location">{{ __('Ubicación') }}</label>
                  <input type="text" id="organizer-location" class="form_control" name="location" placeholder="{{ __('Ubicación') }}"
                    value="{{ request()->input('location') }}">
                </div>

                <div class="search-item">
                  <button type="submit" class="theme-btn rounded-0">{{ __('Buscar') }}</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('content')
  <div class="author-area py-120 rpy-100 bg-lighter">
    <div class="container">
      <div class="product-filter">
        <div class="row justify-content-between align-items-center">
          <div class="col-lg-4 col-md-6">
            <h6 class="mb-20">{{ __('Organizadores encontrados') }}: {{ $collection->total() }}</h6>
          </div>
        </div>
      </div>

      <div class="row">
        @forelse ($collection as $item)
          @php
            $organizerInfo = $item->organizer_info;
            $organizerName = $organizerInfo->name ?? $item->username;
            $organizerLocation = collect([$organizerInfo->city ?? null, $organizerInfo->state ?? null, $organizerInfo->country ?? null])
              ->filter()
              ->implode(', ');
            $organizerUrl = route('frontend.organizer.details', [$item->id, str_replace(' ', '-', $item->username)]);
            $eventCount = OrganizerEventCount($item->id);
          @endphp
          <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card card-center p-4 mb-30">
              <figure class="card-img mx-auto mb-20">
                <a href="{{ $organizerUrl }}" target="_self" title="{{ $organizerName }}">
                  @if ($item->photo == null)
                    <picture>
                      <source srcset="{{ asset('assets/front/images/user.webp') }}" type="image/webp">
                      <img class="rounded-lg lazy" data-src="{{ asset('assets/front/images/user.png') }}" alt="{{ __('Foto del organizador') }}" loading="lazy">
                    </picture>
                  @else
                    <img class="rounded-lg lazy" data-src="{{ asset('assets/admin/img/organizer-photo/' . $item->photo) }}"
                      alt="{{ __('Foto del organizador') }}" loading="lazy">
                  @endif
                </a>
              </figure>
              <div class="card-content">
                <h5 class="card-title mb-1"><a href="{{ $organizerUrl }}">{{ $organizerName }}</a></h5>
                <div>
                  <span class="text-muted mb-1"><a href="{{ $organizerUrl }}">{{ '@' . $item->username }}</a></span>
                </div>
                @if (!empty($organizerLocation))
                  <div class="mb-10 font-sm">
                    <span>{{ $organizerLocation }}</span>
                  </div>
                @endif
                <div class="mb-15 font-sm">
                  <span>{{ $eventCount }} {{ $eventCount === 1 ? __('evento') : __('eventos') }}</span>
                </div>
                <a href="{{ $organizerUrl }}" target="_self" title="{{ $organizerName }}" class="btn-text">{{ __('Ver perfil') }}</a>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="text-center py-5">
              <h4>{{ __('No encontramos organizadores con esos filtros.') }}</h4>
              <a href="{{ route('frontend.all.organizer') }}" class="theme-btn mt-20">{{ __('Limpiar búsqueda') }}</a>
            </div>
          </div>
        @endforelse
      </div>

      {{ $collection->appends(request()->query())->links() }}

      @if (!empty(showAd(3)))
        <div class="text-center mt-4">
          {!! showAd(3) !!}
        </div>
      @endif
    </div>
  </div>

  @include('frontend.partials.home-trust-sections')
@endsection

@push('scripts')
<script>
(function() {
  var els = document.querySelectorAll('.feature-section.reveal-on-scroll, .testimonial-section.reveal-on-scroll');
  if (!els.length || !('IntersectionObserver' in window)) {
    els.forEach(function(el) { el.classList.add('revealed'); });
    return;
  }
  var io = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) {
      if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
  els.forEach(function(el) { io.observe(el); });
})();
</script>
@endpush
