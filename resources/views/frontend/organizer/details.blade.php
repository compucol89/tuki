@extends('frontend.layout')
@section('pageHeading')
  {{ $admin == true ? $organizer->username : $organizer->username }}
@endsection
@section('meta-keywords', "{{ $organizer->username }}")
@section('meta-description', "$organizer->details")

@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
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
                      alt="Author">
                  @else
                    @if ($organizer->photo == null)
                      <img class="rounded-lg lazy" data-src="{{ asset('assets/front/images/user.png') }}" alt="image">
                    @else
                      <img class="rounded-lg lazy"
                        data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}" alt="image">
                    @endif
                  @endif
                </a>
              </figure>
              <div class="author-info">
                <h3 class="mb-1 text-white">{{ @$organizer_info->name }}</h3>
                <h6 class="mb-1 text-white">{{ $organizer->username }}</h6>
                <span>{{ __('Member since') }} {{ date('M Y', strtotime($organizer->created_at)) }}</span>
              </div>
            </div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Organizer Details') }}</li>
              </ol>
            </nav>
          </div>
        </div>
        <div class="col-lg-4 text-white">
          <div class="social-style-one">
            <h5 class="mb-0">{{ __('Follow Me') }}</h5>
            <a target="_blank" href="{{ $organizer->facebook }}"><i class="fab fa-facebook-f"></i></a>
            <a target="_blank" href="{{ $organizer->linkedin }}"><i class="fab fa-linkedin-in"></i></a>
            <a target="_blank" href="{{ $organizer->twitter }}"><i class="fab fa-twitter"></i></a>
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
        <div class="col-lg-8">
          <h3 class="mb-20">{{ __('All Events') }}</h3>
          <div class="author-tabs mb-30">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <button class="nav-link active" type="button" data-toggle="tab" data-target="#all"
                  aria-selected="true">{{ __('All') }}</button>
              </li>
              @foreach ($categories as $category)
                <li class="nav-item">
                  <button class="nav-link" type="button" data-toggle="tab" data-target="#{{ $category->slug }}"
                    aria-selected="false" tabindex="-1">{{ $category->name }}</button>
                </li>
              @endforeach


            </ul>
          </div>
          <div class="tab-content mb-50">
            <div class="tab-pane fade show active" id="all">
              <div class="row">
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
                  <div class="col-md-12">
                    <h5 class="text-center">{{ __('No Event Found') }}</h5>
                  </div>
                @endif
              </div>
            </div>
            @foreach ($categories as $category)
              <div class="tab-pane fade" id="{{ $category->slug }}">
                <div class="row">
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
                    <div class="col-md-12">
                      <h5 class="text-center">{{ __('No Event Found') }}</h5>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>

          @if (!empty(showAd(3)))
            <div class="text-center mt-4">
              {!! showAd(3) !!}
            </div>
          @endif
        </div>

        <div class="col-lg-4">
          <aside class="sidebar-widget-area">
            <div class="widget widget-author-details border mb-30">
              <div class="author mb-20">
                <figure class="author-img">
                  @if ($admin == true)
                    <img class="rounded-lg lazy" data-src="{{ asset('assets/admin/img/admins/' . $organizer->image) }}"
                      alt="Author">
                  @else
                    @if ($organizer->photo == null)
                      <img class="rounded-lg lazy" data-src="{{ asset('assets/front/images/user.png') }}"
                        alt="image">
                    @else
                      <img class="rounded-lg lazy"
                        data-src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}"
                        alt="image">
                    @endif
                  @endif
                </figure>
                <div class="author-info">
                  <h6 class="mb-1">{{ @$organizer_info->name }}</h6>
                  <span class="icon-start">{{ $organizer->username }}</span>
                </div>
              </div>
              @if ($admin == true && $organizer_info)
                @if ($organizer_info->details != null)
                  <div class="font-sm">
                    <div class="click-show">
                      <div class="show-content">
                        <b>{{ __('About') }} : </b>{{ $organizer_info->details }}
                      </div>
                      <div class="read-more-btn">
                        <span>{{ __('Read more') }}</span>
                        <span>{{ __('Read less') }}</span>
                      </div>
                    </div>
                  </div>
                @endif

              @endif
              @if (@$organizer_info->details != null)
                <div class="font-sm">
                  <div class="click-show">
                    <div class="show-content">
                      <b>{{ __('About') }} : </b>{{ @$organizer_info->details }}
                    </div>
                    <div class="read-more-btn">
                      <span>{{ __('Read more') }}</span>
                      <span>{{ __('Read less') }}</span>
                    </div>
                  </div>
                </div>
              @endif
              <ul class="toggle-list list-unstyled mt-15 font-sm">
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
              <div class="btn-groups text-center mt-20">
                <button type="button" class="theme-btn w-100 mb-10" title="Title" data-toggle="modal"
                  data-target="#contactModal">{{ __('Contact Now') }}</button>
              </div>
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
          <h5 class="modal-title" id="contactModalLabel">{{ __('Contact Now') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                      <textarea name="message" class="form_control" placeholder="{{ __('Comment') }}"></textarea>
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
                    <button class="theme-btn" type="submit" title="Submit">{{ __('Submit') }}</button>
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
