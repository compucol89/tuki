@php
  if (! isset($previousLocale)) {
    $previousLocale = app()->getLocale();
    app()->setLocale('admin');
  }
@endphp
<!DOCTYPE html>
<html lang="es" dir="ltr" data-theme="light">

<head>
  <script>
    (function () {
      try {
        var saved = localStorage.getItem('tuki-theme');
        var theme = saved === 'dark' || saved === 'light'
          ? saved
          : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.dataset.theme = theme;
      } catch (e) {
        document.documentElement.dataset.theme = 'light';
      }
    })();
  </script>
  {{-- required meta tags --}}
  <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

  {{-- csrf-token for ajax request --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- title --}}
  <title>{{ __('Organizer') . ' | ' . $websiteInfo->website_title }}</title>

  {{-- fav icon --}}
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/admin/img/' . $websiteInfo->favicon) }}">

  {{-- include styles --}}
  @includeIf('organizer.partials.styles')

  {{-- additional style --}}
  @yield('style')
</head>

<body data-background-color="{{ Auth::guard('organizer')->user()->theme_version == 'light' ? 'white' : 'dark' }}">
  {{-- skip link (WCAG 2.4.1 Bypass Blocks) --}}
  <a href="#main-content" class="skip-link">Saltar al contenido</a>

  {{-- loader start --}}
  <div class="request-loader">
    <img src="{{ asset('assets/admin/img/loader.gif') }}" alt="loader">
  </div>
  {{-- loader end --}}

  <div class="wrapper">
    {{-- top navbar area start --}}
    @includeIf('organizer.partials.top-navbar')
    {{-- top navbar area end --}}

    {{-- side navbar area start --}}
    @includeIf('organizer.partials.side-navbar')
    {{-- side navbar area end --}}

    <div class="main-panel">
      <main class="content" id="main-content">
        <div class="page-inner">
          @yield('content')
        </div>
      </main>

      {{-- footer area start --}}
      @includeIf('organizer.partials.footer')
      {{-- footer area end --}}
    </div>
  </div>

  {{-- include scripts --}}
  @includeIf('organizer.partials.scripts')
  @includeIf('organizer.partials.modal')

  <script>
    (function () {
      function currentTheme() {
        return document.documentElement.dataset.theme || 'light';
      }
      // Tema persistido en DB al renderizar (source of truth para revertir)
      var serverTheme = currentTheme();
      function applyTheme(theme, persist) {
        document.documentElement.dataset.theme = theme;
        // activar el dark nativo de admin-skin (body[data-background-color="dark"])
        document.body.setAttribute('data-background-color', theme === 'dark' ? 'dark' : 'white');
        // sincronizar en vivo los contenedores con data-background-color propio
        // (sidebar/logo usan dark2; navbar-header usa dark) — sin esto el menú
        // quedaba anclado al valor del servidor y no cambiaba al togglear.
        document.querySelectorAll('.sidebar, .logo-header').forEach(function (el) {
          el.setAttribute('data-background-color', theme === 'dark' ? 'dark2' : 'white');
        });
        document.querySelectorAll('.navbar-header').forEach(function (el) {
          el.setAttribute('data-background-color', theme === 'dark' ? 'dark' : 'white');
        });
        if (persist) {
          try { localStorage.setItem('tuki-theme', theme); } catch (e) { /* noop */ }
          persistServerTheme(theme);
        }
        document.querySelectorAll('[data-theme-toggle-panel]').forEach(function (b) {
          b.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
          b.setAttribute('aria-label', theme === 'dark'
            ? 'Cambiar a modo claro'
            : 'Cambiar a modo oscuro');
        });
      }
      function persistServerTheme(theme) {
        var token = document.querySelector('meta[name="csrf-token"]');
        fetch('{{ route('organizer.change_theme') }}', {
          method: 'POST',
          headers: token ? { 'X-CSRF-TOKEN': token.getAttribute('content') } : {},
          body: new URLSearchParams({ theme_version: theme }),
          credentials: 'same-origin'
        }).then(function (res) {
          if (!res.ok) {
            throw new Error('HTTP ' + res.status);
          }
          serverTheme = theme;
        }).catch(function () {
          // fallo de red/servidor: revertir al tema persistido en DB
          applyTheme(serverTheme, false);
        });
      }
      document.querySelectorAll('[data-theme-toggle-panel]').forEach(function (b) {
        b.addEventListener('click', function () {
          applyTheme(currentTheme() === 'dark' ? 'light' : 'dark', true);
        });
      });
      applyTheme(currentTheme(), false);
    })();
  </script>
</body>

</html>

@isset($previousLocale)
  @php
    app()->setLocale($previousLocale);
  @endphp
@endisset
