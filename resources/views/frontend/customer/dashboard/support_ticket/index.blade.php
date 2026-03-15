@extends('frontend.layout')
@section('pageHeading', 'Mis tickets de soporte')
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Mis tickets de soporte</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Soporte</li>
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
            <h3 class="cd-card__title">Mis tickets</h3>
            <a href="{{ route('customer.support_tickert.create') }}" class="st-new-btn">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Nuevo ticket
            </a>
          </div>

          @if (count($collection) > 0)
            <div class="cd-table-wrap">
              <table class="cd-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($collection as $item)
                    @php
                      $stMap = [
                        1 => ['label' => 'Pendiente', 'class' => 'st-badge--pending'],
                        2 => ['label' => 'Abierto',   'class' => 'st-badge--open'],
                        3 => ['label' => 'Cerrado',   'class' => 'st-badge--closed'],
                      ];
                      $st = $stMap[$item->status] ?? ['label' => ucfirst($item->status), 'class' => 'st-badge--pending'];
                      $status = App\Models\SupportTicketStatus::where('id', 1)->first();
                    @endphp
                    <tr>
                      <td class="cd-table__muted">#{{ $item->id }}</td>
                      <td><span class="cd-table__link" style="cursor:default">{{ $item->subject }}</span></td>
                      <td><span class="st-badge {{ $st['class'] }}">{{ $st['label'] }}</span></td>
                      <td>
                        <a href="{{ $status->support_ticket_status == 'active' ? route('customer.support_ticket.message', $item->id) : '#' }}"
                           class="st-view-btn" title="Ver mensajes">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                          Ver
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="st-empty">
              <div class="st-empty__icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
              </div>
              <p class="st-empty__text">No tenés tickets abiertos</p>
              <a href="{{ route('customer.support_tickert.create') }}" class="st-new-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Abrir ticket
              </a>
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
