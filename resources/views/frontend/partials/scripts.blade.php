    <!-- Global vars (sync — needed before deferred scripts) -->
    <script>
      var baseUrl = "{{ url('/') }}";
    </script>
    <!-- jQuery + Bootstrap core (defer, order preserved) -->
    <script src="{{ asset('assets/front/js/jquery.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/popper.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/bootstrap.4.5.3.min.js') }}" defer></script>
    <!-- jQuery plugins (defer) -->
    <script src="{{ asset('assets/front/js/jquery.magnific-popup.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/imagesloaded.pkgd.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/slick.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/vanilla-lazyload.min.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/jquery-syotimer.min.js') }}" defer></script>
    <!-- App scripts (defer) -->
    <script src="{{ asset('assets/front/js/script.js') }}" defer></script>
    <script src="{{ asset('assets/admin/js/event.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/toastr.js') }}" defer></script>
    <script src="{{ asset('assets/front/js/pwa.js') }}" defer></script>

    @if (Session::has('message'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var type = {!! json_encode(Session::get('alert-type') ?? 'info', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
        var msg = {!! json_encode(Session::get('message'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
        toastr.options = {
          "closeButton": true,
          "progressBar": true,
          "timeOut": 10000,
          "extendedTimeOut": 10000,
          "positionClass": "toast-top-right",
        };
        if (toastr[type]) {
          toastr[type](msg);
        } else {
          toastr.info(msg);
        }
      });
    </script>
    @endif
