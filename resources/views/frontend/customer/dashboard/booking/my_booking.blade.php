@extends('frontend.layout')
@section('pageHeading', 'Mis entradas')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Mis entradas</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">Mis entradas</li>
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
            <h3 class="cd-card__title">Mis entradas</h3>
            <span class="cd-count-pill">{{ $bookings->total() }} {{ $bookings->total() == 1 ? 'reserva' : 'reservas' }}</span>
          </div>

          @if($bookings->isEmpty())
            <div class="cd-empty">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 010-6h20a3 3 0 010 6"/><path d="M2 9v11a2 2 0 002 2h16a2 2 0 002-2V9"/></svg>
              <p>Todavía no tenés reservas.</p>
              <a href="{{ route('events') }}" class="cd-empty__link">Explorá eventos</a>
            </div>
          @else
            <div class="cd-table-wrap">
              <table class="cd-table">
                <thead>
                  <tr>
                    <th>Evento</th>
                    <th>ID de reserva</th>
                    <th>Fecha del evento</th>
                    <th>Reservado el</th>
                    <th>Estado</th>
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
                      $isPast = $item->event_date && \Carbon\Carbon::parse($item->event_date)->isPast();
                    @endphp
                    @if($ev)
                      <tr>
                        <td>
                          <a href="{{ route('event.details', ['slug'=>$ev->slug,'id'=>$ev->event_id]) }}"
                             target="_blank" class="cd-table__link">
                            {{ Str::limit($ev->title, 40) }}
                          </a>
                        </td>
                        <td><span class="cd-booking-id">#{{ $item->booking_id }}</span></td>
                        <td class="cd-table__date">{{ \Carbon\Carbon::parse($item->event_date)->translatedFormat('d/m/Y H:i') }}</td>
                        <td class="cd-table__date">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d/m/Y') }}</td>
                        <td>
                          @if($item->paymentStatus == 'free')
                            <span class="cd-status cd-status--free">Gratis</span>
                          @elseif($item->paymentStatus == 'paid' || $item->paymentStatus == 'completed')
                            <span class="cd-status cd-status--paid">{{ $item->paymentStatus == 'completed' ? 'Completado' : 'Pagado' }}</span>
                          @elseif($item->paymentStatus == 'pending')
                            <span class="cd-status cd-status--pending">Pendiente</span>
                          @else
                            <span class="cd-status cd-status--paid">{{ ucfirst($item->paymentStatus) }}</span>
                          @endif
                        </td>
                        <td>
                          <a href="{{ route('customer.booking_details', $item->id) }}" class="cd-table__btn">Ver</a>
                        </td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="cd-pagination-wrap">
              {{ $bookings->links() }}
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>
</section>
@endsection
