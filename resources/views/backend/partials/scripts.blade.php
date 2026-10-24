<script>
  'use strict';

  const baseUrl = "{{ url('/') }}";
  var loadImgs = '';
  var ProductloadImgs = '';
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
  const account_status = 1;
  const secret_login = 1;
</script>

{{-- Vendor scripts required only by the current view. Must load before admin-main.js. --}}
@stack('vendor-scripts')

{{-- admin-main js --}}
@php
  $adminMainJsPath = 'assets/admin/js/admin-main.js';
  $adminMainJsFullPath = public_path($adminMainJsPath);
@endphp
<script type="text/javascript" src="{{ asset($adminMainJsPath) }}{{ is_file($adminMainJsFullPath) ? '?v=' . substr(md5_file($adminMainJsFullPath), 0, 12) : '' }}"></script>

@yield('variables')

@yield('script')
@yield('vuescripts')
