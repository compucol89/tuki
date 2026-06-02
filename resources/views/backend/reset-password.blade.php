<!DOCTYPE html>
<html>

<head>
  {{-- required meta tags --}}
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  {{-- title --}}
  <title>{{ __('Reset Password') . ' | ' . $websiteInfo->website_title }}</title>

  {{-- fav icon --}}
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/admin/img/' . $websiteInfo->favicon) }}">

  {{-- bootstrap css --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">

  {{-- atlantis css --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/atlantis.css') }}">

  {{-- admin-login css also using for forget password --}}
  <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-login.css') }}">
</head>

<body>
  {{-- reset password form start --}}
  <div class="forget-page">
    @if (!empty($websiteInfo->logo))
      <div class="text-center mb-4">
        <img class="login-logo" src="{{ asset('assets/admin/img/' . $websiteInfo->logo) }}" alt="{{ __('Logo') }}">
      </div>
    @endif

    @if (Session::has('alert'))
      <div class="alert alert-warning" role="alert">
        {{ Session::get('alert') }}
      </div>
    @endif

    @if (empty($token))
      <div class="alert alert-danger" role="alert">
        {{ __('Token de recuperación inválido. Pedí uno nuevo.') }}
      </div>
      <a class="back-to-login" href="{{ route('admin.forget_password') }}">
        &lt;&lt; {{ __('Solicitar nuevo link') }}
      </a>
    @else
      <div class="form">
        <form class="forget-password-form" action="{{ route('admin.update_reset_password') }}" method="POST">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <input type="password" name="password" placeholder="{{ __('Nueva contraseña') }}" required />
          @if ($errors->has('password'))
            <p class="text-danger text-left">{{ $errors->first('password') }}</p>
          @endif

          <input type="password" name="password_confirmation" placeholder="{{ __('Confirmar contraseña') }}" required />
          @if ($errors->has('password_confirmation'))
            <p class="text-danger text-left">{{ $errors->first('password_confirmation') }}</p>
          @endif

          <button type="submit">{{ __('Restablecer contraseña') }}</button>
        </form>

        <a class="back-to-login" href="{{ route('admin.login') }}">
          &lt;&lt; {{ __('Volver al login') }}
        </a>
      </div>
    @endif
  </div>
  {{-- reset password form end --}}


  {{-- jQuery --}}
  <script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>

  {{-- popper js --}}
  <script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>

  {{-- bootstrap js --}}
  <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>

  {{-- bootstrap notify --}}
  <script src="{{ asset('assets/admin/js/bootstrap-notify.min.js') }}"></script>

  {{-- fonts and icons script --}}
  <script src="{{ asset('assets/admin/js/webfont.min.js') }}"></script>

  <script>
    "use strict";
    WebFont.load({
      google: {
        "families": ["Lato:300,400,700,900"]
      },
      custom: {
        "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands",
          "simple-line-icons"
        ],
        urls: ['{{ asset('assets/admin/css/fonts.min.css') }}']
      },
      active: function() {
        sessionStorage.fonts = true;
      }
    });
  </script>

  @if (session()->has('success'))
    <script>
      "use strict";
      var content = {};

      content.message = '{{ __(session('success')) }}';
      content.title = '{{ __('Success') }}';
      content.icon = 'fa fa-bell';

      $.notify(content, {
        type: 'success',
        placement: {
          from: 'top',
          align: 'right'
        },
        showProgressbar: true,
        time: 1000,
        delay: 4000
      });
    </script>
  @endif

  @if (session()->has('warning'))
    <script>
      "use strict";
      var content = {};

      content.message = '{{ __(session('warning')) }}';
      content.title = '{{ __('Warning!') }}';
      content.icon = 'fa fa-bell';

      $.notify(content, {
        type: 'warning',
        placement: {
          from: 'top',
          align: 'right'
        },
        showProgressbar: true,
        time: 1000,
        delay: 4000
      });
    </script>
  @endif
</body>

</html>
