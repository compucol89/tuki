@extends('organizer.layout')

@section('style')
  <style>
    .telegram-bot-page {
      color: #1e2532;
    }

    .telegram-bot-page .tb-card {
      border: 1px solid #e7eaf0;
      border-radius: 8px;
      box-shadow: 0 6px 18px rgba(30, 37, 50, .04);
    }

    .tb-status {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
    }

    .tb-status--active {
      background: #ecfdf3;
      color: #027a48;
    }

    .tb-status--pending {
      background: #fff7ed;
      color: #9a3412;
    }

    .tb-token {
      padding: 12px;
      border: 1px solid #dbe2ea;
      border-radius: 7px;
      background: #fbfcfe;
      overflow-wrap: anywhere;
      font-weight: 800;
    }

    .tb-command-list {
      display: grid;
      gap: 8px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .tb-command-list li {
      padding: 10px 12px;
      border: 1px solid #eef1f5;
      border-radius: 7px;
      background: #fbfcfe;
    }
  </style>
@endsection

@section('content')
  @php
    $token = session('telegram_link_token');
    $telegramLink = $botUsername && $token ? 'https://t.me/' . $botUsername . '?start=' . $token : null;
  @endphp

  <div class="telegram-bot-page">
    <div class="page-header">
      <h4 class="page-title">{{ __('Bot de Telegram') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('organizer.dashboard') }}" aria-label="{{ __('Ir al panel') }}">
            <i class="flaticon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
          <a href="#">{{ __('Telegram') }}</a>
        </li>
      </ul>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card tb-card">
          <div class="card-header">
            <div class="card-title">{{ __('Conectar Telegram') }}</div>
          </div>
          <div class="card-body">
            @if (!$botEnabled)
              <div class="alert alert-warning text-dark">
                {{ __('El bot todavía no está habilitado en la configuración del servidor.') }}
              </div>
            @endif

            <div class="mb-3">
              @if ($account)
                <span class="tb-status tb-status--active">{{ __('Vinculado') }}</span>
                <p class="text-muted mt-2 mb-0">
                  {{ __('Cuenta') }}:
                  {{ $account->username ? '@' . $account->username : trim($account->first_name . ' ' . $account->last_name) }}
                </p>
              @else
                <span class="tb-status tb-status--pending">{{ __('Sin vincular') }}</span>
                <p class="text-muted mt-2 mb-0">{{ __('Generá un enlace temporal y abrilo desde tu Telegram.') }}</p>
              @endif
            </div>

            <form action="{{ route('organizer.telegram_bot.generate_token') }}" method="post">
              @csrf
              <button type="submit" class="btn btn-primary">
                <i class="fab fa-telegram-plane mr-1" aria-hidden="true"></i>{{ __('Generar enlace de conexión') }}
              </button>
            </form>

            @if ($token)
              <div class="mt-4">
                <label class="font-weight-bold">{{ __('Enlace temporal') }}</label>
                @if ($telegramLink)
                  <div class="tb-token mb-2">
                    <a href="{{ $telegramLink }}" target="_blank" rel="noopener">{{ $telegramLink }}</a>
                  </div>
                @else
                  <div class="tb-token mb-2">/start {{ $token }}</div>
                  <p class="text-muted mb-0">{{ __('Configurá TELEGRAM_BOT_USERNAME para mostrar el enlace directo.') }}</p>
                @endif
                <p class="text-muted mb-0">{{ __('Vence') }}: {{ session('telegram_link_expires_at') }}</p>
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card tb-card">
          <div class="card-header">
            <div class="card-title">{{ __('Comandos') }}</div>
          </div>
          <div class="card-body">
            <ul class="tb-command-list">
              <li><strong>/resumen</strong><br><span class="text-muted">{{ __('Ventas, entradas, pagos y escaneos de los últimos 30 días.') }}</span></li>
              <li><strong>/eventos</strong><br><span class="text-muted">{{ __('Listado rápido de eventos con reservas y avance.') }}</span></li>
              <li><strong>/evento ID</strong><br><span class="text-muted">{{ __('Detalle por evento, tipos de entrada y add-ons.') }}</span></li>
            </ul>
            <p class="text-muted mt-3 mb-0">{{ __('Solo lectura: el bot no escanea ni modifica reservas.') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
