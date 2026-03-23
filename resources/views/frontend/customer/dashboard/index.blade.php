@extends('frontend.layout')
@section('pageHeading', 'Mi cuenta')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Mi cuenta</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Inicio</a></li>
            <li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
@php
  $u = Auth::guard('customer')->user();
  $totalBookings = \App\Models\Event\Booking::where('customer_id', $u->id)->count();
  $upcomingBookings = \App\Models\Event\Booking::where('customer_id', $u->id)
    ->where('event_date', '>=', now())->count();
  $wishlistCount = \App\Models\Event\Wishlist::where('customer_id', $u->id)->count();
@endphp

<section class="cd-page py-60">
  <div class="container">
    <div class="row g-4">

      @includeIf('frontend.customer.partials.sidebar')

      <div class="col-lg-9">

        {{-- Bienvenida --}}
        <div class="cd-welcome mb-4">
          <div>
            <h2 class="cd-welcome__title">Hola, {{ $u->fname ?? $u->username }} 👋</h2>
            <p class="cd-welcome__sub">Bienvenido a tu panel. Desde acá gestionás tus entradas y tu cuenta.</p>
          </div>
          <a href="{{ route('events') }}" class="cd-welcome__cta">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Ver eventos
          </a>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-4">
          <div class="col-sm-4">
            <div class="cd-stat">
              <div class="cd-stat__icon cd-stat__icon--blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 010-6h20a3 3 0 010 6"/><path d="M2 9v11a2 2 0 002 2h16a2 2 0 002-2V9"/></svg>
              </div>
              <div>
                <p class="cd-stat__num">{{ $totalBookings }}</p>
                <p class="cd-stat__label">Reservas totales</p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="cd-stat">
              <div class="cd-stat__icon cd-stat__icon--green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </div>
              <div>
                <p class="cd-stat__num">{{ $upcomingBookings }}</p>
                <p class="cd-stat__label">Próximos eventos</p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="cd-stat">
              <div class="cd-stat__icon cd-stat__icon--orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
              </div>
              <div>
                <p class="cd-stat__num">{{ $wishlistCount }}</p>
                <p class="cd-stat__label">En lista de deseos</p>
              </div>
            </div>
          </div>
        </div>

        {{-- Info de cuenta --}}
        <div class="cd-card mb-4">
          <div class="cd-card__head">
            <h3 class="cd-card__title">Información de cuenta</h3>
            <a href="{{ route('customer.edit.profile') }}" class="cd-card__action">Editar</a>
          </div>
          <div class="cd-profile-grid">
            @if($u->email)
              <div class="cd-profile-item">
                <span class="cd-profile-item__label">Email</span>
                <span class="cd-profile-item__val">{{ $u->email }}</span>
              </div>
            @endif
            @if($u->username)
              <div class="cd-profile-item">
                <span class="cd-profile-item__label">Usuario</span>
                <span class="cd-profile-item__val">{{ $u->username }}</span>
              </div>
            @endif
            @if($u->phone)
              <div class="cd-profile-item">
                <span class="cd-profile-item__label">Teléfono</span>
                <span class="cd-profile-item__val">{{ $u->phone }}</span>
              </div>
            @endif
            @if($u->country)
              <div class="cd-profile-item">
                <span class="cd-profile-item__label">País</span>
                <span class="cd-profile-item__val">{{ $u->country }}</span>
              </div>
            @endif
            @if($u->city)
              <div class="cd-profile-item">
                <span class="cd-profile-item__label">Ciudad</span>
                <span class="cd-profile-item__val">{{ $u->city }}</span>
              </div>
            @endif
            @if($u->address)
              <div class="cd-profile-item">
                <span class="cd-profile-item__label">Dirección</span>
                <span class="cd-profile-item__val">{{ $u->address }}</span>
              </div>
            @endif
          </div>
        </div>

        {{-- Reservas recientes --}}
        <div class="cd-card">
          <div class="cd-card__head">
            <h3 class="cd-card__title">Reservas recientes</h3>
            <a href="{{ route('customer.booking.my_booking') }}" class="cd-card__action">Ver todas</a>
          </div>
          @if($bookings->isEmpty())
            <div class="cd-empty">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 010-6h20a3 3 0 010 6"/><path d="M2 9v11a2 2 0 002 2h16a2 2 0 002-2V9"/></svg>
              <p>Todavía no tenés reservas.</p>
              <a href="{{ route('events') }}" class="cd-empty__link">Explorá eventos</a>
            </div>
          @else
            <div class="cd-table-wrap">
              <table class="cd-table">
                <thead>
                  <tr>
                    <th>Evento</th>
                    <th>Organizador</th>
                    <th>Fecha del evento</th>
                    <th>Reservado</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($bookings as $item)
                    @php
                      $ev = $item->event()->where('language_id', $currentLanguageInfo->id)->select('title','slug','event_id')->first();
                      if (!$ev) {
                        $defLang = App\Models\Language::where('is_default',1)->first();
                        $ev = $item->event()->where('language_id', $defLang->id)->select('title','slug','event_id')->first();
                      }
                    @endphp
                    @if($ev)
                      <tr>
                        <td>
                          <a href="{{ route('event.details', ['slug'=>$ev->slug,'id'=>$ev->event_id]) }}" target="_blank" class="cd-table__link">
                            {{ Str::limit($ev->title, 35) }}
                          </a>
                        </td>
                        <td>
                          @if($item->organizer)
                            <span class="cd-table__organizer">{{ $item->organizer->username }}</span>
                          @else
                            <span class="cd-badge cd-badge--admin">Admin</span>
                          @endif
                        </td>
                        <td class="cd-table__date">{{ \Carbon\Carbon::parse($item->event_date)->translatedFormat('d/m/Y H:i') }}</td>
                        <td class="cd-table__date">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d/m/Y') }}</td>
                        <td>
                          <a href="{{ route('customer.booking_details', $item->id) }}" class="cd-table__btn">Ver</a>
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
