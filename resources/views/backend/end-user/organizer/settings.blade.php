@extends('backend.layout')

@section('content')
  <div class="admin-organizer-settings">
  <div class="page-header">
    <h4 class="page-title">{{ __('Settings') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Organizers Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Settings') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12 mx-auto">
      <div class="card">
        <div class="card-header">
          <div class="organizer-settings-header">
            <div class="organizer-settings-header__intro">
              <span class="organizer-settings-header__eyebrow">{{ __('Configuracion') }}</span>
              <h3 class="organizer-settings-header__title">{{ __('Alta de productores') }}</h3>
              <p class="organizer-settings-header__text">{{ __('Define si un productor necesita aprobacion manual y si debe confirmar su correo antes de empezar a usar la plataforma.') }}</p>
            </div>

            <div class="organizer-settings-summary">
              <div class="organizer-settings-summary__item">
                <span class="organizer-settings-summary__label">{{ __('Aprobacion manual') }}</span>
                <strong class="organizer-settings-summary__value">{{ $setting->organizer_admin_approval == 1 ? __('Activa') : __('Desactivada') }}</strong>
              </div>
              <div class="organizer-settings-summary__item">
                <span class="organizer-settings-summary__label">{{ __('Verificacion de correo') }}</span>
                <strong class="organizer-settings-summary__value">{{ $setting->organizer_email_verification == 1 ? __('Activa') : __('Desactivada') }}</strong>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="organizer-settings-shell">
            <div class="organizer-settings-intro">
              <span class="organizer-settings-intro__eyebrow">{{ __('Paso 1') }}</span>
              <p class="organizer-settings-intro__text">{{ __('Activa solo lo que realmente necesites. Si pides demasiados pasos, el alta del productor se vuelve mas lenta y puede bajar la conversion.') }}</p>
            </div>

          <div class="row">
            <div class="col-lg-8 mx-auto">
              <form id="organizerSettingForm" action="{{ route('admin.organizer_management.setting.update') }}"
                method="post">
                @csrf
                <div class="organizer-settings-grid">
                  <div class="organizer-setting-card">
                    <div class="organizer-setting-card__top">
                      <div>
                        <h4 class="organizer-setting-card__title">{{ __('Aprobacion manual') }}</h4>
                        <p class="organizer-setting-card__text">{{ __('Cuando esta activa, cada productor nuevo queda pendiente hasta que el admin lo revise y apruebe.') }}</p>
                      </div>
                      <div class="custom-control custom-switch organizer-setting-card__switch organizer_admin_approvalbtn">
                        <input type="checkbox" {{ $setting->organizer_admin_approval == 1 ? 'checked' : '' }}
                          name="organizer_admin_approval" class="custom-control-input" id="organizer_admin_approval">
                        <label class="custom-control-label" for="organizer_admin_approval"></label>
                      </div>
                    </div>
                    <div class="organizer-setting-card__hint">
                      {{ __('Recomendado si quieres revisar identidad, calidad o tipo de evento antes de habilitar la cuenta.') }}
                    </div>
                  </div>

                  <div class="organizer-setting-card">
                    <div class="organizer-setting-card__top">
                      <div>
                        <h4 class="organizer-setting-card__title">{{ __('Verificacion de correo') }}</h4>
                        <p class="organizer-setting-card__text">{{ __('Obliga al productor a confirmar su email antes de acceder por completo a la plataforma.') }}</p>
                      </div>
                      <div class="custom-control custom-switch organizer-setting-card__switch">
                        <input type="checkbox" {{ $setting->organizer_email_verification == 1 ? 'checked' : '' }}
                          name="organizer_email_verification" class="custom-control-input" id="customCheck2">
                        <label class="custom-control-label" for="customCheck2"></label>
                      </div>
                    </div>
                    <div class="organizer-setting-card__hint">
                      {{ __('Ayuda a filtrar registros falsos, pero depende de que tu correo saliente este bien configurado.') }}
                    </div>
                  </div>

                  <div class="organizer-setting-note col-12 {{ $setting->organizer_admin_approval == 0 ? 'd-none' : '' }} admin_approval_notice">
                    <div class="organizer-setting-note__header">
                      <span class="organizer-setting-note__eyebrow">{{ __('Mensaje visible') }}</span>
                      <h4 class="organizer-setting-note__title">{{ __('Aviso de aprobacion manual') }}</h4>
                      <p class="organizer-setting-note__text">{{ __('Este mensaje se muestra en el panel del productor mientras espera aprobacion. Conviene que sea corto, claro y tranquilizador.') }}</p>
                    </div>

                    <div class="form-group mb-0">
                      <label class="organizer-setting-note__label" for="admin_approval_notice">{{ __('Texto del aviso') }}</label>
                      <textarea id="admin_approval_notice" rows="4" name="admin_approval_notice" class="form-control organizer-setting-note__textarea" placeholder="{{ __('Ej: Estamos revisando tu cuenta. Te avisaremos por correo apenas quede aprobada.') }}">{{ $setting->admin_approval_notice }}</textarea>
                      <p class="organizer-setting-note__foot mb-0">{{ __('Se mostrara en el dashboard del productor mientras su cuenta siga pendiente.') }}</p>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="organizer-settings-footer">
            <p class="organizer-settings-footer__text">{{ __('Guarda cuando termines. Estos cambios afectan el flujo de alta de todos los productores nuevos.') }}</p>
            <button type="submit" id="organizerSettingBtn" class="btn btn-success organizer-settings-footer__btn">
              {{ __('Guardar configuracion') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@section('style')
  <style>
    .admin-organizer-settings {
      --os-ink: #1e2532;
      --os-ink-strong: #111827;
      --os-muted: #667085;
      --os-border: #e4e7ec;
      --os-soft: #f8fafc;
      --os-card: #ffffff;
      --os-header: #fbfcfd;
      --os-accent-soft: #fff7ed;
      --os-accent: #c2410c;
      --os-primary: #f97316;
      --os-switch-off: #94a3b8;
      --os-intro-bg: linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%);
      --os-note-bg: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      --os-radius: 12px;
      color: var(--os-ink);
    }

    .admin-organizer-settings > .row > [class*="col-"] > .card {
      background: var(--os-card) !important;
      border-color: var(--os-border) !important;
      box-shadow: none !important;
    }

    .admin-organizer-settings .card-header,
    .admin-organizer-settings .card-footer {
      background: var(--os-header) !important;
      border-color: var(--os-border) !important;
    }

    .admin-organizer-settings .page-title {
      color: var(--os-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
    }

    .admin-organizer-settings .breadcrumbs,
    .admin-organizer-settings .breadcrumbs a {
      color: var(--os-muted) !important;
    }

    .organizer-settings-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      flex-wrap: wrap;
    }

    .organizer-settings-header__eyebrow,
    .organizer-settings-intro__eyebrow,
    .organizer-setting-note__eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 5px 10px;
      border-radius: 999px;
      background: var(--os-accent-soft);
      color: var(--os-accent);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .organizer-settings-header__title {
      margin-bottom: 6px;
      color: var(--os-ink-strong);
      font-size: 24px;
      font-weight: 700;
    }

    .organizer-settings-header__text,
    .organizer-settings-intro__text,
    .organizer-setting-note__text {
      margin-bottom: 0;
      max-width: 640px;
      color: var(--os-muted);
      line-height: 1.55;
      font-size: 13.5px;
    }

    .organizer-settings-summary {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .organizer-settings-summary__item {
      min-width: 160px;
      padding: 12px 14px;
      border: 1px solid var(--os-border);
      border-radius: 10px;
      background: var(--os-soft);
    }

    .organizer-settings-summary__label {
      display: block;
      margin-bottom: 4px;
      color: var(--os-muted);
      font-size: 11.5px;
    }

    .organizer-settings-summary__value {
      color: var(--os-ink-strong);
      font-size: 15px;
      font-weight: 700;
    }

    .organizer-settings-shell {
      max-width: 820px;
      margin: 0 auto;
    }

    .organizer-settings-intro {
      margin-bottom: 18px;
      padding: 14px 16px;
      border: 1px solid var(--os-border);
      border-radius: var(--os-radius);
      background: var(--os-intro-bg);
    }

    .organizer-settings-grid {
      display: grid;
      gap: 14px;
    }

    .organizer-setting-card {
      padding: 18px;
      border: 1px solid var(--os-border);
      border-radius: var(--os-radius);
      background: var(--os-card);
      box-shadow: none;
    }

    .organizer-setting-card__top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
    }

    .organizer-setting-card__title {
      margin-bottom: 6px;
      color: var(--os-ink-strong);
      font-size: 17px;
      font-weight: 700;
    }

    .organizer-setting-card__text {
      margin-bottom: 0;
      color: var(--os-muted);
      line-height: 1.55;
      font-size: 13.5px;
    }

    .organizer-setting-card__hint {
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid var(--os-border);
      color: var(--os-muted);
      line-height: 1.55;
      font-size: 13px;
    }

    .organizer-setting-card__switch {
      padding-left: 0;
      min-width: 62px;
    }

    .organizer-setting-card__switch .custom-control-label {
      padding-left: 0;
    }

    .organizer-setting-card__switch .custom-control-label::before {
      left: auto;
      right: 0;
      width: 46px;
      height: 26px;
      border-radius: 999px;
      border: 0;
      background: var(--os-switch-off);
      box-shadow: none;
    }

    .organizer-setting-card__switch .custom-control-label::after {
      top: calc(.25rem + 3px);
      left: auto;
      right: 23px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: #fff;
    }

    .organizer-setting-card__switch .custom-control-input:checked~.custom-control-label::before {
      background: var(--os-primary);
    }

    .organizer-setting-card__switch .custom-control-input:checked~.custom-control-label::after {
      transform: translateX(20px);
    }

    .organizer-setting-note {
      padding: 18px;
      border: 1px solid var(--os-border);
      border-radius: var(--os-radius);
      background: var(--os-note-bg);
      box-shadow: none;
    }

    .organizer-setting-note__title {
      margin-bottom: 6px;
      color: var(--os-ink-strong);
      font-size: 17px;
      font-weight: 700;
    }

    .organizer-setting-note__header {
      margin-bottom: 14px;
    }

    .organizer-setting-note__label {
      color: var(--os-ink-strong);
      font-weight: 700;
      margin-bottom: 8px;
    }

    .organizer-setting-note__textarea {
      min-height: 120px;
      border-radius: 10px;
      background: var(--os-soft) !important;
      border-color: var(--os-border) !important;
      color: var(--os-ink) !important;
    }

    .organizer-setting-note__textarea:focus {
      border-color: var(--os-primary) !important;
      box-shadow: 0 0 0 3px rgba(249, 115, 22, .2);
    }

    .organizer-setting-note__foot {
      margin-top: 10px;
      color: var(--os-muted);
      line-height: 1.55;
      font-size: 13px;
    }

    .organizer-settings-footer {
      max-width: 540px;
      margin: 0 auto;
      text-align: center;
    }

    .organizer-settings-footer__text {
      margin-bottom: 14px;
      color: var(--os-muted);
      line-height: 1.55;
      font-size: 13.5px;
    }

    .organizer-settings-footer__btn {
      min-width: 220px;
      min-height: 42px;
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: 700;
    }

    @media (max-width: 767.98px) {
      .organizer-setting-card__top {
        flex-direction: column;
      }

      .organizer-settings-summary {
        width: 100%;
      }

      .organizer-settings-summary__item {
        flex: 1 1 100%;
      }
    }

    html[data-theme="dark"] .admin-organizer-settings {
      --os-ink: #e5e5e5;
      --os-ink-strong: #ffffff;
      --os-muted: #a3a3a3;
      --os-border: #3d4354;
      --os-soft: #1f2838;
      --os-card: #2a3040;
      --os-header: #252b38;
      --os-accent-soft: #2a3656;
      --os-accent: #93c5fd;
      --os-primary: #e05d38;
      --os-switch-off: #5b6472;
      --os-intro-bg: linear-gradient(180deg, #252b38 0%, #1f2838 100%);
      --os-note-bg: linear-gradient(180deg, #2a3040 0%, #1f2838 100%);
    }

    html[data-theme="dark"] .admin-organizer-settings > .row > [class*="col-"] > .card,
    html[data-theme="dark"] .admin-organizer-settings .organizer-setting-card,
    html[data-theme="dark"] .admin-organizer-settings .organizer-settings-summary__item {
      background: var(--os-card) !important;
      border-color: var(--os-border) !important;
      box-shadow: none !important;
    }

    html[data-theme="dark"] .admin-organizer-settings .card-header,
    html[data-theme="dark"] .admin-organizer-settings .card-footer,
    html[data-theme="dark"] .admin-organizer-settings .organizer-settings-intro,
    html[data-theme="dark"] .admin-organizer-settings .organizer-setting-note {
      background: var(--os-header) !important;
      border-color: var(--os-border) !important;
    }

    html[data-theme="dark"] .admin-organizer-settings .organizer-settings-intro {
      background: var(--os-intro-bg) !important;
    }

    html[data-theme="dark"] .admin-organizer-settings .organizer-setting-note {
      background: var(--os-note-bg) !important;
    }

    html[data-theme="dark"] .admin-organizer-settings .organizer-settings-header__title,
    html[data-theme="dark"] .admin-organizer-settings .organizer-setting-card__title,
    html[data-theme="dark"] .admin-organizer-settings .organizer-setting-note__title,
    html[data-theme="dark"] .admin-organizer-settings .organizer-setting-note__label,
    html[data-theme="dark"] .admin-organizer-settings .organizer-settings-summary__value {
      color: var(--os-ink-strong) !important;
    }

    html[data-theme="dark"] .admin-organizer-settings .organizer-settings-header__eyebrow,
    html[data-theme="dark"] .admin-organizer-settings .organizer-settings-intro__eyebrow,
    html[data-theme="dark"] .admin-organizer-settings .organizer-setting-note__eyebrow {
      background: var(--os-accent-soft) !important;
      color: var(--os-accent) !important;
    }
  </style>
@endsection
