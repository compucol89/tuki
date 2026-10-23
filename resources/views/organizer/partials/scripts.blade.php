<script>
  'use strict';

  const baseUrl = "{{ url('/') }}";
</script>

{{-- core js files --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/admin/js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>

{{-- jQuery time-picker --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/jquery.timepicker.min.js') }}"></script>

{{-- jQuery scrollbar --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/jquery.scrollbar.min.js') }}"></script>

{{-- bootstrap notify --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/bootstrap-notify.min.js') }}"></script>

{{-- sweet alert --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/sweetalert.min.js') }}"></script>

{{-- bootstrap tags input --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/bootstrap-tagsinput.min.js') }}"></script>

{{-- bootstrap date-picker --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Select2 JS -->
<script src="{{ asset('assets/admin/js/select2.min.js') }}"></script>

{{-- atlantis js --}}
<script type="text/javascript" src="{{ asset('assets/admin/js/atlantis.js') }}"></script>

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

<script>
  'use strict';
  const account_status = {{ Auth::guard('organizer')->user()->status }};
  const secret_login = {{ Session::get('secret_login') }};
</script>


{{-- Vendor scripts required only by the current view. Must load before admin-main.js. --}}
@stack('vendor-scripts')

{{-- admin-main js --}}
@php
  $adminMainJsPath = 'assets/admin/js/admin-main.js';
  $adminMainJsFullPath = public_path($adminMainJsPath);
@endphp
<script type="text/javascript" src="{{ asset($adminMainJsPath) }}{{ is_file($adminMainJsFullPath) ? '?v=' . substr(md5_file($adminMainJsFullPath), 0, 12) : '' }}"></script>

{{-- Sidebar: estado de secciones persistido en la SESIÓN del servidor.
  El servidor renderiza el sidebar con el estado guardado (sin flash al navegar).
  Este script solo notifica los cambios al backend. --}}
<script>
  'use strict';
  (function () {
    var SECTION_IDS = ['course', 'bookings', 'support_ticket'];
    var $sidebar = $('.sidebar');

    if (!$sidebar.length) {
      return;
    }

    function persistState() {
      var state = {};
      SECTION_IDS.forEach(function (id) {
        var $sec = $('#' + id);
        if ($sec.length) {
          state[id] = $sec.hasClass('show');
        }
      });
      $.post({
        url: '{{ route('organizer.sidebar.state') }}',
        data: state,
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
      });
    }

    $sidebar.on('shown.bs.collapse hidden.bs.collapse', '.collapse', function () {
      persistState();
    });

    // Preservar el scroll del sidebar entre navegaciones (sin "saltos" visuales)
    var SCROLL_KEY = 'tuki-sidebar-scroll';

    function currentScrollEl() {
      return $sidebar.find('.sidebar-wrapper .scroll-content').first();
    }

    function saveScroll() {
      try {
        var $el = currentScrollEl();
        if ($el.length) {
          sessionStorage.setItem(SCROLL_KEY, String($el.scrollTop() || 0));
        }
      } catch (e) {}
    }

    $sidebar.on('click', 'a[href]', function (e) {
      var href = this.getAttribute('href') || '';
      if (href.charAt(0) === '#') {
        return;
      }
      saveScroll();
    });
    window.addEventListener('beforeunload', saveScroll);

    (function restoreScroll() {
      try {
        var saved = parseInt(sessionStorage.getItem(SCROLL_KEY) || '0', 10);
        if (!saved) {
          return;
        }
        var tries = 0;
        var iv = setInterval(function () {
          var $el = currentScrollEl();
          tries += 1;
          if ($el.length && $el.scrollTop() >= 0 && (tries > 40)) {
            $el.scrollTop(saved);
            clearInterval(iv);
          }
        }, 50);
      } catch (e) {}
    })();

    // ESC cierra el submenú abierto y devuelve el foco al toggle
    $(document).on('keydown', function (e) {
      if (e.key !== 'Escape') {
        return;
      }
      var $open = $sidebar.find('.collapse.show').first();
      if ($open.length) {
        var $toggle = $open.closest('.nav-item').find('[data-toggle="collapse"]');
        $open.collapse('hide');
        $toggle.focus();
        e.preventDefault();
      }
    });
  })();
</script>

@yield('variables')

@yield('script')
