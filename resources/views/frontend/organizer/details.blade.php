@extends('frontend.layout')
@section('body-class', 'organizer-details-page')
@section('pageHeading')
  {{ $admin == true ? $organizer->username : $organizer->username }}
@endsection
@section('meta-keywords', "{{ $organizer->username }}")
@section('meta-description', "$organizer->details")

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/front/css/organizer.css') }}">
@endpush

@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy org-hero-premium"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-8">
          <div class="banner-inner banner-author">
            <div class="author mb-3">
              <figure class="author-img mb-0">
                <a href="javaScript:void(0)">
                  @if ($admin == true)
                    <img class="rounded-lg lazy" data-src="{{ asset('assets/admin/img/admins/' . $organizer->image) }}"
                      alt="{{ __('Perfil del organizador') }}">
                  @else
                    @if ($organizer->photo == null)
                      <picture>
                        <source srcset="{{ asset('assets/front/images/user.webp') }}" type="image/webp">
                        <img class="rounded-lg lazy" data-src="{{ asset('assets/front/images/user.png') }}" alt="{{ __('Perfil del organizador') }}">
                      </picture>
                    @else
                      <img class="rounded-lg lazy"
                        data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}" alt="{{ __('Perfil del organizador') }}">
                    @endif
                  @endif
                </a>
              </figure>
              <div class="author-info">
                <h3 class="mb-1 text-white">{{ @$organizer_info->name }}</h3>
                <h6 class="mb-1 text-white">{{ $organizer->username }}</h6>
                <span>{{ __('Miembro desde') }} {{ \Carbon\Carbon::parse($organizer->created_at)->locale('es')->translatedFormat('F Y') }}</span>
              </div>
            </div>
            <nav class="org-hero-premium__breadcrumb" aria-label="breadcrumb">
              <ol class="breadcrumb org-hero-premium__crumb-trail">
                <li class="breadcrumb-item org-hero-premium__crumb-item">
                  <a href="{{ route('index') }}">{{ __('Home') }}</a>
                </li>
                <li class="breadcrumb-item active org-hero-premium__crumb-item org-hero-premium__crumb-item--current" aria-current="page">
                  {{ __('Organizer Details') }}
                </li>
              </ol>
            </nav>
          </div>
        </div>
        <div class="col-lg-4 text-white">
          <div class="social-style-one org-hero-premium__social">
            <p class="org-hero-premium__social-eyebrow">{{ __('Seguime') }}</p>
            <div class="org-hero-premium__social-links">
              @foreach ([
                ['url' => $organizer->facebook, 'label' => 'Facebook', 'icon' => 'fab fa-facebook-f'],
                ['url' => $organizer->linkedin, 'label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in'],
                ['url' => $organizer->twitter, 'label' => 'Twitter', 'icon' => 'fab fa-twitter'],
              ] as $social)
                @php
                  $socialUrl = trim((string) ($social['url'] ?? ''));
                @endphp
                @if ($socialUrl !== '')
                  <a target="_blank" rel="noopener noreferrer" href="{{ $socialUrl === '#' ? 'javascript:void(0)' : $socialUrl }}"
                    title="{{ $socialUrl }}"
                    aria-label="{{ $social['label'] }}"><i class="{{ $social['icon'] }}" aria-hidden="true"></i></a>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection
@section('content')
  <!-- Author-single-area start -->
  <div class="author-area py-120 rpy-100 ">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 org-events-showcase">
          <header class="org-events-showcase__header">
            <p class="org-events-showcase__eyebrow">{{ __('Eventos publicados') }}</p>
            <h2 class="org-events-showcase__title">{{ __('Todos los eventos') }}</h2>
          </header>

          <div class="org-events-showcase__tabs" role="navigation" aria-label="{{ __('Event category tabs') }}">
            <div class="org-events-showcase__tabs-scroller">
              <div class="author-tabs org-events-showcase__author-tabs mb-30">
                <ul class="nav nav-tabs org-events-showcase__nav" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" type="button" data-toggle="tab" data-target="#all"
                      id="org-tab-all" role="tab" aria-controls="all" aria-selected="true">{{ __('All') }}</button>
                  </li>
                  @foreach ($categories as $category)
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" type="button" data-toggle="tab" data-target="#{{ $category->slug }}"
                        id="org-tab-{{ $category->slug }}" role="tab" aria-controls="{{ $category->slug }}"
                        aria-selected="false" tabindex="-1">{{ $category->name }}</button>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>

          <div class="tab-content org-events-showcase__tab-panels mb-50">
            <div class="tab-pane fade show active org-events-showcase__panel" id="all" role="tabpanel"
              aria-labelledby="org-tab-all">
              <div class="row org-events-showcase__grid">
                @if (count($events) > 0)
                  @foreach ($events as $event)
                    @if (!empty($event->information))
                      @php
                        $ev = (object) array_merge(
                          $event->information->toArray(),
                          $event->attributesToArray()
                        );
                      @endphp
                      <div class="col-md-6 col-lg-6 ev-card-col">
                        @include('frontend.partials.event-card', ['event' => $ev])
                      </div>
                    @endif
                  @endforeach
                @else
                  <div class="col-12 org-events-showcase__empty">
                    <div class="org-events-showcase__empty-card" role="status">
                      <div class="org-events-showcase__empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="56" height="56">
                          <rect x="10" y="14" width="44" height="40" rx="8" stroke="currentColor" stroke-width="2.25" />
                          <path d="M10 24h44" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" />
                          <circle cx="22" cy="18" r="2" fill="currentColor" />
                          <circle cx="32" cy="18" r="2" fill="currentColor" />
                          <circle cx="42" cy="18" r="2" fill="currentColor" />
                          <path d="M24 36h16M24 42h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".45" />
                        </svg>
                      </div>
                      <p class="org-events-showcase__empty-title">{{ __('No Event Found') }}</p>
                      <p class="org-events-showcase__empty-hint">{{ __('Todavía no hay eventos publicados por este organizador.') }}</p>
                    </div>
                  </div>
                @endif
              </div>
            </div>
            @foreach ($categories as $category)
              <div class="tab-pane fade org-events-showcase__panel" id="{{ $category->slug }}" role="tabpanel"
                aria-labelledby="org-tab-{{ $category->slug }}">
                <div class="row org-events-showcase__grid">
                  @php
                    $language_id = $currentLanguageInfo->id;
                    if (request()->filled('admin') && request()->input('admin') == 'true') {
                        $c_events = adminCategoryWiseEvents($category->id, $language_id, $organizer->id);
                    } else {
                        $c_events = categoryWiseEvents($category->id, $language_id, $organizer->id);
                    }
                  @endphp
                  @if (count($c_events) > 0)
                    @foreach ($c_events as $c_event)
                      @if (!empty($c_event->information))
                        @php
                          $ev = (object) array_merge(
                            $c_event->information->toArray(),
                            $c_event->attributesToArray()
                          );
                        @endphp
                        <div class="col-md-6 col-lg-6 ev-card-col">
                          @include('frontend.partials.event-card', ['event' => $ev])
                        </div>
                      @endif
                    @endforeach
                  @else
                    <div class="col-12 org-events-showcase__empty">
                      <div class="org-events-showcase__empty-card" role="status">
                        <div class="org-events-showcase__empty-icon" aria-hidden="true">
                          <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="56" height="56">
                            <rect x="10" y="14" width="44" height="40" rx="8" stroke="currentColor" stroke-width="2.25" />
                            <path d="M10 24h44" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" />
                            <circle cx="22" cy="18" r="2" fill="currentColor" />
                            <circle cx="32" cy="18" r="2" fill="currentColor" />
                            <circle cx="42" cy="18" r="2" fill="currentColor" />
                            <path d="M24 36h16M24 42h10" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".45" />
                          </svg>
                        </div>
                        <p class="org-events-showcase__empty-title">{{ __('No Event Found') }}</p>
                        <p class="org-events-showcase__empty-hint">{{ __('No encontramos eventos en esta categoría.') }}</p>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>

          @if (!empty(showAd(3)))
            <div class="text-center mt-4 org-events-showcase__ad">
              {!! showAd(3) !!}
            </div>
          @endif
        </div>

        <div class="col-lg-4">
          <aside class="sidebar-widget-area">
            <div class="widget widget-author-details widget-author-details--premium mb-30">
              <div class="org-profile-card">
              <div class="org-profile-card__hero author mb-20">
                <figure class="org-profile-card__avatar author-img">
                  @if ($admin == true)
                    <img class="rounded-lg lazy org-profile-card__img" data-src="{{ asset('assets/admin/img/admins/' . $organizer->image) }}"
                      alt="{{ __('Perfil del organizador') }}">
                  @else
                    @if ($organizer->photo == null)
                      <picture>
                        <source srcset="{{ asset('assets/front/images/user.webp') }}" type="image/webp">
                        <img class="rounded-lg lazy org-profile-card__img" data-src="{{ asset('assets/front/images/user.png') }}"
                          alt="{{ __('Perfil del organizador') }}">
                      </picture>
                    @else
                      <img class="rounded-lg lazy org-profile-card__img"
                        data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}"
                        alt="{{ __('Perfil del organizador') }}">
                    @endif
                  @endif
                </figure>
                <div class="org-profile-card__intro author-info">
                  <p class="org-profile-card__eyebrow">{{ __('Organizador') }}</p>
                  <h6 class="org-profile-card__name mb-1">{{ @$organizer_info->name }}</h6>
                  <span class="org-profile-card__handle icon-start">{{ $organizer->username }}</span>
                </div>
              </div>
              @if ($admin == true && $organizer_info)
                @if ($organizer_info->details != null)
                  <div class="font-sm org-profile-card__bio">
                    <div class="click-show">
                      <div class="show-content">
                        <b>{{ __('Acerca de') }}: </b>{{ $organizer_info->details }}
                      </div>
                      <div class="read-more-btn">
                        <span>{{ __('Read more') }}</span>
                        <span>{{ __('Read less') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
              @else
                @if (@$organizer_info->details != null)
                  <div class="font-sm org-profile-card__bio">
                    <div class="click-show">
                      <div class="show-content">
                        <b>{{ __('Acerca de') }}: </b>{{ @$organizer_info->details }}
                      </div>
                      <div class="read-more-btn">
                        <span>{{ __('Read more') }}</span>
                        <span>{{ __('Read less') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
              @endif
              <ul class="org-profile-card__facts toggle-list list-unstyled mt-15 font-sm">
                <li>
                  <span class="first">{{ __('Total Events') }}</span>
                  <span class="last font-sm">
                    @if ($admin == true)
                      {{ OrganizerEventCount($organizer->id, true) }}
                    @else
                      {{ OrganizerEventCount($organizer->id) }}
                    @endif
                  </span>
                </li>
                @if ($organizer->email != null)
                  <li>
                    <span class="first">{{ __('Email') }}</span>
                    <span class="last font-sm"><a href="mailto:{{ $organizer->email }}"
                        title="{{ $organizer->email }}">{{ $organizer->email }}</a></span>
                  </li>
                @endif

                @if ($organizer->phone != null)
                  <li>
                    <span class="first">{{ __('Phone') }}</span>
                    <span class="last font-sm"><a href="tel:{{ $organizer->phone }}"
                        title="{{ $organizer->phone }}">{{ $organizer->phone }}</a></span>
                  </li>
                @endif
                @if (@$organizer_info->city != null)
                  <li>
                    <span class="first">{{ __('City') }}</span>
                    <span class="last font-sm"><a href="tel:{{ @$organizer_info->city }}"
                        title="{{ @$organizer_info->city }}">{{ @$organizer_info->city }}</a></span>
                  </li>
                @endif

                @if (@$organizer_info->state != null)
                  <li>
                    <span class="first">{{ __('State') }}</span>
                    <span class="last font-sm"><a href="tel:{{ @$organizer_info->state }}"
                        title="{{ @$organizer_info->state }}">{{ @$organizer_info->state }}</a></span>
                  </li>
                @endif
                @if (@$organizer_info->country != null)
                  <li>
                    <span class="first">{{ __('Country') }}</span>
                    <span class="last font-sm"><a href="tel:{{ @$organizer_info->country }}"
                        title="{{ @$organizer_info->country }}">{{ @$organizer_info->country }}</a></span>
                  </li>
                @endif

                @if (@$organizer_info->address != null)
                  <li>
                    <span class="first">{{ __('Address') }}</span>
                    <span class="last font-sm">{{ @$organizer_info->address }}</span>
                  </li>
                @endif

                @if ($admin == true && $organizer->address != null)
                  <li>
                    <span class="first">{{ __('Address') }}</span>
                    <span class="last font-sm">{{ $organizer->address }}</span>
                  </li>
                @endif

              </ul>
              <div class="org-profile-card__actions btn-groups text-center mt-20">
                <button type="button" class="org-profile-card__cta theme-btn w-100 mb-10" title="{{ __('Contactar') }}" data-toggle="modal"
                  data-target="#contactModal">{{ __('Contactar') }}</button>
              </div>
              </div>{{-- /.org-profile-card --}}
            </div>

            <div class="widget widget-business-days mb-30">
              @if (!empty(showAd(1)))
                <div class="text-center mt-4">
                  {!! showAd(1) !!}
                </div>
              @endif
              @if (!empty(showAd(2)))
                <div class="text-center mt-4">
                  {!! showAd(2) !!}
                </div>
              @endif
            </div>
          </aside>
        </div>
      </div>
    </div>
  </div>
  <!-- Author-single-area start -->

  <!-- Contact Modal -->
  <div class="contact-modal modal fade" id="contactModal" tabindex="-1" role="dialog"
    aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="contactModalLabel">{{ __('Contactar') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="contact-wrapper">
            <div class="contact-form m-0">
              <form action="{{ route('organizer.contact.send_mail') }}" method="POST" id="vendorContactForm">
                @csrf
                <input type="hidden" name="id" value="{{ $organizer->id }}">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form_group mb-20">
                      <input type="text" class="form_control" placeholder="{{ __('Enter Your Full Name') }}"
                        name="name">
                      <p class="text-danger em mt_1" id="Error_name"></p>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form_group mb-20">
                      <input type="email" class="form_control" placeholder="{{ __('Enter Your Email') }}"
                        name="email">
                      <p class="text-danger em mt_1" id="Error_email"></p>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form_group mb-20">
                      <input type="text" class="form_control" placeholder="{{ __('Enter Subject') }}"
                        name="subject">
                      <p class="text-danger em mt_1" id="Error_subject"></p>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="form_group mb-20">
                      <textarea name="message" class="form_control" placeholder="{{ __('Escribí tu mensaje') }}"></textarea>
                      <p class="text-danger em mt_1" id="Error_message"></p>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    @if ($basicInfos->google_recaptcha_status == 1)
                      <div class="form_group">
                        {!! NoCaptcha::renderJs() !!}
                        {!! NoCaptcha::display() !!}

                        <p class="text-danger em" id="Error_g-recaptcha-response"></p>
                      </div>
                    @endif
                  </div>
                  <div class="col-lg-12 text-center">
                    <button class="theme-btn" type="submit" title="{{ __('Enviar') }}">{{ __('Enviar') }}</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Contact Modal -->
@endsection

@push('scripts')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('Inicio'),
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => __('Organizador'),
            'item' => url()->current(),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
