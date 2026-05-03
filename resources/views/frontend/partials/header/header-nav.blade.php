<header class="main-header main-header--premium">

  <!--Header-Upper-->
  <div class="header-upper">
    <div class="container clearfix">

      <div class="header-inner">
        <div class="logo-outer">
          <div class="logo"><a href="{{ route('index') }}"><img
                src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ config('app.name', 'Tukipass') }}" width="150" height="40"></a></div>
        </div>

        <div class="nav-outer ml-lg-auto">
          <!-- Main Menu -->
          <nav class="main-menu navbar-expand-xl" aria-label="{{ __('Navegación principal') }}">
            <div class="navbar-header">
              <!-- Toggle Button -->
              <button type="button" class="navbar-toggle" aria-controls="main-menu" aria-expanded="false"
                aria-label="{{ __('Abrir o cerrar navegación') }}">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <div class="logo-mobile"><a href="{{ route('index') }}"><img
                    src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ config('app.name', 'Tukipass') }}" width="150" height="40"></a></div>
              @if (!Auth::guard('customer')->check())
                <a href="{{ route('customer.login') }}" class="header-ingresar-btn">{{ __('Ingresar') }}</a>
              @endif
            </div>

            <div class="navbar-collapse collapse clearfix" id="main-menu">
              <div class="mobile-drawer-body">
              @php
                $links = json_decode($menuInfos, true);
                $currentUrl = url()->current();
              @endphp
              <ul class="navigation navigation--premium clearfix">
                @foreach ($links as $link)
                  @php
                    $href = get_href($link, $currentLanguageInfo->id);
                    $isActive = ($currentUrl === $href);
                    $relAttr = ($link['target'] === '_blank') ? 'noopener noreferrer' : '';
                  @endphp
                  @if (!array_key_exists('children', $link))
                    <li><a href="{{ $href }}" target="{{ $link['target'] }}" {!! $relAttr ? 'rel="'.$relAttr.'"' : '' !!} {!! $isActive ? 'aria-current="page"' : '' !!}>{{ __($link['text']) }}</a></li>
                  @else
                    <li class="dropdown">
                      <a href="{{ $href }}" target="{{ $link['target'] }}" {!! $relAttr ? 'rel="'.$relAttr.'"' : '' !!} {!! $isActive ? 'aria-current="page"' : '' !!}>
                        {{ __($link['text']) }}
                        <i class="fa fa-angle-down"></i>
                      </a>
                      <ul>
                        @foreach ($link['children'] as $level2)
                          @php
                            $l2Href = get_href($level2, $currentLanguageInfo->id);
                            $l2Rel = ($level2['target'] === '_blank') ? 'noopener noreferrer' : '';
                          @endphp
                          <li>
                            <a href="{{ $l2Href }}" target="{{ $level2['target'] }}" {!! $l2Rel ? 'rel="'.$l2Rel.'"' : '' !!}>{{ __($level2['text']) }}</a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @endif
                @endforeach
              </ul>

              <div class="menu-right menu-right--premium">
                @if (!Auth::guard('customer')->check())
                  <div class="dropdown menu-dropdown menu-dropdown--customer">
                    <button type="button" class="menu-btn menu-btn--customer dropdown-toggle mr-1" id="customerGuestDropdown"
                      data-toggle="dropdown">{{ __('Cliente') }}</button>
                    <div class="dropdown-menu" aria-labelledby="customerGuestDropdown">
                      <a class="dropdown-item" href="{{ route('customer.login') }}">{{ __('Ingresar') }}</a>
                      <a class="dropdown-item" href="{{ route('customer.signup') }}">{{ __('Crear cuenta') }}</a>
                    </div>
                  </div>
                @else
                  <div class="dropdown menu-dropdown menu-dropdown--customer">
                    <button type="button" class="menu-btn menu-btn--customer dropdown-toggle mr-1" id="customerUserDropdown"
                      data-toggle="dropdown">{{ Auth::guard('customer')->user()->username }}</button>
                    <div class="dropdown-menu" aria-labelledby="customerUserDropdown">
                      <a class="dropdown-item" href="{{ route('customer.dashboard') }}">{{ __('Mi cuenta') }}</a>
                      <a class="dropdown-item" href="{{ route('customer.logout') }}">{{ __('Salir') }}</a>
                    </div>
                  </div>
                @endif
                @if (Auth::guard('organizer')->check())
                  <div class="dropdown menu-dropdown menu-dropdown--organizer">
                    <button type="button" class="menu-btn menu-btn--organizer dropdown-toggle mr-1" id="organizerUserDropdown"
                      data-toggle="dropdown">{{ Auth::guard('organizer')->user()->username }}</button>
                    <div class="dropdown-menu" aria-labelledby="organizerUserDropdown">
                      <a class="dropdown-item" href="{{ route('organizer.dashboard') }}">{{ __('Panel') }}</a>
                      <a class="dropdown-item" href="{{ route('organizer.logout') }}">{{ __('Salir') }}</a>
                    </div>
                  </div>
                @elseif (!Auth::guard('customer')->check())
                  <div class="dropdown menu-dropdown menu-dropdown--organizer">
                    <button type="button" class="menu-btn menu-btn--organizer dropdown-toggle" id="organizerGuestDropdown"
                      data-toggle="dropdown">{{ __('Organizador') }}</button>
                    <div class="dropdown-menu" aria-labelledby="organizerGuestDropdown">
                      <a class="dropdown-item" href="{{ route('organizer.login') }}">{{ __('Ingresar') }}</a>
                      <a class="dropdown-item" href="{{ route('organizer.signup') }}">{{ __('Crear cuenta') }}</a>
                    </div>
                  </div>
                @endif
              </div>
              </div>
            </div>

            <div class="mobile-menu-overlay" aria-hidden="true"></div>
          </nav>
          <!-- Main Menu End-->
        </div>
      </div>
    </div>
  </div>
  <!--End Header Upper-->
</header>
