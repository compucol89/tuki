@extends('frontend.layout')
@section('pageHeading', 'Lista de deseos')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Lista de deseos</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Lista de deseos</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
<section class="cd-page py-60">
  <div class="container">
    <div class="row g-4">

      @includeIf('frontend.customer.partials.sidebar')

      <div class="col-lg-9">
        <div class="cd-card">
          <div class="cd-card__head">
            <h3 class="cd-card__title">Lista de deseos</h3>
            <span class="cd-count-pill">{{ count($wishlist) }} {{ count($wishlist) == 1 ? 'evento' : 'eventos' }}</span>
          </div>

          @if(count($wishlist) > 0)
            <div class="wl-grid">
              @foreach ($wishlist as $item)
                @php
                  $ev = DB::table('event_contents')
                    ->join('events', 'events.id', 'event_contents.event_id')
                    ->where('event_contents.event_id', $item->event_id)
                    ->select('event_contents.title', 'event_contents.slug', 'events.thumbnail', 'events.start_date', 'events.start_time', 'events.event_type')
                    ->first();
                @endphp
                @if($ev)
                  <div class="wl-item">
                    {{-- Thumbnail --}}
                    <div class="wl-item__img">
                      <img class="lazy"
                           data-src="{{ asset('assets/admin/img/event/thumbnail/' . $ev->thumbnail) }}"
                           src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                           alt="{{ $ev->title }}">
                      <span class="wl-item__type {{ $ev->event_type == 'online' ? 'wl-item__type--online' : 'wl-item__type--venue' }}">
                        {{ $ev->event_type == 'online' ? 'Online' : 'Presencial' }}
                      </span>
                    </div>
                    {{-- Info --}}
                    <div class="wl-item__body">
                      <h4 class="wl-item__title">{{ Str::limit($ev->title, 50) }}</h4>
                      @if($ev->start_date)
                        <p class="wl-item__date">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                          {{ \Carbon\Carbon::parse($ev->start_date)->translatedFormat('d/m/Y') }}
                          @if($ev->start_time) · {{ \Carbon\Carbon::parse($ev->start_time)->format('H:i') }}hs @endif
                        </p>
                      @endif
                      <div class="wl-item__actions">
                        <a href="{{ route('event.details', [$ev->slug, $item->event_id]) }}"
                           class="wl-item__btn wl-item__btn--primary">Ver evento</a>
                        <a href="{{ route('remove.wishlist', $item->event_id) }}"
                           class="wl-item__btn wl-item__btn--remove"
                           onclick="return confirm('¿Quitás este evento de tu lista?')">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                          Quitar
                        </a>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          @else
            <div class="cd-empty">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
              <p>Tu lista de deseos está vacía.</p>
              <a href="{{ route('events') }}" class="cd-empty__link">Explorá eventos</a>
            </div>
          @endif

        </div>
      </div>

    </div>
  </div>
</section>
@endsection
