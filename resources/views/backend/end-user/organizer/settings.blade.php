@extends('backend.layout')

@section('content')
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
@endsection

@section('style')
  <style>
    .organizer-settings-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      flex-wrap: wrap;
    }

    .organizer-settings-header__eyebrow,
    .organizer-settings-intro__eyebrow,
    .organizer-setting-note__eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      background: #e8f1ff;
      color: #1d4ed8;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .organizer-settings-header__title {
      margin-bottom: 6px;
      color: #0f172a;
      font-size: 28px;
      font-weight: 700;
    }

    .organizer-settings-header__text,
    .organizer-settings-intro__text,
    .organizer-setting-note__text {
      margin-bottom: 0;
      max-width: 640px;
      color: #64748b;
      line-height: 1.7;
    }

    .organizer-settings-summary {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .organizer-settings-summary__item {
      min-width: 170px;
      padding: 14px 16px;
      border: 1px solid #dbe5f3;
      border-radius: 16px;
      background: #fff;
    }

    .organizer-settings-summary__label {
      display: block;
      margin-bottom: 4px;
      color: #64748b;
      font-size: 12px;
    }

    .organizer-settings-summary__value {
      color: #0f172a;
      font-size: 16px;
      font-weight: 700;
    }

    .organizer-settings-shell {
      max-width: 980px;
      margin: 0 auto;
    }

    .organizer-settings-intro {
      margin-bottom: 22px;
      padding: 18px 20px;
      border: 1px solid #e5e7eb;
      border-radius: 20px;
      background: linear-gradient(180deg, #fcfdff 0%, #f8fbff 100%);
    }

    .organizer-settings-grid {
      display: grid;
      gap: 18px;
    }

    .organizer-setting-card {
      padding: 22px;
      border: 1px solid #e5e7eb;
      border-radius: 22px;
      background: #fff;
      box-shadow: 0 14px 30px rgba(15, 23, 42, .05);
    }

    .organizer-setting-card__top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
    }

    .organizer-setting-card__title {
      margin-bottom: 8px;
      color: #0f172a;
      font-size: 20px;
      font-weight: 700;
    }

    .organizer-setting-card__text {
      margin-bottom: 0;
      color: #64748b;
      line-height: 1.7;
    }

    .organizer-setting-card__hint {
      margin-top: 14px;
      padding-top: 14px;
      border-top: 1px solid #eef2f7;
      color: #475569;
      line-height: 1.7;
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
      background: #cbd5e1;
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
      background: #2563eb;
    }

    .organizer-setting-card__switch .custom-control-input:checked~.custom-control-label::after {
      transform: translateX(20px);
    }

    .organizer-setting-note {
      padding: 22px;
      border: 1px solid #dbe5f3;
      border-radius: 22px;
      background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
      box-shadow: 0 14px 30px rgba(15, 23, 42, .05);
    }

    .organizer-setting-note__title {
      margin-bottom: 8px;
      color: #0f172a;
      font-size: 20px;
      font-weight: 700;
    }

    .organizer-setting-note__header {
      margin-bottom: 18px;
    }

    .organizer-setting-note__label {
      color: #0f172a;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .organizer-setting-note__textarea {
      min-height: 130px;
      border-radius: 14px;
    }

    .organizer-setting-note__foot {
      margin-top: 10px;
      color: #64748b;
      line-height: 1.7;
    }

    .organizer-settings-footer {
      max-width: 540px;
      margin: 0 auto;
      text-align: center;
    }

    .organizer-settings-footer__text {
      margin-bottom: 16px;
      color: #64748b;
      line-height: 1.7;
    }

    .organizer-settings-footer__btn {
      min-width: 230px;
      border-radius: 14px;
      padding: 12px 22px;
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
  </style>
@endsection
