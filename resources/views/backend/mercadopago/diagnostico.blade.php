@extends('backend.layout')

@section('title', 'Diagnóstico MercadoPago')

@section('content')
<div class="page-inner">
  <div class="page-header">
    <h4 class="page-title">Diagnóstico MercadoPago</h4>
    <ul class="breadcrumbs">
      <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="{{ route('admin.payment_gateways.online_gateways') }}">Pasarelas de pago</a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item">Diagnóstico MercadoPago</li>
    </ul>
  </div>

  @if (!$configured)
    <div class="alert alert-danger">
      <strong>MercadoPago no está configurado.</strong>
      <a href="{{ route('admin.payment_gateways.online_gateways') }}" class="alert-link ml-2">Configurar ahora</a>
    </div>
  @else

  {{-- Card 1: Estado de configuración --}}
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Estado de configuración</h4>
        </div>
        <div class="card-body">
          <table class="table table-borderless mb-0">
            <tbody>
              <tr>
                <td class="text-muted" style="width:160px">Estado</td>
                <td>
                  @if ($active)
                    <span class="badge badge-success">Activo</span>
                  @else
                    <span class="badge badge-danger">Inactivo</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="text-muted">Entorno</td>
                <td>
                  @if ($sandbox)
                    <span class="badge badge-info">Sandbox</span>
                  @else
                    <span class="badge badge-warning">Producción</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td class="text-muted">Modo detectado</td>
                <td><code>{{ $mode }}</code></td>
              </tr>
              <tr>
                <td class="text-muted">Token</td>
                <td><code>{{ $maskedToken }}</code></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Card 4: Último pago --}}
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Último pago procesado</h4>
        </div>
        <div class="card-body">
          @if ($lastBooking)
            <table class="table table-borderless mb-0">
              <tbody>
                <tr>
                  <td class="text-muted" style="width:160px">Booking ID</td>
                  <td><strong>#{{ $lastBooking->id }}</strong></td>
                </tr>
                <tr>
                  <td class="text-muted">Monto</td>
                  <td>{{ number_format($lastBooking->total_price ?? 0, 2) }}</td>
                </tr>
                <tr>
                  <td class="text-muted">Fecha</td>
                  <td>{{ \Carbon\Carbon::parse($lastBooking->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                  <td class="text-muted">Estado</td>
                  <td>
                    @if (($lastBooking->status ?? '') === 'complete')
                      <span class="badge badge-success">Completado</span>
                    @elseif (($lastBooking->status ?? '') === 'pending')
                      <span class="badge badge-warning">Pendiente</span>
                    @else
                      <span class="badge badge-secondary">{{ $lastBooking->status ?? 'N/A' }}</span>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
          @else
            <p class="text-muted mb-0">No hay pagos registrados con MercadoPago.</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Card 2: Test de conexión --}}
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Test de conexión API</h4>
        </div>
        <div class="card-body">
          <p class="text-muted">Verifica que el token puede autenticarse contra la API de MercadoPago.</p>
          <button id="btn-test-connection" class="btn btn-primary btn-sm">
            <i class="fas fa-plug mr-1"></i> Testear conexión
          </button>
          <div id="result-connection" class="mt-3" style="display:none"></div>
        </div>
      </div>
    </div>

    {{-- Card 3: Test de preferencia --}}
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Test de preferencia</h4>
        </div>
        <div class="card-body">
          <p class="text-muted">
            Crea una preferencia de prueba en MercadoPago.
            <strong>No realiza ningún cobro.</strong>
          </p>
          <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:0.85rem">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Esto crea una preferencia real en MercadoPago pero <strong>NO cobra nada</strong>.
          </div>
          <button id="btn-test-preference" class="btn btn-warning btn-sm">
            <i class="fas fa-file-invoice-dollar mr-1"></i> Crear preferencia de prueba
          </button>
          <div id="result-preference" class="mt-3" style="display:none"></div>
        </div>
      </div>
    </div>
  </div>

  @endif
</div>
@endsection

@push('scripts')
<script>
(function ($) {
  var csrfToken = $('meta[name="csrf-token"]').attr('content');

  function renderResult(container, data) {
    var cls = data.success ? 'alert-success' : 'alert-danger';
    var icon = data.success ? 'fa-check-circle' : 'fa-times-circle';
    var extra = '';
    if (data.success && data.user_id) {
      extra = '<br><small>User ID: <code>' + data.user_id + '</code>'
            + (data.nickname ? ' &nbsp;·&nbsp; Nickname: <code>' + data.nickname + '</code>' : '')
            + '</small>';
    }
    if (data.success && data.preference_id) {
      extra = '<br><small>Preference ID: <code>' + data.preference_id + '</code></small>';
    }
    container.html(
      '<div class="alert ' + cls + ' py-2 px-3 mb-0" style="font-size:0.85rem">'
      + '<i class="fas ' + icon + ' mr-1"></i>' + data.message + extra
      + '</div>'
    ).show();
  }

  function runTest(btn, resultContainer, url) {
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Probando...');
    resultContainer.hide();
    $.ajax({
      url: url,
      method: 'GET',
      headers: { 'X-CSRF-TOKEN': csrfToken },
      success: function (data) { renderResult(resultContainer, data); },
      error: function () {
        renderResult(resultContainer, { success: false, message: 'Error al comunicarse con el servidor.' });
      },
      complete: function () {
        btn.prop('disabled', false).html(btn.data('label'));
      }
    });
  }

  $('#btn-test-connection').data('label', '<i class="fas fa-plug mr-1"></i> Testear conexión').on('click', function () {
    runTest($(this), $('#result-connection'), '{{ route("admin.mercadopago.test_connection") }}');
  });

  $('#btn-test-preference').data('label', '<i class="fas fa-file-invoice-dollar mr-1"></i> Crear preferencia de prueba').on('click', function () {
    runTest($(this), $('#result-preference'), '{{ route("admin.mercadopago.test_preference") }}');
  });
}(jQuery));
</script>
@endpush
