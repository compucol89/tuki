@extends('backend.layout')

@section('style')
  <style>
    .admin-billing-settings {
      --bs-ink: #1e2532;
      --bs-ink-strong: #111827;
      --bs-muted: #667085;
      --bs-border: #e4e7ec;
      --bs-soft: #f8fafc;
      --bs-card: #ffffff;
      --bs-header: #fbfcfd;
      --bs-primary: #f97316;
      --bs-warning-bg: #fff7ed;
      --bs-warning-border: #fdba74;
      --bs-warning-ink: #9a3412;
      --bs-radius: 8px;
      --bs-control-h: 42px;
      --bs-gap: 16px;
      color: var(--bs-ink);
    }

    .admin-billing-settings .page-header {
      margin-bottom: 16px;
    }

    .admin-billing-settings .page-title {
      color: var(--bs-ink-strong) !important;
      font-size: 24px !important;
      font-weight: 750 !important;
      line-height: 1.2;
    }

    .admin-billing-settings .breadcrumbs,
    .admin-billing-settings .breadcrumbs a {
      color: var(--bs-muted) !important;
      font-size: 12.5px;
      font-weight: 500;
    }

    .admin-billing-settings .card {
      background: var(--bs-card) !important;
      border: 1px solid var(--bs-border) !important;
      border-radius: var(--bs-radius);
      box-shadow: none !important;
      overflow: hidden;
    }

    .admin-billing-settings .card-header {
      background: var(--bs-header) !important;
      border-bottom: 1px solid var(--bs-border) !important;
      padding: 16px 20px;
    }

    .admin-billing-settings .card-title {
      color: var(--bs-ink-strong);
      font-size: 16px;
      font-weight: 700;
      margin: 0;
    }

    .admin-billing-settings .card-body {
      background: var(--bs-card) !important;
      padding: 20px;
    }

    .admin-billing-settings .card-footer {
      background: var(--bs-header) !important;
      border-top: 1px solid var(--bs-border) !important;
      padding: 14px 20px;
    }

    .admin-billing-settings__shell {
      display: grid;
      gap: 20px;
      margin: 0 auto;
      max-width: 920px;
    }

    .admin-billing-settings .billing-alert {
      align-items: flex-start;
      background: var(--bs-warning-bg) !important;
      border: 1px solid var(--bs-warning-border) !important;
      border-left: 4px solid #f97316 !important;
      border-radius: var(--bs-radius);
      color: var(--bs-warning-ink) !important;
      display: flex;
      font-size: 14px;
      font-weight: 600;
      gap: 12px;
      line-height: 1.45;
      margin: 0;
      padding: 14px 16px;
    }

    .admin-billing-settings .billing-alert__icon {
      color: #c2410c;
      flex: 0 0 auto;
      font-size: 16px;
      line-height: 1.4;
      margin-top: 1px;
    }

    .admin-billing-settings .billing-section {
      background: var(--bs-soft);
      border: 1px solid var(--bs-border);
      border-radius: var(--bs-radius);
      padding: 16px 18px;
    }

    .admin-billing-settings .billing-section__title {
      color: var(--bs-ink-strong);
      font-size: 14px;
      font-weight: 750;
      letter-spacing: .01em;
      margin: 0 0 4px;
    }

    .admin-billing-settings .billing-section__hint {
      color: var(--bs-muted);
      font-size: 13px;
      line-height: 1.4;
      margin: 0 0 16px;
    }

    .admin-billing-settings .billing-grid {
      display: grid;
      gap: var(--bs-gap);
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .admin-billing-settings .billing-grid--full {
      grid-template-columns: 1fr;
    }

    .admin-billing-settings .form-group {
      margin-bottom: 0;
    }

    .admin-billing-settings .form-group.span-2 {
      grid-column: 1 / -1;
    }

    .admin-billing-settings label,
    .admin-billing-settings .billing-label {
      color: var(--bs-muted);
      display: block;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .04em;
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    .admin-billing-settings .billing-req {
      color: #dc2626;
      font-weight: 800;
      margin-left: 2px;
    }

    .admin-billing-settings .form-control {
      background: var(--bs-card) !important;
      border: 1px solid var(--bs-border) !important;
      border-radius: var(--bs-radius);
      color: var(--bs-ink-strong) !important;
      height: var(--bs-control-h);
      min-height: var(--bs-control-h);
    }

    .admin-billing-settings .form-control:focus {
      border-color: var(--bs-primary) !important;
      box-shadow: 0 0 0 3px rgba(249, 115, 22, .2);
      outline: 0;
    }

    .admin-billing-settings .form-control::placeholder {
      color: var(--bs-muted);
      opacity: .85;
    }

    .admin-billing-settings .form-text {
      color: var(--bs-muted) !important;
      font-size: 12.5px;
      line-height: 1.4;
      margin-top: 6px;
    }

    .admin-billing-settings .text-danger {
      color: #dc2626 !important;
      font-size: 12.5px;
      font-weight: 600;
      margin-top: 6px;
    }

    .admin-billing-settings fieldset {
      border: 0;
      margin: 0;
      min-width: 0;
      padding: 0;
    }

    .admin-billing-settings legend.billing-label {
      float: none;
      width: auto;
    }

    .admin-billing-settings .billing-checks {
      display: grid;
      gap: 10px;
    }

    .admin-billing-settings .custom-control {
      align-items: center;
      display: flex;
      min-height: 28px;
      padding-left: 1.75rem;
    }

    .admin-billing-settings .custom-control-label {
      color: var(--bs-ink) !important;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 0;
      line-height: 1.35;
      margin: 0;
      padding-top: 1px;
      text-transform: none;
    }

    .admin-billing-settings .custom-control-input:focus ~ .custom-control-label::before {
      box-shadow: 0 0 0 3px rgba(249, 115, 22, .25);
    }

    .admin-billing-settings .billing-file {
      align-items: center;
      background: var(--bs-card);
      border: 1px dashed var(--bs-border);
      border-radius: var(--bs-radius);
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      padding: 12px 14px;
    }

    .admin-billing-settings .billing-file__input {
      color: var(--bs-ink);
      font-size: 13px;
      max-width: 100%;
    }

    .admin-billing-settings .billing-file__input::file-selector-button {
      background: var(--bs-soft);
      border: 1px solid var(--bs-border);
      border-radius: 6px;
      color: var(--bs-ink-strong);
      cursor: pointer;
      font-size: 13px;
      font-weight: 650;
      margin-right: 12px;
      padding: 8px 12px;
    }

    .admin-billing-settings .billing-file__input::-webkit-file-upload-button {
      background: var(--bs-soft);
      border: 1px solid var(--bs-border);
      border-radius: 6px;
      color: var(--bs-ink-strong);
      cursor: pointer;
      font-size: 13px;
      font-weight: 650;
      margin-right: 12px;
      padding: 8px 12px;
    }

    .admin-billing-settings .billing-file__preview {
      align-items: center;
      background: var(--bs-soft);
      border: 1px solid var(--bs-border);
      border-radius: 6px;
      display: inline-flex;
      padding: 6px 10px;
    }

    .admin-billing-settings .billing-file__preview img {
      display: block;
      max-height: 48px;
      width: auto;
    }

    .admin-billing-settings .billing-actions {
      display: flex;
      justify-content: center;
    }

    .admin-billing-settings .billing-actions .btn {
      align-items: center;
      border-radius: var(--bs-radius);
      box-shadow: none !important;
      display: inline-flex;
      font-size: 14px;
      font-weight: 700;
      gap: 8px;
      min-height: 44px;
      min-width: 160px;
      padding: 0 22px;
    }

    .admin-billing-settings .billing-actions .btn:focus-visible {
      outline: 2px solid var(--bs-primary);
      outline-offset: 2px;
    }

    @media (max-width: 767.98px) {
      .admin-billing-settings .billing-grid {
        grid-template-columns: 1fr;
      }

      .admin-billing-settings .card-body {
        padding: 16px;
      }

      .admin-billing-settings .billing-section {
        padding: 14px;
      }
    }

    html[data-theme="dark"] .admin-billing-settings {
      --bs-ink: #e5e5e5;
      --bs-ink-strong: #ffffff;
      --bs-muted: #a3a3a3;
      --bs-border: #3d4354;
      --bs-soft: #1f2838;
      --bs-card: #2a3040;
      --bs-header: #252b38;
      --bs-primary: #e05d38;
      --bs-warning-bg: #3a2c26;
      --bs-warning-border: #9a5b2f;
      --bs-warning-ink: #fdba74;
    }

    html[data-theme="dark"] .admin-billing-settings .billing-alert__icon {
      color: #fdba74;
    }

    html[data-theme="dark"] .admin-billing-settings .form-control {
      background: #1a2030 !important;
      color: #ffffff !important;
    }

    html[data-theme="dark"] .admin-billing-settings .billing-file {
      background: #1a2030;
    }

    html[data-theme="dark"] .admin-billing-settings .billing-file__input::file-selector-button,
    html[data-theme="dark"] .admin-billing-settings .billing-file__input::-webkit-file-upload-button {
      background: #252b38;
      border-color: #3d4354;
      color: #ffffff;
    }

    html[data-theme="dark"] .admin-billing-settings .billing-req,
    html[data-theme="dark"] .admin-billing-settings .text-danger {
      color: #f87171 !important;
    }

    html[data-theme="dark"] .admin-billing-settings .custom-control-label::before {
      background-color: #1a2030;
      border-color: #5b6478;
    }

    html[data-theme="dark"] .admin-billing-settings .custom-control-input:checked ~ .custom-control-label::before {
      background-color: #e05d38;
      border-color: #e05d38;
    }
  </style>
@endsection

@section('content')
  <div class="admin-billing-settings">
    <div class="page-header">
      <h4 class="page-title">{{ __('Configuración de facturación') }}</h4>
      <ul class="breadcrumbs">
        <li class="nav-home">
          <a href="{{ route('admin.dashboard') }}">
            <i class="flaticon-home" aria-hidden="true"></i>
            <span class="sr-only">{{ __('Inicio') }}</span>
          </a>
        </li>
        <li class="separator"><i class="flaticon-right-arrow" aria-hidden="true"></i></li>
        <li class="nav-item"><a href="#">{{ __('Configuración Básica') }}</a></li>
        <li class="separator"><i class="flaticon-right-arrow" aria-hidden="true"></i></li>
        <li class="nav-item"><a href="#" aria-current="page">{{ __('Facturación ARCA') }}</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <form action="{{ route('admin.basic_settings.billing_settings.update') }}" method="post" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="card-header">
              <div class="card-title">{{ __('Facturación ARCA') }}</div>
            </div>

            <div class="card-body">
              <div class="admin-billing-settings__shell">
                <div class="billing-alert" role="status">
                  <i class="fas fa-exclamation-triangle billing-alert__icon" aria-hidden="true"></i>
                  <div>
                    {{ __('La emisión automática ARCA permanece desactivada hasta completar la validación contable y la integración de pagos.') }}
                  </div>
                </div>

                <section class="billing-section" aria-labelledby="billing-emission-title">
                  <h2 class="billing-section__title" id="billing-emission-title">{{ __('Emisión') }}</h2>
                  <p class="billing-section__hint">{{ __('Controlá si se emiten comprobantes y en qué ambiente de ARCA.') }}</p>
                  <div class="billing-grid">
                    <div class="form-group">
                      <label class="billing-label" for="billing_enabled">{{ __('Emisión automática') }}</label>
                      <select id="billing_enabled" name="enabled" class="form-control" @if($errors->has('enabled')) aria-invalid="true" aria-describedby="billing_enabled_error" @endif>
                        <option value="0" {{ old('enabled', (int) $billingSettings->enabled) == 0 ? 'selected' : '' }}>{{ __('Desactivada') }}</option>
                        <option value="1" {{ old('enabled', (int) $billingSettings->enabled) == 1 ? 'selected' : '' }}>{{ __('Activada') }}</option>
                      </select>
                      @if ($errors->has('enabled'))
                        <p class="mb-0 text-danger" id="billing_enabled_error">{{ $errors->first('enabled') }}</p>
                      @endif
                    </div>

                    <div class="form-group">
                      <label class="billing-label" for="billing_environment">{{ __('Ambiente') }} <span class="billing-req" aria-hidden="true">*</span></label>
                      <select id="billing_environment" name="environment" class="form-control" required aria-required="true" @if($errors->has('environment')) aria-invalid="true" aria-describedby="billing_environment_error" @endif>
                        <option value="testing" {{ old('environment', $billingSettings->environment) == 'testing' ? 'selected' : '' }}>{{ __('Testing') }}</option>
                        <option value="production" {{ old('environment', $billingSettings->environment) == 'production' ? 'selected' : '' }}>{{ __('Producción') }}</option>
                      </select>
                      @if ($errors->has('environment'))
                        <p class="mb-0 text-danger" id="billing_environment_error">{{ $errors->first('environment') }}</p>
                      @endif
                    </div>
                  </div>
                </section>

                <section class="billing-section" aria-labelledby="billing-fiscal-title">
                  <h2 class="billing-section__title" id="billing-fiscal-title">{{ __('Datos fiscales') }}</h2>
                  <p class="billing-section__hint">{{ __('CUIT, condición IVA, punto de venta y reglas de comisión.') }}</p>
                  <div class="billing-grid">
                    <div class="form-group">
                      <label class="billing-label" for="billing_issuer_cuit">{{ __('CUIT emisor') }}</label>
                      <input id="billing_issuer_cuit" type="text" name="issuer_cuit" class="form-control" autocomplete="organization"
                        value="{{ old('issuer_cuit', $billingSettings->issuer_cuit) }}"
                        @if($errors->has('issuer_cuit')) aria-invalid="true" aria-describedby="billing_issuer_cuit_error" @endif>
                      @if ($errors->has('issuer_cuit'))
                        <p class="mb-0 text-danger" id="billing_issuer_cuit_error">{{ $errors->first('issuer_cuit') }}</p>
                      @endif
                    </div>

                    <div class="form-group">
                      <label class="billing-label" for="billing_issuer_iva_condition">{{ __('Condición IVA emisor') }}</label>
                      <input id="billing_issuer_iva_condition" type="text" name="issuer_iva_condition" class="form-control"
                        value="{{ old('issuer_iva_condition', $billingSettings->issuer_iva_condition) }}"
                        @if($errors->has('issuer_iva_condition')) aria-invalid="true" aria-describedby="billing_issuer_iva_condition_error" @endif>
                      @if ($errors->has('issuer_iva_condition'))
                        <p class="mb-0 text-danger" id="billing_issuer_iva_condition_error">{{ $errors->first('issuer_iva_condition') }}</p>
                      @endif
                    </div>

                    <div class="form-group">
                      <label class="billing-label" for="billing_point_of_sale">{{ __('Punto de venta') }}</label>
                      <input id="billing_point_of_sale" type="number" min="1" name="point_of_sale" class="form-control"
                        value="{{ old('point_of_sale', $billingSettings->point_of_sale) }}"
                        @if($errors->has('point_of_sale')) aria-invalid="true" aria-describedby="billing_point_of_sale_error" @endif>
                      @if ($errors->has('point_of_sale'))
                        <p class="mb-0 text-danger" id="billing_point_of_sale_error">{{ $errors->first('point_of_sale') }}</p>
                      @endif
                    </div>

                    <div class="form-group">
                      <label class="billing-label" for="billing_default_invoice_type">{{ __('Tipo de comprobante por defecto') }}</label>
                      <select id="billing_default_invoice_type" name="default_invoice_type" class="form-control"
                        @if($errors->has('default_invoice_type')) aria-invalid="true" aria-describedby="billing_default_invoice_type_error" @endif>
                        <option value="">{{ __('Seleccionar tipo de comprobante') }}</option>
                        <option value="6" {{ (string) old('default_invoice_type', $billingSettings->default_invoice_type) === '6' ? 'selected' : '' }}>{{ __('Factura B — Consumidor final (6)') }}</option>
                        <option value="2" {{ (string) old('default_invoice_type', $billingSettings->default_invoice_type) === '2' ? 'selected' : '' }}>{{ __('Factura A — Responsable inscripto (2)') }}</option>
                        <option value="11" {{ (string) old('default_invoice_type', $billingSettings->default_invoice_type) === '11' ? 'selected' : '' }}>{{ __('Factura C — Monotributo (11)') }}</option>
                      </select>
                      @if ($errors->has('default_invoice_type'))
                        <p class="mb-0 text-danger" id="billing_default_invoice_type_error">{{ $errors->first('default_invoice_type') }}</p>
                      @endif
                    </div>

                    <div class="form-group">
                      <label class="billing-label" for="billing_service_fee_percentage">{{ __('Comisión de servicio (%)') }} <span class="billing-req" aria-hidden="true">*</span></label>
                      <input id="billing_service_fee_percentage" type="number" min="0" max="100" step="0.0001" name="service_fee_percentage"
                        class="form-control" required aria-required="true"
                        value="{{ old('service_fee_percentage', $billingSettings->service_fee_percentage) }}"
                        @if($errors->has('service_fee_percentage')) aria-invalid="true" aria-describedby="billing_service_fee_percentage_error" @endif>
                      @if ($errors->has('service_fee_percentage'))
                        <p class="mb-0 text-danger" id="billing_service_fee_percentage_error">{{ $errors->first('service_fee_percentage') }}</p>
                      @endif
                    </div>

                    <div class="form-group">
                      <label class="billing-label" for="billing_vat_percentage">{{ __('IVA (%)') }} <span class="billing-req" aria-hidden="true">*</span></label>
                      <input id="billing_vat_percentage" type="number" min="0" max="100" step="0.0001" name="vat_percentage" class="form-control"
                        required aria-required="true"
                        value="{{ old('vat_percentage', $billingSettings->vat_percentage) }}"
                        @if($errors->has('vat_percentage')) aria-invalid="true" aria-describedby="billing_vat_percentage_error" @endif>
                      @if ($errors->has('vat_percentage'))
                        <p class="mb-0 text-danger" id="billing_vat_percentage_error">{{ $errors->first('vat_percentage') }}</p>
                      @endif
                    </div>

                    <div class="form-group span-2">
                      <label class="billing-label" for="billing_service_fee_tax_mode">{{ __('Tratamiento IVA comisión') }} <span class="billing-req" aria-hidden="true">*</span></label>
                      <select id="billing_service_fee_tax_mode" name="service_fee_tax_mode" class="form-control" required aria-required="true"
                        @if($errors->has('service_fee_tax_mode')) aria-invalid="true" aria-describedby="billing_service_fee_tax_mode_error" @endif>
                        <option value="no_vat_added" {{ old('service_fee_tax_mode', $billingSettings->service_fee_tax_mode) == 'no_vat_added' ? 'selected' : '' }}>{{ __('Sin IVA agregado') }}</option>
                        <option value="vat_added" {{ old('service_fee_tax_mode', $billingSettings->service_fee_tax_mode) == 'vat_added' ? 'selected' : '' }}>{{ __('IVA agregado') }}</option>
                        <option value="vat_included" {{ old('service_fee_tax_mode', $billingSettings->service_fee_tax_mode) == 'vat_included' ? 'selected' : '' }}>{{ __('IVA incluido') }}</option>
                      </select>
                      @if ($errors->has('service_fee_tax_mode'))
                        <p class="mb-0 text-danger" id="billing_service_fee_tax_mode_error">{{ $errors->first('service_fee_tax_mode') }}</p>
                      @endif
                    </div>
                  </div>
                </section>

                <section class="billing-section" aria-labelledby="billing-template-title">
                  <h2 class="billing-section__title" id="billing-template-title">{{ __('Plantilla de factura') }}</h2>
                  <p class="billing-section__hint">{{ __('Textos y datos del emisor que aparecen en el PDF fiscal.') }}</p>
                  <div class="billing-grid billing-grid--full">
                    <div class="form-group">
                      <label class="billing-label" for="billing_invoice_item_description">{{ __('Descripción del ítem de factura') }}</label>
                      <input id="billing_invoice_item_description" type="text" name="invoice_item_description" class="form-control"
                        value="{{ old('invoice_item_description', $billingSettings->invoice_item_description) }}"
                        placeholder="{{ __('Comisión por servicio de gestión de compra de entradas TukiPass') }}"
                        aria-describedby="billing_invoice_item_description_help{{ $errors->has('invoice_item_description') ? ' billing_invoice_item_description_error' : '' }}">
                      <small class="form-text" id="billing_invoice_item_description_help">{{ __('Podés usar las variables {evento} y {reserva} que se reemplazarán automáticamente.') }}</small>
                      @if ($errors->has('invoice_item_description'))
                        <p class="mb-0 text-danger" id="billing_invoice_item_description_error">{{ $errors->first('invoice_item_description') }}</p>
                      @endif
                    </div>

                    <fieldset class="form-group">
                      <legend class="billing-label">{{ __('Opciones del ítem') }}</legend>
                      <div class="billing-checks">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" name="invoice_item_include_event" class="custom-control-input"
                            id="invoice_item_include_event" value="1"
                            {{ old('invoice_item_include_event', $billingSettings->invoice_item_include_event) ? 'checked' : '' }}>
                          <label class="custom-control-label" for="invoice_item_include_event">{{ __('Incluir nombre del evento') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" name="invoice_item_include_booking" class="custom-control-input"
                            id="invoice_item_include_booking" value="1"
                            {{ old('invoice_item_include_booking', $billingSettings->invoice_item_include_booking) ? 'checked' : '' }}>
                          <label class="custom-control-label" for="invoice_item_include_booking">{{ __('Incluir número de reserva') }}</label>
                        </div>
                      </div>
                    </fieldset>

                    <div class="billing-grid">
                      <div class="form-group">
                        <label class="billing-label" for="billing_issuer_name">{{ __('Nombre del emisor') }}</label>
                        <input id="billing_issuer_name" type="text" name="issuer_name" class="form-control" autocomplete="organization"
                          value="{{ old('issuer_name', $billingSettings->issuer_name) }}"
                          @if($errors->has('issuer_name')) aria-invalid="true" aria-describedby="billing_issuer_name_error" @endif>
                        @if ($errors->has('issuer_name'))
                          <p class="mb-0 text-danger" id="billing_issuer_name_error">{{ $errors->first('issuer_name') }}</p>
                        @endif
                      </div>

                      <div class="form-group">
                        <label class="billing-label" for="billing_issuer_iva_condition_text">{{ __('Texto condición IVA del emisor') }}</label>
                        <input id="billing_issuer_iva_condition_text" type="text" name="issuer_iva_condition_text" class="form-control"
                          value="{{ old('issuer_iva_condition_text', $billingSettings->issuer_iva_condition_text) }}"
                          @if($errors->has('issuer_iva_condition_text')) aria-invalid="true" aria-describedby="billing_issuer_iva_condition_text_error" @endif>
                        @if ($errors->has('issuer_iva_condition_text'))
                          <p class="mb-0 text-danger" id="billing_issuer_iva_condition_text_error">{{ $errors->first('issuer_iva_condition_text') }}</p>
                        @endif
                      </div>

                      <div class="form-group span-2">
                        <label class="billing-label" for="billing_issuer_address">{{ __('Dirección fiscal del emisor') }}</label>
                        <input id="billing_issuer_address" type="text" name="issuer_address" class="form-control" autocomplete="street-address"
                          value="{{ old('issuer_address', $billingSettings->issuer_address) }}"
                          @if($errors->has('issuer_address')) aria-invalid="true" aria-describedby="billing_issuer_address_error" @endif>
                        @if ($errors->has('issuer_address'))
                          <p class="mb-0 text-danger" id="billing_issuer_address_error">{{ $errors->first('issuer_address') }}</p>
                        @endif
                      </div>
                    </div>
                  </div>
                </section>

                <section class="billing-section" aria-labelledby="billing-assets-title">
                  <h2 class="billing-section__title" id="billing-assets-title">{{ __('Logo y envío') }}</h2>
                  <p class="billing-section__hint">{{ __('Logo del PDF fiscal y preferencia de email al cliente.') }}</p>
                  <div class="billing-grid billing-grid--full">
                    <div class="form-group">
                      <label class="billing-label" for="billing_pdf_logo">{{ __('Logo para PDF fiscal') }}</label>
                      <div class="billing-file">
                        <input id="billing_pdf_logo" type="file" name="pdf_logo" class="billing-file__input"
                          accept="image/png,image/jpeg"
                          @if($errors->has('pdf_logo')) aria-invalid="true" aria-describedby="billing_pdf_logo_error" @endif>
                        @if ($billingSettings->pdf_logo_path)
                          <div class="billing-file__preview">
                            <img src="{{ asset('storage/' . $billingSettings->pdf_logo_path) }}" alt="{{ __('Logo actual del PDF fiscal') }}">
                          </div>
                        @endif
                      </div>
                      @if ($errors->has('pdf_logo'))
                        <p class="mb-0 text-danger" id="billing_pdf_logo_error">{{ $errors->first('pdf_logo') }}</p>
                      @endif
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="send_arca_invoice_email" class="custom-control-input"
                          id="send_arca_invoice_email" value="1"
                          {{ old('send_arca_invoice_email', $billingSettings->send_arca_invoice_email) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="send_arca_invoice_email">{{ __('Enviar email de factura fiscal') }}</label>
                      </div>
                      @if ($errors->has('send_arca_invoice_email'))
                        <p class="mb-0 text-danger">{{ $errors->first('send_arca_invoice_email') }}</p>
                      @endif
                    </div>
                  </div>
                </section>
              </div>
            </div>

            <div class="card-footer">
              <div class="billing-actions">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save" aria-hidden="true"></i>
                  {{ __('Actualizar') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
