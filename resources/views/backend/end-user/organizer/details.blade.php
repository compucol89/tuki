@extends('backend.layout')

@section('style')
  <style>
    .admin-profile-diagnostic {
      display: grid;
      grid-template-columns: minmax(160px, 220px) minmax(0, 1fr) auto;
      gap: 18px;
      align-items: center;
      margin-bottom: 18px;
      padding: 16px 18px;
      border: 1px solid #dcdfe2;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 12px 26px rgba(30, 37, 50, .06);
    }

    .admin-profile-diagnostic__score strong {
      display: block;
      color: #1e2532;
      font-size: 34px;
      font-weight: 800;
      line-height: 1;
    }

    .admin-profile-diagnostic__score span {
      display: inline-flex;
      margin-top: 8px;
      padding: 5px 9px;
      border-radius: 999px;
      background: #f3f4f6;
      color: #64748b;
      font-size: 11px;
      font-weight: 800;
      line-height: 1;
      text-transform: uppercase;
    }

    .admin-profile-diagnostic__score span.is-strong {
      background: rgba(22, 163, 74, .12);
      color: #15803d;
    }

    .admin-profile-diagnostic__score span.is-mid {
      background: rgba(224, 93, 56, .12);
      color: #bf4424;
    }

    .admin-profile-diagnostic__score span.is-low {
      background: rgba(220, 38, 38, .1);
      color: #b91c1c;
    }

    .admin-profile-diagnostic__bar {
      height: 8px;
      margin-top: 10px;
      overflow: hidden;
      border-radius: 999px;
      background: #edf0f2;
    }

    .admin-profile-diagnostic__bar span {
      display: block;
      height: 100%;
      border-radius: inherit;
      background: #e05d38;
    }

    .admin-profile-diagnostic__signals {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
    }

    .admin-profile-diagnostic__signals h5 {
      margin: 0 0 8px;
      color: #1e2532;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    .admin-profile-diagnostic__signals ul {
      display: grid;
      gap: 7px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .admin-profile-diagnostic__signals li {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr);
      gap: 8px;
      align-items: start;
      color: #1e2532;
      font-size: 12px;
      line-height: 1.35;
    }

    .admin-profile-diagnostic__signals i {
      margin-top: 2px;
      color: #16a34a;
      font-size: 12px;
    }

    .admin-profile-diagnostic__signals .is-gap i {
      color: #e05d38;
    }

    .admin-profile-diagnostic__signals strong,
    .admin-profile-diagnostic__signals span {
      display: block;
    }

    .admin-profile-diagnostic__signals span {
      margin-top: 2px;
      color: #64748b;
      font-size: 11px;
    }

    .admin-profile-diagnostic__actions {
      display: grid;
      gap: 8px;
      min-width: 150px;
    }

    .admin-profile-diagnostic__actions a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      min-height: 38px;
      padding: 9px 12px;
      border: 1px solid #dcdfe2;
      border-radius: 9px;
      background: #fff;
      color: #1e2532;
      font-size: 12px;
      font-weight: 800;
      text-decoration: none;
      white-space: nowrap;
    }

    .admin-profile-diagnostic__actions a:first-child {
      border-color: #e05d38;
      background: #e05d38;
      color: #fff;
    }

    .admin-profile-diagnostic__actions a:hover,
    .admin-profile-diagnostic__actions a:focus {
      border-color: #bf4424;
      color: #bf4424;
      text-decoration: none;
    }

    .admin-profile-diagnostic__actions a:first-child:hover,
    .admin-profile-diagnostic__actions a:first-child:focus {
      background: #bf4424;
      color: #fff;
    }

    @media (max-width: 991.98px) {
      .admin-profile-diagnostic,
      .admin-profile-diagnostic__signals {
        grid-template-columns: 1fr;
      }

      .admin-profile-diagnostic__actions {
        display: flex;
        flex-wrap: wrap;
      }
    }
  </style>
@endsection

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Organizer Details') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Organizers Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.organizer_management.registered_organizer') }}">{{ __('Registered Organizer') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Organizer Details') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      @if(!empty($profileQualityDetail))
        <section class="admin-profile-diagnostic" aria-labelledby="admin-profile-diagnostic-title">
          <div class="admin-profile-diagnostic__score">
            <p class="mb-1 text-uppercase font-weight-bold text-muted">{{ __('Perfil público') }}</p>
            <strong>{{ $profileQualityDetail['percent'] }}%</strong>
            <span class="{{ $profileQualityDetail['tone'] }}">{{ $profileQualityDetail['label'] }}</span>
            <div class="admin-profile-diagnostic__bar" aria-hidden="true">
              <span style="width: {{ $profileQualityDetail['percent'] }}%;"></span>
            </div>
          </div>

          <div class="admin-profile-diagnostic__signals">
            <div>
              <h5 id="admin-profile-diagnostic-title">{{ __('Bien resuelto') }}</h5>
              @if($profileQualityDetail['complete']->isNotEmpty())
                <ul>
                  @foreach($profileQualityDetail['complete']->take(3) as $signal)
                    <li>
                      <i class="fas fa-check-circle" aria-hidden="true"></i>
                      <span>
                        <strong>{{ $signal['label'] }}</strong>
                        <span><b>{{ __('Impacto') }}:</b> {{ $signal['impact'] }}</span>
                      </span>
                    </li>
                  @endforeach
                </ul>
              @else
                <p class="mb-0 text-muted">{{ __('Todavía no hay señales fuertes en el perfil.') }}</p>
              @endif
            </div>

            <div>
              <h5>{{ __('Por mejorar') }}</h5>
              @if($profileQualityDetail['gaps']->isNotEmpty())
                <ul>
                  @foreach($profileQualityDetail['gaps']->take(3) as $signal)
                    <li class="is-gap">
                      <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                      <span>
                        <strong>{{ $signal['label'] }}</strong>
                        <span><b>{{ __('Impacto') }}:</b> {{ $signal['impact'] }}</span>
                      </span>
                    </li>
                  @endforeach
                </ul>
              @else
                <p class="mb-0 text-muted">{{ __('Sin faltantes críticos para soporte/comercial.') }}</p>
              @endif
            </div>
          </div>

          <div class="admin-profile-diagnostic__actions">
            <a href="{{ $profileQualityDetail['edit_url'] }}">
              <i class="fas fa-user-edit" aria-hidden="true"></i>
              {{ __('Editar perfil') }}
            </a>
            <a href="{{ $profileQualityDetail['public_url'] }}" target="_blank" rel="noopener">
              <i class="fas fa-external-link-alt" aria-hidden="true"></i>
              {{ __('Ver público') }}
            </a>
          </div>
        </section>
      @endif

      <div class="row">
        <div class="col-md-5">
          <div class="card">
            <div class="card-header">
              <div class="row align-items-center">
                <div class="col-lg-8">
                  <div class="author">
                    @if ($organizer->photo == null)
                      <img class="uploaded-img rounded-circle mh70" src="{{ asset('assets/front/images/user.png') }}"
                        alt="{{ __('Image') }}">
                    @else
                      <img class="uploaded-img rounded-circle mh70"
                        src="{{ asset('assets/admin/img/organizer-photo/' . $organizer->photo) }}" alt="{{ __('Image') }}">
                    @endif
                    <div class="h6 card-title">{{ __('Organizer Information') }}</div>
                  </div>
                </div>
                <div class="col-lg-4">
                  <a class="btn btn-info btn-sm float-right d-inline-block mr-2"
                    href="{{ route('admin.organizer_management.registered_organizer') }}">
                    <span class="btn-label">
                      <i class="fas fa-backward"></i>
                    </span>
                    {{ __('Back') }}
                  </a>
                </div>
              </div>

            </div>

            <div class="card-body">
              <div class="payment-information">
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Name') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->name }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Designation') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->designation }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Username') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $organizer->username }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Email') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $organizer->email }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Phone') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $organizer->phone }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Balance') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ symbolPrice($organizer->amount) }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Country') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->country }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('City') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->city }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('State') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->state }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Zip Code') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->zip_code }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Address') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->address }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Details') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ optional($organizer->organizer_info)->details }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-lg-5">
                  <div class="card-title d-inline-block">
                    {{ __('Events') . ' (' . $language->name . ' ' . __('Language') . ')' }}
                  </div>
                </div>

                <div class="col-lg-4">
                  @includeIf('backend.partials.languages')
                </div>

                <div class="col-lg-2 offset-lg-1 mt-2 mt-lg-0">
                  <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                    data-href="{{ route('admin.event_management.bulk_delete_event') }}">
                    <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-lg-12">
                  @if (session()->has('course_status_warning'))
                    <div class="alert alert-warning">
                      <p class="text-dark mb-0">{{ session()->get('course_status_warning') }}</p>
                    </div>
                  @endif

                  @if (count($events) == 0)
                    <h3 class="text-center mt-2">{{ __('NO EVENT CONTENT FOUND FOR') . $language->name . '!' }}</h3>
                  @else
                    <div class="table-responsive">
                      <table class="table table-striped mt-3" id="basic-datatables">
                        <thead>
                          <tr>
                            <th scope="col">
                              <input type="checkbox" class="bulk-check" data-val="all">
                            </th>
                            <th scope="col">{{ __('Title') }}</th>
                            <th scope="col">{{ __('Category') }}</th>
                            <th scope="col">{{ __('Ticket') }}</th>
                            <th scope="col">{{ __('Actions') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($events as $event)
                            <tr>
                              <td>
                                <input type="checkbox" class="bulk-check" data-val="{{ $event->id }}">
                              </td>
                              <td width="20%">
                                <a target="_blank"
                                  href="{{ route('event.details', ['slug' => $event->slug, 'id' => $event->id]) }}">{{ strlen($event->title) > 30 ? mb_substr($event->title, 0, 30, 'UTF-8') . '....' : $event->title }}</a>
                              </td>
                              <td>
                                {{ $event->category }}
                              </td>
                              <td>
                                @if ($event->event_type == 'venue')
                                  <a href="{{ route('admin.event.ticket', ['language' => $defaultLang->code, 'event_id' => $event->id, 'event_type' => $event->event_type]) }}"
                                    class="btn btn-success btn-sm">{{ __('Manage') }}</a>
                                @endif
                              </td>
                              <td>
                                <div class="dropdown">
                                  <button class="btn btn-secondary dropdown-toggle btn-sm" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    {{ __('Select') }}
                                  </button>

                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="{{ route('admin.event_management.edit_event', ['id' => $event->id]) }}"
                                      class="dropdown-item">
                                      {{ __('Edit') }}
                                    </a>

                                    <form class="deleteForm d-block"
                                      action="{{ route('admin.event_management.delete_event', ['id' => $event->id]) }}"
                                      method="post">

                                      @csrf
                                      <button type="submit" class="btn btn-sm deleteBtn">
                                        {{ __('Delete') }}
                                      </button>
                                    </form>
                                  </div>
                                </div>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="card-footer"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
