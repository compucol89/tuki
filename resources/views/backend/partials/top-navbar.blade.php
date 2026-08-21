<div class="main-header">
  <!-- Logo Header Start -->
  <div class="logo-header" data-background-color="{{ $settings->admin_theme_version == 'light' ? 'white' : 'dark2' }}">
    @if (!empty($websiteInfo->logo))
      <a href="{{ route('index') }}" class="logo" target="_blank">
        <img src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ __('Logo') }}" class="navbar-brand" width="120">
      </a>
    @endif

    <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon">
        <i class="fas fa-bars"></i>
      </span>
    </button>
    <button class="topbar-toggler more"><i class="fas fa-ellipsis-v"></i></button>

    <div class="nav-toggle">
      <button class="btn btn-toggle toggle-sidebar">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
  <!-- Logo Header End -->

  <!-- Navbar Header Start -->
  <nav class="navbar navbar-header navbar-expand-lg"
    data-background-color="{{ $settings->admin_theme_version == 'light' ? 'white2' : 'dark' }}">
    <div class="container-fluid">
      <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
        <li class="nav-item">
          <button type="button" class="btn btn-sm btn-secondary mr-2 theme-toggle-panel" data-theme-toggle-panel
            aria-pressed="false" aria-label="{{ __('Cambiar a modo oscuro') }}" title="{{ __('Cambiar tema claro/oscuro') }}">
            <i class="fa fa-moon" aria-hidden="true"></i>
            <i class="fa fa-sun" aria-hidden="true"></i>
          </button>
        </li>

        <li class="nav-item dropdown hidden-caret">
          <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
            <div class="avatar-sm">
              @if (Auth::guard('admin')->user()->image != null)
                <img src="{{ asset('assets/admin/img/admins/' . Auth::guard('admin')->user()->image) }}"
                  alt="Admin Image" class="avatar-img rounded-circle">
              @else
                <img src="{{ asset('assets/admin/img/blank_user.jpg') }}" alt=""
                  class="avatar-img rounded-circle">
              @endif
            </div>
          </a>

          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    @if (Auth::guard('admin')->user()->image != null)
                      <img src="{{ asset('assets/admin/img/admins/' . Auth::guard('admin')->user()->image) }}"
                        alt="Admin Image" class="avatar-img rounded-circle">
                    @else
                      <img src="{{ asset('assets/admin/img/blank_user.jpg') }}" alt=""
                        class="avatar-img rounded-circle">
                    @endif
                  </div>

                  <div class="u-text">
                    <h4>
                      {{ Auth::guard('admin')->user()->first_name . ' ' . Auth::guard('admin')->user()->last_name }}
                    </h4>
                    <p class="text-muted">{{ Auth::guard('admin')->user()->email }}</p>
                  </div>
                </div>
              </li>

              <li>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.edit_profile') }}">
                  {{ __('Edit Profile') }}
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.change_password') }}">
                  {{ __('Change Password') }}
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.logout') }}">
                  {{ __('Logout') }}
                </a>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!-- Navbar Header End -->
</div>
