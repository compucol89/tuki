@php
  $sb = session('sidebar_state', []);
  $sbCourseOpen = $sb['course'] ?? (request()->routeIs('organizer.event_management.event')
    || request()->routeIs('choose-event-type')
    || request()->routeIs('organizer.event_management.ticket_setting')
    || request()->routeIs('organizer.add.event.event')
    || request()->routeIs('organizer.event_management.edit_event')
    || request()->routeIs('organizer.event.ticket')
    || request()->routeIs('organizer.event.add.ticket')
    || request()->routeIs('organizer.event.edit.ticket'));
  $sbBookingsOpen = $sb['bookings'] ?? (request()->routeIs('organizer.event.booking')
    || request()->routeIs('organizer.event_booking.details')
    || request()->routeIs('organizer.event_booking.report'));
  $sbSupportOpen = $sb['support_ticket'] ?? (request()->routeIs('organizer.support_tickets')
    || request()->routeIs('organizer.support_tickets.message')
    || request()->routeIs('organizer.support_ticket.create'));
@endphp
<div class="sidebar sidebar-style-2"
  data-background-color="{{ Auth::guard('organizer')->user()->theme_version == 'light' ? 'white' : 'dark2' }}">
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <div class="user">
        <div class="avatar-sm float-left mr-2">
          @if (Auth::guard('organizer')->user()->photo != null)
            <img src="{{ asset('assets/admin/img/organizer-photo/' . Auth::guard('organizer')->user()->photo) }}"
              alt="Foto del organizador" class="avatar-img rounded-circle">
          @else
            <img src="{{ asset('assets/admin/img/blank_user.jpg') }}" alt="" class="avatar-img rounded-circle">
          @endif
        </div>


        <div class="info">
          <a>
            <span>
              {{ Auth::guard('organizer')->user()->username }}

              <span class="user-level">{{ __('Organizador') }}</span>
            </span>
          </a>

          <div class="clearfix"></div>
        </div>
      </div>
      {{-- search (fuera del <ul> para estructura semántica válida: el <ul> solo
          puede tener <li> como hijos directos, axe rule "list") --}}
      <div class="row mb-3">
        <div class="col-12">
          <form action="" onsubmit="return false">
            <div class="form-group py-0">
              <label for="sidebar-search" class="visually-hidden">{{ __('Buscar menú') }}</label>
              <input id="sidebar-search" name="term" type="text" class="form-control sidebar-search ltr"
                placeholder="{{ __('Buscar en el menú...') }}">
            </div>
          </form>
        </div>
      </div>

      <ul class="nav nav-primary">
        {{-- PANEL --}}
        <li class="nav-section sidebar-nav-section">
          <span class="text-section">{{ __('Panel') }}</span>
        </li>

        {{-- dashboard --}}
        <li class="nav-item @if (request()->routeIs('organizer.dashboard')) active @endif">
          <a href="{{ route('organizer.dashboard') }}" @if (request()->routeIs('organizer.dashboard')) aria-current="page" @endif>
            <i class="fas fa-palette" aria-hidden="true"></i>
            <p>{{ __('Dashboard') }}</p>
          </a>
        </li>

        {{-- EVENTOS --}}
        <li class="nav-section sidebar-nav-section">
          <span class="text-section">{{ __('Eventos') }}</span>
        </li>

        <li
          class="nav-item 
          @if (request()->routeIs('organizer.event_management.event')) active 
          @elseif (request()->routeIs('choose-event-type')) active 
          @elseif (request()->routeIs('organizer.event_management.ticket_setting')) active 
          @elseif (request()->routeIs('organizer.add.event.event')) active 
          @elseif (request()->routeIs('organizer.event_management.edit_event')) active 
          @elseif (request()->routeIs('organizer.event.ticket')) active
              @elseif (request()->routeIs('organizer.event.add.ticket')) active
              @elseif (request()->routeIs('organizer.event.edit.ticket')) active @endif">
          <a data-toggle="collapse" href="#course" aria-controls="course" aria-expanded="{{ $sbCourseOpen ? 'true' : 'false' }}">
            <i class="fas fa-book" aria-hidden="true"></i>
            <p>{{ __('Gestión de eventos') }}</p>
            <span class="caret"></span>
          </a>

          <div id="course"
            class="collapse
            @if ($sbCourseOpen) show @endif">
            <ul class="nav nav-collapse">

              <li
                class="

              @if (request()->routeIs('choose-event-type')) active
              @elseif (request()->routeIs('organizer.add.event.event') && request()->input('type') == 'online') active 
              @elseif (request()->routeIs('organizer.add.event.event') && request()->input('type') == 'venue') active @endif
              ">
                <a href="{{ route('choose-event-type', ['language' => $defaultLang->code]) }}" @if (request()->routeIs('choose-event-type') || request()->routeIs('organizer.add.event.event')) aria-current="page" @endif>
                  <i class="fas fa-plus-circle" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Agregar evento') }}</span>
                </a>
              </li>

              <li
                class="@if (request()->routeIs('organizer.event_management.event') && request()->input('event_type') == '') active
                  @elseif (request()->routeIs('organizer.event_management.edit_event') && request()->input('event_type') == '') active 
                  @elseif (request()->routeIs('organizer.event_management.ticket_setting')) active 
                  @elseif (request()->routeIs('organizer.event.ticket') && request()->input('event_type') == '') active
              @elseif (request()->routeIs('organizer.event.add.ticket') && request()->input('event_type') == '') active
              @elseif (request()->routeIs('organizer.event.edit.ticket') && request()->input('event_type') == '') active @endif">
                <a href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code]) }}" @if (request()->routeIs('organizer.event_management.event') && request()->input('event_type') == '') aria-current="page" @endif>
                  <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Todos los eventos') }}</span>
                </a>
              </li>

              <li
                class="
              @if (request()->routeIs('organizer.event_management.event') && request()->input('event_type') == 'venue') active 
              @elseif (request()->routeIs('organizer.event.ticket') && request()->input('event_type') == 'venue') active 
              @elseif (request()->routeIs('organizer.event.add.ticket') && request()->input('event_type') == 'venue') active
              @elseif (request()->routeIs('organizer.event.edit.ticket') && request()->input('event_type') == 'venue') active @endif">
                <a
                  href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code, 'event_type' => 'venue']) }}" @if (request()->routeIs('organizer.event_management.event') && request()->input('event_type') == 'venue') aria-current="page" @endif>
                  <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Eventos del lugar') }}</span>
                </a>
              </li>

              <li class="

              @if (request()->routeIs('organizer.event_management.event') && request()->input('event_type') == 'online') active @endif
              ">
                <a
                  href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code, 'event_type' => 'online']) }}" @if (request()->routeIs('organizer.event_management.event') && request()->input('event_type') == 'online') aria-current="page" @endif>
                  <i class="fas fa-globe" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Eventos online') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li
          class="nav-item
          @if (request()->routeIs('organizer.event.booking')) active
          @elseif (request()->routeIs('organizer.event_booking.details')) active
          @elseif (request()->routeIs('organizer.event_booking.report')) active @endif">
          <a data-toggle="collapse" href="#bookings" aria-controls="bookings" aria-expanded="{{ $sbBookingsOpen ? 'true' : 'false' }}">
            <i class="fas fa-people-group" aria-hidden="true"></i>
            <p>{{ __('Reservas de eventos') }}</p>
            <span class="caret"></span>
          </a>

          <div id="bookings"
            class="collapse
          @if ($sbBookingsOpen) show @endif">
            <ul class="nav nav-collapse">
              <li
                class="
              @if (request()->routeIs('organizer.event.booking') && empty(request()->input('status'))) active  
              @elseif (request()->routeIs('organizer.event_booking.details')) active @endif">
                <a href="{{ route('organizer.event.booking') }}" @if (request()->routeIs('organizer.event.booking') && empty(request()->input('status'))) aria-current="page" @endif>
                  <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Todas las reservas') }}</span>
                </a>
              </li>

              <li
                class="{{ request()->routeIs('organizer.event.booking') && request()->input('status') == 'completed' ? 'active' : '' }}">
                <a href="{{ route('organizer.event.booking', ['status' => 'completed']) }}">
                  <i class="fas fa-check-circle" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Reservas completadas') }}</span>
                </a>
              </li>

              <li
                class="{{ request()->routeIs('organizer.event.booking') && request()->input('status') == 'pending' ? 'active' : '' }}">
                <a href="{{ route('organizer.event.booking', ['status' => 'pending']) }}">
                  <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Reservas pendientes') }}</span>
                </a>
              </li>

              <li
                class="{{ request()->routeIs('organizer.event.booking') && request()->input('status') == 'rejected' ? 'active' : '' }}">
                <a href="{{ route('organizer.event.booking', ['status' => 'rejected']) }}">
                  <i class="fas fa-times-circle" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Reservas rechazadas') }}</span>
                </a>
              </li>

              <li class="{{ request()->routeIs('organizer.event_booking.report') ? 'active' : '' }}">
                <a href="{{ route('organizer.event_booking.report') }}" @if (request()->routeIs('organizer.event_booking.report')) aria-current="page" @endif>
                  <i class="fas fa-chart-bar" aria-hidden="true"></i>
                  <span class="sub-item">{{ __('Reportes') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <li class="nav-item">
          <a href="{{ route('organizer.pwa') }}" target="_blank">
            <i class="fas fa-qrcode" aria-hidden="true"></i>
            <p>{{ __('Escáner PWA') }}</p>
          </a>
        </li>

        {{-- FINANZAS --}}
        <li class="nav-section sidebar-nav-section">
          <span class="text-section">{{ __('Finanzas') }}</span>
        </li>

        <li
          class="nav-item 
        @if (request()->routeIs('organizer.withdraw')) active 
        @elseif (request()->routeIs('organizer.withdraw.create')) active @endif">
          <a href="{{ route('organizer.withdraw', ['language' => $defaultLang->code]) }}" @if (request()->routeIs('organizer.withdraw')) aria-current="page" @endif>
            <i class="fas fa-donate" aria-hidden="true"></i>
            <p>{{ __('Retiro') }}</p>
          </a>
        </li>
        <li class="nav-item @if (request()->routeIs('organizer.transcation')) active @endif">
          <a href="{{ route('organizer.transcation') }}" @if (request()->routeIs('organizer.transcation')) aria-current="page" @endif>
            <i class="fas fa-exchange-alt" aria-hidden="true"></i>
            <p>{{ __('Transacciones') }}</p>
          </a>
        </li>

        {{-- HERRAMIENTAS --}}
        <li class="nav-section sidebar-nav-section">
          <span class="text-section">{{ __('Herramientas') }}</span>
        </li>

        <li class="nav-item @if (request()->routeIs('organizer.telegram_bot.*')) active @endif">
          <a href="{{ route('organizer.telegram_bot.index') }}">
            <i class="fab fa-telegram-plane" aria-hidden="true"></i>
            <p>{{ __('Bot de Telegram') }}</p>
          </a>
        </li>

        {{-- SOPORTE --}}
        <li class="nav-section sidebar-nav-section">
          <span class="text-section">{{ __('Soporte') }}</span>
        </li>

        @php
          $support_status = DB::table('support_ticket_statuses')->first();
        @endphp
        @if ($support_status->support_ticket_status == 'active')
          {{-- Support Ticket --}}
          <li
            class="nav-item @if (request()->routeIs('organizer.support_tickets')) active
            @elseif (request()->routeIs('organizer.support_tickets.message')) active
            @elseif (request()->routeIs('organizer.support_ticket.create')) active @endif">
            <a data-toggle="collapse" href="#support_ticket" aria-controls="support_ticket" aria-expanded="{{ $sbSupportOpen ? 'true' : 'false' }}">
              <i class="fas fa-globe" aria-hidden="true"></i>
              <p>{{ __('Tickets de soporte') }}</p>
              <span class="caret"></span>
            </a>

            <div id="support_ticket"
              class="collapse
              @if ($sbSupportOpen) show @endif">
              <ul class="nav nav-collapse">

                <li
                  class="@if (request()->routeIs('organizer.support_tickets')) active
              @elseif (request()->routeIs('organizer.support_tickets.message')) active @endif">
                  <a href="{{ route('organizer.support_tickets') }}" @if (request()->routeIs('organizer.support_tickets')) aria-current="page" @endif>
                    <i class="fas fa-inbox" aria-hidden="true"></i>
                    <span class="sub-item">{{ __('Todos los tickets') }}</span>
                  </a>
                </li>
                <li class="{{ request()->routeIs('organizer.support_ticket.create') ? 'active' : '' }}">
                  <a href="{{ route('organizer.support_ticket.create') }}" @if (request()->routeIs('organizer.support_ticket.create')) aria-current="page" @endif>
                    <i class="fas fa-plus-circle" aria-hidden="true"></i>
                    <span class="sub-item">{{ __('Agregar ticket') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        @endif
      </ul>
    </div>
  </div>
</div>
