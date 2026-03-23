@extends('frontend.layout')
@section('pageHeading', 'Ticket #' . $ticket->id)
@section('custom-style')
  <link rel="stylesheet" href="{{ asset('assets/admin/css/summernote-content.css') }}">
@endsection
@section('hero-section')
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">Ticket #{{ $ticket->id }}</h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.support_tickert') }}">Soporte</a></li>
            <li class="breadcrumb-item active">#{{ $ticket->id }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
@endsection

@section('content')
@php
  $stMap = [
    1 => ['label' => 'Pendiente', 'class' => 'st-badge--pending'],
    2 => ['label' => 'Abierto',   'class' => 'st-badge--open'],
    3 => ['label' => 'Cerrado',   'class' => 'st-badge--closed'],
  ];
  $st = $stMap[$ticket->status] ?? ['label' => '—', 'class' => 'st-badge--pending'];
@endphp

<section class="cd-page py-60">
  <div class="container">
    <div class="row g-4">

      @includeIf('frontend.customer.partials.sidebar')

      <div class="col-lg-9">

        {{-- Header del ticket --}}
        <div class="cd-card mb-4">
          <div class="cd-card__head">
            <div>
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span class="cd-bk-header__id">#{{ $ticket->id }}</span>
                <span class="st-badge {{ $st['class'] }}">{{ $st['label'] }}</span>
              </div>
              <p class="st-subject">{{ $ticket->subject }}</p>
            </div>
            <a href="{{ route('customer.support_tickert') }}" class="cd-bk-back-btn">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Mis tickets
            </a>
          </div>

          {{-- Descripción original --}}
          <div class="st-desc-block">
            <p class="st-desc-text">{{ $ticket->description }}</p>
            @if ($ticket->attachment)
              <a href="{{ asset('assets/admin/img/support-ticket/' . $ticket->attachment) }}"
                 download class="st-dl-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Descargar adjunto
              </a>
            @endif
          </div>
        </div>

        {{-- Mensajes --}}
        @if ($ticket->status == 2)
          <div class="cd-card mb-4">
            <div class="cd-card__head">
              <h3 class="cd-card__title">Conversación</h3>
              <span class="cd-count-pill">{{ count($ticket->messages) }} {{ count($ticket->messages) == 1 ? 'mensaje' : 'mensajes' }}</span>
            </div>

            <div class="st-messages">
              @if (count($ticket->messages) > 0)
                @foreach ($ticket->messages as $reply)
                  @php
                    $isAdmin = $reply->type == 2;
                    if ($isAdmin) {
                      $sender = App\Models\Admin::find($reply->user_id);
                      $senderName = $sender ? $sender->username : 'Soporte';
                      $senderRole = $sender ? ($sender->id == 1 ? 'Super Admin' : $sender->role->name) : 'Admin';
                      $senderPhoto = $sender && $sender->image
                        ? asset('assets/admin/img/admins/' . $sender->image)
                        : asset('assets/admin/img/propics/blank_user.jpg');
                    } else {
                      $sender = App\Models\Customer::find($ticket->user_id);
                      $senderName = $sender ? $sender->fname . ' ' . $sender->lname : 'Cliente';
                      $senderRole = 'Cliente';
                      $senderPhoto = $sender && $sender->photo
                        ? asset('assets/admin/img/customer-profile/' . $sender->photo)
                        : asset('assets/front/images/profile.jpg');
                    }
                  @endphp
                  <div class="st-msg {{ $isAdmin ? 'st-msg--admin' : 'st-msg--customer' }}">
                    <img class="st-msg__avatar" src="{{ $senderPhoto }}" alt="{{ $senderName }}">
                    <div class="st-msg__body">
                      <div class="st-msg__meta">
                        <span class="st-msg__name">{{ $senderName }}</span>
                        <span class="st-msg__role">{{ $senderRole }}</span>
                        <span class="st-msg__date">{{ date_format($reply->created_at, 'd/m/Y H:i') }}</span>
                        @if ($reply->file)
                          <a href="{{ asset('assets/admin/img/support-ticket/' . $reply->file) }}" download class="st-dl-btn st-dl-btn--sm">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Adjunto
                          </a>
                        @endif
                      </div>
                      <div class="st-msg__text summernote-content">{!! $reply->reply !!}</div>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="st-no-msgs">Aún no hay respuestas. Te responderemos a la brevedad.</div>
              @endif
            </div>

            {{-- Formulario de respuesta --}}
            <div class="st-reply-wrap">
              <h4 class="st-reply-title">Tu respuesta</h4>
              <form action="{{ route('customer-reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="ep-field mb-3">
                  <textarea name="reply" rows="4"
                            class="ep-field__input ep-field__textarea @error('reply') is-invalid @enderror"
                            placeholder="Escribí tu respuesta acá..."></textarea>
                  @error('reply')<p class="ep-field__error">{{ $message }}</p>@enderror
                </div>
                <div class="ep-field mb-4">
                  <div class="st-file-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    <span class="st-file-label">Adjuntar archivo .zip (máx. 5 MB)</span>
                    <input type="file" name="file" accept=".zip" class="st-file-input">
                  </div>
                  @error('file')<p class="ep-field__error mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="ep-actions">
                  <button type="submit" class="ep-btn-save">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Enviar respuesta
                  </button>
                </div>
              </form>
            </div>

          </div>
        @endif

      </div>
    </div>
  </div>
</section>
@endsection
